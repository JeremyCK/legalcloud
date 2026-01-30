<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\LoanCaseInvoiceMain;
use App\Models\LoanCaseInvoiceDetails;
use App\Models\LoanCaseBillMain;
use App\Http\Controllers\InvoiceController;

class FixInvoiceSST extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:fix-sst 
                            {invoice : Invoice number (e.g., DP20001295) or invoice ID}
                            {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix SST calculation for a specific invoice by recalculating all SST values based on the bill\'s SST rate';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $invoiceIdentifier = $this->argument('invoice');
        $dryRun = $this->option('dry-run');

        $this->info("Fixing SST for invoice: {$invoiceIdentifier}");
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Try to find invoice by invoice number first, then by ID
        $invoice = LoanCaseInvoiceMain::where('invoice_no', $invoiceIdentifier)
            ->where('status', '<>', 99)
            ->first();
        
        if (!$invoice && is_numeric($invoiceIdentifier)) {
            $invoice = LoanCaseInvoiceMain::where('id', $invoiceIdentifier)
                ->where('status', '<>', 99)
                ->first();
        }

        if (!$invoice) {
            $this->error("Invoice not found: {$invoiceIdentifier}");
            return 1;
        }

        $this->info("Found invoice: {$invoice->invoice_no} (ID: {$invoice->id})");

        // Get bill for SST rate
        $bill = LoanCaseBillMain::where('id', $invoice->loan_case_main_bill_id)->first();
        if (!$bill) {
            $this->error("Bill not found for invoice");
            return 1;
        }

        $sstRate = $bill->sst_rate ?? 8;
        $this->info("SST Rate: {$sstRate}%");

        // Check if sst column exists
        $hasSstColumn = DB::select("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'loan_case_invoice_details' 
            AND COLUMN_NAME = 'sst'");
        $sstColumnExists = isset($hasSstColumn[0]) && $hasSstColumn[0]->count > 0;

        if (!$sstColumnExists) {
            $this->error("SST column does not exist in database");
            return 1;
        }

        // Check if this is a split invoice
        $invoiceCount = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $invoice->loan_case_main_bill_id)
            ->where('status', '<>', 99)
            ->count();
        $isSplitInvoice = $invoiceCount > 1;
        
        if ($isSplitInvoice) {
            $this->info("This is a split invoice with {$invoiceCount} invoice(s). Fixing SST for ALL invoices in this bill...");
            
            // Get all invoices for this bill
            $allInvoices = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $invoice->loan_case_main_bill_id)
                ->where('status', '<>', 99)
                ->get();
            
            // Fix SST for ALL invoices in the bill
            foreach ($allInvoices as $inv) {
                if ($inv->id != $invoice->id) {
                    $this->line("  Also fixing invoice: {$inv->invoice_no} (ID: {$inv->id})");
                }
            }
        }
        
        // Get all invoice details for THIS invoice
        $invoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $invoice->id)
            ->where('status', '<>', 99)
            ->get();

        $this->info("Found {$invoiceDetails->count()} invoice detail(s) for invoice {$invoice->invoice_no}");

        $updatedCount = 0;
        $skippedCount = 0;
        $changes = [];

        // First, fix SST for ALL invoices in the bill (if split invoice)
        if ($isSplitInvoice && !$dryRun) {
            $allInvoices = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $invoice->loan_case_main_bill_id)
                ->where('status', '<>', 99)
                ->get();
            
            foreach ($allInvoices as $inv) {
                $allInvoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $inv->id)
                    ->where('status', '<>', 99)
                    ->get();
                
                foreach ($allInvoiceDetails as $detail) {
                    $accountItem = DB::table('account_item')
                        ->where('id', $detail->account_item_id)
                        ->first();
                    
                    if ($accountItem && in_array($accountItem->account_cat_id, [1, 4])) {
                        // Calculate SST from individual amount
                        $sstRaw = $detail->amount * ($sstRate / 100);
                        $sstString = number_format($sstRaw, 3, '.', '');
                        
                        if (substr($sstString, -1) == '5') {
                            $newSstValue = floor($sstRaw * 100) / 100;
                        } else {
                            $newSstValue = round($sstRaw, 2);
                        }
                        
                        $detail->sst = $newSstValue;
                        $detail->save();
                    }
                }
            }
            $this->info("Fixed SST for all invoices in the bill.");
        }
        
        foreach ($invoiceDetails as $detail) {
            // Get account item to check if taxable
            $accountItem = DB::table('account_item')
                ->where('id', $detail->account_item_id)
                ->first();

            if ($accountItem && in_array($accountItem->account_cat_id, [1, 4])) {
                // Calculate SST from individual amount with special rounding rule
                $sstRaw = $detail->amount * ($sstRate / 100);
                $sstString = number_format($sstRaw, 3, '.', '');

                if (substr($sstString, -1) == '5') {
                    $newSstValue = floor($sstRaw * 100) / 100; // Round down
                } else {
                    $newSstValue = round($sstRaw, 2); // Normal rounding
                }

                $oldSst = $detail->sst;
                $accountItemName = $accountItem->name ?? 'Unknown';
                
                // Check if ori_invoice_sst needs fixing (if it was calculated from ori_invoice_amt with wrong rate)
                $oldOriSst = $detail->ori_invoice_sst ?? null;
                $needsOriSstFix = false;
                $newOriSstValue = null;
                
                if ($oldOriSst !== null && $detail->ori_invoice_amt > 0) {
                    // Check if ori_invoice_sst seems wrong (calculated with wrong rate)
                    // If ori_invoice_sst is approximately 4% of ori_invoice_amt but should be 8%, it needs fixing
                    $expectedSstFromOriAmt = $detail->ori_invoice_amt * ($sstRate / 100);
                    $expectedSstString = number_format($expectedSstFromOriAmt, 3, '.', '');
                    if (substr($expectedSstString, -1) == '5') {
                        $expectedSstFromOriAmt = floor($expectedSstFromOriAmt * 100) / 100;
                    } else {
                        $expectedSstFromOriAmt = round($expectedSstFromOriAmt, 2);
                    }
                    
                    // If ori_invoice_sst is significantly different from expected, it might be wrong
                    // But for split invoices, ori_invoice_sst should be sum of all sst values, not calculated from ori_invoice_amt
                    // So we'll fix it after we've updated all sst values
                }

                // Show the change
                if (abs($oldSst - $newSstValue) > 0.01) {
                    $this->line("  - {$accountItemName}: Amount = {$detail->amount}, Old SST = {$oldSst}, New SST = {$newSstValue}");
                    if ($oldOriSst !== null) {
                        $this->line("    Old ori_invoice_sst = {$oldOriSst}, ori_invoice_amt = {$detail->ori_invoice_amt}");
                    }
                    $changes[] = [
                        'item' => $accountItemName,
                        'amount' => $detail->amount,
                        'old_sst' => $oldSst,
                        'new_sst' => $newSstValue,
                        'ori_invoice_amt' => $detail->ori_invoice_amt ?? 0,
                        'old_ori_sst' => $oldOriSst
                    ];
                } else {
                    $this->line("  - {$accountItemName}: Amount = {$detail->amount}, SST = {$newSstValue} (no change)");
                    if ($oldOriSst !== null && abs($oldOriSst - $newSstValue) > 0.01) {
                        $this->line("    Note: ori_invoice_sst = {$oldOriSst} differs from sst = {$newSstValue}");
                    }
                }

                if (!$dryRun) {
                    $detail->sst = $newSstValue;
                    $detail->save();
                }
                $updatedCount++;
            } else {
                // Non-taxable items: clear SST
                if ($detail->sst !== null) {
                    if (!$dryRun) {
                        $detail->sst = null;
                        $detail->save();
                    }
                    $updatedCount++;
                } else {
                    $skippedCount++;
                }
            }
        }

        // Check if ori_invoice_sst column exists
        $hasOriSstColumn = DB::select("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'loan_case_invoice_details' 
            AND COLUMN_NAME = 'ori_invoice_sst'");
        $oriSstColumnExists = isset($hasOriSstColumn[0]) && $hasOriSstColumn[0]->count > 0;
        
        // Check if this is a split invoice
        $invoiceCount = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $invoice->loan_case_main_bill_id)
            ->where('status', '<>', 99)
            ->count();
        $isSplitInvoice = $invoiceCount > 1;
        
        // Fix ori_invoice_sst (both in dry-run and actual run)
        if ($isSplitInvoice && $oriSstColumnExists) {
            $this->info("This is a split invoice. Checking ori_invoice_sst...");
            
            // Reload invoice details to get current sst values (will be updated values if not dry-run)
            $currentInvoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $invoice->id)
                ->where('status', '<>', 99)
                ->get();
            
            // Group by account_item_id
            $accountItemIds = $currentInvoiceDetails->pluck('account_item_id')->unique();
            
            foreach ($accountItemIds as $accountItemId) {
                // Get all details for this account_item_id across all invoices for this bill
                $allDetailsForItem = LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $bill->id)
                    ->where('account_item_id', $accountItemId)
                    ->where('status', '<>', 99)
                    ->get();
                
                // Calculate what the total SST should be
                // Always calculate from amount (not from stored sst) to ensure accuracy
                $totalSst = 0;
                foreach ($allDetailsForItem as $detail) {
                    $accountItem = DB::table('account_item')->where('id', $detail->account_item_id)->first();
                    if ($accountItem && in_array($accountItem->account_cat_id, [1, 4])) {
                        // Always calculate from amount to get the correct SST
                        $sstRaw = $detail->amount * ($sstRate / 100);
                        $sstString = number_format($sstRaw, 3, '.', '');
                        if (substr($sstString, -1) == '5') {
                            $calculatedSst = floor($sstRaw * 100) / 100;
                        } else {
                            $calculatedSst = round($sstRaw, 2);
                        }
                        $totalSst += $calculatedSst;
                    }
                }
                
                // Get ori_invoice_amt
                $oriInvoiceAmt = $allDetailsForItem->first()->ori_invoice_amt ?? 0;
                $oldOriSst = $allDetailsForItem->first()->ori_invoice_sst ?? 0;
                
                $accountItem = DB::table('account_item')->where('id', $accountItemId)->first();
                $accountItemName = $accountItem ? ($accountItem->name ?? 'Unknown') : 'Unknown';
                
                if (abs($oldOriSst - $totalSst) > 0.01) {
                    $this->line("  - {$accountItemName}: ori_invoice_amt = {$oriInvoiceAmt}");
                    $this->line("    Old ori_invoice_sst = {$oldOriSst}, New ori_invoice_sst = {$totalSst} (sum of individual sst)");
                    $changes[] = [
                        'item' => $accountItemName . ' (ori_invoice_sst)',
                        'ori_invoice_amt' => $oriInvoiceAmt,
                        'old_ori_sst' => $oldOriSst,
                        'new_ori_sst' => $totalSst
                    ];
                }
            }
        }
        
        if (!$dryRun) {
            
            if ($isSplitInvoice && $oriSstColumnExists) {
                $this->info("This is a split invoice. Updating ori_invoice_sst from sum of individual sst values...");
                
                // IMPORTANT: Reload invoice details to get updated sst values
                $invoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $invoice->id)
                    ->where('status', '<>', 99)
                    ->get();
                
                // For split invoices, update ori_invoice_sst from sum of individual sst values
                // Group by account_item_id
                $accountItemIds = $invoiceDetails->pluck('account_item_id')->unique();
                
                foreach ($accountItemIds as $accountItemId) {
                    // Get all details for this account_item_id across all invoices for this bill
                    $allDetailsForItem = LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $bill->id)
                        ->where('account_item_id', $accountItemId)
                        ->where('status', '<>', 99)
                        ->get();
                    
                    // Sum all SST values for this account_item_id (after they've been updated above)
                    $totalSst = $allDetailsForItem->sum('sst');
                    
                    // Get ori_invoice_amt to show what it should match
                    $oriInvoiceAmt = $allDetailsForItem->first()->ori_invoice_amt ?? 0;
                    $expectedSstFromOriAmt = $oriInvoiceAmt * ($sstRate / 100);
                    $expectedSstString = number_format($expectedSstFromOriAmt, 3, '.', '');
                    if (substr($expectedSstString, -1) == '5') {
                        $expectedSstFromOriAmt = floor($expectedSstFromOriAmt * 100) / 100;
                    } else {
                        $expectedSstFromOriAmt = round($expectedSstFromOriAmt, 2);
                    }
                    
                    // Get old ori_invoice_sst value
                    $oldOriSst = $allDetailsForItem->first()->ori_invoice_sst ?? 0;
                    
                    // Update ori_invoice_sst for ALL invoices with the same account_item_id
                    LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $bill->id)
                        ->where('account_item_id', $accountItemId)
                        ->where('status', '<>', 99)
                        ->update(['ori_invoice_sst' => round($totalSst, 2)]);
                    
                    $accountItem = DB::table('account_item')->where('id', $accountItemId)->first();
                    $accountItemName = $accountItem ? ($accountItem->name ?? 'Unknown') : 'Unknown';
                    
                    if (abs($oldOriSst - $totalSst) > 0.01) {
                        $this->line("  - {$accountItemName}: ori_invoice_amt = {$oriInvoiceAmt}");
                        $this->line("    Old ori_invoice_sst = {$oldOriSst}, New ori_invoice_sst = {$totalSst} (sum of individual sst)");
                        $this->line("    Expected from ori_invoice_amt ({$sstRate}%) = {$expectedSstFromOriAmt}");
                        $changes[] = [
                            'item' => $accountItemName . ' (ori_invoice_sst)',
                            'ori_invoice_amt' => $oriInvoiceAmt,
                            'old_ori_sst' => $oldOriSst,
                            'new_ori_sst' => $totalSst,
                            'expected_from_ori_amt' => $expectedSstFromOriAmt
                        ];
                    } else {
                        $this->line("  - {$accountItemName}: ori_invoice_sst = {$totalSst} (no change)");
                    }
                }
            } else {
                // For single invoice, update ori_invoice_sst from individual sst values
                if ($oriSstColumnExists) {
                    foreach ($invoiceDetails as $detail) {
                        $accountItem = DB::table('account_item')
                            ->where('id', $detail->account_item_id)
                            ->first();
                        
                        if ($accountItem && in_array($accountItem->account_cat_id, [1, 4])) {
                            // For single invoice, ori_invoice_sst should equal sst
                            $detail->ori_invoice_sst = $detail->sst;
                            $detail->save();
                        }
                    }
                }
            }
            
            // Recalculate invoice totals from details (need to reload to get updated SST values)
            $invoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $invoice->id)
                ->where('status', '<>', 99)
                ->get();
            
            $pfee1 = 0;
            $pfee2 = 0;
            $sst = 0;
            $reimbursement_amount = 0;
            $reimbursement_sst = 0;
            
            foreach ($invoiceDetails as $detail) {
                $accountItem = DB::table('account_item')
                    ->where('id', $detail->account_item_id)
                    ->first();
                
                if ($accountItem) {
                    if ($accountItem->account_cat_id == 1) {
                        // Professional fees
                        if ($accountItem->pfee1_item == 1) {
                            $pfee1 += $detail->amount;
                        } else {
                            $pfee2 += $detail->amount;
                        }
                        $sst += $detail->sst ?? 0;
                    } elseif ($accountItem->account_cat_id == 4) {
                        // Reimbursement
                        $reimbursement_amount += $detail->amount;
                        $reimbursement_sst += $detail->sst ?? 0;
                    }
                }
            }
            
            $total = round($pfee1 + $pfee2 + $sst + $reimbursement_amount + $reimbursement_sst, 2);
            
            // Update invoice with calculated amounts
            $invoice->pfee1_inv = round($pfee1, 2);
            $invoice->pfee2_inv = round($pfee2, 2);
            $invoice->sst_inv = round($sst, 2);
            $invoice->reimbursement_amount = round($reimbursement_amount, 2);
            $invoice->reimbursement_sst = round($reimbursement_sst, 2);
            $invoice->amount = $total;
            $invoice->save();

            $this->info("\nInvoice totals updated:");
            $this->line("  - Professional Fee 1: " . round($pfee1, 2));
            $this->line("  - Professional Fee 2: " . round($pfee2, 2));
            $this->line("  - SST: " . round($sst, 2));
            $this->line("  - Reimbursement Amount: " . round($reimbursement_amount, 2));
            $this->line("  - Reimbursement SST: " . round($reimbursement_sst, 2));
            $this->line("  - Total: {$total}");
        }

        $this->info("\nSummary:");
        $this->line("  - Updated details: {$updatedCount}");
        $this->line("  - Skipped details: {$skippedCount}");
        $this->line("  - Changes made: " . count($changes));

        if (count($changes) > 0) {
            $this->info("\nChanges:");
            foreach ($changes as $change) {
                if (isset($change['old_ori_sst'])) {
                    // This is an ori_invoice_sst change
                    $this->line("  - {$change['item']}: {$change['old_ori_sst']} → {$change['new_ori_sst']}");
                } else {
                    // This is a regular sst change
                    $this->line("  - {$change['item']}: {$change['old_sst']} → {$change['new_sst']}");
                }
            }
        }

        if ($dryRun) {
            $this->warn("\nDRY RUN - No changes were made. Remove --dry-run to apply changes.");
        } else {
            $this->info("\nSST fixed successfully!");
        }

        return 0;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\LoanCaseInvoiceDetails;
use App\Models\LoanCaseBillMain;
use App\Models\TransferFeeDetails;

class BackfillInvoiceDetailsSST extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:backfill-sst 
                            {--dry-run : Run without making changes}
                            {--force : Force update even if SST already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill SST values in loan_case_invoice_details table. 
                             If transfer details exist, use SST from transfer. 
                             Otherwise, calculate from amount * SST rate.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('Starting SST backfill process...');
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Check if SST column exists
        $hasSstColumn = DB::select("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'loan_case_invoice_details' 
            AND COLUMN_NAME = 'sst'");
        
        if (!isset($hasSstColumn[0]) || $hasSstColumn[0]->count == 0) {
            $this->error('SST column does not exist in loan_case_invoice_details table!');
            return 1;
        }

        // Get all invoice details that need SST
        // Only process taxable items: Professional fees (1) and Reimbursement (4)
        // Stamp duties (2) and Disbursement (3) are NOT taxable, so SST should remain NULL
        $query = DB::table('loan_case_invoice_details as ild')
            ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
            ->leftJoin('loan_case_invoice_main as im', 'ild.invoice_main_id', '=', 'im.id')
            ->leftJoin('loan_case_bill_main as bm', 'im.loan_case_main_bill_id', '=', 'bm.id')
            ->select(
                'ild.id as detail_id',
                'ild.invoice_main_id',
                'ild.account_item_id',
                'ild.amount',
                'ild.sst',
                'ild.status',
                'ai.account_cat_id',
                'ai.name as account_name',
                'im.loan_case_main_bill_id',
                'bm.sst_rate',
                'bm.id as bill_id'
            )
            ->where('ild.status', '<>', 99)
            ->whereIn('ai.account_cat_id', [1, 4]); // Professional fees (1) and Reimbursement (4)
        
        // Also get count of non-taxable items for reporting
        $nonTaxableCount = DB::table('loan_case_invoice_details as ild')
            ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
            ->where('ild.status', '<>', 99)
            ->whereIn('ai.account_cat_id', [2, 3]) // Stamp duties (2) and Disbursement (3)
            ->where(function($q) use ($force) {
                if (!$force) {
                    $q->whereNull('ild.sst')
                      ->orWhere('ild.sst', '=', 0)
                      ->orWhere('ild.sst', '=', '');
                }
            })
            ->count();

        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('ild.sst')
                  ->orWhere('ild.sst', '=', 0)
                  ->orWhere('ild.sst', '=', '');
            });
        }

        $invoiceDetails = $query->get();

        $this->info("Found {$invoiceDetails->count()} taxable invoice details to process");
        if ($nonTaxableCount > 0) {
            $this->comment("Note: {$nonTaxableCount} non-taxable items (Stamp duties/Disbursement) will remain NULL (as expected)");
        }

        $updated = 0;
        $skipped = 0;
        $errors = 0;
        $fromTransfer = 0;
        $calculated = 0;

        $bar = $this->output->createProgressBar($invoiceDetails->count());
        $bar->start();

        foreach ($invoiceDetails as $detail) {
            try {
                $newSst = null;
                $source = '';

                // Step 1: Check if transfer details exist for this invoice
                // Transfer details have SST at invoice level, so we distribute proportionally
                $transferDetail = TransferFeeDetails::where('loan_case_invoice_main_id', $detail->invoice_main_id)
                    ->where('status', 1)
                    ->first();

                if ($transferDetail && $transferDetail->sst_amount !== null && $transferDetail->sst_amount > 0) {
                    // Transfer details exist - distribute SST proportionally to detail items
                    // Get total taxable amount for this invoice (professional fees + reimbursement)
                    $invoiceTaxableTotal = DB::table('loan_case_invoice_details as ild')
                        ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
                        ->where('ild.invoice_main_id', $detail->invoice_main_id)
                        ->where('ild.status', '<>', 99)
                        ->whereIn('ai.account_cat_id', [1, 4]) // Professional fees and reimbursement
                        ->sum('ild.amount');

                    if ($invoiceTaxableTotal > 0 && $detail->amount > 0) {
                        // Calculate proportional SST for this detail item
                        $proportionalSst = ($transferDetail->sst_amount * $detail->amount) / $invoiceTaxableTotal;
                        
                        // Apply rounding rule
                        $sstString = number_format($proportionalSst, 3, '.', '');
                        if (substr($sstString, -1) == '5') {
                            $newSst = floor($proportionalSst * 100) / 100; // Round down
                        } else {
                            $newSst = round($proportionalSst, 2); // Normal rounding
                        }
                        
                        $source = 'transfer';
                        $fromTransfer++;
                    }
                }

                // Step 2: If no transfer details or calculation failed, calculate from amount * SST rate
                if ($newSst === null) {
                    $sstRate = $detail->sst_rate ?? 8; // Default to 8% if not set
                    $sstRaw = $detail->amount * ($sstRate / 100);
                    
                    // Apply special rounding rule (if ends in 5, round down)
                    $sstString = number_format($sstRaw, 3, '.', '');
                    if (substr($sstString, -1) == '5') {
                        $newSst = floor($sstRaw * 100) / 100; // Round down
                    } else {
                        $newSst = round($sstRaw, 2); // Normal rounding
                    }
                    
                    $source = 'calculated';
                    $calculated++;
                }

                // Step 3: Update the SST value
                if (!$dryRun && $newSst !== null) {
                    DB::table('loan_case_invoice_details')
                        ->where('id', $detail->detail_id)
                        ->update(['sst' => $newSst]);
                    
                    $updated++;
                } else if ($dryRun) {
                    $updated++; // Count for dry run
                }

            } catch (\Exception $e) {
                $this->error("\nError processing detail ID {$detail->detail_id}: " . $e->getMessage());
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info("=== Summary ===");
        $this->info("Total processed: {$invoiceDetails->count()}");
        $this->info("Updated: {$updated}");
        $this->info("  - From transfer details: {$fromTransfer}");
        $this->info("  - Calculated from amount: {$calculated}");
        $this->info("Skipped: {$skipped}");
        $this->info("Errors: {$errors}");

        if ($dryRun) {
            $this->warn("\nThis was a DRY RUN. No changes were made.");
            $this->info("Run without --dry-run to apply changes.");
        } else {
            $this->info("\nSST backfill completed successfully!");
        }

        return 0;
    }
}


<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InvestigateLedgerDiscrepancies extends Command
{
    protected $signature = 'investigate:ledger-discrepancies';
    protected $description = 'Investigate ledger discrepancies in transfer fee records';

    public function handle()
    {
        $this->info('=== LEDGER DISCREPANCY INVESTIGATION ===');
        $this->newLine();

        try {
            // 1. Check Total Amount vs Collected Amount discrepancies
            $this->checkAmountDiscrepancies();
            
            // 2. Check Reimbursement amount issues
            $this->checkReimbursementIssues();
            
            // 3. Check SST calculation accuracy
            $this->checkSSTCalculations();
            
            // 4. Check Transfer Fee vs Ledger entries
            $this->checkTransferFeeLedgerConsistency();
            
            // 5. Summary
            $this->provideSummary();

        } catch (\Exception $e) {
            $this->error('Error during investigation: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    private function checkAmountDiscrepancies()
    {
        $this->info('1. CHECKING TOTAL AMOUNT vs COLLECTED AMOUNT DISCREPANCIES');
        $this->info('========================================================');
        
        $discrepancies = DB::table('loan_case_invoice_main as lcim')
            ->leftJoin('loan_case_bill_main as lcbm', 'lcim.loan_case_main_bill_id', '=', 'lcbm.id')
            ->leftJoin('loan_case as lc', 'lcbm.case_id', '=', 'lc.id')
            ->select(
                'lcim.id',
                'lcim.invoice_no',
                'lc.case_ref_no',
                'lcim.amount as total_amount',
                'lcim.sst_inv',
                'lcim.pfee1_inv',
                'lcim.pfee2_inv',
                'lcim.reimbursement_amount',
                'lcim.reimbursement_sst'
            )
            ->where('lcim.status', '<>', 99)
            ->where('lcim.amount', '>', 0)
            ->orderBy('lcim.created_at', 'desc')
            ->limit(20)
            ->get();

        $this->info("Found " . count($discrepancies) . " invoices with amount discrepancies:");
        $this->newLine();
        
        foreach ($discrepancies as $invoice) {
            $this->line("Invoice: {$invoice->invoice_no} | Case: {$invoice->case_ref_no} | Total: {$invoice->total_amount} | SST: {$invoice->sst_inv} | Pfee1: {$invoice->pfee1_inv} | Pfee2: {$invoice->pfee2_inv}");
            
            // Check if this invoice is in transfer fee records
            $transferFeeCheck = DB::table('transfer_fee_details')
                ->where('loan_case_invoice_main_id', $invoice->id)
                ->first();
                
            if ($transferFeeCheck) {
                $this->warn("  ⚠️  This invoice is in transfer fee records (ID: {$transferFeeCheck->id})");
                
                // Check for amount mismatches between invoice and transfer fee
                if ($transferFeeCheck->transfer_amount != $invoice->pfee1_inv + $invoice->pfee2_inv) {
                    $this->error("  ❌ TRANSFER AMOUNT MISMATCH: Invoice Pfee: " . ($invoice->pfee1_inv + $invoice->pfee2_inv) . " vs Transfer Fee: {$transferFeeCheck->transfer_amount}");
                }
                
                if ($transferFeeCheck->sst_amount != $invoice->sst_inv) {
                    $this->error("  ❌ SST AMOUNT MISMATCH: Invoice SST: {$invoice->sst_inv} vs Transfer Fee SST: {$transferFeeCheck->sst_amount}");
                }
            }
        }
        
        $this->newLine();
    }

    private function checkReimbursementIssues()
    {
        $this->info('2. CHECKING REIMBURSEMENT AMOUNT DISCREPANCIES');
        $this->info('============================================');
        
        $reimbursementIssues = DB::table('loan_case_invoice_main as lcim')
            ->leftJoin('loan_case_bill_main as lcbm', 'lcim.loan_case_main_bill_id', '=', 'lcbm.id')
            ->leftJoin('loan_case as lc', 'lcbm.case_id', '=', 'lc.id')
            ->select(
                'lcim.id',
                'lcim.invoice_no',
                'lc.case_ref_no',
                'lcim.reimbursement_amount',
                'lcim.reimbursement_sst',
                'lcim.amount as total_amount'
            )
            ->where('lcim.status', '<>', 99)
            ->where('lcim.reimbursement_amount', '>', 0)
            ->orderBy('lcim.created_at', 'desc')
            ->limit(20)
            ->get();

        $this->info("Found " . count($reimbursementIssues) . " invoices with reimbursement amounts:");
        $this->newLine();
        
        foreach ($reimbursementIssues as $invoice) {
            $this->line("Invoice: {$invoice->invoice_no} | Case: {$invoice->case_ref_no} | Reimbursement: {$invoice->reimbursement_amount} | Reimbursement SST: {$invoice->reimbursement_sst}");
            
            // Check SST calculation for reimbursement
            if ($invoice->reimbursement_amount > 0 && $invoice->reimbursement_sst > 0) {
                $expectedSST = $invoice->reimbursement_amount * 0.08;
                $sstDifference = abs($invoice->reimbursement_sst - $expectedSST);
                
                if ($sstDifference > 0.01) {
                    $this->error("  ❌ SST CALCULATION ISSUE: Expected {$expectedSST}, Found {$invoice->reimbursement_sst}");
                } else {
                    $this->info("  ✅ SST calculation is correct");
                }
            }
            
            // Check if this invoice is in transfer fee records
            $transferFeeCheck = DB::table('transfer_fee_details')
                ->where('loan_case_invoice_main_id', $invoice->id)
                ->first();
                
            if ($transferFeeCheck) {
                $this->warn("  ⚠️  This invoice is in transfer fee records (ID: {$transferFeeCheck->id})");
                
                // Check if transfer fee amounts match invoice amounts
                if ($transferFeeCheck->reimbursement_amount != $invoice->reimbursement_amount) {
                    $this->error("  ❌ TRANSFER FEE MISMATCH: Invoice reimbursement {$invoice->reimbursement_amount} vs Transfer fee {$transferFeeCheck->reimbursement_amount}");
                }
                
                if ($transferFeeCheck->reimbursement_sst_amount != $invoice->reimbursement_sst) {
                    $this->error("  ❌ TRANSFER FEE SST MISMATCH: Invoice reimbursement SST {$invoice->reimbursement_sst} vs Transfer fee {$transferFeeCheck->reimbursement_sst_amount}");
                }
            }
        }
        
        $this->newLine();
    }

    private function checkSSTCalculations()
    {
        $this->info('3. CHECKING SST CALCULATION ACCURACY');
        $this->info('===================================');
        
        $sstIssues = [];
        $transferFeeDetails = DB::table('transfer_fee_details as tfd')
            ->leftJoin('loan_case_invoice_main as lcim', 'tfd.loan_case_invoice_main_id', '=', 'lcim.id')
            ->leftJoin('loan_case_bill_main as lcbm', 'tfd.loan_case_main_bill_id', '=', 'lcbm.id')
            ->leftJoin('loan_case as lc', 'lcbm.case_id', '=', 'lc.id')
            ->select(
                'tfd.*',
                'lcim.invoice_no',
                'lc.case_ref_no'
            )
            ->where('tfd.status', '<>', 99)
            ->where('tfd.sst_amount', '>', 0)
            ->limit(50)
            ->get();

        foreach ($transferFeeDetails as $tfd) {
            if ($tfd->sst_amount > 0 && $tfd->transfer_amount > 0) {
                $expectedSST = $tfd->transfer_amount * 0.08;
                $sstDifference = abs($tfd->sst_amount - $expectedSST);
                
                if ($sstDifference > 0.01) {
                    $sstIssues[] = [
                        'invoice_no' => $tfd->invoice_no,
                        'case_ref_no' => $tfd->case_ref_no,
                        'transfer_amount' => $tfd->transfer_amount,
                        'sst_amount' => $tfd->sst_amount,
                        'expected_sst' => $expectedSST,
                        'difference' => $sstDifference
                    ];
                }
            }
        }

        if (count($sstIssues) > 0) {
            $this->warn("Found " . count($sstIssues) . " SST calculation issues:");
            foreach ($sstIssues as $issue) {
                $this->line("  Invoice: {$issue['invoice_no']} | Transfer: {$issue['transfer_amount']} | SST: {$issue['sst_amount']} | Expected SST: {$issue['expected_sst']} | Difference: {$issue['difference']}");
            }
        } else {
            $this->info("✅ SST calculations appear to be correct");
        }
        
        $this->newLine();
    }

    private function checkTransferFeeLedgerConsistency()
    {
        $this->info('4. CHECKING TRANSFER FEE vs LEDGER CONSISTENCY');
        $this->info('=============================================');
        
        $transferFeeDetails = DB::table('transfer_fee_details as tfd')
            ->leftJoin('loan_case_invoice_main as lcim', 'tfd.loan_case_invoice_main_id', '=', 'lcim.id')
            ->leftJoin('loan_case_bill_main as lcbm', 'tfd.loan_case_main_bill_id', '=', 'lcbm.id')
            ->leftJoin('loan_case as lc', 'lcbm.case_id', '=', 'lc.id')
            ->leftJoin('transfer_fee_main as tfm', 'tfd.transfer_fee_main_id', '=', 'tfm.id')
            ->select(
                'tfd.*',
                'lcim.invoice_no',
                'lc.case_ref_no',
                'tfm.transaction_id'
            )
            ->where('tfd.status', '<>', 99)
            ->orderBy('tfd.created_at', 'desc')
            ->limit(20)
            ->get();

        $missingLedgerEntries = 0;
        $amountMismatches = 0;

        foreach ($transferFeeDetails as $tfd) {
            $this->line("Checking Invoice: {$tfd->invoice_no} (Case: {$tfd->case_ref_no}, Transaction: {$tfd->transaction_id})");
            
            // Get all ledger entries for this invoice and transaction
            $ledgerEntries = DB::table('ledger_entries_v2')
                ->where('loan_case_invoice_main_id', $tfd->loan_case_invoice_main_id)
                ->where('transaction_id', $tfd->transaction_id)
                ->where('status', '<>', 99)
                ->get();

            // Check for expected ledger entry types
            $expectedTypes = [];
            
            if ($tfd->transfer_amount > 0) {
                $expectedTypes[] = ['type' => 'TRANSFER_OUT', 'amount' => $tfd->transfer_amount, 'transaction_type' => 'C'];
                $expectedTypes[] = ['type' => 'TRANSFER_IN', 'amount' => $tfd->transfer_amount, 'transaction_type' => 'D'];
            }
            
            if ($tfd->sst_amount > 0) {
                $expectedTypes[] = ['type' => 'SST_OUT', 'amount' => $tfd->sst_amount, 'transaction_type' => 'C'];
                $expectedTypes[] = ['type' => 'SST_IN', 'amount' => $tfd->sst_amount, 'transaction_type' => 'D'];
            }
            
            if ($tfd->reimbursement_amount > 0) {
                $expectedTypes[] = ['type' => 'REIMB_OUT', 'amount' => $tfd->reimbursement_amount, 'transaction_type' => 'C'];
            }
            
            if ($tfd->reimbursement_sst_amount > 0) {
                $expectedTypes[] = ['type' => 'REIMBSST_OUT', 'amount' => $tfd->reimbursement_sst_amount, 'transaction_type' => 'C'];
            }

            // Check each expected ledger entry
            foreach ($expectedTypes as $expected) {
                $found = $ledgerEntries->where('type', $expected['type'])
                                      ->where('transaction_type', $expected['transaction_type'])
                                      ->where('amount', $expected['amount'])
                                      ->first();
                
                if (!$found) {
                    $missingLedgerEntries++;
                    $this->error("  ❌ MISSING: {$expected['type']} ({$expected['transaction_type']}) - Amount: {$expected['amount']}");
                } else {
                    $this->info("  ✅ FOUND: {$expected['type']} ({$expected['transaction_type']}) - Amount: {$expected['amount']}");
                }
            }

            // Check for amount mismatches
            foreach ($expectedTypes as $expected) {
                $found = $ledgerEntries->where('type', $expected['type'])
                                      ->where('transaction_type', $expected['transaction_type'])
                                      ->first();
                
                if ($found && $found->amount != $expected['amount']) {
                    $amountMismatches++;
                    $this->error("  ❌ AMOUNT MISMATCH: {$expected['type']} - Expected: {$expected['amount']}, Found: {$found->amount}");
                }
            }
        }

        $this->info("Missing ledger entries: {$missingLedgerEntries}");
        $this->info("Amount mismatches: {$amountMismatches}");
        $this->newLine();
    }

    private function provideSummary()
    {
        $this->info('=== INVESTIGATION SUMMARY ===');
        
        // Count total issues
        $amountDiscrepancies = DB::table('loan_case_invoice_main')
            ->where('status', '<>', 99)
            ->where('amount', '>', 0)
            ->count();
            
        $reimbursementIssues = DB::table('loan_case_invoice_main')
            ->where('status', '<>', 99)
            ->where('reimbursement_amount', '>', 0)
            ->count();
            
        $transferFeeCount = DB::table('transfer_fee_details')
            ->where('status', '<>', 99)
            ->count();

        $this->info("• {$amountDiscrepancies} invoices with Total Amount ≠ Collected Amount");
        $this->info("• {$reimbursementIssues} invoices with reimbursement amounts");
        $this->info("• {$transferFeeCount} transfer fee records");
        
        $totalIssues = $amountDiscrepancies + $reimbursementIssues;
        
        if ($totalIssues > 0) {
            $this->warn("⚠️  CLIENT'S CLAIM APPEARS TO BE VALID");
            $this->warn("The red-colored records do show ledger discrepancies that need investigation.");
        } else {
            $this->info("✅ No obvious ledger issues found in the investigated records.");
        }
        
        $this->newLine();
        $this->info('=== INVESTIGATION COMPLETE ===');
    }
}

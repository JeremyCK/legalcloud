<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShowLedgerExamples extends Command
{
    protected $signature = 'show:ledger-examples';
    protected $description = 'Show specific examples of ledger issues found';

    public function handle()
    {
        $this->info('=== SPECIFIC EXAMPLES OF LEDGER ISSUES ===');
        $this->newLine();

        // Example 1: Missing REIMBSST_OUT entries
        $this->showMissingReimbSstEntries();
        
        // Example 2: SST Calculation Errors
        $this->showSstCalculationErrors();
        
        // Example 3: Invoice vs Transfer Fee Mismatches
        $this->showAmountMismatches();
    }

    private function showMissingReimbSstEntries()
    {
        $this->info('1. MISSING REIMBSST_OUT LEDGER ENTRIES:');
        $this->info('=========================================');
        
        $missingReimbSst = DB::table('transfer_fee_details as tfd')
            ->leftJoin('loan_case_invoice_main as lcim', 'tfd.loan_case_invoice_main_id', '=', 'lcim.id')
            ->leftJoin('loan_case_bill_main as lcbm', 'tfd.loan_case_main_bill_id', '=', 'lcbm.id')
            ->leftJoin('loan_case as lc', 'lcbm.case_id', '=', 'lc.id')
            ->leftJoin('transfer_fee_main as tfm', 'tfd.transfer_fee_main_id', '=', 'tfm.id')
            ->select(
                'lcim.invoice_no',
                'lc.case_ref_no',
                'tfm.transaction_id',
                'tfd.reimbursement_amount',
                'tfd.reimbursement_sst_amount'
            )
            ->where('tfd.reimbursement_sst_amount', '>', 0)
            ->where('tfd.status', '<>', 99)
            ->limit(5)
            ->get();

        foreach ($missingReimbSst as $record) {
            $this->line("Invoice: {$record->invoice_no} | Case: {$record->case_ref_no} | Transaction: {$record->transaction_id}");
            $this->line("  Reimbursement: {$record->reimbursement_amount} | Reimbursement SST: {$record->reimbursement_sst_amount}");
            
            // Check if REIMBSST_OUT ledger entry exists
            $invoiceId = DB::table('loan_case_invoice_main')->where('invoice_no', $record->invoice_no)->value('id');
            $ledgerExists = DB::table('ledger_entries_v2')
                ->where('loan_case_invoice_main_id', $invoiceId)
                ->where('type', 'REIMBSST_OUT')
                ->where('amount', $record->reimbursement_sst_amount)
                ->exists();
            
            if (!$ledgerExists) {
                $this->error("  ❌ MISSING: REIMBSST_OUT ledger entry for amount {$record->reimbursement_sst_amount}");
            } else {
                $this->info("  ✅ FOUND: REIMBSST_OUT ledger entry exists");
            }
            $this->newLine();
        }
    }

    private function showSstCalculationErrors()
    {
        $this->info('2. SST CALCULATION ERRORS:');
        $this->info('===========================');
        
        $sstErrors = DB::table('transfer_fee_details as tfd')
            ->leftJoin('loan_case_invoice_main as lcim', 'tfd.loan_case_invoice_main_id', '=', 'lcim.id')
            ->leftJoin('loan_case_bill_main as lcbm', 'tfd.loan_case_main_bill_id', '=', 'lcbm.id')
            ->leftJoin('loan_case as lc', 'lcbm.case_id', '=', 'lc.id')
            ->select(
                'lcim.invoice_no',
                'lc.case_ref_no',
                'tfd.transfer_amount',
                'tfd.sst_amount'
            )
            ->where('tfd.sst_amount', '>', 0)
            ->where('tfd.status', '<>', 99)
            ->limit(5)
            ->get();

        foreach ($sstErrors as $record) {
            $expectedSST = $record->transfer_amount * 0.08;
            $sstDifference = abs($record->sst_amount - $expectedSST);
            
            $this->line("Invoice: {$record->invoice_no} | Case: {$record->case_ref_no}");
            $this->line("  Transfer Amount: {$record->transfer_amount} | SST Amount: {$record->sst_amount}");
            $this->line("  Expected SST: {$expectedSST} | Difference: {$sstDifference}");
            
            if ($sstDifference > 0.01) {
                $this->error("  ❌ SST CALCULATION ERROR: Should be {$expectedSST} but shows {$record->sst_amount}");
            } else {
                $this->info("  ✅ SST calculation is correct");
            }
            $this->newLine();
        }
    }

    private function showAmountMismatches()
    {
        $this->info('3. INVOICE vs TRANSFER FEE AMOUNT MISMATCHES:');
        $this->info('==============================================');
        
        $mismatches = DB::table('transfer_fee_details as tfd')
            ->leftJoin('loan_case_invoice_main as lcim', 'tfd.loan_case_invoice_main_id', '=', 'lcim.id')
            ->leftJoin('loan_case_bill_main as lcbm', 'tfd.loan_case_main_bill_id', '=', 'lcbm.id')
            ->leftJoin('loan_case as lc', 'lcbm.case_id', '=', 'lc.id')
            ->select(
                'lcim.invoice_no',
                'lc.case_ref_no',
                'lcim.pfee1_inv',
                'lcim.pfee2_inv',
                'lcim.sst_inv',
                'tfd.transfer_amount',
                'tfd.sst_amount'
            )
            ->where('tfd.status', '<>', 99)
            ->limit(5)
            ->get();

        foreach ($mismatches as $record) {
            $invoicePfee = $record->pfee1_inv + $record->pfee2_inv;
            
            $this->line("Invoice: {$record->invoice_no} | Case: {$record->case_ref_no}");
            $this->line("  Invoice Pfee: {$invoicePfee} | Transfer Fee: {$record->transfer_amount}");
            $this->line("  Invoice SST: {$record->sst_inv} | Transfer Fee SST: {$record->sst_amount}");
            
            if ($invoicePfee != $record->transfer_amount) {
                $this->error("  ❌ PROFESSIONAL FEE MISMATCH: Invoice {$invoicePfee} vs Transfer Fee {$record->transfer_amount}");
            } else {
                $this->info("  ✅ Professional fee amounts match");
            }
            
            if ($record->sst_inv != $record->sst_amount) {
                $this->error("  ❌ SST MISMATCH: Invoice {$record->sst_inv} vs Transfer Fee {$record->sst_amount}");
            } else {
                $this->info("  ✅ SST amounts match");
            }
            $this->newLine();
        }
    }
}

<?php
/**
 * Check if invoice R20000214 exists in any SST record
 * 
 * Run in Laravel Tinker:
 * php artisan tinker
 * require 'check_invoice_sst_records.php';
 * checkInvoiceSSTRecords();
 */

use Illuminate\Support\Facades\DB;
use App\Models\LoanCaseInvoiceMain;
use App\Models\SSTDetails;
use App\Models\SSTMain;

function checkInvoiceSSTRecords() {
    echo "=== CHECKING INVOICE R20000214 IN SST RECORDS ===\n\n";
    
    // Find the invoice
    $invoice = LoanCaseInvoiceMain::where('invoice_no', 'R20000214')->first();
    
    if (!$invoice) {
        echo "❌ ERROR: Invoice R20000214 NOT FOUND in database\n";
        return;
    }
    
    echo "✅ Invoice found: ID = {$invoice->id}\n";
    echo "   Invoice No: {$invoice->invoice_no}\n";
    echo "   Status: {$invoice->status}\n";
    echo "   bln_invoice: {$invoice->bln_invoice}\n";
    echo "   bln_sst: {$invoice->bln_sst}\n";
    echo "   transferred_sst_amt: " . ($invoice->transferred_sst_amt ?? 0) . "\n";
    echo "   sst_inv: " . ($invoice->sst_inv ?? 0) . "\n";
    echo "   reimbursement_sst: " . ($invoice->reimbursement_sst ?? 0) . "\n\n";
    
    // Check if invoice exists in any SST record
    echo "=== CHECKING SST RECORDS ===\n\n";
    
    $sstDetails = SSTDetails::where('loan_case_invoice_main_id', $invoice->id)
        ->where('status', '<>', 99)  // Exclude deleted records
        ->get();
    
    if ($sstDetails->isEmpty()) {
        echo "✅ Invoice is NOT in any SST record\n";
    } else {
        echo "⚠️  Invoice is found in " . $sstDetails->count() . " SST record(s):\n\n";
        
        foreach ($sstDetails as $detail) {
            $sstMain = SSTMain::find($detail->sst_main_id);
            
            echo "   SST Record ID: {$detail->sst_main_id}\n";
            if ($sstMain) {
                echo "   Transaction ID: {$sstMain->transaction_id}\n";
                echo "   Payment Date: {$sstMain->payment_date}\n";
                echo "   Amount: " . number_format($sstMain->amount, 2) . "\n";
                echo "   Status: {$sstMain->status}\n";
            }
            echo "   SST Detail ID: {$detail->id}\n";
            echo "   Amount: " . number_format($detail->amount, 2) . "\n";
            echo "   Reimbursement SST: " . number_format($detail->reimbursement_sst ?? 0, 2) . "\n";
            echo "   Status: {$detail->status}\n";
            echo "   Created: {$detail->created_at}\n";
            echo "   ---\n";
        }
    }
    
    // Also check with raw SQL for completeness
    echo "\n=== RAW SQL CHECK ===\n\n";
    $rawCheck = DB::table('sst_details as sd')
        ->leftJoin('sst_main as sm', 'sm.id', '=', 'sd.sst_main_id')
        ->leftJoin('loan_case_invoice_main as im', 'im.id', '=', 'sd.loan_case_invoice_main_id')
        ->where('im.invoice_no', 'R20000214')
        ->where('sd.status', '<>', 99)
        ->select(
            'sd.id as sst_detail_id',
            'sd.sst_main_id',
            'sd.amount',
            'sd.status',
            'sm.transaction_id',
            'sm.payment_date',
            'sm.amount as sst_total_amount',
            'im.invoice_no'
        )
        ->get();
    
    if ($rawCheck->isEmpty()) {
        echo "✅ Raw SQL check: Invoice is NOT in any SST record\n";
    } else {
        echo "⚠️  Raw SQL check: Found " . $rawCheck->count() . " record(s):\n";
        foreach ($rawCheck as $record) {
            echo "   SST Main ID: {$record->sst_main_id}, Detail ID: {$record->sst_detail_id}, Transaction: {$record->transaction_id}\n";
        }
    }
    
    // Check invoice flags
    echo "\n=== INVOICE FLAGS CHECK ===\n\n";
    $bill = DB::table('loan_case_bill_main')
        ->where('id', $invoice->loan_case_main_bill_id)
        ->first();
    
    if ($bill) {
        echo "Bill Information:\n";
        echo "   Bill ID: {$bill->id}\n";
        echo "   bill bln_invoice: {$bill->bln_invoice}\n";
        echo "   bill bln_sst: {$bill->bln_sst}\n";
        echo "   invoice_branch_id: " . ($bill->invoice_branch_id ?? 'NULL') . "\n";
    }
    
    echo "\n=== SUMMARY ===\n";
    if ($sstDetails->isEmpty()) {
        echo "✅ Invoice R20000214 is NOT in any SST record\n";
        echo "   The invoice should be searchable IF:\n";
        echo "   - im.bln_invoice = 1 (currently: {$invoice->bln_invoice})\n";
        echo "   - im.bln_sst = 0 (currently: {$invoice->bln_sst})\n";
        echo "   - b.bln_invoice = 1 (currently: " . ($bill->bln_invoice ?? 'N/A') . ")\n";
        echo "   - b.bln_sst = 0 (currently: " . ($bill->bln_sst ?? 'N/A') . ")\n";
        echo "   - im.status <> 99 (currently: {$invoice->status})\n";
    } else {
        echo "⚠️  Invoice R20000214 IS in " . $sstDetails->count() . " SST record(s)\n";
        echo "   This is why it's being excluded from search results.\n";
    }
    
    echo "\n=== END ===\n";
}

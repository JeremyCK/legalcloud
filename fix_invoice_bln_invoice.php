<?php
/**
 * Fix bln_invoice for invoice R20000214
 * 
 * Run in Laravel Tinker:
 * php artisan tinker
 * require 'fix_invoice_bln_invoice.php';
 * fixInvoiceBlnInvoice('R20000214');
 */

use Illuminate\Support\Facades\DB;
use App\Models\LoanCaseInvoiceMain;
use App\Models\LoanCaseBillMain;

function fixInvoiceBlnInvoice($invoiceNo) {
    echo "=== FIXING bln_invoice FOR INVOICE {$invoiceNo} ===\n\n";
    
    // Find the invoice
    $invoice = LoanCaseInvoiceMain::where('invoice_no', $invoiceNo)->first();
    
    if (!$invoice) {
        echo "❌ ERROR: Invoice {$invoiceNo} NOT FOUND in database\n";
        return false;
    }
    
    echo "✅ Invoice found: ID = {$invoice->id}\n";
    echo "   Current bln_invoice: {$invoice->bln_invoice}\n\n";
    
    // Get the bill
    $bill = LoanCaseBillMain::find($invoice->loan_case_main_bill_id);
    
    if (!$bill) {
        echo "❌ ERROR: Bill NOT FOUND (ID: {$invoice->loan_case_main_bill_id})\n";
        return false;
    }
    
    echo "✅ Bill found: ID = {$bill->id}\n";
    echo "   Bill bln_invoice: {$bill->bln_invoice}\n\n";
    
    if ($invoice->bln_invoice == $bill->bln_invoice) {
        echo "✅ Invoice bln_invoice already matches bill bln_invoice. No update needed.\n";
        return true;
    }
    
    echo "⚠️  MISMATCH DETECTED:\n";
    echo "   Invoice bln_invoice: {$invoice->bln_invoice}\n";
    echo "   Bill bln_invoice: {$bill->bln_invoice}\n\n";
    
    // Update invoice to match bill
    $invoice->bln_invoice = $bill->bln_invoice;
    $invoice->save();
    
    echo "✅ UPDATED: Invoice bln_invoice set to {$bill->bln_invoice}\n";
    echo "   Invoice should now appear in SST V2 search.\n\n";
    
    return true;
}

// Also provide a function to sync all mismatched invoices
function syncAllBlnInvoice() {
    echo "=== SYNCING ALL MISMATCHED bln_invoice VALUES ===\n\n";
    
    $mismatched = DB::table('loan_case_invoice_main as im')
        ->join('loan_case_bill_main as bm', 'im.loan_case_main_bill_id', '=', 'bm.id')
        ->where('im.status', '<>', 99)
        ->where('bm.status', '<>', 99)
        ->whereColumn('im.bln_invoice', '!=', 'bm.bln_invoice')
        ->select('im.id', 'im.invoice_no', 'im.bln_invoice as invoice_bln', 'bm.bln_invoice as bill_bln')
        ->get();
    
    if ($mismatched->isEmpty()) {
        echo "✅ No mismatches found. All invoices are in sync.\n";
        return 0;
    }
    
    echo "Found " . $mismatched->count() . " mismatched invoice(s):\n\n";
    
    $updated = 0;
    foreach ($mismatched as $item) {
        echo "   Invoice {$item->invoice_no} (ID: {$item->id}): {$item->invoice_bln} -> {$item->bill_bln}\n";
        
        LoanCaseInvoiceMain::where('id', $item->id)
            ->update(['bln_invoice' => $item->bill_bln]);
        
        $updated++;
    }
    
    echo "\n✅ Updated {$updated} invoice(s)\n";
    return $updated;
}

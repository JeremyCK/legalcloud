<?php
// Quick debug script for invoice 20002059
// Run this in Laravel Tinker: php artisan tinker
// Then copy and paste this entire code block

use Illuminate\Support\Facades\DB;

echo "=== QUICK DEBUG: Invoice 20002059 ===\n\n";

// Check invoice in loan_case_invoice_main
$invoice = DB::table('loan_case_invoice_main')->where('invoice_no', '20002059')->where('status', '<>', 99)->first();
echo "1. INVOICE TABLE:\n";
if ($invoice) {
    echo "✅ Found: ID={$invoice->id}, Status={$invoice->status}, BillID={$invoice->loan_case_main_bill_id}, SST={$invoice->sst_inv}, BlnSST={$invoice->bln_sst}\n";
} else {
    echo "❌ Not found in invoice table\n";
}

// Check bill in loan_case_bill_main  
$bill = DB::table('loan_case_bill_main')->where('invoice_no', '20002059')->where('status', '<>', 99)->first();
echo "\n2. BILL TABLE:\n";
if ($bill) {
    echo "✅ Found: ID={$bill->id}, Status={$bill->status}, BlnInvoice={$bill->bln_invoice}, BlnSST={$bill->bln_sst}, Branch={$bill->invoice_branch_id}\n";
} else {
    echo "❌ Not found in bill table\n";
}

// Check relationship
echo "\n3. RELATIONSHIP:\n";
if ($invoice && $bill) {
    if ($invoice->loan_case_main_bill_id == $bill->id) {
        echo "✅ Perfect match: Invoice BillID={$invoice->loan_case_main_bill_id} = Bill ID={$bill->id}\n";
    } else {
        echo "❌ Mismatch: Invoice BillID={$invoice->loan_case_main_bill_id} ≠ Bill ID={$bill->id}\n";
    }
} else {
    echo "❌ Cannot check relationship - missing data\n";
}

// Check why original SST doesn't show it
echo "\n4. ORIGINAL SST CONDITIONS:\n";
if ($bill) {
    $conditions = [];
    $conditions[] = $bill->status != 99 ? "✅ Status OK" : "❌ Status = 99";
    $conditions[] = $bill->bln_invoice == 1 ? "✅ Billable" : "❌ Not billable";
    $conditions[] = $bill->bln_sst == 0 ? "✅ Not transferred" : "❌ Already transferred";
    echo implode("\n   ", $conditions) . "\n";
} else {
    echo "❌ No bill record - that's why original SST doesn't show it!\n";
}

// Check why SST v2 shows it
echo "\n5. SST V2 CONDITIONS:\n";
if ($invoice) {
    $conditions = [];
    $conditions[] = $invoice->status != 99 ? "✅ Status OK" : "❌ Status = 99";
    $conditions[] = $invoice->transferred_to_office_bank == 0 ? "✅ Not fully transferred" : "❌ Fully transferred";
    $conditions[] = $invoice->loan_case_main_bill_id ? "✅ Has bill ID" : "❌ No bill ID";
    $conditions[] = $invoice->sst_inv > 0 ? "✅ Has SST amount" : "❌ No SST amount";
    $conditions[] = $invoice->bln_sst == 0 ? "✅ Not SST transferred" : "❌ Already SST transferred";
    echo implode("\n   ", $conditions) . "\n";
} else {
    echo "❌ No invoice record\n";
}

echo "\n=== SUMMARY ===\n";
if (!$bill && $invoice) {
    echo "🎯 ISSUE FOUND: Invoice exists but NO corresponding bill record!\n";
    echo "   - Original SST looks at bill table → finds nothing\n";
    echo "   - SST v2 looks at invoice table → finds invoice\n";
} elseif ($bill && $invoice) {
    echo "🎯 Both records exist - checking conditions...\n";
    if ($bill->bln_sst == 1) {
        echo "   - Bill already transferred (bln_sst=1) → Original SST excludes it\n";
    }
    if ($bill->bln_invoice != 1) {
        echo "   - Bill not billable (bln_invoice≠1) → Original SST excludes it\n";
    }
} else {
    echo "🎯 Neither record exists - check invoice number spelling\n";
}

echo "\nRun this script and share the output!\n";

<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING INVOICE 20002059 ===\n\n";

// Check invoice in loan_case_invoice_main
echo "1. Checking loan_case_invoice_main:\n";
$invoice = DB::table('loan_case_invoice_main')
    ->where('invoice_no', '20002059')
    ->where('status', '<>', 99)
    ->first();

if ($invoice) {
    echo "✅ Found invoice:\n";
    echo "   ID: {$invoice->id}\n";
    echo "   Status: {$invoice->status}\n";
    echo "   Bill ID: {$invoice->loan_case_main_bill_id}\n";
    echo "   SST Inv: {$invoice->sst_inv}\n";
    echo "   Bln SST: {$invoice->bln_sst}\n";
    echo "   Transferred SST: {$invoice->transferred_sst_amt}\n";
} else {
    echo "❌ Invoice not found\n";
}

// Check bill in loan_case_bill_main
echo "\n2. Checking loan_case_bill_main:\n";
$bill = DB::table('loan_case_bill_main')
    ->where('invoice_no', '20002059')
    ->where('status', '<>', 99)
    ->first();

if ($bill) {
    echo "✅ Found bill:\n";
    echo "   ID: {$bill->id}\n";
    echo "   Status: {$bill->status}\n";
    echo "   Bln Invoice: {$bill->bln_invoice}\n";
    echo "   Bln SST: {$bill->bln_sst}\n";
    echo "   Branch: {$bill->invoice_branch_id}\n";
} else {
    echo "❌ Bill not found\n";
}

// Check relationship
echo "\n3. Relationship check:\n";
if ($invoice && $bill) {
    if ($invoice->loan_case_main_bill_id == $bill->id) {
        echo "✅ Perfect match: Invoice BillID = Bill ID\n";
    } else {
        echo "❌ Mismatch: Invoice BillID ({$invoice->loan_case_main_bill_id}) ≠ Bill ID ({$bill->id})\n";
    }
} else {
    echo "❌ Cannot check relationship - missing data\n";
}

// Summary
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

echo "\n=== INVESTIGATION COMPLETE ===\n";

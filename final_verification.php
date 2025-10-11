<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CaseController;

echo "=== FINAL VERIFICATION AFTER FIX ===\n\n";

$billId = 8377;

// Get current state before running updatePfeeDisbAmountINVFromDetails
echo "=== BEFORE RUNNING updatePfeeDisbAmountINVFromDetails ===\n";
$billBefore = DB::table('loan_case_bill_main')
    ->where('id', $billId)
    ->first();

$invoicesBefore = DB::table('loan_case_invoice_main')
    ->where('loan_case_main_bill_id', $billId)
    ->where('status', '<>', 99)
    ->get();

echo "Bill total_amt_inv: {$billBefore->total_amt_inv}\n";
echo "Bill pfee1_inv: {$billBefore->pfee1_inv}\n";
echo "Bill pfee2_inv: {$billBefore->pfee2_inv}\n";
echo "Bill sst_inv: {$billBefore->sst_inv}\n\n";

foreach ($invoicesBefore as $invoice) {
    echo "Invoice {$invoice->invoice_no}:\n";
    echo "  Amount: {$invoice->amount}\n";
    echo "  Pfee1: {$invoice->pfee1_inv}\n";
    echo "  Pfee2: {$invoice->pfee2_inv}\n";
    echo "  SST: {$invoice->sst_inv}\n";
}

// Check Letter of Authorisation items
echo "\n=== LETTER OF AUTHORISATION ITEMS ===\n";
$letterAuthItems = DB::table('loan_case_invoice_details as ild')
    ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
    ->leftJoin('loan_case_invoice_main as im', 'ild.invoice_main_id', '=', 'im.id')
    ->where('im.loan_case_main_bill_id', $billId)
    ->where('ai.name', 'LIKE', '%Letter of Authorisation%')
    ->where('ild.status', '<>', 99)
    ->select('ild.*', 'ai.name as item_name', 'im.invoice_no')
    ->get();

foreach ($letterAuthItems as $item) {
    echo "Invoice {$item->invoice_no} - Letter of Authorisation:\n";
    echo "  Amount: {$item->amount}\n";
    echo "  Original Amount: {$item->ori_invoice_amt}\n";
}

echo "\n=== RUNNING updatePfeeDisbAmountINVFromDetails ===\n";
$controller = new CaseController();
$controller->updatePfeeDisbAmountINVFromDetails($billId);
echo "Method executed successfully!\n\n";

// Get current state after running updatePfeeDisbAmountINVFromDetails
echo "=== AFTER RUNNING updatePfeeDisbAmountINVFromDetails ===\n";
$billAfter = DB::table('loan_case_bill_main')
    ->where('id', $billId)
    ->first();

$invoicesAfter = DB::table('loan_case_invoice_main')
    ->where('loan_case_main_bill_id', $billId)
    ->where('status', '<>', 99)
    ->get();

echo "Bill total_amt_inv: {$billAfter->total_amt_inv}\n";
echo "Bill pfee1_inv: {$billAfter->pfee1_inv}\n";
echo "Bill pfee2_inv: {$billAfter->pfee2_inv}\n";
echo "Bill sst_inv: {$billAfter->sst_inv}\n\n";

foreach ($invoicesAfter as $invoice) {
    echo "Invoice {$invoice->invoice_no}:\n";
    echo "  Amount: {$invoice->amount}\n";
    echo "  Pfee1: {$invoice->pfee1_inv}\n";
    echo "  Pfee2: {$invoice->pfee2_inv}\n";
    echo "  SST: {$invoice->sst_inv}\n";
}

echo "\n=== COMPARISON ===\n";
echo "Bill total_amt_inv: {$billBefore->total_amt_inv} → {$billAfter->total_amt_inv}\n";
echo "Bill pfee1_inv: {$billBefore->pfee1_inv} → {$billAfter->pfee1_inv}\n";
echo "Bill pfee2_inv: {$billBefore->pfee2_inv} → {$billAfter->pfee2_inv}\n";
echo "Bill sst_inv: {$billBefore->sst_inv} → {$billAfter->sst_inv}\n\n";

foreach ($invoicesBefore as $index => $invoiceBefore) {
    $invoiceAfter = $invoicesAfter[$index];
    echo "Invoice {$invoiceBefore->invoice_no}:\n";
    echo "  Amount: {$invoiceBefore->amount} → {$invoiceAfter->amount}\n";
    echo "  Pfee1: {$invoiceBefore->pfee1_inv} → {$invoiceAfter->pfee1_inv}\n";
    echo "  Pfee2: {$invoiceBefore->pfee2_inv} → {$invoiceAfter->pfee2_inv}\n";
    echo "  SST: {$invoiceBefore->sst_inv} → {$invoiceAfter->sst_inv}\n";
}

echo "\n=== FINAL RESULT ===\n";
$expectedTotal = 2500.00; // This is what you expected
$actualTotal = $billAfter->total_amt_inv;

echo "Expected Bill Total: {$expectedTotal}\n";
echo "Actual Bill Total: {$actualTotal}\n";
echo "Difference: " . ($actualTotal - $expectedTotal) . "\n\n";

if (abs($actualTotal - $expectedTotal) < 0.01) {
    echo "✅ SUCCESS! The bill total now matches the expected amount!\n";
} else {
    echo "⚠️  The bill total is still different from expected.\n";
    echo "This might be due to other items having different amounts between the invoices.\n";
}

echo "\n=== LETTER OF AUTHORISATION VERIFICATION ===\n";
$letterAuthItemsAfter = DB::table('loan_case_invoice_details as ild')
    ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
    ->leftJoin('loan_case_invoice_main as im', 'ild.invoice_main_id', '=', 'im.id')
    ->where('im.loan_case_main_bill_id', $billId)
    ->where('ai.name', 'LIKE', '%Letter of Authorisation%')
    ->where('ild.status', '<>', 99)
    ->select('ild.*', 'ai.name as item_name', 'im.invoice_no')
    ->get();

foreach ($letterAuthItemsAfter as $item) {
    echo "Invoice {$item->invoice_no} - Letter of Authorisation:\n";
    echo "  Amount: {$item->amount}\n";
    echo "  Original Amount: {$item->ori_invoice_amt}\n";
    
    if ($item->amount == 21.30) {
        echo "  ✅ Correct amount!\n";
    } else {
        echo "  ❌ Still incorrect!\n";
    }
}

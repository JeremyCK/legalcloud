<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CaseController;

echo "=== TESTING updatePfeeDisbAmountINVFromDetails METHOD ===\n\n";

$billId = 8377;

// Get current state BEFORE running the method
echo "=== BEFORE UPDATE ===\n";
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

echo "\n=== RUNNING updatePfeeDisbAmountINVFromDetails ===\n";

// Create controller instance and run the method
$controller = new CaseController();
$controller->updatePfeeDisbAmountINVFromDetails($billId);

echo "Method executed successfully!\n\n";

// Get current state AFTER running the method
echo "=== AFTER UPDATE ===\n";
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

// Check if any changes were made
$billChanged = ($billBefore->total_amt_inv != $billAfter->total_amt_inv) ||
               ($billBefore->pfee1_inv != $billAfter->pfee1_inv) ||
               ($billBefore->pfee2_inv != $billAfter->pfee2_inv) ||
               ($billBefore->sst_inv != $billAfter->sst_inv);

$invoiceChanged = false;
foreach ($invoicesBefore as $index => $invoiceBefore) {
    $invoiceAfter = $invoicesAfter[$index];
    if (($invoiceBefore->amount != $invoiceAfter->amount) ||
        ($invoiceBefore->pfee1_inv != $invoiceAfter->pfee1_inv) ||
        ($invoiceBefore->pfee2_inv != $invoiceAfter->pfee2_inv) ||
        ($invoiceBefore->sst_inv != $invoiceAfter->sst_inv)) {
        $invoiceChanged = true;
        break;
    }
}

echo "\n=== RESULT ===\n";
if ($billChanged || $invoiceChanged) {
    echo "✅ CHANGES DETECTED - Method is working!\n";
} else {
    echo "❌ NO CHANGES DETECTED - Method may not be working properly!\n";
}

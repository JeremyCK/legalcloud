<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Updating SST Details Invoice IDs ===\n";

// Step 1: Check current state
$currentState = DB::table('sst_details')
    ->selectRaw('
        COUNT(*) as total_records,
        COUNT(loan_case_invoice_main_id) as records_with_invoice_id,
        COUNT(loan_case_main_bill_id) as records_with_bill_id
    ')
    ->first();

echo "Before update:\n";
echo "- Total records: " . $currentState->total_records . "\n";
echo "- Records with invoice_id: " . $currentState->records_with_invoice_id . "\n";
echo "- Records with bill_id: " . $currentState->records_with_bill_id . "\n\n";

// Step 2: Show sample data before update
echo "Sample data before update:\n";
$sampleBefore = DB::table('sst_details as sd')
    ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'sd.loan_case_main_bill_id')
    ->leftJoin('loan_case_invoice_main as im', 'im.loan_case_main_bill_id', '=', 'b.id')
    ->whereNull('sd.loan_case_invoice_main_id')
    ->select('sd.id', 'sd.loan_case_main_bill_id', 'sd.loan_case_invoice_main_id', 'b.id as bill_id', 'im.id as invoice_id', 'im.invoice_no')
    ->limit(5)
    ->get();

foreach ($sampleBefore as $record) {
    echo "SST Detail ID: {$record->id}, Bill ID: {$record->loan_case_main_bill_id}, Invoice ID: {$record->invoice_id}, Invoice No: {$record->invoice_no}\n";
}

// Step 3: Perform the update
echo "\nPerforming update...\n";

$updated = DB::statement("
    UPDATE sst_details sd
    INNER JOIN loan_case_bill_main b ON b.id = sd.loan_case_main_bill_id
    INNER JOIN loan_case_invoice_main im ON im.loan_case_main_bill_id = b.id
    SET sd.loan_case_invoice_main_id = im.id
    WHERE sd.loan_case_invoice_main_id IS NULL
      AND sd.loan_case_main_bill_id IS NOT NULL
");

echo "Update completed: " . ($updated ? "Success" : "Failed") . "\n\n";

// Step 4: Check results after update
$afterState = DB::table('sst_details')
    ->selectRaw('
        COUNT(*) as total_records,
        COUNT(loan_case_invoice_main_id) as records_with_invoice_id,
        COUNT(loan_case_main_bill_id) as records_with_bill_id
    ')
    ->first();

echo "After update:\n";
echo "- Total records: " . $afterState->total_records . "\n";
echo "- Records with invoice_id: " . $afterState->records_with_invoice_id . "\n";
echo "- Records with bill_id: " . $afterState->records_with_bill_id . "\n\n";

// Step 5: Show sample data after update
echo "Sample data after update:\n";
$sampleAfter = DB::table('sst_details as sd')
    ->leftJoin('loan_case_invoice_main as im', 'im.id', '=', 'sd.loan_case_invoice_main_id')
    ->whereNotNull('sd.loan_case_invoice_main_id')
    ->select('sd.id', 'sd.loan_case_invoice_main_id', 'im.invoice_no', 'im.Invoice_date', 'im.amount')
    ->limit(5)
    ->get();

foreach ($sampleAfter as $record) {
    echo "SST Detail ID: {$record->id}, Invoice ID: {$record->loan_case_invoice_main_id}, Invoice No: {$record->invoice_no}, Date: {$record->Invoice_date}, Amount: {$record->amount}\n";
}

echo "\n=== Update Complete ===\n";

?>

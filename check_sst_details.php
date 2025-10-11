<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING SST_DETAILS TABLE STRUCTURE ===\n\n";

try {
    // Check current table structure
    echo "1. Current sst_details table structure:\n";
    $columns = DB::select('DESCRIBE sst_details');
    
    foreach ($columns as $column) {
        echo "   {$column->Field} - {$column->Type}\n";
    }
    
    // Check if loan_case_invoice_main_id column exists
    echo "\n2. Checking for loan_case_invoice_main_id column:\n";
    $hasInvoiceColumn = false;
    $hasBillColumn = false;
    
    foreach ($columns as $column) {
        if ($column->Field === 'loan_case_invoice_main_id') {
            $hasInvoiceColumn = true;
            echo "   ✅ loan_case_invoice_main_id exists\n";
        }
        if ($column->Field === 'loan_case_main_bill_id') {
            $hasBillColumn = true;
            echo "   ✅ loan_case_main_bill_id exists\n";
        }
    }
    
    if (!$hasInvoiceColumn) {
        echo "   ❌ loan_case_invoice_main_id column missing\n";
    }
    if (!$hasBillColumn) {
        echo "   ❌ loan_case_main_bill_id column missing\n";
    }
    
    // Show sample data
    echo "\n3. Sample sst_details records:\n";
    $sampleRecords = DB::table('sst_details')->limit(3)->get();
    if ($sampleRecords->count() > 0) {
        foreach ($sampleRecords as $record) {
            echo "   ID: {$record->id}, SST Main ID: {$record->sst_main_id}\n";
            if (isset($record->loan_case_main_bill_id)) {
                echo "   Bill ID: {$record->loan_case_main_bill_id}\n";
            }
            if (isset($record->loan_case_invoice_main_id)) {
                echo "   Invoice ID: {$record->loan_case_invoice_main_id}\n";
            }
            echo "   Amount: {$record->amount}\n\n";
        }
    } else {
        echo "   No records found\n";
    }
    
    echo "\n=== ANALYSIS COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

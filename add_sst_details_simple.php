<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== ADDING LOAN_CASE_INVOICE_MAIN_ID COLUMN ===\n\n";

try {
    // Check if column already exists
    echo "1. Checking if column exists...\n";
    $columns = DB::select('DESCRIBE sst_details');
    $hasColumn = false;
    
    foreach ($columns as $column) {
        if ($column->Field === 'loan_case_invoice_main_id') {
            $hasColumn = true;
            break;
        }
    }
    
    if ($hasColumn) {
        echo "✅ Column loan_case_invoice_main_id already exists.\n";
    } else {
        echo "❌ Column missing. Adding it now...\n";
        
        // Add the column
        DB::statement("ALTER TABLE `sst_details` 
            ADD COLUMN `loan_case_invoice_main_id` BIGINT UNSIGNED NULL 
            COMMENT 'Reference to loan_case_invoice_main table for SST v2' 
            AFTER `loan_case_main_bill_id`");
        
        echo "✅ Column added successfully.\n";
        
        // Add index
        try {
            DB::statement("ALTER TABLE `sst_details` 
                ADD INDEX `idx_sst_details_invoice_id` (`loan_case_invoice_main_id`)");
            echo "✅ Index added successfully.\n";
        } catch (Exception $e) {
            echo "⚠️  Index error (may already exist): " . $e->getMessage() . "\n";
        }
    }
    
    // Verify the changes
    echo "\n2. Verifying table structure...\n";
    $newColumns = DB::select('DESCRIBE sst_details');
    
    foreach ($newColumns as $column) {
        if ($column->Field === 'loan_case_invoice_main_id') {
            echo "✅ loan_case_invoice_main_id: {$column->Type}\n";
        }
        if ($column->Field === 'loan_case_main_bill_id') {
            echo "✅ loan_case_main_bill_id: {$column->Type}\n";
        }
    }
    
    echo "\n✅ SST v2 can now save invoice references!\n";
    echo "The sst_details table now supports both:\n";
    echo "  - SST v1: loan_case_main_bill_id (original)\n";
    echo "  - SST v2: loan_case_invoice_main_id (new)\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

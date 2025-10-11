<?php
/**
 * Script to add loan_case_invoice_main_id column to sst_details table
 * This is needed for SST v2 system to work with invoices instead of bills
 * 
 * Run this script from Laravel tinker or create a command
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Starting sst_details table update process...\n";

try {
    // Step 1: Check if column already exists
    echo "Step 1: Checking if loan_case_invoice_main_id column exists...\n";
    
    if (Schema::hasColumn('sst_details', 'loan_case_invoice_main_id')) {
        echo "⚠️  Column loan_case_invoice_main_id already exists.\n";
    } else {
        echo "Step 2: Adding loan_case_invoice_main_id column...\n";
        
        DB::statement("ALTER TABLE `sst_details` 
            ADD COLUMN `loan_case_invoice_main_id` BIGINT UNSIGNED NULL 
            COMMENT 'Reference to loan_case_invoice_main table for SST v2' 
            AFTER `loan_case_main_bill_id`");
        
        echo "✅ Column loan_case_invoice_main_id added successfully.\n";
    }

    // Step 3: Add index for better performance
    echo "Step 3: Adding index for performance...\n";
    
    try {
        DB::statement("ALTER TABLE `sst_details` 
            ADD INDEX `idx_sst_details_invoice_id` (`loan_case_invoice_main_id`)");
        echo "✅ Index added successfully.\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "⚠️  Index already exists.\n";
        } else {
            echo "❌ Error adding index: " . $e->getMessage() . "\n";
        }
    }

    // Step 4: Verify the changes
    echo "Step 4: Verifying table structure...\n";
    
    $columns = DB::select('DESCRIBE sst_details');
    $hasInvoiceColumn = false;
    $hasBillColumn = false;
    
    foreach ($columns as $column) {
        if ($column->Field === 'loan_case_invoice_main_id') {
            $hasInvoiceColumn = true;
        }
        if ($column->Field === 'loan_case_main_bill_id') {
            $hasBillColumn = true;
        }
    }
    
    echo "✅ Table structure verification:\n";
    echo "   loan_case_main_bill_id: " . ($hasBillColumn ? "✅ Exists" : "❌ Missing") . "\n";
    echo "   loan_case_invoice_main_id: " . ($hasInvoiceColumn ? "✅ Exists" : "❌ Missing") . "\n";

    // Step 5: Show updated table structure
    echo "\nStep 5: Updated table structure:\n";
    foreach ($columns as $column) {
        echo "   {$column->Field} - {$column->Type}" . 
             ($column->Null === 'YES' ? ' (NULL)' : ' (NOT NULL)') . 
             ($column->Default ? " DEFAULT {$column->Default}" : '') . "\n";
    }

    echo "\n✅ Script completed successfully!\n";
    echo "The sst_details table now supports both SST v1 (bills) and SST v2 (invoices)!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Please check the error and run the script again.\n";
}

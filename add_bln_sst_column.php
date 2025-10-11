<?php
/**
 * Script to add bln_sst column to loan_case_invoice_main table
 * and update it based on matching invoice numbers with loan_case_bill_main
 * 
 * Run this script from Laravel tinker or create a command
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Starting bln_sst column addition and update process...\n";

try {
    // Step 1: Add the bln_sst column to loan_case_invoice_main table
    echo "Step 1: Adding bln_sst column to loan_case_invoice_main table...\n";
    
    if (!Schema::hasColumn('loan_case_invoice_main', 'bln_sst')) {
        DB::statement("ALTER TABLE `loan_case_invoice_main` 
            ADD COLUMN `bln_sst` TINYINT(1) NOT NULL DEFAULT 0 
            COMMENT 'SST transfer flag: 0=not transferred, 1=transferred' 
            AFTER `transferred_sst_amt`");
        echo "✅ Column bln_sst added successfully.\n";
    } else {
        echo "⚠️  Column bln_sst already exists.\n";
    }

    // Step 2: Update bln_sst in loan_case_invoice_main based on matching invoice numbers
    echo "Step 2: Updating bln_sst based on matching invoice numbers...\n";
    
    $updatedCount = DB::update("
        UPDATE `loan_case_invoice_main` im
        INNER JOIN `loan_case_bill_main` bm ON im.invoice_no = bm.invoice_no
        SET im.bln_sst = 1
        WHERE bm.bln_sst = 1 
        AND im.status <> 99 
        AND bm.status <> 99
    ");
    
    echo "✅ Updated {$updatedCount} records based on matching invoice numbers.\n";

    // Step 3: Update bln_sst in loan_case_invoice_main based on existing SST transfers
    echo "Step 3: Updating bln_sst based on existing transferred_sst_amt...\n";
    
    $updatedCount2 = DB::update("
        UPDATE `loan_case_invoice_main` 
        SET `bln_sst` = 1 
        WHERE `transferred_sst_amt` > 0 
        AND `status` <> 99
    ");
    
    echo "✅ Updated {$updatedCount2} records based on existing transferred_sst_amt.\n";

    // Step 4: Get statistics
    echo "Step 4: Getting statistics...\n";
    
    $billStats = DB::select("
        SELECT 
            COUNT(*) as total_records,
            SUM(CASE WHEN bln_sst = 1 THEN 1 ELSE 0 END) as transferred_count,
            SUM(CASE WHEN bln_sst = 0 THEN 1 ELSE 0 END) as not_transferred_count
        FROM `loan_case_bill_main` 
        WHERE status <> 99
    ")[0];
    
    $invoiceStats = DB::select("
        SELECT 
            COUNT(*) as total_records,
            SUM(CASE WHEN bln_sst = 1 THEN 1 ELSE 0 END) as transferred_count,
            SUM(CASE WHEN bln_sst = 0 THEN 1 ELSE 0 END) as not_transferred_count
        FROM `loan_case_invoice_main` 
        WHERE status <> 99
    ")[0];
    
    echo "\n📊 Statistics:\n";
    echo "loan_case_bill_main: {$billStats->total_records} total, {$billStats->transferred_count} transferred, {$billStats->not_transferred_count} not transferred\n";
    echo "loan_case_invoice_main: {$invoiceStats->total_records} total, {$invoiceStats->transferred_count} transferred, {$invoiceStats->not_transferred_count} not transferred\n";

    // Step 5: Show sample of updated records
    echo "\nStep 5: Sample of updated records:\n";
    
    $sampleRecords = DB::select("
        SELECT 
            im.id,
            im.invoice_no,
            im.bln_sst,
            im.transferred_sst_amt,
            bm.id as bill_id,
            bm.bln_sst as bill_bln_sst
        FROM `loan_case_invoice_main` im
        LEFT JOIN `loan_case_bill_main` bm ON im.invoice_no = bm.invoice_no
        WHERE im.status <> 99
        ORDER BY im.id DESC
        LIMIT 10
    ");
    
    echo "ID\tInvoice No\tInvoice bln_sst\tTransferred SST\tBill ID\tBill bln_sst\n";
    echo str_repeat("-", 80) . "\n";
    
    foreach ($sampleRecords as $record) {
        echo "{$record->id}\t{$record->invoice_no}\t{$record->bln_sst}\t\t{$record->transferred_sst_amt}\t\t{$record->bill_id}\t{$record->bill_bln_sst}\n";
    }

    echo "\n✅ Script completed successfully!\n";
    echo "The bln_sst column has been added and updated in loan_case_invoice_main table.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Please check the error and run the script again.\n";
}

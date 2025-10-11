<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Invoice Amount Update Script\n";
echo "============================\n\n";

// Check if --fix flag is provided
$fixMode = isset($argv[1]) && $argv[1] === '--fix';

if (!$fixMode) {
    echo "🔍 DRY RUN MODE - No changes will be made\n";
    echo "To actually update invoices, run: php update_invoice_amounts.php --fix\n\n";
} else {
    echo "🔧 FIXING MODE - Will update database\n\n";
}

// First, let's see how many invoices will be affected
$mismatchedCount = \DB::select("
    SELECT COUNT(*) as count FROM (
        SELECT 
            im.id,
            im.amount - ROUND(
                (COALESCE(cat1.total, 0) + (COALESCE(cat1.total, 0) * b.sst_rate / 100)) + 
                COALESCE(cat2.total, 0) + 
                COALESCE(cat3.total, 0) + 
                (COALESCE(cat4.total, 0) + (COALESCE(cat4.total, 0) * b.sst_rate / 100)), 2
            ) as difference
        FROM loan_case_invoice_main im
        LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
        LEFT JOIN (
            SELECT d.invoice_main_id, SUM(d.amount) as total
            FROM loan_case_invoice_details d
            LEFT JOIN account_item ai ON ai.id = d.account_item_id
            WHERE ai.account_cat_id = 1
            GROUP BY d.invoice_main_id
        ) cat1 ON cat1.invoice_main_id = im.id
        LEFT JOIN (
            SELECT d.invoice_main_id, SUM(d.amount) as total
            FROM loan_case_invoice_details d
            LEFT JOIN account_item ai ON ai.id = d.account_item_id
            WHERE ai.account_cat_id = 2
            GROUP BY d.invoice_main_id
        ) cat2 ON cat2.invoice_main_id = im.id
        LEFT JOIN (
            SELECT d.invoice_main_id, SUM(d.amount) as total
            FROM loan_case_invoice_details d
            LEFT JOIN account_item ai ON ai.id = d.account_item_id
            WHERE ai.account_cat_id = 3
            GROUP BY d.invoice_main_id
        ) cat3 ON cat3.invoice_main_id = im.id
        LEFT JOIN (
            SELECT d.invoice_main_id, SUM(d.amount) as total
            FROM loan_case_invoice_details d
            LEFT JOIN account_item ai ON ai.id = d.account_item_id
            WHERE ai.account_cat_id = 4
            GROUP BY d.invoice_main_id
        ) cat4 ON cat4.invoice_main_id = im.id
        WHERE im.status = 1
        HAVING ABS(difference) > 0.01
    ) as mismatched
")[0]->count;

echo "Invoices to be updated: {$mismatchedCount}\n\n";

if ($mismatchedCount == 0) {
    echo "✅ No invoices need updating!\n";
    exit;
}

if ($fixMode) {
    echo "⚠️  WARNING: About to update {$mismatchedCount} invoices!\n";
    echo "Press Enter to continue or Ctrl+C to cancel...\n";
    readline();
    
    echo "Updating invoices...\n";
    
    // Perform the update
    $updatedCount = \DB::update("
        UPDATE loan_case_invoice_main im
        LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
        LEFT JOIN (
            SELECT d.invoice_main_id, SUM(d.amount) as total
            FROM loan_case_invoice_details d
            LEFT JOIN account_item ai ON ai.id = d.account_item_id
            WHERE ai.account_cat_id = 1
            GROUP BY d.invoice_main_id
        ) cat1 ON cat1.invoice_main_id = im.id
        LEFT JOIN (
            SELECT d.invoice_main_id, SUM(d.amount) as total
            FROM loan_case_invoice_details d
            LEFT JOIN account_item ai ON ai.id = d.account_item_id
            WHERE ai.account_cat_id = 2
            GROUP BY d.invoice_main_id
        ) cat2 ON cat2.invoice_main_id = im.id
        LEFT JOIN (
            SELECT d.invoice_main_id, SUM(d.amount) as total
            FROM loan_case_invoice_details d
            LEFT JOIN account_item ai ON ai.id = d.account_item_id
            WHERE ai.account_cat_id = 3
            GROUP BY d.invoice_main_id
        ) cat3 ON cat3.invoice_main_id = im.id
        LEFT JOIN (
            SELECT d.invoice_main_id, SUM(d.amount) as total
            FROM loan_case_invoice_details d
            LEFT JOIN account_item ai ON ai.id = d.account_item_id
            WHERE ai.account_cat_id = 4
            GROUP BY d.invoice_main_id
        ) cat4 ON cat4.invoice_main_id = im.id
        SET im.amount = ROUND(
            (COALESCE(cat1.total, 0) + (COALESCE(cat1.total, 0) * b.sst_rate / 100)) + 
            COALESCE(cat2.total, 0) + 
            COALESCE(cat3.total, 0) + 
            (COALESCE(cat4.total, 0) + (COALESCE(cat4.total, 0) * b.sst_rate / 100)), 2
        )
        WHERE im.status = 1
        AND ABS(im.amount - ROUND(
            (COALESCE(cat1.total, 0) + (COALESCE(cat1.total, 0) * b.sst_rate / 100)) + 
            COALESCE(cat2.total, 0) + 
            COALESCE(cat3.total, 0) + 
            (COALESCE(cat4.total, 0) + (COALESCE(cat4.total, 0) * b.sst_rate / 100)), 2
        )) > 0.01
    ");
    
    echo "✅ Successfully updated {$updatedCount} invoices!\n";
    
    // Verify the update
    $remainingMismatched = \DB::select("
        SELECT COUNT(*) as count FROM (
            SELECT 
                im.id,
                im.amount - ROUND(
                    (COALESCE(cat1.total, 0) + (COALESCE(cat1.total, 0) * b.sst_rate / 100)) + 
                    COALESCE(cat2.total, 0) + 
                    COALESCE(cat3.total, 0) + 
                    (COALESCE(cat4.total, 0) + (COALESCE(cat4.total, 0) * b.sst_rate / 100)), 2
                ) as difference
            FROM loan_case_invoice_main im
            LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
            LEFT JOIN (
                SELECT d.invoice_main_id, SUM(d.amount) as total
                FROM loan_case_invoice_details d
                LEFT JOIN account_item ai ON ai.id = d.account_item_id
                WHERE ai.account_cat_id = 1
                GROUP BY d.invoice_main_id
            ) cat1 ON cat1.invoice_main_id = im.id
            LEFT JOIN (
                SELECT d.invoice_main_id, SUM(d.amount) as total
                FROM loan_case_invoice_details d
                LEFT JOIN account_item ai ON ai.id = d.account_item_id
                WHERE ai.account_cat_id = 2
                GROUP BY d.invoice_main_id
            ) cat2 ON cat2.invoice_main_id = im.id
            LEFT JOIN (
                SELECT d.invoice_main_id, SUM(d.amount) as total
                FROM loan_case_invoice_details d
                LEFT JOIN account_item ai ON ai.id = d.account_item_id
                WHERE ai.account_cat_id = 3
                GROUP BY d.invoice_main_id
            ) cat3 ON cat3.invoice_main_id = im.id
            LEFT JOIN (
                SELECT d.invoice_main_id, SUM(d.amount) as total
                FROM loan_case_invoice_details d
                LEFT JOIN account_item ai ON ai.id = d.account_item_id
                WHERE ai.account_cat_id = 4
                GROUP BY d.invoice_main_id
            ) cat4 ON cat4.invoice_main_id = im.id
            WHERE im.status = 1
            HAVING ABS(difference) > 0.01
        ) as mismatched
    ")[0]->count;
    
    echo "Remaining mismatched invoices: {$remainingMismatched}\n";
    
    if ($remainingMismatched == 0) {
        echo "🎉 All invoices are now correctly calculated!\n";
    }
} else {
    echo "To update all {$mismatchedCount} invoices, run:\n";
    echo "php update_invoice_amounts.php --fix\n\n";
    echo "⚠️  WARNING: Always backup your database before running the update!\n";
}

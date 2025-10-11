<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Invoice Analysis Report\n";
echo "======================\n\n";

// Get total invoice count
$totalInvoices = \App\Models\LoanCaseInvoiceMain::where('status', 1)->count();
echo "Total Active Invoices: {$totalInvoices}\n\n";

// Get mismatched invoices count
$mismatchedCount = \DB::select("
    SELECT COUNT(*) as count FROM (
        SELECT 
            im.id,
            im.amount - (
                (COALESCE(cat1.total, 0) + (COALESCE(cat1.total, 0) * b.sst_rate / 100)) + 
                COALESCE(cat2.total, 0) + 
                COALESCE(cat3.total, 0) + 
                (COALESCE(cat4.total, 0) + (COALESCE(cat4.total, 0) * b.sst_rate / 100))
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

$matchCount = $totalInvoices - $mismatchedCount;
$matchRate = ($matchCount / $totalInvoices) * 100;

echo "Invoice Status:\n";
echo "  ✅ Correct: {$matchCount} (" . number_format($matchRate, 1) . "%)\n";
echo "  ❌ Mismatched: {$mismatchedCount} (" . number_format(100 - $matchRate, 1) . "%)\n\n";

// Get top 10 worst mismatches
echo "Top 10 Worst Mismatches:\n";
$worstMismatches = \DB::select("
    SELECT 
        im.invoice_no,
        im.amount as invoice_amount,
        (COALESCE(cat1.total, 0) + (COALESCE(cat1.total, 0) * b.sst_rate / 100)) + 
        COALESCE(cat2.total, 0) + 
        COALESCE(cat3.total, 0) + 
        (COALESCE(cat4.total, 0) + (COALESCE(cat4.total, 0) * b.sst_rate / 100)) as calculated_amount,
        ABS(im.amount - (
            (COALESCE(cat1.total, 0) + (COALESCE(cat1.total, 0) * b.sst_rate / 100)) + 
            COALESCE(cat2.total, 0) + 
            COALESCE(cat3.total, 0) + 
            (COALESCE(cat4.total, 0) + (COALESCE(cat4.total, 0) * b.sst_rate / 100))
        )) as difference
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
    ORDER BY difference DESC
    LIMIT 10
");

foreach ($worstMismatches as $i => $mismatch) {
    echo "  " . ($i + 1) . ". {$mismatch->invoice_no}: " . 
         number_format($mismatch->invoice_amount, 2) . " → " . 
         number_format($mismatch->calculated_amount, 2) . 
         " (diff: " . number_format($mismatch->difference, 2) . ")\n";
}

echo "\n=== RECOMMENDATIONS ===\n";
echo "1. 🔍 Use 'php invoice_validation_tool.php [INVOICE_NO]' to check specific invoices\n";
echo "2. 🔧 Use 'php fix_mismatched_invoices.php [INVOICE_NO] --fix' to fix specific invoices\n";
echo "3. 📊 Use 'php find_mismatched_invoices.php' to see all mismatched invoices\n";
echo "4. ⚠️  Always backup database before running fixes\n";
echo "5. 🎯 Focus on invoices with large differences first\n";
echo "6. 📈 Consider updating the Transfer Fee V3 system to use the correct formula\n\n";

echo "=== NEXT STEPS ===\n";
echo "1. Review the worst mismatches above\n";
echo "2. Test fixing a few invoices manually first\n";
echo "3. Consider implementing the correct formula in the Transfer Fee V3 system\n";
echo "4. Update invoice creation process to use the correct calculation\n";

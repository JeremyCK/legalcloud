<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Finding invoices with mismatched amounts...\n";
echo "==========================================\n\n";

// Get all invoices with their calculated amounts
$mismatchedInvoices = \DB::select("
    SELECT 
        im.invoice_no,
        im.amount as invoice_amount,
        b.sst_rate,
        COALESCE(cat1.total, 0) as cat1_amount,
        COALESCE(cat2.total, 0) as cat2_amount,
        COALESCE(cat3.total, 0) as cat3_amount,
        COALESCE(cat4.total, 0) as cat4_amount,
        -- Apply the formula
        (COALESCE(cat1.total, 0) + (COALESCE(cat1.total, 0) * b.sst_rate / 100)) + 
        COALESCE(cat2.total, 0) + 
        COALESCE(cat3.total, 0) + 
        (COALESCE(cat4.total, 0) + (COALESCE(cat4.total, 0) * b.sst_rate / 100)) as calculated_amount,
        -- Calculate difference
        im.amount - (
            (COALESCE(cat1.total, 0) + (COALESCE(cat1.total, 0) * b.sst_rate / 100)) + 
            COALESCE(cat2.total, 0) + 
            COALESCE(cat3.total, 0) + 
            (COALESCE(cat4.total, 0) + (COALESCE(cat4.total, 0) * b.sst_rate / 100))
        ) as difference,
        im.created_at
    FROM loan_case_invoice_main im
    LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
    -- Category 1 (P.Fee)
    LEFT JOIN (
        SELECT 
            d.invoice_main_id,
            SUM(d.amount) as total
        FROM loan_case_invoice_details d
        LEFT JOIN account_item ai ON ai.id = d.account_item_id
        WHERE ai.account_cat_id = 1
        GROUP BY d.invoice_main_id
    ) cat1 ON cat1.invoice_main_id = im.id
    -- Category 2 (Disbursement)
    LEFT JOIN (
        SELECT 
            d.invoice_main_id,
            SUM(d.amount) as total
        FROM loan_case_invoice_details d
        LEFT JOIN account_item ai ON ai.id = d.account_item_id
        WHERE ai.account_cat_id = 2
        GROUP BY d.invoice_main_id
    ) cat2 ON cat2.invoice_main_id = im.id
    -- Category 3 (Reimbursement)
    LEFT JOIN (
        SELECT 
            d.invoice_main_id,
            SUM(d.amount) as total
        FROM loan_case_invoice_details d
        LEFT JOIN account_item ai ON ai.id = d.account_item_id
        WHERE ai.account_cat_id = 3
        GROUP BY d.invoice_main_id
    ) cat3 ON cat3.invoice_main_id = im.id
    -- Category 4 (SST)
    LEFT JOIN (
        SELECT 
            d.invoice_main_id,
            SUM(d.amount) as total
        FROM loan_case_invoice_details d
        LEFT JOIN account_item ai ON ai.id = d.account_item_id
        WHERE ai.account_cat_id = 4
        GROUP BY d.invoice_main_id
    ) cat4 ON cat4.invoice_main_id = im.id
    WHERE im.status = 1
    -- Filter out invoices where difference is more than 0.01
    HAVING ABS(difference) > 0.01
    ORDER BY ABS(difference) DESC, im.created_at DESC
    LIMIT 50
");

if (count($mismatchedInvoices) == 0) {
    echo "✅ No mismatched invoices found! All invoices match the correct calculation.\n";
} else {
    echo "❌ Found " . count($mismatchedInvoices) . " invoices with mismatched amounts:\n\n";
    
    foreach ($mismatchedInvoices as $invoice) {
        echo "Invoice: {$invoice->invoice_no}\n";
        echo "  Invoice Amount: " . number_format($invoice->invoice_amount, 2) . "\n";
        echo "  Calculated Amount: " . number_format($invoice->calculated_amount, 2) . "\n";
        echo "  Difference: " . number_format($invoice->difference, 2) . "\n";
        echo "  SST Rate: " . number_format($invoice->sst_rate, 2) . "%\n";
        echo "  Categories: Cat1=" . number_format($invoice->cat1_amount, 2) . 
               ", Cat2=" . number_format($invoice->cat2_amount, 2) . 
               ", Cat3=" . number_format($invoice->cat3_amount, 2) . 
               ", Cat4=" . number_format($invoice->cat4_amount, 2) . "\n";
        echo "  Created: " . $invoice->created_at . "\n";
        echo "\n";
    }
    
    // Generate summary
    $totalDifference = array_sum(array_column($mismatchedInvoices, 'difference'));
    $avgDifference = $totalDifference / count($mismatchedInvoices);
    
    echo "=== SUMMARY ===\n";
    echo "Total mismatched invoices: " . count($mismatchedInvoices) . "\n";
    echo "Total difference amount: " . number_format($totalDifference, 2) . "\n";
    echo "Average difference: " . number_format($avgDifference, 2) . "\n";
    
    // Show distribution by difference ranges
    $ranges = [
        '0.01 - 1.00' => 0,
        '1.01 - 10.00' => 0,
        '10.01 - 100.00' => 0,
        '100.01+' => 0
    ];
    
    foreach ($mismatchedInvoices as $invoice) {
        $diff = abs($invoice->difference);
        if ($diff <= 1.00) {
            $ranges['0.01 - 1.00']++;
        } elseif ($diff <= 10.00) {
            $ranges['1.01 - 10.00']++;
        } elseif ($diff <= 100.00) {
            $ranges['10.01 - 100.00']++;
        } else {
            $ranges['100.01+']++;
        }
    }
    
    echo "\nDifference Distribution:\n";
    foreach ($ranges as $range => $count) {
        echo "  {$range}: {$count} invoices\n";
    }
}

echo "\n=== RECOMMENDATIONS ===\n";
echo "1. Review invoices with large differences (>100) - may need manual correction\n";
echo "2. Small differences (0.01-1.00) might be due to rounding in original calculation\n";
echo "3. Consider updating invoice amounts using the correct formula for consistency\n";
echo "4. Use the validation tool to check specific invoices before processing\n";

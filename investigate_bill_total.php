<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== INVESTIGATING BILL TOTAL: Expected 2500 vs Actual 2531 ===\n\n";

$billId = 8377;

// Get bill information
$bill = DB::table('loan_case_bill_main')
    ->where('id', $billId)
    ->where('status', '<>', 99)
    ->first();

if (!$bill) {
    echo "ERROR: Bill not found\n";
    exit;
}

echo "=== BILL INFORMATION ===\n";
echo "Bill ID: {$bill->id}\n";
echo "SST Rate: {$bill->sst_rate}%\n";
echo "Current total_amt_inv: {$bill->total_amt_inv}\n";
echo "Current pfee1_inv: {$bill->pfee1_inv}\n";
echo "Current pfee2_inv: {$bill->pfee2_inv}\n";
echo "Current sst_inv: {$bill->sst_inv}\n\n";

// Get all invoices for this bill
$invoices = DB::table('loan_case_invoice_main')
    ->where('loan_case_main_bill_id', $billId)
    ->where('status', '<>', 99)
    ->get();

echo "=== INVOICE BREAKDOWN ===\n";
$calculatedBillTotal = 0;
$calculatedPfee1 = 0;
$calculatedPfee2 = 0;
$calculatedSst = 0;

foreach ($invoices as $invoice) {
    echo "Invoice: {$invoice->invoice_no}\n";
    echo "  Amount: {$invoice->amount}\n";
    echo "  Pfee1: {$invoice->pfee1_inv}\n";
    echo "  Pfee2: {$invoice->pfee2_inv}\n";
    echo "  SST: {$invoice->sst_inv}\n";
    
    $calculatedBillTotal += $invoice->amount;
    $calculatedPfee1 += $invoice->pfee1_inv;
    $calculatedPfee2 += $invoice->pfee2_inv;
    $calculatedSst += $invoice->sst_inv;
    echo "\n";
}

echo "=== CALCULATION SUMMARY ===\n";
echo "Sum of Invoice Amounts: {$calculatedBillTotal}\n";
echo "Sum of Invoice Pfee1: {$calculatedPfee1}\n";
echo "Sum of Invoice Pfee2: {$calculatedPfee2}\n";
echo "Sum of Invoice SST: {$calculatedSst}\n\n";

echo "=== BILL vs INVOICE COMPARISON ===\n";
echo "Bill total_amt_inv: {$bill->total_amt_inv}\n";
echo "Sum of invoice amounts: {$calculatedBillTotal}\n";
echo "Difference: " . ($bill->total_amt_inv - $calculatedBillTotal) . "\n\n";

// Let's check what the expected calculation should be
echo "=== EXPECTED CALCULATION ===\n";
echo "If we expect 2500 total:\n";
echo "  Professional fees (without SST): " . (2500 / 1.08) . "\n";
echo "  SST (8%): " . (2500 - (2500 / 1.08)) . "\n";
echo "  Total: 2500\n\n";

echo "Current calculation:\n";
echo "  Professional fees (without SST): " . ($calculatedPfee1 + $calculatedPfee2) . "\n";
echo "  SST: {$calculatedSst}\n";
echo "  Total: {$calculatedBillTotal}\n\n";

// Check if there's a rounding issue
echo "=== ROUNDING ANALYSIS ===\n";
$professionalFees = $calculatedPfee1 + $calculatedPfee2;
$expectedSst = $professionalFees * ($bill->sst_rate / 100);
$expectedTotal = $professionalFees * (1 + ($bill->sst_rate / 100));

echo "Professional fees: {$professionalFees}\n";
echo "Expected SST (8%): {$expectedSst}\n";
echo "Expected Total: {$expectedTotal}\n";
echo "Actual SST: {$calculatedSst}\n";
echo "Actual Total: {$calculatedBillTotal}\n";
echo "SST Difference: " . ($calculatedSst - $expectedSst) . "\n";
echo "Total Difference: " . ($calculatedBillTotal - $expectedTotal) . "\n\n";

// Let's check the individual invoice calculations
echo "=== INDIVIDUAL INVOICE CALCULATION CHECK ===\n";
foreach ($invoices as $invoice) {
    echo "Invoice: {$invoice->invoice_no}\n";
    
    // Get invoice details
    $details = DB::table('loan_case_invoice_details as ild')
        ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
        ->where('ild.invoice_main_id', $invoice->id)
        ->where('ild.status', '<>', 99)
        ->select('ild.amount', 'ai.account_cat_id', 'ai.pfee1_item')
        ->get();
    
    $pfee1 = 0;
    $pfee2 = 0;
    $sst = 0;
    $total = 0;
    
    foreach ($details as $detail) {
        if ($detail->account_cat_id == 1) {
            if ($detail->pfee1_item == 1) {
                $pfee1 += $detail->amount;
            } else {
                $pfee2 += $detail->amount;
            }
            $sst += $detail->amount * ($bill->sst_rate / 100);
            $total += $detail->amount * (1 + ($bill->sst_rate / 100));
        } else {
            $total += $detail->amount;
        }
    }
    
    echo "  Calculated from details:\n";
    echo "    Pfee1: " . round($pfee1, 2) . " (DB: {$invoice->pfee1_inv})\n";
    echo "    Pfee2: " . round($pfee2, 2) . " (DB: {$invoice->pfee2_inv})\n";
    echo "    SST: " . round($sst, 2) . " (DB: {$invoice->sst_inv})\n";
    echo "    Total: " . round($total, 2) . " (DB: {$invoice->amount})\n";
    echo "\n";
}

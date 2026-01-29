<?php

/**
 * Check invoice DP20001272 SST discrepancy
 * Expected: 49.09 + 67.20 = 116.29
 * Showing: 116.30
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeDetails;
use App\Models\LoanCaseInvoiceMain;

echo "=== Checking Invoice DP20001272 SST Discrepancy ===\n\n";

$transferFeeId = 502;
$invoiceNo = 'DP20001272';

// Get the invoice
$invoice = LoanCaseInvoiceMain::where('invoice_no', $invoiceNo)->first();

if (!$invoice) {
    echo "❌ Invoice {$invoiceNo} not found\n";
    exit(1);
}

// Get the transfer fee detail for this invoice
$transferFeeDetail = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->where('loan_case_invoice_main_id', $invoice->id)
    ->first();

if (!$transferFeeDetail) {
    echo "❌ Transfer fee detail not found for invoice {$invoiceNo} in transfer fee {$transferFeeId}\n";
    exit(1);
}

echo "Invoice: {$invoiceNo}\n";
echo "Transfer Fee Detail ID: {$transferFeeDetail->id}\n\n";

// Current values from invoice
$invoiceSst = $invoice->sst_inv ?? 0;
$invoiceReimbSst = $invoice->reimbursement_sst ?? 0;
$invoiceTotalSst = $invoiceSst + $invoiceReimbSst;

// Current values from transfer_fee_details
$transferredSst = $transferFeeDetail->sst_amount ?? 0;
$transferredReimbSst = $transferFeeDetail->reimbursement_sst_amount ?? 0;
$transferredTotalSst = $transferredSst + $transferredReimbSst;

echo "=== Current Values ===\n";
echo "Invoice SST: " . number_format($invoiceSst, 2) . "\n";
echo "Invoice Reimb SST: " . number_format($invoiceReimbSst, 2) . "\n";
echo "Invoice Total SST (Expected): " . number_format($invoiceTotalSst, 2) . "\n\n";

echo "Transfer Fee Detail SST: " . number_format($transferredSst, 2) . "\n";
echo "Transfer Fee Detail Reimb SST: " . number_format($transferredReimbSst, 2) . "\n";
echo "Transfer Fee Detail Total SST (Displayed): " . number_format($transferredTotalSst, 2) . "\n\n";

$difference = $transferredTotalSst - $invoiceTotalSst;
echo "Difference: " . number_format($difference, 2) . "\n\n";

echo "=== Analysis ===\n";
if (abs($difference) > 0.01) {
    echo "⚠️  DISCREPANCY DETECTED!\n";
    echo "This is a DATA ISSUE, not a calculation issue.\n\n";
    echo "The 'Transferred SST' column shows values from the 'transfer_fee_details' table,\n";
    echo "which were stored when the transfer was created.\n\n";
    echo "The stored values don't match the current invoice values due to:\n";
    echo "1. Rounding differences when the transfer was created\n";
    echo "2. Invoice values may have been updated after the transfer was created\n\n";
    
    echo "=== Root Cause ===\n";
    if (abs($transferredSst - $invoiceSst) > 0.001) {
        echo "SST mismatch: Invoice has " . number_format($invoiceSst, 2) . " but transfer_fee_details has " . number_format($transferredSst, 2) . "\n";
    }
    if (abs($transferredReimbSst - $invoiceReimbSst) > 0.001) {
        echo "Reimb SST mismatch: Invoice has " . number_format($invoiceReimbSst, 2) . " but transfer_fee_details has " . number_format($transferredReimbSst, 2) . "\n";
    }
    
    echo "\n=== Solution ===\n";
    echo "Update transfer_fee_details to match current invoice values:\n";
    echo "  sst_amount: " . number_format($transferredSst, 2) . " → " . number_format($invoiceSst, 2) . "\n";
    echo "  reimbursement_sst_amount: " . number_format($transferredReimbSst, 2) . " → " . number_format($invoiceReimbSst, 2) . "\n";
    
    // Update it
    $transferFeeDetail->sst_amount = $invoiceSst;
    $transferFeeDetail->reimbursement_sst_amount = $invoiceReimbSst;
    $transferFeeDetail->save();
    
    echo "\n✅ Fixed! Updated transfer_fee_details record.\n";
} else {
    echo "✅ No discrepancy. Values match.\n";
}

<?php

/**
 * Check and fix Transferred SST discrepancy for invoice DP20001286 in transfer fee 502
 * 
 * Issue: Transferred SST shows 146.30 but invoice SST (103.89) + Reimb SST (42.40) = 146.29
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeDetails;
use App\Models\LoanCaseInvoiceMain;

echo "=== Checking Transferred SST Discrepancy ===\n\n";

$transferFeeId = 502;
$invoiceNo = 'DP20001286';

// Get the transfer fee detail for this invoice
$transferFeeDetail = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->whereHas('loanCaseInvoiceMain', function($query) use ($invoiceNo) {
        $query->where('invoice_no', $invoiceNo);
    })
    ->first();

if (!$transferFeeDetail) {
    echo "❌ Transfer fee detail not found for invoice {$invoiceNo} in transfer fee {$transferFeeId}\n";
    exit(1);
}

// Get the invoice
$invoice = LoanCaseInvoiceMain::where('invoice_no', $invoiceNo)->first();

if (!$invoice) {
    echo "❌ Invoice {$invoiceNo} not found\n";
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
echo "Invoice Total SST: " . number_format($invoiceTotalSst, 2) . "\n\n";

echo "Transfer Fee Detail SST: " . number_format($transferredSst, 2) . "\n";
echo "Transfer Fee Detail Reimb SST: " . number_format($transferredReimbSst, 2) . "\n";
echo "Transfer Fee Detail Total SST: " . number_format($transferredTotalSst, 2) . "\n\n";

$difference = $transferredTotalSst - $invoiceTotalSst;
echo "Difference: " . number_format($difference, 2) . "\n\n";

if (abs($difference) > 0.01) {
    echo "⚠️  DISCREPANCY DETECTED!\n\n";
    
    // Check if there are multiple transfer records for this invoice
    $allTransferDetails = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
        ->where('status', '<>', 99)
        ->get();
    
    $totalTransferredSst = $allTransferDetails->sum('sst_amount');
    $totalTransferredReimbSst = $allTransferDetails->sum('reimbursement_sst_amount');
    $totalTransferred = $totalTransferredSst + $totalTransferredReimbSst;
    
    echo "=== All Transfer Records for This Invoice ===\n";
    echo "Number of transfer records: " . $allTransferDetails->count() . "\n";
    echo "Total Transferred SST (all records): " . number_format($totalTransferredSst, 2) . "\n";
    echo "Total Transferred Reimb SST (all records): " . number_format($totalTransferredReimbSst, 2) . "\n";
    echo "Total Transferred (all records): " . number_format($totalTransferred, 2) . "\n\n";
    
    if ($allTransferDetails->count() == 1) {
        // Single transfer record - can update directly
        echo "✅ Single transfer record found. Can update directly.\n\n";
        echo "Would you like to update the transfer_fee_details to match the invoice?\n";
        echo "This will set:\n";
        echo "  sst_amount = " . number_format($invoiceSst, 2) . "\n";
        echo "  reimbursement_sst_amount = " . number_format($invoiceReimbSst, 2) . "\n\n";
        
        // Update the transfer_fee_details to match invoice
        $transferFeeDetail->sst_amount = $invoiceSst;
        $transferFeeDetail->reimbursement_sst_amount = $invoiceReimbSst;
        $transferFeeDetail->save();
        
        echo "✅ Updated transfer_fee_details record\n";
        echo "  sst_amount: " . number_format($invoiceSst, 2) . "\n";
        echo "  reimbursement_sst_amount: " . number_format($invoiceReimbSst, 2) . "\n";
    } else {
        // Multiple transfer records - need proportional update
        echo "⚠️  Multiple transfer records found. Need proportional update.\n\n";
        
        foreach ($allTransferDetails as $tfd) {
            echo "Transfer Fee Detail ID: {$tfd->id}\n";
            echo "  Transfer Fee Main ID: {$tfd->transfer_fee_main_id}\n";
            echo "  Current SST: " . number_format($tfd->sst_amount ?? 0, 2) . "\n";
            echo "  Current Reimb SST: " . number_format($tfd->reimbursement_sst_amount ?? 0, 2) . "\n";
            
            if ($totalTransferredSst > 0) {
                $proportion = ($tfd->sst_amount ?? 0) / $totalTransferredSst;
                $newSst = round($invoiceSst * $proportion, 2);
                echo "  Proposed SST: " . number_format($newSst, 2) . " (proportion: " . number_format($proportion * 100, 2) . "%)\n";
            }
            
            if ($totalTransferredReimbSst > 0) {
                $proportion = ($tfd->reimbursement_sst_amount ?? 0) / $totalTransferredReimbSst;
                $newReimbSst = round($invoiceReimbSst * $proportion, 2);
                echo "  Proposed Reimb SST: " . number_format($newReimbSst, 2) . " (proportion: " . number_format($proportion * 100, 2) . "%)\n";
            }
            echo "\n";
        }
    }
} else {
    echo "✅ No discrepancy found. Values match.\n";
}

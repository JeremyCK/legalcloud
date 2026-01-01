<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;
use App\Models\LoanCaseInvoiceMain;

$transferFeeId = 491; // The transfer fee ID from the URL

echo "=== Transfer Fee Totals Investigation ===\n";
echo "Transfer Fee ID: {$transferFeeId}\n\n";

// Get the transfer fee main record
$transferFeeMain = TransferFeeMain::find($transferFeeId);
if (!$transferFeeMain) {
    die("Transfer fee not found!\n");
}

echo "Transaction ID: {$transferFeeMain->transaction_id}\n";
echo "Transfer Date: {$transferFeeMain->transfer_date}\n";
echo "Purpose: {$transferFeeMain->purpose}\n\n";

// Get all transfer fee details for this transfer fee
$transferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->where('status', '<>', 99)
    ->get();

echo "Total Transfer Fee Details: " . $transferFeeDetails->count() . "\n\n";

// Get all invoice IDs in this transfer fee
$invoiceIds = $transferFeeDetails->pluck('loan_case_invoice_main_id')->unique()->toArray();

echo "=== Checking Each Invoice ===\n\n";

$discrepancies = [];
$totalPfee = 0;
$totalSst = 0;
$totalReimb = 0;
$totalReimbSst = 0;
$totalTransferredBal = 0;
$totalTransferredSst = 0;
$totalTransferredReimb = 0;
$totalTransferredReimbSst = 0;

foreach ($invoiceIds as $invoiceId) {
    $invoice = LoanCaseInvoiceMain::find($invoiceId);
    if (!$invoice) {
        continue;
    }
    
    // Get invoice amounts
    $pfee = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0);
    $sst = $invoice->sst_inv ?? 0;
    $reimb = $invoice->reimbursement_amount ?? 0;
    $reimbSst = $invoice->reimbursement_sst ?? 0;
    
    // Get amounts transferred in THIS transfer fee record
    $thisTransferDetails = $transferFeeDetails->where('loan_case_invoice_main_id', $invoiceId);
    $thisTransferredPfee = $thisTransferDetails->sum('transfer_amount');
    $thisTransferredSst = $thisTransferDetails->sum('sst_amount');
    $thisTransferredReimb = $thisTransferDetails->sum('reimbursement_amount');
    $thisTransferredReimbSst = $thisTransferDetails->sum('reimbursement_sst_amount');
    
    // Calculate transferred balance and SST as shown in the view
    $transferredBal = $thisTransferredPfee + $thisTransferredReimb;
    $transferredSst = $thisTransferredSst + $thisTransferredReimbSst;
    
    // Get total amounts transferred across ALL transfer fees (cumulative)
    $totalTransferredPfeeAll = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
        ->where('status', '<>', 99)
        ->sum('transfer_amount');
    $totalTransferredSstAll = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
        ->where('status', '<>', 99)
        ->sum('sst_amount');
    $totalTransferredReimbAll = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
        ->where('status', '<>', 99)
        ->sum('reimbursement_amount');
    $totalTransferredReimbSstAll = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
        ->where('status', '<>', 99)
        ->sum('reimbursement_sst_amount');
    
    // Check if there are other transfer fees
    $otherTransferFees = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
        ->where('transfer_fee_main_id', '<>', $transferFeeId)
        ->where('status', '<>', 99)
        ->get();
    
    $hasOtherTransfers = $otherTransferFees->count() > 0;
    
    // Calculate expected vs actual
    $expectedTotal = $pfee + $sst + $reimb + $reimbSst;
    $actualTransferred = $transferredBal + $transferredSst;
    $remaining = $expectedTotal - $actualTransferred;
    
    // Check if there's a discrepancy
    $isDiscrepancy = false;
    $discrepancyReason = '';
    
    if (abs($expectedTotal - ($actualTransferred + ($pfee - $totalTransferredPfeeAll) + ($sst - $totalTransferredSstAll) + ($reimb - $totalTransferredReimbAll) + ($reimbSst - $totalTransferredReimbSstAll))) > 0.01) {
        $isDiscrepancy = true;
        $discrepancyReason = "Invoice totals don't match transferred + remaining amounts";
    }
    
    // Add to totals
    $totalPfee += $pfee;
    $totalSst += $sst;
    $totalReimb += $reimb;
    $totalReimbSst += $reimbSst;
    $totalTransferredBal += $transferredBal;
    $totalTransferredSst += $transferredSst;
    $totalTransferredReimb += $thisTransferredReimb;
    $totalTransferredReimbSst += $thisTransferredReimbSst;
    
    // Store discrepancy info
    if ($isDiscrepancy || $hasOtherTransfers) {
        $discrepancies[] = [
            'invoice_id' => $invoiceId,
            'invoice_no' => $invoice->invoice_no ?? 'N/A',
            'case_ref_no' => DB::table('loan_case')->where('id', $invoice->loan_case_main_bill_id ? DB::table('loan_case_bill_main')->where('id', $invoice->loan_case_main_bill_id)->value('case_id') : 0)->value('case_ref_no') ?? 'N/A',
            'pfee' => $pfee,
            'sst' => $sst,
            'reimb' => $reimb,
            'reimb_sst' => $reimbSst,
            'expected_total' => $expectedTotal,
            'this_transferred_bal' => $transferredBal,
            'this_transferred_sst' => $transferredSst,
            'this_transferred_total' => $actualTransferred,
            'total_transferred_pfee_all' => $totalTransferredPfeeAll,
            'total_transferred_sst_all' => $totalTransferredSstAll,
            'total_transferred_reimb_all' => $totalTransferredReimbAll,
            'total_transferred_reimb_sst_all' => $totalTransferredReimbSstAll,
            'has_other_transfers' => $hasOtherTransfers,
            'other_transfer_count' => $otherTransferFees->count(),
            'other_transfer_fee_ids' => $otherTransferFees->pluck('transfer_fee_main_id')->unique()->toArray(),
            'remaining_pfee' => $pfee - $totalTransferredPfeeAll,
            'remaining_sst' => $sst - $totalTransferredSstAll,
            'remaining_reimb' => $reimb - $totalTransferredReimbAll,
            'remaining_reimb_sst' => $reimbSst - $totalTransferredReimbSstAll,
            'discrepancy' => $isDiscrepancy,
            'discrepancy_reason' => $discrepancyReason
        ];
    }
}

echo "=== Summary Totals ===\n";
echo "Total Pfee: " . number_format($totalPfee, 2) . "\n";
echo "Total SST: " . number_format($totalSst, 2) . "\n";
echo "Total Reimb: " . number_format($totalReimb, 2) . "\n";
echo "Total Reimb SST: " . number_format($totalReimbSst, 2) . "\n";
echo "Total Expected: " . number_format($totalPfee + $totalSst + $totalReimb + $totalReimbSst, 2) . "\n\n";

echo "Total Transferred Bal (this transfer): " . number_format($totalTransferredBal, 2) . "\n";
echo "Total Transferred SST (this transfer): " . number_format($totalTransferredSst, 2) . "\n";
echo "Total Transferred (this transfer): " . number_format($totalTransferredBal + $totalTransferredSst, 2) . "\n\n";

$expectedTotal = $totalPfee + $totalSst + $totalReimb + $totalReimbSst;
$actualTotal = $totalTransferredBal + $totalTransferredSst;
$difference = $expectedTotal - $actualTotal;

echo "Expected Total: " . number_format($expectedTotal, 2) . "\n";
echo "Actual Transferred (this transfer): " . number_format($actualTotal, 2) . "\n";
echo "Difference: " . number_format($difference, 2) . "\n\n";

echo "=== Discrepancies and Other Transfers ===\n";
echo "Found " . count($discrepancies) . " invoices with other transfers or discrepancies\n\n";

foreach ($discrepancies as $index => $disc) {
    echo "--- Invoice #" . ($index + 1) . " ---\n";
    echo "Invoice ID: {$disc['invoice_id']}\n";
    echo "Invoice No: {$disc['invoice_no']}\n";
    echo "Case Ref: {$disc['case_ref_no']}\n";
    echo "Pfee: " . number_format($disc['pfee'], 2) . "\n";
    echo "SST: " . number_format($disc['sst'], 2) . "\n";
    echo "Reimb: " . number_format($disc['reimb'], 2) . "\n";
    echo "Reimb SST: " . number_format($disc['reimb_sst'], 2) . "\n";
    echo "Expected Total: " . number_format($disc['expected_total'], 2) . "\n";
    echo "This Transfer - Transferred Bal: " . number_format($disc['this_transferred_bal'], 2) . "\n";
    echo "This Transfer - Transferred SST: " . number_format($disc['this_transferred_sst'], 2) . "\n";
    echo "This Transfer - Total: " . number_format($disc['this_transferred_total'], 2) . "\n\n";
    
    echo "Total Transferred (ALL transfers):\n";
    echo "  Pfee: " . number_format($disc['total_transferred_pfee_all'], 2) . "\n";
    echo "  SST: " . number_format($disc['total_transferred_sst_all'], 2) . "\n";
    echo "  Reimb: " . number_format($disc['total_transferred_reimb_all'], 2) . "\n";
    echo "  Reimb SST: " . number_format($disc['total_transferred_reimb_sst_all'], 2) . "\n";
    echo "  Total: " . number_format($disc['total_transferred_pfee_all'] + $disc['total_transferred_sst_all'] + $disc['total_transferred_reimb_all'] + $disc['total_transferred_reimb_sst_all'], 2) . "\n\n";
    
    echo "Remaining (not yet transferred):\n";
    echo "  Pfee: " . number_format($disc['remaining_pfee'], 2) . "\n";
    echo "  SST: " . number_format($disc['remaining_sst'], 2) . "\n";
    echo "  Reimb: " . number_format($disc['remaining_reimb'], 2) . "\n";
    echo "  Reimb SST: " . number_format($disc['remaining_reimb_sst'], 2) . "\n";
    echo "  Total: " . number_format($disc['remaining_pfee'] + $disc['remaining_sst'] + $disc['remaining_reimb'] + $disc['remaining_reimb_sst'], 2) . "\n\n";
    
    if ($disc['has_other_transfers']) {
        echo "⚠️  HAS OTHER TRANSFERS!\n";
        echo "Other Transfer Fee IDs: " . implode(', ', $disc['other_transfer_fee_ids']) . "\n";
        echo "Number of other transfers: {$disc['other_transfer_count']}\n";
        
        // Get details of other transfers
        $otherTransfers = TransferFeeDetails::where('loan_case_invoice_main_id', $disc['invoice_id'])
            ->where('transfer_fee_main_id', '<>', $transferFeeId)
            ->where('status', '<>', 99)
            ->get();
        
        foreach ($otherTransfers as $otherTransfer) {
            $otherTransferFee = TransferFeeMain::find($otherTransfer->transfer_fee_main_id);
            echo "  - Transfer Fee ID: {$otherTransfer->transfer_fee_main_id}";
            if ($otherTransferFee) {
                echo " (Transaction: {$otherTransferFee->transaction_id}, Date: {$otherTransferFee->transfer_date})";
            }
            echo "\n";
            echo "    Amount: Pfee=" . number_format($otherTransfer->transfer_amount, 2) . 
                 ", SST=" . number_format($otherTransfer->sst_amount, 2) . 
                 ", Reimb=" . number_format($otherTransfer->reimbursement_amount, 2) . 
                 ", ReimbSST=" . number_format($otherTransfer->reimbursement_sst_amount, 2) . "\n";
        }
        echo "\n";
    }
    
    if ($disc['discrepancy']) {
        echo "❌ DISCREPANCY DETECTED: {$disc['discrepancy_reason']}\n\n";
    }
    
    echo "\n";
}

echo "=== End of Report ===\n";


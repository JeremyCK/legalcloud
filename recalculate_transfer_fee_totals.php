<?php
/**
 * Recalculate Transfer Fee Totals from Details
 * This fixes the issue where small rounding differences (0.01) accumulate
 * Run: php recalculate_transfer_fee_totals.php [transfer_fee_id]
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;

// Get transfer fee ID from command line or use DP004-1025
$transferFeeId = $argv[1] ?? null;

if (!$transferFeeId) {
    // Find DP004-1025 by transaction_id - try different field names
    $transferFee = TransferFeeMain::where('transaction_id', 'DP004-1025')
        ->orWhere('transaction_id', 'like', '%DP004-1025%')
        ->first();
    
    if (!$transferFee) {
        // Try finding by ID 472 (from URL: /transferfee/472/edit)
        $transferFee = TransferFeeMain::find(472);
    }
    
    if ($transferFee) {
        $transferFeeId = $transferFee->id;
        echo "Found Transfer Fee: ID {$transferFeeId}\n";
        if (isset($transferFee->transaction_id)) {
            echo "Transaction ID: {$transferFee->transaction_id}\n";
        }
        echo "\n";
    } else {
        echo "Usage: php recalculate_transfer_fee_totals.php [transfer_fee_id]\n";
        echo "Or ensure DP004-1025 exists in transfer_fee_main table\n";
        echo "Trying to find transfer fee with ID 472...\n";
        $transferFee = TransferFeeMain::find(472);
        if ($transferFee) {
            $transferFeeId = 472;
            echo "Found Transfer Fee ID 472\n\n";
        } else {
            exit(1);
        }
    }
} else {
    $transferFee = TransferFeeMain::find($transferFeeId);
    if (!$transferFee) {
        echo "Transfer Fee ID {$transferFeeId} not found\n";
        exit(1);
    }
}

echo "========================================\n";
echo "RECALCULATE TRANSFER FEE TOTALS\n";
echo "========================================\n\n";
echo "Transfer Fee ID: {$transferFeeId}\n";
echo "Transaction ID: {$transferFee->transaction_id}\n";
echo "Date: {$transferFee->date}\n\n";

// Get all transfer fee details
$details = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->where('status', '<>', 99)
    ->get();

echo "Total Details: " . $details->count() . "\n\n";

if ($details->count() == 0) {
    echo "No details found for this transfer fee.\n";
    exit(1);
}

// Calculate totals from details
$totalPfee = 0;
$totalSst = 0;
$totalReimbursement = 0;
$totalReimbursementSst = 0;
$totalCollectedAmt = 0;

foreach ($details as $detail) {
    $totalPfee += $detail->transfer_amount ?? 0;
    $totalSst += $detail->sst_amount ?? 0;
    $totalReimbursement += $detail->reimbursement_amount ?? 0;
    $totalReimbursementSst += $detail->reimbursement_sst_amount ?? 0;
    $totalCollectedAmt += $detail->bill_collected_amt_divided ?? 0;
}

// Round to 2 decimal places
$totalPfee = round($totalPfee, 2);
$totalSst = round($totalSst, 2);
$totalReimbursement = round($totalReimbursement, 2);
$totalReimbursementSst = round($totalReimbursementSst, 2);
$totalCollectedAmt = round($totalCollectedAmt, 2);

// Get current stored values (if they exist in transfer_fee_main)
// Note: These might be stored differently, let's check the structure
$currentTransferAmount = $transferFee->transfer_amount ?? 0;

echo "========================================\n";
echo "CALCULATED TOTALS (from details)\n";
echo "========================================\n";
echo "Professional Fee:     " . number_format($totalPfee, 2) . "\n";
echo "SST:                 " . number_format($totalSst, 2) . "\n";
echo "Reimbursement:       " . number_format($totalReimbursement, 2) . "\n";
echo "Reimbursement SST:   " . number_format($totalReimbursementSst, 2) . "\n";
echo "Collected Amount:    " . number_format($totalCollectedAmt, 2) . "\n";
echo "Total Transfer Amt:  " . number_format($totalPfee + $totalSst + $totalReimbursement + $totalReimbursementSst, 2) . "\n\n";

echo "========================================\n";
echo "CLIENT EXPECTED VALUES\n";
echo "========================================\n";
echo "Collected amt:       1,681,810.72\n";
echo "Pfee:               521,831.74\n";
echo "SST:                41,746.47\n";
echo "Reimb:              66,373.63\n";
echo "Reimb SST:          5,309.91\n\n";

// Compare with expected
$expectedPfee = 521831.74;
$expectedSst = 41746.47;
$expectedReimb = 66373.63;
$expectedReimbSst = 5309.91;
$expectedCollected = 1681810.72;

echo "========================================\n";
echo "DIFFERENCES\n";
echo "========================================\n";
$pfeeDiff = $totalPfee - $expectedPfee;
$sstDiff = $totalSst - $expectedSst;
$reimbDiff = $totalReimbursement - $expectedReimb;
$reimbSstDiff = $totalReimbursementSst - $expectedReimbSst;
$collectedDiff = $totalCollectedAmt - $expectedCollected;

echo "Professional Fee:    " . ($pfeeDiff >= 0 ? '+' : '') . number_format($pfeeDiff, 2) . "\n";
echo "SST:                 " . ($sstDiff >= 0 ? '+' : '') . number_format($sstDiff, 2) . "\n";
echo "Reimbursement:       " . ($reimbDiff >= 0 ? '+' : '') . number_format($reimbDiff, 2) . "\n";
echo "Reimbursement SST:   " . ($reimbSstDiff >= 0 ? '+' : '') . number_format($reimbSstDiff, 2) . "\n";
echo "Collected Amount:    " . ($collectedDiff >= 0 ? '+' : '') . number_format($collectedDiff, 2) . "\n\n";

// Check if we need to update
$needsUpdate = false;
if (abs($pfeeDiff) > 0.01 || abs($sstDiff) > 0.01 || abs($reimbDiff) > 0.01 || abs($reimbSstDiff) > 0.01) {
    $needsUpdate = true;
    echo "⚠️  Differences detected! The totals need to be adjusted.\n\n";
    
    // The issue is likely that individual invoices have 0.01 differences that accumulate
    // We need to check if the issue is in the transfer_fee_details or in the invoice amounts
    
    echo "Checking individual invoice amounts in transfer_fee_details...\n\n";
    
    // Get invoices from transfer fee details
    $invoiceIds = $details->pluck('loan_case_invoice_main_id')->unique();
    $invoices = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as bm', 'bm.id', '=', 'im.loan_case_main_bill_id')
        ->whereIn('im.id', $invoiceIds)
        ->where('im.status', '<>', 99)
        ->select(
            'im.id',
            'im.invoice_no',
            'im.pfee1_inv',
            'im.pfee2_inv',
            'im.sst_inv',
            'im.reimbursement_amount',
            'im.reimbursement_sst',
            'im.amount'
        )
        ->get();
    
    $invoiceTotalPfee = $invoices->sum(function($inv) {
        return ($inv->pfee1_inv ?? 0) + ($inv->pfee2_inv ?? 0);
    });
    $invoiceTotalSst = $invoices->sum('sst_inv');
    $invoiceTotalReimb = $invoices->sum('reimbursement_amount');
    $invoiceTotalReimbSst = $invoices->sum('reimbursement_sst');
    
    echo "Invoice Totals (from loan_case_invoice_main):\n";
    echo "  Professional Fee: " . number_format($invoiceTotalPfee, 2) . "\n";
    echo "  SST:              " . number_format($invoiceTotalSst, 2) . "\n";
    echo "  Reimbursement:    " . number_format($invoiceTotalReimb, 2) . "\n";
    echo "  Reimbursement SST: " . number_format($invoiceTotalReimbSst, 2) . "\n\n";
    
    echo "Transfer Fee Details Totals:\n";
    echo "  Professional Fee: " . number_format($totalPfee, 2) . "\n";
    echo "  SST:              " . number_format($totalSst, 2) . "\n";
    echo "  Reimbursement:    " . number_format($totalReimbursement, 2) . "\n";
    echo "  Reimbursement SST: " . number_format($totalReimbursementSst, 2) . "\n\n";
    
    $pfeeDetailDiff = $totalPfee - $invoiceTotalPfee;
    $sstDetailDiff = $totalSst - $invoiceTotalSst;
    $reimbDetailDiff = $totalReimbursement - $invoiceTotalReimb;
    $reimbSstDetailDiff = $totalReimbursementSst - $invoiceTotalReimbSst;
    
    echo "Difference (Transfer Details vs Invoice Totals):\n";
    echo "  Professional Fee: " . ($pfeeDetailDiff >= 0 ? '+' : '') . number_format($pfeeDetailDiff, 2) . "\n";
    echo "  SST:              " . ($sstDetailDiff >= 0 ? '+' : '') . number_format($sstDetailDiff, 2) . "\n";
    echo "  Reimbursement:    " . ($reimbDetailDiff >= 0 ? '+' : '') . number_format($reimbDetailDiff, 2) . "\n";
    echo "  Reimbursement SST: " . ($reimbSstDetailDiff >= 0 ? '+' : '') . number_format($reimbSstDetailDiff, 2) . "\n\n";
    
    if (abs($pfeeDetailDiff) > 0.01 || abs($sstDetailDiff) > 0.01 || abs($reimbDetailDiff) > 0.01 || abs($reimbSstDetailDiff) > 0.01) {
        echo "⚠️  The transfer_fee_details amounts don't match the invoice totals!\n";
        echo "This suggests the transfer_fee_details need to be recalculated from invoices.\n\n";
    }
} else {
    echo "✅ All totals match expected values!\n";
}

echo "\n========================================\n";
echo "NOTE\n";
echo "========================================\n";
echo "The 0.01 differences are likely due to:\n";
echo "1. Rounding in split invoices\n";
echo "2. SST rounding rules (round down when 3rd decimal is 5)\n";
echo "3. Accumulation of small differences across many invoices\n\n";
echo "To fix this, you may need to:\n";
echo "1. Re-run the invoice fix for all invoices in this transfer fee\n";
echo "2. Recalculate transfer_fee_details from corrected invoice amounts\n";
echo "3. Or manually adjust the transfer_fee_main totals\n";


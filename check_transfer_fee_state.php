<?php
/**
 * Check Transfer Fee State After Account Tool
 * 
 * Run: php check_transfer_fee_state.php [transfer_fee_id]
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;
use App\Models\LoanCaseInvoiceMain;

$transferFeeId = $argv[1] ?? 472; // DP004-1025

// Expected totals
$expectedPfee = 521831.74;
$expectedSst = 41746.47;
$expectedReimb = 66373.63;
$expectedReimbSst = 5309.91;

echo "========================================\n";
echo "CHECK TRANSFER FEE STATE\n";
echo "========================================\n\n";
echo "Transfer Fee ID: {$transferFeeId}\n\n";

// Get all invoices in this transfer fee
$allDetails = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->where('status', '<>', 99)
    ->get();

$invoiceIds = $allDetails->pluck('loan_case_invoice_main_id')
    ->unique()
    ->filter();

$invoices = LoanCaseInvoiceMain::whereIn('id', $invoiceIds)
    ->where('status', '<>', 99)
    ->get();

echo "Total Invoices: " . $invoices->count() . "\n";
echo "Total Transfer Fee Details: " . $allDetails->count() . "\n\n";

// Calculate current totals from invoices
$currentPfee = $invoices->sum(function($inv) {
    return ($inv->pfee1_inv ?? 0) + ($inv->pfee2_inv ?? 0);
});
$currentSst = $invoices->sum('sst_inv');
$currentReimb = $invoices->sum('reimbursement_amount');
$currentReimbSst = $invoices->sum('reimbursement_sst');

echo "Current Invoice Totals:\n";
echo "  Pfee: " . number_format($currentPfee, 2) . "\n";
echo "  SST:  " . number_format($currentSst, 2) . "\n";
echo "  Reimb: " . number_format($currentReimb, 2) . "\n";
echo "  ReimbSST: " . number_format($currentReimbSst, 2) . "\n\n";

echo "Expected Totals:\n";
echo "  Pfee: " . number_format($expectedPfee, 2) . "\n";
echo "  SST:  " . number_format($expectedSst, 2) . "\n";
echo "  Reimb: " . number_format($expectedReimb, 2) . "\n";
echo "  ReimbSST: " . number_format($expectedReimbSst, 2) . "\n\n";

$pfeeDiff = $expectedPfee - $currentPfee;
$sstDiff = $expectedSst - $currentSst;
$reimbDiff = $expectedReimb - $currentReimb;
$reimbSstDiff = $expectedReimbSst - $currentReimbSst;

echo "Differences:\n";
echo "  Pfee: " . ($pfeeDiff >= 0 ? '+' : '') . number_format($pfeeDiff, 2) . "\n";
echo "  SST:  " . ($sstDiff >= 0 ? '+' : '') . number_format($sstDiff, 2) . "\n";
echo "  Reimb: " . ($reimbDiff >= 0 ? '+' : '') . number_format($reimbDiff, 2) . "\n";
echo "  ReimbSST: " . ($reimbSstDiff >= 0 ? '+' : '') . number_format($reimbSstDiff, 2) . "\n\n";

// Check "to Transfer" columns
$totalPfeeToTransfer = 0;
$totalSstToTransfer = 0;
$totalReimbToTransfer = 0;
$totalReimbSstToTransfer = 0;

$invoicesWithIssues = [];

foreach ($invoices as $invoice) {
    $invoicePfee = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0);
    $transferredPfee = $invoice->transferred_pfee_amt ?? 0;
    $pfeeToTransfer = $invoicePfee - $transferredPfee;
    
    $sstToTransfer = ($invoice->sst_inv ?? 0) - ($invoice->transferred_sst_amt ?? 0);
    $reimbToTransfer = ($invoice->reimbursement_amount ?? 0) - ($invoice->transferred_reimbursement_amt ?? 0);
    $reimbSstToTransfer = ($invoice->reimbursement_sst ?? 0) - ($invoice->transferred_reimbursement_sst_amt ?? 0);
    
    if (abs($pfeeToTransfer) > 0.01 || abs($sstToTransfer) > 0.01 || 
        abs($reimbToTransfer) > 0.01 || abs($reimbSstToTransfer) > 0.01) {
        $invoicesWithIssues[] = [
            'invoice_no' => $invoice->invoice_no,
            'pfee_to_transfer' => $pfeeToTransfer,
            'sst_to_transfer' => $sstToTransfer,
            'reimb_to_transfer' => $reimbToTransfer,
            'reimb_sst_to_transfer' => $reimbSstToTransfer
        ];
    }
    
    $totalPfeeToTransfer += max(0, $pfeeToTransfer);
    $totalSstToTransfer += max(0, $sstToTransfer);
    $totalReimbToTransfer += max(0, $reimbToTransfer);
    $totalReimbSstToTransfer += max(0, $reimbSstToTransfer);
}

echo "Total 'To Transfer':\n";
echo "  Pfee: " . number_format($totalPfeeToTransfer, 2) . "\n";
echo "  SST:  " . number_format($totalSstToTransfer, 2) . "\n";
echo "  Reimb: " . number_format($totalReimbToTransfer, 2) . "\n";
echo "  ReimbSST: " . number_format($totalReimbSstToTransfer, 2) . "\n\n";

if (count($invoicesWithIssues) > 0) {
    echo "Invoices with 'To Transfer' issues (" . count($invoicesWithIssues) . "):\n";
    foreach (array_slice($invoicesWithIssues, 0, 10) as $issue) {
        echo "  {$issue['invoice_no']}: Pfee=" . number_format($issue['pfee_to_transfer'], 2) . 
             ", SST=" . number_format($issue['sst_to_transfer'], 2) . 
             ", Reimb=" . number_format($issue['reimb_to_transfer'], 2) . 
             ", ReimbSST=" . number_format($issue['reimb_sst_to_transfer'], 2) . "\n";
    }
    if (count($invoicesWithIssues) > 10) {
        echo "  ... and " . (count($invoicesWithIssues) - 10) . " more\n";
    }
    echo "\n";
}

// Check transfer_fee_details totals
$detailsPfee = $allDetails->sum('transfer_amount');
$detailsSst = $allDetails->sum('sst_amount');
$detailsReimb = $allDetails->sum('reimbursement_amount');
$detailsReimbSst = $allDetails->sum('reimbursement_sst_amount');

echo "Transfer Fee Details Totals:\n";
echo "  Pfee: " . number_format($detailsPfee, 2) . "\n";
echo "  SST:  " . number_format($detailsSst, 2) . "\n";
echo "  Reimb: " . number_format($detailsReimb, 2) . "\n";
echo "  ReimbSST: " . number_format($detailsReimbSst, 2) . "\n\n";

echo "Summary:\n";
if (abs($pfeeDiff) <= 0.01 && abs($sstDiff) <= 0.01 && 
    abs($reimbDiff) <= 0.01 && abs($reimbSstDiff) <= 0.01) {
    echo "  ✅ Totals match expected values\n";
} else {
    echo "  ❌ Totals do NOT match expected values\n";
}

if ($totalPfeeToTransfer < 0.01 && $totalSstToTransfer < 0.01 &&
    $totalReimbToTransfer < 0.01 && $totalReimbSstToTransfer < 0.01) {
    echo "  ✅ 'To Transfer' columns are 0.00\n";
} else {
    echo "  ❌ 'To Transfer' columns have non-zero values\n";
}




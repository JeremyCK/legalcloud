<?php
/**
 * Diagnostic Script for DP004-1025
 * Shows current state and what needs to be fixed
 * Run this FIRST to see what's wrong
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;
use App\Models\LoanCaseInvoiceMain;

$transferFeeId = 472;

// Expected values
$expectedPfee = 521831.74;
$expectedSst = 41746.47;
$expectedReimb = 66373.63;
$expectedReimbSst = 5309.91;

echo "========================================\n";
echo "DP004-1025 DIAGNOSTIC REPORT\n";
echo "========================================\n\n";

// Get all invoices
$details = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->where('status', '<>', 99)
    ->pluck('loan_case_invoice_main_id')
    ->unique()
    ->filter();

$invoices = LoanCaseInvoiceMain::whereIn('id', $details)
    ->where('status', '<>', 99)
    ->get();

echo "Total Invoices: " . $invoices->count() . "\n\n";

// Calculate totals from invoices (as the report does)
$totalPfee = $invoices->sum(function($inv) {
    return ($inv->pfee1_inv ?? 0) + ($inv->pfee2_inv ?? 0);
});
$totalSst = $invoices->sum('sst_inv');
$totalReimb = $invoices->sum('reimbursement_amount');
$totalReimbSst = $invoices->sum('reimbursement_sst');

echo "========================================\n";
echo "CURRENT TOTALS (from invoices)\n";
echo "========================================\n";
echo "Professional Fee:     " . number_format($totalPfee, 2) . "\n";
echo "SST:                  " . number_format($totalSst, 2) . "\n";
echo "Reimbursement:        " . number_format($totalReimb, 2) . "\n";
echo "Reimbursement SST:    " . number_format($totalReimbSst, 2) . "\n\n";

echo "========================================\n";
echo "EXPECTED TOTALS\n";
echo "========================================\n";
echo "Professional Fee:     " . number_format($expectedPfee, 2) . "\n";
echo "SST:                  " . number_format($expectedSst, 2) . "\n";
echo "Reimbursement:        " . number_format($expectedReimb, 2) . "\n";
echo "Reimbursement SST:    " . number_format($expectedReimbSst, 2) . "\n\n";

$pfeeDiff = $totalPfee - $expectedPfee;
$sstDiff = $totalSst - $expectedSst;
$reimbDiff = $totalReimb - $expectedReimb;
$reimbSstDiff = $totalReimbSst - $expectedReimbSst;

echo "========================================\n";
echo "DIFFERENCES\n";
echo "========================================\n";
echo "Professional Fee:     " . ($pfeeDiff >= 0 ? '+' : '') . number_format($pfeeDiff, 2) . "\n";
echo "SST:                  " . ($sstDiff >= 0 ? '+' : '') . number_format($sstDiff, 2) . "\n";
echo "Reimbursement:        " . ($reimbDiff >= 0 ? '+' : '') . number_format($reimbDiff, 2) . "\n";
echo "Reimbursement SST:    " . ($reimbSstDiff >= 0 ? '+' : '') . number_format($reimbSstDiff, 2) . "\n\n";

// Calculate "to Transfer" totals (as the report does)
$totalPfeeToTransfer = 0;
$totalSstToTransfer = 0;
$totalReimbToTransfer = 0;
$totalReimbSstToTransfer = 0;

$issues = [];

foreach ($invoices as $invoice) {
    $invoicePfee = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0);
    $invoiceSst = $invoice->sst_inv ?? 0;
    $invoiceReimb = $invoice->reimbursement_amount ?? 0;
    $invoiceReimbSst = $invoice->reimbursement_sst ?? 0;
    
    $transferredPfee = $invoice->transferred_pfee_amt ?? 0;
    $transferredSst = $invoice->transferred_sst_amt ?? 0;
    $transferredReimb = $invoice->transferred_reimbursement_amt ?? 0;
    $transferredReimbSst = $invoice->transferred_reimbursement_sst_amt ?? 0;
    
    // Calculate "to Transfer" (same as report)
    $pfeeToTransfer = max(0, $invoicePfee - $transferredPfee);
    $sstToTransfer = max(0, $invoiceSst - $transferredSst);
    $reimbToTransfer = max(0, $invoiceReimb - $transferredReimb);
    $reimbSstToTransfer = max(0, $invoiceReimbSst - $transferredReimbSst);
    
    $totalPfeeToTransfer += $pfeeToTransfer;
    $totalSstToTransfer += $sstToTransfer;
    $totalReimbToTransfer += $reimbToTransfer;
    $totalReimbSstToTransfer += $reimbSstToTransfer;
    
    if (abs($pfeeToTransfer) > 0.01 || abs($sstToTransfer) > 0.01 || 
        abs($reimbToTransfer) > 0.01 || abs($reimbSstToTransfer) > 0.01) {
        $issues[] = [
            'invoice' => $invoice->invoice_no,
            'pfee_to_transfer' => $pfeeToTransfer,
            'sst_to_transfer' => $sstToTransfer,
            'reimb_to_transfer' => $reimbToTransfer,
            'reimb_sst_to_transfer' => $reimbSstToTransfer
        ];
    }
}

echo "========================================\n";
echo "TOTAL 'TO TRANSFER' AMOUNTS\n";
echo "========================================\n";
echo "Professional Fee:     " . number_format($totalPfeeToTransfer, 2) . "\n";
echo "SST:                  " . number_format($totalSstToTransfer, 2) . "\n";
echo "Reimbursement:        " . number_format($totalReimbToTransfer, 2) . "\n";
echo "Reimbursement SST:    " . number_format($totalReimbSstToTransfer, 2) . "\n\n";

if (count($issues) > 0) {
    echo "========================================\n";
    echo "INVOICES WITH 'TO TRANSFER' > 0.01\n";
    echo "========================================\n";
    echo "Total: " . count($issues) . " invoices\n\n";
    
    foreach (array_slice($issues, 0, 20) as $issue) {
        echo "{$issue['invoice']}: Pfee={$issue['pfee_to_transfer']}, SST={$issue['sst_to_transfer']}, Reimb={$issue['reimb_to_transfer']}, ReimbSST={$issue['reimb_sst_to_transfer']}\n";
    }
    
    if (count($issues) > 20) {
        echo "... and " . (count($issues) - 20) . " more\n";
    }
    echo "\n";
}

// Summary
echo "========================================\n";
echo "SUMMARY\n";
echo "========================================\n";

$hasTotalIssues = (abs($pfeeDiff) > 0.01 || abs($sstDiff) > 0.01 || 
                   abs($reimbDiff) > 0.01 || abs($reimbSstDiff) > 0.01);

$hasTransferIssues = ($totalPfeeToTransfer > 0.01 || $totalSstToTransfer > 0.01 || 
                      $totalReimbToTransfer > 0.01 || $totalReimbSstToTransfer > 0.01);

if (!$hasTotalIssues && !$hasTransferIssues) {
    echo "✅ Everything looks correct!\n";
    echo "   - Totals match expected values\n";
    echo "   - 'To Transfer' columns are 0.00\n";
} else {
    if ($hasTotalIssues) {
        echo "❌ Totals don't match expected values\n";
        echo "   Need to adjust invoice amounts\n";
    }
    
    if ($hasTransferIssues) {
        echo "❌ 'To Transfer' columns show non-zero values\n";
        echo "   Need to sync invoice.transferred_* with transfer_fee_details\n";
    }
    
    echo "\n";
    echo "Run: php fix_dp004_1025_correct_approach.php\n";
    echo "This will fix both issues.\n";
}


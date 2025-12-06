<?php
/**
 * Verification script for SST Record 96 fix
 * Run this after applying the fix to verify everything is correct
 * 
 * Usage:
 * php artisan tinker
 * require 'verify_sst_96_fix.php';
 */

use Illuminate\Support\Facades\DB;
use App\Models\SSTMain;
use App\Models\SSTDetails;
use App\Models\LoanCaseInvoiceMain;

$sstMainId = 96;

echo "=== VERIFYING SST RECORD ID {$sstMainId} ===\n\n";

// 1. Check SST Main
$sstMain = SSTMain::find($sstMainId);
if (!$sstMain) {
    echo "❌ SST Record {$sstMainId} not found!\n";
    exit;
}

echo "SST Main:\n";
echo "  ID: {$sstMain->id}\n";
echo "  Payment Date: {$sstMain->payment_date}\n";
echo "  Transaction ID: {$sstMain->transaction_id}\n";
echo "  Stored Amount: " . number_format($sstMain->amount ?? 0, 2) . "\n\n";

// 2. Check SST Details using the same query as the controller
$SSTDetails = DB::table('sst_details as sd')
    ->leftJoin('loan_case_invoice_main as im', 'im.id', '=', 'sd.loan_case_invoice_main_id')
    ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
    ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
    ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
    ->where('sd.sst_main_id', $sstMainId)
    ->select(
        'sd.*',
        'im.invoice_no',
        'im.Invoice_date as invoice_date',
        'im.amount as total_amount',
        'im.pfee1_inv as pfee1',
        'im.pfee2_inv as pfee2',
        'im.reimbursement_sst',
        'im.transferred_reimbursement_sst_amt',
        'b.collected_amt as collected_amount',
        'b.payment_receipt_date as payment_date',
        'l.case_ref_no',
        'l.id as case_id',
        'c.name as client_name'
    )
    ->get();

echo "SST Details Count: {$SSTDetails->count()}\n\n";

if ($SSTDetails->count() == 0) {
    echo "⚠️  No SST details found!\n";
    exit;
}

// 3. Check each detail record
$totalSST = 0;
$totalReimbSST = 0;
$grandTotal = 0;
$issues = [];

echo "Checking each invoice:\n";
foreach ($SSTDetails as $index => $detail) {
    $sstAmount = $detail->amount ?? 0;
    $reimbursementSst = $detail->reimbursement_sst ?? 0;
    $transferredReimbSst = $detail->transferred_reimbursement_sst_amt ?? 0;
    $remainingReimbSst = max(0, $reimbursementSst - $transferredReimbSst);
    $totalSstRow = $sstAmount + $remainingReimbSst;
    
    $totalSST += $sstAmount;
    $totalReimbSST += $remainingReimbSst;
    $grandTotal += $totalSstRow;
    
    // Check for issues
    $status = "✅";
    $issueMsg = "";
    
    if ($sstAmount == 0 && ($detail->sst_inv ?? 0) > 0) {
        $status = "❌";
        $issueMsg = "SST amount is 0 but invoice has SST";
        $issues[] = "Invoice {$detail->invoice_no}: {$issueMsg}";
    } elseif ($sstAmount != ($detail->sst_inv ?? 0)) {
        $status = "⚠️";
        $issueMsg = "SST amount doesn't match invoice sst_inv";
        $issues[] = "Invoice {$detail->invoice_no}: {$issueMsg}";
    }
    
    echo "  {$status} Invoice #" . ($index + 1) . ": {$detail->invoice_no}\n";
    echo "      SST: " . number_format($sstAmount, 2) . "\n";
    echo "      Reimb SST: " . number_format($remainingReimbSst, 2) . "\n";
    echo "      Total SST: " . number_format($totalSstRow, 2) . "\n";
    if ($issueMsg) {
        echo "      Issue: {$issueMsg}\n";
    }
}

echo "\n";
echo "Totals:\n";
echo "  Total SST: " . number_format($totalSST, 2) . "\n";
echo "  Total Reimb SST: " . number_format($totalReimbSST, 2) . "\n";
echo "  Grand Total: " . number_format($grandTotal, 2) . "\n\n";

echo "Comparison:\n";
echo "  Stored Amount (sst_main.amount): " . number_format($sstMain->amount ?? 0, 2) . "\n";
echo "  Calculated Grand Total: " . number_format($grandTotal, 2) . "\n";

$difference = abs(($sstMain->amount ?? 0) - $grandTotal);
if ($difference < 0.01) {
    echo "  ✅ Amounts match!\n";
} else {
    echo "  ⚠️  MISMATCH! Difference: " . number_format($difference, 2) . "\n";
}

echo "\n";

if (count($issues) > 0) {
    echo "Issues found:\n";
    foreach ($issues as $issue) {
        echo "  - {$issue}\n";
    }
    echo "\n";
    echo "❌ Fix needed! Run fix_sst_96_amounts.php or fix_sst_96_amounts.sql\n";
} else {
    echo "✅ All checks passed! SST Record 96 is correctly configured.\n";
    echo "\n";
    echo "You can now view the page at: http://127.0.0.1:8000/sst-v2-edit/96\n";
    echo "All SST amounts should display correctly.\n";
}





<?php
/**
 * Fix SST Record 96: Update sst_details.amount from invoice.sst_inv
 * 
 * This script will:
 * 1. Update sst_details.amount to match invoice.sst_inv where amount is 0, NULL, or doesn't match
 * 2. Recalculate sst_main.amount to include both SST and reimbursement SST
 * 
 * Usage:
 * php artisan tinker
 * require 'fix_sst_96_amounts.php';
 */

use Illuminate\Support\Facades\DB;
use App\Models\SSTMain;
use App\Models\SSTDetails;
use App\Models\LoanCaseInvoiceMain;

$sstMainId = 96;

echo "=== FIXING SST RECORD ID {$sstMainId} ===\n\n";

// 1. Check SST Main Record
$sstMain = SSTMain::find($sstMainId);
if (!$sstMain) {
    echo "❌ SST Record {$sstMainId} not found!\n";
    exit;
}

echo "✅ SST Main Record found: ID={$sstMain->id}, Current Amount=" . number_format($sstMain->amount ?? 0, 2) . "\n\n";

// 2. Get all SST Details for this record
$sstDetails = SSTDetails::where('sst_main_id', $sstMainId)->get();
echo "Found {$sstDetails->count()} SST detail records\n\n";

if ($sstDetails->count() == 0) {
    echo "⚠️  No SST details found. Nothing to fix.\n";
    exit;
}

// 3. Update sst_details.amount from invoice.sst_inv
$updatedCount = 0;
$totalSST = 0;
$totalReimbSST = 0;

echo "Updating SST Details:\n";
foreach ($sstDetails as $detail) {
    $invoice = LoanCaseInvoiceMain::find($detail->loan_case_invoice_main_id);
    
    if (!$invoice) {
        echo "  ⚠️  Detail ID {$detail->id}: Invoice ID {$detail->loan_case_invoice_main_id} not found\n";
        continue;
    }
    
    $invoiceSstAmount = $invoice->sst_inv ?? 0;
    $currentAmount = $detail->amount ?? 0;
    
    // Check if update is needed
    if ($currentAmount == 0 || $currentAmount != $invoiceSstAmount) {
        $detail->amount = $invoiceSstAmount;
        $detail->updated_at = now();
        $detail->save();
        
        echo "  ✅ Detail ID {$detail->id}: Updated amount from " . number_format($currentAmount, 2) . " to " . number_format($invoiceSstAmount, 2) . " (Invoice: {$invoice->invoice_no})\n";
        $updatedCount++;
    } else {
        echo "  ✓ Detail ID {$detail->id}: Amount already correct (" . number_format($currentAmount, 2) . ")\n";
    }
    
    // Calculate totals
    $totalSST += $invoiceSstAmount;
    
    // Calculate remaining reimbursement SST
    $reimbursementSst = $invoice->reimbursement_sst ?? 0;
    $transferredReimbSst = $invoice->transferred_reimbursement_sst_amt ?? 0;
    $remainingReimbSst = max(0, $reimbursementSst - $transferredReimbSst);
    $totalReimbSST += $remainingReimbSst;
}

echo "\n";
echo "Summary:\n";
echo "  Records updated: {$updatedCount}\n";
echo "  Total SST: " . number_format($totalSST, 2) . "\n";
echo "  Total Remaining Reimb SST: " . number_format($totalReimbSST, 2) . "\n";
echo "  Grand Total: " . number_format($totalSST + $totalReimbSST, 2) . "\n\n";

// 4. Update sst_main.amount
$grandTotal = $totalSST + $totalReimbSST;
$oldAmount = $sstMain->amount ?? 0;

$sstMain->amount = $grandTotal;
$sstMain->updated_at = now();
$sstMain->save();

echo "Updated SST Main:\n";
echo "  Old Amount: " . number_format($oldAmount, 2) . "\n";
echo "  New Amount: " . number_format($grandTotal, 2) . "\n";
echo "  Difference: " . number_format($grandTotal - $oldAmount, 2) . "\n\n";

echo "✅ Fix completed successfully!\n";
echo "\n";
echo "Next steps:\n";
echo "1. Refresh the page: http://127.0.0.1:8000/sst-v2-edit/96\n";
echo "2. Verify that SST amounts are now showing correctly\n";
echo "3. Check that Total Amount matches the calculated grand total\n";













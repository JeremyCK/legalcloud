<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;

$transferFeeId = 484;

echo "=== Suggesting Fixes for Transfer Fee ID: {$transferFeeId} ===\n\n";

// Get all transfer fee details
$transferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->leftJoin('loan_case_invoice_main as im', 'im.id', '=', 'transfer_fee_details.loan_case_invoice_main_id')
    ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'transfer_fee_details.loan_case_main_bill_id')
    ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
    ->select(
        'transfer_fee_details.id',
        'transfer_fee_details.transfer_amount',
        'transfer_fee_details.sst_amount',
        'transfer_fee_details.reimbursement_amount',
        'transfer_fee_details.reimbursement_sst_amount',
        'im.invoice_no',
        'l.case_ref_no',
        'im.pfee1_inv',
        'im.pfee2_inv',
        'im.sst_inv',
        'im.reimbursement_amount as invoice_reimbursement_amount',
        'im.reimbursement_sst as invoice_reimbursement_sst'
    )
    ->orderBy('transfer_fee_details.id')
    ->get();

$totalCalculated = 0;
$expectedTotal = 616549.16;
$allEntries = [];

foreach ($transferFeeDetails as $detail) {
    $transferAmount = floatval($detail->transfer_amount ?? 0);
    $sstAmount = floatval($detail->sst_amount ?? 0);
    $reimbursementAmount = floatval($detail->reimbursement_amount ?? 0);
    $reimbursementSstAmount = floatval($detail->reimbursement_sst_amount ?? 0);
    
    $rowTotal = $transferAmount + $sstAmount + $reimbursementAmount + $reimbursementSstAmount;
    $totalCalculated += $rowTotal;
    
    $allEntries[] = [
        'id' => $detail->id,
        'invoice' => $detail->invoice_no ?? 'N/A',
        'ref' => $detail->case_ref_no ?? 'N/A',
        'transfer_amount' => $transferAmount,
        'sst_amount' => $sstAmount,
        'reimbursement_amount' => $reimbursementAmount,
        'reimbursement_sst_amount' => $reimbursementSstAmount,
        'row_total' => $rowTotal,
        'invoice_pfee' => floatval($detail->pfee1_inv ?? 0) + floatval($detail->pfee2_inv ?? 0),
        'invoice_sst' => floatval($detail->sst_inv ?? 0),
        'invoice_reimb' => floatval($detail->invoice_reimbursement_amount ?? 0),
        'invoice_reimb_sst' => floatval($detail->invoice_reimbursement_sst ?? 0)
    ];
}

$difference = $totalCalculated - $expectedTotal; // Should be 0.09

echo "Current Total: " . number_format($totalCalculated, 2) . "\n";
echo "Expected Total: " . number_format($expectedTotal, 2) . "\n";
echo "Difference: " . number_format($difference, 2) . "\n\n";

echo "To fix this, we need to REDUCE the total by 0.09.\n\n";

// Strategy: Look for entries where the stored values don't match what they should be
// based on the invoice amounts, or where rounding might have caused issues

echo "=== Checking entries against invoice amounts ===\n\n";
$entriesWithMismatch = [];

foreach ($allEntries as $entry) {
    // Check if stored transfer_amount matches invoice pfee
    // Note: This might not always match if amounts were split or partially transferred
    // But we're looking for obvious discrepancies
    
    $issues = [];
    
    // Check if values seem rounded incorrectly
    // Look for entries where the sum of components might have rounding issues
    $storedTotal = $entry['row_total'];
    $roundedTransfer = round($entry['transfer_amount'], 2);
    $roundedSst = round($entry['sst_amount'], 2);
    $roundedReimb = round($entry['reimbursement_amount'], 2);
    $roundedReimbSst = round($entry['reimbursement_sst_amount'], 2);
    
    $sumOfRounded = $roundedTransfer + $roundedSst + $roundedReimb + $roundedReimbSst;
    $roundedTotal = round($storedTotal, 2);
    
    // Check for values that might have been stored with incorrect rounding
    // Specifically look for values that end in .x5 which round up
    $transferStr = number_format($entry['transfer_amount'], 2, '.', '');
    $sstStr = number_format($entry['sst_amount'], 2, '.', '');
    $reimbStr = number_format($entry['reimbursement_amount'], 2, '.', '');
    $reimbSstStr = number_format($entry['reimbursement_sst_amount'], 2, '.', '');
    
    // Check if any value ends in .x5 (which always rounds up)
    $hasRoundingUp = false;
    if (substr($transferStr, -1) == '5' && substr($transferStr, -3, 1) == '.') {
        $hasRoundingUp = true;
        $issues[] = "Transfer amount {$transferStr} ends in .x5 (rounds up)";
    }
    if (substr($sstStr, -1) == '5' && substr($sstStr, -3, 1) == '.') {
        $hasRoundingUp = true;
        $issues[] = "SST amount {$sstStr} ends in .x5 (rounds up)";
    }
    if (substr($reimbStr, -1) == '5' && substr($reimbStr, -3, 1) == '.') {
        $hasRoundingUp = true;
        $issues[] = "Reimbursement amount {$reimbStr} ends in .x5 (rounds up)";
    }
    if (substr($reimbSstStr, -1) == '5' && substr($reimbSstStr, -3, 1) == '.') {
        $hasRoundingUp = true;
        $issues[] = "Reimbursement SST {$reimbSstStr} ends in .x5 (rounds up)";
    }
    
    if ($hasRoundingUp) {
        $entriesWithMismatch[] = [
            'entry' => $entry,
            'issues' => $issues
        ];
    }
}

// Now, let's try to find a combination of entries that sum to 0.09
// We'll look for entries where reducing by 0.01 increments would help

echo "=== Potential Fixes ===\n\n";
echo "Since we need to reduce by 0.09, here are some options:\n\n";

// Option 1: Reduce one entry by 0.09
echo "Option 1: Reduce ONE entry by 0.09\n";
echo "This would require finding an entry that was incorrectly increased by 0.09.\n";
echo "However, this is unlikely as individual amounts are typically smaller increments.\n\n";

// Option 2: Reduce multiple entries by small amounts (0.01 each)
echo "Option 2: Reduce MULTIPLE entries by 0.01 each (9 entries × 0.01 = 0.09)\n";
echo "Look for entries with values ending in .x5 that might have been rounded up incorrectly.\n\n";

// Option 3: Reduce one entry by 0.05 and another by 0.04
echo "Option 3: Reduce entries by combinations like:\n";
echo "  - One entry by 0.05 + another by 0.04 = 0.09\n";
echo "  - One entry by 0.06 + another by 0.03 = 0.09\n";
echo "  - One entry by 0.07 + another by 0.02 = 0.09\n";
echo "  - One entry by 0.08 + another by 0.01 = 0.09\n\n";

// Show entries that end in .x5 (candidates for reduction)
echo "=== Candidates for Adjustment (entries ending in .x5) ===\n\n";
$candidates = [];
foreach ($allEntries as $entry) {
    $transferStr = number_format($entry['transfer_amount'], 2, '.', '');
    $sstStr = number_format($entry['sst_amount'], 2, '.', '');
    $reimbStr = number_format($entry['reimbursement_amount'], 2, '.', '');
    $reimbSstStr = number_format($entry['reimbursement_sst_amount'], 2, '.', '');
    
    $candidate = false;
    $details = [];
    
    if (substr($transferStr, -1) == '5' && substr($transferStr, -3, 1) == '.') {
        $candidate = true;
        $details[] = "Transfer: {$transferStr}";
    }
    if (substr($sstStr, -1) == '5' && substr($sstStr, -3, 1) == '.') {
        $candidate = true;
        $details[] = "SST: {$sstStr}";
    }
    if (substr($reimbStr, -1) == '5' && substr($reimbStr, -3, 1) == '.') {
        $candidate = true;
        $details[] = "Reimb: {$reimbStr}";
    }
    if (substr($reimbSstStr, -1) == '5' && substr($reimbSstStr, -3, 1) == '.') {
        $candidate = true;
        $details[] = "ReimbSST: {$reimbSstStr}";
    }
    
    if ($candidate) {
        $candidates[] = [
            'id' => $entry['id'],
            'invoice' => $entry['invoice'],
            'ref' => $entry['ref'],
            'row_total' => $entry['row_total'],
            'details' => $details
        ];
    }
}

echo "Found " . count($candidates) . " entries with values ending in .x5:\n\n";
foreach ($candidates as $candidate) {
    echo "ID: {$candidate['id']} | Invoice: {$candidate['invoice']} | Ref: {$candidate['ref']} | Total: " . number_format($candidate['row_total'], 2) . "\n";
    foreach ($candidate['details'] as $detail) {
        echo "  - {$detail}\n";
    }
    echo "\n";
}

// Final recommendation
echo "=== Recommendation ===\n\n";
echo "To fix the 0.09 difference, you have two options:\n\n";
echo "1. **Manual Database Fix**: Identify which entries should be reduced and update them.\n";
echo "   Example SQL:\n";
echo "   UPDATE transfer_fee_details SET transfer_amount = transfer_amount - 0.01 WHERE id = [ID];\n";
echo "   (Repeat for 9 entries, or adjust one entry by 0.09 if appropriate)\n\n";
echo "2. **Fix Calculation Logic**: Review the code that calculates these amounts to ensure\n";
echo "   consistent rounding. The issue might be in how amounts are calculated from invoices.\n\n";
echo "**Important**: Before making any changes, verify which total is correct:\n";
echo "  - Is 616,549.16 the correct total from your source?\n";
echo "  - Or is 616,549.25 the correct calculated total?\n\n";
echo "If 616,549.16 is correct, then reduce entries totaling 0.09.\n";
echo "If 616,549.25 is correct, then update the expected total.\n";

<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;

$transferFeeId = 484;

echo "=== Detailed Analysis for Transfer Fee ID: {$transferFeeId} ===\n\n";

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
$totalExpected = 616549.16;
$rowNumber = 1;
$suspiciousEntries = [];

echo "Checking each entry for rounding discrepancies...\n\n";

foreach ($transferFeeDetails as $detail) {
    $transferAmount = floatval($detail->transfer_amount ?? 0);
    $sstAmount = floatval($detail->sst_amount ?? 0);
    $reimbursementAmount = floatval($detail->reimbursement_amount ?? 0);
    $reimbursementSstAmount = floatval($detail->reimbursement_sst_amount ?? 0);
    
    $rowTotal = $transferAmount + $sstAmount + $reimbursementAmount + $reimbursementSstAmount;
    $totalCalculated += $rowTotal;
    
    // Check if the values match what they should be based on invoice amounts
    $invoicePfee = floatval($detail->pfee1_inv ?? 0) + floatval($detail->pfee2_inv ?? 0);
    $invoiceSst = floatval($detail->sst_inv ?? 0);
    $invoiceReimb = floatval($detail->invoice_reimbursement_amount ?? 0);
    $invoiceReimbSst = floatval($detail->invoice_reimbursement_sst ?? 0);
    
    // Check for rounding issues - compare stored values with what they should be if rounded
    $roundedTransfer = round($transferAmount, 2);
    $roundedSst = round($sstAmount, 2);
    $roundedReimb = round($reimbursementAmount, 2);
    $roundedReimbSst = round($reimbursementSstAmount, 2);
    
    // Check if any value has precision beyond 2 decimals
    $transferStr = (string)$transferAmount;
    $sstStr = (string)$sstAmount;
    $reimbStr = (string)$reimbursementAmount;
    $reimbSstStr = (string)$reimbursementSstAmount;
    
    $transferDecimals = strpos($transferStr, '.') !== false ? strlen(substr($transferStr, strpos($transferStr, '.') + 1)) : 0;
    $sstDecimals = strpos($sstStr, '.') !== false ? strlen(substr($sstStr, strpos($sstStr, '.') + 1)) : 0;
    $reimbDecimals = strpos($reimbStr, '.') !== false ? strlen(substr($reimbStr, strpos($reimbStr, '.') + 1)) : 0;
    $reimbSstDecimals = strpos($reimbSstStr, '.') !== false ? strlen(substr($reimbSstStr, strpos($reimbSstStr, '.') + 1)) : 0;
    
    // Check for values that might have been rounded incorrectly
    $hasIssue = false;
    $issues = [];
    
    // Check if rounding each component and summing gives different result than summing then rounding
    $sumOfRounded = round($transferAmount, 2) + round($sstAmount, 2) + round($reimbursementAmount, 2) + round($reimbursementSstAmount, 2);
    $roundedSum = round($rowTotal, 2);
    
    if (abs($sumOfRounded - $roundedSum) > 0.0001) {
        $hasIssue = true;
        $issues[] = "Rounding order issue: sum of rounded ({$sumOfRounded}) != rounded sum ({$roundedSum})";
    }
    
    // Check for precision issues
    if ($transferDecimals > 2 || $sstDecimals > 2 || $reimbDecimals > 2 || $reimbSstDecimals > 2) {
        $hasIssue = true;
        $issues[] = "Precision issue: Transfer has {$transferDecimals} decimals, SST has {$sstDecimals}, Reimb has {$reimbDecimals}, ReimbSST has {$reimbSstDecimals}";
    }
    
    // Check if values don't match rounded versions (stored with extra precision)
    if (abs($transferAmount - $roundedTransfer) > 0.0001 || 
        abs($sstAmount - $roundedSst) > 0.0001 || 
        abs($reimbursementAmount - $roundedReimb) > 0.0001 || 
        abs($reimbursementSstAmount - $roundedReimbSst) > 0.0001) {
        $hasIssue = true;
        $issues[] = "Value precision mismatch detected";
    }
    
    if ($hasIssue) {
        $suspiciousEntries[] = [
            'row' => $rowNumber,
            'id' => $detail->id,
            'invoice' => $detail->invoice_no ?? 'N/A',
            'ref' => $detail->case_ref_no ?? 'N/A',
            'transfer_amount' => $transferAmount,
            'sst_amount' => $sstAmount,
            'reimbursement_amount' => $reimbursementAmount,
            'reimbursement_sst_amount' => $reimbursementSstAmount,
            'row_total' => $rowTotal,
            'issues' => $issues
        ];
    }
    
    $rowNumber++;
}

echo "=== Summary ===\n\n";
echo "Total Calculated: " . number_format($totalCalculated, 2) . "\n";
echo "Expected Total: " . number_format($totalExpected, 2) . "\n";
echo "Difference: " . number_format($totalExpected - $totalCalculated, 2) . "\n\n";

if (count($suspiciousEntries) > 0) {
    echo "=== Entries with Potential Issues ===\n\n";
    foreach ($suspiciousEntries as $entry) {
        echo "Row {$entry['row']} (ID: {$entry['id']}, Invoice: {$entry['invoice']}, Ref: {$entry['ref']}):\n";
        echo "  Transfer: {$entry['transfer_amount']}, SST: {$entry['sst_amount']}, Reimb: {$entry['reimbursement_amount']}, ReimbSST: {$entry['reimbursement_sst_amount']}\n";
        echo "  Row Total: {$entry['row_total']}\n";
        foreach ($entry['issues'] as $issue) {
            echo "  ⚠️  {$issue}\n";
        }
        echo "\n";
    }
} else {
    echo "No obvious precision issues found in individual entries.\n\n";
}

// Now let's try a different approach - check if the issue is cumulative rounding
echo "=== Cumulative Rounding Analysis ===\n\n";
$cumulativeSum = 0;
$cumulativeRoundedSum = 0;
$rowNumber = 1;
$entriesContributingToDifference = [];

foreach ($transferFeeDetails as $detail) {
    $transferAmount = floatval($detail->transfer_amount ?? 0);
    $sstAmount = floatval($detail->sst_amount ?? 0);
    $reimbursementAmount = floatval($detail->reimbursement_amount ?? 0);
    $reimbursementSstAmount = floatval($detail->reimbursement_sst_amount ?? 0);
    
    $rowTotal = $transferAmount + $sstAmount + $reimbursementAmount + $reimbursementSstAmount;
    
    $cumulativeSum += $rowTotal;
    $cumulativeRoundedSum += round($rowTotal, 2);
    
    // Check if this row contributes to the difference
    $diffAtThisPoint = $cumulativeSum - $cumulativeRoundedSum;
    
    if (abs($diffAtThisPoint) > 0.0001) {
        $entriesContributingToDifference[] = [
            'row' => $rowNumber,
            'id' => $detail->id,
            'invoice' => $detail->invoice_no ?? 'N/A',
            'ref' => $detail->case_ref_no ?? 'N/A',
            'row_total' => $rowTotal,
            'cumulative_sum' => $cumulativeSum,
            'cumulative_rounded' => $cumulativeRoundedSum,
            'diff' => $diffAtThisPoint
        ];
    }
    
    $rowNumber++;
}

if (count($entriesContributingToDifference) > 0) {
    echo "Entries contributing to rounding difference:\n\n";
    foreach ($entriesContributingToDifference as $entry) {
        printf("Row %3d | ID: %5d | Invoice: %-20s | Row Total: %12.2f | Cumulative Diff: %12.10f\n",
            $entry['row'],
            $entry['id'],
            $entry['invoice'],
            $entry['row_total'],
            $entry['diff']
        );
    }
} else {
    echo "No cumulative rounding issues detected.\n";
}

// Final check: Let's see if we can find the exact 0.09 difference
echo "\n=== Finding the 0.09 Difference ===\n\n";
$targetDifference = 0.09;
$currentDifference = $totalCalculated - $totalExpected;

echo "Current difference: " . number_format($currentDifference, 10) . "\n";
echo "Target difference: " . number_format($targetDifference, 10) . "\n";
echo "Actual difference: " . number_format($currentDifference, 2) . "\n\n";

// Try to find entries that when rounded differently would account for the difference
echo "Checking if rounding individual components vs rounding sum causes the issue...\n\n";

$sumOfRoundedComponents = 0;
$sumThenRound = 0;
$rowNumber = 1;
$roundingDifferences = [];

foreach ($transferFeeDetails as $detail) {
    $transferAmount = floatval($detail->transfer_amount ?? 0);
    $sstAmount = floatval($detail->sst_amount ?? 0);
    $reimbursementAmount = floatval($detail->reimbursement_amount ?? 0);
    $reimbursementSstAmount = floatval($detail->reimbursement_sst_amount ?? 0);
    
    $rowTotal = $transferAmount + $sstAmount + $reimbursementAmount + $reimbursementSstAmount;
    
    // Method 1: Round each component then sum
    $roundedEach = round($transferAmount, 2) + round($sstAmount, 2) + round($reimbursementAmount, 2) + round($reimbursementSstAmount, 2);
    
    // Method 2: Sum then round
    $sumThenRoundVal = round($rowTotal, 2);
    
    $sumOfRoundedComponents += $roundedEach;
    $sumThenRound += $sumThenRoundVal;
    
    $diff = $roundedEach - $sumThenRoundVal;
    
    if (abs($diff) > 0.0001) {
        $roundingDifferences[] = [
            'row' => $rowNumber,
            'id' => $detail->id,
            'invoice' => $detail->invoice_no ?? 'N/A',
            'ref' => $detail->case_ref_no ?? 'N/A',
            'rounded_each' => $roundedEach,
            'sum_then_round' => $sumThenRoundVal,
            'diff' => $diff,
            'transfer' => $transferAmount,
            'sst' => $sstAmount,
            'reimb' => $reimbursementAmount,
            'reimb_sst' => $reimbursementSstAmount
        ];
    }
    
    $rowNumber++;
}

echo "Total using 'round each then sum': " . number_format($sumOfRoundedComponents, 2) . "\n";
echo "Total using 'sum then round': " . number_format($sumThenRound, 2) . "\n";
echo "Difference: " . number_format($sumOfRoundedComponents - $sumThenRound, 10) . "\n\n";

if (count($roundingDifferences) > 0) {
    echo "Entries with rounding differences:\n\n";
    $totalDiff = 0;
    foreach ($roundingDifferences as $entry) {
        $totalDiff += $entry['diff'];
        printf("Row %3d | ID: %5d | Invoice: %-20s | Ref: %-30s\n",
            $entry['row'],
            $entry['id'],
            $entry['invoice'],
            $entry['ref']
        );
        printf("         Round each then sum: %12.2f | Sum then round: %12.2f | Diff: %12.10f\n",
            $entry['rounded_each'],
            $entry['sum_then_round'],
            $entry['diff']
        );
        printf("         Values: T=%s, S=%s, R=%s, RS=%s\n\n",
            $entry['transfer'],
            $entry['sst'],
            $entry['reimb'],
            $entry['reimb_sst']
        );
    }
    echo "Total difference from rounding method: " . number_format($totalDiff, 10) . "\n";
}

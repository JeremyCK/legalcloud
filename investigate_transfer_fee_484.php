<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;

$transferFeeId = 484;

echo "=== Investigating Transfer Fee ID: {$transferFeeId} ===\n\n";

// Get the transfer fee main record
$transferFeeMain = TransferFeeMain::find($transferFeeId);

if (!$transferFeeMain) {
    echo "Transfer fee record not found!\n";
    exit(1);
}

echo "Transfer Fee Main Record:\n";
echo "  ID: {$transferFeeMain->id}\n";
echo "  Transfer Amount (from main): " . number_format($transferFeeMain->transfer_amount ?? 0, 2) . "\n";
echo "  Expected Total: 616,549.16\n";
echo "  Current Total: " . number_format($transferFeeMain->transfer_amount ?? 0, 2) . "\n";
echo "  Difference: " . number_format(616549.16 - ($transferFeeMain->transfer_amount ?? 0), 2) . "\n\n";

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
        'l.case_ref_no'
    )
    ->orderBy('transfer_fee_details.id')
    ->get();

echo "=== Transfer Fee Details Breakdown ===\n\n";

$totalCalculated = 0;
$rowNumber = 1;
$entriesWithDecimals = [];

foreach ($transferFeeDetails as $detail) {
    $transferAmount = floatval($detail->transfer_amount ?? 0);
    $sstAmount = floatval($detail->sst_amount ?? 0);
    $reimbursementAmount = floatval($detail->reimbursement_amount ?? 0);
    $reimbursementSstAmount = floatval($detail->reimbursement_sst_amount ?? 0);
    
    $rowTotal = $transferAmount + $sstAmount + $reimbursementAmount + $reimbursementSstAmount;
    $totalCalculated += $rowTotal;
    
    // Check for decimal precision issues (more than 2 decimal places in raw values)
    $transferAmountRaw = $detail->transfer_amount ?? 0;
    $sstAmountRaw = $detail->sst_amount ?? 0;
    $reimbursementAmountRaw = $detail->reimbursement_amount ?? 0;
    $reimbursementSstAmountRaw = $detail->reimbursement_sst_amount ?? 0;
    
    // Check if any value has more than 2 decimal places when stored
    $hasDecimalIssue = false;
    $decimalInfo = [];
    
    if (is_numeric($transferAmountRaw)) {
        $decimals = strlen(substr(strrchr((string)$transferAmountRaw, "."), 1));
        if ($decimals > 2) {
            $hasDecimalIssue = true;
            $decimalInfo[] = "transfer_amount has {$decimals} decimals: {$transferAmountRaw}";
        }
    }
    
    if (is_numeric($sstAmountRaw)) {
        $decimals = strlen(substr(strrchr((string)$sstAmountRaw, "."), 1));
        if ($decimals > 2) {
            $hasDecimalIssue = true;
            $decimalInfo[] = "sst_amount has {$decimals} decimals: {$sstAmountRaw}";
        }
    }
    
    if (is_numeric($reimbursementAmountRaw)) {
        $decimals = strlen(substr(strrchr((string)$reimbursementAmountRaw, "."), 1));
        if ($decimals > 2) {
            $hasDecimalIssue = true;
            $decimalInfo[] = "reimbursement_amount has {$decimals} decimals: {$reimbursementAmountRaw}";
        }
    }
    
    if (is_numeric($reimbursementSstAmountRaw)) {
        $decimals = strlen(substr(strrchr((string)$reimbursementSstAmountRaw, "."), 1));
        if ($decimals > 2) {
            $hasDecimalIssue = true;
            $decimalInfo[] = "reimbursement_sst_amount has {$decimals} decimals: {$reimbursementSstAmountRaw}";
        }
    }
    
    // Check for rounding issues - if sum doesn't match when rounded
    $roundedRowTotal = round($rowTotal, 2);
    $roundedTransfer = round($transferAmount, 2);
    $roundedSst = round($sstAmount, 2);
    $roundedReimb = round($reimbursementAmount, 2);
    $roundedReimbSst = round($reimbursementSstAmount, 2);
    $sumOfRounded = $roundedTransfer + $roundedSst + $roundedReimb + $roundedReimbSst;
    
    if (abs($rowTotal - $sumOfRounded) > 0.0001) {
        $hasDecimalIssue = true;
        $decimalInfo[] = "Rounding issue: sum of rounded values ({$sumOfRounded}) != row total ({$rowTotal})";
    }
    
    printf("Row %3d | ID: %5d | Invoice: %-20s | Ref: %-30s\n", 
        $rowNumber, 
        $detail->id, 
        $detail->invoice_no ?? 'N/A',
        $detail->case_ref_no ?? 'N/A'
    );
    printf("         Transfer: %12.2f | SST: %12.2f | Reimb: %12.2f | Reimb SST: %12.2f | Row Total: %12.2f\n",
        $transferAmount,
        $sstAmount,
        $reimbursementAmount,
        $reimbursementSstAmount,
        $rowTotal
    );
    
    // Show raw values for precision checking
    printf("         Raw Values: Transfer=%s, SST=%s, Reimb=%s, ReimbSST=%s\n",
        $transferAmountRaw,
        $sstAmountRaw,
        $reimbursementAmountRaw,
        $reimbursementSstAmountRaw
    );
    
    if ($hasDecimalIssue) {
        echo "         ⚠️  DECIMAL ISSUE DETECTED:\n";
        foreach ($decimalInfo as $info) {
            echo "         - {$info}\n";
        }
        $entriesWithDecimals[] = [
            'row' => $rowNumber,
            'id' => $detail->id,
            'invoice' => $detail->invoice_no ?? 'N/A',
            'ref' => $detail->case_ref_no ?? 'N/A',
            'issues' => $decimalInfo,
            'transfer_amount' => $transferAmountRaw,
            'sst_amount' => $sstAmountRaw,
            'reimbursement_amount' => $reimbursementAmountRaw,
            'reimbursement_sst_amount' => $reimbursementSstAmountRaw,
            'row_total' => $rowTotal
        ];
    }
    
    echo "\n";
    $rowNumber++;
}

echo "=== Summary ===\n\n";
echo "Total Calculated (sum of all rows): " . number_format($totalCalculated, 2) . "\n";
echo "Expected Total: 616,549.16\n";
echo "Current Total (from DB): " . number_format($transferFeeMain->transfer_amount ?? 0, 2) . "\n";
echo "Difference (Expected - Calculated): " . number_format(616549.16 - $totalCalculated, 2) . "\n";
echo "Difference (Expected - DB): " . number_format(616549.16 - ($transferFeeMain->transfer_amount ?? 0), 2) . "\n\n";

if (count($entriesWithDecimals) > 0) {
    echo "=== Entries with Decimal Issues ===\n\n";
    foreach ($entriesWithDecimals as $entry) {
        echo "Row {$entry['row']} (ID: {$entry['id']}, Invoice: {$entry['invoice']}, Ref: {$entry['ref']}):\n";
        foreach ($entry['issues'] as $issue) {
            echo "  - {$issue}\n";
        }
        echo "  Row Total: " . number_format($entry['row_total'], 2) . "\n\n";
    }
} else {
    echo "No obvious decimal precision issues found in individual fields.\n";
    echo "The issue might be in the summation or rounding logic.\n\n";
}

// Check if there's a rounding issue in the total calculation
echo "=== Rounding Analysis ===\n\n";
$totalWithPrecision = 0;
$totalRounded = 0;

foreach ($transferFeeDetails as $detail) {
    $transferAmount = floatval($detail->transfer_amount ?? 0);
    $sstAmount = floatval($detail->sst_amount ?? 0);
    $reimbursementAmount = floatval($detail->reimbursement_amount ?? 0);
    $reimbursementSstAmount = floatval($detail->reimbursement_sst_amount ?? 0);
    
    $totalWithPrecision += $transferAmount + $sstAmount + $reimbursementAmount + $reimbursementSstAmount;
    
    $totalRounded += round($transferAmount, 2) + round($sstAmount, 2) + round($reimbursementAmount, 2) + round($reimbursementSstAmount, 2);
}

echo "Total with full precision: " . number_format($totalWithPrecision, 10) . "\n";
echo "Total with rounded values: " . number_format($totalRounded, 2) . "\n";
echo "Difference: " . number_format($totalWithPrecision - $totalRounded, 10) . "\n";
echo "Expected: 616,549.16\n";
echo "Difference from expected (rounded): " . number_format(616549.16 - $totalRounded, 2) . "\n";

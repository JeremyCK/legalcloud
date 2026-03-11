<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;

$transferFeeId = 484;

echo "=== Finding Exact 0.09 Difference for Transfer Fee ID: {$transferFeeId} ===\n\n";

// Get all transfer fee details with raw database values
$transferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeId)
    ->leftJoin('loan_case_invoice_main as im', 'im.id', '=', 'transfer_fee_details.loan_case_invoice_main_id')
    ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'transfer_fee_details.loan_case_main_bill_id')
    ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
    ->select(
        'transfer_fee_details.id',
        DB::raw('transfer_fee_details.transfer_amount as raw_transfer_amount'),
        DB::raw('transfer_fee_details.sst_amount as raw_sst_amount'),
        DB::raw('transfer_fee_details.reimbursement_amount as raw_reimbursement_amount'),
        DB::raw('transfer_fee_details.reimbursement_sst_amount as raw_reimbursement_sst_amount'),
        'im.invoice_no',
        'l.case_ref_no'
    )
    ->orderBy('transfer_fee_details.id')
    ->get();

$totalFromDB = 0;
$rowNumber = 1;
$allEntries = [];

foreach ($transferFeeDetails as $detail) {
    // Get raw values directly from database
    $rawTransfer = $detail->raw_transfer_amount ?? 0;
    $rawSst = $detail->raw_sst_amount ?? 0;
    $rawReimb = $detail->raw_reimbursement_amount ?? 0;
    $rawReimbSst = $detail->raw_reimbursement_sst_amount ?? 0;
    
    // Convert to float for calculation
    $transferAmount = floatval($rawTransfer);
    $sstAmount = floatval($rawSst);
    $reimbursementAmount = floatval($rawReimb);
    $reimbursementSstAmount = floatval($rawReimbSst);
    
    $rowTotal = $transferAmount + $sstAmount + $reimbursementAmount + $reimbursementSstAmount;
    $totalFromDB += $rowTotal;
    
    $allEntries[] = [
        'row' => $rowNumber,
        'id' => $detail->id,
        'invoice' => $detail->invoice_no ?? 'N/A',
        'ref' => $detail->case_ref_no ?? 'N/A',
        'transfer_amount' => $transferAmount,
        'sst_amount' => $sstAmount,
        'reimbursement_amount' => $reimbursementAmount,
        'reimbursement_sst_amount' => $reimbursementSstAmount,
        'row_total' => $rowTotal,
        'raw_transfer' => $rawTransfer,
        'raw_sst' => $rawSst,
        'raw_reimb' => $rawReimb,
        'raw_reimb_sst' => $rawReimbSst
    ];
    
    $rowNumber++;
}

$expectedTotal = 616549.16;
$actualTotal = $totalFromDB;
$difference = $expectedTotal - $actualTotal;

echo "Expected Total: " . number_format($expectedTotal, 2) . "\n";
echo "Actual Total: " . number_format($actualTotal, 2) . "\n";
echo "Difference: " . number_format($difference, 2) . "\n\n";

// The difference is -0.09, meaning actual is 0.09 more than expected
// So we need to find entries that should be reduced by 0.09 total

echo "=== Analyzing to find which entries need adjustment ===\n\n";
echo "Since actual is 0.09 MORE than expected, we need to find where 0.09 extra was added.\n\n";

// Check each entry's contribution - look for entries that might have been rounded up incorrectly
$cumulativeSum = 0;
$potentialIssues = [];

foreach ($allEntries as $entry) {
    $cumulativeSum += $entry['row_total'];
    
    // Check if this entry has values that when rounded might contribute to the difference
    // Look for entries with values ending in .xx5 or similar that might round incorrectly
    $transferStr = number_format($entry['transfer_amount'], 2);
    $sstStr = number_format($entry['sst_amount'], 2);
    $reimbStr = number_format($entry['reimbursement_amount'], 2);
    $reimbSstStr = number_format($entry['reimbursement_sst_amount'], 2);
    
    // Check if any value ends in .x5 (which rounds up)
    $hasRoundingIssue = false;
    $roundingDetails = [];
    
    if (substr($transferStr, -1) == '5' && substr($transferStr, -3, 1) == '.') {
        $hasRoundingIssue = true;
        $roundingDetails[] = "Transfer amount ends in .x5: {$transferStr}";
    }
    if (substr($sstStr, -1) == '5' && substr($sstStr, -3, 1) == '.') {
        $hasRoundingIssue = true;
        $roundingDetails[] = "SST amount ends in .x5: {$sstStr}";
    }
    if (substr($reimbStr, -1) == '5' && substr($reimbStr, -3, 1) == '.') {
        $hasRoundingIssue = true;
        $roundingDetails[] = "Reimbursement amount ends in .x5: {$reimbStr}";
    }
    if (substr($reimbSstStr, -1) == '5' && substr($reimbSstStr, -3, 1) == '.') {
        $hasRoundingIssue = true;
        $roundingDetails[] = "Reimbursement SST ends in .x5: {$reimbSstStr}";
    }
    
    // Also check for entries with very specific decimal values that might indicate rounding errors
    $rowTotalRounded = round($entry['row_total'], 2);
    $sumOfRounded = round($entry['transfer_amount'], 2) + round($entry['sst_amount'], 2) + 
                     round($entry['reimbursement_amount'], 2) + round($entry['reimbursement_sst_amount'], 2);
    
    if (abs($rowTotalRounded - $sumOfRounded) > 0.0001) {
        $hasRoundingIssue = true;
        $roundingDetails[] = "Rounding mismatch: row total rounded ({$rowTotalRounded}) != sum of rounded ({$sumOfRounded})";
    }
    
    if ($hasRoundingIssue) {
        $potentialIssues[] = [
            'entry' => $entry,
            'details' => $roundingDetails
        ];
    }
}

if (count($potentialIssues) > 0) {
    echo "Entries with potential rounding issues:\n\n";
    foreach ($potentialIssues as $issue) {
        $e = $issue['entry'];
        echo "Row {$e['row']} (ID: {$e['id']}, Invoice: {$e['invoice']}, Ref: {$e['ref']}):\n";
        echo "  Transfer: {$e['transfer_amount']}, SST: {$e['sst_amount']}, Reimb: {$e['reimbursement_amount']}, ReimbSST: {$e['reimbursement_sst_amount']}\n";
        echo "  Row Total: {$e['row_total']}\n";
        foreach ($issue['details'] as $detail) {
            echo "  ⚠️  {$detail}\n";
        }
        echo "\n";
    }
}

// Now let's try a brute force approach - find which entries if adjusted by 0.01 would get us closer
echo "=== Trying to find entries that sum to 0.09 difference ===\n\n";

// Since we need to reduce by 0.09, we're looking for entries that might have been rounded up
// Let's check if there are entries that when their cents are adjusted, we get the right total

$adjustments = [];
$currentDiff = $actualTotal - $expectedTotal; // Should be 0.09

echo "Current difference to fix: " . number_format($currentDiff, 10) . "\n\n";

// Check entries with values that might have rounding issues
// Look for entries where the sum of rounded components doesn't match the stored total
foreach ($allEntries as $entry) {
    $roundedTransfer = round($entry['transfer_amount'], 2);
    $roundedSst = round($entry['sst_amount'], 2);
    $roundedReimb = round($entry['reimbursement_amount'], 2);
    $roundedReimbSst = round($entry['reimbursement_sst_amount'], 2);
    
    $sumOfRounded = $roundedTransfer + $roundedSst + $roundedReimb + $roundedReimbSst;
    $storedTotal = round($entry['row_total'], 2);
    
    // Check if stored values have precision beyond 2 decimals
    $transferPrecision = strlen(substr(strrchr((string)$entry['transfer_amount'], "."), 1));
    $sstPrecision = strlen(substr(strrchr((string)$entry['sst_amount'], "."), 1));
    $reimbPrecision = strlen(substr(strrchr((string)$entry['reimbursement_amount'], "."), 1));
    $reimbSstPrecision = strlen(substr(strrchr((string)$entry['reimbursement_sst_amount'], "."), 1));
    
    if ($transferPrecision > 2 || $sstPrecision > 2 || $reimbPrecision > 2 || $reimbSstPrecision > 2) {
        echo "Row {$entry['row']} (ID: {$entry['id']}, Invoice: {$entry['invoice']}): Has precision > 2 decimals\n";
        echo "  Transfer: {$entry['transfer_amount']} ({$transferPrecision} decimals)\n";
        echo "  SST: {$entry['sst_amount']} ({$sstPrecision} decimals)\n";
        echo "  Reimb: {$entry['reimbursement_amount']} ({$reimbPrecision} decimals)\n";
        echo "  ReimbSST: {$entry['reimbursement_sst_amount']} ({$reimbSstPrecision} decimals)\n\n";
    }
}

// Final approach: Check the database column types and see if there's a precision issue
echo "=== Checking Database Column Precision ===\n\n";
$columnInfo = DB::select("
    SELECT 
        COLUMN_NAME,
        DATA_TYPE,
        NUMERIC_PRECISION,
        NUMERIC_SCALE
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'transfer_fee_details'
    AND COLUMN_NAME IN ('transfer_amount', 'sst_amount', 'reimbursement_amount', 'reimbursement_sst_amount')
");

echo "Column precision information:\n";
foreach ($columnInfo as $col) {
    echo "  {$col->COLUMN_NAME}: {$col->DATA_TYPE} ({$col->NUMERIC_PRECISION}, {$col->NUMERIC_SCALE})\n";
}

<?php

/**
 * Investigation Script for Transfer Fee Discrepancy
 * 
 * This script investigates why transfer fee record 447 shows 460,979.79
 * but bank reconciliation shows 461,746.59 for transaction ID DP001-0825
 * 
 * Run this script via: php artisan tinker < investigate_transfer_fee_discrepancy.php
 * Or access via web route if configured
 */

use Illuminate\Support\Facades\DB;

$transferFeeId = 447;
$transactionId = 'DP001-0825';

echo "========================================\n";
echo "TRANSFER FEE DISCREPANCY INVESTIGATION\n";
echo "========================================\n\n";

// 1. Get Transfer Fee Main Record
echo "1. TRANSFER FEE MAIN RECORD (ID: {$transferFeeId})\n";
echo "---------------------------------------------------\n";
$transferFeeMain = DB::table('transfer_fee_main')
    ->where('id', $transferFeeId)
    ->first();

if (!$transferFeeMain) {
    echo "ERROR: Transfer fee record not found!\n";
    exit;
}

echo "Transaction ID: {$transferFeeMain->transaction_id}\n";
echo "Transfer Date: {$transferFeeMain->transfer_date}\n";
echo "Transfer Amount (from transfer_fee_main): " . number_format($transferFeeMain->transfer_amount, 2) . "\n";
echo "Transfer From Bank ID: {$transferFeeMain->transfer_from}\n";
echo "Transfer To Bank ID: {$transferFeeMain->transfer_to}\n\n";

// 2. Calculate Total from Transfer Fee Details
echo "2. TRANSFER FEE DETAILS BREAKDOWN\n";
echo "----------------------------------\n";
$transferFeeDetails = DB::table('transfer_fee_details')
    ->where('transfer_fee_main_id', $transferFeeId)
    ->where('status', '<>', 99)
    ->get();

$calculatedTotal = 0;
$pfeeTotal = 0;
$sstTotal = 0;
$reimbTotal = 0;
$reimbSstTotal = 0;

foreach ($transferFeeDetails as $detail) {
    $pfeeTotal += $detail->transfer_amount ?? 0;
    $sstTotal += $detail->sst_amount ?? 0;
    $reimbTotal += $detail->reimbursement_amount ?? 0;
    $reimbSstTotal += $detail->reimbursement_sst_amount ?? 0;
}

$calculatedTotal = $pfeeTotal + $sstTotal + $reimbTotal + $reimbSstTotal;

echo "Total from transfer_fee_details:\n";
echo "  Professional Fee: " . number_format($pfeeTotal, 2) . "\n";
echo "  SST: " . number_format($sstTotal, 2) . "\n";
echo "  Reimbursement: " . number_format($reimbTotal, 2) . "\n";
echo "  Reimbursement SST: " . number_format($reimbSstTotal, 2) . "\n";
echo "  TOTAL: " . number_format($calculatedTotal, 2) . "\n";
echo "  Record Count: " . $transferFeeDetails->count() . "\n\n";

// 3. Check Ledger Entries for EXACT Transaction ID Match
echo "3. LEDGER ENTRIES - EXACT TRANSACTION ID MATCH\n";
echo "-------------------------------------------------\n";
$ledgerEntriesExact = DB::table('ledger_entries_v2')
    ->where('transaction_id', '=', $transactionId)
    ->where('status', '<>', 99)
    ->whereIn('type', ['TRANSFER_IN', 'SST_IN', 'REIMB_IN', 'REIMB_SST_IN'])
    ->get();

$ledgerTotalExact = 0;
$ledgerBreakdownExact = [
    'TRANSFER_IN' => 0,
    'SST_IN' => 0,
    'REIMB_IN' => 0,
    'REIMB_SST_IN' => 0
];

foreach ($ledgerEntriesExact as $entry) {
    $ledgerTotalExact += $entry->amount;
    if (isset($ledgerBreakdownExact[$entry->type])) {
        $ledgerBreakdownExact[$entry->type] += $entry->amount;
    }
}

echo "Ledger entries with EXACT transaction_id = '{$transactionId}':\n";
echo "  TRANSFER_IN: " . number_format($ledgerBreakdownExact['TRANSFER_IN'], 2) . " (Count: " . $ledgerEntriesExact->where('type', 'TRANSFER_IN')->count() . ")\n";
echo "  SST_IN: " . number_format($ledgerBreakdownExact['SST_IN'], 2) . " (Count: " . $ledgerEntriesExact->where('type', 'SST_IN')->count() . ")\n";
echo "  REIMB_IN: " . number_format($ledgerBreakdownExact['REIMB_IN'], 2) . " (Count: " . $ledgerEntriesExact->where('type', 'REIMB_IN')->count() . ")\n";
echo "  REIMB_SST_IN: " . number_format($ledgerBreakdownExact['REIMB_SST_IN'], 2) . " (Count: " . $ledgerEntriesExact->where('type', 'REIMB_SST_IN')->count() . ")\n";
echo "  TOTAL: " . number_format($ledgerTotalExact, 2) . "\n";
echo "  Entry Count: " . $ledgerEntriesExact->count() . "\n\n";

// 4. Check Ledger Entries for LIKE Pattern (what bank recon uses)
echo "4. LEDGER ENTRIES - LIKE PATTERN MATCH (Bank Recon Method)\n";
echo "------------------------------------------------------------\n";
$ledgerEntriesLike = DB::table('ledger_entries_v2')
    ->where('transaction_id', 'like', '%' . $transactionId . '%')
    ->where('status', '<>', 99)
    ->whereIn('type', ['TRANSFER_IN', 'SST_IN', 'REIMB_IN', 'REIMB_SST_IN'])
    ->get();

$ledgerTotalLike = 0;
$ledgerBreakdownLike = [
    'TRANSFER_IN' => 0,
    'SST_IN' => 0,
    'REIMB_IN' => 0,
    'REIMB_SST_IN' => 0
];

$uniqueTransactionIds = [];

foreach ($ledgerEntriesLike as $entry) {
    $ledgerTotalLike += $entry->amount;
    if (isset($ledgerBreakdownLike[$entry->type])) {
        $ledgerBreakdownLike[$entry->type] += $entry->amount;
    }
    if (!in_array($entry->transaction_id, $uniqueTransactionIds)) {
        $uniqueTransactionIds[] = $entry->transaction_id;
    }
}

echo "Ledger entries with LIKE '%{$transactionId}%' pattern:\n";
echo "  TRANSFER_IN: " . number_format($ledgerBreakdownLike['TRANSFER_IN'], 2) . " (Count: " . $ledgerEntriesLike->where('type', 'TRANSFER_IN')->count() . ")\n";
echo "  SST_IN: " . number_format($ledgerBreakdownLike['SST_IN'], 2) . " (Count: " . $ledgerEntriesLike->where('type', 'SST_IN')->count() . ")\n";
echo "  REIMB_IN: " . number_format($ledgerBreakdownLike['REIMB_IN'], 2) . " (Count: " . $ledgerEntriesLike->where('type', 'REIMB_IN')->count() . ")\n";
echo "  REIMB_SST_IN: " . number_format($ledgerBreakdownLike['REIMB_SST_IN'], 2) . " (Count: " . $ledgerEntriesLike->where('type', 'REIMB_SST_IN')->count() . ")\n";
echo "  TOTAL: " . number_format($ledgerTotalLike, 2) . "\n";
echo "  Entry Count: " . $ledgerEntriesLike->count() . "\n";
echo "  Unique Transaction IDs Found: " . implode(', ', $uniqueTransactionIds) . "\n\n";

// 5. Find Extra Entries (entries in LIKE but not in EXACT)
echo "5. EXTRA ENTRIES (In LIKE but not in EXACT match)\n";
echo "---------------------------------------------------\n";
$extraEntries = $ledgerEntriesLike->filter(function($entry) use ($transactionId) {
    return $entry->transaction_id !== $transactionId;
});

if ($extraEntries->count() > 0) {
    echo "Found " . $extraEntries->count() . " entries with different transaction IDs:\n\n";
    
    $groupedByTrxId = $extraEntries->groupBy('transaction_id');
    foreach ($groupedByTrxId as $trxId => $entries) {
        $trxTotal = $entries->sum('amount');
        echo "  Transaction ID: {$trxId}\n";
        echo "    Amount: " . number_format($trxTotal, 2) . "\n";
        echo "    Count: " . $entries->count() . "\n";
        
        // Check if this transaction ID belongs to another transfer fee
        $otherTransferFee = DB::table('transfer_fee_main')
            ->where('transaction_id', '=', $trxId)
            ->where('status', '<>', 99)
            ->first();
        
        if ($otherTransferFee) {
            echo "    Belongs to Transfer Fee ID: {$otherTransferFee->id}\n";
            echo "    Transfer Date: {$otherTransferFee->transfer_date}\n";
        }
        echo "\n";
    }
} else {
    echo "No extra entries found.\n\n";
}

// 6. Check for entries linked to this transfer fee details
echo "6. LEDGER ENTRIES LINKED TO TRANSFER FEE DETAILS\n";
echo "-------------------------------------------------\n";
$detailIds = $transferFeeDetails->pluck('id')->toArray();
$ledgerEntriesLinked = DB::table('ledger_entries_v2')
    ->whereIn('key_id_2', $detailIds)
    ->where('status', '<>', 99)
    ->whereIn('type', ['TRANSFER_IN', 'SST_IN', 'REIMB_IN', 'REIMB_SST_IN'])
    ->get();

$ledgerTotalLinked = 0;
foreach ($ledgerEntriesLinked as $entry) {
    $ledgerTotalLinked += $entry->amount;
}

echo "Ledger entries linked to transfer_fee_details (via key_id_2):\n";
echo "  TOTAL: " . number_format($ledgerTotalLinked, 2) . "\n";
echo "  Entry Count: " . $ledgerEntriesLinked->count() . "\n\n";

// 7. Summary and Discrepancy Analysis
echo "7. DISCREPANCY ANALYSIS\n";
echo "-----------------------\n";
echo "Transfer Fee Main Amount: " . number_format($transferFeeMain->transfer_amount, 2) . "\n";
echo "Calculated from Details: " . number_format($calculatedTotal, 2) . "\n";
echo "Ledger (Exact Match): " . number_format($ledgerTotalExact, 2) . "\n";
echo "Ledger (Like Pattern): " . number_format($ledgerTotalLike, 2) . "\n";
echo "Ledger (Linked to Details): " . number_format($ledgerTotalLinked, 2) . "\n\n";

$differenceExact = $ledgerTotalExact - $transferFeeMain->transfer_amount;
$differenceLike = $ledgerTotalLike - $transferFeeMain->transfer_amount;
$differenceLinked = $ledgerTotalLinked - $transferFeeMain->transfer_amount;

echo "Differences:\n";
echo "  Exact Match vs Transfer Fee: " . number_format($differenceExact, 2) . "\n";
echo "  Like Pattern vs Transfer Fee: " . number_format($differenceLike, 2) . "\n";
echo "  Linked vs Transfer Fee: " . number_format($differenceLinked, 2) . "\n\n";

// 8. Recommendations
echo "8. RECOMMENDATIONS\n";
echo "------------------\n";
if (abs($differenceLike - 766.80) < 0.01) {
    echo "✓ Found the discrepancy! Bank reconciliation uses LIKE pattern which includes extra entries.\n";
    echo "  Difference matches reported: " . number_format($differenceLike, 2) . "\n\n";
}

if ($extraEntries->count() > 0) {
    echo "⚠ ISSUE: Bank reconciliation LIKE pattern matches multiple transaction IDs.\n";
    echo "  Solution: Use exact transaction ID match instead of LIKE pattern.\n\n";
}

if (abs($differenceLinked) > 0.01) {
    echo "⚠ ISSUE: Ledger entries linked to transfer_fee_details don't match.\n";
    echo "  This suggests missing or extra ledger entries.\n\n";
}

if (abs($transferFeeMain->transfer_amount - $calculatedTotal) > 0.01) {
    echo "⚠ ISSUE: Transfer fee main amount doesn't match calculated total from details.\n";
    echo "  Consider running recalculateTransferFeeTotal() function.\n\n";
}

echo "========================================\n";
echo "INVESTIGATION COMPLETE\n";
echo "========================================\n";

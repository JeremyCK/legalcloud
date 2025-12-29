<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$journalEntryId = 467;
// Check command line argument for --execute flag
$dryRun = !in_array('--execute', $argv ?? []);

echo "Journal Entry Duplicate Removal Script\n";
echo str_repeat("=", 80) . "\n";
echo "Journal Entry ID: {$journalEntryId}\n";
echo "Mode: " . ($dryRun ? "DRY RUN (no changes will be made)" : "LIVE (will delete duplicates)") . "\n";
echo str_repeat("=", 80) . "\n\n";

// Get the main journal entry
$journalEntryMain = DB::table('journal_entry_main')
    ->where('id', $journalEntryId)
    ->first();

if (!$journalEntryMain) {
    echo "ERROR: Journal Entry #{$journalEntryId} not found!\n";
    exit(1);
}

echo "Journal Entry: {$journalEntryMain->journal_no} - {$journalEntryMain->name}\n";
echo "Current Totals - Debit: " . number_format($journalEntryMain->total_debit ?? 0, 2) . 
     ", Credit: " . number_format($journalEntryMain->total_credit ?? 0, 2) . "\n\n";

// Get all details
$journalEntryDetails = DB::table('journal_entry_details')
    ->leftJoin('account_code', 'account_code.id', '=', 'journal_entry_details.account_code_id')
    ->select(
        'journal_entry_details.*',
        'account_code.code',
        'account_code.name as account_name'
    )
    ->where('journal_entry_main_id', $journalEntryId)
    ->orderBy('journal_entry_details.id')
    ->get();

if ($journalEntryDetails->isEmpty()) {
    echo "ERROR: No journal entry details found!\n";
    exit(1);
}

echo "Total entries found: " . count($journalEntryDetails) . "\n\n";

// Group entries by: amount + type + account + remarks + case_id
$entryGroups = [];
foreach ($journalEntryDetails as $detail) {
    $amount = floatval($detail->amount ?? 0);
    $sstAmount = floatval($detail->sst_amount ?? 0);
    $total = $amount + $sstAmount;
    $type = $detail->transaction_type;
    $remarks = trim($detail->remarks ?? '');
    $accountCodeId = $detail->account_code_id;
    $caseId = $detail->case_id;
    
    // Create a unique key for grouping identical entries
    $key = number_format($total, 2) . '_' . $type . '_' . $accountCodeId . '_' . md5($remarks) . '_' . ($caseId ?? 'null');
    
    if (!isset($entryGroups[$key])) {
        $entryGroups[$key] = [];
    }
    
    $entryGroups[$key][] = [
        'id' => $detail->id,
        'amount' => $amount,
        'sst' => $sstAmount,
        'total' => $total,
        'type' => $type,
        'account_code_id' => $accountCodeId,
        'account_name' => $detail->account_name ?? 'N/A',
        'code' => $detail->code ?? 'N/A',
        'remarks' => $remarks,
        'case_id' => $caseId
    ];
}

// Identify duplicates
$duplicatesToRemove = [];
$entriesToKeep = [];

foreach ($entryGroups as $key => $entries) {
    if (count($entries) > 2) {
        // More than 2 identical entries - keep only 2 (1 debit, 1 credit if applicable)
        usort($entries, function($a, $b) {
            return $a['id'] <=> $b['id'];
        });
        
        // Separate by type
        $debitEntries = array_filter($entries, function($e) { return $e['type'] == 'D'; });
        $creditEntries = array_filter($entries, function($e) { return $e['type'] == 'C'; });
        
        // Keep first debit and first credit
        $keptIds = [];
        if (count($debitEntries) > 0) {
            $firstDebit = reset($debitEntries);
            $keptIds[] = $firstDebit['id'];
            $entriesToKeep[] = $firstDebit;
        }
        if (count($creditEntries) > 0) {
            $firstCredit = reset($creditEntries);
            $keptIds[] = $firstCredit['id'];
            $entriesToKeep[] = $firstCredit;
        }
        
        // All others are duplicates
        foreach ($entries as $entry) {
            if (!in_array($entry['id'], $keptIds)) {
                $duplicatesToRemove[] = $entry;
            }
        }
    } elseif (count($entries) == 2) {
        // Check if both are same type (shouldn't happen in proper double-entry)
        $types = array_unique(array_column($entries, 'type'));
        if (count($types) == 1) {
            // Both same type - keep first, remove second
            usort($entries, function($a, $b) {
                return $a['id'] <=> $b['id'];
            });
            $entriesToKeep[] = $entries[0];
            $duplicatesToRemove[] = $entries[1];
        } else {
            // Different types - keep both (proper pair)
            $entriesToKeep = array_merge($entriesToKeep, $entries);
        }
    } else {
        // Single entry - keep it
        $entriesToKeep = array_merge($entriesToKeep, $entries);
    }
}

echo "Analysis Results:\n";
echo str_repeat("-", 80) . "\n";
echo "Entries to KEEP: " . count($entriesToKeep) . "\n";
echo "Duplicate entries to REMOVE: " . count($duplicatesToRemove) . "\n\n";

// Continue even if no duplicates in journal_entry_details, as ledger_entries_v2 might have duplicates

echo "\n";

// Show summary of duplicates by amount
echo "Duplicates Summary (by amount):\n";
echo str_repeat("-", 80) . "\n";

$duplicatesByAmount = [];
foreach ($duplicatesToRemove as $entry) {
    $amountKey = number_format($entry['total'], 2);
    if (!isset($duplicatesByAmount[$amountKey])) {
        $duplicatesByAmount[$amountKey] = ['count' => 0, 'ids' => []];
    }
    $duplicatesByAmount[$amountKey]['count']++;
    $duplicatesByAmount[$amountKey]['ids'][] = $entry['id'];
}

krsort($duplicatesByAmount);
foreach ($duplicatesByAmount as $amount => $data) {
    echo "  " . $amount . ": " . $data['count'] . " duplicate(s)\n";
}

echo "\n";

// Get IDs to remove
$idsToRemove = array_column($duplicatesToRemove, 'id');
echo "Entry IDs to remove: " . count($idsToRemove) . "\n";
if (count($idsToRemove) > 0) {
    echo "First 10 IDs: " . implode(', ', array_slice($idsToRemove, 0, 10)) . "...\n";
    echo "Last 10 IDs: " . implode(', ', array_slice($idsToRemove, -10)) . "\n";
}
echo "\n";

// Calculate new totals (only if we're removing journal_entry_details duplicates)
if (count($idsToRemove) > 0) {
    $newTotalDebit = 0;
    $newTotalCredit = 0;
    foreach ($entriesToKeep as $entry) {
        if ($entry['type'] == 'D') {
            $newTotalDebit += $entry['total'];
        } else {
            $newTotalCredit += $entry['total'];
        }
    }

    echo "Totals After Cleanup:\n";
    echo "  Debit: " . number_format($newTotalDebit, 2) . "\n";
    echo "  Credit: " . number_format($newTotalCredit, 2) . "\n";
    echo "  Difference: " . number_format(abs($newTotalDebit - $newTotalCredit), 2) . "\n";

    if (abs($newTotalDebit - $newTotalCredit) < 0.01) {
        echo "  ✓ Will remain balanced\n";
    } else {
        echo "  ✗ WARNING: Will NOT be balanced after removal!\n";
        echo "  Aborting to prevent data corruption.\n";
        exit(1);
    }
} else {
    // No journal_entry_details to remove, keep existing totals
    $newTotalDebit = $journalEntryMain->total_debit ?? 0;
    $newTotalCredit = $journalEntryMain->total_credit ?? 0;
    echo "No journal_entry_details duplicates to remove. Totals will remain unchanged.\n";
}

// Check for related ledger entries (old system)
echo "Checking for related ledger entries (old system)...\n";
$journalNo = $journalEntryMain->journal_no;
$ledgerEntries = DB::table('ledger_entries')
    ->where('cheque_no', $journalNo)
    ->whereIn('key_id_2', $idsToRemove)
    ->get();

echo "Found " . count($ledgerEntries) . " related ledger entries (old) that will also be removed.\n";

// Check for related ledger entries v2 (new system)
echo "Checking for related ledger entries v2 (new system)...\n";
$allLedgerEntriesV2 = DB::table('ledger_entries_v2')
    ->where('key_id', $journalEntryId)
    ->whereIn('type', ['JOURNAL_IN', 'JOURNAL_OUT'])
    ->get();

echo "Total ledger_entries_v2 found: " . count($allLedgerEntriesV2) . "\n";

// Get valid journal_entry_details IDs (ones we're keeping)
$validDetailIds = array_column($entriesToKeep, 'id');

// Find ledger_entries_v2 that reference duplicate journal_entry_details (if any)
$ledgerEntriesV2ToRemove = DB::table('ledger_entries_v2')
    ->where('key_id', $journalEntryId)
    ->whereIn('key_id_2', count($idsToRemove) > 0 ? $idsToRemove : [-1]) // Use -1 to ensure no matches if empty
    ->whereIn('type', ['JOURNAL_IN', 'JOURNAL_OUT'])
    ->get();

// Find ledger_entries_v2 with invalid key_id_2 (references non-existent detail IDs)
// This handles the case where details were already deleted but ledger entries remain
$invalidLedgerV2 = DB::table('ledger_entries_v2')
    ->where('key_id', $journalEntryId)
    ->whereIn('type', ['JOURNAL_IN', 'JOURNAL_OUT'])
    ->whereNotIn('key_id_2', count($validDetailIds) > 0 ? $validDetailIds : [-1])
    ->get();

// Also check for duplicates in ledger_entries_v2 itself (multiple entries for same detail_id)
$ledgerV2ByDetailId = [];
foreach ($allLedgerEntriesV2 as $ledger) {
    $detailId = $ledger->key_id_2;
    if (!isset($ledgerV2ByDetailId[$detailId])) {
        $ledgerV2ByDetailId[$detailId] = [];
    }
    $ledgerV2ByDetailId[$detailId][] = $ledger;
}

// Find duplicates in ledger_entries_v2 (more than 1 entry per detail_id)
$ledgerV2Duplicates = [];
foreach ($ledgerV2ByDetailId as $detailId => $entries) {
    if (count($entries) > 1) {
        // Keep the first one (lowest ID), mark others as duplicates
        usort($entries, function($a, $b) {
            return $a->id <=> $b->id;
        });
        // Add all except the first to duplicates
        for ($i = 1; $i < count($entries); $i++) {
            $ledgerV2Duplicates[] = $entries[$i];
        }
    }
}

// Combine all ledger v2 entries to remove
$ledgerV2IdsFromDetails = $ledgerEntriesV2ToRemove->count() > 0 
    ? array_column($ledgerEntriesV2ToRemove->toArray(), 'id') 
    : [];
$ledgerV2InvalidIds = $invalidLedgerV2->count() > 0
    ? array_column($invalidLedgerV2->toArray(), 'id')
    : [];
$ledgerV2DuplicateIds = array_column($ledgerV2Duplicates, 'id');
$totalLedgerV2ToRemove = array_unique(array_merge($ledgerV2IdsFromDetails, $ledgerV2InvalidIds, $ledgerV2DuplicateIds));

echo "Ledger entries v2 to remove (from duplicate details): " . count($ledgerEntriesV2ToRemove) . "\n";
echo "Ledger entries v2 with invalid key_id_2: " . count($invalidLedgerV2) . "\n";
echo "Ledger entries v2 duplicates found: " . count($ledgerV2Duplicates) . "\n";
echo "Total ledger entries v2 to remove: " . count($totalLedgerV2ToRemove) . "\n\n";

// Check if there's anything to remove
$totalToRemove = count($idsToRemove) + count($totalLedgerV2ToRemove) + count($ledgerEntries);
if ($totalToRemove == 0) {
    echo "✓ No duplicates found. Journal entry is clean.\n";
    exit(0);
}

if ($dryRun) {
    echo str_repeat("=", 80) . "\n";
    echo "DRY RUN COMPLETE - No changes were made.\n";
    echo "To actually remove duplicates, run: php remove_journal_entry_duplicates.php --execute\n";
    echo str_repeat("=", 80) . "\n";
} else {
    echo str_repeat("=", 80) . "\n";
    echo "WARNING: You are about to DELETE duplicates!\n";
    echo "  - " . count($idsToRemove) . " duplicate journal entry details\n";
    echo "  - " . count($totalLedgerV2ToRemove) . " duplicate ledger entries v2\n";
    echo "  - " . count($ledgerEntries) . " duplicate ledger entries (old)\n";
    echo "This action cannot be undone.\n";
    echo str_repeat("=", 80) . "\n";
    echo "Press Enter to continue or Ctrl+C to cancel...\n";
    fgets(STDIN);
    
    echo "\nStarting deletion process...\n";
    echo str_repeat("-", 80) . "\n";
    
    DB::beginTransaction();
    
    try {
        // Delete related ledger entries v2 first (new system)
        if (count($totalLedgerV2ToRemove) > 0) {
            echo "Deleting " . count($totalLedgerV2ToRemove) . " duplicate/related ledger entries v2...\n";
            DB::table('ledger_entries_v2')
                ->whereIn('id', $totalLedgerV2ToRemove)
                ->delete();
        }
        
        // Delete related ledger entries (old system)
        if (count($ledgerEntries) > 0) {
            $ledgerIds = array_column($ledgerEntries->toArray(), 'id');
            echo "Deleting " . count($ledgerIds) . " related ledger entries (old)...\n";
            DB::table('ledger_entries')
                ->whereIn('id', $ledgerIds)
                ->delete();
        }
        
        // Delete duplicate journal entry details
        echo "Deleting " . count($idsToRemove) . " duplicate journal entry details...\n";
        DB::table('journal_entry_details')
            ->whereIn('id', $idsToRemove)
            ->delete();
        
        // Update journal entry main totals
        echo "Updating journal entry totals...\n";
        DB::table('journal_entry_main')
            ->where('id', $journalEntryId)
            ->update([
                'total_debit' => $newTotalDebit,
                'total_credit' => $newTotalCredit
            ]);
        
        DB::commit();
        
        echo "\n✓ SUCCESS: Duplicates removed successfully!\n";
        echo "  Removed " . count($idsToRemove) . " duplicate journal entry details\n";
        echo "  Removed " . count($totalLedgerV2ToRemove) . " duplicate/related ledger entries v2\n";
        echo "  Removed " . count($ledgerEntries) . " related ledger entries (old)\n";
        echo "  Updated totals: Debit = " . number_format($newTotalDebit, 2) . 
             ", Credit = " . number_format($newTotalCredit, 2) . "\n";
        
    } catch (\Exception $e) {
        DB::rollBack();
        echo "\n✗ ERROR: Failed to remove duplicates!\n";
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "\n";


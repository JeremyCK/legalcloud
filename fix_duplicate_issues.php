<?php
/**
 * FIXES FOR JOURNAL ENTRY DUPLICATE ISSUES
 * 
 * This file contains code fixes to prevent duplicate entries in journal entries
 */

// ============================================================================
// FIX 1: Add Double-Submission Protection in Frontend
// ============================================================================
// File: resources/views/dashboard/journal-entry/edit.blade.php
// Location: Around line 829-957

/*
BEFORE:
function SaveJournalEntry() {
    // ... validation code ...
    $.ajax({
        type: 'POST',
        url: '/updateJournalEntry/{{ $JournalEntryMain->id }}',
        // ... no protection against double submission
    });
}

AFTER:
function SaveJournalEntry() {
    // Prevent double submission
    if ($('#saveBtn').prop('disabled')) {
        return; // Already processing
    }
    
    // ... validation code ...
    
    // Disable button and show loading
    $('#saveBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    
    $.ajax({
        type: 'POST',
        url: '/updateJournalEntry/{{ $JournalEntryMain->id }}',
        data: form_data,
        processData: false,
        contentType: false,
        success: function(result) {
            if (result.status == 1) {
                Swal.fire('Success!', result.message, 'success');
                location.reload();
            } else {
                Swal.fire('notice!', result.message, 'warning');
                // Re-enable button on error
                $('#saveBtn').prop('disabled', false).html('Save');
            }
        },
        error: function() {
            Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
            // Re-enable button on error
            $('#saveBtn').prop('disabled', false).html('Save');
        }
    });
}
*/

// ============================================================================
// FIX 2: Add Database Transaction and Duplicate Detection
// ============================================================================
// File: app/Http/Controllers/AccountController.php
// Location: updateJournalEntry function (around line 4310)

/*
BEFORE:
public function updateJournalEntry(Request $request, $id)
{
    // ... get entries_list ...
    
    JournalEntryDetails::where('journal_entry_main_id', '=', $id)->delete();
    LedgerEntries::where('cheque_no', '=', $JournalEntryMain->journal_no)->delete();
    LedgerEntriesV2::where('key_id', $id)->whereIn('type', ['JOURNAL_IN', 'JOURNAL_OUT'])->delete();
    
    for ($i = 0; $i < count($entriesList); $i++) {
        // ... create entries without checking for duplicates ...
    }
}

AFTER:
public function updateJournalEntry(Request $request, $id)
{
    $current_user = auth()->user();
    $logNote = '';
    $total_debit = 0;
    $total_credit = 0;
    
    // Use cache lock to prevent concurrent updates
    $lockKey = "journal_entry_update_{$id}";
    $lock = Cache::lock($lockKey, 30); // 30 second lock
    
    if (!$lock->get()) {
        return response()->json(['status' => 2, 'message' => 'Another update is in progress. Please wait.']);
    }
    
    try {
        DB::beginTransaction();
        
        if ($request->input('entries_list') != null) {
            $entriesList = json_decode($request->input('entries_list'), true);
        }
        
        if (count($entriesList) <= 0) {
            DB::rollBack();
            $lock->release();
            return response()->json(['status' => 2, 'message' => 'No Entries']);
        }
        
        // FIX: Remove duplicates from entries_list before processing
        $uniqueEntries = [];
        $seenEntries = [];
        foreach ($entriesList as $entry) {
            $entryKey = md5(json_encode([
                'account_code_id' => $entry['account_code_id'] ?? '',
                'desc' => $entry['desc'] ?? '',
                'debit' => $entry['debit'] ?? 0,
                'credit' => $entry['credit'] ?? 0,
                'case_id' => $entry['case_id'] ?? '',
                'sst_amount' => $entry['sst_amount'] ?? 0,
            ]));
            
            if (!isset($seenEntries[$entryKey])) {
                $seenEntries[$entryKey] = true;
                $uniqueEntries[] = $entry;
            }
        }
        $entriesList = $uniqueEntries;
        
        $JournalEntryMain = JournalEntryMain::where('id', '=', $id)->first();
        
        if (!$JournalEntryMain) {
            DB::rollBack();
            $lock->release();
            return response()->json(['status' => 2, 'message' => 'Record not exists']);
        }
        
        // ... update JournalEntryMain ...
        
        // FIX: Verify deletions before proceeding
        $deletedDetails = JournalEntryDetails::where('journal_entry_main_id', '=', $id)->delete();
        $deletedLedger = LedgerEntries::where('cheque_no', '=', $JournalEntryMain->journal_no)->delete();
        $deletedLedgerV2 = LedgerEntriesV2::where('key_id', $id)
            ->whereIn('type', ['JOURNAL_IN', 'JOURNAL_OUT'])
            ->delete();
        
        // Log deletions for debugging
        \Log::info("Journal Entry Update - Deleted: Details={$deletedDetails}, Ledger={$deletedLedger}, LedgerV2={$deletedLedgerV2}");
        
        // FIX: Double-check no entries exist before inserting
        $remainingDetails = JournalEntryDetails::where('journal_entry_main_id', '=', $id)->count();
        $remainingLedgerV2 = LedgerEntriesV2::where('key_id', $id)
            ->whereIn('type', ['JOURNAL_IN', 'JOURNAL_OUT'])
            ->count();
        
        if ($remainingDetails > 0 || $remainingLedgerV2 > 0) {
            \Log::warning("Journal Entry Update - Found remaining entries: Details={$remainingDetails}, LedgerV2={$remainingLedgerV2}");
            // Force delete again
            JournalEntryDetails::where('journal_entry_main_id', '=', $id)->forceDelete();
            LedgerEntriesV2::where('key_id', $id)
                ->whereIn('type', ['JOURNAL_IN', 'JOURNAL_OUT'])
                ->forceDelete();
        }
        
        for ($i = 0; $i < count($entriesList); $i++) {
            // ... create entries ...
            
            // FIX: Check if ledger entry already exists before creating
            $existingLedgerV2 = LedgerEntriesV2::where('key_id', $JournalEntryMain->id)
                ->where('key_id_2', $JournalEntryDetails->id)
                ->whereIn('type', ['JOURNAL_IN', 'JOURNAL_OUT'])
                ->first();
            
            if (!$existingLedgerV2) {
                $LedgerEntries = new LedgerEntriesV2();
                // ... set properties ...
                $LedgerEntries->save();
            } else {
                \Log::warning("Journal Entry Update - Skipped duplicate LedgerEntriesV2 for detail_id={$JournalEntryDetails->id}");
            }
        }
        
        $JournalEntryMain->total_debit = $total_debit;
        $JournalEntryMain->total_credit = $total_credit;
        $JournalEntryMain->save();
        
        DB::commit();
        $lock->release();
        
        return response()->json(['status' => 1, 'message' => 'Journal entry updated successfully']);
        
    } catch (\Exception $e) {
        DB::rollBack();
        $lock->release();
        \Log::error("Journal Entry Update Error: " . $e->getMessage());
        return response()->json(['status' => 2, 'message' => 'Error updating journal entry: ' . $e->getMessage()]);
    }
}
*/

// ============================================================================
// FIX 3: Add Request Validation Middleware
// ============================================================================
// Create: app/Http/Middleware/PreventDuplicateJournalUpdate.php

/*
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class PreventDuplicateJournalUpdate
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post') && $request->route()->named('journal.update')) {
            $journalId = $request->route('id');
            $lockKey = "journal_update_lock_{$journalId}";
            
            if (Cache::has($lockKey)) {
                return response()->json([
                    'status' => 2,
                    'message' => 'Another update is in progress. Please wait a moment and try again.'
                ], 429);
            }
            
            Cache::put($lockKey, true, 30); // 30 second lock
            
            $response = $next($request);
            
            Cache::forget($lockKey);
            
            return $response;
        }
        
        return $next($request);
    }
}
*/

// ============================================================================
// FIX 4: Add Database Unique Constraint (Migration)
// ============================================================================

/*
// Create migration: database/migrations/xxxx_add_unique_constraint_journal_entry_details.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintJournalEntryDetails extends Migration
{
    public function up()
    {
        // Add unique constraint to prevent duplicate entries
        // Note: This might need adjustment based on your exact requirements
        Schema::table('journal_entry_details', function (Blueprint $table) {
            $table->unique([
                'journal_entry_main_id',
                'account_code_id',
                'amount',
                'sst_amount',
                'transaction_type',
                'case_id'
            ], 'unique_journal_entry_detail');
        });
        
        // Add unique constraint for ledger_entries_v2
        Schema::table('ledger_entries_v2', function (Blueprint $table) {
            $table->unique([
                'key_id',
                'key_id_2',
                'type'
            ], 'unique_ledger_entry_v2_journal');
        });
    }
    
    public function down()
    {
        Schema::table('journal_entry_details', function (Blueprint $table) {
            $table->dropUnique('unique_journal_entry_detail');
        });
        
        Schema::table('ledger_entries_v2', function (Blueprint $table) {
            $table->dropUnique('unique_ledger_entry_v2_journal');
        });
    }
}
*/


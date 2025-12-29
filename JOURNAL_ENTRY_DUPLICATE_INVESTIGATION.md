# Journal Entry Duplicate Investigation Report

## Summary
Investigation of duplicate journal entries in the system. Found multiple potential causes and recommended fixes.

---

## 🔍 Issues Identified

### 1. **No Double-Submission Protection** ⚠️ HIGH RISK
**Location**: `resources/views/dashboard/journal-entry/edit.blade.php` (line 935-956)

**Problem**:
- The `SaveJournalEntry()` function has no protection against:
  - Double-clicking the save button
  - Network retries
  - Browser back/forward navigation
  - Multiple tabs submitting simultaneously

**Impact**: If the AJAX request is sent twice, both requests will:
1. Delete existing entries
2. Insert new entries
3. Result: Duplicate entries created

**Evidence**: The AJAX call has no:
- Button disable during submission
- Request locking mechanism
- Duplicate request detection

---

### 2. **Delete-Then-Insert Without Transaction** ⚠️ HIGH RISK
**Location**: `app/Http/Controllers/AccountController.php` (lines 4364-4468)

**Problem**:
```php
// Current code flow:
JournalEntryDetails::where('journal_entry_main_id', '=', $id)->delete();
LedgerEntries::where('cheque_no', '=', $JournalEntryMain->journal_no)->delete();
LedgerEntriesV2::where('key_id', $id)->whereIn('type', ['JOURNAL_IN', 'JOURNAL_OUT'])->delete();

// Then inserts new entries...
for ($i = 0; $i < count($entriesList); $i++) {
    // Creates new entries
}
```

**Issues**:
- No database transaction wrapping
- If process fails mid-way, data is partially deleted
- If called twice concurrently:
  - First call deletes entries
  - Second call also deletes (nothing left)
  - Both calls insert → duplicates created

**Impact**: Race conditions can cause duplicates

---

### 3. **No Duplicate Detection in entries_list** ⚠️ MEDIUM RISK
**Location**: `app/Http/Controllers/AccountController.php` (line 4371)

**Problem**:
- The code loops through `entries_list` without checking for duplicates
- If the frontend accidentally sends duplicate entries, they all get inserted

**Example Scenario**:
```javascript
// Frontend could accidentally create:
entries_list = [
    {account_code_id: 1, debit: 100, ...},
    {account_code_id: 1, debit: 100, ...}, // Duplicate!
]
```

**Impact**: Duplicate entries in the same request

---

### 4. **No Verification After Delete** ⚠️ MEDIUM RISK
**Location**: `app/Http/Controllers/AccountController.php` (lines 4364-4368)

**Problem**:
- Deletes are performed but not verified
- If delete fails silently, old entries remain
- New entries are inserted on top → duplicates

**Impact**: Failed deletes result in duplicates

---

### 5. **No Check Before Creating LedgerEntriesV2** ⚠️ MEDIUM RISK
**Location**: `app/Http/Controllers/AccountController.php` (lines 4435-4463)

**Problem**:
```php
$LedgerEntries = new LedgerEntriesV2();
// ... set properties ...
$LedgerEntries->save(); // No check if it already exists!
```

**Impact**: If delete didn't work, new ledger entries are created alongside old ones

---

## 🛠️ Recommended Fixes

### Fix 1: Add Double-Submission Protection (Frontend)
**File**: `resources/views/dashboard/journal-entry/edit.blade.php`

**Changes**:
1. Disable save button during AJAX call
2. Show loading indicator
3. Re-enable button on error

**Code**:
```javascript
function SaveJournalEntry() {
    // Prevent double submission
    var $saveBtn = $('#saveBtn');
    if ($saveBtn.prop('disabled')) {
        return;
    }
    
    // ... validation ...
    
    // Disable and show loading
    $saveBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    
    $.ajax({
        // ... existing code ...
        success: function(result) {
            if (result.status == 1) {
                Swal.fire('Success!', result.message, 'success');
                location.reload();
            } else {
                Swal.fire('notice!', result.message, 'warning');
                $saveBtn.prop('disabled', false).html('Save');
            }
        },
        error: function() {
            Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
            $saveBtn.prop('disabled', false).html('Save');
        }
    });
}
```

---

### Fix 2: Add Database Transaction & Duplicate Detection (Backend)
**File**: `app/Http/Controllers/AccountController.php`

**Changes**:
1. Wrap in DB transaction
2. Add cache lock for concurrent requests
3. Remove duplicates from entries_list
4. Verify deletions before inserting
5. Check before creating ledger entries

**Key Code Additions**:
```php
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

public function updateJournalEntry(Request $request, $id)
{
    // Add cache lock
    $lockKey = "journal_entry_update_{$id}";
    $lock = Cache::lock($lockKey, 30);
    
    if (!$lock->get()) {
        return response()->json(['status' => 2, 'message' => 'Another update is in progress.']);
    }
    
    try {
        DB::beginTransaction();
        
        // Remove duplicates from entries_list
        $uniqueEntries = [];
        $seenEntries = [];
        foreach ($entriesList as $entry) {
            $entryKey = md5(json_encode([
                'account_code_id' => $entry['account_code_id'] ?? '',
                'desc' => $entry['desc'] ?? '',
                'debit' => $entry['debit'] ?? 0,
                'credit' => $entry['credit'] ?? 0,
                'case_id' => $entry['case_id'] ?? '',
            ]));
            
            if (!isset($seenEntries[$entryKey])) {
                $seenEntries[$entryKey] = true;
                $uniqueEntries[] = $entry;
            }
        }
        $entriesList = $uniqueEntries;
        
        // Delete existing entries
        JournalEntryDetails::where('journal_entry_main_id', '=', $id)->delete();
        LedgerEntriesV2::where('key_id', $id)
            ->whereIn('type', ['JOURNAL_IN', 'JOURNAL_OUT'])
            ->delete();
        
        // Verify deletions
        $remaining = JournalEntryDetails::where('journal_entry_main_id', '=', $id)->count();
        if ($remaining > 0) {
            // Force delete if needed
            JournalEntryDetails::where('journal_entry_main_id', '=', $id)->forceDelete();
        }
        
        // Insert new entries with duplicate check
        for ($i = 0; $i < count($entriesList); $i++) {
            // ... create JournalEntryDetails ...
            
            // Check before creating LedgerEntriesV2
            $existing = LedgerEntriesV2::where('key_id', $JournalEntryMain->id)
                ->where('key_id_2', $JournalEntryDetails->id)
                ->whereIn('type', ['JOURNAL_IN', 'JOURNAL_OUT'])
                ->first();
            
            if (!$existing) {
                $LedgerEntries = new LedgerEntriesV2();
                // ... set properties ...
                $LedgerEntries->save();
            }
        }
        
        DB::commit();
        $lock->release();
        
        return response()->json(['status' => 1, 'message' => 'Updated successfully']);
        
    } catch (\Exception $e) {
        DB::rollBack();
        $lock->release();
        \Log::error("Journal Entry Update Error: " . $e->getMessage());
        return response()->json(['status' => 2, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
```

---

### Fix 3: Add Request Locking Middleware (Optional but Recommended)
**File**: Create `app/Http/Middleware/PreventDuplicateJournalUpdate.php`

This provides an additional layer of protection at the middleware level.

---

### Fix 4: Add Database Constraints (Optional)
**File**: Create migration for unique constraints

This provides database-level protection against duplicates.

---

## 📊 Priority Order

1. **HIGH PRIORITY**: Fix 1 (Frontend double-submission protection)
2. **HIGH PRIORITY**: Fix 2 (Backend transaction & duplicate detection)
3. **MEDIUM PRIORITY**: Fix 3 (Middleware locking)
4. **LOW PRIORITY**: Fix 4 (Database constraints - may need adjustment)

---

## 🧪 Testing Recommendations

After implementing fixes, test:
1. Double-click save button rapidly
2. Submit form, then quickly refresh and submit again
3. Open same journal entry in two tabs and update simultaneously
4. Submit form with duplicate entries in entries_list
5. Simulate network failure mid-update
6. Check logs for any warnings/errors

---

## 📝 Notes

- The current issue shows 62 duplicate `ledger_entries_v2` entries
- This suggests the delete didn't work properly, or entries were inserted twice
- The fixes above should prevent future occurrences
- Consider adding logging to track when duplicates are detected/prevented


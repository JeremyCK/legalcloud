# Investigation: Why Ramakrishnan Branch Invoices Don't Appear

## 🔍 Problem
When searching for invoices in the SST transfer modal, invoices from Ramakrishnan & Co branch (branch_id = 4) are not appearing, even when "Ramakrishnan & Co" is selected in the branch filter dropdown.

## 🐛 Root Causes Identified

### Issue 1: NULL invoice_branch_id Not Handled
**Problem:** The branch filter only checks `b.invoice_branch_id`, but many invoices have `NULL` for this field. When `invoice_branch_id` is NULL, the filter excludes those invoices even if the case belongs to Ramakrishnan branch.

**Location:** `app/Http/Controllers/SSTV2Controller.php` line 333 and 370-372

**Original Code:**
```php
// Line 333 - Only checks invoice_branch_id
BranchAccessService::applyBranchFilter($query, $current_user, 'b.invoice_branch_id');

// Line 370-372 - Only checks invoice_branch_id
if ($filterBranch && $filterBranch != 0) {
    $query = $query->where('b.invoice_branch_id', $filterBranch);
}
```

**Impact:** 
- Invoices with `invoice_branch_id = NULL` are excluded
- Even if `case.branch_id = 4` (Ramakrishnan), they won't appear
- Branch filter dropdown doesn't work for these invoices

### Issue 2: BranchAccessService Filter Applied First
**Problem:** The `BranchAccessService::applyBranchFilter()` is applied BEFORE the user's branch filter dropdown, which means:
- If user doesn't have access to branch 4, invoices are filtered out
- Even if user selects "Ramakrishnan & Co" from dropdown, they're already excluded

## ✅ Fix Applied

### Fix 1: Handle NULL invoice_branch_id with Case Branch Fallback

**Changed from:**
```php
BranchAccessService::applyBranchFilter($query, $current_user, 'b.invoice_branch_id');
```

**Changed to:**
```php
// Apply centralized branch filtering with fallback to case branch_id
$accessibleBranches = \App\Services\BranchAccessService::getAccessibleBranchIds($current_user);

// Handle NULL invoice_branch_id by falling back to case branch_id
if (count($accessibleBranches) === 1) {
    $query->where(function($q) use ($accessibleBranches) {
        $q->where('b.invoice_branch_id', '=', $accessibleBranches[0])
          ->orWhere(function($subQ) use ($accessibleBranches) {
              $subQ->whereNull('b.invoice_branch_id')
                   ->where('l.branch_id', '=', $accessibleBranches[0]);
          });
    });
} else {
    $query->where(function($q) use ($accessibleBranches) {
        $q->whereIn('b.invoice_branch_id', $accessibleBranches)
          ->orWhere(function($subQ) use ($accessibleBranches) {
              $subQ->whereNull('b.invoice_branch_id')
                   ->whereIn('l.branch_id', $accessibleBranches);
          });
    });
}
```

### Fix 2: Branch Filter Dropdown with Fallback

**Changed from:**
```php
if ($filterBranch && $filterBranch != 0) {
    $query = $query->where('b.invoice_branch_id', $filterBranch);
}
```

**Changed to:**
```php
if ($filterBranch && $filterBranch != 0) {
    // Filter by branch with fallback to case branch_id if invoice_branch_id is NULL
    $query = $query->where(function($q) use ($filterBranch) {
        $q->where('b.invoice_branch_id', $filterBranch)
          ->orWhere(function($subQ) use ($filterBranch) {
              $subQ->whereNull('b.invoice_branch_id')
                   ->where('l.branch_id', $filterBranch);
          });
    });
}
```

## 📋 How to Verify the Fix

### Step 1: Run Diagnostic SQL
Run the queries in `investigate_ramakrishnan_invoices.sql` to check:
- How many Ramakrishnan invoices have NULL `invoice_branch_id`
- How many should appear in search
- Sample invoices that should be visible

### Step 2: Test in Application
1. Go to: http://127.0.0.1:8000/sst-v2-create
2. Click "Manage Invoices"
3. Select "Ramakrishnan & Co" from branch dropdown
4. Click "Search"
5. **Expected:** Invoices from Ramakrishnan branch should now appear

### Step 3: Check Different Scenarios
- **With invoice_branch_id = 4:** Should appear ✅
- **With invoice_branch_id = NULL but case.branch_id = 4:** Should now appear ✅ (was broken before)
- **User without access to branch 4:** Should not see invoices (by design)
- **User with access to branch 4:** Should see invoices ✅

## 🔍 Additional Checks

### Check User's Branch Access
The user's accessible branches are determined by:
- **Admin/Account roles:** Can access all branches
- **Maker/Lawyer roles:** Only their own branch (unless configured otherwise)
- **Sales role:** Based on configuration

**To check user's accessible branches:**
```php
// In Laravel Tinker
$user = \App\Models\Users::find([USER_ID]);
$accessibleBranches = \App\Services\BranchAccessService::getAccessibleBranchIds($user);
print_r($accessibleBranches);
```

### Check if Invoices Have NULL invoice_branch_id
```sql
SELECT 
    COUNT(*) as total,
    COUNT(CASE WHEN b.invoice_branch_id = 4 THEN 1 END) as has_branch_id_4,
    COUNT(CASE WHEN b.invoice_branch_id IS NULL AND l.branch_id = 4 THEN 1 END) as null_but_case_4
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN loan_case l ON l.id = b.case_id
WHERE l.branch_id = 4
  AND im.status <> 99;
```

## 📝 Summary

**Issues Found:**
1. ❌ NULL `invoice_branch_id` not handled - invoices excluded even if case belongs to branch
2. ❌ Branch filter dropdown doesn't check case branch_id as fallback

**Fixes Applied:**
1. ✅ BranchAccessService filter now falls back to `case.branch_id` when `invoice_branch_id` is NULL
2. ✅ Branch filter dropdown now checks both `invoice_branch_id` and `case.branch_id`

**Result:**
- Ramakrishnan branch invoices should now appear in search
- Works for both invoices with `invoice_branch_id = 4` and `invoice_branch_id = NULL` (with case branch = 4)

## 🧪 Testing Checklist

- [ ] Test with branch filter = "Ramakrishnan & Co"
- [ ] Test with branch filter = "All Branches" (if user has access)
- [ ] Test with no branch filter selected
- [ ] Verify invoices with `invoice_branch_id = 4` appear
- [ ] Verify invoices with `invoice_branch_id = NULL` but `case.branch_id = 4` appear
- [ ] Check browser console for any JavaScript errors
- [ ] Check Laravel logs for any SQL errors



# SST Delete Invoice Process - Investigation

## 🔍 Current Delete Process Flow

When you click the red "X" button to delete an invoice from SST Record 96, here's what happens:

### Step 1: Frontend (JavaScript) - `removeCurrentInvoice()`

**Location:** `resources/views/dashboard/sst-v2/edit.blade.php` (line 1086)

1. **Confirmation Dialog:**
   - Shows SweetAlert confirmation: "Are you sure?"
   - Message: "You won't be able to revert this! This will remove the invoice from SST transfer and update the invoice record."

2. **If User Confirms:**
   - Shows loading spinner
   - Makes AJAX POST request to `/deleteSSTDetail`

3. **AJAX Request Data:**
   ```javascript
   {
       _token: CSRF token,
       sst_detail_id: ID of the SST detail record,
       invoice_main_id: ID of the invoice
   }
   ```

### Step 2: Backend (Controller) - `deleteSSTDetail()`

**Location:** `app/Http/Controllers/SSTV2Controller.php` (line 786)

**Process:**

1. **Validation:**
   - Checks if `sst_detail_id` and `invoice_main_id` are provided
   - Returns error if missing

2. **Get SST Detail Record:**
   - Retrieves the `SSTDetails` record
   - Gets `sst_main_id` and `amount` (SST amount only)

3. **Delete SST Detail:**
   - Deletes the record from `sst_details` table
   - **Note:** This is a hard delete (not soft delete)

4. **Update Invoice Record:**
   ```php
   LoanCaseInvoiceMain::where('id', $invoiceMainId)->update([
       'bln_sst' => 0,                    // Mark SST as not transferred
       'transferred_sst_amt' => 0         // Reset transferred SST amount
   ]);
   ```
   **⚠️ ISSUE:** Only resets `transferred_sst_amt`, but NOT `transferred_reimbursement_sst_amt`!

5. **Update SST Main Total:**
   ```php
   $newTotal = $sstMain->amount - $deletedAmount;
   $sstMain->amount = max(0, $newTotal);
   ```
   **⚠️ ISSUE:** Only subtracts SST amount, doesn't subtract reimbursement SST!

6. **Return Response:**
   - Returns success with new total amount

### Step 3: Frontend (After Delete) - Success Handler

**Location:** `resources/views/dashboard/sst-v2/edit.blade.php` (line 1118)

1. **Remove Row from Table:**
   - Removes the invoice row from the "Current Invoices" table

2. **Update Totals:**
   - Calls `updateCurrentInvoiceTotals()` - recalculates totals from remaining rows
   - Calls `updateTransferTotalAmount()` - updates the "Transfer Total Amount" field

3. **Refresh Modal (if open):**
   - If invoice selection modal is open, refreshes to show deleted invoice as available

4. **Show Success Message:**
   - Shows success notification

---

## ⚠️ ISSUES IDENTIFIED

### Issue 1: Reimbursement SST Not Reset
**Problem:** When deleting an invoice, the `transferred_reimbursement_sst_amt` is NOT reset to 0.

**Impact:** 
- Invoice shows as having reimbursement SST already transferred
- Invoice won't be available for selection in other SST records
- Reimbursement SST amount is "stuck" on the invoice

**Current Code:**
```php
LoanCaseInvoiceMain::where('id', $invoiceMainId)->update([
    'bln_sst' => 0,
    'transferred_sst_amt' => 0
    // ❌ Missing: 'transferred_reimbursement_sst_amt' => 0
]);
```

### Issue 2: SST Main Total Calculation Wrong
**Problem:** When deleting, only the SST amount is subtracted, not the reimbursement SST.

**Impact:**
- SST Main total becomes incorrect
- Total doesn't match the sum of remaining invoices

**Current Code:**
```php
$newTotal = $sstMain->amount - $deletedAmount;  // Only SST amount
// ❌ Should also subtract reimbursement SST
```

---

## ✅ RECOMMENDED FIX

### Fix 1: Reset Reimbursement SST on Invoice

Update `deleteSSTDetail()` function:

```php
// Update loan_case_invoice_main record
LoanCaseInvoiceMain::where('id', $invoiceMainId)->update([
    'bln_sst' => 0,
    'transferred_sst_amt' => 0,
    'transferred_reimbursement_sst_amt' => 0  // ✅ ADD THIS
]);
```

### Fix 2: Recalculate SST Main Total Properly

Instead of subtracting, recalculate from remaining invoices:

```php
// Instead of: $newTotal = $sstMain->amount - $deletedAmount;
// Do this:
$remainingDetails = SSTDetails::where('sst_main_id', $sstMainId)->get();
$newTotal = 0;

foreach ($remainingDetails as $detail) {
    $invoice = LoanCaseInvoiceMain::find($detail->loan_case_invoice_main_id);
    if ($invoice) {
        $sstAmount = $detail->amount ?? 0;
        $reimbursementSst = $invoice->reimbursement_sst ?? 0;
        $transferredReimbSst = $invoice->transferred_reimbursement_sst_amt ?? 0;
        $remainingReimbSst = max(0, $reimbursementSst - $transferredReimbSst);
        $newTotal += $sstAmount + $remainingReimbSst;
    }
}

$sstMain->amount = $newTotal;
```

---

## 📋 Complete Delete Process (After Fix)

### What Gets Updated:

1. **sst_details table:**
   - ✅ Record is deleted

2. **loan_case_invoice_main table:**
   - ✅ `bln_sst` = 0 (SST not transferred)
   - ✅ `transferred_sst_amt` = 0 (Reset SST transferred amount)
   - ✅ `transferred_reimbursement_sst_amt` = 0 (Reset reimbursement SST - NEEDS FIX)

3. **sst_main table:**
   - ✅ `amount` = Recalculated total (NEEDS FIX to include reimbursement SST)

4. **Frontend:**
   - ✅ Row removed from table
   - ✅ Totals recalculated
   - ✅ Transfer Total Amount updated

---

## 🔄 What Happens to the Invoice After Delete?

1. **Invoice Status:**
   - `bln_sst` = 0 → Invoice is no longer marked as SST transferred
   - `transferred_sst_amt` = 0 → SST amount reset
   - `transferred_reimbursement_sst_amt` = 0 → Reimbursement SST reset (after fix)

2. **Invoice Availability:**
   - Invoice becomes available for selection in other SST records
   - Can be added to new SST transfers

3. **SST Record:**
   - Invoice removed from the SST record
   - SST record total updated
   - Invoice no longer appears in the "Current Invoices" table

---

## 🧪 Testing the Delete Process

### Test Scenario:
1. Go to: http://127.0.0.1:8000/sst-v2-edit/96
2. Note the current "Transfer Total Amount"
3. Note the invoice details (SST and Reimb SST)
4. Click red "X" on one invoice
5. Confirm deletion
6. Verify:
   - Invoice row is removed
   - Transfer Total Amount is updated
   - Invoice can be selected again in modal

### Expected Results (After Fix):
- ✅ Invoice removed from table
- ✅ Totals recalculated correctly
- ✅ Invoice available for selection again
- ✅ Reimbursement SST reset on invoice
- ✅ SST Main total accurate

---

## 📝 Summary

**Current Process:**
1. User clicks delete → Confirmation → AJAX request
2. Backend deletes SST detail record
3. Backend resets invoice SST flags (but NOT reimbursement SST)
4. Backend updates SST main total (but calculation is wrong)
5. Frontend removes row and updates totals

**Issues:**
- ❌ Reimbursement SST not reset on invoice
- ❌ SST Main total calculation doesn't include reimbursement SST

**Recommended Actions:**
1. Fix `deleteSSTDetail()` to reset `transferred_reimbursement_sst_amt`
2. Fix SST Main total calculation to include reimbursement SST
3. Test the delete process thoroughly



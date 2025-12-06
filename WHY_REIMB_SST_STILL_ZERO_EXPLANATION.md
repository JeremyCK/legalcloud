# Why Reimb SST Still Shows 0.00 After Running Patch

## The Problem

After running the patch scripts, Reimb SST still shows 0.00 because:

1. **The patch set `transferred_reimbursement_sst_amt = reimbursement_sst`**
   - This marks reimbursement SST as "fully transferred"
   - Remaining reimbursement SST = `reimbursement_sst - transferred_reimbursement_sst_amt = 0`

2. **The view shows REMAINING reimbursement SST, not total**
   - Formula: `remaining_reimb_sst = reimbursement_sst - transferred_reimbursement_sst_amt`
   - If `transferred_reimbursement_sst_amt = reimbursement_sst`, then `remaining = 0`
   - So it shows 0.00 (correct, since it's already transferred)

3. **The SST main total calculation also uses remaining reimbursement SST**
   - Total = SST + remaining reimbursement SST
   - If remaining = 0, reimbursement SST is NOT included in the total

## The Solution

**If reimbursement SST should be included in SST 96**, you need to **RESET** `transferred_reimbursement_sst_amt` to 0, not set it equal to `reimbursement_sst`.

## Two Scenarios

### Scenario 1: Reimbursement SST Should Be in SST 96
- **Action:** Reset `transferred_reimbursement_sst_amt = 0`
- **Result:** Reimb SST column will show values > 0
- **Result:** SST main total will include reimbursement SST

### Scenario 2: Reimbursement SST Already Transferred to Another SST Record
- **Action:** Keep `transferred_reimbursement_sst_amt = reimbursement_sst`
- **Result:** Reimb SST column shows 0.00 (correct)
- **Result:** SST main total does NOT include reimbursement SST (correct)

## How to Fix

Run this script to reset reimbursement SST so it shows in SST 96:

```sql
SOURCE reset_reimb_sst_to_show_in_sst_96.sql;
```

**⚠️ WARNING:** Only run this if reimbursement SST should be included in SST 96. If it was already transferred to another SST record, resetting will cause **double-counting**.

## What the Reset Script Does

1. **Resets `transferred_reimbursement_sst_amt = 0`** for all invoices in SST 96
2. **Recalculates SST main total** to include reimbursement SST
3. **Shows verification** of what changed

## Expected Result After Reset

- ✅ Reimb SST column shows values > 0 (e.g., 81.22, 69.21, etc.)
- ✅ Total SST = SST + Reimb SST for each row
- ✅ Transfer Total Amount increases to include reimbursement SST
- ✅ SST main total includes both SST and reimbursement SST

## Example

For invoice A20000408:
- **Before reset:**
  - SST: 581.00
  - Reimbursement SST: 81.22
  - Transferred Reimb SST: 81.22
  - **Remaining Reimb SST: 0.00** ❌

- **After reset:**
  - SST: 581.00
  - Reimbursement SST: 81.22
  - Transferred Reimb SST: 0.00 (reset)
  - **Remaining Reimb SST: 81.22** ✅
  - **Total SST: 662.22** ✅

## Recommendation

**First, verify:** Was reimbursement SST already transferred to another SST record?

If **NO** (should be in SST 96):
- Run `reset_reimb_sst_to_show_in_sst_96.sql`

If **YES** (already in another SST record):
- Don't reset - 0.00 is correct
- But then SST 96 total won't include reimbursement SST

## Summary

The previous patch scripts marked reimbursement SST as "transferred" (which is why it shows 0.00). If you want reimbursement SST to show in SST 96, you need to **reset** the transferred amount to 0.





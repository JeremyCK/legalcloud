# Reimbursement SST Already Transferred - Explanation

## The Situation

Your diagnostic results show that **ALL invoices have reimbursement SST**, but it's showing **0.00** because:

```
reimbursement_sst = transferred_reimbursement_sst_amt
```

This means the reimbursement SST was **already fully transferred** to another SST record (or marked as transferred).

## The Question

**Should the reimbursement SST be included in SST Record 96?**

There are two scenarios:

### Scenario 1: Reimbursement SST Should Be in SST 96
- The invoices were added to SST 96, so their reimbursement SST should also be included
- The `transferred_reimbursement_sst_amt` was incorrectly set (maybe from a previous transfer attempt)
- **Solution:** Reset `transferred_reimbursement_sst_amt` to 0 so reimbursement SST shows in SST 96

### Scenario 2: Reimbursement SST Was Already Transferred to Another SST Record
- The reimbursement SST was correctly transferred to a different SST record
- SST 96 should only include the regular SST, not reimbursement SST
- **Solution:** Leave it as is - 0.00 is correct

## How to Check

Run this query to see which SST records contain these invoices:

```sql
SOURCE check_where_reimb_sst_transferred.sql;
```

This will show:
- If invoices are in SST 96 (they should be)
- Which other SST records contain these invoices
- Whether reimbursement SST was transferred when added to SST 96

## How to Fix (If Reimbursement SST Should Be in SST 96)

If you determine that reimbursement SST **should** be included in SST 96, run:

```sql
SOURCE reset_reimb_sst_for_sst_96.sql;
```

**⚠️ WARNING:** This will reset `transferred_reimbursement_sst_amt` to 0 for all invoices in SST 96. 

**Before running:**
1. Check if these invoices are in other SST records
2. Verify that reimbursement SST wasn't already included in another SST record
3. If it was included elsewhere, resetting will cause **double-counting**

## Expected Result After Reset

After resetting (if appropriate):
- ✅ Reimb SST column will show values > 0
- ✅ Total SST = SST + Reimb SST
- ✅ Transfer Total Amount will increase to include reimbursement SST

## Example Calculation

For invoice A20000408:
- SST: 581.00
- Reimbursement SST: 81.22
- Transferred Reimb SST: 81.22 (currently)
- **Remaining Reimb SST: 0.00** (because already transferred)

After reset (if appropriate):
- SST: 581.00
- Reimbursement SST: 81.22
- Transferred Reimb SST: 0.00 (reset)
- **Remaining Reimb SST: 81.22** ✅

## Recommendation

**Before resetting, verify:**
1. Are these invoices in other SST records?
2. Was reimbursement SST already included in another SST record?
3. Should reimbursement SST be included in SST 96?

If unsure, check the other SST records first to avoid double-counting.






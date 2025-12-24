# Purpose of Remaining SST Check

## The Condition
```sql
((im.sst_inv - COALESCE(im.transferred_sst_amt, 0)) + 
 (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) > 0
```

## Purpose

This check ensures that **only invoices with remaining SST available to transfer** are shown in the selection list.

### What it calculates:
- **Remaining Regular SST** = `sst_inv - transferred_sst_amt`
- **Remaining Reimbursement SST** = `reimbursement_sst - transferred_reimbursement_sst_amt`
- **Total Remaining SST** = Remaining Regular SST + Remaining Reimbursement SST

### Why it's needed:

1. **Prevent Double Transfer**: If an invoice has already transferred all its SST (both regular and reimbursement), showing it would allow users to try transferring it again, which could cause data inconsistencies.

2. **User Experience**: Users should only see invoices that have something available to transfer. Showing invoices with zero remaining SST would be confusing and waste time.

3. **Data Integrity**: Ensures that the system only allows transferring SST that actually exists and hasn't been transferred yet.

## Example Scenarios

### Scenario 1: Invoice with Remaining SST ✅
- `sst_inv` = 100.00
- `transferred_sst_amt` = 50.00
- `reimbursement_sst` = 20.00
- `transferred_reimbursement_sst_amt` = 0.00
- **Remaining SST** = (100 - 50) + (20 - 0) = 70.00
- **Result**: ✅ **SHOWS** in list (has 70.00 available to transfer)

### Scenario 2: Invoice with Zero Remaining SST ❌
- `sst_inv` = 100.00
- `transferred_sst_amt` = 100.00 (fully transferred)
- `reimbursement_sst` = 20.00
- `transferred_reimbursement_sst_amt` = 20.00 (fully transferred)
- **Remaining SST** = (100 - 100) + (20 - 20) = 0.00
- **Result**: ❌ **HIDDEN** from list (nothing left to transfer)

### Scenario 3: Invoice with Only Reimbursement SST Remaining ✅
- `sst_inv` = 100.00
- `transferred_sst_amt` = 100.00 (fully transferred)
- `reimbursement_sst` = 20.00
- `transferred_reimbursement_sst_amt` = 0.00
- **Remaining SST** = (100 - 100) + (20 - 0) = 20.00
- **Result**: ✅ **SHOWS** in list (has 20.00 reimbursement SST available)

## When to Remove This Check

You might want to remove or make this check optional if:

1. **Audit/Viewing Purpose**: You want to see ALL invoices (even those fully transferred) for auditing or reporting purposes.

2. **Partial Transfer Allowed**: Your business logic allows showing invoices even if they have zero remaining SST (though this would be unusual).

3. **Debugging**: Temporarily removing it to see if it's causing the filtering issue.

## Current Behavior in Code

Looking at `SSTV2Controller.php` line 429-431:

```php
// For normal selection and 'add' type, exclude invoices with zero remaining SST (including reimbursement SST)
// Remaining SST = (sst_inv - transferred_sst_amt) + (reimbursement_sst - transferred_reimbursement_sst_amt)
$query = $query->whereRaw('((im.sst_inv - COALESCE(im.transferred_sst_amt, 0)) + (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) > 0');
```

**Note**: This check is **skipped** when `type == 'transferred'` (line 427), because in that case, you want to see already-transferred invoices.

## Recommendation

**Keep this check** for normal selection because:
- It prevents transferring SST that doesn't exist
- It improves user experience by only showing actionable invoices
- It maintains data integrity

**However**, if you're getting results WITHOUT this condition, it means your Ramakrishnan invoices **DO have remaining SST**, so the condition should pass. If they're not showing, the issue is likely elsewhere (branch access, bln_invoice sync, etc.).











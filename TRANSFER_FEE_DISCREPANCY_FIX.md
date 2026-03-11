# Transfer Fee Discrepancy Investigation & Fix

## Issue Summary

**Problem**: Transfer fee record ID 447 shows total amount of **460,979.79**, but bank reconciliation shows **461,746.59** when filtering by transaction ID "DP001-0825" and same period.

**Difference**: 766.80

## Root Cause

The discrepancy was caused by the bank reconciliation function (`getBankReconTotal`) using a **LIKE pattern match** (`'%DP001-0825%'`) instead of an exact match when filtering by transaction ID.

### Why This Causes Issues

1. **LIKE Pattern Matching**: The query `WHERE transaction_id LIKE '%DP001-0825%'` matches ANY transaction ID containing "DP001-0825", including:
   - `DP001-0825` (the correct one)
   - `DP001-0825-001` (different transfer fee)
   - `DP001-0825-SOMETHING` (different transfer fee)
   - Any other transaction ID containing this pattern

2. **Multiple Transfer Fees**: When multiple transfer fees share similar transaction ID patterns, the LIKE query includes entries from all of them, inflating the total amount.

3. **Period Filtering**: Even though the code attempts to skip date filtering when transaction_id is provided, the LIKE pattern still matches entries from other transfer fees that may fall within the same period.

## Solution Implemented

### Fix in `getBankReconTotal()` Function

**File**: `app/Http/Controllers/AccountController.php`

**Change**: Modified the transaction ID filtering logic to use **exact match** when the transaction ID exists in `transfer_fee_main` table, falling back to LIKE pattern only if exact match doesn't exist.

```php
if ($hasTransactionIdFilter) {
    $trxId = trim($request->input("trx_id"));
    
    // Check if this transaction ID exists exactly in transfer_fee_main
    $exactMatchExists = DB::table('transfer_fee_main')
        ->where('transaction_id', '=', $trxId)
        ->where('status', '<>', 99)
        ->exists();
    
    if ($exactMatchExists) {
        // Use exact match for transfer fee transaction IDs
        $safe_keeping->where('m.transaction_id', '=', $trxId);
    } else {
        // Use LIKE for partial match only if exact match doesn't exist
        $safe_keeping->where('m.transaction_id', 'like', '%' . $trxId . '%');
    }
}
```

### Benefits

1. **Accurate Totals**: Exact match ensures only entries for the specific transfer fee are included
2. **Backward Compatible**: Still supports LIKE pattern for non-transfer-fee transaction types
3. **Prevents Discrepancies**: Eliminates the issue where multiple transaction IDs are matched

## Enhanced Diagnostic Function

**Function**: `diagnoseBankReconDiscrepancy()`

**Enhancement**: Added detection for LIKE pattern matches to help identify discrepancies:

- Shows exact match total vs LIKE pattern total
- Lists all transaction IDs that match the LIKE pattern
- Calculates the difference caused by LIKE pattern matching

## How to Verify the Fix

1. **Test Bank Reconciliation**:
   - Navigate to bank reconciliation page
   - Filter by transaction ID "DP001-0825"
   - Verify the total matches transfer fee record amount (460,979.79)

2. **Use Diagnostic Endpoint**:
   - Call `/diagnoseBankReconDiscrepancy` with:
     - `trx_id`: "DP001-0825"
     - `bank_id`: [appropriate bank ID]
   - Review the response to see:
     - Exact match total
     - LIKE pattern matches (if any)
     - Difference calculation

3. **Check Transfer Fee Record**:
   - Navigate to `/transferfee/447/edit`
   - Verify the total amount displayed matches bank reconciliation

## Investigation Script

A diagnostic script (`investigate_transfer_fee_discrepancy.php`) has been created to investigate discrepancies. This script:

- Compares transfer fee main amount vs calculated from details
- Checks ledger entries with exact match vs LIKE pattern
- Identifies extra entries that don't belong to the transfer fee
- Provides recommendations for fixing discrepancies

## Related Files

- `app/Http/Controllers/AccountController.php` - Main fix location
- `investigate_transfer_fee_discrepancy.php` - Diagnostic script
- `app/Http/Controllers/TransferFeeV3Controller.php` - Transfer fee controller

## Testing Checklist

- [ ] Bank reconciliation shows correct total for transaction ID "DP001-0825"
- [ ] Diagnostic endpoint shows no LIKE pattern matches for this transaction ID
- [ ] Transfer fee edit page total matches bank reconciliation total
- [ ] Other transaction IDs still work correctly with LIKE pattern (for non-transfer-fee transactions)
- [ ] Date filtering still works correctly when transaction_id is not provided

## Notes

- The fix maintains backward compatibility for non-transfer-fee transaction types
- The LIKE pattern is still used when exact match doesn't exist in transfer_fee_main
- This ensures other transaction types (vouchers, journals, etc.) can still use partial matching

# Transfer Fee Totals Investigation Report

## Transfer Fee ID: 491
- **Transaction ID**: DP003-1225
- **Transfer Date**: 2025-12-26
- **Total Invoices**: 66

## Summary

### Totals from Report:
- **Total Pfee**: 211,160.80
- **Total SST**: 16,892.84
- **Total Reimb**: 46,147.76
- **Total Reimb SST**: 3,691.83
- **Expected Total**: 277,893.23

### Transferred Amounts (This Transfer Fee):
- **Transferred Balance**: 207,711.80
- **Transferred SST**: 16,616.93
- **Total Transferred**: 224,328.73

### Discrepancy:
- **Difference**: 53,564.50 (277,893.23 - 224,328.73)

## Root Cause

**1 invoice was already fully transferred in another transfer fee:**

### Invoice DP20001139 (Invoice ID: 9838)
- **Case Ref**: DP/T/ZU/LPPSA/41087/AFBI/RUP
- **Original Amounts**:
  - Pfee: 3,449.01
  - SST: 275.92
  - Reimb: 1,160.00
  - Reimb SST: 92.80
  - **Expected Total**: 4,977.73

- **This Transfer Fee (491)**:
  - Transferred Pfee: 0.00
  - Transferred SST: 0.00
  - Transferred Reimb: 0.00
  - Transferred Reimb SST: 0.00
  - **Total**: 0.00

- **Already Transferred in Transfer Fee ID: 484**
  - Transaction: DP001-1225
  - Date: 2025-12-03
  - Transferred Pfee: 3,449.01
  - Transferred SST: 275.92
  - Transferred Reimb: 1,160.00
  - Transferred Reimb SST: 92.80
  - **Total**: 4,977.73

- **Status**: ✅ Fully transferred (no remaining amounts)

## Formula Analysis

### Expected Formula:
```
pfee + sst + reimb + reimbsst = transferredBal + transferredSst + remaining
```

### For Invoice DP20001139:
- **Expected**: 4,977.73
- **Actual (this transfer)**: 0.00 + 0.00 + 0.00 = 0.00
- **Difference**: 4,977.73 ❌

### Why It Doesn't Match:
The "Transferred Balance" and "Transferred SST" columns only show amounts transferred in **THIS specific transfer fee record**, not the total transferred across all transfer fees.

For invoices that were already fully transferred in other transfer fees:
- They show 0.00 in "Transferred Balance" and "Transferred SST" columns
- But they still appear in the invoice list because they were added to this transfer fee before being fully transferred elsewhere

## Issue

**Invoice DP20001139 should not appear in Transfer Fee 491** because:
1. It was already fully transferred in Transfer Fee 484 (on 2025-12-03)
2. It has 0.00 transferred amounts in Transfer Fee 491
3. It contributes to the discrepancy: 4,977.73

## Recommendation

1. **Remove invoice DP20001139 from Transfer Fee 491** since it's already fully transferred elsewhere
2. **Update the edit view** to exclude or flag invoices that are already fully transferred in other transfer fees
3. **Fix the formula display** to account for invoices transferred elsewhere, or exclude them from the totals

## Other Invoices Status

- **65 invoices**: Not transferred elsewhere (normal)
- **1 invoice**: Fully transferred elsewhere (DP20001139) - **SHOULD BE REMOVED**


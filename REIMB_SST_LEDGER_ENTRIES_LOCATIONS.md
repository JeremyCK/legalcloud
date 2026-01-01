# REIMB_SST_IN and REIMB_SST_OUT Ledger Entries Locations

This document lists all places where `REIMB_SST_IN` and `REIMB_SST_OUT` are created or updated in the `ledger_entries_v2` table.

## 1. TransferFeeV3Controller.php

### Location 1: `createReimbursementLedgerEntries()` method
- **Lines**: 2233, 2270
- **Action**: Creates both `REIMB_SST_OUT` and `REIMB_SST_IN`
- **Context**: When creating transfer fee with reimbursement SST
- **Note**: Creates both OUT and IN entries

### Location 2: `updateTransferFeeLedgerEntries()` method  
- **Lines**: 2916-2979
- **Action**: Updates or creates both `REIMB_SST_OUT` and `REIMB_SST_IN`
- **Context**: When updating transfer fee details with reimbursement SST
- **Note**: Updates existing entries or creates new ones if they don't exist

## 2. CaseController.php

### Location: `updateTransferFeeLedgerEntriesV2()` method
- **Lines**: 14227-14243
- **Action**: Updates `REIMB_SST_OUT` only (does NOT create/update `REIMB_SST_IN`)
- **Context**: When updating transfer fee ledger entries for a case
- **Issue**: ⚠️ This method only handles `REIMB_SST_OUT`, missing `REIMB_SST_IN`

## 3. InvoiceController.php

### Location: `updateTransferFeeLedgerEntries()` method
- **Lines**: 2301-2369
- **Action**: Updates or creates both `REIMB_SST_OUT` and `REIMB_SST_IN`
- **Context**: When updating invoice transfer fee details
- **Note**: Updates existing entries or creates new ones if they don't exist

## 4. InvoiceFixController.php

### Location: `updateTransferFeeLedgerEntries()` method
- **Lines**: 1166-1229
- **Action**: Updates or creates both `REIMB_SST_OUT` and `REIMB_SST_IN`
- **Context**: Fix/repair script for updating transfer fee ledger entries
- **Note**: Updates existing entries or creates new ones if they don't exist

## 5. DataRepairController.php

### Location: `createReimbSstOutEntry()` and `createReimbSstInEntry()` methods
- **Lines**: 376-448
- **Action**: Creates `REIMB_SST_OUT` and `REIMB_SST_IN` entries
- **Context**: Data repair script (one-time use)
- **Note**: Repair script, not regular operation

## 6. TransferFeeV3Controller.php - Reconciliation Updates

### Location 1: Reconciliation (mark as reconciled)
- **Lines**: 1776-1778
- **Action**: Updates `is_recon = 1` and `recon_date` for both `REIMB_SST_IN` and `REIMB_SST_OUT`
- **Context**: When marking transfer fee as reconciled

### Location 2: Unreconciliation (mark as unreconciled)
- **Lines**: 1878-1880
- **Action**: Updates `is_recon = 0` and `recon_date = null` for both `REIMB_SST_IN` and `REIMB_SST_OUT`
- **Context**: When unmarking transfer fee as reconciled

## Summary

### Controllers that CREATE/UPDATE REIMB_SST entries:
1. ✅ **TransferFeeV3Controller** - Creates both OUT and IN (2 locations)
   - Also handles reconciliation updates (2 more locations)
2. ⚠️ **CaseController** - Only updates OUT (missing IN)
3. ✅ **InvoiceController** - Updates/creates both OUT and IN
4. ✅ **InvoiceFixController** - Updates/creates both OUT and IN
5. ✅ **DataRepairController** - Creates both OUT and IN (repair script)

### Key Findings:
- **CaseController** only handles `REIMB_SST_OUT` but not `REIMB_SST_IN` - this may be intentional or a bug
- All other controllers handle both OUT and IN entries
- The pattern is consistent: OUT entries have `transaction_type = 'C'` and IN entries have `transaction_type = 'D'`

## Recommendations:
1. Review `CaseController::updateTransferFeeLedgerEntriesV2()` to determine if it should also handle `REIMB_SST_IN`
2. Ensure all controllers follow the same pattern for creating/updating both OUT and IN entries


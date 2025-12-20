# SST Update - Transfer Fee Details & Ledger V2 Changes

## Overview
When invoice SST values are updated, the system now automatically:
1. Updates `transfer_fee_details` records to reflect new SST values
2. Recalculates `transfer_fee_main.transfer_amount` totals
3. Updates `ledger_entries_v2` records to match updated transfer fee details

---

## File Changes

### 1. `app/Http/Controllers/InvoiceController.php`

#### Added Imports:
```php
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;
```

#### Modified Method: `update()`
**Location:** Around line 640-675

**Changes:**
- Added call to `updateTransferFeeMainAmountsForInvoice()` after invoice update
- This triggers transfer fee details and ledger updates

**Code Added:**
```php
// Recalculate transfer fee main amounts for all transfer fees that include this invoice
$this->updateTransferFeeMainAmountsForInvoice($invoiceId);
```

---

#### New Method: `updateTransferFeeMainAmountsForInvoice()`
**Location:** Around line 1290-1387

**Purpose:** Updates transfer fee details when invoice SST changes

**What it does:**
1. Gets current invoice values (`reimbursement_sst`, `sst_inv`)
2. Finds all `transfer_fee_details` for this invoice
3. Updates `transfer_fee_details.reimbursement_sst_amount` to match current invoice value
4. Updates `transfer_fee_details.sst_amount` to match current invoice value (if changed)
5. Handles single and multiple transfer records proportionally
6. Calls `updateTransferFeeMainAmt()` to recalculate transfer fee main totals
7. Calls `updateLedgerEntriesForTransferFeeDetails()` to update ledger entries

**Key Logic:**
- If single transfer record: Updates directly to new invoice value
- If multiple transfer records: Updates proportionally based on each record's share
- Updates both `reimbursement_sst_amount` and `sst_amount` fields

---

#### New Method: `updateLedgerEntriesForTransferFeeDetails()`
**Location:** Around line 1389-1550

**Purpose:** Updates ledger entries V2 when transfer fee details change

**What it does:**
1. Updates existing ledger entries for:
   - `TRANSFER_OUT` / `TRANSFER_IN` (professional fee)
   - `SST_OUT` / `SST_IN` (professional fee SST)
   - `REIMB_OUT` / `REIMB_IN` (reimbursement amount)
   - `REIMB_SST_OUT` / `REIMB_SST_IN` (reimbursement SST) ⭐ **Main focus**

2. Creates new ledger entries if they don't exist:
   - Creates `REIMB_OUT` / `REIMB_IN` if reimbursement amount > 0
   - Creates `REIMB_SST_OUT` / `REIMB_SST_IN` if reimbursement SST > 0

**Ledger Entry Types Updated:**
- `TRANSFER_OUT` / `TRANSFER_IN` → Uses `transfer_fee_details.transfer_amount`
- `SST_OUT` / `SST_IN` → Uses `transfer_fee_details.sst_amount`
- `REIMB_OUT` / `REIMB_IN` → Uses `transfer_fee_details.reimbursement_amount`
- `REIMB_SST_OUT` / `REIMB_SST_IN` → Uses `transfer_fee_details.reimbursement_sst_amount` ⭐

**Key Fields Updated:**
- `amount` field in `ledger_entries_v2` table
- `updated_at` timestamp

**Key Fields Used for Matching:**
- `key_id_2` = `transfer_fee_details.id`
- `type` = Entry type (REIMB_SST_OUT, REIMB_SST_IN, etc.)
- `status` <> 99 (not deleted)

---

#### New Method: `updateTransferFeeMainAmt()`
**Location:** Around line 1552-1580

**Purpose:** Recalculates transfer fee main total amount

**What it does:**
1. Sums all `transfer_fee_details` components:
   - `transfer_amount` (professional fee)
   - `sst_amount` (professional fee SST)
   - `reimbursement_amount`
   - `reimbursement_sst_amount`
2. Updates `transfer_fee_main.transfer_amount` with total
3. Logs changes when amount differs significantly

---

## Flow Diagram

```
Invoice SST Updated
    ↓
InvoiceController::update()
    ↓
updateTransferFeeMainAmountsForInvoice()
    ├── Update transfer_fee_details.reimbursement_sst_amount
    ├── Update transfer_fee_details.sst_amount
    ├── updateTransferFeeMainAmt() → Recalculate transfer_fee_main.transfer_amount
    └── updateLedgerEntriesForTransferFeeDetails()
        ├── Update ledger_entries_v2 (REIMB_SST_OUT/IN)
        ├── Update ledger_entries_v2 (SST_OUT/IN)
        ├── Update ledger_entries_v2 (REIMB_OUT/IN)
        └── Update ledger_entries_v2 (TRANSFER_OUT/IN)
```

---

## Database Tables Affected

### 1. `transfer_fee_details`
**Fields Updated:**
- `reimbursement_sst_amount` → Updated to match invoice `reimbursement_sst`
- `sst_amount` → Updated to match invoice `sst_inv` (if changed)

**When:** Every time invoice SST is updated

---

### 2. `transfer_fee_main`
**Fields Updated:**
- `transfer_amount` → Recalculated as sum of all transfer_fee_details components

**When:** After transfer_fee_details are updated

---

### 3. `ledger_entries_v2`
**Fields Updated:**
- `amount` → Updated to match transfer_fee_details values
- `updated_at` → Set to current timestamp

**Entry Types Updated:**
- `REIMB_SST_OUT` / `REIMB_SST_IN` ⭐ **Primary focus**
- `SST_OUT` / `SST_IN`
- `REIMB_OUT` / `REIMB_IN`
- `TRANSFER_OUT` / `TRANSFER_IN`

**Matching Criteria:**
- `key_id_2` = `transfer_fee_details.id`
- `type` = Entry type
- `status` <> 99

**When:** After transfer_fee_details are updated

---

## Example Scenario

### Before Update:
```
Invoice:
  reimbursement_sst = 67.55

Transfer Fee Detail:
  reimbursement_sst_amount = 67.55

Ledger Entry V2:
  type = REIMB_SST_OUT
  amount = 67.55
  
Ledger Entry V2:
  type = REIMB_SST_IN
  amount = 67.55
```

### After SST Update to 67.56:
```
Invoice:
  reimbursement_sst = 67.56 ✅

Transfer Fee Detail:
  reimbursement_sst_amount = 67.56 ✅ (automatically updated)

Ledger Entry V2:
  type = REIMB_SST_OUT
  amount = 67.56 ✅ (automatically updated)
  
Ledger Entry V2:
  type = REIMB_SST_IN
  amount = 67.56 ✅ (automatically updated)
```

---

## Logging

All operations are logged with:
- Transfer fee detail updates
- Ledger entry updates/creates
- Transfer fee main recalculations

**Log Location:** `storage/logs/laravel-YYYY-MM-DD.log`

**Example Log Entry:**
```json
{
  "message": "Updated transfer fee details and main amounts for invoice",
  "invoice_id": "9721",
  "transfer_fee_main_ids": [489],
  "old_total_reimbursement_sst": 67.55,
  "new_total_reimbursement_sst": "67.56",
  "details_updated": 1,
  "ledger_entries_updated": 2,
  "ledger_entries_created": 0
}
```

---

## Summary

**Single File Changed:**
- `app/Http/Controllers/InvoiceController.php`

**Methods Added:**
1. `updateTransferFeeMainAmountsForInvoice()` - Updates transfer fee details
2. `updateLedgerEntriesForTransferFeeDetails()` - Updates ledger entries V2
3. `updateTransferFeeMainAmt()` - Recalculates transfer fee main totals

**Methods Modified:**
1. `update()` - Added call to update transfer fees and ledger

**Database Tables Updated:**
1. `transfer_fee_details` - SST amounts updated
2. `transfer_fee_main` - Total amount recalculated
3. `ledger_entries_v2` - Amounts updated to match transfer fee details

**Result:**
When invoice SST is updated, transfer fee details and ledger entries automatically reflect the new values, ensuring data consistency across the system.


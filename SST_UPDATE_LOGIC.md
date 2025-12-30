# SST Column Update Logic: `sst` vs `ori_invoice_sst`

## Column Definitions

### `sst` Column
- **Purpose**: Individual SST value for each invoice detail item
- **Single Invoice**: Same as `ori_invoice_sst` (the SST for that item)
- **Split Invoice**: Proportional SST for that specific invoice (e.g., if total SST is 20.00 and split into 2 invoices, each gets 10.00)

### `ori_invoice_sst` Column
- **Purpose**: Total SST across all split invoices for the same `account_item_id`
- **Single Invoice**: Same as `sst` (the SST for that item)
- **Split Invoice**: Total SST before splitting (e.g., 20.00 total, stored in all split invoices)

---

## When to Update `sst` Column

### ✅ Update `sst` When:

1. **User manually edits SST** (via Edit Invoice SST modal)
   - **Single Invoice**: Update `sst` directly
   - **Split Invoice**: Distribute new total SST proportionally to each split invoice's `sst`

2. **Invoice amount changes** (via Edit Invoice Amount)
   - **Single Invoice**: Recalculate `sst = amount * sst_rate`
   - **Split Invoice**: Recalculate total SST from total amount, then distribute proportionally

3. **Invoice is split** (creating new split invoices)
   - Calculate total SST from total amount
   - Distribute proportionally to each split invoice's `sst`

4. **Invoice is merged** (removing split, becoming single invoice)
   - Sum all `sst` values from split invoices
   - Set as single `sst` value

5. **Transfer fee details update** (if invoice is linked to transfer fees)
   - Update `sst` to match transfer fee `sst_amount` (proportionally distributed)

---

## When to Update `ori_invoice_sst` Column

### ✅ Update `ori_invoice_sst` When:

1. **User manually edits SST** (via Edit Invoice SST modal)
   - **Single Invoice**: Update `ori_invoice_sst` to match new `sst`
   - **Split Invoice**: Update `ori_invoice_sst` for ALL invoices with same `account_item_id` to the new total SST

2. **Invoice amount changes** (via Edit Invoice Amount)
   - **Single Invoice**: Recalculate `ori_invoice_sst = new_amount * sst_rate`
   - **Split Invoice**: Calculate new total SST from new total amount, update `ori_invoice_sst` for ALL invoices with same `account_item_id`

3. **Invoice is split** (creating new split invoices)
   - Calculate total SST from total amount
   - Set `ori_invoice_sst` to this total for ALL split invoices with same `account_item_id`

4. **Invoice is merged** (removing split, becoming single invoice)
   - Set `ori_invoice_sst` to match the single `sst` value

5. **Initial creation** (when invoice is first created)
   - Calculate `ori_invoice_sst = ori_invoice_amt * sst_rate`

---

## Update Scenarios

### Scenario 1: User Edits SST for Single Invoice
```
Action: User changes SST from 8.00 to 10.00
Update:
- sst: 8.00 → 10.00
- ori_invoice_sst: 8.00 → 10.00
```

### Scenario 2: User Edits SST for Split Invoice
```
Action: User changes total SST from 20.00 to 24.00 (for CKHT 502 split into 2 invoices)
Update:
- ori_invoice_sst: 20.00 → 24.00 (for ALL invoices with same account_item_id)
- Invoice 1 sst: 10.00 → 12.00 (proportional: 24.00 * 75/150)
- Invoice 2 sst: 10.00 → 12.00 (proportional: 24.00 * 75/150)
```

### Scenario 3: User Edits Invoice Amount for Single Invoice
```
Action: User changes amount from 100.00 to 120.00 (SST rate: 8%)
Update:
- ori_invoice_amt: 100.00 → 120.00
- ori_invoice_sst: 8.00 → 9.60 (120.00 * 0.08)
- sst: 8.00 → 9.60
```

### Scenario 4: User Edits Invoice Amount for Split Invoice
```
Action: User changes total amount from 150.00 to 180.00 (split into 2 invoices, SST rate: 8%)
Update:
- ori_invoice_amt: 150.00 → 180.00 (for ALL invoices)
- ori_invoice_sst: 12.00 → 14.40 (180.00 * 0.08, for ALL invoices)
- Invoice 1 sst: 6.00 → 7.20 (proportional: 14.40 * 90/180)
- Invoice 2 sst: 6.00 → 7.20 (proportional: 14.40 * 90/180)
```

### Scenario 5: Invoice Split (Creating Split Invoices)
```
Action: Split invoice with amount 200.00, SST 16.00 into 2 invoices
Update:
- ori_invoice_amt: 200.00 (for ALL invoices)
- ori_invoice_sst: 16.00 (for ALL invoices)
- Invoice 1 sst: 8.00 (proportional: 16.00 * 100/200)
- Invoice 2 sst: 8.00 (proportional: 16.00 * 100/200)
```

### Scenario 6: Invoice Merge (Removing Split)
```
Action: Merge 2 split invoices back to single invoice
Update:
- ori_invoice_amt: 200.00 (sum of both)
- ori_invoice_sst: 16.00 (sum of both SST: 8.00 + 8.00)
- sst: 16.00 (same as ori_invoice_sst for single invoice)
```

---

## Code Locations

### Update `sst` Column:
1. **`CaseController::updateInvoiceSST()`** - When user edits SST
2. **`CaseController::updateInvoiceValue()`** - When user edits amount (triggers recalculation)
3. **`InvoiceController::update()`** - When invoice details are updated
4. **`InvoiceController::calculateInvoiceAmountsFromDetails()`** - During invoice recalculation
5. **`InvoiceController::removeInvoice()`** - When invoice is removed (recalculates remaining)

### Update `ori_invoice_sst` Column:
1. **`CaseController::updateInvoiceSST()`** - When user edits SST
2. **`CaseController::updateInvoiceValue()`** - When user edits amount (calculates new total SST)
3. **`CaseController::loadCaseBill()`** - When invoice is created/split (sets initial value)
4. **`InvoiceController::update()`** - When invoice details are updated
5. **`EInvoiceContoller::createNewTransferFeeV3()`** - When transfer fee is created

---

## Key Rules

1. **For Split Invoices:**
   - `ori_invoice_sst` = Total SST across all split invoices (same value for all invoices with same `account_item_id`)
   - `sst` = Proportional SST for each individual invoice
   - Sum of all `sst` values = `ori_invoice_sst`

2. **For Single Invoices:**
   - `ori_invoice_sst` = `sst` (they are the same)

3. **Manual SST Edits:**
   - Always preserve manual SST values
   - For split invoices, distribute remaining SST proportionally among non-manual items

4. **Automatic Recalculation:**
   - Only happens when amount changes or invoice is split/merged
   - Manual SST values are preserved during recalculation

---

## Example: Split Invoice with Manual SST

```
Account Item: CKHT 502
Total Amount: 150.00 (split into Invoice 1: 75.00, Invoice 2: 75.00)
SST Rate: 8%
Total SST: 12.00

Initial State:
- Invoice 1: sst = 6.00, ori_invoice_sst = 12.00
- Invoice 2: sst = 6.00, ori_invoice_sst = 12.00

User manually edits Invoice 1 SST to 7.00:
- Invoice 1: sst = 7.00 (manual), ori_invoice_sst = 12.00
- Invoice 2: sst = 5.00 (recalculated: 12.00 - 7.00), ori_invoice_sst = 12.00

User edits total SST to 20.00:
- Invoice 1: sst = 7.00 (preserved manual), ori_invoice_sst = 20.00
- Invoice 2: sst = 13.00 (recalculated: 20.00 - 7.00), ori_invoice_sst = 20.00
```

---

## Summary Table

| Scenario | Update `sst`? | Update `ori_invoice_sst`? | Notes |
|----------|---------------|---------------------------|-------|
| User edits SST (single invoice) | ✅ Yes | ✅ Yes | Both set to same value |
| User edits SST (split invoice) | ✅ Yes | ✅ Yes | Distribute `sst` proportionally, update `ori_invoice_sst` for all |
| User edits amount (single invoice) | ✅ Yes | ✅ Yes | Recalculate both from new amount |
| User edits amount (split invoice) | ✅ Yes | ✅ Yes | Recalculate total SST, distribute `sst`, update `ori_invoice_sst` for all |
| Invoice split | ✅ Yes | ✅ Yes | Set `ori_invoice_sst` to total, distribute `sst` |
| Invoice merge | ✅ Yes | ✅ Yes | Sum `sst` values, set both to sum |
| Transfer fee update | ✅ Yes | ❌ No | Update `sst` to match transfer fee |
| Initial invoice creation | ✅ Yes | ✅ Yes | Calculate from amount × SST rate |






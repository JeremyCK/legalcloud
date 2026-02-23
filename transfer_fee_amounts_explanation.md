# Transfer Fee Edit Page - Total Amount and Collected Amount Sources

## Overview
For Transfer Fee Record ID: 516, here's where the **Total amt** and **Collected amt** values come from:

---

## 1. TOTAL AMT (bill_total_amt_divided)

### Source Priority:
1. **Primary Source**: `loan_case_invoice_main.amount` (invoice_amount)
   - This is the stored invoice total amount in the database
   - Used directly if available and > 0

2. **Fallback Calculation**: If invoice_amount is NULL or 0
   - Calculated from `loan_case_invoice_details` table
   - Formula: `(cat1 + cat1×sst_rate) + cat2 + cat3 + (cat4 + cat4×sst_rate)`
   - Where:
     - `cat1` = Sum of amounts with account_cat_id = 1
     - `cat2` = Sum of amounts with account_cat_id = 2
     - `cat3` = Sum of amounts with account_cat_id = 3
     - `cat4` = Sum of amounts with account_cat_id = 4
     - `sst_rate` = SST rate from `loan_case_bill_main.sst_rate`

### Code Location:
- **Controller**: `app/Http/Controllers/TransferFeeV3Controller.php` (lines 1014-1039)
- **View**: `resources/views/dashboard/transfer-fee-v3/edit.blade.php` (line 439)

### Example for Record 516:
- Invoice A20000532: Total amt = 68,000.00 (from invoice_amount)
- Invoice A20000531: Total amt = 1,623.00 (from invoice_amount)

---

## 2. COLLECTED AMT (bill_collected_amt_divided)

### Source:
- **Source Table**: `loan_case_bill_main.collected_amt` (bill_collected_amt)
- **Formula**: `bill_collected_amt / invoice_count`
- **invoice_count**: Number of invoices linked to the same bill (`loan_case_main_bill_id`)

### Calculation Logic:
1. Get the bill's collected amount from `loan_case_bill_main.collected_amt`
2. Count how many invoices are linked to this bill:
   ```php
   $invoiceCount = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_id)
       ->where('status', 1)
       ->count();
   ```
3. Divide the bill collected amount equally: `collected_amt / invoice_count`

### Code Location:
- **Controller**: `app/Http/Controllers/TransferFeeV3Controller.php` (lines 1005-1065)
- **View**: `resources/views/dashboard/transfer-fee-v3/edit.blade.php` (line 452)

### Example for Record 516:
- Invoice A20000532: Collected amt = 68,000.00 (bill_collected_amt = 68,000.00, invoice_count = 1)
- Invoice A20000531: Collected amt = 58,195.00 (bill_collected_amt = 58,195.00, invoice_count = 1)
- Invoice A20000527: Collected amt = 7,500.00 (bill_collected_amt = 7,500.00, invoice_count = 1)

---

## 3. Why They Can Differ

### Total amt vs Collected amt:
- **Total amt**: Represents the **individual invoice's total amount** (what the invoice is worth)
- **Collected amt**: Represents the **bill's collected amount divided by invoice count** (what was actually collected, split equally)

### When They Match:
- When `invoice_count = 1` (one invoice per bill)
- When the bill's collected amount equals the invoice amount

### When They Differ:
- When a bill has multiple invoices (`invoice_count > 1`)
- When the bill's collected amount differs from individual invoice amounts
- Example: Invoice A20000531 shows Total amt = 1,623.00 but Collected amt = 58,195.00

---

## 4. Database Tables Involved

### Primary Tables:
1. **transfer_fee_main**: Main transfer fee record
2. **transfer_fee_details**: Individual invoice records in the transfer
3. **loan_case_invoice_main**: Invoice records (source of invoice_amount)
4. **loan_case_bill_main**: Bill records (source of collected_amt)
5. **loan_case_invoice_details**: Invoice line items (for fallback calculation)

### Key Fields:
- `loan_case_invoice_main.amount` → Used for Total amt
- `loan_case_bill_main.collected_amt` → Used for Collected amt
- `loan_case_bill_main.total_amt` → Bill total (not directly used in display)
- `loan_case_invoice_main.loan_case_main_bill_id` → Links invoice to bill

---

## 5. Summary for Record 516

| Field | Source | Calculation |
|-------|--------|-------------|
| **Total amt** | `loan_case_invoice_main.amount` | Direct value or calculated from invoice details |
| **Collected amt** | `loan_case_bill_main.collected_amt` | `bill_collected_amt / invoice_count` |

### Totals:
- **Sum of Total amt**: 120,559.36
- **Sum of Collected amt**: 180,436.36

---

## 6. Code References

### Controller Method:
`TransferFeeV3Controller::transferFeeEditV3($id)` (line 920)

### Key Calculation Lines:
- Total amt calculation: Lines 1014-1039
- Collected amt calculation: Lines 1062-1065
- Invoice count: Lines 1006-1009

### View Display:
- Total amt display: `edit.blade.php` line 439
- Collected amt display: `edit.blade.php` line 452
- Footer totals: `edit.blade.php` lines 624, 627

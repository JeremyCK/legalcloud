# How Reimbursement SST Records Are Retrieved in the Listing

## Data Flow Overview

The reimbursement SST data flows from the database through the controller to the view. Here's how it works:

## Step 1: Controller Query (sstEditV2 method)

**File:** `app/Http/Controllers/SSTV2Controller.php` (lines 133-155)

```php
$SSTDetails = DB::table('sst_details as sd')
    ->leftJoin('loan_case_invoice_main as im', 'im.id', '=', 'sd.loan_case_invoice_main_id')
    ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
    ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
    ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
    ->where('sd.sst_main_id', $id)
    ->select(
        'sd.*',
        'im.invoice_no',
        'im.Invoice_date as invoice_date',
        'im.amount as total_amount',
        'im.pfee1_inv as pfee1',
        'im.pfee2_inv as pfee2',
        'im.reimbursement_sst',                    // ← Reimbursement SST field
        'im.transferred_reimbursement_sst_amt',    // ← Transferred amount field
        'b.collected_amt as collected_amount',
        'b.payment_receipt_date as payment_date',
        'l.case_ref_no',
        'l.id as case_id',
        'c.name as client_name'
    )
    ->get();
```

### Key Fields Retrieved:
1. **`im.reimbursement_sst`** - Total reimbursement SST for the invoice
2. **`im.transferred_reimbursement_sst_amt`** - Amount already transferred to other SST records

### Source Tables:
- **`sst_details`** - Links invoices to SST records
- **`loan_case_invoice_main`** - Contains reimbursement SST fields
- **`loan_case_bill_main`** - Bill information
- **`loan_case`** - Case information
- **`client`** - Client information

## Step 2: View Calculation (edit.blade.php)

**File:** `resources/views/dashboard/sst-v2/edit.blade.php` (lines 260-264)

```php
@php
    // Calculate remaining reimbursement SST
    $reimbSst = max(0, ($detail->reimbursement_sst ?? 0) - ($detail->transferred_reimbursement_sst_amt ?? 0));
    $totalSstRow = ($detail->amount ?? 0) + $reimbSst;
@endphp
<td class="text-right">{{ number_format($reimbSst, 2) }}</td>
<td class="text-right">{{ number_format($totalSstRow, 2) }}</td>
```

### Formula:
```
Reimb SST (displayed) = reimbursement_sst - transferred_reimbursement_sst_amt
```

### Logic:
- If `transferred_reimbursement_sst_amt = reimbursement_sst` → Remaining = 0 (shows 0.00)
- If `transferred_reimbursement_sst_amt < reimbursement_sst` → Remaining > 0 (shows value)
- If `transferred_reimbursement_sst_amt = 0` → Remaining = reimbursement_sst (shows full amount)

## Step 3: Total Amount Calculation

**File:** `resources/views/dashboard/sst-v2/edit.blade.php` (lines 76-82)

```php
$totalSst = $SSTDetails->sum('amount') ?? 0;
$totalReimbSst = 0;
foreach($SSTDetails as $detail) {
    $totalReimbSst += max(0, ($detail->reimbursement_sst ?? 0) - ($detail->transferred_reimbursement_sst_amt ?? 0));
}
$grandTotalSst = $totalSst + $totalReimbSst;
```

### Total Calculation:
```
Transfer Total Amount = SUM(SST) + SUM(Remaining Reimb SST)
```

## Data Source: loan_case_invoice_main Table

The reimbursement SST comes from the `loan_case_invoice_main` table:

| Field | Description | Source |
|-------|-------------|--------|
| `reimbursement_sst` | Total reimbursement SST for invoice | Calculated from invoice details (account_cat_id = 4) |
| `transferred_reimbursement_sst_amt` | Amount already transferred | Updated when invoice is added to SST record |

### How reimbursement_sst is Calculated:

1. **From Invoice Details:**
   ```sql
   SELECT SUM(ild.amount) * sst_rate / 100
   FROM loan_case_invoice_details ild
   INNER JOIN account_item ai ON ai.id = ild.account_item_id
   WHERE ild.invoice_main_id = ?
     AND ai.account_cat_id = 4  -- Reimbursement items
     AND ild.status <> 99
   ```

2. **Stored in:** `loan_case_invoice_main.reimbursement_sst`

## Why Reimb SST Shows 0.00

The Reimb SST column shows **remaining** reimbursement SST, not total:

```
Remaining Reimb SST = reimbursement_sst - transferred_reimbursement_sst_amt
```

### Example:

**Invoice A20000408:**
- `reimbursement_sst` = 81.22
- `transferred_reimbursement_sst_amt` = 81.22
- **Remaining Reimb SST = 81.22 - 81.22 = 0.00** ✅ (shows 0.00)

**If reset to 0:**
- `reimbursement_sst` = 81.22
- `transferred_reimbursement_sst_amt` = 0 (reset)
- **Remaining Reimb SST = 81.22 - 0 = 81.22** ✅ (shows 81.22)

## Summary

1. **Controller** retrieves `reimbursement_sst` and `transferred_reimbursement_sst_amt` from `loan_case_invoice_main`
2. **View** calculates remaining: `reimbursement_sst - transferred_reimbursement_sst_amt`
3. **Display** shows remaining reimbursement SST (not total)
4. **Total** includes remaining reimbursement SST in the grand total

## To Show Reimbursement SST in SST 96

If reimbursement SST should be included in SST 96, reset `transferred_reimbursement_sst_amt` to 0:

```sql
UPDATE loan_case_invoice_main im
INNER JOIN sst_details sd ON sd.loan_case_invoice_main_id = im.id
SET im.transferred_reimbursement_sst_amt = 0
WHERE sd.sst_main_id = 96
  AND im.reimbursement_sst > 0;
```

This will make:
- Reimb SST column show values > 0
- Total SST include reimbursement SST
- SST main total include reimbursement SST








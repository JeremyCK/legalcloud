# Split Invoice Fix - File Changes Summary

## Issue
When updating a split invoice (e.g., `/invoice/9933/details`), the changes were not reflecting in the case details page (`/case/7208`). The case details page was only showing data from the first invoice, ignoring other split invoices.

## Root Cause
The `CaseController.php` was only querying the first invoice ID using `->pluck('id')->first()`, which meant it only displayed data from that first invoice, not the combined totals from all split invoices.

## Files Changed

### 1. `app/Http/Controllers/CaseController.php`

**Location**: Around line 7472-7562

#### Changes Made:

**A. Get ALL Invoice IDs (not just first)**
```php
// BEFORE:
$invoice_main_id = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->pluck('id')->first();

// AFTER:
// Get ALL invoice IDs for this bill (handles split invoices)
$invoiceIds = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)
    ->where('status', '<>', 99)
    ->pluck('id')
    ->toArray();

// For backward compatibility, keep first invoice ID
$invoice_main_id = !empty($invoiceIds) ? $invoiceIds[0] : null;

// Check if this is a split invoice
$isSplitInvoice = count($invoiceIds) > 1;
```

**B. Query from ALL Invoices**
```php
// BEFORE:
$QuotationTemplateDetails = DB::table('loan_case_invoice_details AS qd')
    ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
    ->select($selectFields)
    ->where('qd.invoice_main_id', '=', $invoice_main_id)  // Only first invoice
    ->where('qd.status', '=', 1)
    ->where('a.account_cat_id', '=', $category[$i]->id)
    ->get();

// AFTER:
$query = DB::table('loan_case_invoice_details AS qd')
    ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
    ->select($selectFields)
    ->whereIn('qd.invoice_main_id', $invoiceIds)  // ALL invoices
    ->where('qd.status', '=', 1)
    ->where('a.account_cat_id', '=', $category[$i]->id);
```

**C. Group Split Invoice Details**
```php
// NEW CODE: For split invoices, combine details by account_item_id
if ($isSplitInvoice) {
    // Get all details grouped by account_item_id
    $allDetails = $query->get();
    
    // Group by account_item_id - use ori_invoice_amt and ori_invoice_sst
    // (which are the same across all split invoices for same account_item_id)
    $groupedDetails = [];
    foreach ($allDetails as $detail) {
        $key = $detail->account_item_id;
        if (!isset($groupedDetails[$key])) {
            // Use ori_invoice_amt and ori_invoice_sst (total across all split invoices)
            $groupedDetails[$key] = (object)[
                'id' => $detail->id,
                'account_item_id' => $detail->account_item_id,
                'amount' => (float)($detail->ori_invoice_amt ?? 0),  // Total amount
                'quo_amount' => $detail->quo_amount ?? 0,
                'ori_invoice_amt' => (float)($detail->ori_invoice_amt ?? 0),
                'item_remark' => $detail->item_remark ?? '',  // Fixed: was 'remark'
                'quotation_item_id' => $detail->quotation_item_id ?? null,
                'account_name' => $detail->account_name ?? '',
                'account_name_cn' => $detail->account_name_cn ?? '',
                'formula' => $detail->account_formula ?? '',
                'min' => $detail->account_min ?? 0,
                'pfee1_item' => $detail->pfee1_item ?? 0,
                'item_desc' => $detail->item_desc ?? '',
                'sst' => $sstColumnExists ? (float)($detail->ori_invoice_sst ?? 0) : 0,  // Total SST
                'ori_invoice_sst' => (float)($detail->ori_invoice_sst ?? 0)
            ];
        }
    }
    
    // Convert back to array for compatibility
    $QuotationTemplateDetails = array_values($groupedDetails);
} else {
    // Single invoice - use original query
    $QuotationTemplateDetails = $query->get();
}
```

**D. Fixed Split Invoice Check**
```php
// BEFORE:
if ($invoice_main_id) {
    $invoiceCount = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)
        ->where('status', '<>', 99)
        ->count();
    $isSplitInvoice = $invoiceCount > 1;
    // ...
}

// AFTER:
if (!empty($invoiceIds)) {
    if ($isSplitInvoice) {  // Already calculated above
        // ...
    }
}
```

**E. Fixed Property Name Bug**
```php
// Fixed: Changed 'remark' to 'item_remark' to match view expectations
'item_remark' => $detail->item_remark ?? '',  // Was: 'remark' => ...
```

## Key Points

1. **Query All Invoices**: Changed from querying only the first invoice to querying ALL invoices for the bill
2. **Use Total Values**: For split invoices, use `ori_invoice_amt` and `ori_invoice_sst` which represent totals across all split invoices
3. **Group by Account Item**: Combine details by `account_item_id` to avoid duplicates
4. **Property Name Fix**: Fixed `remark` → `item_remark` to match view expectations

## Testing

After these changes:
1. Update a split invoice at `/invoice/9933/details`
2. Check case details at `/case/7208`
3. The invoice tab should now show updated values from all split invoices combined

## Files That Need to be Deployed to Server

- `app/Http/Controllers/CaseController.php` (lines ~7472-7600)

## Related Views (No Changes Needed)

These views will automatically work with the updated data structure:
- `resources/views/dashboard/case/table/tbl-case-invoice-list.blade.php`
- `resources/views/dashboard/case/table/tbl-case-invoice-p.blade.php`
- `resources/views/dashboard/case/tabs/bill/tab-invoice.blade.php`


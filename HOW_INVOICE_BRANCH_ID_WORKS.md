# How Invoice Branch ID is Determined

## Overview
The branch ID for an invoice is determined using a **two-level fallback system**:

1. **Primary Source**: `loan_case_bill_main.invoice_branch_id`
2. **Fallback Source**: `loan_case.branch_id` (if `invoice_branch_id` is NULL)

## Database Structure

```
loan_case_invoice_main (im)
    └── loan_case_main_bill_id → loan_case_bill_main (b)
            ├── invoice_branch_id (PRIMARY - can be NULL)
            └── case_id → loan_case (l)
                    └── branch_id (FALLBACK)
```

## Current Implementation in SSTV2Controller

The branch filtering logic in `SSTV2Controller.php` (lines 342-348) works as follows:

```php
$query->where(function($q) use ($filterBranch) {
    $q->where('b.invoice_branch_id', $filterBranch)  // Check invoice_branch_id first
      ->orWhere(function($subQ) use ($filterBranch) {
          $subQ->whereNull('b.invoice_branch_id')      // If NULL, fallback to case branch
               ->where('l.branch_id', $filterBranch);
      });
});
```

## Logic Flow

```
For each invoice:
    IF loan_case_bill_main.invoice_branch_id IS NOT NULL
        → Use invoice_branch_id as the branch
    ELSE
        → Use loan_case.branch_id as the branch
```

## Why This Design?

1. **Flexibility**: Allows invoices to be assigned to a different branch than the case
2. **Backward Compatibility**: If `invoice_branch_id` is not set, it falls back to the case's branch
3. **Data Integrity**: Ensures every invoice has a branch association

## Example Scenarios

### Scenario 1: Invoice with invoice_branch_id set
- Case branch: 1 (HQ)
- Invoice branch: 4 (Ramakrishnan)
- **Result**: Invoice belongs to branch 4

### Scenario 2: Invoice without invoice_branch_id (NULL)
- Case branch: 4 (Ramakrishnan)
- Invoice branch: NULL
- **Result**: Invoice belongs to branch 4 (from case)

### Scenario 3: Invoice with invoice_branch_id = 0
- Case branch: 4 (Ramakrishnan)
- Invoice branch: 0
- **Result**: This might be treated as NULL, falling back to case branch 4

## Diagnostic Query

To check which invoices use which branch source:

```sql
SELECT 
    im.id as invoice_id,
    im.invoice_no,
    b.invoice_branch_id,
    l.branch_id as case_branch_id,
    CASE 
        WHEN b.invoice_branch_id IS NOT NULL THEN b.invoice_branch_id
        ELSE l.branch_id
    END as effective_branch_id,
    CASE 
        WHEN b.invoice_branch_id IS NOT NULL THEN 'invoice_branch_id'
        ELSE 'case_branch_id (fallback)'
    END as branch_source
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN loan_case l ON l.id = b.case_id
WHERE im.status <> 99
LIMIT 20;
```

## Common Issues

1. **Missing invoice_branch_id**: If many invoices have NULL `invoice_branch_id`, they all fall back to case branch
2. **Incorrect invoice_branch_id**: If set incorrectly, invoices may appear in wrong branch
3. **Case branch mismatch**: If case branch is wrong, all invoices without `invoice_branch_id` will be wrong

## Recommendations

1. **Always set invoice_branch_id** when creating invoices to avoid ambiguity
2. **Validate branch consistency** between invoice and case
3. **Use diagnostic queries** to identify invoices using fallback logic












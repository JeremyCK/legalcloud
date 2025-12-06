-- Sync bln_invoice from loan_case_bill_main to loan_case_invoice_main
-- This ensures invoice-level bln_invoice matches bill-level bln_invoice

-- Check current mismatch
SELECT 
    'Mismatch Count' as description,
    COUNT(*) as count
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE im.status <> 99
  AND bm.status <> 99
  AND im.bln_invoice != bm.bln_invoice;

-- Show sample mismatches
SELECT 
    im.id as invoice_id,
    im.invoice_no,
    im.bln_invoice as invoice_bln_invoice,
    bm.id as bill_id,
    bm.bln_invoice as bill_bln_invoice,
    bm.invoice_branch_id
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE im.status <> 99
  AND bm.status <> 99
  AND im.bln_invoice != bm.bln_invoice
LIMIT 20;

-- Update: Sync invoice bln_invoice to match bill bln_invoice
UPDATE loan_case_invoice_main im
INNER JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
SET im.bln_invoice = bm.bln_invoice
WHERE im.status <> 99
  AND bm.status <> 99
  AND im.bln_invoice != bm.bln_invoice;

-- Verify after update
SELECT 
    'After Sync - Mismatch Count' as description,
    COUNT(*) as count
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE im.status <> 99
  AND bm.status <> 99
  AND im.bln_invoice != bm.bln_invoice;





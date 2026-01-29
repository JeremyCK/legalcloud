-- Sync ALL mismatched bln_invoice values from bill to invoice
-- This will fix R20000214 and any other invoices with the same issue
-- Run this SQL directly on the server database

-- First, check how many mismatches exist
SELECT 
    COUNT(*) as mismatch_count
FROM loan_case_invoice_main im
INNER JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE im.status <> 99
  AND bm.status <> 99
  AND im.bln_invoice != bm.bln_invoice;

-- Show sample mismatches (optional - to see what will be fixed)
SELECT 
    im.id as invoice_id,
    im.invoice_no,
    im.bln_invoice as invoice_bln_invoice,
    bm.id as bill_id,
    bm.bln_invoice as bill_bln_invoice
FROM loan_case_invoice_main im
INNER JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
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
    COUNT(*) as remaining_mismatch_count
FROM loan_case_invoice_main im
INNER JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE im.status <> 99
  AND bm.status <> 99
  AND im.bln_invoice != bm.bln_invoice;

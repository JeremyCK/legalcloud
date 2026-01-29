-- Fix bln_invoice for invoice R20000214
-- Run this SQL directly on the server database

UPDATE loan_case_invoice_main im
INNER JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
SET im.bln_invoice = bm.bln_invoice
WHERE im.invoice_no = 'R20000214'
  AND im.status <> 99
  AND bm.status <> 99
  AND im.bln_invoice != bm.bln_invoice;

-- Verify the fix
SELECT 
    im.id as invoice_id,
    im.invoice_no,
    im.bln_invoice as invoice_bln_invoice,
    bm.id as bill_id,
    bm.bln_invoice as bill_bln_invoice
FROM loan_case_invoice_main im
INNER JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE im.invoice_no = 'R20000214';

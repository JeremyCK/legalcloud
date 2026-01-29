-- SQL Query to check why invoice R20000214 is not appearing in SST V2 search
-- Replace {SST_MAIN_ID} with the actual SST main ID (e.g., 96)

-- First, find the invoice
SELECT 
    im.id as invoice_id,
    im.invoice_no,
    im.status,
    im.loan_case_main_bill_id,
    im.bln_invoice,
    im.bln_sst,
    im.transferred_sst_amt,
    im.sst_inv,
    im.reimbursement_sst,
    b.id as bill_id,
    b.bln_invoice as bill_bln_invoice,
    b.bln_sst as bill_bln_sst,
    b.invoice_branch_id,
    l.id as case_id,
    l.case_ref_no,
    l.branch_id as case_branch_id,
    c.name as client_name
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN loan_case l ON l.id = b.case_id
LEFT JOIN client c ON c.id = l.customer_id
WHERE im.invoice_no = 'R20000214';

-- Check if invoice is already in SST record 96
SELECT 
    sd.id as sst_detail_id,
    sd.sst_main_id,
    sd.loan_case_invoice_main_id,
    sd.amount,
    im.invoice_no
FROM sst_details sd
JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
  AND im.invoice_no = 'R20000214';

-- Check all invoices in SST record 96 (to see transfer_list)
SELECT 
    sd.loan_case_invoice_main_id,
    im.invoice_no
FROM sst_details sd
JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
ORDER BY im.invoice_no;

-- Simulate the actual query conditions
-- This should return the invoice if all conditions are met
SELECT 
    im.id,
    im.invoice_no,
    im.status,
    im.bln_invoice,
    im.bln_sst,
    im.transferred_sst_amt,
    b.bln_invoice as bill_bln_invoice,
    b.bln_sst as bill_bln_sst,
    CASE 
        WHEN b.invoice_branch_id IS NOT NULL AND b.invoice_branch_id <> 0 THEN b.invoice_branch_id
        ELSE l.branch_id
    END as effective_branch
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN loan_case l ON l.id = b.case_id
WHERE im.invoice_no = 'R20000214'
  AND im.status <> 99
  AND im.loan_case_main_bill_id IS NOT NULL
  AND im.loan_case_main_bill_id > 0
  AND b.bln_invoice = 1
  AND im.bln_invoice = 1
  AND b.bln_sst = 0
  AND im.bln_sst = 0;

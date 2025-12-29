-- SQL Query to Check SST Record ID 96
-- Run this query in your database to diagnose issues

-- 1. Check SST Main Record
SELECT 
    'SST Main Record' as section,
    id,
    payment_date,
    transaction_id,
    amount as stored_amount,
    status,
    is_recon,
    created_at,
    updated_at
FROM sst_main
WHERE id = 96;

-- 2. Check SST Details with Invoice Information
SELECT 
    'SST Details' as section,
    sd.id as sst_detail_id,
    sd.sst_main_id,
    sd.loan_case_invoice_main_id,
    sd.amount as sst_amount,
    im.invoice_no,
    im.sst_inv as invoice_sst,
    im.reimbursement_sst,
    im.transferred_reimbursement_sst_amt,
    (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0)) as remaining_reimb_sst,
    (sd.amount + GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as row_total
FROM sst_details sd
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
ORDER BY sd.id;

-- 3. Calculate Total Summary
SELECT 
    'Summary' as section,
    COUNT(*) as total_invoices,
    SUM(sd.amount) as total_sst,
    SUM(GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as total_remaining_reimb_sst,
    SUM(sd.amount + GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as calculated_grand_total,
    sm.amount as stored_amount,
    (sm.amount - SUM(sd.amount + GREATEST(0, (im.reimbursement_sst - COALESCE(im.transferred_reimbursement_sst_amt, 0))))) as difference
FROM sst_details sd
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
LEFT JOIN sst_main sm ON sm.id = sd.sst_main_id
WHERE sd.sst_main_id = 96
GROUP BY sm.amount;

-- 4. Check for Missing Invoice References
SELECT 
    'Missing References' as section,
    sd.id as sst_detail_id,
    sd.loan_case_invoice_main_id,
    CASE 
        WHEN im.id IS NULL THEN 'Invoice not found'
        ELSE 'OK'
    END as status
FROM sst_details sd
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
  AND im.id IS NULL;












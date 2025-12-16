-- Check Reimbursement SST for SST Record 96
-- This will show why Reimb SST is showing 0.00

SELECT 
    'Reimbursement SST Analysis' as check_type,
    sd.id as sst_detail_id,
    sd.loan_case_invoice_main_id,
    im.invoice_no,
    -- Reimbursement SST fields
    COALESCE(im.reimbursement_sst, 0) as reimbursement_sst,
    COALESCE(im.transferred_reimbursement_sst_amt, 0) as transferred_reimbursement_sst_amt,
    GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) as remaining_reimb_sst,
    -- Status
    CASE 
        WHEN COALESCE(im.reimbursement_sst, 0) = 0 THEN 'No reimbursement SST in invoice'
        WHEN COALESCE(im.transferred_reimbursement_sst_amt, 0) >= COALESCE(im.reimbursement_sst, 0) THEN 'Already fully transferred'
        ELSE 'Has remaining reimbursement SST'
    END as status,
    -- Additional info
    l.case_ref_no,
    c.name as client_name
FROM sst_details sd
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN loan_case l ON l.id = b.case_id
LEFT JOIN client c ON c.id = l.customer_id
WHERE sd.sst_main_id = 96
ORDER BY sd.id;

-- Summary: Count invoices with reimbursement SST
SELECT 
    'Summary' as check_type,
    COUNT(*) as total_invoices,
    SUM(CASE WHEN COALESCE(im.reimbursement_sst, 0) > 0 THEN 1 ELSE 0 END) as invoices_with_reimb_sst,
    SUM(CASE WHEN COALESCE(im.reimbursement_sst, 0) = 0 THEN 1 ELSE 0 END) as invoices_without_reimb_sst,
    SUM(CASE WHEN COALESCE(im.transferred_reimbursement_sst_amt, 0) >= COALESCE(im.reimbursement_sst, 0) 
             AND COALESCE(im.reimbursement_sst, 0) > 0 THEN 1 ELSE 0 END) as invoices_fully_transferred,
    SUM(GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as total_remaining_reimb_sst
FROM sst_details sd
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96;

-- Check if reimbursement_sst needs to be calculated from invoice details
-- Reimbursement SST should be calculated from invoice details where account_cat_id = 4
SELECT 
    'Invoice Details Check' as check_type,
    im.id as invoice_id,
    im.invoice_no,
    im.reimbursement_sst as stored_reimbursement_sst,
    COALESCE(reimb_details.total_reimbursement, 0) as calculated_reimbursement_amount,
    COALESCE(reimb_details.total_reimbursement, 0) * COALESCE(b.sst_rate, 0.06) as calculated_reimbursement_sst,
    CASE 
        WHEN im.reimbursement_sst IS NULL OR im.reimbursement_sst = 0 THEN 'MISSING - needs calculation'
        WHEN ABS(im.reimbursement_sst - (COALESCE(reimb_details.total_reimbursement, 0) * COALESCE(b.sst_rate, 0.06))) > 0.01 THEN 'MISMATCH'
        ELSE 'OK'
    END as status
FROM sst_details sd
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN (
    SELECT 
        id.invoice_main_id,
        SUM(id.amount) as total_reimbursement
    FROM loan_case_invoice_details id
    INNER JOIN account_item ai ON id.account_item_id = ai.id
    WHERE ai.account_cat_id = 4
    GROUP BY id.invoice_main_id
) reimb_details ON im.id = reimb_details.invoice_main_id
WHERE sd.sst_main_id = 96
ORDER BY im.id;









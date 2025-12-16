-- Check which SST record the reimbursement SST was transferred to
-- This will help determine if we should reset transferred_reimbursement_sst_amt

SELECT 
    'Check Transfer History' as section,
    im.invoice_no,
    im.reimbursement_sst,
    im.transferred_reimbursement_sst_amt,
    -- Check if this invoice is in SST 96
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM sst_details sd2 
            WHERE sd2.loan_case_invoice_main_id = im.id 
            AND sd2.sst_main_id = 96
        ) THEN '✅ In SST 96'
        ELSE '❌ NOT in SST 96'
    END as in_sst_96,
    -- Check which other SST records contain this invoice
    GROUP_CONCAT(DISTINCT sd.sst_main_id ORDER BY sd.sst_main_id) as other_sst_records,
    -- Check if reimbursement SST was transferred when invoice was added to SST 96
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM sst_details sd2 
            WHERE sd2.loan_case_invoice_main_id = im.id 
            AND sd2.sst_main_id = 96
        ) AND im.transferred_reimbursement_sst_amt >= im.reimbursement_sst
        THEN '⚠️ Reimbursement SST was transferred (possibly to SST 96 or another record)'
        ELSE '✅ Reimbursement SST not yet transferred'
    END as transfer_status
FROM sst_details sd_current
INNER JOIN loan_case_invoice_main im ON im.id = sd_current.loan_case_invoice_main_id
LEFT JOIN sst_details sd ON sd.loan_case_invoice_main_id = im.id AND sd.sst_main_id != 96
WHERE sd_current.sst_main_id = 96
GROUP BY im.id, im.invoice_no, im.reimbursement_sst, im.transferred_reimbursement_sst_amt
ORDER BY im.invoice_no
LIMIT 10;

-- Check if reimbursement SST should be included in SST 96
-- If invoices are in SST 96, their reimbursement SST should also be included
SELECT 
    'Summary' as section,
    COUNT(*) as total_invoices,
    SUM(CASE WHEN im.transferred_reimbursement_sst_amt >= im.reimbursement_sst THEN 1 ELSE 0 END) as invoices_with_transferred_reimb,
    SUM(im.reimbursement_sst) as total_reimbursement_sst,
    SUM(im.transferred_reimbursement_sst_amt) as total_transferred_reimb_sst,
    SUM(GREATEST(0, (im.reimbursement_sst - im.transferred_reimbursement_sst_amt))) as total_remaining_reimb_sst,
    CASE 
        WHEN SUM(im.transferred_reimbursement_sst_amt) >= SUM(im.reimbursement_sst) 
        THEN '⚠️ All reimbursement SST already transferred - need to reset if should be in SST 96'
        ELSE '✅ Some reimbursement SST remaining'
    END as status
FROM sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96;









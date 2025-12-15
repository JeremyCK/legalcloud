-- Comprehensive diagnostic query to check why invoices aren't appearing in SST v2 search
-- This checks ALL criteria from the controller

SELECT 
    im.id as invoice_id,
    im.invoice_no,
    im.status as invoice_status,
    im.loan_case_main_bill_id,
    im.bln_invoice as im_bln_invoice,
    im.bln_sst as im_bln_sst,
    im.sst_inv,
    im.transferred_sst_amt,
    im.reimbursement_sst,
    im.transferred_reimbursement_sst_amt,
    bm.id as bill_id,
    bm.bln_invoice as bm_bln_invoice,
    bm.bln_sst as bm_bln_sst,
    bm.invoice_branch_id,
    l.branch_id as case_branch_id,
    -- Check each criteria
    CASE WHEN im.status <> 99 THEN 'PASS' ELSE 'FAIL' END as status_check,
    CASE WHEN im.loan_case_main_bill_id IS NOT NULL AND im.loan_case_main_bill_id > 0 THEN 'PASS' ELSE 'FAIL' END as bill_id_check,
    CASE WHEN bm.bln_invoice = 1 THEN 'PASS' ELSE 'FAIL' END as bm_bln_invoice_check,
    CASE WHEN im.bln_invoice = 1 THEN 'PASS' ELSE 'FAIL' END as im_bln_invoice_check,
    CASE WHEN bm.bln_sst = 0 THEN 'PASS' ELSE 'FAIL' END as bm_bln_sst_check,
    CASE WHEN im.sst_inv > 0 THEN 'PASS' ELSE 'FAIL' END as sst_inv_check,
    CASE WHEN im.bln_sst = 0 THEN 'PASS' ELSE 'FAIL' END as im_bln_sst_check,
    -- Calculate remaining SST
    (im.sst_inv - COALESCE(im.transferred_sst_amt, 0)) + 
    (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)) as remaining_sst,
    CASE WHEN ((im.sst_inv - COALESCE(im.transferred_sst_amt, 0)) + 
               (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) > 0 
         THEN 'PASS' ELSE 'FAIL' END as remaining_sst_check,
    -- Branch check
    CASE 
        WHEN bm.invoice_branch_id = 4 THEN 'PASS (invoice_branch_id)'
        WHEN bm.invoice_branch_id IS NULL AND l.branch_id = 4 THEN 'PASS (case_branch_id fallback)'
        ELSE 'FAIL'
    END as branch_check,
    -- Overall result
    CASE 
        WHEN im.status <> 99
         AND im.loan_case_main_bill_id IS NOT NULL 
         AND im.loan_case_main_bill_id > 0
         AND bm.bln_invoice = 1
         AND im.bln_invoice = 1
         AND bm.bln_sst = 0
         AND im.sst_inv > 0
         AND im.bln_sst = 0
         AND ((im.sst_inv - COALESCE(im.transferred_sst_amt, 0)) + 
              (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) > 0
         AND (bm.invoice_branch_id = 4 OR (bm.invoice_branch_id IS NULL AND l.branch_id = 4))
        THEN 'SHOULD APPEAR'
        ELSE 'FILTERED OUT'
    END as final_result
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
LEFT JOIN loan_case l ON l.id = bm.case_id
WHERE bm.invoice_branch_id = 4
  AND im.bln_sst = 0
ORDER BY im.id DESC
LIMIT 20;

-- Summary: Count how many pass each check
SELECT 
    'Total invoices (branch 4, bln_sst=0)' as check_name,
    COUNT(*) as count
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE bm.invoice_branch_id = 4
  AND im.bln_sst = 0

UNION ALL

SELECT 
    'Pass: status <> 99' as check_name,
    COUNT(*) as count
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE bm.invoice_branch_id = 4
  AND im.bln_sst = 0
  AND im.status <> 99

UNION ALL

SELECT 
    'Pass: bill_id valid' as check_name,
    COUNT(*) as count
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE bm.invoice_branch_id = 4
  AND im.bln_sst = 0
  AND im.status <> 99
  AND im.loan_case_main_bill_id IS NOT NULL 
  AND im.loan_case_main_bill_id > 0

UNION ALL

SELECT 
    'Pass: bm.bln_invoice = 1' as check_name,
    COUNT(*) as count
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE bm.invoice_branch_id = 4
  AND im.bln_sst = 0
  AND im.status <> 99
  AND im.loan_case_main_bill_id IS NOT NULL 
  AND im.loan_case_main_bill_id > 0
  AND bm.bln_invoice = 1

UNION ALL

SELECT 
    'Pass: im.bln_invoice = 1' as check_name,
    COUNT(*) as count
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE bm.invoice_branch_id = 4
  AND im.bln_sst = 0
  AND im.status <> 99
  AND im.loan_case_main_bill_id IS NOT NULL 
  AND im.loan_case_main_bill_id > 0
  AND bm.bln_invoice = 1
  AND im.bln_invoice = 1

UNION ALL

SELECT 
    'Pass: bm.bln_sst = 0' as check_name,
    COUNT(*) as count
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE bm.invoice_branch_id = 4
  AND im.bln_sst = 0
  AND im.status <> 99
  AND im.loan_case_main_bill_id IS NOT NULL 
  AND im.loan_case_main_bill_id > 0
  AND bm.bln_invoice = 1
  AND im.bln_invoice = 1
  AND bm.bln_sst = 0

UNION ALL

SELECT 
    'Pass: im.sst_inv > 0' as check_name,
    COUNT(*) as count
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE bm.invoice_branch_id = 4
  AND im.bln_sst = 0
  AND im.status <> 99
  AND im.loan_case_main_bill_id IS NOT NULL 
  AND im.loan_case_main_bill_id > 0
  AND bm.bln_invoice = 1
  AND im.bln_invoice = 1
  AND bm.bln_sst = 0
  AND im.sst_inv > 0

UNION ALL

SELECT 
    'Pass: im.bln_sst = 0' as check_name,
    COUNT(*) as count
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE bm.invoice_branch_id = 4
  AND im.bln_sst = 0
  AND im.status <> 99
  AND im.loan_case_main_bill_id IS NOT NULL 
  AND im.loan_case_main_bill_id > 0
  AND bm.bln_invoice = 1
  AND im.bln_invoice = 1
  AND bm.bln_sst = 0
  AND im.sst_inv > 0
  AND im.bln_sst = 0

UNION ALL

SELECT 
    'Pass: remaining_sst > 0' as check_name,
    COUNT(*) as count
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE bm.invoice_branch_id = 4
  AND im.bln_sst = 0
  AND im.status <> 99
  AND im.loan_case_main_bill_id IS NOT NULL 
  AND im.loan_case_main_bill_id > 0
  AND bm.bln_invoice = 1
  AND im.bln_invoice = 1
  AND bm.bln_sst = 0
  AND im.sst_inv > 0
  AND im.bln_sst = 0
  AND ((im.sst_inv - COALESCE(im.transferred_sst_amt, 0)) + 
       (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) > 0;








-- Diagnostic SQL to Investigate Why Ramakrishnan Branch Invoices Don't Appear
-- Run these queries to understand the issue

-- 1. Check if Ramakrishnan branch exists and its ID
SELECT 
    'Branch Info' as section,
    id,
    name,
    status,
    short_code
FROM branch
WHERE name LIKE '%Ramakrishnan%' OR name LIKE '%rama%' OR id = 4;

-- 2. Check invoices from Ramakrishnan branch (assuming branch_id = 4)
SELECT 
    'Ramakrishnan Invoices - All' as section,
    COUNT(*) as total_invoices,
    COUNT(CASE WHEN im.bln_sst = 0 THEN 1 END) as not_transferred_sst,
    COUNT(CASE WHEN im.sst_inv > 0 THEN 1 END) as has_sst_amount,
    COUNT(CASE WHEN b.bln_sst = 0 THEN 1 END) as bill_not_transferred_sst,
    COUNT(CASE WHEN b.invoice_branch_id = 4 THEN 1 END) as invoice_branch_id_4,
    COUNT(CASE WHEN b.invoice_branch_id IS NULL THEN 1 END) as invoice_branch_id_null,
    COUNT(CASE WHEN l.branch_id = 4 THEN 1 END) as case_branch_id_4
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN loan_case l ON l.id = b.case_id
WHERE (b.invoice_branch_id = 4 OR l.branch_id = 4)
  AND im.status <> 99
  AND im.loan_case_main_bill_id > 0;

-- 3. Check invoices that SHOULD appear in SST search (meeting all criteria)
SELECT 
    'Invoices Meeting SST Search Criteria' as section,
    COUNT(*) as total_eligible,
    COUNT(CASE WHEN b.invoice_branch_id = 4 THEN 1 END) as invoice_branch_id_4,
    COUNT(CASE WHEN b.invoice_branch_id IS NULL AND l.branch_id = 4 THEN 1 END) as null_branch_but_case_4,
    COUNT(CASE WHEN b.invoice_branch_id IS NULL THEN 1 END) as invoice_branch_id_null,
    COUNT(CASE WHEN l.branch_id = 4 THEN 1 END) as case_branch_id_4
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN loan_case l ON l.id = b.case_id
WHERE im.status <> 99
  AND im.loan_case_main_bill_id > 0
  AND im.loan_case_main_bill_id IS NOT NULL
  AND b.bln_invoice = 1
  AND b.bln_sst = 0
  AND im.sst_inv > 0
  AND im.bln_sst = 0
  AND (b.invoice_branch_id = 4 OR (b.invoice_branch_id IS NULL AND l.branch_id = 4));

-- 4. Sample invoices from Ramakrishnan branch
SELECT 
    'Sample Ramakrishnan Invoices' as section,
    im.id as invoice_id,
    im.invoice_no,
    im.sst_inv,
    im.bln_sst,
    im.transferred_sst_amt,
    b.invoice_branch_id as bill_invoice_branch_id,
    l.branch_id as case_branch_id,
    b.bln_sst as bill_bln_sst,
    b.bln_invoice as bill_bln_invoice,
    CASE 
        WHEN b.invoice_branch_id = 4 THEN 'Has invoice_branch_id = 4'
        WHEN b.invoice_branch_id IS NULL AND l.branch_id = 4 THEN 'NULL invoice_branch_id but case branch = 4'
        ELSE 'Other'
    END as branch_status
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN loan_case l ON l.id = b.case_id
WHERE (b.invoice_branch_id = 4 OR (b.invoice_branch_id IS NULL AND l.branch_id = 4))
  AND im.status <> 99
  AND im.loan_case_main_bill_id > 0
  AND b.bln_invoice = 1
  AND b.bln_sst = 0
  AND im.sst_inv > 0
  AND im.bln_sst = 0
LIMIT 10;

-- 5. Check if invoice_branch_id is NULL for Ramakrishnan cases
SELECT 
    'NULL invoice_branch_id Issue' as section,
    COUNT(*) as total_cases,
    COUNT(CASE WHEN b.invoice_branch_id IS NULL THEN 1 END) as null_invoice_branch_id,
    COUNT(CASE WHEN b.invoice_branch_id IS NULL AND l.branch_id = 4 THEN 1 END) as null_but_case_branch_4,
    COUNT(CASE WHEN b.invoice_branch_id = 4 THEN 1 END) as has_invoice_branch_id_4
FROM loan_case_bill_main b
LEFT JOIN loan_case l ON l.id = b.case_id
WHERE l.branch_id = 4
  AND b.bln_invoice = 1;

-- 6. Check what BranchAccessService would filter (need to know user's branch_id)
-- Replace [USER_BRANCH_ID] with actual user's branch_id
-- This shows what branches the user can access
SELECT 
    'User Branch Access (Example)' as section,
    'If user is admin/account: All branches' as note,
    'If user is maker/lawyer: Only their branch' as note2,
    'If user branch_id = 4: Should see branch 4' as note3;






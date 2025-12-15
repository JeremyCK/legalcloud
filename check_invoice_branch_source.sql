-- Check Invoice Branch Source
-- This query shows which invoices use invoice_branch_id vs case branch_id as fallback

-- General check (all invoices)
SELECT 
    im.id as invoice_id,
    im.invoice_no,
    b.invoice_branch_id,
    br1.name as invoice_branch_name,
    l.branch_id as case_branch_id,
    br2.name as case_branch_name,
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
LEFT JOIN branch br1 ON br1.id = b.invoice_branch_id
LEFT JOIN branch br2 ON br2.id = l.branch_id
WHERE im.status <> 99
ORDER BY im.id DESC
LIMIT 50;

-- Statistics: Count by source
SELECT 
    CASE 
        WHEN b.invoice_branch_id IS NOT NULL THEN 'invoice_branch_id'
        ELSE 'case_branch_id (fallback)'
    END as branch_source,
    COUNT(*) as count
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN loan_case l ON l.id = b.case_id
WHERE im.status <> 99
GROUP BY branch_source;

-- Ramakrishnan (Branch 4) specific check
SELECT 
    im.id as invoice_id,
    im.invoice_no,
    b.invoice_branch_id,
    br1.name as invoice_branch_name,
    l.branch_id as case_branch_id,
    br2.name as case_branch_name,
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
LEFT JOIN branch br1 ON br1.id = b.invoice_branch_id
LEFT JOIN branch br2 ON br2.id = l.branch_id
WHERE im.status <> 99
  AND (
    b.invoice_branch_id = 4
    OR (b.invoice_branch_id IS NULL AND l.branch_id = 4)
  )
ORDER BY im.id DESC
LIMIT 50;

-- Ramakrishnan statistics
SELECT 
    CASE 
        WHEN b.invoice_branch_id = 4 THEN 'invoice_branch_id = 4'
        WHEN b.invoice_branch_id IS NULL AND l.branch_id = 4 THEN 'case_branch_id = 4 (fallback)'
        ELSE 'Other'
    END as branch_source,
    COUNT(*) as count
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN loan_case l ON l.id = b.case_id
WHERE im.status <> 99
  AND (
    b.invoice_branch_id = 4
    OR (b.invoice_branch_id IS NULL AND l.branch_id = 4)
  )
GROUP BY branch_source;

-- Check for invoices that should be Ramakrishnan but aren't showing
-- (meeting SST criteria but not appearing in search)
SELECT 
    im.id as invoice_id,
    im.invoice_no,
    im.sst_inv,
    im.bln_sst,
    im.transferred_sst_amt,
    im.reimbursement_sst,
    im.transferred_reimbursement_sst_amt,
    b.invoice_branch_id,
    l.branch_id as case_branch_id,
    b.bln_sst as bill_bln_sst,
    b.bln_invoice,
    CASE 
        WHEN b.invoice_branch_id IS NOT NULL THEN b.invoice_branch_id
        ELSE l.branch_id
    END as effective_branch_id,
    (im.sst_inv - COALESCE(im.transferred_sst_amt, 0)) + 
    (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)) as remaining_sst
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN loan_case l ON l.id = b.case_id
WHERE im.status <> 99
  AND im.loan_case_main_bill_id IS NOT NULL
  AND im.loan_case_main_bill_id > 0
  AND b.bln_invoice = 1
  AND b.bln_sst = 0
  AND im.sst_inv > 0
  AND im.bln_sst = 0
  AND (
    b.invoice_branch_id = 4
    OR (b.invoice_branch_id IS NULL AND l.branch_id = 4)
  )
ORDER BY im.id DESC;








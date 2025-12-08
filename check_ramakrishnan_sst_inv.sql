-- Quick check: Do Ramakrishnan invoices have sst_inv > 0?
-- This is likely why they're not appearing

SELECT 
    im.invoice_no,
    im.sst_inv,
    b.sst as bill_sst,
    im.bln_invoice,
    b.bln_invoice,
    im.bln_sst,
    b.bln_sst,
    CASE 
        WHEN im.sst_inv > 0 THEN '✅ HAS SST'
        WHEN im.sst_inv = 0 THEN '❌ NO SST (FILTERED OUT)'
        WHEN im.sst_inv IS NULL THEN '❌ NULL SST (FILTERED OUT)'
    END as sst_status
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON im.loan_case_main_bill_id = b.id
WHERE b.invoice_branch_id = 4
  AND im.bln_sst = 0
  AND im.bln_invoice = 1
  AND b.bln_invoice = 1
ORDER BY im.id DESC
LIMIT 20;

-- Count by sst_inv status
SELECT 
    CASE 
        WHEN im.sst_inv > 0 THEN 'Has SST (sst_inv > 0)'
        WHEN im.sst_inv = 0 THEN 'No SST (sst_inv = 0)'
        WHEN im.sst_inv IS NULL THEN 'NULL SST'
    END as sst_status,
    COUNT(*) as count
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON im.loan_case_main_bill_id = b.id
WHERE b.invoice_branch_id = 4
  AND im.bln_sst = 0
  AND im.bln_invoice = 1
  AND b.bln_invoice = 1
GROUP BY sst_status;

-- If sst_inv is 0 but bill.sst > 0, we need to sync them
SELECT 
    im.invoice_no,
    im.sst_inv as invoice_sst,
    b.sst as bill_sst,
    CASE 
        WHEN im.sst_inv != b.sst THEN 'MISMATCH - NEED SYNC'
        ELSE 'OK'
    END as status
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON im.loan_case_main_bill_id = b.id
WHERE b.invoice_branch_id = 4
  AND im.bln_sst = 0
  AND im.bln_invoice = 1
  AND b.bln_invoice = 1
  AND im.sst_inv != b.sst
LIMIT 20;







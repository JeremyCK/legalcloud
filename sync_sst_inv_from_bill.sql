-- Sync sst_inv from loan_case_bill_main.sst to loan_case_invoice_main.sst_inv
-- This ensures invoice-level sst_inv matches bill-level sst

-- Check current mismatches
SELECT 
    'Mismatch Count' as description,
    COUNT(*) as count
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE im.status <> 99
  AND bm.status <> 99
  AND COALESCE(im.sst_inv, 0) != COALESCE(bm.sst, 0);

-- Show sample mismatches
SELECT 
    im.id as invoice_id,
    im.invoice_no,
    im.sst_inv as invoice_sst,
    bm.id as bill_id,
    bm.sst as bill_sst,
    bm.invoice_branch_id
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE im.status <> 99
  AND bm.status <> 99
  AND COALESCE(im.sst_inv, 0) != COALESCE(bm.sst, 0)
LIMIT 20;

-- Update: Sync invoice sst_inv to match bill sst
UPDATE loan_case_invoice_main im
INNER JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
SET im.sst_inv = COALESCE(bm.sst, 0)
WHERE im.status <> 99
  AND bm.status <> 99
  AND COALESCE(im.sst_inv, 0) != COALESCE(bm.sst, 0);

-- Verify after update
SELECT 
    'After Sync - Mismatch Count' as description,
    COUNT(*) as count
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE im.status <> 99
  AND bm.status <> 99
  AND COALESCE(im.sst_inv, 0) != COALESCE(bm.sst, 0);

-- Check Ramakrishnan specifically after sync
SELECT 
    im.invoice_no,
    im.sst_inv,
    bm.sst as bill_sst,
    CASE 
        WHEN im.sst_inv > 0 THEN '✅ HAS SST'
        ELSE '❌ NO SST'
    END as status
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE bm.invoice_branch_id = 4
  AND im.bln_sst = 0
  AND im.bln_invoice = 1
  AND bm.bln_invoice = 1
ORDER BY im.id DESC
LIMIT 20;












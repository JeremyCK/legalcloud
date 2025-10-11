-- Script to add bln_sst column to loan_case_invoice_main table
-- and update it based on matching invoice numbers with loan_case_bill_main

-- Step 1: Add the bln_sst column to loan_case_invoice_main table
ALTER TABLE `loan_case_invoice_main` 
ADD COLUMN `bln_sst` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'SST transfer flag: 0=not transferred, 1=transferred' 
AFTER `transferred_sst_amt`;

-- Step 2: Update bln_sst in loan_case_invoice_main based on matching invoice numbers
-- This will set bln_sst = 1 for invoices that have been transferred in the original SST system
UPDATE `loan_case_invoice_main` im
INNER JOIN `loan_case_bill_main` bm ON im.invoice_no = bm.invoice_no
SET im.bln_sst = 1
WHERE bm.bln_sst = 1 
AND im.status <> 99 
AND bm.status <> 99;

-- Step 3: Update bln_sst in loan_case_invoice_main based on existing SST transfers
-- This will set bln_sst = 1 for invoices that already have transferred_sst_amt > 0
UPDATE `loan_case_invoice_main` 
SET `bln_sst` = 1 
WHERE `transferred_sst_amt` > 0 
AND `status` <> 99;

-- Step 4: Verify the results
SELECT 
    'loan_case_bill_main' as table_name,
    COUNT(*) as total_records,
    SUM(CASE WHEN bln_sst = 1 THEN 1 ELSE 0 END) as transferred_count,
    SUM(CASE WHEN bln_sst = 0 THEN 1 ELSE 0 END) as not_transferred_count
FROM `loan_case_bill_main` 
WHERE status <> 99

UNION ALL

SELECT 
    'loan_case_invoice_main' as table_name,
    COUNT(*) as total_records,
    SUM(CASE WHEN bln_sst = 1 THEN 1 ELSE 0 END) as transferred_count,
    SUM(CASE WHEN bln_sst = 0 THEN 1 ELSE 0 END) as not_transferred_count
FROM `loan_case_invoice_main` 
WHERE status <> 99;

-- Step 5: Show sample of updated records
SELECT 
    im.id,
    im.invoice_no,
    im.bln_sst,
    im.transferred_sst_amt,
    bm.id as bill_id,
    bm.bln_sst as bill_bln_sst
FROM `loan_case_invoice_main` im
LEFT JOIN `loan_case_bill_main` bm ON im.invoice_no = bm.invoice_no
WHERE im.status <> 99
ORDER BY im.id DESC
LIMIT 10;

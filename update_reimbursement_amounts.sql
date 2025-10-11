-- MySQL Script to Update Reimbursement Amounts in loan_case_invoice_main
-- This script calculates and updates reimb_inv and reimb_sst_inv based on:
-- 1. Sum of amounts from loan_case_invoice_details where account_cat_id = 4
-- 2. SST calculation using sst_rate from loan_case_bill_main

-- First, add missing columns to loan_case_bill_main table
ALTER TABLE loan_case_bill_main 
ADD COLUMN transferred_reimbursement_amt DECIMAL(20,2) DEFAULT 0.00 COMMENT 'Total transferred reimbursement amount';

ALTER TABLE loan_case_bill_main 
ADD COLUMN transferred_reimbursement_sst_amt DECIMAL(20,2) DEFAULT 0.00 COMMENT 'Total transferred reimbursement SST amount';

-- Add reimbursement columns to loan_case_bill_main table
ALTER TABLE loan_case_bill_main 
ADD COLUMN reimbursement_amount DECIMAL(20,2) DEFAULT 0.00 COMMENT 'Reimbursement amount (calculated from loan_case_invoice_details where account_cat_id = 4)';

ALTER TABLE loan_case_bill_main 
ADD COLUMN reimbursement_sst DECIMAL(20,2) DEFAULT 0.00 COMMENT 'Reimbursement SST amount (calculated from loan_case_invoice_details where account_cat_id = 4)';

-- Update reimbursement_amount (base reimbursement amount)
UPDATE loan_case_invoice_main im
INNER JOIN (
    SELECT 
        id.invoice_main_id,
        SUM(id.amount) as total_reimbursement
    FROM loan_case_invoice_details id
    INNER JOIN account_item ai ON id.account_item_id = ai.id
    WHERE ai.account_cat_id = 4
    GROUP BY id.invoice_main_id
) reimbursement_totals ON im.id = reimbursement_totals.invoice_main_id
SET im.reimbursement_amount = reimbursement_totals.total_reimbursement;

-- Update reimbursement_sst (SST on reimbursement amount)
UPDATE loan_case_invoice_main im
INNER JOIN (
    SELECT 
        id.invoice_main_id,
        SUM(id.amount) as total_reimbursement
    FROM loan_case_invoice_details id
    INNER JOIN account_item ai ON id.account_item_id = ai.id
    WHERE ai.account_cat_id = 4
    GROUP BY id.invoice_main_id
) reimbursement_totals ON im.id = reimbursement_totals.invoice_main_id
INNER JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
SET im.reimbursement_sst = ROUND(reimbursement_totals.total_reimbursement * (bm.sst_rate / 100), 2);

-- Update loan_case_bill_main with reimbursement amounts
-- Update reimbursement_amount (base reimbursement amount) for bills
UPDATE loan_case_bill_main bm
INNER JOIN (
    SELECT 
        im.loan_case_main_bill_id,
        SUM(id.amount) as total_reimbursement
    FROM loan_case_invoice_main im
    INNER JOIN loan_case_invoice_details id ON im.id = id.invoice_main_id
    INNER JOIN account_item ai ON id.account_item_id = ai.id
    WHERE ai.account_cat_id = 4
    GROUP BY im.loan_case_main_bill_id
) reimbursement_totals ON bm.id = reimbursement_totals.loan_case_main_bill_id
SET bm.reimbursement_amount = reimbursement_totals.total_reimbursement;

-- Update reimbursement_sst (SST on reimbursement amount) for bills
UPDATE loan_case_bill_main bm
INNER JOIN (
    SELECT 
        im.loan_case_main_bill_id,
        SUM(id.amount) as total_reimbursement
    FROM loan_case_invoice_main im
    INNER JOIN loan_case_invoice_details id ON im.id = id.invoice_main_id
    INNER JOIN account_item ai ON id.account_item_id = ai.id
    WHERE ai.account_cat_id = 4
    GROUP BY im.loan_case_main_bill_id
) reimbursement_totals ON bm.id = reimbursement_totals.loan_case_main_bill_id
SET bm.reimbursement_sst = ROUND(reimbursement_totals.total_reimbursement * (bm.sst_rate / 100), 2);

-- Alternative single query approach (if you prefer to run both updates in one statement):
/*
UPDATE loan_case_invoice_main im
INNER JOIN (
    SELECT 
        id.invoice_main_id,
        SUM(id.amount) as total_reimbursement
    FROM loan_case_invoice_details id
    INNER JOIN account_item ai ON id.account_item_id = ai.id
    WHERE ai.account_cat_id = 4
    GROUP BY id.invoice_main_id
) reimbursement_totals ON im.id = reimbursement_totals.invoice_main_id
INNER JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
SET 
    im.reimbursement_amount = reimbursement_totals.total_reimbursement,
    im.reimbursement_sst = ROUND(reimbursement_totals.total_reimbursement * (bm.sst_rate / 100), 2);
*/

-- Verification query to check the results
SELECT 
    im.id,
    im.invoice_no,
    im.reimbursement_amount,
    im.reimbursement_sst,
    bm.reimbursement_amount as bill_reimbursement_amount,
    bm.reimbursement_sst as bill_reimbursement_sst,
    bm.sst_rate,
    (SELECT SUM(id.amount) 
     FROM loan_case_invoice_details id 
     INNER JOIN account_item ai ON id.account_item_id = ai.id 
     WHERE id.invoice_main_id = im.id AND ai.account_cat_id = 4) as calculated_reimbursement,
    ROUND((SELECT SUM(id.amount) 
            FROM loan_case_invoice_details id 
            INNER JOIN account_item ai ON id.account_item_id = ai.id 
            WHERE id.invoice_main_id = im.id AND ai.account_cat_id = 4) * (bm.sst_rate / 100), 2) as calculated_sst
FROM loan_case_invoice_main im
INNER JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
WHERE im.reimbursement_amount > 0 OR im.reimbursement_sst > 0
ORDER BY im.id;

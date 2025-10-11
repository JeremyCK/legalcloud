-- Add missing reimbursement columns to loan_case_bill_main table
-- Run this script first to fix the "Column not found" error

-- Add transferred reimbursement columns to loan_case_bill_main
ALTER TABLE loan_case_bill_main 
ADD COLUMN transferred_reimbursement_amt DECIMAL(20,2) DEFAULT 0.00 COMMENT 'Total transferred reimbursement amount';

ALTER TABLE loan_case_bill_main 
ADD COLUMN transferred_reimbursement_sst_amt DECIMAL(20,2) DEFAULT 0.00 COMMENT 'Total transferred reimbursement SST amount';

-- Add base reimbursement columns to loan_case_bill_main (if not already exist)
ALTER TABLE loan_case_bill_main 
ADD COLUMN reimbursement_amount DECIMAL(20,2) DEFAULT 0.00 COMMENT 'Reimbursement amount (calculated from loan_case_invoice_details where account_cat_id = 4)';

ALTER TABLE loan_case_bill_main 
ADD COLUMN reimbursement_sst DECIMAL(20,2) DEFAULT 0.00 COMMENT 'Reimbursement SST amount (calculated from loan_case_invoice_details where account_cat_id = 4)';

-- Verify columns were added
DESCRIBE loan_case_bill_main;


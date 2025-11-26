-- Helper Script: Generate UPDATE SQL for specific invoice numbers
-- 
-- INSTRUCTIONS:
-- 1. Copy the invoice numbers from your Excel (the ones highlighted in red)
-- 2. Replace the invoice numbers in the WHERE clause below
-- 3. Run this query to generate the UPDATE statement
-- 4. Copy and run the generated UPDATE statement

-- Step 1: List your invoice numbers here (replace with actual numbers from Excel)
SET @invoice_numbers = '20002388,20002385,20002386';  -- Add your invoice numbers here, comma-separated

-- Step 2: This query will show you the invoice numbers and their current vs calculated values
SELECT 
    im.invoice_no,
    im.transferred_reimbursement_amt as current_amt,
    im.transferred_reimbursement_sst_amt as current_sst_amt,
    (SELECT COALESCE(SUM(tfd.reimbursement_amount), 0) 
     FROM transfer_fee_details tfd 
     WHERE tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99) as should_be_amt,
    (SELECT COALESCE(SUM(tfd.reimbursement_sst_amount), 0) 
     FROM transfer_fee_details tfd 
     WHERE tfd.loan_case_invoice_main_id = im.id AND tfd.status <> 99) as should_be_sst_amt
FROM loan_case_invoice_main im
WHERE FIND_IN_SET(im.invoice_no, @invoice_numbers) > 0;

-- Step 3: Use OPTION A in update_transferred_reimbursement_amounts.sql 
-- and replace the invoice numbers in the WHERE clause


-- Check if transfer_fee_details has records for invoice 20002388
SELECT 
    tfd.*,
    im.invoice_no
FROM transfer_fee_details tfd
LEFT JOIN loan_case_invoice_main im ON tfd.loan_case_invoice_main_id = im.id
WHERE im.invoice_no = '20002388'
   OR tfd.loan_case_invoice_main_id = 9267;

-- Check all transfer_fee_details for invoice ID 9267
SELECT * FROM transfer_fee_details 
WHERE loan_case_invoice_main_id = 9267;

-- Check what the sum should be
SELECT 
    loan_case_invoice_main_id,
    SUM(COALESCE(reimbursement_amount, 0)) as total_reimbursement,
    SUM(COALESCE(reimbursement_sst_amount, 0)) as total_reimbursement_sst,
    COUNT(*) as record_count
FROM transfer_fee_details
WHERE loan_case_invoice_main_id = 9267
  AND status <> 99
GROUP BY loan_case_invoice_main_id;


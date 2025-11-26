-- TEST: Update just one invoice first to verify it works
-- Test with invoice 20002388

-- Check what should be the value
SELECT 
    im.id,
    im.invoice_no,
    im.transferred_reimbursement_amt as current_value,
    (SELECT IFNULL(SUM(reimbursement_amount), 0)
     FROM transfer_fee_details
     WHERE loan_case_invoice_main_id = im.id AND status <> 99) as should_be
FROM loan_case_invoice_main im
WHERE im.invoice_no = '20002388';

-- Update just this one invoice
UPDATE loan_case_invoice_main
SET 
    transferred_reimbursement_amt = (
        SELECT IFNULL(SUM(reimbursement_amount), 0)
        FROM transfer_fee_details
        WHERE loan_case_invoice_main_id = loan_case_invoice_main.id
          AND status <> 99
    ),
    transferred_reimbursement_sst_amt = (
        SELECT IFNULL(SUM(reimbursement_sst_amount), 0)
        FROM transfer_fee_details
        WHERE loan_case_invoice_main_id = loan_case_invoice_main.id
          AND status <> 99
    ),
    updated_at = NOW()
WHERE invoice_no = '20002388';

-- Verify it worked
SELECT 
    invoice_no,
    transferred_reimbursement_amt,
    transferred_reimbursement_sst_amt,
    updated_at
FROM loan_case_invoice_main
WHERE invoice_no = '20002388';


-- ALTERNATIVE: Update invoices one by one (if batch update doesn't work)
-- This approach updates each invoice individually

-- Template for one invoice - repeat for each invoice number
UPDATE loan_case_invoice_main 
SET 
    transferred_reimbursement_amt = (
        SELECT COALESCE(SUM(reimbursement_amount), 0)
        FROM transfer_fee_details
        WHERE loan_case_invoice_main_id = loan_case_invoice_main.id
          AND status <> 99
    ),
    transferred_reimbursement_sst_amt = (
        SELECT COALESCE(SUM(reimbursement_sst_amount), 0)
        FROM transfer_fee_details
        WHERE loan_case_invoice_main_id = loan_case_invoice_main.id
          AND status <> 99
    ),
    updated_at = NOW()
WHERE invoice_no = '20002388';

-- Copy the above block and change '20002388' to each invoice number:
-- '20002389', '20002392', '20002395', etc.


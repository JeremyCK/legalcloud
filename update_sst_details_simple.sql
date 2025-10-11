-- Simple script to update sst_details loan_case_invoice_main_id
-- Links existing SST details to invoice records through bill relationship

UPDATE sst_details sd
INNER JOIN loan_case_bill_main b ON b.id = sd.loan_case_main_bill_id
INNER JOIN loan_case_invoice_main im ON im.loan_case_main_bill_id = b.id
SET sd.loan_case_invoice_main_id = im.id
WHERE sd.loan_case_invoice_main_id IS NULL
  AND sd.loan_case_main_bill_id IS NOT NULL;

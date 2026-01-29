-- Check the discrepancy in Transferred SST for invoice DP20001286
-- Transfer Fee ID: 502

SELECT 
    tfd.id as transfer_fee_detail_id,
    im.invoice_no,
    im.sst_inv as invoice_sst,
    im.reimbursement_sst as invoice_reimb_sst,
    (im.sst_inv + im.reimbursement_sst) as invoice_total_sst,
    tfd.sst_amount as transferred_sst_amount,
    tfd.reimbursement_sst_amount as transferred_reimb_sst_amount,
    (tfd.sst_amount + tfd.reimbursement_sst_amount) as transferred_total_sst,
    ((tfd.sst_amount + tfd.reimbursement_sst_amount) - (im.sst_inv + im.reimbursement_sst)) as difference
FROM transfer_fee_details tfd
INNER JOIN loan_case_invoice_main im ON im.id = tfd.loan_case_invoice_main_id
WHERE tfd.transfer_fee_main_id = 502
  AND im.invoice_no = 'DP20001286';

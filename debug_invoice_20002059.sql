-- Debug script to investigate invoice 20002059
-- Check why it appears in SST v2 but not in original SST

-- Step 1: Check if invoice 20002059 exists in loan_case_invoice_main
SELECT 
    'loan_case_invoice_main' as table_name,
    id,
    invoice_no,
    status,
    loan_case_main_bill_id,
    transferred_sst_amt,
    sst_inv,
    bln_sst,
    Invoice_date,
    created_at
FROM loan_case_invoice_main 
WHERE invoice_no = '20002059'
AND status <> 99;

-- Step 2: Check if corresponding bill exists in loan_case_bill_main
SELECT 
    'loan_case_bill_main' as table_name,
    b.id,
    b.invoice_no,
    b.status,
    b.bln_invoice,
    b.bln_sst,
    b.invoice_branch_id,
    b.payment_receipt_date,
    b.total_amt,
    b.collected_amt,
    l.case_ref_no,
    c.name as client_name
FROM loan_case_bill_main b
LEFT JOIN loan_case l ON l.id = b.case_id
LEFT JOIN client c ON c.id = l.customer_id
WHERE b.invoice_no = '20002059'
AND b.status <> 99;

-- Step 3: Check the relationship between invoice and bill
SELECT 
    'relationship_check' as check_type,
    im.id as invoice_id,
    im.invoice_no as invoice_no,
    im.loan_case_main_bill_id,
    b.id as bill_id,
    b.invoice_no as bill_invoice_no,
    b.bln_invoice,
    b.bln_sst,
    b.status as bill_status,
    im.status as invoice_status
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
WHERE im.invoice_no = '20002059'
AND im.status <> 99;

-- Step 4: Check if there are any SST transfers for this invoice
SELECT 
    'sst_transfers' as check_type,
    sd.id,
    sd.sst_main_id,
    sd.loan_case_invoice_main_id,
    sd.amount,
    sd.status,
    sm.transaction_id,
    sm.payment_date
FROM sst_details sd
LEFT JOIN sst_main sm ON sm.id = sd.sst_main_id
WHERE sd.loan_case_invoice_main_id IN (
    SELECT id FROM loan_case_invoice_main WHERE invoice_no = '20002059'
);

-- Step 5: Check original SST logic conditions
SELECT 
    'original_sst_conditions' as check_type,
    b.id,
    b.invoice_no,
    b.status,
    b.bln_invoice,
    b.bln_sst,
    b.invoice_branch_id,
    b.payment_receipt_date,
    l.case_ref_no,
    c.name as client_name,
    CASE 
        WHEN b.status <> 99 THEN 'status_ok'
        ELSE 'status_not_ok'
    END as status_check,
    CASE 
        WHEN b.bln_invoice = 1 THEN 'billable_ok'
        ELSE 'not_billable'
    END as billable_check,
    CASE 
        WHEN b.bln_sst = 0 THEN 'not_transferred'
        ELSE 'already_transferred'
    END as transfer_check
FROM loan_case_bill_main b
LEFT JOIN loan_case l ON l.id = b.case_id
LEFT JOIN client c ON c.id = l.customer_id
WHERE b.invoice_no = '20002059'
AND b.status <> 99
AND b.bln_invoice = 1;

-- Step 6: Check SST v2 logic conditions
SELECT 
    'sst_v2_conditions' as check_type,
    im.id,
    im.invoice_no,
    im.status,
    im.loan_case_main_bill_id,
    im.transferred_sst_amt,
    im.sst_inv,
    im.bln_sst,
    b.id as bill_id,
    b.bln_invoice,
    b.invoice_branch_id,
    b.status as bill_status,
    CASE 
        WHEN im.status <> 99 THEN 'status_ok'
        ELSE 'status_not_ok'
    END as status_check,
    CASE 
        WHEN im.transferred_to_office_bank = 0 THEN 'not_fully_transferred'
        ELSE 'fully_transferred'
    END as transfer_check,
    CASE 
        WHEN im.loan_case_main_bill_id IS NOT NULL AND im.loan_case_main_bill_id > 0 THEN 'has_bill'
        ELSE 'no_bill'
    END as bill_check,
    CASE 
        WHEN b.bln_invoice = 1 THEN 'billable_ok'
        ELSE 'not_billable'
    END as billable_check,
    CASE 
        WHEN im.sst_inv > 0 THEN 'has_sst'
        ELSE 'no_sst'
    END as sst_check,
    CASE 
        WHEN im.bln_sst = 0 THEN 'not_transferred'
        ELSE 'already_transferred'
    END as sst_transfer_check
FROM loan_case_invoice_main im
LEFT JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
WHERE im.invoice_no = '20002059'
AND im.status <> 99;

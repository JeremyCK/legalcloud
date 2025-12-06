-- COMPLETE FIX for SST Record 96
-- This fixes both SST amounts AND reimbursement SST
-- Run this script step by step

-- ============================================
-- STEP 1: DIAGNOSTIC - Check current state
-- ============================================
SELECT 
    'STEP 1: Current State' as section,
    sd.id as sst_detail_id,
    im.invoice_no,
    -- SST amounts
    sd.amount as sst_details_amount,
    im.sst_inv as invoice_sst_amount,
    CASE 
        WHEN sd.amount = 0 OR sd.amount IS NULL THEN '❌ SST MISSING'
        WHEN sd.amount != COALESCE(im.sst_inv, 0) THEN '⚠️ SST MISMATCH'
        ELSE '✅ SST OK'
    END as sst_status,
    -- Reimbursement SST
    COALESCE(im.reimbursement_sst, 0) as reimbursement_sst,
    COALESCE(im.transferred_reimbursement_sst_amt, 0) as transferred_reimb_sst,
    GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) as remaining_reimb_sst,
    CASE 
        WHEN COALESCE(im.reimbursement_sst, 0) = 0 THEN '⚠️ No reimbursement SST'
        WHEN COALESCE(im.transferred_reimbursement_sst_amt, 0) >= COALESCE(im.reimbursement_sst, 0) THEN '⚠️ Already fully transferred'
        ELSE '✅ Has remaining reimbursement SST'
    END as reimb_status
FROM sst_details sd
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
ORDER BY sd.id;

-- ============================================
-- STEP 2: FIX SST AMOUNTS
-- ============================================
-- Update sst_details.amount from invoice.sst_inv
UPDATE sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
SET 
    sd.amount = COALESCE(im.sst_inv, 0),
    sd.updated_at = NOW()
WHERE sd.sst_main_id = 96
AND (
    sd.amount = 0 
    OR sd.amount IS NULL 
    OR sd.amount != COALESCE(im.sst_inv, 0)
);

-- ============================================
-- STEP 3: FIX REIMBURSEMENT SST
-- ============================================
-- Calculate and update reimbursement_sst from invoice details
-- Reimbursement SST = (sum of invoice details where account_cat_id = 4) * sst_rate
UPDATE loan_case_invoice_main im
INNER JOIN sst_details sd ON sd.loan_case_invoice_main_id = im.id
INNER JOIN loan_case_bill_main b ON b.id = im.loan_case_main_bill_id
LEFT JOIN (
    SELECT 
        ild.invoice_main_id,
        SUM(ild.amount) as total_reimb
    FROM loan_case_invoice_details ild
    INNER JOIN account_item ai ON ai.id = ild.account_item_id
    WHERE ai.account_cat_id = 4
      AND ild.status <> 99
    GROUP BY ild.invoice_main_id
) reimb_details ON reimb_details.invoice_main_id = im.id
SET 
    im.reimbursement_amount = COALESCE(reimb_details.total_reimb, 0),
    im.reimbursement_sst = ROUND(COALESCE(reimb_details.total_reimb, 0) * COALESCE(b.sst_rate, 6) / 100, 2),
    im.updated_at = NOW()
WHERE sd.sst_main_id = 96
  AND reimb_details.total_reimb IS NOT NULL
  AND reimb_details.total_reimb > 0
  AND (
    ABS(COALESCE(im.reimbursement_amount, 0) - COALESCE(reimb_details.total_reimb, 0)) > 0.01
    OR ABS(COALESCE(im.reimbursement_sst, 0) - ROUND(COALESCE(reimb_details.total_reimb, 0) * COALESCE(b.sst_rate, 6) / 100, 2)) > 0.01
  );

-- ============================================
-- STEP 4: RESET TRANSFERRED REIMBURSEMENT SST (if needed)
-- ============================================
-- If reimbursement SST was just calculated, we may need to reset transferred amount
-- This ensures remaining reimbursement SST shows correctly
-- Only reset if transferred amount is greater than reimbursement SST
UPDATE loan_case_invoice_main im
INNER JOIN sst_details sd ON sd.loan_case_invoice_main_id = im.id
SET 
    im.transferred_reimbursement_sst_amt = LEAST(
        COALESCE(im.transferred_reimbursement_sst_amt, 0),
        COALESCE(im.reimbursement_sst, 0)
    ),
    im.updated_at = NOW()
WHERE sd.sst_main_id = 96
AND COALESCE(im.transferred_reimbursement_sst_amt, 0) > COALESCE(im.reimbursement_sst, 0);

-- ============================================
-- STEP 5: RECALCULATE SST MAIN TOTAL
-- ============================================
-- Update sst_main.amount to include both SST and reimbursement SST
UPDATE sst_main sm
INNER JOIN (
    SELECT 
        sd.sst_main_id,
        SUM(COALESCE(sd.amount, 0) + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as calculated_total
    FROM sst_details sd
    LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
    WHERE sd.sst_main_id = 96
    GROUP BY sd.sst_main_id
) calculated ON calculated.sst_main_id = sm.id
SET 
    sm.amount = calculated.calculated_total,
    sm.updated_at = NOW()
WHERE sm.id = 96;

-- ============================================
-- STEP 6: VERIFICATION
-- ============================================
SELECT 
    'STEP 6: Verification' as section,
    sm.id as sst_main_id,
    sm.amount as stored_amount,
    COUNT(sd.id) as invoice_count,
    SUM(COALESCE(sd.amount, 0)) as total_sst,
    SUM(GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as total_remaining_reimb_sst,
    SUM(COALESCE(sd.amount, 0) + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)))) as calculated_total,
    ABS(sm.amount - SUM(COALESCE(sd.amount, 0) + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))))) as difference,
    CASE 
        WHEN ABS(sm.amount - SUM(COALESCE(sd.amount, 0) + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))))) < 0.01 
        THEN '✅ OK' 
        ELSE '❌ MISMATCH' 
    END as status
FROM sst_main sm
LEFT JOIN sst_details sd ON sd.sst_main_id = sm.id
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sm.id = 96
GROUP BY sm.id, sm.amount;

-- ============================================
-- STEP 7: DETAILED INVOICE CHECK
-- ============================================
SELECT 
    'STEP 7: Invoice Details' as section,
    sd.id as sst_detail_id,
    im.invoice_no,
    COALESCE(sd.amount, 0) as sst_amount,
    COALESCE(im.reimbursement_sst, 0) as reimbursement_sst,
    COALESCE(im.transferred_reimbursement_sst_amt, 0) as transferred_reimb_sst,
    GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) as remaining_reimb_sst,
    COALESCE(sd.amount, 0) + GREATEST(0, (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) as total_sst_row
FROM sst_details sd
LEFT JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
WHERE sd.sst_main_id = 96
ORDER BY sd.id;





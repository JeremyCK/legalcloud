-- ============================================================================
-- Fix E-Invoice Client Profile Status
-- 
-- This script fixes the client_profile_completed status for:
-- 1. einvoice_details - based on invoice's billing party or all bill billing parties
-- 2. einvoice_main - based on all einvoice_details being completed
--
-- Run this script on your server database
-- ============================================================================

-- Step 1: Update einvoice_details.client_profile_completed
-- Logic: Mark as completed if:
--   - Invoice's billing party is completed, OR
--   - All billing parties for the bill are completed, OR
--   - No billing parties exist (default to completed)

UPDATE einvoice_details ed
INNER JOIN loan_case_invoice_main i ON i.id = ed.loan_case_invoice_id
LEFT JOIN invoice_billing_party p ON p.id = i.bill_party_id
LEFT JOIN (
    -- Get count of completed billing parties per bill
    SELECT 
        loan_case_main_bill_id,
        COUNT(*) as total_parties,
        SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_parties
    FROM invoice_billing_party
    GROUP BY loan_case_main_bill_id
) bp ON bp.loan_case_main_bill_id = ed.loan_case_main_bill_id
SET ed.client_profile_completed = CASE
    -- If invoice's billing party is completed, mark detail as completed
    WHEN p.completed = 1 THEN 1
    -- If all billing parties for the bill are completed, mark detail as completed
    WHEN bp.total_parties > 0 AND bp.completed_parties = bp.total_parties THEN 1
    -- If no billing parties exist, mark as completed by default
    WHEN (bp.total_parties IS NULL OR bp.total_parties = 0) AND (p.id IS NULL OR p.completed IS NULL) THEN 1
    -- Otherwise, mark as pending
    ELSE 0
END
WHERE ed.status <> 99
  AND ed.loan_case_invoice_id IS NOT NULL
  AND ed.loan_case_main_bill_id IS NOT NULL;

-- Step 2: Update einvoice_main.client_profile_completed
-- Logic: Mark as completed only if ALL einvoice_details are completed

UPDATE einvoice_main em
INNER JOIN (
    -- Get completion status for each einvoice_main
    SELECT 
        einvoice_main_id,
        COUNT(*) as total_details,
        SUM(CASE WHEN client_profile_completed = 1 THEN 1 ELSE 0 END) as completed_details
    FROM einvoice_details
    WHERE status <> 99
    GROUP BY einvoice_main_id
) ed_summary ON ed_summary.einvoice_main_id = em.id
SET em.client_profile_completed = CASE
    -- If all details are completed, mark main as completed
    WHEN ed_summary.total_details > 0 AND ed_summary.completed_details = ed_summary.total_details THEN 1
    -- Otherwise, mark as pending
    ELSE 0
END
WHERE em.status <> 99;

-- ============================================================================
-- Verification Queries (Optional - run these to check the results)
-- ============================================================================

-- Check how many details were updated
-- SELECT 
--     COUNT(*) as total_details,
--     SUM(CASE WHEN client_profile_completed = 1 THEN 1 ELSE 0 END) as completed_details,
--     SUM(CASE WHEN client_profile_completed = 0 THEN 1 ELSE 0 END) as pending_details
-- FROM einvoice_details
-- WHERE status <> 99;

-- Check how many main records were updated
-- SELECT 
--     COUNT(*) as total_main,
--     SUM(CASE WHEN client_profile_completed = 1 THEN 1 ELSE 0 END) as completed_main,
--     SUM(CASE WHEN client_profile_completed = 0 THEN 1 ELSE 0 END) as pending_main
-- FROM einvoice_main
-- WHERE status <> 99;

-- Check for any mismatches (details completed but main pending, or vice versa)
-- SELECT 
--     em.id,
--     em.ref_no,
--     em.client_profile_completed as main_status,
--     COUNT(ed.id) as total_details,
--     SUM(CASE WHEN ed.client_profile_completed = 1 THEN 1 ELSE 0 END) as completed_details,
--     CASE 
--         WHEN em.client_profile_completed = 1 AND SUM(CASE WHEN ed.client_profile_completed = 1 THEN 1 ELSE 0 END) < COUNT(ed.id) THEN 'Main completed but details not all completed'
--         WHEN em.client_profile_completed = 0 AND SUM(CASE WHEN ed.client_profile_completed = 1 THEN 1 ELSE 0 END) = COUNT(ed.id) THEN 'Main pending but all details completed'
--         ELSE 'OK'
--     END as status_check
-- FROM einvoice_main em
-- LEFT JOIN einvoice_details ed ON ed.einvoice_main_id = em.id AND ed.status <> 99
-- WHERE em.status <> 99
-- GROUP BY em.id, em.ref_no, em.client_profile_completed
-- HAVING status_check <> 'OK';

-- =====================================================
-- TRANSFER FEE MIGRATION - STEP 1
-- Add new columns to loan_case_invoice_main table
-- =====================================================

-- Step 1.1: Add transferred amount tracking columns
ALTER TABLE loan_case_invoice_main 
ADD COLUMN transferred_pfee_amt DECIMAL(20,2) DEFAULT 0.00 COMMENT 'Transferred professional fee amount';

ALTER TABLE loan_case_invoice_main 
ADD COLUMN transferred_sst_amt DECIMAL(20,2) DEFAULT 0.00 COMMENT 'Transferred SST amount';

-- Step 1.2: Add transfer status column
ALTER TABLE loan_case_invoice_main 
ADD COLUMN transferred_to_office_bank TINYINT DEFAULT 0 COMMENT 'Transfer status flag (0=not transferred, 1=transferred)';

-- Step 1.3: Add fee amount columns
ALTER TABLE loan_case_invoice_main 
ADD COLUMN pfee1_inv DECIMAL(20,2) DEFAULT 0.00 COMMENT 'Professional fee 1 amount';

ALTER TABLE loan_case_invoice_main 
ADD COLUMN pfee2_inv DECIMAL(20,2) DEFAULT 0.00 COMMENT 'Professional fee 2 amount';

ALTER TABLE loan_case_invoice_main 
ADD COLUMN sst_inv DECIMAL(20,2) DEFAULT 0.00 COMMENT 'SST amount';

-- Step 1.4: Add invoice flag
ALTER TABLE loan_case_invoice_main 
ADD COLUMN bln_invoice TINYINT DEFAULT 0 COMMENT 'Invoice flag (0=not invoice, 1=is invoice)';

-- Step 1.5: Add indexes for performance
ALTER TABLE loan_case_invoice_main 
ADD INDEX idx_transferred_status (transferred_to_office_bank);

ALTER TABLE loan_case_invoice_main 
ADD INDEX idx_bln_invoice (bln_invoice);

ALTER TABLE loan_case_invoice_main 
ADD INDEX idx_transfer_amounts (transferred_pfee_amt, transferred_sst_amt);

-- =====================================================
-- TRANSFER FEE MIGRATION - STEP 2
-- Add new column to transfer_fee_details table
-- =====================================================

-- Step 2.1: Add new column to track invoice-based transfers
ALTER TABLE transfer_fee_details 
ADD COLUMN loan_case_invoice_main_id BIGINT UNSIGNED NULL COMMENT 'Reference to loan_case_invoice_main table';

-- Step 2.2: Add index for performance
ALTER TABLE transfer_fee_details 
ADD INDEX idx_invoice_main_id (loan_case_invoice_main_id);

-- Step 2.3: Add foreign key constraint (optional - for data integrity)
-- ALTER TABLE transfer_fee_details 
-- ADD CONSTRAINT fk_transfer_fee_invoice_main 
-- FOREIGN KEY (loan_case_invoice_main_id) REFERENCES loan_case_invoice_main(id) ON DELETE SET NULL;

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================

-- Verify loan_case_invoice_main columns were added successfully
DESCRIBE loan_case_invoice_main;

-- Verify transfer_fee_details column was added successfully
DESCRIBE transfer_fee_details;

-- Check if indexes were created
SHOW INDEX FROM loan_case_invoice_main;
SHOW INDEX FROM transfer_fee_details;

-- =====================================================
-- ROLLBACK COMMANDS (if needed)
-- =====================================================

-- To rollback Step 1, uncomment and run these commands:
/*
ALTER TABLE loan_case_invoice_main DROP COLUMN transferred_pfee_amt;
ALTER TABLE loan_case_invoice_main DROP COLUMN transferred_sst_amt;
ALTER TABLE loan_case_invoice_main DROP COLUMN transferred_to_office_bank;
ALTER TABLE loan_case_invoice_main DROP COLUMN pfee1_inv;
ALTER TABLE loan_case_invoice_main DROP COLUMN pfee2_inv;
ALTER TABLE loan_case_invoice_main DROP COLUMN sst_inv;
ALTER TABLE loan_case_invoice_main DROP COLUMN bln_invoice;
*/

-- To rollback Step 2, uncomment and run these commands:
/*
ALTER TABLE transfer_fee_details DROP COLUMN loan_case_invoice_main_id;
*/ 
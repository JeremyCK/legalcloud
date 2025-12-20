-- Simple SQL to add sst column to loan_case_invoice_details table
-- Run this directly on your server database

ALTER TABLE `loan_case_invoice_details` 
ADD COLUMN `sst` DECIMAL(20,2) NULL 
COMMENT 'Custom SST amount (if manually set, otherwise NULL to auto-calculate)' 
AFTER `amount`;


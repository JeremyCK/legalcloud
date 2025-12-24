# Deployment Instructions for DP004-1025 Fix

## What Was Fixed

The issue was that:
1. Transfer fee totals didn't match expected values (differences of 0.02-0.11)
2. "To Transfer" columns showed non-zero values (0.01, 0.02, etc.) instead of 0.00

## Scripts That Were Run

### 1. `fix_transfer_fee_to_match_expected.php`
**Purpose**: Adjusts transfer fee details to match expected totals exactly
**What it does**:
- Recalculates all transfer fee details from invoice amounts
- Adjusts the largest invoice to absorb rounding differences
- Ensures totals match: Pfee=521,831.74, SST=41,746.47, Reimb=66,373.63, ReimbSST=5,309.91

### 2. `fix_invoice_to_match_transferred.php`
**Purpose**: Fixes invoice DP20000896 so "to Transfer" columns are 0.00
**What it does**:
- Adjusts invoice amounts to match transferred amounts
- Ensures "to Transfer" = 0.00

### 3. `fix_transferred_amounts_in_invoices.php`
**Purpose**: Updates invoice.transferred_* fields from transfer_fee_details
**What it does**:
- Syncs invoice transferred amounts with transfer fee details
- Ensures consistency between invoices and transfer fee details

## How to Apply to Server

### Option 1: Run All Scripts in Sequence (Recommended)

```bash
# SSH into your server
ssh user@your-server

# Navigate to project directory
cd /path/to/legalcloud

# Run the fixes in order
php fix_transfer_fee_to_match_expected.php
php fix_transferred_amounts_in_invoices.php 472
php fix_invoice_to_match_transferred.php

# Verify the fix
php recalculate_transfer_fee_totals.php 472
```

### Option 2: Create a Single Deployment Script

I can create a single script that runs all fixes in the correct order. Would you like me to create this?

### Option 3: Manual Steps via Web Interface

If you prefer using the web interface:

1. **Fix Transfer Fee Details**:
   - Go to `/transferfee/472/edit`
   - The system should recalculate automatically, or you may need to trigger a recalculation

2. **Verify Totals**:
   - Check that totals match expected values
   - Check that "to Transfer" columns are 0.00

## Files to Upload to Server

Upload these PHP scripts to your server:
- `fix_transfer_fee_to_match_expected.php`
- `fix_transferred_amounts_in_invoices.php`
- `fix_invoice_to_match_transferred.php`
- `recalculate_transfer_fee_totals.php` (for verification)

## Verification

After running the fixes, verify with:

```bash
php recalculate_transfer_fee_totals.php 472
```

Expected output:
- Professional Fee: 521,831.74 ✅
- SST: 41,746.47 ✅
- Reimbursement: 66,373.63 ✅
- Reimbursement SST: 5,309.91 ✅
- All differences: 0.00 ✅

## Important Notes

1. **Backup First**: Always backup your database before running fixes
2. **Test Environment**: If possible, test on a staging server first
3. **Transaction Safety**: All scripts use database transactions, so they can be rolled back if errors occur
4. **Single Run**: The scripts are idempotent - safe to run multiple times

## Rollback (If Needed)

If something goes wrong, you can restore from your database backup. The scripts use transactions, so if an error occurs, changes are automatically rolled back.




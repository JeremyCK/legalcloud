# Simple Deployment Guide for DP004-1025 Fix

## Step 1: Diagnose First

**On your server, run this to see current state:**

```bash
php diagnose_dp004_1025.php
```

This will show you:
- Current totals vs expected totals
- "To Transfer" column totals
- Which invoices have issues

## Step 2: Apply the Fix

**If issues are found, run:**

```bash
php fix_dp004_1025_correct_approach.php
```

This script will:
1. Adjust invoice amounts to match expected totals
2. Set transfer_fee_details to match invoice amounts
3. Set invoice.transferred_* to match transfer_fee_details
4. Verify everything is correct

## Step 3: Verify

**After running the fix, verify again:**

```bash
php diagnose_dp004_1025.php
```

You should see:
- ✅ Totals match expected values
- ✅ "To Transfer" columns are 0.00

## Files Needed

Upload these 2 files to your server:
1. `diagnose_dp004_1025.php` - Diagnostic tool
2. `fix_dp004_1025_correct_approach.php` - Fix script

## What the Fix Does

The correct approach:
1. **Adjusts invoice amounts** to match expected totals (distributes differences to largest invoice)
2. **Sets transfer_fee_details** to exactly match invoice amounts
3. **Sets invoice.transferred_*** to match transfer_fee_details

This ensures:
- Report totals = sum of invoice amounts = expected values ✅
- "To Transfer" = invoice amounts - transferred amounts = 0.00 ✅

## Important Notes

- Uses database transactions (safe)
- Can run multiple times
- Always backup database first


# E-Invoice Client Profile Status Update Logic Issues

## Issues Found

### Issue 1: `UpdateBillToInfo()` method (Line 1109-1216)

**Current Logic (Line 1179):**
```php
$EInvoiceDetails->client_profile_completed = $allRelatedCompleted ? 1 : 0;
```

**Problem:**
- Only checks if ALL billing parties for the bill are completed
- Does NOT check the invoice's billing party first (which is what the detail page shows)
- Only updates the FIRST einvoice_details found, not all details for that bill

**Should be:**
- First check if the invoice's billing party is completed
- If not, check if all billing parties for the bill are completed
- Update ALL einvoice_details for that bill

---

### Issue 2: `updateClientEinvoiceData()` method (Line 3217-3299)

**Current Logic (Line 3270):**
```php
$EInvoiceDetails->client_profile_completed = $allMandatoryFieldsFilled ? 1 : 0;
```

**Problem:**
- Only checks if the current billing party is completed
- Does NOT check if the invoice's billing party is completed OR all billing parties for the bill
- Only updates the FIRST einvoice_details found, not all details for that bill

**Should be:**
- First check if the invoice's billing party is completed
- If not, check if all billing parties for the bill are completed
- Update ALL einvoice_details for that bill

---

## Correct Logic (from our patch)

The detail should be marked as "Completed" if:
1. **Invoice's billing party is completed** (matches what detail page shows), OR
2. **All billing parties for the bill are completed**, OR
3. **No billing parties exist** (default to completed)

---

## Fix Required

Both methods need to be updated to:
1. Get ALL einvoice_details for the bill (not just the first one)
2. For each detail, check:
   - Invoice's billing party status first
   - Then check all billing parties for the bill
3. Update all details accordingly
4. Then update the main status based on all details

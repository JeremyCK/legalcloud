# Troubleshooting: Edit Icons Not Showing for pfee, sst, reimb, reimb sst

## Common Issues and Solutions

### 1. **User Permissions**
The edit icons only show if:
- User role is `admin`, `maker`, OR `account`
- Transfer fee is NOT reconciled (`is_recon != '1'`)

**Check:**
```php
// In the view, check:
@if(in_array(auth()->user()->menuroles, ['admin', 'maker', 'account']) && $TransferFeeMain->is_recon != '1')
    // Edit icon should appear here
@endif
```

**Solution:**
- Verify user's `menuroles` field in database
- Check if `$TransferFeeMain->is_recon == '1'` (if yes, icons won't show)

### 2. **Missing Data Attributes**
The edit icons need these data attributes:
- `data-detail-id="{{ $detail->id }}"`
- `data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"`
- `data-bill-id="{{ $detail->loan_case_main_bill_id }}"` ⚠️ **IMPORTANT - Recently added**

**Check in browser console:**
```javascript
// Inspect the icon element
$('.edit-pfee').first().data()
// Should show: {detailId: ..., invoiceId: ..., billId: ..., ...}
```

**Solution:**
- Ensure all data attributes are present in the HTML
- Check that `$detail->loan_case_main_bill_id` exists

### 3. **CSS/Display Issues**
Icons might be hidden by CSS or not visible.

**Check:**
```css
/* In browser DevTools, check if icon has: */
.fa-pencil {
    display: none; /* ❌ Problem */
    visibility: hidden; /* ❌ Problem */
    opacity: 0; /* ❌ Problem */
    color: transparent; /* ❌ Problem */
}
```

**Solution:**
- Check for conflicting CSS rules
- Verify Font Awesome is loaded: `<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">`
- Check icon has inline style: `style="cursor: pointer; color: #007bff; font-size: 11px;"`

### 4. **JavaScript Not Loaded**
The click handlers are attached via jQuery event delegation.

**Check in browser console:**
```javascript
// Check if jQuery is loaded
typeof jQuery !== 'undefined' // Should be true

// Check if handlers are attached
$(document).off('click', '.edit-pfee').on('click', '.edit-pfee', function() {
    console.log('Handler attached');
});

// Test click
$('.edit-pfee').first().click(); // Should trigger handler
```

**Solution:**
- Ensure jQuery is loaded before the script
- Check for JavaScript errors in console
- Verify the script block is inside `@section('content')` or loaded properly

### 5. **View Cache**
Laravel might be serving cached view.

**Solution:**
```bash
php artisan view:clear
php artisan cache:clear
```

### 6. **File Not Updated on Server**
The view file might not be uploaded to server.

**Check:**
- Verify `resources/views/dashboard/transfer-fee-v3/edit.blade.php` is the latest version
- Check file modification date
- Compare with local version

### 7. **Missing Font Awesome Icon**
The icon class `fa fa-pencil` might not be rendering.

**Check:**
```html
<!-- Should see pencil icon, not empty space or square -->
<i class="fa fa-pencil"></i>
```

**Solution:**
- Verify Font Awesome CSS is loaded
- Check Font Awesome version compatibility
- Try different icon: `fa fa-edit` or `fa fa-pencil-square-o`

## Quick Diagnostic Steps

### Step 1: Check HTML Source
View page source and search for `edit-pfee`. You should see:
```html
<i class="fa fa-pencil edit-pfee ml-1" 
   style="cursor: pointer; color: #007bff; font-size: 11px;" 
   data-detail-id="123"
   data-invoice-id="456"
   data-bill-id="789"
   data-pfee1="100.00"
   data-pfee2="200.00"
   title="Edit Professional Fee"></i>
```

### Step 2: Check Browser Console
Open browser DevTools (F12) and check:
- No JavaScript errors
- jQuery is loaded
- Font Awesome CSS is loaded

### Step 3: Test Click Handler
In browser console:
```javascript
// Check if element exists
$('.edit-pfee').length // Should be > 0

// Check if click works
$('.edit-pfee').first().click(); // Should show input fields

// Check data attributes
$('.edit-pfee').first().data('detail-id') // Should return number
$('.edit-pfee').first().data('bill-id') // Should return number (not undefined)
```

### Step 4: Check User Permissions
In browser console or Laravel tinker:
```php
// Check current user
auth()->user()->menuroles // Should be 'admin', 'maker', or 'account'

// Check transfer fee status
$TransferFeeMain->is_recon // Should be '0' or null (not '1')
```

## Recent Fixes Applied

1. ✅ Added `data-bill-id` attribute to all edit icons (pfee, sst, reimb, reimb-sst)
2. ✅ Updated JavaScript handlers to pass `bill_id` to AJAX calls
3. ✅ Updated `editAmountInline()` function to accept and use `billId` parameter

## Files to Check

1. `resources/views/dashboard/transfer-fee-v3/edit.blade.php`
   - Lines ~342-349: pfee edit icon
   - Lines ~363-367: sst edit icon
   - Lines ~381-385: reimb edit icon
   - Lines ~399-403: reimb-sst edit icon
   - Lines ~3417-3628: JavaScript handlers

2. Check if these lines exist:
   - `data-bill-id="{{ $detail->loan_case_main_bill_id }}"` on all edit icons
   - `const billId = $icon.data('bill-id');` in JavaScript handlers
   - `bill_id: billId,` in AJAX data

## Still Not Working?

If icons still don't appear after checking all above:

1. **Clear all caches:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan optimize:clear
   ```

2. **Hard refresh browser:**
   - Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)

3. **Check server logs:**
   ```bash
   tail -f storage/logs/laravel-$(date +%Y-%m-%d).log
   ```

4. **Verify file permissions:**
   ```bash
   ls -la resources/views/dashboard/transfer-fee-v3/edit.blade.php
   ```

5. **Compare with working environment:**
   - Check if it works on local/staging
   - Compare file contents
   - Check database structure differences


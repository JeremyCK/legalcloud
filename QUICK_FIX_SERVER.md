# Quick Fix: Add Edit Icons Manually on Server

Since "total amt" edit icon IS showing, we know:
- ✅ Permissions are correct
- ✅ @foreach loop is working
- ✅ View file is being rendered

The issue: pfee/sst/reimb/reimb-sst icons are missing from the server file.

## Quick Fix on Server

### Step 1: Find the Working "Total Amt" Icon
In the view file, search for `edit-total-amt`. You'll see this structure:

```blade
<span class="total-amt-display" ...>
    {{ number_format(...) }}
</span>
@if(in_array(auth()->user()->menuroles, ['admin', 'maker', 'account']) && $TransferFeeMain->is_recon != '1')
    <i class="fa fa-pencil edit-total-amt ml-1" 
       style="cursor: pointer; color: #007bff; font-size: 11px;" 
       data-detail-id="{{ $detail->id }}"
       data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
       title="Edit Total Amount"></i>
@endif
```

### Step 2: Copy This Structure for Each Column

#### For "pfee" column:
Find this:
```blade
<span class="pfee-display" 
      data-detail-id="{{ $detail->id }}" 
      data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
      data-pfee1="{{ $detail->pfee1_inv ?? 0 }}"
      data-pfee2="{{ $detail->pfee2_inv ?? 0 }}"
      data-original-value="{{ ($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0) }}">
    {{ number_format(($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0), 2) }}
</span>
```

Add RIGHT AFTER (before `</div>`):
```blade
@if(in_array(auth()->user()->menuroles, ['admin', 'maker', 'account']) && $TransferFeeMain->is_recon != '1')
    <i class="fa fa-pencil edit-pfee ml-1" 
       style="cursor: pointer; color: #007bff; font-size: 11px;" 
       data-detail-id="{{ $detail->id }}"
       data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
       data-bill-id="{{ $detail->loan_case_main_bill_id }}"
       data-pfee1="{{ $detail->pfee1_inv ?? 0 }}"
       data-pfee2="{{ $detail->pfee2_inv ?? 0 }}"
       title="Edit Professional Fee"></i>
@endif
```

#### For "sst" column:
Find this:
```blade
<span class="sst-display" 
      data-detail-id="{{ $detail->id }}" 
      data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
      data-original-value="{{ $detail->sst_inv ?? 0 }}">
    {{ number_format($detail->sst_inv ?? 0, 2) }}
</span>
```

Add RIGHT AFTER:
```blade
@if(in_array(auth()->user()->menuroles, ['admin', 'maker', 'account']) && $TransferFeeMain->is_recon != '1')
    <i class="fa fa-pencil edit-sst ml-1" 
       style="cursor: pointer; color: #007bff; font-size: 11px;" 
       data-detail-id="{{ $detail->id }}"
       data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
       data-bill-id="{{ $detail->loan_case_main_bill_id }}"
       title="Edit SST"></i>
@endif
```

#### For "reimb" column:
Find this:
```blade
<span class="reimb-display" 
      data-detail-id="{{ $detail->id }}" 
      data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
      data-original-value="{{ $detail->reimbursement_amount ?? 0 }}">
    {{ number_format($detail->reimbursement_amount ?? 0, 2) }}
</span>
```

Add RIGHT AFTER:
```blade
@if(in_array(auth()->user()->menuroles, ['admin', 'maker', 'account']) && $TransferFeeMain->is_recon != '1')
    <i class="fa fa-pencil edit-reimb ml-1" 
       style="cursor: pointer; color: #007bff; font-size: 11px;" 
       data-detail-id="{{ $detail->id }}"
       data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
       data-bill-id="{{ $detail->loan_case_main_bill_id }}"
       title="Edit Reimbursement"></i>
@endif
```

#### For "reimb sst" column:
Find this:
```blade
<span class="reimb-sst-display" 
      data-detail-id="{{ $detail->id }}" 
      data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
      data-original-value="{{ $detail->reimbursement_sst ?? 0 }}">
    {{ number_format($detail->reimbursement_sst ?? 0, 2) }}
</span>
```

Add RIGHT AFTER:
```blade
@if(in_array(auth()->user()->menuroles, ['admin', 'maker', 'account']) && $TransferFeeMain->is_recon != '1')
    <i class="fa fa-pencil edit-reimb-sst ml-1" 
       style="cursor: pointer; color: #007bff; font-size: 11px;" 
       data-detail-id="{{ $detail->id }}"
       data-invoice-id="{{ $detail->loan_case_invoice_main_id }}"
       data-bill-id="{{ $detail->loan_case_main_bill_id }}"
       title="Edit Reimbursement SST"></i>
@endif
```

### Step 3: Clear Cache
```bash
php artisan view:clear
php artisan cache:clear
```

### Step 4: Test
Refresh the page and check if icons appear.

## Alternative: Use sed/awk to Add Icons

If you're comfortable with command line:

```bash
# Backup first
cp resources/views/dashboard/transfer-fee-v3/edit.blade.php resources/views/dashboard/transfer-fee-v3/edit.blade.php.backup2

# The icons should already be in the file if it was uploaded correctly
# If not, you'll need to manually add them using the structure above
```

## Verify Icons Were Added

After adding, check:
```bash
grep -c "edit-pfee" resources/views/dashboard/transfer-fee-v3/edit.blade.php
# Should return: 1 or more
```

If it returns 0, the icons weren't added correctly.





# Collected Amount Source - Transfer Fee Edit Page

## Overview
The "Collected amt" displayed in the transfer fee edit page (`/transferfee/{id}/edit`) comes from the `loan_case_bill_main.collected_amt` field in the database.

## Data Flow

### 1. Source: `loan_case_bill_main.collected_amt`
- **Table**: `loan_case_bill_main`
- **Field**: `collected_amt`
- **Location**: `app/Http/Controllers/TransferFeeV3Controller.php` line 343

```php
'b.collected_amt as bill_collected_amt',
```

### 2. How `collected_amt` is Updated

The `collected_amt` field in `loan_case_bill_main` is automatically calculated and updated from **Receipt Vouchers** (voucher_type = 4).

#### Method 1: `VoucherControllerV2::updateTotalFigureBillTrust()`
- **File**: `app/Http/Controllers/VoucherControllerV2.php`
- **Lines**: 778-812
- **Logic**:
  ```php
  // Sum all receipt vouchers (voucher_type = 4) for the bill
  $total_bill_sum = DB::table('voucher_main as v')
      ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
      ->where('v.voucher_type', 4)  // Receipt vouchers
      ->whereNotIn('v.account_approval', [2])  // Exclude rejected
      ->where('v.case_bill_main_id', '=', $voucherMain->case_bill_main_id)
      ->where('v.status', '<>', 99)
      ->where('vd.status', '<>', 99)
      ->sum('amount');
  
  // Update the bill's collected_amt
  LoanCaseBillMain::where('id', $voucherMain->case_bill_main_id)
      ->update(['collected_amt' => $total_bill_sum]);
  ```

#### Method 2: `CaseController::updateBillandCaseFigure()`
- **File**: `app/Http/Controllers/CaseController.php`
- **Lines**: 7124-7167
- **Logic**:
  ```php
  // Sum all approved receipt vouchers (status = 4) for the bill
  $bill_receive = DB::table('voucher_main as v')
      ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
      ->where('vd.status', '=', 4)  // Approved vouchers
      ->where('v.case_bill_main_id', '=', $bill_id)
      ->where('v.status', '<>', 99)
      ->get();
  
  $bill_receive_sum = 0;
  for ($i = 0; $i < count($bill_receive); $i++) {
      $bill_receive_sum += $bill_receive[$i]->amount;
  }
  
  // Update the bill's collected_amt
  $LoanCaseBillMain->collected_amt = $bill_receive_sum;
  $LoanCaseBillMain->save();
  ```

### 3. Display in Transfer Fee Edit Page

#### Step 1: Query from Database
- **File**: `app/Http/Controllers/TransferFeeV3Controller.php`
- **Method**: `getInvoiceList()` (around line 320)
- **Query**: Joins `loan_case_invoice_main` with `loan_case_bill_main` and selects `b.collected_amt as bill_collected_amt`

#### Step 2: Divide by Invoice Count
- **File**: `app/Http/Controllers/TransferFeeV3Controller.php`
- **Lines**: 1062-1065
- **Logic**: When a bill has multiple invoices, the collected amount is divided equally among all invoices
  ```php
  // Calculate Collected amt from bill collected amount (divided equally)
  $totalAmount = $detail->bill_collected_amt ?? 0;
  $calculatedCollectedAmount = round($totalAmount / $invoiceCount, 2);
  $detail->bill_collected_amt_divided = $calculatedCollectedAmount;
  ```

#### Step 3: Display in View
- **File**: `resources/views/dashboard/transfer-fee-v3/edit.blade.php`
- **Line**: 2898 (JavaScript calculation)
  ```javascript
  const collectedAmount = invoices.reduce((sum, invoice) => 
      sum + parseFloat(invoice.bill_collected_amt_divided || 0), 0);
  ```

## Summary

**Collected Amount = Sum of all Receipt Vouchers (voucher_type = 4) for that bill**

### Key Points:
1. ✅ **Source**: `loan_case_bill_main.collected_amt`
2. ✅ **Updated by**: Receipt vouchers (voucher_type = 4)
3. ✅ **Calculation**: Sum of all approved receipt voucher amounts for the bill
4. ✅ **Display**: Divided equally among invoices if bill has multiple invoices
5. ✅ **Auto-updated**: When vouchers are created/updated/deleted

### When is it Updated?
- When a receipt voucher is created/approved
- When a receipt voucher is updated
- When a receipt voucher is deleted
- When voucher approval status changes
- When `VoucherControllerV2::updateTotalFigureBillTrust()` is called
- When `CaseController::updateBillandCaseFigure()` is called


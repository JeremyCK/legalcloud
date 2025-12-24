# Invoice Verification Guide

## Overview
A verification function has been added to check if all 79 invoices have been fixed correctly. This function compares the stored values in the database with the calculated values from invoice details.

## How to Use

### Option 1: Using the Web Interface (Recommended)

1. **Access the Account Tool**
   - Navigate to: `/account-tool` or `/invoice-fix`
   - You should see the invoice fix interface

2. **Use Browser Console or Postman**
   - Open browser developer tools (F12)
   - Go to Console tab
   - Run this JavaScript code:

```javascript
// Copy all invoice numbers from invoice_numbers_simple.txt
const invoiceNumbers = "DP20000817,DP20000826,DP20000829,DP20000840,DP20000844,DP20000845,DP20000849,DP20000964,DP20000965,DP20000966,DP20000869,DP20000873,DP20000874,DP20000875,DP20000877,DP20000878,DP20000879,DP20000941,DP20000942,DP20000943,DP20000884,DP20000885,DP20000986,DP20000987,DP20000870,DP20000896,DP20000897,DP20000871,DP20000891,DP20000892,DP20000934,DP20000935,DP20000936,DP20000944,DP20000945,DP20000948,DP20000949,DP20000952,DP20000953,DP20000982,DP20000995,DP20000998,DP20001168,DP20001170,DP20001000,DP20001012,DP20001013,DP20001014,DP20001015,DP20001025,DP20001026,DP20001035,DP20001040,DP20001041,DP20001042,DP20001057,DP20001058,DP20001065,DP20001066,DP20001067,DP20001070,DP20001071,DP20001074,DP20001085,DP20001089,DP20001090,DP20001091,DP20001092,DP20001095,DP20001096,DP20001110,DP20001111,DP20001119,DP20001130,DP20001142,DP20001145,DP20001146,DP20001147,DP20001148,DP20001149,DP20001150,DP20001151,DP20001155,DP20001158,DP20001159,DP20001160";

fetch('/invoice-fix/verify', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        invoice_numbers: invoiceNumbers
    })
})
.then(response => response.json())
.then(data => {
    console.log('Verification Summary:', data.summary);
    console.log('Results:', data.results);
    
    // Show summary
    const fixed = data.results.filter(r => r.status === 'fixed').length;
    const issues = data.results.filter(r => r.status === 'has_issues').length;
    const notFound = data.results.filter(r => r.status === 'not_found').length;
    
    console.log(`\n✅ Fixed: ${fixed}`);
    console.log(`⚠️  With Issues: ${issues}`);
    console.log(`❌ Not Found: ${notFound}`);
    
    // Show invoices with issues
    if (issues > 0) {
        console.log('\nInvoices with issues:');
        data.results.filter(r => r.status === 'has_issues').forEach(r => {
            console.log(`- ${r.invoice_no}: ${r.message}`);
            r.issues.forEach(issue => {
                console.log(`  ${issue.field}: Stored=${issue.stored}, Calculated=${issue.calculated}, Diff=${issue.difference}`);
            });
        });
    }
})
.catch(error => console.error('Error:', error));
```

### Option 2: Using Postman or cURL

**POST Request:**
```
URL: https://legal-cloud.co/invoice-fix/verify
Method: POST
Headers:
  Content-Type: application/json
  X-CSRF-TOKEN: [your CSRF token]
Body (JSON):
{
  "invoice_numbers": "DP20000817,DP20000826,DP20000829,..."
}
```

**cURL Command:**
```bash
curl -X POST https://legal-cloud.co/invoice-fix/verify \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: [your token]" \
  -d '{"invoice_numbers": "DP20000817,DP20000826,DP20000829,..."}'
```

### Option 3: Using Laravel Tinker

```php
php artisan tinker

$controller = new \App\Http\Controllers\InvoiceFixController();
$request = new \Illuminate\Http\Request();
$request->merge([
    'invoice_numbers' => 'DP20000817,DP20000826,DP20000829,DP20000840,DP20000844,DP20000845,DP20000849,DP20000964,DP20000965,DP20000966,DP20000869,DP20000873,DP20000874,DP20000875,DP20000877,DP20000878,DP20000879,DP20000941,DP20000942,DP20000943,DP20000884,DP20000885,DP20000986,DP20000987,DP20000870,DP20000896,DP20000897,DP20000871,DP20000891,DP20000892,DP20000934,DP20000935,DP20000936,DP20000944,DP20000945,DP20000948,DP20000949,DP20000952,DP20000953,DP20000982,DP20000995,DP20000998,DP20001168,DP20001170,DP20001000,DP20001012,DP20001013,DP20001014,DP20001015,DP20001025,DP20001026,DP20001035,DP20001040,DP20001041,DP20001042,DP20001057,DP20001058,DP20001065,DP20001066,DP20001067,DP20001070,DP20001071,DP20001074,DP20001085,DP20001089,DP20001090,DP20001091,DP20001092,DP20001095,DP20001096,DP20001110,DP20001111,DP20001119,DP20001130,DP20001142,DP20001145,DP20001146,DP20001147,DP20001148,DP20001149,DP20001150,DP20001151,DP20001155,DP20001158,DP20001159,DP20001160'
]);
$response = $controller->verifyInvoices($request);
$data = json_decode($response->getContent(), true);
print_r($data);
```

## Understanding the Results

### Response Structure
```json
{
  "success": true,
  "summary": {
    "total": 79,
    "fixed": 75,
    "with_issues": 4,
    "not_found": 0
  },
  "results": [
    {
      "invoice_no": "DP20000817",
      "case_ref_no": "DP/T/ZU/...",
      "status": "fixed",
      "message": "All values match calculated values",
      "stored_values": {
        "pfee1_inv": 1234.56,
        "pfee2_inv": 5678.90,
        "sst_inv": 686.31,
        ...
      },
      "calculated_values": {
        "pfee1": 1234.56,
        "pfee2": 5678.90,
        "sst": 686.31,
        ...
      },
      "issues": []
    }
  ]
}
```

### Status Values
- **`fixed`**: All stored values match calculated values (within 0.01 tolerance)
- **`has_issues`**: One or more fields have mismatches
- **`not_found`**: Invoice not found in database
- **`error`**: Error occurred during verification

### Issue Details
For invoices with issues, each issue shows:
- **`field`**: The field name (e.g., `pfee1_inv`, `sst_inv`)
- **`stored`**: Current value in database
- **`calculated`**: Expected value from invoice details
- **`difference`**: The difference between stored and calculated

## What Gets Verified

The verification checks these fields:
1. **pfee1_inv** - Professional Fee 1
2. **pfee2_inv** - Professional Fee 2
3. **sst_inv** - SST amount
4. **reimbursement_amount** - Reimbursement amount
5. **reimbursement_sst** - Reimbursement SST
6. **amount** - Total invoice amount

## Tolerance
A tolerance of **0.01** (1 cent) is allowed for rounding differences. Differences larger than this will be flagged as issues.

## Next Steps

1. **If all invoices show `fixed`**: ✅ All corrections are complete!
2. **If some show `has_issues`**: 
   - Review the issue details
   - Re-run the fix for those specific invoices
   - Verify again
3. **If invoices show `not_found`**: 
   - Check if invoice numbers are correct
   - Verify invoices exist in the database




<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\EInvoiceContoller;

echo "=== TESTING updateInvoiceValue BUG ===\n\n";

$billId = 8377;

// Get the current state of Letter of Authorisation items
echo "=== BEFORE ANY CHANGES ===\n";
$letterAuthItems = DB::table('loan_case_invoice_details as ild')
    ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
    ->leftJoin('loan_case_invoice_main as im', 'ild.invoice_main_id', '=', 'im.id')
    ->where('im.loan_case_main_bill_id', $billId)
    ->where('ai.name', 'LIKE', '%Letter of Authorisation%')
    ->where('ild.status', '<>', 99)
    ->select('ild.*', 'ai.name as item_name', 'im.invoice_no')
    ->get();

foreach ($letterAuthItems as $item) {
    echo "Invoice {$item->invoice_no} - Letter of Authorisation:\n";
    echo "  ID: {$item->id}\n";
    echo "  Amount: {$item->amount}\n";
    echo "  Original Amount: {$item->ori_invoice_amt}\n";
    echo "  Bill ID: {$item->loan_case_main_bill_id}\n\n";
}

// Get party count
$partyCount = EInvoiceContoller::getPartyCount($billId);
echo "Party Count: {$partyCount}\n\n";

// Simulate what happens in updateInvoiceValue
echo "=== SIMULATING updateInvoiceValue LOGIC ===\n";
echo "The problematic line in updateInvoiceValue:\n";
echo "LoanCaseInvoiceDetails::where('loan_case_main_bill_id', \$billId)->update(['amount' => DB::raw('ori_invoice_amt / {$partyCount}')]);\n\n";

echo "This means ALL items in the bill get updated to: ori_invoice_amt / {$partyCount}\n\n";

// Show what this would do to each Letter of Authorisation item
foreach ($letterAuthItems as $item) {
    $newAmount = $item->ori_invoice_amt / $partyCount;
    echo "Invoice {$item->invoice_no} - Letter of Authorisation:\n";
    echo "  Original Amount: {$item->ori_invoice_amt}\n";
    echo "  Current Amount: {$item->amount}\n";
    echo "  Would become: {$item->ori_invoice_amt} / {$partyCount} = {$newAmount}\n";
    
    if (abs($item->amount - $newAmount) > 0.01) {
        echo "  ⚠️  WOULD CHANGE!\n";
    } else {
        echo "  ✅ No change\n";
    }
    echo "\n";
}

// Let's check what the original amounts are for all items
echo "=== CHECKING ALL ORIGINAL AMOUNTS ===\n";
$allItems = DB::table('loan_case_invoice_details as ild')
    ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
    ->leftJoin('loan_case_invoice_main as im', 'ild.invoice_main_id', '=', 'im.id')
    ->where('im.loan_case_main_bill_id', $billId)
    ->where('ild.status', '<>', 99)
    ->select('ild.*', 'ai.name as item_name', 'im.invoice_no')
    ->orderBy('im.invoice_no')
    ->orderBy('ild.id')
    ->get();

$invoiceGroups = $allItems->groupBy('invoice_no');

foreach ($invoiceGroups as $invoiceNo => $items) {
    echo "Invoice {$invoiceNo}:\n";
    foreach ($items as $item) {
        if (strpos($item->item_name, 'Letter of Authorisation') !== false) {
            echo "  {$item->item_name}: Amount={$item->amount}, Original={$item->ori_invoice_amt}\n";
        }
    }
    echo "\n";
}

echo "=== THE BUG EXPLANATION ===\n";
echo "The updateInvoiceValue method has a critical bug:\n";
echo "1. It updates the ori_invoice_amt for ONE specific item\n";
echo "2. Then it updates ALL items in the bill using: ori_invoice_amt / party_count\n";
echo "3. This means ALL items get recalculated based on their original amounts\n";
echo "4. If the original amounts are different between invoices, this creates inconsistency\n\n";

echo "In this case:\n";
echo "- DP20000813 Letter of Authorisation: ori_invoice_amt = 42.59, so amount = 42.59/2 = 21.30\n";
echo "- DP20000814 Letter of Authorisation: ori_invoice_amt = 100.00, so amount = 100.00/2 = 50.00\n";
echo "This is why DP20000814 shows 50.00 instead of 21.30!\n";

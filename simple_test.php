<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== SIMPLE TEST: Current Letter of Authorisation amounts ===\n\n";

$billId = 8377;

// Get current state
$items = DB::table('loan_case_invoice_details as ild')
    ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
    ->leftJoin('loan_case_invoice_main as im', 'ild.invoice_main_id', '=', 'im.id')
    ->where('im.loan_case_main_bill_id', $billId)
    ->where('ai.name', 'LIKE', '%Letter of Authorisation%')
    ->where('ild.status', '<>', 99)
    ->select('ild.*', 'ai.name as item_name', 'im.invoice_no')
    ->get();

echo "Found " . $items->count() . " Letter of Authorisation items:\n\n";

foreach ($items as $item) {
    echo "Invoice: {$item->invoice_no}\n";
    echo "  ID: {$item->id}\n";
    echo "  Amount: {$item->amount}\n";
    echo "  Original Amount: {$item->ori_invoice_amt}\n";
    echo "  Account Item ID: {$item->account_item_id}\n\n";
}

// Check if there are any other items that might be confused with Letter of Authorisation
echo "=== CHECKING FOR SIMILAR ITEMS ===\n";
$similarItems = DB::table('loan_case_invoice_details as ild')
    ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
    ->leftJoin('loan_case_invoice_main as im', 'ild.invoice_main_id', '=', 'im.id')
    ->where('im.loan_case_main_bill_id', $billId)
    ->where(function($query) {
        $query->where('ai.name', 'LIKE', '%Letter%')
              ->orWhere('ai.name', 'LIKE', '%Authorisation%')
              ->orWhere('ai.name', 'LIKE', '%Authorization%');
    })
    ->where('ild.status', '<>', 99)
    ->select('ild.*', 'ai.name as item_name', 'im.invoice_no')
    ->get();

echo "Found " . $similarItems->count() . " similar items:\n\n";

foreach ($similarItems as $item) {
    echo "Invoice: {$item->invoice_no}\n";
    echo "  Item: {$item->item_name}\n";
    echo "  ID: {$item->id}\n";
    echo "  Amount: {$item->amount}\n";
    echo "  Original Amount: {$item->ori_invoice_amt}\n\n";
}

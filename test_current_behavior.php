<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TESTING CURRENT BEHAVIOR AFTER FIX ===\n\n";

$billId = 8377;

// Get current state of Letter of Authorisation items
echo "=== CURRENT STATE ===\n";
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

// Let's check if there are multiple Letter of Authorisation items
echo "=== CHECKING FOR DUPLICATE ITEMS ===\n";
$allLetterAuthItems = DB::table('loan_case_invoice_details as ild')
    ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
    ->leftJoin('loan_case_invoice_main as im', 'ild.invoice_main_id', '=', 'im.id')
    ->where('im.loan_case_main_bill_id', $billId)
    ->where('ai.name', 'LIKE', '%Letter of Authorisation%')
    ->where('ild.status', '<>', 99)
    ->select('ild.*', 'ai.name as item_name', 'im.invoice_no')
    ->get();

echo "Total Letter of Authorisation items found: " . $allLetterAuthItems->count() . "\n\n";

foreach ($allLetterAuthItems as $item) {
    echo "Invoice {$item->invoice_no}:\n";
    echo "  ID: {$item->id}\n";
    echo "  Amount: {$item->amount}\n";
    echo "  Original Amount: {$item->ori_invoice_amt}\n";
    echo "  Account Item ID: {$item->account_item_id}\n\n";
}

// Let's check if there are any other items that might be causing confusion
echo "=== CHECKING ALL ITEMS WITH 'AUTHORISATION' IN NAME ===\n";
$allAuthItems = DB::table('loan_case_invoice_details as ild')
    ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
    ->leftJoin('loan_case_invoice_main as im', 'ild.invoice_main_id', '=', 'im.id')
    ->where('im.loan_case_main_bill_id', $billId)
    ->where('ai.name', 'LIKE', '%Authorisation%')
    ->where('ild.status', '<>', 99)
    ->select('ild.*', 'ai.name as item_name', 'im.invoice_no')
    ->get();

foreach ($allAuthItems as $item) {
    echo "Invoice {$item->invoice_no}:\n";
    echo "  Item: {$item->item_name}\n";
    echo "  ID: {$item->id}\n";
    echo "  Amount: {$item->amount}\n";
    echo "  Original Amount: {$item->ori_invoice_amt}\n\n";
}

// Let's also check what happens when we simulate the old behavior
echo "=== SIMULATING OLD BEHAVIOR (BEFORE FIX) ===\n";
echo "If we were to run the old code that updates ALL items:\n\n";

$partyCount = 2; // We know this from previous tests

foreach ($allLetterAuthItems as $item) {
    $newAmount = $item->ori_invoice_amt / $partyCount;
    echo "Invoice {$item->invoice_no} - Letter of Authorisation:\n";
    echo "  Current Amount: {$item->amount}\n";
    echo "  Original Amount: {$item->ori_invoice_amt}\n";
    echo "  Would become (ori_invoice_amt / {$partyCount}): {$newAmount}\n";
    
    if (abs($item->amount - $newAmount) > 0.01) {
        echo "  ⚠️  WOULD CHANGE from {$item->amount} to {$newAmount}\n";
    } else {
        echo "  ✅ No change\n";
    }
    echo "\n";
}

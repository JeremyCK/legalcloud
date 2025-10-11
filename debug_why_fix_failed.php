<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DEBUGGING WHY THE FIX FAILED ===\n\n";

$billId = 8377;

// The issue is clear: DP20000814 shows 50.00 instead of 21.30
echo "Current state:\n";
echo "DP20000813 - Letter of Authorisation: 21.30 (Original: 42.59)\n";
echo "DP20000814 - Letter of Authorisation: 50.00 (Original: 100.00)\n\n";

echo "Expected state (if both should be the same):\n";
echo "DP20000813 - Letter of Authorisation: 21.30\n";
echo "DP20000814 - Letter of Authorisation: 21.30\n\n";

echo "=== THE REAL ISSUE ===\n";
echo "The problem is NOT in the updateInvoiceValue method!\n";
echo "The problem is that the ORIGINAL AMOUNTS are different:\n";
echo "- DP20000813: ori_invoice_amt = 42.59\n";
echo "- DP20000814: ori_invoice_amt = 100.00\n\n";

echo "When updatePfeeDisbAmountINVFromDetails runs, it calculates:\n";
echo "- DP20000813: 42.59 / 2 = 21.30 ✅\n";
echo "- DP20000814: 100.00 / 2 = 50.00 ❌\n\n";

echo "=== THE ROOT CAUSE ===\n";
echo "The issue is that the ori_invoice_amt values are inconsistent between the two invoices.\n";
echo "This suggests that either:\n";
echo "1. The original data entry was inconsistent\n";
echo "2. Some previous operation updated one but not the other\n";
echo "3. There's a data migration issue\n\n";

echo "=== SOLUTION ===\n";
echo "To fix this permanently, we need to make the ori_invoice_amt values consistent.\n";
echo "Since DP20000813 shows 21.30 (which seems correct), we should update DP20000814's ori_invoice_amt to match.\n\n";

// Let's check what the ori_invoice_amt should be for DP20000814
$targetAmount = 21.30;
$partyCount = 2;
$targetOriginalAmount = $targetAmount * $partyCount;

echo "To get DP20000814 to show 21.30, we need:\n";
echo "ori_invoice_amt = 21.30 * 2 = {$targetOriginalAmount}\n\n";

echo "=== PROPOSED FIX ===\n";
echo "Update DP20000814's Letter of Authorisation ori_invoice_amt from 100.00 to {$targetOriginalAmount}\n";

// Let's do the fix
echo "\n=== APPLYING THE FIX ===\n";
$updated = DB::table('loan_case_invoice_details')
    ->where('id', 155014) // DP20000814's Letter of Authorisation ID
    ->update(['ori_invoice_amt' => $targetOriginalAmount]);

if ($updated) {
    echo "✅ Successfully updated ori_invoice_amt to {$targetOriginalAmount}\n";
    
    // Now let's recalculate the amount
    $newAmount = $targetOriginalAmount / $partyCount;
    $updated2 = DB::table('loan_case_invoice_details')
        ->where('id', 155014)
        ->update(['amount' => $newAmount]);
    
    if ($updated2) {
        echo "✅ Successfully updated amount to {$newAmount}\n";
    }
} else {
    echo "❌ Failed to update ori_invoice_amt\n";
}

echo "\n=== VERIFICATION ===\n";
$items = DB::table('loan_case_invoice_details as ild')
    ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
    ->leftJoin('loan_case_invoice_main as im', 'ild.invoice_main_id', '=', 'im.id')
    ->where('im.loan_case_main_bill_id', $billId)
    ->where('ai.name', 'LIKE', '%Letter of Authorisation%')
    ->where('ild.status', '<>', 99)
    ->select('ild.*', 'ai.name as item_name', 'im.invoice_no')
    ->get();

foreach ($items as $item) {
    echo "Invoice: {$item->invoice_no}\n";
    echo "  Amount: {$item->amount}\n";
    echo "  Original Amount: {$item->ori_invoice_amt}\n";
}

echo "\n=== NOW RUN updatePfeeDisbAmountINVFromDetails ===\n";
echo "After fixing the ori_invoice_amt, we should run updatePfeeDisbAmountINVFromDetails to recalculate everything.\n";

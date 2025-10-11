<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\EInvoiceContoller;

echo "=== DETAILED ANALYSIS: DP20000813 and DP20000814 ===\n\n";

$billId = 8377;

// Get all invoice details for this bill
$allDetails = DB::table('loan_case_invoice_details as ild')
    ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
    ->leftJoin('loan_case_invoice_main as im', 'ild.invoice_main_id', '=', 'im.id')
    ->where('im.loan_case_main_bill_id', $billId)
    ->where('ild.status', '<>', 99)
    ->select('ild.*', 'ai.account_cat_id', 'ai.pfee1_item', 'ai.name as item_name', 'im.invoice_no')
    ->orderBy('im.invoice_no')
    ->orderBy('ild.id')
    ->get();

echo "=== ORIGINAL AMOUNTS vs CURRENT AMOUNTS ===\n";
echo "Item Name | Invoice | Original Amount | Current Amount | Difference | Account Category\n";
echo str_repeat("-", 100) . "\n";

$totalOriginal = 0;
$totalCurrent = 0;

foreach ($allDetails as $detail) {
    $difference = $detail->ori_invoice_amt - $detail->amount;
    $totalOriginal += $detail->ori_invoice_amt;
    $totalCurrent += $detail->amount;
    
    printf("%-30s | %-12s | %-15s | %-14s | %-10s | %d\n", 
        substr($detail->item_name, 0, 30),
        $detail->invoice_no,
        number_format($detail->ori_invoice_amt, 2),
        number_format($detail->amount, 2),
        number_format($difference, 2),
        $detail->account_cat_id
    );
}

echo str_repeat("-", 100) . "\n";
printf("%-30s | %-12s | %-15s | %-14s | %-10s |\n", 
    "TOTAL", 
    "", 
    number_format($totalOriginal, 2), 
    number_format($totalCurrent, 2), 
    number_format($totalOriginal - $totalCurrent, 2)
);

echo "\n=== THE ISSUE EXPLAINED ===\n";
echo "1. Original Total Amount: " . number_format($totalOriginal, 2) . "\n";
echo "2. Current Total Amount: " . number_format($totalCurrent, 2) . "\n";
echo "3. Party Count (Number of Invoices): 2\n";
echo "4. Expected Division: " . number_format($totalOriginal, 2) . " ÷ 2 = " . number_format($totalOriginal / 2, 2) . "\n";
echo "5. Actual Current Total: " . number_format($totalCurrent, 2) . "\n";
echo "6. Missing Amount: " . number_format($totalOriginal - $totalCurrent, 2) . "\n\n";

echo "=== WHAT HAPPENED ===\n";
echo "The system divided the original amounts by 2 (party count) but the current amounts don't match.\n";
echo "This suggests that either:\n";
echo "- The division wasn't applied correctly\n";
echo "- Some amounts were manually adjusted\n";
echo "- There's a calculation error in the system\n\n";

// Calculate what the amounts should be with SST
$sstRate = 8.0;
$totalWithSst = 0;
$totalPfee1 = 0;
$totalPfee2 = 0;
$totalSst = 0;

echo "=== CALCULATION WITH SST (8%) ===\n";
foreach ($allDetails as $detail) {
    if ($detail->account_cat_id == 1) {
        if ($detail->pfee1_item == 1) {
            $totalPfee1 += $detail->amount;
        } else {
            $totalPfee2 += $detail->amount;
        }
        $sstAmount = $detail->amount * ($sstRate / 100);
        $totalSst += $sstAmount;
        $totalWithSst += $detail->amount * (($sstRate / 100) + 1);
    } else {
        $totalWithSst += $detail->amount;
    }
}

echo "Total Pfee1: " . number_format($totalPfee1, 2) . "\n";
echo "Total Pfee2: " . number_format($totalPfee2, 2) . "\n";
echo "Total SST: " . number_format($totalSst, 2) . "\n";
echo "Total with SST: " . number_format($totalWithSst, 2) . "\n";
echo "Expected per Invoice: " . number_format($totalWithSst / 2, 2) . "\n\n";

// Get current invoice amounts
$invoices = DB::table('loan_case_invoice_main')
    ->where('loan_case_main_bill_id', $billId)
    ->where('status', '<>', 99)
    ->get();

echo "=== CURRENT INVOICE AMOUNTS ===\n";
foreach ($invoices as $invoice) {
    echo "Invoice {$invoice->invoice_no}: " . number_format($invoice->amount, 2) . "\n";
}
echo "Sum of Current Invoices: " . number_format($invoices->sum('amount'), 2) . "\n";
echo "Expected Sum: " . number_format($totalWithSst, 2) . "\n";
echo "Difference: " . number_format($invoices->sum('amount') - $totalWithSst, 2) . "\n";

?>

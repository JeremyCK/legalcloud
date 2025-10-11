<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\EInvoiceContoller;

echo "=== INVESTIGATING INVOICES DP20000813 and DP20000814 ===\n\n";

// 1. Find both invoices and their bill information
$invoices = DB::table('loan_case_invoice_main as im')
    ->leftJoin('loan_case_bill_main as b', 'im.loan_case_main_bill_id', '=', 'b.id')
    ->whereIn('im.invoice_no', ['DP20000813', 'DP20000814'])
    ->where('im.status', '<>', 99)
    ->select('im.*', 'b.sst_rate', 'b.total_amt_inv as bill_total_amt_inv', 'b.id as bill_id')
    ->get();

if ($invoices->isEmpty()) {
    echo "ERROR: No invoices found with those numbers\n";
    exit;
}

echo "=== INVOICE INFORMATION ===\n";
foreach ($invoices as $invoice) {
    echo "Invoice: {$invoice->invoice_no}\n";
    echo "  ID: {$invoice->id}\n";
    echo "  Bill ID: {$invoice->bill_id}\n";
    echo "  Current Amount: {$invoice->amount}\n";
    echo "  Current Pfee1: {$invoice->pfee1_inv}\n";
    echo "  Current Pfee2: {$invoice->pfee2_inv}\n";
    echo "  Current SST: {$invoice->sst_inv}\n";
    echo "  SST Rate: {$invoice->sst_rate}%\n";
    echo "  Bill Total Amt: {$invoice->bill_total_amt_inv}\n\n";
}

$billId = $invoices->first()->bill_id;

// 2. Get party count (number of invoices)
$partyCount = EInvoiceContoller::getPartyCount($billId);
echo "=== PARTY COUNT INFO ===\n";
echo "Party Count (Number of Invoices): {$partyCount}\n\n";

// 3. Get all invoices for this bill
$allInvoices = DB::table('loan_case_invoice_main')
    ->where('loan_case_main_bill_id', $billId)
    ->where('status', '<>', 99)
    ->get();

echo "=== ALL INVOICES FOR THIS BILL ===\n";
foreach ($allInvoices as $inv) {
    echo "Invoice: {$inv->invoice_no} - Amount: {$inv->amount}\n";
}
echo "Total Invoices: " . $allInvoices->count() . "\n\n";

// 4. Get all invoice details for this bill
$allDetails = DB::table('loan_case_invoice_details as ild')
    ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
    ->leftJoin('loan_case_invoice_main as im', 'ild.invoice_main_id', '=', 'im.id')
    ->where('im.loan_case_main_bill_id', $billId)
    ->where('ild.status', '<>', 99)
    ->select('ild.*', 'ai.account_cat_id', 'ai.pfee1_item', 'ai.name as item_name', 'im.invoice_no')
    ->get();

echo "=== INVOICE DETAILS BREAKDOWN ===\n";
$totalFromDetails = 0;
$totalPfee1FromDetails = 0;
$totalPfee2FromDetails = 0;
$totalSstFromDetails = 0;
$sstRate = $invoices->first()->sst_rate;

foreach ($allDetails as $detail) {
    echo "Invoice: {$detail->invoice_no}\n";
    echo "  Item: {$detail->item_name}\n";
    echo "  Amount: {$detail->amount}\n";
    echo "  Original Amount: {$detail->ori_invoice_amt}\n";
    echo "  Account Category: {$detail->account_cat_id}\n";
    echo "  Pfee1 Item: {$detail->pfee1_item}\n";
    
    if ($detail->account_cat_id == 1) {
        if ($detail->pfee1_item == 1) {
            $totalPfee1FromDetails += $detail->amount;
        } else {
            $totalPfee2FromDetails += $detail->amount;
        }
        $sstAmount = $detail->amount * ($sstRate / 100);
        $totalSstFromDetails += $sstAmount;
        $totalWithSst = $detail->amount * (($sstRate / 100) + 1);
        $totalFromDetails += $totalWithSst;
        echo "  SST Amount: " . round($sstAmount, 2) . "\n";
        echo "  Total with SST: " . round($totalWithSst, 2) . "\n";
    } else {
        $totalFromDetails += $detail->amount;
        echo "  Total (no SST): " . round($detail->amount, 2) . "\n";
    }
    echo "\n";
}

echo "=== CALCULATION SUMMARY ===\n";
echo "Total from Details: " . round($totalFromDetails, 2) . "\n";
echo "Total Pfee1 from Details: " . round($totalPfee1FromDetails, 2) . "\n";
echo "Total Pfee2 from Details: " . round($totalPfee2FromDetails, 2) . "\n";
echo "Total SST from Details: " . round($totalSstFromDetails, 2) . "\n";
echo "Party Count: {$partyCount}\n";
echo "Expected Invoice Amount: " . round($totalFromDetails / $partyCount, 2) . "\n\n";

// 5. Calculate current totals
$currentInvoiceTotal = $allInvoices->sum('amount');
$currentBillTotal = $invoices->first()->bill_total_amt_inv;

echo "=== CURRENT TOTALS ===\n";
echo "Current Invoice Total: " . round($currentInvoiceTotal, 2) . "\n";
echo "Current Bill Total: " . round($currentBillTotal, 2) . "\n";
echo "Calculated Bill Total: " . round($totalFromDetails, 2) . "\n\n";

// 6. Show differences
echo "=== DIFFERENCES ===\n";
echo "Invoice Total Difference: " . round($currentInvoiceTotal - ($totalFromDetails / $partyCount), 2) . "\n";
echo "Bill Total Difference: " . round($currentBillTotal - $totalFromDetails, 2) . "\n";

?>

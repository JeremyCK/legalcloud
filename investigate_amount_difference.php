<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== INVESTIGATING AMOUNT DIFFERENCE: DP20000813 vs DP20000814 ===\n\n";

$billId = 8377;

// Get both invoices
$invoices = DB::table('loan_case_invoice_main as im')
    ->leftJoin('loan_case_bill_main as b', 'im.loan_case_main_bill_id', '=', 'b.id')
    ->whereIn('im.invoice_no', ['DP20000813', 'DP20000814'])
    ->where('im.status', '<>', 99)
    ->select('im.*', 'b.sst_rate')
    ->orderBy('im.invoice_no')
    ->get();

echo "=== INVOICE INFORMATION ===\n";
foreach ($invoices as $invoice) {
    echo "Invoice: {$invoice->invoice_no}\n";
    echo "  ID: {$invoice->id}\n";
    echo "  Current Amount: {$invoice->amount}\n";
    echo "  SST Rate: {$invoice->sst_rate}%\n\n";
}

// Get all invoice details for both invoices
$allDetails = DB::table('loan_case_invoice_details as ild')
    ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
    ->leftJoin('loan_case_invoice_main as im', 'ild.invoice_main_id', '=', 'im.id')
    ->whereIn('im.invoice_no', ['DP20000813', 'DP20000814'])
    ->where('ild.status', '<>', 99)
    ->select('ild.*', 'ai.account_cat_id', 'ai.pfee1_item', 'ai.name as item_name', 'im.invoice_no', 'im.id as invoice_id')
    ->orderBy('im.invoice_no')
    ->orderBy('ild.id')
    ->get();

echo "=== DETAILED BREAKDOWN BY INVOICE ===\n";

foreach ($invoices as $invoice) {
    echo "\n--- INVOICE: {$invoice->invoice_no} ---\n";
    
    $invoiceDetails = $allDetails->where('invoice_id', $invoice->id);
    
    $pfee1 = 0;
    $pfee2 = 0;
    $sst = 0;
    $total = 0;
    
    echo "Details:\n";
    foreach ($invoiceDetails as $detail) {
        echo "  Item: {$detail->item_name}\n";
        echo "    Amount: {$detail->amount}\n";
        echo "    Original Amount: {$detail->ori_invoice_amt}\n";
        echo "    Account Category: {$detail->account_cat_id}\n";
        echo "    Pfee1 Item: {$detail->pfee1_item}\n";
        
        if ($detail->account_cat_id == 1) {
            // Professional fee item
            if ($detail->pfee1_item == 1) {
                $pfee1 += $detail->amount;
                echo "    → Added to Pfee1: {$detail->amount}\n";
            } else {
                $pfee2 += $detail->amount;
                echo "    → Added to Pfee2: {$detail->amount}\n";
            }
            
            // Calculate SST and total
            $itemSst = $detail->amount * ($invoice->sst_rate / 100);
            $itemTotal = $detail->amount * (($invoice->sst_rate / 100) + 1);
            $sst += $itemSst;
            $total += $itemTotal;
            echo "    → SST: {$itemSst}\n";
            echo "    → Total: {$itemTotal}\n";
        } else {
            // Non-professional fee item
            $total += $detail->amount;
            echo "    → Added to Total (non-professional): {$detail->amount}\n";
        }
        echo "\n";
    }
    
    echo "CALCULATED TOTALS:\n";
    echo "  Pfee1: " . round($pfee1, 2) . "\n";
    echo "  Pfee2: " . round($pfee2, 2) . "\n";
    echo "  SST: " . round($sst, 2) . "\n";
    echo "  Total: " . round($total, 2) . "\n";
    echo "  Current Amount in DB: {$invoice->amount}\n";
    
    if (abs($total - $invoice->amount) > 0.01) {
        echo "  ⚠️  MISMATCH! Calculated: {$total}, DB: {$invoice->amount}\n";
    } else {
        echo "  ✅ Match!\n";
    }
}

// Check if there are any differences in the original amounts
echo "\n=== CHECKING ORIGINAL AMOUNTS ===\n";
$originalAmounts = $allDetails->groupBy('invoice_no')->map(function($details) {
    return $details->sum('ori_invoice_amt');
});

foreach ($originalAmounts as $invoiceNo => $originalTotal) {
    echo "Invoice {$invoiceNo}: Original Total = {$originalTotal}\n";
}

// Check if the difference comes from rounding or calculation
echo "\n=== ROUNDING ANALYSIS ===\n";
foreach ($invoices as $invoice) {
    $invoiceDetails = $allDetails->where('invoice_id', $invoice->id);
    
    $exactTotal = 0;
    $roundedTotal = 0;
    
    foreach ($invoiceDetails as $detail) {
        if ($detail->account_cat_id == 1) {
            $itemTotal = $detail->amount * (($invoice->sst_rate / 100) + 1);
            $exactTotal += $itemTotal;
            $roundedTotal += round($itemTotal, 2);
        } else {
            $exactTotal += $detail->amount;
            $roundedTotal += $detail->amount;
        }
    }
    
    echo "Invoice {$invoice->invoice_no}:\n";
    echo "  Exact Total: {$exactTotal}\n";
    echo "  Rounded Total: {$roundedTotal}\n";
    echo "  Difference: " . ($exactTotal - $roundedTotal) . "\n";
    echo "  Current DB Amount: {$invoice->amount}\n\n";
}

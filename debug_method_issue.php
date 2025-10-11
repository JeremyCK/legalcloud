<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DEBUGGING updatePfeeDisbAmountINVFromDetails METHOD ISSUE ===\n\n";

$billId = 8377;

// Let's manually trace through what the method should do
echo "=== STEP 1: Get the main bill record ===\n";
$LoanCaseBillMain = DB::table('loan_case_bill_main')
    ->where('id', '=', $billId)
    ->first();

if (!$LoanCaseBillMain) {
    echo "ERROR: Bill not found!\n";
    exit;
}

echo "Bill found: ID {$LoanCaseBillMain->id}, SST Rate: {$LoanCaseBillMain->sst_rate}%\n\n";

echo "=== STEP 2: Get all invoices for this bill ===\n";
$invoices = DB::table('loan_case_invoice_main')
    ->where("loan_case_main_bill_id", $billId)
    ->where("status", "<>", 99)
    ->get();

echo "Found " . $invoices->count() . " invoices:\n";
foreach ($invoices as $invoice) {
    echo "  Invoice {$invoice->invoice_no} (ID: {$invoice->id})\n";
}

echo "\n=== STEP 3: Calculate amounts for each invoice ===\n";

$total_pfee1 = 0;
$total_pfee2 = 0;
$total_sst = 0;
$total_amount = 0;

foreach ($invoices as $invoice) {
    echo "\n--- Processing Invoice {$invoice->invoice_no} ---\n";
    
    // Get invoice details
    $details = DB::table('loan_case_invoice_details as ild')
        ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
        ->where('ild.invoice_main_id', $invoice->id)
        ->where('ild.status', '<>', 99)
        ->select('ild.amount', 'ai.account_cat_id', 'ai.pfee1_item')
        ->get();

    echo "Found " . $details->count() . " details for this invoice\n";

    $pfee1 = 0;
    $pfee2 = 0;
    $sst = 0;
    $total = 0;

    foreach ($details as $detail) {
        if ($detail->account_cat_id == 1) {
            // Calculate pfee1 and pfee2
            if ($detail->pfee1_item == 1) {
                $pfee1 += $detail->amount;
            } else {
                $pfee2 += $detail->amount;
            }
            
            // Calculate SST and total (same logic as SQL script)
            $sst += $detail->amount * ($LoanCaseBillMain->sst_rate / 100);
            $total += $detail->amount * (($LoanCaseBillMain->sst_rate / 100) + 1);
        } else {
            // For other account categories (disbursements, etc.), add amount directly to total
            $total += $detail->amount;
        }
    }

    $pfee1 = round($pfee1, 2);
    $pfee2 = round($pfee2, 2);
    $sst = round($sst, 2);
    $total = round($total, 2);

    echo "Calculated amounts:\n";
    echo "  Pfee1: {$pfee1}\n";
    echo "  Pfee2: {$pfee2}\n";
    echo "  SST: {$sst}\n";
    echo "  Total: {$total}\n";

    echo "Current DB amounts:\n";
    echo "  Pfee1: {$invoice->pfee1_inv}\n";
    echo "  Pfee2: {$invoice->pfee2_inv}\n";
    echo "  SST: {$invoice->sst_inv}\n";
    echo "  Total: {$invoice->amount}\n";

    // Check if update is needed
    $needsUpdate = ($pfee1 != $invoice->pfee1_inv) ||
                   ($pfee2 != $invoice->pfee2_inv) ||
                   ($sst != $invoice->sst_inv) ||
                   ($total != $invoice->amount);

    if ($needsUpdate) {
        echo "  ⚠️  UPDATE NEEDED!\n";
        
        // Try to update the invoice
        echo "  Attempting to update invoice...\n";
        $updated = DB::table('loan_case_invoice_main')
            ->where('id', $invoice->id)
            ->update([
                'pfee1_inv' => $pfee1,
                'pfee2_inv' => $pfee2,
                'sst_inv' => $sst,
                'amount' => $total,
                'updated_at' => now()
            ]);
        
        if ($updated) {
            echo "  ✅ Invoice updated successfully!\n";
        } else {
            echo "  ❌ Failed to update invoice!\n";
        }
    } else {
        echo "  ✅ No update needed - amounts match\n";
    }

    // Add to bill totals
    $total_pfee1 += $pfee1;
    $total_pfee2 += $pfee2;
    $total_sst += $sst;
    $total_amount += $total;
}

echo "\n=== STEP 4: Update bill totals ===\n";
echo "Calculated bill totals:\n";
echo "  Total Pfee1: {$total_pfee1}\n";
echo "  Total Pfee2: {$total_pfee2}\n";
echo "  Total SST: {$total_sst}\n";
echo "  Total Amount: {$total_amount}\n";

echo "Current bill totals:\n";
echo "  Total Pfee1: {$LoanCaseBillMain->pfee1_inv}\n";
echo "  Total Pfee2: {$LoanCaseBillMain->pfee2_inv}\n";
echo "  Total SST: {$LoanCaseBillMain->sst_inv}\n";
echo "  Total Amount: {$LoanCaseBillMain->total_amt_inv}\n";

// Check if bill update is needed
$billNeedsUpdate = ($total_pfee1 != $LoanCaseBillMain->pfee1_inv) ||
                   ($total_pfee2 != $LoanCaseBillMain->pfee2_inv) ||
                   ($total_sst != $LoanCaseBillMain->sst_inv) ||
                   ($total_amount != $LoanCaseBillMain->total_amt_inv);

if ($billNeedsUpdate) {
    echo "  ⚠️  BILL UPDATE NEEDED!\n";
    
    // Try to update the bill
    echo "  Attempting to update bill...\n";
    $billUpdated = DB::table('loan_case_bill_main')
        ->where('id', $billId)
        ->update([
            'pfee1_inv' => $total_pfee1,
            'pfee2_inv' => $total_pfee2,
            'sst_inv' => $total_sst,
            'total_amt_inv' => $total_amount,
            'updated_at' => now()
        ]);
    
    if ($billUpdated) {
        echo "  ✅ Bill updated successfully!\n";
    } else {
        echo "  ❌ Failed to update bill!\n";
    }
} else {
    echo "  ✅ No bill update needed - amounts match\n";
}

echo "\n=== FINAL RESULT ===\n";
echo "Manual calculation and update completed!\n";

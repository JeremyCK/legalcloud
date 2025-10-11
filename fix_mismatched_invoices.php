<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Invoice Amount Correction Tool\n";
echo "=============================\n\n";

// Function to fix a specific invoice
function fixInvoice($invoiceNo, $dryRun = true) {
    echo "Processing Invoice: {$invoiceNo}\n";
    echo str_repeat("-", 50) . "\n";
    
    $invoice = \App\Models\LoanCaseInvoiceMain::where('invoice_no', $invoiceNo)->first();
    
    if (!$invoice) {
        echo "❌ Invoice not found\n\n";
        return false;
    }
    
    echo "Current Invoice Amount: " . number_format($invoice->amount, 2) . "\n";
    
    // Get SST rate from associated bill
    $sstRate = $invoice->loanCaseBillMain ? ($invoice->loanCaseBillMain->sst_rate / 100) : 0;
    echo "SST Rate: " . number_format($sstRate * 100, 2) . "%\n";
    
    // Get all invoice details with account_cat_id
    $details = \DB::table('loan_case_invoice_details as d')
        ->leftJoin('account_item as ai', 'ai.id', '=', 'd.account_item_id')
        ->where('d.invoice_main_id', $invoice->id)
        ->select('d.*', 'ai.account_cat_id')
        ->get();
    
    if ($details->count() == 0) {
        echo "❌ No details found\n\n";
        return false;
    }
    
    // Calculate by category
    $sumCat1 = $details->where('account_cat_id', 1)->sum('amount');
    $sumCat2 = $details->where('account_cat_id', 2)->sum('amount');
    $sumCat3 = $details->where('account_cat_id', 3)->sum('amount');
    $sumCat4 = $details->where('account_cat_id', 4)->sum('amount');
    
    // Apply the correct formula
    $correctAmount = 0;
    $correctAmount += $sumCat1 + ($sumCat1 * $sstRate); // Cat1 + SST
    $correctAmount += $sumCat2; // Cat2 (no SST)
    $correctAmount += $sumCat3; // Cat3 (no SST)
    $correctAmount += $sumCat4 + ($sumCat4 * $sstRate); // Cat4 + SST
    
    $difference = $correctAmount - $invoice->amount;
    
    echo "Categories: Cat1=" . number_format($sumCat1, 2) . 
         ", Cat2=" . number_format($sumCat2, 2) . 
         ", Cat3=" . number_format($sumCat3, 2) . 
         ", Cat4=" . number_format($sumCat4, 2) . "\n";
    echo "Correct Amount: " . number_format($correctAmount, 2) . "\n";
    echo "Difference: " . number_format($difference, 2) . "\n";
    
    if (abs($difference) < 0.01) {
        echo "✅ Already correct - no action needed\n\n";
        return true;
    }
    
    if ($dryRun) {
        echo "🔍 DRY RUN - Would update invoice amount from " . number_format($invoice->amount, 2) . " to " . number_format($correctAmount, 2) . "\n";
        echo "⚠️  To actually fix this invoice, run: php fix_mismatched_invoices.php {$invoiceNo} --fix\n\n";
    } else {
        // Actually update the invoice
        $oldAmount = $invoice->amount;
        $invoice->amount = $correctAmount;
        $invoice->save();
        
        echo "✅ UPDATED - Invoice amount changed from " . number_format($oldAmount, 2) . " to " . number_format($correctAmount, 2) . "\n\n";
    }
    
    return true;
}

// Check command line arguments
$invoiceNo = $argv[1] ?? null;
$fixMode = isset($argv[2]) && $argv[2] === '--fix';

if ($invoiceNo) {
    // Fix specific invoice
    if ($fixMode) {
        echo "🔧 FIXING MODE - Will update database\n\n";
    } else {
        echo "🔍 DRY RUN MODE - No changes will be made\n\n";
    }
    
    fixInvoice($invoiceNo, !$fixMode);
} else {
    // Show help
    echo "Usage:\n";
    echo "  php fix_mismatched_invoices.php [INVOICE_NUMBER] [--fix]\n\n";
    echo "Examples:\n";
    echo "  php fix_mismatched_invoices.php 20002239          # Dry run for invoice 20002239\n";
    echo "  php fix_mismatched_invoices.php 20002239 --fix    # Actually fix invoice 20002239\n\n";
    
    echo "To fix all mismatched invoices, you can run:\n";
    echo "  php find_mismatched_invoices.php | grep 'Invoice:' | awk '{print \$2}' | while read inv; do\n";
    echo "    echo \"Fixing \$inv...\"\n";
    echo "    php fix_mismatched_invoices.php \$inv --fix\n";
    echo "  done\n\n";
    
    echo "⚠️  WARNING: Always backup your database before running with --fix!\n";
}

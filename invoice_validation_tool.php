<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Invoice Amount Validation Tool\n";
echo "=============================\n\n";

// Function to validate a single invoice
function validateInvoice($invoiceNo) {
    echo "Validating Invoice: {$invoiceNo}\n";
    echo str_repeat("-", 50) . "\n";
    
    $invoice = \App\Models\LoanCaseInvoiceMain::where('invoice_no', $invoiceNo)->first();
    
    if (!$invoice) {
        echo "❌ Invoice not found\n\n";
        return false;
    }
    
    echo "Invoice Amount (DB): " . number_format($invoice->amount, 2) . "\n";
    
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
    
    echo "\nCategory Breakdown:\n";
    echo "  Category 1 (P.Fee): " . number_format($sumCat1, 2) . "\n";
    echo "  Category 2 (Disbursement): " . number_format($sumCat2, 2) . "\n";
    echo "  Category 3 (Reimbursement): " . number_format($sumCat3, 2) . "\n";
    echo "  Category 4 (SST): " . number_format($sumCat4, 2) . "\n";
    
    // Apply your formula
    $cat1WithSST = $sumCat1 + ($sumCat1 * $sstRate);
    $cat4WithSST = $sumCat4 + ($sumCat4 * $sstRate);
    $calculatedTotal = $cat1WithSST + $sumCat2 + $sumCat3 + $cat4WithSST;
    
    echo "\nCalculation:\n";
    echo "  Cat1 + SST: " . number_format($sumCat1, 2) . " + (" . number_format($sumCat1, 2) . " × " . number_format($sstRate, 2) . ") = " . number_format($cat1WithSST, 2) . "\n";
    echo "  Cat2: " . number_format($sumCat2, 2) . "\n";
    echo "  Cat3: " . number_format($sumCat3, 2) . "\n";
    echo "  Cat4 + SST: " . number_format($sumCat4, 2) . " + (" . number_format($sumCat4, 2) . " × " . number_format($sstRate, 2) . ") = " . number_format($cat4WithSST, 2) . "\n";
    echo "  Total: " . number_format($calculatedTotal, 2) . "\n";
    
    $difference = abs($calculatedTotal - $invoice->amount);
    
    if ($difference < 0.01) {
        echo "\n✅ MATCH - Invoice amount is correct!\n";
        return true;
    } else {
        echo "\n❌ MISMATCH - Difference: " . number_format($difference, 2) . "\n";
        echo "  Invoice DB: " . number_format($invoice->amount, 2) . "\n";
        echo "  Calculated: " . number_format($calculatedTotal, 2) . "\n";
        return false;
    }
}

// Check if specific invoice number provided
if (isset($argv[1])) {
    $invoiceNo = $argv[1];
    validateInvoice($invoiceNo);
} else {
    echo "Usage: php invoice_validation_tool.php [INVOICE_NUMBER]\n";
    echo "Example: php invoice_validation_tool.php 10001121\n\n";
    
    echo "Or run without parameters to check recent invoices:\n";
    
    // Check last 5 invoices
    $recentInvoices = \App\Models\LoanCaseInvoiceMain::where('status', 1)
        ->orderBy('id', 'desc')
        ->limit(5)
        ->pluck('invoice_no');
    
    foreach ($recentInvoices as $invoiceNo) {
        validateInvoice($invoiceNo);
        echo "\n";
    }
}
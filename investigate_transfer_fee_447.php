<?php
/**
 * Investigate Transfer Fee 447 - Why invoices still show values after account tool fix
 * 
 * This script helps understand:
 * 1. What the account tool fix does
 * 2. Why invoices still show "Transferred Bal" values
 * 3. Whether these invoices should be removed from the transfer fee record
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\LoanCaseInvoiceMain;
use App\Models\TransferFeeDetails;
use App\Models\TransferFeeMain;

$transferFeeMainId = 447;
$invoiceNos = ['DP20000817', 'DP20000816'];

echo "========================================\n";
echo "INVESTIGATING TRANSFER FEE 447\n";
echo "========================================\n\n";

$transferFeeMain = TransferFeeMain::find($transferFeeMainId);
if ($transferFeeMain) {
    echo "Transfer Fee Main ID: {$transferFeeMainId}\n";
    echo "Transaction ID: {$transferFeeMain->transaction_id}\n";
    echo "Transfer Date: {$transferFeeMain->transfer_date}\n";
    echo "Purpose: {$transferFeeMain->purpose}\n";
    echo "Total Transfer Amount: " . number_format($transferFeeMain->transfer_amount ?? 0, 2) . "\n";
    echo "\n";
}

echo "Understanding the Issue:\n";
echo "------------------------\n";
echo "The 'Transferred Bal' column shows amounts that were transferred in THIS transfer fee record.\n";
echo "These invoices have been FULLY transferred, meaning:\n";
echo "  - transferred_pfee_amt = pfee (all professional fee transferred)\n";
echo "  - transferred_reimbursement_amt = reimbursement (all reimbursement transferred)\n";
echo "\n";
echo "The account tool fix UPDATES transfer_fee_details to match current invoice amounts.\n";
echo "It does NOT remove invoices from transfer fee records.\n";
echo "\n";

foreach ($invoiceNos as $invoiceNo) {
    $invoice = LoanCaseInvoiceMain::where('invoice_no', $invoiceNo)->first();
    
    if (!$invoice) {
        echo "{$invoiceNo}: Invoice not found\n\n";
        continue;
    }
    
    echo "{$invoiceNo} Analysis:\n";
    echo str_repeat("-", 50) . "\n";
    
    $pfee = ($invoice->pfee1_inv ?? 0) + ($invoice->pfee2_inv ?? 0);
    $reimbursement = $invoice->reimbursement_amount ?? 0;
    
    $transferredPfee = $invoice->transferred_pfee_amt ?? 0;
    $transferredReimb = $invoice->transferred_reimbursement_amt ?? 0;
    
    echo "Invoice Amounts:\n";
    echo "  Pfee: " . number_format($pfee, 2) . "\n";
    echo "  Reimbursement: " . number_format($reimbursement, 2) . "\n";
    echo "\n";
    
    echo "Transferred Amounts (from invoice table):\n";
    echo "  transferred_pfee_amt: " . number_format($transferredPfee, 2) . "\n";
    echo "  transferred_reimbursement_amt: " . number_format($transferredReimb, 2) . "\n";
    echo "\n";
    
    // Check if fully transferred
    $isFullyTransferred = false;
    if (abs($pfee - $transferredPfee) < 0.01 && abs($reimbursement - $transferredReimb) < 0.01) {
        $isFullyTransferred = true;
        echo "✅ Status: FULLY TRANSFERRED\n";
        echo "   All amounts have been transferred.\n";
    } else {
        echo "⚠️  Status: PARTIALLY TRANSFERRED\n";
        echo "   Remaining to transfer:\n";
        echo "     Pfee: " . number_format($pfee - $transferredPfee, 2) . "\n";
        echo "     Reimbursement: " . number_format($reimbursement - $transferredReimb, 2) . "\n";
    }
    echo "\n";
    
    // Check transfer fee details for this transfer fee main
    $tfd = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
        ->where('transfer_fee_main_id', $transferFeeMainId)
        ->where('status', '<>', 99)
        ->first();
    
    if ($tfd) {
        echo "Transfer Fee Detail (Transfer Fee Main ID: {$transferFeeMainId}):\n";
        echo "  transfer_amount: " . number_format($tfd->transfer_amount ?? 0, 2) . "\n";
        echo "  reimbursement_amount: " . number_format($tfd->reimbursement_amount ?? 0, 2) . "\n";
        echo "  Transferred Bal (shown in view): " . number_format(($tfd->transfer_amount ?? 0) + ($tfd->reimbursement_amount ?? 0), 2) . "\n";
        echo "\n";
        
        // Check if transfer_fee_detail matches invoice amounts
        if (abs($pfee - ($tfd->transfer_amount ?? 0)) < 0.01 && 
            abs($reimbursement - ($tfd->reimbursement_amount ?? 0)) < 0.01) {
            echo "✅ Transfer Fee Detail matches invoice amounts (account tool fix worked correctly)\n";
        } else {
            echo "⚠️  Transfer Fee Detail does NOT match invoice amounts\n";
            echo "   Difference:\n";
            echo "     Pfee: " . number_format($pfee - ($tfd->transfer_amount ?? 0), 2) . "\n";
            echo "     Reimbursement: " . number_format($reimbursement - ($tfd->reimbursement_amount ?? 0), 2) . "\n";
        }
    } else {
        echo "❌ Transfer Fee Detail NOT FOUND for transfer_fee_main_id {$transferFeeMainId}\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n\n";
}

echo "Conclusion:\n";
echo "----------\n";
echo "The 'Transferred Bal' values are CORRECT - they show the amounts transferred.\n";
echo "The account tool fix UPDATES these values to match current invoice amounts.\n";
echo "It does NOT remove invoices from transfer fee records.\n";
echo "\n";
echo "If you want to REMOVE these invoices from transfer fee 447:\n";
echo "  1. This would require deleting the transfer_fee_details records\n";
echo "  2. This would also require updating invoice.transferred_* amounts\n";
echo "  3. This would require updating ledger entries\n";
echo "\n";
echo "Is this what you want to do? (This is a different operation than the account tool fix)\n";


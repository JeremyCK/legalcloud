<?php
/**
 * Check and Fix Reimbursement SST for SST Record 96
 * 
 * This script:
 * 1. Checks if invoices have reimbursement_sst values
 * 2. Shows which invoices need reimbursement SST calculated
 * 3. Optionally calculates and updates reimbursement SST values
 * 
 * Usage:
 * php artisan tinker
 * require 'check_and_fix_reimbursement_sst_96.php';
 * checkReimbursementSST(96);
 * fixReimbursementSST(96); // Only if needed
 */

use Illuminate\Support\Facades\DB;
use App\Models\SSTMain;
use App\Models\SSTDetails;
use App\Models\LoanCaseInvoiceMain;
use App\Models\LoanCaseBillMain;

/**
 * Check reimbursement SST status for SST record
 */
function checkReimbursementSST($sstMainId) {
    echo "=== Checking Reimbursement SST for SST Record ID: {$sstMainId} ===\n\n";
    
    $sstMain = SSTMain::find($sstMainId);
    if (!$sstMain) {
        echo "❌ SST Record {$sstMainId} not found!\n";
        return false;
    }
    
    $sstDetails = SSTDetails::where('sst_main_id', $sstMainId)->get();
    echo "Total Invoices: {$sstDetails->count()}\n\n";
    
    $invoicesWithReimb = 0;
    $invoicesWithoutReimb = 0;
    $totalReimbSST = 0;
    $totalSST = 0;
    $issues = [];
    
    foreach ($sstDetails as $index => $detail) {
        $invoice = LoanCaseInvoiceMain::find($detail->loan_case_invoice_main_id);
        
        if (!$invoice) {
            $issues[] = "Invoice ID {$detail->loan_case_invoice_main_id} not found";
            continue;
        }
        
        $sstAmount = $detail->amount ?? 0;
        $reimbursementSst = $invoice->reimbursement_sst ?? 0;
        $transferredReimbSst = $invoice->transferred_reimbursement_sst_amt ?? 0;
        $remainingReimbSst = max(0, $reimbursementSst - $transferredReimbSst);
        
        $totalSST += $sstAmount;
        $totalReimbSST += $remainingReimbSst;
        
        // Check if invoice has bill and SST rate
        $bill = null;
        if ($invoice->loan_case_main_bill_id) {
            $bill = LoanCaseBillMain::find($invoice->loan_case_main_bill_id);
        }
        
        $sstRate = $bill ? ($bill->sst_rate ?? 0) : 0;
        
        // Check if reimbursement details exist
        $reimbDetails = DB::table('loan_case_invoice_details as ild')
            ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
            ->where('ild.invoice_main_id', $invoice->id)
            ->where('ai.account_cat_id', 4)
            ->where('ild.status', '<>', 99)
            ->sum('ild.amount');
        
        $calculatedReimbSST = $reimbDetails ? ($reimbDetails * $sstRate / 100) : 0;
        
        if ($reimbursementSst > 0 || $calculatedReimbSST > 0) {
            $invoicesWithReimb++;
        } else {
            $invoicesWithoutReimb++;
            if ($reimbDetails > 0) {
                $issues[] = "Invoice {$invoice->invoice_no} (ID: {$invoice->id}) has reimbursement details ({$reimbDetails}) but reimbursement_sst is 0.00. Should be: " . number_format($calculatedReimbSST, 2);
            }
        }
        
        if ($index < 10) { // Show first 10 invoices
            echo "Invoice #" . ($index + 1) . ": {$invoice->invoice_no}\n";
            echo "  SST: " . number_format($sstAmount, 2) . "\n";
            echo "  Reimb SST (stored): " . number_format($reimbursementSst, 2) . "\n";
            echo "  Reimb SST (calculated from details): " . number_format($calculatedReimbSST, 2) . "\n";
            echo "  Reimb Details Amount: " . number_format($reimbDetails, 2) . "\n";
            echo "  SST Rate: {$sstRate}%\n";
            echo "  Remaining Reimb SST: " . number_format($remainingReimbSst, 2) . "\n";
            echo "\n";
        }
    }
    
    echo str_repeat("=", 80) . "\n";
    echo "SUMMARY:\n";
    echo "  Invoices with Reimb SST: {$invoicesWithReimb}\n";
    echo "  Invoices without Reimb SST: {$invoicesWithoutReimb}\n";
    echo "  Total SST: " . number_format($totalSST, 2) . "\n";
    echo "  Total Remaining Reimb SST: " . number_format($totalReimbSST, 2) . "\n";
    echo "  Grand Total: " . number_format($totalSST + $totalReimbSST, 2) . "\n";
    
    if (count($issues) > 0) {
        echo "\n⚠️  ISSUES FOUND:\n";
        foreach ($issues as $issue) {
            echo "  - {$issue}\n";
        }
        echo "\n💡 Run fixReimbursementSST({$sstMainId}) to fix these issues.\n";
    } else {
        echo "\n✅ All invoices have reimbursement SST values populated.\n";
    }
    
    return true;
}

/**
 * Fix reimbursement SST for invoices in SST record
 */
function fixReimbursementSST($sstMainId) {
    echo "=== Fixing Reimbursement SST for SST Record ID: {$sstMainId} ===\n\n";
    
    $sstDetails = SSTDetails::where('sst_main_id', $sstMainId)->get();
    $fixedCount = 0;
    $skippedCount = 0;
    
    foreach ($sstDetails as $detail) {
        $invoice = LoanCaseInvoiceMain::find($detail->loan_case_invoice_main_id);
        
        if (!$invoice) {
            $skippedCount++;
            continue;
        }
        
        // Get bill to get SST rate
        $bill = null;
        if ($invoice->loan_case_main_bill_id) {
            $bill = LoanCaseBillMain::find($invoice->loan_case_main_bill_id);
        }
        
        if (!$bill) {
            $skippedCount++;
            continue;
        }
        
        $sstRate = $bill->sst_rate ?? 0;
        
        // Calculate reimbursement amount and SST from invoice details
        $reimbDetails = DB::table('loan_case_invoice_details as ild')
            ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
            ->where('ild.invoice_main_id', $invoice->id)
            ->where('ai.account_cat_id', 4)
            ->where('ild.status', '<>', 99)
            ->sum('ild.amount');
        
        $calculatedReimbAmount = $reimbDetails ?? 0;
        $calculatedReimbSST = $calculatedReimbAmount * ($sstRate / 100);
        
        // Update invoice if values are different
        if (abs(($invoice->reimbursement_sst ?? 0) - $calculatedReimbSST) > 0.01) {
            $invoice->reimbursement_amount = $calculatedReimbAmount;
            $invoice->reimbursement_sst = $calculatedReimbSST;
            $invoice->save();
            $fixedCount++;
            echo "✅ Fixed Invoice {$invoice->invoice_no}: Reimb SST = " . number_format($calculatedReimbSST, 2) . "\n";
        } else {
            $skippedCount++;
        }
    }
    
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "FIX SUMMARY:\n";
    echo "  Fixed: {$fixedCount}\n";
    echo "  Skipped: {$skippedCount}\n";
    echo "\n💡 Now run checkReimbursementSST({$sstMainId}) again to verify.\n";
    echo "💡 Then update the SST record amount by clicking 'Update SST' on the edit page.\n";
}

// If running directly, show usage
if (php_sapi_name() === 'cli' && !defined('LARAVEL_START')) {
    echo "Reimbursement SST Check and Fix Script\n";
    echo "=======================================\n\n";
    echo "Usage:\n";
    echo "1. Check: checkReimbursementSST(96);\n";
    echo "2. Fix: fixReimbursementSST(96);\n\n";
    echo "Run via Laravel Tinker:\n";
    echo "  php artisan tinker\n";
    echo "  require 'check_and_fix_reimbursement_sst_96.php';\n";
    echo "  checkReimbursementSST(96);\n";
}



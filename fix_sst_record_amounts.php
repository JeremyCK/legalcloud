<?php
/**
 * Fix SST Record Amounts - Recalculate and Update SST Main Amount
 * 
 * This script recalculates the total amount for SST records including reimbursement SST
 * 
 * Usage:
 * 1. For single record: php artisan tinker -> require 'fix_sst_record_amounts.php'; fixSSTRecord(96);
 * 2. For all records: php artisan tinker -> require 'fix_sst_record_amounts.php'; fixAllSSTRecords();
 */

use Illuminate\Support\Facades\DB;
use App\Models\SSTMain;
use App\Models\SSTDetails;
use App\Models\LoanCaseInvoiceMain;

/**
 * Fix a single SST record
 */
function fixSSTRecord($sstMainId) {
    echo "=== Fixing SST Record ID: {$sstMainId} ===\n\n";
    
    $sstMain = SSTMain::find($sstMainId);
    if (!$sstMain) {
        echo "❌ SST Record {$sstMainId} not found!\n";
        return false;
    }
    
    echo "SST Main Record:\n";
    echo "  Payment Date: {$sstMain->payment_date}\n";
    echo "  Transaction ID: {$sstMain->transaction_id}\n";
    echo "  Current Stored Amount: " . number_format($sstMain->amount ?? 0, 2) . "\n\n";
    
    $sstDetails = SSTDetails::where('sst_main_id', $sstMainId)->get();
    
    if ($sstDetails->count() == 0) {
        echo "⚠️  No invoices found in this SST record.\n";
        $sstMain->amount = 0;
        $sstMain->save();
        echo "✅ Updated amount to 0.00\n";
        return true;
    }
    
    $totalAmount = 0;
    $invoiceCount = 0;
    
    echo "Processing {$sstDetails->count()} invoice(s):\n";
    echo str_repeat("-", 80) . "\n";
    
    foreach ($sstDetails as $detail) {
        $invoice = LoanCaseInvoiceMain::find($detail->loan_case_invoice_main_id);
        
        if (!$invoice) {
            echo "⚠️  Invoice ID {$detail->loan_case_invoice_main_id} not found - skipping\n";
            continue;
        }
        
        $invoiceCount++;
        $sstAmount = $detail->amount ?? 0;
        $reimbursementSst = $invoice->reimbursement_sst ?? 0;
        $transferredReimbSst = $invoice->transferred_reimbursement_sst_amt ?? 0;
        
        // Calculate remaining reimbursement SST (what should be included)
        $remainingReimbSst = max(0, $reimbursementSst - $transferredReimbSst);
        
        // Total for this invoice
        $invoiceTotal = $sstAmount + $remainingReimbSst;
        $totalAmount += $invoiceTotal;
        
        echo "Invoice #{$invoiceCount}:\n";
        echo "  Invoice No: " . ($invoice->invoice_no ?? 'N/A') . "\n";
        echo "  SST Amount: " . number_format($sstAmount, 2) . "\n";
        echo "  Reimbursement SST: " . number_format($reimbursementSst, 2) . "\n";
        echo "  Transferred Reimb SST: " . number_format($transferredReimbSst, 2) . "\n";
        echo "  Remaining Reimb SST: " . number_format($remainingReimbSst, 2) . "\n";
        echo "  Invoice Total: " . number_format($invoiceTotal, 2) . "\n";
        echo "\n";
    }
    
    echo str_repeat("-", 80) . "\n";
    echo "SUMMARY:\n";
    echo "  Calculated Grand Total: " . number_format($totalAmount, 2) . "\n";
    echo "  Current Stored Amount: " . number_format($sstMain->amount ?? 0, 2) . "\n";
    
    $difference = abs(($sstMain->amount ?? 0) - $totalAmount);
    
    if ($difference < 0.01) {
        echo "  ✅ Amounts match - no update needed\n";
        return true;
    }
    
    echo "  Difference: " . number_format($difference, 2) . "\n";
    echo "  🔄 Updating stored amount...\n";
    
    $sstMain->amount = $totalAmount;
    $sstMain->save();
    
    echo "  ✅ Updated successfully!\n";
    echo "  New Stored Amount: " . number_format($sstMain->amount, 2) . "\n";
    
    return true;
}

/**
 * Fix all SST records
 */
function fixAllSSTRecords() {
    echo "=== Fixing All SST Records ===\n\n";
    
    $allSSTRecords = SSTMain::all();
    $totalRecords = $allSSTRecords->count();
    $fixedCount = 0;
    $skippedCount = 0;
    $errorCount = 0;
    
    echo "Found {$totalRecords} SST record(s) to process.\n\n";
    
    foreach ($allSSTRecords as $index => $sstMain) {
        echo "Processing record " . ($index + 1) . "/{$totalRecords} (ID: {$sstMain->id})...\n";
        
        try {
            $result = fixSSTRecord($sstMain->id);
            if ($result) {
                $fixedCount++;
            } else {
                $skippedCount++;
            }
        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
            $errorCount++;
        }
        
        echo "\n";
    }
    
    echo str_repeat("=", 80) . "\n";
    echo "FINAL SUMMARY:\n";
    echo "  Total Records: {$totalRecords}\n";
    echo "  Fixed/Updated: {$fixedCount}\n";
    echo "  Skipped: {$skippedCount}\n";
    echo "  Errors: {$errorCount}\n";
    echo str_repeat("=", 80) . "\n";
}

// If running directly (not via require), show usage
if (php_sapi_name() === 'cli' && !defined('LARAVEL_START')) {
    echo "SST Record Amount Fix Script\n";
    echo "============================\n\n";
    echo "Usage:\n";
    echo "1. Fix single record: fixSSTRecord(96);\n";
    echo "2. Fix all records: fixAllSSTRecords();\n\n";
    echo "Run via Laravel Tinker:\n";
    echo "  php artisan tinker\n";
    echo "  require 'fix_sst_record_amounts.php';\n";
    echo "  fixSSTRecord(96);\n";
}












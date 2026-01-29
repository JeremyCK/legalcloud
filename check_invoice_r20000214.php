<?php
/**
 * Diagnostic script to check why invoice R20000214 is not appearing in SST V2 search
 * 
 * Run in Laravel Tinker:
 * php artisan tinker
 * require 'check_invoice_r20000214.php';
 * checkInvoiceR20000214();
 */

use Illuminate\Support\Facades\DB;
use App\Models\LoanCaseInvoiceMain;
use App\Models\LoanCaseBillMain;
use App\Models\LoanCase;
use App\Models\SSTDetails;

function checkInvoiceR20000214($sstMainId = 96) {
    echo "=== CHECKING INVOICE R20000214 ===\n\n";
    
    // Find the invoice
    $invoice = LoanCaseInvoiceMain::where('invoice_no', 'R20000214')->first();
    
    if (!$invoice) {
        echo "❌ ERROR: Invoice R20000214 NOT FOUND in database\n";
        return;
    }
    
    echo "✅ Invoice found: ID = {$invoice->id}\n\n";
    
    // Check all conditions from the query
    echo "=== CHECKING QUERY CONDITIONS ===\n\n";
    
    // 1. Status check
    $statusOk = $invoice->status != 99;
    echo ($statusOk ? "✅" : "❌") . " Status check: status = {$invoice->status} (must be <> 99)\n";
    
    // 2. Bill ID check
    $billIdOk = !is_null($invoice->loan_case_main_bill_id) && $invoice->loan_case_main_bill_id > 0;
    echo ($billIdOk ? "✅" : "❌") . " Bill ID check: loan_case_main_bill_id = " . ($invoice->loan_case_main_bill_id ?? 'NULL') . " (must be NOT NULL and > 0)\n";
    
    // 3. Get bill information
    $bill = null;
    if ($invoice->loan_case_main_bill_id) {
        $bill = LoanCaseBillMain::find($invoice->loan_case_main_bill_id);
        if ($bill) {
            echo "✅ Bill found: ID = {$bill->id}\n";
            
            // Check bill invoice flag
            $billInvoiceOk = $bill->bln_invoice == 1;
            echo ($billInvoiceOk ? "✅" : "❌") . " Bill invoice flag: bln_invoice = {$bill->bln_invoice} (must be 1)\n";
            
            // Check bill SST flag
            $billSstOk = $bill->bln_sst == 0;
            echo ($billSstOk ? "✅" : "❌") . " Bill SST flag: bln_sst = {$bill->bln_sst} (must be 0)\n";
            
            // Get case information
            $case = LoanCase::find($bill->case_id);
            if ($case) {
                echo "✅ Case found: ID = {$case->id}, Ref = {$case->case_ref_no}\n";
                echo "   Case branch_id = " . ($case->branch_id ?? 'NULL') . "\n";
                echo "   Bill invoice_branch_id = " . ($bill->invoice_branch_id ?? 'NULL') . "\n";
            }
        } else {
            echo "❌ Bill NOT FOUND: ID = {$invoice->loan_case_main_bill_id}\n";
        }
    }
    
    // 4. Check invoice flags
    $invoiceFlagOk = $invoice->bln_invoice == 1;
    echo ($invoiceFlagOk ? "✅" : "❌") . " Invoice flag: bln_invoice = {$invoice->bln_invoice} (must be 1)\n";
    
    $invoiceSstOk = $invoice->bln_sst == 0;
    echo ($invoiceSstOk ? "✅" : "❌") . " Invoice SST flag: bln_sst = {$invoice->bln_sst} (must be 0)\n";
    
    // 5. Check SST amounts
    echo "\n=== SST AMOUNTS ===\n";
    echo "   sst_inv = " . ($invoice->sst_inv ?? 0) . "\n";
    echo "   transferred_sst_amt = " . ($invoice->transferred_sst_amt ?? 0) . "\n";
    echo "   reimbursement_sst = " . ($invoice->reimbursement_sst ?? 0) . "\n";
    echo "   transferred_reimbursement_sst_amt = " . ($invoice->transferred_reimbursement_sst_amt ?? 0) . "\n";
    
    // 6. Check if already in SST record 96
    echo "\n=== CHECKING IF IN SST RECORD {$sstMainId} ===\n";
    $sstDetail = SSTDetails::where('sst_main_id', $sstMainId)
        ->where('loan_case_invoice_main_id', $invoice->id)
        ->first();
    
    if ($sstDetail) {
        echo "⚠️  Invoice is ALREADY in SST record {$sstMainId}\n";
        echo "   SST Detail ID = {$sstDetail->id}\n";
        echo "   This means it will be excluded from search (unless type='add')\n";
    } else {
        echo "✅ Invoice is NOT in SST record {$sstMainId}\n";
    }
    
    // 7. Check transfer_list exclusion
    $sstDetails = SSTDetails::where('sst_main_id', $sstMainId)->get();
    $transferList = [];
    foreach ($sstDetails as $detail) {
        if ($detail->loan_case_invoice_main_id) {
            $transferList[] = $detail->loan_case_invoice_main_id;
        }
    }
    
    if (in_array($invoice->id, $transferList)) {
        echo "⚠️  Invoice ID is in transfer_list (will be excluded)\n";
    } else {
        echo "✅ Invoice ID is NOT in transfer_list\n";
    }
    
    // 8. Summary
    echo "\n=== SUMMARY ===\n";
    $allConditionsMet = $statusOk && $billIdOk && ($bill ? ($billInvoiceOk && $billSstOk) : false) && $invoiceFlagOk && $invoiceSstOk;
    
    if ($allConditionsMet && !$sstDetail) {
        echo "✅ All basic conditions are met. Invoice SHOULD appear in search.\n";
        echo "   If it's still not showing, check:\n";
        echo "   1. Branch access filtering\n";
        echo "   2. Date filters (if applied)\n";
        echo "   3. Other search filters\n";
    } else {
        echo "❌ One or more conditions are NOT met:\n";
        if (!$statusOk) echo "   - Status is 99 (aborted)\n";
        if (!$billIdOk) echo "   - Missing or invalid bill ID\n";
        if ($bill && !$billInvoiceOk) echo "   - Bill bln_invoice is not 1\n";
        if ($bill && !$billSstOk) echo "   - Bill bln_sst is not 0 (SST already transferred)\n";
        if (!$invoiceFlagOk) echo "   - Invoice bln_invoice is not 1\n";
        if (!$invoiceSstOk) echo "   - Invoice bln_sst is not 0 (SST already transferred)\n";
        if ($sstDetail) echo "   - Invoice is already in SST record {$sstMainId}\n";
    }
    
    echo "\n=== END ===\n";
}

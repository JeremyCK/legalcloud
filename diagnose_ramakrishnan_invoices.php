<?php
/**
 * Comprehensive Diagnostic for Ramakrishnan Invoices Not Appearing
 * 
 * Run in Laravel Tinker:
 * php artisan tinker
 * require 'diagnose_ramakrishnan_invoices.php';
 * diagnoseRamakrishnanInvoices();
 */

use Illuminate\Support\Facades\DB;
use App\Models\Users;
use App\Services\BranchAccessService;

function diagnoseRamakrishnanInvoices() {
    echo "=== COMPREHENSIVE DIAGNOSTIC FOR RAMAKRISHNAN INVOICES ===\n\n";
    
    // Get current user
    $current_user = auth()->user();
    if (!$current_user) {
        echo "❌ No user logged in. Please login first.\n";
        return;
    }
    
    echo "User: {$current_user->name} (ID: {$current_user->id}, Role: {$current_user->menuroles})\n";
    
    // Check accessible branches
    $accessibleBranches = BranchAccessService::getAccessibleBranchIds($current_user);
    echo "Accessible branches: " . implode(', ', $accessibleBranches) . "\n";
    echo "Has access to branch 4: " . (in_array(4, $accessibleBranches) ? 'YES ✅' : 'NO ❌') . "\n\n";
    
    if (!in_array(4, $accessibleBranches)) {
        echo "❌ User doesn't have access to branch 4. This is why invoices aren't showing!\n";
        return;
    }
    
    // Step 1: Basic query - invoices with branch 4 and bln_sst = 0
    echo "=== STEP 1: Basic Query (branch 4, bln_sst = 0) ===\n";
    $step1 = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->where('b.invoice_branch_id', 4)
        ->where('im.bln_sst', 0)
        ->count();
    echo "Count: {$step1}\n\n";
    
    // Step 2: Add status check
    echo "=== STEP 2: Add status <> 99 ===\n";
    $step2 = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->where('b.invoice_branch_id', 4)
        ->where('im.bln_sst', 0)
        ->where('im.status', '<>', 99)
        ->count();
    echo "Count: {$step2}\n";
    echo "Filtered out: " . ($step1 - $step2) . "\n\n";
    
    // Step 3: Add bill_id check
    echo "=== STEP 3: Add bill_id valid ===\n";
    $step3 = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->where('b.invoice_branch_id', 4)
        ->where('im.bln_sst', 0)
        ->where('im.status', '<>', 99)
        ->whereNotNull('im.loan_case_main_bill_id')
        ->where('im.loan_case_main_bill_id', '>', 0)
        ->count();
    echo "Count: {$step3}\n";
    echo "Filtered out: " . ($step2 - $step3) . "\n\n";
    
    // Step 4: Add bln_invoice checks
    echo "=== STEP 4: Add bln_invoice = 1 (both levels) ===\n";
    $step4 = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->where('b.invoice_branch_id', 4)
        ->where('im.bln_sst', 0)
        ->where('im.status', '<>', 99)
        ->whereNotNull('im.loan_case_main_bill_id')
        ->where('im.loan_case_main_bill_id', '>', 0)
        ->where('b.bln_invoice', 1)
        ->where('im.bln_invoice', 1)
        ->count();
    echo "Count: {$step4}\n";
    echo "Filtered out: " . ($step3 - $step4) . "\n\n";
    
    // Step 5: Add bln_sst check on bill
    echo "=== STEP 5: Add b.bln_sst = 0 ===\n";
    $step5 = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->where('b.invoice_branch_id', 4)
        ->where('im.bln_sst', 0)
        ->where('im.status', '<>', 99)
        ->whereNotNull('im.loan_case_main_bill_id')
        ->where('im.loan_case_main_bill_id', '>', 0)
        ->where('b.bln_invoice', 1)
        ->where('im.bln_invoice', 1)
        ->where('b.bln_sst', 0)
        ->count();
    echo "Count: {$step5}\n";
    echo "Filtered out: " . ($step4 - $step5) . "\n\n";
    
    // Step 6: Add sst_inv > 0 check
    echo "=== STEP 6: Add im.sst_inv > 0 ===\n";
    $step6 = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->where('b.invoice_branch_id', 4)
        ->where('im.bln_sst', 0)
        ->where('im.status', '<>', 99)
        ->whereNotNull('im.loan_case_main_bill_id')
        ->where('im.loan_case_main_bill_id', '>', 0)
        ->where('b.bln_invoice', 1)
        ->where('im.bln_invoice', 1)
        ->where('b.bln_sst', 0)
        ->where('im.sst_inv', '>', 0)
        ->count();
    echo "Count: {$step6}\n";
    echo "Filtered out: " . ($step5 - $step6) . "\n";
    
    // Check sst_inv values
    $sstInvCheck = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->where('b.invoice_branch_id', 4)
        ->where('im.bln_sst', 0)
        ->where('im.status', '<>', 99)
        ->whereNotNull('im.loan_case_main_bill_id')
        ->where('im.loan_case_main_bill_id', '>', 0)
        ->where('b.bln_invoice', 1)
        ->where('im.bln_invoice', 1)
        ->where('b.bln_sst', 0)
        ->select('im.invoice_no', 'im.sst_inv', 'b.sst as bill_sst')
        ->limit(10)
        ->get();
    
    echo "\nSample sst_inv values:\n";
    foreach ($sstInvCheck as $inv) {
        echo "  {$inv->invoice_no}: sst_inv = {$inv->sst_inv}, bill.sst = {$inv->bill_sst}\n";
    }
    echo "\n";
    
    // Step 7: Add remaining SST check
    echo "=== STEP 7: Add remaining_sst > 0 ===\n";
    $step7 = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->where('b.invoice_branch_id', 4)
        ->where('im.bln_sst', 0)
        ->where('im.status', '<>', 99)
        ->whereNotNull('im.loan_case_main_bill_id')
        ->where('im.loan_case_main_bill_id', '>', 0)
        ->where('b.bln_invoice', 1)
        ->where('im.bln_invoice', 1)
        ->where('b.bln_sst', 0)
        ->where('im.sst_inv', '>', 0)
        ->whereRaw('((im.sst_inv - COALESCE(im.transferred_sst_amt, 0)) + (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) > 0')
        ->count();
    echo "Count: {$step7}\n";
    echo "Filtered out: " . ($step6 - $step7) . "\n";
    
    // Check remaining SST values
    $remainingSstCheck = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->where('b.invoice_branch_id', 4)
        ->where('im.bln_sst', 0)
        ->where('im.status', '<>', 99)
        ->whereNotNull('im.loan_case_main_bill_id')
        ->where('im.loan_case_main_bill_id', '>', 0)
        ->where('b.bln_invoice', 1)
        ->where('im.bln_invoice', 1)
        ->where('b.bln_sst', 0)
        ->where('im.sst_inv', '>', 0)
        ->select(
            'im.invoice_no',
            'im.sst_inv',
            'im.transferred_sst_amt',
            'im.reimbursement_sst',
            'im.transferred_reimbursement_sst_amt',
            DB::raw('(im.sst_inv - COALESCE(im.transferred_sst_amt, 0)) + (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)) as remaining_sst')
        )
        ->limit(10)
        ->get();
    
    echo "\nSample remaining SST values:\n";
    foreach ($remainingSstCheck as $inv) {
        $status = $inv->remaining_sst > 0 ? '✅' : '❌';
        echo "  {$status} {$inv->invoice_no}: remaining_sst = {$inv->remaining_sst}\n";
        echo "      (sst_inv: {$inv->sst_inv}, transferred: {$inv->transferred_sst_amt}, reimb: {$inv->reimbursement_sst}, reimb_transferred: {$inv->transferred_reimbursement_sst_amt})\n";
    }
    echo "\n";
    
    // Final summary
    echo "=== SUMMARY ===\n";
    echo "Step 1 (Basic): {$step1} invoices\n";
    echo "Step 2 (+ status): {$step2} invoices\n";
    echo "Step 3 (+ bill_id): {$step3} invoices\n";
    echo "Step 4 (+ bln_invoice): {$step4} invoices\n";
    echo "Step 5 (+ b.bln_sst): {$step5} invoices\n";
    echo "Step 6 (+ sst_inv > 0): {$step6} invoices\n";
    echo "Step 7 (+ remaining_sst > 0): {$step7} invoices\n\n";
    
    if ($step7 == 0) {
        echo "❌ NO INVOICES PASS ALL CRITERIA!\n";
        echo "\nMost likely issues:\n";
        if ($step6 == 0) {
            echo "  - sst_inv is 0 or NULL (Step 6 failed)\n";
        } else if ($step7 < $step6) {
            echo "  - Remaining SST is 0 or negative (Step 7 failed)\n";
        }
    } else {
        echo "✅ {$step7} invoices should appear in search results\n";
    }
    
    echo "\n=== END ===\n";
}








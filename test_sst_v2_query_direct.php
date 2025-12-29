<?php
/**
 * Direct Test of SST V2 Query Logic
 * This replicates the exact query from the controller
 * 
 * Run in Laravel Tinker:
 * php artisan tinker
 * require 'test_sst_v2_query_direct.php';
 * testSSTV2QueryDirect(4); // Test with branch 4 (Ramakrishnan)
 */

use Illuminate\Support\Facades\DB;
use App\Models\Users;
use App\Services\BranchAccessService;

function testSSTV2QueryDirect($filterBranch = 4) {
    echo "=== TESTING SST V2 QUERY DIRECTLY ===\n\n";
    
    // Get current user
    $current_user = auth()->user();
    if (!$current_user) {
        echo "❌ No user logged in. Please login first.\n";
        return;
    }
    
    echo "User: {$current_user->name} (ID: {$current_user->id}, Role: {$current_user->menuroles})\n";
    echo "Filter Branch: " . ($filterBranch ?: 'ALL') . "\n\n";
    
    // Get accessible branches
    $accessibleBranches = BranchAccessService::getAccessibleBranchIds($current_user);
    echo "Accessible branches: " . implode(', ', $accessibleBranches) . "\n";
    echo "Has access to branch 4: " . (in_array(4, $accessibleBranches) ? 'YES ✅' : 'NO ❌') . "\n\n";
    
    // Build query exactly as controller does
    $query = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
        ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
        ->leftJoin('invoice_billing_party as ibp', 'ibp.id', '=', 'im.bill_party_id')
        ->select(
            'im.id',
            'im.invoice_no',
            'im.bln_invoice',
            'im.bln_sst',
            'im.sst_inv',
            'im.transferred_sst_amt',
            'im.reimbursement_sst',
            'im.transferred_reimbursement_sst_amt',
            'b.bln_invoice as b_bln_invoice',
            'b.bln_sst as b_bln_sst',
            'b.invoice_branch_id',
            'l.branch_id as case_branch_id',
            DB::raw('((im.sst_inv - COALESCE(im.transferred_sst_amt, 0)) + (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) as remaining_sst')
        )
        ->where('im.status', '<>', 99)
        ->whereNotNull('im.loan_case_main_bill_id')
        ->where('im.loan_case_main_bill_id', '>', 0)
        ->where('b.bln_invoice', '=', 1)
        ->where('im.bln_invoice', '=', 1)
        ->where('b.bln_sst', '=', 0)
        ->where('im.bln_sst', '=', 0);
    
    // Apply branch filtering (exactly as controller)
    if ($filterBranch && $filterBranch != 0) {
        echo "Applying branch filter for branch: {$filterBranch}\n";
        if (in_array($filterBranch, $accessibleBranches)) {
            echo "✅ User has access to branch {$filterBranch}\n";
            $query->where(function($q) use ($filterBranch) {
                $q->where('b.invoice_branch_id', $filterBranch)
                  ->orWhere(function($subQ) use ($filterBranch) {
                      $subQ->whereNull('b.invoice_branch_id')
                           ->where('l.branch_id', $filterBranch);
                  });
            });
        } else {
            echo "❌ User doesn't have access to branch {$filterBranch}\n";
            $query->whereRaw('1 = 0');
        }
    } else {
        echo "No specific branch selected - applying accessible branches filter\n";
        if (count($accessibleBranches) === 1) {
            $query->where(function($q) use ($accessibleBranches) {
                $q->where('b.invoice_branch_id', '=', $accessibleBranches[0])
                  ->orWhere(function($subQ) use ($accessibleBranches) {
                      $subQ->whereNull('b.invoice_branch_id')
                           ->where('l.branch_id', '=', $accessibleBranches[0]);
                  });
            });
        } else {
            $query->where(function($q) use ($accessibleBranches) {
                $q->whereIn('b.invoice_branch_id', $accessibleBranches)
                  ->orWhere(function($subQ) use ($accessibleBranches) {
                      $subQ->whereNull('b.invoice_branch_id')
                           ->whereIn('l.branch_id', $accessibleBranches);
                  });
            });
        }
    }
    
    // Add remaining SST check
    $query->whereRaw('((im.sst_inv - COALESCE(im.transferred_sst_amt, 0)) + (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) > 0');
    
    echo "\n=== QUERY RESULTS ===\n";
    echo "SQL: " . $query->toSql() . "\n";
    echo "Bindings: " . json_encode($query->getBindings()) . "\n\n";
    
    $count = $query->count();
    echo "Total count: {$count}\n\n";
    
    if ($count > 0) {
        echo "Sample results (first 10):\n";
        $results = $query->limit(10)->get();
        
        foreach ($results as $row) {
            echo "  Invoice: {$row->invoice_no}\n";
            echo "    im.bln_invoice: {$row->bln_invoice}, b.bln_invoice: {$row->b_bln_invoice}\n";
            echo "    im.bln_sst: {$row->bln_sst}, b.bln_sst: {$row->b_bln_sst}\n";
            echo "    sst_inv: {$row->sst_inv}\n";
            echo "    invoice_branch_id: " . ($row->invoice_branch_id ?? 'NULL') . "\n";
            echo "    case_branch_id: {$row->case_branch_id}\n";
            echo "    remaining_sst: {$row->remaining_sst}\n";
            echo "\n";
        }
    } else {
        echo "❌ NO RESULTS FOUND\n\n";
        
        // Check each filter step by step
        echo "=== DEBUGGING EACH FILTER ===\n\n";
        
        // Step 1: Basic query
        $step1 = DB::table('loan_case_invoice_main as im')
            ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
            ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            ->where('im.status', '<>', 99)
            ->whereNotNull('im.loan_case_main_bill_id')
            ->where('im.loan_case_main_bill_id', '>', 0)
            ->count();
        echo "Step 1 (basic): {$step1} invoices\n";
        
        // Step 2: Add bln_invoice
        $step2 = DB::table('loan_case_invoice_main as im')
            ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
            ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            ->where('im.status', '<>', 99)
            ->whereNotNull('im.loan_case_main_bill_id')
            ->where('im.loan_case_main_bill_id', '>', 0)
            ->where('b.bln_invoice', '=', 1)
            ->where('im.bln_invoice', '=', 1)
            ->count();
        echo "Step 2 (+ bln_invoice): {$step2} invoices\n";
        
        // Step 3: Add bln_sst
        $step3 = DB::table('loan_case_invoice_main as im')
            ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
            ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            ->where('im.status', '<>', 99)
            ->whereNotNull('im.loan_case_main_bill_id')
            ->where('im.loan_case_main_bill_id', '>', 0)
            ->where('b.bln_invoice', '=', 1)
            ->where('im.bln_invoice', '=', 1)
            ->where('b.bln_sst', '=', 0)
            ->where('im.bln_sst', '=', 0)
            ->count();
        echo "Step 3 (+ bln_sst = 0): {$step3} invoices\n";
        
        // Step 4: Add branch filter
        $step4 = DB::table('loan_case_invoice_main as im')
            ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
            ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            ->where('im.status', '<>', 99)
            ->whereNotNull('im.loan_case_main_bill_id')
            ->where('im.loan_case_main_bill_id', '>', 0)
            ->where('b.bln_invoice', '=', 1)
            ->where('im.bln_invoice', '=', 1)
            ->where('b.bln_sst', '=', 0)
            ->where('im.bln_sst', '=', 0)
            ->where(function($q) use ($filterBranch) {
                $q->where('b.invoice_branch_id', $filterBranch)
                  ->orWhere(function($subQ) use ($filterBranch) {
                      $subQ->whereNull('b.invoice_branch_id')
                           ->where('l.branch_id', $filterBranch);
                  });
            })
            ->count();
        echo "Step 4 (+ branch {$filterBranch}): {$step4} invoices\n";
        
        // Step 5: Add remaining SST
        $step5 = DB::table('loan_case_invoice_main as im')
            ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
            ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            ->where('im.status', '<>', 99)
            ->whereNotNull('im.loan_case_main_bill_id')
            ->where('im.loan_case_main_bill_id', '>', 0)
            ->where('b.bln_invoice', '=', 1)
            ->where('im.bln_invoice', '=', 1)
            ->where('b.bln_sst', '=', 0)
            ->where('im.bln_sst', '=', 0)
            ->where(function($q) use ($filterBranch) {
                $q->where('b.invoice_branch_id', $filterBranch)
                  ->orWhere(function($subQ) use ($filterBranch) {
                      $subQ->whereNull('b.invoice_branch_id')
                           ->where('l.branch_id', $filterBranch);
                  });
            })
            ->whereRaw('((im.sst_inv - COALESCE(im.transferred_sst_amt, 0)) + (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) > 0')
            ->count();
        echo "Step 5 (+ remaining_sst > 0): {$step5} invoices\n\n";
        
        // Check what's failing
        if ($step4 > 0 && $step5 == 0) {
            echo "⚠️  ISSUE: Branch filter passes but remaining SST check fails!\n";
            echo "   Checking remaining SST values...\n\n";
            
            $remainingCheck = DB::table('loan_case_invoice_main as im')
                ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->where('im.status', '<>', 99)
                ->whereNotNull('im.loan_case_main_bill_id')
                ->where('im.loan_case_main_bill_id', '>', 0)
                ->where('b.bln_invoice', '=', 1)
                ->where('im.bln_invoice', '=', 1)
                ->where('b.bln_sst', '=', 0)
                ->where('im.bln_sst', '=', 0)
                ->where(function($q) use ($filterBranch) {
                    $q->where('b.invoice_branch_id', $filterBranch)
                      ->orWhere(function($subQ) use ($filterBranch) {
                          $subQ->whereNull('b.invoice_branch_id')
                               ->where('l.branch_id', $filterBranch);
                      });
                })
                ->select(
                    'im.invoice_no',
                    'im.sst_inv',
                    'im.transferred_sst_amt',
                    'im.reimbursement_sst',
                    'im.transferred_reimbursement_sst_amt',
                    DB::raw('((im.sst_inv - COALESCE(im.transferred_sst_amt, 0)) + (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) as remaining_sst')
                )
                ->limit(10)
                ->get();
            
            foreach ($remainingCheck as $inv) {
                echo "  {$inv->invoice_no}: remaining_sst = {$inv->remaining_sst}\n";
                echo "    (sst_inv: {$inv->sst_inv}, transferred: {$inv->transferred_sst_amt}, reimb: {$inv->reimbursement_sst}, reimb_transferred: {$inv->transferred_reimbursement_sst_amt})\n";
            }
        }
    }
    
    echo "\n=== END ===\n";
}












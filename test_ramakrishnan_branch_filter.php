<?php
/**
 * Quick Test Script for Ramakrishnan Branch Filter
 * 
 * Run this in Laravel Tinker:
 * php artisan tinker
 * require 'test_ramakrishnan_branch_filter.php';
 * testRamakrishnanFilter();
 */

use Illuminate\Support\Facades\DB;
use App\Models\Users;
use App\Services\BranchAccessService;

function testRamakrishnanFilter() {
    echo "=== TESTING RAMAKRISHNAN BRANCH FILTER ===\n\n";
    
    // Get current user
    $current_user = auth()->user();
    if (!$current_user) {
        echo "❌ No user logged in. Please login first or set user manually:\n";
        echo "   \$current_user = App\Models\Users::find([USER_ID]);\n";
        return;
    }
    
    echo "User: {$current_user->name} (ID: {$current_user->id}, Role: {$current_user->menuroles})\n";
    
    // Check accessible branches
    $accessibleBranches = BranchAccessService::getAccessibleBranchIds($current_user);
    echo "Accessible branches: " . implode(', ', $accessibleBranches) . "\n";
    echo "Has access to branch 4: " . (in_array(4, $accessibleBranches) ? 'YES ✅' : 'NO ❌') . "\n\n";
    
    // Test the query with branch 4 filter
    $query = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
        ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
        ->leftJoin('invoice_billing_party as ibp', 'ibp.id', '=', 'b.billing_party_id')
        ->where('im.status', '<>', 99)
        ->whereNotNull('im.loan_case_main_bill_id')
        ->where('im.loan_case_main_bill_id', '>', 0)
        ->where('b.bln_invoice', '=', 1)
        ->where('b.bln_sst', '=', 0)
        ->where('im.sst_inv', '>', 0)
        ->where('im.bln_sst', '=', 0);
    
    // Apply branch access filter
    if (in_array(4, $accessibleBranches)) {
        $query->where(function($q) {
            $q->where('b.invoice_branch_id', 4)
              ->orWhere(function($subQ) {
                  $subQ->whereNull('b.invoice_branch_id')
                       ->where('l.branch_id', 4);
              });
        });
    } else {
        echo "❌ User doesn't have access to branch 4!\n";
        return;
    }
    
    // Apply remaining SST check
    $query->whereRaw('((im.sst_inv - COALESCE(im.transferred_sst_amt, 0)) + (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0))) > 0');
    
    $count = $query->count();
    echo "Total invoices matching criteria: {$count}\n\n";
    
    if ($count > 0) {
        echo "Sample invoices (first 5):\n";
        $invoices = $query->select(
            'im.id',
            'im.invoice_no',
            'im.sst_inv',
            'im.transferred_sst_amt',
            'im.reimbursement_sst',
            'im.transferred_reimbursement_sst_amt',
            'im.bln_sst',
            'b.invoice_branch_id',
            'l.branch_id as case_branch_id',
            'b.bln_sst as bill_bln_sst',
            'l.case_ref_no',
            'c.name as client_name',
            DB::raw('(im.sst_inv - COALESCE(im.transferred_sst_amt, 0)) + (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)) as remaining_sst')
        )
        ->limit(5)
        ->get();
        
        foreach ($invoices as $inv) {
            echo "  Invoice: {$inv->invoice_no}\n";
            echo "    SST: {$inv->sst_inv}, Transferred: {$inv->transferred_sst_amt}\n";
            echo "    Reimb SST: {$inv->reimbursement_sst}, Transferred: {$inv->transferred_reimbursement_sst_amt}\n";
            echo "    Remaining SST: {$inv->remaining_sst}\n";
            echo "    Branch (invoice): " . ($inv->invoice_branch_id ?? 'NULL') . ", Branch (case): {$inv->case_branch_id}\n";
            echo "\n";
        }
    } else {
        echo "❌ No invoices found. Checking why...\n\n";
        
        // Check without remaining SST filter
        $query2 = DB::table('loan_case_invoice_main as im')
            ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
            ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            ->where('im.status', '<>', 99)
            ->whereNotNull('im.loan_case_main_bill_id')
            ->where('im.loan_case_main_bill_id', '>', 0)
            ->where('b.bln_invoice', '=', 1)
            ->where('b.bln_sst', '=', 0)
            ->where('im.sst_inv', '>', 0)
            ->where('im.bln_sst', '=', 0)
            ->where(function($q) {
                $q->where('b.invoice_branch_id', 4)
                  ->orWhere(function($subQ) {
                      $subQ->whereNull('b.invoice_branch_id')
                           ->where('l.branch_id', 4);
                  });
            });
        
        $count2 = $query2->count();
        echo "Without remaining SST filter: {$count2} invoices\n";
        
        if ($count2 > 0) {
            echo "⚠️  Issue: Remaining SST filter is excluding invoices!\n";
            $problemInvoices = $query2->select(
                'im.id',
                'im.invoice_no',
                'im.sst_inv',
                'im.transferred_sst_amt',
                'im.reimbursement_sst',
                'im.transferred_reimbursement_sst_amt',
                DB::raw('(im.sst_inv - COALESCE(im.transferred_sst_amt, 0)) + (COALESCE(im.reimbursement_sst, 0) - COALESCE(im.transferred_reimbursement_sst_amt, 0)) as remaining_sst')
            )
            ->limit(5)
            ->get();
            
            foreach ($problemInvoices as $inv) {
                echo "  Invoice {$inv->invoice_no}: Remaining SST = {$inv->remaining_sst}\n";
            }
        }
    }
    
    echo "\n=== END OF TEST ===\n";
}





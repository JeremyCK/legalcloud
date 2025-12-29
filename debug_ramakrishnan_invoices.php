<?php
/**
 * Debug Script to Find Why Ramakrishnan Invoices Don't Appear
 * 
 * Run this in Laravel Tinker:
 * php artisan tinker
 * require 'debug_ramakrishnan_invoices.php';
 * debugRamakrishnanInvoices();
 */

use Illuminate\Support\Facades\DB;
use App\Models\Users;
use App\Services\BranchAccessService;

function debugRamakrishnanInvoices() {
    echo "=== DEBUGGING RAMAKRISHNAN INVOICES ===\n\n";
    
    // Get current user (you may need to set this manually)
    $current_user = auth()->user();
    if (!$current_user) {
        echo "❌ No user logged in. Please set user manually:\n";
        echo "   \$current_user = App\Models\Users::find([USER_ID]);\n";
        return;
    }
    
    echo "1. USER INFORMATION:\n";
    echo "   User ID: {$current_user->id}\n";
    echo "   User Name: {$current_user->name}\n";
    echo "   User Role: {$current_user->menuroles}\n";
    echo "   User Branch ID: {$current_user->branch_id}\n\n";
    
    // Check accessible branches
    $accessibleBranches = BranchAccessService::getAccessibleBranchIds($current_user);
    echo "2. ACCESSIBLE BRANCHES:\n";
    echo "   " . implode(', ', $accessibleBranches) . "\n";
    echo "   Has access to branch 4 (Ramakrishnan): " . (in_array(4, $accessibleBranches) ? 'YES ✅' : 'NO ❌') . "\n\n";
    
    // Check Ramakrishnan branch info
    $ramakrishnanBranch = DB::table('branch')->where('id', 4)->first();
    echo "3. RAMAKRISHNAN BRANCH INFO:\n";
    if ($ramakrishnanBranch) {
        echo "   Branch ID: {$ramakrishnanBranch->id}\n";
        echo "   Branch Name: {$ramakrishnanBranch->name}\n";
        echo "   Status: {$ramakrishnanBranch->status}\n\n";
    } else {
        echo "   ❌ Branch 4 not found!\n\n";
    }
    
    // Check total Ramakrishnan invoices
    echo "4. RAMAKRISHNAN INVOICES - ALL:\n";
    $totalInvoices = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
        ->where(function($q) {
            $q->where('b.invoice_branch_id', 4)
              ->orWhere(function($subQ) {
                  $subQ->whereNull('b.invoice_branch_id')
                       ->where('l.branch_id', 4);
              });
        })
        ->where('im.status', '<>', 99)
        ->whereNotNull('im.loan_case_main_bill_id')
        ->where('im.loan_case_main_bill_id', '>', 0)
        ->count();
    echo "   Total invoices: {$totalInvoices}\n\n";
    
    // Check invoices meeting basic criteria
    echo "5. INVOICES MEETING BASIC CRITERIA:\n";
    $basicCriteria = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
        ->where(function($q) {
            $q->where('b.invoice_branch_id', 4)
              ->orWhere(function($subQ) {
                  $subQ->whereNull('b.invoice_branch_id')
                       ->where('l.branch_id', 4);
              });
        })
        ->where('im.status', '<>', 99)
        ->whereNotNull('im.loan_case_main_bill_id')
        ->where('im.loan_case_main_bill_id', '>', 0)
        ->where('b.bln_invoice', '=', 1)
        ->count();
    echo "   With bln_invoice = 1: {$basicCriteria}\n";
    
    $withSst = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
        ->where(function($q) {
            $q->where('b.invoice_branch_id', 4)
              ->orWhere(function($subQ) {
                  $subQ->whereNull('b.invoice_branch_id')
                       ->where('l.branch_id', 4);
              });
        })
        ->where('im.status', '<>', 99)
        ->whereNotNull('im.loan_case_main_bill_id')
        ->where('im.loan_case_main_bill_id', '>', 0)
        ->where('b.bln_invoice', '=', 1)
        ->where('im.sst_inv', '>', 0)
        ->count();
    echo "   With sst_inv > 0: {$withSst}\n";
    
    $notTransferred = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
        ->where(function($q) {
            $q->where('b.invoice_branch_id', 4)
              ->orWhere(function($subQ) {
                  $subQ->whereNull('b.invoice_branch_id')
                       ->where('l.branch_id', 4);
              });
        })
        ->where('im.status', '<>', 99)
        ->whereNotNull('im.loan_case_main_bill_id')
        ->where('im.loan_case_main_bill_id', '>', 0)
        ->where('b.bln_invoice', '=', 1)
        ->where('im.sst_inv', '>', 0)
        ->where('b.bln_sst', '=', 0)
        ->where('im.bln_sst', '=', 0)
        ->count();
    echo "   With bln_sst = 0 (not transferred): {$notTransferred}\n\n";
    
    // Check with branch access filter
    echo "6. INVOICES AFTER BRANCH ACCESS FILTER:\n";
    $query = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
        ->where('im.status', '<>', 99)
        ->whereNotNull('im.loan_case_main_bill_id')
        ->where('im.loan_case_main_bill_id', '>', 0)
        ->where('b.bln_invoice', '=', 1)
        ->where('b.bln_sst', '=', 0)
        ->where('im.sst_inv', '>', 0)
        ->where('im.bln_sst', '=', 0);
    
    // Apply branch filter like in controller
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
    
    $afterBranchFilter = $query->count();
    echo "   After branch access filter: {$afterBranchFilter}\n\n";
    
    // Show sample invoices
    echo "7. SAMPLE RAMAKRISHNAN INVOICES (first 5):\n";
    $sampleInvoices = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
        ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
        ->select(
            'im.id',
            'im.invoice_no',
            'im.sst_inv',
            'im.bln_sst',
            'b.invoice_branch_id',
            'l.branch_id as case_branch_id',
            'b.bln_sst as bill_bln_sst',
            'b.bln_invoice',
            'l.case_ref_no',
            'c.name as client_name'
        )
        ->where(function($q) {
            $q->where('b.invoice_branch_id', 4)
              ->orWhere(function($subQ) {
                  $subQ->whereNull('b.invoice_branch_id')
                       ->where('l.branch_id', 4);
              });
        })
        ->where('im.status', '<>', 99)
        ->limit(5)
        ->get();
    
    foreach ($sampleInvoices as $invoice) {
        echo "   Invoice: {$invoice->invoice_no}\n";
        echo "      ID: {$invoice->id}\n";
        echo "      SST: {$invoice->sst_inv}\n";
        echo "      bln_sst: {$invoice->bln_sst}\n";
        echo "      invoice_branch_id: " . ($invoice->invoice_branch_id ?? 'NULL') . "\n";
        echo "      case_branch_id: {$invoice->case_branch_id}\n";
        echo "      bill_bln_sst: {$invoice->bill_bln_sst}\n";
        echo "      bill_bln_invoice: {$invoice->bln_invoice}\n";
        echo "      Case Ref: {$invoice->case_ref_no}\n";
        echo "      Client: {$invoice->client_name}\n";
        echo "\n";
    }
    
    // Check the actual query that would be run
    echo "8. ACTUAL QUERY THAT WOULD BE RUN:\n";
    $testQuery = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
        ->where('im.status', '<>', 99)
        ->whereNotNull('im.loan_case_main_bill_id')
        ->where('im.loan_case_main_bill_id', '>', 0)
        ->where('b.bln_invoice', '=', 1)
        ->where('b.bln_sst', '=', 0)
        ->where('im.sst_inv', '>', 0)
        ->where('im.bln_sst', '=', 0);
    
    if (count($accessibleBranches) === 1) {
        $testQuery->where(function($q) use ($accessibleBranches) {
            $q->where('b.invoice_branch_id', '=', $accessibleBranches[0])
              ->orWhere(function($subQ) use ($accessibleBranches) {
                  $subQ->whereNull('b.invoice_branch_id')
                       ->where('l.branch_id', '=', $accessibleBranches[0]);
              });
        });
    } else {
        $testQuery->where(function($q) use ($accessibleBranches) {
            $q->whereIn('b.invoice_branch_id', $accessibleBranches)
              ->orWhere(function($subQ) use ($accessibleBranches) {
                  $subQ->whereNull('b.invoice_branch_id')
                       ->whereIn('l.branch_id', $accessibleBranches);
              });
        });
    }
    
    // Add branch filter = 4
    $testQuery->where(function($q) {
        $q->where('b.invoice_branch_id', 4)
          ->orWhere(function($subQ) {
              $subQ->whereNull('b.invoice_branch_id')
                   ->where('l.branch_id', 4);
          });
    });
    
    echo "   SQL: " . $testQuery->toSql() . "\n";
    echo "   Bindings: " . json_encode($testQuery->getBindings()) . "\n";
    echo "   Count: " . $testQuery->count() . "\n\n";
    
    echo "=== END OF DEBUG ===\n";
}












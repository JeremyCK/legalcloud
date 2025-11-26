<?php
/**
 * Check Invoice Branch Source
 * 
 * This script shows which invoices use invoice_branch_id vs case branch_id as fallback
 * 
 * Run in Laravel Tinker:
 * php artisan tinker
 * require 'check_invoice_branch_source.php';
 * checkInvoiceBranchSource();
 */

use Illuminate\Support\Facades\DB;

function checkInvoiceBranchSource($branchId = null) {
    echo "=== CHECKING INVOICE BRANCH SOURCE ===\n\n";
    
    $query = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
        ->leftJoin('branch as br1', 'br1.id', '=', 'b.invoice_branch_id')
        ->leftJoin('branch as br2', 'br2.id', '=', 'l.branch_id')
        ->select(
            'im.id as invoice_id',
            'im.invoice_no',
            'b.invoice_branch_id',
            DB::raw('br1.name as invoice_branch_name'),
            'l.branch_id as case_branch_id',
            DB::raw('br2.name as case_branch_name'),
            DB::raw('CASE 
                WHEN b.invoice_branch_id IS NOT NULL THEN b.invoice_branch_id
                ELSE l.branch_id
            END as effective_branch_id'),
            DB::raw('CASE 
                WHEN b.invoice_branch_id IS NOT NULL THEN "invoice_branch_id"
                ELSE "case_branch_id (fallback)"
            END as branch_source')
        )
        ->where('im.status', '<>', 99);
    
    if ($branchId) {
        $query->where(function($q) use ($branchId) {
            $q->where('b.invoice_branch_id', $branchId)
              ->orWhere(function($subQ) use ($branchId) {
                  $subQ->whereNull('b.invoice_branch_id')
                       ->where('l.branch_id', $branchId);
              });
        });
    }
    
    $invoices = $query->limit(50)->get();
    
    echo "Total invoices found: " . count($invoices) . "\n\n";
    
    // Count by source
    $byInvoiceBranch = 0;
    $byCaseBranch = 0;
    
    foreach ($invoices as $inv) {
        if ($inv->branch_source === 'invoice_branch_id') {
            $byInvoiceBranch++;
        } else {
            $byCaseBranch++;
        }
    }
    
    echo "Branch Source Statistics:\n";
    echo "  Using invoice_branch_id: {$byInvoiceBranch}\n";
    echo "  Using case_branch_id (fallback): {$byCaseBranch}\n\n";
    
    echo "Sample Invoices:\n";
    echo str_repeat("-", 120) . "\n";
    printf("%-10s %-15s %-8s %-25s %-8s %-25s %-8s %-30s\n", 
        "Invoice ID", "Invoice No", "Inv Br", "Invoice Branch", "Case Br", "Case Branch", "Effective", "Source");
    echo str_repeat("-", 120) . "\n";
    
    foreach ($invoices as $inv) {
        printf("%-10s %-15s %-8s %-25s %-8s %-25s %-8s %-30s\n",
            $inv->invoice_id,
            $inv->invoice_no ?? 'N/A',
            $inv->invoice_branch_id ?? 'NULL',
            $inv->invoice_branch_name ?? 'N/A',
            $inv->case_branch_id ?? 'NULL',
            $inv->case_branch_name ?? 'N/A',
            $inv->effective_branch_id,
            $inv->branch_source
        );
    }
    
    echo "\n";
    
    // Check for Ramakrishnan specifically
    if (!$branchId || $branchId == 4) {
        echo "=== RAMAKRISHNAN (Branch 4) SPECIFIC CHECK ===\n\n";
        
        $ramakrishnanQuery = DB::table('loan_case_invoice_main as im')
            ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
            ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            ->where('im.status', '<>', 99)
            ->where(function($q) {
                $q->where('b.invoice_branch_id', 4)
                  ->orWhere(function($subQ) {
                      $subQ->whereNull('b.invoice_branch_id')
                           ->where('l.branch_id', 4);
                  });
            });
        
        $ramakrishnanCount = $ramakrishnanQuery->count();
        echo "Total Ramakrishnan invoices (branch 4): {$ramakrishnanCount}\n";
        
        $byInvoiceBranch4 = DB::table('loan_case_invoice_main as im')
            ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
            ->where('im.status', '<>', 99)
            ->where('b.invoice_branch_id', 4)
            ->count();
        
        $byCaseBranch4 = DB::table('loan_case_invoice_main as im')
            ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
            ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            ->where('im.status', '<>', 99)
            ->whereNull('b.invoice_branch_id')
            ->where('l.branch_id', 4)
            ->count();
        
        echo "  Using invoice_branch_id = 4: {$byInvoiceBranch4}\n";
        echo "  Using case_branch_id = 4 (fallback): {$byCaseBranch4}\n\n";
    }
    
    echo "=== END ===\n";
}



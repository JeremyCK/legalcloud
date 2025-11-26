<?php
/**
 * Sync bln_invoice from loan_case_bill_main to loan_case_invoice_main
 * 
 * This ensures invoice-level bln_invoice matches bill-level bln_invoice
 * 
 * Run in Laravel Tinker:
 * php artisan tinker
 * require 'sync_bln_invoice.php';
 * syncBlnInvoice();
 */

use Illuminate\Support\Facades\DB;

function syncBlnInvoice($dryRun = true) {
    echo "=== SYNCING bln_invoice FROM BILL TO INVOICE ===\n\n";
    
    if ($dryRun) {
        echo "⚠️  DRY RUN MODE - No changes will be made\n";
        echo "   To actually sync, call: syncBlnInvoice(false);\n\n";
    }
    
    // Check current mismatch
    $mismatchCount = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as bm', 'im.loan_case_main_bill_id', '=', 'bm.id')
        ->where('im.status', '<>', 99)
        ->where('bm.status', '<>', 99)
        ->whereColumn('im.bln_invoice', '!=', 'bm.bln_invoice')
        ->count();
    
    echo "Current mismatches: {$mismatchCount}\n\n";
    
    if ($mismatchCount == 0) {
        echo "✅ No mismatches found. Everything is in sync!\n";
        return;
    }
    
    // Show sample mismatches
    echo "Sample mismatches (first 10):\n";
    $mismatches = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as bm', 'im.loan_case_main_bill_id', '=', 'bm.id')
        ->select(
            'im.id as invoice_id',
            'im.invoice_no',
            'im.bln_invoice as invoice_bln_invoice',
            'bm.id as bill_id',
            'bm.bln_invoice as bill_bln_invoice',
            'bm.invoice_branch_id'
        )
        ->where('im.status', '<>', 99)
        ->where('bm.status', '<>', 99)
        ->whereColumn('im.bln_invoice', '!=', 'bm.bln_invoice')
        ->limit(10)
        ->get();
    
    foreach ($mismatches as $mismatch) {
        echo "  Invoice {$mismatch->invoice_no} (ID: {$mismatch->invoice_id}):\n";
        echo "    Invoice bln_invoice: {$mismatch->invoice_bln_invoice}\n";
        echo "    Bill bln_invoice: {$mismatch->bill_bln_invoice}\n";
        echo "    Bill branch: " . ($mismatch->invoice_branch_id ?? 'NULL') . "\n\n";
    }
    
    if ($dryRun) {
        echo "\n⚠️  DRY RUN - Would update {$mismatchCount} invoices\n";
        echo "   Call syncBlnInvoice(false) to perform the actual update.\n";
        return;
    }
    
    // Perform the sync
    echo "Updating invoices...\n";
    $updated = DB::update("
        UPDATE loan_case_invoice_main im
        INNER JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
        SET im.bln_invoice = bm.bln_invoice
        WHERE im.status <> 99
          AND bm.status <> 99
          AND im.bln_invoice != bm.bln_invoice
    ");
    
    echo "✅ Updated {$updated} invoices\n\n";
    
    // Verify after update
    $remainingMismatches = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as bm', 'im.loan_case_main_bill_id', '=', 'bm.id')
        ->where('im.status', '<>', 99)
        ->where('bm.status', '<>', 99)
        ->whereColumn('im.bln_invoice', '!=', 'bm.bln_invoice')
        ->count();
    
    if ($remainingMismatches == 0) {
        echo "✅ All invoices are now in sync!\n";
    } else {
        echo "⚠️  Warning: {$remainingMismatches} mismatches still remain\n";
    }
    
    echo "\n=== END ===\n";
}



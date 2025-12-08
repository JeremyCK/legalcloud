<?php
/**
 * Sync sst_inv from loan_case_bill_main.sst to loan_case_invoice_main.sst_inv
 * 
 * This ensures invoice-level sst_inv matches bill-level sst
 * 
 * Run in Laravel Tinker:
 * php artisan tinker
 * require 'sync_sst_inv_from_bill.php';
 * syncSstInvFromBill();
 */

use Illuminate\Support\Facades\DB;

function syncSstInvFromBill($dryRun = true) {
    echo "=== SYNCING sst_inv FROM BILL TO INVOICE ===\n\n";
    
    if ($dryRun) {
        echo "⚠️  DRY RUN MODE - No changes will be made\n";
        echo "   To actually sync, call: syncSstInvFromBill(false);\n\n";
    }
    
    // Check current mismatch
    $mismatchCount = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as bm', 'im.loan_case_main_bill_id', '=', 'bm.id')
        ->where('im.status', '<>', 99)
        ->where('bm.status', '<>', 99)
        ->whereColumn(DB::raw('COALESCE(im.sst_inv, 0)'), '!=', DB::raw('COALESCE(bm.sst, 0)'))
        ->count();
    
    echo "Current mismatches: {$mismatchCount}\n\n";
    
    if ($mismatchCount == 0) {
        echo "✅ No mismatches found. Everything is in sync!\n";
        
        // Check if Ramakrishnan invoices have sst_inv > 0
        echo "\nChecking Ramakrishnan invoices...\n";
        $ramakrishnanWithSst = DB::table('loan_case_invoice_main as im')
            ->leftJoin('loan_case_bill_main as bm', 'im.loan_case_main_bill_id', '=', 'bm.id')
            ->where('bm.invoice_branch_id', 4)
            ->where('im.bln_sst', 0)
            ->where('im.bln_invoice', 1)
            ->where('bm.bln_invoice', 1)
            ->where('im.sst_inv', '>', 0)
            ->count();
        
        $ramakrishnanTotal = DB::table('loan_case_invoice_main as im')
            ->leftJoin('loan_case_bill_main as bm', 'im.loan_case_main_bill_id', '=', 'bm.id')
            ->where('bm.invoice_branch_id', 4)
            ->where('im.bln_sst', 0)
            ->where('im.bln_invoice', 1)
            ->where('bm.bln_invoice', 1)
            ->count();
        
        echo "Ramakrishnan invoices with sst_inv > 0: {$ramakrishnanWithSst} / {$ramakrishnanTotal}\n";
        
        if ($ramakrishnanWithSst == 0 && $ramakrishnanTotal > 0) {
            echo "⚠️  WARNING: Ramakrishnan invoices have sst_inv = 0 or NULL!\n";
            echo "   This is why they're not appearing in search results.\n";
        }
        
        return;
    }
    
    // Show sample mismatches
    echo "Sample mismatches (first 10):\n";
    $mismatches = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as bm', 'im.loan_case_main_bill_id', '=', 'bm.id')
        ->select(
            'im.id as invoice_id',
            'im.invoice_no',
            'im.sst_inv as invoice_sst',
            'bm.id as bill_id',
            'bm.sst as bill_sst',
            'bm.invoice_branch_id'
        )
        ->where('im.status', '<>', 99)
        ->where('bm.status', '<>', 99)
        ->whereColumn(DB::raw('COALESCE(im.sst_inv, 0)'), '!=', DB::raw('COALESCE(bm.sst, 0)'))
        ->limit(10)
        ->get();
    
    foreach ($mismatches as $mismatch) {
        echo "  Invoice {$mismatch->invoice_no} (ID: {$mismatch->invoice_id}):\n";
        echo "    Invoice sst_inv: " . ($mismatch->invoice_sst ?? 'NULL') . "\n";
        echo "    Bill sst: " . ($mismatch->bill_sst ?? 'NULL') . "\n";
        echo "    Bill branch: " . ($mismatch->invoice_branch_id ?? 'NULL') . "\n\n";
    }
    
    if ($dryRun) {
        echo "\n⚠️  DRY RUN - Would update {$mismatchCount} invoices\n";
        echo "   Call syncSstInvFromBill(false) to perform the actual update.\n";
        return;
    }
    
    // Perform the sync
    echo "Updating invoices...\n";
    $updated = DB::update("
        UPDATE loan_case_invoice_main im
        INNER JOIN loan_case_bill_main bm ON im.loan_case_main_bill_id = bm.id
        SET im.sst_inv = COALESCE(bm.sst, 0)
        WHERE im.status <> 99
          AND bm.status <> 99
          AND COALESCE(im.sst_inv, 0) != COALESCE(bm.sst, 0)
    ");
    
    echo "✅ Updated {$updated} invoices\n\n";
    
    // Verify after update
    $remainingMismatches = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as bm', 'im.loan_case_main_bill_id', '=', 'bm.id')
        ->where('im.status', '<>', 99)
        ->where('bm.status', '<>', 99)
        ->whereColumn(DB::raw('COALESCE(im.sst_inv, 0)'), '!=', DB::raw('COALESCE(bm.sst, 0)'))
        ->count();
    
    if ($remainingMismatches == 0) {
        echo "✅ All invoices are now in sync!\n";
    } else {
        echo "⚠️  Warning: {$remainingMismatches} mismatches still remain\n";
    }
    
    echo "\n=== END ===\n";
}







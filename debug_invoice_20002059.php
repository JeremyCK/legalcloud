<?php
/**
 * Debug script to investigate invoice 20002059
 * Run this in Laravel Tinker: php artisan tinker
 * Then paste this code
 */

use Illuminate\Support\Facades\DB;

echo "=== DEBUGGING INVOICE 20002059 ===\n\n";

try {
    // Step 1: Check if invoice 20002059 exists in loan_case_invoice_main
    echo "1. Checking loan_case_invoice_main:\n";
    $invoiceData = DB::table('loan_case_invoice_main')
        ->where('invoice_no', '20002059')
        ->where('status', '<>', 99)
        ->first();
    
    if ($invoiceData) {
        echo "✅ Found in loan_case_invoice_main:\n";
        echo "   ID: {$invoiceData->id}\n";
        echo "   Invoice No: {$invoiceData->invoice_no}\n";
        echo "   Status: {$invoiceData->status}\n";
        echo "   Bill ID: {$invoiceData->loan_case_main_bill_id}\n";
        echo "   Transferred SST: {$invoiceData->transferred_sst_amt}\n";
        echo "   SST Inv: {$invoiceData->sst_inv}\n";
        echo "   Bln SST: {$invoiceData->bln_sst}\n";
    } else {
        echo "❌ Not found in loan_case_invoice_main\n";
    }

    // Step 2: Check if corresponding bill exists in loan_case_bill_main
    echo "\n2. Checking loan_case_bill_main:\n";
    $billData = DB::table('loan_case_bill_main as b')
        ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
        ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
        ->select('b.*', 'l.case_ref_no', 'c.name as client_name')
        ->where('b.invoice_no', '20002059')
        ->where('b.status', '<>', 99)
        ->first();
    
    if ($billData) {
        echo "✅ Found in loan_case_bill_main:\n";
        echo "   ID: {$billData->id}\n";
        echo "   Invoice No: {$billData->invoice_no}\n";
        echo "   Status: {$billData->status}\n";
        echo "   Bln Invoice: {$billData->bln_invoice}\n";
        echo "   Bln SST: {$billData->bln_sst}\n";
        echo "   Branch ID: {$billData->invoice_branch_id}\n";
        echo "   Case Ref: {$billData->case_ref_no}\n";
        echo "   Client: {$billData->client_name}\n";
    } else {
        echo "❌ Not found in loan_case_bill_main\n";
    }

    // Step 3: Check relationship
    echo "\n3. Checking relationship:\n";
    $relationship = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->select('im.id as invoice_id', 'im.invoice_no', 'im.loan_case_main_bill_id', 
                'b.id as bill_id', 'b.invoice_no as bill_invoice_no', 'b.bln_invoice', 'b.bln_sst')
        ->where('im.invoice_no', '20002059')
        ->where('im.status', '<>', 99)
        ->first();
    
    if ($relationship) {
        echo "✅ Relationship found:\n";
        echo "   Invoice ID: {$relationship->invoice_id}\n";
        echo "   Invoice No: {$relationship->invoice_no}\n";
        echo "   Bill ID: {$relationship->bill_id}\n";
        echo "   Bill Invoice No: {$relationship->bill_invoice_no}\n";
        echo "   Bill Bln Invoice: {$relationship->bln_invoice}\n";
        echo "   Bill Bln SST: {$relationship->bln_sst}\n";
    } else {
        echo "❌ No relationship found\n";
    }

    // Step 4: Check SST transfers
    echo "\n4. Checking SST transfers:\n";
    $sstTransfers = DB::table('sst_details as sd')
        ->leftJoin('sst_main as sm', 'sm.id', '=', 'sd.sst_main_id')
        ->select('sd.*', 'sm.transaction_id', 'sm.payment_date')
        ->whereIn('sd.loan_case_invoice_main_id', function($query) {
            $query->select('id')
                  ->from('loan_case_invoice_main')
                  ->where('invoice_no', '20002059');
        })
        ->get();
    
    if ($sstTransfers->count() > 0) {
        echo "✅ Found {$sstTransfers->count()} SST transfers:\n";
        foreach ($sstTransfers as $transfer) {
            echo "   SST Detail ID: {$transfer->id}\n";
            echo "   Amount: {$transfer->amount}\n";
            echo "   Transaction ID: {$transfer->transaction_id}\n";
            echo "   Payment Date: {$transfer->payment_date}\n";
        }
    } else {
        echo "❌ No SST transfers found\n";
    }

    // Step 5: Check original SST logic conditions
    echo "\n5. Original SST logic conditions:\n";
    $originalSSTConditions = DB::table('loan_case_bill_main as b')
        ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
        ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
        ->select('b.*', 'l.case_ref_no', 'c.name as client_name')
        ->where('b.invoice_no', '20002059')
        ->where('b.status', '<>', 99)
        ->where('b.bln_invoice', '=', 1)
        ->first();
    
    if ($originalSSTConditions) {
        echo "✅ Original SST conditions met:\n";
        echo "   Status: {$originalSSTConditions->status} (should be <> 99) ✅\n";
        echo "   Bln Invoice: {$originalSSTConditions->bln_invoice} (should be 1) ✅\n";
        echo "   Bln SST: {$originalSSTConditions->bln_sst} (should be 0 for available) " . ($originalSSTConditions->bln_sst == 0 ? "✅" : "❌") . "\n";
        echo "   Branch ID: {$originalSSTConditions->invoice_branch_id}\n";
    } else {
        echo "❌ Original SST conditions NOT met (bill not found or not billable)\n";
    }

    // Step 6: Check SST v2 logic conditions
    echo "\n6. SST v2 logic conditions:\n";
    $sstV2Conditions = DB::table('loan_case_invoice_main as im')
        ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
        ->select('im.*', 'b.bln_invoice', 'b.invoice_branch_id', 'b.status as bill_status')
        ->where('im.invoice_no', '20002059')
        ->where('im.status', '<>', 99)
        ->first();
    
    if ($sstV2Conditions) {
        echo "✅ SST v2 conditions check:\n";
        echo "   Invoice Status: {$sstV2Conditions->status} (should be <> 99) ✅\n";
        echo "   Transferred to Office Bank: {$sstV2Conditions->transferred_to_office_bank} (should be 0) " . ($sstV2Conditions->transferred_to_office_bank == 0 ? "✅" : "❌") . "\n";
        echo "   Has Bill ID: " . ($sstV2Conditions->loan_case_main_bill_id ? "✅" : "❌") . "\n";
        echo "   Bill Bln Invoice: {$sstV2Conditions->bln_invoice} (should be 1) " . ($sstV2Conditions->bln_invoice == 1 ? "✅" : "❌") . "\n";
        echo "   SST Inv: {$sstV2Conditions->sst_inv} (should be > 0) " . ($sstV2Conditions->sst_inv > 0 ? "✅" : "❌") . "\n";
        echo "   Bln SST: {$sstV2Conditions->bln_sst} (should be 0 for available) " . ($sstV2Conditions->bln_sst == 0 ? "✅" : "❌") . "\n";
    } else {
        echo "❌ SST v2 conditions NOT met\n";
    }

    echo "\n=== INVESTIGATION COMPLETE ===\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

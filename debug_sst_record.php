<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING SST RECORD ID 96 ===\n\n";

try {
    // Check if SST main record exists
    echo "1. Checking SST main record (ID: 96):\n";
    $sstMain = DB::table('sst_main')->where('id', 96)->first();
    
    if ($sstMain) {
        echo "✅ Found SST main record:\n";
        echo "   ID: {$sstMain->id}\n";
        echo "   Transaction ID: {$sstMain->transaction_id}\n";
        echo "   Payment Date: {$sstMain->payment_date}\n";
        echo "   Amount: {$sstMain->amount}\n";
        echo "   Branch ID: {$sstMain->branch_id}\n";
        echo "   Status: {$sstMain->status}\n";
        echo "   Created At: {$sstMain->created_at}\n";
    } else {
        echo "❌ SST main record not found\n";
    }
    
    // Check SST details
    echo "\n2. Checking SST details for main ID 96:\n";
    $sstDetails = DB::table('sst_details')->where('sst_main_id', 96)->get();
    
    if ($sstDetails->count() > 0) {
        echo "✅ Found {$sstDetails->count()} SST detail records:\n";
        foreach ($sstDetails as $detail) {
            echo "   Detail ID: {$detail->id}\n";
            echo "   Bill ID: {$detail->loan_case_main_bill_id}\n";
            echo "   Invoice ID: {$detail->loan_case_invoice_main_id}\n";
            echo "   Amount: {$detail->amount}\n";
            echo "   Status: {$detail->status}\n\n";
        }
    } else {
        echo "❌ No SST details found\n";
    }
    
    // Check if there are any related invoices
    echo "3. Checking related invoices:\n";
    if ($sstDetails->count() > 0) {
        foreach ($sstDetails as $detail) {
            if ($detail->loan_case_invoice_main_id) {
                $invoice = DB::table('loan_case_invoice_main')
                    ->where('id', $detail->loan_case_invoice_main_id)
                    ->first();
                
                if ($invoice) {
                    echo "✅ Invoice found (ID: {$invoice->id}):\n";
                    echo "   Invoice No: {$invoice->invoice_no}\n";
                    echo "   Status: {$invoice->status}\n";
                    echo "   SST Inv: {$invoice->sst_inv}\n";
                    echo "   Transferred SST: {$invoice->transferred_sst_amt}\n";
                } else {
                    echo "❌ Invoice not found (ID: {$detail->loan_case_invoice_main_id})\n";
                }
            }
            
            if ($detail->loan_case_main_bill_id) {
                $bill = DB::table('loan_case_bill_main')
                    ->where('id', $detail->loan_case_main_bill_id)
                    ->first();
                
                if ($bill) {
                    echo "✅ Bill found (ID: {$bill->id}):\n";
                    echo "   Invoice No: {$bill->invoice_no}\n";
                    echo "   Status: {$bill->status}\n";
                    echo "   Bln Invoice: {$bill->bln_invoice}\n";
                    echo "   Bln SST: {$bill->bln_sst}\n";
                } else {
                    echo "❌ Bill not found (ID: {$detail->loan_case_main_bill_id})\n";
                }
            }
        }
    }
    
    // Check user permissions
    echo "\n4. Checking user access:\n";
    $user = auth()->user();
    if ($user) {
        echo "✅ User authenticated: {$user->name} (ID: {$user->id})\n";
        echo "   Branch ID: {$user->branch_id}\n";
        echo "   Menu Roles: {$user->menuroles}\n";
    } else {
        echo "❌ User not authenticated\n";
    }
    
    echo "\n=== INVESTIGATION COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

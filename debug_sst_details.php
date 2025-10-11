<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Debug SST Details Query ===\n";

$id = 91; // The SST main ID from the URL

echo "Checking SST Main ID: $id\n\n";

// Check if SST main exists
$sstMain = DB::table('sst_main')->where('id', $id)->first();
if (!$sstMain) {
    echo "❌ SST Main record not found!\n";
    exit;
}

echo "✅ SST Main found: ID=$sstMain->id, Amount=$sstMain->amount\n\n";

// Check SST details
$sstDetails = DB::table('sst_details')->where('sst_main_id', $id)->get();
echo "SST Details count: " . $sstDetails->count() . "\n";

foreach ($sstDetails as $detail) {
    echo "SST Detail ID: $detail->id, Invoice Main ID: $detail->loan_case_invoice_main_id, Amount: $detail->amount\n";
}

echo "\n=== Testing the join query ===\n";

$SSTDetails = DB::table('sst_details as sd')
    ->leftJoin('loan_case_invoice_main as im', 'im.id', '=', 'sd.loan_case_invoice_main_id')
    ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
    ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
    ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
    ->where('sd.sst_main_id', $id)
    ->select(
        'sd.*',
        'im.invoice_no',
        'im.Invoice_date as invoice_date',
        'im.amount as total_amount',
        'b.collected_amt as collected_amount',
        'b.payment_receipt_date as payment_date',
        'l.case_ref_no',
        'c.name as client_name'
    )
    ->get();

echo "Join query result count: " . $SSTDetails->count() . "\n";

foreach ($SSTDetails as $detail) {
    echo "\n--- Detail ID: $detail->id ---\n";
    echo "Invoice No: " . ($detail->invoice_no ?? 'NULL') . "\n";
    echo "Invoice Date: " . ($detail->invoice_date ?? 'NULL') . "\n";
    echo "Total Amount: " . ($detail->total_amount ?? 'NULL') . "\n";
    echo "Collected Amount: " . ($detail->collected_amount ?? 'NULL') . "\n";
    echo "Payment Date: " . ($detail->payment_date ?? 'NULL') . "\n";
    echo "Case Ref No: " . ($detail->case_ref_no ?? 'NULL') . "\n";
    echo "Client Name: " . ($detail->client_name ?? 'NULL') . "\n";
    echo "SST Amount: " . ($detail->amount ?? 'NULL') . "\n";
}

echo "\n=== Checking individual tables ===\n";

// Check loan_case_invoice_main
foreach ($sstDetails as $detail) {
    $invoiceMain = DB::table('loan_case_invoice_main')->where('id', $detail->loan_case_invoice_main_id)->first();
    if ($invoiceMain) {
        echo "Invoice Main ID: $invoiceMain->id\n";
        echo "  - Invoice No: " . ($invoiceMain->invoice_no ?? 'NULL') . "\n";
        echo "  - Invoice Date: " . ($invoiceMain->Invoice_date ?? 'NULL') . "\n";
        echo "  - Amount: " . ($invoiceMain->amount ?? 'NULL') . "\n";
        echo "  - Bill ID: " . ($invoiceMain->loan_case_main_bill_id ?? 'NULL') . "\n";
        
        // Check bill
        if ($invoiceMain->loan_case_main_bill_id) {
            $bill = DB::table('loan_case_bill_main')->where('id', $invoiceMain->loan_case_main_bill_id)->first();
            if ($bill) {
                echo "  - Bill ID: $bill->id\n";
                echo "  - Collected Amt: " . ($bill->collected_amt ?? 'NULL') . "\n";
                echo "  - Payment Date: " . ($bill->payment_receipt_date ?? 'NULL') . "\n";
                echo "  - Case ID: " . ($bill->case_id ?? 'NULL') . "\n";
                
                // Check case
                if ($bill->case_id) {
                    $case = DB::table('loan_case')->where('id', $bill->case_id)->first();
                    if ($case) {
                        echo "  - Case ID: $case->id\n";
                        echo "  - Case Ref No: " . ($case->case_ref_no ?? 'NULL') . "\n";
                        echo "  - Customer ID: " . ($case->customer_id ?? 'NULL') . "\n";
                        
                        // Check client
                        if ($case->customer_id) {
                            $client = DB::table('client')->where('id', $case->customer_id)->first();
                            if ($client) {
                                echo "  - Client ID: $client->id\n";
                                echo "  - Client Name: " . ($client->name ?? 'NULL') . "\n";
                            } else {
                                echo "  - ❌ Client not found for ID: $case->customer_id\n";
                            }
                        } else {
                            echo "  - ❌ No customer_id in case\n";
                        }
                    } else {
                        echo "  - ❌ Case not found for ID: $bill->case_id\n";
                    }
                } else {
                    echo "  - ❌ No case_id in bill\n";
                }
            } else {
                echo "  - ❌ Bill not found for ID: $invoiceMain->loan_case_main_bill_id\n";
            }
        } else {
            echo "  - ❌ No bill_id in invoice_main\n";
        }
    } else {
        echo "❌ Invoice Main not found for ID: $detail->loan_case_invoice_main_id\n";
    }
    echo "\n";
}

?>

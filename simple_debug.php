<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Simple SST Details Debug ===\n";

$id = 91;

// Check sst_details
$details = DB::table('sst_details')->where('sst_main_id', $id)->get();
echo "SST Details found: " . $details->count() . "\n";

foreach ($details as $detail) {
    echo "Detail ID: $detail->id, Invoice Main ID: $detail->loan_case_invoice_main_id, Amount: $detail->amount\n";
    
    // Check if invoice main exists
    $invoiceMain = DB::table('loan_case_invoice_main')->where('id', $detail->loan_case_invoice_main_id)->first();
    if ($invoiceMain) {
        echo "  Invoice Main exists: Invoice No = " . ($invoiceMain->invoice_no ?? 'NULL') . "\n";
    } else {
        echo "  ❌ Invoice Main NOT found!\n";
    }
}

?>

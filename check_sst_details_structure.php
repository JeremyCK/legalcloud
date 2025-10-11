<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking SST Details Table Structure ===\n";

// Check if loan_case_invoice_main_id column exists
$columns = DB::select("DESCRIBE sst_details");
echo "SST Details table columns:\n";
foreach ($columns as $column) {
    echo "- " . $column->Field . " (" . $column->Type . ")\n";
}

echo "\n=== Checking SST Details Data ===\n";
$details = DB::table('sst_details')->where('sst_main_id', 91)->get();
echo "SST Details for main_id 91: " . $details->count() . " records\n";

foreach ($details as $detail) {
    echo "ID: $detail->id, Main ID: $detail->sst_main_id, Amount: $detail->amount\n";
    if (isset($detail->loan_case_invoice_main_id)) {
        echo "  Invoice Main ID: " . $detail->loan_case_invoice_main_id . "\n";
    } else {
        echo "  ❌ loan_case_invoice_main_id column does not exist!\n";
    }
}

?>

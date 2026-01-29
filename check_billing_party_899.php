<?php

/**
 * Check Billing Party 899 - What's Missing?
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\InvoiceBillingParty;

echo "=== Checking Billing Party 899 ===\n\n";

$party = InvoiceBillingParty::find(899);

if (!$party) {
    echo "❌ Billing Party ID 899 not found\n";
    exit(1);
}

echo "Billing Party ID: {$party->id}\n";
echo "Bill ID: {$party->loan_case_main_bill_id}\n";
echo "Current completed status: " . ($party->completed == 1 ? "Completed (1)" : "Pending (0)") . "\n\n";

// Get mandatory fields
$mandatoryFields = [
    'customer_code',
    'customer_name',
    'customer_category',
    'id_no',
    'id_type',
    'tin',
    'address_1',
    'postcode',
    'city',
    'state',
    'country',
    'phone1'
];

echo "=== Mandatory Fields Check ===\n";
$missingFields = [];
$filledFields = [];

foreach ($mandatoryFields as $field) {
    $value = $party->$field ?? null;
    if (empty($value)) {
        $missingFields[] = $field;
        echo "❌ {$field}: EMPTY\n";
    } else {
        $filledFields[] = $field;
        echo "✅ {$field}: " . (strlen($value) > 50 ? substr($value, 0, 50) . "..." : $value) . "\n";
    }
}

echo "\n=== Summary ===\n";
echo "Filled fields: " . count($filledFields) . " / " . count($mandatoryFields) . "\n";
echo "Missing fields: " . count($missingFields) . "\n";

if (count($missingFields) > 0) {
    echo "\nMissing fields:\n";
    foreach ($missingFields as $field) {
        echo "  - {$field}\n";
    }
    echo "\n⚠️  This billing party cannot be marked as 'Completed' until all mandatory fields are filled.\n";
} else {
    echo "\n✅ All mandatory fields are filled!\n";
    echo "The billing party should be marked as completed. Let me check why it's not...\n";
    
    // Check if it should be completed
    $allFilled = true;
    foreach ($mandatoryFields as $field) {
        if (empty($party->$field)) {
            $allFilled = false;
            break;
        }
    }
    
    if ($allFilled && $party->completed != 1) {
        echo "\n⚠️  ISSUE: All fields are filled but status is still Pending!\n";
        echo "Updating status to Completed...\n";
        $party->completed = 1;
        $party->save();
        echo "✅ Fixed! Billing party is now marked as Completed.\n";
    }
}

<?php

/**
 * Check Detail 1589 - Which billing party does the invoice use?
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\EInvoiceDetails;
use App\Models\LoanCaseInvoiceMain;
use App\Models\InvoiceBillingParty;

echo "=== Checking Detail 1589 Billing Party ===\n\n";

$detail = EInvoiceDetails::find(1589);

if (!$detail) {
    echo "❌ Detail ID 1589 not found\n";
    exit(1);
}

$invoice = LoanCaseInvoiceMain::find($detail->loan_case_invoice_id);

echo "Detail ID: {$detail->id}\n";
echo "Invoice ID: {$detail->loan_case_invoice_id}\n";
echo "Invoice No: " . ($invoice ? $invoice->invoice_no : 'N/A') . "\n";
echo "Bill ID: {$detail->loan_case_main_bill_id}\n";
echo "Invoice bill_party_id: " . ($invoice ? ($invoice->bill_party_id ?? 'NULL') : 'N/A') . "\n\n";

// Get invoice's billing party
if ($invoice && $invoice->bill_party_id) {
    $invoiceBillingParty = InvoiceBillingParty::find($invoice->bill_party_id);
    if ($invoiceBillingParty) {
        echo "Invoice's Billing Party (ID {$invoice->bill_party_id}):\n";
        echo "  Status: " . ($invoiceBillingParty->completed == 1 ? "Completed" : "Pending") . "\n";
        echo "  Belongs to Bill ID: {$invoiceBillingParty->loan_case_main_bill_id}\n";
        echo "  This is what shows on the detail page!\n\n";
    }
}

// Get all billing parties for the bill
$billingParties = InvoiceBillingParty::where('loan_case_main_bill_id', $detail->loan_case_main_bill_id)->get();

echo "All Billing Parties for Bill ID {$detail->loan_case_main_bill_id}:\n";
foreach ($billingParties as $party) {
    echo "  Party ID {$party->id}: " . ($party->completed == 1 ? "Completed" : "Pending") . "\n";
}

echo "\n=== Explanation ===\n";
echo "The detail page shows the invoice's billing party status (bill_party_id).\n";
echo "But einvoice_details.client_profile_completed should be based on whether\n";
echo "ALL billing parties for the bill are completed.\n\n";

if ($invoice && $invoice->bill_party_id) {
    $invoiceBillingParty = InvoiceBillingParty::find($invoice->bill_party_id);
    if ($invoiceBillingParty && $invoiceBillingParty->completed == 1) {
        echo "⚠️  The invoice's billing party IS completed, which is why the detail page shows 'Completed'.\n";
        echo "But the detail record is marked as Pending because not ALL billing parties for the bill are completed.\n";
        echo "\nThis is a data consistency issue. The detail should be marked as Completed if:\n";
        echo "1. All billing parties for the bill are completed, OR\n";
        echo "2. The invoice's billing party is completed (even if it's from a different bill)\n";
    }
}

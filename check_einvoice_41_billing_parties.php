<?php

/**
 * Check E-Invoice 41 Billing Parties Issue
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\EInvoiceMain;
use App\Models\EInvoiceDetails;
use App\Models\InvoiceBillingParty;
use App\Models\LoanCaseBillMain;
use App\Models\LoanCaseInvoiceMain;

echo "=== Checking E-Invoice 41 Billing Parties ===\n\n";

$main = EInvoiceMain::find(41);

if (!$main) {
    echo "❌ E-Invoice Main ID 41 not found\n";
    exit(1);
}

echo "E-Invoice Main ID: {$main->id} ({$main->ref_no})\n\n";

// Get details with no billing parties
$details = EInvoiceDetails::where('einvoice_main_id', $main->id)->get();

$detailsWithNoBillingParties = [];

foreach ($details as $detail) {
    $billId = $detail->loan_case_main_bill_id;
    $invoiceId = $detail->loan_case_invoice_id;
    
    $billingParties = InvoiceBillingParty::where('loan_case_main_bill_id', $billId)->get();
    
    if ($billingParties->count() == 0) {
        // Get bill and invoice info
        $bill = LoanCaseBillMain::find($billId);
        $invoice = LoanCaseInvoiceMain::find($invoiceId);
        
        $detailsWithNoBillingParties[] = [
            'detail_id' => $detail->id,
            'bill_id' => $billId,
            'invoice_id' => $invoiceId,
            'invoice_no' => $invoice ? $invoice->invoice_no : 'N/A',
            'bill_invoice_no' => $bill ? $bill->invoice_no : 'N/A',
            'bill_party_id' => $invoice ? $invoice->bill_party_id : null
        ];
    }
}

echo "=== Details with No Billing Parties ===\n";
echo "Count: " . count($detailsWithNoBillingParties) . "\n\n";

foreach ($detailsWithNoBillingParties as $detail) {
    echo "Detail ID: {$detail['detail_id']}\n";
    echo "  Bill ID: {$detail['bill_id']}\n";
    echo "  Invoice ID: {$detail['invoice_id']}\n";
    echo "  Invoice No: {$detail['invoice_no']}\n";
    echo "  Bill Invoice No: {$detail['bill_invoice_no']}\n";
    echo "  Invoice bill_party_id: " . ($detail['bill_party_id'] ?? 'NULL') . "\n";
    
    // Check if there are any billing parties for this bill at all
    $allBillingParties = InvoiceBillingParty::where('loan_case_main_bill_id', $detail['bill_id'])->get();
    echo "  Total billing parties for this bill: " . $allBillingParties->count() . "\n";
    
    // Check if invoice has a bill_party_id that should link to a billing party
    if ($detail['bill_party_id']) {
        $billingParty = InvoiceBillingParty::find($detail['bill_party_id']);
        if ($billingParty) {
            echo "  ⚠️  Invoice has bill_party_id={$detail['bill_party_id']} but billing party belongs to bill_id={$billingParty->loan_case_main_bill_id}\n";
            if ($billingParty->loan_case_main_bill_id != $detail['bill_id']) {
                echo "     MISMATCH! Billing party belongs to different bill.\n";
            }
        } else {
            echo "  ⚠️  Invoice has bill_party_id={$detail['bill_party_id']} but billing party not found!\n";
        }
    }
    
    echo "\n";
}

echo "=== Options ===\n";
echo "1. These details have no billing parties, so they can't be marked as 'Completed'\n";
echo "2. If these invoices don't need billing parties, we could mark them as 'Completed' by default\n";
echo "3. Or we need to create/assign billing parties for these bills\n\n";

echo "=== Recommendation ===\n";
echo "If these invoices truly don't need billing parties (e.g., internal invoices),\n";
echo "we should mark details without billing parties as 'Completed' by default.\n";
echo "This way, the main status will be 'Completed' if all details with billing parties are completed.\n";

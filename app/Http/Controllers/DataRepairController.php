<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\LedgerEntries;
use App\Models\LedgerEntriesV2;
use App\Models\LoanCaseInvoiceMain;
use App\Models\TransferFeeDetails;
use App\Models\TransferFeeMain;

class DataRepairController extends Controller
{
    public function index()
    {
        $current_user = auth()->user();
        
        return view('dashboard.data-repair.index', [
            'current_user' => $current_user,
            'locales' => [], // Empty array since locales functionality is commented out
            'appLocale' => 'en' // Default locale
        ]);
    }

    public function getMissingEntries(Request $request)
    {
        Log::info('DataRepairController::getMissingEntries called');
        $limit = $request->get('limit', 50);
        $offset = $request->get('offset', 0);
        $search = $request->get('search', '');
        
        $query = DB::table('loan_case_invoice_main as lcim')
            ->leftJoin('loan_case_bill_main as lcbm', 'lcim.loan_case_main_bill_id', '=', 'lcbm.id')
            ->leftJoin('loan_case as lc', 'lcbm.case_id', '=', 'lc.id')
            ->leftJoin('transfer_fee_details as tfd', 'lcim.id', '=', 'tfd.loan_case_invoice_main_id')
            ->leftJoin('transfer_fee_main as tfm', 'tfd.transfer_fee_main_id', '=', 'tfm.id')
            ->select(
                'lcim.id as invoice_id',
                'lcim.invoice_no',
                'lcim.reimbursement_amount',
                'lcim.reimbursement_sst',
                'tfm.id as transfer_fee_main_id',
                'tfm.transaction_id',
                'tfm.transfer_from',
                'tfm.transfer_to',
                'tfm.transfer_date',
                'tfm.purpose',
                'tfd.id as transfer_fee_detail_id',
                'tfd.status as tfd_status',
                'lcbm.case_id',
                'lcbm.id as bill_id',
                'lc.case_ref_no'
            )
            ->where('lcim.status', '<>', 99)
            ->where('lcim.reimbursement_amount', '>', 0);
            
        // Add search functionality
        if (!empty($search)) {
            // Support both comma and space separators
            $searchTerms = preg_split('/[,\s]+/', $search);
            $searchTerms = array_filter(array_map('trim', $searchTerms));
            Log::info('Searching for invoice numbers: ' . implode(', ', $searchTerms));
            $query->where(function($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    if (!empty($term)) {
                        $q->orWhere('lcim.invoice_no', 'LIKE', '%' . $term . '%');
                    }
                }
            });
        }
        
        $query->orderBy('lcim.id', 'desc')
            ->limit($limit)
            ->offset($offset);

        try {
            $invoices = $query->get();
            Log::info('Found ' . $invoices->count() . ' invoices');
            
            $results = [];
            foreach ($invoices as $invoice) {
                $missingEntries = $this->checkMissingEntries($invoice);
                if (!empty($missingEntries)) {
                    $results[] = [
                        'invoice' => $invoice,
                        'missing_entries' => $missingEntries,
                        'total_missing' => count($missingEntries)
                    ];
                }
            }
            
            Log::info('Found ' . count($results) . ' invoices with missing entries');
            
            return response()->json([
                'success' => true,
                'data' => $results,
                'total' => count($results)
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getMissingEntries: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function fixSingleEntry(Request $request)
    {
        $invoiceId = $request->get('invoice_id');
        $entryType = $request->get('entry_type');
        
        try {
            $invoice = $this->getInvoiceDetails($invoiceId);
            if (!$invoice) {
                return response()->json(['success' => false, 'message' => 'Invoice not found']);
            }
            
            $result = $this->createMissingEntry($invoice, $entryType);
            
            return response()->json([
                'success' => true,
                'message' => "Successfully created {$entryType} entry",
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function fixAllEntries(Request $request)
    {
        $invoiceId = $request->get('invoice_id');
        
        try {
            $invoice = $this->getInvoiceDetails($invoiceId);
            if (!$invoice) {
                return response()->json(['success' => false, 'message' => 'Invoice not found']);
            }
            
            $missingEntries = $this->checkMissingEntries($invoice);
            $results = [];
            
            foreach ($missingEntries as $entryType) {
                $result = $this->createMissingEntry($invoice, $entryType);
                $results[] = [
                    'type' => $entryType,
                    'success' => $result['success'],
                    'message' => $result['message']
                ];
            }
            
            return response()->json([
                'success' => true,
                'message' => 'All entries processed',
                'data' => $results
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    private function checkMissingEntries($invoice)
    {
        $missing = [];
        
        // If invoice has no transfer fee details, all entries are missing
        if (empty($invoice->transfer_fee_detail_id) || $invoice->tfd_status == 99) {
            $missing[] = 'REIMB_OUT';
            $missing[] = 'REIMB_IN';
            if ($invoice->reimbursement_sst > 0) {
                $missing[] = 'REIMB_SST_OUT';
                $missing[] = 'REIMB_SST_IN';
            }
            return $missing;
        }
        
        // Check REIMB_OUT
        if (!$this->checkLedgerEntry($invoice->invoice_id, 'REIMB_OUT', $invoice->reimbursement_amount)) {
            $missing[] = 'REIMB_OUT';
        }
        
        // Check REIMB_IN
        if (!$this->checkLedgerEntry($invoice->invoice_id, 'REIMB_IN', $invoice->reimbursement_amount)) {
            $missing[] = 'REIMB_IN';
        }
        
        // Check REIMB_SST_OUT
        if ($invoice->reimbursement_sst > 0 && !$this->checkLedgerEntry($invoice->invoice_id, 'REIMB_SST_OUT', $invoice->reimbursement_sst)) {
            $missing[] = 'REIMB_SST_OUT';
        }
        
        // Check REIMB_SST_IN
        if ($invoice->reimbursement_sst > 0 && !$this->checkLedgerEntry($invoice->invoice_id, 'REIMB_SST_IN', $invoice->reimbursement_sst)) {
            $missing[] = 'REIMB_SST_IN';
        }
        
        return $missing;
    }

    private function checkLedgerEntry($invoiceId, $type, $amount)
    {
        return DB::table('ledger_entries_v2')
            ->where('loan_case_invoice_main_id', $invoiceId)
            ->where('type', $type)
            ->where('amount', $amount)
            ->exists();
    }

    private function getInvoiceDetails($invoiceId)
    {
        return DB::table('loan_case_invoice_main as lcim')
            ->leftJoin('loan_case_bill_main as lcbm', 'lcim.loan_case_main_bill_id', '=', 'lcbm.id')
            ->leftJoin('loan_case as lc', 'lcbm.case_id', '=', 'lc.id')
            ->leftJoin('transfer_fee_details as tfd', 'lcim.id', '=', 'tfd.loan_case_invoice_main_id')
            ->leftJoin('transfer_fee_main as tfm', 'tfd.transfer_fee_main_id', '=', 'tfm.id')
            ->select(
                'lcim.id as invoice_id',
                'lcim.invoice_no',
                'lcim.reimbursement_amount',
                'lcim.reimbursement_sst',
                'tfm.id as transfer_fee_main_id',
                'tfm.transaction_id',
                'tfm.transfer_from',
                'tfm.transfer_to',
                'tfm.transfer_date',
                'tfm.purpose',
                'tfd.id as transfer_fee_detail_id',
                'tfd.status as tfd_status',
                'lcbm.case_id',
                'lcbm.id as bill_id',
                'lc.case_ref_no'
            )
            ->where('lcim.id', $invoiceId)
            ->where('lcim.status', '<>', 99)
            ->where('lcim.reimbursement_amount', '>', 0)
            ->first();
    }

    private function createMissingEntry($invoice, $entryType)
    {
        switch ($entryType) {
            case 'REIMB_OUT':
                return $this->createReimbOutEntry($invoice);
            case 'REIMB_IN':
                return $this->createReimbInEntry($invoice);
            case 'REIMB_SST_OUT':
                return $this->createReimbSstOutEntry($invoice);
            case 'REIMB_SST_IN':
                return $this->createReimbSstInEntry($invoice);
            default:
                return ['success' => false, 'message' => 'Invalid entry type'];
        }
    }

    private function createReimbOutEntry($invoice)
    {
        try {
            // Create REIMB_OUT entry (OLD SYSTEM)
            $ledgerEntry = new LedgerEntries();
            $ledgerEntry->transaction_id = $invoice->transaction_id;
            $ledgerEntry->case_id = $invoice->case_id;
            $ledgerEntry->loan_case_main_bill_id = $invoice->bill_id;
            $ledgerEntry->user_id = 1; // System user
            $ledgerEntry->key_id = $invoice->transfer_fee_detail_id;
            $ledgerEntry->transaction_type = 'C';
            $ledgerEntry->amount = $invoice->reimbursement_amount;
            $ledgerEntry->bank_id = $invoice->transfer_from;
            $ledgerEntry->remark = $invoice->purpose;
            $ledgerEntry->status = 1;
            $ledgerEntry->created_at = now();
            $ledgerEntry->date = $invoice->transfer_date;
            $ledgerEntry->type = 'REIMBOUT';
            $ledgerEntry->save();

            // Create REIMB_OUT entry (NEW SYSTEM)
            $ledgerEntryV2 = new LedgerEntriesV2();
            $ledgerEntryV2->transaction_id = $invoice->transaction_id;
            $ledgerEntryV2->case_id = $invoice->case_id;
            $ledgerEntryV2->loan_case_main_bill_id = $invoice->bill_id;
            $ledgerEntryV2->loan_case_invoice_main_id = $invoice->invoice_id;
            $ledgerEntryV2->user_id = 1; // System user
            $ledgerEntryV2->key_id = $invoice->transfer_fee_main_id;
            $ledgerEntryV2->key_id_2 = $invoice->transfer_fee_detail_id;
            $ledgerEntryV2->transaction_type = 'C';
            $ledgerEntryV2->amount = $invoice->reimbursement_amount;
            $ledgerEntryV2->bank_id = $invoice->transfer_from;
            $ledgerEntryV2->remark = $invoice->purpose;
            $ledgerEntryV2->status = 1;
            $ledgerEntryV2->is_recon = 0;
            $ledgerEntryV2->created_at = now();
            $ledgerEntryV2->date = $invoice->transfer_date;
            $ledgerEntryV2->type = 'REIMB_OUT';
            $ledgerEntryV2->save();

            return ['success' => true, 'message' => 'REIMB_OUT entry created successfully'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error creating REIMB_OUT: ' . $e->getMessage()];
        }
    }

    private function createReimbInEntry($invoice)
    {
        try {
            // Create REIMB_IN entry (OLD SYSTEM)
            $ledgerEntry = new LedgerEntries();
            $ledgerEntry->transaction_id = $invoice->transaction_id;
            $ledgerEntry->case_id = $invoice->case_id;
            $ledgerEntry->loan_case_main_bill_id = $invoice->bill_id;
            $ledgerEntry->user_id = 1; // System user
            $ledgerEntry->key_id = $invoice->transfer_fee_detail_id;
            $ledgerEntry->transaction_type = 'D';
            $ledgerEntry->amount = $invoice->reimbursement_amount;
            $ledgerEntry->bank_id = $invoice->transfer_to;
            $ledgerEntry->remark = $invoice->purpose;
            $ledgerEntry->status = 1;
            $ledgerEntry->created_at = now();
            $ledgerEntry->date = $invoice->transfer_date;
            $ledgerEntry->type = 'REIMBIN';
            $ledgerEntry->save();

            // Create REIMB_IN entry (NEW SYSTEM)
            $ledgerEntryV2 = new LedgerEntriesV2();
            $ledgerEntryV2->transaction_id = $invoice->transaction_id;
            $ledgerEntryV2->case_id = $invoice->case_id;
            $ledgerEntryV2->loan_case_main_bill_id = $invoice->bill_id;
            $ledgerEntryV2->loan_case_invoice_main_id = $invoice->invoice_id;
            $ledgerEntryV2->user_id = 1; // System user
            $ledgerEntryV2->key_id = $invoice->transfer_fee_main_id;
            $ledgerEntryV2->key_id_2 = $invoice->transfer_fee_detail_id;
            $ledgerEntryV2->transaction_type = 'D';
            $ledgerEntryV2->amount = $invoice->reimbursement_amount;
            $ledgerEntryV2->bank_id = $invoice->transfer_to;
            $ledgerEntryV2->remark = $invoice->purpose;
            $ledgerEntryV2->status = 1;
            $ledgerEntryV2->is_recon = 0;
            $ledgerEntryV2->created_at = now();
            $ledgerEntryV2->date = $invoice->transfer_date;
            $ledgerEntryV2->type = 'REIMB_IN';
            $ledgerEntryV2->save();

            return ['success' => true, 'message' => 'REIMB_IN entry created successfully'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error creating REIMB_IN: ' . $e->getMessage()];
        }
    }

    private function createReimbSstOutEntry($invoice)
    {
        try {
            // Create REIMBSST_OUT entry (OLD SYSTEM)
            $ledgerEntry = new LedgerEntries();
            $ledgerEntry->transaction_id = $invoice->transaction_id;
            $ledgerEntry->case_id = $invoice->case_id;
            $ledgerEntry->loan_case_main_bill_id = $invoice->bill_id;
            $ledgerEntry->user_id = 1; // System user
            $ledgerEntry->key_id = $invoice->transfer_fee_detail_id;
            $ledgerEntry->transaction_type = 'C';
            $ledgerEntry->amount = $invoice->reimbursement_sst;
            $ledgerEntry->bank_id = $invoice->transfer_from;
            $ledgerEntry->remark = $invoice->purpose;
            $ledgerEntry->status = 1;
            $ledgerEntry->created_at = now();
            $ledgerEntry->date = $invoice->transfer_date;
            $ledgerEntry->type = 'REIMBSSTOUT';
            $ledgerEntry->save();

            // Create REIMBSST_OUT entry (NEW SYSTEM)
            $ledgerEntryV2 = new LedgerEntriesV2();
            $ledgerEntryV2->transaction_id = $invoice->transaction_id;
            $ledgerEntryV2->case_id = $invoice->case_id;
            $ledgerEntryV2->loan_case_main_bill_id = $invoice->bill_id;
            $ledgerEntryV2->loan_case_invoice_main_id = $invoice->invoice_id;
            $ledgerEntryV2->user_id = 1; // System user
            $ledgerEntryV2->key_id = $invoice->transfer_fee_main_id;
            $ledgerEntryV2->key_id_2 = $invoice->transfer_fee_detail_id;
            $ledgerEntryV2->transaction_type = 'C';
            $ledgerEntryV2->amount = $invoice->reimbursement_sst;
            $ledgerEntryV2->bank_id = $invoice->transfer_from;
            $ledgerEntryV2->remark = $invoice->purpose;
            $ledgerEntryV2->status = 1;
            $ledgerEntryV2->is_recon = 0;
            $ledgerEntryV2->created_at = now();
            $ledgerEntryV2->date = $invoice->transfer_date;
            $ledgerEntryV2->type = 'REIMB_SST_OUT';
            $ledgerEntryV2->save();

            return ['success' => true, 'message' => 'REIMB_SST_OUT entry created successfully'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error creating REIMB_SST_OUT: ' . $e->getMessage()];
        }
    }

    private function createReimbSstInEntry($invoice)
    {
        try {
            // Create REIMBSST_IN entry (OLD SYSTEM)
            $ledgerEntry = new LedgerEntries();
            $ledgerEntry->transaction_id = $invoice->transaction_id;
            $ledgerEntry->case_id = $invoice->case_id;
            $ledgerEntry->loan_case_main_bill_id = $invoice->bill_id;
            $ledgerEntry->user_id = 1; // System user
            $ledgerEntry->key_id = $invoice->transfer_fee_detail_id;
            $ledgerEntry->transaction_type = 'D';
            $ledgerEntry->amount = $invoice->reimbursement_sst;
            $ledgerEntry->bank_id = $invoice->transfer_to;
            $ledgerEntry->remark = $invoice->purpose;
            $ledgerEntry->status = 1;
            $ledgerEntry->created_at = now();
            $ledgerEntry->date = $invoice->transfer_date;
            $ledgerEntry->type = 'REIMBSSTIN';
            $ledgerEntry->save();

            // Create REIMBSST_IN entry (NEW SYSTEM)
            $ledgerEntryV2 = new LedgerEntriesV2();
            $ledgerEntryV2->transaction_id = $invoice->transaction_id;
            $ledgerEntryV2->case_id = $invoice->case_id;
            $ledgerEntryV2->loan_case_main_bill_id = $invoice->bill_id;
            $ledgerEntryV2->loan_case_invoice_main_id = $invoice->invoice_id;
            $ledgerEntryV2->user_id = 1; // System user
            $ledgerEntryV2->key_id = $invoice->transfer_fee_main_id;
            $ledgerEntryV2->key_id_2 = $invoice->transfer_fee_detail_id;
            $ledgerEntryV2->transaction_type = 'D';
            $ledgerEntryV2->amount = $invoice->reimbursement_sst;
            $ledgerEntryV2->bank_id = $invoice->transfer_to;
            $ledgerEntryV2->remark = $invoice->purpose;
            $ledgerEntryV2->status = 1;
            $ledgerEntryV2->is_recon = 0;
            $ledgerEntryV2->created_at = now();
            $ledgerEntryV2->date = $invoice->transfer_date;
            $ledgerEntryV2->type = 'REIMB_SST_IN';
            $ledgerEntryV2->save();

            return ['success' => true, 'message' => 'REIMB_SST_IN entry created successfully'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error creating REIMB_SST_IN: ' . $e->getMessage()];
        }
    }
}

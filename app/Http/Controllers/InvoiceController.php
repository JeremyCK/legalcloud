<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\LoanCase;
use App\Models\LoanCaseBillMain;
use App\Models\LoanCaseInvoiceMain;
use App\Models\LoanCaseInvoiceDetails;
use App\Models\AccountLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    /**
     * Display invoice list page
     */
    public function index()
    {
        $current_user = auth()->user();
        $Branch = Branch::where('status', 1)->get();
        
        return view('dashboard.invoice.list', [
            'current_user' => $current_user,
            'Branch' => $Branch
        ]);
    }

    /**
     * Display invoice details page
     */
    public function show($invoiceId)
    {
        $current_user = auth()->user();
        
        $invoice = LoanCaseInvoiceMain::with(['loanCaseBillMain.loanCase'])
            ->where('id', $invoiceId)
            ->where('status', '<>', 99)
            ->first();

        if (!$invoice) {
            abort(404, 'Invoice not found');
        }

        // Verify case access
        if (!in_array($current_user->menuroles, ['admin', 'management', 'account'])) {
            $accessCaseList = $this->caseManagementEngine();
            if ($invoice->loanCaseBillMain && $invoice->loanCaseBillMain->case_id) {
                if (!in_array($invoice->loanCaseBillMain->case_id, $accessCaseList)) {
                    abort(403, 'Access denied');
                }
            } else {
                abort(404, 'Invoice bill information not found');
            }
        }

        return view('dashboard.invoice.details', [
            'current_user' => $current_user,
            'invoice' => $invoice,
            'invoiceId' => $invoiceId
        ]);
    }

    /**
     * Get invoice list with search functionality (AJAX)
     */
    public function getInvoiceList(Request $request)
    {
        $current_user = auth()->user();
        
        // Get search parameters
        $searchInvoiceNo = $request->input('search_invoice_no', '');
        $searchCaseRef = $request->input('search_case_ref', '');
        $searchBillNo = $request->input('search_bill_no', '');
        $filterSstStatus = $request->input('filter_sst_status', 'unpaid'); // Default to unpaid
        $filterDateFrom = $request->input('filter_date_from', '');
        $filterDateTo = $request->input('filter_date_to', '');
        $filterTransferredStatus = $request->input('filter_transferred_status', 'all'); // Default to all

        // Build query
        $query = DB::table('loan_case_invoice_main as im')
            ->leftJoin('loan_case_bill_main as bm', 'im.loan_case_main_bill_id', '=', 'bm.id')
            ->leftJoin('loan_case as l', 'bm.case_id', '=', 'l.id')
            ->leftJoin('client as c', 'l.customer_id', '=', 'c.id')
            ->leftJoin('branch as b', 'l.branch_id', '=', 'b.id')
            ->select(
                'im.id',
                'im.invoice_no',
                'im.Invoice_date',
                'im.amount',
                'im.status as invoice_status',
                'im.bln_sst as sst_paid_status',
                'im.transferred_to_office_bank as transferred_status',
                'bm.id as bill_id',
                'bm.invoice_no as bill_invoice_no',
                'bm.payment_receipt_date',
                'l.id as case_id',
                'l.case_ref_no',
                'c.name as client_name',
                'b.name as branch_name'
            )
            ->where('im.status', '<>', 99);

        // Apply access control
        if (!in_array($current_user->menuroles, ['admin', 'management', 'account'])) {
            $accessCaseList = $this->caseManagementEngine();
            $query->whereIn('l.id', $accessCaseList);
        }

        // Apply SST status filter (default to unpaid)
        if ($filterSstStatus === 'unpaid') {
            $query->where(function($q) {
                $q->where('im.bln_sst', '=', 0)
                  ->orWhereNull('im.bln_sst');
            });
        } elseif ($filterSstStatus === 'paid') {
            $query->where('im.bln_sst', '=', 1);
        }

        // Apply date range filter
        if (!empty($filterDateFrom)) {
            $query->whereDate('im.Invoice_date', '>=', $filterDateFrom);
        }
        if (!empty($filterDateTo)) {
            $query->whereDate('im.Invoice_date', '<=', $filterDateTo);
        }

        // Apply transferred status filter
        if ($filterTransferredStatus === 'transferred') {
            $query->where('im.transferred_to_office_bank', '=', 1);
        } elseif ($filterTransferredStatus === 'not_transferred') {
            $query->where(function($q) {
                $q->where('im.transferred_to_office_bank', '=', 0)
                  ->orWhereNull('im.transferred_to_office_bank');
            });
        }

        // Apply search filters
        if (!empty($searchInvoiceNo)) {
            $invoiceNumbers = array_filter(array_map('trim', preg_split('/[,\n\r]+/', $searchInvoiceNo)));
            if (!empty($invoiceNumbers)) {
                $query->where(function($q) use ($invoiceNumbers) {
                    foreach ($invoiceNumbers as $invoiceNo) {
                        $q->orWhere('im.invoice_no', 'LIKE', '%' . $invoiceNo . '%');
                    }
                });
            }
        }

        if (!empty($searchCaseRef)) {
            $query->where('l.case_ref_no', 'LIKE', '%' . $searchCaseRef . '%');
        }

        if (!empty($searchBillNo)) {
            $query->where(function($q) use ($searchBillNo) {
                $q->where('bm.invoice_no', 'LIKE', '%' . $searchBillNo . '%')
                  ->orWhere('bm.id', '=', $searchBillNo);
            });
        }

        $perPage = $request->input('per_page', 50);
        $page = $request->input('page', 1);
        
        $total = $query->count();
        $invoices = $query->orderBy('im.Invoice_date', 'DESC')
            ->orderBy('im.id', 'DESC')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $lastPage = ceil($total / $perPage);

        return response()->json([
            'status' => 1,
            'data' => $invoices,
            'pagination' => [
                'current_page' => (int)$page,
                'last_page' => $lastPage,
                'per_page' => $perPage,
                'total' => $total
            ]
        ]);
    }

    /**
     * Get invoice details for editing
     */
    public function getInvoiceDetails($invoiceId)
    {
        try {
            $current_user = auth()->user();
            
            $invoice = LoanCaseInvoiceMain::with(['loanCaseBillMain.loanCase'])
                ->where('id', $invoiceId)
                ->where('status', '<>', 99)
                ->first();

            if (!$invoice) {
                return response()->json(['status' => 0, 'message' => 'Invoice not found'], 404);
            }

            // Verify case access
            if (!in_array($current_user->menuroles, ['admin', 'management', 'account'])) {
                $accessCaseList = $this->caseManagementEngine();
                if ($invoice->loanCaseBillMain && $invoice->loanCaseBillMain->case_id) {
                    if (!in_array($invoice->loanCaseBillMain->case_id, $accessCaseList)) {
                        return response()->json(['status' => 0, 'message' => 'Access denied'], 403);
                    }
                } else {
                    return response()->json(['status' => 0, 'message' => 'Invoice bill information not found'], 404);
                }
            }

            // Check if this is a split invoice (multiple invoices for same bill)
            $splitInvoices = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $invoice->loan_case_main_bill_id)
                ->where('status', '<>', 99)
                ->orderBy('id', 'ASC')
                ->get(['id', 'invoice_no', 'amount', 'Invoice_date']);
            
            $isSplitInvoice = $splitInvoices->count() > 1;
            $currentInvoiceIndex = $splitInvoices->search(function($item) use ($invoiceId) {
                return $item->id == $invoiceId;
            });
            $currentInvoiceIndex = $currentInvoiceIndex !== false ? $currentInvoiceIndex + 1 : 1;

            // Get bill information for SST rate
            $bill = LoanCaseBillMain::where('id', $invoice->loan_case_main_bill_id)->first();
            $sstRate = $bill ? ($bill->sst_rate ?? 6) : 6; // Default to 6% if not set
            
            // Get invoice details grouped by category
            $invoiceDetails = DB::table('loan_case_invoice_details as id')
                ->leftJoin('account_item as ai', 'id.account_item_id', '=', 'ai.id')
                ->leftJoin('account_category as ac', 'ai.account_cat_id', '=', 'ac.id')
                ->select(
                    'id.id',
                    'id.account_item_id',
                    'id.amount',
                    'id.quo_amount',
                    'id.ori_invoice_amt',
                    'id.remark',
                    'ai.name as account_name',
                    'ai.name_cn as account_name_cn',
                    'ac.category as category_name',
                    'ac.id as category_id',
                    'ac.order as category_order',
                    'ac.code as category_code',
                    'ac.taxable as category_taxable'
                )
                ->where('id.invoice_main_id', '=', $invoiceId)
                ->where('id.status', '<>', 99)
                ->orderBy('ac.order', 'ASC')
                ->orderBy('ai.name', 'ASC')
                ->get();
            
            // Group details by category
            $groupedDetails = [];
            foreach ($invoiceDetails as $detail) {
                $categoryId = $detail->category_id;
                if (!isset($groupedDetails[$categoryId])) {
                    $groupedDetails[$categoryId] = [
                        'category_id' => $categoryId,
                        'category_name' => $detail->category_name,
                        'category_code' => $detail->category_code,
                        'category_order' => $detail->category_order,
                        'category_taxable' => $detail->category_taxable,
                        'items' => []
                    ];
                }
                $groupedDetails[$categoryId]['items'][] = $detail;
            }
            
            // Sort by category order
            usort($groupedDetails, function($a, $b) {
                return $a['category_order'] <=> $b['category_order'];
            });

            // Get additional information
            $case = null;
            $clientName = null;
            $branchName = null;
            
            if ($invoice->loanCaseBillMain && $invoice->loanCaseBillMain->loanCase) {
                $case = $invoice->loanCaseBillMain->loanCase;
                if ($case->customer_id) {
                    $client = DB::table('client')->where('id', $case->customer_id)->first();
                    $clientName = $client ? $client->name : null;
                }
                if ($case->branch_id) {
                    $branch = DB::table('branch')->where('id', $case->branch_id)->first();
                    $branchName = $branch ? $branch->name : null;
                }
            }

            // Format split invoices for response
            $splitInvoicesData = [];
            if ($isSplitInvoice) {
                foreach ($splitInvoices as $splitInvoice) {
                    $splitInvoicesData[] = [
                        'id' => $splitInvoice->id,
                        'invoice_no' => $splitInvoice->invoice_no,
                        'amount' => $splitInvoice->amount,
                        'Invoice_date' => $splitInvoice->Invoice_date,
                        'is_current' => $splitInvoice->id == $invoiceId
                    ];
                }
            }

            // Get billing party information
            $billingParty = null;
            if ($invoice->bill_party_id) {
                $billingParty = DB::table('invoice_billing_party')
                    ->where('id', $invoice->bill_party_id)
                    ->first();
            }

            // Format invoice data for response
            $invoiceData = [
                'id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no,
                'Invoice_date' => $invoice->Invoice_date,
                'amount' => $invoice->amount,
                'bln_sst' => $invoice->bln_sst ?? 0,
                'bill_party_id' => $invoice->bill_party_id ?? 0,
                'loan_case_main_bill_id' => $invoice->loan_case_main_bill_id,
                'is_split_invoice' => $isSplitInvoice,
                'split_invoice_count' => $splitInvoices->count(),
                'current_invoice_index' => $currentInvoiceIndex,
                'split_invoices' => $splitInvoicesData,
                'loanCaseBillMain' => $invoice->loanCaseBillMain ? [
                    'id' => $invoice->loanCaseBillMain->id,
                    'case_id' => $invoice->loanCaseBillMain->case_id ?? null,
                    'loanCase' => $invoice->loanCaseBillMain->loanCase ? [
                        'id' => $invoice->loanCaseBillMain->loanCase->id,
                        'case_ref_no' => $invoice->loanCaseBillMain->loanCase->case_ref_no ?? null
                    ] : null
                ] : null,
                'client_name' => $clientName,
                'branch_name' => $branchName,
                'billing_party' => $billingParty ? [
                    'id' => $billingParty->id,
                    'customer_name' => $billingParty->customer_name,
                    'customer_code' => $billingParty->customer_code,
                    'email' => $billingParty->email,
                    'phone' => $billingParty->phone1 ?? $billingParty->mobile,
                    'completed' => $billingParty->completed ?? 0
                ] : null
            ];

            return response()->json([
                'status' => 1,
                'invoice' => $invoiceData,
                'details' => $invoiceDetails,
                'grouped_details' => array_values($groupedDetails),
                'sst_rate' => $sstRate
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting invoice details: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Error loading invoice details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update invoice
     */
    public function update(Request $request, $invoiceId)
    {
        $current_user = auth()->user();
        
        $invoice = LoanCaseInvoiceMain::where('id', $invoiceId)
            ->where('status', '<>', 99)
            ->first();

        if (!$invoice) {
            return response()->json(['status' => 0, 'message' => 'Invoice not found'], 404);
        }

        // Verify case access
        if (!in_array($current_user->menuroles, ['admin', 'management', 'account'])) {
            $accessCaseList = $this->caseManagementEngine();
            $bill = LoanCaseBillMain::where('id', $invoice->loan_case_main_bill_id)->first();
            if (!$bill || !in_array($bill->case_id, $accessCaseList)) {
                return response()->json(['status' => 0, 'message' => 'Access denied'], 403);
            }
        }

        // Get bill for SST rate
        $bill = LoanCaseBillMain::where('id', $invoice->loan_case_main_bill_id)->first();
        if (!$bill) {
            return response()->json(['status' => 0, 'message' => 'Bill not found'], 404);
        }
        $sstRate = $bill->sst_rate ?? 6;

        // Invoice number cannot be updated (read-only)
        // if ($request->has('invoice_no')) {
        //     $invoice->invoice_no = $request->input('invoice_no');
        // }
        if ($request->has('Invoice_date')) {
            $invoiceDate = $request->input('Invoice_date');
            // Allow empty date - set to null if empty string
            $invoice->Invoice_date = !empty($invoiceDate) ? $invoiceDate : null;
        }

        // Store custom SST values for use in calculation
        $customSstValues = [];
        
        // Update invoice details if provided
        if ($request->has('details')) {
            $details = $request->input('details');
            foreach ($details as $detail) {
                if (isset($detail['id']) && isset($detail['amount'])) {
                    // Get the existing detail to compare old and new amounts
                    $existingDetail = LoanCaseInvoiceDetails::where('id', $detail['id'])
                        ->where('invoice_main_id', $invoiceId)
                        ->first();
                    
                    if ($existingDetail) {
                        $oldAmount = $existingDetail->amount;
                        $newAmount = floatval($detail['amount']);
                        
                        // Store custom SST if provided
                        if (isset($detail['sst']) && $detail['sst'] !== null && $detail['sst'] !== '') {
                            $customSstValues[$detail['id']] = floatval($detail['sst']);
                        }
                        
                        // Only update and log if amount actually changed
                        if ($oldAmount != $newAmount) {
                            // Get account item name for logging
                            $accountItem = DB::table('account_item')
                                ->where('id', $existingDetail->account_item_id)
                                ->first();
                            $itemName = $accountItem ? ($accountItem->name ?? 'N/A') : 'N/A';
                            
                            // Update the detail
                            $existingDetail->amount = $newAmount;
                            $existingDetail->save();
                            
                            // Create AccountLog entry
                            $AccountLog = new AccountLog();
                            $AccountLog->user_id = $current_user->id;
                            $AccountLog->case_id = $bill->case_id;
                            $AccountLog->bill_id = $bill->id;
                            $AccountLog->object_id = $existingDetail->id;
                            $AccountLog->ori_amt = $oldAmount;
                            $AccountLog->new_amt = $newAmount;
                            $AccountLog->action = 'Update';
                            $AccountLog->desc = $current_user->name . ' update invoice detail item (' . $itemName . ') for invoice ' . $invoice->invoice_no . ' from ' . number_format($oldAmount, 2) . ' to ' . number_format($newAmount, 2);
                            $AccountLog->status = 1;
                            $AccountLog->created_at = date('Y-m-d H:i:s');
                            $AccountLog->save();
                        }
                    }
                }
            }
        }

        // Recalculate invoice amounts from details (same as case details)
        // Pass custom SST values if any were provided
        $calculated = $this->calculateInvoiceAmountsFromDetails($invoiceId, $sstRate, $customSstValues);
        
        // Update invoice with calculated amounts
        $invoice->pfee1_inv = $calculated['pfee1'];
        $invoice->pfee2_inv = $calculated['pfee2'];
        $invoice->sst_inv = $calculated['sst'];
        $invoice->reimbursement_amount = $calculated['reimbursement_amount'];
        $invoice->reimbursement_sst = $calculated['reimbursement_sst'];
        $invoice->amount = $calculated['total'];
        $invoice->save();

        // Update bill totals (same as case details)
        $this->updatePfeeDisbAmountINVFromDetails($bill->id);

        return response()->json([
            'status' => 1,
            'message' => 'Invoice updated successfully',
            'invoice' => $invoice,
            'calculated' => $calculated
        ]);
    }

    /**
     * Calculate invoice amounts from details (same logic as CaseController)
     * @param int $invoiceId
     * @param float $sstRate
     * @param array $customSstValues Optional array of custom SST values keyed by detail_id
     */
    private function calculateInvoiceAmountsFromDetails($invoiceId, $sstRate, $customSstValues = [])
    {
        $details = DB::table('loan_case_invoice_details as ild')
            ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
            ->where('ild.invoice_main_id', $invoiceId)
            ->where('ild.status', '<>', 99)
            ->select('ild.amount', 'ild.id as detail_id', 'ai.account_cat_id', 'ai.pfee1_item')
            ->get();

        $pfee1 = 0;
        $pfee2 = 0;
        $sst = 0;
        $reimbursement_amount = 0;
        $reimbursement_sst = 0;
        $total = 0;

        // Calculate using the same method as invoice display: from each detail item, then sum
        foreach ($details as $detail) {
            if ($detail->account_cat_id == 1) {
                // Calculate pfee1 and pfee2 for professional fees
                if ($detail->pfee1_item == 1) {
                    $pfee1 += $detail->amount;
                } else {
                    $pfee2 += $detail->amount;
                }
                
                // Use custom SST if provided, otherwise calculate
                if (isset($customSstValues[$detail->detail_id])) {
                    $row_sst = floatval($customSstValues[$detail->detail_id]);
                } else {
                    // Apply special rounding rule for SST: round DOWN if 3rd decimal is 5
                    $sst_calculation = $detail->amount * ($sstRate / 100);
                    $sst_string = number_format($sst_calculation, 3, '.', '');
                    
                    if (substr($sst_string, -1) == '5') {
                        $row_sst = floor($sst_calculation * 100) / 100; // Round down
                    } else {
                        $row_sst = round($sst_calculation, 2); // Normal rounding
                    }
                }
                
                $sst += $row_sst;
                $total += $detail->amount + $row_sst;
            } elseif ($detail->account_cat_id == 4) {
                // Calculate reimbursement amounts for account_cat_id == 4
                $reimbursement_amount += $detail->amount;
                
                // Use custom SST if provided, otherwise calculate
                if (isset($customSstValues[$detail->detail_id])) {
                    $row_sst = floatval($customSstValues[$detail->detail_id]);
                } else {
                    // Apply special rounding rule for reimbursement SST too
                    $sst_calculation = $detail->amount * ($sstRate / 100);
                    $sst_string = number_format($sst_calculation, 3, '.', '');
                    
                    if (substr($sst_string, -1) == '5') {
                        $row_sst = floor($sst_calculation * 100) / 100; // Round down
                    } else {
                        $row_sst = round($sst_calculation, 2); // Normal rounding
                    }
                }
                
                $reimbursement_sst += $row_sst;
                $total += $detail->amount + $row_sst;
            } else {
                // For other account categories, add amount directly to total
                $total += $detail->amount;
            }
        }

        return [
            'pfee1' => round($pfee1, 2),
            'pfee2' => round($pfee2, 2),
            'sst' => round($sst, 2),
            'reimbursement_amount' => round($reimbursement_amount, 2),
            'reimbursement_sst' => round($reimbursement_sst, 2),
            'total' => round($total, 2)
        ];
    }

    /**
     * Update bill totals from invoice details (same logic as CaseController)
     */
    private function updatePfeeDisbAmountINVFromDetails($billId)
    {
        $bill = LoanCaseBillMain::where('id', $billId)->first();
        if (!$bill) {
            return;
        }

        // Get all invoices for this bill
        $invoices = DB::table('loan_case_invoice_main')
            ->where('loan_case_main_bill_id', $billId)
            ->where('status', '<>', 99)
            ->get();

        $total_pfee1 = 0;
        $total_pfee2 = 0;
        $total_sst = 0;
        $total_amount = 0;
        $total_reimbursement_amount = 0;
        $total_reimbursement_sst = 0;

        // Update each invoice from its details
        foreach ($invoices as $invoice) {
            $invoiceCalculations = $this->calculateInvoiceAmountsFromDetails($invoice->id, $bill->sst_rate ?? 6);

            // Update the invoice record
            DB::table('loan_case_invoice_main')
                ->where('id', $invoice->id)
                ->update([
                    'pfee1_inv' => $invoiceCalculations['pfee1'],
                    'pfee2_inv' => $invoiceCalculations['pfee2'],
                    'sst_inv' => $invoiceCalculations['sst'],
                    'reimbursement_amount' => $invoiceCalculations['reimbursement_amount'],
                    'reimbursement_sst' => $invoiceCalculations['reimbursement_sst'],
                    'amount' => $invoiceCalculations['total'],
                    'updated_at' => now()
                ]);

            // Add to bill totals
            $total_pfee1 += $invoiceCalculations['pfee1'];
            $total_pfee2 += $invoiceCalculations['pfee2'];
            $total_sst += $invoiceCalculations['sst'];
            $total_reimbursement_amount += $invoiceCalculations['reimbursement_amount'];
            $total_reimbursement_sst += $invoiceCalculations['reimbursement_sst'];
            $total_amount += $invoiceCalculations['total'];
        }

        // Update the bill record
        DB::table('loan_case_bill_main')
            ->where('id', $billId)
            ->update([
                'pfee1_inv' => $total_pfee1,
                'pfee2_inv' => $total_pfee2,
                'sst_inv' => $total_sst,
                'reimbursement_amount' => $total_reimbursement_amount,
                'reimbursement_sst' => $total_reimbursement_sst,
                'total_amt_inv' => $total_amount,
                'updated_at' => now()
            ]);
    }

    /**
     * Split invoice - create a new invoice from the same bill
     */
    public function splitInvoice(Request $request, $billId)
    {
        $current_user = auth()->user();
        
        $bill = LoanCaseBillMain::where('id', $billId)->first();
        
        if (!$bill) {
            return response()->json(['status' => 0, 'message' => 'Bill not found.']);
        }

        $case = LoanCase::where('id', $bill->case_id)->first();
        
        if (!$case) {
            return response()->json(['status' => 0, 'message' => 'Case not found.']);
        }

        // Get existing invoice for this bill
        $existingInvoice = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $billId)
            ->where('status', '<>', 99)
            ->first();
        
        if (!$existingInvoice) {
            return response()->json(['status' => 0, 'message' => 'No invoice found for this bill.']);
        }

        // Create new invoice
        $newInvoice = new LoanCaseInvoiceMain();
        $newInvoice->loan_case_main_bill_id = $billId;
        $newInvoice->invoice_no = '';
        $newInvoice->Invoice_date = $bill->Invoice_date;
        $newInvoice->amount = 0; // Will be calculated
        $newInvoice->pfee1_inv = 0;
        $newInvoice->pfee2_inv = 0;
        $newInvoice->sst_inv = 0;
        $newInvoice->reimbursement_amount = 0;
        $newInvoice->reimbursement_sst = 0;
        $newInvoice->bill_party_id = 0;
        $newInvoice->remark = "";
        $newInvoice->created_by = $current_user->id;
        $newInvoice->status = 1;
        $newInvoice->created_at = now();
        $newInvoice->save();

        $newInvoiceId = $newInvoice->id;

        // Generate invoice number
        $einvoiceController = new \App\Http\Controllers\EInvoiceContoller();
        $einvoiceController->generateNewInvNo($billId, $newInvoiceId, false);

        // Get all invoices for this bill
        $allInvoices = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $billId)
            ->where('status', '<>', 99)
            ->orderBy('id')
            ->get();
        
        $partyCount = $allInvoices->count();

        // Get all invoice details for this bill
        $allDetails = LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $billId)
            ->where('status', '<>', 99)
            ->get();
        
        // Group details by account_item_id to redistribute
        $detailsByItem = $allDetails->groupBy('account_item_id');
        
        foreach ($detailsByItem as $accountItemId => $details) {
            $originalTotalAmount = $details->first()->ori_invoice_amt;
            
            // Distribute amounts properly - each invoice gets its correct share
            foreach ($allInvoices as $invoiceIndex => $invoice) {
                $distributedAmount = \App\Http\Controllers\EInvoiceContoller::distributeAmount($originalTotalAmount, $partyCount, $invoiceIndex);
                
                // Update existing details
                foreach ($details as $detail) {
                    if ($detail->invoice_main_id == $invoice->id) {
                        $detail->amount = $distributedAmount;
                        $detail->save();
                    }
                }
                
                // Create new detail for the new invoice if it doesn't exist
                if ($invoice->id == $newInvoiceId) {
                    $existingNewDetail = LoanCaseInvoiceDetails::where('invoice_main_id', $newInvoiceId)
                        ->where('account_item_id', $accountItemId)
                        ->first();
                    
                    if (!$existingNewDetail) {
                        $originalDetail = $details->first();
                        $newDetail = new LoanCaseInvoiceDetails();
                        $newDetail->loan_case_main_bill_id = $billId;
                        $newDetail->account_item_id = $originalDetail->account_item_id;
                        $newDetail->quotation_item_id = $originalDetail->quotation_item_id;
                        $newDetail->invoice_main_id = $newInvoiceId;
                        $newDetail->amount = $distributedAmount;
                        $newDetail->ori_invoice_amt = $originalDetail->ori_invoice_amt;
                        $newDetail->quo_amount = $originalDetail->quo_amount;
                        $newDetail->remark = $originalDetail->remark;
                        $newDetail->created_by = $current_user->id;
                        $newDetail->status = 1;
                        $newDetail->created_at = now();
                        $newDetail->save();
                    }
                }
            }
        }

        // Recalculate invoice amounts
        $caseController = new \App\Http\Controllers\CaseController();
        $caseController->updatePfeeDisbAmountINV($billId);
        
        return response()->json([
            'status' => 1, 
            'message' => 'Invoice split successfully',
            'bill_id' => $billId,
            'new_invoice_id' => $newInvoiceId
        ]);
    }

    /**
     * Remove/Unsplit invoice - delete one invoice from split invoices
     */
    public function removeInvoice($invoiceId)
    {
        $current_user = auth()->user();
        
        $invoice = LoanCaseInvoiceMain::where('id', $invoiceId)
            ->where('status', '<>', 99)
            ->first();

        if (!$invoice) {
            return response()->json(['status' => 0, 'message' => 'Invoice not found'], 404);
        }

        $billId = $invoice->loan_case_main_bill_id;
        $bill = LoanCaseBillMain::where('id', $billId)->first();
        
        if (!$bill) {
            return response()->json(['status' => 0, 'message' => 'Bill not found'], 404);
        }

        // Check if this is the only invoice (can't remove if only one)
        $invoiceCount = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $billId)
            ->where('status', '<>', 99)
            ->count();
        
        if ($invoiceCount <= 1) {
            return response()->json(['status' => 0, 'message' => 'Cannot remove invoice. This is the only invoice for this bill.'], 400);
        }

        // Get main invoice (first invoice by ID)
        $mainInvoice = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $billId)
            ->where('status', '<>', 99)
            ->orderBy('id', 'ASC')
            ->first();
        
        // Prevent deletion of main invoice
        if ($mainInvoice && $mainInvoice->id == $invoiceId) {
            return response()->json(['status' => 0, 'message' => 'Cannot remove the main invoice. Please remove other invoices first.'], 400);
        }

        // Check if invoice is in transfer fee records
        $transferFeeDetails = \App\Models\TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)->get();
        
        if ($transferFeeDetails->isNotEmpty()) {
            $transferFeeMainIds = $transferFeeDetails->pluck('transfer_fee_main_id')->unique();
            $transferFeeMains = \App\Models\TransferFeeMain::whereIn('id', $transferFeeMainIds)->get();
            $transferIds = $transferFeeMains->pluck('transaction_id')->implode(', ');
            
            return response()->json([
                'status' => 0, 
                'message' => 'Cannot remove invoice. This invoice is already included in transfer fee record(s): ' . $transferIds . '. Please remove it from transfer fee first.'
            ], 400);
        }

        // Update billing party references
        DB::table('invoice_billing_party')
            ->where('invoice_main_id', $invoiceId)
            ->update(['invoice_main_id' => 0]);

        // Delete invoice details
        LoanCaseInvoiceDetails::where('invoice_main_id', $invoiceId)->delete();
        
        // Delete invoice (hard delete, same as EInvoiceController)
        $invoice->delete();

        // Redistribute amounts among remaining invoices
        $remainingInvoices = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $billId)
            ->where('status', '<>', 99)
            ->orderBy('id')
            ->get();
        
        $remainingCount = $remainingInvoices->count();
        
        if ($remainingCount > 0) {
            // Get all details for this bill and redistribute
            $allDetails = LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $billId)
                ->where('status', '<>', 99)
                ->get();
            
            // Group details by account_item_id to redistribute
            $detailsByItem = $allDetails->groupBy('account_item_id');
            
            foreach ($detailsByItem as $accountItemId => $details) {
                $originalTotalAmount = $details->first()->ori_invoice_amt;
                
                // Redistribute amounts among remaining invoices
                foreach ($remainingInvoices as $invoiceIndex => $remainingInvoice) {
                    $distributedAmount = \App\Http\Controllers\EInvoiceContoller::distributeAmount($originalTotalAmount, $remainingCount, $invoiceIndex);
                    
                    foreach ($details as $detail) {
                        if ($detail->invoice_main_id == $remainingInvoice->id) {
                            $detail->amount = $distributedAmount;
                            $detail->save();
                        }
                    }
                }
            }
            
            // Recalculate all invoice amounts
            $caseController = new \App\Http\Controllers\CaseController();
            $caseController->updatePfeeDisbAmountINV($billId);
        }

        // Generate new invoice numbers
        $einvoiceController = new \App\Http\Controllers\EInvoiceContoller();
        $einvoiceController->generateNewInvNo($billId, $invoiceId, true);

        return response()->json([
            'status' => 1, 
            'message' => 'Invoice removed successfully',
            'bill_id' => $billId
        ]);
    }

    /**
     * Add invoice item - adds to all split invoices if applicable
     */
    public function addInvoiceItem(Request $request, $billId)
    {
        $current_user = auth()->user();
        
        $bill = LoanCaseBillMain::where('id', $billId)->first();
        if (!$bill) {
            return response()->json(['status' => 0, 'message' => 'Bill not found.']);
        }

        // Validate input
        if (!$request->has('details_id') || !$request->has('NewAmount')) {
            return response()->json(['status' => 0, 'message' => 'Missing required fields: details_id and NewAmount.']);
        }

        $partyCount = \App\Http\Controllers\EInvoiceContoller::getPartyCount($billId);
        $invoices = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $billId)
            ->where('status', '<>', 99)
            ->orderBy('id')
            ->get();

        if ($invoices->count() == 0) {
            return response()->json(['status' => 0, 'message' => 'No invoices found for this bill.']);
        }

        // Add item to all invoices (for split invoices)
        // Use ->values() to ensure array indices are sequential (0, 1, 2...)
        $invoicesArray = $invoices->values()->all();
        $addedCount = 0;
        
        foreach ($invoicesArray as $invoiceIndex => $invoice) {
            // Use distributeAmount for proper distribution
            $distributedAmount = \App\Http\Controllers\EInvoiceContoller::distributeAmount(
                $request->input('NewAmount'), 
                $partyCount, 
                $invoiceIndex
            );

            $detail = new LoanCaseInvoiceDetails();
            $detail->loan_case_main_bill_id = $billId;
            $detail->invoice_main_id = $invoice->id;
            $detail->account_item_id = $request->input('details_id');
            $detail->quotation_item_id = 0;
            $detail->amount = $distributedAmount;
            $detail->ori_invoice_amt = $request->input('NewAmount');
            $detail->quo_amount = $request->input('NewAmount');
            $detail->remark = '';
            $detail->created_by = $current_user->id;
            $detail->status = 1;
            $detail->created_at = now();
            $detail->save();
            
            $addedCount++;
        }

        // Recalculate all invoice amounts
        $caseController = new \App\Http\Controllers\CaseController();
        $caseController->updatePfeeDisbAmountINV($billId);

        Log::info('Invoice item added', [
            'bill_id' => $billId,
            'account_item_id' => $request->input('details_id'),
            'amount' => $request->input('NewAmount'),
            'invoices_count' => $invoices->count(),
            'items_added' => $addedCount
        ]);

        return response()->json([
            'status' => 1, 
            'message' => 'Item added successfully',
            'items_added' => $addedCount,
            'bill_id' => $billId
        ]);
    }

    /**
     * Delete invoice item - removes from all split invoices if applicable
     */
    public function deleteInvoiceItem(Request $request, $billId)
    {
        $detail = LoanCaseInvoiceDetails::where('id', $request->input('details_id'))->first();
        
        if (!$detail) {
            return response()->json(['status' => 0, 'message' => 'Item not found.']);
        }

        $billId = $detail->loan_case_main_bill_id;
        $accountItemId = $detail->account_item_id;

        // Delete from all invoices with the same account_item_id (for split invoices)
        LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $billId)
            ->where('account_item_id', $accountItemId)
            ->where('status', '<>', 99)
            ->delete();

        // Recalculate all invoice amounts
        $caseController = new \App\Http\Controllers\CaseController();
        $caseController->updatePfeeDisbAmountINV($billId);

        return response()->json(['status' => 1, 'message' => 'Item deleted successfully']);
    }

    /**
     * Get all invoices for a bill (for remove invoice modal)
     */
    public function getBillInvoices($billId)
    {
        $invoices = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $billId)
            ->where('status', '<>', 99)
            ->orderBy('id', 'ASC')
            ->get(['id', 'invoice_no', 'amount', 'Invoice_date']);

        // Main invoice is the first one (lowest ID)
        $mainInvoiceId = $invoices->first() ? $invoices->first()->id : null;

        return response()->json([
            'status' => 1,
            'invoices' => $invoices,
            'main_invoice_id' => $mainInvoiceId
        ]);
    }

    /**
     * Get account items by category for dropdown
     */
    public function getAccountItemsByCategory($categoryId)
    {
        $accountItems = DB::table('account_item as ai')
            ->leftJoin('account_category as ac', 'ai.account_cat_id', '=', 'ac.id')
            ->select('ai.id', 'ai.name', 'ai.name_cn', 'ai.account_cat_id')
            ->where('ai.account_cat_id', $categoryId)
            ->where('ai.status', 1)
            ->orderBy('ai.name', 'ASC')
            ->get();

        return response()->json([
            'status' => 1,
            'items' => $accountItems
        ]);
    }

    /**
     * Get case management engine for access control
     * This method is used by CaseController, so we need to replicate it here
     */
    private function caseManagementEngine()
    {
        $current_user = auth()->user();
        $accessCaseList = [];

        // Get cases based on user role and access
        if (in_array($current_user->menuroles, ['lawyer', 'sales', 'clerk', 'receptionist', 'chambering', 'maker'])) {
            $accessCaseList = DB::table('cases_pic')
                ->where('user_id', $current_user->id)
                ->pluck('case_id')
                ->toArray();
        }

        return $accessCaseList;
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\LoanCase;
use App\Models\LoanCaseBillMain;
use App\Models\LoanCaseInvoiceMain;
use App\Models\LoanCaseInvoiceDetails;
use App\Models\AccountLog;
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;
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
            // Only apply filter if there are accessible cases, otherwise return empty result
            if (!empty($accessCaseList)) {
                $query->whereIn('l.id', $accessCaseList);
            } else {
                // User has no accessible cases, return empty result
                $query->whereRaw('1 = 0'); // This ensures no results are returned
            }
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
            // Check if sst column exists before selecting it
            $hasSstColumn = DB::select("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'loan_case_invoice_details' 
                AND COLUMN_NAME = 'sst'");
            
            $selectFields = [
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
            ];
            
            // Add sst column if it exists
            if (isset($hasSstColumn[0]) && $hasSstColumn[0]->count > 0) {
                $selectFields[] = 'id.sst';
            } else {
                // If column doesn't exist, add NULL as sst
                $selectFields[] = DB::raw('NULL as sst');
            }
            
            $invoiceDetails = DB::table('loan_case_invoice_details as id')
                ->leftJoin('account_item as ai', 'id.account_item_id', '=', 'ai.id')
                ->leftJoin('account_category as ac', 'ai.account_cat_id', '=', 'ac.id')
                ->select($selectFields)
                ->where('id.invoice_main_id', '=', $invoiceId)
                ->where('id.status', '<>', 99)
                ->orderBy('ac.order', 'ASC')
                ->orderBy('id.id', 'ASC')
                ->get();
            
            // IMPORTANT: Do NOT recalculate SST when loading - just read what's in the database
            // SST recalculation should only happen when saving, not when loading
            // This preserves manually entered SST values like 11.21
            // Only for split invoices, we might need to recalculate, but even then, we should preserve manual values
            // For now, just read SST values directly from database without recalculation
            // if ($isSplitInvoice && isset($hasSstColumn[0]) && $hasSstColumn[0]->count > 0) {
            if (false && $isSplitInvoice && isset($hasSstColumn[0]) && $hasSstColumn[0]->count > 0) {
                // Get all account_item_ids that exist in any invoice for this bill
                $allAccountItemIds = DB::table('loan_case_invoice_details as ild')
                    ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
                    ->where('ild.loan_case_main_bill_id', $bill->id)
                    ->where('ild.status', '<>', 99)
                    ->whereIn('ai.account_cat_id', [1, 4]) // Professional fees and reimbursement
                    ->distinct()
                    ->pluck('ild.account_item_id')
                    ->toArray();
                
                // For each account_item_id, calculate total amount across all invoices and distribute SST
                foreach ($allAccountItemIds as $accountItemId) {
                    // Get total amount for this account_item_id across all invoices
                    $totalAmountForItem = DB::table('loan_case_invoice_details as ild')
                        ->where('ild.loan_case_main_bill_id', $bill->id)
                        ->where('ild.account_item_id', $accountItemId)
                        ->where('ild.status', '<>', 99)
                        ->sum('ild.amount');
                    
                    // Calculate total SST from total amount
                    $totalSstRaw = $totalAmountForItem * ($sstRate / 100);
                    $totalSstString = number_format($totalSstRaw, 3, '.', '');
                    if (substr($totalSstString, -1) == '5') {
                        $totalSstForItem = floor($totalSstRaw * 100) / 100; // Round down
                    } else {
                        $totalSstForItem = round($totalSstRaw, 2); // Normal rounding
                    }
                    
                    // Get all detail records for this account_item_id across all invoices
                    $allDetailsForItem = DB::table('loan_case_invoice_details as ild')
                        ->where('ild.loan_case_main_bill_id', $bill->id)
                        ->where('ild.account_item_id', $accountItemId)
                        ->where('ild.status', '<>', 99)
                        ->select('ild.id', 'ild.amount', 'ild.invoice_main_id')
                        ->get();
                    
                    // Distribute SST proportionally to each detail
                    $distributedSst = [];
                    $totalDistributedSst = 0;
                    
                    foreach ($allDetailsForItem as $detail) {
                        if ($totalAmountForItem > 0) {
                            $proportionalSst = ($totalSstForItem * $detail->amount) / $totalAmountForItem;
                            $distributedSst[$detail->id] = round($proportionalSst, 2);
                            $totalDistributedSst += $distributedSst[$detail->id];
                        }
                    }
                    
                    // Adjust for rounding differences - add difference to first detail
                    $difference = $totalSstForItem - $totalDistributedSst;
                    if (abs($difference) > 0.001 && count($distributedSst) > 0) {
                        $firstDetailId = array_key_first($distributedSst);
                        $distributedSst[$firstDetailId] = round($distributedSst[$firstDetailId] + $difference, 2);
                    }
                    
                    // Update SST values in the invoiceDetails collection for display
                    foreach ($invoiceDetails as $detail) {
                        if ($detail->account_item_id == $accountItemId && isset($distributedSst[$detail->id])) {
                            $detail->sst = $distributedSst[$detail->id];
                        }
                    }
                }
            }
            
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
        // Log that the method is being called
        Log::info("=== InvoiceController::update CALLED ===", [
            'invoice_id' => $invoiceId,
            'request_data' => $request->all(),
            'user_id' => auth()->id()
        ]);
        
        $current_user = auth()->user();
        
        $invoice = LoanCaseInvoiceMain::where('id', $invoiceId)
            ->where('status', '<>', 99)
            ->first();

        if (!$invoice) {
            Log::warning("Invoice not found", ['invoice_id' => $invoiceId]);
            return response()->json(['status' => 0, 'message' => 'Invoice not found'], 404);
        }
        
        Log::info("Invoice found", [
            'invoice_id' => $invoice->id,
            'invoice_no' => $invoice->invoice_no,
            'bill_id' => $invoice->loan_case_main_bill_id
        ]);

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
            
            Log::info("Processing details update", [
                'details_count' => count($details),
                'details' => $details
            ]);
            
            // Check if this is a split invoice (multiple invoices for the same bill)
            $partyCount = \App\Http\Controllers\EInvoiceContoller::getPartyCount($bill->id);
            $isSplitInvoice = $partyCount > 1;
            
            Log::info("Split invoice check", [
                'party_count' => $partyCount,
                'is_split_invoice' => $isSplitInvoice,
                'bill_id' => $bill->id
            ]);
            
            foreach ($details as $detail) {
                if (isset($detail['id']) && isset($detail['amount'])) {
                    // Get the existing detail to compare old and new amounts
                    $existingDetail = LoanCaseInvoiceDetails::where('id', $detail['id'])
                        ->where('invoice_main_id', $invoiceId)
                        ->first();
                    
                    if ($existingDetail) {
                        $oldAmount = $existingDetail->amount;
                        // Get old SST only if column exists
                        $hasSstColumn = DB::select("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
                            WHERE TABLE_SCHEMA = DATABASE() 
                            AND TABLE_NAME = 'loan_case_invoice_details' 
                            AND COLUMN_NAME = 'sst'");
                        $sstColumnExists = isset($hasSstColumn[0]) && $hasSstColumn[0]->count > 0;
                        $oldSst = $sstColumnExists ? ($existingDetail->sst ?? null) : null;
                        
                        // Preserve exact value entered by user (no rounding)
                        // Parse the value and format to exactly 2 decimal places to preserve precision
                        // This ensures values like 11.21 stay as 11.21, not 11.20
                        $amountValue = $detail['amount'];
                        
                        // Validate it's a valid number
                        if (!is_numeric($amountValue)) {
                            return response()->json(['status' => 0, 'message' => 'Invalid amount value: ' . $amountValue], 400);
                        }
                        
                        // Format to exactly 2 decimal places using number_format to preserve exact value
                        // This prevents floating point precision issues
                        $newAmount = number_format((float)$amountValue, 2, '.', '');
                        $newAmountFloat = (float)$newAmount;
                        
                        // Store custom SST if provided - save it to database
                        // Check if sst column exists first
                        $hasSstColumn = DB::select("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
                            WHERE TABLE_SCHEMA = DATABASE() 
                            AND TABLE_NAME = 'loan_case_invoice_details' 
                            AND COLUMN_NAME = 'sst'");
                        $sstColumnExists = isset($hasSstColumn[0]) && $hasSstColumn[0]->count > 0;
                        
                        $customSst = null;
                        $sstChanged = false;
                        if (isset($detail['sst']) && $detail['sst'] !== null && $detail['sst'] !== '') {
                            // Preserve exact SST value entered by user (no rounding)
                            // Format to 2 decimal places to preserve precision
                            $sstValue = $detail['sst'];
                            if (!is_numeric($sstValue)) {
                                return response()->json(['status' => 0, 'message' => 'Invalid SST value: ' . $sstValue], 400);
                            }
                            $customSst = number_format((float)$sstValue, 2, '.', '');
                            $customSstValues[$detail['id']] = $customSst;
                            
                            // Only save to database if column exists
                            if ($sstColumnExists) {
                                // Check if SST changed
                                if ($oldSst === null || abs($oldSst - $customSst) > 0.001) {
                                    $sstChanged = true;
                                }
                                
                                // Save custom SST to database
                                // Set SST value - will save together with amount below (line 677)
                                $existingDetail->sst = $customSst;
                                Log::info("Setting custom SST value (will save below)", [
                                    'detail_id' => $existingDetail->id,
                                    'old_sst' => $oldSst,
                                    'new_sst' => $customSst,
                                    'sst_changed' => $sstChanged,
                                    'customSstValues_array' => $customSstValues,
                                    'sst_in_model_before_save' => $existingDetail->sst
                                ]);
                            } else {
                                Log::warning("SST column does not exist, cannot save custom SST value", [
                                    'detail_id' => $existingDetail->id,
                                    'sst_value' => $customSst
                                ]);
                            }
                        } else {
                            // If SST is not provided and it was previously set, clear it to allow auto-calculation
                            if ($sstColumnExists && $oldSst !== null) {
                                $existingDetail->sst = null;
                                $sstChanged = true;
                                Log::info("Clearing custom SST value to allow auto-calculation", [
                                    'detail_id' => $existingDetail->id,
                                    'old_sst' => $oldSst
                                ]);
                            }
                        }
                        
                        // Update if amount changed OR SST changed
                        // Compare using float values, but save the exact string value
                        $amountChanged = abs($oldAmount - $newAmountFloat) > 0.001;
                        if ($amountChanged || $sstChanged) {
                            // Get account item name for logging
                            $accountItem = DB::table('account_item')
                                ->where('id', $existingDetail->account_item_id)
                                ->first();
                            $itemName = $accountItem ? ($accountItem->name ?? 'N/A') : 'N/A';
                            
                            Log::info("Updating invoice detail", [
                                'detail_id' => $existingDetail->id,
                                'invoice_id' => $invoiceId,
                                'account_item_id' => $existingDetail->account_item_id,
                                'item_name' => $itemName,
                                'old_amount' => $oldAmount,
                                'new_amount' => $newAmount,
                                'is_split_invoice' => $isSplitInvoice,
                                'party_count' => $partyCount
                            ]);
                            
                            // Update the detail with exact amount (preserve user input)
                            if ($amountChanged) {
                                // Save the exact value entered by user
                                $existingDetail->amount = $newAmount;
                            }
                            
                            // CRITICAL: For manual SST values, save them even for split invoices
                            // They will be preserved during split invoice recalculation below
                            if ($sstChanged && isset($customSst) && $sstColumnExists) {
                                // Use direct DB update to preserve exact SST value (bypasses Eloquent)
                                DB::table('loan_case_invoice_details')
                                    ->where('id', $existingDetail->id)
                                    ->update(['sst' => $customSst]);
                                
                                Log::info("SST saved via direct DB update", [
                                    'detail_id' => $existingDetail->id,
                                    'sst_value' => $customSst,
                                    'is_split_invoice' => $isSplitInvoice,
                                    'will_be_preserved' => $isSplitInvoice
                                ]);
                                
                                // Refresh model to get updated SST
                                $existingDetail->refresh();
                            }
                            
                            // For split invoices, DON'T save SST here - it will be recalculated below
                            // For single invoices, save amount (SST already saved above if manual)
                            if (!$isSplitInvoice || !$sstColumnExists) {
                                // Single invoice: save amount (SST already saved via direct DB update if manual)
                                if ($amountChanged) {
                                    $existingDetail->save();
                                } else if (!$sstChanged) {
                                    // Only save if nothing changed (shouldn't happen, but safety check)
                                    $existingDetail->save();
                                }
                                
                                // CRITICAL: Final verification after all saves
                                if ($sstChanged && isset($customSst)) {
                                    $existingDetail->refresh();
                                    $actualSst = (string)$existingDetail->sst;
                                    $expectedSst = (string)$customSst;
                                    $matches = $actualSst === $expectedSst;
                                    
                                    Log::info("Final SST verification", [
                                        'detail_id' => $existingDetail->id,
                                        'sst_in_db' => $actualSst,
                                        'expected' => $expectedSst,
                                        'match' => $matches
                                    ]);
                                    
                                    if (!$matches) {
                                        Log::error("SST STILL MISMATCHED AFTER DIRECT DB UPDATE!", [
                                            'detail_id' => $existingDetail->id,
                                            'expected' => $expectedSst,
                                            'actual' => $actualSst
                                        ]);
                                    }
                                }
                            } else {
                                // Split invoice: save amount
                                // SST will be handled by recalculation below (which will preserve manual values)
                                if ($amountChanged) {
                                    $existingDetail->save();
                                }
                                // Note: SST is NOT saved here for split invoices - it will be recalculated below
                                // But manual SST values will be preserved during recalculation
                            }
                            
                            // If this is a split invoice, update ori_invoice_amt and ori_invoice_sst to reflect the new total
                            // Sum all split invoice amounts for this account_item_id to get the new total
                            if ($isSplitInvoice) {
                                $newTotalAmount = LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $bill->id)
                                    ->where('account_item_id', $existingDetail->account_item_id)
                                    ->where('status', '<>', 99)
                                    ->sum('amount');
                                
                                // Calculate total SST from total amount (same logic as case details)
                                $totalSstRaw = $newTotalAmount * ($sstRate / 100);
                                $totalSstString = number_format($totalSstRaw, 3, '.', '');
                                if (substr($totalSstString, -1) == '5') {
                                    $newTotalSst = floor($totalSstRaw * 100) / 100; // Round down
                                } else {
                                    $newTotalSst = round($totalSstRaw, 2); // Normal rounding
                                }
                                
                                // Check if ori_invoice_sst column exists
                                $hasOriSstColumn = DB::select("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
                                    WHERE TABLE_SCHEMA = DATABASE() 
                                    AND TABLE_NAME = 'loan_case_invoice_details' 
                                    AND COLUMN_NAME = 'ori_invoice_sst'");
                                
                                $updateFields = ['ori_invoice_amt' => round($newTotalAmount, 2)];
                                if (isset($hasOriSstColumn[0]) && $hasOriSstColumn[0]->count > 0) {
                                    $updateFields['ori_invoice_sst'] = $newTotalSst;
                                }
                                
                                // Update ori_invoice_amt and ori_invoice_sst for ALL split invoices with this account_item_id
                                LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $bill->id)
                                    ->where('account_item_id', $existingDetail->account_item_id)
                                    ->where('status', '<>', 99)
                                    ->update($updateFields);
                                
                                Log::info("Updated ori_invoice_amt and ori_invoice_sst for split invoice", [
                                    'account_item_id' => $existingDetail->account_item_id,
                                    'new_total_amount' => $newTotalAmount,
                                    'new_total_sst' => $newTotalSst,
                                    'bill_id' => $bill->id
                                ]);
                            }
                            
                            // Create AccountLog entry for amount change
                            if ($amountChanged) {
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
                            
                            // Create AccountLog entry for SST change
                            if ($sstChanged) {
                                $AccountLog = new AccountLog();
                                $AccountLog->user_id = $current_user->id;
                                $AccountLog->case_id = $bill->case_id;
                                $AccountLog->bill_id = $bill->id;
                                $AccountLog->object_id = $existingDetail->id;
                                $AccountLog->ori_amt = $oldSst ?? 0;
                                $AccountLog->new_amt = $customSst ?? 0;
                                $AccountLog->action = 'Update SST';
                                $AccountLog->desc = $current_user->name . ' update invoice detail SST (' . $itemName . ') for invoice ' . $invoice->invoice_no . ' from ' . number_format($oldSst ?? 0, 2) . ' to ' . number_format($customSst ?? 0, 2);
                                $AccountLog->status = 1;
                                $AccountLog->created_at = date('Y-m-d H:i:s');
                                $AccountLog->save();
                            }
                            
                            // Verify the amount was saved correctly
                            $existingDetail->refresh();
                            if ($amountChanged && abs($existingDetail->amount - $newAmount) > 0.001) {
                                Log::warning("Amount mismatch after save", [
                                    'detail_id' => $existingDetail->id,
                                    'expected' => $newAmount,
                                    'actual' => $existingDetail->amount
                                ]);
                            }
                            if ($sstChanged) {
                                Log::info("SST saved successfully", [
                                    'detail_id' => $existingDetail->id,
                                    'saved_sst' => $existingDetail->sst,
                                    'expected_sst' => $customSst
                                ]);
                            }
                        }
                    }
                }
            }
        }

        // For split invoices, ALWAYS recalculate SST from total across all invoices
        // For single invoices, ONLY recalculate SST if NO manual SST values were provided
        // If manual SST values exist, skip recalculation entirely to preserve user input
        // (isSplitInvoice already defined above)
        
        // Check if any manual SST values were provided
        $hasAnyManualSst = !empty($customSstValues);
        
        if ($sstColumnExists) {
            if ($isSplitInvoice) {
                // For split invoices, ALWAYS recalculate SST from total (ignore custom SST inputs)
            // Get all account_item_ids that exist in any invoice for this bill
            $allAccountItemIds = DB::table('loan_case_invoice_details as ild')
                ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
                ->where('ild.loan_case_main_bill_id', $bill->id)
                ->where('ild.status', '<>', 99)
                ->whereIn('ai.account_cat_id', [1, 4]) // Professional fees and reimbursement
                ->distinct()
                ->pluck('ild.account_item_id')
                ->toArray();
            
            // For each account_item_id, calculate total amount across all invoices and distribute SST
            foreach ($allAccountItemIds as $accountItemId) {
                // Get total amount for this account_item_id across all invoices
                $totalAmountForItem = DB::table('loan_case_invoice_details as ild')
                    ->where('ild.loan_case_main_bill_id', $bill->id)
                    ->where('ild.account_item_id', $accountItemId)
                    ->where('ild.status', '<>', 99)
                    ->sum('ild.amount');
                
                // Calculate total SST from total amount
                $totalSstRaw = $totalAmountForItem * ($sstRate / 100);
                $totalSstString = number_format($totalSstRaw, 3, '.', '');
                if (substr($totalSstString, -1) == '5') {
                    $totalSstForItem = floor($totalSstRaw * 100) / 100; // Round down
                } else {
                    $totalSstForItem = round($totalSstRaw, 2); // Normal rounding
                }
                
                // Get all detail records for this account_item_id across all invoices
                $allDetailsForItem = DB::table('loan_case_invoice_details as ild')
                    ->where('ild.loan_case_main_bill_id', $bill->id)
                    ->where('ild.account_item_id', $accountItemId)
                    ->where('ild.status', '<>', 99)
                    ->select('ild.id', 'ild.amount', 'ild.invoice_main_id', 'ild.sst')
                    ->get();
                
                // For split invoices, recalculate SST from total BUT preserve manual SST values
                // Check if any detail has a manual SST value that should be preserved
                $manualSstDetails = [];
                $detailsToRecalculate = [];
                
                foreach ($allDetailsForItem as $index => $detail) {
                    // Check if this detail has a manual SST value in customSstValues
                    if (isset($customSstValues[$detail->id])) {
                        // This detail has a manual SST - preserve it
                        $manualSstDetails[$detail->id] = $customSstValues[$detail->id];
                        Log::info("Preserving manual SST for split invoice detail", [
                            'detail_id' => $detail->id,
                            'manual_sst' => $customSstValues[$detail->id],
                            'account_item_id' => $accountItemId
                        ]);
                    } else {
                        // This detail should be recalculated
                        $detailsToRecalculate[] = $detail;
                    }
                }
                
                // Calculate total SST for items that need recalculation
                $manualSstTotal = array_sum($manualSstDetails);
                $remainingSst = $totalSstForItem - $manualSstTotal;
                
                // Distribute remaining SST proportionally to details that need recalculation
                $distributedSst = [];
                $totalDistributedSst = 0;
                
                if (count($detailsToRecalculate) > 0 && $remainingSst > 0) {
                    $totalAmountForRecalc = array_sum(array_column($detailsToRecalculate, 'amount'));
                    
                    foreach ($detailsToRecalculate as $detail) {
                        if ($totalAmountForRecalc > 0) {
                            $proportionalSst = ($remainingSst * $detail->amount) / $totalAmountForRecalc;
                            $distributedSst[$detail->id] = round($proportionalSst, 2);
                            $totalDistributedSst += $distributedSst[$detail->id];
                        }
                    }
                    
                    // Adjust for rounding differences - add difference to first detail that needs recalculation
                    $difference = $remainingSst - $totalDistributedSst;
                    if (abs($difference) > 0.001 && count($distributedSst) > 0) {
                        $firstDetailId = array_key_first($distributedSst);
                        $distributedSst[$firstDetailId] = round($distributedSst[$firstDetailId] + $difference, 2);
                    }
                }
                
                // Merge manual SST values with recalculated ones
                $finalSstValues = array_merge($manualSstDetails, $distributedSst);
                
                // Update SST values in database
                foreach ($finalSstValues as $detailId => $sstValue) {
                    DB::table('loan_case_invoice_details')
                        ->where('id', $detailId)
                        ->update(['sst' => $sstValue]);
                }
                
                Log::info("Updated SST for split invoice account_item", [
                    'account_item_id' => $accountItemId,
                    'total_amount' => $totalAmountForItem,
                    'total_sst' => $totalSstForItem,
                    'distributed_sst' => $distributedSst
                ]);
            }
            } else {
                // Single invoice: ONLY recalculate SST if NO manual SST values were provided
                // If ANY manual SST was provided, skip recalculation entirely to preserve user input
                if ($hasAnyManualSst) {
                    Log::info("Skipping ALL SST recalculation for single invoice - manual SST values provided", [
                        'invoice_id' => $invoiceId,
                        'manual_sst_count' => count($customSstValues),
                        'manual_sst_values' => $customSstValues,
                        'hasAnyManualSst' => $hasAnyManualSst
                    ]);
                    
                    // CRITICAL: Verify SST values are still correct in database
                    foreach ($customSstValues as $detailId => $expectedSst) {
                        $savedDetail = LoanCaseInvoiceDetails::find($detailId);
                        if ($savedDetail) {
                            $actualSst = $savedDetail->sst;
                            $matches = (string)$actualSst === (string)$expectedSst;
                            Log::info("Verifying manual SST preserved", [
                                'detail_id' => $detailId,
                                'expected' => $expectedSst,
                                'actual' => $actualSst,
                                'match' => $matches
                            ]);
                            
                            if (!$matches) {
                                Log::error("MANUAL SST WAS OVERWRITTEN!", [
                                    'detail_id' => $detailId,
                                    'expected' => $expectedSst,
                                    'actual' => $actualSst
                                ]);
                            }
                        }
                    }
                } else {
                    // No manual SST provided - recalculate from amounts
                    // Get all details for this invoice
                    $invoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $invoiceId)
                        ->where('status', '<>', 99)
                        ->get();
                    
                    foreach ($invoiceDetails as $detail) {
                        // Get account item to check if taxable
                        $accountItem = DB::table('account_item')
                            ->where('id', $detail->account_item_id)
                            ->first();
                        
                        if ($accountItem && in_array($accountItem->account_cat_id, [1, 4])) {
                            // Calculate SST from individual amount
                            $sstRaw = $detail->amount * ($sstRate / 100);
                            $sstString = number_format($sstRaw, 3, '.', '');
                            if (substr($sstString, -1) == '5') {
                                $sstValue = floor($sstRaw * 100) / 100; // Round down
                            } else {
                                $sstValue = round($sstRaw, 2); // Normal rounding
                            }
                            
                            $detail->sst = $sstValue;
                            $detail->save();
                        } else {
                            // Non-taxable items: clear SST
                            $detail->sst = null;
                            $detail->save();
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
        
        // Log the updated reimbursement SST
        Log::info("Invoice reimbursement_sst updated", [
            'invoice_id' => $invoiceId,
            'reimbursement_sst' => $calculated['reimbursement_sst'],
            'reimbursement_amount' => $calculated['reimbursement_amount'],
            'calculated' => $calculated
        ]);

        // Log before updating bill totals
        Log::info("Before updatePfeeDisbAmountINVFromDetails - Checking detail amounts", [
            'invoice_id' => $invoiceId,
            'bill_id' => $bill->id
        ]);
        
        // Verify the amounts were saved correctly before recalculating totals
        if ($request->has('details')) {
            foreach ($request->input('details') as $detail) {
                if (isset($detail['id'])) {
                    $savedDetail = LoanCaseInvoiceDetails::where('id', $detail['id'])
                        ->where('invoice_main_id', $invoiceId)
                        ->first();
                    if ($savedDetail) {
                        Log::info("Detail amount after save (before updatePfeeDisbAmountINVFromDetails)", [
                            'detail_id' => $savedDetail->id,
                            'amount' => $savedDetail->amount,
                            'expected' => isset($detail['amount']) ? round(floatval($detail['amount']), 2) : null
                        ]);
                    }
                }
            }
        }
        
        // Update bill totals (same as case details)
        // Pass customSstValues to preserve manually entered SST values
        $this->updatePfeeDisbAmountINVFromDetails($bill->id, $customSstValues);
        
        // Recalculate transfer fee main amounts for all transfer fees that include this invoice
        $this->updateTransferFeeMainAmountsForInvoice($invoiceId);
        
        // Log after updating bill totals to see if amounts changed
        Log::info("After updatePfeeDisbAmountINVFromDetails - Checking detail amounts", [
            'invoice_id' => $invoiceId,
            'bill_id' => $bill->id
        ]);
        
        if ($request->has('details')) {
            foreach ($request->input('details') as $detail) {
                if (isset($detail['id'])) {
                    $savedDetail = LoanCaseInvoiceDetails::where('id', $detail['id'])
                        ->where('invoice_main_id', $invoiceId)
                        ->first();
                    if ($savedDetail) {
                        $expectedAmount = isset($detail['amount']) ? round(floatval($detail['amount']), 2) : null;
                        if ($expectedAmount && abs($savedDetail->amount - $expectedAmount) > 0.001) {
                            Log::warning("Detail amount changed after updatePfeeDisbAmountINVFromDetails!", [
                                'detail_id' => $savedDetail->id,
                                'expected' => $expectedAmount,
                                'actual' => $savedDetail->amount,
                                'difference' => $savedDetail->amount - $expectedAmount
                            ]);
                        } else {
                            Log::info("Detail amount still correct after updatePfeeDisbAmountINVFromDetails", [
                                'detail_id' => $savedDetail->id,
                                'amount' => $savedDetail->amount
                            ]);
                        }
                    }
                }
            }
        }

        return response()->json([
            'status' => 1,
            'message' => 'Invoice updated successfully',
            'invoice' => $invoice,
            'calculated' => $calculated
        ]);
    }

    /**
     * Calculate invoice amounts from details (same logic as CaseController)
     * For split invoices: calculates SST from total pfee/reimbursement to ensure correct distribution
     * @param int $invoiceId
     * @param float $sstRate
     * @param array $customSstValues Optional array of custom SST values keyed by detail_id
     */
    private function calculateInvoiceAmountsFromDetails($invoiceId, $sstRate, $customSstValues = [])
    {
        // Get invoice and bill info
        $invoice = LoanCaseInvoiceMain::find($invoiceId);
        if (!$invoice) {
            return ['pfee1' => 0, 'pfee2' => 0, 'sst' => 0, 'reimbursement_amount' => 0, 'reimbursement_sst' => 0, 'total' => 0];
        }
        
        $billId = $invoice->loan_case_main_bill_id;
        $sstRateDecimal = $sstRate / 100;
        
        // Check if this is a split invoice
        $invoiceCount = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $billId)
            ->where('status', '<>', 99)
            ->count();
        
        $isSplitInvoice = $invoiceCount > 1;
        
        // Check if sst column exists
        $hasSstColumn = DB::select("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'loan_case_invoice_details' 
            AND COLUMN_NAME = 'sst'");
        $sstColumnExists = isset($hasSstColumn[0]) && $hasSstColumn[0]->count > 0;
        
        $selectFields = ['ild.amount', 'ild.id as detail_id', 'ai.account_cat_id', 'ai.pfee1_item'];
        if ($sstColumnExists) {
            $selectFields[] = 'ild.sst';
        }
        
        // Get individual invoice details
        $details = DB::table('loan_case_invoice_details as ild')
            ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
            ->where('ild.invoice_main_id', $invoiceId)
            ->where('ild.status', '<>', 99)
            ->select($selectFields)
            ->get();

        $pfee1 = 0;
        $pfee2 = 0;
        $reimbursement_amount = 0;

        // Calculate pfee1, pfee2, and reimbursement_amount first
        foreach ($details as $detail) {
            if ($detail->account_cat_id == 1) {
                if ($detail->pfee1_item == 1) {
                    $pfee1 += $detail->amount;
                } else {
                    $pfee2 += $detail->amount;
                }
            } elseif ($detail->account_cat_id == 4) {
                $reimbursement_amount += $detail->amount;
            }
        }

        $pfee1 = round($pfee1, 2);
        $pfee2 = round($pfee2, 2);
        $totalPfee = $pfee1 + $pfee2;
        $reimbursement_amount = round($reimbursement_amount, 2);

        // Calculate SST based on whether it's a split invoice
        $sst = 0;
        $reimbursement_sst = 0;
        
        if ($isSplitInvoice) {
            // For split invoices: Calculate SST from stored SST values in database (which were recalculated above)
            // Sum up the SST values from detail items
            foreach ($details as $detail) {
                if ($detail->account_cat_id == 1) {
                    // Check if there's a custom SST value first (manually entered)
                    if (isset($customSstValues[$detail->detail_id])) {
                        $sst += floatval($customSstValues[$detail->detail_id]);
                    } elseif ($sstColumnExists && isset($detail->sst) && $detail->sst !== null && $detail->sst !== '') {
                        // For split invoices, use the stored SST value (which was recalculated from total)
                        $sst += floatval($detail->sst);
                    } else {
                        // Fallback: calculate from this invoice's amount (shouldn't happen if recalc worked)
                        $sst_calculation = $detail->amount * $sstRateDecimal;
                        $sst_string = number_format($sst_calculation, 3, '.', '');
                        if (substr($sst_string, -1) == '5') {
                            $row_sst = floor($sst_calculation * 100) / 100;
                        } else {
                            $row_sst = round($sst_calculation, 2);
                        }
                        $sst += $row_sst;
                    }
                } elseif ($detail->account_cat_id == 4) {
                    // Check if there's a custom SST value first (manually entered)
                    if (isset($customSstValues[$detail->detail_id])) {
                        $reimbursement_sst += floatval($customSstValues[$detail->detail_id]);
                    } elseif ($sstColumnExists && isset($detail->sst) && $detail->sst !== null && $detail->sst !== '') {
                        // For split invoices, use the stored SST value (which was recalculated from total)
                        $reimbursement_sst += floatval($detail->sst);
                    } else {
                        // Fallback: calculate from this invoice's amount (shouldn't happen if recalc worked)
                        $sst_calculation = $detail->amount * $sstRateDecimal;
                        $sst_string = number_format($sst_calculation, 3, '.', '');
                        if (substr($sst_string, -1) == '5') {
                            $row_sst = floor($sst_calculation * 100) / 100;
                        } else {
                            $row_sst = round($sst_calculation, 2);
                        }
                        $reimbursement_sst += $row_sst;
                    }
                }
            }
        } else {
            // For single invoice: calculate SST from individual detail items
            // Check for custom SST values first
            foreach ($details as $detail) {
                if ($detail->account_cat_id == 1) {
                    // Use custom SST if provided, otherwise check database, otherwise calculate
                    if (isset($customSstValues[$detail->detail_id])) {
                        $row_sst = floatval($customSstValues[$detail->detail_id]);
                    } elseif ($sstColumnExists && isset($detail->sst) && $detail->sst !== null && $detail->sst !== '') {
                        $row_sst = floatval($detail->sst);
                    } else {
                        // Apply special rounding rule for SST: round DOWN if 3rd decimal is 5
                        $sst_calculation = $detail->amount * $sstRateDecimal;
                        $sst_string = number_format($sst_calculation, 3, '.', '');
                        
                        if (substr($sst_string, -1) == '5') {
                            $row_sst = floor($sst_calculation * 100) / 100; // Round down
                        } else {
                            $row_sst = round($sst_calculation, 2); // Normal rounding
                        }
                    }
                    $sst += $row_sst;
                } elseif ($detail->account_cat_id == 4) {
                    // Use custom SST if provided, otherwise check database, otherwise calculate
                    if (isset($customSstValues[$detail->detail_id])) {
                        $row_sst = floatval($customSstValues[$detail->detail_id]);
                    } elseif ($sstColumnExists && isset($detail->sst) && $detail->sst !== null && $detail->sst !== '') {
                        $row_sst = floatval($detail->sst);
                    } else {
                        // Apply special rounding rule for reimbursement SST
                        $sst_calculation = $detail->amount * $sstRateDecimal;
                        $sst_string = number_format($sst_calculation, 3, '.', '');
                        
                        if (substr($sst_string, -1) == '5') {
                            $row_sst = floor($sst_calculation * 100) / 100; // Round down
                        } else {
                            $row_sst = round($sst_calculation, 2); // Normal rounding
                        }
                    }
                    $reimbursement_sst += $row_sst;
                }
            }
        }

        $sst = round($sst, 2);
        $reimbursement_sst = round($reimbursement_sst, 2);
        $total = round($totalPfee + $sst + $reimbursement_amount + $reimbursement_sst, 2);

        Log::info("Final calculated amounts", [
            'invoice_id' => $invoiceId,
            'is_split_invoice' => $isSplitInvoice,
            'reimbursement_sst' => $reimbursement_sst,
            'reimbursement_amount' => $reimbursement_amount,
            'sst' => $sst,
            'total_pfee' => $totalPfee
        ]);

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
     * @param int $billId
     * @param array $customSstValues Optional array of custom SST values to preserve (keyed by detail_id)
     */
    private function updatePfeeDisbAmountINVFromDetails($billId, $customSstValues = [])
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
            // Pass customSstValues to preserve manually entered SST values
            $invoiceCalculations = $this->calculateInvoiceAmountsFromDetails($invoice->id, $bill->sst_rate ?? 6, $customSstValues);

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
        
        // Get SST rate from bill
        $sstRate = $bill->sst_rate ?? 8;
        
        // Check if sst column exists
        $hasSstColumn = DB::select("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'loan_case_invoice_details' 
            AND COLUMN_NAME = 'sst'");
        $sstColumnExists = isset($hasSstColumn[0]) && $hasSstColumn[0]->count > 0;
        
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
            
            // Recalculate SST for remaining invoices
            // If only one invoice remains, calculate SST from individual amounts
            // If multiple invoices remain, calculate from total across all invoices
            if ($sstColumnExists) {
                if ($remainingCount == 1) {
                    // Single invoice: Calculate SST from individual item amounts
                    $singleInvoice = $remainingInvoices->first();
                    $singleInvoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $singleInvoice->id)
                        ->where('status', '<>', 99)
                        ->get();
                    
                    foreach ($singleInvoiceDetails as $detail) {
                        // Get account item to check if taxable
                        $accountItem = DB::table('account_item')
                            ->where('id', $detail->account_item_id)
                            ->first();
                        
                        if ($accountItem && in_array($accountItem->account_cat_id, [1, 4])) {
                            // Calculate SST from individual amount
                            $sstRaw = $detail->amount * ($sstRate / 100);
                            $sstString = number_format($sstRaw, 3, '.', '');
                            if (substr($sstString, -1) == '5') {
                                $sstValue = floor($sstRaw * 100) / 100; // Round down
                            } else {
                                $sstValue = round($sstRaw, 2); // Normal rounding
                            }
                            
                            $detail->sst = $sstValue;
                            $detail->save();
                        } else {
                            // Non-taxable items: clear SST
                            $detail->sst = null;
                            $detail->save();
                        }
                    }
                } else {
                    // Still split: Recalculate SST from total across all remaining invoices
                    $allAccountItemIds = DB::table('loan_case_invoice_details as ild')
                        ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
                        ->where('ild.loan_case_main_bill_id', $billId)
                        ->where('ild.status', '<>', 99)
                        ->whereIn('ai.account_cat_id', [1, 4]) // Professional fees and reimbursement
                        ->distinct()
                        ->pluck('ild.account_item_id')
                        ->toArray();
                    
                    foreach ($allAccountItemIds as $accountItemId) {
                        // Get total amount for this account_item_id across all remaining invoices
                        $totalAmountForItem = DB::table('loan_case_invoice_details as ild')
                            ->where('ild.loan_case_main_bill_id', $billId)
                            ->where('ild.account_item_id', $accountItemId)
                            ->where('ild.status', '<>', 99)
                            ->sum('ild.amount');
                        
                        // Calculate total SST from total amount
                        $totalSstRaw = $totalAmountForItem * ($sstRate / 100);
                        $totalSstString = number_format($totalSstRaw, 3, '.', '');
                        if (substr($totalSstString, -1) == '5') {
                            $totalSstForItem = floor($totalSstRaw * 100) / 100; // Round down
                        } else {
                            $totalSstForItem = round($totalSstRaw, 2); // Normal rounding
                        }
                        
                        // Get all detail records for this account_item_id across all remaining invoices
                        $allDetailsForItem = DB::table('loan_case_invoice_details as ild')
                            ->where('ild.loan_case_main_bill_id', $billId)
                            ->where('ild.account_item_id', $accountItemId)
                            ->where('ild.status', '<>', 99)
                            ->select('ild.id', 'ild.amount')
                            ->get();
                        
                        // Distribute SST proportionally to each detail
                        $distributedSst = [];
                        $totalDistributedSst = 0;
                        
                        foreach ($allDetailsForItem as $detail) {
                            if ($totalAmountForItem > 0) {
                                $proportionalSst = ($totalSstForItem * $detail->amount) / $totalAmountForItem;
                                $distributedSst[$detail->id] = round($proportionalSst, 2);
                                $totalDistributedSst += $distributedSst[$detail->id];
                            }
                        }
                        
                        // Adjust for rounding differences
                        $difference = $totalSstForItem - $totalDistributedSst;
                        if (abs($difference) > 0.001 && count($distributedSst) > 0) {
                            $firstDetailId = array_key_first($distributedSst);
                            $distributedSst[$firstDetailId] = round($distributedSst[$firstDetailId] + $difference, 2);
                        }
                        
                        // Update SST values in database
                        foreach ($distributedSst as $detailId => $sstValue) {
                            DB::table('loan_case_invoice_details')
                                ->where('id', $detailId)
                                ->update(['sst' => $sstValue]);
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
     * Update transfer fee main amounts for all transfer fees that include this invoice
     * This ensures the transfer_fee_main.transfer_amount matches the sum of all transfer_fee_details
     * Also updates transfer_fee_details to reflect current invoice SST values
     */
    private function updateTransferFeeMainAmountsForInvoice($invoiceId)
    {
        // Get current invoice values
        $invoice = LoanCaseInvoiceMain::find($invoiceId);
        if (!$invoice) {
            return;
        }
        
        // Find all transfer fee details that include this invoice
        $transferFeeDetails = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
            ->where('status', '<>', 99)
            ->get();
        
        if ($transferFeeDetails->isEmpty()) {
            return;
        }
        
        // Calculate current invoice totals
        $currentPfee = $invoice->pfee1_inv + $invoice->pfee2_inv;
        $currentSst = $invoice->sst_inv;
        $currentReimbursement = $invoice->reimbursement_amount;
        $currentReimbursementSst = $invoice->reimbursement_sst;
        
        // Calculate total transferred amounts from transfer_fee_details
        $totalTransferredPfee = $transferFeeDetails->sum('transfer_amount');
        $totalTransferredSst = $transferFeeDetails->sum('sst_amount');
        $totalTransferredReimbursement = $transferFeeDetails->sum('reimbursement_amount');
        $totalTransferredReimbursementSst = $transferFeeDetails->sum('reimbursement_sst_amount');
        
        // If there are transferred amounts, update them to reflect current invoice values
        if ($totalTransferredPfee > 0 || $totalTransferredSst > 0 || $totalTransferredReimbursement > 0 || $totalTransferredReimbursementSst > 0) {
            // Get unique transfer fee main IDs
            $transferFeeMainIds = $transferFeeDetails->pluck('transfer_fee_main_id')->unique();
            
            // Update each transfer fee detail to reflect current invoice values
            foreach ($transferFeeDetails as $tfd) {
                $oldReimbursementSst = $tfd->reimbursement_sst_amount ?? 0;
                $oldSst = $tfd->sst_amount ?? 0;
                
                // If reimbursement SST was transferred, update it to match current invoice value
                // Use proportion to distribute if there are multiple transfer records
                if ($totalTransferredReimbursementSst > 0 && $currentReimbursementSst > 0) {
                    // Calculate proportion of total transferred reimbursement SST that this detail represents
                    $proportion = $totalTransferredReimbursementSst > 0 ? ($oldReimbursementSst / $totalTransferredReimbursementSst) : 0;
                    $newReimbursementSst = round($currentReimbursementSst * $proportion, 2);
                    
                    // If this is the only transfer or it's the full amount, use the current invoice value directly
                    if ($transferFeeDetails->count() == 1 || abs($oldReimbursementSst - $totalTransferredReimbursementSst) < 0.01) {
                        $newReimbursementSst = $currentReimbursementSst;
                    }
                    
                    $tfd->reimbursement_sst_amount = $newReimbursementSst;
                    
                    if (abs($oldReimbursementSst - $newReimbursementSst) > 0.001) {
                        Log::info("Updating transfer fee detail reimbursement SST", [
                            'transfer_fee_detail_id' => $tfd->id,
                            'old_reimbursement_sst' => $oldReimbursementSst,
                            'new_reimbursement_sst' => $newReimbursementSst,
                            'proportion' => $proportion,
                            'current_invoice_reimbursement_sst' => $currentReimbursementSst
                        ]);
                    }
                }
                
                // Update SST if it changed (though this is less common)
                if ($totalTransferredSst > 0 && $currentSst > 0) {
                    $proportion = $totalTransferredSst > 0 ? ($oldSst / $totalTransferredSst) : 0;
                    $newSst = round($currentSst * $proportion, 2);
                    
                    if ($transferFeeDetails->count() == 1 || abs($oldSst - $totalTransferredSst) < 0.01) {
                        $newSst = $currentSst;
                    }
                    
                    $tfd->sst_amount = $newSst;
                }
                
                $tfd->save();
            }
            
            // Recalculate transfer fee main amount for each transfer fee
            foreach ($transferFeeMainIds as $transferFeeMainId) {
                $this->updateTransferFeeMainAmt($transferFeeMainId);
            }
            
            // Update ledger entries V2 to reflect updated transfer fee details
            $ledgerUpdateResult = $this->updateLedgerEntriesForTransferFeeDetails($invoiceId, $transferFeeDetails);
            
            Log::info("Updated transfer fee details and main amounts for invoice", [
                'invoice_id' => $invoiceId,
                'transfer_fee_main_ids' => $transferFeeMainIds->toArray(),
                'old_total_reimbursement_sst' => $totalTransferredReimbursementSst,
                'new_total_reimbursement_sst' => $currentReimbursementSst,
                'details_updated' => $transferFeeDetails->count(),
                'ledger_entries_updated' => $ledgerUpdateResult['updated_count'],
                'ledger_entries_created' => $ledgerUpdateResult['created_count']
            ]);
        }
    }
    
    /**
     * Update ledger entries V2 when transfer fee details are updated
     */
    private function updateLedgerEntriesForTransferFeeDetails($invoiceId, $transferFeeDetails)
    {
        $updatedCount = 0;
        $createdCount = 0;
        
        // Get invoice and bill information
        $invoice = LoanCaseInvoiceMain::find($invoiceId);
        if (!$invoice) {
            return ['updated_count' => 0, 'created_count' => 0];
        }
        
        $bill = LoanCaseBillMain::find($invoice->loan_case_main_bill_id);
        if (!$bill) {
            return ['updated_count' => 0, 'created_count' => 0];
        }
        
        foreach ($transferFeeDetails as $tfd) {
            // Get TransferFeeMain for transaction details
            $transferFeeMain = TransferFeeMain::find($tfd->transfer_fee_main_id);
            if (!$transferFeeMain) {
                continue;
            }
            
            // Update LedgerEntriesV2 - TRANSFER_OUT/IN (professional fee)
            $count = DB::table('ledger_entries_v2')
                ->where('key_id_2', $tfd->id)
                ->where('status', '<>', 99)
                ->whereIn('type', ['TRANSFER_OUT', 'TRANSFER_IN'])
                ->update(['amount' => $tfd->transfer_amount ?? 0, 'updated_at' => now()]);
            $updatedCount += $count;
            
            // Update LedgerEntriesV2 - SST_OUT/IN (professional fee SST)
            $count = DB::table('ledger_entries_v2')
                ->where('key_id_2', $tfd->id)
                ->where('status', '<>', 99)
                ->whereIn('type', ['SST_OUT', 'SST_IN'])
                ->update(['amount' => $tfd->sst_amount ?? 0, 'updated_at' => now()]);
            $updatedCount += $count;
            
            // Update or Create LedgerEntriesV2 - REIMB_OUT/IN (reimbursement amount)
            if (($tfd->reimbursement_amount ?? 0) > 0) {
                $reimbOutExists = DB::table('ledger_entries_v2')
                    ->where('key_id_2', $tfd->id)
                    ->where('status', '<>', 99)
                    ->where('type', 'REIMB_OUT')
                    ->exists();
                
                $reimbInExists = DB::table('ledger_entries_v2')
                    ->where('key_id_2', $tfd->id)
                    ->where('status', '<>', 99)
                    ->where('type', 'REIMB_IN')
                    ->exists();
                
                if ($reimbOutExists || $reimbInExists) {
                    $count = DB::table('ledger_entries_v2')
                        ->where('key_id_2', $tfd->id)
                        ->where('status', '<>', 99)
                        ->whereIn('type', ['REIMB_OUT', 'REIMB_IN'])
                        ->update(['amount' => $tfd->reimbursement_amount, 'updated_at' => now()]);
                    $updatedCount += $count;
                } else {
                    // Create REIMB_OUT entry
                    DB::table('ledger_entries_v2')->insert([
                        'transaction_id' => $transferFeeMain->transaction_id,
                        'case_id' => $bill->case_id,
                        'loan_case_main_bill_id' => $bill->id,
                        'loan_case_invoice_main_id' => $invoiceId,
                        'user_id' => auth()->id() ?? $transferFeeMain->transfer_by,
                        'key_id' => $transferFeeMain->id,
                        'key_id_2' => $tfd->id,
                        'transaction_type' => 'C',
                        'amount' => $tfd->reimbursement_amount,
                        'bank_id' => $transferFeeMain->transfer_from,
                        'remark' => $transferFeeMain->purpose ?? '',
                        'status' => 1,
                        'is_recon' => 0,
                        'created_at' => $transferFeeMain->transfer_date ?? now(),
                        'date' => $transferFeeMain->transfer_date ?? now(),
                        'type' => 'REIMB_OUT'
                    ]);
                    $createdCount++;
                    
                    // Create REIMB_IN entry
                    DB::table('ledger_entries_v2')->insert([
                        'transaction_id' => $transferFeeMain->transaction_id,
                        'case_id' => $bill->case_id,
                        'loan_case_main_bill_id' => $bill->id,
                        'loan_case_invoice_main_id' => $invoiceId,
                        'user_id' => auth()->id() ?? $transferFeeMain->transfer_by,
                        'key_id' => $transferFeeMain->id,
                        'key_id_2' => $tfd->id,
                        'transaction_type' => 'D',
                        'amount' => $tfd->reimbursement_amount,
                        'bank_id' => $transferFeeMain->transfer_to,
                        'remark' => $transferFeeMain->purpose ?? '',
                        'status' => 1,
                        'is_recon' => 0,
                        'created_at' => $transferFeeMain->transfer_date ?? now(),
                        'date' => $transferFeeMain->transfer_date ?? now(),
                        'type' => 'REIMB_IN'
                    ]);
                    $createdCount++;
                }
            }
            
            // Update or Create LedgerEntriesV2 - REIMB_SST_OUT/IN (reimbursement SST)
            if (($tfd->reimbursement_sst_amount ?? 0) > 0) {
                $reimbSstOutExists = DB::table('ledger_entries_v2')
                    ->where('key_id_2', $tfd->id)
                    ->where('status', '<>', 99)
                    ->where('type', 'REIMB_SST_OUT')
                    ->exists();
                
                $reimbSstInExists = DB::table('ledger_entries_v2')
                    ->where('key_id_2', $tfd->id)
                    ->where('status', '<>', 99)
                    ->where('type', 'REIMB_SST_IN')
                    ->exists();
                
                if ($reimbSstOutExists || $reimbSstInExists) {
                    $count = DB::table('ledger_entries_v2')
                        ->where('key_id_2', $tfd->id)
                        ->where('status', '<>', 99)
                        ->whereIn('type', ['REIMB_SST_OUT', 'REIMB_SST_IN'])
                        ->update(['amount' => $tfd->reimbursement_sst_amount, 'updated_at' => now()]);
                    $updatedCount += $count;
                    
                    Log::info("Updated ledger entries for reimbursement SST", [
                        'transfer_fee_detail_id' => $tfd->id,
                        'reimbursement_sst_amount' => $tfd->reimbursement_sst_amount,
                        'entries_updated' => $count
                    ]);
                } else {
                    // Create REIMB_SST_OUT entry
                    DB::table('ledger_entries_v2')->insert([
                        'transaction_id' => $transferFeeMain->transaction_id,
                        'case_id' => $bill->case_id,
                        'loan_case_main_bill_id' => $bill->id,
                        'loan_case_invoice_main_id' => $invoiceId,
                        'user_id' => auth()->id() ?? $transferFeeMain->transfer_by,
                        'key_id' => $transferFeeMain->id,
                        'key_id_2' => $tfd->id,
                        'transaction_type' => 'C',
                        'amount' => $tfd->reimbursement_sst_amount,
                        'bank_id' => $transferFeeMain->transfer_from,
                        'remark' => $transferFeeMain->purpose ?? '',
                        'status' => 1,
                        'is_recon' => 0,
                        'created_at' => $transferFeeMain->transfer_date ?? now(),
                        'date' => $transferFeeMain->transfer_date ?? now(),
                        'type' => 'REIMB_SST_OUT'
                    ]);
                    $createdCount++;
                    
                    // Create REIMB_SST_IN entry
                    DB::table('ledger_entries_v2')->insert([
                        'transaction_id' => $transferFeeMain->transaction_id,
                        'case_id' => $bill->case_id,
                        'loan_case_main_bill_id' => $bill->id,
                        'loan_case_invoice_main_id' => $invoiceId,
                        'user_id' => auth()->id() ?? $transferFeeMain->transfer_by,
                        'key_id' => $transferFeeMain->id,
                        'key_id_2' => $tfd->id,
                        'transaction_type' => 'D',
                        'amount' => $tfd->reimbursement_sst_amount,
                        'bank_id' => $transferFeeMain->transfer_to,
                        'remark' => $transferFeeMain->purpose ?? '',
                        'status' => 1,
                        'is_recon' => 0,
                        'created_at' => $transferFeeMain->transfer_date ?? now(),
                        'date' => $transferFeeMain->transfer_date ?? now(),
                        'type' => 'REIMB_SST_IN'
                    ]);
                    $createdCount++;
                    
                    Log::info("Created ledger entries for reimbursement SST", [
                        'transfer_fee_detail_id' => $tfd->id,
                        'reimbursement_sst_amount' => $tfd->reimbursement_sst_amount
                    ]);
                }
            }
        }
        
        return [
            'updated_count' => $updatedCount,
            'created_count' => $createdCount
        ];
    }
    
    /**
     * Update transfer_fee_main transfer_amount from sum of all transfer_fee_details
     */
    private function updateTransferFeeMainAmt($transferFeeMainId)
    {
        $sumTransferFee = 0;
        $transferFeeMain = TransferFeeMain::find($transferFeeMainId);
        
        if (!$transferFeeMain) {
            return;
        }
        
        $transferFeeDetailsSum = TransferFeeDetails::where('transfer_fee_main_id', $transferFeeMainId)
            ->where('status', '<>', 99)
            ->get();
        
        if ($transferFeeDetailsSum->count() > 0) {
            foreach ($transferFeeDetailsSum as $tfd) {
                // Include all amount components: transfer_amount + sst_amount + reimbursement_amount + reimbursement_sst_amount
                $sumTransferFee += $tfd->transfer_amount ?? 0;
                $sumTransferFee += $tfd->sst_amount ?? 0;
                $sumTransferFee += $tfd->reimbursement_amount ?? 0;
                $sumTransferFee += $tfd->reimbursement_sst_amount ?? 0;
            }
        }
        
        $oldAmount = $transferFeeMain->transfer_amount;
        $transferFeeMain->transfer_amount = round($sumTransferFee, 2);
        $transferFeeMain->save();
        
        if (abs($oldAmount - $transferFeeMain->transfer_amount) > 0.01) {
            Log::info("Updated transfer_fee_main amount", [
                'transfer_fee_main_id' => $transferFeeMainId,
                'old_amount' => $oldAmount,
                'new_amount' => $transferFeeMain->transfer_amount
            ]);
        }
    }

    /**
     * Get case management engine for access control
     * This method is used by CaseController, so we need to replicate it here
     */
    private function caseManagementEngine()
    {
        $current_user = auth()->user();
        
        // Use the same case access logic as CaseController
        $accessCaseList = CaseController::caseManagementEngine();

        // For maker, lawyer, clerk, receptionist, chambering roles, also check cases_pic table
        // This ensures users can see invoices for cases they are assigned as PIC
        if (in_array($current_user->menuroles, ['lawyer', 'sales', 'clerk', 'receptionist', 'chambering', 'maker'])) {
            $picCaseList = DB::table('cases_pic')
                ->where('pic_id', $current_user->id)
                ->where('status', 1)
                ->pluck('case_id')
                ->toArray();
            $accessCaseList = array_merge($accessCaseList, $picCaseList);
        }

        // Remove duplicates and return
        return array_unique($accessCaseList);
    }
}


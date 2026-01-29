<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\AccountCode;
use App\Models\AccountLog;
use App\Models\BankReconRecord;
use App\Models\Branch;
use App\Models\CaseAccountTransaction;
use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\EmailTemplateMain;
use App\Models\DocumentTemplateMain;
use App\Models\DocumentTemplateDetails;
use App\Models\DocumentTemplatePages;
use App\Models\caseTemplate;
use App\Models\Roles;
use App\Models\caseTemplateDetails;
use App\Models\EmailTemplateDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\MenusLang;
use App\Models\Users;
use App\Models\CaseMasterListCategory;
use App\Models\CaseMasterListField;
use App\Models\Customer;
use App\Models\EInvoiceDetails;
use App\Models\EInvoiceMain;
use App\Models\InvoiceBillingParty;
use App\Models\JournalEntryDetails;
use App\Models\JournalEntryMain;
use App\Models\LedgerEntries;
use App\Models\LedgerEntriesV2;
use App\Models\LoanCase;
use App\Models\LoanCaseAccount;
use App\Models\LoanCaseBillDetails;
use App\Models\LoanCaseBillMain;
use App\Models\LoanCaseInvoiceDetails;
use App\Models\LoanCaseInvoiceMain;
use App\Models\OfficeBankAccount;
use App\Models\Parameter;
use App\Models\SSTDetails;
use App\Models\SSTDetailsDelete;
use App\Models\SSTMain;
use App\Models\TransferFeeDetails;
use App\Models\TransferFeeDetailsDelete;
use App\Models\TransferFeeMain;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherDetails;
use App\Models\VoucherMain;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\BranchController;
use App\Services\BranchAccessService;
use Yajra\DataTables\Facades\DataTables;

class SSTV2Controller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function getAccessCode()
    {
        return 'SSTPermission';
    }

    /**
     * Display SST V2 listing page
     */
    public function sstListV2(Request $request)
    {
        $current_user = auth()->user();
        $branchInfo = BranchController::manageBranchAccess();

        if (AccessController::UserAccessPermissionController(PermissionController::SSTPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        return view('dashboard.sst-v2.index', [
            'current_user' => $current_user,
            'Branchs' => $branchInfo['branch'],
        ]);
    }

    /**
     * Display SST V2 create page
     */
    public function sstCreateV2(Request $request)
    {
        $current_user = auth()->user();
        $branchInfo = BranchController::manageBranchAccess();

        if (AccessController::UserAccessPermissionController(PermissionController::SSTPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        return view('dashboard.sst-v2.create', [
            'current_user' => $current_user,
            'Branchs' => $branchInfo['branch'],
        ]);
    }

    /**
     * Display SST V2 edit page
     */
    public function sstEditV2(Request $request, $id)
    {
        $current_user = auth()->user();
        $branchInfo = BranchController::manageBranchAccess();

        if (AccessController::UserAccessPermissionController(PermissionController::SSTPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $SSTMain = SSTMain::where('id', $id)->first();
        
        if (!$SSTMain) {
            return redirect()->route('sst-v2.list')->with('error', 'SST record not found');
        }

        // Get SST details with related data using joins
        $SSTDetails = DB::table('sst_details as sd')
            ->leftJoin('loan_case_invoice_main as im', 'im.id', '=', 'sd.loan_case_invoice_main_id')
            ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
            ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
            ->where('sd.sst_main_id', $id)
            ->select(
                'sd.*',
                'im.invoice_no',
                DB::raw('COALESCE(DATE(im.Invoice_date), DATE(b.invoice_date)) as invoice_date'),
                'im.amount as total_amount',
                'im.pfee1_inv as pfee1',
                'im.pfee2_inv as pfee2',
                'im.reimbursement_amount',
                'im.reimbursement_sst',
                'im.transferred_reimbursement_sst_amt',
                'b.collected_amt as collected_amount',
                'b.payment_receipt_date as payment_date',
                'l.case_ref_no',
                'l.id as case_id',
                'c.name as client_name'
            )
            ->get();

        // Debug: Log the query results
        Log::info('SST Details Query Results:', [
            'count' => $SSTDetails->count(),
            'data' => $SSTDetails->toArray()
        ]);

        return view('dashboard.sst-v2.edit', [
            'current_user' => $current_user,
            'Branchs' => $branchInfo['branch'],
            'SSTMain' => $SSTMain,
            'SSTDetails' => $SSTDetails,
        ]);
    }

    /**
     * Display SST V2 show page
     */
    public function sstShowV2(Request $request, $id)
    {
        $current_user = auth()->user();
        $branchInfo = BranchController::manageBranchAccess();

        if (AccessController::UserAccessPermissionController(PermissionController::SSTPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $SSTMain = SSTMain::where('id', $id)->first();
        
        if (!$SSTMain) {
            return redirect()->route('sst-v2.list')->with('error', 'SST record not found');
        }

        return view('dashboard.sst-v2.show', [
            'current_user' => $current_user,
            'Branchs' => $branchInfo['branch'],
            'SSTMain' => $SSTMain,
        ]);
    }

    /**
     * Get SST V2 main list data (DataTables)
     */
    public function getSSTMainListV2(Request $request)
    {
        if ($request->ajax()) {
            $current_user = auth()->user();

            $query = DB::table('sst_main as sm')
                ->leftJoin('branch as b', 'b.id', '=', 'sm.branch_id')
                ->leftJoin('users as u', 'u.id', '=', 'sm.paid_by')
                ->select(
                    'sm.id',
                    'sm.payment_date',
                    'sm.transaction_id',
                    'sm.remark',
                    'sm.amount',
                    'sm.status',
                    'sm.created_at',
                    'b.name as branch_name',
                    'u.name as paid_by_name'
                )
                ->where('sm.status', '<>', 99);

            // Apply branch filtering
            if (in_array($current_user->menuroles, ['maker'])) {
                if (in_array($current_user->branch_id, [5, 6])) {
                    $query = $query->whereIn('sm.branch_id', [5, 6]);
                } else {
                    $query = $query->where('sm.branch_id', $current_user->branch_id);
                }
            } elseif (in_array($current_user->menuroles, ['lawyer'])) {
                $query = $query->where('sm.branch_id', $current_user->branch_id);
            }

            // Apply date filters
            if ($request->input('transfer_date_from') && $request->input('transfer_date_to')) {
                $query = $query->whereBetween('sm.payment_date', [
                    $request->input('transfer_date_from'),
                    $request->input('transfer_date_to')
                ]);
            }

            // Apply branch filter
            if ($request->input('branch_id') && $request->input('branch_id') != 0) {
                $query = $query->where('sm.branch_id', $request->input('branch_id'));
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('sst-v2.edit', $row->id);
                    
                    return '
                        <div class="btn-group" role="group">
                            <a href="' . $editUrl . '" class="btn btn-sm btn-warning">
                                <i class="cil-pencil"></i> Edit
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->order(function ($query) {
                    $query->orderBy('sm.id', 'desc');
                })
                ->make(true);
        }
    }

    /**
     * Get invoice list for SST V2 (Transfer Fee v3 style)
     */
    public function getInvoiceListV2(Request $request)
    {
        try {
            $current_user = auth()->user();
            
            // Get page number and per page from request
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 20);
            
            // Get search parameters
            $searchInvoiceNo = $request->input('search_invoice_no');
            $searchCaseRef = $request->input('search_case_ref');
            $searchClient = $request->input('search_client');
            $searchBillingParty = $request->input('search_billing_party');
            $filterBranch = $request->input('filter_branch');
            // Convert to integer if not empty, to ensure proper comparison with branch IDs
            if ($filterBranch !== null && $filterBranch !== '') {
                $filterBranch = (int) $filterBranch;
            } else {
                $filterBranch = null;
            }
            $filterStartDate = $request->input('filter_start_date');
            $filterEndDate = $request->input('filter_end_date');
            
            // Get sorting parameters
            $sortField = $request->input('sort_field');
            $sortOrder = $request->input('sort_order', 'asc');
            
            // Validate sort order
            if (!in_array(strtolower($sortOrder), ['asc', 'desc'])) {
                $sortOrder = 'asc';
            }
            
            // Build query using loan_case_invoice_main as primary table
            $query = DB::table('loan_case_invoice_main as im')
                ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->leftJoin('invoice_billing_party as ibp', 'ibp.id', '=', 'im.bill_party_id')
                ->select(
                    'im.id',
                    'im.invoice_no',
                    DB::raw('DATE(im.Invoice_date) as invoice_date'),
                    'im.status',
                    'im.loan_case_main_bill_id',
                    'im.transferred_sst_amt',
                    'im.sst_inv',
                    'im.reimbursement_sst',
                    'im.transferred_reimbursement_sst_amt',
                    'im.pfee1_inv as pfee1',
                    'im.pfee2_inv as pfee2',
                    'im.amount as total_amt_inv',
                    'b.collected_amt as collected_amt_inv',
                    'b.payment_receipt_date',
                    'b.collected_amt as bill_collected_amt',
                    'b.total_amt as bill_total_amt',
                    'b.invoice_no as bill_invoice_no',
                    DB::raw('DATE(b.invoice_date) as bill_invoice_date'),
                    'b.invoice_branch_id',
                    'l.case_ref_no',
                    'l.id as case_id',
                    'c.name as client_name',
                    'ibp.customer_code',
                    'ibp.customer_name as billing_party_name'
                )
                ->where('im.status', '<>', 99)
                // Removed transferred_to_office_bank check - for SST V2, we check SST-specific flags instead
                // An invoice can have other amounts transferred but still have SST available
                ->whereNotNull('im.loan_case_main_bill_id')
                ->where('im.loan_case_main_bill_id', '>', 0)
                ->where('b.bln_invoice', '=', 1)  // Bill is an invoice
                ->where('im.bln_invoice', '=', 1)  // Invoice flag is set (should match bill level)
                ->where('b.bln_sst', '=', 0)  // SST not yet transferred on bill
                ->where('im.bln_sst', '=', 0);  // SST not yet transferred on invoice

            // Get accessible branches for user (already handles admin/account roles)
            $accessibleBranches = \App\Services\BranchAccessService::getAccessibleBranchIds($current_user);
            
            // Apply branch filtering
            // If user selects a specific branch from dropdown, check if they have access and apply that filter
            // Otherwise, apply the user's accessible branches filter
            if ($filterBranch && $filterBranch != 0) {
                // User selected a specific branch - check if they have access
                if (in_array($filterBranch, $accessibleBranches)) {
                    // User has access to selected branch - filter STRICTLY by that branch only
                    // Effective branch logic: 
                    // - If invoice_branch_id is NOT NULL and NOT 0, use invoice_branch_id
                    // - Otherwise, use case_branch_id (l.branch_id)
                    // Only show invoices where effective branch = filterBranch
                    $query->whereRaw("(
                        (b.invoice_branch_id IS NOT NULL AND b.invoice_branch_id <> 0 AND b.invoice_branch_id = ?)
                        OR
                        ((b.invoice_branch_id IS NULL OR b.invoice_branch_id = 0) AND l.branch_id = ?)
                    )", [$filterBranch, $filterBranch]);
                } else {
                    // User doesn't have access to selected branch - return empty result
                    $query->whereRaw('1 = 0'); // Force no results
                }
            } else {
                // No specific branch selected - apply user's accessible branches filter with fallback to case branch_id
                if (count($accessibleBranches) === 1) {
                    $query->where(function($q) use ($accessibleBranches) {
                        $q->where('b.invoice_branch_id', '=', $accessibleBranches[0])
                          ->orWhere(function($subQ) use ($accessibleBranches) {
                              $subQ->where(function($nullQ) {
                                  $nullQ->whereNull('b.invoice_branch_id')
                                        ->orWhere('b.invoice_branch_id', 0);
                              })
                              ->where('l.branch_id', '=', $accessibleBranches[0]);
                          });
                    });
                } else {
                    $query->where(function($q) use ($accessibleBranches) {
                        $q->whereIn('b.invoice_branch_id', $accessibleBranches)
                          ->orWhere(function($subQ) use ($accessibleBranches) {
                              $subQ->where(function($nullQ) {
                                  $nullQ->whereNull('b.invoice_branch_id')
                                        ->orWhere('b.invoice_branch_id', 0);
                              })
                              ->whereIn('l.branch_id', $accessibleBranches);
                          });
                    });
                }
            }

            // Apply search filters
            if ($searchInvoiceNo) {
                $invoiceNumbers = array_filter(array_map('trim', 
                    preg_split('/[,\n\r]+/', $searchInvoiceNo)
                ));
                
                if (!empty($invoiceNumbers)) {
                    $query = $query->where(function($q) use ($invoiceNumbers) {
                        foreach ($invoiceNumbers as $index => $invoiceNo) {
                            if ($index === 0) {
                                $q->where('im.invoice_no', 'LIKE', '%' . $invoiceNo . '%');
                            } else {
                                $q->orWhere('im.invoice_no', 'LIKE', '%' . $invoiceNo . '%');
                            }
                        }
                    });
                }
            }

            if ($searchCaseRef) {
                $query = $query->where('l.case_ref_no', 'LIKE', '%' . $searchCaseRef . '%');
            }

            if ($searchClient) {
                $query = $query->where('c.name', 'LIKE', '%' . $searchClient . '%');
            }

            if ($searchBillingParty) {
                $query = $query->where('ibp.customer_name', 'LIKE', '%' . $searchBillingParty . '%');
            }

            if ($filterStartDate && $filterEndDate) {
                $query = $query->whereBetween('b.payment_receipt_date', [$filterStartDate, $filterEndDate]);
            }

            // Handle transfer list filtering
            if ($request->input('type') == 'transferred') {
                $transferred_list = [];
                $SSTDetails = SSTDetails::where('sst_main_id', '=', $request->input('transaction_id'))->get();
                
                for ($i = 0; $i < count($SSTDetails); $i++) {
                    array_push($transferred_list, $SSTDetails[$i]->loan_case_invoice_main_id);
                }

                if ($request->input('transaction_id')) {
                    $query = $query->whereIn('im.id', $transferred_list);
                }
                // For 'transferred' type, don't filter by remaining SST as we want to show already transferred invoices
            } else {
                // For normal selection and 'add' type, bln_sst = 0 is sufficient to indicate SST hasn't been transferred
                // No need to check remaining SST amounts as bln_sst flag already handles this
                
                if ($request->input('type') == 'add') {
                    if ($request->input('transfer_list')) {
                        $transfer_list = json_decode($request->input('transfer_list'), true);
                        // Extract only the IDs from the transfer list
                        $transfer_ids = [];
                        if (is_array($transfer_list)) {
                            foreach ($transfer_list as $item) {
                                if (isset($item['id'])) {
                                    $transfer_ids[] = $item['id'];
                                }
                            }
                        }
                        if (!empty($transfer_ids)) {
                            $query = $query->whereIn('im.id', $transfer_ids);
                        }
                    }
                } else {
                    if ($request->input('transfer_list')) {
                        $transfer_list = json_decode($request->input('transfer_list'), true);
                        // Extract only the IDs from the transfer list
                        $transfer_ids = [];
                        if (is_array($transfer_list)) {
                            foreach ($transfer_list as $item) {
                                if (isset($item['id'])) {
                                    $transfer_ids[] = $item['id'];
                                }
                            }
                        }
                        if (!empty($transfer_ids)) {
                            $query = $query->whereNotIn('im.id', $transfer_ids);
                        }
                        $query = $query->where('im.transferred_sst_amt', '=', 0);
                    }
                }
            }

            // Get total count for pagination
            $totalCount = $query->count();

            // Apply sorting
            $orderByColumn = 'im.invoice_no';
            $orderByDirection = 'asc';
            
            if ($sortField) {
                // Map frontend sort fields to database columns
                $sortFieldMap = [
                    'case_ref_no' => 'l.case_ref_no',
                    'client_name' => 'c.name',
                    'invoice_no' => 'im.invoice_no',
                    'invoice_date' => 'im.Invoice_date',
                    'total_amt' => 'im.amount',
                    'collected_amt' => 'b.collected_amt',
                    'sst' => 'im.sst_inv',
                    'reimb_sst' => 'im.reimbursement_sst',
                    'total_sst' => DB::raw('(COALESCE(im.sst_inv, 0) + COALESCE(im.reimbursement_sst, 0))'),
                    'payment_date' => 'b.payment_receipt_date'
                ];
                
                if (isset($sortFieldMap[$sortField])) {
                    $orderByColumn = $sortFieldMap[$sortField];
                    $orderByDirection = $sortOrder;
                }
            }
            
            // Apply pagination
            $offset = ($page - 1) * $perPage;
            $rows = $query->offset($offset)
                ->limit($perPage)
                ->orderBy($orderByColumn, $orderByDirection)
                ->get();

            $invoiceList = view('dashboard.sst-v2.table.tbl-sst-invoice-list', [
                'rows' => $rows,
                'current_user' => auth()->user(),
                'currentPage' => $page,
                'totalPages' => ceil($totalCount / $perPage),
                'totalCount' => $totalCount
            ])->render();

            return response()->json([
                'status' => 1,
                'invoiceList' => $invoiceList,
                'count' => count($rows),
                'totalCount' => $totalCount,
                'currentPage' => $page,
                'totalPages' => ceil($totalCount / $perPage)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice add list for SST V2
     */
    public function getInvoiceAddListV2(Request $request)
    {
        if ($request->ajax()) {
            $current_user = auth()->user();

            $query = DB::table('loan_case_invoice_main as im')
                ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->leftJoin('invoice_billing_party as ibp', 'ibp.id', '=', 'im.bill_party_id')
                ->select(
                    'im.id',
                    'im.invoice_no',
                    DB::raw('DATE(im.Invoice_date) as invoice_date'),
                    'im.sst_inv',
                    'im.pfee1_inv as pfee1',
                    'im.pfee2_inv as pfee2',
                    'im.amount as total_amt_inv',
                    'b.collected_amt as collected_amt_inv',
                    'b.payment_receipt_date',
                    'b.collected_amt as bill_collected_amt',
                    'b.total_amt as bill_total_amt',
                    'b.invoice_no as bill_invoice_no',
                    DB::raw('DATE(b.invoice_date) as bill_invoice_date'),
                    'l.case_ref_no',
                    'l.id as case_id',
                    'c.name as client_name',
                    'ibp.customer_name as billing_party_name'
                )
                ->where('im.status', '<>', 99)
                ->where('im.transferred_to_office_bank', '=', 0);

            if ($request->input('transfer_list')) {
                $transfer_list = json_decode($request->input('transfer_list'), true);
                // Extract only the IDs from the transfer list
                $transfer_ids = [];
                if (is_array($transfer_list)) {
                    foreach ($transfer_list as $item) {
                        if (isset($item['id'])) {
                            $transfer_ids[] = $item['id'];
                        }
                    }
                }
                if (!empty($transfer_ids)) {
                    $query = $query->whereIn('im.id', $transfer_ids);
                }
            }

            // Apply branch filtering
            if (in_array($current_user->menuroles, ['maker'])) {
                if (in_array($current_user->branch_id, [5, 6])) {
                    $query = $query->whereIn('l.branch_id', [5, 6]);
                } else {
                    $query = $query->where('l.branch_id', $current_user->branch_id);
                }
            } elseif (in_array($current_user->menuroles, ['lawyer'])) {
                $query = $query->where('l.branch_id', $current_user->branch_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $checkboxId = 'add_bill_' . $row->id;
                    return '<input type="checkbox" name="add_bill" id="' . $checkboxId . '" value="' . $row->id . '">';
                })
                ->addColumn('total_amt_inv', function ($row) {
                    return number_format($row->bill_total_amt, 2);
                })
                ->addColumn('collected_amt', function ($row) {
                    return number_format($row->bill_collected_amt, 2);
                })
                ->addColumn('sst_inv', function ($row) {
                    return number_format($row->sst_inv, 2);
                })
                ->rawColumns(['action'])
                ->order(function ($query) {
                    $query->orderBy('im.id', 'asc');
                })
                ->make(true);
        }
    }

    /**
     * Create new SST V2 record
     */
    public function createNewSSTRecordV2(Request $request)
    {
        $current_user = auth()->user();
        $total_amount = 0;

        $SSTMain = new SSTMain();
        $SSTMain->payment_date = $request->input("payment_date");
        $SSTMain->paid_by = $current_user->id;
        $SSTMain->transaction_id = $request->input("trx_id");
        $SSTMain->receipt_no = '';
        $SSTMain->voucher_no = '';
        $SSTMain->branch_id = $request->input("branch");
        $SSTMain->remark = $request->input("remark");
        $SSTMain->status = 1;
        $SSTMain->created_at = date('Y-m-d H:i:s');
        $SSTMain->save();

        if ($request->input('add_bill') != null) {
            $add_bill = json_decode($request->input('add_bill'), true);
        }

        if (count($add_bill) > 0) {
            for ($i = 0; $i < count($add_bill); $i++) {
                $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $add_bill[$i]['id'])->first();
                
                $SSTDetails = new SSTDetails();
                $sst_amount = $add_bill[$i]['value'];
                
                // Get full reimbursement SST (not remaining)
                $reimbursement_sst = $LoanCaseInvoiceMain->reimbursement_sst ?? 0;
                
                // Total for this invoice = SST + full reimbursement SST
                $invoice_total = $sst_amount + $reimbursement_sst;
                $total_amount += $invoice_total;

                $SSTDetails->sst_main_id = $SSTMain->id;
                $SSTDetails->loan_case_invoice_main_id = $add_bill[$i]['id']; // Changed to use invoice_main_id
                $SSTDetails->created_by = $current_user->id;
                $SSTDetails->amount = $sst_amount;
                $SSTDetails->status = 1;
                $SSTDetails->created_at = date('Y-m-d H:i:s');
                $SSTDetails->save();

                // Update the invoice to mark SST as transferred
                LoanCaseInvoiceMain::where('id', '=', $add_bill[$i]['id'])->update([
                    'transferred_sst_amt' => $sst_amount,
                    'bln_sst' => 1
                ]);
                
                // Sync bln_sst to bill record
                if ($LoanCaseInvoiceMain && $LoanCaseInvoiceMain->loan_case_main_bill_id) {
                    LoanCaseBillMain::where('id', $LoanCaseInvoiceMain->loan_case_main_bill_id)
                        ->update(['bln_sst' => 1]);
                }
            }

            $SSTMain->amount = $total_amount;
            $SSTMain->save();
        }

        return response()->json(['status' => 1, 'data' => 'success']);
    }

    /**
     * Update SST V2 record
     */
    public function updateSSTV2(Request $request, $id)
    {
        $current_user = auth()->user();
        $total_amount = 0;

        $SSTMain = SSTMain::where('id', '=', $id)->first();
        $SSTMain->payment_date = $request->input("payment_date");
        $SSTMain->updated_by = $current_user->id;
        $SSTMain->transaction_id = $request->input("trx_id");
        $SSTMain->remark = $request->input("remark");
        $SSTMain->status = 1;
        $SSTMain->updated_at = date('Y-m-d H:i:s');
        $SSTMain->save();

        // Initialize add_bill as empty array
        $add_bill = [];
        if ($request->input('add_bill') != null) {
            $add_bill = json_decode($request->input('add_bill'), true);
            Log::info('SST V2 Update - add_bill data:', ['add_bill' => $add_bill]);
        } else {
            Log::info('SST V2 Update - no add_bill data received');
        }

        $SSTDetails = SSTDetails::where('sst_main_id', '=', $id)->get();

        if (count($SSTDetails) > 0) {
            for ($i = 0; $i < count($SSTDetails); $i++) {
                $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $SSTDetails[$i]['loan_case_invoice_main_id'])->first();
                if ($LoanCaseInvoiceMain) {
                    // Get SST amount
                    $transfer_amount = $LoanCaseInvoiceMain->sst_inv ?? 0;
                    
                    // Get full reimbursement SST (not remaining)
                    $reimbursement_sst = $LoanCaseInvoiceMain->reimbursement_sst ?? 0;
                    
                    // Total for this invoice = SST + full reimbursement SST (matches edit page display)
                    $invoice_total = $transfer_amount + $reimbursement_sst;
                    $total_amount += $invoice_total;
                    
                    $SSTDetails[$i]['amount'] = $transfer_amount;
                    $SSTDetails[$i]->save();
                }
            }
        }

        if (count($add_bill) > 0) {
            for ($i = 0; $i < count($add_bill); $i++) {
                $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $add_bill[$i]['id'])->first();
                if ($LoanCaseInvoiceMain) {
                    // Get SST amount from add_bill (this is the SST being transferred)
                    $sst_amount = $add_bill[$i]['value'];
                    
                    // Get full reimbursement SST (not remaining)
                    $reimbursement_sst = $LoanCaseInvoiceMain->reimbursement_sst ?? 0;
                    
                    // Total for this new invoice = SST + full reimbursement SST
                    $invoice_total = $sst_amount + $reimbursement_sst;
                    $total_amount += $invoice_total;
                    
                    $SSTDetails = new SSTDetails();
                    $SSTDetails->sst_main_id = $SSTMain->id;
                    $SSTDetails->loan_case_invoice_main_id = $add_bill[$i]['id'];
                    $SSTDetails->created_by = $current_user->id;
                    $SSTDetails->amount = $sst_amount;
                    $SSTDetails->status = 1;
                    $SSTDetails->created_at = date('Y-m-d H:i:s');
                    $SSTDetails->save();

                    // Update transferred amounts
                    $new_transferred_sst = ($LoanCaseInvoiceMain->transferred_sst_amt ?? 0) + $sst_amount;
                    // Note: transferred_reimbursement_sst_amt is not updated here as reimbursement SST is separate
                    
                    LoanCaseInvoiceMain::where('id', '=', $add_bill[$i]['id'])->update([
                        'transferred_sst_amt' => $new_transferred_sst,
                        'bln_sst' => 1
                    ]);
                    
                    // Sync bln_sst to bill record
                    if ($LoanCaseInvoiceMain && $LoanCaseInvoiceMain->loan_case_main_bill_id) {
                        LoanCaseBillMain::where('id', $LoanCaseInvoiceMain->loan_case_main_bill_id)
                            ->update(['bln_sst' => 1]);
                    }
                }
            }

            $SSTMain->amount = $total_amount;
            $SSTMain->save();
        } else {
            // Even if no new invoices, update the total amount for existing invoices
            $SSTMain->amount = $total_amount;
            $SSTMain->save();
        }

        return redirect()->route('sst-v2.edit', $id)->with('success', 'SST record updated successfully!');
    }

    /**
     * Recalculate SST record amount (fix existing records)
     */
    public function recalculateSSTAmount($id)
    {
        $sstMain = SSTMain::where('id', '=', $id)->first();
        
        if (!$sstMain) {
            return response()->json(['status' => 0, 'message' => 'SST record not found']);
        }

        $total_amount = 0;
        $SSTDetails = SSTDetails::where('sst_main_id', '=', $id)->get();

        if (count($SSTDetails) > 0) {
            foreach ($SSTDetails as $detail) {
                $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $detail->loan_case_invoice_main_id)->first();
                if ($LoanCaseInvoiceMain) {
                    // Get SST amount
                    $transfer_amount = $detail->amount ?? 0;
                    
                    // Get full reimbursement SST (not remaining)
                    $reimbursement_sst = $LoanCaseInvoiceMain->reimbursement_sst ?? 0;
                    
                    // Total for this invoice = SST + full reimbursement SST
                    $invoice_total = $transfer_amount + $reimbursement_sst;
                    $total_amount += $invoice_total;
                }
            }
        }

        $old_amount = $sstMain->amount ?? 0;
        $sstMain->amount = $total_amount;
        $sstMain->save();

        return response()->json([
            'status' => 1,
            'message' => 'SST amount recalculated successfully',
            'old_amount' => $old_amount,
            'new_amount' => $total_amount,
            'difference' => abs($old_amount - $total_amount)
        ]);
    }

    /**
     * Delete SST V2 record
     */
    public function deleteSSTV2(Request $request, $id)
    {
        $current_user = auth()->user();

        if ($request->input('delete_bill') != null) {
            $delete_bill = json_decode($request->input('delete_bill'), true);
        }

        if (count($delete_bill) > 0) {
            for ($i = 0; $i < count($delete_bill); $i++) {
                $SSTDetails = SSTDetails::where('sst_main_id', '=', $id)
                    ->where('loan_case_invoice_main_id', '=', $delete_bill[$i]['id'])
                    ->first();

                if ($SSTDetails) {
                    $SSTDetails->status = 99;
                    $SSTDetails->updated_by = $current_user->id;
                    $SSTDetails->updated_at = date('Y-m-d H:i:s');
                    $SSTDetails->save();

                    // Reset the invoice SST transferred amount
                    LoanCaseInvoiceMain::where('id', '=', $delete_bill[$i]['id'])->update([
                        'transferred_sst_amt' => 0,
                        'bln_sst' => 0
                    ]);
                }
            }
        }

        return response()->json(['status' => 1, 'data' => 'success']);
    }

    /**
     * Delete individual SST detail record
     */
    public function deleteSSTDetail(Request $request)
    {
        try {
            $sstDetailId = $request->input('sst_detail_id');
            $invoiceMainId = $request->input('invoice_main_id');

            if (!$sstDetailId || !$invoiceMainId) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Missing required parameters'
                ]);
            }

            // Get the SST detail record to get the amount and main ID
            $sstDetail = SSTDetails::where('id', $sstDetailId)->first();
            
            if (!$sstDetail) {
                return response()->json([
                    'status' => 0,
                    'message' => 'SST detail not found'
                ]);
            }

            $sstMainId = $sstDetail->sst_main_id;
            $deletedAmount = $sstDetail->amount;

            // Get invoice to check reimbursement SST
            $invoice = LoanCaseInvoiceMain::where('id', $invoiceMainId)->first();
            $deletedReimbSst = 0;
            if ($invoice) {
                // Calculate how much reimbursement SST was transferred for this invoice
                $reimbursementSst = $invoice->reimbursement_sst ?? 0;
                $transferredReimbSst = $invoice->transferred_reimbursement_sst_amt ?? 0;
                // The remaining reimbursement SST that was included in this SST record
                $deletedReimbSst = max(0, $reimbursementSst - $transferredReimbSst);
            }

            // Delete from sst_details table
            $deleted = SSTDetails::where('id', $sstDetailId)->delete();

            if ($deleted) {
                // Update loan_case_invoice_main record - reset both SST and reimbursement SST
                LoanCaseInvoiceMain::where('id', $invoiceMainId)->update([
                    'bln_sst' => 0,
                    'transferred_sst_amt' => 0,
                    'transferred_reimbursement_sst_amt' => 0  // Reset reimbursement SST
                ]);

                // Sync bln_sst to bill record - check if other invoices for this bill still have bln_sst = 1
                if ($invoice && $invoice->loan_case_main_bill_id) {
                    $otherInvoicesWithSst = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $invoice->loan_case_main_bill_id)
                        ->where('id', '!=', $invoiceMainId)
                        ->where('bln_sst', 1)
                        ->exists();
                    
                    if (!$otherInvoicesWithSst) {
                        LoanCaseBillMain::where('id', $invoice->loan_case_main_bill_id)
                            ->update(['bln_sst' => 0]);
                    }
                }

                // Recalculate SST main record total amount from remaining invoices
                $sstMain = SSTMain::where('id', $sstMainId)->first();
                if ($sstMain) {
                    $remainingDetails = SSTDetails::where('sst_main_id', $sstMainId)->get();
                    $newTotal = 0;

                    foreach ($remainingDetails as $detail) {
                        $remainingInvoice = LoanCaseInvoiceMain::find($detail->loan_case_invoice_main_id);
                        if ($remainingInvoice) {
                            $sstAmount = $detail->amount ?? 0;
                            $reimbursementSst = $remainingInvoice->reimbursement_sst ?? 0;
                            $transferredReimbSst = $remainingInvoice->transferred_reimbursement_sst_amt ?? 0;
                            $remainingReimbSst = max(0, $reimbursementSst - $transferredReimbSst);
                            $newTotal += $sstAmount + $remainingReimbSst;
                        }
                    }

                    $sstMain->amount = $newTotal;
                    $sstMain->save();
                }

                return response()->json([
                    'status' => 1,
                    'message' => 'SST detail deleted successfully',
                    'new_total' => $sstMain->amount ?? 0
                ]);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Failed to delete SST detail'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting SST detail: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'An error occurred while deleting the record'
            ]);
        }
    }

    /**
     * Export SST V2 to Excel
     */
    public function exportSSTV2Excel($id)
    {
        try {
            // Clear any existing output
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            // Get SST main record
            $SSTMain = SSTMain::where('id', $id)->first();
            if (!$SSTMain) {
                return response()->json(['error' => 'SST record not found'], 404);
            }

            // Get SST details with related data
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
                    'im.pfee1_inv as pfee1',
                    'im.pfee2_inv as pfee2',
                    'b.collected_amt as collected_amount',
                    'b.payment_receipt_date as payment_date',
                    'l.case_ref_no',
                    'l.id as case_id',
                    'c.name as client_name'
                )
                ->orderBy('im.invoice_no', 'asc')
                ->get();

            // Prepare data for export
            $exportData = [];
            $rowNumber = 1;
            
            foreach ($SSTDetails as $detail) {
                $exportData[] = [
                    'No' => $rowNumber++,
                    'Case Ref No' => $detail->case_ref_no ?? 'N/A',
                    'Client Name' => $detail->client_name ?? 'N/A',
                    'Invoice No' => $detail->invoice_no ?? 'N/A',
                    'Invoice Date' => $detail->invoice_date ?? 'N/A',
                    'Total Amount' => (float)($detail->total_amount ?? 0),
                    'Pfee1' => (float)($detail->pfee1 ?? 0),
                    'Pfee2' => (float)($detail->pfee2 ?? 0),
                    'Collected Amount' => (float)($detail->collected_amount ?? 0),
                    'SST Amount' => (float)($detail->amount ?? 0),
                    'Payment Date' => $detail->payment_date ?? 'N/A'
                ];
            }

            // Add totals row
            $totals = [
                'No' => 'TOTAL',
                'Case Ref No' => '',
                'Client Name' => '',
                'Invoice No' => '',
                'Invoice Date' => '',
                'Total Amount' => array_sum(array_column($exportData, 'Total Amount')),
                'Pfee1' => array_sum(array_column($exportData, 'Pfee1')),
                'Pfee2' => array_sum(array_column($exportData, 'Pfee2')),
                'Collected Amount' => array_sum(array_column($exportData, 'Collected Amount')),
                'SST Amount' => array_sum(array_column($exportData, 'SST Amount')),
                'Payment Date' => ''
            ];
            $exportData[] = $totals;

            return $this->exportToExcel($exportData, $SSTMain);

        } catch (\Exception $e) {
            Log::error('Error exporting SST V2 to Excel: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to export to Excel: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Alternative export method using direct file download
     */
    public function exportSSTV2ExcelDirect($id)
    {
        try {
            // Get SST main record
            $SSTMain = SSTMain::where('id', $id)->first();
            if (!$SSTMain) {
                return response()->json(['error' => 'SST record not found'], 404);
            }

            // Get SST details with related data
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
                    'im.pfee1_inv as pfee1',
                    'im.pfee2_inv as pfee2',
                    'b.collected_amt as collected_amount',
                    'b.payment_receipt_date as payment_date',
                    'l.case_ref_no',
                    'l.id as case_id',
                    'c.name as client_name'
                )
                ->orderBy('im.invoice_no', 'asc')
                ->get();

            // Prepare data for export
            $exportData = [];
            $rowNumber = 1;
            
            foreach ($SSTDetails as $detail) {
                $exportData[] = [
                    'No' => $rowNumber++,
                    'Case Ref No' => $detail->case_ref_no ?? 'N/A',
                    'Client Name' => $detail->client_name ?? 'N/A',
                    'Invoice No' => $detail->invoice_no ?? 'N/A',
                    'Invoice Date' => $detail->invoice_date ?? 'N/A',
                    'Total Amount' => (float)($detail->total_amount ?? 0),
                    'Pfee1' => (float)($detail->pfee1 ?? 0),
                    'Pfee2' => (float)($detail->pfee2 ?? 0),
                    'Collected Amount' => (float)($detail->collected_amount ?? 0),
                    'SST Amount' => (float)($detail->amount ?? 0),
                    'Payment Date' => $detail->payment_date ?? 'N/A'
                ];
            }

            // Add totals row
            $totals = [
                'No' => 'TOTAL',
                'Case Ref No' => '',
                'Client Name' => '',
                'Invoice No' => '',
                'Invoice Date' => '',
                'Total Amount' => array_sum(array_column($exportData, 'Total Amount')),
                'Pfee1' => array_sum(array_column($exportData, 'Pfee1')),
                'Pfee2' => array_sum(array_column($exportData, 'Pfee2')),
                'Collected Amount' => array_sum(array_column($exportData, 'Collected Amount')),
                'SST Amount' => array_sum(array_column($exportData, 'SST Amount')),
                'Payment Date' => ''
            ];
            $exportData[] = $totals;

            return $this->exportToExcelDirect($exportData, $SSTMain);

        } catch (\Exception $e) {
            Log::error('Error exporting SST V2 to Excel (Direct): ' . $e->getMessage());
            return response()->json(['error' => 'Failed to export to Excel: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export SST V2 to PDF
     */
    public function exportSSTV2PDF($id)
    {
        try {
            // Get SST main record
            $SSTMain = SSTMain::where('id', $id)->first();
            if (!$SSTMain) {
                return redirect()->back()->with('error', 'SST record not found');
            }

            // Get SST details with related data
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
                    'im.pfee1_inv as pfee1',
                    'im.pfee2_inv as pfee2',
                    'b.collected_amt as collected_amount',
                    'b.payment_receipt_date as payment_date',
                    'l.case_ref_no',
                    'l.id as case_id',
                    'c.name as client_name'
                )
                ->orderBy('im.invoice_no', 'asc')
                ->get();

            // Prepare data for export
            $exportData = [];
            $rowNumber = 1;
            
            foreach ($SSTDetails as $detail) {
                $exportData[] = [
                    'No' => $rowNumber++,
                    'Case Ref No' => $detail->case_ref_no ?? 'N/A',
                    'Client Name' => $detail->client_name ?? 'N/A',
                    'Invoice No' => $detail->invoice_no ?? 'N/A',
                    'Invoice Date' => $detail->invoice_date ?? 'N/A',
                    'Total Amount' => (float)($detail->total_amount ?? 0),
                    'Pfee1' => (float)($detail->pfee1 ?? 0),
                    'Pfee2' => (float)($detail->pfee2 ?? 0),
                    'Collected Amount' => (float)($detail->collected_amount ?? 0),
                    'SST Amount' => (float)($detail->amount ?? 0),
                    'Payment Date' => $detail->payment_date ?? 'N/A'
                ];
            }

            // Add totals row
            $totals = [
                'No' => 'TOTAL',
                'Case Ref No' => '',
                'Client Name' => '',
                'Invoice No' => '',
                'Invoice Date' => '',
                'Total Amount' => array_sum(array_column($exportData, 'Total Amount')),
                'Pfee1' => array_sum(array_column($exportData, 'Pfee1')),
                'Pfee2' => array_sum(array_column($exportData, 'Pfee2')),
                'Collected Amount' => array_sum(array_column($exportData, 'Collected Amount')),
                'SST Amount' => array_sum(array_column($exportData, 'SST Amount')),
                'Payment Date' => ''
            ];
            $exportData[] = $totals;

            return $this->exportToPDF($exportData, $SSTMain);

        } catch (\Exception $e) {
            Log::error('Error exporting SST V2 to PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export to PDF');
        }
    }

    /**
     * Export data to Excel
     */
    private function exportToExcel($data, $SSTMain)
    {
        $filename = 'SST_' . $SSTMain->id . '_' . date('Y-m-d') . '.xlsx';
        
        try {
            // Clear any existing output buffers
            while (ob_get_level()) {
                ob_end_clean();
            }
        
        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setCellValue('A1', 'SST Payment Record');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        
        // Set subtitle
        $sheet->setCellValue('A2', 'SST ID: ' . $SSTMain->id);
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
        
        $sheet->setCellValue('A3', 'Payment Date: ' . ($SSTMain->payment_date ?? 'N/A'));
        $sheet->mergeCells('A3:K3');
        $sheet->getStyle('A3')->getFont()->setSize(12);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');
        
        $sheet->setCellValue('A4', 'Transaction ID: ' . ($SSTMain->transaction_id ?? 'N/A'));
        $sheet->mergeCells('A4:K4');
        $sheet->getStyle('A4')->getFont()->setSize(12);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal('center');
        
        // Set headers
        $headers = ['No', 'Case Ref No', 'Client Name', 'Invoice No', 'Invoice Date', 'Total Amount', 'Pfee1', 'Pfee2', 'Collected Amount', 'SST Amount', 'Payment Date'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '6', $header);
            $col++;
        }
        
        // Style headers
        $sheet->getStyle('A6:K6')->getFont()->setBold(true);
        $sheet->getStyle('A6:K6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E0E0E0');
        
        // Add data
        $row = 7;
        foreach ($data as $item) {
            $col = 'A';
                foreach ($item as $key => $value) {
                // Format numeric columns with proper number formatting
                if (in_array($key, ['Total Amount', 'Pfee1', 'Pfee2', 'Collected Amount', 'SST Amount']) && is_numeric($value)) {
                    $sheet->setCellValue($col . $row, (float)$value);
                    $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                } else {
                $sheet->setCellValue($col . $row, $value);
                }
                $col++;
            }
            $row++;
        }
        
        // Style the totals row
        if (!empty($data) && end($data)['No'] === 'TOTAL') {
            $lastRow = $row - 1;
            $sheet->getStyle('A' . $lastRow . ':K' . $lastRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . $lastRow . ':K' . $lastRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F0F0F0');
        }
        
        // Auto-size columns
        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
            // Create Excel file using StreamedResponse for better server compatibility
        $writer = new Xlsx($spreadsheet);
        
            // Set headers before any output
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
                'Expires' => '0',
                'Content-Transfer-Encoding' => 'binary'
            ];
            
            // Use StreamedResponse instead of output buffering for better server compatibility
            return response()->stream(function() use ($writer) {
                $writer->save('php://output');
            }, 200, $headers);
            
        } catch (\Exception $e) {
            Log::error('Excel export error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to generate Excel file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Alternative Excel export method using temporary file
     */
    private function exportToExcelDirect($data, $SSTMain)
    {
        $filename = 'SST_' . $SSTMain->id . '_' . date('Y-m-d') . '.xlsx';
        
        try {
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set title
            $sheet->setCellValue('A1', 'SST Payment Record');
            $sheet->mergeCells('A1:K1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
            
            // Set subtitle
            $sheet->setCellValue('A2', 'SST ID: ' . $SSTMain->id);
            $sheet->mergeCells('A2:K2');
            $sheet->getStyle('A2')->getFont()->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
            
            $sheet->setCellValue('A3', 'Payment Date: ' . ($SSTMain->payment_date ?? 'N/A'));
            $sheet->mergeCells('A3:K3');
            $sheet->getStyle('A3')->getFont()->setSize(12);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');
            
            $sheet->setCellValue('A4', 'Transaction ID: ' . ($SSTMain->transaction_id ?? 'N/A'));
            $sheet->mergeCells('A4:K4');
            $sheet->getStyle('A4')->getFont()->setSize(12);
            $sheet->getStyle('A4')->getAlignment()->setHorizontal('center');
            
            // Set headers
            $headers = ['No', 'Case Ref No', 'Client Name', 'Invoice No', 'Invoice Date', 'Total Amount', 'Pfee1', 'Pfee2', 'Collected Amount', 'SST Amount', 'Payment Date'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '6', $header);
                $col++;
            }
            
            // Style headers
            $sheet->getStyle('A6:K6')->getFont()->setBold(true);
            $sheet->getStyle('A6:K6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E0E0E0');
            
            // Add data
            $row = 7;
            foreach ($data as $item) {
                $col = 'A';
                foreach ($item as $key => $value) {
                // Format numeric columns with proper number formatting
                if (in_array($key, ['Total Amount', 'Pfee1', 'Pfee2', 'Collected Amount', 'SST Amount']) && is_numeric($value)) {
                    $sheet->setCellValue($col . $row, (float)$value);
                    $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                } else {
                    $sheet->setCellValue($col . $row, $value);
                }
                    $col++;
                }
                $row++;
            }
            
            // Style the totals row
            if (!empty($data) && end($data)['No'] === 'TOTAL') {
                $lastRow = $row - 1;
                $sheet->getStyle('A' . $lastRow . ':K' . $lastRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $lastRow . ':K' . $lastRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F0F0F0');
            }
            
            // Auto-size columns
            foreach (range('A', 'K') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Create temporary file
            $tempPath = storage_path('app/temp/' . $filename);
            
            // Ensure temp directory exists
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            // Create Excel file
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempPath);
            
            // Return file download response
            return response()->download($tempPath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
            'Expires' => '0'
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            Log::error('Excel direct export error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to generate Excel file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simple Excel export without middleware interference
     */
    public function exportSSTV2ExcelSimple($id)
    {
        try {
            // Clear all output buffers
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            // Disable any output buffering
            ini_set('output_buffering', 'Off');
            ini_set('zlib.output_compression', 'Off');
            
            // Get SST main record
            $SSTMain = SSTMain::where('id', $id)->first();
            if (!$SSTMain) {
                http_response_code(404);
                echo json_encode(['error' => 'SST record not found']);
                exit;
            }

            // Get SST details with related data
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
                    'im.pfee1_inv as pfee1',
                    'im.pfee2_inv as pfee2',
                    'b.collected_amt as collected_amount',
                    'b.payment_receipt_date as payment_date',
                    'l.case_ref_no',
                    'l.id as case_id',
                    'c.name as client_name'
                )
                ->orderBy('im.invoice_no', 'asc')
                ->get();

            // Prepare data for export
            $exportData = [];
            $rowNumber = 1;
            
            foreach ($SSTDetails as $detail) {
                $exportData[] = [
                    'No' => $rowNumber++,
                    'Case Ref No' => $detail->case_ref_no ?? 'N/A',
                    'Client Name' => $detail->client_name ?? 'N/A',
                    'Invoice No' => $detail->invoice_no ?? 'N/A',
                    'Invoice Date' => $detail->invoice_date ?? 'N/A',
                    'Total Amount' => (float)($detail->total_amount ?? 0),
                    'Pfee1' => (float)($detail->pfee1 ?? 0),
                    'Pfee2' => (float)($detail->pfee2 ?? 0),
                    'Collected Amount' => (float)($detail->collected_amount ?? 0),
                    'SST Amount' => (float)($detail->amount ?? 0),
                    'Payment Date' => $detail->payment_date ?? 'N/A'
                ];
            }

            // Add totals row
            $totals = [
                'No' => 'TOTAL',
                'Case Ref No' => '',
                'Client Name' => '',
                'Invoice No' => '',
                'Invoice Date' => '',
                'Total Amount' => array_sum(array_column($exportData, 'Total Amount')),
                'Pfee1' => array_sum(array_column($exportData, 'Pfee1')),
                'Pfee2' => array_sum(array_column($exportData, 'Pfee2')),
                'Collected Amount' => array_sum(array_column($exportData, 'Collected Amount')),
                'SST Amount' => array_sum(array_column($exportData, 'SST Amount')),
                'Payment Date' => ''
            ];
            $exportData[] = $totals;

            // Create Excel file
            $filename = 'SST_' . $SSTMain->id . '_' . date('Y-m-d') . '.xlsx';
            
            // Set headers
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Content-Transfer-Encoding: binary');
            
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set title
            $sheet->setCellValue('A1', 'SST Payment Record');
            $sheet->mergeCells('A1:K1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
            
            // Set subtitle
            $sheet->setCellValue('A2', 'SST ID: ' . $SSTMain->id);
            $sheet->mergeCells('A2:K2');
            $sheet->getStyle('A2')->getFont()->setSize(12);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');
            
            $sheet->setCellValue('A3', 'Payment Date: ' . ($SSTMain->payment_date ?? 'N/A'));
            $sheet->mergeCells('A3:K3');
            $sheet->getStyle('A3')->getFont()->setSize(12);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal('center');
            
            $sheet->setCellValue('A4', 'Transaction ID: ' . ($SSTMain->transaction_id ?? 'N/A'));
            $sheet->mergeCells('A4:K4');
            $sheet->getStyle('A4')->getFont()->setSize(12);
            $sheet->getStyle('A4')->getAlignment()->setHorizontal('center');
            
            // Set headers
            $headers = ['No', 'Case Ref No', 'Client Name', 'Invoice No', 'Invoice Date', 'Total Amount', 'Pfee1', 'Pfee2', 'Collected Amount', 'SST Amount', 'Payment Date'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '6', $header);
                $col++;
            }
            
            // Style headers
            $sheet->getStyle('A6:K6')->getFont()->setBold(true);
            $sheet->getStyle('A6:K6')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E0E0E0');
            
            // Add data
            $row = 7;
            foreach ($exportData as $item) {
                $col = 'A';
                foreach ($item as $key => $value) {
                // Format numeric columns with proper number formatting
                if (in_array($key, ['Total Amount', 'Pfee1', 'Pfee2', 'Collected Amount', 'SST Amount']) && is_numeric($value)) {
                    $sheet->setCellValue($col . $row, (float)$value);
                    $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                } else {
                    $sheet->setCellValue($col . $row, $value);
                }
                    $col++;
                }
                $row++;
            }
            
            // Style the totals row
            if (!empty($exportData) && end($exportData)['No'] === 'TOTAL') {
                $lastRow = $row - 1;
                $sheet->getStyle('A' . $lastRow . ':K' . $lastRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $lastRow . ':K' . $lastRow)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F0F0F0');
            }
            
            // Auto-size columns
            foreach (range('A', 'K') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Create Excel file
            $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
            exit;
            
        } catch (\Exception $e) {
            Log::error('Simple Excel export error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Failed to generate Excel file: ' . $e->getMessage()]);
            exit;
        }
    }

    /**
     * Export data to PDF
     */
    private function exportToPDF($data, $SSTMain)
    {
        $filename = 'SST_' . $SSTMain->id . '_' . date('Y-m-d') . '.pdf';
        
        // Get branch info
        $branchInfo = DB::table('branch')->where('id', $SSTMain->branch_id)->first();
        $branchName = $branchInfo ? $branchInfo->name : 'Unknown';
        
        // Generate PDF using DomPDF
        $pdf = Pdf::loadView('dashboard.sst-v2.export-pdf', [
            'data' => $data,
            'SSTMain' => $SSTMain,
            'branchName' => $branchName
        ]);
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'landscape');
        
        // Download the PDF
        return $pdf->download($filename);
    }
}

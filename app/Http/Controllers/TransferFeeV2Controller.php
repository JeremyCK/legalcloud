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
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransferFeeV2Controller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function getAccessCode()
    {
        return 'TransferFeePermission';
    }

    public static function getSSTAccessCode()
    {
        return 'SSTPermission';
    }

    public static function getJEAccessCode()
    {
        return 'JournalEntryPermission';
    }

    /**
     * Display Transfer Fee V2 listing page
     */
    public function transferFeeListV2()
    {
        $current_user = auth()->user();
        $branchInfo = BranchController::manageBranchAccess();

        $TransferFeeMain = TransferFeeMain::where('status', '=', 1);

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
        }
        
        if (!in_array($current_user->menuroles, ['admin', 'account'])) {
            if (in_array($current_user->branch_id, [5,6])) {
                $TransferFeeMain = $TransferFeeMain->whereIn('branch_id', [5,6]);
            } else {
                $TransferFeeMain = $TransferFeeMain->where('branch_id', $current_user->branch_id);
            }
        }

        $TransferFeeMain = $TransferFeeMain->get();
        
        return view('dashboard.transfer-fee-v2.index', [
            'TransferFeeMain' => $TransferFeeMain,
            'current_user' => $current_user,
            'Branchs' => $branchInfo['branch']
        ]);
    }

    /**
     * Display Transfer Fee V2 simple listing page (without DataTables)
     */
    public function transferFeeListSimpleV2()
    {
        $current_user = auth()->user();
        $branchInfo = BranchController::manageBranchAccess();

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        // Get transfer fee data directly (no AJAX needed)
        $query = DB::table('transfer_fee_main as m')
            ->leftJoin('office_bank_account as b1', 'b1.id', '=', 'm.transfer_from')
            ->leftJoin('office_bank_account as b2', 'b2.id', '=', 'm.transfer_to')
            ->leftJoin('users as u', 'u.id', '=', 'm.transfer_by')
            ->leftJoin('branch as br', 'br.id', '=', 'm.branch_id')
            ->select(
                'm.*', 
                'b1.name as transfer_from_bank', 
                'b2.name as transfer_to_bank', 
                'b1.account_no as transfer_from_bank_acc_no', 
                'b2.account_no as transfer_to_bank_acc_no',
                'u.name as created_by_name',
                'br.name as branch_name'
            )
            ->where('m.status', '<>', 99);

        // User access control
        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [5,6])) {
                $query = $query->whereIn('b2.branch_id', [5,6]);
            } else {
                $query = $query->where('b2.branch_id', '=', $current_user->branch_id);
            }
        }

        $TransferFeeMain = $query->orderBy('m.transfer_date', 'DESC')->get();
        
        return view('dashboard.transfer-fee-v2.index-simple', [
            'current_user' => $current_user,
            'Branchs' => $branchInfo['branch'],
            'TransferFeeMain' => $TransferFeeMain
        ]);
    }

    /**
     * Display Transfer Fee V2 creation page
     */
    public function transferFeeCreateV2()
    {
        $current_user = auth()->user();

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $Branchs = Branch::where('status', '=', 1)->get();
        $branchInfo = BranchController::manageBranchAccess();

        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [5,6])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id', [5,6])->get();
            } else  {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
            }
        } else if (in_array($current_user->menuroles, ['lawyer'])) {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
        } else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }

        return view('dashboard.transfer-fee-v2.create', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'Branchs' => $branchInfo['branch']
        ]);
    }

    /**
     * Get available invoices for transfer (V2 - Invoice-based)
     */
    public function getTransferInvoiceListV2()
    {
        $current_user = auth()->user();

        // NEW: Query from loan_case_invoice_main instead of loan_case_bill_main
        $rows = DB::table('loan_case_invoice_main as im')
            ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'im.loan_case_main_bill_id')
            ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
            ->leftJoin('invoice_billing_party as ibp', 'ibp.id', '=', 'im.bill_party_id')
            ->select(
                'im.*',
                'b.invoice_no as bill_invoice_no',
                'b.invoice_date as bill_invoice_date',
                'l.case_ref_no',
                'c.name as client_name',
                'ibp.customer_code',
                'ibp.customer_name as billing_party_name'
            )
            ->where(function($q) {
                // Only show invoices that haven't been transferred (handle both 0 and NULL)
                $q->where('im.transferred_to_office_bank', '=', 0)
                  ->orWhereNull('im.transferred_to_office_bank');
            })
            ->where('im.status', '<>', 99)
            ->where('im.bln_invoice', '=', 1)
            ->whereNotExists(function($query) {
                // Exclude invoices that already have transfer_fee_details records (already transferred)
                $query->select(DB::raw(1))
                      ->from('transfer_fee_details as tfd')
                      ->whereColumn('tfd.loan_case_invoice_main_id', 'im.id')
                      ->where('tfd.status', '<>', 99);
            });

        // Branch access control
        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [5,6])) {
                $rows = $rows->whereIn('l.branch_id', [5,6]);
            } else {
                $rows = $rows->where('l.branch_id', '=', $current_user->branch_id);
            }
        }

        $rows = $rows->orderBy('im.id', 'ASC')->get();

        $invoiceList = view('dashboard.transfer-fee-v2.table.tbl-transfer-invoice-list', compact('rows', 'current_user'))->render();

        return [
            'status' => 1,
            'invoiceList' => $invoiceList,
        ];
    }

    /**
     * Create new transfer fee (V2 - Invoice-based)
     */
    public function createNewTransferFeeV2(Request $request)
    {
        $current_user = auth()->user();
        $total_amount = 0;

        $TransferFeeMain = new TransferFeeMain();
        $TransferFeeMain->transfer_date = $request->input("transfer_date");
        $TransferFeeMain->transfer_by = $current_user->id;
        $TransferFeeMain->transaction_id = $request->input("trx_id");
        $TransferFeeMain->receipt_no = '';
        $TransferFeeMain->voucher_no = '';
        $TransferFeeMain->transfer_from = $request->input("transfer_from");
        $TransferFeeMain->transfer_to = $request->input("transfer_to");
        $TransferFeeMain->purpose = $request->input("purpose");
        $TransferFeeMain->status = 1;
        $TransferFeeMain->created_at = date('Y-m-d H:i:s');

        // Get branch from destination bank account
        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('id', $request->input("transfer_to"))->first();
        if ($OfficeBankAccount) {
            $TransferFeeMain->branch_id = $OfficeBankAccount->branch_id;
        }

        $TransferFeeMain->save();

        // Process invoice items
        if ($request->input('add_invoice') != null) {
            $add_invoices = json_decode($request->input('add_invoice'), true);
        }

        if (count($add_invoices) > 0) {
            for ($i = 0; $i < count($add_invoices); $i++) {
                $TransferFeeDetails = new TransferFeeDetails();
                $total_amount += $add_invoices[$i]['value'];

                $TransferFeeDetails->transfer_fee_main_id = $TransferFeeMain->id;
                $TransferFeeDetails->loan_case_invoice_main_id = $add_invoices[$i]['id']; // NEW: Use invoice ID
                $TransferFeeDetails->loan_case_main_bill_id = $add_invoices[$i]['bill_id']; // Keep for backward compatibility
                $TransferFeeDetails->created_by = $current_user->id;
                $TransferFeeDetails->transfer_amount = $add_invoices[$i]['value'];

                if ($add_invoices[$i]['sst'] > 0) {
                    $TransferFeeDetails->sst_amount = $add_invoices[$i]['sst'];
                }

                $TransferFeeDetails->status = 1;
                $TransferFeeDetails->created_at = date('Y-m-d H:i:s');
                $TransferFeeDetails->save();

                // Update invoice record
                $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $add_invoices[$i]['id'])->first();
                if ($LoanCaseInvoiceMain) {
                    $LoanCaseInvoiceMain->transferred_pfee_amt += $add_invoices[$i]['value'];
                    
                    // Calculate total transferred amount for this invoice
                    $TransferFeeDetailsSum = TransferFeeDetails::where('loan_case_invoice_main_id', '=', $add_invoices[$i]['id'])->get();
                    
                    $SumTransferFee = 0;
                    if (count($TransferFeeDetailsSum) > 0) {
                        for ($j = 0; $j < count($TransferFeeDetailsSum); $j++) {
                            $SumTransferFee += $TransferFeeDetailsSum[$j]->transfer_amount;
                        }
                    }
                    
                    $LoanCaseInvoiceMain->transferred_pfee_amt = $SumTransferFee;
                    $inv_pfee = $LoanCaseInvoiceMain->pfee1_inv + $LoanCaseInvoiceMain->pfee2_inv;
                    
                    $sum = bcsub($inv_pfee, $SumTransferFee, 2);
                    
                    if (($sum) <= 0) {
                        $LoanCaseInvoiceMain->transferred_pfee_amt = $SumTransferFee;
                        $LoanCaseInvoiceMain->transferred_to_office_bank = 1;
                    }
                    
                    if ($add_invoices[$i]['sst'] > 0) {
                        $LoanCaseInvoiceMain->transferred_sst_amt = $add_invoices[$i]['sst'];
                    }
                    
                    $LoanCaseInvoiceMain->save();
                }

                // Create ledger entries
                $this->addLedgerEntriesV2($TransferFeeMain, $TransferFeeDetails, $LoanCaseInvoiceMain, $add_invoices[$i]['sst'], $add_invoices[$i]['value']);
            }
        }

        $TransferFeeMain->transfer_amount = $total_amount;
        $TransferFeeMain->save();

        return response()->json(['status' => 1, 'message' => 'Transfer fee created successfully']);
    }

    /**
     * Get Transfer Fee Main Records V2 (Enhanced version) - AJAX endpoint
     */
    public function getTransferMainRecordsV2(Request $request)
    {
        if ($request->ajax()) {
            $current_user = auth()->user();
            $branchInfo = BranchController::manageBranchAccess();

            $TransferFeeMain = DB::table('transfer_fee_main as m')
                ->leftJoin('office_bank_account as b1', 'b1.id', '=', 'm.transfer_from')
                ->leftJoin('office_bank_account as b2', 'b2.id', '=', 'm.transfer_to')
                ->leftJoin('users as u', 'u.id', '=', 'm.transfer_by')
                ->leftJoin('branch as br', 'br.id', '=', 'm.branch_id')
                ->select(
                    'm.*', 
                    'b1.name as transfer_from_bank', 
                    'b2.name as transfer_to_bank', 
                    'b1.account_no as transfer_from_bank_acc_no', 
                    'b2.account_no as transfer_to_bank_acc_no',
                    'u.name as created_by_name',
                    'br.name as branch_name'
                )
                ->where('m.status', '<>', 99);

            // Apply filters
            if ($request->input("transfer_date_from") <> null && $request->input("transfer_date_to") <> null) {
                $TransferFeeMain = $TransferFeeMain->whereBetween('m.transfer_date', [$request->input("transfer_date_from"), $request->input("transfer_date_to")]);
            } else {
                if ($request->input("transfer_date_from") <> null) {
                    $TransferFeeMain = $TransferFeeMain->where('m.transfer_date', '>=', $request->input("transfer_date_from"));
                }
                if ($request->input("transfer_date_to") <> null) {
                    $TransferFeeMain = $TransferFeeMain->where('m.transfer_date', '<=', $request->input("transfer_date_to"));
                }
            }

            if ($request->input("branch_id")) {
                $TransferFeeMain = $TransferFeeMain->where('m.branch_id', '=', $request->input("branch_id"));
            }

            // User access control
            if (in_array($current_user->menuroles, ['maker'])) {
                if (in_array($current_user->branch_id, [5,6])) {
                    $TransferFeeMain = $TransferFeeMain->whereIn('b2.branch_id', [5,6]);
                } else {
                    $TransferFeeMain = $TransferFeeMain->where('b2.branch_id', '=', $current_user->branch_id);
                }
            } else if (in_array($current_user->menuroles, ['sales'])) {
                if (in_array($current_user->id, [51, 32])) {
                    $TransferFeeMain = $TransferFeeMain->whereIn('b2.branch_id', [5, 6]);
                }
            } else {
                if (in_array($current_user->id, [13])) {
                    $TransferFeeMain = $TransferFeeMain->whereIn('b2.branch_id', [$current_user->branch_id]);
                }
            }

            $TransferFeeMain = $TransferFeeMain->orderBy('m.transfer_date', 'DESC')->get();

            return DataTables::of($TransferFeeMain)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $actionBtn = '
                    <div class="btn-group" role="group">
                        <a href="/transfer-fee-v2/' . $data->id . '" class="btn btn-info btn-sm" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/transfer-fee-v2/' . $data->id . '/edit" class="btn btn-warning btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="/transfer-fee-v2/' . $data->id . '/download" class="btn btn-success btn-sm" title="Download" target="_blank">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                    ';
                    return $actionBtn;
                })
                ->addColumn('transfer_from_bank', function ($data) {
                    $actionBtn = '<strong>' . $data->transfer_from_bank . '</strong><br/><small class="text-muted">(' . $data->transfer_from_bank_acc_no . ')</small>';
                    return $actionBtn;
                })
                ->addColumn('transfer_to_bank', function ($data) {
                    $actionBtn = '<strong>' . $data->transfer_to_bank . '</strong><br/><small class="text-muted">(' . $data->transfer_to_bank_acc_no . ')</small>';
                    return $actionBtn;
                })
                ->editColumn('is_recon', function ($data) {
                    if ($data->is_recon == '1')
                        return '<span class="label bg-success">Yes</span>';
                    else
                        return '<span class="label bg-warning">No</span>';
                })
                ->editColumn('transfer_date', function ($data) {
                    return date('d/m/Y', strtotime($data->transfer_date));
                })
                ->editColumn('transfer_amount', function ($data) {
                    return number_format($data->transfer_amount, 2);
                })
                ->rawColumns(['action', 'transfer_from_bank', 'transfer_to_bank', 'is_recon'])
                ->make(true);
        } else {
            // If not AJAX request, redirect to the main listing page
            return redirect()->route('transfer-fee-v2.index');
        }
    }

    /**
     * View Transfer Fee V2 details
     */
    public function transferFeeViewV2($id)
    {
        $current_user = auth()->user();
        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [2,5])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id',[$current_user->branch_id, 6])->get();
            } else  {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
            }
        } else  if (in_array($current_user->menuroles, ['lawyer'])) {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
        }else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }

        $Branchs = Branch::where('status', '=', 1)->get();
        $branchInfo = BranchController::manageBranchAccess();
        
        return view('dashboard.transfer-fee-v2.show', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'TransferFeeMain' => $TransferFeeMain,
            'Branchs' => $branchInfo['branch']
        ]);
    }

    /**
     * Edit Transfer Fee V2
     */
    public function transferFeeEditV2($id)
    {
        $current_user = auth()->user();
        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [2,5])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id',[$current_user->branch_id, 6])->get();
            } else  {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
            }
        } else  if (in_array($current_user->menuroles, ['lawyer'])) {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
        }else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }

        $Branchs = Branch::where('status', '=', 1)->get();
        $branchInfo = BranchController::manageBranchAccess();
        
        return view('dashboard.transfer-fee-v2.edit', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'TransferFeeMain' => $TransferFeeMain,
            'Branchs' => $branchInfo['branch']
        ]);
    }

    /**
     * Update Transfer Fee V2
     */
    public function transferFeeUpdateV2(Request $request, $id)
    {
        $current_user = auth()->user();
        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();

        if (!$TransferFeeMain) {
            return response()->json(['status' => 0, 'message' => 'Transfer fee record not found']);
        }

        $TransferFeeMain->transfer_date = $request->input("transfer_date");
        $TransferFeeMain->transaction_id = $request->input("transaction_id");
        $TransferFeeMain->transfer_from = $request->input("transfer_from");
        $TransferFeeMain->transfer_to = $request->input("transfer_to");
        $TransferFeeMain->purpose = $request->input("purpose");
        $TransferFeeMain->updated_at = date('Y-m-d H:i:s');

        $TransferFeeMain->save();

        return response()->json(['status' => 1, 'message' => 'Transfer fee updated successfully']);
    }

    /**
     * Delete Transfer Fee V2
     */
    public function transferFeeDeleteV2($id)
    {
        $current_user = auth()->user();
        $TransferFeeMain = TransferFeeMain::where('id', '=', $id)->first();

        if (!$TransferFeeMain) {
            return response()->json(['status' => 0, 'message' => 'Transfer fee record not found']);
        }

        // Delete associated transfer fee details
        TransferFeeDetails::where('transfer_fee_main_id', $id)->delete();

        // Delete the main record
        $TransferFeeMain->delete();

        return response()->json(['status' => 1, 'message' => 'Transfer fee deleted successfully']);
    }

    /**
     * Alternative simple method for getting transfer records (backup)
     */
    public function getTransferRecordsSimpleV2(Request $request)
    {
        try {
            $current_user = auth()->user();
            
            if (!$current_user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $query = DB::table('transfer_fee_main as m')
                ->leftJoin('office_bank_account as b1', 'b1.id', '=', 'm.transfer_from')
                ->leftJoin('office_bank_account as b2', 'b2.id', '=', 'm.transfer_to')
                ->leftJoin('users as u', 'u.id', '=', 'm.transfer_by')
                ->leftJoin('branch as br', 'br.id', '=', 'm.branch_id')
                ->select(
                    'm.*', 
                    'b1.name as transfer_from_bank', 
                    'b2.name as transfer_to_bank', 
                    'b1.account_no as transfer_from_bank_acc_no', 
                    'b2.account_no as transfer_to_bank_acc_no',
                    'u.name as created_by_name',
                    'br.name as branch_name'
                )
                ->where('m.status', '<>', 99);

            // Apply filters
            if ($request->input("transfer_date_from")) {
                $query = $query->where('m.transfer_date', '>=', $request->input("transfer_date_from"));
            }
            if ($request->input("transfer_date_to")) {
                $query = $query->where('m.transfer_date', '<=', $request->input("transfer_date_to"));
            }
            if ($request->input("branch_id")) {
                $query = $query->where('m.branch_id', '=', $request->input("branch_id"));
            }

            // User access control
            if (in_array($current_user->menuroles, ['maker'])) {
                if (in_array($current_user->branch_id, [5,6])) {
                    $query = $query->whereIn('b2.branch_id', [5,6]);
                } else {
                    $query = $query->where('b2.branch_id', '=', $current_user->branch_id);
                }
            }

            $data = $query->orderBy('m.transfer_date', 'DESC')->get();

            return response()->json([
                'status' => 1,
                'data' => $data,
                'count' => count($data)
            ]);

        } catch (\Exception $e) {
            Log::error('Transfer Fee V2 Simple List Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Create ledger entries for V2
     */
    private function addLedgerEntriesV2($TransferFeeMain, $TransferFeeDetails, $LoanCaseInvoiceMain, $sst_amount, $transfer_amount)
    {
        // Get the bill record for case information
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $LoanCaseInvoiceMain->loan_case_main_bill_id)->first();
        
        if (!$LoanCaseBillMain) {
            return;
        }

        // Create ledger entries for transfer out
        $LedgerEntries = new LedgerEntriesV2();
        $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
        $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
        $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
        $LedgerEntries->loan_case_invoice_main_id = $LoanCaseInvoiceMain->id;
        $LedgerEntries->user_id = auth()->user()->id;
        $LedgerEntries->key_id = $TransferFeeDetails->id;
        $LedgerEntries->transaction_type = 'C';
        $LedgerEntries->amount = $transfer_amount;
        $LedgerEntries->bank_id = $TransferFeeMain->transfer_from;
        $LedgerEntries->remark = $TransferFeeMain->purpose;
        $LedgerEntries->status = 1;
        $LedgerEntries->created_at = date('Y-m-d H:i:s');
        $LedgerEntries->date = $TransferFeeMain->transfer_date;
        $LedgerEntries->type = 'TRANSFER_OUT';
        $LedgerEntries->save();

        // Create ledger entries for transfer in
        $LedgerEntries = new LedgerEntriesV2();
        $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
        $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
        $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
        $LedgerEntries->loan_case_invoice_main_id = $LoanCaseInvoiceMain->id;
        $LedgerEntries->user_id = auth()->user()->id;
        $LedgerEntries->key_id = $TransferFeeDetails->id;
        $LedgerEntries->transaction_type = 'D';
        $LedgerEntries->amount = $transfer_amount;
        $LedgerEntries->bank_id = $TransferFeeMain->transfer_to;
        $LedgerEntries->remark = $TransferFeeMain->purpose;
        $LedgerEntries->status = 1;
        $LedgerEntries->created_at = date('Y-m-d H:i:s');
        $LedgerEntries->date = $TransferFeeMain->transfer_date;
        $LedgerEntries->type = 'TRANSFER_IN';
        $LedgerEntries->save();

        // Create SST ledger entries if applicable
        if ($sst_amount > 0) {
            // SST Out
            $LedgerEntries = new LedgerEntriesV2();
            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
            $LedgerEntries->loan_case_invoice_main_id = $LoanCaseInvoiceMain->id;
            $LedgerEntries->user_id = auth()->user()->id;
            $LedgerEntries->key_id = $TransferFeeDetails->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $sst_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_from;
            $LedgerEntries->remark = $TransferFeeMain->purpose . ' - SST';
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'SST_OUT';
            $LedgerEntries->save();

            // SST In
            $LedgerEntries = new LedgerEntriesV2();
            $LedgerEntries->transaction_id = $TransferFeeMain->transaction_id;
            $LedgerEntries->case_id = $LoanCaseBillMain->case_id;
            $LedgerEntries->loan_case_main_bill_id = $LoanCaseBillMain->id;
            $LedgerEntries->loan_case_invoice_main_id = $LoanCaseInvoiceMain->id;
            $LedgerEntries->user_id = auth()->user()->id;
            $LedgerEntries->key_id = $TransferFeeDetails->id;
            $LedgerEntries->transaction_type = 'D';
            $LedgerEntries->amount = $sst_amount;
            $LedgerEntries->bank_id = $TransferFeeMain->transfer_to;
            $LedgerEntries->remark = $TransferFeeMain->purpose . ' - SST';
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $TransferFeeMain->transfer_date;
            $LedgerEntries->type = 'SST_IN';
            $LedgerEntries->save();
        }
    }

    /**
     * Test method to create a sample transfer fee record (for testing only)
     */
    public function createTestRecordV2()
    {
        try {
            $current_user = auth()->user();
            
            // Create a test transfer fee record
            $TransferFeeMain = new TransferFeeMain();
            $TransferFeeMain->transfer_date = date('Y-m-d');
            $TransferFeeMain->transfer_by = $current_user->id;
            $TransferFeeMain->transaction_id = 'TEST-V2-' . date('YmdHis');
            $TransferFeeMain->receipt_no = '';
            $TransferFeeMain->voucher_no = '';
            $TransferFeeMain->transfer_from = 1; // Default bank account
            $TransferFeeMain->transfer_to = 2; // Default bank account
            $TransferFeeMain->purpose = 'Test Transfer Fee V2 Record';
            $TransferFeeMain->transfer_amount = 1000.00;
            $TransferFeeMain->status = 1;
            $TransferFeeMain->branch_id = $current_user->branch_id ?? 1;
            $TransferFeeMain->created_at = date('Y-m-d H:i:s');
            $TransferFeeMain->save();

            return response()->json([
                'status' => 1, 
                'message' => 'Test record created successfully',
                'record_id' => $TransferFeeMain->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0, 
                'message' => 'Error creating test record: ' . $e->getMessage()
            ]);
        }
    }
}

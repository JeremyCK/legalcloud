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
use App\Models\CaseMasterListMainCat;
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
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class EInvoiceContoller extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['clientEinvoiceData', 'updateClientEinvoiceData']);
        // $this->middleware('admin');

    }

    public static function getEinvoiceCustomerMandatoryField()
    {
        return [
            'customer_code',
            'customer_name',
            // 'brn',
            // 'brn2',
            // 'sales_tax_no',
            // 'service_tax_no',
            'customer_category',
            'id_no',
            'id_type',
            'tin',
            'address_1',
            // 'address_2',
            // 'address_3',
            // 'address_4',
            'postcode',
            'city',
            'state',
            'country',
            'phone1'
        ];
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $accounts = DB::table('account AS a')
            ->leftJoin('account_category AS ac', 'ac.id', '=', 'a.account_category_id')
            ->select('a.*', 'ac.category')
            ->orderBy('id', 'ASC')
            ->paginate(10);

        return view('dashboard.account.index', ['accounts' => $accounts]);
    }

    public function EInvoiceList()
    {
        $current_user = auth()->user();
        $branchInfo = BranchController::manageBranchAccess();

        $EInvoiceMain = EInvoiceMain::where('status', '=', 1);

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        if (!in_array($current_user->menuroles, ['admin', 'account'])) {


            if (in_array($current_user->branch_id, [5, 6])) {
                $EInvoiceMain = $EInvoiceMain->whereIn('branch_id', [5, 6]);
            } else {
                $EInvoiceMain = $EInvoiceMain->where('branch_id', $current_user->branch_id);
            }
        }

        $EInvoiceMain = $EInvoiceMain->get();


        return view('dashboard.e-invoice.index', [
            'TransferFeeMain' => $EInvoiceMain,
            'current_user' => $current_user,
            'Branchs' => $branchInfo['branch']
        ]);
    }



    public function einvoiceView($id)
    {
        $current_user = auth()->user();
        $EInvoiceMain = EInvoiceMain::where('id', '=', $id)->first();

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        // $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();

        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [2, 5])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id', [$current_user->branch_id, 6])->get();
            } else {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
            }
        } else  if (in_array($current_user->menuroles, ['lawyer'])) {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
        } else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }

        $Branchs = Branch::where('status', '=', 1)->get();
        $branchInfo = BranchController::manageBranchAccess();
        return view('dashboard.e-invoice.editv2', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'EInvoiceMain' => $EInvoiceMain,
            'Branchs' => $branchInfo['branch']
        ]);
    }

    public function einvoiceCreate()
    {
        $current_user = auth()->user();

        if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $Branchs = Branch::where('status', '=', 1)->get();
        $branchInfo = BranchController::manageBranchAccess();

        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [5, 6])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id', [5, 6])->get();
            } else {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
            }
        } else  if (in_array($current_user->menuroles, ['lawyer'])) {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
        } else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }


        return view('dashboard.e-invoice.create', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'Branchs' => $branchInfo['branch']
        ]);
    }

    public function getTransferList()
    {
        $current_user = auth()->user();

        $rows = DB::table('loan_case_bill_main as b')
            ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
            ->select('b.*', 'l.case_ref_no', 'c.name as client_name',)
            ->where('b.transferred_to_office_bank', '=',  0)
            ->where('b.status', '<>',  99);

        if (in_array($current_user->menuroles, ['maker'])) {
            if ($current_user->branch_id == 3) {
                $rows = $rows->where('l.branch_id', '=',  3);
            }
        }

        $rows = $rows->orderBy('b.id', 'ASC')->get();


        $billList = view('dashboard.transfer-fee.table.tbl-transfer-list', compact('rows', 'current_user'))->render();

        return [
            'status' => 1,
            'billList' => $billList,
        ];
    }

    public function getEInvoiceMainList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            // $TransferFeeMain = TransferFeeMain::where('status', '=', 1)->get();
            $branchInfo = BranchController::manageBranchAccess();


            $TransferFeeMain = DB::table('einvoice_main as m')
                ->leftJoin('users as u', 'u.id', '=', 'm.created_by')
                ->select('m.*')
                ->where('m.status', '<>',  99);

            // if ($request->input("transfer_date_from") <> null && $request->input("transfer_date_to") <> null) {
            //     $TransferFeeMain = $TransferFeeMain->whereBetween('m.transfer_date', [$request->input("transfer_date_from"), $request->input("transfer_date_to")]);
            // } else {
            //     if ($request->input("transfer_date_from") <> null) {
            //         $TransferFeeMain = $TransferFeeMain->where('m.transfer_date', '>=', $request->input("transfer_date_from"));
            //     }

            //     if ($request->input("transfer_date_to") <> null) {
            //         $TransferFeeMain = $TransferFeeMain->where('m.transfer_date', '<=', $request->input("transfer_date_to"));
            //     }
            // }

            if ($request->input("branch_id")) {
                $TransferFeeMain = $TransferFeeMain->where('m.branch_id', '=',  $request->input("branch_id"));
            }

            // if (in_array($current_user->menuroles, ['maker'])) {
            //     if (in_array($current_user->branch_id, [5, 6])) {
            //         $TransferFeeMain = $TransferFeeMain->whereIn('m.branch_id', [5, 6]);
            //     } else {
            //         $TransferFeeMain = $TransferFeeMain->where('m.branch_id', '=',  $current_user->branch_id);
            //     }
            // } else if (in_array($current_user->menuroles, ['sales'])) {
            //     if (in_array($current_user->id, [51, 32])) {
            //         $TransferFeeMain = $TransferFeeMain->whereIn('m.branch_id', [5, 6]);
            //     }
            // } else {
            //     if (in_array($current_user->id, [13])) {
            //         $TransferFeeMain = $TransferFeeMain->whereIn('m.branch_id', [$current_user->branch_id]);
            //     }
            // }

            
            if (in_array($current_user->menuroles, ['receptionist', 'account', 'sales', 'maker', 'lawyer'])) {

                $TransferFeeMain = $TransferFeeMain->where(function ($q) use ($current_user, $branchInfo) {
                    $q->whereIn('m.branch_id', $branchInfo['brancAccessList']);
                });
            }

            $TransferFeeMain = $TransferFeeMain->orderBy('m.created_at', 'DESC')->get();

            return DataTables::of($TransferFeeMain)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $actionBtn = '


<div class="btn-group  normal-edit-mode">
                    <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                    <i class="cil-settings"></i>
                    </button>
                    <div class="dropdown-menu" style="padding:0">
                      <a class="dropdown-item btn-info" target="_blank" href="/einvoice/' . $data->id . '" style="color:white;margin:0"><i style="margin-right: 10px;" class="cil-pencil"></i>Edit</a>
                     
                       <div class="dropdown-divider" style="margin:0"></div>
                        <a class="dropdown-item btn-danger" href="javascript:void(0)" onclick="deleteEinvoiceMainRecords(' . $data->id . ')" style="color:white;margin:0"><i style="margin-right: 10px;" class="cil-x"></i>Delete</a>
                        </div></div>

                    ';
                    return $actionBtn;
                })
                ->editColumn('is_recon', function ($data) {

                    if ($data->is_recon == '1')
                        return '<span class="label bg-success">Yes</span>';
                    else
                        return '<span class="label bg-warning">No</span>';
                })

                ->editColumn('client_profile_completed', function ($row) {
                    return $row->client_profile_completed == 1 ? '<span class="label label-success">Completed</span>' : '<span class="label label-warning">Pending</span>';
                })
                ->rawColumns(['action', 'case_ref_no', 'client_profile_completed'])
                ->make(true);
        }
    }

    public function getEInvoiceSentList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $accessInfo = AccessController::manageAccess();
            $branchInfo = BranchController::manageBranchAccess();
            $transfer_list = null;

            $rows = DB::table('loan_case_invoice_main as i')
                ->leftJoin('loan_case_bill_main as b', 'i.loan_case_main_bill_id', '=', 'b.id')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                // ->leftJoin('einvoice_details as t', 't.loan_case_main_bill_id', '=', 'b.id')
                ->leftJoin('einvoice_details as t', 't.loan_case_invoice_id', '=', 'i.id')
                ->leftJoin('invoice_billing_party as p', 'i.bill_party_id', '=', 'p.id')
                // ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                // ->select('b.*', 'l.case_ref_no', 'c.name as client_name', 't.transfer_amount', 't.sst_amount', 't.is_recon', 't.id as transfer_id')
                ->select('b.*','i.invoice_no as invoice_no_v2','i.id as invoice_id', 'i.bill_party_id','i.amount as invoice_amout', 'l.case_ref_no', 't.einvoice_status', 'p.completed as client_profile_completed', 'p.id as party_id')
                ->where('i.status', '<>',  99);


            if ($request->input('type') == 'add') {
                $transfer_list = json_decode($request->input('transfer_list'));
                $rows = $rows->whereIn('i.id', $transfer_list);
            } else if ($request->input('type') == 'transferred') {
                if ($request->input('transaction_id')) {
                    $rows = $rows->whereIn('b.id', EInvoiceDetails::where('status', '<>', 99)->where('einvoice_main_id', $request->input('transaction_id'))->pluck('loan_case_main_bill_id')->toArray())
                        ->where('t.einvoice_main_id', '=',  $request->input('transaction_id'));
                }
            } else if ($request->input('type') == 'sent') {
                if ($request->input('id')) {
                    $rows = $rows->whereIn('i.id', EInvoiceDetails::where('status', '<>', 99)->where('einvoice_main_id', $request->input('id'))->pluck('loan_case_invoice_id')->toArray());
                }
            } else {
                // $rows = $rows->whereNotIn('b.id', EInvoiceDetails::where('status', '<>', 99)->pluck('loan_case_main_bill_id')->toArray());
                // $rows = $rows->whereNotIn('i.id', EInvoiceDetails::where('status', '<>', 99)->pluck('einvoice_main_id')->toArray());
                    $rows = $rows->whereNotIn('i.id', EInvoiceDetails::where('status', '<>', 99)->pluck('loan_case_invoice_id')->toArray());
            }

            if ($request->input("start_date") <> null && $request->input("end_date") <> null) {
                $rows = $rows->whereBetween('b.invoice_date', [$request->input("start_date"), $request->input("end_date")]);
            } else {
                if ($request->input("date_from") <> null) {
                    $rows = $rows->where('b.invoice_date', '>=', $request->input("start_date"));
                }

                if ($request->input("date_to") <> null) {
                    $rows = $rows->where('b.invoice_date', '<=', $request->input("end_date"));
                }
            }

            if ($request->input('branch')) {
                $rows = $rows->where('b.invoice_branch_id', '=', $request->input("branch"));
            }

            // if (in_array($current_user->menuroles, ['maker'])) {
            //     if (in_array($current_user->branch_id, [5, 6])) {
            //         $rows = $rows->whereIn('b.invoice_branch_id', [5, 6]);
            //     } else if (in_array($current_user->branch_id, [2])) {
            //         $rows = $rows->whereIn('l.sales_user_id', [13]);
            //     } else {
            //         $rows = $rows->whereIn('b.invoice_branch_id', $accessInfo['brancAccessList']);
            //     }
            // } else if (in_array($current_user->menuroles, ['sales'])) {
            //     if (in_array($current_user->id, [32, 51])) {
            //         $rows = $rows->whereIn('b.invoice_branch_id', [5, 6]);
            //     }
            // } else if (in_array($current_user->menuroles, ['lawyer'])) {
            //     if (in_array($current_user->id, [13])) {
            //         $rows = $rows->whereIn('b.invoice_branch_id', [2]);
            //     }
            // }

            if (in_array($current_user->menuroles, ['receptionist', 'account', 'sales', 'maker', 'lawyer'])) {

                $rows = $rows->where(function ($q) use ($current_user, $branchInfo) {
                    $q->whereIn('b.invoice_branch_id', $branchInfo['brancAccessList']);
                });
            }

            $rows = $rows->orderBy('b.invoice_no', 'ASC')->get();

            return DataTables::of($rows)
                ->addIndexColumn()
                ->addColumn('action', function ($data) use ($request, $transfer_list) {
                    if ($request->input('type') == 'transferred') {

                        $is_disabled = '';

                        // if ($data->is_recon == 1) {
                        //     $is_disabled = 'disabled';
                        // }
                        // $is_disabled = 'disabled';

                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="checkbox" name="trans_bill" value="' . $data->invoice_id . '" id="trans_chk_' . $data->invoice_id . '" ' . $is_disabled . ' >
                        <label for="trans_chk_' . $data->invoice_id . '"></label>
                        </div> ';
                    } elseif ($request->input('type') == 'add') {

                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="checkbox" name="add_bill" value="' . $data->invoice_id . '" id="chk_' . $data->invoice_id . '"  >
                        <label for="chk_' . $data->invoice_id . '"></label>
                        </div> ';
                    } elseif ($request->input('type') == 'sent') {

                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="checkbox" name="add_bill" class="invoice_all" value="' . $data->invoice_id . '" id="chk_' . $data->invoice_id . '" data-status="' . $data->einvoice_status . '" >
                        <label for="chk_' . $data->invoice_id . '"></label>
                        </div> ';
                    } elseif ($request->input('type') == 'not_transfer') {

                        $is_checked = '';

                        echo ($transfer_list);

                        if ($transfer_list != null) {

                            if (in_array($data->invoice_id, $transfer_list)) {
                                $is_checked = 'checked';
                            }
                        }

                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="hidden"  value="' . $data->id . '" id="bill_id_' . $data->invoice_id . '"  >
                        <input type="hidden"  value="' . $data->case_id . '" id="case_id_' . $data->invoice_id . '"  >
                        <input type="checkbox" name="bill" value="' . $data->invoice_id . '" id="chk_' . $data->invoice_id . '"  ' . $is_checked . '>
                        <label for="chk_' . $data->invoice_id . '">' . $transfer_list . '</label>
                        </div> ';
                    } else {
                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="checkbox" name="bill" value="' . $data->invoice_id . '" id="chk_' . $data->invoice_id . '" >
                        <label for="chk_' . $data->invoice_id . '"></label>
                        </div> ';
                    }

                    return $actionBtn;
                })
                ->editColumn('case_ref_no', function ($data) {
                    return '<b>Ref No: </b><a href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a> ';
                })
                ->editColumn('einvoice_status', function ($data) {
                    if ($data->einvoice_status == 'SQL') {
                        return 'Sent to SQL Server';
                    } else if ($data->einvoice_status == 'EXCEL') {
                        return 'Excel template generated';
                    } else if ($data->einvoice_status == 'LHDN') {
                        return 'Submmited to LHDN';
                    } else {
                        return 'Pending';
                    }
                })

                ->editColumn('client_profile_completed', function ($data) {

                    if ($data->client_profile_completed == 1)
                    {
                        return '<span class="label label-success">Completed</span>' ;
                    }
                    else
                    {
                        if($data->bill_party_id != null)
                        {
                            return '<span class="label label-warning">
                                    <a  href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                data-keyboard="false" data-toggle="modal" data-target="#modalAddBilltoInfo"
                                onclick="loadInvBilltoDetails(' . $data->bill_party_id . ');" style="color:white;margin:0"><i
                                    style="margin-right: 10px;" class="cil-calendar"></i>Pending update</a>
                                    </span>';
                        }
                        else{
                                //   return '<span class="label label-warning">
                                //     <a  href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                // data-keyboard="false" data-toggle="modal" data-target="#modalAddBillto"
                                // onclick="loadInvBilltoDetails(' . $data->id . ');" style="color:white;margin:0"><i
                                //     style="margin-right: 10px;" class="cil-calendar"></i>Add</a>
                                //     </span>';

                                
                        return '<span class="label label-warning">No Recipient</span>' ;
                        }
                        
                    }

  


                //     return $data->client_profile_completed == 1 ? '<span class="label label-success">Completed</span>' : 
                //     '<span class="label label-warning">
                //     <a  href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                //  data-keyboard="false" data-toggle="modal" data-target="#modalAddBilltoInfo"
                //  onclick="loadInvBilltoDetails(' . $data->party_id . ');" style="color:white;margin:0"><i
                //      style="margin-right: 10px;" class="cil-calendar"></i>Pending Update</a>
                //     </span>';
                })
                ->addColumn('pfee_sum', function ($data) {
                    $pfee_sum = $data->pfee1_inv + $data->pfee2_inv;
                    return $pfee_sum;
                })
                ->editColumn('invoice_date', function ($data) {
                    if ($data->invoice_date) {
                        $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->invoice_date)->format('d-m-Y');
                        return $formatedDate;
                    } else {
                        return $data->invoice_date;
                    }
                })
                ->rawColumns(['action',  'case_ref_no', 'pfee_sum', 'sst_to_transfer', 'invoice_date', 'client_profile_completed'])
                ->make(true);
        }
    }



    public function AddInvoiceIntoEInvoice(Request $request, $id)
    {
        $current_user = auth()->user();

        if ($request->input('add_invoice') != null) {
            $add_bill = json_decode($request->input('add_invoice'), true);
        }

        if (count($add_bill) > 0) {
            for ($i = 0; $i < count($add_bill); $i++) {
                $EInvoiceDetails  = new EInvoiceDetails();

                $EInvoiceDetails->einvoice_main_id = $id;

                $EInvoiceDetails->loan_case_main_bill_id = $add_bill[$i]['bill_id']; // Use 'id' from the bill item
                $EInvoiceDetails->loan_case_invoice_id = $add_bill[$i]['id']; // Use 'id' from the bill item
                $EInvoiceDetails->case_id = $add_bill[$i]['case_id']; // Use 'id' from the bill item

                $EInvoiceDetails->created_by = $current_user->id;
                $EInvoiceDetails->amt = 0;
                $EInvoiceDetails->einvoice_status = 'SENT';

                $EInvoiceDetails->status = 1;
                $EInvoiceDetails->created_at = date('Y-m-d H:i:s');

                $EInvoiceDetails->save();

                LoanCaseBillMain::where('id', '=', $add_bill[$i]['id'])->update(['sql_submit' => 1]);
            }
        }

        return response()->json(['status' => 1]);
    }

    public function DeleteInvoiceFromEInvoice(Request $request, $id)
    {
        $current_user = auth()->user();

        if ($request->input('delete_invoice') != null) {
            $delete_invoice = json_decode($request->input('delete_invoice'), true);
        }

        if (count($delete_invoice) > 0) {
            for ($i = 0; $i < count($delete_invoice); $i++) {
                EInvoiceDetails::where('loan_case_invoice_id', $delete_invoice[$i]['id'])->where('einvoice_main_id', $id)->delete();
                LoanCaseBillMain::where('id', '=', $delete_invoice[$i]['id'])->update(['sql_submit' => 0]);
            }

            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = 0;
            $AccountLog->bill_id = 0;
            $AccountLog->ori_amt = 0;
            $AccountLog->new_amt = 0;
            $AccountLog->action = 'delete_einvoice';
            $AccountLog->desc = $current_user->name . ' remove invoice id (' . $request->input('delete_invoice') . ') from E-Invoice Records';
            $AccountLog->status = 1;
            $AccountLog->object_id = $id;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();
        }

        return response()->json(['status' => 1]);
    }

    public function deleteEinvoiceMainRecords(Request $request, $id)
    {
        $einvoiceMain = EInvoiceMain::Where('id', $id)->first();
        $transaction_id = $einvoiceMain->transaction_id;

        if (!$einvoiceMain) {
            return response()->json(['status' => 0, 'message' => 'E-Invoice record not found.']);
        }

        // Check if any associated invoices have been sent
        $detailsSent = EInvoiceDetails::where('einvoice_main_id', $id)
            ->whereIn('einvoice_status', ['LHDN', 'SQL'])
            ->exists();

        if ($detailsSent) {
            return response()->json(['status' => 0, 'message' => 'Cannot delete this record as it contains invoices already sent to LHDN or SQL.']);
        }

        // Get related bill IDs to update their status after deletion
        $billIds = EInvoiceDetails::where('einvoice_main_id', $id)->pluck('loan_case_main_bill_id');

        // Delete associated details
        EInvoiceDetails::where('einvoice_main_id', $id)->delete();

        // Delete the main record
        $einvoiceMain->delete();

        // Reset the submission flag on the related bills
        if ($billIds->isNotEmpty()) {
            LoanCaseBillMain::whereIn('id', $billIds)->update(['sql_submit' => 0]);
        }

        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = 0;
        $AccountLog->bill_id = 0;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->action = 'DeleteEInvoiceMainRecord';
        $AccountLog->desc = $current_user->name . ' Deleted Einvoice Main record ('. $transaction_id. ')';
        $AccountLog->object_id = $id;
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        return response()->json(['status' => 1, 'message' => 'E-Invoice record deleted successfully.']);
    }

    public function AddBilltoInvoice(Request $request, $bill_id)
    {
        $current_user = auth()->user();
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $bill_id)->first();

        // Check if a specific billing_party_id is provided (from existing party selection)
        if ($request->has('billing_party_id') && $request->input('billing_party_id')) {
            $InvoiceBillingParty = InvoiceBillingParty::find($request->input('billing_party_id'));
            
            if (!$InvoiceBillingParty) {
                return response()->json(['status' => 0, 'message' => 'Billing party not found']);
            }
            
            // Check if this billing party is already associated with this bill
            // For split invoices, allow the same billing party to be associated with multiple invoices
            if ($InvoiceBillingParty->loan_case_main_bill_id == $bill_id) {
                // Only prevent if trying to add to the same specific invoice
                if($request->input('invoice_id') != "undefined") {
                    $existingInvoice = LoanCaseInvoiceMain::where('id', $request->input('invoice_id'))
                        ->where('bill_party_id', $InvoiceBillingParty->id)
                        ->first();
                    
                    if ($existingInvoice) {
                        return response()->json(['status' => 2, 'message' => 'This party is already associated with this specific invoice']);
                    }
                }
                // If not adding to a specific invoice, or if it's a different invoice, allow it
            }
            
            // Associate the existing billing party with this bill
            $InvoiceBillingParty->loan_case_main_bill_id = $bill_id;
            $InvoiceBillingParty->save();
        } else {
            // Legacy logic for creating new billing parties (when no specific ID provided)
            // First check if a billing party is already associated with this specific bill_id
            $existingBillingParty = InvoiceBillingParty::where('loan_case_main_bill_id', $bill_id)
                ->where('customer_name', $request->input('bill_to'))
                ->first();

            if ($existingBillingParty) {
                // Billing party already exists for this bill, just use it
                $InvoiceBillingParty = $existingBillingParty;
            } else {
                // Check if there's an existing billing party with the same customer name that can be reused
                $InvoiceBillingParty = InvoiceBillingParty::where('case_id', $request->input('case_id'))
                    ->where('customer_name', $request->input('bill_to'))
                    ->whereNull('loan_case_main_bill_id') // Not already associated with another bill
                    ->first();

                if (!$InvoiceBillingParty) {
                    // No existing party found, create a new one
                    $Parameter = Parameter::where('parameter_type', '=', 'sql_client_code_running_no')->first();
                    $SQL_prefix = Parameter::where('parameter_type', '=', 'sql_client_code_prefix')->value('parameter_value_1');
                    $nextRunningNo = str_pad((int)$Parameter->parameter_value_1, 5, '0', STR_PAD_LEFT);
                    $customer_code = $SQL_prefix . '-' . $nextRunningNo;

                    $InvoiceBillingParty = new InvoiceBillingParty();
                    $InvoiceBillingParty->customer_code = $customer_code;
                    $InvoiceBillingParty->case_id = $request->input('case_id');
                    $InvoiceBillingParty->customer_name = $request->input('bill_to');
                    $InvoiceBillingParty->status = 1;
                    $InvoiceBillingParty->created_at = date('Y-m-d H:i:s');
                    $InvoiceBillingParty->created_by = $current_user->id;
                    $InvoiceBillingParty->save();

                    $Parameter->parameter_value_1 = (int)$Parameter->parameter_value_1 + 1;
                    $Parameter->save();
                }

                // Associate the billing party with this bill
                $InvoiceBillingParty->loan_case_main_bill_id = $bill_id;
                $InvoiceBillingParty->save();
            }
        }

        // Check if this specific billing party is already associated with this specific invoice
        if($request->input('invoice_id') != "undefined") {
            $existingInvoice = LoanCaseInvoiceMain::where('id', $request->input('invoice_id'))
                ->where('bill_party_id', $InvoiceBillingParty->id)
                ->first();
            
            if ($existingInvoice) {
                return response()->json(['status' => 2, 'message' => 'This party is already associated with this specific invoice']);
            }
        }

        if($request->input('invoice_id') != "undefined")
        {
            $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', $request->input('invoice_id'))->first();
                
            $LoanCaseInvoiceMain->bill_party_id = $InvoiceBillingParty->id;
            $LoanCaseInvoiceMain->save();

            $InvoiceBillingParty->invoice_main_id = $request->input('invoice_id');
            $InvoiceBillingParty->save();

            $this->updateInvoiceDetailsAmt($LoanCaseInvoiceMain->id, $LoanCaseBillMain->id, $LoanCaseBillMain->case_id);

        }
        else
        {
            $InvoiceBillingParty->invoice_main_id = 0;
            $InvoiceBillingParty->save();
        }

        
        // $InvoiceBillingParty = InvoiceBillingParty::where('loan_case_main_bill_id', $bill_id)->get();

        $InvoiceBillingParty = DB::table('invoice_billing_party as bp')
            ->leftJoin('loan_case_invoice_main as im', 'im.id', '=', 'bp.invoice_main_id')
            ->select('bp.*', 'im.invoice_no', 'im.bill_party_id', 'im.id as invoice_id')
            ->where('bp.loan_case_main_bill_id', $bill_id)
            ->get();

        // $billList = view('dashboard.case.section.d-invoice-billto', compact('InvoiceBillingParty'))->render();

        // Recalculate bill totals after adding invoice recipient
        $caseController = new \App\Http\Controllers\CaseController();
        $caseController->updatePfeeDisbAmountINV($bill_id);

        return response()->json([
            'status' => 1, 
            'message' => 'Added party into list',
            'bill_id' => $bill_id
        ]);
    }


    public function loadBillToInv($id)
    {
        \Log::info('loadBillToInv called with ID: ' . $id);
        
        $InvoiceBillingParty = InvoiceBillingParty::where('id', $id)->first();
        
        if (!$InvoiceBillingParty) {
            \Log::error('InvoiceBillingParty not found for ID: ' . $id);
            return response()->json([
                'status' => 0,
                'message' => 'Billing party not found',
                'data' => null
            ]);
        }
        
        \Log::info('InvoiceBillingParty found:', $InvoiceBillingParty->toArray());

        $invoice_no = LoanCaseInvoiceMain::where('id', $InvoiceBillingParty->invoice_main_id)->pluck('invoice_no');

        $response = [
            'status' => 1,
            'data' => $InvoiceBillingParty,
            'inv_no' => $invoice_no,
            'view' => view('dashboard.case.section.d-party-infov2', compact('InvoiceBillingParty'))->render(),
        ];
        
        \Log::info('loadBillToInv response:', $response);

        return response()->json($response);
    }

    public function loadBillToInvWIthInvoice($id)
    {
        // $InvoiceBillingParty = InvoiceBillingParty::where('id', $id)->first();
        $invoiceMain = LoanCaseInvoiceMain::where('id', $id)->first();
        $invoice_no = $invoiceMain->invoice_no;
        $InvoiceBillingParty = InvoiceBillingParty::where('id', $invoiceMain->bill_party_id)->first();

        // Get required variables for invoicePrint view (referenced from CaseController loadCaseBill)
        $current_user = auth()->user();
        
        // Get LoanCaseBillMain
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $invoiceMain->loan_case_main_bill_id)->first();
        
        // Get case information
        $case = LoanCase::where('id', '=', $LoanCaseBillMain->case_id)->first();
        
        // Get Branch information
        $Branch = Branch::where('id', '=', $case->branch_id)->first();
        
        // Get Bank Ref No (purchaser_financier_ref_no) from masterlist field 160
        $purchaser_financier_ref_no = DB::table('loan_case_masterlist as m')
            ->select('m.value')
            ->where('m.case_id', '=', $case->id)
            ->where('m.masterlist_field_id', '=', 160)
            ->first();
        
        // Get account categories
        $category = AccountCategory::where('status', '=', 1)->orderBy('order', 'asc')->get();
        
        // Build invoice_v2 array (similar to CaseController logic)
        $invoice_v2 = array();
        
        for ($i = 0; $i < count($category); $i++) {
            array_push($invoice_v2, array('row' => 'title', 'category' => $category[$i], 'account_details' => []));
            
            $QuotationTemplateDetails = DB::table('loan_case_invoice_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.name_cn as account_name_cn', 'a.formula as account_formula', 'a.min as account_min', 'a.id as account_item_id', 'a.pfee1_item', 'a.remark as item_desc', 'qd.remark as item_remark')
                ->where('qd.invoice_main_id', '=', $id)
                ->where('qd.status', '=', 1)
                ->where('a.account_cat_id', '=', $category[$i]->id)
                ->get();
            
            for ($j = 0; $j < count($QuotationTemplateDetails); $j++) {
                array_push($invoice_v2, array('row' => 'item', 'category' => $category[$i], 'account_details' => $QuotationTemplateDetails[$j]));
            }
            
            array_push($invoice_v2, array('row' => 'subtotal', 'category' => $category[$i], 'account_details' => []));
        }
        
        // Create pieces_inv array (chunked for pagination)
        $pieces_inv = array_chunk($invoice_v2, 30);
        
        // Load master list data for print (similar to CaseController loadMasterListUpdateValue)
        $masterlistValue = $this->loadMasterListUpdateValue($case->id);
        
        return response()->json([
            'status' => 1,
            'data' => $InvoiceBillingParty,
            'invoicePrint' => view('dashboard.case.d-invoice-print', compact('LoanCaseBillMain', 'current_user', 'case', 'Branch', 'invoice_v2', 'pieces_inv', 'InvoiceBillingParty', 'invoiceMain', 'purchaser_financier_ref_no', 'masterlistValue'))->render(),
            'inv_no' => $invoice_no,
            'view' => view('dashboard.case.section.d-party-infov2', compact('InvoiceBillingParty'))->render(),
        ]);
    }
    
    /**
     * Load master list update value for print (similar to CaseController::loadMasterListUpdateValue)
     */
    private function loadMasterListUpdateValue($caseId)
    {
        $CaseMasterListMainCat = CaseMasterListMainCat::where('letter_head', 1)->orderBy('order', 'asc')->get();

        for ($i = 0; $i < count($CaseMasterListMainCat); $i++) {
            $master_list = DB::table('loan_case_masterlist as m')
                ->leftJoin('case_masterlist_field AS f', 'f.id', '=', 'm.masterlist_field_id')
                ->leftJoin('case_masterlist_field_category AS c', 'c.id', '=', 'f.case_field_id')
                ->select('m.*')
                ->where('m.case_id', '=', $caseId)
                ->where('f.letter_head', 1)
                ->where('f.master_list_code', $CaseMasterListMainCat[$i]->code)
                ->get();

            $CaseMasterListMainCat[$i]->details = $master_list;
        }

        return [
            'CaseMasterListMainCat' => $CaseMasterListMainCat,
        ];
    }

    public function UpdateBillToInfo(Request $request, $id)
    {
        try {
            $billingParty = InvoiceBillingParty::find($id);

            if (!$billingParty) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Billing party not found'
                ], 404);
            }

            // Update all fields
            $billingParty->customer_name = $request->input('customer_name');
            $billingParty->customer_code = $request->input('customer_code');
            $billingParty->customer_category = $request->input('customer_category');
            $billingParty->tin = $request->input('tin');
            $billingParty->brn = $request->input('brn');
            $billingParty->brn2 = $request->input('brn2');
            $billingParty->sales_tax_no = $request->input('sales_tax_no');
            $billingParty->service_tax_no = $request->input('service_tax_no');
            $billingParty->id_type = $request->input('id_type');
            $billingParty->id_no = $request->input('id_no');
            $billingParty->address_1 = $request->input('address_1');
            $billingParty->address_2 = $request->input('address_2');
            $billingParty->address_3 = $request->input('address_3');
            $billingParty->address_4 = $request->input('address_4');
            $billingParty->postcode = $request->input('postcode');
            $billingParty->city = $request->input('city');
            $billingParty->state = $request->input('state');
            $billingParty->country = $request->input('country');
            $billingParty->phone1 = $request->input('phone1');
            $billingParty->mobile = $request->input('mobile');
            $billingParty->fax1 = $request->input('fax1');
            $billingParty->fax2 = $request->input('fax2');
            $billingParty->email = $request->input('email');

            // Check mandatory fields
            $mandatoryFields = self::getEinvoiceCustomerMandatoryField();
            $allMandatoryFieldsFilled = true;

            foreach ($mandatoryFields as $field) {
                if (empty($billingParty->$field)) {
                    $allMandatoryFieldsFilled = false;
                    break;
                }
            }

            // Update completed status of the current billing party
            $billingParty->completed = $allMandatoryFieldsFilled ? 1 : 0;
            $billingParty->save();

            // Now check all billing parties related to the same loan_case_main_bill_id
            $relatedBillingParties = InvoiceBillingParty::where('loan_case_main_bill_id', $billingParty->loan_case_main_bill_id)->get();

            $allRelatedCompleted = true;
            foreach ($relatedBillingParties as $relatedParty) {
                if ($relatedParty->completed == 0) {
                    $allRelatedCompleted = false;
                    break;
                }
            }

            // Find the associated LoanCaseBillMain record
            $loanCaseBillMain = LoanCaseBillMain::find($billingParty->loan_case_main_bill_id);

            // If LoanCaseBillMain and associated EInvoiceMain exist, update client_profile_completed
            if ($loanCaseBillMain && $loanCaseBillMain->id) {
                $EInvoiceDetails = EInvoiceDetails::where('loan_case_main_bill_id', $billingParty->loan_case_main_bill_id)->first();
                if ($EInvoiceDetails) {
                    $EInvoiceDetails->client_profile_completed = $allRelatedCompleted ? 1 : 0;
                    $EInvoiceDetails->save();


                    $EInvoiceDetailsAll = EInvoiceDetails::where('einvoice_main_id', $EInvoiceDetails->einvoice_main_id)->get();

                    $allRelatedCompleted = true;
                    foreach ($EInvoiceDetailsAll as $relatedParty) {
                        if ($relatedParty->client_profile_completed == 0) {
                            $allRelatedCompleted = false;
                            break;
                        }
                    }

                    EInvoiceMain::where('id', $EInvoiceDetails->einvoice_main_id)->update(['client_profile_completed' => $allRelatedCompleted]);
                }
            }





            $InvoiceBillingParty = InvoiceBillingParty::where('loan_case_main_bill_id', $billingParty->loan_case_main_bill_id)->get();
            $billList = view('dashboard.case.section.d-invoice-billto', compact('InvoiceBillingParty'))->render();

            return response()->json([
                'status' => 1,
                'message' => 'Billing party information updated successfully',
                'view' => $billList,
                'bill_id' => $billingParty->loan_case_main_bill_id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error updating billing party information: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeBillto($id)
    {
        
        $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $id)->first();
        $billingParty = InvoiceBillingParty::find($LoanCaseInvoiceMain->bill_party_id);


        if (!$billingParty) {
            return response()->json(['status' => 0, 'message' => 'Billing party not found'], 404);
        };

        // Check if completed or sent_to_sql status is 1
        if ($billingParty->completed == 1 || $billingParty->sent_to_sql == 1) {
            return response()->json(['status' => 0, 'message' => 'Cannot remove a completed or sent record.']);
        }

        $invoice_main_id = $billingParty->invoice_main_id;
        $bill_main_id = $billingParty->loan_case_main_bill_id;
        $case_id = $billingParty->case_id;

        

        // return $invoice_main_id . '-' . $bill_main_id . '-' . $case_id;

        // If not completed or sent, delete the record
        // $billingParty->delete();
        

         InvoiceBillingParty::where('invoice_main_id', $invoice_main_id)->update(['loan_case_main_bill_id' => 0, 'invoice_main_id' => 0]); 


        $InvoiceBillingParty = InvoiceBillingParty::where('loan_case_main_bill_id', $bill_main_id)->get();


        $current_user = auth()->user();
        $loanCaseInvoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $invoice_main_id)->get();

        // $party_count = InvoiceBillingParty::where('loan_case_main_bill_id', $bill_main_id)
        //     ->where('case_id', $case_id)
        //     ->count();

          $party_count = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)
            ->count();

        
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $bill_main_id)->first();
        $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $invoice_main_id)->first();

        
        // $this->updateInvoiceDetailsAmt($LoanCaseInvoiceMain->id, $LoanCaseBillMain->id, $LoanCaseBillMain->case_id);

        // if ($LoanCaseBillMain->invoice_no != $LoanCaseInvoiceMain->invoice_no)
        // {
        //     LoanCaseInvoiceDetails::where('invoice_main_id', $invoice_main_id)->delete();
        //     LoanCaseInvoiceMain::where('id', $invoice_main_id)->delete();
        // }

        if($party_count == 0)
        {
            $party_count = 1;
        }


        
        // LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $bill_main_id)->update(['amount' => DB::raw('ori_invoice_amt /' . $party_count)]);

        // $total_amt = LoanCaseBillDetails::where("loan_case_main_bill_id", $bill_main_id)->sum("amount");
        // $total_sst = LoanCaseBillDetails::where("loan_case_main_bill_id", $bill_main_id)->sum("sst");

        // $total_amt = ($total_amt + $total_sst)/$party_count;

        // LoanCaseInvoiceMain::where("loan_case_main_bill_id", $bill_main_id)->update(["amount" => $total_amt]);
        LoanCaseInvoiceMain::where("id", $id)->update(["bill_party_id" => 0]);

        

        $billList = view('dashboard.case.section.d-invoice-billto', compact('InvoiceBillingParty'))->render();

        return response()->json(['status' => 1, 'message' => 'Billing party removed successfully', 'view' => $billList]);
    }

    public function removeInvoice($id)
    {
        $delete_invoice_id = '';
        $loan_case_main_bill_id = '';
        $case_id = '';

        $current_user = auth()->user();
        $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $id)->first();

        if (!$LoanCaseInvoiceMain) {
            return response()->json(['status' => 0, 'message' => 'Invoice not found'], 404);
        };

        
        $delete_invoice_id = $LoanCaseInvoiceMain->id;
        $loan_case_main_bill_id = $LoanCaseInvoiceMain->loan_case_main_bill_id;
        $case_id = LoanCaseBillMain::where('id', $loan_case_main_bill_id)->pluck('case_id');

        // Check if this invoice is already in transfer fee records
        $transferFeeDetails = \App\Models\TransferFeeDetails::where('loan_case_invoice_main_id', $id)->get();
        
        if ($transferFeeDetails->isNotEmpty()) {
            // Get transfer fee main information for better error message
            $transferFeeMainIds = $transferFeeDetails->pluck('transfer_fee_main_id')->unique();
            $transferFeeMains = \App\Models\TransferFeeMain::whereIn('id', $transferFeeMainIds)->get();
            
            $transferIds = $transferFeeMains->pluck('transaction_id')->implode(', ');
            
            return response()->json([
                'status' => 0, 
                'message' => 'Cannot remove invoice. This invoice is already included in transfer fee record(s): ' . $transferIds . '. Please remove it from transfer fee first.'
            ], 400);
        }

        InvoiceBillingParty::where('invoice_main_id', $id)->update(['invoice_main_id' => 0]); 
        $LoanCaseInvoiceMain->delete();

        LoanCaseInvoiceDetails::where('invoice_main_id', '=', $id)->delete();
        
        // After removing an invoice, we need to redistribute the details among remaining invoices
        $remainingInvoices = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $loan_case_main_bill_id)->get();
        $remainingCount = $remainingInvoices->count();
        
        if ($remainingCount > 0) {
            // Get all details for this bill and redistribute them
            $allDetails = LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $loan_case_main_bill_id)
                ->where('status', '<>', 99)
                ->get();
            
            // Group details by account_item_id to redistribute
            $detailsByItem = $allDetails->groupBy('account_item_id');
            
            foreach ($detailsByItem as $accountItemId => $details) {
                // Get the original total amount for this account item
                $originalTotalAmount = $details->first()->ori_invoice_amt;
                $newAmount = $originalTotalAmount / $remainingCount;
                
                // Update all details for this account item with the new divided amount
                foreach ($details as $detail) {
                    $detail->amount = $newAmount;
                    $detail->save();
                }
            }
            
            // Now recalculate all invoice amounts
            $this->updateInvoiceDetailsAmt($remainingInvoices->first()->id, $loan_case_main_bill_id, $case_id);
        }
        
        $this->generateNewInvNo($loan_case_main_bill_id, $delete_invoice_id, true);
        
        // Recalculate bill totals after removing invoice
        $caseController = new \App\Http\Controllers\CaseController();
        $caseController->updatePfeeDisbAmountINV($loan_case_main_bill_id);

        return response()->json([
            'status' => 1, 
            'message' => 'Invoice removed successfully',
            'bill_id' => $loan_case_main_bill_id
        ]);
    }



    /**
     * Update transfer fee main amount by recalculating from details
     */
    private function updateTransferFeeMainAmt($transferFeeMainId)
    {
        $SumTransferFee = 0;
        $TransferFeeMain = \App\Models\TransferFeeMain::where('id', '=', $transferFeeMainId)->first();
        $TransferFeeDetailsSum = \App\Models\TransferFeeDetails::where('transfer_fee_main_id', '=', $transferFeeMainId)->get();

        if (count($TransferFeeDetailsSum) > 0) {
            for ($j = 0; $j < count($TransferFeeDetailsSum); $j++) {
                // Include all amount components: transfer_amount + sst_amount + reimbursement_amount + reimbursement_sst_amount
                $SumTransferFee += $TransferFeeDetailsSum[$j]->transfer_amount;
                $SumTransferFee += $TransferFeeDetailsSum[$j]->sst_amount ?? 0;
                $SumTransferFee += $TransferFeeDetailsSum[$j]->reimbursement_amount ?? 0;
                $SumTransferFee += $TransferFeeDetailsSum[$j]->reimbursement_sst_amount ?? 0;
            }
        }
        $TransferFeeMain->transfer_amount = $SumTransferFee;
        $TransferFeeMain->save();
    }

    /**
     * Distribute amount with proper rounding to avoid total discrepancies
     * 
     * @param float $totalAmount The total amount to distribute
     * @param int $partyCount Number of parties to split between
     * @param int $currentIndex Current invoice index (0-based)
     * @return float The distributed amount for the current index
     */
    public static function distributeAmount($totalAmount, $partyCount, $currentIndex = 0)
    {
        $baseAmount = $totalAmount / $partyCount;
        $distributedAmounts = [];
        $totalDistributed = 0;
        
        // Distribute amounts with proper rounding
        for ($i = 0; $i < $partyCount - 1; $i++) {
            $amount = round($baseAmount, 2);
            $distributedAmounts[] = $amount;
            $totalDistributed += $amount;
        }
        
        // Last amount ensures total matches original
        $distributedAmounts[] = round($totalAmount - $totalDistributed, 2);
        
        return $distributedAmounts[$currentIndex];
    }

     public function splitInvoice(Request $request, $bill_main_id)
    {
        $current_user = auth()->user();
        
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $bill_main_id)->first();
        
        if (!$LoanCaseBillMain) {
            return response()->json(['status' => 0, 'message' => 'Bill not found.']);
        }

        $case = LoanCase::where('id', '=', $LoanCaseBillMain->case_id)->first();
        
        if (!$case) {
            return response()->json(['status' => 0, 'message' => 'Case not found.']);
        }

        // Get existing invoice for this bill
        $existingInvoice = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)->first();
        
        if (!$existingInvoice) {
            return response()->json(['status' => 0, 'message' => 'No invoice found for this bill.']);
        }

        // Skip party creation - will set bill_party_id to 0

        $loanCaseInvoiceMain = new LoanCaseInvoiceMain();

        $loanCaseInvoiceMain->loan_case_main_bill_id = $bill_main_id;
        $loanCaseInvoiceMain->invoice_no = '';
        $loanCaseInvoiceMain->Invoice_date = $LoanCaseBillMain->Invoice_date;
        $loanCaseInvoiceMain->amount = $LoanCaseBillMain->total_amt_inv;
        $loanCaseInvoiceMain->pfee1_inv = $LoanCaseBillMain->pfee1_inv;
        $loanCaseInvoiceMain->pfee2_inv = $LoanCaseBillMain->pfee2_inv;
        $loanCaseInvoiceMain->sst_inv = $LoanCaseBillMain->sst;
        $loanCaseInvoiceMain->reimbursement_amount = $LoanCaseBillMain->reimbursement_amount ?? 0;
        $loanCaseInvoiceMain->reimbursement_sst = $LoanCaseBillMain->reimbursement_sst ?? 0;
        $loanCaseInvoiceMain->bill_party_id = 0; // Set to 0 since we're not creating a party
        $loanCaseInvoiceMain->remark = "";
        $loanCaseInvoiceMain->created_by = $current_user->id;
        $loanCaseInvoiceMain->status = 1;
        $loanCaseInvoiceMain->created_at = date('Y-m-d H:i:s');

        $loanCaseInvoiceMain->save();

        $invoice_main_id_new = $loanCaseInvoiceMain->id;

        $this->generateNewInvNo($bill_main_id, $invoice_main_id_new, false);

        $invoice_main_id = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)->pluck('id')->first();


        



        $loanCaseInvoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $invoice_main_id)->get();

        // $party_count = InvoiceBillingParty::where('loan_case_main_bill_id', $bill_main_id)
        //     ->where('case_id', $case_id)
        //     ->where('invoice_main_id','!=', 0)
        //     ->count();

        $party_count = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)
            ->count();

        $sum = 0;

        if (count($loanCaseInvoiceDetails) > 0) {
            // Use the same redistribution logic as removeInvoice
            $allDetails = LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $bill_main_id)
                ->where('status', '<>', 99)
                ->get();
            
            // Get all invoices for this bill to know their indices for proper distribution
            $allInvoices = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)
                ->where('status', '<>', 99)
                ->orderBy('id')
                ->get();
            
            // Group details by account_item_id to redistribute
            $detailsByItem = $allDetails->groupBy('account_item_id');
            
            foreach ($detailsByItem as $accountItemId => $details) {
                // Get the original total amount for this account item
                $originalTotalAmount = $details->first()->ori_invoice_amt;
                
                // Distribute amounts properly - each invoice gets its correct share
                foreach ($allInvoices as $invoiceIndex => $invoice) {
                    // Get the distributed amount for this invoice index
                    $distributedAmount = $this->distributeAmount($originalTotalAmount, $party_count, $invoiceIndex);
                    
                    // Update details for this specific invoice
                    foreach ($details as $detail) {
                        if ($detail->invoice_main_id == $invoice->id) {
                            $detail->amount = $distributedAmount;
                            $detail->save();
                        }
                    }
                    
                    // Create new detail for the new invoice if it doesn't exist
                    if ($invoice_main_id_new != "" && $invoice->id == $invoice_main_id_new) {
                        \Log::info("Creating detail for new invoice {$invoice_main_id_new}, account_item_id: {$accountItemId}");
                        
                        $existingNewDetail = LoanCaseInvoiceDetails::where('invoice_main_id', $invoice_main_id_new)
                            ->where('account_item_id', $accountItemId)
                            ->first();
                        
                        if (!$existingNewDetail) {
                            $originalDetail = $details->first();
                            $LoanCaseInvoiceDetailsNew = new LoanCaseInvoiceDetails();
                            $LoanCaseInvoiceDetailsNew->loan_case_main_bill_id = $bill_main_id;
                            $LoanCaseInvoiceDetailsNew->account_item_id = $originalDetail->account_item_id;
                            $LoanCaseInvoiceDetailsNew->quotation_item_id = $originalDetail->quotation_item_id;
                            $LoanCaseInvoiceDetailsNew->invoice_main_id = $invoice_main_id_new;
                            $LoanCaseInvoiceDetailsNew->amount = $distributedAmount; // Use the correct distributed amount for this invoice
                            $LoanCaseInvoiceDetailsNew->ori_invoice_amt = $originalDetail->ori_invoice_amt;
                            $LoanCaseInvoiceDetailsNew->quo_amount = $originalDetail->quo_amount;
                            $LoanCaseInvoiceDetailsNew->remark = $originalDetail->remark;
                            $LoanCaseInvoiceDetailsNew->created_by = $current_user->id;
                            $LoanCaseInvoiceDetailsNew->status = 1;
                            $LoanCaseInvoiceDetailsNew->created_at = date('Y-m-d H:i:s');
                            $LoanCaseInvoiceDetailsNew->save();
                            
                            \Log::info("Detail created successfully for invoice {$invoice_main_id_new} with amount {$distributedAmount}");
                        } else {
                            \Log::info("Detail already exists for invoice {$invoice_main_id_new}, account_item_id: {$accountItemId}");
                        }
                    }
                }
            }

        }
        
        // Force database commit before calling updateInvoiceDetailsAmt
        // DB::commit();
        
        // Verify the new invoice exists in database
        $newInvoice = LoanCaseInvoiceMain::find($invoice_main_id_new);
        \Log::info("New invoice verification: " . ($newInvoice ? "FOUND ID {$newInvoice->id}" : "NOT FOUND"));
        
        // Call updateInvoiceDetailsAmt AFTER all details are created
        \Log::info("Calling updateInvoiceDetailsAmt for invoice_main_id: {$invoice_main_id}, bill_main_id: {$bill_main_id}, case_id: {$LoanCaseBillMain->case_id}, invoice_main_id_new: {$invoice_main_id_new}");
        $this->updateInvoiceDetailsAmt($invoice_main_id, $bill_main_id, $LoanCaseBillMain->case_id, $invoice_main_id_new);
        \Log::info("updateInvoiceDetailsAmt completed");
        
        // Final verification - check if the new invoice has correct values
        $finalInvoice = LoanCaseInvoiceMain::find($invoice_main_id_new);
        if ($finalInvoice) {
            \Log::info("FINAL VERIFICATION - New invoice {$invoice_main_id_new}: reimbursement_amount={$finalInvoice->reimbursement_amount}, reimbursement_sst={$finalInvoice->reimbursement_sst}");
        }

        // Recalculate bill totals after splitting invoice
        $caseController = new \App\Http\Controllers\CaseController();
        $caseController->updatePfeeDisbAmountINV($bill_main_id);
        
        return response()->json([
            'status' => 1, 
            'message' => 'Invoice split successfully',
            'bill_id' => $bill_main_id
        ]);

        // $loanCaseInvoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $invoice_main_id)->get();

        //  $party_count = InvoiceBillingParty::where('loan_case_main_bill_id', $bill_main_id)
        //     ->where('case_id', $LoanCaseBillMain->case_id)
        //     ->count();


        // if (count($loanCaseInvoiceDetails) > 0) {
        //     for ($i = 0; $i < count($loanCaseInvoiceDetails); $i++) {

        //         $LoanCaseInvoiceDetailsNew = new LoanCaseInvoiceDetails();

        //         $LoanCaseInvoiceDetailsNew->loan_case_main_bill_id = $bill_main_id;
        //         $LoanCaseInvoiceDetailsNew->account_item_id = $loanCaseInvoiceDetails[$i]->account_item_id;
        //         $LoanCaseInvoiceDetailsNew->quotation_item_id = $loanCaseInvoiceDetails[$i]->id;
        //         $LoanCaseInvoiceDetailsNew->invoice_main_id = $invoice_main_id_new;
        //         $LoanCaseInvoiceDetailsNew->amount = $loanCaseInvoiceDetails[$i]->ori_invoice_amt/$party_count;
        //         $LoanCaseInvoiceDetailsNew->ori_invoice_amt = $loanCaseInvoiceDetails[$i]->ori_invoice_amt;
        //         $LoanCaseInvoiceDetailsNew->quo_amount = $loanCaseInvoiceDetails[$i]->quo_amount;
        //         $LoanCaseInvoiceDetailsNew->remark = $loanCaseInvoiceDetails[$i]->remark;
        //         $LoanCaseInvoiceDetailsNew->created_by = $current_user->id;
        //         $LoanCaseInvoiceDetailsNew->status = 1;
        //         $LoanCaseInvoiceDetailsNew->created_at = date('Y-m-d H:i:s');

        //         $LoanCaseInvoiceDetailsNew->save();

        //         $loanCaseInvoiceDetails[$i]->amount = $loanCaseInvoiceDetails[$i]->ori_invoice_amt/$party_count;
        //         $loanCaseInvoiceDetails[$i]->save();
        //     }
        // }
    }

    public function getSplitInvoiceDetails($invoice_id)
    {
        $current_user = auth()->user();
        
        $invoice = LoanCaseInvoiceMain::where('id', $invoice_id)->first();
        
        if (!$invoice) {
            return response()->json(['status' => 0, 'message' => 'Invoice not found.']);
        }
        
        // Check if user has permission to view invoice
        $bill = LoanCaseBillMain::where('id', $invoice->loan_case_main_bill_id)->first();
        if (!$bill) {
            return response()->json(['status' => 0, 'message' => 'Bill not found.']);
        }
        
        // Get account categories
        $category = AccountCategory::where('status', '=', 1)->orderBy('order', 'ASC')->get();
        
        // Get invoice details grouped by category
        // Use the 'amount' column (not ori_invoice_amt) for display
        $invoice_details = [];
        foreach ($category as $cat) {
            // Get details for this specific invoice
            $details = DB::table('loan_case_invoice_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.id', 'qd.account_item_id', 'qd.quo_amount', 'qd.amount', 
                    'qd.ori_invoice_amt', 'qd.remark as item_remark', 'qd.quotation_item_id',
                    'a.name as account_name', 'a.name_cn as account_name_cn', 
                    'a.account_cat_id', 'a.pfee1_item', 'a.remark as item_desc')
                ->where('qd.invoice_main_id', '=', $invoice_id)
                ->where('qd.status', '=', 1)
                ->where('a.account_cat_id', '=', $cat->id)
                ->get();
            
            if ($details->count() > 0) {
                $invoice_details[] = [
                    'category' => [
                        'id' => $cat->id,
                        'category' => $cat->category,
                        'code' => $cat->code
                    ],
                    'account_details' => $details
                ];
            }
        }
        
        return response()->json([
            'status' => 1,
            'data' => [
                'id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no,
                'sst_rate' => $bill->sst_rate ?? 0,
                'invoice_details' => $invoice_details
            ]
        ]);
    }

    public function updateSplitInvoiceDetail(Request $request)
    {
        // Force write to log immediately
        error_log("=== updateSplitInvoiceDetail CALLED ===");
        error_log("Request method: " . $request->method());
        error_log("Request URL: " . $request->fullUrl());
        error_log("Request data: " . json_encode($request->all()));
        
        $current_user = auth()->user();
        
        \Log::info("=== updateSplitInvoiceDetail START ===");
        \Log::info("Request data: " . json_encode($request->all()));
        
        try {
            // Validate input
            $request->validate([
                'invoice_id' => 'required|integer',
                'details' => 'required|array',
                'details.*.id' => 'required|integer',
                'details.*.ori_invoice_amt' => 'required|numeric|min:0', // Field name is ori_invoice_amt but we update 'amount' column
            ]);
            
            $invoice = LoanCaseInvoiceMain::where('id', $request->invoice_id)->first();
            
            if (!$invoice) {
                \Log::error("Invoice {$request->invoice_id} not found!");
                error_log("Invoice {$request->invoice_id} not found!");
                return response()->json(['status' => 0, 'message' => 'Invoice not found.']);
            }
            
            \Log::info("Processing invoice {$invoice->id} (invoice_no: {$invoice->invoice_no})");
            error_log("Processing invoice {$invoice->id} (invoice_no: {$invoice->invoice_no})");
        
        // Check if invoice has been sent to SQL accounting system (this should prevent editing)
        // The relationship is: loan_case_invoice_main.bill_party_id -> invoice_billing_party.id
        // Note: 'completed' status only means billing party info is complete, not that invoice is finalized
        // So we allow editing even if completed, but prevent if sent to SQL
        if ($invoice->bill_party_id) {
            $billingParty = InvoiceBillingParty::where('id', $invoice->bill_party_id)->first();
            if ($billingParty && $billingParty->sent_to_sql == 1) {
                return response()->json(['status' => 0, 'message' => 'Cannot edit invoice that has been sent to SQL accounting system.']);
            }
        }
        
        // Get bill for party count calculation
        $bill = LoanCaseBillMain::where('id', $invoice->loan_case_main_bill_id)->first();
        if (!$bill) {
            return response()->json(['status' => 0, 'message' => 'Bill not found.']);
        }
        
        $party_count = self::getPartyCount($bill->id);
        
        // Group details by account_item_id to handle split invoices
        $detailsByItem = [];
        foreach ($request->details as $detailData) {
            $detail = LoanCaseInvoiceDetails::where('id', $detailData['id'])
                ->where('invoice_main_id', $invoice->id)
                ->first();
            
            if ($detail) {
                $account_item_id = $detail->account_item_id;
                if (!isset($detailsByItem[$account_item_id])) {
                    $detailsByItem[$account_item_id] = [];
                }
                $detailsByItem[$account_item_id][] = [
                    'detail' => $detail,
                    'new_amount' => $detailData['ori_invoice_amt'],
                    'old_amount' => $detailData['old_amount'] ?? $detail->amount // Get old amount from request or current value
                ];
            }
        }
        
        // For each account_item_id, update the edited invoice's amount and recalculate total
        foreach ($detailsByItem as $account_item_id => $itemDetails) {
            \Log::info("Processing account_item_id: {$account_item_id}");
            
            // Get all split invoice details for this account_item_id
            $allDetailsForItem = LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $bill->id)
                ->where('account_item_id', $account_item_id)
                ->where('status', '<>', 99)
                ->orderBy('invoice_main_id', 'ASC')
                ->get();
            
            \Log::info("Found " . $allDetailsForItem->count() . " details for account_item_id {$account_item_id}");
            foreach ($allDetailsForItem as $d) {
                \Log::info("  Detail ID {$d->id}: invoice_main_id={$d->invoice_main_id}, amount={$d->amount}, ori_invoice_amt={$d->ori_invoice_amt}");
            }
            
            // Find the detail that was edited (from current invoice)
            $editedDetail = null;
            $newSplitAmount = null;
            $oldSplitAmount = null;
            foreach ($itemDetails as $itemDetail) {
                if ($itemDetail['detail']->invoice_main_id == $invoice->id) {
                    $editedDetail = $itemDetail['detail'];
                    $newSplitAmount = $itemDetail['new_amount']; // This is the NEW split amount for this invoice
                    $oldSplitAmount = $itemDetail['old_amount'] ?? $editedDetail->amount; // Old amount for logging
                    break;
                }
            }
            
            if ($editedDetail && $newSplitAmount !== null) {
                \Log::info("Found edited detail: ID={$editedDetail->id}, invoice_main_id={$editedDetail->invoice_main_id}, old_split_amount={$oldSplitAmount}, new_split_amount={$newSplitAmount}");
                \Log::info("BEFORE UPDATE - Invoice Detail ID {$editedDetail->id}: invoice_main_id={$editedDetail->invoice_main_id}, account_item_id={$account_item_id}, current amount={$editedDetail->amount}, new split amount={$newSplitAmount}");
                
                // Get account item name for logging
                $accountItem = \App\Models\AccountItem::where('id', $account_item_id)->first();
                $itemName = $accountItem ? ($accountItem->name ?? 'N/A') : 'N/A';
                
                // Step 1: Update the edited invoice's amount directly
                $oldAmount = $editedDetail->amount; // Store old amount before update
                $editedDetail->amount = $newSplitAmount;
                $editedDetail->save();
                \Log::info("Updated invoice {$invoice->id}'s amount from {$oldAmount} to {$newSplitAmount}");
                
                // Step 1.5: Create AccountLog entry for this change
                $AccountLog = new AccountLog();
                $AccountLog->user_id = $current_user->id;
                $AccountLog->case_id = $bill->case_id;
                $AccountLog->bill_id = $bill->id;
                $AccountLog->object_id = $editedDetail->id;
                $AccountLog->ori_amt = $oldAmount;
                $AccountLog->new_amt = $newSplitAmount;
                $AccountLog->action = 'Update';
                $AccountLog->desc = $current_user->name . ' update invoice detail item (' . $itemName . ') for invoice ' . $invoice->invoice_no . ' from ' . number_format($oldAmount, 2) . ' to ' . number_format($newSplitAmount, 2);
                $AccountLog->status = 1;
                $AccountLog->created_at = date('Y-m-d H:i:s');
                $AccountLog->save();
                \Log::info("AccountLog created: {$AccountLog->desc}");
                
                // Step 2: Calculate new total by summing ALL split invoice amounts for this account_item_id
                $newTotalAmount = LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $bill->id)
                    ->where('account_item_id', $account_item_id)
                    ->where('status', '<>', 99)
                    ->sum('amount');
                
                \Log::info("Calculated new total amount: {$newTotalAmount} (sum of all split amounts)");
                
                // Step 3: Update ori_invoice_amt for ALL split invoices with this account_item_id to the new total
                LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $bill->id)
                    ->where('account_item_id', $account_item_id)
                    ->where('status', '<>', 99)
                    ->update(['ori_invoice_amt' => $newTotalAmount]);
                
                \Log::info("Updated ori_invoice_amt for all invoices to {$newTotalAmount}");
                
                // Step 4: Verify all amounts are correct
                $allDetailsAfterUpdate = LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $bill->id)
                    ->where('account_item_id', $account_item_id)
                    ->where('status', '<>', 99)
                    ->orderBy('invoice_main_id', 'ASC')
                    ->get();
                
                foreach ($allDetailsAfterUpdate as $d) {
                    \Log::info("  AFTER UPDATE - Detail ID {$d->id}: invoice_main_id={$d->invoice_main_id}, amount={$d->amount}, ori_invoice_amt={$d->ori_invoice_amt}");
                }
            }
        }
        
        // IMPORTANT: Update bill main totals (pfee1_inv, pfee2_inv, etc.)
        // This method will:
        // 1. Recalculate invoice totals from details for ALL invoices
        // 2. Sum them up to update bill main totals
        // This ensures the Prof Fee summary card shows the correct total
        // Use CaseController's method which properly sums all invoices
        $caseController = new \App\Http\Controllers\CaseController();
        $caseController->updatePfeeDisbAmountINV($bill->id);
        
        // Note: We don't need to call updateInvoiceDetailsAmt separately because
        // updatePfeeDisbAmountINVFromDetails already recalculates all invoice totals from details
        
        // IMPORTANT: Fetch fresh bill data from database to ensure we have the latest values
        // The updatePfeeDisbAmountINVFromDetails method saves the bill, so we need to reload it
        $updatedBill = LoanCaseBillMain::where('id', $bill->id)->first();
        
        // Debug: Log all invoice details to verify calculation
        $allInvoiceDetails = LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $bill->id)
            ->where('status', '<>', 99)
            ->get();
        foreach ($allInvoiceDetails as $detail) {
            $accountItem = DB::table('account_item')->where('id', $detail->account_item_id)->first();
            if ($accountItem && $accountItem->account_cat_id == 1) {
                \Log::info("Invoice Detail - Invoice {$detail->invoice_main_id}: account_item_id={$detail->account_item_id}, amount={$detail->amount}, pfee1_item={$accountItem->pfee1_item}");
            }
        }
        
        // Debug: Log all invoice totals
        $allInvoices = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill->id)
            ->where('status', '<>', 99)
            ->get();
        foreach ($allInvoices as $inv) {
            \Log::info("Invoice {$inv->id}: pfee1_inv={$inv->pfee1_inv}, pfee2_inv={$inv->pfee2_inv}, sst_inv={$inv->sst_inv}");
        }
        
        \Log::info("After updateSplitInvoiceDetail - Bill {$bill->id}: pfee1_inv={$updatedBill->pfee1_inv}, pfee2_inv={$updatedBill->pfee2_inv}, sst_inv={$updatedBill->sst_inv}, total_prof_fee=" . ($updatedBill->pfee1_inv + $updatedBill->pfee2_inv));
        
        \Log::info("=== updateSplitInvoiceDetail END - SUCCESS ===");
        error_log("=== updateSplitInvoiceDetail END - SUCCESS ===");
        
        return response()->json([
            'status' => 1, 
            'message' => 'Invoice details updated successfully.',
            'bill_id' => $bill->id,
            'bill_totals' => [
                'pfee1_inv' => $updatedBill->pfee1_inv,
                'pfee2_inv' => $updatedBill->pfee2_inv,
                'sst_inv' => $updatedBill->sst_inv,
                'total_prof_fee' => $updatedBill->pfee1_inv + $updatedBill->pfee2_inv
            ]
        ]);
        } catch (\Exception $e) {
            \Log::error("ERROR in updateSplitInvoiceDetail: " . $e->getMessage());
            \Log::error("Stack trace: " . $e->getTraceAsString());
            error_log("ERROR in updateSplitInvoiceDetail: " . $e->getMessage());
            return response()->json(['status' => 0, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function getInvoiceDate($invoice_id)
    {
        $current_user = auth()->user();

        $invoice = LoanCaseInvoiceMain::where('id', $invoice_id)->first();

        if (!$invoice) {
            return response()->json(['status' => 0, 'message' => 'Invoice not found.']);
        }

        // Format date as YYYY-MM-DD for consistent handling
        $invoiceDate = null;
        
        // First, try to get date from invoice main
        if ($invoice->Invoice_date) {
            // Convert to YYYY-MM-DD format
            $invoiceDate = date('Y-m-d', strtotime($invoice->Invoice_date));
        } else {
            // If invoice date is empty, retrieve from loan_case_bill_main
            $bill = LoanCaseBillMain::where('id', $invoice->loan_case_main_bill_id)->first();
            if ($bill && $bill->invoice_date) {
                $invoiceDate = date('Y-m-d', strtotime($bill->invoice_date));
            }
        }

        return response()->json([
            'status' => 1,
            'invoice_date' => $invoiceDate,
            'invoice_no' => $invoice->invoice_no
        ]);
    }

    public function updateInvoiceDate(Request $request)
    {
        $current_user = auth()->user();

        $request->validate([
            'invoice_id' => 'required|integer',
            'invoice_date' => 'required|date',
        ]);

        $invoice = LoanCaseInvoiceMain::where('id', $request->invoice_id)->first();

        if (!$invoice) {
            return response()->json(['status' => 0, 'message' => 'Invoice not found.']);
        }

        // Check if invoice has been sent to SQL accounting system
        if ($invoice->bill_party_id) {
            $billingParty = InvoiceBillingParty::where('id', $invoice->bill_party_id)->first();
            if ($billingParty && $billingParty->sent_to_sql == 1) {
                return response()->json(['status' => 0, 'message' => 'Cannot edit invoice that has been sent to SQL accounting system.']);
            }
        }

        // Get bill main for case_id
        $bill = LoanCaseBillMain::where('id', $invoice->loan_case_main_bill_id)->first();
        if (!$bill) {
            return response()->json(['status' => 0, 'message' => 'Bill not found.']);
        }

        // Store old date for logging
        $oldDate = $invoice->Invoice_date;
        
        \Log::info("=== UPDATE INVOICE DATE DEBUG ===");
        \Log::info("Invoice ID: {$invoice->id}");
        \Log::info("Old Date: {$oldDate}");
        \Log::info("Request invoice_date: {$request->invoice_date}");
        \Log::info("Request all data: " . json_encode($request->all()));
        
        // Validate and format the date
        $newDate = $request->invoice_date;
        if (!$newDate || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $newDate)) {
            \Log::error("Invalid date format received: {$newDate}");
            return response()->json(['status' => 0, 'message' => 'Invalid date format.']);
        }
        
        \Log::info("Formatted new date: {$newDate}");
        
        // Update invoice date - ensure it's saved as date format
        // Use direct DB update to ensure the date is saved correctly
        $updated = DB::table('loan_case_invoice_main')
            ->where('id', $invoice->id)
            ->update([
                'Invoice_date' => $newDate,
                'updated_by' => $current_user->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        \Log::info("DB update result - rows affected: {$updated}");

        // Reload invoice to get the updated date from database
        $invoice->refresh();
        
        \Log::info("After refresh - Invoice ID: {$invoice->id}, Invoice_date: {$invoice->Invoice_date}");
        
        // Verify the date was saved correctly
        $verifyDate = DB::table('loan_case_invoice_main')
            ->where('id', $invoice->id)
            ->value('Invoice_date');
        \Log::info("Verified date from DB: {$verifyDate}");

        // Create AccountLog entry
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $bill->case_id;
        $AccountLog->bill_id = $bill->id;
        $AccountLog->object_id = $invoice->id;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->action = 'Update';
        $AccountLog->desc = $current_user->name . ' update invoice date for invoice ' . $invoice->invoice_no . ' from ' . ($oldDate ? date('d-m-Y', strtotime($oldDate)) : 'N/A') . ' to ' . date('d-m-Y', strtotime($request->invoice_date));
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        // Format the updated date for response
        $updatedDate = $invoice->Invoice_date ? date('Y-m-d', strtotime($invoice->Invoice_date)) : null;

        return response()->json([
            'status' => 1,
            'message' => 'Invoice date updated successfully.',
            'invoice_date' => $updatedDate,
            'invoice_no' => $invoice->invoice_no,
            'bill_id' => $bill->id // Return bill_id for refreshing invoice section
        ]);
    }

    public function splitInvoiceBak(Request $request, $bill_main_id)
    {
        $current_user = auth()->user();
        
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $bill_main_id)->first();
        
        if (!$LoanCaseBillMain) {
            return response()->json(['status' => 0, 'message' => 'Bill not found.']);
        }

        $case = LoanCase::where('id', '=', $LoanCaseBillMain->case_id)->first();
        
        if (!$case) {
            return response()->json(['status' => 0, 'message' => 'Case not found.']);
        }

        // Get existing invoice for this bill
        $existingInvoice = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)->first();
        
        if (!$existingInvoice) {
            return response()->json(['status' => 0, 'message' => 'No invoice found for this bill.']);
        }

        // Skip party creation - will set bill_party_id to 0

        $loanCaseInvoiceMain = new LoanCaseInvoiceMain();

        $loanCaseInvoiceMain->loan_case_main_bill_id = $bill_main_id;
        $loanCaseInvoiceMain->invoice_no = '';
        $loanCaseInvoiceMain->Invoice_date = $LoanCaseBillMain->Invoice_date;
        // Don't set amounts here - let updateInvoiceDetailsAmt calculate them correctly
        $loanCaseInvoiceMain->amount = 0; // Will be calculated by updateInvoiceDetailsAmt
        $loanCaseInvoiceMain->pfee1_inv = 0; // Will be calculated by updateInvoiceDetailsAmt
        $loanCaseInvoiceMain->pfee2_inv = 0; // Will be calculated by updateInvoiceDetailsAmt
        $loanCaseInvoiceMain->sst_inv = 0; // Will be calculated by updateInvoiceDetailsAmt
        $loanCaseInvoiceMain->reimbursement_amount = 0; // Will be calculated by updateInvoiceDetailsAmt
        $loanCaseInvoiceMain->reimbursement_sst = 0; // Will be calculated by updateInvoiceDetailsAmt
        $loanCaseInvoiceMain->bill_party_id = 0; // Set to 0 since we're not creating a party
        $loanCaseInvoiceMain->remark = "";
        $loanCaseInvoiceMain->created_by = $current_user->id;
        $loanCaseInvoiceMain->status = 1;
        $loanCaseInvoiceMain->created_at = date('Y-m-d H:i:s');

        $loanCaseInvoiceMain->save();

        $invoice_main_id_new = $loanCaseInvoiceMain->id;

        $this->generateNewInvNo($bill_main_id, $invoice_main_id_new, false);

        $invoice_main_id = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)->pluck('id')->first();

        // Copy invoice details from the original invoice to the new split invoice
        $loanCaseInvoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $invoice_main_id)->get();

         $party_count = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)
            ->count();

        $sum = 0;

        if (count($loanCaseInvoiceDetails) > 0) {
            // Use the same redistribution logic as removeInvoice
            $allDetails = LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $bill_main_id)
                ->where('status', '<>', 99)
                ->get();
            
            // Get all invoices for this bill to know their indices
            $allInvoices = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)
                ->where('status', '<>', 99)
                ->orderBy('id')
                ->get();
            
            // Group details by account_item_id to redistribute
            $detailsByItem = $allDetails->groupBy('account_item_id');
            
            foreach ($detailsByItem as $accountItemId => $details) {
                // Get the original total amount for this account item
                $originalTotalAmount = $details->first()->ori_invoice_amt;
                
                // Distribute amounts properly - each invoice gets its correct share
                foreach ($allInvoices as $invoiceIndex => $invoice) {
                    // Get the distributed amount for this invoice index
                    $distributedAmount = $this->distributeAmount($originalTotalAmount, $party_count, $invoiceIndex);
                    
                    // Update details for this specific invoice
                    foreach ($details as $detail) {
                        if ($detail->invoice_main_id == $invoice->id) {
                            $detail->amount = $distributedAmount;
                            $detail->save();
                        }
                    }
                }
            }

            // Use the existing updateInvoiceDetailsAmt function
            $this->updateInvoiceDetailsAmt($invoice_main_id, $bill_main_id, $LoanCaseBillMain->case_id, $invoice_main_id_new);

        }
        
        // if (count($originalInvoiceDetails) > 0) {
        //     foreach ($originalInvoiceDetails as $originalDetail) {
        //         $newDetail = new LoanCaseInvoiceDetails();
        //         $newDetail->loan_case_main_bill_id = $bill_main_id;
        //         $newDetail->account_item_id = $originalDetail->account_item_id;
        //         $newDetail->quotation_item_id = $originalDetail->quotation_item_id;
        //         $newDetail->invoice_main_id = $invoice_main_id_new;
        //         $newDetail->amount = $originalDetail->amount; // Copy the same amount initially
        //         $newDetail->ori_invoice_amt = $originalDetail->ori_invoice_amt;
        //         $newDetail->quo_amount = $originalDetail->quo_amount;
        //         $newDetail->remark = $originalDetail->remark;
        //         $newDetail->created_by = $current_user->id;
        //         $newDetail->status = 1;
        //         $newDetail->created_at = date('Y-m-d H:i:s');
        //         $newDetail->save();
        //     }
        // }


        



        // // Use the existing updateInvoiceDetailsAmt function
        // $this->updateInvoiceDetailsAmt($invoice_main_id, $bill_main_id, $LoanCaseBillMain->case_id, $invoice_main_id_new);

        
        return response()->json(['status' => 1, 'message' => 'Invoice split successfully']);

        // $loanCaseInvoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $invoice_main_id)->get();

        //  $party_count = InvoiceBillingParty::where('loan_case_main_bill_id', $bill_main_id)
        //     ->where('case_id', $LoanCaseBillMain->case_id)
        //     ->count();


        // if (count($loanCaseInvoiceDetails) > 0) {
        //     for ($i = 0; $i < count($loanCaseInvoiceDetails); $i++) {

        //         $LoanCaseInvoiceDetailsNew = new LoanCaseInvoiceDetails();

        //         $LoanCaseInvoiceDetailsNew->loan_case_main_bill_id = $bill_main_id;
        //         $LoanCaseInvoiceDetailsNew->account_item_id = $loanCaseInvoiceDetails[$i]->account_item_id;
        //         $LoanCaseInvoiceDetailsNew->quotation_item_id = $loanCaseInvoiceDetails[$i]->id;
        //         $LoanCaseInvoiceDetailsNew->invoice_main_id = $invoice_main_id_new;
        //         $LoanCaseInvoiceDetailsNew->amount = $loanCaseInvoiceDetails[$i]->ori_invoice_amt/$party_count;
        //         $LoanCaseInvoiceDetailsNew->ori_invoice_amt = $loanCaseInvoiceDetails[$i]->ori_invoice_amt;
        //         $LoanCaseInvoiceDetailsNew->quo_amount = $loanCaseInvoiceDetails[$i]->quo_amount;
        //         $LoanCaseInvoiceDetailsNew->remark = $loanCaseInvoiceDetails[$i]->remark;
        //         $LoanCaseInvoiceDetailsNew->created_by = $current_user->id;
        //         $LoanCaseInvoiceDetailsNew->status = 1;
        //         $LoanCaseInvoiceDetailsNew->created_at = date('Y-m-d H:i:s');

        //         $LoanCaseInvoiceDetailsNew->save();

        //         $loanCaseInvoiceDetails[$i]->amount = $loanCaseInvoiceDetails[$i]->ori_invoice_amt/$party_count;
        //         $loanCaseInvoiceDetails[$i]->save();
        //     }
        // }
    }

    public static function updateInvoiceDetailsAmtBak($invoice_main_id, $bill_main_id, $case_id, $invoice_main_id_new = "")
    {

        $current_user = auth()->user();
        $loanCaseInvoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $invoice_main_id)->get();

        // $party_count = InvoiceBillingParty::where('loan_case_main_bill_id', $bill_main_id)
        //     ->where('case_id', $case_id)
        //     ->where('invoice_main_id','!=', 0)
        //     ->count();

        $party_count = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)
            ->count();

        $sum = 0;

        if (count($loanCaseInvoiceDetails) > 0) {
            for ($i = 0; $i < count($loanCaseInvoiceDetails); $i++) {

                if ($invoice_main_id_new != "") {
                    $LoanCaseInvoiceDetailsNew = new LoanCaseInvoiceDetails();

                    $LoanCaseInvoiceDetailsNew->loan_case_main_bill_id = $bill_main_id;
                    $LoanCaseInvoiceDetailsNew->account_item_id = $loanCaseInvoiceDetails[$i]->account_item_id;
                    $LoanCaseInvoiceDetailsNew->quotation_item_id = $loanCaseInvoiceDetails[$i]->id;
                    $LoanCaseInvoiceDetailsNew->invoice_main_id = $invoice_main_id_new;
                    $LoanCaseInvoiceDetailsNew->amount = $this->distributeAmount($loanCaseInvoiceDetails[$i]->ori_invoice_amt, $party_count, 1);
                    $LoanCaseInvoiceDetailsNew->ori_invoice_amt = $loanCaseInvoiceDetails[$i]->ori_invoice_amt;
                    $LoanCaseInvoiceDetailsNew->quo_amount = $loanCaseInvoiceDetails[$i]->quo_amount;
                    $LoanCaseInvoiceDetailsNew->remark = $loanCaseInvoiceDetails[$i]->remark;
                    $LoanCaseInvoiceDetailsNew->created_by = $current_user->id;
                    $LoanCaseInvoiceDetailsNew->status = 1;
                    $LoanCaseInvoiceDetailsNew->created_at = date('Y-m-d H:i:s');

                    $LoanCaseInvoiceDetailsNew->save();

                }

                $loanCaseInvoiceDetails[$i]->amount = $this->distributeAmount($loanCaseInvoiceDetails[$i]->ori_invoice_amt, $party_count, 0);
                $loanCaseInvoiceDetails[$i]->save();
            }

            $total_amt = LoanCaseBillDetails::where("loan_case_main_bill_id", $bill_main_id)->sum("amount");
            $total_sst = LoanCaseBillDetails::where("loan_case_main_bill_id", $bill_main_id)->sum("sst");
            // $total_amt = LoanCaseBillDetails::where("loan_case_main_bill_id", $bill_main_id)->sum("ori_invoice_amt");

            $total_amt = $this->distributeAmount($total_amt + $total_sst, $party_count, 0);

            LoanCaseInvoiceMain::where("loan_case_main_bill_id", $bill_main_id)->update(["amount" => $total_amt]);

        }
    }

    // public static function updateInvoiceDetailsAmt($invoice_main_id, $bill_main_id, $case_id, $invoice_main_id_new = "")
    // {

    //     $current_user = auth()->user();

    //     $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)->get();

    //     $party_count = EInvoiceContoller::getPartyCount($bill_main_id);

    //     LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $bill_main_id)
    //         ->update([
    //             'amount' => DB::raw("ori_invoice_amt / {$party_count}"),
    //         ]);

    //     $total_amt = LoanCaseBillDetails::where("loan_case_main_bill_id", $bill_main_id)->sum("amount");
    //     $total_sst = LoanCaseBillDetails::where("loan_case_main_bill_id", $bill_main_id)->sum("sst");

    //     $total_amt = ($total_amt + $total_sst) / $party_count;
    //     // $total_amt = ($total_amt) / $party_count;

    //     LoanCaseInvoiceMain::where("id", $invoice_main_id)->update(["amount" => $total_amt]);

    // }

    public static function updateInvoiceDetailsAmt($invoice_main_id, $bill_main_id, $case_id, $invoice_main_id_new = "")
    {
        // Get SST rate once
        $bill = LoanCaseBillMain::find($bill_main_id);
        $sstRate = $bill ? ($bill->sst_rate / 100) : 0;
        

        // Get all invoices for this bill
        $invoices = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $bill_main_id)->get();
        \Log::info("Found " . $invoices->count() . " invoices for bill {$bill_main_id}: " . $invoices->pluck('id')->implode(', '));
        
        $isSplitInvoice = $invoices->count() > 1;

        // Update each invoice by summing its existing details
        foreach ($invoices as $invoiceIndex => $invoice) {
            \Log::info("Processing invoice {$invoice->id} in updateInvoiceDetailsAmt");
            $details = LoanCaseInvoiceDetails::where('invoice_main_id', $invoice->id)
                ->where('status', '<>', 99)
                ->get();
            
            \Log::info("Found {$details->count()} details for invoice {$invoice->id}");
            
            // Debug: Show all details for this invoice
            foreach ($details as $detail) {
                $accountItem = DB::table('account_item')->where('id', $detail->account_item_id)->first();
                $accountCatId = $accountItem ? $accountItem->account_cat_id : 'unknown';
                \Log::info("Detail for invoice {$invoice->id}: account_item_id={$detail->account_item_id}, account_cat_id={$accountCatId}, amount={$detail->amount}");
            }

            $pfee1 = 0;
            $pfee2 = 0;
            $sst = 0;
            $reimbursement_amount = 0;
            $reimbursement_sst = 0;
            $total = 0;

            // Calculate pfee1 and pfee2 first
            foreach ($details as $detail) {
                // Get account item info to categorize
                $accountItem = DB::table('account_item')->where('id', $detail->account_item_id)->first();
                
                if ($accountItem) {
                    if ($accountItem->account_cat_id == 1) {
                        if ($accountItem->pfee1_item == 1) {
                            $pfee1 += $detail->amount;
                        } else {
                            $pfee2 += $detail->amount;
                        }
                    }
                    // Note: reimbursement_amount will be calculated separately for split invoices
                    // For single invoices, it's calculated here
                    if (!$isSplitInvoice && $accountItem->account_cat_id == 4) {
                        \Log::info("Found reimbursement detail for invoice {$invoice->id}: amount={$detail->amount}, adding to reimbursement_amount");
                        $reimbursement_amount += $detail->amount;
                    }
                }
            }
            
            $pfee1 = round($pfee1, 2);
            $pfee2 = round($pfee2, 2);
            $totalPfee = $pfee1 + $pfee2;
            
            // For SST calculation:
            // - If split invoice: calculate from total pfee of ALL invoices, then distribute proportionally
            //   This ensures correct SST distribution (e.g., 628.22 and 628.21)
            // - If single invoice: calculate from individual detail items (matches invoice display)
            if ($isSplitInvoice) {
                // Calculate total pfee across all invoices for this bill
                $totalPfeeAllInvoices = 0;
                $invoicePfees = [];
                foreach ($invoices as $inv) {
                    $invDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $inv->id)
                        ->where('status', '<>', 99)
                        ->get();
                    $invPfee1 = 0;
                    $invPfee2 = 0;
                    foreach ($invDetails as $invDetail) {
                        $invAccountItem = DB::table('account_item')->where('id', $invDetail->account_item_id)->first();
                        if ($invAccountItem && $invAccountItem->account_cat_id == 1) {
                            if ($invAccountItem->pfee1_item == 1) {
                                $invPfee1 += $invDetail->amount;
                            } else {
                                $invPfee2 += $invDetail->amount;
                            }
                        }
                    }
                    $invTotalPfee = round($invPfee1 + $invPfee2, 2);
                    $invoicePfees[$inv->id] = $invTotalPfee;
                    $totalPfeeAllInvoices += $invTotalPfee;
                }
                
                // Calculate total SST from total pfee (applying special rounding rule)
                $totalSstRaw = $totalPfeeAllInvoices * $sstRate;
                $totalSstString = number_format($totalSstRaw, 3, '.', '');
                if (substr($totalSstString, -1) == '5') {
                    $totalSstAllInvoices = floor($totalSstRaw * 100) / 100; // Round down
                } else {
                    $totalSstAllInvoices = round($totalSstRaw, 2); // Normal rounding
                }
                
                // Distribute SST: Calculate from each invoice's total pfee individually
                // Then adjust to ensure total matches exactly
                // Sort invoices by pfee (descending) so higher pfee gets processed first
                $sortedInvoices = $invoices->sortByDesc(function($inv) use ($invoicePfees) {
                    return $invoicePfees[$inv->id];
                })->values();
                
                $calculatedSsts = [];
                $totalCalculatedSst = 0;
                
                // Calculate SST for each invoice from its own pfee
                foreach ($sortedInvoices as $inv) {
                    $invPfee = $invoicePfees[$inv->id];
                    $invSstRaw = $invPfee * $sstRate;
                    $invSstString = number_format($invSstRaw, 3, '.', '');
                    
                    if (substr($invSstString, -1) == '5') {
                        $invSst = floor($invSstRaw * 100) / 100;
                    } else {
                        $invSst = round($invSstRaw, 2);
                    }
                    
                    $calculatedSsts[$inv->id] = $invSst;
                    $totalCalculatedSst += $invSst;
                }
                
                // Adjust to match total SST exactly
                $difference = $totalSstAllInvoices - $totalCalculatedSst;
                if (abs($difference) > 0.001) {
                    // Add difference to invoice with highest pfee (first in sorted list)
                    $highestPfeeInvoice = $sortedInvoices->first();
                    $calculatedSsts[$highestPfeeInvoice->id] = round($calculatedSsts[$highestPfeeInvoice->id] + $difference, 2);
                }
                
                // Get SST for current invoice
                $sst = $calculatedSsts[$invoice->id];
            } else {
                // For single invoice: calculate SST from individual detail items (matches invoice display)
                foreach ($details as $detail) {
                    $accountItem = DB::table('account_item')->where('id', $detail->account_item_id)->first();
                    
                    if ($accountItem && $accountItem->account_cat_id == 1) {
                        // Apply special rounding rule for SST: round DOWN if 3rd decimal is 5
                        $sst_calculation = $detail->amount * $sstRate;
                        $sst_string = number_format($sst_calculation, 3, '.', '');
                        
                        if (substr($sst_string, -1) == '5') {
                            $row_sst = floor($sst_calculation * 100) / 100; // Round down
                        } else {
                            $row_sst = round($sst_calculation, 2); // Normal rounding
                        }
                        
                        $sst += $row_sst;
                    }
                }
            }
            
            // Calculate reimbursement amount and SST based on whether it's a split invoice
            if ($isSplitInvoice) {
                // For split invoices: Calculate total reimbursement across all invoices, then distribute equally
                $totalReimbAllInvoices = 0;
                foreach ($invoices as $inv) {
                    $invDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $inv->id)
                        ->where('status', '<>', 99)
                        ->get();
                    $invReimb = 0;
                    foreach ($invDetails as $invDetail) {
                        $invAccountItem = DB::table('account_item')->where('id', $invDetail->account_item_id)->first();
                        if ($invAccountItem && $invAccountItem->account_cat_id == 4) {
                            $invReimb += $invDetail->amount;
                        }
                    }
                    $totalReimbAllInvoices += round($invReimb, 2);
                }
                
                // Distribute reimbursement equally across invoices
                $invoiceCount = $invoices->count();
                $reimbPerInvoice = round($totalReimbAllInvoices / $invoiceCount, 2);
                $totalDistributedReimb = $reimbPerInvoice * ($invoiceCount - 1);
                $lastReimb = round($totalReimbAllInvoices - $totalDistributedReimb, 2);
                
                // Find current invoice index
                $sortedInvoices = $invoices->sortBy('id')->values();
                $currentIndex = 0;
                foreach ($sortedInvoices as $idx => $inv) {
                    if ($inv->id == $invoice->id) {
                        $currentIndex = $idx;
                        break;
                    }
                }
                
                // Last invoice gets remainder, others get equal share
                if ($currentIndex == $invoiceCount - 1) {
                    $reimbursement_amount = $lastReimb;
                } else {
                    $reimbursement_amount = $reimbPerInvoice;
                }
                
                // Calculate total reimbursement SST from total reimbursement (applying special rounding rule)
                $totalReimbSstRaw = $totalReimbAllInvoices * $sstRate;
                $totalReimbSstString = number_format($totalReimbSstRaw, 3, '.', '');
                if (substr($totalReimbSstString, -1) == '5') {
                    $totalReimbSstAllInvoices = floor($totalReimbSstRaw * 100) / 100; // Round down
                } else {
                    $totalReimbSstAllInvoices = round($totalReimbSstRaw, 2); // Normal rounding
                }
                
                // Distribute reimbursement SST equally across invoices
                $reimbSstPerInvoice = round($totalReimbSstAllInvoices / $invoiceCount, 2);
                $totalDistributedReimbSst = $reimbSstPerInvoice * ($invoiceCount - 1);
                $lastReimbSst = round($totalReimbSstAllInvoices - $totalDistributedReimbSst, 2);
                
                // Use the same currentIndex from above
                if ($currentIndex == $invoiceCount - 1) {
                    $reimbursement_sst = $lastReimbSst;
                } else {
                    $reimbursement_sst = $reimbSstPerInvoice;
                }
            } else {
                // For single invoice: calculate reimbursement amount and SST from individual detail items
                foreach ($details as $detail) {
                    $accountItem = DB::table('account_item')->where('id', $detail->account_item_id)->first();
                    
                    if ($accountItem && $accountItem->account_cat_id == 4) {
                        $reimbursement_amount += $detail->amount;
                        
                        // Apply special rounding rule for reimbursement SST
                        $sst_calculation = $detail->amount * $sstRate;
                        $sst_string = number_format($sst_calculation, 3, '.', '');
                        
                        if (substr($sst_string, -1) == '5') {
                            $row_sst = floor($sst_calculation * 100) / 100; // Round down
                        } else {
                            $row_sst = round($sst_calculation, 2); // Normal rounding
                        }
                        
                        $reimbursement_sst += $row_sst;
                    }
                }
                $reimbursement_amount = round($reimbursement_amount, 2);
                $reimbursement_sst = round($reimbursement_sst, 2);
            }
            
            // Calculate total
            $total = $pfee1 + $pfee2 + $sst + $reimbursement_amount + $reimbursement_sst;
            
            // Add non-pfee, non-reimbursement amounts
            foreach ($details as $detail) {
                $accountItem = DB::table('account_item')->where('id', $detail->account_item_id)->first();
                if (!$accountItem || ($accountItem->account_cat_id != 1 && $accountItem->account_cat_id != 4)) {
                    $total += $detail->amount;
                }
            }
            
            \Log::info("Final calculation for invoice {$invoice->id}: pfee1={$pfee1}, pfee2={$pfee2}, sst={$sst}, reimbursement_amount={$reimbursement_amount}, reimbursement_sst={$reimbursement_sst}, total={$total}");

            // Update the invoice record
            $invoice->update([
                'pfee1_inv' => $pfee1,
                'pfee2_inv' => $pfee2,
                'sst_inv' => round($sst, 2),
                'reimbursement_amount' => round($reimbursement_amount, 2),
                'reimbursement_sst' => round($reimbursement_sst, 2),
                'amount' => round($total, 2),
                'updated_at' => now()
            ]);
            
            // Update transfer_fee_details to match corrected invoice amounts (with equal distribution if possible)
            $totalPfee = $pfee1 + $pfee2;
            $transferFeeDetails = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
                ->where('status', '<>', 99)
                ->get();
            
            if ($transferFeeDetails->count() > 0) {
                // Check if total can be divided equally across split invoices
                $canDivideEqually = false;
                $equalPfee = 0;
                $equalSst = 0;
                $equalReimb = 0;
                $equalReimbSst = 0;
                
                if ($isSplitInvoice) {
                    // Calculate totals across all invoices using the same calculation method
                    $totalPfeeAllInvoices = 0;
                    $totalSstAllInvoices = 0;
                    $totalReimbAllInvoices = 0;
                    $totalReimbSstAllInvoices = 0;
                    
                    foreach ($invoices as $inv) {
                        // Use the same calculation method
                        $invDetails = LoanCaseInvoiceDetails::where('invoice_main_id', $inv->id)
                            ->where('status', '<>', 99)
                            ->get();
                        
                        $invPfee1 = 0;
                        $invPfee2 = 0;
                        $invSst = 0;
                        $invReimb = 0;
                        $invReimbSst = 0;
                        
                        foreach ($invDetails as $invDetail) {
                            $invAccountItem = DB::table('account_item')->where('id', $invDetail->account_item_id)->first();
                            if ($invAccountItem) {
                                if ($invAccountItem->account_cat_id == 1) {
                                    if ($invAccountItem->pfee1_item == 1) {
                                        $invPfee1 += $invDetail->amount;
                                    } else {
                                        $invPfee2 += $invDetail->amount;
                                    }
                                    // Use standard rounding to match invoice display
                                    $invSst += round($invDetail->amount * $sstRate, 2);
                                } elseif ($invAccountItem->account_cat_id == 4) {
                                    $invReimb += $invDetail->amount;
                                    $invReimbSst += round($invDetail->amount * $sstRate, 2);
                                }
                            }
                        }
                        
                        $totalPfeeAllInvoices += round($invPfee1 + $invPfee2, 2);
                        $totalSstAllInvoices += round($invSst, 2);
                        $totalReimbAllInvoices += round($invReimb, 2);
                        $totalReimbSstAllInvoices += round($invReimbSst, 2);
                    }
                    
                    // Check if totals can be divided equally
                    $equalPfeeTest = round($totalPfeeAllInvoices / $invoices->count(), 2);
                    $expectedPfeeTotal = $equalPfeeTest * $invoices->count();
                    $canDivideEqually = abs($totalPfeeAllInvoices - $expectedPfeeTotal) <= 0.01;
                    
                    if ($canDivideEqually) {
                        $equalPfee = $equalPfeeTest;
                        $equalSst = round($totalSstAllInvoices / $invoices->count(), 2);
                        $equalReimb = round($totalReimbAllInvoices / $invoices->count(), 2);
                        $equalReimbSst = round($totalReimbSstAllInvoices / $invoices->count(), 2);
                    }
                }
                
                // If only one record, update it directly
                if ($transferFeeDetails->count() == 1) {
                    $tfd = $transferFeeDetails->first();
                    
                    // Use equal amounts if can divide equally, otherwise use calculated amounts
                    if ($canDivideEqually) {
                        $tfd->transfer_amount = round($equalPfee, 2);
                        $tfd->sst_amount = round($equalSst, 2);
                        $tfd->reimbursement_amount = round($equalReimb, 2);
                        $tfd->reimbursement_sst_amount = round($equalReimbSst, 2);
                    } else {
                        $tfd->transfer_amount = round($totalPfee, 2);
                        $tfd->sst_amount = round($sst, 2);
                        $tfd->reimbursement_amount = round($reimbursement_amount, 2);
                        $tfd->reimbursement_sst_amount = round($reimbursement_sst, 2);
                    }
                    $tfd->updated_at = now();
                    $tfd->save();
                } else {
                    // Multiple records: distribute equally if possible, otherwise preserve ratio
                    if ($canDivideEqually) {
                        // Distribute equally: Use equal amounts for this invoice
                        $pfeePerRecord = round($equalPfee / $transferFeeDetails->count(), 2);
                        $totalDistributedPfee = $pfeePerRecord * ($transferFeeDetails->count() - 1);
                        $lastPfeeRecord = round($equalPfee - $totalDistributedPfee, 2);
                        
                        $sstPerRecord = round($equalSst / $transferFeeDetails->count(), 2);
                        $totalDistributedSst = $sstPerRecord * ($transferFeeDetails->count() - 1);
                        $lastSstRecord = round($equalSst - $totalDistributedSst, 2);
                        
                        $reimbPerRecord = round($equalReimb / $transferFeeDetails->count(), 2);
                        $totalDistributedReimb = $reimbPerRecord * ($transferFeeDetails->count() - 1);
                        $lastReimbRecord = round($equalReimb - $totalDistributedReimb, 2);
                        
                        $reimbSstPerRecord = round($equalReimbSst / $transferFeeDetails->count(), 2);
                        $totalDistributedReimbSst = $reimbSstPerRecord * ($transferFeeDetails->count() - 1);
                        $lastReimbSstRecord = round($equalReimbSst - $totalDistributedReimbSst, 2);
                        
                        foreach ($transferFeeDetails as $index => $tfd) {
                            if ($index == $transferFeeDetails->count() - 1) {
                                $tfd->transfer_amount = $lastPfeeRecord;
                                $tfd->sst_amount = $lastSstRecord;
                                $tfd->reimbursement_amount = $lastReimbRecord;
                                $tfd->reimbursement_sst_amount = $lastReimbSstRecord;
                            } else {
                                $tfd->transfer_amount = $pfeePerRecord;
                                $tfd->sst_amount = $sstPerRecord;
                                $tfd->reimbursement_amount = $reimbPerRecord;
                                $tfd->reimbursement_sst_amount = $reimbSstPerRecord;
                            }
                            
                            $tfd->updated_at = now();
                            $tfd->save();
                        }
                    } else {
                        // Preserve original distribution ratio
                        $totalOriginalPfee = $transferFeeDetails->sum('transfer_amount');
                        $totalOriginalSst = $transferFeeDetails->sum('sst_amount');
                        $totalOriginalReimb = $transferFeeDetails->sum('reimbursement_amount');
                        $totalOriginalReimbSst = $transferFeeDetails->sum('reimbursement_sst_amount');
                        
                        $totalDistributedPfee = 0;
                        $totalDistributedSst = 0;
                        $totalDistributedReimb = 0;
                        $totalDistributedReimbSst = 0;
                        
                        foreach ($transferFeeDetails as $index => $tfd) {
                            if ($totalOriginalPfee > 0.01) {
                                $ratio = $tfd->transfer_amount / $totalOriginalPfee;
                                $tfd->transfer_amount = round($totalPfee * $ratio, 2);
                            } else {
                                $tfd->transfer_amount = round($totalPfee / $transferFeeDetails->count(), 2);
                            }
                            $totalDistributedPfee += $tfd->transfer_amount;
                            
                            if ($totalOriginalSst > 0.01) {
                                $ratio = $tfd->sst_amount / $totalOriginalSst;
                                $tfd->sst_amount = round($sst * $ratio, 2);
                            } else {
                                $tfd->sst_amount = round($sst / $transferFeeDetails->count(), 2);
                            }
                            $totalDistributedSst += $tfd->sst_amount;
                            
                            if ($totalOriginalReimb > 0.01) {
                                $ratio = $tfd->reimbursement_amount / $totalOriginalReimb;
                                $tfd->reimbursement_amount = round($reimbursement_amount * $ratio, 2);
                            } else {
                                $tfd->reimbursement_amount = round($reimbursement_amount / $transferFeeDetails->count(), 2);
                            }
                            $totalDistributedReimb += $tfd->reimbursement_amount;
                            
                            if ($totalOriginalReimbSst > 0.01) {
                                $ratio = $tfd->reimbursement_sst_amount / $totalOriginalReimbSst;
                                $tfd->reimbursement_sst_amount = round($reimbursement_sst * $ratio, 2);
                            } else {
                                $tfd->reimbursement_sst_amount = round($reimbursement_sst / $transferFeeDetails->count(), 2);
                            }
                            $totalDistributedReimbSst += $tfd->reimbursement_sst_amount;
                            
                            $tfd->updated_at = now();
                            $tfd->save();
                        }
                        
                        // Ensure last record gets remainder
                        if ($transferFeeDetails->count() > 1) {
                            $lastTfd = $transferFeeDetails->last();
                            $lastTfd->transfer_amount = round($totalPfee - ($totalDistributedPfee - $lastTfd->transfer_amount), 2);
                            $lastTfd->sst_amount = round($sst - ($totalDistributedSst - $lastTfd->sst_amount), 2);
                            $lastTfd->reimbursement_amount = round($reimbursement_amount - ($totalDistributedReimb - $lastTfd->reimbursement_amount), 2);
                            $lastTfd->reimbursement_sst_amount = round($reimbursement_sst - ($totalDistributedReimbSst - $lastTfd->reimbursement_sst_amount), 2);
                            $lastTfd->save();
                        }
                    }
                }
                
                // Recalculate transferred amounts in invoice
                $invoice->transferred_pfee_amt = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
                    ->where('status', '<>', 99)
                    ->sum('transfer_amount');
                $invoice->transferred_sst_amt = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
                    ->where('status', '<>', 99)
                    ->sum('sst_amount');
                $invoice->transferred_reimbursement_amt = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
                    ->where('status', '<>', 99)
                    ->sum('reimbursement_amount');
                $invoice->transferred_reimbursement_sst_amt = TransferFeeDetails::where('loan_case_invoice_main_id', $invoice->id)
                    ->where('status', '<>', 99)
                    ->sum('reimbursement_sst_amount');
                $invoice->save();
            }
        }

        // Update bill totals
        $total_pfee1 = $invoices->sum('pfee1_inv');
        $total_pfee2 = $invoices->sum('pfee2_inv');
        $total_sst = $invoices->sum('sst_inv');
        $total_reimbursement_amount = $invoices->sum('reimbursement_amount');
        $total_reimbursement_sst = $invoices->sum('reimbursement_sst');
        $total_amount = $invoices->sum('amount');

        // Update the bill record
        $bill->update([
            'pfee1_inv' => $total_pfee1,
            'pfee2_inv' => $total_pfee2,
            'sst_inv' => $total_sst,
            'reimbursement_amount' => $total_reimbursement_amount,
            'reimbursement_sst' => $total_reimbursement_sst,
            'total_amt_inv' => $total_amount,
            'total_amt' => $total_amount  // Update main total amount field
        ]);
    }

    /**
     * Calculate invoice amounts from details using the correct formula
     */
    private static function calculateInvoiceAmountsFromDetails($invoiceId, $sstRate)
    {
        $details = DB::table('loan_case_invoice_details as ild')
            ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
            ->where('ild.invoice_main_id', $invoiceId)
            ->where('ild.status', '<>', 99)
            ->select('ild.amount', 'ai.account_cat_id', 'ai.pfee1_item')
            ->get();

        $pfee1 = 0;
        $pfee2 = 0;
        $sst = 0;
        $reimbursement_amount = 0;
        $reimbursement_sst = 0;
        $total = 0;

        foreach ($details as $detail) {
            if ($detail->account_cat_id == 1) {
                // Calculate pfee1 and pfee2 for professional fees
                if ($detail->pfee1_item == 1) {
                    $pfee1 += $detail->amount;
                } else {
                    $pfee2 += $detail->amount;
                }

                // Calculate SST and total for account_cat_id == 1 (base amount + SST)
                $sst += $detail->amount * ($sstRate / 100);
                $total += $detail->amount * (($sstRate / 100) + 1);
            } elseif ($detail->account_cat_id == 4) {
                // Calculate reimbursement amounts for account_cat_id == 4
                $reimbursement_amount += $detail->amount;
                $reimbursement_sst += $detail->amount * ($sstRate / 100);
                $total += $detail->amount * (($sstRate / 100) + 1);
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

    public static function getPartyCount($id)
    {
        $party_count= 1;
        $party_count = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)
            ->count();

        if ($party_count <= 0)
        {
            $party_count= 1;
        }

        return $party_count;
    }

    public function generateNewInvNo($id, $invoice_main_id, $is_revert)
    {
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        $LoanCase = LoanCase::where('id', '=', $LoanCaseBillMain->case_id)->first();

        if ($LoanCase->branch_id == 2) {
            $running_no = $LoanCase->case_running_no;
            $newPuchong =  substr($running_no, 0, 1) === "7";

            if ($newPuchong == 1) {
                $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)
                    ->where('parameter_value_2', 'A')->first();

                if ($LoanCaseBillMain->sst_rate == 6) {
                    $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)
                        ->where('parameter_value_2', 'A')->first();
                } else if ($LoanCaseBillMain->sst_rate == 8) {
                    $parameter = Parameter::where('parameter_type', 'like', '%8_invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)
                        ->where('parameter_value_2', 'A')->first();
                }
            } else {
                if ($LoanCaseBillMain->sst_rate == 6) {
                    $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)->first();
                } else if ($LoanCaseBillMain->sst_rate == 8) {
                    $parameter = Parameter::where('parameter_type', 'like', '%8_invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)->first();
                }
            }
        } else {
            // $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)->first();

            if ($LoanCaseBillMain->sst_rate == 6) {
                $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)->first();
            } else if ($LoanCaseBillMain->sst_rate == 8) {
                $parameter = Parameter::where('parameter_type', 'like', '%8_invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)->first();
            }
        }

        $blnFound = 0;
        $breakCount = 0;

        $running_no = (int)$parameter->parameter_value_1;

        if ($is_revert == true) {
            $running_no = (int)$parameter->parameter_value_1;
            $running_no = $running_no - 1;
        } else {
            while ($blnFound == 0 && $breakCount < 20) {
                $running_no += 1;

                $LoanCaseInvoiceMainCheck = LoanCaseInvoiceMain::where('invoice_no', $parameter->parameter_value_2 . $running_no)->count();

                if ($LoanCaseInvoiceMainCheck == 0) {
                    $blnFound = 1;
                }

                $breakCount += 1;
            }

            $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', $invoice_main_id)->first();

            $LoanCaseInvoiceMain->invoice_no = $parameter->parameter_value_2 . $running_no;
            $LoanCaseInvoiceMain->save();
        }

        $parameter->parameter_value_1 = $running_no;
        $parameter->save();
    }


    public function getTransferFeeAddBillList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            $rows = DB::table('loan_case_bill_main as b')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->select('b.*', 'l.case_ref_no', 'c.name as client_name',)
                ->where('b.transferred_to_office_bank', '=',  0)
                ->where('b.status', '<>',  99)
                ->where('b.bln_invoice', '=',  1);

            if ($request->input('transfer_list')) {
                $transfer_list = json_decode($request->input('transfer_list'), true);
                $rows = $rows->whereIn('b.id', $transfer_list);
            }

            if (in_array($current_user->menuroles, ['maker'])) {
                if ($current_user->branch_id == 3) {
                    $rows = $rows->where('l.branch_id', '=',  3);
                }
            }



            $rows = $rows->orderBy('b.id', 'ASC')->get();

            return DataTables::of($rows)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv + $data->sst_inv;
                    $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                    <input type="checkbox" name="add_bill" value="' . $data->id . '" id="add_chk_' . $data->id . '" >
                    <label for="add_chk_' . $data->id . '" ></label>
                    </div> 
                    <input id="selected_amt_' . $data->id . '" type="hidden" value="' . $bal_to_transfer . '" />';
                    return $actionBtn;
                })
                ->editColumn('case_ref_no', function ($data) {

                    // $status_lbl = '';
                    // if ($data->status === '2')
                    //     $status_lbl = '<span class="label bg-info">Open</span>';
                    // elseif ($data->status === '0')
                    //     $status_lbl = '<span class="label bg-success">Closed</span>';
                    // elseif ($data->status === '1')
                    //     $status_lbl = '<span class="label bg-purple">Running</span>';
                    // elseif ($data->status === '3')
                    //     $status_lbl = '<span class="label bg-warning">KIV</span>';
                    // elseif ($data->status === '99')
                    //     $status_lbl = '<span class="label bg-danger">Aborted</span>';
                    // else
                    //     $status_lbl = '<span class="label bg-danger">Overdue</span>'; 


                    return $data->client_name . '<br/><b>Ref No: </b><a href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a> ';
                })
                ->addColumn('pfee_sum', function ($data) {
                    $pfee_sum = $data->pfee1_inv + $data->pfee2_inv;
                    return $pfee_sum;
                })
                ->addColumn('bal_to_transfer_v2', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;


                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv  - $data->transferred_pfee_amt;
                    $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv  - $data->transferred_pfee_amt;
                    $sst_to_transfer = $data->sst_inv  - $data->transferred_sst_amt;
                    $actionBtn = '<input class="bal_to_transfer" onchange="balUpdate()" type="number" id="ban_to_transfer' . $data->id  . '" value = "' . $bal_to_transfer . '" />
                    <a href="javascript:void(0)" onclick="maxValue(' . $data->id  . ', ' . $bal_to_transfer . ')" class="btn btn-info btn-xs">Max</a>
                    <input type="hidden" id="ban_to_transfer_limt_' . $data->id  . '" value = "' . $bal_to_transfer . '" />
                    <input type="hidden" id="sst_to_transfer_' . $data->id  . '" value = "' . $sst_to_transfer . '" />
                    ';
                    return $actionBtn;
                })
                ->addColumn('sst_to_transfer', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;


                    $sst_to_transfer = $data->sst_inv  - $data->transferred_sst_amt;
                    return $sst_to_transfer;
                })
                ->editColumn('client_profile_completed', function ($row) {
                    return $row->client_profile_completed == 1 ? '<span class="label label-success">Completed</span>' : '<span class="label label-warning">Pending</span>';
                })
                ->addColumn('bal_to_transfer', function ($data) {
                    $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv + $data->sst_inv;
                    return $bal_to_transfer;
                })

                ->editColumn('invoice_date', function ($data) {
                    $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->invoice_date)->format('d-m-Y');
                    return $formatedDate;
                })
                ->rawColumns(['action', 'bal_to_transfer', 'voucher_type', 'transaction_type', 'client_profile_completed', 'case_ref_no', 'bal_to_transfer_v2', 'pfee_sum', 'sst_to_transfer'])
                ->make(true);
        }
    }

    public function einvoice_billto()
    {
        return view('dashboard.einvoice-client.index');
    }

    public function getEInvoiceClientList()
    {
        $current_user = auth()->user();
        $customer_id_list = [];
        $BillingParties = DB::table('invoice_billing_party as ibp')
            ->leftJoin('loan_case_bill_main as lcbm', 'lcbm.id', '=', 'ibp.loan_case_main_bill_id')
            ->leftJoin('loan_case as lc', 'lc.id', '=', 'ibp.case_id')
            ->select(
                'ibp.id',
                'ibp.customer_code',
                'ibp.loan_case_main_bill_id',
                'ibp.case_id',
                'ibp.customer_name',
                'ibp.brn',
                'ibp.brn2',
                'ibp.sales_tax_no',
                'ibp.service_tax_no',
                'ibp.customer_category',
                'ibp.id_no',
                'ibp.tin',
                'ibp.address_1',
                'ibp.address_2',
                'ibp.address_3',
                'ibp.address_4',
                'ibp.postcode',
                'ibp.city',
                'ibp.state',
                'ibp.country',
                'ibp.phone1',
                'ibp.mobile',
                'ibp.fax1',
                'ibp.fax2',
                'ibp.email',
                'ibp.completed',
                'ibp.sent_to_sql',
                'ibp.created_at',
                'lc.case_ref_no' // Include case reference number if needed
            );

        if (!in_array($current_user->menuroles,  ['admin', 'management', 'account'])) {

            $ids = CaseController::getCaseListHub('array', 'customer_id');
            $customer_id_list = array_merge($customer_id_list, $ids);

            $BillingParties = $BillingParties->whereIn('ibp.id', $customer_id_list);
        }


        // else if (in_array($current_user->menuroles,  ['lawyer'])) {

        // return $customer_id_list;


        //     if (in_array($current_user->id,  [118]))
        //     {
        //         $Customer = $Customer->whereIn('l.sales_user_id', [$current_user->id, 32]);
        //     }
        //     else
        //     {
        //         $Customer = $Customer->where('l.sales_user_id', '=', $current_user->id);
        //     }
        // }


        $BillingParties = $BillingParties->get();

        if (count($BillingParties) > 0) {
            for ($i = 0; $i < count($BillingParties); $i++) {
                $LoanCaseCount = LoanCase::where('status', '<>', 99)->where('customer_id', '=', $BillingParties[$i]->id)->count();
                $BillingParties[$i]->case_count = $LoanCaseCount;
            }
        }

        return DataTables::of($BillingParties)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $actionBtn = ' <a  href="/einvoice-client-edit/' . $row->id . '"  class="btn btn-info shadow sharp mr-1" title="Edit"><i class="cil-pencil"></i></a>';
                return $actionBtn;
            })
            ->editColumn('case_id', function ($row) {
                return '<a href="/case/' . $row->case_id . '">' . $row->case_ref_no . '</a>';
            })
            ->editColumn('completed', function ($row) {
                return $row->completed == 1 ? '<span class="label label-success">Completed</span>' : '<span class="label label-warning">Pending</span>';
            })
            ->editColumn('sent_to_sql', function ($row) {
                return $row->sent_to_sql == 1 ? '<span class="label label-success">Sent</span>' : '<span class="label label-warning">Pending</span>';
            })
            ->editColumn('customer_category', function ($row) {
                if ($row->customer_category == 1) return 'Individual';
                if ($row->customer_category == 2) return 'Company';
                return '';
            })
            ->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i:s');
            })
            ->rawColumns(['action', 'case_id', 'completed', 'sent_to_sql'])
            ->make(true);
    }

    public function einvoiceBilltoEdit($id)
    {
        $current_user = auth()->user();

        // Retrieve billing party record from invoice_billing_party table
        $billingParty = InvoiceBillingParty::where('id', $id)->first();

        if (!$billingParty) {
            abort(404, 'Billing party not found');
        }

        // Retrieve LoanCase records associated with the case_id from the billing party
        $LoanCase = DB::table('loan_case as l')
            ->leftJoin('users as u1', 'u1.id', '=', 'l.lawyer_id')
            ->leftJoin('users as u2', 'u2.id', '=', 'l.clerk_id')
            ->select('l.*', 'u1.name as lawyer', 'u2.name as clerk')
            ->where('l.id', '=', $billingParty->case_id) // Filter by case_id from billing party
            ->get();


        return view('dashboard.einvoice-client.edit', ['billingParty' => $billingParty, 'LoanCase' => $LoanCase]);
    }

    public function generateClientLink(Request $request, $id)
    {
        try {
            $billingParty = InvoiceBillingParty::find($id);

            if (!$billingParty) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Billing party not found'
                ], 404);
            }

            // Generate token if it doesn't exist
            if (empty($billingParty->token)) {
                $billingParty->token = $this->generateUniqueToken();
                $billingParty->save();
            }

            // Generate the client link
            $clientLink = url('/client-einvoice-data/' . $billingParty->token);

            return response()->json([
                'status' => 1,
                'message' => 'Client link generated successfully',
                'client_link' => $clientLink,
                'token' => $billingParty->token
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error generating client link: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateUniqueToken()
    {
        do {
            $token = bin2hex(random_bytes(32)); // Generate 64 character hex string
        } while (InvoiceBillingParty::where('token', $token)->exists());

        return $token;
    }

    public function clientEinvoiceData($token)
    {
        try {
            // Find the billing party by token
            $billingParty = InvoiceBillingParty::where('token', $token)->first();

            if (!$billingParty) {
                abort(404, 'Invalid or expired link');
            }

            // Get related case information
            $case = DB::table('loan_case as l')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->select('l.*', 'c.name as client_name')
                ->where('l.id', '=', $billingParty->case_id)
                ->first();

            // Get invoice information
            $invoice = null;
            if ($billingParty->invoice_main_id) {
                $invoice = DB::table('loan_case_invoice_main as im')
                    ->leftJoin('loan_case_bill_main as bm', 'bm.id', '=', 'im.loan_case_main_bill_id')
                    ->select('im.*', 'bm.invoice_date', 'bm.total_amt_inv')
                    ->where('im.id', '=', $billingParty->invoice_main_id)
                    ->first();
            }

            return view('dashboard.einvoice-client.client-view', [
                'billingParty' => $billingParty,
                'case' => $case,
                'invoice' => $invoice
            ]);

        } catch (\Exception $e) {
            abort(500, 'Error accessing e-invoice data');
        }
    }

    public function updateClientEinvoiceData(Request $request, $token)
    {
        try {
            // Find the billing party by token
            $billingParty = InvoiceBillingParty::where('token', $token)->first();

            if (!$billingParty) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Invalid or expired link'
                ], 404);
            }

            // Update all fields
            $billingParty->customer_category = $request->input('customer_category');
            $billingParty->tin = $request->input('tin');
            $billingParty->brn = $request->input('brn');
            $billingParty->brn2 = $request->input('brn2');
            $billingParty->sales_tax_no = $request->input('sales_tax_no');
            $billingParty->service_tax_no = $request->input('service_tax_no');
            $billingParty->id_no = $request->input('id_no');
            $billingParty->address_1 = $request->input('address_1');
            $billingParty->address_2 = $request->input('address_2');
            $billingParty->address_3 = $request->input('address_3');
            $billingParty->address_4 = $request->input('address_4');
            $billingParty->postcode = $request->input('postcode');
            $billingParty->city = $request->input('city');
            $billingParty->state = $request->input('state');
            $billingParty->country = $request->input('country');
            $billingParty->phone1 = $request->input('phone1');
            $billingParty->mobile = $request->input('mobile');
            $billingParty->fax1 = $request->input('fax1');
            $billingParty->email = $request->input('email');

            // Check mandatory fields
            $mandatoryFields = self::getEinvoiceCustomerMandatoryField();
            $allMandatoryFieldsFilled = true;

            foreach ($mandatoryFields as $field) {
                if (empty($billingParty->$field)) {
                    $allMandatoryFieldsFilled = false;
                    break;
                }
            }

            // Update completed status
            $billingParty->completed = $allMandatoryFieldsFilled ? 1 : 0;
            $billingParty->save();

            // Update related EInvoiceDetails and EInvoiceMain if needed
            if ($billingParty->loan_case_main_bill_id) {
                $EInvoiceDetails = EInvoiceDetails::where('loan_case_main_bill_id', $billingParty->loan_case_main_bill_id)->first();
                if ($EInvoiceDetails) {
                    $EInvoiceDetails->client_profile_completed = $allMandatoryFieldsFilled ? 1 : 0;
                    $EInvoiceDetails->save();

                    // Check all related EInvoiceDetails for this einvoice_main
                    $EInvoiceDetailsAll = EInvoiceDetails::where('einvoice_main_id', $EInvoiceDetails->einvoice_main_id)->get();
                    $allRelatedCompleted = true;
                    foreach ($EInvoiceDetailsAll as $relatedDetail) {
                        if ($relatedDetail->client_profile_completed == 0) {
                            $allRelatedCompleted = false;
                            break;
                        }
                    }

                    EInvoiceMain::where('id', $EInvoiceDetails->einvoice_main_id)->update(['client_profile_completed' => $allRelatedCompleted]);
                }
            }

            return response()->json([
                'status' => 1,
                'message' => 'Information updated successfully',
                'completed' => $allMandatoryFieldsFilled
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error updating information: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sstMainList()
    {
        $current_user = auth()->user();

        if (AccessController::UserAccessPermissionController(PermissionController::SSTPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $branchInfo = BranchController::manageBranchAccess();

        if (in_array($current_user->menuroles, ['maker'])) {
            if ($current_user->branch_id == 3) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 3)->get();
            } else if ($current_user->branch_id == 5) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 3)->get();
            }
        } else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }


        $SSTMain = SSTMain::where('status', '=', 1)->get();
        return view('dashboard.sst.index', [
            'SSTMain' => $SSTMain,
            'current_user' => $current_user,
            'Branchs' => $branchInfo['branch'],
        ]);
    }

    public function getSSTMainList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $branchInfo = BranchController::manageBranchAccess();
            // $TransferFeeMain = TransferFeeMain::where('status', '=', 1)->get();


            $sstMain = DB::table('sst_main as m')
                ->leftJoin('users as u', 'u.id', '=', 'm.paid_by')
                ->leftJoin('branch as b', 'b.id', '=', 'm.branch_id')
                ->select('m.*', 'u.name as paid_user', 'b.name as branch_name')
                ->where('m.status', '<>',  99);

            if ($request->input("transfer_date_from") <> null && $request->input("transfer_date_to") <> null) {
                $sstMain = $sstMain->whereBetween('m.payment_date', [$request->input("transfer_date_from"), $request->input("transfer_date_to")]);
            } else {
                if ($request->input("transfer_date_from") <> null) {
                    $sstMain = $sstMain->where('m.payment_date', '>=', $request->input("transfer_date_from"));
                }

                if ($request->input("transfer_date_to") <> null) {
                    $sstMain = $sstMain->where('m.payment_date', '<=', $request->input("transfer_date_to"));
                }
            }

            if ($request->input("branch_id")) {
                $sstMain = $sstMain->where('m.branch_id', '=',  $request->input("branch_id"));
            }

            if (in_array($current_user->menuroles, ['maker'])) {
                $sstMain = $sstMain->where('m.branch_id', '=',  $current_user->branch_id);
            } else if (in_array($current_user->id, [51, 32])) {
                $sstMain = $sstMain->whereIn('m.branch_id', [5, 6]);
            }
            if (in_array($current_user->menuroles, ['lawyer'])) {
                $sstMain = $sstMain->where('m.branch_id', '=',  $current_user->branch_id);
            }


            $sstMain = $sstMain->OrderBy('payment_date', 'desc')->get();


            return DataTables::of($sstMain)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $actionBtn = '
                    <a href="/sst/' . $data->id . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="edit"><i class="cil-pencil"></i></a>
                    ';
                    return $actionBtn;
                })
                // ->addColumn('transfer_from_bank', function ($data) {
                //     $actionBtn = $data->transfer_from_bank . '<br/>(' . $data->transfer_from_bank_acc_no . ')';
                //     return $actionBtn;
                // })
                // ->addColumn('transfer_to_bank', function ($data) {
                //     $actionBtn = $data->transfer_to_bank . '<br/>(' . $data->transfer_to_bank_acc_no . ')';
                //     return $actionBtn;
                // })
                ->rawColumns(['action', 'bal_to_transfer', 'transfer_from_bank', 'transfer_to_bank', 'transferred_to_office_bank', 'case_ref_no'])
                ->make(true);
        }
    }

    public function getInvoiceList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            $rows = DB::table('loan_case_bill_main as b')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->select('b.*', 'l.case_ref_no', 'c.name as client_name',)
                ->where('b.status', '<>',  99)
                ->where('b.bln_invoice', '=',  1);

            if ($request->input('type') == 'transferred') {

                // $rows = $rows->where('b.transferred_to_office_bank', '=',  $request->input('transfer_list'));


                $transferred_list = [];

                $SSTDetails = SSTDetails::where('sst_main_id', '=', $request->input('transaction_id'))->get();

                for ($i = 0; $i < count($SSTDetails); $i++) {
                    array_push($transferred_list, $SSTDetails[$i]->loan_case_main_bill_id);
                }


                if ($request->input('transaction_id')) {
                    $rows = $rows->whereIn('b.id', $transferred_list);
                    // $rows = $rows->whereIn('b.id',[15,17]);
                }
            } else {

                if ($request->input('type') == 'add') {
                    if ($request->input('transfer_list')) {
                        $transfer_list = json_decode($request->input('transfer_list'), true);
                        $rows = $rows->whereIn('b.id', $transfer_list);
                    }
                } else {
                    if ($request->input('transfer_list')) {
                        $transfer_list = json_decode($request->input('transfer_list'), true);
                        $rows = $rows->whereNotIn('b.id', $transfer_list);
                        $rows = $rows->where('b.bln_sst', '=',  0);
                    }
                }
            }

            if ($request->input("recv_start_date") <> null && $request->input("recv_end_date") <> null) {
                $rows = $rows->whereBetween('b.payment_receipt_date', [$request->input("recv_start_date"), $request->input("recv_end_date")]);
            } else {
                if ($request->input("date_from") <> null) {
                    $rows = $rows->where('b.payment_receipt_date', '>=', $request->input("recv_start_date"));
                }

                if ($request->input("date_to") <> null) {
                    $rows = $rows->where('b.payment_receipt_date', '<=', $request->input("recv_end_date"));
                }
            }

            if ($request->input('branch')) {
                // $rows = $rows->where('l.branch_id', '=', $request->input("branch"));
                $rows = $rows->where('b.invoice_branch_id', '=', $request->input("branch"));
            }

            // if (in_array($current_user->menuroles, ['maker'])) {
            //     if ($current_user->branch_id == 3) {
            //         $rows = $rows->where('l.branch_id', '=',  3);
            //     } else if ($current_user->branch_id == 5) {
            //         $rows = $rows->where('l.branch_id', '=',  5);
            //     }
            // }

            if (in_array($current_user->menuroles, ['maker'])) {
                if (in_array($current_user->branch_id, [5, 6])) {
                    $rows = $rows->whereIn('l.branch_id', [5, 6]);
                } else if (in_array($current_user->branch_id, [2])) {
                    $rows = $rows->whereIn('l.sales_user_id', [13]);
                } else {
                    $rows = $rows->whereIn('l.branch_id', [$current_user->branch_id]);
                }
            } else  if (in_array($current_user->menuroles, ['lawyer'])) {
                if (in_array($current_user->id, [13])) {
                    $rows = $rows->whereIn('l.branch_id', [$current_user->branch_id])->where('l.id', '>=', 2342);
                } else {
                    $rows = $rows->whereIn('l.branch_id', [$current_user->branch_id]);
                }
            }

            $rows = $rows->orderBy('b.id', 'ASC')->get();

            return DataTables::of($rows)
                ->addIndexColumn()
                ->addColumn('action', function ($data) use ($request) {
                    if ($request->input('type') == 'transferred') {
                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="checkbox" name="trans_bill" value="' . $data->id . '" id="trans_chk_' . $data->id . '" >
                        <label for="trans_chk_' . $data->id . '"></label>
                        </div> ';
                    } else {
                        $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                        <input type="checkbox" name="bill" value="' . $data->id . '" id="chk_' . $data->id . '" >
                        <label for="chk_' . $data->id . '"></label>
                        </div> ';
                    }

                    return $actionBtn;
                })
                ->editColumn('case_ref_no', function ($data) {

                    // $status_lbl = '';
                    // if ($data->status === '2')
                    //     $status_lbl = '<span class="label bg-info">Open</span>';
                    // elseif ($data->status === '0')
                    //     $status_lbl = '<span class="label bg-success">Closed</span>';
                    // elseif ($data->status === '1')
                    //     $status_lbl = '<span class="label bg-purple">Running</span>';
                    // elseif ($data->status === '3')
                    //     $status_lbl = '<span class="label bg-warning">KIV</span>';
                    // elseif ($data->status === '99')
                    //     $status_lbl = '<span class="label bg-danger">Aborted</span>';
                    // else
                    //     $status_lbl = '<span class="label bg-danger">Overdue</span>'; 


                    return '<a target="_blank" href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a> ';
                })
                ->addColumn('bal_to_transfer', function ($data) {
                    $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv + $data->sst_inv;
                    return $bal_to_transfer;
                })
                ->addColumn('transferred_to_office_bank', function ($data) {


                    if ($data->transferred_to_office_bank == 1)
                        return '<span class="label bg-success">Yes</span>';
                    else
                        return '<span class="label bg-warning">No</span>';
                })
                ->rawColumns(['action', 'bal_to_transfer', 'voucher_type', 'transaction_type', 'transferred_to_office_bank', 'case_ref_no'])
                ->make(true);
        }
    }

    public function getInvoiceAddList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            $rows = DB::table('loan_case_bill_main as b')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->select('b.*', 'l.case_ref_no', 'c.name as client_name',)
                // ->where('b.transferred_to_office_bank', '=',  0)
                ->where('b.status', '<>',  99)
                ->where('b.bln_invoice', '=',  1);

            if ($request->input('transfer_list')) {
                $transfer_list = json_decode($request->input('transfer_list'), true);
                $rows = $rows->whereIn('b.id', $transfer_list);
            }

            if (in_array($current_user->menuroles, ['maker'])) {
                if (in_array($current_user->branch_id, [5, 6])) {
                    $rows = $rows->whereIn('l.branch_id', [5, 6]);
                } else {
                    $rows = $rows->whereIn('l.branch_id', [$current_user->branch_id]);
                }
            } else  if (in_array($current_user->menuroles, ['lawyer'])) {
                $rows = $rows->whereIn('l.branch_id', [$current_user->branch_id]);
            }



            $rows = $rows->orderBy('b.id', 'ASC')->get();

            return DataTables::of($rows)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv + $data->sst_inv;
                    $actionBtn = ' <div class="checkbox  bulk-edit-mode" >
                    <input type="checkbox" name="add_bill" value="' . $data->id . '" id="add_chk_' . $data->id . '" >
                    <label for="add_chk_' . $data->id . '" ></label>
                    </div> 
                    <input id="selected_amt_' . $data->id . '" type="hidden" value="' . $data->sst_inv . '" />';
                    return $actionBtn;
                })
                ->editColumn('case_ref_no', function ($data) {

                    // $status_lbl = '';
                    // if ($data->status === '2')
                    //     $status_lbl = '<span class="label bg-info">Open</span>';
                    // elseif ($data->status === '0')
                    //     $status_lbl = '<span class="label bg-success">Closed</span>';
                    // elseif ($data->status === '1')
                    //     $status_lbl = '<span class="label bg-purple">Running</span>';
                    // elseif ($data->status === '3')
                    //     $status_lbl = '<span class="label bg-warning">KIV</span>';
                    // elseif ($data->status === '99')
                    //     $status_lbl = '<span class="label bg-danger">Aborted</span>';
                    // else
                    //     $status_lbl = '<span class="label bg-danger">Overdue</span>';


                    return '<a href="/case/' . $data->id . '">' . $data->case_ref_no . '</a> ';
                })

                ->editColumn('client_profile_completed', function ($row) {
                    return $row->client_profile_completed == 1 ? '<span class="label label-success">Completed</span>' : '<span class="label label-warning">Pending</span>';
                })
                ->addColumn('bal_to_transfer', function ($data) {
                    $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv + $data->sst_inv;
                    return $bal_to_transfer;
                })
                ->rawColumns(['action', 'bal_to_transfer', 'voucher_type', 'transaction_type', 'client_profile_completed', 'case_ref_no'])
                ->make(true);
        }
    }

    public function SSTRecordCreate()
    {
        $current_user = auth()->user();
        $branchInfo = BranchController::manageBranchAccess();

        // if (!in_array($current_user->menuroles, ['admin', 'account', 'maker'])) {
        //     return redirect()->route('dashboard.index');
        // }

        // if (AccessController::UserAccessController($this->getSSTAccessCode()) == false) {
        //     return redirect()->route('dashboard.index');
        // }

        if (AccessController::UserAccessPermissionController(PermissionController::SSTPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $Branchs = Branch::where('status', '=', 1)->get();


        if (in_array($current_user->menuroles, ['maker'])) {
            if ($current_user->branch_id == 3) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 3)->get();
            } else if ($current_user->branch_id == 5) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 5)->get();
            } else if ($current_user->branch_id == 2) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 2)->get();
            }
        } else if (in_array($current_user->menuroles, ['lawyer'])) {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
        } else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }

        return view('dashboard.sst.create', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'Branchs' => $branchInfo['branch'],
        ]);
    }

    public function createNewSSTRecord(Request $request)
    {
        $current_user = auth()->user();
        $total_amount = 0;

        $SSTMain  = new SSTMain();

        // $TransferFeeMain->transfer_amount = $request->input("transfer_amount");
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
                $SSTDetails  = new SSTDetails();

                $total_amount += $add_bill[$i]['value'];

                $SSTDetails->sst_main_id = $SSTMain->id;
                $SSTDetails->loan_case_main_bill_id = $add_bill[$i]['id'];
                $SSTDetails->created_by = $current_user->id;
                $SSTDetails->amount = $add_bill[$i]['value'];
                $SSTDetails->status = 1;
                $SSTDetails->created_at = date('Y-m-d H:i:s');

                $SSTDetails->save();

                LoanCaseBillMain::where('id', '=', $add_bill[$i]['id'])->update(['bln_sst' => 1]);
            }

            $SSTMain->amount = $total_amount;
            $SSTMain->save();
        }

        return response()->json(['status' => 1, 'data' => 'success']);
    }

    public function sstView($id)
    {
        $current_user = auth()->user();
        $SSTMain = SSTMain::where('id', '=', $id)->first();

        if (AccessController::UserAccessPermissionController(PermissionController::SSTPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        // $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        $branchInfo = BranchController::manageBranchAccess();

        if (in_array($current_user->menuroles, ['maker'])) {
            if ($current_user->branch_id == 3) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 3)->get();
            } else if ($current_user->branch_id == 5) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 3)->get();
            }
        } else {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        }

        $Branchs = Branch::where('status', '=', 1)->get();
        return view('dashboard.sst.edit', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'SSTMain' => $SSTMain,
            'Branchs' => $branchInfo['branch'],
        ]);
    }


    public function updateSST(Request $request, $id)
    {
        $current_user = auth()->user();
        $total_amount = 0;

        $SSTMain  = SSTMain::where('id', '=', $id)->first();

        $SSTMain->payment_date = $request->input("payment_date");
        $SSTMain->updated_by = $current_user->id;
        $SSTMain->transaction_id = $request->input("trx_id");
        $SSTMain->remark = $request->input("remark");
        $SSTMain->status = 1;
        $SSTMain->updated_at = date('Y-m-d H:i:s');

        $SSTMain->save();

        if ($request->input('add_bill') != null) {
            $add_bill = json_decode($request->input('add_bill'), true);
        }

        $SSTDetails  = SSTDetails::where('sst_main_id', '=', $id)->get();

        if (count($SSTDetails) > 0) {
            for ($i = 0; $i < count($SSTDetails); $i++) {

                $LoanCaseBillMain  = LoanCaseBillMain::where('id', '=', $SSTDetails[$i]['loan_case_main_bill_id'])->first();

                $transfer_amount = $LoanCaseBillMain->sst_inv;

                $total_amount += $transfer_amount;
                $SSTDetails[$i]['amount'] = $transfer_amount;
                $SSTDetails[$i]->save();
                // $total_amount += $TransferFeeDetails[$i]['transfer_amount'];
            }
        }

        if (count($add_bill) > 0) {

            for ($i = 0; $i < count($add_bill); $i++) {
                $SSTDetails  = new SSTDetails();

                $total_amount += $add_bill[$i]['value'];

                $SSTDetails->sst_main_id = $SSTMain->id;
                $SSTDetails->loan_case_main_bill_id = $add_bill[$i]['id'];
                $SSTDetails->case_id = $add_bill[$i]['id'];
                $SSTDetails->created_by = $current_user->id;
                $SSTDetails->amount = $add_bill[$i]['value'];
                $SSTDetails->status = 1;
                $SSTDetails->created_at = date('Y-m-d H:i:s');

                $SSTDetails->save();

                LoanCaseBillMain::where('id', '=', $add_bill[$i]['id'])->update(['bln_sst' => 1]);
            }
        }

        $SSTMain->amount = $total_amount;
        $SSTMain->save();

        return response()->json(['status' => 1, 'data' => 'success', 'total_amount' => $total_amount]);
    }

    public function deleteSST(Request $request, $id)
    {
        $total_amount = 0;
        $current_user = auth()->user();

        if ($request->input('delete_bill') != null) {
            $delete_bill = json_decode($request->input('delete_bill'), true);
        }

        if (count($delete_bill) > 0) {
            for ($i = 0; $i < count($delete_bill); $i++) {

                $SSTDetails  = SSTDetails::where('loan_case_main_bill_id', '=', $delete_bill[$i]['id'])->first();

                if ($SSTDetails) {
                    $SSTDetailsDelete  = new SSTDetailsDelete();

                    $SSTDetailsDelete->sst_main_id = $SSTDetails->sst_main_id;
                    $SSTDetailsDelete->loan_case_main_bill_id = $SSTDetails->loan_case_main_bill_id;
                    $SSTDetailsDelete->created_by = $current_user->id;
                    $SSTDetailsDelete->amount = $SSTDetails->amount;
                    $SSTDetailsDelete->status = 1;
                    $SSTDetailsDelete->created_at = date('Y-m-d H:i:s');

                    $SSTDetailsDelete->save();

                    $SSTDetails->delete();

                    LoanCaseBillMain::where('id', '=', $SSTDetails->loan_case_main_bill_id)->update(['bln_sst' => 0]);
                }
            }


            $SSTMain  = SSTMain::where('id', '=', $id)->first();
            $SSTDetails  = SSTDetails::where('sst_main_id', '=', $id)->get();

            if (count($SSTDetails) > 0) {
                for ($i = 0; $i < count($SSTDetails); $i++) {
                    $total_amount += $SSTDetails[$i]['amount'];
                }

                $SSTMain->amount = $total_amount;
                $SSTMain->save();
            }
        }

        return response()->json(['status' => 1, 'data' => 'success']);
    }


    function view($id)
    {
        $account_template_cat = DB::table('voucher')
            ->join('loan_case', 'loan_case.id', '=', 'voucher.case_id')
            ->join('loan_case_account', 'loan_case_account.id', '=', 'voucher.account_details_id')
            ->join('users', 'users.id', '=', 'voucher.user_id')
            ->select('voucher.*', 'loan_case.case_ref_no', 'loan_case_account.item_name', 'users.name')
            ->where('voucher.id', '=', $id)
            ->first();

        return response()->json(['status' => 1, 'data' => $account_template_cat]);
    }

    public function create()
    {
        $account_category = AccountCategory::all();

        return view('dashboard.account.create', [
            'account_category' => $account_category
        ]);
    }

    public function store(Request $request)
    {

        $account  = new Account();

        $account->code = $request->input('code');
        $account->name = $request->input('name');
        $account->account_category_id = $request->input('account_category_id');
        $account->approval = $request->input('approval');
        $account->remark = $request->input('remark');
        $account->status =  $request->input('status');
        $account->created_at = date('Y-m-d H:i:s');

        $account->save();

        $request->session()->flash('message', 'Successfully created new account');
        return redirect()->route('account.index');
    }

    public function edit($id)
    {
        $account = Account::where('id', '=', $id)->first();
        $account_category = AccountCategory::where('status', '=', 1)->get();

        return view('dashboard.account.edit', [
            'account_category' => $account_category,
            'account' => $account
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        // $caseTemplateDetail = CaseTemplateDetails::all();

        $docTemplateDetailSelected = DocumentTemplateDetails::where('document_template_main_id', '=', $id)->where('status', '=', 1)->get();
        $caseMasterListCategory = CaseMasterListCategory::all();
        $caseMasterListField = CaseMasterListField::all();

        $docTemplatePages = DB::table('document_template_pages')
            ->leftJoin('users', 'users.id', '=', 'document_template_pages.is_locked')
            ->select('document_template_pages.*', 'users.name')
            ->get();

        $current_user = auth()->user();

        // $docTemplatePage = DocumentTemplatePages::where('document_template_details_id', '=', $docTemplateDetailSelected[0]->id)->get();
        $docTemplateDetail = DocumentTemplateDetails::where('document_template_main_id', '=', $id)->get();
        $docTemplateMain = DocumentTemplateMain::where('id', '=', $id)->get();
        return view('dashboard.documentTemplate.show', [
            'template' => DocumentTemplateMain::where('id', '=', $id)->first(),
            'docTemplatePages' => $docTemplatePages,
            'docTemplateDetail' => $docTemplateDetail,
            'docTemplateMain' => $docTemplateMain,
            'caseMasterListField' => $caseMasterListField,
            'caseMasterListCategory' => $caseMasterListCategory
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MenuLangList  $menuLangList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // $validatedData = $request->validate([
        //     'name'             => 'required|min:1|max:64',
        //     'shortName'        => 'required|min:1|max:64',
        //     'is_default'       => 'required|in:true,false'
        // ]);
        $status = 1;
        $message = '';
        $current_user = auth()->user();

        try {
            $voucher = Voucher::where('id', '=',  $request->input('voucher_id'))->first();

            $voucher->remark = $request->input('remarks');
            $voucher->status = $request->input('status');
            $voucher->approval_id = $current_user->id;
            $voucher->updated_at = date('Y-m-d H:i:s');
            $voucher->save();
            $message = 'Voucher approved';

            // if voucher rejected
            if ($request->input('status') == 2) {
                $loanCaseAccount = LoanCaseAccount::where('id', '=', $voucher->account_details_id)->first();
                $loanCaseAccount->amount = $loanCaseAccount->amount + $voucher->amount;
                $loanCaseAccount->updated_at = date('Y-m-d H:i:s');
                $loanCaseAccount->save();
                $message = 'Voucher rejected';

                $caseAccountTransaction = new CaseAccountTransaction();

                $caseAccountTransaction->case_id = $voucher->case_id;
                $caseAccountTransaction->account_details_id = $voucher->account_details_id;
                $caseAccountTransaction->debit = $voucher->amount;
                $caseAccountTransaction->credit = 0;
                $caseAccountTransaction->remark = 'rejected_knockoff';
                $caseAccountTransaction->status = 1;
                $caseAccountTransaction->created_at = date('Y-m-d H:i:s');
                $caseAccountTransaction->save();
            }
        } catch (\Throwable $e) {
            $status = 0;
            $message = $e;
        }

        return response()->json(['status' => $status, 'data' => $message]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MenuLangList  $menuLangList
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request) {}

    public function sendInvoicesToSQL(Request $request)
    {
        try {
            $invoiceIds = json_decode($request->input('invoice_ids'), true);
            $einvoiceMainId = $request->input('einvoice_main_id');

            if (empty($invoiceIds)) {
                return response()->json(['status' => 0, 'message' => 'No invoice IDs provided.'], 400);
            }

            // Update einvoice_status for the selected invoices in einvoice_details table
            EInvoiceDetails::whereIn('loan_case_invoice_id', $invoiceIds)
                ->where('einvoice_main_id', $einvoiceMainId)
                ->update(['einvoice_status' => 'SQL']);

            // Update sent_to_sql in invoice_billing_party
            InvoiceBillingParty::whereIn('loan_case_main_bill_id', $invoiceIds)
                ->update(['sent_to_sql' => 1]);

            // Optionally, check if all associated einvoice_details for this einvoice_main are 'SQL' and update einvoice_main batch_status
            $allDetailsSent = EInvoiceDetails::where('einvoice_main_id', $einvoiceMainId)->where('einvoice_status', '<>', 'SQL')->doesntExist();

            if ($allDetailsSent) {
                EInvoiceMain::where('id', $einvoiceMainId)->update(['batch_status' => 'SQL']);
            }

            return response()->json(['status' => 1, 'message' => 'Invoices status updated successfully to SQL.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Error updating invoice status: ' . $e->getMessage()], 500);
        }
    }

    public function sendInvoicesToLHDN(Request $request)
    {
        try {
            $invoiceIds = json_decode($request->input('invoice_ids'), true);
            $einvoiceMainId = $request->input('einvoice_main_id');

            if (empty($invoiceIds)) {
                return response()->json(['status' => 0, 'message' => 'No invoice IDs provided.'], 400);
            }

            // Update einvoice_status for the selected invoices in einvoice_details table
            EInvoiceDetails::whereIn('loan_case_invoice_id', $invoiceIds)
                ->where('einvoice_main_id', $einvoiceMainId)
                ->update(['einvoice_status' => 'LHDN']);

            // Optionally, check if all associated einvoice_details for this einvoice_main are 'LHDN' and update einvoice_main batch_status
            $allDetailsSent = EInvoiceDetails::where('einvoice_main_id', $einvoiceMainId)->where('einvoice_status', '<>', 'LHDN')->doesntExist();

            if ($allDetailsSent) {
                EInvoiceMain::where('id', $einvoiceMainId)->update(['batch_status' => 'LHDN']);
            }

            return response()->json(['status' => 1, 'message' => 'Invoices status updated successfully to LHDN.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Error updating invoice status: ' . $e->getMessage()], 500);
        }
    }

    public function updateEinvoice(Request $request, $id)
    {
        try {
            $eInvoiceMain = EInvoiceMain::find($id);

            if (!$eInvoiceMain) {
                return response()->json(['status' => 0, 'message' => 'E-Invoice record not found.'], 404);
            }

            // Update fields from the request
            $eInvoiceMain->einvoice_date = $request->input('einvoice_date');
            // $eInvoiceMain->ref_no = $request->input('ref_no');
            // total_amount is read-only in the form, so we don't update it here
            $eInvoiceMain->transaction_id = $request->input('transaction_id');
            $eInvoiceMain->branch_id = $request->input('branch_id');
            $eInvoiceMain->batch_status = $request->input('batch_status');
            $eInvoiceMain->description = $request->input('description');
            $eInvoiceMain->pfee_only = $request->input('pfee_only', 0);

            $eInvoiceMain->save();

            return response()->json(['status' => 1, 'message' => 'E-Invoice record updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Error updating E-Invoice record: ' . $e->getMessage()], 500);
        }
    }

    public function saveEinvoice(Request $request)
    {
        try {
            // Start database transaction
            DB::beginTransaction();

            // Get the authenticated user
            $user = auth()->user();

            // Get parameters for generating ref_no
            $prefixParameter = Parameter::where('parameter_type', '=', 'sql_einvoice_ref_prefix')->first();
            $runningNoParameter = Parameter::where('parameter_type', '=', 'sql_einvoice_ref_running_no')->first();

            if (!$prefixParameter || !$runningNoParameter) {
                return response()->json([
                    'status' => 0,
                    'message' => 'E-invoice reference number parameters not found.'
                ]);
            }

            $prefix = $prefixParameter->parameter_value_1;
            $currentRunningNo = (int)$runningNoParameter->parameter_value_1;
            $newRunningNo = $currentRunningNo + 1;

            // Generate the new ref_no
            $generatedRefNo = $prefix . str_pad($newRunningNo, 5, '0', STR_PAD_LEFT);

            // Update the running number in the parameter table
            $runningNoParameter->parameter_value_1 = $newRunningNo;
            $runningNoParameter->save();

            // Validate required fields (excluding ref_no as it's generated)
            if (!$request->has('transaction_id')) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Required fields are missing' // transaction_id is mandatory now
                ]);
            }

            // Create main e-invoice record
            $einvoiceMain = new EInvoiceMain();
            $einvoiceMain->ref_no = $generatedRefNo; // Use the generated ref_no
            $einvoiceMain->einvoice_date = $request->input('einvoice_date');
            // $einvoiceMain->total_amount = $request->input('total_amount') ?: 0; // Use input() to get value
            $einvoiceMain->transaction_id = $request->input('transaction_id');
            $einvoiceMain->branch_id = $request->input('branch_id');
            $einvoiceMain->description = $request->input('description');
            $einvoiceMain->batch_status = $request->input('batch_status') ?: 'NOTSENT';
            $einvoiceMain->pfee_only = $request->input('pfee_only', 0);
            $einvoiceMain->created_by = 1;
            $einvoiceMain->status = 1; // Assuming status 1 means active
            $einvoiceMain->created_at = now();
            $einvoiceMain->updated_at = now();
            $einvoiceMain->save();

            $einvoiceId = $einvoiceMain->id;

            // Process invoice items from 'add_invoice'
            if ($request->has('add_invoice')) {
                $add_bills = json_decode($request->input('add_invoice'), true);

                foreach ($add_bills as $bill) {
                    // Assuming $bill has 'id', 'value', and 'sst'
                    $einvoiceDetails = new EInvoiceDetails();
                    $einvoiceDetails->einvoice_main_id = $einvoiceId;
                    $einvoiceDetails->loan_case_main_bill_id = $bill['bill_id']; // Use 'id' from the bill item
                    $einvoiceDetails->loan_case_invoice_id = $bill['id']; // Use 'id' from the bill item
                    $einvoiceDetails->case_id = $bill['case_id']; // Use 'id' from the bill item
                    $einvoiceDetails->created_by = 1;
                    // $einvoiceDetails->amt = $bill['value']; // Use 'value' from the bill item for amount
                    $einvoiceDetails->einvoice_status = 'SENT'; // Default status for new details
                    $einvoiceDetails->status = 1;
                    $einvoiceDetails->created_at = now();
                    $einvoiceDetails->updated_at = now();
                    $einvoiceDetails->save();

                    // Optionally update related LoanCaseBillMain if needed, similar to AddInvoiceIntoEInvoice
                    LoanCaseBillMain::where('id', '=', $bill['id'])->update(['sql_submit' => 1]);
                }
            }

            // Commit the transaction
            DB::commit();

            return response()->json([
                'status' => 1,
                'message' => 'E-invoice record created successfully ',
                'einvoice_id' => $einvoiceId
            ]);
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            // Log the error
            Log::error('Error creating e-invoice: ' . $e->getMessage());

            return response()->json([
                'status' => 0,
                'message' => 'An error occurred while saving the e-invoice: ' . $e->getMessage()
            ], 500); // Use 500 for internal server error
        }
    }

    /**
     * Search billing parties API
     */
    public function searchBillingParties(Request $request)
    {
        try {
            $current_user = auth()->user();
            $perPage = 10;
            $page = $request->input('page', 1);
            
            $query = DB::table('invoice_billing_party as ibp')
                ->select(
                    'ibp.id',
                    'ibp.customer_name',
                    'ibp.customer_code',
                    'ibp.id_no',
                    'ibp.tin',
                    'ibp.email',
                    'ibp.phone1 as phone',
                    'ibp.brn',
                    'ibp.brn2',
                    'ibp.sales_tax_no',
                    'ibp.service_tax_no',
                    'ibp.customer_category',
                    'ibp.address_1',
                    'ibp.address_2',
                    'ibp.address_3',
                    'ibp.address_4',
                    'ibp.postcode',
                    'ibp.city',
                    'ibp.state',
                    'ibp.country',
                    'ibp.created_at'
                )
                ->where('ibp.status', 1);

            // Apply search filters
            if ($request->input('name')) {
                $query->where('ibp.customer_name', 'LIKE', '%' . $request->input('name') . '%');
            }
            
            if ($request->input('id_no')) {
                $query->where('ibp.id_no', 'LIKE', '%' . $request->input('id_no') . '%');
            }
            
            if ($request->input('tin')) {
                $query->where('ibp.tin', 'LIKE', '%' . $request->input('tin') . '%');
            }

            // Apply user access control - admin, management, account, maker, and lawyer users should see all billing parties
            if (!in_array($current_user->menuroles, ['admin', 'management', 'account', 'maker', 'lawyer'])) {
                $ids = CaseController::getCaseListHub('array', 'customer_id');
                $query->whereIn('ibp.case_id', $ids);
            }

            // Get total count for pagination
            $totalCount = $query->count();
            
            // Get paginated results
            $parties = $query->orderBy('ibp.customer_name', 'ASC')
                           ->offset(($page - 1) * $perPage)
                           ->limit($perPage)
                           ->get();

            $pagination = [
                'current_page' => $page,
                'total_pages' => ceil($totalCount / $perPage),
                'total_count' => $totalCount,
                'per_page' => $perPage
            ];

            return response()->json([
                'status' => 1,
                'data' => $parties,
                'pagination' => $pagination
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error searching billing parties: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get specific billing party details API
     */
    public function getBillingParty($id)
    {
        try {
            $current_user = auth()->user();
            
            $party = DB::table('invoice_billing_party as ibp')
                ->select(
                    'ibp.id',
                    'ibp.customer_name',
                    'ibp.customer_code',
                    'ibp.id_no',
                    'ibp.tin',
                    'ibp.email',
                    'ibp.phone1 as phone',
                    'ibp.brn',
                    'ibp.brn2',
                    'ibp.sales_tax_no',
                    'ibp.service_tax_no',
                    'ibp.customer_category',
                    'ibp.address_1',
                    'ibp.address_2',
                    'ibp.address_3',
                    'ibp.address_4',
                    'ibp.postcode',
                    'ibp.city',
                    'ibp.state',
                    'ibp.country',
                    'ibp.created_at'
                )
                ->where('ibp.id', $id)
                ->where('ibp.status', 1)
                ->first();

            if (!$party) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Billing party not found'
                ], 404);
            }

            // Apply user access control
            if (!in_array($current_user->menuroles, ['admin', 'management', 'account', 'maker', 'lawyer'])) {
                $ids = CaseController::getCaseListHub('array', 'customer_id');
                $partyExists = DB::table('invoice_billing_party')
                    ->where('id', $id)
                    ->whereIn('case_id', $ids)
                    ->exists();
                
                if (!$partyExists) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Access denied to this billing party'
                    ], 403);
                }
            }

            return response()->json([
                'status' => 1,
                'data' => $party
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error getting billing party details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new billing party API
     */
    public function createBillingParty(Request $request)
    {
        try {
            $current_user = auth()->user();

            // Debug logging
            \Illuminate\Support\Facades\Log::info('Create billing party request data:', $request->all());

            // Validate required fields
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'customer_name' => 'required|string|max:255',
                'customer_category' => 'required|in:1,2',
                'id_type' => 'required|in:1,2,3,4',
                'tin' => 'nullable|string|max:255',
                'id_no' => 'nullable|string|max:255',
                'address_1' => 'nullable|string|max:255',
                'phone1' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                \Illuminate\Support\Facades\Log::error('Validation failed:', $validator->errors()->toArray());
                return response()->json([
                    'status' => 0,
                    'message' => 'Validation failed: ' . $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Generate customer code using existing Parameter table logic
            $Parameter = Parameter::where('parameter_type', '=', 'sql_client_code_running_no')->first();
            $SQL_prefix = Parameter::where('parameter_type', '=', 'sql_client_code_prefix')->value('parameter_value_1');

            $nextRunningNo = str_pad((int)$Parameter->parameter_value_1, 5, '0', STR_PAD_LEFT);
            $customerCode = $SQL_prefix . '-' . $nextRunningNo;

            // Create new billing party
            $billingParty = new InvoiceBillingParty();
            $billingParty->customer_name = $request->input('customer_name');
            $billingParty->customer_code = $customerCode;
            $billingParty->customer_category = $request->input('customer_category');
            $billingParty->id_type = $request->input('id_type');
            $billingParty->id_no = $request->input('id_no');
            $billingParty->tin = $request->input('tin');
            $billingParty->brn = $request->input('brn');
            $billingParty->brn2 = $request->input('brn2');
            $billingParty->sales_tax_no = $request->input('sales_tax_no');
            $billingParty->service_tax_no = $request->input('service_tax_no');
            $billingParty->email = $request->input('email');
            $billingParty->phone1 = $request->input('phone1');
            $billingParty->mobile = $request->input('mobile');
            $billingParty->fax1 = $request->input('fax1');
            $billingParty->fax2 = $request->input('fax2');
            $billingParty->address_1 = $request->input('address_1');
            $billingParty->address_2 = $request->input('address_2');
            $billingParty->address_3 = $request->input('address_3');
            $billingParty->address_4 = $request->input('address_4');
            $billingParty->postcode = $request->input('postcode');
            $billingParty->city = $request->input('city');
            $billingParty->state = $request->input('state');
            $billingParty->country = $request->input('country', 'Malaysia');
            
            // Set case and bill information if provided
            $billingParty->case_id = $request->input('case_id');
            $billingParty->loan_case_main_bill_id = $request->input('loan_case_main_bill_id');
            
            // Set default values
            $billingParty->completed = 0;
            $billingParty->sent_to_sql = 0;
            $billingParty->status = 1;
            $billingParty->created_at = date('Y-m-d H:i:s');
            $billingParty->created_by = $current_user->id;

            $billingParty->save();

            // Increment the running number in Parameter table
            $Parameter->parameter_value_1 = (int)$Parameter->parameter_value_1 + 1;
            $Parameter->save();

            return response()->json([
                'status' => 1,
                'message' => 'Billing party created successfully',
                'data' => [
                    'id' => $billingParty->id,
                    'customer_name' => $billingParty->customer_name,
                    'customer_code' => $billingParty->customer_code
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error creating billing party: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Search clients API
     */
    public function searchClients(Request $request)
    {
        try {
            $current_user = auth()->user();
            $perPage = 10;
            $page = $request->input('page', 1);
            
            $query = DB::table('client as c')
                ->select(
                    'c.id',
                    'c.name',
                    'c.ic_no',
                    'c.account_no',
                    'c.phone_no',
                    'c.email',
                    'c.client_type',
                    'c.address',
                    'c.mailing_state',
                    'c.mailing_postcode',
                    'c.mailing_country',
                    'c.billing_state',
                    'c.billing_postcode',
                    'c.billing_country'
                )
                ->where('c.status', 1);

            // Apply search filters
            if ($request->input('name')) {
                $query->where('c.name', 'LIKE', '%' . $request->input('name') . '%');
            }
            
            if ($request->input('ic_no')) {
                $query->where('c.ic_no', 'LIKE', '%' . $request->input('ic_no') . '%');
            }
            
            if ($request->input('account_no')) {
                $query->where('c.account_no', 'LIKE', '%' . $request->input('account_no') . '%');
            }

            // Apply user access control if needed
            if (!in_array($current_user->menuroles, ['admin', 'management'])) {
                // Add any client access restrictions here if needed
            }

            // Get total count for pagination
            $totalCount = $query->count();
            
            // Debug logging
            \Illuminate\Support\Facades\Log::info('Client search - Total count: ' . $totalCount);
            \Illuminate\Support\Facades\Log::info('Client search - Query: ' . $query->toSql());
            \Illuminate\Support\Facades\Log::info('Client search - Bindings: ' . json_encode($query->getBindings()));
            
            // Get paginated results
            $clients = $query->orderBy('c.name', 'ASC')
                           ->offset(($page - 1) * $perPage)
                           ->limit($perPage)
                           ->get();
            
            \Illuminate\Support\Facades\Log::info('Client search - Results count: ' . $clients->count());

            $pagination = [
                'current_page' => $page,
                'total_pages' => ceil($totalCount / $perPage),
                'total_count' => $totalCount,
                'per_page' => $perPage
            ];

            return response()->json([
                'status' => 1,
                'data' => $clients,
                'pagination' => $pagination
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error searching clients: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Combined search for clients and masterlist parties
     */
    public function searchParties(Request $request)
    {
        try {
            $current_user = auth()->user();
            $perPage = 10;
            $page = $request->input('page', 1);
            $search = $request->input('search', '');
            $sourceFilter = $request->input('source_filter', 'both'); // 'client', 'masterlist', 'both'
            
            $allResults = collect();
            
            // Search clients if source filter allows
            if (in_array($sourceFilter, ['client', 'both'])) {
                $clients = $this->searchClientParties($search);
                \Illuminate\Support\Facades\Log::info('Client search results count: ' . $clients->count());
                $allResults = $allResults->merge($clients);
            }
            
            // Search masterlist if source filter allows
            if (in_array($sourceFilter, ['masterlist', 'both'])) {
                $masterlistParties = $this->searchMasterlistParties($search);
                \Illuminate\Support\Facades\Log::info('Masterlist search results count: ' . $masterlistParties->count());
                $allResults = $allResults->merge($masterlistParties);
            }
            
            // Sort combined results by name
            $allResults = $allResults->sortBy('name')->values();
            
            // Use Laravel's LengthAwarePaginator for proper pagination
            $totalCount = $allResults->count();
            $currentPageItems = $allResults->forPage($page, $perPage);
            
            // Create pagination data
            $pagination = [
                'current_page' => (int)$page,
                'total_pages' => (int)ceil($totalCount / $perPage),
                'total_count' => (int)$totalCount,
                'per_page' => (int)$perPage,
                'has_next_page' => $page < ceil($totalCount / $perPage),
                'has_prev_page' => $page > 1
            ];
            
            \Illuminate\Support\Facades\Log::info('Combined party search - Total results: ' . $totalCount);
            \Illuminate\Support\Facades\Log::info('Combined party search - Source filter: ' . $sourceFilter);
            \Illuminate\Support\Facades\Log::info('Combined party search - Page: ' . $page . ', Items on this page: ' . $currentPageItems->count());
            \Illuminate\Support\Facades\Log::info('Combined party search - Current page items: ' . json_encode($currentPageItems->take(3)->toArray()));
            
            $dataArray = $currentPageItems->toArray();
            
            return response()->json([
                'status' => 1,
                'data' => $dataArray, // Ensure data is always an array
                'pagination' => $pagination
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error searching parties: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Search client parties (no pagination - pagination applied to combined results)
     */
    private function searchClientParties($search)
    {
        $query = DB::table('client as c')
            ->select(
                DB::raw("'client' as source"),
                'c.id',
                'c.name',
                'c.ic_no as id_no',
                'c.account_no',
                'c.phone_no as phone1',
                'c.email',
                'c.client_type',
                'c.income_tax_no as tin',
                'c.address',
                'c.mailing_state as state',
                'c.mailing_postcode as postcode',
                'c.mailing_country as country',
                DB::raw("NULL as case_id"),
                DB::raw("NULL as case_ref_no"),
                DB::raw("NULL as party_type"),
                DB::raw("NULL as party_category")
            )
            ->where('c.status', 1)
            ->where(function($q) use ($search) {
                $q->where('c.name', 'LIKE', '%' . $search . '%')
                  ->orWhere('c.ic_no', 'LIKE', '%' . $search . '%')
                  ->orWhere('c.account_no', 'LIKE', '%' . $search . '%');
            });
            
        return $query->orderBy('c.name', 'ASC')->get();
    }
    
    /**
     * Search masterlist parties (no pagination - pagination applied to combined results)
     */
    private function searchMasterlistParties($search)
    {
        // Get individual parties (Purchaser 1-6, Vendor 1-6, Borrower 1-6, Guarantor 1-6)
        $individualParties = DB::table('loan_case_masterlist as lcm')
            ->join('case_masterlist_field as cmf', 'lcm.masterlist_field_id', '=', 'cmf.id')
            ->join('case_masterlist_field_category as cmfc', 'cmf.case_field_id', '=', 'cmfc.id')
            ->join('loan_case as lc', 'lcm.case_id', '=', 'lc.id')
            ->select(
                DB::raw("'masterlist' as source"),
                'lcm.case_id as id',
                'lcm.value as name',
                DB::raw("NULL as id_no"),
                DB::raw("NULL as account_no"),
                DB::raw("NULL as phone1"),
                DB::raw("NULL as email"),
                DB::raw("'individual' as client_type"),
                DB::raw("NULL as tin"),
                DB::raw("NULL as address"),
                DB::raw("NULL as state"),
                DB::raw("NULL as postcode"),
                DB::raw("NULL as country"),
                'lcm.case_id',
                'lc.case_ref_no',
                DB::raw("CASE 
                    WHEN cmfc.name LIKE 'Purchaser%' THEN 'Purchaser'
                    WHEN cmfc.name LIKE 'Vendor%' THEN 'Vendor'
                    WHEN cmfc.name LIKE 'Borrower%' THEN 'Borrower'
                    WHEN cmfc.name LIKE 'Guarantor%' THEN 'Guarantor'
                    ELSE cmfc.name
                END as party_type"),
                'cmfc.name as party_category'
            )
            ->where(function($query) {
                $query->where('cmfc.name', 'LIKE', 'Purchaser%')
                      ->orWhere('cmfc.name', 'LIKE', 'Vendor%')
                      ->orWhere('cmfc.name', 'LIKE', 'Borrower%')
                      ->orWhere('cmfc.name', 'LIKE', 'Guarantor%');
            })
            ->where('cmf.name', 'Name')
            ->where('lcm.value', 'LIKE', '%' . $search . '%')
            ->where('lc.status', '!=', 'deleted');
            
        // Get company parties (Purchaser Company, Vendor Company, Borrower Company, Guarantor Company)
        $companyParties = DB::table('loan_case_masterlist as lcm')
            ->join('case_masterlist_field as cmf', 'lcm.masterlist_field_id', '=', 'cmf.id')
            ->join('case_masterlist_field_category as cmfc', 'cmf.case_field_id', '=', 'cmfc.id')
            ->join('loan_case as lc', 'lcm.case_id', '=', 'lc.id')
            ->select(
                DB::raw("'masterlist' as source"),
                'lcm.case_id as id',
                'lcm.value as name',
                DB::raw("NULL as id_no"),
                DB::raw("NULL as account_no"),
                DB::raw("NULL as phone1"),
                DB::raw("NULL as email"),
                DB::raw("'company' as client_type"),
                DB::raw("NULL as tin"),
                DB::raw("NULL as address"),
                DB::raw("NULL as state"),
                DB::raw("NULL as postcode"),
                DB::raw("NULL as country"),
                'lcm.case_id',
                'lc.case_ref_no',
                DB::raw("CASE 
                    WHEN cmfc.name = 'Purchaser Company' THEN 'Purchaser Company'
                    WHEN cmfc.name = 'Vendor Company' THEN 'Vendor Company'
                    WHEN cmfc.name = 'Borrower Company' THEN 'Borrower Company'
                    WHEN cmfc.name = 'Guarantor Company' THEN 'Guarantor Company'
                    ELSE cmfc.name
                END as party_type"),
                'cmfc.name as party_category'
            )
            ->whereIn('cmfc.name', ['Purchaser Company', 'Vendor Company', 'Borrower Company', 'Guarantor Company'])
            ->where('cmf.name', 'Name')
            ->where('lcm.value', 'LIKE', '%' . $search . '%')
            ->where('lc.status', '!=', 'deleted');
            
        // Combine and return
        return $individualParties->union($companyParties)
                                ->orderBy('name', 'ASC')
                                ->get();
    }
    
    /**
     * Get masterlist party detailed data for billing party creation
     */
    public function getMasterlistPartyData($caseId, $partyType, $partyCategory)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Getting masterlist party data:', [
                'caseId' => $caseId,
                'partyType' => $partyType,
                'partyCategory' => $partyCategory
            ]);
            
            // Get the masterlist field IDs for the party category
            $fieldIds = DB::table('case_masterlist_field as cmf')
                ->join('case_masterlist_field_category as cmfc', 'cmf.case_field_id', '=', 'cmfc.id')
                ->where('cmfc.name', $partyCategory)
                ->pluck('cmf.id');
                
            \Illuminate\Support\Facades\Log::info('Field IDs found:', $fieldIds->toArray());
                
            // Get all masterlist data for this case and party
            $masterlistData = DB::table('loan_case_masterlist as lcm')
                ->join('case_masterlist_field as cmf', 'lcm.masterlist_field_id', '=', 'cmf.id')
                ->where('lcm.case_id', $caseId)
                ->whereIn('lcm.masterlist_field_id', $fieldIds)
                ->select('cmf.name as field_name', 'lcm.value')
                ->get();
                
            \Illuminate\Support\Facades\Log::info('Masterlist data found:', $masterlistData->toArray());
            
            $masterlistData = $masterlistData->keyBy('field_name');
                
            // Map masterlist fields to billing party fields
            $billingPartyData = [
                'customer_name' => $masterlistData->get('Name') ? $masterlistData->get('Name')->value : '',
                'id_no' => $masterlistData->get('NRIC') ? $masterlistData->get('NRIC')->value : '',
                'tin' => $masterlistData->get('Income Tax No') ? $masterlistData->get('Income Tax No')->value : '',
                'brn' => $masterlistData->get('Company No') ? $masterlistData->get('Company No')->value : '',
                'email' => $masterlistData->get('Email') ? $masterlistData->get('Email')->value : '',
                'phone1' => $masterlistData->get('Tel') ? $masterlistData->get('Tel')->value : '',
                'mobile' => $masterlistData->get('HP') ? $masterlistData->get('HP')->value : '',
                'fax1' => $masterlistData->get('Fax') ? $masterlistData->get('Fax')->value : '',
            ];
            
            // Handle address mapping
            $address = $masterlistData->get('Address') ? $masterlistData->get('Address')->value : '';
            if ($address) {
                $addressParts = $this->splitAddressComplete($address);
                $billingPartyData['address_1'] = $addressParts['address_1'] ?? '';
                $billingPartyData['address_2'] = $addressParts['address_2'] ?? '';
                $billingPartyData['address_3'] = $addressParts['address_3'] ?? '';
                $billingPartyData['address_4'] = $addressParts['address_4'] ?? '';
                $billingPartyData['postcode'] = $addressParts['postcode'] ?? '';
                $billingPartyData['city'] = $addressParts['city'] ?? '';
                $billingPartyData['state'] = $addressParts['state'] ?? '';
            }
            
            // Map country
            $country = $masterlistData->get('Country') ? $masterlistData->get('Country')->value : 'Malaysia';
            $billingPartyData['country'] = $this->mapCountryNameToCode($country);
            
            \Illuminate\Support\Facades\Log::info('Billing party data mapped:', $billingPartyData);
            
            return response()->json([
                'status' => 1,
                'data' => $billingPartyData
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in getMasterlistPartyData:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 0,
                'message' => 'Error getting masterlist party data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get client data mapped for billing party creation
     */
    public function getClientBillingPartyData($clientId)
    {
        try {
            $current_user = auth()->user();
            
            $client = DB::table('client as c')
                ->select(
                    'c.id',
                    'c.name',
                    'c.ic_no',
                    'c.account_no',
                    'c.phone_no',
                    'c.email',
                    'c.client_type',
                    'c.income_tax_no',
                    'c.company_ref_no',
                    'c.address',
                    'c.mailing_state',
                    'c.mailing_postcode',
                    'c.mailing_country',
                    'c.billing_state',
                    'c.billing_postcode',
                    'c.billing_country'
                )
                ->where('c.id', $clientId)
                ->where('c.status', 1)
                ->first();

            if (!$client) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Client not found'
                ], 404);
            }

            // Extract location information from address
            $locationInfo = $this->extractLocationInfo($client->address);
            
            // Debug logging for address processing
            \Illuminate\Support\Facades\Log::info('Client address processing:', [
                'client_id' => $client->id,
                'original_address' => $client->address,
                'address_1' => $this->splitAddress($client->address, 0),
                'address_2' => $this->splitAddress($client->address, 1),
                'address_3' => $this->splitAddress($client->address, 2),
                'address_4' => $this->splitAddress($client->address, 3),
                'extracted_postcode' => $locationInfo['postcode'],
                'extracted_state' => $locationInfo['state']
            ]);
            
            // Map client data to billing party format
            $billingPartyData = [
                'customer_name' => $client->name,
                'customer_code' => $client->account_no,
                'customer_category' => $client->client_type,
                'id_no' => $client->ic_no,
                'tin' => $client->income_tax_no,
                'brn' => $client->company_ref_no,
                'email' => $client->email,
                'phone1' => $client->phone_no,
                'address_1' => $this->splitAddress($client->address, 0),
                'address_2' => $this->splitAddress($client->address, 1),
                'address_3' => $this->splitAddress($client->address, 2),
                'address_4' => $this->splitAddress($client->address, 3),
                'postcode' => $client->mailing_postcode ?: $client->billing_postcode ?: $locationInfo['postcode'],
                'state' => $client->mailing_state ?: $client->billing_state ?: $locationInfo['state'],
                'country' => $this->mapCountryNameToCode($client->mailing_country ?: $client->billing_country ?: 'Malaysia'),
                'city' => '', // Client table doesn't have city field
            ];

            return response()->json([
                'status' => 1,
                'data' => $billingPartyData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error getting client data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Split address into multiple parts with smart character limit handling
     */
    private function splitAddress($address, $partIndex)
    {
        if (empty($address)) {
            return '';
        }

        $address = trim($address);
        $maxLength = 60; // Maximum characters per address line
        
        // If address is shorter than max length, return as is for first part
        if (strlen($address) <= $maxLength) {
            return $partIndex === 0 ? $address : '';
        }

        // Split address into parts based on character limit and natural breaks
        $parts = [];
        $remaining = $address;
        
        while (strlen($remaining) > 0 && count($parts) < 4) {
            if (strlen($remaining) <= $maxLength) {
                $parts[] = trim($remaining);
                break;
            }
            
            // Find the best break point within the character limit
            $breakPoint = $this->findBestBreakPoint($remaining, $maxLength);
            
            if ($breakPoint === false) {
                // If no good break point found, force break at max length
                $parts[] = trim(substr($remaining, 0, $maxLength));
                $remaining = trim(substr($remaining, $maxLength));
            } else {
                $parts[] = trim(substr($remaining, 0, $breakPoint));
                $remaining = trim(substr($remaining, $breakPoint));
            }
        }
        
        return $parts[$partIndex] ?? '';
    }

    /**
     * Find the best break point in an address string
     */
    private function findBestBreakPoint($address, $maxLength)
    {
        // Look for natural break points in order of preference
        $breakPoints = [
            ', ', // Comma followed by space
            ' ',  // Space
            ',',  // Comma
            ';',  // Semicolon
        ];
        
        foreach ($breakPoints as $breakPoint) {
            $pos = strrpos(substr($address, 0, $maxLength), $breakPoint);
            if ($pos !== false) {
                return $pos + strlen($breakPoint);
            }
        }
        
        return false;
    }

    /**
     * Extract postcode and state from address
     */
    private function extractLocationInfo($address)
    {
        $postcode = '';
        $state = '';
        
        if (empty($address)) {
            return ['postcode' => '', 'state' => ''];
        }

        // Malaysian postcode pattern: 5 digits
        if (preg_match('/\b(\d{5})\b/', $address, $matches)) {
            $postcode = $matches[1];
        }

        // Malaysian states list
        $malaysianStates = [
            'Johor', 'Kedah', 'Kelantan', 'Melaka', 'Negeri Sembilan', 
            'Pahang', 'Perak', 'Perlis', 'Pulau Pinang', 'Sabah', 
            'Sarawak', 'Selangor', 'Terengganu', 'Kuala Lumpur', 
            'Labuan', 'Putrajaya'
        ];

        foreach ($malaysianStates as $stateName) {
            if (stripos($address, $stateName) !== false) {
                $state = $stateName;
                break;
            }
        }

        return ['postcode' => $postcode, 'state' => $state];
    }

    /**
     * Split address completely and return all parts
     */
    private function splitAddressComplete($address)
    {
        if (empty($address)) {
            return [
                'address_1' => '',
                'address_2' => '',
                'address_3' => '',
                'address_4' => '',
                'postcode' => '',
                'city' => '',
                'state' => ''
            ];
        }

        // Extract location info first
        $locationInfo = $this->extractLocationInfo($address);
        
        // Split address into parts
        $addressParts = [
            'address_1' => $this->splitAddress($address, 0),
            'address_2' => $this->splitAddress($address, 1),
            'address_3' => $this->splitAddress($address, 2),
            'address_4' => $this->splitAddress($address, 3),
            'postcode' => $locationInfo['postcode'],
            'state' => $locationInfo['state'],
            'city' => '' // We'll try to extract city from the address
        ];
        
        // Try to extract city from the address
        $city = $this->extractCityFromAddress($address);
        $addressParts['city'] = $city;
        
        return $addressParts;
    }

    /**
     * Extract city from address
     */
    private function extractCityFromAddress($address)
    {
        if (empty($address)) {
            return '';
        }

        // Common Malaysian cities
        $malaysianCities = [
            'Kuala Lumpur', 'Petaling Jaya', 'Shah Alam', 'Subang Jaya', 'Klang',
            'Johor Bahru', 'Ipoh', 'Penang', 'George Town', 'Malacca', 'Melaka',
            'Kuantan', 'Kuching', 'Kota Kinabalu', 'Alor Setar', 'Kangar',
            'Kota Bharu', 'Kuala Terengganu', 'Seremban', 'Miri', 'Sandakan',
            'Tawau', 'Lahad Datu', 'Sibu', 'Bintulu', 'Muar', 'Batu Pahat',
            'Segamat', 'Kluang', 'Kulai', 'Pontian', 'Tangkak', 'Yong Peng'
        ];

        foreach ($malaysianCities as $city) {
            if (stripos($address, $city) !== false) {
                return $city;
            }
        }

        return '';
    }

    /**
     * Check for duplicate billing party
     */
    public function checkDuplicateBillingParty(Request $request)
    {
        try {
            $name = $request->input('customer_name');
            $idNo = $request->input('id_no');
            $tin = $request->input('tin');
            
            // Debug logging
            \Illuminate\Support\Facades\Log::info('Duplicate check input values:', [
                'customer_name' => $name,
                'id_no' => $idNo,
                'tin' => $tin,
                'tin_empty_check' => empty($tin),
                'tin_trim_check' => trim($tin) !== '',
                'id_no_empty_check' => empty($idNo),
                'id_no_trim_check' => trim($idNo) !== ''
            ]);
            
            $duplicates = [];
            
            // Check by exact ID number match
            if (!empty($idNo) && trim($idNo) !== '') {
                $idMatch = \App\Models\InvoiceBillingParty::where('id_no', $idNo)
                    ->where('status', 1)
                    ->first();
                if ($idMatch) {
                    $duplicates[] = [
                        'type' => 'ID Number',
                        'value' => $idNo,
                        'record' => $idMatch
                    ];
                }
            }
            
            // Check by exact TIN match
            if (!empty($tin) && trim($tin) !== '') {
                $tinMatch = \App\Models\InvoiceBillingParty::where('tin', $tin)
                    ->where('status', 1)
                    ->first();
                if ($tinMatch) {
                    $duplicates[] = [
                        'type' => 'TIN',
                        'value' => $tin,
                        'record' => $tinMatch
                    ];
                }
            }
            
            // Check by similar name (fuzzy match)
            if (!empty($name) && trim($name) !== '') {
                $similarNames = \App\Models\InvoiceBillingParty::where('status', 1)
                    ->where(function($query) use ($name) {
                        // Only exact substring match - more conservative approach
                        $query->where('customer_name', 'LIKE', '%' . $name . '%');
                    })
                    ->limit(5)
                    ->get();
                
                if ($similarNames->count() > 0) {
                    foreach ($similarNames as $similar) {
                        // Additional filtering: only consider it a duplicate if the similarity is significant
                        $inputName = strtolower(trim($name));
                        $existingName = strtolower(trim($similar->customer_name));
                        
                        // Calculate similarity percentage
                        $similarity = 0;
                        similar_text($inputName, $existingName, $similarity);
                        
                        // Only flag as duplicate if similarity is above 60% or if it's an exact substring match
                        if ($similarity > 60 || strpos($existingName, $inputName) !== false) {
                            $duplicates[] = [
                                'type' => 'Similar Name',
                                'value' => $name,
                                'record' => $similar,
                                'similarity' => round($similarity, 2)
                            ];
                        }
                    }
                }
            }
            
            return response()->json([
                'status' => 1,
                'has_duplicates' => count($duplicates) > 0,
                'duplicates' => $duplicates
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error checking duplicates: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Error checking for duplicates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Map country name to country code
     */
    private function mapCountryNameToCode($countryName)
    {
        if (empty($countryName)) {
            return 'MY';
        }

        $countryMap = [
            'malaysia' => 'MY',
            'singapore' => 'SG',
            'indonesia' => 'ID',
            'thailand' => 'TH',
            'philippines' => 'PH',
            'vietnam' => 'VN',
            'brunei' => 'BN',
            'myanmar' => 'MM',
            'laos' => 'LA',
            'cambodia' => 'KH',
            'united states' => 'US',
            'united kingdom' => 'GB',
            'australia' => 'AU',
            'china' => 'CN',
            'japan' => 'JP',
            'south korea' => 'KR',
            'india' => 'IN'
        ];

        return $countryMap[strtolower($countryName)] ?? 'MY';
    }

    /**
     * Search parties with access control (NEW - Access Controlled Version)
     */
    public function searchPartiesWithAccess(Request $request)
    {
        try {
            $perPage = $request->input('perPage', 10);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');
            $sourceFilter = $request->input('source_filter', 'both'); // 'client', 'masterlist', 'both'
            
            $allResults = collect();
            
            // Search clients if source filter allows
            if (in_array($sourceFilter, ['client', 'both'])) {
                $clients = $this->searchClientPartiesWithAccess($search);
                $allResults = $allResults->merge($clients);
            }
            
            // Search masterlist if source filter allows
            if (in_array($sourceFilter, ['masterlist', 'both'])) {
                $masterlistParties = $this->searchMasterlistPartiesWithAccess($search);
                $allResults = $allResults->merge($masterlistParties);
            }
            
            // Sort combined results by name
            $allResults = $allResults->sortBy('name')->values();
            
            // Use Laravel's forPage method for proper pagination
            $totalCount = $allResults->count();
            $currentPageItems = $allResults->forPage($page, $perPage);
            
            $pagination = [
                'current_page' => (int)$page,
                'total_pages' => (int)ceil($totalCount / $perPage),
                'total_count' => (int)$totalCount,
                'per_page' => (int)$perPage,
                'has_next_page' => $page < ceil($totalCount / $perPage),
                'has_prev_page' => $page > 1
            ];
            
            \Illuminate\Support\Facades\Log::info('Access-controlled party search - Total results: ' . $totalCount);
            \Illuminate\Support\Facades\Log::info('Access-controlled party search - Source filter: ' . $sourceFilter);
            \Illuminate\Support\Facades\Log::info('Access-controlled party search - Page: ' . $page . ', Items on this page: ' . $currentPageItems->count());
            \Illuminate\Support\Facades\Log::info('Access-controlled party search - Current page items: ' . json_encode($currentPageItems->take(3)->toArray()));
            
            return response()->json([
                'status' => 1,
                'data' => $currentPageItems->toArray(),
                'pagination' => $pagination
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error searching parties: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Search client parties with access control (NEW)
     */
    private function searchClientPartiesWithAccess($search)
    {
        // Get user's accessible cases using existing caseManagementEngine
        $accessCaseList = \App\Http\Controllers\CaseController::caseManagementEngine();
        
        $query = DB::table('client as c')
            ->join('loan_case as lc', 'c.id', '=', 'lc.customer_id')
            ->select(
                DB::raw("'client' as source"),
                'c.id',
                'c.name',
                'c.ic_no as id_no',
                'c.account_no',
                'c.phone_no as phone1',
                'c.email',
                'c.client_type',
                'c.income_tax_no as tin',
                'c.address',
                'c.mailing_state as state',
                'c.mailing_postcode as postcode',
                'c.mailing_country as country',
                'lc.id as case_id',
                'lc.case_ref_no',
                DB::raw("NULL as party_type"),
                DB::raw("NULL as party_category")
            )
            ->where('c.status', 1)
            ->whereIn('lc.id', $accessCaseList) // Filter by accessible cases
            ->where(function($q) use ($search) {
                $q->where('c.name', 'LIKE', '%' . $search . '%')
                  ->orWhere('c.ic_no', 'LIKE', '%' . $search . '%')
                  ->orWhere('c.account_no', 'LIKE', '%' . $search . '%');
            });
            
        return $query->orderBy('c.name', 'ASC')->get();
    }
    
    /**
     * Search masterlist parties with access control (NEW)
     */
    private function searchMasterlistPartiesWithAccess($search)
    {
        // Get user's accessible cases using existing caseManagementEngine
        $accessCaseList = \App\Http\Controllers\CaseController::caseManagementEngine();
        
        // Get individual parties (Purchaser 1-6, Vendor 1-6, Borrower 1-6, Guarantor 1-6)
        $individualParties = DB::table('loan_case_masterlist as lcm')
            ->join('case_masterlist_field as cmf', 'lcm.masterlist_field_id', '=', 'cmf.id')
            ->join('case_masterlist_field_category as cmfc', 'cmf.case_field_id', '=', 'cmfc.id')
            ->join('loan_case as lc', 'lcm.case_id', '=', 'lc.id')
            ->select(
                DB::raw("'masterlist' as source"),
                'lcm.case_id as id',
                'lcm.value as name',
                DB::raw("NULL as id_no"),
                DB::raw("NULL as account_no"),
                DB::raw("NULL as phone1"),
                DB::raw("NULL as email"),
                DB::raw("'individual' as client_type"),
                DB::raw("NULL as tin"),
                DB::raw("NULL as address"),
                DB::raw("NULL as state"),
                DB::raw("NULL as postcode"),
                DB::raw("NULL as country"),
                'lcm.case_id',
                'lc.case_ref_no',
                DB::raw("CASE 
                    WHEN cmfc.name LIKE 'Purchaser%' THEN 'Purchaser'
                    WHEN cmfc.name LIKE 'Vendor%' THEN 'Vendor'
                    WHEN cmfc.name LIKE 'Borrower%' THEN 'Borrower'
                    WHEN cmfc.name LIKE 'Guarantor%' THEN 'Guarantor'
                    ELSE cmfc.name
                END as party_type"),
                'cmfc.name as party_category'
            )
            ->where(function($query) {
                $query->where('cmfc.name', 'LIKE', 'Purchaser%')
                      ->orWhere('cmfc.name', 'LIKE', 'Vendor%')
                      ->orWhere('cmfc.name', 'LIKE', 'Borrower%')
                      ->orWhere('cmfc.name', 'LIKE', 'Guarantor%');
            })
            ->where('cmf.name', 'Name')
            ->where('lcm.value', 'LIKE', '%' . $search . '%')
            ->whereIn('lc.id', $accessCaseList) // Filter by accessible cases
            ->where('lc.status', '!=', 'deleted');
            
        // Get company parties (Purchaser Company, Vendor Company, Borrower Company, Guarantor Company)
        $companyParties = DB::table('loan_case_masterlist as lcm')
            ->join('case_masterlist_field as cmf', 'lcm.masterlist_field_id', '=', 'cmf.id')
            ->join('case_masterlist_field_category as cmfc', 'cmf.case_field_id', '=', 'cmfc.id')
            ->join('loan_case as lc', 'lcm.case_id', '=', 'lc.id')
            ->select(
                DB::raw("'masterlist' as source"),
                'lcm.case_id as id',
                'lcm.value as name',
                DB::raw("NULL as id_no"),
                DB::raw("NULL as account_no"),
                DB::raw("NULL as phone1"),
                DB::raw("NULL as email"),
                DB::raw("'company' as client_type"),
                DB::raw("NULL as tin"),
                DB::raw("NULL as address"),
                DB::raw("NULL as state"),
                DB::raw("NULL as postcode"),
                DB::raw("NULL as country"),
                'lcm.case_id',
                'lc.case_ref_no',
                DB::raw("CASE 
                    WHEN cmfc.name = 'Purchaser Company' THEN 'Purchaser Company'
                    WHEN cmfc.name = 'Vendor Company' THEN 'Vendor Company'
                    WHEN cmfc.name = 'Borrower Company' THEN 'Borrower Company'
                    WHEN cmfc.name = 'Guarantor Company' THEN 'Guarantor Company'
                    ELSE cmfc.name
                END as party_type"),
                'cmfc.name as party_category'
            )
            ->where(function($query) {
                $query->where('cmfc.name', '=', 'Purchaser Company')
                      ->orWhere('cmfc.name', '=', 'Vendor Company')
                      ->orWhere('cmfc.name', '=', 'Borrower Company')
                      ->orWhere('cmfc.name', '=', 'Guarantor Company');
            })
            ->where('cmf.name', 'Name')
            ->where('lcm.value', 'LIKE', '%' . $search . '%')
            ->whereIn('lc.id', $accessCaseList) // Filter by accessible cases
            ->where('lc.status', '!=', 'deleted');
            
        // Union individual and company parties
        $combinedQuery = $individualParties->union($companyParties);
        
        return $combinedQuery->orderBy('name', 'ASC')->get();
    }
    
    /**
     * Get client data with access control (NEW)
     */
    public function getClientBillingPartyDataWithAccess($clientId)
    {
        try {
            $current_user = auth()->user();
            
            // Get user's accessible cases
            $accessCaseList = \App\Http\Controllers\CaseController::caseManagementEngine();
            
            $client = DB::table('client as c')
                ->join('loan_case as lc', 'c.id', '=', 'lc.customer_id')
                ->select(
                    'c.id',
                    'c.name',
                    'c.ic_no',
                    'c.account_no',
                    'c.phone_no',
                    'c.email',
                    'c.client_type',
                    'c.income_tax_no',
                    'c.company_ref_no',
                    'c.address',
                    'c.mailing_state',
                    'c.mailing_postcode',
                    'c.mailing_country',
                    'c.billing_state',
                    'c.billing_postcode',
                    'c.billing_country'
                )
                ->where('c.id', $clientId)
                ->where('c.status', 1)
                ->whereIn('lc.id', $accessCaseList) // Filter by accessible cases
                ->first();

            if (!$client) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Client not found or access denied'
                ], 404);
            }

            // Extract location information from address
            $locationInfo = $this->extractLocationInfo($client->address);
            
            // Map client data to billing party format
            $billingPartyData = [
                'customer_name' => $client->name,
                'customer_code' => $client->account_no,
                'customer_category' => $client->client_type,
                'id_no' => $client->ic_no,
                'tin' => $client->income_tax_no,
                'brn' => $client->company_ref_no,
                'email' => $client->email,
                'phone1' => $client->phone_no,
                'address_1' => $this->splitAddress($client->address, 0),
                'address_2' => $this->splitAddress($client->address, 1),
                'address_3' => $this->splitAddress($client->address, 2),
                'address_4' => $this->splitAddress($client->address, 3),
                'postcode' => $client->mailing_postcode ?: $client->billing_postcode ?: $locationInfo['postcode'],
                'state' => $client->mailing_state ?: $client->billing_state ?: $locationInfo['state'],
                'country' => $this->mapCountryNameToCode($client->mailing_country ?: $client->billing_country ?: 'Malaysia'),
                'city' => '', // Client table doesn't have city field
            ];

            return response()->json([
                'status' => 1,
                'data' => $billingPartyData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error getting client data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get masterlist party data with access control (NEW)
     */
    public function getMasterlistPartyDataWithAccess($caseId, $partyType, $partyCategory)
    {
        try {
            // Get user's accessible cases
            $accessCaseList = \App\Http\Controllers\CaseController::caseManagementEngine();
            
            // Check if user has access to this specific case
            if (!in_array($caseId, $accessCaseList)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Access denied to this case'
                ], 403);
            }
            
            // Use existing logic but with access control
            return $this->getMasterlistPartyData($caseId, $partyType, $partyCategory);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error getting masterlist party data: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Helper\Helper;
use App\Models\Account;
use App\Models\AccountCategory;
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
use App\Models\LoanCaseAccount;
use App\Models\OfficeBankAccount;
use App\Models\Parameter;
use App\Models\QuotationGeneratorDetails;
use App\Models\QuotationGeneratorMain;
use App\Models\QuotationTemplateDetails;
use App\Models\QuotationTemplateMain;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class QuotationGeneratorController extends Controller
{

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

        return view('dashboard.quotation-generator-v2.index', ['accounts' => $accounts]);
    }

    public function quotationGenerator() 
    {

        $current_user = auth()->user();

        // $allowTransferSales = AccessController::UserAccessPermissionController(PermissionController::QuotationGeneratorPermission());

        // if (in_array($current_user->menuroles, ['clerk', 'receptionist', 'maker', 'lawyer']))
        // {
        //     if (!in_array($current_user->id, [89,13,88,109, 14, 38,122,141]))
        //     {
        //         // return redirect()->route('case.index');
        //          return redirect()->route('cases.list', 'active');
        //     }
        // }

        if(AccessController::UserAccessPermissionController(PermissionController::QuotationGeneratorPermission()) == false)
        {
            return redirect()->route('dashboard.index');
        }

        $quotation_template = QuotationTemplateMain::where('status', '=', 1)->get();

        return view('dashboard.quotation-generator-v2.index', ['current_user' => $current_user,
                                                    'quotation_template' => $quotation_template]);
    }

    public function getQuotationGeneratorList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $branchInfo = BranchController::manageBranchAccess();

            $QuotationGeneratorMain = DB::table('quotation_generator_main AS a')
                ->leftJoin('users AS u', 'u.id', '=', 'a.user_id')
                ->leftJoin('quotation_template_main AS b', 'b.id', '=', 'a.template_id')
                ->select('a.*', 'u.name as user', 'b.name as template_name')
                // ->orderBy('id', 'ASC')
                ->where('a.status', '=', 1);

            if (in_array($current_user->menuroles, ['sales','account', 'lawyer', 'clerk', 'chambering']) || in_array($current_user->id, [2]))
            {
                if(in_array($current_user->id, [144]))
                {
                    $QuotationGeneratorMain = $QuotationGeneratorMain->whereIn('user_id', [$current_user->id,29]);
                }
                else{
                    $QuotationGeneratorMain = $QuotationGeneratorMain->where('user_id', '=', $current_user->id);
                }
                
            }

            if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
                $QuotationGeneratorMain = $QuotationGeneratorMain->whereBetween('a.created_at', [$request->input("date_from"), $request->input("date_to")]);
            } else {
                if ($request->input("date_from") <> null) {
                    $QuotationGeneratorMain = $QuotationGeneratorMain->where('a.created_at', '>=', $request->input("date_from"));
                }
    
                if ($request->input("date_to") <> null) {
                    $QuotationGeneratorMain = $QuotationGeneratorMain->where('a.created_at', '<=', $request->input("date_to"));
                }
            }

            if ($request->input("template"))
            {
                $QuotationGeneratorMain = $QuotationGeneratorMain->where('a.template_id', $request->input("template"));
            }

            $QuotationGeneratorMain = $QuotationGeneratorMain->orderBy('a.created_at', 'DESC')->get();


            return DataTables::of($QuotationGeneratorMain)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use($current_user) {
                    $actionBtn = ' <a target="_blank" href="/quotation-generator/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>';
                    
                    $actionBtn = '
                    <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                    <i class="cil-settings"></i>
                    </button>
                    <div class="dropdown-menu" style="padding:0">
                      <a class="dropdown-item btn-info" href="/quotation-generator-edit/' . $row->id . '"  style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-pencil"></i>Edit</a>
                      <a class="dropdown-item btn-success"  href="javascript:void(0)" onclick="copyTemplate(' . $row->id . ')"  style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-search"></i>Copy</a>
                      <div class="dropdown-divider" style="margin:0"></div>
                      <a class="dropdown-item btn-success" href="javascript:void(0)" onclick="showGenerateQuotationModal(' . $row->id . ')"  style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-cloud-download"></i>Download Quotation</a>
                      <div class="dropdown-divider" style="margin:0"></div>
                      <a class="dropdown-item btn-danger"  href="javascript:void(0)" onclick="deleteSavedQuotation(' . $row->id . ')"  style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-x"></i>Delete</a>
                      ';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function quotationGeneratorCreate()
    {

        $current_user = auth()->user();

        if(AccessController::UserAccessPermissionController(PermissionController::QuotationGeneratorPermission()) == false)
        {
            return redirect()->route('dashboard.index');
        }

        $quotation_template = QuotationTemplateMain::where('status', '=', 1)->get();

        $parameter = Parameter::where('parameter_type', '=', 'quotation_running_no')->first();

        $running_no = (int)$parameter->parameter_value_1 + 1;
        $parameter->parameter_value_1 = $running_no;
        $parameter->save();
           
        $branchInfo = BranchController::manageBranchAccess();

        return view('dashboard.quotation-generator-v2.create', [
                                                    'parameter' => $parameter,
                                                    'current_user' => $current_user,
                                                    'branchInfo' => $branchInfo['branch'],
                                                    'quotation_template' => $quotation_template]);
    }

    public function quotationGeneratorEdit(Request $request, $id)
    {

        $current_user = auth()->user();
        $allow_add_item = true;

        if(AccessController::UserAccessPermissionController(PermissionController::QuotationGeneratorPermission()) == false)
        {
            return redirect()->route('dashboard.index');
        }

        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();

        $quotation_template = QuotationTemplateMain::where('status', '=', 1)->get();
        $QuotationGeneratorMain = QuotationGeneratorMain::where('status', '=', 1);

        $QuotationGeneratorMain = DB::table('quotation_generator_main AS a')
        ->leftJoin('users AS u', 'u.id', '=', 'a.user_id')
        ->select('a.*', 'u.name as user')
        ->orderBy('id', 'ASC')
        ->where('a.status', '=', 1);

        if (in_array($current_user->menuroles, ['sales','account', 'lawyer', 'clerk', 'chambering']) || in_array($current_user->id, [2]))
        {
            $QuotationGeneratorMain = $QuotationGeneratorMain->where('user_id', '=', $current_user->id);
        }

        $QuotationGeneratorMain = $QuotationGeneratorMain->paginate(20);

        $QuotationGeneratorMain = QuotationGeneratorMain::where('id', '=', $id)->first();
        $QuotationGeneratorDetails = QuotationGeneratorDetails::where('quo_gen_main_template_id', '=', $id)->get();
        


        $transaction = DB::table('transaction as t')
        ->join('account as a', 'a.id', '=', 't.account_details_id')
        ->leftJoin('banks as b', 'b.id', '=', 't.bank_id')
        ->select('t.*', 'a.name', 'b.name as bank_name')
        ->get();

        $parameter = Parameter::where('parameter_type', '=', 'quotation_running_no')->first();

        $running_no = (int)$parameter->parameter_value_1 + 1;
        $parameter->parameter_value_1 = $running_no;
        $parameter->save();

        if($current_user->id == 38)
        {
            $Branch = Branch::where('id', 1)->first();
        }
        else
        {
            $Branch = Branch::where('id', $current_user->branch_id)->first();
        }
           
        $branchInfo = BranchController::manageBranchAccess();

        return view('dashboard.quotation-generator-v2.edit', ['transactions' => $transaction,
                                                    'parameter' => $parameter,
                                                    'current_user' => $current_user,
                                                    'allow_add_item' => $allow_add_item,
                                                    'branchInfo' => $branchInfo['branch'],
                                                    'QuotationGeneratorMain' => $QuotationGeneratorMain, 
                                                    'QuotationGeneratorDetails' => $QuotationGeneratorDetails, 
                                                    'quotation_template' => $quotation_template]);
    }

    public function copyTemplate(Request $request, $id)
    {

        $current_user = auth()->user();
        $allow_add_item = true;

        if (in_array($current_user->menuroles, ['clerk', 'receptionist', 'maker', 'lawyer']))
        {
            if (!in_array($current_user->id, [89,13,88,109, 14, 38,122]))
            {
                return redirect()->route('case.index');
            }
        }


        $quotation_template = QuotationTemplateMain::where('status', '=', 1)->get();
        $QuotationGeneratorMain = QuotationGeneratorMain::where('status', '=', 1);

        $QuotationGeneratorMain = DB::table('quotation_generator_main AS a')
        ->leftJoin('users AS u', 'u.id', '=', 'a.user_id')
        ->select('a.*', 'u.name as user')
        ->orderBy('id', 'ASC')
        ->where('a.status', '=', 1);

        if (in_array($current_user->menuroles, ['sales','account', 'lawyer', 'clerk', 'chambering']) || in_array($current_user->id, [2]))
        {
            $QuotationGeneratorMain = $QuotationGeneratorMain->where('user_id', '=', $current_user->id);
        }

        $QuotationGeneratorMain = $QuotationGeneratorMain->paginate(20);

        $QuotationGeneratorMain = QuotationGeneratorMain::where('id', '=', $id)->first();
        $QuotationGeneratorDetails = QuotationGeneratorDetails::where('quo_gen_main_template_id', '=', $id)->get();
        
        $transaction = DB::table('transaction as t')
        ->join('account as a', 'a.id', '=', 't.account_details_id')
        ->leftJoin('banks as b', 'b.id', '=', 't.bank_id')
        ->select('t.*', 'a.name', 'b.name as bank_name')
        ->get();

        $parameter = Parameter::where('parameter_type', '=', 'quotation_running_no')->first();

        $running_no = (int)$parameter->parameter_value_1 + 1;
        $parameter->parameter_value_1 = $running_no;
        $parameter->save();

        if($current_user->id == 38)
        {
            $Branch = Branch::where('id', 1)->first();
        }
        else
        {
            $Branch = Branch::where('id', $current_user->branch_id)->first();
        }
           
        $branchInfo = BranchController::manageBranchAccess();

        return view('dashboard.quotation-generator-v2.create', ['transactions' => $transaction,
                                                    'parameter' => $parameter,
                                                    'current_user' => $current_user,
                                                    'allow_add_item' => $allow_add_item,
                                                    'branchInfo' => $branchInfo['branch'],
                                                    'QuotationGeneratorMain' => $QuotationGeneratorMain, 
                                                    'QuotationGeneratorDetails' => $QuotationGeneratorDetails, 
                                                    'quotation_template' => $quotation_template]);
    }


    public function generateQuotationPrint(Request $request)
    {
        if ($request->input('account_list_1') != null) {
            $account_list_1 = json_decode($request->input('account_list_1'), true);
            $account_list_2 = json_decode($request->input('account_list_2'), true);
            $account_list_3 = json_decode($request->input('account_list_3'), true);
            $account_list_4 = json_decode($request->input('account_list_4'), true);
        }

        $bln_discount = $request->input('bln_discount');
        $discount = $request->input('discount');
        $sst_rate = $request->input('sst_percentage');

        if (count($account_list_1) > 0) {
        }

        return response()->json([
            'view' => view('dashboard.quotation-generator-v2.table.tbl-quotation-p', compact('account_list_1', 'account_list_2', 'account_list_3', 'account_list_4', 'bln_discount','discount', 'sst_rate'))->render(),
            'parameter' => $account_list_4,
        ]);
    }

    public function quotationGenAddAccountItem(Request $request, $id)
    {
        $category = AccountCategory::where('status', '=', 1)->get();
        
        $allow_add_item = true;
        
        $purchase_price =  $request->input('purchase_price');
        $loan_sum =  $request->input('loan_sum');
        $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)->get();


        $quotation = array();

        for ($i = 0; $i < count($category); $i++) {

            $QuotationTemplateDetails = DB::table('quotation_generator_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.formula as account_formula', 'a.min as account_min', 'a.max as max', 'a.amount as account_amt', 'a.id as account_item_id', 'a.remark as item_desc','a.pfee_item_desc')
                ->where('qd.quo_gen_main_template_id', '=',  $id)
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->orderBy('order_no', 'ASC')
                ->get();

            array_push($quotation,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));
        }

        
        $quotationSelect = array();

        // return $request->input('template_id');

        for ($i = 0; $i < count($category); $i++) {

            $QuotationTemplateDetails = DB::table('quotation_template_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.formula as account_formula', 'a.min as account_min', 'a.max as max', 'a.amount as account_amt', 'a.id as account_item_id', 'a.remark as item_desc')
                ->where('qd.acc_main_template_id', '=',  $request->input('template_id'))
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->orderBy('a.name', 'ASC')
                ->get();

                // DB::table('quotation_template_details AS qd')
                // ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                // ->select('qd.*', 'a.name as account_name', 'a.account_cat_id as account_cat_id', 'a.min as account_min', 'a.id as account_item_id', 'a.amount as default_amt')
                // ->where('qd.acc_main_template_id', '=',  $quotation_template_id)
                // ->whereNotIn('qd.account_item_id', $item_id)
                // ->where('qd.status', '=',  1)
                // ->get();

            array_push($quotationSelect,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));
        }



        return response()->json([
            'view' => view('dashboard.quotation-generator-v2.table.tbl-template-bill-list', compact('quotation','purchase_price','loan_sum', 'allow_add_item'))->render(),
            'view2' => view('dashboard.quotation-generator-v2.controller.select-account-item', compact('quotationSelect'))->render(),
        ]);
    }

    public function quotationGeneratorbak()
    {

        $current_user = auth()->user();

        if (in_array($current_user->menuroles, ['clerk', 'receptionist', 'maker', 'lawyer']))
        {
            if (!in_array($current_user->id, [89,13,88,109, 14, 38,122]))
            {
                return redirect()->route('case.index');
            }
        }

        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();

        $quotation_template = QuotationTemplateMain::where('status', '=', 1)->get();
        $QuotationGeneratorMain = QuotationGeneratorMain::where('status', '=', 1);

        $QuotationGeneratorMain = DB::table('quotation_generator_main AS a')
        ->leftJoin('users AS u', 'u.id', '=', 'a.user_id')
        ->select('a.*', 'u.name as user')
        ->orderBy('id', 'ASC')
        ->where('a.status', '=', 1);

        if (in_array($current_user->menuroles, ['sales','account', 'lawyer', 'clerk', 'chambering']) || in_array($current_user->id, [2]))
        {
            $QuotationGeneratorMain = $QuotationGeneratorMain->where('user_id', '=', $current_user->id);
        }

        $QuotationGeneratorMain = $QuotationGeneratorMain->paginate(20);


        $transaction = DB::table('transaction as t')
        ->join('account as a', 'a.id', '=', 't.account_details_id')
        ->leftJoin('banks as b', 'b.id', '=', 't.bank_id')
        ->select('t.*', 'a.name', 'b.name as bank_name')
        ->get();

        $parameter = Parameter::where('parameter_type', '=', 'quotation_running_no')->first();

        $running_no = (int)$parameter->parameter_value_1 + 1;
        $parameter->parameter_value_1 = $running_no;
        $parameter->save();

        if($current_user->id == 38)
        {
            $Branch = Branch::where('id', 1)->first();
        }
        else
        {
            $Branch = Branch::where('id', $current_user->branch_id)->first();
        }
           

        return view('dashboard.quotation-generator.index', ['transactions' => $transaction,
                                                    'parameter' => $parameter,
                                                    'current_user' => $current_user,
                                                    'Branch' => $Branch,
                                                    'QuotationGeneratorMain' => $QuotationGeneratorMain, 
                                                    'quotation_template' => $quotation_template]);
    }

    public function loadSavedQuotationTemplate(Request $request, $id)
    {
        // $caseTemplateDetail = AccountCategory::where('template_main_id', '=', $request->input('template_id'))->get()->sortBy('process_number');

        // $loanCase = LoanCase::where('id', '=', $request->input('case_id'))->first();

        $category = AccountCategory::where('status', '=', 1)->OrderBy("order","asc")->get();
        $purchase_price =  $request->input('purchase_price');
        $loan_sum =  $request->input('loan_sum');
        $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)->get();

        
        $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)->get();


        $quotation = array();

        for ($i = 0; $i < count($category); $i++) {

            // $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)
            //     ->where('account_cat_id', '=', $category[$i]->id)
            //     ->get();

            $QuotationTemplateDetails = DB::table('quotation_generator_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.formula as account_formula', 'a.min as account_min', 'a.max as max', 'a.amount as account_amt', 'a.id as account_item_id', 'a.remark as item_desc')
                ->where('qd.acc_main_template_id', '=',  $id)
                
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->orderBy('order_no', 'ASC')
                ->get();

            array_push($quotation,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));
        }

        return 1;

        // $parameter = Parameter::where('parameter_type', '=', 'quotation_running_no')->first();

        //     $running_no = (int)$parameter->parameter_value_1 + 1;
        //     $parameter->parameter_value_1 = $running_no;
        //     $parameter->save();



        return response()->json([
            'view' => view('dashboard.quotation-generator.table.tbl-bill-list', compact('quotation','purchase_price','loan_sum'))->render(),
            'view2' => view('dashboard.quotation-generator.table.tbl-case-quotation-p', compact('quotation','purchase_price','loan_sum'))->render(),
            // 'parameter' => $parameter,
        ]);

        // return  $users;
    }

    public function loadQuotationTemplateGenerator(Request $request, $id)
    {
        // $caseTemplateDetail = AccountCategory::where('template_main_id', '=', $request->input('template_id'))->get()->sortBy('process_number');

        // $loanCase = LoanCase::where('id', '=', $request->input('case_id'))->first();

        $category = AccountCategory::where('status', '=', 1)->orderBy('order', 'asc')->get();
        $purchase_price =  $request->input('purchase_price');
        $loan_sum =  $request->input('loan_sum');
        $QuotationTemplateMain = QuotationTemplateMain::where('id', '=', $id)->first();

        
        $sst_rate =  $request->input('sst_rate');


        $quotation = array();

        for ($i = 0; $i < count($category); $i++) {

            // $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)
            //     ->where('account_cat_id', '=', $category[$i]->id)
            //     ->get();

            $QuotationTemplateDetails = DB::table('quotation_template_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.name_cn as account_name_cn',  'a.formula as account_formula', 'a.min as account_min', 'a.max as max', 'a.amount as account_amt', 'a.id as account_item_id', 'a.remark as item_desc','a.pfee_item_desc')
                ->where('qd.acc_main_template_id', '=',  $id)
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->orderBy('order_no', 'ASC')
                ->get();

            array_push($quotation,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));
        }

        // $parameter = Parameter::where('parameter_type', '=', 'quotation_running_no')->first();

        //     $running_no = (int)$parameter->parameter_value_1 + 1;
        //     $parameter->parameter_value_1 = $running_no;
        //     $parameter->save();



        return response()->json([
            'view' => view('dashboard.quotation-generator-v2.table.tbl-template-bill-list', compact('QuotationTemplateMain','quotation','purchase_price','loan_sum', 'sst_rate'))->render(),
            // 'view2' => view('dashboard.quotation-generator-v2.controller.select-account-item', compact('quotation','purchase_price','loan_sum'))->render(),
            // 'view2' => view('dashboard.quotation-generator.table.tbl-case-quotation-p', compact('quotation','purchase_price','loan_sum'))->render(),
            // 'parameter' => $parameter,
        ]);

        // return  $users;
    }

    function saveQuotationTemplate(Request $request)
    {
        $QuotationGeneratorMain  = new QuotationGeneratorMain();

        $current_user = auth()->user();

        $QuotationGeneratorMain->template_id = $request->input('template_id');
        $QuotationGeneratorMain->loan_sum = $request->input('loan_sum');
        $QuotationGeneratorMain->template_date = $request->input('template_date');
        $QuotationGeneratorMain->name = $request->input('template_name');
        $QuotationGeneratorMain->bill_to = $request->input('bill_to');
        $QuotationGeneratorMain->purchase_price = $request->input('purchase_price');
        $QuotationGeneratorMain->bln_discount = $request->input('bln_discount');
        $QuotationGeneratorMain->bln_firmname = $request->input('bln_firmname');
        $QuotationGeneratorMain->discount = $request->input('discount');
        $QuotationGeneratorMain->sst_rate = $request->input('sst_rate');
        $QuotationGeneratorMain->status =  1;
        $QuotationGeneratorMain->user_id =  $current_user->id;
        $QuotationGeneratorMain->created_at = date('Y-m-d H:i:s');
        $QuotationGeneratorMain->save();

        if ($request->input('bill_list') != null) {
            $billList = json_decode($request->input('bill_list'), true);
        }

        if (count($billList) > 0) {

            for ($i = 0; $i < count($billList); $i++) {

                $QuotationGeneratorDetails = new QuotationGeneratorDetails();

                $QuotationGeneratorDetails->quo_gen_main_template_id = $QuotationGeneratorMain->id;
                $QuotationGeneratorDetails->account_item_id = $billList[$i]['account_item_id'];
                $QuotationGeneratorDetails->order_no = $billList[$i]['order_no'];
                $QuotationGeneratorDetails->min = $billList[$i]['min'];
                $QuotationGeneratorDetails->max = $billList[$i]['max'];
                $QuotationGeneratorDetails->amount = (float)$billList[$i]['amount'];

                if(isset( $billList[$i]['item_desc']))
                {
                    $QuotationGeneratorDetails->remark = $billList[$i]['item_desc'];
                }
                else
                {
                    $QuotationGeneratorDetails->remark = '';
                }
                
                // $QuotationGeneratorDetails->order_no = $billList[$i]['order_no'];
                $QuotationGeneratorDetails->status = 1;
                $QuotationGeneratorDetails->created_at = date('Y-m-d H:i:s');
                $QuotationGeneratorDetails->save();
            }
        }

        return response()->json(['status' => 1, 'data' => 'Saved']);
    }


    function updateQuotationTemplate(Request $request,$id)
    {
        $QuotationGeneratorMain  = new QuotationGeneratorMain();

        $QuotationGeneratorMain = QuotationGeneratorMain::where('id', '=', $id)->first();

        $current_user = auth()->user();

        $QuotationGeneratorMain->template_id = $request->input('template_id');
        $QuotationGeneratorMain->loan_sum = $request->input('loan_sum');
        $QuotationGeneratorMain->template_date = $request->input('template_date');
        $QuotationGeneratorMain->name = $request->input('template_name');
        $QuotationGeneratorMain->bill_to = $request->input('bill_to');
        $QuotationGeneratorMain->purchase_price = $request->input('purchase_price');
        $QuotationGeneratorMain->bln_discount = $request->input('bln_discount');
        $QuotationGeneratorMain->discount = $request->input('discount');
        $QuotationGeneratorMain->bln_firmname = $request->input('bln_firmname');
        $QuotationGeneratorMain->status =  1;
        $QuotationGeneratorMain->user_id =  $current_user->id;
        $QuotationGeneratorMain->sst_rate =  $request->input('ddl_sst');
        $QuotationGeneratorMain->created_at = date('Y-m-d H:i:s');
        $QuotationGeneratorMain->save();

        if ($request->input('bill_list') != null) {
            $billList = json_decode($request->input('bill_list'), true);
        }

        if (count($billList) > 0) {

            // for ($i = 0; $i < count($billList); $i++) {

            //     $QuotationGeneratorDetails = new QuotationGeneratorDetails();
            //     $QuotationGeneratorDetails = QuotationGeneratorDetails::where('id', '=', $billList[$i]['itemID'])->first();

            //     $QuotationGeneratorDetails->account_item_id = $billList[$i]['account_item_id'];
            //     $QuotationGeneratorDetails->min = $billList[$i]['min'];
            //     $QuotationGeneratorDetails->max = $billList[$i]['max'];
            //     $QuotationGeneratorDetails->amount = (float)$billList[$i]['amount'];
            //     // $QuotationGeneratorDetails->order_no = $billList[$i]['order_no'];
            //     $QuotationGeneratorDetails->status = 1;
            //     $QuotationGeneratorDetails->updated_at = date('Y-m-d H:i:s');
            //     $QuotationGeneratorDetails->save();
            // }

            $QuotationGeneratorDetails = QuotationGeneratorDetails::where('quo_gen_main_template_id', '=', $id)->delete();

            for ($i = 0; $i < count($billList); $i++) {

                $QuotationGeneratorDetails = new QuotationGeneratorDetails();

                $QuotationGeneratorDetails->quo_gen_main_template_id = $id;
                $QuotationGeneratorDetails->account_item_id = $billList[$i]['account_item_id'];
                $QuotationGeneratorDetails->min = $billList[$i]['min'];
                $QuotationGeneratorDetails->max = $billList[$i]['max'];
                $QuotationGeneratorDetails->amount = (float)$billList[$i]['amount'];

                if(isset( $billList[$i]['item_desc']))
                {
                    $QuotationGeneratorDetails->remark = $billList[$i]['item_desc'];
                }
                else
                {
                    $QuotationGeneratorDetails->remark = '';
                }
                
                // $QuotationGeneratorDetails->order_no = $billList[$i]['order_no'];
                $QuotationGeneratorDetails->status = 1;
                $QuotationGeneratorDetails->created_at = date('Y-m-d H:i:s');
                $QuotationGeneratorDetails->save();
            }
        }

        return response()->json(['status' => 1, 'data' => 'Saved']);
    }

    function deleteSavedQuotation(Request $request,$id)
    {
        $QuotationGeneratorDetails = QuotationGeneratorDetails::where('quo_gen_main_template_id', '=', $id)->delete();
        $QuotationGeneratorMain = QuotationGeneratorMain::where('id', '=', $id)->delete();

        return response()->json(['status' => 1, 'data' => 'Saved']);
    }

    public function loadSavedQuotationTemplateGenerator(Request $request, $id)
    {

        $category = AccountCategory::where('status', '=', 1)->OrderBy('order','asc')->get();
        
        $allow_add_item = true;
        
        $purchase_price =  $request->input('purchase_price');
        $loan_sum =  $request->input('loan_sum');
        $sst_rate =  $request->input('sst_rate');
        $QuotationTemplateMain = QuotationTemplateMain::where('id', $request->input('template_id'))->first();


        $quotation = array();

        for ($i = 0; $i < count($category); $i++) {

            $QuotationTemplateDetails = DB::table('quotation_generator_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.name_cn as account_name_cn', 'a.formula as account_formula', 'a.min as account_min', 'a.max as max', 'a.amount as account_amt', 'a.id as account_item_id', 'a.remark as item_desc','a.pfee_item_desc')
                ->where('qd.quo_gen_main_template_id', '=',  $id)
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->orderBy('order_no', 'ASC')
                ->get();

            array_push($quotation,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));
        }

        
        $quotationSelect = array();

        // return $request->input('template_id');

        for ($i = 0; $i < count($category); $i++) {

            $QuotationTemplateDetails = DB::table('quotation_template_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.name_cn as account_name_cn', 'a.formula as account_formula', 'a.min as account_min', 'a.max as max', 'a.amount as account_amt', 'a.id as account_item_id', 'a.remark as item_desc')
                ->where('qd.acc_main_template_id', '=',  $request->input('template_id'))
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->orderBy('a.name', 'ASC')
                ->get();

                // DB::table('quotation_template_details AS qd')
                // ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                // ->select('qd.*', 'a.name as account_name', 'a.account_cat_id as account_cat_id', 'a.min as account_min', 'a.id as account_item_id', 'a.amount as default_amt')
                // ->where('qd.acc_main_template_id', '=',  $quotation_template_id)
                // ->whereNotIn('qd.account_item_id', $item_id)
                // ->where('qd.status', '=',  1)
                // ->get();

            array_push($quotationSelect,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));
        }



        return response()->json([
            'view' => view('dashboard.quotation-generator-v2.table.tbl-template-saved-bill-list', compact('QuotationTemplateMain','quotation','purchase_price','loan_sum', 'allow_add_item', 'sst_rate'))->render(),
            'view2' => view('dashboard.quotation-generator-v2.controller.select-account-item', compact('QuotationTemplateMain','quotationSelect'))->render(),
        ]);

        // return  $users;
    }

    public function loadSavedQuotationTemplateGeneratorBak(Request $request, $id)
    {

        $category = AccountCategory::where('status', '=', 1)->get();
        
        $QuotationGeneratorMain = QuotationGeneratorMain::where('id', '=', $id)->first();
        $purchase_price =  $request->input('purchase_price');
        $loan_sum =  $request->input('loan_sum');
        $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)->get();


        $quotation = array();

        for ($i = 0; $i < count($category); $i++) {

            $QuotationTemplateDetails = DB::table('quotation_generator_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.formula as account_formula', 'a.min as account_min', 'a.max as max', 'a.amount as account_amt', 'a.id as account_item_id', 'a.remark as item_desc')
                ->where('qd.quo_gen_main_template_id', '=',  $id)
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->orderBy('order_no', 'ASC')
                ->get();

            array_push($quotation,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));
        }


        return response()->json([
            'view' => view('dashboard.quotation-generator-v2.table.tbl-template-bill-list', compact('quotation','purchase_price','loan_sum'))->render(),
            'view2' => view('dashboard.quotation-generator.table.tbl-case-quotation-p', compact('quotation','purchase_price','loan_sum'))->render(),
            'QuotationGeneratorMain' => $QuotationGeneratorMain,
        ]);

        // return  $users;
    }

    function logPrintedQuotation(Request $request)
    {
        Helper::logAction('Quotation', ' Printed '.$request->input('quotation_no'));
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

    public function checkPageLocked(Request $request)
    {
        $result = "0";

        $docPage = DocumentTemplatePages::where('id', '=', $request->input('id'))->first();

        $docTemplatePages = DB::table('document_template_pages')
            ->leftJoin('users', 'users.id', '=', 'document_template_pages.is_locked')
            ->select('document_template_pages.*', 'users.name')
            ->get();

        // if ($docPage->is_locked != "0")
        // {
        //     $lock_user = Users::where('id', '=', $docPage->is_locked)->get();
        //     $result = $lock_user[0]->name." is editing this page";
        // }

        return $docTemplatePages;
    }

    public function createNewPage(Request $request)
    {

        $page = DB::table('document_template_pages')
            ->select(array('page'))
            ->where('document_template_details_id', '=', $request->input('page'))
            ->orderBy('page', 'DESC')
            ->first();


        // $templateEmail->content = $request->input('content');
        // $templateEmail->save();


        return $page;
    }

    public function updatePage(Request $request)
    {



        $pageId = $request->input('pageId');
        $templateEmail = null;

        if ($pageId == "0") {
            $page = DB::table('document_template_pages')
                ->select(array('page'))
                ->where('document_template_details_id', '=', $request->input('template_id'))
                ->orderBy('page', 'DESC')
                ->get();

            $templateEmail  = new DocumentTemplatePages();
            $templateEmail->document_template_details_id = $request->input('template_id');
            $templateEmail->is_locked = 0;
            $templateEmail->page = $page[0]->page + 1;
            $templateEmail->status = 1;
        } else {
            $templateEmail = DocumentTemplatePages::where('id', '=', $request->input('pageId'))->first();
        }

        $templateEmail->content = $request->input('content');
        $templateEmail->save();


        return $templateEmail;
    }

    public function updateLockStatus(Request $request)
    {
        // $request->content;
        $current_user = auth()->user();

        $templateEmail = DocumentTemplatePages::where('id', '=', $request->input('id'))->first();

        if ($request->input('is_locked') == '1') {
            $templateEmail->is_locked = $current_user->id;;
        } else {
            $templateEmail->is_locked = 0;
        }

        $templateEmail->save();


        return  'test';
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
    public function destroy($id, Request $request)
    {
        $menu = MenuLangList::where('id', '=', $id)->first();
        $menusLang = MenusLang::where('lang', '=', $menu->short_name)->first();
        if (!empty($menusLang)) {
            $request->session()->flash('message', "Can't delete. Language has one or more assigned tranlsation of menu element");
            $request->session()->flash('back', 'todolist.index');
            return view('dashboard.shared.universal-info');
        } else {
            $menus = MenuLangList::all();
            if (count($menus) <= 1) {
                $request->session()->flash('message', "Can't delete. This is last language on the list");
                $request->session()->flash('back', 'todolist.index');
                return view('dashboard.shared.universal-info');
            } else {
                if ($menu->is_default == true) {
                    $request->session()->flash('message', "Can't delete. This is default language");
                    $request->session()->flash('back', 'todolist.index');
                    return view('dashboard.shared.universal-info');
                } else {
                    $menu->delete();
                    $request->session()->flash('message', 'Successfully deleted language');
                    $request->session()->flash('back', 'todolist.index');
                    return view('dashboard.shared.universal-info');
                }
            }
        }
    }

    /**
     * Generate Quotation PDF from quotation generator
     */
    public function generateQuotation($id)
    {
        try {
            $QuotationGeneratorMain = QuotationGeneratorMain::where('id', '=', $id)->where('status', '=', 1)->first();
            
            if (!$QuotationGeneratorMain) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Quotation not found'
                ], 404);
            }

            $current_user = auth()->user();
            $category = AccountCategory::where('status', '=', 1)->orderBy('order', 'asc')->get();
            
            // Get branch info
            $Branch = Branch::where('id', '=', $current_user->branch_id)->first();
            if (!$Branch) {
                $Branch = Branch::where('id', '=', 1)->first();
            }
            
            // Organize quotation details by category (similar to how print works)
            $account_list_1 = []; // Professional fees
            $account_list_2 = []; // Stamp duties
            $account_list_3 = []; // Disbursement
            $account_list_4 = []; // Reimbursement
            
            $sst_rate = $QuotationGeneratorMain->sst_rate ?? 6;
            
            for ($i = 0; $i < count($category); $i++) {
                $QuotationGeneratorDetails = DB::table('quotation_generator_details AS qd')
                    ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                    ->select('qd.*', 'a.name as account_name', 'a.formula as account_formula', 'a.min as account_min', 'a.max as max', 'a.amount as account_amt', 'a.id as account_item_id', 'a.remark as item_desc')
                    ->where('qd.quo_gen_main_template_id', '=', $id)
                    ->where('a.account_cat_id', '=', $category[$i]->id)
                    ->orderBy('order_no', 'ASC')
                    ->get();
                
                foreach ($QuotationGeneratorDetails as $detail) {
                    $item = [
                        'account_name' => $detail->account_name,
                        'amount' => $detail->amount,
                        'item_desc' => $detail->remark ?? $detail->item_desc ?? ''
                    ];
                    
                    // Categorize by account category
                    if ($category[$i]->id == 1) { // Professional fees
                        $account_list_1[] = $item;
                    } else if ($category[$i]->id == 2) { // Stamp duties
                        $account_list_2[] = $item;
                    } else if ($category[$i]->id == 3) { // Disbursement
                        $account_list_3[] = $item;
                    } else if ($category[$i]->id == 4) { // Reimbursement
                        $account_list_4[] = $item;
                    }
                }
            }
            
            // Generate filename
            $filename = 'Quotation_' . str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $QuotationGeneratorMain->name) . '_' . date('Y-m-d') . '.pdf';
            
            // Get quotation number
            $parameter = Parameter::where('parameter_type', '=', 'quotation_running_no')->first();
            $quotation_no = $current_user->nick_name . '_' . $parameter->parameter_value_1;
            
            // Build table HTML for quotation PDF
            $bln_discount = $QuotationGeneratorMain->bln_discount ?? 0;
            $discount = $QuotationGeneratorMain->discount ?? 0;
            
            $tableHtml = view('dashboard.quotation-generator-v2.table.tbl-quotation-p', compact(
                'account_list_1',
                'account_list_2',
                'account_list_3',
                'account_list_4',
                'sst_rate',
                'bln_discount',
                'discount'
            ))->render();
            
            // Get quotation number
            $parameter = Parameter::where('parameter_type', '=', 'quotation_running_no')->first();
            $quotation_no = $current_user->nick_name . '_' . $parameter->parameter_value_1;
            
            // Create simplified quotation HTML
            $fullHtml = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 portrait;
        }
        body { 
            font-family: Arial, sans-serif; 
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .page-header { 
            border-bottom: 2px solid #333; 
            padding-bottom: 10px; 
            margin-bottom: 20px; 
        }
        .invoice-info { 
            margin-bottom: 20px; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px;
            table-layout: fixed;
        }
        thead {
            display: table-header-group;
        }
        tbody {
            display: table-row-group;
        }
        th, td { 
            border: 1px solid #000; 
            padding: 8px; 
            word-wrap: break-word;
        }
        th { 
            background-color: #9fcff0; 
            font-weight: bold; 
        }
        .text-right { 
            text-align: right; 
        }
        address { 
            margin: 0; 
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h2>Quotation <small style="float: right;">Date: ' . date('d/m/Y') . '</small></h2>
    </div>
    
    <div class="invoice-info">
        <div style="width: 50%; float: left;">
            <b>From</b>
            <address>
                <strong style="color: #2d659d">' . htmlspecialchars($Branch->office_name) . '</strong><br>
                Advocates & Solicitors<br>
                ' . strip_tags($Branch->address) . '<br>
                <b>Phone</b>: ' . htmlspecialchars($Branch->tel_no) . ' <b>Fax</b>: ' . htmlspecialchars($Branch->fax) . '<br>
                <b>Email</b>: ' . htmlspecialchars($Branch->email) . '
            </address>
        </div>
        <div style="width: 50%; float: right; text-align: right;">
            <b>To</b>
            <address>
                <strong style="color: #0066cc">' . htmlspecialchars($QuotationGeneratorMain->bill_to ?? '') . '</strong>
            </address>
        </div>
        <div style="clear: both;"></div>
        <div style="margin-top: 20px;">
            <div style="float: left;"><b>Quotation No: </b>' . htmlspecialchars($quotation_no) . '</div>
            <div style="clear: both;"></div>
        </div>
    </div>
    
    <table class="table table-striped" style="width: 100%; border-collapse: collapse;">
    ' . $tableHtml . '
    </table>
</body>
</html>';
            
            try {
                $pdf = Pdf::loadHTML($fullHtml)->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => false,
                    'chroot' => public_path(),
                ]);
            } catch (\Exception $pdfError) {
                Log::error('Quotation PDF Generation Error: ' . $pdfError->getMessage());
                return response()->json([
                    'status' => 0,
                    'message' => 'Error generating PDF: ' . $pdfError->getMessage()
                ], 500);
            }
            
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Invoice PDF from quotation
     */
    public function generateInvoice($id)
    {
        try {
            $QuotationGeneratorMain = QuotationGeneratorMain::where('id', '=', $id)->where('status', '=', 1)->first();
            
            if (!$QuotationGeneratorMain) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Quotation not found'
                ], 404);
            }

            $current_user = auth()->user();
            $category = AccountCategory::where('status', '=', 1)->orderBy('order', 'asc')->get();
            
            $quotation_v3 = array();
            
            for ($i = 0; $i < count($category); $i++) {
                $QuotationGeneratorDetails = DB::table('quotation_generator_details AS qd')
                    ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                    ->select('qd.*', 'a.name as account_name', 'a.name_cn as account_name_cn', 'a.formula as account_formula', 'a.min as account_min', 'a.id as account_item_id', 'a.pfee1_item', 'a.remark as item_desc', 'qd.remark as item_remark')
                    ->where('qd.quo_gen_main_template_id', '=', $id)
                    ->where('a.account_cat_id', '=', $category[$i]->id)
                    ->orderBy('order_no', 'ASC')
                    ->get();
                
                if (count($QuotationGeneratorDetails) > 0) {
                    array_push($quotation_v3, array('row' => 'title', 'category' => $category[$i], 'account_details' => []));
                    
                    for ($j = 0; $j < count($QuotationGeneratorDetails); $j++) {
                        array_push($quotation_v3, array('row' => 'item', 'category' => $category[$i], 'account_details' => $QuotationGeneratorDetails[$j]));
                    }
                    
                    array_push($quotation_v3, array('row' => 'subtotal', 'category' => $category[$i], 'account_details' => []));
                }
            }
            
            $pieces = array_chunk($quotation_v3, 30);
            
            // Get branch info
            $Branch = Branch::where('id', '=', $current_user->branch_id)->first();
            if (!$Branch) {
                $Branch = Branch::where('id', '=', 1)->first();
            }
            
            // Generate filename
            $filename = 'Invoice_' . $QuotationGeneratorMain->name . '_' . date('Y-m-d') . '.pdf';
            
            // Use invoice print template
            $fullHtml = view('dashboard.case.d-invoice-print-simple', compact(
                'current_user', 
                'Branch', 
                'quotation_v3', 
                'pieces',
                'QuotationGeneratorMain'
            ))->render();
            
            try {
                $pdf = Pdf::loadHTML($fullHtml)->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => false,
                    'chroot' => public_path(),
                ]);
            } catch (\Exception $pdfError) {
                Log::error('Invoice PDF Generation Error: ' . $pdfError->getMessage());
                return response()->json([
                    'status' => 0,
                    'message' => 'Error generating PDF: ' . $pdfError->getMessage()
                ], 500);
            }
            
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate Proforma Invoice PDF from quotation
     */
    public function generateProformaInvoice($id)
    {
        try {
            $QuotationGeneratorMain = QuotationGeneratorMain::where('id', '=', $id)->where('status', '=', 1)->first();
            
            if (!$QuotationGeneratorMain) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Quotation not found'
                ], 404);
            }

            $current_user = auth()->user();
            $category = AccountCategory::where('status', '=', 1)->orderBy('order', 'asc')->get();
            
            $quotation_v3 = array();
            
            for ($i = 0; $i < count($category); $i++) {
                $QuotationGeneratorDetails = DB::table('quotation_generator_details AS qd')
                    ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                    ->select('qd.*', 'a.name as account_name', 'a.name_cn as account_name_cn', 'a.formula as account_formula', 'a.min as account_min', 'a.id as account_item_id', 'a.pfee1_item', 'a.remark as item_desc', 'qd.remark as item_remark')
                    ->where('qd.quo_gen_main_template_id', '=', $id)
                    ->where('a.account_cat_id', '=', $category[$i]->id)
                    ->orderBy('order_no', 'ASC')
                    ->get();
                
                if (count($QuotationGeneratorDetails) > 0) {
                    array_push($quotation_v3, array('row' => 'title', 'category' => $category[$i], 'account_details' => []));
                    
                    for ($j = 0; $j < count($QuotationGeneratorDetails); $j++) {
                        array_push($quotation_v3, array('row' => 'item', 'category' => $category[$i], 'account_details' => $QuotationGeneratorDetails[$j]));
                    }
                    
                    array_push($quotation_v3, array('row' => 'subtotal', 'category' => $category[$i], 'account_details' => []));
                }
            }
            
            $pieces = array_chunk($quotation_v3, 30);
            
            // Get branch info
            $Branch = Branch::where('id', '=', $current_user->branch_id)->first();
            if (!$Branch) {
                $Branch = Branch::where('id', '=', 1)->first();
            }
            
            // Generate filename
            $filename = 'Proforma_Invoice_' . $QuotationGeneratorMain->name . '_' . date('Y-m-d') . '.pdf';
            
            // Use proforma invoice print template
            $fullHtml = view('dashboard.case.d-quotation-print-simple', compact(
                'current_user', 
                'Branch', 
                'quotation_v3', 
                'pieces',
                'QuotationGeneratorMain'
            ))->render();
            
            try {
                $pdf = Pdf::loadHTML($fullHtml)->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => false,
                    'chroot' => public_path(),
                ]);
            } catch (\Exception $pdfError) {
                Log::error('Proforma Invoice PDF Generation Error: ' . $pdfError->getMessage());
                return response()->json([
                    'status' => 0,
                    'message' => 'Error generating PDF: ' . $pdfError->getMessage()
                ], 500);
            }
            
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }
}

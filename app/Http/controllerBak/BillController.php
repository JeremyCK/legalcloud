<?php

namespace App\Http\Controllers;

use App\Models\AccountCategory;
use App\Models\AccountTemplateDetails;
use App\Models\AccountTemplateMain;
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
use App\Models\LoanCase;
use App\Models\LoanCaseAccount;
use App\Models\LoanCaseBillMain;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;


class BillController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bills = DB::table('loan_case_bill_main')
            ->join('loan_case', 'loan_case.id', '=', 'loan_case_bill_main.case_id')
            ->select('loan_case_bill_main.*', 'loan_case.case_ref_no')
            ->get();

        return view('dashboard.bill.index', ['bills' => $bills]);
    }

    public function getBillList(Request $request)
    {
        if ($request->ajax()) {

            $bills = DB::table('loan_case_bill_main')
            ->join('loan_case', 'loan_case.id', '=', 'loan_case_bill_main.case_id')
            ->join('client', 'client.id', '=', 'loan_case.customer_id')
            ->select('loan_case.case_ref_no', 'loan_case_bill_main.*', 'client.name as client_name' )
            ->get();


            return DataTables::of($bills)
                ->addIndexColumn()
                ->addColumn('action', function ($row)  {
                    $actionBtn = ' <a  href="/bill/' . $row->id . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>
                    ';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
                
        }
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Roles::where('status', '=', '1')->get();

        return view('dashboard.documentTemplate.create', [
            'templates' => CaseTemplate::all(),
            'roles' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $to = '';
        $from = '';
        $cc = '';

        if (!empty($request->input('emailTo'))) {
            $to = implode(",", $request->input('emailTo'));
        }

        if (!empty($request->input('emailFrom'))) {
            $from = implode(",", $request->input('emailFrom'));
        }

        if (!empty($request->input('emailCC'))) {
            $cc = implode(",", $request->input('emailCC'));
        }

        $templateEmail  = new EmailTemplateMain();

        $templateEmail->name = $request->input('name');
        $templateEmail->desc = $request->input('desc');
        $templateEmail->code = $request->input('code');
        $templateEmail->subject = $request->input('subject');
        $templateEmail->to = $to;
        $templateEmail->from = $from;
        $templateEmail->cc = $cc;
        $templateEmail->status =  $request->input('status');
        // $templateEmail->content = $request->input('summary-ckeditor');

        $templateEmail->save();


        if ($templateEmail->id != null && $templateEmail->id != '') {
            $templateEmailDetails  = new EmailTemplateDetails();

            $templateEmailDetails->email_template_id = $templateEmail->id;
            $templateEmailDetails->version_name = 'Orinal';
            $templateEmailDetails->content = $request->input('summary-ckeditor');
            $templateEmailDetails->status = $request->input('status');

            $templateEmailDetails->save();
        }



        // $lastId = DB::getPdo()->lastInsertId();

        $request->session()->flash('message', 'Successfully created template');
        return redirect()->route('email-template.index');

        // $validatedData = $request->validate([
        //     'name'             => 'required|min:1|max:64',
        //     'shortName'        => 'required|min:1|max:64',
        //     'is_default'       => 'required|in:true,false'
        // ]);
        // $menuLang = new MenuLangList();
        // $menuLang->name         = $request->input('name');
        // $menuLang->short_name   = $request->input('shortName');
        // if($request->input('is_default') === 'true'){
        //     $menuLangList->is_default = true;
        // }else{
        //     $menuLangList->is_default = false;
        // }
        // $menuLang->save();
        // $request->session()->flash('message', 'Successfully created language');
        // return redirect()->route('todolist.create');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        // $caseTemplateDetail = CaseTemplateDetails::all();

        $loanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->where('status', '=', 1)->first();

        if ($loanCaseBillMain)
        {
            $case = LoanCase::where('id', '=', $loanCaseBillMain->case_id)->where('status', '=', 1)->first();
            $customer = Customer::where('id', '=', $case->customer_id)->first();
        }

        

        $docTemplateDetailSelected = DocumentTemplateDetails::where('document_template_main_id', '=', $id)->where('status', '=', 1)->get();
        $caseMasterListCategory = CaseMasterListCategory::all();
        $caseMasterListField = CaseMasterListField::all();

        $docTemplatePages = DB::table('document_template_pages')
            ->leftJoin('users', 'users.id', '=', 'document_template_pages.is_locked')
            ->select('document_template_pages.*', 'users.name')
            ->get();

        $current_user = auth()->user();

        $account_template = AccountTemplateMain::where('id', '=', 1)->get();
        $account_template_details = AccountTemplateDetails::where('acc_main_template_id', '=', $id)->get();

        $account_template_cat = DB::table('loan_case_account')
            ->join('account_category', 'loan_case_account.account_cat_id', '=', 'account_category.id')
            ->select('account_category.id', 'account_category.category', 'taxable', 'percentage')
            ->distinct()
            ->groupBy('loan_case_account.id')
            ->where('loan_case_account.case_id', '=',1)
            ->get();

        $joinData = array();

        for ($i = 0; $i < count($account_template_cat); $i++) {

            $account_template_details_by_cat = LoanCaseAccount::where('case_id', '=', $loanCaseBillMain->case_id)
                ->where('account_cat_id', '=', $account_template_cat[$i]->id)
                ->get();
            array_push($joinData,  array('category' => $account_template_cat[$i], 'account_details' => $account_template_details_by_cat));
        }
        $category = AccountCategory::where('status', '=', 1)->get();
        $quotation = array();

        for ($i = 0; $i < count($category); $i++) {

            // $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)
            //     ->where('account_cat_id', '=', $category[$i]->id)
            //     ->get();

            $QuotationTemplateDetails = DB::table('loan_case_bill_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.formula as account_formula', 'a.min as account_min', 'a.id as account_item_id')
                ->where('qd.loan_case_main_bill_id', '=',  $id)
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->get();

            array_push($quotation,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));
        }

        // $docTemplatePage = DocumentTemplatePages::where('document_template_details_id', '=', $docTemplateDetailSelected[0]->id)->get();
        $docTemplateDetail = DocumentTemplateDetails::where('document_template_main_id', '=', $id)->get();
        $docTemplateMain = DocumentTemplateMain::where('id', '=', $id)->get();
        return view('dashboard.bill.show', [
            'case' => $case,
            'customer' => $customer,
            'loanCaseBillMain' => $loanCaseBillMain,
            'quotation' => $quotation,
            'account_template_with_cat' => $joinData,
            'caseMasterListCategory' => $caseMasterListCategory
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $roles = Roles::where('status', '=', '1')->get();
        $templateEmailDetails = DocumentTemplateDetails::where('document_template_main_id', '=', $id)->get();
        return view('dashboard.documentTemplate.edit', [
            'template' => DocumentTemplateMain::where('id', '=', $id)->first(),
            'templateEmailDetails' => $templateEmailDetails
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
}

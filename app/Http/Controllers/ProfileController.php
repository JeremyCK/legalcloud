<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountCategory;
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
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
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

        return view('dashboard.account.index', ['accounts' => $accounts]);
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

    public function changePasswordView()
    {
        $current_user = auth()->user();
        
        $user = User::find($current_user->id);
        return view('dashboard.admin.userChangePassword', compact('user'));
    }

    public function changePassword(Request $request)
    {
        $status = 1;
        $data = '';

        $current_user = auth()->user();
        $user = User::find($current_user->id);

        // validate current password
        if (!password_verify($request->input('current_password'), $user->password)) {
            $status = 0;
            $data = 'Invalid current password';
            return response()->json(['status' => $status, 'data' => $data]);
        } 

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        $data = 'Password updated';
        
        return response()->json(['status' => $status, 'data' => $data]);
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
}

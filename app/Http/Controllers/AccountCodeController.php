<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\AccountCode;
use App\Models\AccountItem;
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


class AccountCodeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $AccountCode = AccountCode::get();

        return view('dashboard.settings.account-code.index', [
            'AccountCode' => $AccountCode
        ]);
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

        return view('dashboard.settings.account-code.create', [
            'account_category' => $account_category
        ]);
    }

    public function store(Request $request)
    {
        $current_user = auth()->user();

        $account  = new AccountCode();

        $account->name = $request->input('name');
        $account->code = $request->input('code');
        $account->desc = $request->input('desc');
        $account->group = $request->input('group');
        $account->status = $request->input('status');
        $account->created_at = date('Y-m-d H:i:s');
        $account->created_by = $current_user->id;
        $account->save();

        $request->session()->flash('message', 'Successfully created new account code');
        return redirect()->route('account-code.index');
    }

    public function edit($id)
    {
        $AccountCode = AccountCode::where('id', '=', $id)->first();

        return view('dashboard.settings.account-code.edit', [
            'AccountCode' => $AccountCode
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
    public function update(Request $request, $id)
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
            $account  = AccountCode::where('id', '=', $id)->first();

            $account->name = $request->input('name');
            $account->code = $request->input('code');
            $account->desc = $request->input('desc');
            $account->group = $request->input('group');
            $account->status = $request->input('status');
            $account->updated_at = date('Y-m-d H:i:s');
            $account->updated_by = $current_user->id;
            $account->save();

            $message = 'Account code updated';
        } catch (\Throwable $e) {
            $status = 0;
            $message = $e;
        }

        $request->session()->flash('message', $message);
        return redirect()->route('account-code.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MenuLangList  $menuLangList
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
    }
}

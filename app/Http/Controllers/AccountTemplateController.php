<?php

namespace App\Http\Controllers;

use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\EmailTemplateMain;
use App\Models\DocumentTemplateMain;
use App\Models\DocumentTemplateDetails;
use App\Models\DocumentTemplatePages;
use App\Models\caseTemplate;
use App\Models\Roles;
use App\Models\AuditLog;
use App\Models\EmailTemplateDetails;
use App\Models\AccountCategory;
use App\Models\AccountTemplateDetails;
use App\Models\AccountTemplateMain;
use Illuminate\Http\Request;
use App\Models\MenusLang;
use App\Models\Users;
use Illuminate\Support\Facades\DB;


class AccountTemplateController extends Controller
{
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $hierarchy = AuditLog::all()
        // ->orderBy('created_at', 'desc')->get();

        $account_template = AccountTemplateMain::all();

        return view('dashboard.accountTemplate.index', ['account_template' => $account_template]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.accountCat.create', [
            'templates' => CaseTemplate::all()
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
        $accountCat  = new AccountCategory();

        $accountCat->code = $request->input('code');
        $accountCat->category = $request->input('category');
        $accountCat->status =  $request->input('status');
        $accountCat->created_at = date('Y-m-d H:i:s');

        $accountCat->save();

        $request->session()->flash('message', 'Successfully created category');
        return redirect()->route('account-cat.index');
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

        $docTemplatePages = DB::table('document_template_pages')
        ->leftJoin('users', 'users.id', '=', 'document_template_pages.is_locked')
        ->select('document_template_pages.*', 'users.name')
        ->get();

        $current_user = auth()->user();

        // $docTemplatePage = DocumentTemplatePages::where('document_template_details_id', '=', $docTemplateDetailSelected[0]->id)->get();
        $docTemplateDetail = DocumentTemplateDetails::where('document_template_main_id', '=', $id)->get();
        $docTemplateMain = DocumentTemplateMain::where('id', '=', $id)->get();
        return view('dashboard.documentTemplate.show', [
            'docTemplatePages' => $docTemplatePages, 
            'docTemplateDetail' => $docTemplateDetail, 
            'docTemplateMain' => $docTemplateMain
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
        $account_template = AccountTemplateMain::where('id', '=', $id)->get();
        $account_template_details = AccountTemplateDetails::where('acc_main_template_id', '=', $id)->get();

        

        $account_template_cat = DB::table('account_template_details')
        ->join('account_category', 'account_template_details.account_cat_id', '=', 'account_category.id')
        ->select('account_category.id', 'account_category.category', 'taxable', 'percentage')
        ->distinct()
        ->groupBy('account_template_details.id')
        ->where('acc_main_template_id', '=', $id)
        ->get();

        

        $joinData = array();

        for ($i=0;$i<count($account_template_cat);$i++)
        {
            
            $account_template_details_by_cat = AccountTemplateDetails::where('acc_main_template_id', '=', $id)
            ->where('account_cat_id', '=', $account_template_cat[$i]->id)
            ->get();
            array_push($joinData,  array('category' => $account_template_cat[$i], 'account_details' => $account_template_details_by_cat));
            
        }

        // return $account_template_details;


        return view('dashboard.accountTemplate.edit', [
            'account_template' => $account_template[0],
            'account_template_detail' => $account_template_details,
            'account_template_with_cat' => $joinData
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

        $accountCat = AccountCategory::where('id', '=', $id)->first();


        $accountCat->code = $request->input('code');
        $accountCat->category = $request->input('category');
        $accountCat->status =  $request->input('status');
        $accountCat->updated_at = date('Y-m-d H:i:s');

        $accountCat->save();

        $request->session()->flash('message', 'Successfully updated category');
        return redirect()->route('account-cat.index');

    }

}

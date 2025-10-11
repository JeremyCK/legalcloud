<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\AccountItem;
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
use App\Models\LoanCaseAccount;
use App\Models\QuotationGeneratorMain;
use App\Models\QuotationTemplateDetails;
use App\Models\QuotationTemplateMain;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;


class QuotationController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $current_user = auth()->user();

        // Build the query with search and filtering
        $query = DB::table('quotation_template_main AS a')
            ->leftJoin('quotation_template_details AS qd', 'qd.acc_main_template_id', '=', 'a.id')
            ->leftJoin('quotation_generator_main AS qgm', 'qgm.template_id', '=', 'a.id')
            ->select(
                'a.*',
                DB::raw('COUNT(DISTINCT qd.id) as details_count'),
                DB::raw('COUNT(DISTINCT qgm.id) as generated_count'),
                DB::raw('MAX(qgm.created_at) as last_generated')
            )
            ->groupBy('a.id');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('a.name', 'LIKE', "%{$search}%")
                  ->orWhere('a.remark', 'LIKE', "%{$search}%");
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('a.status', $request->get('status'));
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        switch ($sortBy) {
            case 'name':
                $query->orderBy('a.name', $sortOrder);
                break;
            case 'updated_at':
                $query->orderBy('a.updated_at', $sortOrder);
                break;
            case 'created_at':
            default:
                $query->orderBy('a.created_at', $sortOrder);
                break;
        }

        $quotations = $query->paginate(12)->appends($request->query());

        // Get statistics for the dashboard
        $stats = [
            'total' => DB::table('quotation_template_main')->count(),
            'active' => DB::table('quotation_template_main')->where('status', 1)->count(),
            'this_month' => DB::table('quotation_template_main')
                ->where('created_at', '>=', now()->startOfMonth())
                ->count(),
            'last_updated' => DB::table('quotation_template_main')
                ->max('updated_at')
        ];



        return view('dashboard.quotation.index', [
            'quotations' => $quotations,
            'stats' => $stats
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

        return view('dashboard.quotation.create', [
            'account_category' => $account_category
        ]);
    }

    public function store(Request $request)
    {

        $quotationTemplateMain  = new QuotationTemplateMain();

        $quotationTemplateMain->name = $request->input('name');
        $quotationTemplateMain->remark = $request->input('remark');
        $quotationTemplateMain->status = $request->input('status');
        $quotationTemplateMain->created_at = date('Y-m-d H:i:s');
        $quotationTemplateMain->save();

        $quotationTemplateMain->save();

        $request->session()->flash('message', 'Successfully created new quotation');
        return redirect()->route('quotation.index');
    }

    public function edit($id)
    {
        $quotation = QuotationTemplateMain::where('id', '=', $id)->first();
        $account_category = AccountCategory::where('status', '=', 1)->get();

        $accounts = DB::table('account_item AS a')
            ->leftJoin('account_category AS ac', 'ac.id', '=', 'a.account_cat_id')
            ->select('a.*', 'ac.category')
            ->orderBy('name', 'ASC')
            ->get();

        $category = AccountCategory::where('status', '=', 1)->orderBy('order','asc')->get();
        $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)->get();



        $quotation_details = array();

        for ($i = 0; $i < count($category); $i++) {

            // $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)
            //     ->where('account_cat_id', '=', $category[$i]->id)
            //     ->get();

            $QuotationTemplateDetails = DB::table('quotation_template_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.formula as account_formula', 'a.min as account_min', 'a.id as account_item_id')
                ->where('qd.acc_main_template_id', '=',  $id)
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->orderBy('order_no', 'ASC')
                // ->orderBy('id', 'ASC')
                ->get();

            array_push($quotation_details,  array(
                'category' => $category[$i],
                'account_details' => $QuotationTemplateDetails
            ));
        }

        // return $quotation_details;



        // $account_template = AccountTemplateMain::where('id', '=', 1)->get();
        // $account_template_details = AccountTemplateDetails::where('acc_main_template_id', '=', $id)->get();

        // $account_template_cat = DB::table('loan_case_account')
        //     ->join('account_category', 'loan_case_account.account_cat_id', '=', 'account_category.id')
        //     ->select('account_category.id', 'account_category.category', 'taxable', 'percentage')
        //     ->distinct()
        //     ->groupBy('loan_case_account.id')
        //     ->where('loan_case_account.case_id', '=', 1)
        //     ->get();

        // $joinData = array();

        // for ($i = 0; $i < count($account_template_cat); $i++) {

        //     $account_template_details_by_cat = LoanCaseAccount::where('case_id', '=', $id)
        //         ->where('account_cat_id', '=', $account_template_cat[$i]->id)
        //         ->get();
        //     array_push($joinData,  array('category' => $account_template_cat[$i], 'account_details' => $account_template_details_by_cat));
        // }

        return view('dashboard.quotation.edit', [
            'quotation' => $quotation,
            'quotation_details' => $quotation_details,
            'accounts' => $accounts
            // 'account_template_with_cat' => $joinData
        ]);
    }

    function addAccountIntoQuotation(Request $request, $id)
    {
    
        $status = 1;
        $message = 'Added account item into quotation template';
        $current_user = auth()->user();
        $accountItem = AccountItem::where('id', '=', $id)->first();

        if ($accountItem)
        {
            $quotationTemplateDetails  = new QuotationTemplateDetails();

            $quotationTemplateDetails->acc_main_template_id = $id;
            $quotationTemplateDetails->account_item_id = $request->input('selected_account_id');
            $quotationTemplateDetails->max =  $accountItem->max;
            $quotationTemplateDetails->min =  $accountItem->max;
            $quotationTemplateDetails->amount =  $accountItem->max;
            $quotationTemplateDetails->formula =  $accountItem->formula;
            $quotationTemplateDetails->created_by =  $current_user->id;
            $quotationTemplateDetails->status = 1;
            $quotationTemplateDetails->created_at = date('Y-m-d H:i:s');
            $quotationTemplateDetails->save();
        }
        return response()->json(['status' => $status, 'data' => $message]);
        
    }

    function deleteAccountIntoQuotation(Request $request, $id)
    {
        $status = 1;
        $data = '';

        $quotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)->first();

        $quotationTemplateDetails->delete();

        return response()->json(['status' => $status, 'message' => 'Deleted account item']);
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

    public function updateQuotationBill(Request $request, $id)
    {
        $status = 1;
        $need_approval = 0;
        $totalAmount = 0;
        $message = 'Voucher requested';
        $billList = [];

        if ($request->input('bill_list') != null) {
            $billList = json_decode($request->input('bill_list'), true);
        }

        $current_user = auth()->user();

        if (count($billList) > 0) {

            for ($i = 0; $i < count($billList); $i++) {

                $quotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $billList[$i]['id'])->first();

                $quotationTemplateDetails->min = $billList[$i]['min'];
                $quotationTemplateDetails->max = $billList[$i]['max'];
                $quotationTemplateDetails->order_no = $billList[$i]['order_no'];
                $quotationTemplateDetails->amount = $billList[$i]['amount'];
                $quotationTemplateDetails->updated_at = date('Y-m-d H:i:s');
                $quotationTemplateDetails->save();
            }
        }

        return response()->json(['status' => $status, 'data' => $message]);
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
            $account = QuotationTemplateMain::where('id', '=', $id)->first();

            $account->name = $request->input('name');
            $account->remark = $request->input('remark');
            $account->status = $request->input('status');
            $account->updated_at = date('Y-m-d H:i:s');
            $account->save();
            $message = 'Quotation template information updated';
        } catch (\Throwable $e) {
            $status = 0;
            $message = $e;
        }

        $request->session()->flash('message', $message);
        return redirect()->route('quotation.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        try {
            $quotation = QuotationTemplateMain::where('id', '=', $id)->first();
            
            if (!$quotation) {
                $request->session()->flash('error', 'Quotation template not found');
                return redirect()->route('quotation.index');
            }

            // Check if template is being used in generated quotations
            $usedInQuotations = DB::table('quotation_generator_main')
                ->where('template_id', $id)
                ->count();

            if ($usedInQuotations > 0) {
                $request->session()->flash('error', 'Cannot delete template. It is being used in ' . $usedInQuotations . ' generated quotation(s)');
                return redirect()->route('quotation.index');
            }

            // Delete related template details first
            QuotationTemplateDetails::where('acc_main_template_id', $id)->delete();
            
            // Delete the main template
            $quotation->delete();

            $request->session()->flash('message', 'Quotation template "' . $quotation->name . '" has been successfully deleted');
            
        } catch (\Exception $e) {
            $request->session()->flash('error', 'Error deleting quotation template: ' . $e->getMessage());
        }

        return redirect()->route('quotation.index');
    }
}

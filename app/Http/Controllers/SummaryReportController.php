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
use App\Models\LoanCaseBillMain;
use App\Models\QuotationTemplateDetails;
use App\Models\QuotationTemplateMain;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherMain;
use Illuminate\Support\Facades\DB;


class SummaryReportController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $quotations = DB::table('quotation_template_main AS a')
            ->select('a.*')
            ->orderBy('id', 'ASC')
            ->paginate(10);

        $quotations = DB::table('loan_case_bill_main AS a')
            ->join('loan_case as c', 'c.id', '=', 'a.case_id')
            ->select('a.*', 'c.case_ref_no')
            ->where('a.status', '<>', '99')
            ->orderBy('a.id', 'ASC')
            ->get();

            // for ($i = 0; $i < count($quotations); $i++)
            // {
            //     $this->updateBillSummary($quotations[$i]->id);
            // }


            $total_Pfee1 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('pfee1_recv');
            $total_Pfee2 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('pfee2_recv');
        $total_Pfee = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('pfee_recv');
        $total_disb = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('disb_recv');
        $total_sst = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('sst_recv');
        $total_referral_a1 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('referral_a1');
        $total_referral_a2 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('referral_a2');
        $total_referral_a3 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('referral_a3');
        $total_referral_a4 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('referral_a4');
        $total_marketing = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('marketing');
        $total_amt = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('total_amt');
        $total_collected_amt = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('collected_amt');
        $total_uncollected = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('uncollected');


        return view('dashboard.summary-report.index', [ 
            'quotations' => $quotations,
            'total_Pfee' => $total_Pfee,
            'total_Pfee1' => $total_Pfee1,
            'total_Pfee2' => $total_Pfee2,
            'total_disb' => $total_disb,
            'total_sst' => $total_sst,
            'total_amt' => $total_amt,
            'total_uncollected' => $total_uncollected,
            'total_collected_amt' => $total_collected_amt,
            'total_referral_a1' => $total_referral_a1,
            'total_referral_a2' => $total_referral_a2,
            'total_referral_a3' => $total_referral_a3,
            'total_referral_a4' => $total_referral_a4,
            'total_marketing' => $total_marketing
        ]);
    }

    public function voucherReport()
    {
        // $quotations = DB::table('quotation_template_main AS a')
        // ->select('a.*')
        // ->orderBy('id', 'ASC')
        // ->paginate(10);

        $quotations = DB::table('loan_case_bill_main AS a')
            ->join('loan_case as c', 'c.id', '=', 'a.case_id')
            ->select('a.*', 'c.case_ref_no')
            ->where('a.status', '<>', '99')
            ->orderBy('a.id', 'ASC')
            ->get();

        // $quotations = DB::table('voucher_main AS v')
        // ->join('loan_case as c', 'c.id', '=', 'a.case_id')
        // ->select('a.*', 'c.case_ref_no')
        // ->where('a.status','<>','99')
        // ->orderBy('a.id', 'ASC')
        // ->get();


        //      $voucher = DB::table('voucher_main as m')
        // ->join('voucher_details as d','d.voucher_main_id','=','m.id')
        // ->select('users.name',DB::raw('SUM(mileages.mileage) as mileage'))
        // ->get();

        $vouchers = DB::table('voucher_main as m')
            ->join('voucher_details as d', 'd.voucher_main_id', '=', 'm.id')
            ->join('loan_case as c', 'c.id', '=', 'm.case_id')
            ->select('m.*', 'c.case_ref_no', DB::raw('SUM(d.amount) as mileage'))
            ->groupBy('m.id')
            // ->where('voucher.id', '=', $id)
            ->get();



        $total_Pfee = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('pfee_recv');
        $total_disb = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('disb_recv');
        $total_sst = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('sst_recv');
        $total_referral_a1 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('referral_a1');
        $total_referral_a2 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('referral_a2');
        $total_marketing = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('marketing');
        $total_amt = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('total_amt');
        $total_collected_amt = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('collected_amt');


        return view('dashboard.reports.voucher-report', [
            'quotations' => $quotations,
            'total_Pfee' => $total_Pfee,
            'vouchers' => $vouchers,
            'total_disb' => $total_disb,
            'total_sst' => $total_sst,
            'total_amt' => $total_amt,
            'total_collected_amt' => $total_collected_amt,
            'total_referral_a1' => $total_referral_a1,
            'total_referral_a2' => $total_referral_a2,
            'total_marketing' => $total_marketing
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

        $category = AccountCategory::where('status', '=', 1)->get();
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

    public function updateBillSummary($id)
    {
        $pfee = 0;
        $disb = 0;
        $sst = 0;

        $referral_a1 = 0;
        $referral_a2 = 0;
        $marketing = 0;

        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        $loanBillDetails = DB::table('loan_case_bill_details AS bd')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->select('bd.*', 'a.account_cat_id')
            ->where('bd.loan_case_main_bill_id', '=',  $id)
            ->get();

        for ($i = 0; $i < count($loanBillDetails); $i++) {

            if ($loanBillDetails[$i]->account_cat_id == 1) {
                $pfee += $loanBillDetails[$i]->quo_amount;
            }

            if ($loanBillDetails[$i]->account_cat_id == 3) {
                $disb += $loanBillDetails[$i]->quo_amount;
            }
        }

        $sst = $pfee * 0.06;
        $sst = number_format((float)$sst, 2, '.', '');

        $LoanCaseBillMain->pfee = $pfee;
        $LoanCaseBillMain->disb = $disb;
        $LoanCaseBillMain->sst = $sst;

        $referral_a1 = $LoanCaseBillMain->referral_a1;
            $referral_a2 = $LoanCaseBillMain->referral_a2;
            $marketing = $LoanCaseBillMain->marketing;

        $collected_amt = $LoanCaseBillMain->collected_amt;
        $collected_amt_sum = $collected_amt;

        // 

        if ($collected_amt >= 0) {
           
            if (($collected_amt - $pfee) >= 0) {
                $collected_amt = $collected_amt - $pfee;
                $LoanCaseBillMain->pfee_recv = $pfee;

                $sst = $pfee * 0.06;
                $sst = number_format((float)$sst, 2, '.', '');

                $LoanCaseBillMain->sst_recv = $sst;
            } else {
                $LoanCaseBillMain->pfee_recv = $collected_amt;

                $sst = $collected_amt * 0.06;
                $sst = number_format((float)$sst, 2, '.', '');

                $LoanCaseBillMain->sst_recv = $sst;
                $collected_amt = 0;
            }
        }
       
        if ($collected_amt >= 0) {
            if (($collected_amt - $disb) >= 0) {
                $collected_amt = $collected_amt - $disb;
                $LoanCaseBillMain->disb_recv = $disb;
            } else {
                $LoanCaseBillMain->disb_recv = $collected_amt;
                $collected_amt = 0;
            }
        }

        if ($collected_amt >= 0) 
        {
            $collected_amt = $collected_amt -$referral_a1;
            $collected_amt = $collected_amt -$referral_a2;
            $collected_amt = $collected_amt -$marketing;

            $LoanCaseBillMain->uncollected = $collected_amt;
        }

        $LoanCaseBillMain->save();

        return response()->json(['status' => 1, 'data' => 'Updated bill details']);
    }

    function addAccountIntoQuotation(Request $request, $id)
    {

        $status = 1;
        $message = 'Added account item into quotation template';
        $current_user = auth()->user();
        $accountItem = AccountItem::where('id', '=', $id)->first();

        if ($accountItem) {
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

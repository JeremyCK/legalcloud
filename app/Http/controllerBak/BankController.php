<?php

namespace App\Http\Controllers;

use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\Users;
use App\Models\Banks;
use App\Models\BanksUsersRel;
use App\Models\Customer;
use App\Models\Parameter;
use App\Models\caseTemplate;
use App\Models\LoanCase;
use App\Models\LoanCaseDetails;
use App\Models\CaseMasterListCategory;
use App\Models\CaseMasterListField;
use App\Models\perm;
use Illuminate\Http\Request;
use App\Models\MenusLang;
use App\Http\Helper\Helper;
use App\Models\OfficeBankAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banks = Banks::all();

        return view('dashboard.banks.index', ['banks' => $banks]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $account = Users::where('menuroles', '=', 'account')->get();
        $lawyer = Users::where('menuroles', '=', 'lawyer')->get();
        $sales = Users::where('menuroles', '=', 'sales')->get();
        $clerk = Users::where('menuroles', '=', 'clerk')->get();
        $banks = Banks::where('status', '=', 1)->get();

        return view('dashboard.banks.create', [
            'banks' => $banks,
            'lawyers' => $lawyer,
            'sales' => $sales,
            'accounts' => $account,
            'clerks' => $clerk
        ]);
    }

    public function BankLedger()
    {
        $current_user = auth()->user();
        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        $rows = [];
        $case_receive = [];

        if (AccessController::UserAccessPermissionController(PermissionController::BankLedgerPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        if (in_array($current_user->menuroles, ['sales'])) {
            if (!in_array($current_user->id,[51,32])) {
                return redirect()->route('case.index');
            } 
        }

        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id,[2,3,5,6])) {
                $OfficeBankAccount = $OfficeBankAccount->where('branch_id', '=', $current_user->branch_id);
            }
        }
        else if (in_array($current_user->menuroles, ['sales'])) {
            if (in_array($current_user->id,[32,51])) {
                $OfficeBankAccount = $OfficeBankAccount->whereIn('branch_id', [5,6]);
            }
        }


        return view('dashboard.banks.bank-ledger', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'rows' => $rows,
            'case_receive' => $case_receive,
        ]);
    }

    public function getBankLedger(Request $request)
    {
        $case_receive = DB::table('voucher_details as a')
            ->leftJoin('voucher_main as b', 'b.id', '=', 'a.voucher_main_id')
            ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'b.case_bill_main_id')
            ->leftJoin('loan_case as e', 'e.id', '=', 'b.case_id')
            ->select('a.*', 'b.payment_date', 'b.transaction_id', 'e.case_ref_no', 'd.invoice_no', 'd.case_id', 'b.remark', 'b.payee')
            ->where('b.status', '<>',  99)
            ->where('a.bank_id', '=',  $request->input('bank_id'))
            ->where('b.voucher_type', '=',  3)->get();

        $rows = DB::table('transfer_fee_details as a')
            ->leftJoin('transfer_fee_main as b', 'b.id', '=', 'a.transfer_fee_main_id')
            ->leftJoin('banks as c', 'c.id', '=', 'b.transfer_to')
            ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'a.loan_case_main_bill_id')
            ->leftJoin('loan_case as e', 'e.id', '=', 'd.case_id')
            ->select('a.*', 'c.name as bank_name', 'b.transfer_date', 'b.transaction_id', 'e.case_ref_no', 'd.invoice_no', 'd.case_id', 'b.purpose', 'd.pfee1_inv', 'd.pfee2_inv', 'd.sst_inv')
            ->where('b.transfer_to', '=',  $request->input('bank_id'))
            ->where('b.status', '<>',  99);

            $rows = DB::table('ledger_entries as a')
            ->leftJoin('banks as c', 'c.id', '=', 'a.bank_id')
            ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'a.loan_case_main_bill_id')
            ->leftJoin('loan_case as e', 'e.id', '=', 'a.case_id')
            ->leftJoin('voucher_main as f', 'f.id', '=', 'a.key_id')
            ->select('a.*', 'c.name as bank_name', 'e.case_ref_no', 'e.id as case_id', 'f.voucher_no as voucher_no', 'f.payee as payee')
            ->where('a.bank_id', '=',  $request->input('bank_id'))
            ->whereNotIn('a.type', ['RECONADD','RECONLESS'])
            ->where('a.status', '<>',  99);

            $rows = DB::table('ledger_entries_v2 as a')
            ->leftJoin('banks as c', 'c.id', '=', 'a.bank_id')
            ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'a.loan_case_main_bill_id')
            ->leftJoin('loan_case as e', 'e.id', '=', 'a.case_id')
            ->leftJoin('voucher_main as f', 'f.id', '=', 'a.key_id')
            ->select('a.*', 'c.name as bank_name', 'e.case_ref_no', 'e.id as case_id', 'f.voucher_no as voucher_no', 'f.payee as payee')
            ->where('a.bank_id', '=',  $request->input('bank_id'))
            ->whereNotIn('a.type', ['RECONADD','RECONLESS'])
            ->where('a.status', '<>',  99);

        if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
            $rows = $rows->whereBetween('a.date', [$request->input("date_from"), $request->input("date_to")]);
        } else {
            if ($request->input("date_from") <> null) {
                $rows = $rows->where('a.date', '>=', $request->input("date_from"));
            }

            if ($request->input("date_to") <> null) {
                $rows = $rows->where('a.date', '<=', $request->input("date_to"));
            }
        }

        $rows = $rows->orderBy('a.date', 'asc')->get();
        $bank_name = $request->input('bank_name');


        return response()->json([
            'view' => view('dashboard.banks.tab-ledger', compact('case_receive', 'rows'))->render(),
        ]);
    }

    public function getBankLedgerList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            $rows = DB::table('transfer_fee_details as a')
                ->leftJoin('transfer_fee_main as b', 'b.id', '=', 'a.transfer_fee_main_id')
                ->leftJoin('banks as c', 'c.id', '=', 'b.transfer_to')
                ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'a.loan_case_main_bill_id')
                ->leftJoin('loan_case as e', 'e.id', '=', 'd.case_id')
                ->select('a.*', 'c.name as bank_name', 'b.transfer_date', 'b.transaction_id', 'e.case_ref_no', 'd.invoice_no', 'd.case_id')
                ->where('b.status', '<>',  99);



            // if ($request->input('type') == 'transferred') {

            //     // $rows = $rows->where('b.transferred_to_office_bank', '=',  $request->input('transfer_list'));


            //     $transferred_list = [];

            //     $TransferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $request->input('transaction_id'))->get();

            //     for ($i = 0; $i < count($TransferFeeDetails); $i++) {
            //         array_push($transferred_list, $TransferFeeDetails[$i]->loan_case_main_bill_id);
            //     }


            //     if ($request->input('transaction_id')) {
            //         $rows = $rows->whereIn('b.id', $transferred_list);
            //         // $rows = $rows->whereIn('b.id',[15,17]);
            //     }
            // } else {

            //     if ($request->input('type') == 'add') {
            //         if ($request->input('transfer_list')) {
            //             $transfer_list = json_decode($request->input('transfer_list'), true);
            //             $rows = $rows->whereIn('b.id', $transfer_list);
            //         }
            //     } else {
            //         if ($request->input('transfer_list')) {
            //             $transfer_list = json_decode($request->input('transfer_list'), true);
            //             $rows = $rows->whereNotIn('b.id', $transfer_list);
            //             $rows = $rows->where('b.transferred_to_office_bank', '=',  0);
            //         }
            //     }
            // }

            // if ($request->input("recv_start_date") <> null && $request->input("recv_end_date") <> null) {
            //     $rows = $rows->whereBetween('b.payment_receipt_date', [$request->input("recv_start_date"), $request->input("recv_end_date")]);
            // } else {
            //     if ($request->input("date_from") <> null) {
            //         $rows = $rows->where('b.payment_receipt_date', '>=', $request->input("recv_start_date"));
            //     }

            //     if ($request->input("date_to") <> null) {
            //         $rows = $rows->where('b.payment_receipt_date', '<=', $request->input("recv_end_date"));
            //     }
            // }

            // if ($request->input('branch')) {
            //     // $rows = $rows->where('l.branch_id', '=', $request->input("branch"));
            //     $rows = $rows->where('b.invoice_branch_id', '=', $request->input("branch"));
            // }

            // if (in_array($current_user->menuroles, ['maker'])) {
            //     if ($current_user->branch_id == 3) {
            //         $rows = $rows->where('l.branch_id', '=',  3);
            //     }
            // }

            $rows = $rows->orderBy('a.id', 'desc')->get();

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

                    return '<a target="_blank" href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a> ';
                })
                ->addColumn('credit', function ($data) {

                    return $data->transfer_amount;
                })
                ->addColumn('debit', function ($data) {

                    return '-';
                })
                ->rawColumns(['action', 'debit', 'credit', 'transaction_type', 'transferred_to_office_bank', 'case_ref_no'])
                ->make(true);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $banks = new Banks();

        $banks->name = $request->input('name');
        $banks->short_code = $request->input('short_code');
        $banks->tel_no = $request->input('tel_no');
        $banks->fax = $request->input('fax');
        $banks->address = $request->input('address');
        $banks->status = $request->input('status');
        $banks->created_at = now();

        $banks->save();

        // if($banks)
        // {
        //     if (!empty($request->input('assignTo')))
        //     {
        //         $staffList =$request->input('assignTo');

        //         for($i = 0; $i < count($staffList); $i++){

        //             $banksUsersRel = new BanksUsersRel();

        //             $banksUsersRel->bank_id = $banks->id;
        //             $banksUsersRel->user_id = $staffList[$i];
        //             $banksUsersRel->status = 1;
        //             $banksUsersRel->created_at = now();

        //             $banksUsersRel->save();

        //         }
        //     }
        // }

        $request->session()->flash('message', 'Successfully created new Bank');

        return redirect('banks');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $current_user = auth()->user();


        $loanCase = LoanCase::where('id', '=', $id)->get();
        $loanCaseDetails = LoanCaseDetails::where('case_id', '=', $id)->get();
        $loanCaseDetailsCount = LoanCaseDetails::where('case_id', '=', $id)->where('check_point', '>', 0)->get();

        $caseMasterListCategory = CaseMasterListCategory::all();
        $caseMasterListField = CaseMasterListField::all();


        $lawyer = Users::where('id', '=', $loanCase[0]->lawyer_id)->get();
        $clerk = Users::where('id', '=', $loanCase[0]->clerk_id)->get();
        $sales = Users::where('id', '=', $loanCase[0]->sales_user_id)->get();
        $caseTemplate = caseTemplate::all();

        $loanCase[0]->lawyer = $lawyer[0]->name;
        $loanCase[0]->clerk = $clerk[0]->name;
        $loanCase[0]->sales = $sales[0]->name;

        if (count($loanCase)) {
        }
        // return $loanCaseDetails;


        return view('dashboard.todolist.show', [
            'cases' => $loanCase,
            'cases_details' => $loanCaseDetails,
            'caseTemplate' => $caseTemplate,
            'current_user' => $current_user,
            'caseMasterListCategory' => $caseMasterListCategory,
            'caseMasterListField' => $caseMasterListField,
            'loanCaseDetailsCount' => $loanCaseDetailsCount
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $account = Users::where('menuroles', '=', 'account')->get();
        $lawyer = Users::where('menuroles', '=', 'lawyer')->get();
        $sales = Users::where('menuroles', '=', 'sales')->get();
        $clerk = Users::where('menuroles', '=', 'clerk')->get();
        $banks = Banks::where('id', '=', $id)->get();
        $banksUsersRelAraa = BanksUsersRel::where('bank_id', '=', $id)->get();

        $banksUsersRel = [];

        for ($i = 0; $i < count($banksUsersRelAraa); $i++) {
            array_push($banksUsersRel, $banksUsersRelAraa[$i]->user_id);
        }


        return view('dashboard.banks.edit', [
            'banks' => $banks[0],
            'lawyers' => $lawyer,
            'sales' => $sales,
            'accounts' => $account,
            'banksUsersRel' => $banksUsersRel,
            'clerks' => $clerk
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
        // $menuLangList = MenuLangList::where('id', '=', $request->input('id'))->first();
        // $menuLangList->name = $request->input('name');
        // $menuLangList->short_name = $request->input('shortName');
        // if($request->input('is_default') === 'true'){
        //     $menuLangList->is_default = true;
        // }else{
        //     $menuLangList->is_default = false;
        // }
        // $menuLangList->save();
        // $request->session()->flash('message', 'Successfully updated language');
        // return redirect()->route('todolist.edit', [$request->input('id')]); 

        $banks = Banks::where('id', '=', $id)->first();

        $banks->name = $request->input('name');
        $banks->short_code = $request->input('short_code');
        $banks->tel_no = $request->input('tel_no');
        $banks->fax = $request->input('fax');
        $banks->address = $request->input('address');
        $banks->status = $request->input('status');
        $banks->created_at = now();

        $banks->save();


        if ($banks) {
            $banksUsersRel = BanksUsersRel::where('bank_id', '=', $id);
            $banksUsersRel->delete();

            if (!empty($request->input('assignTo'))) {
                $staffList = $request->input('assignTo');

                for ($i = 0; $i < count($staffList); $i++) {

                    $banksUsersRel = new BanksUsersRel();

                    $banksUsersRel->bank_id = $banks->id;
                    $banksUsersRel->user_id = $staffList[$i];
                    $banksUsersRel->status = 1;
                    $banksUsersRel->created_at = now();

                    $banksUsersRel->save();
                }
            }
        }

        $request->session()->flash('message', 'Successfully updated bank info');

        $banks = Banks::all();

        return view('dashboard.banks.index', ['banks' => $banks]);
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

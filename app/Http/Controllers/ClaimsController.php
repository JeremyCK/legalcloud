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
use App\Models\BonusRequestHistory;
use App\Models\BonusRequestList;
use App\Models\BonusRequestRecords;
use App\Models\ClaimRequest;
use App\Models\LoanCaseBillMain;
use App\Models\OfficeBankAccount;
use App\Models\User;
use App\Models\UserAccessControl;
use App\Models\VoucherDetails;
use App\Models\VoucherMain;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;

class ClaimsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public static function getAccessCode()
    {
        return 'ClaimsView';
    }

    public function getEditPermission()
    {
        return 'ClaimsApproval';
    }

    public function index()
    {
        $current_user = auth()->user();
        $role = $current_user->menuroles;

        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();

        $staff = Users::where('status', '<>', 99)->whereIn('menuroles', ['lawyer', 'clerk', 'chambering'])->orderBy('name', 'asc')->get();
        $recon_date = VoucherDetails::where('recon_date', '<>', null)->select('recon_date')
            ->groupBy('recon_date')
            ->get();

        $recon_date = VoucherMain::where('recon_date', '<>', null)->select('recon_date')
            ->groupBy('recon_date')
            ->orderBy('recon_date', 'asc')
            ->get();

        $bonus_total_sum_2 = DB::table('bonus_request_records as l')
            ->where('l.status', '=',  1)
            ->where('l.percentage', '=',  2)
            ->whereYear('created_at', '2022')->sum('amount');

        // $bonus_total_sum_3 = DB::table('bonus_request_records as l')
        //     ->where('l.status', '=',  1)
        //     ->where('l.percentage', '=',  3)
        //     ->whereYear('created_at','2022')->sum('amount');

        $bonus_total_sum_3 = DB::table('bonus_request_records as l')
            ->where('l.status', '=',  1)
            ->where('l.percentage', '=',  3)->sum('amount');

        // $dbVersion = \DB::connection('mysql2')->table('custom_data')->update(['value' => '4444444']);

        // return 'updated wordpress value to 3333333333';

        return view('dashboard.bonus.index', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'staffs' => $staff,
            'current_user' => $current_user,
            'bonus_total_sum_2' => $bonus_total_sum_2,
            'bonus_total_sum_3' => $bonus_total_sum_3,
            'recon_date' => $recon_date
        ]);
    }

    public function ClaimsReqList()
    {
        $current_user = auth()->user();
        $role = $current_user->menuroles;

        $ClaimsApproval = AccessController::UserAccessController($this->getEditPermission());

        if (AccessController::UserAccessController($this->getAccessCode()) == false) {
            return redirect()->route('dashboard.index');
        }

        $ClaimRequestApproved = ClaimRequest::where('status', 1);
        $ClaimRequestPending = ClaimRequest::where('status', 2);

        if (AccessController::UserAccessController($this->getEditPermission()) == false) {
            $ClaimRequestApproved = $ClaimRequestApproved->where('user_id', $current_user->id);
            $ClaimRequestPending = $ClaimRequestPending->where('user_id', $current_user->id);
        }

        $ClaimRequestApproved = $ClaimRequestApproved->count();
        $ClaimRequestPending = $ClaimRequestPending->count();


        $staff = Users::where('status', '<>', 99)->whereIn('menuroles', ['lawyer', 'clerk', 'chambering'])->orderBy('name', 'asc')->get();

        return view('dashboard.claims.index', [
            'staffs' => $staff,
            'ClaimRequestApproved' => $ClaimRequestApproved,
            'ClaimRequestPending' => $ClaimRequestPending,
            'ClaimsApproval' => $ClaimsApproval,
        ]);
    }

    public function getClaimsList(Request $request)
    {
        if ($request->ajax()) {
            $current_user = auth()->user();

            $Claims = DB::table('claims_request as a')
                ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
                ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
                ->leftJoin('claims_type as t', 't.id', '=', 'a.claims_type')
                ->select('a.*', 'l.case_ref_no', 'u.name as user_name', 't.name as type_name')
                ->orderBy('a.created_at', 'desc');

            if (AccessController::UserAccessController($this->getEditPermission()) == true) {
                if ($request->input("requestor")) {
                    if ($request->input("requestor") <> 99) {
                        $Claims = $Claims->where(function ($q) use ($request) {
                            $q->where('lawyer_id', '=', $request->input("requestor"))
                                ->orWhere('clerk_id', '=', $request->input("requestor"));
                        });
                    }
                }
            } else {
                $Claims = $Claims->where('user_id', $current_user->id);
            }

            if ($request->input("status") <> 0) {
                $Claims = $Claims->where('a.status', $request->input("status"));
            }

           $date_type = 'request';

            if ($request->input("date_type") == 'request') {
                // if ($request->input("year") <> 0) {
                //     $Claims = $Claims->whereYear('a.created_at', $request->input("year"));
                // }
    
                // if ($request->input("month") <> 0) {
                //     $Claims = $Claims->whereMonth('a.created_at', $request->input("month"));
                // }


                $date_type = 'a.created_at';
            }
            else if ($request->input("date_type") == 'approval') {
                // if ($request->input("year") <> 0) {
                //     $Claims = $Claims->whereYear('a.approved_date', $request->input("year"));
                // }
    
                // if ($request->input("month") <> 0) {
                //     $Claims = $Claims->whereMonth('a.approved_date', $request->input("month"));
                // }

                $date_type = 'a.approved_date';
            }

            

            if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
                $Claims = $Claims->whereBetween($date_type, [$request->input("date_from"), $request->input("date_to")]);
            } else {
                if ($request->input("date_from") <> null) {
                    $Claims = $Claims->where($date_type, '>=', $request->input("date_from"));
                }

                if ($request->input("date_to") <> null) {
                    $Claims = $Claims->where($date_type, '<=', $request->input("date_to"));
                }
            }
            $Claims = $Claims->get();

            return DataTables::of($Claims)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a target="_blank" href="/claims-request-details/' . $row->id . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>';
                    // $actionBtn = 'test';
                    return $actionBtn;
                })
                ->editColumn('status', function ($data) {
                    if ($data->status == '2')
                        return '<span class="label bg-warning">Reviewing</span>';
                    elseif ($data->status == '1')
                        return '<span class="label bg-success">Approved</span>';
                    elseif ($data->status == '3')
                        return '<span class="label bg-danger">Rejected</span>';
                })
                ->editColumn('claims_type', function ($data) {
                    if ($data->claims_type == 'SMPSIGNED')
                        return '<span class="label bg-info">2% Bonus</span>';
                    elseif ($data->claims_type == 'CLOSEDCASE')
                        return '<span class="label bg-success">3% Bonus</span>';
                })
                ->editColumn('created_at', function ($data) {
                    $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->format('d-m-Y h:i A');
                    return $formatedDate;
                })
                ->editColumn('approved_date', function ($data) {
                    if($data->approved_date)
                    {
                        $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->approved_date)->format('d-m-Y h:i A');
                    }
                    else
                    {
                        $formatedDate = '';
                    }
                    
                    return $formatedDate;
                })
                ->editColumn('case_ref_no', function ($row) {
                    if ($row->case_id != 0) {
                        $actionBtn = ' <a target="_blank" href="/case/' . $row->case_id . '" class="  " >' . $row->case_ref_no . ' >> </a>';
                    } else {
                        $actionBtn = $row->case_ref;
                    }

                    return $actionBtn;
                })
                ->rawColumns(['action', 'status', 'case_ref_no', 'claims_type'])
                ->make(true);
        }
    }
 
    public function getClaimSum(Request $request)
    {
        $Claims = ClaimRequest::where('status', 1);
        $current_user = auth()->user();

        if (AccessController::UserAccessController($this->getEditPermission()) == true) {
            if ($request->input("requestor")) {
                if ($request->input("requestor") <> 99) {
                    $Claims = $Claims->where(function ($q) use ($request) {
                        $q->where('lawyer_id', '=', $request->input("requestor"))
                            ->orWhere('clerk_id', '=', $request->input("requestor"));
                    });
                }
            }
        } else {
            $Claims = $Claims->where('user_id', $current_user->id);
        }

        if ($request->input("year") <> 0) {
            $Claims = $Claims->whereYear('created_at', $request->input("year"));
        }

        if ($request->input("month") <> 0) {
            $Claims = $Claims->whereMonth('created_at', $request->input("month"));
        }


        $Claims = $Claims->sum('amount');

        return $Claims;
    }

    public function ClaimsReviewDetails($id)
    {
        $current_user = auth()->user();

        if (AccessController::UserAccessController($this->getAccessCode()) == false) {
            return redirect()->route('dashboard.index');
        }

        $LoanCase = [];
        $LoanCaseBillMain = [];

        $Claims = DB::table('claims_request as a')
            ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->leftJoin('claims_type as t', 't.id', '=', 'a.claims_type')
            ->select('a.*', 'l.case_ref_no', 'l.id as case_id', 'u.name as user_name', 't.name as type_name')
            ->where('a.id', '=', $id)
            ->orderBy('a.created_at', 'desc')->first();

        $LoanCase = DB::table('loan_case as l')
            ->leftJoin('users as u', 'u.id', '=', 'l.sales_user_id')
            ->select('l.*', 'u.name as sales_name')
            ->where('l.id', '=', $Claims->case_id)->first();

        $LoanCaseBillMain = DB::table('loan_case_bill_main as b')
            ->leftJoin('users as u', 'u.id', '=', 'b.marketing_id')
            ->where('b.status', '=', 1)
            ->select('b.*', 'u.name as sales_name')
            ->where('case_id', '=', $Claims->case_id)->get();


        return view('dashboard.claims.details', [
            'case' => $LoanCase,
            'LoanCaseBillMain' => $LoanCaseBillMain,
            'Claims' => $Claims,
        ]);
    }

    public function rejectBonus(Request $request, $id)
    {
        $reject_reason = $request->input('reject_reason');

        if ($reject_reason == '' || $reject_reason == null) {
            return response()->json(['status' => 0, 'message' => 'Please provide reason of rejection']);
        }

        $current_user = auth()->user();
        $BonusRequestHistory  = new BonusRequestHistory();

        $BonusRequestHistory->bonus_request_list_id = $id;
        $BonusRequestHistory->status = 2;
        $BonusRequestHistory->remarks = $reject_reason;
        $BonusRequestHistory->created_at = date('Y-m-d H:i:s');
        $BonusRequestHistory->created_by = $current_user->id;
        $BonusRequestHistory->save();

        $BonusRequestList = BonusRequestList::where('id', '=', $id)->first();

        if ($BonusRequestList) {
            $BonusRequestList->status = 3;
            $BonusRequestList->save();
        }

        return response()->json(['status' => 1, 'message' => 'Application Rejected']);
    }

    public function approveClaim(Request $request, $id)
    {
        $current_user = auth()->user();

        $ClaimRequest = ClaimRequest::where('id', '=', $id)->first();

        if ($ClaimRequest) {
            $ClaimRequest->amount = $request->input('claims');
            $ClaimRequest->selected_bill_id = $request->input('selected_bill');
            $ClaimRequest->status = 1;
            $ClaimRequest->approved_date = date('Y-m-d H:i:s');
            $ClaimRequest->save();

            $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $request->input('selected_bill'))->first();


            if ($LoanCaseBillMain) {
                switch ($ClaimRequest->percentage) {
                    case 10.00:
                        $LoanCaseBillMain->lawyer_claims_10 = $request->input('claims');
                        break;
                    case 15.00:
                        $LoanCaseBillMain->lawyer_claims_15 = $request->input('claims');
                        break;
                    default:

                        break;
                }

                $LoanCaseBillMain->save();
            }

            $LoanCase = LoanCase::where('id', '=', $ClaimRequest->case_id)->first();


            if ($LoanCase) {
                switch ($ClaimRequest->percentage) {
                    case 10.00:
                        $LoanCase->lawyer_claims_10 = $request->input('claims');
                        break;
                    case 15.00:
                        $LoanCase->lawyer_claims_15 = $request->input('claims');
                        break;
                    default:

                        break;
                }

                $LoanCase->save();
            }
        }

        return response()->json(['status' => 1, 'message' => 'Claims approved']);
    }

    public function editClaim(Request $request, $id)
    {
        $current_user = auth()->user();

        $ClaimRequest = ClaimRequest::where('id', '=', $id)->first();

        if ($ClaimRequest) {
            $ClaimRequest->amount = $request->input('claims');
            $ClaimRequest->save();

            $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $ClaimRequest->selected_bill_id)->first();


            if ($LoanCaseBillMain) {
                switch ($ClaimRequest->percentage) {
                    case 10.00:
                        $LoanCaseBillMain->lawyer_claims_10 = $request->input('claims');
                        break;
                    case 15.00:
                        $LoanCaseBillMain->lawyer_claims_15 = $request->input('claims');
                        break;
                    default:

                        break;
                }

                $LoanCaseBillMain->save();
            }

            $LoanCase = LoanCase::where('id', '=', $ClaimRequest->case_id)->first();


            if ($LoanCase) {
                switch ($ClaimRequest->percentage) {
                    case 10.00:
                        $LoanCase->lawyer_claims_10 = $request->input('claims');
                        break;
                    case 15.00:
                        $LoanCase->lawyer_claims_15 = $request->input('claims');
                        break;
                    default:

                        break;
                }

                $LoanCase->save();
            }
        }

        return response()->json(['status' => 1, 'message' => 'Claims Edited']);
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

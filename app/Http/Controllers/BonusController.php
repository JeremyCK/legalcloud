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
use App\Models\AccountLog;
use App\Models\BonusRequestHistory;
use App\Models\BonusRequestList;
use App\Models\BonusRequestRecords;
use App\Models\LoanCaseBillMain;
use App\Models\OfficeBankAccount;
use App\Models\User;
use App\Models\VoucherDetails;
use App\Models\VoucherMain;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BonusController extends Controller
{
    public static function getAccessCode()
    {
        return 'StaffBonusView';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    public function BonusReviewList()
    {
        $current_user = auth()->user();
        $role = $current_user->menuroles;

        if (AccessController::UserAccessPermissionController(PermissionController::BonusRequestListPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        if (in_array($current_user->menuroles, ['admin', 'management']) || $current_user->id == 37) {
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

            $bonus_total_sum_5 = DB::table('bonus_request_records as l')
                ->where('l.status', '=',  1)
                ->where('l.percentage', '=',  5)->sum('amount');

            return view('dashboard.bonus-request.index', [
                'OfficeBankAccount' => $OfficeBankAccount,
                'staffs' => $staff,
                'bonus_total_sum_2' => $bonus_total_sum_2,
                'bonus_total_sum_3' => $bonus_total_sum_3,
                'bonus_total_sum_5' => $bonus_total_sum_5,
                'recon_date' => $recon_date
            ]);
        } else {

            return redirect()->route('dashboard.index');
        }
    }

    public function StaffBonusReviewList()
    {
        $current_user = auth()->user();
        $role = $current_user->menuroles;

        if (AccessController::UserAccessController($this->getAccessCode()) == false) {
            return redirect()->route('dashboard.index');
        }

        $bonus_approved_count = DB::table('bonus_request_list as b')
            ->leftjoin('bonus_request_records as r', function ($join) use ($current_user) {
                $join->on('r.bonus_request_list_id', '=', 'b.id')
                    ->where('r.bonus_type', 'CLOSEDCASE')
                    ->where('r.user_id', '=', $current_user->id);
            })
            ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            ->leftJoin('users as u', 'u.id', '=', 'b.user_id')
            ->where('b.bonus_type', 'CLOSEDCASE')
            ->where(function ($q) use ($current_user) {
                $q->where('lawyer_id', '=', $current_user->id)
                    ->orWhere('clerk_id', '=', $current_user->id);
            })->where('b.status', 2)->count();

        $bonus_reviewing_count = DB::table('bonus_request_list as b')
            ->leftjoin('bonus_request_records as r', function ($join) use ($current_user) {
                $join->on('r.bonus_request_list_id', '=', 'b.id')
                    ->where('r.bonus_type', 'CLOSEDCASE')
                    ->where('r.user_id', '=', $current_user->id);
            })
            ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            ->leftJoin('users as u', 'u.id', '=', 'b.user_id')
            ->where('b.bonus_type', 'CLOSEDCASE')
            ->where(function ($q) use ($current_user) {
                $q->where('lawyer_id', '=', $current_user->id)
                    ->orWhere('clerk_id', '=', $current_user->id);
            })->where('b.status', 1)->count();

        return view('dashboard.bonus.staff.index', [
            'bonus_approved_count' => $bonus_approved_count,
            'bonus_reviewing_count' => $bonus_reviewing_count,
        ]);
    }

    public function sumStaffBonus(Request $request)
    {

        $bonus_total_sum_3 = DB::table('bonus_request_records as l')
            ->where('l.status', '=',  1)
            ->where('l.percentage', '=',  3);

        $bonus_total_sum_5 = DB::table('bonus_request_records as l')
            ->where('l.status', '=',  1)
            ->where('l.percentage', '=',  5);

        if ($request->input("requestor") <> 99) {
            $bonus_total_sum_3 = $bonus_total_sum_3->where('user_id', $request->input("requestor"));
            $bonus_total_sum_5 = $bonus_total_sum_5->where('user_id', $request->input("requestor"));
        }

        $bonus_total_sum_3 = $bonus_total_sum_3->sum('amount');
        $bonus_total_sum_5 = $bonus_total_sum_5->sum('amount');

        return response()->json(['status' => 1, 'bonus_total_sum_3' => $bonus_total_sum_3, 'bonus_total_sum_5' => $bonus_total_sum_5]);
    }

    public function getBonusReviewList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            $SubmitedCastList = BonusRequestList::DISTINCT()->whereDate('created_at', '<', '2022-12-31')->pluck('case_id')->toArray();

            $BonusRequestList = DB::table('bonus_request_list as b')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->leftJoin('users as u', 'u.id', '=', 'b.user_id')
                ->where('b.bonus_type', 'CLOSEDCASE')
                // ->whereIn('b.case_id', $SubmitedCastList)
                ->select('b.*', 'l.case_ref_no', 'u.name as user_name')
                ->orderBy('b.created_at', 'desc');


            // $BonusRequestList = DB::table('bonus_request_list as b')
            // ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
            // ->leftJoin('users as u', 'u.id', '=', 'b.user_id')
            // // ->where('b.status', '=', 1)
            // ->select('b.*', 'l.case_ref_no', 'u.name as user_name')
            // ->orderBy('created_at', 'desc')->get(); 

            if ($request->input("requestor") <> 99) {
                // $BonusRequestList = $BonusRequestList->where('user_id', $request->input("requestor"));

                $BonusRequestList = $BonusRequestList->where(function ($q) use ($request) {
                    $q->where('lawyer_id', '=', $request->input("requestor"))
                        ->orWhere('clerk_id', '=', $request->input("requestor"));
                });
            }

            if ($request->input("status") <> 0) {
                $BonusRequestList = $BonusRequestList->where('b.status', $request->input("status"));
            }

            $date_type = 'request';

            if ($request->input("date_type") == 'request') {
                $date_type = 'b.created_at';
            }
            else if ($request->input("date_type") == 'approval') {
                $date_type = 'b.approved_date';
            }

            if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
                $BonusRequestList = $BonusRequestList->whereBetween($date_type, [$request->input("date_from"), $request->input("date_to")]);
            } else {
                if ($request->input("date_from") <> null) {
                    $BonusRequestList = $BonusRequestList->where($date_type, '>=', $request->input("date_from"));
                }

                if ($request->input("date_to") <> null) {
                    $BonusRequestList = $BonusRequestList->where($date_type, '<=', $request->input("date_to"));
                }
            }

            $BonusRequestList = $BonusRequestList->get();


            return DataTables::of($BonusRequestList)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a target="_blank" href="/bonus-request-details/' . $row->id . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>';
                    return $actionBtn;
                })
                ->editColumn('status', function ($data) {
                    if ($data->status == '1')
                        return '<span class="label bg-warning">Reviewing</span>';
                    elseif ($data->status == '2')
                        return '<span class="label bg-success">Approved</span>';
                    elseif ($data->status == '3')
                        return '<span class="label bg-danger">Rejected</span>';
                })
                ->editColumn('bonus_type', function ($data) {
                    if ($data->bonus_type == 'SMPSIGNED')
                        return '<span class="label bg-info">2% Bonus</span>';
                    elseif ($data->bonus_type == 'CLOSEDCASE')
                        return '<span class="label bg-success">3% Bonus</span>';
                })
                ->editColumn('created_at', function ($data) {
                    $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->format('d-m-Y h:i A');
                    return $formatedDate;
                })
                ->editColumn('case_ref_no', function ($row) {
                    if ($row->case_id != 0) {
                        $actionBtn = ' <a target="_blank" href="/case/' . $row->case_id . '" class="  " >' . $row->case_ref_no . ' >> </a>';
                    } else {
                        $actionBtn = $row->case_ref;
                    }
                    // $actionBtn = ' <a href="/case/'. $row->case_id . '" class="  " >'. $row->case_ref_no . ' </a>';

                    return $actionBtn;
                })
                ->rawColumns(['action', 'status', 'case_ref_no', 'bonus_type'])
                ->make(true);
        }
    }

    public function BonusReviewDetails($id)
    {
        $current_user = auth()->user();
        $role = $current_user->menuroles;

        $LoanCase = [];
        $LoanCaseBillMain = [];

        $lawyer = [];
        $clerk = [];

        $BonusRequestRecordsLawyer = 0;
        $BonusRequestRecordsClerk = 0;
        $BonusRequestRecordsLawyerPer = 0;
        $BonusRequestRecordsClerkPer = 0;

        $BonusRequestList = BonusRequestList::where('id', '=', $id)->first();

        if ($BonusRequestList) {
            $LoanCase = LoanCase::where('id', '=', $BonusRequestList->case_id)->first();
            $LoanCaseBillMain = LoanCaseBillMain::where('case_id', '=', $BonusRequestList->case_id)->get();

            if ($LoanCase->lawyer_id != 0) {
                $lawyer = User::where('id', '=', $LoanCase->lawyer_id)->first();
                $BonusRequestRecords = BonusRequestRecords::where('bonus_request_list_id', '=', $id)->where('user_id', '=', $LoanCase->lawyer_id)->first();

                if ($BonusRequestRecords) {
                    $BonusRequestRecordsLawyer = $BonusRequestRecords->amount;
                    $BonusRequestRecordsLawyerPer = $BonusRequestRecords->percentage;
                }
            }

            if ($LoanCase->clerk_id != 0) {
                $clerk = User::where('id', '=', $LoanCase->clerk_id)->first();

                $BonusRequestRecords = BonusRequestRecords::where('bonus_request_list_id', '=', $id)->where('user_id', '=', $LoanCase->clerk_id)->first();

                if ($BonusRequestRecords) {
                    $BonusRequestRecordsClerk = $BonusRequestRecords->amount;
                    $BonusRequestRecordsClerkPer = $BonusRequestRecords->percentage;
                }
            } else {
                $clerk = User::where('id', '=', $LoanCase->lawyer_id)->first();

                $BonusRequestRecords = BonusRequestRecords::where('bonus_request_list_id', '=', $id)->where('user_id', '=', $LoanCase->lawyer_id)->first();

                if ($BonusRequestRecords) {
                    $BonusRequestRecordsClerk = $BonusRequestRecords->amount;
                    $BonusRequestRecordsClerkPer = $BonusRequestRecords->percentage;
                }
            }


            $LoanCase = DB::table('loan_case as l')
                ->leftJoin('users as u', 'u.id', '=', 'l.sales_user_id')
                ->select('l.*', 'u.name as sales_name')
                ->where('l.id', '=', $BonusRequestList->case_id)->first();


            $LoanCaseBillMain = DB::table('loan_case_bill_main as b')
                ->leftJoin('users as u', 'u.id', '=', 'b.marketing_id')
                ->where('b.status', '=', 1)
                ->select('b.*', 'u.name as sales_name')
                ->where('case_id', '=', $BonusRequestList->case_id)->get();

            if (count($LoanCaseBillMain) > 0) {
                for ($i = 0; $i < count($LoanCaseBillMain); $i++) {

                    $total_collection = 0;

                    $VoucherMain = VoucherMain::where('case_bill_main_id', '=', $LoanCaseBillMain[$i]->id)->where('status', '<>', 99)->where('voucher_type', '=', 4)->get();

                    if (count($VoucherMain) > 0) {
                        for ($j = 0; $j < count($VoucherMain); $j++) {
                            $total_collection += $VoucherMain[$j]->total_amount;
                        }
                    }

                    $LoanCaseBillMain[$i]->total_collection_sum = $total_collection;
                }
            }
        }

        if (in_array($current_user->menuroles, ['admin', 'management']) || $current_user->id == 37) {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();

            $staff = Users::where('status', '<>', 99)->whereIn('menuroles', ['lawyer', 'clerk', 'chambering'])->orderBy('name', 'asc')->get();
            $recon_date = VoucherDetails::where('recon_date', '<>', null)->select('recon_date')
                ->groupBy('recon_date')
                ->get();

            $recon_date = VoucherMain::where('recon_date', '<>', null)->select('recon_date')
                ->groupBy('recon_date')
                ->orderBy('recon_date', 'asc')
                ->get();

            return view('dashboard.bonus-request.edit', [
                'case' => $LoanCase,
                'LoanCaseBillMain' => $LoanCaseBillMain,
                'OfficeBankAccount' => $OfficeBankAccount,
                'BonusRequestList' => $BonusRequestList,
                'BonusRequestRecordsLawyer' => $BonusRequestRecordsLawyer,
                'BonusRequestRecordsClerk' => $BonusRequestRecordsClerk,
                'BonusRequestRecordsLawyerPer' => $BonusRequestRecordsLawyerPer,
                'BonusRequestRecordsClerkPer' => $BonusRequestRecordsClerkPer,
                'staffs' => $staff,
                'lawyer' => $lawyer,
                'clerk' => $clerk,
                'current_user' => $current_user,
                'recon_date' => $recon_date
            ]);
        } else {

            return redirect()->route('dashboard.index');
        }
    }

    public function getStaffBonusReviewList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            $SubmitedCastList = BonusRequestList::DISTINCT()->whereDate('created_at', '<', '2022-12-31')->pluck('case_id')->toArray();

            $BonusRequestList = DB::table('bonus_request_list as b')
                ->leftjoin('bonus_request_records as r', function ($join) use ($current_user) {
                    $join->on('r.bonus_request_list_id', '=', 'b.id')
                        ->where('r.bonus_type', 'CLOSEDCASE')
                        ->where('r.user_id', '=', $current_user->id);
                })
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->leftJoin('users as u', 'u.id', '=', 'b.user_id')
                ->where('b.bonus_type', 'CLOSEDCASE')
                ->select('b.*', 'l.case_ref_no', 'u.name as user_name', 'r.amount as bonus_amt')
                ->orderBy('b.created_at', 'desc');


            if ($current_user->id == 37)
            {
                $BonusRequestList = $BonusRequestList->where(function ($q) use ($request, $current_user) {
                    $q->where('lawyer_id', '=', 17)
                        ->orWhere('clerk_id', '=', 17);
                });
            }
            else
            {
                $BonusRequestList = $BonusRequestList->where(function ($q) use ($request, $current_user) {
                    $q->where('lawyer_id', '=', $current_user->id)
                        ->orWhere('clerk_id', '=', $current_user->id);
                });
            }

            

            if ($request->input("status") <> 0) {
                $BonusRequestList = $BonusRequestList->where('b.status', $request->input("status"));
            }

            $BonusRequestList = $BonusRequestList->distinct()->get();


            return DataTables::of($BonusRequestList)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a target="_blank" href="/bonus-request-details/' . $row->id . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>';
                    return $actionBtn;
                })
                ->editColumn('status', function ($data) {
                    if ($data->status == '1')
                        return '<span class="label bg-warning">Reviewing</span>';
                    elseif ($data->status == '2')
                        return '<span class="label bg-success">Approved</span>';
                    elseif ($data->status == '3')
                        return '<span class="label bg-danger">Rejected</span>';
                })
                ->editColumn('bonus_type', function ($data) {
                    if ($data->bonus_type == 'SMPSIGNED')
                        return '<span class="label bg-info">2% Bonus</span>';
                    elseif ($data->bonus_type == 'CLOSEDCASE')
                        return '<span class="label bg-success">3% Bonus</span>';
                })
                ->editColumn('created_at', function ($data) {
                    $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->format('d-m-Y h:i A');
                    return $formatedDate;
                })
                ->editColumn('case_ref_no', function ($row) {
                    if ($row->case_id != 0) {
                        $actionBtn = ' <a target="_blank" href="/case/' . $row->case_id . '" class="  " >' . $row->case_ref_no . ' >> </a>';
                    } else {
                        $actionBtn = $row->case_ref;
                    }
                    // $actionBtn = ' <a href="/case/'. $row->case_id . '" class="  " >'. $row->case_ref_no . ' </a>';

                    return $actionBtn;
                })
                ->rawColumns(['action', 'status', 'case_ref_no', 'bonus_type'])
                ->make(true);
        }
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

            $LoanCase = LoanCase::where('id', '=', $BonusRequestList->case_id)->first();

            $current_user = auth()->user();
            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = $BonusRequestList->id;
            $AccountLog->bill_id = 0;
            $AccountLog->ori_amt = 0;
            $AccountLog->new_amt = 0;
            $AccountLog->action = 'reject_bonus';
            $AccountLog->desc = $current_user->name . ' Rejected bonus for case (' . $LoanCase->case_ref_no . ')';
            $AccountLog->status = 1;
            $AccountLog->object_id = $BonusRequestList->id;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();
        }

        return response()->json(['status' => 1, 'message' => 'Application Rejected']);
    }

    public function approveBonus(Request $request, $id)
    {
        // $reject_reason = $request->input('reject_reason');

        // if ($reject_reason == '' || $reject_reason == null)
        // {
        //     return response()->json(['status' => 0, 'message' => 'Please provide reason of rejection']);
        // }

        $current_user = auth()->user();
        // $BonusRequestHistory  = new BonusRequestHistory();

        // $BonusRequestHistory->bonus_request_list_id = $id;
        // $BonusRequestHistory->status = 2;
        // // $BonusRequestHistory->remarks = $request->input('remarks');
        // $BonusRequestHistory->created_at = date('Y-m-d H:i:s');
        // $BonusRequestHistory->created_by = $current_user->id;
        // $BonusRequestHistory->save();

        $BonusRequestList = BonusRequestList::where('id', '=', $id)->first();

        $LoanCase = LoanCase::where('id', '=', $BonusRequestList->case_id)->first();

        $lawyer = $LoanCase->lawyer_id;
        $clerk = $LoanCase->clerk_id;


        // create record for lawyer
        $BonusRequestRecords  = new BonusRequestRecords();

        $BonusRequestRecords->bonus_request_list_id = $id;
        $BonusRequestRecords->status = 1;
        $BonusRequestRecords->user_id = $lawyer;
        $BonusRequestRecords->bonus_type = $BonusRequestList->bonus_type;
        $BonusRequestRecords->percentage = $request->input('bonus_lawyer_per');
        $BonusRequestRecords->amount = $request->input('bonus_lawyer');
        $BonusRequestRecords->created_at = date('Y-m-d H:i:s');
        $BonusRequestRecords->created_by = $current_user->id;
        $BonusRequestRecords->save();


        // create record for clerk, if no clerk then lawyer will take this portion
        $BonusRequestRecords  = new BonusRequestRecords();

        $BonusRequestRecords->bonus_request_list_id = $id;
        $BonusRequestRecords->status = 1;

        if ($clerk != 0) {
            $BonusRequestRecords->user_id = $clerk;
        } else {
            $BonusRequestRecords->user_id = $lawyer;
        }

        $BonusRequestRecords->bonus_type = $BonusRequestList->bonus_type;
        $BonusRequestRecords->amount = $request->input('bonus_lawyer');
        $BonusRequestRecords->percentage = $request->input('bonus_lawyer_per');
        $BonusRequestRecords->created_at = date('Y-m-d H:i:s');
        $BonusRequestRecords->created_by = $current_user->id;
        $BonusRequestRecords->save();



        if ($BonusRequestList) {
            $BonusRequestList->status = 2;
            $BonusRequestList->approved_date = date('Y-m-d H:i:s');
            $BonusRequestList->selected_bill_id = $request->input('selected_bill');
            $BonusRequestList->save();

            $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $request->input('selected_bill'))->first();

            if ($LoanCaseBillMain) {
                if ($BonusRequestList->bonus_type == 'SMPSIGNED') {
                    $LoanCaseBillMain->total_staff_bonus_2_per += ($request->input('bonus_lawyer') * 2);
                } else if ($BonusRequestList->bonus_type == 'CLOSEDCASE') {
                    $LoanCaseBillMain->total_staff_bonus_3_per += ($request->input('bonus_lawyer') * 2);
                }

                $LoanCaseBillMain->save();
            }

            $current_user = auth()->user();
            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = $LoanCase->id;
            $AccountLog->bill_id = $LoanCaseBillMain->id;
            $AccountLog->ori_amt = 0;
            $AccountLog->new_amt = 0;
            $AccountLog->action = 'approve_bonus';
            $AccountLog->desc = $current_user->name . ' Approved bonus for case (' . $LoanCase->case_ref_no . ')';
            $AccountLog->status = 1;
            $AccountLog->object_id = $BonusRequestList->id;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();
        }

        return response()->json(['status' => 1, 'message' => 'Application approved']);
    }

    public function revertBonus(Request $request, $id)
    {

        $current_user = auth()->user();
        
        $BonusRequestList = BonusRequestList::where('id', '=', $id)->first();

        if($BonusRequestList->status == 2)
        {
            BonusRequestRecords::where('bonus_request_list_id', $id)->delete();
        }
        else if($BonusRequestList->status == 3)
        {
            BonusRequestHistory::where('bonus_request_list_id', $id)->delete();
        }

        $BonusRequestList->status = 1;
        $BonusRequestList->approved_date = null;
        $BonusRequestList->selected_bill_id = 0;
        $BonusRequestList->save();

        $LoanCase = LoanCase::where('id', '=', $BonusRequestList->case_id)->first();

        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $LoanCase->id;
        $AccountLog->bill_id = 0;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->action = 'revert_bonus';
        $AccountLog->desc = $current_user->name . ' Reverted bonus for case (' . $LoanCase->case_ref_no . ')';
        $AccountLog->status = 1;
        $AccountLog->object_id = $BonusRequestList->id;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        return response()->json(['status' => 1, 'message' => 'Bonus reverted']);
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

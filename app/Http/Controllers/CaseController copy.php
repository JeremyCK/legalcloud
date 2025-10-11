<?php

namespace App\Http\Controllers;

use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\Users;
use App\Models\Banks;
use App\Models\Customer;
use App\Models\Parameter;
use App\Models\CaseTemplate;
use App\Models\LoanCase;
use App\Models\LoanCaseDetails;
use App\Models\CaseType;
use App\Models\CaseMasterListCategory;
use App\Models\CaseMasterListField;
use App\Models\LoanAttachment;
use App\Models\LoanCaseNotes;
use App\Models\AccountTemplateMain;
use App\Models\LoanCaseDocumentVersion;
use App\Models\LoanCaseDocumentPage;
use App\Models\LoanCaseMasterList;
use App\Models\LoanCaseAccount;
use App\Models\Voucher;
use App\Models\CaseAccountTransaction;

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\EmailController;

use App\Models\perm;
use Illuminate\Http\Request;
use App\Models\MenusLang;
use App\Http\Helper\Helper;
use App\Models\AccountCategory;
use App\Models\AccountTemplateDetails;
use App\Models\CaseTemplateCategories;
use App\Models\CaseTemplateDetails;
use App\Models\CaseTemplateItems;
use App\Models\CaseTemplateMain;
use App\Models\CaseTodo;
use App\Models\Courier;
use App\Models\DocumentTemplateFileDetails;
use App\Models\DocumentTemplateFileMain;
use App\Models\GroupPortfolio;
use App\Models\LoanCaseBillDetails;
use App\Models\LoanCaseBillMain;
use App\Models\LoanCaseChecklistDetails;
use App\Models\LoanCaseChecklistMain;
use App\Models\LoanCaseDispatch;
use App\Models\LoanCaseFiles;
use App\Models\LoanCaseTrust;
use App\Models\MemberPortfolio;
use App\Models\Portfolio;
use App\Models\QuotationTemplateDetails;
use App\Models\QuotationTemplateMain;
use App\Models\TeamMember;
use App\Models\TeamMembers;
use App\Models\TeamPortfolio;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserKpiHistory;
use App\Models\VoucherDetails;
use App\Models\VoucherMain;
use Facade\FlareClient\Http\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Type\Decimal;

use App\Http\Controllers\ReferralController;
use App\Models\AccountItem;
use App\Models\AccountLog;
use App\Models\Branch;
use App\Models\DocumentTemplateFileFolder;
use App\Models\LoanCaseBillAccount;
use App\Models\LoanCaseKivNotes;
use App\Models\LoanCaseTrustMain;
use App\Models\Notification;
use App\Models\OfficeBankAccount;
use App\Models\Referral;
// use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use NumberFormatter;
use PhpOffice\PhpSpbln_readsheet\Style\NumberFormat\NumberFormatter as NumberFormatNumberFormatter;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\Facades\DataTables;

class CaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allowCreateCase = "false";
        $openCaseCount = 0;
        $closedCaseCount = 0;
        $InProgressCaseCount = 0;
        $OverdueCaseCount = 0;
        $current_user = auth()->user();

        $userRoles = Auth::user()->getRoleNames();

        $role = array('sales', 'admin', 'management');

        $allowCreateCase = Helper::getRolePermission($userRoles, $role);
        $case_type = CaseType::where('status', '=', 1)->get();
        $branch = Branch::where('status', '=', 1)->get();

        if ($current_user->menuroles == 'sales') {

            $case = DB::table('loan_case')
                ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
                ->leftJoin('client', 'client.id', '=', 'loan_case.customer_id')
                ->leftJoin("loan_case_masterlist as m", function ($join) {
                    $join->on("m.case_id", "=", "loan_case.id")
                        ->where("m.masterlist_field_id", "=", "148");
                })
                ->leftJoin("loan_case_masterlist as m2", function ($join) {
                    $join->on("m2.case_id", "=", "loan_case.id")
                        ->where("m2.masterlist_field_id", "=", "147");
                })
                ->select(array('loan_case.*', 'case_type.name AS type_name', 'client.name AS client_name', 'm.value AS completion_date', 'm2.value AS spa_date'))
                ->where('sales_user_id', '=', $current_user->id)
                ->where('loan_case.status', '!=', 99)
                ->orderBy('loan_case.id', 'ASC')
                ->paginate(10);

            $InProgressCaseCount = DB::table('loan_case')->where('status', '=', 1)->where('sales_user_id', '=', $current_user->id)->count();
            $openCaseCount = DB::table('loan_case')->whereIn('status', [1, 2, 3])->where('sales_user_id', '=', $current_user->id)->count();
            $closedCaseCount = DB::table('loan_case')->where('status', '=', 0)->where('sales_user_id', '=', $current_user->id)->count();
            $OverdueCaseCount = DB::table('loan_case')->where('status', '=', 4)->where('sales_user_id', '=', $current_user->id)->count();
        }
        if ($current_user->menuroles == 'lawyer' || $current_user->menuroles == 'chambering') {

            $case = DB::table('loan_case')
                ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
                ->leftJoin('client', 'client.id', '=', 'loan_case.customer_id')
                ->leftJoin("loan_case_masterlist as m", function ($join) {
                    $join->on("m.case_id", "=", "loan_case.id")
                        ->where("m.masterlist_field_id", "=", "148");
                })
                ->leftJoin("loan_case_masterlist as m2", function ($join) {
                    $join->on("m2.case_id", "=", "loan_case.id")
                        ->where("m2.masterlist_field_id", "=", "147");
                })
                ->select(array('loan_case.*', 'case_type.name AS type_name', 'client.name AS client_name', 'm.value AS completion_date', 'm2.value AS spa_date'))
                ->where('lawyer_id', '=', $current_user->id)
                // ->where('clerk_id', '=', $current_user->id)
                ->where('loan_case.status', '!=', 99)
                ->orderBy('loan_case.id', 'ASC')
                ->paginate(10);
                

            $InProgressCaseCount = DB::table('loan_case')->where('status', '=', 1)->where('lawyer_id', '=', $current_user->id)->count();
            $openCaseCount = DB::table('loan_case')->whereIn('status', [1, 2, 3])->where('lawyer_id', '=', $current_user->id)->count();
            $closedCaseCount = DB::table('loan_case')->where('status', '=', 0)->where('lawyer_id', '=', $current_user->id)->count();
            $OverdueCaseCount = DB::table('loan_case')->where('status', '=', 4)->where('lawyer_id', '=', $current_user->id)->count();
        }

        if ($current_user->menuroles == 'clerk') {
            // $case = TodoList::where('lawyer_id', '=', $current_user->id)->get();

            $case = DB::table('loan_case')
                ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
                ->leftJoin('client', 'client.id', '=', 'loan_case.customer_id')
                ->leftJoin("loan_case_masterlist as m", function ($join) {
                    $join->on("m.case_id", "=", "loan_case.id")
                        ->where("m.masterlist_field_id", "=", "148");
                })
                ->leftJoin("loan_case_masterlist as m2", function ($join) {
                    $join->on("m2.case_id", "=", "loan_case.id")
                        ->where("m2.masterlist_field_id", "=", "147");
                })
                ->select(array('loan_case.*', 'case_type.name AS type_name', 'client.name AS client_name', 'm.value AS completion_date', 'm2.value AS spa_date'))
                ->where('clerk_id', '=', $current_user->id)
                ->where('loan_case.status', '!=', 99)
                ->orderBy('loan_case.id', 'ASC')
                ->paginate(10);
        }

        if ($current_user->menuroles == 'admin' || $current_user->menuroles == 'management'  || $current_user->menuroles == 'account' || $current_user->menuroles == 'receptionist') {
            $case = DB::table('loan_case')
                ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
                ->leftJoin('client', 'client.id', '=', 'loan_case.customer_id')
                ->leftJoin("loan_case_masterlist as m", function ($join) {
                    $join->on("m.case_id", "=", "loan_case.id")
                        ->where("m.masterlist_field_id", "=", "148");
                })
                ->leftJoin("loan_case_masterlist as m2", function ($join) {
                    $join->on("m2.case_id", "=", "loan_case.id")
                        ->where("m2.masterlist_field_id", "=", "147");
                })
                ->select(array('loan_case.*', 'case_type.name AS type_name', 'client.name AS client_name', 'm.value AS completion_date', 'm2.value AS spa_date'))
                ->where('loan_case.status', '!=', 99)
                ->orderBy('loan_case.id', 'ASC')
                ->paginate(10);

            $InProgressCaseCount = DB::table('loan_case')->where('status', '=', 1)->count();
            $openCaseCount = DB::table('loan_case')->whereIn('status', [1, 2, 3])->count();
            $closedCaseCount = DB::table('loan_case')->where('status', '=', 0)->count();
            $OverdueCaseCount = DB::table('loan_case')->where('status', '=', 4)->count();
        } else {
            $role = $current_user->menuroles;
            if ($current_user->menuroles == 'sales') {
                $role = 'sales_user';

                $case = DB::table('loan_case')
                    ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
                    ->leftJoin('client', 'client.id', '=', 'loan_case.customer_id')
                    ->leftJoin("loan_case_masterlist as m", function ($join) {
                        $join->on("m.case_id", "=", "loan_case.id")
                            ->where("m.masterlist_field_id", "=", "148");
                    })
                    ->leftJoin("loan_case_masterlist as m2", function ($join) {
                        $join->on("m.case_id", "=", "loan_case.id")
                            ->where("m.masterlist_field_id", "=", "147");
                    })
                    ->select(array('loan_case.*', 'case_type.name AS type_name', 'client.name AS client_name', 'm.value AS completion_date', 'm2.value AS spa_date'))
                    ->where($role . '_id', '=', $current_user->id)
                    ->where('loan_case.status', '!=', 99)
                    ->orderBy('loan_case.id', 'ASC')
                    ->paginate(10);
            } else if ($current_user->menuroles == 'chambering') {
                $role = 'lawyer';

                $case = DB::table('loan_case')
                    ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
                    ->leftJoin('client', 'client.id', '=', 'loan_case.customer_id')
                    ->leftJoin("loan_case_masterlist as m", function ($join) {
                        $join->on("m.case_id", "=", "loan_case.id")
                            ->where("m.masterlist_field_id", "=", "148");
                    })
                    ->leftJoin("loan_case_masterlist as m2", function ($join) {
                        $join->on("m.case_id", "=", "loan_case.id")
                            ->where("m.masterlist_field_id", "=", "147");
                    })
                    ->select(array('loan_case.*', 'case_type.name AS type_name', 'client.name AS client_name', 'm.value AS completion_date', 'm2.value AS spa_date'))
                    // ->where('lawyer_id', '=', $current_user->id)
                    ->where('clerk_id', '=', $current_user->id)
                    ->orWhere('lawyer_id', '=', $current_user->id)
                    ->where('loan_case.status', '!=', 99)
                    ->orderBy('loan_case.id', 'ASC')
                    ->paginate(10);
            }
        }
        

        $lawyerList = Users::where('menuroles', '=', 'lawyer')->get();
        $clerkList = Users::where('menuroles', '=', 'clerk')->get();


        // return Helper::generateEmail('1','3');

        return view('dashboard.case.index', [
            'cases' => $case,
            'openCaseCount' => $openCaseCount,
            'InProgressCaseCount' => $InProgressCaseCount,
            'closedCaseCount' => $closedCaseCount,
            'OverdueCaseCount' => $OverdueCaseCount,
            'allowCreateCase' => $allowCreateCase,
            'lawyerList' => $lawyerList,
            'current_user' => $current_user,
            'clerkList' => $clerkList,
            'branch' => $branch,
            'case_type' => $case_type
        ]);
    }

    public function filterCase(Request $request)
    {

        $cases = DB::table('loan_case')
            ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
            ->leftJoin('client', 'client.id', '=', 'loan_case.customer_id')
            ->leftJoin("loan_case_masterlist as m", function ($join) {
                $join->on("m.case_id", "=", "loan_case.id")
                    ->where("m.masterlist_field_id", "=", "148");
            })
            ->leftJoin("loan_case_masterlist as m2", function ($join) {
                $join->on("m2.case_id", "=", "loan_case.id")
                    ->where("m2.masterlist_field_id", "=", "147");
            })
            ->select(array('loan_case.*', 'case_type.name AS type_name', 'client.name AS client_name', 'm.value AS completion_date', 'm2.value AS spa_date'))
            // ->where('loan_case.case_ref_no', '=',   $request->input('case_ref_no_search'))
            // ->where('loan_case.branch_id', '=',   $request->input('branch_id'))
            ->orWhere('loan_case.case_ref_no', 'like',   '%' . $request->input('case_ref_no_search') . '%')
            ->orWhere('client.name', 'like',   '%' . $request->input('case_ref_no_search') . '%')
            ->orderBy('loan_case.id', 'ASC')
            ->paginate(10);


        // $cases = DB::table('loan_case')
        // ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
        // ->leftJoin('client', 'client.id', '=', 'loan_case.customer_id')
        // ->leftJoin('users as lawyer', 'lawyer.id', '=', 'loan_case.lawyer_id')
        // ->leftJoin('users as clerk', 'clerk.id', '=', 'loan_case.clerk_id')
        // ->select(array('loan_case.*', 'case_type.name AS type_name', 'client.name AS client_name', 'client.phone_no AS client_phone_no', 
        // 'lawyer.name AS lawyer_name', 'clerk.name AS clerk_name'))
        // // ->where('loan_case.case_ref_no', '=',   $request->input('case_ref_no_search'))
        // // ->orWhere('client.name', 'like',   '%'.$request->input('case_ref_no_search').'%')
        // ->when($request->has('case_ref_no'), function ($cases) use ($request) {
        //     // $cases->where('loan_case.case_ref_no', '=', $request->input('case_ref_no'));
        //     $cases->orWhere('loan_case.case_ref_no', 'like',   '%'.$request->input('case_ref_no').'%');
        // })
        // ->when($request->has('ic'), function ($cases) use ($request) {
        //     $cases->where('client.ic_no', '=', $request->input('ic'));
        // })
        // ->when($request->has('name'), function ($cases) use ($request) {
        //     $cases->orWhere('client.name', 'like',   '%'.$request->input('name').'%');
        //     $cases->orWhere('client.name', 'like',   $request->input('name').'%');
        //     $cases->orWhere('client.name', 'like',   '%'.$request->input('name'));
        // })
        // ->when($request->has('tel_no'), function ($cases) use ($request) {
        //     $cases->Where('client.phone_no', '=', '%'.$request->input('tel_no').'%');
        // })
        // ->paginate(10);

        return response()->json([
            'view' => view('dashboard.case.table.tbl-case-list', compact('cases'))->render()
        ]);

        // return  $users;
    }

    public function filterCaseByRole(Request $request)
    {

        if ($request->input('role_id') == "7") {
            $cases = DB::table('loan_case')
                ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
                ->leftJoin('client', 'client.id', '=', 'loan_case.customer_id')
                ->leftJoin("loan_case_masterlist as m", function ($join) {
                    $join->on("m.case_id", "=", "loan_case.id")
                        ->where("m.masterlist_field_id", "=", "148");
                })
                ->leftJoin("loan_case_masterlist as m2", function ($join) {
                    $join->on("m2.case_id", "=", "loan_case.id")
                        ->where("m2.masterlist_field_id", "=", "147");
                })
                ->select(array('loan_case.*', 'case_type.name AS type_name', 'client.name AS client_name', 'm.value AS completion_date', 'm2.value AS spa_date'))
                ->where('loan_case.lawyer_id', '=',   $request->input('id'))
                ->orderBy('loan_case.id', 'ASC')
                ->paginate(100);
        } else {
            $cases = DB::table('loan_case')
                ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
                ->leftJoin('client', 'client.id', '=', 'loan_case.customer_id')
                ->leftJoin("loan_case_masterlist as m", function ($join) {
                    $join->on("m.case_id", "=", "loan_case.id")
                        ->where("m.masterlist_field_id", "=", "148");
                })
                ->leftJoin("loan_case_masterlist as m2", function ($join) {
                    $join->on("m2.case_id", "=", "loan_case.id")
                        ->where("m2.masterlist_field_id", "=", "147");
                })
                ->select(array('loan_case.*', 'case_type.name AS type_name', 'client.name AS client_name', 'm.value AS completion_date', 'm2.value AS spa_date'))
                ->where('loan_case.clerk_id', '=',   $request->input('id'))
                ->orderBy('loan_case.id', 'ASC')
                ->paginate(100);
        }




        return response()->json([
            'view' => view('dashboard.case.table.tbl-case-list', compact('cases'))->render()
        ]);

        // return  $users;
    }

    public function filterCaseByBranch(Request $request)
    {

        $cases = DB::table('loan_case')
            ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
            ->leftJoin('client', 'client.id', '=', 'loan_case.customer_id')
            ->leftJoin("loan_case_masterlist as m", function ($join) {
                $join->on("m.case_id", "=", "loan_case.id")
                    ->where("m.masterlist_field_id", "=", "148");
            })
            ->leftJoin("loan_case_masterlist as m2", function ($join) {
                $join->on("m2.case_id", "=", "loan_case.id")
                    ->where("m2.masterlist_field_id", "=", "147");
            })
            ->select(array('loan_case.*', 'case_type.name AS type_name', 'client.name AS client_name', 'm.value AS completion_date', 'm2.value AS spa_date'))
            ->where('loan_case.branch_id', '=',   $request->input('branch_id'))
            ->orderBy('loan_case.id', 'ASC')
            ->paginate(100);




        return response()->json([
            'view' => view('dashboard.case.table.tbl-case-list', compact('cases'))->render()
        ]);

        // return  $users;
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
        $lawyer = Users::where('id', '=', 7)->get();
        $sales = Users::where('id', '=', 6)->get();
        $banks = Banks::where('status', '=', 1)->get();
        $portfolios = Portfolio::where('status', '=', 1)->orderBy('name')->get();
        $case_type = CaseType::where('status', '=', 1)->orderByDesc('name')->get();
        // $referrals = Referral::where('status', '=', 1)->get();

        $referrals = DB::table('referral as r')
            ->leftJoin('banks as b', 'b.id', '=', 'r.bank_id')
            ->select('r.*', 'b.name as bank_name')
            ->where('r.status', '=', 1)
            ->get();

        $Branchs = Branch::where('status', '=', 1)->get();

        // $query = DB::table('users')
        // ->leftJoin('loan_case', 'users.id', '=', 'loan_case.lawyer_id')
        // ->select(array('users.*', DB::raw('COUNT(loan_case.lawyer_id) as followers')))
        // ->groupBy('users.id')
        // ->orderByDesc('followers')
        // ->get();



        return view('dashboard.case.create', [
            'templates' => CaseTemplate::all(),
            'lawyers' => $lawyer,
            'sales' => $sales,
            'banks' => $banks,
            'referrals' => $referrals,
            'portfolios' => $portfolios,
            'Branchs' => $Branchs,
            'case_type' => $case_type
        ]);
    }

    public function assignTask($role)
    {
        $result = [];

        //in future maybe have to take staff leave status as consideration, currently based on least task and by sorting
        $user = DB::table('users')
            ->leftJoin('loan_case', 'users.id', '=', 'loan_case.' . $role . '_id')
            ->select(array('users.*', DB::raw('COUNT(loan_case.' . $role . '_id) as task_count')))
            ->where('menuroles', 'like', '%' . $role . '%')
            ->groupBy('users.id')
            ->orderBy('task_count')
            ->get();

        if (count($user)) {
            $result[0] = $user[0];
        }

        return $result;
    }

    public function assignTaskV2($case_type, $bank)
    {
        $result = [];


        $CaseTypeDB = CaseType::where('id', '=', $case_type)->get();




        if ($CaseTypeDB) {
            // 1. select out the group that have selected portfolio
            if ($CaseTypeDB[0]->is_bank_required == 1) {
                $group = GroupPortfolio::where('portfolio_id', '=', $bank)->where('type', '=', 'BANK')->get();
            } else {
                $group = GroupPortfolio::where('portfolio_id', '=', $case_type)->where('type', '=', 'CASE')->get();
            }

            // 2. filter out the group with lesser case
            if ($group) {
                $groupList = [];

                for ($i = 0; $i < count($group); $i++) {
                    array_push($groupList, $group[$i]->group_id);
                }



                if (count($groupList) > 0) {


                    $team = DB::table('team_main')
                        ->leftJoin('loan_case', 'team_main.id', '=', 'loan_case.handle_group_id')
                        ->select(array('team_main.*', DB::raw('COUNT(loan_case.handle_group_id) as task_count')))
                        ->where('team_main.status',  1)
                        ->whereIn('team_main.id',  $groupList)
                        ->groupBy('team_main.id')
                        ->orderBy('task_count', 'ASC')
                        ->get();

                    return $team;

                    $member = TeamMember::where('team_main_id', '=', $team[0]->id)->get();

                    $memberList = [];

                    for ($i = 0; $i < count($member); $i++) {
                        array_push($memberList, $member[$i]->user_id);
                    }

                    if ($memberList) {
                        $clerk = DB::table('users')
                            ->leftJoin('loan_case', 'users.id', '=', 'loan_case.clerk_id')
                            ->select(array('users.*', DB::raw('COUNT(loan_case.clerk_id) as task_count')))
                            ->where('menuroles', 'like', '%clerk%')
                            ->whereIn('users.id',  $memberList)
                            ->groupBy('users.id')
                            ->orderBy('task_count', 'ASC')
                            ->limit(1)
                            ->get();

                        $lawyer = DB::table('users')
                            ->leftJoin('loan_case', 'users.id', '=', 'loan_case.lawyer_id')
                            ->select(array('users.*', DB::raw('COUNT(loan_case.lawyer_id) as task_count')))
                            ->where('menuroles', 'like', '%lawyer%')
                            ->whereIn('users.id',  $memberList)
                            ->groupBy('users.id')
                            ->orderBy('task_count', 'ASC')
                            ->limit(1)
                            ->get();


                        array_push($result,  ["team_id" => $team[0]->id, "lawyer_id" =>  $lawyer[0]->id, "lawyer_nick" =>  $lawyer[0]->nick_name,  "clerk_id" =>  $clerk[0]->id,  "clerk_nick" =>  $clerk[0]->nick_name]);
                    }
                }
            }
        }

        return $result;
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function masterlist()
    {
        $lawyer = Users::where('id', '=', 7)->get();
        $sales = Users::where('id', '=', 6)->get();
        $banks = Banks::where('status', '=', 1)->get();

        return view('dashboard.todolist.masterlist', ['templates' => CaseTemplate::all(), 'lawyers' => $lawyer, 'sales' => $sales, 'banks' => $banks]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $client_name =  $request->input('client_name');
        $bank_id =  $request->input('bank');
        $case_type =  $request->input('case_type');
        $customer = new Customer();
        $current_user = auth()->user();



        // temporary only one type
        $case_type = 1;

        $group = $this->assignTaskV3($case_type, $bank_id);

        if (count($group) <= 0) {
            return;
        }

        // Assign task
        // $group = $this->assignTaskV2($case_type, $bank_id);
        $client_short_code = Helper::generateNickName($client_name);




        $case_ref_no = '[sales]/[lawyer]/[bank]/[running_no]/[client]/[clerk]';
        $lawyer_id = $group[0]['lawyer_id'];
        $clerk_id = $group[0]['clerk_id'];

        $bank = Banks::where('id', '=', $bank_id)->get();
        $bank = Portfolio::where('id', '=', $bank_id)->get();


        $parameter = Parameter::where('parameter_type', '=', 'case_running_no')->first();


        $running_no = (int)$parameter->parameter_value_1 + 1;
        $parameter->parameter_value_1 = $running_no;
        $parameter->save();


        $case_ref_no = str_replace("[sales]", $current_user->nick_name, $case_ref_no);
        $case_ref_no = str_replace("[bank]", $bank[0]->short_code, $case_ref_no);
        $case_ref_no = str_replace("[running_no]", $running_no, $case_ref_no);
        $case_ref_no = str_replace("[client]", $client_short_code, $case_ref_no);

        $case_ref_no = str_replace("[lawyer]", $group[0]['lawyer_nick'], $case_ref_no);

        $case_ref_no = str_replace("[clerk]", $group[0]['clerk_nick'], $case_ref_no);

        $loanCase = new TodoList();
        $loanCase->case_ref_no = $case_ref_no;
        $loanCase->property_address = 'test';
        $loanCase->referral_name = $request->input('referral_name');
        $loanCase->referral_phone_no = $request->input('referral_phone_no');
        $loanCase->referral_email = $request->input('referral_email');
        $loanCase->purchase_price = $request->input('purchase_price');
        $loanCase->loan_sum = $request->input('loan_sum');
        $loanCase->remark = $request->input('remark');
        $loanCase->sales_user_id = $current_user->id;
        $loanCase->handle_group_id = $group[0]['team_id'];
        $loanCase->bank_id = $request->input('bank');
        $loanCase->lawyer_id = $lawyer_id;
        $loanCase->clerk_id = $clerk_id;
        $loanCase->case_type_id =  $case_type;
        $loanCase->status = "2";
        $loanCase->created_at = now();


        $loanCase->save();

        if ($loanCase) {
            $customer = $this->createCustomer($request, $case_ref_no);
            $loanCase->customer_id = $customer->id;
            $loanCase->save();
        }

        $request->session()->flash('message', 'Successfully created new case');

        return redirect()->route('case.index', ['cases' => TodoList::all()]);
    }

    public function createCase(Request $request)
    {
        $status = 1;
        $data = [];

        // return $request->input('client_email')

        $client_name =  $request->input('client_name');
        $bank_id =  $request->input('bank');
        $branch_id =  $request->input('branch');
        $case_type =  $request->input('case_type');
        $race =  $request->input('race');
        $first_house =  $request->input('first_house');
        $other_race =  $request->input('client_race_others');
        $customer = new Customer();
        $current_user = auth()->user();


        // temporary only one type
        $case_type = 1;

        $group = $this->taskAllocation($bank_id, $race, $branch_id);

        // if ($branch_id == 1) {
        //     $group = $this->taskAllocation($bank_id, $race);
        // } else {
        //     $group = [];
        //     $test =  ["team_id" => 20, "lawyer_id" =>  53, "lawyer_nick" =>  'E',  "clerk_id" =>  60,  "clerk_nick" =>  'NO'];
        //     array_push($group,  $test);
        // }


        // $group = $this->assignTaskV3($case_type, $bank_id);

        if (count($group) <= 0) {
            return response()->json(['status' => 0, 'message' => 'No such team handle this case type']);
        }
        // Assign task
        // $group = $this->assignTaskV2($case_type, $bank_id);
        $client_short_code = Helper::generateNickName($client_name);



        $case_ref_no = '[sales]/[lawyer]/[bank]/[running_no]/[client]/[clerk]';
        $lawyer_id = $group[0]['lawyer_id'];

        if ($group[0]['clerk_id'] != "") {
            $clerk_id = $group[0]['clerk_id'];
        } else {
            $clerk_id = 0;
        }

        $lawyer_user = User::where('id', '=', $lawyer_id)->first();



        if ($lawyer_user->branch_id == 2) {
            $Branch = Branch::where('id', '=', $lawyer_user->branch_id)->first();
            $case_ref_no = $Branch->short_code . "/" . $case_ref_no;
        }



        // if ($Branch->hq != "1") {
        //     $case_ref_no = $Branch->short_code . "/" . $case_ref_no;
        // }

        $bank = Banks::where('id', '=', $bank_id)->get();
        $bank = Portfolio::where('id', '=', $bank_id)->get();


        $parameter = Parameter::where('parameter_type', '=', 'case_running_no')->first();


        $running_no = (int)$parameter->parameter_value_1 + 1;
        $parameter->parameter_value_1 = $running_no;
        $parameter->save();


        $case_ref_no = str_replace("[sales]", $current_user->nick_name, $case_ref_no);
        $case_ref_no = str_replace("[bank]", $bank[0]->short_code, $case_ref_no);
        $case_ref_no = str_replace("[running_no]", $running_no, $case_ref_no);
        $case_ref_no = str_replace("[client]", $client_short_code, $case_ref_no);

        $case_ref_no = str_replace("[lawyer]", $group[0]['lawyer_nick'], $case_ref_no);

        if ($group[0]['clerk_id'] != "") {
            $case_ref_no = str_replace("[clerk]", $group[0]['clerk_nick'], $case_ref_no);
        } else {
            $case_ref_no = str_replace("/[clerk]", '', $case_ref_no);
        }

        $property_address = '';

        if ($request->input('property_address') == null) {
            $property_address = '';
        } else {
            $property_address = $request->input('property_address');
        }


        $loanCase = new TodoList();
        $loanCase->case_ref_no = $case_ref_no;
        $loanCase->property_address = $property_address;
        $loanCase->referral_name = $request->input('referral_name');
        $loanCase->referral_phone_no = $request->input('referral_phone_no');
        $loanCase->referral_email = $request->input('referral_email');
        $loanCase->referral_id = $request->input('referral_id');
        $loanCase->purchase_price = $request->input('purchase_price');
        $loanCase->loan_sum = $request->input('loan_sum');
        $loanCase->remark = $request->input('remark');
        $loanCase->sales_user_id = $current_user->id;
        $loanCase->handle_group_id = $group[0]['team_id'];
        $loanCase->bank_id = $request->input('bank');
        $loanCase->lawyer_id = $lawyer_id;
        $loanCase->clerk_id = $clerk_id;
        $loanCase->case_type_id =  $case_type;
        $loanCase->first_house =  $first_house;
        $loanCase->branch_id =  $branch_id;
        $loanCase->status = "2";
        $loanCase->created_at = now();


        $loanCase->save();

        if ($loanCase) {
            $customer = $this->createCustomer($request, $case_ref_no);
            $loanCase->customer_id = $customer->id;
            $loanCase->save();
        }

        return response()->json(['status' => 1, 'message' => 'Successfully created new case']);
    }

    public function taskAllocation($portfolio, $race, $branch_id)
    {
        $blnCheckRace = 1;
        $lawyerID = 0;
        $lawyerNick = '';
        $clerkID  = 0;
        $clerkNick  = '';
        $memberPortfolio = [];
        $result = [];


        $memberPortfolioid = DB::table('team_members as m')
            ->leftJoin('users as u', 'u.id', '=', 'm.user_id')
            ->select('m.user_id', 'm.team_main_id')
            ->where('m.leader', '=',  1)
            ->where('u.status', '=',  1)
            ->where('u.branch_id', '=',  $branch_id)
            ->where('m.status', '=',  1)
            ->get();


        $member_id = [];

        for ($i = 0; $i < count($memberPortfolioid); $i++) {
            array_push($member_id, $memberPortfolioid[$i]->user_id);
        }

        // // check race
        // if ($blnCheckRace == 1) {
        //     $memberPortfolio = DB::table('member_portfolio as m')
        //         ->leftJoin('portfolio as p', 'p.id', '=', 'm.portfolio_id')
        //         ->leftJoin('users as u', 'u.id', '=', 'm.user_id')
        //         ->select(array('m.*', 'p.name', 'u.race'))
        //         ->whereIn('m.user_id',  $member_id)
        //         ->where('u.race', '=',  $race)
        //         ->where('m.portfolio_id', '=',  $portfolio)
        //         ->get();
        // }




        if (count($memberPortfolio) <= 0) {
            $memberPortfolio = DB::table('member_portfolio as m')
                ->leftJoin('portfolio as p', 'p.id', '=', 'm.portfolio_id')
                ->leftJoin('users as u', 'u.id', '=', 'm.user_id')
                ->select(array('m.*', 'p.name', 'u.race'))
                ->whereIn('m.user_id',  $member_id)
                ->where('m.status', '=',  1)
                ->where('m.portfolio_id', '=',  $portfolio)
                ->get();
        }

        // return  $memberPortfolio;

        if (count($memberPortfolio) <= 0) {
            return [];
        }

        // get the lawyer for the case
        $member_id = [];

        for ($i = 0; $i < count($memberPortfolio); $i++) {

            array_push($member_id, $memberPortfolio[$i]->user_id);
        }

        $lawyer = DB::table('users as u')
            ->leftJoin('loan_case', function ($join) use ($member_id) {
                $join->on('u.id', '=', 'loan_case.lawyer_id')
                    ->whereIn('loan_case.lawyer_id',  $member_id);
            })
            ->select(array('u.*', DB::raw('COUNT(loan_case.id) as task_count')))
            ->whereIn('u.id',  $member_id)
            ->where('u.status', '=',  1)
            ->groupBy('u.id')
            ->orderBy('task_count', 'ASC')
            ->get();

        if (count($lawyer) > 0) {
            $lawyerID = $lawyer[0]->id;
            $lawyerNick = $lawyer[0]->nick_name;
        }

        // get clerk from team member
        $team_id = DB::table('team_members as m')->select('m.team_main_id')->where('m.user_id', '=',  $lawyerID)->get();


        $memberPortfolioid = DB::table('team_members as m')
            ->leftJoin('users as u', 'u.id', '=', 'm.user_id')
            ->select('m.user_id', 'm.team_main_id')
            ->where('m.leader', '=',  0)
            ->where('u.status', '=',  1)
            ->where('m.team_main_id', '=',  $team_id[0]->team_main_id)
            ->get();

        $member_id = [];


        for ($i = 0; $i < count($memberPortfolioid); $i++) {
            array_push($member_id, $memberPortfolioid[$i]->user_id);
        }


        // check race
        if ($blnCheckRace == 1) {
            $memberPortfolio = DB::table('member_portfolio as m')
                ->leftJoin('portfolio as p', 'p.id', '=', 'm.portfolio_id')
                ->leftJoin('users as u', 'u.id', '=', 'm.user_id')
                ->select(array('m.*', 'p.name', 'u.race'))
                ->whereIn('m.user_id',  $member_id)
                ->where('u.race', '=',  $race)
                ->where('u.status', '=',  1)
                ->where('m.portfolio_id', '=',  $portfolio)
                ->get();
        }

        if (count($memberPortfolio) <= 0) {
            $memberPortfolio = DB::table('member_portfolio as m')
                ->leftJoin('portfolio as p', 'p.id', '=', 'm.portfolio_id')
                ->leftJoin('users as u', 'u.id', '=', 'm.user_id')
                ->select(array('m.*', 'p.name', 'u.race'))
                ->whereIn('m.user_id',  $member_id)
                ->where('m.portfolio_id', '=',  $portfolio)
                ->where('u.status', '=',  1)
                ->get();
        }


        // get the lawyer for the case
        $member_id = [];

        for ($i = 0; $i < count($memberPortfolio); $i++) {

            array_push($member_id, $memberPortfolio[$i]->user_id);
        }

        $clerk = DB::table('users as u')
            ->leftJoin('loan_case', function ($join) use ($member_id) {
                $join->on('u.id', '=', 'loan_case.clerk_id')
                    ->whereIn('loan_case.clerk_id',  $member_id);
            })
            ->select(array('u.*', DB::raw('COUNT(loan_case.id) as task_count')))
            ->whereIn('u.id',  $member_id)
            ->where('u.status', '=',  1)
            ->groupBy('u.id')
            ->orderBy('task_count', 'ASC')
            ->get();



        if (count($clerk) > 0) {

            $clerkID = $clerk[0]->id;
            $clerkNick = $clerk[0]->nick_name;

            // $task_count = 0;
            // $clerk_int = 0;

            // $task_count =  $clerk[0]->task_count;


            // for ($i = 0; $i < count($clerk); $i++) {



            //     if ((int) $clerk[$i]->task_count >= $task_count)
            //     {
            //         $task_count = (int) $clerk[$i]->task_count;
            //         $clerk_int = $i;
            //     }

            // }

            // $clerkID = $clerk[ $clerk_int]->id;
            // $clerkNick = $clerk[ $clerk_int]->nick_name;
        }


        // $memberPortfolio = DB::table('member_portfolio as m')
        // ->leftJoin('portfolio as p', 'p.id', '=', 'm.portfolio_id')
        // ->leftJoin('users as u', 'u.id', '=', 'm.user_id')
        // ->select(array('m.*', 'p.name', 'u.race'))
        // ->whereIn('m.user_id',  $member_id)
        // ->where('m.portfolio_id', '=',  $portfolio)
        // ->get();
        array_push($result,  ["team_id" => $team_id[0]->team_main_id, "lawyer_id" =>  $lawyerID, "lawyer_nick" =>  $lawyerNick,  "clerk_id" =>  $clerkID,  "clerk_nick" =>  $clerkNick]);


        return $result;
    }

    public function assignTaskV3($ccaseType, $bank)
    {
        $result = [];


        $teamPortfolio = TeamPortfolio::where('portfolio_id', '=', $bank)->get();
        $groupList = [];

        if ($teamPortfolio) {

            for ($i = 0; $i < count($teamPortfolio); $i++) {
                array_push($groupList, $teamPortfolio[$i]->team_main_id);
            }
        } else {
            $teamPortfolio = TeamPortfolio::all();
            for ($i = 0; $i < count($teamPortfolio); $i++) {
                array_push($groupList, $teamPortfolio[$i]->team_main_id);
            }
        }



        if (count($groupList) > 0) {


            $team = DB::table('team_main')
                ->leftJoin('loan_case', 'team_main.id', '=', 'loan_case.handle_group_id')
                ->select(array('team_main.*', DB::raw('COUNT(loan_case.handle_group_id) as task_count')))
                ->whereIn('team_main.id',  $groupList)
                ->groupBy('team_main.id')
                ->orderBy('task_count', 'ASC')
                ->get();


            $member = TeamMember::where('team_main_id', '=', $team[0]->id)->get();

            $memberList = [];

            for ($i = 0; $i < count($member); $i++) {
                array_push($memberList, $member[$i]->user_id);
            }

            // return $memberList; test

            if ($memberList) {

                $lawyer_id = '';
                $lawyer_nick = '';
                $clerk_id = '';
                $clerk_nick = '';

                $clerk = DB::table('users')
                    ->leftJoin('loan_case', 'users.id', '=', 'loan_case.clerk_id')
                    ->select(array('users.*', DB::raw('COUNT(loan_case.clerk_id) as task_count')))
                    ->where('menuroles', 'like', '%clerk%')
                    ->whereIn('users.id',  $memberList)
                    ->groupBy('users.id')
                    ->orderBy('task_count', 'ASC')
                    ->limit(1)
                    ->first();

                $lawyer = DB::table('users')
                    ->leftJoin('loan_case', 'users.id', '=', 'loan_case.lawyer_id')
                    ->select(array('users.*', DB::raw('COUNT(loan_case.lawyer_id) as task_count')))
                    ->where('menuroles', 'like', '%lawyer%')
                    ->whereIn('users.id',  $memberList)
                    ->groupBy('users.id')
                    ->orderBy('task_count', 'ASC')
                    ->limit(1)
                    ->first();

                if (!empty($lawyer)) {
                    $lawyer_id = $lawyer->id;
                    $lawyer_nick = $lawyer->nick_name;
                }

                if (!empty($clerk)) {
                    $clerk_id = $clerk->id;
                    $clerk_nick = $clerk->nick_name;
                }


                // return $lawyer;

                array_push($result,  ["team_id" => $team[0]->id, "lawyer_id" =>  $lawyer_id, "lawyer_nick" =>  $lawyer_nick,  "clerk_id" =>  $clerk_id,  "clerk_nick" =>  $clerk_nick]);
            }
        }


        return $result;


        $CaseTypeDB = CaseType::where('id', '=', $case_type)->get();




        if ($CaseTypeDB) {
            // 1. select out the group that have selected portfolio
            if ($CaseTypeDB[0]->is_bank_required == 1) {
                $group = GroupPortfolio::where('portfolio_id', '=', $bank)->where('type', '=', 'BANK')->get();
            } else {
                $group = GroupPortfolio::where('portfolio_id', '=', $case_type)->where('type', '=', 'CASE')->get();
            }

            // 2. filter out the group with lesser case
            if ($group) {
                $groupList = [];

                for ($i = 0; $i < count($group); $i++) {
                    array_push($groupList, $group[$i]->group_id);
                }



                if (count($groupList) > 0) {


                    $team = DB::table('team_main')
                        ->leftJoin('loan_case', 'team_main.id', '=', 'loan_case.handle_group_id')
                        ->select(array('team_main.*', DB::raw('COUNT(loan_case.handle_group_id) as task_count')))
                        ->whereIn('team_main.id',  $groupList)
                        ->groupBy('team_main.id')
                        ->orderBy('task_count', 'ASC')
                        ->get();

                    $member = TeamMember::where('team_main_id', '=', $team[0]->id)->get();

                    $memberList = [];

                    for ($i = 0; $i < count($member); $i++) {
                        array_push($memberList, $member[$i]->user_id);
                    }

                    if ($memberList) {
                        $clerk = DB::table('users')
                            ->leftJoin('loan_case', 'users.id', '=', 'loan_case.clerk_id')
                            ->select(array('users.*', DB::raw('COUNT(loan_case.clerk_id) as task_count')))
                            ->where('menuroles', 'like', '%clerk%')
                            ->whereIn('users.id',  $memberList)
                            ->groupBy('users.id')
                            ->orderBy('task_count', 'ASC')
                            ->limit(1)
                            ->get();

                        $lawyer = DB::table('users')
                            ->leftJoin('loan_case', 'users.id', '=', 'loan_case.lawyer_id')
                            ->select(array('users.*', DB::raw('COUNT(loan_case.lawyer_id) as task_count')))
                            ->where('menuroles', 'like', '%lawyer%')
                            ->whereIn('users.id',  $memberList)
                            ->groupBy('users.id')
                            ->orderBy('task_count', 'ASC')
                            ->limit(1)
                            ->get();


                        array_push($result,  ["team_id" => $team[0]->id, "lawyer_id" =>  $lawyer[0]->id, "lawyer_nick" =>  $lawyer[0]->nick_name,  "clerk_id" =>  $clerk[0]->id,  "clerk_nick" =>  $clerk[0]->nick_name]);
                    }
                }
            }
        }

        return $result;
    }

    public function createCustomer($request, $case_ref_no)
    {
        $customer = new Customer();

        if ($request->input('customer_type') == 1) {
            $existingCustomer = Customer::where('ic_no', '=', $request->input('client_ic'))->first();
            $customer->ic_no = $request->input('client_ic');
        } else {
            $existingCustomer = Customer::where('company_ref_no', '=', $request->input('company_reg_no'))->first();
            $customer->company_ref_no = $request->input('company_reg_no');
        }

        if ($existingCustomer) {
            $customer = $existingCustomer;
        } else {

            if ($request->input('client_email') == "") {
                $customer->email = '';
            }


            // $property_address
            // if ($request->input('property_address') == null) {
            //     $property_address = '';
            // } else {
            //     $property_address = $request->input('property_address');
            // }

            // if ($request->input('client_phone_no') ==)
            // $customer->case_ref_no = $case_ref_no;
            $customer->name = $request->input('client_name');
            $customer->phone_no = $request->input('client_phone_no');
            $customer->client_type = $request->input('customer_type');
            $customer->address = $request->input('address');

            $customer->race = $request->input('race');
            $customer->race_others = $request->input('client_race_others');
            $customer->email = $request->input('client_email');
            $customer->status = "1";
            $customer->created_at = now();

            $customer->save();
        }

        return $customer;
    }

    public function uploadDoc()
    {
        if (isset($_FILES['file']['name'])) {
            return 1;
        } else {
            return 2;
        }
    }

    // public function generateRunnungNo()
    // {
    //     $maxlength = 6;
    //     $extra_string = 0;
    //     $parameter = Parameter::where('parameter_type', '=', 'case_running_no')->get();

    //     $runnung_no = (int)$parameter[0]->parameter_value_1;

    //     $extra_string = $maxlength - strlen($runnung_no);

    //     $running_no = str_pad($runnung_no, $extra_string, '0', STR_PAD_LEFT);

    //     return $running_no;
    // }

    public  function round_up($value, $places)
    {
        $mult = pow(10, abs($places));
        return $places < 0 ?
            ceil($value / $mult) * $mult :
            ceil($value * $mult) / $mult;
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        // return $this->round_up(499001, -3);

        // return $this->getDocuments();
        $current_user = auth()->user();
        $role = $current_user->menuroles;

        if ($current_user->menuroles == 'lawyer' || $current_user->menuroles == 'clerk') {
            $case_check = DB::table('loan_case')
                ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
                ->leftJoin('client', 'client.id', '=', 'loan_case.customer_id')
                ->select(array('loan_case.*', 'case_type.name AS type_name', 'client.name AS client_name'))
                ->where($role . '_id', '=', $current_user->id)
                ->where('loan_case.id', '=', $id)
                ->where('loan_case.status', '!=', 99)
                ->first();

            if (!$case_check) {
                return redirect()->route('case.index');
            }
        } elseif ($current_user->menuroles == 'chambering') {
            $case_check = DB::table('loan_case')
                ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
                ->leftJoin('client', 'client.id', '=', 'loan_case.customer_id')
                ->select(array('loan_case.*', 'case_type.name AS type_name', 'client.name AS client_name'))
                // ->where('lawyer_id', '=', $current_user->id)
                ->where('clerk_id', '=', $current_user->id)
                ->orWhere('lawyer_id', '=', $current_user->id)
                ->where('loan_case.id', '=', $id)
                ->where('loan_case.status', '!=', 99)
                ->first();

            if (!$case_check) {
                return redirect()->route('case.index');
            }
        }



        $loanCase = LoanCase::where('id', '=', $id)->get();
        // $case = LoanCase::where('id', '=', $id)->first();

        $case = DB::table('loan_case as l')
            ->leftJoin('portfolio as p', 'l.bank_id', '=', 'p.id')
            ->select('l.*', 'p.name as portfolio')
            ->where('l.id', '=', $id)
            ->first();

        if ($case == null) {
            return redirect()->route('case.index');
        }

        $loanCaseDetails = DB::table('loan_case_checklist')
            ->leftJoin('users', 'loan_case_checklist.sales_user_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'loan_case_checklist.role')
            ->select('loan_case_checklist.*', 'users.name', 'roles.name AS role_name')
            ->where('case_id', '=', $id)
            ->get();

        $loanCaseCheckPoint = DB::table('loan_case_checklist')
            ->leftJoin('users', 'loan_case_checklist.sales_user_id', '=', 'users.id')
            ->leftJoin('roles', 'roles.id', '=', 'loan_case_checklist.role')
            ->select('loan_case_checklist.*', 'users.name', 'roles.name AS role_name')
            ->where('case_id', '=', $id)
            ->where('check_point', '!=', 0)
            ->get();

        $process_no_start = count($loanCaseDetails);

        for ($i = count($loanCaseCheckPoint) - 1; $i >= 0; $i--) {

            $process_no_end = $loanCaseCheckPoint[$i]->process_number;

            if ($i == 0) {
                $process_no_end = 1;

                $loanCaseCheckDetails = DB::table('loan_case_checklist')
                    ->leftJoin('users', 'loan_case_checklist.sales_user_id', '=', 'users.id')
                    ->leftJoin('roles', 'roles.id', '=', 'loan_case_checklist.role')
                    ->select('loan_case_checklist.*', 'users.name', 'roles.name AS role_name')
                    ->where('case_id', '=', $id)
                    ->whereBetween('process_number', array($process_no_end, $process_no_start))
                    ->get();
            } else {
                $process_no_end = ($loanCaseCheckPoint[$i - 1]->process_number);

                $loanCaseCheckDetails = DB::table('loan_case_checklist')
                    ->leftJoin('users', 'loan_case_checklist.sales_user_id', '=', 'users.id')
                    ->leftJoin('roles', 'roles.id', '=', 'loan_case_checklist.role')
                    ->select('loan_case_checklist.*', 'users.name', 'roles.name AS role_name')
                    ->where('case_id', '=', $id)
                    ->whereBetween('process_number', array($process_no_end + 1, $process_no_start))
                    ->get();
            }

            for ($j = 0; $j < count($loanCaseCheckDetails); $j++) {
                if ($loanCaseCheckDetails[$j]->need_attachment == 1) {
                    $loanCaseCheckFile = DB::table('loan_attachment')
                        ->select('*')
                        ->where('checklist_id', '=', $loanCaseCheckDetails[$j]->id)
                        ->get();

                    $loanCaseCheckDetails[$j]->files = $loanCaseCheckFile;
                }
            }

            $process_no_start = $process_no_end;
            $loanCaseCheckPoint[$i]->details = $loanCaseCheckDetails;
        }



        $loanCaseDetailsCount = LoanCaseDetails::where('case_id', '=', $id)->where('check_point', '>', 0)->get();
        $loanCaseNotes = LoanCaseNotes::where('case_id', '=', $id)->get();

        $caseMasterListCategory = CaseMasterListCategory::all();

        // $caseMasterListField = CaseMasterListField::all();

        $now = time(); // or your date as well
        $your_date = strtotime($loanCase[0]->created_at);
        $datediff = $now - $your_date;
        $datediff = ($datediff / (60 * 60 * 24));
        $datediff = number_format($datediff);

        $lawyer = Users::where('id', '=', $loanCase[0]->lawyer_id)->get();
        $clerk = Users::where('id', '=', $loanCase[0]->clerk_id)->get();
        $sales = Users::where('id', '=', $loanCase[0]->sales_user_id)->get();
        $caseTemplateCategories = CaseTemplateCategories::all();
        $caseTemplate = CaseTemplateMain::all();

        $loanCase[0]->lawyer = $lawyer[0]->name;

        if ($loanCase[0]->clerk_id != "0") {
            $loanCase[0]->clerk = $clerk[0]->name;
        } else {
            $loanCase[0]->clerk = "";
        }

        $loanCase[0]->sales = $sales[0]->name;

        // temporary use the tempalte
        $loan_accountDetails = AccountTemplateDetails::where('acc_main_template_id', '=', $id)->get();


        // $caseMasterListCategory = CaseMasterListCategory::all()->orderorderby;


        $caseMasterListCategory = CaseMasterListCategory::where('status', '=', 1)->orderBy('name', 'ASC')->get();

        // $caseMasterListField = CaseMasterListField::all();

        $caseMasterListField = DB::table('case_masterlist_field')
            ->leftJoin('loan_case_masterlist',  function ($join) use ($id) {
                $join->on('loan_case_masterlist.masterlist_field_id', '=', 'case_masterlist_field.id');
                $join->where('loan_case_masterlist.case_id', '=', $id);
            })
            ->select('case_masterlist_field.*', 'loan_case_masterlist.value')
            ->orderBy('case_masterlist_field.id')
            // ->where('case_id', '=', $id)
            ->get();

        // get PIC
        $lawyer = Users::where('id', '=', $loanCase[0]->lawyer_id)->first();
        $clerk = Users::where('id', '=', $loanCase[0]->clerk_id)->first();
        $sales = Users::where('id', '=', $loanCase[0]->sales_user_id)->first();




        // return $loanCaseDetails;

        // account details template temp

        $account_template = AccountTemplateMain::where('id', '=', 1)->get();
        $account_template_details = AccountTemplateDetails::where('acc_main_template_id', '=', $id)->get();

        $account_template_cat = DB::table('loan_case_account')
            ->join('account_category', 'loan_case_account.account_cat_id', '=', 'account_category.id')
            ->select('account_category.id', 'account_category.category', 'taxable', 'percentage')
            ->distinct()
            ->groupBy('loan_case_account.id')
            ->where('loan_case_account.case_id', '=', $id)
            ->get();

        $joinData = array();

        for ($i = 0; $i < count($account_template_cat); $i++) {

            $account_template_details_by_cat = LoanCaseAccount::where('case_id', '=', $id)
                ->where('account_cat_id', '=', $account_template_cat[$i]->id)
                ->get();
            array_push($joinData,  array('category' => $account_template_cat[$i], 'account_details' => $account_template_details_by_cat));
        }


        // get courier list
        $couriers = Courier::all();

        $loan_dispatch = LoanCaseDispatch::where('case_id', '=', $id)->get();

        $loan_dispatch = DB::table('loan_case_dispatch')
            ->leftJoin('courier', 'loan_case_dispatch.courier_id', '=', 'courier.id')
            ->select('loan_case_dispatch.*', 'courier.name AS courier_name')
            ->where('case_id', '=', $id)
            ->get();

        // get ledger
        $transactions = DB::table('transaction as t')
            ->leftJoin('loan_case_bill_details as lb', 'lb.id', '=', 't.account_details_id')
            ->leftJoin('loan_case_bill_main as mb', 'mb.id', '=', 'lb.loan_case_main_bill_id')
            ->leftJoin('account as a', 'a.id', '=', 'lb.account_item_id')
            ->leftJoin('banks as b', 'b.id', '=', 't.bank_id')
            ->select('t.*', 'a.name', 'b.name as bank_name', 'mb.bill_no')
            ->where('t.case_id', '=', $id)
            ->get();

        // $transactions = DB::table('transaction as t')
        // ->leftJoin('loan_case_bill_details as lb', 'lb.id', '=', 't.account_details_id')
        // ->leftJoin('loan_case_bill_main as mb', 'mb.id', '=', 'lb.loan_case_main_bill_id')
        // ->join('voucher_details as vd', 'vd.account_details_id', '=', 't.account_details_id')
        // ->leftJoin('account as a', 'a.id', '=', 'lb.account_item_id')
        // ->leftJoin('banks as b', 'b.id', '=', 't.bank_id')
        // // ->select('t.*', 'a.name', 'b.name as bank_name', 'vd.voucher_no', 'vd.voucher_no')
        // ->select('t.*', 'lb.*', 'b.name as bank_name', 'vd.voucher_no', 'vd.voucher_no')
        // ->where('t.case_id', '=', $id)
        // ->get();

        // return $transactions;

        // get parameter
        $parameter_controller = new ParameterController;
        $parameters = $parameter_controller->getParameter('payment_type');

        // get client data
        $customer = Customer::where('id', '=', $loanCase[0]->customer_id)->first();
        $referral = Referral::where('id', '=', $loanCase[0]->refferal_id)->first();

        // get bank list
        $banks = Banks::where('status', '=', 1)->get();

        // get ledger
        $loan_case_trust = DB::table('loan_case_trust as t')
            //  ->leftJoin('account as a', 'a.id', '=', 't.account_details_id')
            ->leftJoin('banks as b', 'b.id', '=', 't.bank_id')
            ->select('t.*', 'b.name as bank_name')
            ->where('t.case_id', '=', $id)
            ->where('t.status', '=', 1)
            ->get();

        $loan_case_trust_main_dis = DB::table('loan_case_trust as t')
            //  ->leftJoin('account as a', 'a.id', '=', 't.account_details_id')
            ->leftJoin('banks as b', 'b.id', '=', 't.bank_id')
            ->leftJoin('voucher_main as v', 'v.item_code', '=', 't.id')
            ->select('t.*', 'b.name as bank_name',  'v.id as voucher_id', 'v.voucher_no as voucher_no', 'v.lawyer_approval', 'v.account_approval')
            ->where('t.case_id', '=', $id)
            ->where('t.movement_type', '=', 2)
            ->where('t.status', '<>', 99)
            ->get();

        $loan_case_trust_main_receive = DB::table('loan_case_trust as t')
            //  ->leftJoin('account as a', 'a.id', '=', 't.account_details_id')
            ->leftJoin('banks as b', 'b.id', '=', 't.bank_id')
            ->select('t.*', 'b.name as bank_name')
            ->where('t.case_id', '=', $id)
            ->where('t.movement_type', '=', 1)
            ->where('t.status', '<>', 99)
            ->get();

        $loan_case_trust_main_receive = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->select('v.*')
            ->where('v.case_id', '=', $id)
            ->where('v.voucher_type', '=', 3)
            ->where('v.status', '<>', 99)
            ->get();

        $loan_case_trust_main_dis = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->select('v.*')
            ->where('v.case_id', '=', $id)
            ->where('v.voucher_type', '=', 2)
            ->where('v.status', '<>', 99)
            ->get();

        // return $loan_case_trust_main_dis;

        // $loan_case_bill_account = DB::table('loan_case_bill_account as t')
        //     //  ->leftJoin('account as a', 'a.id', '=', 't.account_details_id')
        //     ->leftJoin('banks as b', 'b.id', '=', 't.bank_id')
        //     ->select('t.*', 'b.name as bank_name')
        //     ->where('t.case_id', '=', $id)
        //     ->where('t.movement_type', '=', 1)
        //     ->where('t.status', '<>', 99)
        //     ->get();

        //get voucher running no
        $voucher_running_no = Parameter::where('parameter_type', '=', 'voucher_running_no')->first();
        $voucher_running_no = (int)$voucher_running_no->parameter_value_1;


        $parameter = Parameter::where('parameter_type', '=', 'template_file_path')->first();
        $template_path = $parameter->parameter_value_1;

        $parameter = Parameter::where('parameter_type', '=', 'case_file_path')->first();
        $case_path = $parameter->parameter_value_1;


        $documentTemplateFile = DB::table('document_template_file_main AS m')
            ->leftJoin('document_template_file_details AS d', 'm.id', '=', 'd.document_template_file_main_id')
            ->select('m.*', 'd.file_name')
            ->where('d.status', '=',  '1')
            ->get();


        $loan_case_files = DB::table('loan_case_files')
            ->select('loan_case_files.*')
            ->where('case_id', '=', $id)
            ->get();

        $case_file_count = count($loan_case_files) % 20;

        $loan_case_checklist_main = DB::table('loan_case_checklist_main')
            ->select('loan_case_checklist_main.*')
            ->where('case_id', '=', $id)
            ->get();

        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();

        // $loan_case_files =  Datatables::table('loan_case_files')
        // ->select('loan_case_files.*')
        // ->where('case_id', '=', $id)
        // ->make(true);

        // $loan_case_file =  datatables()->of(DB::table('loan_case_files')
        // ->select('loan_case_files.*')
        // ->where('case_id', '=', $id)
        // ->get())->toJson();

        // return $loan_case_file;


        // get latest checklist
        for ($i = 0; $i < count($loan_case_checklist_main); $i++) {

            $loanCaseChecklistDetails = DB::table('loan_case_checklist_details as d')
                ->leftJoin('users', 'd.pic_id', '=', 'users.id')
                ->leftJoin('roles', 'roles.id', '=', 'd.roles')
                ->select('d.*', 'users.name as user_name', 'roles.name AS role_name')
                ->where('loan_case_main_id', '=', $loan_case_checklist_main[$i]->id)
                ->get();

            for ($j = 0; $j < count($loanCaseChecklistDetails); $j++) {
                if ($loanCaseChecklistDetails[$j]->need_attachment == 1) {
                    $loanCaseCheckFile = DB::table('loan_attachment')
                        ->select('*')
                        ->where('checklist_id', '=', $loanCaseChecklistDetails[$j]->id)
                        ->get();

                    $loanCaseChecklistDetails[$j]->files = $loanCaseCheckFile;
                }
            }

            $loan_case_checklist_main[$i]->details = $loanCaseChecklistDetails;
        }


        // get log activity
        $activityLog = DB::table('activity_log AS a')
            ->leftJoin('users AS u', 'u.id', '=', 'a.user_id')
            ->leftJoin('loan_case AS l', 'l.id', '=', 'a.case_id')
            ->select('a.*', 'u.name AS user_name', 'l.case_ref_no')
            ->where('a.case_id', '=',  $id)
            ->get();

        // $transactions = DB::table('transaction as t')
        // ->leftJoin('loan_case_trust as ct', 'ct.id', '=', 't.account_details_id')
        // ->leftJoin('account_item as a', 'a.id', '=', 'lb.account_item_id')
        // ->leftJoin('banks as b', 'b.id', '=', 't.bank_id')
        // ->select('t.*', 'ct.item_name as name', 'b.name as bank_name', DB::raw('null as voucher_no'), DB::raw('null as voucher_id'))
        // ->where('t.case_id', '=', $id)
        // ->where('t.status', '<>', 99)
        // ->get();

        // return $transactions;

        $quotation_template = QuotationTemplateMain::where('status', '=', 1)->get();

        $loanCaseBillMain = LoanCaseBillMain::where('case_id', '=', $id)->where('status', '=', 1)->get();

        $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $id)->first();

        $fileFolder = DocumentTemplateFileFolder::where('status', '=', 1)->get();

        // $LoanCaseTrustMain = LoanCaseNotes::where('case_id', '=', $id)->first();

        $documentTemplateFilev2 = DB::table('document_template_file_main AS m')
            ->select('m.*')
            ->where('m.status', '=',  '1')
            ->orderBy('m.name', 'ASC')
            ->get();

        $LoanCaseNotes = DB::table('loan_case_notes AS n')
            ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
            ->select('n.*', 'u.name as user_name')
            ->where('n.case_id', '=',  $id)
            ->where('n.role', 'like',  '%' . $role . '%')
            ->orderBy('n.created_at', 'ASC')
            ->get();

            $LoanCaseKIVNotes = DB::table('loan_case_kiv_notes AS n')
            ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
            ->select('n.*', 'u.name as user_name')
            ->where('n.case_id', '=',  $id)
            ->where('n.role', 'like',  '%' . $role . '%')
            ->orderBy('n.created_at', 'ASC')
            ->get();

        $LoanAttachment = LoanAttachment::where('case_id', '=', $id)->where('checklist_id', '=', 0)->get();

        $LoanAttachment = DB::table('loan_attachment AS a')
            ->leftJoin('users AS u', 'u.id', '=', 'a.user_id')
            ->select('a.*', 'u.name as user_name')
            ->where('a.case_id', '=',  $id)
            ->where('a.checklist_id', '=',)
            ->get();

        $referrals = DB::table('referral as r')
            ->leftJoin('banks as b', 'b.id', '=', 'r.bank_id')
            ->select('r.*', 'b.name as bank_name')
            ->where('r.status', '=', 1)
            ->get();

        // return $LoanCaseNotes;

        return view('dashboard.case.show', [
            'cases' => $loanCase,
            'case' => $case,
            'cases_details' => $loanCaseDetails,
            'cases_notes' => $loanCaseNotes,
            'referrals' => $referrals,
            'caseTemplate' => $caseTemplate,
            'caseTemplateCategories' => $caseTemplateCategories,
            'current_user' => $current_user,
            'caseMasterListCategory' => $caseMasterListCategory,
            'caseMasterListField' => $caseMasterListField,
            'datediff' => $datediff,
            'loan_accountDetails' => $loan_accountDetails,
            'loanCaseDetailsCount' => $loanCaseDetailsCount,
            'loanCaseCheckPoint' => $loanCaseCheckPoint,
            'couriers' => $couriers,
            'loan_dispatch' => $loan_dispatch,
            'customer' => $customer,
            'referral' => $referral,
            'transactions' => $transactions,
            'parameters' => $parameters,
            'voucher_running_no' => $voucher_running_no,
            'banks' => $banks,
            'sales' => $sales,
            'lawyer' => $lawyer,
            'clerk' => $clerk,
            'role' => $role,
            'fileFolder' => $fileFolder,
            'loan_case_trust' => $loan_case_trust,
            'loan_case_trust_main_dis' => $loan_case_trust_main_dis,
            'loan_case_trust_main_receive' => $loan_case_trust_main_receive,
            'account_template' => $account_template,
            'loanCaseBillMain' => $loanCaseBillMain,
            'quotation_template' => $quotation_template,
            'documentTemplateFile' => $documentTemplateFile,
            'template_path' => $template_path,
            'case_path' => $case_path,
            'activityLog' => $activityLog,
            'loan_case_checklist_main' => $loan_case_checklist_main,
            'loan_case_files' => $loan_case_files,
            'account_template_with_cat' => $joinData,
            'OfficeBankAccount' => $OfficeBankAccount,
            'LoanCaseTrustMain' => $LoanCaseTrustMain,
            'documentTemplateFilev2' => $documentTemplateFilev2,
            'LoanCaseNotes' => $LoanCaseNotes,
            'LoanCaseKIVNotes' => $LoanCaseKIVNotes,
            'LoanAttachment' => $LoanAttachment,
            'case_file_count' => $case_file_count
        ]);
    }


    public function getCaseFile(Request $request, $id)
    {
        if ($request->ajax()) {

            $parameter = Parameter::where('parameter_type', '=', 'case_file_path')->first();
            $case_path = $parameter->parameter_value_1;

            $loan_case_files = DB::table('loan_case_files')
                ->select('loan_case_files.*')
                ->where('case_id', '=', $id)
                ->get();

            return DataTables::of($loan_case_files)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($id, $case_path) {
                    $actionBtn = ' <a target="_blank" href="/' . $case_path . 'file_case_' . $id . '/' . $row->name . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Download"><i class="cil-cloud-download"></i></a>
                    <a href="javascript:void(0)" onclick="deleteFile(' . $row->id . ')" class="btn btn-danger"><i class="cil-x"></i></a>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function getLedger(Request $request, $id)
    {
        if ($request->ajax()) {

            $transactions = DB::table('transaction as t')
                ->leftJoin('loan_case_bill_details as lb', 'lb.id', '=', 't.account_details_id')
                ->leftJoin('loan_case_bill_main as mb', 'mb.id', '=', 'lb.loan_case_main_bill_id')
                ->join('voucher_details as vd', 'vd.account_details_id', '=', 't.account_details_id')
                ->join('voucher_main as vm', 'vm.id', '=', 'vd.voucher_main_id')
                ->leftJoin('account_item as a', 'a.id', '=', 'lb.account_item_id')
                ->leftJoin('banks as b', 'b.id', '=', 't.bank_id')
                ->select('t.*', 'a.name', 'b.name as bank_name', 'vd.voucher_no', 'vd.voucher_main_id as voucher_id')
                ->where('t.case_id', '=', $id)
                ->where('t.status', '<>', 99)
                ->where('vm.lawyer_approval', '=', 1)
                ->where('vm.account_approval', '=', 1)
                ->get();

            $transactions = DB::table('voucher_main as m')
                ->join('voucher_details as vd', 'm.id', '=', 'vd.voucher_main_id')
                ->leftJoin('loan_case_bill_details as lb', 'lb.id', '=', 'vd.account_details_id')
                ->leftJoin('account_item as a', 'a.id', '=', 'lb.account_item_id')
                ->select('m.*', 'a.name', 'vd.voucher_no', 'vd.voucher_main_id as voucher_id')
                ->where('m.case_id', '=', $id)
                ->where('m.status', '<>', 99)
                ->where('vd.status', '<>', 99)
                ->where('m.lawyer_approval', '=', 1)
                ->where('m.account_approval', '=', 1)
                ->get();

            $transactions = DB::table('voucher_details as d')
                ->leftJoin('voucher_main as m', 'm.id', '=', 'd.voucher_main_id')
                // ->leftJoin('loan_case_bill_details as lb', 'lb.id', '=', 'd.account_details_id')
                // ->leftJoin('account_item as a', 'a.id', '=', 'lb.account_item_id')
                // ->select('m.*', 'a.name', 'vd.voucher_no', 'vd.voucher_main_id as voucher_id')
                ->select('m.*', 'a.name as account_name', 'm.id as voucher_id')
                ->select('m.*', 'm.id as voucher_id', 'd.amount')
                ->where('m.case_id', '=', $id)
                // ->where('m.lawyer_approval', '=', 1)
                ->where('m.account_approval', '=', 1)
                ->where('m.status', '<>', 99)
                // ->where('m.status', '<>', 4)
                ->get();

            $transactions = DB::table('voucher_main as m')
                // ->leftJoin('loan_case_bill_details as lb', 'lb.id', '=', 'd.account_details_id')
                // ->leftJoin('account_item as a', 'a.id', '=', 'lb.account_item_id')
                // ->select('m.*', 'a.name', 'vd.voucher_no', 'vd.voucher_main_id as voucher_id')
                ->select('m.*', 'a.name as account_name', 'm.id as voucher_id')
                ->select('m.*', 'm.id as voucher_id', 'm.total_amount as amount')
                ->where('m.case_id', '=', $id)
                // ->where('m.lawyer_approval', '=', 1)
                ->where('m.account_approval', '=', 1)
                ->where('m.status', '<>', 99)
                // ->where('m.status', '<>', 4)
                ->get();

            // return  $transactions;

            // $transactions = DB::table('transaction as t')
            // ->leftJoin('loan_case_trust as ct', 'ct.id', '=', 't.account_details_id')
            // ->leftJoin('account_item as a', 'a.id', '=', 'lb.account_item_id')
            // ->leftJoin('banks as b', 'b.id', '=', 't.bank_id')
            // ->select('t.*', 'ct.item_name as name', 'b.name as bank_name', '"" as voucher_no','"" as voucher_id')
            // ->where('t.case_id', '=', $id)
            // ->where('t.status', '<>', 99)
            // ->get();


            // ->leftJoin('loan_case_bill_details as lb', 'lb.id', '=', 't.account_details_id')
            // ->leftJoin('loan_case_bill_main as mb', 'mb.id', '=', 'lb.loan_case_main_bill_id')
            // ->leftJoin('account as a', 'a.id', '=', 'lb.account_item_id')
            // ->leftJoin('banks as b', 'b.id', '=', 't.bank_id')
            // ->select('t.*', 'a.name', 'b.name as bank_name', 'mb.bill_no')
            // ->where('t.case_id', '=', $id)

            return DataTables::of($transactions)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($id) {
                    $actionBtn = ' <a target="_blank" href="/" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Download"><i class="cil-cloud-download"></i></a>
                    <a href="javascript:void(0)" onclick="deleteFile(' . $row->id . ')" class="btn btn-danger"><i class="cil-x"></i></a>';
                    return $actionBtn;
                })
                ->addColumn('debit', function ($row) {
                    if ($row->voucher_type == '3' || $row->status == '4')
                        return $row->amount;
                    else
                        return '-';
                })
                ->addColumn('credit', function ($row) {
                    if (($row->voucher_type == '1' && $row->status != '4') || $row->voucher_type == '2')
                        return $row->amount;
                    else
                        return '-';
                })->addColumn('voucher_href', function ($data) {

                    if (($data->voucher_type == '1' && $data->status != '4') || $data->voucher_type == '2')
                        return '<a target="_blank" class="text-info" href="/voucher/' . $data->id . '/edit">' . $data->voucher_no . ' ></a>';
                    elseif ($data->voucher_type == '3' || $data->status == '4')
                        return '-';
                })
                ->addColumn('status1', function ($data) {
                    if ($data->status === '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->status === '1')
                        return '<span class="label bg-success">Approved</span>';
                    else
                        return '<span class="label bg-danger">Rejected</span>';
                })
                ->addColumn('type', function ($data) {
                    if (($data->voucher_type == '1' && $data->status != '4') || $data->status == '4')
                        return 'Bill';
                    elseif ($data->voucher_type == '3' || $data->voucher_type == '2')
                        return 'Trust';
                })
                ->editColumn('lawyer_approval', function ($data) {
                    if ($data->lawyer_approval == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->lawyer_approval == '1')
                        return '<span class="label bg-success">Approved</span>';
                    else
                        return '<span class="label bg-danger">Rejected</span>';
                })
                ->editColumn('account_approval', function ($data) {
                    if ($data->account_approval === '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->account_approval === '1')
                        return '<span class="label bg-success">Approved</span>';
                    else
                        return '<span class="label bg-danger">Rejected</span>';
                })
                // ->editColumn('type', function ($data) {
                //     if ($data->account_approval === '0')
                //         return '<span class="label bg-warning">Pending</span>';
                //     elseif ($data->account_approval === '1')
                //         return '<span class="label bg-success">Approved</span>';
                //     else
                //         return '<span class="label bg-danger">Rejected</span>';
                // })
                ->rawColumns(['action', 'debit', 'credit', 'status1', 'lawyer_approval', 'account_approval', 'voucher_href', 'type'])
                ->make(true);
        }
    }

    public function deleteFile($id)
    {

        $loanCaseFiles = LoanCaseFiles::where('id', '=', $id)->first();

        $case_id = $loanCaseFiles->case_id;
        $file_name = $loanCaseFiles->name;

        $parameter = Parameter::where('parameter_type', '=', 'case_file_path')->first();
        $case_path = $parameter->parameter_value_1;

        // return $templateDocumentDetails;

        $loanCaseFiles->delete();

        if (File::exists(public_path($case_path . 'file_case_' . $case_id . '/' . $file_name))) {
            File::delete(public_path($case_path . 'file_case_' . $case_id . '/' . $file_name));
        }


        return response()->json(['status' => 1, 'message' => 'Deleted the file']);
    }

    public function deleteMarketingBill($id)
    {

        $LoanAttachment = LoanAttachment::where('id', '=', $id)->first();

        $filename = $LoanAttachment->filename;

        $LoanAttachment->delete();

        if (File::exists(public_path($filename))) {
            File::delete(public_path($filename));
        }


        return response()->json(['status' => 1, 'message' => 'Deleted the file']);
    }

    public function genFile(Request $request)
    {
        // take case 1 as example
        $id = 1;

        $template_id =  $request->input('template_id');

        return $template_id;

        $documentTemplateFile = DocumentTemplateFileMain::where('id', '=', $template_id)->first();
        $case = DB::table('loan_case')->select('case_ref_no')->where('id', '=', $id)->first();

        $case_ref_no = $case->case_ref_no;

        // get file template path
        $templatePath =  $documentTemplateFile->path;

        // 1. Copy the file from templatepath $templatePath to public\documents\cases
        // 2. replace the content with laster list below and save

        $templatePath = public_path() . "/template/documents/SPA_with_Title_PV_010122.docx";

        $filename = basename($templatePath);
        $newSavePath = storage_path('app/documents/cases/S1_UWD_MBB_1_lll_RLI/' . $filename);

        // $newSavePath = storage_path('/template/documents/test.docx');

        // $templateWord = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        $templateWord = new TemplateProcessor($templatePath);


        // get masterlist template field join with case master list field
        $caseMasterListField = DB::table('case_masterlist_field')
            ->leftJoin('loan_case_masterlist',  function ($join) {
                $join->on('loan_case_masterlist.masterlist_field_id', '=', 'case_masterlist_field.id');
                // $join->where('loan_case_masterlist.case_id', '=', 1);
            })
            ->where('case_id', '=', $id)
            ->get();
        //[xxxx] => ${xxxx} -> need to convert in existing doc template file
        $case = DB::table('loan_case')->select('case_ref_no')->where('id', '=', $id)->first();

        for ($j = 0; $j < count($caseMasterListField); $j++) {
            $templateWord->setValue($caseMasterListField[$j]->code, $caseMasterListField[$j]->value);
        }


        $templateWord->setValue('case_ref_no', $case_ref_no);
        $templateWord->setValue('File_Ref', $case_ref_no);


        $templateWord->saveAs($newSavePath);
        echo $templatePath;
    }

    public function generateFilesFromTemplate(Request $request, $id)
    {
        $status = 1;
        $message = '';
        $fileTemplateId = $request->input('template_id');
        $array = (array) $fileTemplateId;

        $parameter = Parameter::where('parameter_type', '=', 'template_file_path')->first();
        $template_path = $parameter->parameter_value_1;

        $parameter = Parameter::where('parameter_type', '=', 'case_file_path')->first();
        $case_path = $parameter->parameter_value_1;

        // get loan caseref no
        $loanCase = LoanCase::where('id', '=', $id)->first();
        $case_ref_no = $loanCase->case_ref_no;

        $lawyer = Users::where('id', '=', $loanCase->lawyer_id)->first();
        $clerk = Users::where('id', '=', $loanCase->clerk_id)->first();

        $array = explode(",", $fileTemplateId);


        // $mainTemplateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($template_path . 'header/cover_1.docx');

        // $innerTemplateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($template_path . 'file_template_41/testtest.docx');

        // $innerXml = $innerTemplateProcessor->gettempDocumentMainPart();
        // $innerXml = preg_replace('/^[\s\S]*<w:body>(.*)<\/w:body>.*/', '$1', $innerXml);

        // $innerXml = preg_replace('/<w:sectPr>.*<\/w:sectPr>/', '', $innerXml);

        // $mainXml = $mainTemplateProcessor->gettempDocumentMainPart();
        // $mainXml = preg_replace('/<\/w:body>/', '<w:p><w:r><w:br w:type="page" /><w:lastRenderedPageBreak/></w:r></w:p>' . $innerXml . '</w:body>', $mainXml);
        // $mainTemplateProcessor->settempDocumentMainPart($mainXml);

        // $mainTemplateProcessor->saveAs($template_path . 'header/test.docx');


        // return "test";


        if (count($array) > 0) {
            for ($i = 0; $i < count($array); $i++) {

                $file_folder_name_temp = $case_path . 'file_case_' . $id;
                $file_folder_name_public = public_path($file_folder_name_temp);

                // $templatePath = public_path() . "/app/documents/templates/file_template_'.$id.'/SPA_with_Title_PV_010122.docx";
                // $filename = basename($templatePath);
                // $newSavePath = public_path('app/documents/cases/S1_UWD_MBB_1_lll_RLI/' . $filename);

                // $templateWord = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

                if (!File::isDirectory($file_folder_name_public)) {
                    File::makeDirectory($file_folder_name_public, 0777, true, true);
                }

                $documentTemplateFile = DB::table('document_template_file_main AS m')
                    ->leftJoin('document_template_file_details AS d', 'm.id', '=', 'd.document_template_file_main_id')
                    ->select('m.*', 'd.file_name')
                    ->where('m.id', '=',  $array[$i])
                    ->where('d.status', '=',  '1')
                    ->first();

                $genFileName = time() . '_' . str_replace(" ", "_", $documentTemplateFile->name) . '.docx';

                $template_folder_name_temp = $template_path . 'file_template_' . $array[$i] . '/' . $documentTemplateFile->file_name;
                $file_folder_name_temp = $case_path . 'file_case_' . $id . '/' . $genFileName;


                // $templateWord = new \PhpOffice\PhpWord\TemplateProcessor($template_folder_name_temp);
                $templateWord = new TemplateProcessor($template_folder_name_temp);

                $caseMasterListField = DB::table('case_masterlist_field')
                    ->leftJoin('loan_case_masterlist',  function ($join) {
                        $join->on('loan_case_masterlist.masterlist_field_id', '=', 'case_masterlist_field.id');
                        // $join->where('loan_case_masterlist.case_id', '=', 1);
                    })
                    ->where('case_id', '=', $id)
                    ->get();


                $caseMasterListField = DB::table('case_masterlist_field AS m')
                    ->join('loan_case_masterlist AS d', 'm.id', '=', 'd.masterlist_field_id')
                    ->where('case_id', '=', $id)
                    ->get();

                $caseMasterListField = DB::table('case_masterlist_field')
                    ->leftJoin('loan_case_masterlist',  function ($join) use ($id) {
                        $join->on('loan_case_masterlist.masterlist_field_id', '=', 'case_masterlist_field.id');
                        $join->where('loan_case_masterlist.case_id', '=', $id);
                    })
                    ->select('case_masterlist_field.*', 'loan_case_masterlist.value')
                    ->orderBy('case_masterlist_field.id')
                    // ->where('case_id', '=', $id)
                    ->get();

                // return $caseMasterListField;

                $case = DB::table('loan_case')->select('case_ref_no')->where('id', '=', $id)->first();



                for ($j = 0; $j < count($caseMasterListField); $j++) {


                    if ($caseMasterListField[$j]->value == null) {
                        $templateWord->setValue($caseMasterListField[$j]->code, '-');
                    } else {
                        $templateWord->setValue($caseMasterListField[$j]->code, htmlspecialchars($caseMasterListField[$j]->value));
                    }

                    if (strtoupper($caseMasterListField[$j]->code) == null) {
                        $templateWord->setValue(strtoupper($caseMasterListField[$j]->code), '-');
                    } else {
                        $templateWord->setValue(strtoupper($caseMasterListField[$j]->code), htmlspecialchars($caseMasterListField[$j]->value));
                    }
                }

                $templateWord->setValue('case_ref_no', $case_ref_no);
                $templateWord->setValue('file_ref', $case_ref_no);
                $templateWord->setValue('bc_no', $loanCase->bc_no);
                $templateWord->setValue('current_date', date('Y-m-d'));

                if ($lawyer->ic_name != null && $lawyer->ic_name != '') {
                    $templateWord->setValue('lawyer', $lawyer->ic_name);
                } else {
                    $templateWord->setValue('lawyer', $lawyer->name);
                }


                ob_clean();
                $templateWord->saveAs($file_folder_name_temp);
                // chmod($file_folder_name_temp, 0644);
                // exit;
                $loanCaseFile = new LoanCaseFiles();

                $loanCaseFile->case_id = $id;
                $loanCaseFile->name = $genFileName;
                $loanCaseFile->path = $documentTemplateFile->file_name;
                $loanCaseFile->type = 1;
                $loanCaseFile->status = 1;
                $loanCaseFile->created_at = date('Y-m-d H:i:s');

                $loanCaseFile->save();
            }
        }

        return response()->json(['status' => $status, 'data' => $message]);
    }

    public function getBillTemplate($template_id)
    {
        // $accTemplateDetail = AccountTemplateDetails::where('acc_template_main_id', '=', $template_id)->get();

        $account_template = AccountTemplateMain::where('id', '=', $template_id)->get();
        $account_template_details = AccountTemplateDetails::where('acc_main_template_id', '=', $template_id)->get();

        $account_template_cat = DB::table('loan_case_account')
            ->join('account_category', 'loan_case_account.account_cat_id', '=', 'account_category.id')
            ->select('account_category.id', 'account_category.category', 'taxable', 'percentage')
            ->distinct()
            ->groupBy('loan_case_account.id')
            ->where('loan_case_account.case_id', '=', 1)
            ->get();

        $joinData = array();

        for ($i = 0; $i < count($account_template_cat); $i++) {

            // $account_template_details_by_cat = LoanCaseAccount::where('case_id', '=', $id)
            //     ->where('account_cat_id', '=', $account_template_cat[$i]->id)
            //     ->get();
            array_push($joinData,  array('category' => $account_template_cat[$i], 'account_details' => $account_template_cat));
        }
        return $joinData;
    }

    public function document($id, $id2)
    {
        $docTemplatePages = DB::table('document_template_pages')
            ->leftJoin('users', 'users.id', '=', 'document_template_pages.is_locked')
            ->select('document_template_pages.*', 'users.name')
            ->where('document_template_details_id', '=', $id)
            ->get();

        $caseMasterListField = DB::table('case_masterlist_field')
            ->leftJoin('loan_case_masterlist',  function ($join) {
                $join->on('loan_case_masterlist.masterlist_field_id', '=', 'case_masterlist_field.id');
                // $join->where('loan_case_masterlist.case_id', '=', 1);
            })
            ->where('case_id', '=', $id)
            ->get();

        $case = DB::table('loan_case')->select('case_ref_no')->where('id', '=', $id)->first();




        for ($i = 0; $i < count($docTemplatePages); $i++) {
            // $docTemplatePages[$i]->content =   str_replace("[purchaser_name]", "Mr Peter", $docTemplatePages[$i]->content);
            for ($j = 0; $j < count($caseMasterListField); $j++) {
                $docTemplatePages[$i]->content =   str_replace("[" . $caseMasterListField[$j]->code . "]", $caseMasterListField[$j]->value, $docTemplatePages[$i]->content);;
            }

            $docTemplatePages[$i]->content =   str_replace("[case_ref_no]", $case->case_ref_no, $docTemplatePages[$i]->content);;
        }


        return view('dashboard.case.document', [
            'docTemplatePages' => $docTemplatePages
        ]);
    }

    public function saveDocumentAsVersion($request)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('dashboard.todolist.edit', [
            'lang' => MenuLangList::where('id', '=', $id)->first()
        ]);
    }

    public function acceptCase(Request $request, $id)
    {
        $status = 1;
        $message = 'Accepted the case';

        try {
            $template_id =  $request->input('template');
            $case = LoanCase::where('id', '=', $id)->first();

            $lawyer_id = $case->lawyer_id;
            $clerk_id = $case->clerk_id;
            $sales_id = $case->sales_id;
            $account_id = $case->account_id;

            $loanCase = LoanCase::where('id', '=', $id)->first();

            $openfile = 0;

            $caseTemplateSteps = DB::table('checklist_template_steps AS s')
                ->leftJoin('checklist_template_main_step_rel AS r', 'r.checklist_step_id', '=', 's.id')
                ->select('s.*', 'r.template_main_id')
                ->where('r.template_main_id', '=',  $template_id)
                ->get();

            $caseTemplateMain = CaseTemplateMain::where('id', '=', $template_id)->first();

            for ($i = 0; $i < count($caseTemplateSteps); $i++) {

                $loanCaseChecklistMain = new LoanCaseChecklistMain();

                $loanCaseChecklistMain->case_id = $id;
                $loanCaseChecklistMain->name =  $caseTemplateSteps[$i]->name;
                $loanCaseChecklistMain->status = 0;
                $loanCaseChecklistMain->created_at = date('Y-m-d H:i:s');

                $loanCaseChecklistMain->save();

                $caseTemplateItems = CaseTemplateItems::where('step_id', '=', $caseTemplateSteps[$i]->id)->get();

                if (count($caseTemplateItems) > 0) {
                    for ($j = 0; $j < count($caseTemplateItems); $j++) {

                        if ($caseTemplateItems[$j]->roles == "6") {
                            $pic_id = $sales_id;
                        } else if ($caseTemplateItems[$j]->roles == "7") {
                            $pic_id = $lawyer_id;
                        } else if ($caseTemplateItems[$j]->roles == "8") {
                            $pic_id = $clerk_id;
                        } else if ($caseTemplateItems[$j]->roles == "5") {
                            $pic_id = $account_id;
                        }

                        $loanCaseChecklistDetails = new LoanCaseChecklistDetails();

                        $loanCaseChecklistDetails->case_id = $id;
                        $loanCaseChecklistDetails->name =  $caseTemplateItems[$j]->name;
                        $loanCaseChecklistDetails->loan_case_main_id =  $loanCaseChecklistMain->id;
                        $loanCaseChecklistDetails->order =  $caseTemplateItems[$j]->order;
                        $loanCaseChecklistDetails->kpi = $caseTemplateItems[$j]->kpi;
                        $loanCaseChecklistDetails->roles = $caseTemplateItems[$j]->roles;
                        $loanCaseChecklistDetails->days = $caseTemplateItems[$j]->days;
                        $loanCaseChecklistDetails->start = $caseTemplateItems[$j]->start;
                        $loanCaseChecklistDetails->duration = $caseTemplateItems[$j]->duration;
                        $loanCaseChecklistDetails->need_attachment = $caseTemplateItems[$j]->need_attachment;
                        $loanCaseChecklistDetails->auto_dispatch = $caseTemplateItems[$j]->auto_dispatch;
                        $loanCaseChecklistDetails->auto_receipt = $caseTemplateItems[$j]->days;
                        $loanCaseChecklistDetails->close_case = $caseTemplateItems[$j]->close_case;
                        // $loanCaseChecklistDetails->target_close_date = $caseTemplateItems[$j]->close_case;
                        $loanCaseChecklistDetails->target_close_date = date('Y-m-d H:i:s', strtotime("+90 days"));
                        $loanCaseChecklistDetails->open_case = $caseTemplateItems[$j]->open_case;
                        $loanCaseChecklistDetails->pic_id = $pic_id;
                        $loanCaseChecklistDetails->email_template_id = $caseTemplateItems[$j]->email_template_id;
                        $loanCaseChecklistDetails->email_recurring = $caseTemplateItems[$j]->email_recurring;
                        $loanCaseChecklistDetails->remark = '';

                        if ($openfile == 0) {
                            $loanCaseChecklistDetails->status = 1;
                        } else {
                            $loanCaseChecklistDetails->status = 0;
                        }

                        $openfile += 1;


                        $loanCaseChecklistDetails->created_at = date('Y-m-d H:i:s');

                        $loanCaseChecklistDetails->save();
                    }
                }
            }

            $loanCase->status = 1;
            $loanCase->template_id = $template_id;
            $loanCase->case_accept_date = date('Y-m-d H:i:s');
            $loanCase->target_close_date = date('Y-m-d H:i:s', strtotime("+" . $caseTemplateMain->target_close_day . " days"));

            $loanCase->save();


            // save activity log
            $activityLog = [];

            $activityLog['action'] = 'Create';
            $activityLog['case_id'] = $id;
            $activityLog['checklist_id'] = 1;
            $activityLog['desc'] = 'Open case';

            $activity_controller = new ActivityLogController;
            $activity_controller->storeActivityLog($activityLog);
        } catch (\Throwable $e) {
            $status = 3;
            $message = $e;
        }

        return response()->json(['status' => $status, 'message' => $message]);
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
        $template_id =  $request->input('template');

        $case = LoanCase::where('id', '=', $id)->first();

        $lawyer_id = $case->lawyer_id;
        $clerk_id = $case->clerk_id;
        $sales_id = $case->sales_id;

        $CaseTemplate = CaseTemplate::where('id', '=', $template_id)->get();
        $caseTemplateDetail = CaseTemplateDetails::where('template_main_id', '=', $template_id)->get();

        for ($i = 0; $i < count($caseTemplateDetail); $i++) {

            $stop_date = date('Y-m-d H:i:s');

            $pic_id = 0;
            $status = 0;

            if ($i == 0) {
                $status = 1;
            }

            if ($caseTemplateDetail[$i]->role_id == "6") {
                $pic_id = $sales_id;
            } elseif ($caseTemplateDetail[$i]->role_id == "7") {
                $pic_id = $lawyer_id;
            } elseif ($caseTemplateDetail[$i]->role_id == "8") {
                $pic_id = $clerk_id;
            }

            $target_date = $this->getDuration($caseTemplateDetail[$i]->duration);

            DB::table('loan_case_checklist')->insert([
                'case_id' => $id,
                'process_number' => $caseTemplateDetail[$i]->process_number,
                'checklist_name' =>  $caseTemplateDetail[$i]->checklist_name,
                'target_date' => date('Y-m-d H:i:s', strtotime($stop_date . ' +' . $caseTemplateDetail[$i]->duration . ' day')),
                'target_close_date' => date('Y-m-d H:i:s'),
                'completion_date' => null,
                'need_attachment' => $caseTemplateDetail[$i]->need_attachment,
                'check_point' => $caseTemplateDetail[$i]->check_point,
                'kpi' => $caseTemplateDetail[$i]->kpi,
                'status' => $status,
                'is_checkbox' => 0,
                'sales_user_id' => $pic_id,
                'handle_group_id' => 0,
                'runner_user_id' => 0,
                'role' => $caseTemplateDetail[$i]->role_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null
            ]);
        }

        // $accTemplateDetail = AccountTemplateDetails::where('acc_template_main_id', '=', $template_id)->get();

        $case->status = 1;
        $case->target_close_date = date('Y-m-d H:i:s', strtotime("+" . $CaseTemplate[0]->target_close_day . " days"));
        // 'target_close_date' => date('Y-m-d H:i:s'),
        $case->save();

        // direct to show

        $request->session()->flash('message', 'Successfully accepted new case');

        return redirect()->route('case.index', ['cases' => TodoList::all()]);
    }

    public function updateCheckList(Request $request)
    {
        $status = 1;
        $type = 0;
        $remark = $request->input('remarks');
        $check_list_status = $request->input('check_list_status');
        // $loanCaseDetails = LoanCaseDetails::where('id', '=', $request->input('case_id_action'))->first();

        $loanCaseDetails = LoanCaseChecklistDetails::where('id', '=', $request->input('selected_id'))->first();

        $update_date = date('Y-m-d H:i:s');
        if ($loanCaseDetails) {
            $loanCaseDetails->remark = $remark;
            $loanCaseDetails->updated_at = $update_date;


            if ($loanCaseDetails->status == 1) {
                if ($check_list_status == 0) {
                    $type = 2;
                }
            } else {
                if ($check_list_status == 1) {
                    $type = 1;
                }
            }

            if ($check_list_status == 99) {
                $type = 0;
            }

            $loanCaseDetails->status = $check_list_status;
            $loanCaseDetails->save();

            $activityLog = [];

            $activityLog['action'] = 'update';
            $activityLog['case_id'] = $request->input('case_id_action');
            $activityLog['checklist_id'] = $request->input('selected_id');
            $activityLog['desc'] = $loanCaseDetails->name;

            $activity_controller = new ActivityLogController;
            $activity_controller->storeActivityLog($activityLog);

            if ($this->checkStep($loanCaseDetails->loan_case_main_id) == 0) {
                $loanCaseMain = LoanCaseChecklistMain::where('id', '=', $loanCaseDetails->loan_case_main_id)->first();
                $loanCaseMain->status = 1;

                $loanCaseMain->save();
            }

            $this->updatePercentage($request->input('case_id_action'));

            if ($type != 0) {
                $this->updateKPIScore($loanCaseDetails, $type, $request->input('case_id_action'));
            }
        }

        return response()->json(['status' => $status, 'data' => $update_date]);
    }

    public function updatePercentage($case_id)
    {
        $loanCaseDetailsAll = LoanCaseChecklistDetails::where('case_id', '=', $case_id)->get();
        $loanCaseDetailsDone = LoanCaseChecklistDetails::where('case_id', '=', $case_id)->where('status', '=', 1)->get();


        $loanCase = LoanCase::where('id', '=', $case_id)->first();

        $loanCase->percentage = (count($loanCaseDetailsDone) / count($loanCaseDetailsAll)) * 100;

        $loanCase->save();
    }

    public function closeCase($case_id, $checklist_id)
    {
        $status = 1;
        $message = '';

        $loanCaseDetails = LoanCaseChecklistDetails::where('case_id', '=', $case_id)
            ->where('id', '<>', $checklist_id)->where('status', '=', 0)->get();

        if (count($loanCaseDetails) == 0) {
            $loanCaseDetails = LoanCaseChecklistDetails::where('id', '=', $checklist_id)->first();
            $loanCaseDetails->status = 1;
            $loanCaseDetails->save();

            $loanCaseMain = LoanCaseChecklistMain::where('id', '=', $loanCaseDetails->loan_case_main_id)->first();
            $loanCaseMain->status = 1;
            $loanCaseMain->save();

            $loanCase = LoanCase::where('id', '=', $case_id)->first();
            $loanCase->percentage = 100;
            $loanCase->status = 0;
            $loanCase->save();

            $activityLog = [];

            $activityLog['action'] = 'Close';
            $activityLog['case_id'] = $case_id;
            $activityLog['checklist_id'] = $checklist_id;
            $activityLog['desc'] = $loanCaseDetails->name;

            $activity_controller = new ActivityLogController;
            $activity_controller->storeActivityLog($activityLog);

            $status = 1;
            $message = 'Case closed';
        } else {
            $status = 0;
            $message = 'Please make sure all checklist complete before close case';
        }

        return response()->json(['status' => $status, 'message' => $message]);
    }

    public function updateKPIScore($loanCaseDetails, $type, $case_id)
    {
        $date_now = date("Y-m-d");
        $blnOver = 0;
        $points = $loanCaseDetails->kpi;

        if ($date_now >= date('2022-02-17 20:56:13')) {
            if ($date_now > $loanCaseDetails->target_close_date) {
                $blnOver = 1;
            } else {
                $blnOver = 0;
            }
        }



        if ($type == 1) {
            if ($blnOver == 1) {
                $points = 0 - $points;
            }
        } else {
            $points = 0 - $points;
        }


        $userKpiHistory = new UserKpiHistory();
        $current_user = auth()->user();

        $user = User::where('id', '=', $current_user->id)->first();

        if ($user) {
            $user->kpi = (int)$user->kpi + $points;
            $user->save();
        }

        $userKpiHistory->type = $type;
        $userKpiHistory->point = $points;
        $userKpiHistory->case_id = $case_id;
        $userKpiHistory->user_id = $current_user->id;
        $userKpiHistory->status = 1;
        $userKpiHistory->created_at = date('Y-m-d H:i:s');
        $userKpiHistory->save();
    }

    public function checkStep($loanCaseMainId)
    {
        $loanCaseDetails = LoanCaseChecklistDetails::where('loan_case_main_id', '=', $loanCaseMainId)
            ->where('status', '=', 0)->get();

        return count($loanCaseDetails);
    }

    public function UploadFile(Request $request)
    {
        $status = 1;
        $data = '';
        $file = $request->file('case_file');


        $filename = time() . '_' . $file->getClientOriginalName();


        $current_user = auth()->user();

        // File extension
        $extension = $file->getClientOriginalExtension();
        $case_ref_no =  $request->input('case_ref_no');
        $file_type =  $request->input('file_type');

        // File upload location
        $case_ref_no = str_replace("/", "_", $case_ref_no);
        if ($file_type == 1) {
            // $location = 'documents/cases/' . $case_ref_no . '/';
            $location = 'documents/cases/' . $request->input('case_id') . '/';
        } else {
            // $location = 'documents/cases/' . $case_ref_no . '/marketing/';
            $location = 'documents/cases/' . $request->input('case_id') . '/marketing/';
        }


        // Upload file
        $file->move($location, $filename);

        // File path
        $filepath = url($location . $filename);

        $LoanAttachment = new LoanAttachment();



        $LoanAttachment->case_id =  $request->input('case_id');
        $LoanAttachment->checklist_id = $request->input('selected_id');
        $LoanAttachment->display_name = $file->getClientOriginalName();
        $LoanAttachment->filename = $location . $filename;
        $LoanAttachment->type = $extension;
        $LoanAttachment->remark = $request->input('remark');
        $LoanAttachment->user_id = $current_user->id;
        $LoanAttachment->status = 1;
        $LoanAttachment->created_at = date('Y-m-d H:i:s');
        $LoanAttachment->save();

        $activityLog = [];

        $activityLog['action'] = 'Upload';
        $activityLog['case_id'] = $request->input('case_id');
        $activityLog['checklist_id'] = $request->input('selected_id');
        $activityLog['desc'] = 'Upload file';

        $activity_controller = new ActivityLogController;
        $activity_controller->storeActivityLog($activityLog);

        return response()->json(['status' => $status, 'data' => $data]);
    }


    public function uploadMarketingBill(Request $request)
    {
        $status = 1;
        $data = '';
        $file = $request->file('marketing_bill_file');

        $filename = time() . '_' . $file->getClientOriginalName();


        $current_user = auth()->user();

        // File extension
        $extension = $file->getClientOriginalExtension();
        $case_ref_no =  $request->input('case_ref_no');

        // File upload location
        $case_ref_no = str_replace("/", "_", $case_ref_no);
        $location = 'documents/cases/' . $case_ref_no . '/marketing/';

        // Upload file
        $file->move($location, $filename);

        // File path
        $filepath = url($location . $filename);

        $LoanAttachment = new LoanAttachment();


        $LoanAttachment->case_id =  $request->input('case_id');
        $LoanAttachment->checklist_id = 0;
        $LoanAttachment->display_name = $file->getClientOriginalName();
        $LoanAttachment->filename = $location . $filename;
        $LoanAttachment->type = $extension;
        $LoanAttachment->user_id = $current_user->id;
        $LoanAttachment->status = 1;
        $LoanAttachment->created_at = date('Y-m-d H:i:s');
        $LoanAttachment->save();

        // $activityLog = [];

        // $activityLog['action'] = 'Upload';
        // $activityLog['case_id'] = $request->input('case_id');
        // $activityLog['checklist_id'] = $request->input('selected_id');
        // $activityLog['desc'] = 'Upload mak file';

        // $activity_controller = new ActivityLogController;
        // $activity_controller->storeActivityLog($activityLog);

        return response()->json(['status' => $status, 'data' => $data]);
    }

    public function getDocuments()
    {
        // $str=file_get_contents(resource_path('public/template/documents/SPA_with_Title_PV_010122.docx'));
        // $str = file_get_contents(resource_path('resources/template/documents/SPA_with_Title_PV_010122.docx'));
        $str = file_get_contents('public/template/documents/SPA_with_Title_PV_010122.docx');

        // File::copy(from_path, to_path);

        // $path = storage_path('template\documents\SPA_with_Title_PV_010122.docx');
        // Storage::delete(str_replace('storage/', 'public/', $category->image_path));

        // $str = file_get_contents(resource_path($path));

        // Storage::copy(
        //     storage_path('template/documents/SPA_with_Title_PV_010122.docx'),
        //     storage_path('template/documents/SPA_with_Title_PV_010122_v2.docx')
        // );

        // Storage::copy('/resources/template/documents/SPA_with_Title_PV_010122.docx','/resources/template/documents/SPA_with_Title_PV_010122_v2.docx');

        // $file = storage_path('/template/documents/SPA_with_Title_PV_010122.docx');
        // $destination = public_path('files/file.zip');
        // Storage::copy($file, $destination);

        // File::copy(public_path('exist/test.png'), public_path('copy/test_copy.png'));


        return $str;
    }

    public function getDuration($duration)
    {
        $target_date = date('Y-m-d H:i:s');
        $today_date = date('Y-m-d H:i:s');
        $target_date = date('Y-m-d H:i:s', strtotime($target_date . ' +' . $duration . ' day'));

        return $target_date;
    }

    public function createDispatch(Request $request)
    {
        $status = 1;
        $data = '';

        $loanCaseDispatch = new LoanCaseDispatch();

        $loanCaseDispatch->case_id =  $request->input('case_id_dispatch');
        $loanCaseDispatch->courier_id =  $request->input('courier_id');
        $loanCaseDispatch->package_name =  $request->input('package_name');
        $loanCaseDispatch->departure_address =  $request->input('departure_address');
        $loanCaseDispatch->destination_address =  $request->input('destination_address');
        $loanCaseDispatch->departure_time =  $request->input('departure_time');
        $loanCaseDispatch->delivered_time =  $request->input('delivered_time');
        $loanCaseDispatch->remark =  $request->input('remark');
        $loanCaseDispatch->Status =  $request->input('cl_delivery_status');
        $loanCaseDispatch->created_at = date('Y-m-d H:i:s');
        $loanCaseDispatch->save();

        return response()->json(['status' => $status, 'data' => $data]);
    }

    public function trustEntry(Request $request, $id)
    {
        $status = 1;
        $data = '';

        $current_user = auth()->user();

        $loanCaseTrust = new LoanCaseTrust();
        $loanCaseTrust->case_id =  $id;
        $loanCaseTrust->movement_type =  $request->input('payment_movement');

        if ($request->input('payment_movement') == 1) {
            $loanCaseTrust->transaction_type =  'D';
        } else {
            $loanCaseTrust->transaction_type =  'C';
        }


        $loanCaseTrust->payment_type =  $request->input('ddl_payment_type_trust');
        $loanCaseTrust->cheque_no =  $request->input('txt_cheque_no_trust');
        $loanCaseTrust->bank_id =  $request->input('txt_bank_name_trust');
        $loanCaseTrust->bank_account =  $request->input('txt_bank_account_trust');
        $loanCaseTrust->payment_date =  $request->input('voucher_payment_time_trust');
        $loanCaseTrust->item_name =  $request->input('payment_name');
        $loanCaseTrust->item_code =  $request->input('transaction_id');
        $loanCaseTrust->amount =  $request->input('payment_amt');
        $loanCaseTrust->office_account_id =  $request->input('OfficeBankAccount_id_trust');
        $loanCaseTrust->remark =  $request->input('payment_desc');
        $loanCaseTrust->status =  1;
        $loanCaseTrust->created_at = date('Y-m-d H:i:s');
        // $loanCaseTrust->save();

        $transaction = new Transaction();
        $loanCase = LoanCase::where('id', '=', $id)->first();

        $collected_trust  = 0;

        if ($request->input('payment_movement') == 1) {
            $collected_trust = (float)($loanCase->collected_trust) + (float)($request->input('payment_amt'));
            $total_trust = (float)($loanCase->total_trust) + (float)($request->input('payment_amt'));
        } else if ($request->input('payment_movement') == 2 || $request->input('payment_movement') == 3) {
            $collected_trust = (float)($loanCase->collected_trust) - (float)($request->input('payment_amt'));
            $total_trust = (float)($loanCase->total_trust) - (float)($request->input('payment_amt'));
        }

        $loanCase->collected_trust = $collected_trust;


        $loanCase->total_trust = $total_trust;
        $loanCase->updated_at = date('Y-m-d H:i:s');
        $loanCase->save();


        // get parameter (will move into parameter controller)
        $parameter = Parameter::where('parameter_type', '=', 'transaction_running_no')->first();

        $running_no = (int)$parameter->parameter_value_1 + 1;

        $parameter->parameter_value_1 = $running_no;
        $parameter->save();

        $transaction->transaction_id = $running_no;
        $transaction->case_id = $id;
        $transaction->user_id = $current_user->id;
        $transaction->account_details_id = $request->input('account_details_id');


        if ($request->input('payment_movement') == 1) {
            $transaction->transaction_type = 'D';
        } else if ($request->input('payment_movement') == 2 || $request->input('payment_movement') == 3) {
            $transaction->transaction_type = 'C';
        }


        $transaction->amount = $request->input('payment_amt');
        $transaction->cheque_no = '';
        $transaction->bank_id = 0;

        if ($request->input('payment_movement') == 1) {
            $transaction->remark = 'received';
        } else if ($request->input('payment_movement') == 2) {
            $transaction->remark = 'disbursement';
        } else if ($request->input('payment_movement') == 3) {
            $transaction->remark = 'Transfer to bill';
        }


        $transaction->status = 1;
        $transaction->created_at = date('Y-m-d H:i:s');
        $transaction->account_details_id = $loanCaseTrust->id;
        $transaction->save();

        if ($request->input('payment_movement') == 3) {
            $transaction = new Transaction();
            $loanCase = LoanCase::where('id', '=', $id)->first();

            $collected_bill = (float)($loanCase->collected_bill) + (float)($request->input('payment_amt'));
            $total_bill = (float)($loanCase->total_bill) + (float)($request->input('total_bill'));

            $loanCase->collected_bill = $collected_bill;
            $loanCase->total_bill = $total_bill;
            $loanCase->updated_at = date('Y-m-d H:i:s');
            $loanCase->save();


            // get parameter (will move into parameter controller)
            $parameter = Parameter::where('parameter_type', '=', 'transaction_running_no')->first();

            $running_no = (int)$parameter->parameter_value_1 + 1;

            $parameter->parameter_value_1 = $running_no;
            $parameter->save();

            $transaction->transaction_id = $running_no;
            $transaction->case_id = $id;
            $transaction->user_id = $current_user->id;
            $transaction->account_details_id = $request->input('account_details_id');
            $transaction->transaction_type = 'D';
            $transaction->amount = $request->input('payment_amt');
            $transaction->cheque_no = '';
            $transaction->bank_id = 0;
            $transaction->remark = 'Transfer from trust to bill';
            $transaction->status = 1;
            $transaction->created_at = date('Y-m-d H:i:s');
            $transaction->save();
        }

        return response()->json(['status' => $status, 'data' => $data]);
    }


    public function requestTrustDisbusement(Request $request, $id)
    {
        $status = 1;
        $data = '';

        $loanCase = LoanCase::where('id', '=', $id)->first();


        if ($loanCase->collected_trust <= 0) {
            return response()->json(['status' => 2, 'message' => 'No trust fund received yet']);
        }


        $current_user = auth()->user();

        $loanCaseTrust = new LoanCaseTrust();
        $loanCaseTrust->case_id =  $id;
        $loanCaseTrust->movement_type =  2;
        $loanCaseTrust->transaction_type =  'C';
        $loanCaseTrust->payment_type =  $request->input('payment_type');
        $loanCaseTrust->payment_date =  $request->input('payment_date');
        $loanCaseTrust->cheque_no =  $request->input('cheque_no');
        $loanCaseTrust->bank_id =  $request->input('bank_id');
        $loanCaseTrust->bank_account =  $request->input('bank_account');
        $loanCaseTrust->item_name =  $request->input('payee_name');
        // $loanCaseTrust->transaction_id =  $request->input('transaction_id');
        $loanCaseTrust->amount =  $request->input('amount');
        $loanCaseTrust->office_account_id =  $request->input('office_account_id');
        $loanCaseTrust->remark =  $request->input('payment_desc');
        $loanCaseTrust->status =  1;
        $loanCaseTrust->created_at = date('Y-m-d H:i:s');
        $loanCaseTrust->created_by = $current_user->id;
        $loanCaseTrust->save();

        $transaction = new Transaction();

        $collected_trust  = 0;

        $collected_trust = (float)($loanCase->collected_trust) - (float)($request->input('amount'));
        $total_trust = (float)($loanCase->total_trust) - (float)($request->input('amount'));

        $loanCase->collected_trust = $collected_trust;

        $loanCase->total_trust = $total_trust;
        $loanCase->updated_at = date('Y-m-d H:i:s');
        $loanCase->save();


        // get parameter (will move into parameter controller)
        $parameter = Parameter::where('parameter_type', '=', 'transaction_running_no')->first();
        $running_no = (int)$parameter->parameter_value_1 + 1;
        $parameter->parameter_value_1 = $running_no;
        $parameter->save();

        $transaction->transaction_id = $running_no;
        $transaction->case_id = $id;
        $transaction->user_id = $current_user->id;
        $transaction->account_details_id = $request->input('account_details_id');


        $transaction->transaction_type = 'C';

        $transaction->amount = $request->input('amount');
        $transaction->cheque_no = '';
        $transaction->bank_id = 0;
        $transaction->remark = 'disbursement';
        $transaction->status = 1;
        $transaction->created_at = date('Y-m-d H:i:s');
        $transaction->account_details_id = $loanCaseTrust->id;
        $transaction->save();

        // $this->updateLoanCaseTrustMain($request, $id);

        // check loantrustcasemain exist
        $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $id)->first();

        if ($LoanCaseTrustMain == null) {
            $LoanCaseTrustMain = new LoanCaseTrustMain();
            $LoanCaseTrustMain->case_id =  $id;
            $LoanCaseTrustMain->payment_type =  $request->input('payment_type');
            $LoanCaseTrustMain->payment_date =  $request->input('payment_date');
            $LoanCaseTrustMain->transaction_id =  $request->input('transaction_id');
            $LoanCaseTrustMain->office_account_id =  $request->input('office_account_id');
            $LoanCaseTrustMain->status =  1;
            $LoanCaseTrustMain->updated_by = $current_user->id;
            $LoanCaseTrustMain->updated_at = date('Y-m-d H:i:s');
            // $LoanCaseTrustMain->save();
        }

        // $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $id)->first();
        $LoanCaseTrustMain->total_used = (float)($LoanCaseTrustMain->total_used) + (float)($request->input('amount'));
        $LoanCaseTrustMain->save();

        // temporary add request voucher header_register_callback==================================================

        $Parameter = Parameter::where('parameter_type', '=', 'voucher_running_no')->first();
        $voucher_running_no = (int)$Parameter->parameter_value_1;

        $Parameter->parameter_value_1 = (int)$Parameter->parameter_value_1 + 1;
        $Parameter->save();

        $voucherMain = new VoucherMain();

        $voucherMain->user_id = $current_user->id;
        $voucherMain->case_id = $id;
        $voucherMain->payment_type = $request->input('payment_type');
        $voucherMain->voucher_no = $voucher_running_no;
        $voucherMain->cheque_no = $request->input('cheque_no');
        $voucherMain->credit_card_no = $request->input('credit_card_no');
        $voucherMain->bank_id = $request->input('bank_id');
        $voucherMain->payee = $request->input('payee_name');
        $voucherMain->created_by = $current_user->id;
        $voucherMain->transaction_id =  $request->input('transaction_id');
        $voucherMain->bank_account = $request->input('bank_account');
        $voucherMain->office_account_id = $request->input('office_account_id');
        $voucherMain->payment_date = $request->input('payment_date');
        $voucherMain->total_amount = $request->input('amount');
        $voucherMain->voucher_type = 2;
        $voucherMain->item_code = $loanCaseTrust->id;

        if ($current_user->menuroles == 'lawyer') {
            $voucherMain->lawyer_approval = 1;
            $voucherMain->lawyer_id = $current_user->id;
        }

        // if($current_user->menuroles == 'acccount')
        // {
        //     $voucherMain->accojunt_approval = 1;
        // }

        $voucherMain->status = 1;
        $voucherMain->created_at = date('Y-m-d H:i:s');
        $voucherMain->save();

        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;

        if ($userRoles == "lawyer") {

            // temporary auto approve voucher if lawyer sent
            $Notification  = new Notification();
            $Notification->name = $current_user->name;
            $Notification->desc = 'approved voucher ' . $voucher_running_no;
            $Notification->user_id = 0;
            $Notification->role = 'account|admin|management';
            $Notification->parameter1 = $id;
            $Notification->parameter2 = $voucherMain->id;
            $Notification->module = 'trust';
            $Notification->bln_read = 0;
            $Notification->status = 1;
            $Notification->created_at = now();
            $Notification->created_by = $current_user->id;
            $Notification->save();
        } else {
            $Notification  = new Notification();
            $Notification->name = $current_user->name;
            $Notification->desc = 'request voucher ' . $voucher_running_no;
            $Notification->user_id = $loanCase->lawyer_id;
            $Notification->role = '';
            $Notification->parameter1 = $id;
            $Notification->parameter2 = $voucherMain->id;
            $Notification->module = 'trust';
            $Notification->bln_read = 0;
            $Notification->status = 1;
            $Notification->created_at = now();
            $Notification->created_by = $current_user->id;
            $Notification->save();
        }



        $voucherDetails = new VoucherDetails();

        $voucherDetails->voucher_main_id = $voucherMain->id;
        $voucherDetails->user_id = $current_user->id;
        $voucherDetails->case_id = $id;
        $voucherDetails->account_details_id = $loanCaseTrust->id;
        $voucherDetails->amount = $request->input('amount');
        $voucherDetails->payment_type = $request->input('payment_type');
        $voucherDetails->voucher_no = $voucher_running_no;
        $voucherDetails->cheque_no = $request->input('cheque_no');
        $voucherDetails->credit_card_no = $request->input('credit_card_no');
        $voucherDetails->bank_id = $request->input('bank_id');
        $voucherDetails->bank_account = $request->input('bank_account');
        $voucherDetails->status = 1;
        $voucherDetails->created_at = date('Y-m-d H:i:s');
        $voucherDetails->save();

        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;


        if ($userRoles == "lawyer") {

            // temporary auto approve voucher if lawyer sent
            $Notification  = new Notification();
            $Notification->name = $current_user->name;
            $Notification->desc = 'approved voucher ' . $voucher_running_no;
            $Notification->user_id = 0;
            $Notification->role = 'account|admin|management';
            $Notification->parameter1 = $id;
            $Notification->parameter2 = $voucherMain->id;
            $Notification->module = 'voucher';
            $Notification->bln_read = 0;
            $Notification->status = 1;
            $Notification->created_at = now();
            $Notification->created_by = $current_user->id;
            $Notification->save();
        } else {
            $Notification  = new Notification();
            $Notification->name = $current_user->name;
            $Notification->desc = 'approved voucher ' . $voucher_running_no;
            $Notification->user_id = $loanCase->lawyer_id;
            $Notification->role = '';
            $Notification->parameter1 = $id;
            $Notification->parameter2 = $voucherMain->id;
            $Notification->module = 'voucher';
            $Notification->bln_read = 0;
            $Notification->status = 1;
            $Notification->created_at = now();
            $Notification->created_by = $current_user->id;
            $Notification->save();
        }

        return response()->json(['status' => $status, 'data' => $data]);
    }


    public function receiveTrustDisbusement(Request $request, $id)
    {
        $status = 1;
        $data = '';

        $current_user = auth()->user();

        $loanCaseTrust = new LoanCaseTrust();
        $loanCaseTrust->case_id =  $id;
        $loanCaseTrust->movement_type =  1;
        $loanCaseTrust->transaction_type =  'D';
        $loanCaseTrust->payment_type =  $request->input('payment_type');
        $loanCaseTrust->payment_date =  $request->input('payment_date');
        $loanCaseTrust->cheque_no =  $request->input('cheque_no');
        $loanCaseTrust->bank_id =  $request->input('bank_id');
        $loanCaseTrust->bank_account =  $request->input('bank_account');
        $loanCaseTrust->item_name =  $request->input('payee_name');
        $loanCaseTrust->item_code =  $request->input('transaction_id');
        $loanCaseTrust->amount =  $request->input('amount');
        $loanCaseTrust->office_account_id =  $request->input('office_account_id');
        $loanCaseTrust->remark =  $request->input('payment_desc');
        $loanCaseTrust->status =  1;
        $loanCaseTrust->created_at = date('Y-m-d H:i:s');
        $loanCaseTrust->created_by = $current_user->id;
        $loanCaseTrust->save();

        $transaction = new Transaction();
        $loanCase = LoanCase::where('id', '=', $id)->first();

        $collected_trust  = 0;

        $collected_trust = (float)($loanCase->collected_trust) + (float)($request->input('amount'));
        $total_trust = (float)($loanCase->total_trust) + (float)($request->input('amount'));

        $loanCase->collected_trust = $collected_trust;

        $loanCase->total_trust = $total_trust;
        $loanCase->updated_at = date('Y-m-d H:i:s');
        $loanCase->save();


        // get parameter (will move into parameter controller)
        $parameter = Parameter::where('parameter_type', '=', 'transaction_running_no')->first();
        $running_no = (int)$parameter->parameter_value_1 + 1;
        $parameter->parameter_value_1 = $running_no;
        $parameter->save();

        $transaction->transaction_id = $running_no;
        $transaction->case_id = $id;
        $transaction->user_id = $current_user->id;
        // $transaction->account_details_id = $request->input('account_details_id');


        $transaction->transaction_type = 'D';

        $transaction->amount = $request->input('amount');
        $transaction->cheque_no = '';
        $transaction->bank_id = 0;
        $transaction->remark = 'Received';
        $transaction->status = 1;
        $transaction->created_at = date('Y-m-d H:i:s');
        $transaction->account_details_id = $loanCaseTrust->id;
        $transaction->save();

        // $this->updateLoanCaseTrustMain($request, $id);

        // check loantrustcasemain exist
        $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $id)->first();

        if ($LoanCaseTrustMain == null) {
            $LoanCaseTrustMain = new LoanCaseTrustMain();
            $LoanCaseTrustMain->case_id =  $id;
            $LoanCaseTrustMain->transaction_id =  $request->input('transaction_id');
            $LoanCaseTrustMain->office_account_id =  $request->input('office_account_id');
            $LoanCaseTrustMain->status =  1;
            $LoanCaseTrustMain->updated_by = $current_user->id;
            $LoanCaseTrustMain->updated_at = date('Y-m-d H:i:s');
            // $LoanCaseTrustMain->save();
        }



        // $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $id)->first();
        $LoanCaseTrustMain->total_received = (float)($LoanCaseTrustMain->total_received) + (float)($request->input('amount'));
        $LoanCaseTrustMain->save();




        // temporary add request voucher header_register_callback==================================================

        $Parameter = Parameter::where('parameter_type', '=', 'voucher_running_no')->first();
        $voucher_running_no = (int)$Parameter->parameter_value_1;

        $Parameter->parameter_value_1 = (int)$Parameter->parameter_value_1 + 1;
        $Parameter->save();



        $voucherMain = new VoucherMain();


        $voucherMain->user_id = $current_user->id;
        $voucherMain->case_id = $id;
        $voucherMain->payment_type = $request->input('payment_type');
        $voucherMain->voucher_no = $voucher_running_no;
        $voucherMain->cheque_no = $request->input('cheque_no');
        $voucherMain->credit_card_no = $request->input('credit_card_no');
        $voucherMain->bank_id = $request->input('bank_id');
        $voucherMain->payee = $request->input('payee_name');
        $voucherMain->transaction_id = $request->input('transaction_id');
        $voucherMain->created_by = $current_user->id;
        $voucherMain->bank_account = $request->input('bank_account');
        $voucherMain->payment_date = $request->input('payment_date');
        $voucherMain->remark = $request->input('payment_desc');
        $voucherMain->total_amount = $request->input('amount');
        $voucherMain->status = 3;
        $voucherMain->lawyer_approval = 1;
        $voucherMain->account_approval = 1;
        $voucherMain->voucher_type = 3;
        $voucherMain->office_account_id = $request->input('office_account_id');
        $voucherMain->created_at = date('Y-m-d H:i:s');
        $voucherMain->save();


        $voucherDetails = new VoucherDetails();

        $voucherDetails->voucher_main_id = $voucherMain->id;
        $voucherDetails->user_id = $current_user->id;
        $voucherDetails->case_id = $id;
        $voucherDetails->account_details_id = 0;
        $voucherDetails->amount = $request->input('amount');
        $voucherDetails->payment_type = $request->input('payment_type');
        $voucherDetails->voucher_no = $voucher_running_no;
        $voucherDetails->cheque_no = $request->input('cheque_no');
        $voucherDetails->credit_card_no = $request->input('credit_card_no');
        $voucherDetails->bank_id = $request->input('bank_id');
        $voucherDetails->bank_account = $request->input('bank_account');
        $voucherDetails->status = 3;
        $voucherDetails->created_at = date('Y-m-d H:i:s');
        $voucherDetails->save();




        // $voucherMain = new VoucherMain();

        // $voucherMain->user_id = $current_user->id;
        // $voucherMain->case_id = $id;
        // $voucherMain->payment_type = $request->input('payment_type');
        // $voucherMain->voucher_no = $voucher_running_no;
        // $voucherMain->cheque_no = $request->input('cheque_no');
        // $voucherMain->credit_card_no = $request->input('credit_card_no');
        // $voucherMain->bank_id = $request->input('bank_id');
        // $voucherMain->payee = $request->input('payee_name');
        // $voucherMain->created_by = $current_user->id;
        // $voucherMain->transaction_id =  $request->input('transaction_id');
        // $voucherMain->bank_account = $request->input('bank_account');
        // $voucherMain->office_account_id = $request->input('office_account_id');
        // $voucherMain->payment_date = $request->input('payment_date');
        // $voucherMain->total_amount = $request->input('amount');
        // $voucherMain->voucher_type = 2;
        // $voucherMain->item_code = $loanCaseTrust->id;

        // if($current_user->menuroles == 'lawyer')
        // {
        //     $voucherMain->lawyer_approval = 1;
        //     $voucherMain->lawyer_id = $current_user->id;
        // }

        // if($current_user->menuroles == 'acccount')
        // {
        //     $voucherMain->accojunt_approval = 1;
        // }

        // $voucherMain->status = 1;
        // $voucherMain->created_at = date('Y-m-d H:i:s');
        // $voucherMain->save();



        // $voucherDetails = new VoucherDetails();

        // $voucherDetails->voucher_main_id = $voucherMain->id;
        // $voucherDetails->user_id = $current_user->id;
        // $voucherDetails->case_id = $id;
        // $voucherDetails->account_details_id = $loanCaseTrust->id;
        // $voucherDetails->amount = $request->input('amount');
        // $voucherDetails->payment_type = $request->input('payment_type');
        // $voucherDetails->voucher_no = $voucher_running_no;
        // $voucherDetails->cheque_no = $request->input('cheque_no');
        // $voucherDetails->credit_card_no = $request->input('credit_card_no');
        // $voucherDetails->bank_id = $request->input('bank_id');
        // $voucherDetails->bank_account = $request->input('bank_account');
        // $voucherDetails->status = 1;
        // $voucherDetails->created_at = date('Y-m-d H:i:s');
        // $voucherDetails->save();

        return response()->json(['status' => $status, 'data' => $data]);
    }

    public function updateLoanCaseTrustMain(Request $request, $id)
    {
        $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $id)->first();

        if ($LoanCaseTrustMain == null) {
            $LoanCaseTrustMain = new LoanCaseTrustMain();
        }

        $current_user = auth()->user();

        $LoanCaseTrustMain->case_id =  $id;

        $LoanCaseTrustMain->payee =  $request->input('payee_name');
        $LoanCaseTrustMain->transaction_id =  $request->input('transaction_id');
        $LoanCaseTrustMain->payment_type =  $request->input('payment_type');
        $LoanCaseTrustMain->cheque_no =  $request->input('cheque_no');
        $LoanCaseTrustMain->credit_card_no =  $request->input('credit_card_no');
        $LoanCaseTrustMain->office_account_id =  $request->input('office_account_id');
        $LoanCaseTrustMain->payment_date =  $request->input('payment_date');
        // $LoanCaseTrustMain->voucher_no =  $request->input('txt_voucher_no_trust_edit');
        $LoanCaseTrustMain->office_account_id =  $request->input('office_account_id');
        $LoanCaseTrustMain->remark =  $request->input('payment_desc');
        $LoanCaseTrustMain->status =  1;
        $LoanCaseTrustMain->updated_by = $current_user->id;
        $LoanCaseTrustMain->updated_at = date('Y-m-d H:i:s');
        $LoanCaseTrustMain->save();

        return response()->json(['status' => 1, 'data' => 'Data updated']);
    }

    public function TrustEntryMain(Request $request, $id)
    {
        $current_user = auth()->user();

        $LoanCaseTrustMain = new LoanCaseTrustMain();
        $LoanCaseTrustMain->case_id =  $id;
        $LoanCaseTrustMain->movement_type =  $request->input('payment_movement');

        $LoanCaseTrustMain->transaction_type =  'D';


        $LoanCaseTrustMain->payment_type =  $request->input('ddl_payment_type_trust');
        $LoanCaseTrustMain->cheque_no =  $request->input('txt_cheque_no_trust');
        $LoanCaseTrustMain->bank_id =  $request->input('txt_bank_name_trust');
        $LoanCaseTrustMain->bank_account =  $request->input('txt_bank_account_trust');
        $LoanCaseTrustMain->payment_date =  $request->input('voucher_payment_time_trust');
        $LoanCaseTrustMain->item_name =  $request->input('payment_name');
        $LoanCaseTrustMain->item_code =  $request->input('transaction_id');
        $LoanCaseTrustMain->amount =  $request->input('payment_amt');
        $LoanCaseTrustMain->office_account_id =  $request->input('OfficeBankAccount_id_trust');
        $LoanCaseTrustMain->remark =  $request->input('payment_desc');
        $LoanCaseTrustMain->status =  1;
        $LoanCaseTrustMain->created_at = date('Y-m-d H:i:s');
    }


    public function billEntry(Request $request)
    {
        $status = 1;
        $data = '';

        $current_user = auth()->user();

        // $loanCaseTrust = new LoanCaseTrust();
        // $loanCaseTrust->case_id =  $request->input('case_id_trust');
        // $loanCaseTrust->item_name =  $request->input('name');
        // $loanCaseTrust->amount =  $request->input('amt');
        // $loanCaseTrust->status =  1;
        // $loanCaseTrust->created_at = date('Y-m-d H:i:s');
        // $loanCaseTrust->save();

        $transaction = new Transaction();


        $loanCase = LoanCase::where('id', '=', $request->input('case_id_bill'))->first();

        $collected_bill = (float)($loanCase->collected_bill) + (float)($request->input('amt'));
        $total_bill = (float)($loanCase->total_bill) + (float)($request->input('amt'));

        $loanCase->collected_bill = $collected_bill;
        $loanCase->total_bill = $total_bill;
        $loanCase->updated_at = date('Y-m-d H:i:s');
        $loanCase->save();


        // get parameter (will move into parameter controller)
        $parameter = Parameter::where('parameter_type', '=', 'transaction_running_no')->first();

        $running_no = (int)$parameter->parameter_value_1 + 1;

        $parameter->parameter_value_1 = $running_no;
        $parameter->save();

        $transaction->transaction_id = $running_no;
        $transaction->case_id = $request->input('case_id_bill');
        $transaction->user_id = $current_user->id;
        $transaction->account_details_id = $request->input('account_details_id');
        $transaction->transaction_type = 'D';
        $transaction->amount = $request->input('amt');
        $transaction->cheque_no = '';
        $transaction->bank_id = 0;
        $transaction->remark = '';
        $transaction->status = 1;
        $transaction->created_at = date('Y-m-d H:i:s');
        $transaction->save();

        return response()->json(['status' => $status, 'data' => $data]);
    }

    public function setKIV(Request $request, $id)
    {
        $status = 1;
        $data = '';

        $loanCase = LoanCase::where('id', '=', $id)->first();

        $loanCase->status = 3; //kiv
        $loanCase->remark = $request->input('reason');
        $loanCase->save();
        return response()->json(['status' => $status, 'data' => $data]);
    }

    public function updateKPI($point, $type)
    {
        $userKpiHistory = new UserKpiHistory();
        $current_user = auth()->user();

        $userKpiHistory->type = $type;
        $userKpiHistory->point = $point;
        $userKpiHistory->user_id = $current_user->id;
        $userKpiHistory->status = 1;
        $userKpiHistory->created_at = date('Y-m-d H:i:s');
        $userKpiHistory->save();
    }

    public function updateMasterList(Request $request, $id)
    {
        $status = 1;
        $message = '';
        $data = $request->except('_token');

        try {

            $loanCase = LoanCase::where('id', '=', $id)->first();

            foreach ($data as $key => $value) {

                // if ($value != null) {
                $loanCaseMasterList = LoanCaseMasterList::where('case_id', '=', $id)->where('masterlist_field_id', '=', $key)->first();

                if ($loanCaseMasterList) {
                    $loanCaseMasterList->case_id = $id;
                    $loanCaseMasterList->masterlist_field_id = $key;
                    $loanCaseMasterList->value = $value;
                    $loanCaseMasterList->updated_at = date('Y-m-d H:i:s');
                    $loanCaseMasterList->save();
                } else {
                    $loanCaseMasterList = new LoanCaseMasterList();

                    $loanCaseMasterList->case_id = $id;
                    $loanCaseMasterList->masterlist_field_id = $key;
                    $loanCaseMasterList->value = $value;
                    $loanCaseMasterList->created_at = date('Y-m-d H:i:s');
                    $loanCaseMasterList->save();
                }

                if ($key == 141) {
                    $loanCase->loan_sum = $value;
                    $loanCase->save();
                }

                if ($key == 129) {
                    $loanCase->purchase_price = $value;
                    $loanCase->save();
                }
                // }
            }
        } catch (\Throwable $e) {
            $status = 0;
            $message = $e;
        }

        return response()->json(['status' => $status, 'data' => $message]);
    }

    public function requestVoucher(Request $request, $id)
    {
        $status = 1;
        $need_approval = 0;
        $totalAmount = 0;
        $message = 'Voucher requested';
        $voucherList = [];

        // If the role is lawyer then will auto approve
        $auto_approval = 0;

        if ($request->input('voucher_list') != null) {
            $voucherList = json_decode($request->input('voucher_list'), true);
        }

        $current_user = auth()->user();

        $userRoles = $current_user->menuroles;

        if ($userRoles == "lawyer" || $userRoles == "admin" || $userRoles == "management") {
            $auto_approval = 1;
        }

        // $loanCaseAccount = LoanCaseAccount::where('id', '=', $request->input('account_details_id'))->first();

        if (count($voucherList) > 0) {

            $Parameter = Parameter::where('parameter_type', '=', 'voucher_running_no')->first();
            $voucher_running_no = (int)$Parameter->parameter_value_1;

            $Parameter->parameter_value_1 = (int)$Parameter->parameter_value_1 + 1;
            $Parameter->save();

            $current_user = auth()->user();
            $loanCase = LoanCase::where('id', '=', $id)->first();

            // $LoanCaseBillAccount = new LoanCaseBillAccount();
            // $LoanCaseBillAccount->case_id =  $id;
            // $LoanCaseBillAccount->movement_type =  2;
            // $LoanCaseBillAccount->transaction_type =  'C';
            // $LoanCaseBillAccount->payment_type =  $request->input('payment_type');
            // $LoanCaseBillAccount->payment_date =  $request->input('payment_date');
            // $LoanCaseBillAccount->cheque_no =  $request->input('cheque_no');
            // $LoanCaseBillAccount->bank_id =  $request->input('bank_id');
            // $LoanCaseBillAccount->bank_account =  $request->input('bank_account');
            // $LoanCaseBillAccount->item_name =  $request->input('payee_name');
            // // $loanCaseTrust->transaction_id =  $request->input('transaction_id');
            // $LoanCaseBillAccount->amount =  $request->input('amount');
            // $LoanCaseBillAccount->office_account_id =  $request->input('office_account_id');
            // $LoanCaseBillAccount->remark =  $request->input('payment_desc');
            // $LoanCaseBillAccount->status =  1;
            // $LoanCaseBillAccount->created_at = date('Y-m-d H:i:s');
            // $LoanCaseBillAccount->created_by = $current_user->id;
            // $LoanCaseBillAccount->save();

            // return $LoanCaseBillAccount;

            $voucherMain = new VoucherMain();

            $voucherMain->user_id = $current_user->id;
            $voucherMain->case_id = $id;
            $voucherMain->payment_type = $request->input('payment_type');
            $voucherMain->voucher_no = $voucher_running_no;
            $voucherMain->cheque_no = $request->input('cheque_no');
            $voucherMain->credit_card_no = $request->input('credit_card_no');
            $voucherMain->bank_id = $request->input('bank_id');
            $voucherMain->payee = $request->input('payee');
            $voucherMain->remark = $request->input('remark');
            $voucherMain->created_by = $current_user->id;
            $voucherMain->bank_account = $request->input('bank_account');
            $voucherMain->adjudication_no = $request->input('adjudication_no');
            // $voucherMain->office_account_id = $request->input('OfficeBankAccount');
            if ($userRoles == "lawyer" || $userRoles == "account") {
                $voucherMain->lawyer_approval = 1;
            }

            $voucherMain->payment_date = $request->input('payment_date');
            $voucherMain->total_amount = 0;
            $voucherMain->status = 1;
            $voucherMain->created_at = date('Y-m-d H:i:s');
            $voucherMain->save();

            if ($userRoles == "lawyer") {

                // temporary auto approve voucher if lawyer sent
                $Notification  = new Notification();
                $Notification->name = $current_user->name;
                $Notification->desc = 'approved voucher ' . $voucher_running_no;
                $Notification->user_id = 0;
                $Notification->role = 'account|admin|management';
                $Notification->parameter1 = $id;
                $Notification->parameter2 = $voucherMain->id;
                $Notification->module = 'voucher';
                $Notification->bln_read = 0;
                $Notification->status = 1;
                $Notification->created_at = now();
                $Notification->created_by = $current_user->id;
                $Notification->save();
            } else {
                $Notification  = new Notification();
                $Notification->name = $current_user->name;
                $Notification->desc = 'request voucher ' . $voucher_running_no;
                $Notification->user_id = $loanCase->lawyer_id;
                $Notification->role = '';
                $Notification->parameter1 = $id;
                $Notification->parameter2 = $voucherMain->id;
                $Notification->module = 'voucher';
                $Notification->bln_read = 0;
                $Notification->status = 1;
                $Notification->created_at = now();
                $Notification->created_by = $current_user->id;
                $Notification->save();
            }

            for ($i = 0; $i < count($voucherList); $i++) {

                $voucherDetails = new VoucherDetails();

                $voucherDetails->voucher_main_id = $voucherMain->id;
                $voucherDetails->user_id = $current_user->id;
                $voucherDetails->case_id = $id;
                $voucherDetails->account_details_id = $voucherList[$i]['account_details_id'];
                $voucherDetails->amount = $voucherList[$i]['amount'];
                $voucherDetails->payment_type = $request->input('payment_type');
                $voucherDetails->voucher_no = $voucher_running_no;
                $voucherDetails->cheque_no = $request->input('cheque_no');
                $voucherDetails->credit_card_no = $request->input('credit_card_no');
                $voucherDetails->bank_id = $request->input('bank_id');
                $voucherDetails->bank_account = $request->input('bank_account');
                $voucherDetails->status = 1;
                $voucherDetails->created_at = date('Y-m-d H:i:s');
                $voucherDetails->save();

                $totalAmount += $voucherList[$i]['amount'];

                // $loanCaseAccount = LoanCaseAccount::where('id', '=', $voucherList[$i]['account_details_id'])->first();

                $loanCaseAccount = LoanCaseBillDetails::where('id', '=', $voucherList[$i]['account_details_id'])->first();

                if ($loanCaseAccount->need_approval == 1) {
                    $need_approval = 1;
                } else {
                    // // update the main amount
                    $loanCaseAccount->amount = $loanCaseAccount->amount - $voucherList[$i]['amount'];
                    $loanCaseAccount->save();

                    $loanCaseBillMain = LoanCaseBillMain::where('id', '=', $request->input('bill_main_id'))->first();

                    $loanCaseBillMain->used_amt = $loanCaseBillMain->used_amt + $voucherList[$i]['amount'];
                    $loanCaseBillMain->save();


                    $loanCase->total_bill = $loanCase->total_bill + $voucherList[$i]['amount'];
                    $loanCase->save();
                }


                // $voucherMain->account_details_id = $request->input('account_details_id');


                $caseAccountTransaction = new CaseAccountTransaction();

                $caseAccountTransaction->case_id = $id;
                $caseAccountTransaction->account_details_id = $voucherList[$i]['account_details_id'];
                $caseAccountTransaction->debit = 0;
                $caseAccountTransaction->credit = $voucherList[$i]['amount'];
                $caseAccountTransaction->status = 1;
                $caseAccountTransaction->created_at = date('Y-m-d H:i:s');
                $caseAccountTransaction->save();

                // $loanCaseAccount->amount = $loanCaseAccount->amount - $voucherList[$i]['amount'];
                // $loanCaseAccount->save();

                $transaction = new Transaction();


                // get parameter (will move into parameter controller)
                $parameter = Parameter::where('parameter_type', '=', 'transaction_running_no')->first();

                $running_no = (int)$parameter->parameter_value_1 + 1;

                $parameter->parameter_value_1 = $running_no;
                $parameter->save();

                $transaction->transaction_id = $running_no;
                $transaction->case_id = $id;
                $transaction->user_id = $current_user->id;
                $transaction->account_details_id = $voucherList[$i]['account_details_id'];
                $transaction->transaction_type = 'C';
                $transaction->amount = $voucherList[$i]['amount'];
                $transaction->cheque_no = '';
                $transaction->bank_id = 0;
                $transaction->remark = '';
                $transaction->status = $auto_approval;
                $transaction->created_at = date('Y-m-d H:i:s');
                $transaction->save();

                // $loanCaseAccount->amount = $loanCaseAccount->amount - $request->input('amt');
                // $loanCaseAccount->save();
            }

            if ($need_approval == 0) {
                // // temporary all need apporlva
                //  $voucherMain->status = 1;
            } else {
                $caseTodo = new CaseTodo();

                $caseTodo->type = 1;
                $caseTodo->ref_id = $voucherMain->id;
                $caseTodo->case_id = $id;
                $caseTodo->request_user_id = $current_user->id;
                $caseTodo->start_dttm = date('Y-m-d H:i:s');
                $caseTodo->remark = '';
                $caseTodo->status = 0;
                $caseTodo->created_at = date('Y-m-d H:i:s');
                $caseTodo->save();
            }

            $voucherMain->total_amount = $totalAmount;
            $voucherMain->save();
        }

        return response()->json(['status' => $status, 'data' => $message]);
    }


    public function receiveBillPayment(Request $request, $id, $billMainTempId)
    {
        $status = 1;
        $need_approval = 0;
        $totalAmount = 0;
        $message = 'Payment received';
        $voucherList = [];

        $current_user = auth()->user();
        $Parameter = Parameter::where('parameter_type', '=', 'voucher_running_no')->first();
        $voucher_running_no = (int)$Parameter->parameter_value_1;

        $Parameter->parameter_value_1 = (int)$Parameter->parameter_value_1 + 1;
        $Parameter->save();

        $voucherMain = new VoucherMain();


        $voucherMain->user_id = $current_user->id;
        $voucherMain->case_id = $id;
        $voucherMain->payment_type = $request->input('ddl_payment_type_trust');
        $voucherMain->voucher_no = $voucher_running_no;
        $voucherMain->cheque_no = $request->input('cheque_no');
        $voucherMain->credit_card_no = $request->input('credit_card_no');
        $voucherMain->bank_id = $request->input('bank_id');
        $voucherMain->payee = $request->input('payment_name');
        $voucherMain->transaction_id = $request->input('transaction_id');
        $voucherMain->created_by = $current_user->id;
        $voucherMain->bank_account = $request->input('bank_account');
        $voucherMain->payment_date = $request->input('payment_date');
        $voucherMain->remark = $request->input('remark');
        $voucherMain->total_amount = $request->input('payment_amt');
        $voucherMain->status = 4;
        $voucherMain->lawyer_approval = 1;
        $voucherMain->account_approval = 1;
        $voucherMain->office_account_id = $request->input('OfficeBankAccount_id');
        $voucherMain->created_at = date('Y-m-d H:i:s');
        $voucherMain->save();


        $voucherDetails = new VoucherDetails();

        $voucherDetails->voucher_main_id = $voucherMain->id;
        $voucherDetails->user_id = $current_user->id;
        $voucherDetails->case_id = $id;
        $voucherDetails->account_details_id = 0;
        $voucherDetails->amount = $request->input('payment_amt');
        $voucherDetails->payment_type = $request->input('ddl_payment_type_trust');
        $voucherDetails->voucher_no = $voucher_running_no;
        $voucherDetails->cheque_no = $request->input('cheque_no');
        $voucherDetails->credit_card_no = $request->input('credit_card_no');
        $voucherDetails->bank_id = $request->input('bank_id');
        $voucherDetails->bank_account = $request->input('bank_account');
        $voucherDetails->status = 4;
        $voucherDetails->created_at = date('Y-m-d H:i:s');
        $voucherDetails->save();




        $loanCaseBillMain = LoanCaseBillMain::where('id', '=', $billMainTempId)->first();

        $loanCaseBillMain->collected_amt = $loanCaseBillMain->collected_amt + $request->input('payment_amt');


        // auto allocate payment into pfee & disb

        // $LoanCaseBillDetails = LoanCaseBillDetails::where('loan_case_main_bill_id', '=', $billMainTempId)->first();

        // $category = AccountCategory::where('status', '=', 1)->get();
        // $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)->get();


        // $quotation = array();

        // for ($i = 0; $i < count($category); $i++) {

        //     // $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)
        //     //     ->where('account_cat_id', '=', $category[$i]->id)
        //     //     ->get();

        //     $QuotationTemplateDetails = DB::table('quotation_template_details AS qd')
        //         ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
        //         ->select('qd.*', 'a.name as account_name', 'a.formula as account_formula', 'a.min as account_min', 'a.max as max', 'a.amount as account_amt', 'a.id as account_item_id')
        //         ->where('qd.acc_main_template_id', '=',  $id)
        //         ->where('a.account_cat_id', '=',  $category[$i]->id)
        //         ->orderBy('order_no', 'ASC')
        //         ->get();

        //     array_push($quotation,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));
        // }

        $loanCaseBillMain->save();

        $loanCase = LoanCase::where('id', '=', $id)->first();
        $loanCase->collected_bill = $loanCase->collected_bill + $request->input('payment_amt');
        $loanCase->save();

        $this->updateBillSummary($request, $billMainTempId);






        $transaction = new Transaction();
        $parameter = Parameter::where('parameter_type', '=', 'transaction_running_no')->first();

        $running_no = (int)$parameter->parameter_value_1 + 1;

        $parameter->parameter_value_1 = $running_no;
        $parameter->save();

        $transaction->transaction_id = $running_no;
        $transaction->case_id = $id;
        $transaction->user_id = $current_user->id;
        $transaction->account_details_id = 0;
        $transaction->transaction_type = 'D';
        $transaction->amount = $request->input('payment_amt');
        $transaction->cheque_no = '';
        $transaction->bank_id = 0;
        $transaction->remark = '';
        $transaction->status = 1;
        $transaction->created_at = date('Y-m-d H:i:s');
        $transaction->save();

        return response()->json(['status' => $status, 'data' => $message]);
    }

    public function loadCaseTemplate(Request $request)
    {
        $caseTemplateDetail = CaseTemplateDetails::where('template_main_id', '=', $request->input('template_id'))->get()->sortBy('process_number');

        return response()->json([
            'view' => view('dashboard.case.table.tbl-list', compact('cases'))->render()
        ]);

        // return  $users;
    }

    public function loadQuotationTemplate(Request $request, $id)
    {
        // $caseTemplateDetail = AccountCategory::where('template_main_id', '=', $request->input('template_id'))->get()->sortBy('process_number');

        $loanCase = LoanCase::where('id', '=', $request->input('case_id'))->first();

        $category = AccountCategory::where('status', '=', 1)->get();
        $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)->get();


        $quotation = array();

        for ($i = 0; $i < count($category); $i++) {

            // $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)
            //     ->where('account_cat_id', '=', $category[$i]->id)
            //     ->get();

            $QuotationTemplateDetails = DB::table('quotation_template_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.formula as account_formula', 'a.min as account_min', 'a.max as max', 'a.amount as account_amt', 'a.id as account_item_id')
                ->where('qd.acc_main_template_id', '=',  $id)
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->orderBy('order_no', 'ASC')
                ->get();

            array_push($quotation,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));
        }


        return response()->json([
            'view' => view('dashboard.case.table.tbl-bill-list', compact('quotation', 'loanCase'))->render()
        ]);

        // return  $users;
    }

    public function loadCaseBill(Request $request, $id)
    {
        // $caseTemplateDetail = AccountCategory::where('template_main_id', '=', $request->input('template_id'))->get()->sortBy('process_number');

        $loanCase = LoanCase::where('id', '=', $request->input('case_id'))->first();
        $quotation_template_id = 0;

        $client = Customer::where('id', '=', $loanCase->customer_id)->first();

        $category = AccountCategory::where('status', '=', 1)->get();

        $loanCaseBillDetails = LoanCaseBillDetails::where('id', '=', $id)->where('status', '=', 1)->get();
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        // hardcode get name

        if ($LoanCaseBillMain->name == 'Loan (Title & Master Title)') {
            $quotation_template_id = 13;
        } else if ($LoanCaseBillMain->name == 'Purchaser (Title & Master Title)') {
            $quotation_template_id = 14;
        } else if ($LoanCaseBillMain->name == 'Loan (Title & Master Title) - RHB Islamic') {
            $quotation_template_id = 15;
        } else if ($LoanCaseBillMain->name == 'Vendor (title & Master Title)') {
            $quotation_template_id = 16;
        }



        $LoanCaseBillMain = DB::table('loan_case_bill_main AS mb')
            ->leftJoin('quotation_template_main AS q', 'q.id', '=', 'mb.quotation_template_id')
            ->select('mb.*', 'q.pf_desc')
            ->where('mb.id', '=',  $id)
            ->first();


        $voucher = DB::table('voucher_main AS m')
            ->leftJoin('voucher_details AS d', 'd.voucher_main_id', '=', 'm.id')
            ->leftJoin('loan_case_bill_details AS qd', 'qd.id', '=', 'd.account_details_id')
            ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
            ->select('m.*', 'a.name as account_name')
            ->where('m.case_id', '=',  $request->input('case_id'))
            ->get();

        $bill_disburse = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->select('vd.*', 'a.name as account_name', 'vm.id as voucher_id', 'vm.voucher_no', 'vm.lawyer_approval as lawyer_approval', 'vm.account_approval as account_approval')
            ->where('vd.case_id', '=',  $request->input('case_id'))
            ->where('bd.loan_case_main_bill_id', '=',  $id)
            ->where('vd.status', '<>',  4)
            ->where('vd.status', '<>',  99)
            ->get();



        $bill_receive = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->select('vd.*', 'a.name as account_name', 'vm.id as voucher_id', 'vm.voucher_no', 'vm.payee', 'vm.remark as remark')
            ->where('vd.case_id', '=',  $request->input('case_id'))
            ->where('vd.status', '=',  4)
            ->get();


        $quotation = array();
        $item_id = array();

        for ($i = 0; $i < count($category); $i++) {

            // $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)
            //     ->where('account_cat_id', '=', $category[$i]->id)
            //     ->get();

            $QuotationTemplateDetails = DB::table('loan_case_bill_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.formula as account_formula', 'a.min as account_min', 'a.id as account_item_id')
                ->where('qd.loan_case_main_bill_id', '=',  $id)
                ->where('qd.status', '=',  1)
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->get();

            array_push($quotation,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));

            for ($j = 0; $j < count($QuotationTemplateDetails); $j++) {
                array_push($item_id,  $QuotationTemplateDetails[$j]->account_item_id);
            }
        }


        $current_user = auth()->user();

        $QuotationTemplate = [];

        if ($quotation_template_id != 0) {
            $QuotationTemplate = DB::table('quotation_template_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.account_cat_id as account_cat_id', 'a.min as account_min', 'a.id as account_item_id', 'a.amount as default_amt')
                ->where('qd.acc_main_template_id', '=',  $quotation_template_id)
                ->whereNotIn('qd.account_item_id', $item_id)
                ->where('qd.status', '=',  1)
                ->get();
        }

        return response()->json([
            'view' => view('dashboard.case.table.tbl-case-bill-list', compact('quotation', 'current_user', 'LoanCaseBillMain'))->render(),
            'view2' => view('dashboard.case.table.tbl-case-quotation-p', compact('quotation', 'LoanCaseBillMain'))->render(),
            'view3' => view('dashboard.case.table.tbl-case-invoice-p', compact('quotation', 'LoanCaseBillMain'))->render(),
            'disburse' => view('dashboard.case.table.tbl-bill-disburse-list', compact('bill_disburse'))->render(),
            'receive' => view('dashboard.case.table.tbl-bill-receive-list', compact('bill_receive'))->render(),
            'client' => $client,
            'current_user' => $current_user,
            'QuotationTemplate' => $QuotationTemplate,
            'LoanCaseBillMain' => $LoanCaseBillMain
        ]);

        // return  $users;
    }


    public function createBill(Request $request, $id)
    {
        $status = 1;
        $need_approval = 0;
        $totalAmount = 0;
        $totalAmountNoSST = 0;
        $message = 'Voucher requested';
        $billList = [];

        $current_user = auth()->user();


        if ($request->input('bill_list') != null) {
            $billList = json_decode($request->input('bill_list'), true);
        }

        if (count($billList) > 0) {

            $parameter = Parameter::where('parameter_type', '=', 'bill_running_no')->first();

            $running_no = (int)$parameter->parameter_value_1 + 1;
            $parameter->parameter_value_1 = $running_no;
            $parameter->save();

            $loanCaseBillMain = new LoanCaseBillMain();

            $loanCaseBillMain->case_id = $id;
            $loanCaseBillMain->bill_no = $running_no;
            $loanCaseBillMain->name = $request->input('name');
            $loanCaseBillMain->status = 1;
            $loanCaseBillMain->created_at = date('Y-m-d H:i:s');
            $loanCaseBillMain->created_by = $current_user->id;
            $loanCaseBillMain->save();

            for ($i = 0; $i < count($billList); $i++) {

                $loanCaseBillDetails = new LoanCaseBillDetails();

                $loanCaseBillDetails->loan_case_main_bill_id = $loanCaseBillMain->id;
                $loanCaseBillDetails->account_item_id = $billList[$i]['account_item_id'];
                $loanCaseBillDetails->min = $billList[$i]['min'];
                $loanCaseBillDetails->max = $billList[$i]['max'];
                $loanCaseBillDetails->need_approval = $billList[$i]['need_approval'];
                if ($billList[$i]['cat_id'] == 1) {
                    $loanCaseBillDetails->quo_amount = (float)$billList[$i]['amount'] * 1.06;
                    $loanCaseBillDetails->amount = (float)$billList[$i]['amount'];

                    $totalAmount +=  (float)$billList[$i]['amount'];
                    $totalAmountNoSST += $billList[$i]['amount'];
                } else {
                    $loanCaseBillDetails->quo_amount = $billList[$i]['amount'];
                    $loanCaseBillDetails->amount = $billList[$i]['amount'];

                    $totalAmount += $billList[$i]['amount'];
                    $totalAmountNoSST += $billList[$i]['amount'];
                }

                $loanCaseBillDetails->quo_amount_no_sst = $billList[$i]['amount'];
                $loanCaseBillDetails->status = 1;
                $loanCaseBillDetails->created_at = date('Y-m-d H:i:s');
                $loanCaseBillDetails->save();
            }



            $loanCaseBillMain->total_amt = $totalAmount;
            $loanCaseBillMain->total_amount_without_sst = $totalAmountNoSST;
            $loanCaseBillMain->save();

            $loanCase = LoanCase::where('id', '=', $id)->first();

            $loanCase->targeted_bill += $totalAmount;
            $loanCase->save();

            $this->updateBillSummary($request, $loanCaseBillMain->id);
        }

        return response()->json(['status' => $status, 'data' => $message]);
    }

    public function updateBillSummary(Request $request, $id)
    {
        $pfee = 0;
        $pfee1 = 0;
        $pfee2 = 0;
        $disb = 0;
        $sst = 0;

        $referral_a1 = 0;
        $referral_a2 = 0;
        $referral_a3 = 0;
        $referral_a4 = 0;
        $marketing = 0;
        $uncollected = 0;

        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        // temporary update all
        $LoanCaseBillMainAll = LoanCaseBillMain::where('status', '=', 1)->get();


        for ($j = 0; $j < count($LoanCaseBillMainAll); $j++) {
            $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $LoanCaseBillMainAll[$j]->id)->first();

            $loanBillDetails = DB::table('loan_case_bill_details AS bd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
                ->select('bd.*', 'a.account_cat_id', 'a.pfee1_item')
                ->where('bd.loan_case_main_bill_id', '=',  $LoanCaseBillMainAll[$j]->id)
                ->get();

            $pfee = 0;
            $pfee1 = 0;
            $pfee2 = 0;
            $disb = 0;
            $sst = 0;

            $referral_a1 = 0;
            $referral_a2 = 0;
            $referral_a3 = 0;
            $referral_a4 = 0;
            $marketing = 0;


            for ($i = 0; $i < count($loanBillDetails); $i++) {

                if ($loanBillDetails[$i]->account_cat_id == 1) {
                    $pfee += $loanBillDetails[$i]->quo_amount_no_sst;

                    if ($loanBillDetails[$i]->pfee1_item == 1) {
                        $pfee1 += $loanBillDetails[$i]->quo_amount_no_sst;
                    } else {
                        $pfee2 += $loanBillDetails[$i]->quo_amount_no_sst;
                    }
                }

                if ($loanBillDetails[$i]->account_cat_id == 3 || $loanBillDetails[$i]->account_cat_id == 2) {
                    $disb += $loanBillDetails[$i]->quo_amount_no_sst;
                }
            }

            $sst = $pfee * 0.06;
            $sst = number_format((float)$sst, 2, '.', '');

            $LoanCaseBillMain->pfee1 = $pfee1;
            $LoanCaseBillMain->pfee2 = $pfee2;
            $LoanCaseBillMain->pfee = $pfee;
            $LoanCaseBillMain->disb = $disb;
            $LoanCaseBillMain->sst = $sst;

            if ($request->input('referral_a1') != null) {
                // $LoanCaseBillMain->referral_a1 = $request->input('referral_a1');
                // $LoanCaseBillMain->referral_a2 = $request->input('referral_a2');
                // $LoanCaseBillMain->referral_a3 = $request->input('referral_a3');
                // $LoanCaseBillMain->referral_a4 = $request->input('referral_a4');
                // $LoanCaseBillMain->marketing = $request->input('marketing');

                // $referral_a1 = $request->input('referral_a1');
                // $referral_a2 = $request->input('referral_a2');
                // $referral_a3 = $request->input('referral_a3');
                // $referral_a4 = $request->input('referral_a4');
                // $marketing = $request->input('marketing');


            }

            $LoanCaseBillMain->referral_a1 = $request->input('referral_a1');
                $LoanCaseBillMain->referral_a2 = $request->input('referral_a2');
                $LoanCaseBillMain->referral_a3 = $request->input('referral_a3');
                $LoanCaseBillMain->referral_a4 = $request->input('referral_a4');
                $LoanCaseBillMain->marketing = $request->input('marketing');

            $referral_a1 = $LoanCaseBillMain->referral_a1;
            $referral_a2 = $LoanCaseBillMain->referral_a2;
            $referral_a3 = $LoanCaseBillMain->referral_a3;
            $referral_a4 = $LoanCaseBillMain->referral_a4;
            $marketing = $LoanCaseBillMain->marketing;

            $collected_amt = $LoanCaseBillMain->collected_amt;
            $collected_amt_sum = $collected_amt;



            // if ($collected_amt >= 0) {

            //     if (($collected_amt - $disb) >= 0) {
            //         $collected_amt = $collected_amt - $disb;
            //         $LoanCaseBillMain->disb_recv = $disb;
            //     } else {
            //         $LoanCaseBillMain->disb_recv = $collected_amt;
            //         $collected_amt = 0;
            //     }

            //     // if (($collected_amt - $pfee) >= 0) {
            //     //     // $collected_amt = $collected_amt - $pfee;
            //     //     $LoanCaseBillMain->pfee_recv = $pfee;
            //     //     // $LoanCaseBillMain->pfee_recv = $pfee;
            //     //     // $LoanCaseBillMain->pfee_recv = $pfee;

            //     //     $sst = $pfee * 0.06;
            //     //     $sst = number_format((float)$sst, 2, '.', '');

            //     //     $LoanCaseBillMain->sst_recv = $sst;
            //     // } else {
            //     //     $LoanCaseBillMain->pfee_recv = $collected_amt;

            //     //     $sst = $collected_amt * 0.06;
            //     //     $sst = number_format((float)$sst, 2, '.', '');

            //     //     $LoanCaseBillMain->sst_recv = $sst;
            //     //     // $collected_amt = 0;
            //     // }

            //     // if (($collected_amt - $pfee1) >= 0) {
            //     //     $collected_amt = $collected_amt - $pfee1;
            //     //     $LoanCaseBillMain->pfee1_recv = $pfee1;
            //     //     // $LoanCaseBillMain->pfee_recv = $pfee;
            //     //     // $LoanCaseBillMain->pfee_recv = $pfee;

            //     //     // $sst = $pfee * 0.06;
            //     //     // $sst = number_format((float)$sst, 2, '.', '');

            //     //     // $LoanCaseBillMain->sst_recv = $sst;
            //     // } else {
            //     //     $LoanCaseBillMain->pfee1_recv = $collected_amt;


            //     //     $collected_amt = 0;
            //     // }

            //     // if ($collected_amt >= 0) {
            //     //     if (($collected_amt - $pfee2) >= 0) {
            //     //         $collected_amt = $collected_amt - $pfee2;
            //     //         $LoanCaseBillMain->pfee2_recv = $pfee2;
            //     //     } else {
            //     //         $LoanCaseBillMain->pfee2_recv = $collected_amt;
            //     //         $collected_amt = 0;
            //     //     }
            //     // }
            // }

            // if ($collected_amt >= 0) {
            // }

            // if ($collected_amt >= 0) {
            //     if (($collected_amt - $disb) >= 0) {
            //         $collected_amt = $collected_amt - $disb;
            //         $LoanCaseBillMain->disb_recv = $disb;
            //     } else {
            //         $LoanCaseBillMain->disb_recv = $collected_amt;
            //         $collected_amt = 0;
            //     }
            // }

            // if ($collected_amt >= 0) {
            //     $collected_amt = $collected_amt - $referral_a1;
            //     $collected_amt = $collected_amt - $referral_a2;
            //     $collected_amt = $collected_amt - $referral_a3;
            //     $collected_amt = $collected_amt - $referral_a4;
            //     $collected_amt = $collected_amt - $marketing;

            //     $LoanCaseBillMain->uncollected = $collected_amt;
            // }

            $LoanCaseBillMain->save();
        }



        // assign yto pfee first


        //     if ($collected_amt >= 0) {

        //         if (($collected_amt - $pfee) >= 0) {
        //             // $collected_amt = $collected_amt - $pfee;
        //             $LoanCaseBillMain->pfee_recv = $pfee;
        //             // $LoanCaseBillMain->pfee_recv = $pfee;
        //             // $LoanCaseBillMain->pfee_recv = $pfee;

        //             $sst = $pfee * 0.06;
        //             $sst = number_format((float)$sst, 2, '.', '');

        //             $LoanCaseBillMain->sst_recv = $sst;
        //         } else {
        //             $LoanCaseBillMain->pfee_recv = $collected_amt;

        //             $sst = $collected_amt * 0.06;
        //             $sst = number_format((float)$sst, 2, '.', '');

        //             $LoanCaseBillMain->sst_recv = $sst;
        //             // $collected_amt = 0;
        //         }

        //         if (($collected_amt - $pfee1) >= 0) {
        //             $collected_amt = $collected_amt - $pfee1;
        //             $LoanCaseBillMain->pfee1_recv = $pfee1;
        //             // $LoanCaseBillMain->pfee_recv = $pfee;
        //             // $LoanCaseBillMain->pfee_recv = $pfee;

        //             // $sst = $pfee * 0.06;
        //             // $sst = number_format((float)$sst, 2, '.', '');

        //             // $LoanCaseBillMain->sst_recv = $sst;
        //         } else {
        //             $LoanCaseBillMain->pfee1_recv = $collected_amt;


        //             $collected_amt = 0;
        //         }

        //         if ($collected_amt >= 0) {
        //             if (($collected_amt - $pfee2) >= 0) {
        //                 $collected_amt = $collected_amt - $pfee2;
        //                 $LoanCaseBillMain->pfee2_recv = $pfee2;
        //             } else {
        //                 $LoanCaseBillMain->pfee2_recv = $collected_amt;
        //                 $collected_amt = 0;
        //             }
        //         }
        //     }

        //     if ($collected_amt >= 0) {
        //         if (($collected_amt - $disb) >= 0) {
        //             $collected_amt = $collected_amt - $disb;
        //             $LoanCaseBillMain->disb_recv = $disb;
        //         } else {
        //             $LoanCaseBillMain->disb_recv = $collected_amt;
        //             $collected_amt = 0;
        //         }
        //     }

        //     if ($collected_amt >= 0) {
        //         $collected_amt = $collected_amt - $referral_a1;
        //         $collected_amt = $collected_amt - $referral_a2;
        //         $collected_amt = $collected_amt - $referral_a3;
        //         $collected_amt = $collected_amt - $referral_a4;
        //         $collected_amt = $collected_amt - $marketing;

        //         $LoanCaseBillMain->uncollected = $collected_amt;
        //     }

        //     $LoanCaseBillMain->save();
        // }

        return;



        $loanBillDetails = DB::table('loan_case_bill_details AS bd')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->select('bd.*', 'a.account_cat_id', 'a.pfee1_item')
            ->where('bd.loan_case_main_bill_id', '=',  $id)
            ->get();


        for ($i = 0; $i < count($loanBillDetails); $i++) {

            if ($loanBillDetails[$i]->account_cat_id == 1) {
                $pfee += $loanBillDetails[$i]->quo_amount;

                if ($loanBillDetails[$i]->pfee1_item == 1) {
                    $pfee1 += $loanBillDetails[$i]->quo_amount;
                } else {
                    $pfee2 += $loanBillDetails[$i]->quo_amount;
                }
            }

            if ($loanBillDetails[$i]->account_cat_id == 3) {
                $disb += $loanBillDetails[$i]->quo_amount;
            }
        }

        $sst = $pfee * 0.06;
        $sst = number_format((float)$sst, 2, '.', '');

        $LoanCaseBillMain->pfee1 = $pfee1;
        $LoanCaseBillMain->pfee2 = $pfee2;
        $LoanCaseBillMain->pfee = $pfee;
        $LoanCaseBillMain->disb = $disb;
        $LoanCaseBillMain->sst = $sst;

        if ($request->input('referral_a1') != null) {
            // $LoanCaseBillMain->referral_a1 = $request->input('referral_a1');
            // $LoanCaseBillMain->referral_a2 = $request->input('referral_a2');
            // $LoanCaseBillMain->referral_a3 = $request->input('referral_a3');
            // $LoanCaseBillMain->referral_a4 = $request->input('referral_a4');
            // $LoanCaseBillMain->marketing = $request->input('marketing');

            // $referral_a1 = $request->input('referral_a1');
            // $referral_a2 = $request->input('referral_a2');
            // $referral_a3 = $request->input('referral_a3');
            // $referral_a4 = $request->input('referral_a4');
            // $marketing = $request->input('marketing');


        }

        $LoanCaseBillMain->referral_a1 = $request->input('referral_a1');
        $LoanCaseBillMain->referral_a2 = $request->input('referral_a2');
        $LoanCaseBillMain->referral_a3 = $request->input('referral_a3');
        $LoanCaseBillMain->referral_a4 = $request->input('referral_a4');
        $LoanCaseBillMain->marketing = $request->input('marketing');
        $LoanCaseBillMain->uncollected = $request->input('uncollected');

        $referral_a1 = $LoanCaseBillMain->referral_a1;
        $referral_a2 = $LoanCaseBillMain->referral_a2;
        $referral_a3 = $LoanCaseBillMain->referral_a3;
        $referral_a4 = $LoanCaseBillMain->referral_a4;
        $marketing = $LoanCaseBillMain->marketing;
        $uncollected = $LoanCaseBillMain->uncollected;

        $collected_amt = $LoanCaseBillMain->collected_amt;
        $collected_amt_sum = $collected_amt;

        // 

        if ($collected_amt >= 0) {

            if (($collected_amt - $pfee) >= 0) {
                // $collected_amt = $collected_amt - $pfee;
                $LoanCaseBillMain->pfee_recv = $pfee;
                // $LoanCaseBillMain->pfee_recv = $pfee;
                // $LoanCaseBillMain->pfee_recv = $pfee;

                $sst = $pfee * 0.06;
                $sst = number_format((float)$sst, 2, '.', '');

                $LoanCaseBillMain->sst_recv = $sst;
            } else {
                $LoanCaseBillMain->pfee_recv = $collected_amt;

                $sst = $collected_amt * 0.06;
                $sst = number_format((float)$sst, 2, '.', '');

                $LoanCaseBillMain->sst_recv = $sst;
                // $collected_amt = 0;
            }

            if (($collected_amt - $pfee1) >= 0) {
                $collected_amt = $collected_amt - $pfee1;
                $LoanCaseBillMain->pfee1_recv = $pfee1;
                // $LoanCaseBillMain->pfee_recv = $pfee;
                // $LoanCaseBillMain->pfee_recv = $pfee;

                // $sst = $pfee * 0.06;
                // $sst = number_format((float)$sst, 2, '.', '');

                // $LoanCaseBillMain->sst_recv = $sst;
            } else {
                $LoanCaseBillMain->pfee1_recv = $collected_amt;


                $collected_amt = 0;
            }

            if ($collected_amt >= 0) {
                if (($collected_amt - $pfee2) >= 0) {
                    $collected_amt = $collected_amt - $pfee2;
                    $LoanCaseBillMain->pfee2_recv = $pfee2;
                } else {
                    $LoanCaseBillMain->pfee2_recv = $collected_amt;
                    $collected_amt = 0;
                }
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

        if ($collected_amt >= 0) {
            $collected_amt = $collected_amt - $referral_a1;
            $collected_amt = $collected_amt - $referral_a2;
            $collected_amt = $collected_amt - $referral_a3;
            $collected_amt = $collected_amt - $referral_a4;
            $collected_amt = $collected_amt - $marketing;

            // $LoanCaseBillMain->uncollected = $collected_amt;
        }

        $LoanCaseBillMain->save();

        return response()->json(['status' => 1, 'data' => 'Updated bill details']);
    }


    public function generateReceipt(Request $request, $id, $case_id)
    {
        $status = 1;
        $message = '';
        $fileTemplateId = $request->input('template_id');
        $array = (array) $fileTemplateId;


        $parameter = Parameter::where('parameter_type', '=', 'template_file_path')->first();
        $template_path = 'app/documents/account_template/';

        $parameter = Parameter::where('parameter_type', '=', 'case_file_path')->first();
        $case_path = $parameter->parameter_value_1;

        // get loan caseref no
        $loanCase = LoanCase::where('id', '=', $case_id)->first();
        $case_ref_no = $loanCase->case_ref_no;

        $lawyer = Users::where('id', '=', $loanCase->lawyer_id)->first();
        $clerk = Users::where('id', '=', $loanCase->clerk_id)->first();


        //=====================================================


        $file_folder_name_temp = $case_path . 'file_case_' . $case_id . '/account';
        $file_folder_name_public = public_path($file_folder_name_temp);

        if (!File::isDirectory($file_folder_name_public)) {
            File::makeDirectory($file_folder_name_public, 0777, true, true);
        }

        // $documentTemplateFile = DB::table('document_template_file_main AS m')
        //     ->leftJoin('document_template_file_details AS d', 'm.id', '=', 'd.document_template_file_main_id')
        //     ->select('m.*', 'd.file_name')
        //     ->where('m.id', '=',  $array[$i])
        //     ->where('d.status', '=',  '1')
        //     ->first();

        $parameter = Parameter::where('parameter_type', '=', 'receipt_running_no')->first();


        $running_no = (int)$parameter->parameter_value_1 + 1;
        $parameter->parameter_value_1 = $running_no;
        $parameter->save();

        $genFileName = time() . '_' . 'receipt' . '.docx';
        $genFileNamepdf = time() . '_' . 'receipt' . '.pdf';

        $template_folder_name_temp = $template_path . 'receipt_template.docx';
        $file_folder_name_temp = $case_path . 'file_case_' . $case_id . '/account/' . $genFileName;
        $file_folder_name_temp_pdf = $case_path . 'file_case_' . $case_id . '/account/' . $genFileNamepdf;

        $domPdfPath = base_path('vendor/dompdf/dompdf');
        \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');

        // $templateWord = new \PhpOffice\PhpWord\TemplateProcessor($template_folder_name_temp);
        $templateWord = new TemplateProcessor($template_folder_name_temp);

        $templateWord->setValue('case_ref_no', $case_ref_no);

        // $LoanCaseTrust = LoanCaseTrust::where('id', '=', $id)->first();
        $LoanCaseTrust = VoucherMain::where('id', '=', $id)->first();

        if ($LoanCaseTrust->office_account_id != 0) {
            $OfficeBankAccount = OfficeBankAccount::where('id', '=', $LoanCaseTrust->office_account_id)->first();
            $templateWord->setValue('bank_account', $OfficeBankAccount->account_no);
        }

        $templateWord->setValue('amount',  number_format($LoanCaseTrust->total_amount, 2, ".", ","));
        $templateWord->setValue('payee_name', htmlspecialchars($LoanCaseTrust->payee));
        $templateWord->setValue('cheque_no', htmlspecialchars($LoanCaseTrust->transaction_id));
        $templateWord->setValue('date', htmlspecialchars($LoanCaseTrust->payment_date));
        $templateWord->setValue('receipt_no', htmlspecialchars($running_no));
        $templateWord->setValue('payment_desc', htmlspecialchars($LoanCaseTrust->remark));

        // $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);

        $amount_in_en = $this->numberTowords($LoanCaseTrust->total_amount);

        if ($amount_in_en == null) {
            $amount_in_en = "Zero";
        }


        $templateWord->setValue('amount_en',  strtoupper($amount_in_en));

        $templateWord->saveAs($file_folder_name_temp);


        // $Content = \PhpOffice\PhpWord\IOFactory::load($file_folder_name_temp);

        // $PDFWriter = \PhpOffice\PhpWord\IOFactory::createWriter($Content,'PDF');
        // $PDFWriter->save($file_folder_name_temp_pdf); 



        // return;


        return response()->json(['status' => $status, 'data' => $file_folder_name_temp]);
    }

    public function generateBillReceipt(Request $request, $id, $case_id)
    {
        $status = 1;
        $message = '';
        $fileTemplateId = $request->input('template_id');
        $array = (array) $fileTemplateId;


        $parameter = Parameter::where('parameter_type', '=', 'template_file_path')->first();
        $template_path = 'app/documents/account_template/';

        $parameter = Parameter::where('parameter_type', '=', 'case_file_path')->first();
        $case_path = $parameter->parameter_value_1;

        // get loan caseref no
        $loanCase = LoanCase::where('id', '=', $case_id)->first();
        $case_ref_no = $loanCase->case_ref_no;

        $lawyer = Users::where('id', '=', $loanCase->lawyer_id)->first();
        $clerk = Users::where('id', '=', $loanCase->clerk_id)->first();


        //=====================================================


        $file_folder_name_temp = $case_path . 'file_case_' . $case_id . '/account';
        $file_folder_name_public = public_path($file_folder_name_temp);

        if (!File::isDirectory($file_folder_name_public)) {
            File::makeDirectory($file_folder_name_public, 0777, true, true);
        }

        // $documentTemplateFile = DB::table('document_template_file_main AS m')
        //     ->leftJoin('document_template_file_details AS d', 'm.id', '=', 'd.document_template_file_main_id')
        //     ->select('m.*', 'd.file_name')
        //     ->where('m.id', '=',  $array[$i])
        //     ->where('d.status', '=',  '1')
        //     ->first();


        $parameter = Parameter::where('parameter_type', '=', 'receipt_running_no')->first();


        $running_no = (int)$parameter->parameter_value_1 + 1;
        $parameter->parameter_value_1 = $running_no;
        $parameter->save();

        $genFileName = time() . '_' . 'receipt' . '.docx';
        $genFileNamepdf = time() . '_' . 'receipt' . '.pdf';

        $template_folder_name_temp = $template_path . 'receipt_template.docx';
        $file_folder_name_temp = $case_path . 'file_case_' . $case_id . '/account/' . $genFileName;
        $file_folder_name_temp_pdf = $case_path . 'file_case_' . $case_id . '/account/' . $genFileNamepdf;

        $domPdfPath = base_path('vendor/dompdf/dompdf');
        \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');

        // $templateWord = new \PhpOffice\PhpWord\TemplateProcessor($template_folder_name_temp);
        $templateWord = new TemplateProcessor($template_folder_name_temp);

        $templateWord->setValue('case_ref_no', $case_ref_no);

        // $LoanCaseTrust = LoanCaseTrust::where('id', '=', $id)->first();

        $bill_disburse = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->select('vd.*', 'a.name as account_name', 'vm.transaction_id as transaction_id',  'vm.id as voucher_id', 'vm.voucher_no', 'vm.remark as remark', 'vm.office_account_id as v_office_account_id', 'vm.payee as payee', 'vm.payment_date as payment_date')
            ->where('vm.id', '=',  $id)
            ->where('vd.status', '=',  4)
            ->first();

        // $bill_disburse = DB::table('voucher_main AS vm')
        // ->join('voucher_details AS vd', 'vd.voucher_main_id', '=', 'vm.id')
        // // ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
        // // ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
        // ->select('vd.*', 'vm.id as voucher_id', 'vm.voucher_no', 'vm.remark as remark','vm.office_account_id as v_office_account_id', 'vm.payee as payee', 'vm.payment_date as payment_date')
        // ->where('vm.id', '=',  $id)
        // ->where('vd.status', '<>',  4)
        // ->get();

        // $bill_disburse = DB::table('voucher_main AS vm')
        // ->select('vm.*')
        // ->where('vm.id', '=',  $id)
        // ->get();


        if ($bill_disburse->v_office_account_id != 0) {
            $OfficeBankAccount = OfficeBankAccount::where('id', '=', $bill_disburse->v_office_account_id)->first();
            $templateWord->setValue('bank_account', $OfficeBankAccount->account_no);
        } else {

            $templateWord->setValue('bank_account', '');
        }

        $templateWord->setValue('amount',  number_format($bill_disburse->amount, 2, ".", ","));
        $templateWord->setValue('payee_name', htmlspecialchars($bill_disburse->payee));
        $templateWord->setValue('cheque_no', htmlspecialchars($bill_disburse->transaction_id));
        $templateWord->setValue('date', htmlspecialchars($bill_disburse->payment_date));
        $templateWord->setValue('receipt_no', htmlspecialchars($running_no));
        $templateWord->setValue('payment_desc', htmlspecialchars($bill_disburse->remark));

        // $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);

        $amount_in_en = $this->numberTowords($bill_disburse->amount);

        if ($amount_in_en == null) {
            $amount_in_en = "Zero";
        }


        $templateWord->setValue('amount_en',  strtoupper($amount_in_en));

        $templateWord->saveAs($file_folder_name_temp);


        // $Content = \PhpOffice\PhpWord\IOFactory::load($file_folder_name_temp);

        // $PDFWriter = \PhpOffice\PhpWord\IOFactory::createWriter($Content,'PDF');
        // $PDFWriter->save($file_folder_name_temp_pdf); 



        // return;


        return response()->json(['status' => $status, 'data' => $file_folder_name_temp]);
    }

    public function generateTrustReceipt(Request $request, $case_id)
    {
        $status = 1;
        $message = '';
        $fileTemplateId = $request->input('template_id');
        $array = (array) $fileTemplateId;


        $parameter = Parameter::where('parameter_type', '=', 'template_file_path')->first();
        $template_path = 'app/documents/account_template/';

        $parameter = Parameter::where('parameter_type', '=', 'case_file_path')->first();
        $case_path = $parameter->parameter_value_1;

        // get loan caseref no
        $loanCase = LoanCase::where('id', '=', $case_id)->first();
        $case_ref_no = $loanCase->case_ref_no;

        $lawyer = Users::where('id', '=', $loanCase->lawyer_id)->first();
        $clerk = Users::where('id', '=', $loanCase->clerk_id)->first();
        //=====================================================

        $file_folder_name_temp = $case_path . 'file_case_' . $case_id . '/account';
        $file_folder_name_public = public_path($file_folder_name_temp);

        if (!File::isDirectory($file_folder_name_public)) {
            File::makeDirectory($file_folder_name_public, 0777, true, true);
        }

        // $documentTemplateFile = DB::table('document_template_file_main AS m')
        //     ->leftJoin('document_template_file_details AS d', 'm.id', '=', 'd.document_template_file_main_id')
        //     ->select('m.*', 'd.file_name')
        //     ->where('m.id', '=',  $array[$i])
        //     ->where('d.status', '=',  '1')
        //     ->first();

        $parameter = Parameter::where('parameter_type', '=', 'receipt_running_no')->first();


        $running_no = (int)$parameter->parameter_value_1 + 1;
        $parameter->parameter_value_1 = $running_no;
        $parameter->save();

        $genFileName = time() . '_' . 'receipt' . '.docx';
        $genFileNamepdf = time() . '_' . 'receipt' . '.pdf';

        $template_folder_name_temp = $template_path . 'receipt_template.docx';
        $file_folder_name_temp = $case_path . 'file_case_' . $case_id . '/account/' . $genFileName;
        $file_folder_name_temp_pdf = $case_path . 'file_case_' . $case_id . '/account/' . $genFileNamepdf;

        $domPdfPath = base_path('vendor/dompdf/dompdf');
        \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');

        // $templateWord = new \PhpOffice\PhpWord\TemplateProcessor($template_folder_name_temp);
        $templateWord = new TemplateProcessor($template_folder_name_temp);

        $templateWord->setValue('case_ref_no', $case_ref_no);

        $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $case_id)->first();

        if ($LoanCaseTrustMain->office_account_id != 0) {
            $OfficeBankAccount = OfficeBankAccount::where('id', '=', $LoanCaseTrustMain->office_account_id)->first();
            $templateWord->setValue('bank_account', $OfficeBankAccount->account_no);
        }

        $templateWord->setValue('amount',  number_format($LoanCaseTrustMain->total_received, 2, ".", ","));
        $templateWord->setValue('payee_name', htmlspecialchars($LoanCaseTrustMain->payee));
        $templateWord->setValue('cheque_no', htmlspecialchars($LoanCaseTrustMain->transaction_id));
        $templateWord->setValue('date', htmlspecialchars($LoanCaseTrustMain->payment_date));
        $templateWord->setValue('receipt_no', htmlspecialchars($running_no));
        $templateWord->setValue('payment_desc', htmlspecialchars($LoanCaseTrustMain->remark));

        // $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);

        $amount_in_en = $this->numberTowords($LoanCaseTrustMain->total_received);

        if ($amount_in_en == null) {
            $amount_in_en = "Zero";
        }


        $templateWord->setValue('amount_en',  strtoupper($amount_in_en));

        $templateWord->saveAs($file_folder_name_temp);


        // $Content = \PhpOffice\PhpWord\IOFactory::load($file_folder_name_temp);

        // $PDFWriter = \PhpOffice\PhpWord\IOFactory::createWriter($Content,'PDF');
        // $PDFWriter->save($file_folder_name_temp_pdf); 



        // return;


        return response()->json(['status' => $status, 'data' => $file_folder_name_temp]);
    }

    function convertQuotationToInvoice(Request $request, $id)
    {
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        $LoanCaseBillMain->bln_invoice = 1;
        $LoanCaseBillMain->save();

     

        return response()->json(['status' => 1, 'message' => 'Converted to Invoice']);
    }



    public function updateQuotationBillByAdmin(Request $request)
    {
        $status = 1;
        $need_approval = 0;
        $totalAmount = 0;
        $message = 'Updated the amount';
        $billList = [];
        $count = 0;

        if ($request->input('bill_list') != null) {
            $billList = json_decode($request->input('bill_list'), true);
        }

        $current_user = auth()->user();

        // if (count($billList) > 0) {

        //     for ($i = 0; $i < count($billList); $i++) {

        //         // $LoanCaseBillDetails = LoanCaseBillDetails::where('id', '=', $billList[$i]['id'])->first();

        //         $VoucherDetails = VoucherDetails::where('account_details_id', '=', $billList[$i]['id'])->first();

        //         if ($VoucherDetails != null)
        //         {
        //             $count += 1;
        //         }
        //     }
        // }

        // if ($count > 0)
        // {
        //     return response()->json(['status' => 0, 'message' => 'Disbursement exist']);
        // }



        if (count($billList) > 0) {

            for ($i = 0; $i < count($billList); $i++) {

                $LoanCaseBillDetails = LoanCaseBillDetails::where('id', '=', $billList[$i]['id'])->first();

                $LoanCaseBillDetails->quo_amount = $billList[$i]['amount'];
                $LoanCaseBillDetails->updated_at = date('Y-m-d H:i:s');
                $LoanCaseBillDetails->save();
            }
        }

        return response()->json(['status' => $status, 'message' => $message]);
    }

    public function addQuotationItem(Request $request, $id)
    {
        $loanCaseBillDetails = new LoanCaseBillDetails();

        $loanCaseBillDetails->loan_case_main_bill_id = $id;
        $loanCaseBillDetails->account_item_id = $request->input('details_id');
        $loanCaseBillDetails->min = 0;
        $loanCaseBillDetails->max = 0;
        $loanCaseBillDetails->need_approval = 0;
        if ($request->input('catID') == 1) {
            $loanCaseBillDetails->quo_amount = (float)$request->input('NewAmount')  * 1.06;
            $loanCaseBillDetails->amount = (float)$request->input('NewAmount')  * 1.06;
        } else {
            $loanCaseBillDetails->quo_amount = (float)$request->input('NewAmount');
            $loanCaseBillDetails->amount = (float)$request->input('NewAmount');

        }

        $loanCaseBillDetails->quo_amount_no_sst = $request->input('NewAmount');
        $loanCaseBillDetails->status = 1;
        $loanCaseBillDetails->created_at = date('Y-m-d H:i:s');
        $loanCaseBillDetails->save();


        // update all vaue
        $sumTotalAmount = 0;
            $sumTotalAmountCase = 0;
            $case_id = 0;

            $LoanCaseBillDetails = LoanCaseBillDetails::where('loan_case_main_bill_id', '=', $id)->get();

            for ($i = 0; $i < count($LoanCaseBillDetails); $i++) {
                $sumTotalAmount += $LoanCaseBillDetails[$i]->amount;
            }


            $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();
            $LoanCaseBillMain->total_amt = $sumTotalAmount;
            $case_id = $LoanCaseBillMain->case_id;
            $LoanCaseBillMain->save();

            if ($case_id != 0 && $case_id != null) {
                $LoanCaseBillMain = LoanCaseBillMain::where('case_id', '=', $case_id)->get();

                for ($i = 0; $i < count($LoanCaseBillMain); $i++) {
                    $sumTotalAmountCase += $LoanCaseBillMain[$i]->total_amt;
                }

                $LoanCase = LoanCase::where('id', '=', $case_id)->first();
                $LoanCase->targeted_bill = $sumTotalAmountCase;
                $LoanCase->save();
            }
            $AccountItem = AccountItem::where('id', '=', $request->input('details_id'))->first();

            

            $current_user = auth()->user();
            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = $case_id;
            $AccountLog->bill_id = $id;
            $AccountLog->ori_amt = 0;
            $AccountLog->new_amt = $request->input('NewAmount');
            $AccountLog->action = 'Add';
            $AccountLog->desc = $current_user->name . ' add new item ('.$AccountItem->name.') for ' . $request->input('NewAmount');
            $AccountLog->status = 1;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();

            return response()->json(['status' => 1, 'message' => 'yes']);
    }

    public function updateQuotationValue(Request $request)
    {
        $new_quo_amount = 0;
        $new_amount = 0;
        $addtional_value = 0;
        $main_bill_id = 0;

        if ($request->input('typeID') ==2)
        {
            $LoanCaseBillDetails = LoanCaseBillDetails::where('id', '=', $request->input('details_id'))->first();
            
            if ($request->input('catID') == 1) {
                $new_quo_amount =  (float)$request->input('NewAmount')  * 1.06;
                $LoanCaseBillDetails->invoice_amount = $new_quo_amount;
            } else {
                $new_quo_amount = $request->input('NewAmount');
                $LoanCaseBillDetails->invoice_amount = $new_quo_amount;
            }
            $LoanCaseBillDetails->invoice_amount_no_sst = $request->input('NewAmount');
            $LoanCaseBillDetails->save();

            
            return response()->json(['status' => 1, 'message' => 'yes']);
        }


        if ($request->input('NewAmount') != null) {
            $LoanCaseBillDetails = LoanCaseBillDetails::where('id', '=', $request->input('details_id'))->first();

            if ($LoanCaseBillDetails) {
                $main_bill_id = $LoanCaseBillDetails->loan_case_main_bill_id;
                $quo_amount_no_sst = $LoanCaseBillDetails->quo_amount_no_sst;
                $quo_amount = $LoanCaseBillDetails->quo_amount;
                $amount = $LoanCaseBillDetails->amount;

                if ($request->input('catID') == 1) {
                    $new_quo_amount =  (float)$request->input('NewAmount')  * 1.06;
                    // $new_quo_amount = $request->input('NewAmount');
                    $addtional_value = $new_quo_amount - $quo_amount;
                } else {
                    $new_quo_amount = $request->input('NewAmount');
                    $addtional_value = $new_quo_amount - $quo_amount_no_sst;
                }

                if ($new_quo_amount > $quo_amount_no_sst) {
                    
                    $addtional_value = $request->input('NewAmount') - $quo_amount;
                    $LoanCaseBillDetails->quo_amount_no_sst = $request->input('NewAmount');
                    $LoanCaseBillDetails->quo_amount = $new_quo_amount;
                    $LoanCaseBillDetails->amount = $LoanCaseBillDetails->amount + $addtional_value;
                    $LoanCaseBillDetails->save();
                } else {
                    $VoucherDetails = VoucherDetails::where('account_details_id', '=', $request->input('details_id'))->get();
                    $voucherSum = 0;

                    if (count($VoucherDetails)  > 0) {
                        // for ($i = 0; $i < count($VoucherDetails); $i++) {
                        //     $voucherSum += $VoucherDetails[$i]->amount;
                        // }

                        // if ($new_quo_amount <= $voucherSum)
                        // {
                        //     // $addtional_value = $new_quo_amount - $quo_amount;
                        //     // $LoanCaseBillDetails->quo_amount_no_sst = $request->input('NewAmount');
                        //     // $LoanCaseBillDetails->quo_amount = $new_quo_amount;
                        //     // $LoanCaseBillDetails->amount = $LoanCaseBillDetails->amount - $addtional_value;
                        //     // $LoanCaseBillDetails->save();
                        //     return response()->json(['status' => 2, 'message' => $addtional_value]);
                        // }
                        // else
                        // {
                        //     return response()->json(['status' => 2, 'message' => 'yes']);
                        // }

                        return response()->json(['status' => 2, 'message' => 'This item already diburse']);
                    } else {
                        $addtional_value = $request->input('NewAmount') - $quo_amount;
                        $LoanCaseBillDetails->quo_amount_no_sst = $request->input('NewAmount');
                        $LoanCaseBillDetails->quo_amount = $new_quo_amount;
                        $LoanCaseBillDetails->amount = $request->input('NewAmount');
                        $LoanCaseBillDetails->save();
                    }
                }
            }

            // update all value
            $sumTotalAmount = 0;
            $sumTotalAmountCase = 0;
            $case_id = 0;

            $LoanCaseBillDetails = LoanCaseBillDetails::where('loan_case_main_bill_id', '=', $main_bill_id)->get();

            for ($i = 0; $i < count($LoanCaseBillDetails); $i++) {
                $sumTotalAmount += $LoanCaseBillDetails[$i]->amount;
            }


            $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $main_bill_id)->first();
            $LoanCaseBillMain->total_amt = $sumTotalAmount;
            $case_id = $LoanCaseBillMain->case_id;
            $LoanCaseBillMain->save();

            if ($case_id != 0 && $case_id != null) {
                $LoanCaseBillMain = LoanCaseBillMain::where('case_id', '=', $case_id)->get();

                for ($i = 0; $i < count($LoanCaseBillMain); $i++) {
                    $sumTotalAmountCase += $LoanCaseBillMain[$i]->total_amt;
                }

                $LoanCase = LoanCase::where('id', '=', $case_id)->first();
                $LoanCase->targeted_bill = $sumTotalAmountCase;
                $LoanCase->save();
            }

            $current_user = auth()->user();
            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = $case_id;
            $AccountLog->bill_id = $main_bill_id;
            $AccountLog->ori_amt = $quo_amount;
            $AccountLog->new_amt = $new_quo_amount;
            $AccountLog->bill_id = $main_bill_id;
            $AccountLog->action = 'Update';
            $AccountLog->desc = $current_user->name . ' update amount from ' . $quo_amount . ' to ' . $new_quo_amount;
            $AccountLog->status = 1;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();

            return response()->json(['status' => 1, 'message' => 'yes']);
        }
    }


    public function generateBillLumdReceipt(Request $request, $id, $case_id)
    {
        $status = 1;
        $message = '';
        $amount = $request->input('amount');
        // $array = (array) $fileTemplateId;



        $parameter = Parameter::where('parameter_type', '=', 'template_file_path')->first();
        $template_path = 'app/documents/account_template/';

        $parameter = Parameter::where('parameter_type', '=', 'case_file_path')->first();
        $case_path = $parameter->parameter_value_1;

        // get loan caseref no
        $loanCase = LoanCase::where('id', '=', $case_id)->first();
        $case_ref_no = $loanCase->case_ref_no;

        $lawyer = Users::where('id', '=', $loanCase->lawyer_id)->first();
        $clerk = Users::where('id', '=', $loanCase->clerk_id)->first();
        //=====================================================

        $file_folder_name_temp = $case_path . 'file_case_' . $case_id . '/account';
        $file_folder_name_public = public_path($file_folder_name_temp);

        if (!File::isDirectory($file_folder_name_public)) {
            File::makeDirectory($file_folder_name_public, 0777, true, true);
        }

        // $documentTemplateFile = DB::table('document_template_file_main AS m')
        //     ->leftJoin('document_template_file_details AS d', 'm.id', '=', 'd.document_template_file_main_id')
        //     ->select('m.*', 'd.file_name')
        //     ->where('m.id', '=',  $array[$i])
        //     ->where('d.status', '=',  '1')
        //     ->first();

        $parameter = Parameter::where('parameter_type', '=', 'receipt_running_no')->first();


        $running_no = (int)$parameter->parameter_value_1 + 1;
        $parameter->parameter_value_1 = $running_no;
        $parameter->save();

        $genFileName = time() . '_' . 'receipt' . '.docx';
        $genFileNamepdf = time() . '_' . 'receipt' . '.pdf';

        $template_folder_name_temp = $template_path . 'receipt_template.docx';
        $file_folder_name_temp = $case_path . 'file_case_' . $case_id . '/account/' . $genFileName;
        $file_folder_name_temp_pdf = $case_path . 'file_case_' . $case_id . '/account/' . $genFileNamepdf;

        $domPdfPath = base_path('vendor/dompdf/dompdf');
        \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');

        // $templateWord = new \PhpOffice\PhpWord\TemplateProcessor($template_folder_name_temp);
        $templateWord = new TemplateProcessor($template_folder_name_temp);

        $templateWord->setValue('case_ref_no', $case_ref_no);

        $LoanCaseTrustMain = LoanCaseBillMain::where('id', '=', $id)->first();

        if ($LoanCaseTrustMain->office_account_id != 0) {
            $OfficeBankAccount = OfficeBankAccount::where('id', '=', $LoanCaseTrustMain->office_account_id)->first();
            $templateWord->setValue('bank_account', $OfficeBankAccount->account_no);
        }

        $templateWord->setValue('amount',   number_format($amount, 2, ".", ","));
        $templateWord->setValue('payee_name', htmlspecialchars($LoanCaseTrustMain->payee));
        $templateWord->setValue('cheque_no', htmlspecialchars($LoanCaseTrustMain->transaction_id));
        $templateWord->setValue('date', htmlspecialchars($LoanCaseTrustMain->payment_date));
        $templateWord->setValue('receipt_no', htmlspecialchars($running_no));
        $templateWord->setValue('payment_desc', htmlspecialchars($LoanCaseTrustMain->remark));

        // $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);

        $amount_in_en = $this->numberTowords($amount);

        if ($amount_in_en == null) {
            $amount_in_en = "Zero";
        }


        $templateWord->setValue('amount_en',  strtoupper($amount_in_en));

        $templateWord->saveAs($file_folder_name_temp);


        // $Content = \PhpOffice\PhpWord\IOFactory::load($file_folder_name_temp);

        // $PDFWriter = \PhpOffice\PhpWord\IOFactory::createWriter($Content,'PDF');
        // $PDFWriter->save($file_folder_name_temp_pdf); 



        // return;


        return response()->json(['status' => $status, 'data' => $file_folder_name_temp]);
    }

    function getTrustValue($id)
    {
        // $LoanCaseTrust = LoanCaseTrust::where('id', '=', $id)->first();
        $LoanCaseTrust = VoucherMain::where('id', '=', $id)->first();


        return response()->json(['status' => 1, 'data' => $LoanCaseTrust]);
    }

    public function getBillReceive($id)
    {

        $bill_disburse = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->select('vm.*', 'vd.amount')
            ->where('vm.id', '=',  $id)
            ->where('vd.status', '=',  4)
            ->first();


        return response()->json(['status' => 1, 'data' => $bill_disburse]);
    }

    public function updateTrustValueV2(Request $request, $id)
    {
        // $loanCaseTrust = LoanCaseTrust::where('id', '=', $id)->first();

        $current_user = auth()->user();

        // $loanCaseTrust->payment_type =  $request->input('payment_type');
        // $loanCaseTrust->cheque_no =  $request->input('cheque_no');
        // $loanCaseTrust->bank_id =  $request->input('bank_id');
        // $loanCaseTrust->bank_account =  $request->input('bank_account');
        // $loanCaseTrust->payment_date =  $request->input('payment_date');
        // $loanCaseTrust->item_name =  $request->input('payee_name');
        // $loanCaseTrust->item_code =  $request->input('transaction_id');
        // $loanCaseTrust->voucher_no =  $request->input('voucher_no');
        // $loanCaseTrust->office_account_id =  $request->input('office_account_id');
        // $loanCaseTrust->remark =  $request->input('payment_desc');
        // // $loanCaseTrust->status =  1;
        // $loanCaseTrust->updated_by = $current_user->id;
        // $loanCaseTrust->updated_at = date('Y-m-d H:i:s');
        // $loanCaseTrust->save();

        $voucherMain = voucherMain::where('id', '=', $id)->first();

        $voucherMain->payment_type = $request->input('payment_type');
        $voucherMain->cheque_no = $request->input('cheque_no');
        $voucherMain->credit_card_no = $request->input('credit_card_no');
        $voucherMain->bank_id = $request->input('bank_id');
        $voucherMain->payee = $request->input('payee_name');
        $voucherMain->transaction_id = $request->input('transaction_id');
        // $voucherMain->updated_by = $current_user->id;
        $voucherMain->bank_account = $request->input('bank_account');
        $voucherMain->payment_date = $request->input('payment_date');
        $voucherMain->remark = $request->input('payment_desc');
        $voucherMain->total_amount = $request->input('amount');
        $voucherMain->office_account_id = $request->input('office_account_id');
        $voucherMain->updated_at = date('Y-m-d H:i:s');
        $voucherMain->save();


        $voucherDetails = VoucherDetails::where('voucher_main_id', '=', $id)->first();

        $voucherDetails->amount = $request->input('amount');
        $voucherDetails->payment_type = $request->input('payment_type');
        $voucherDetails->cheque_no = $request->input('cheque_no');
        $voucherDetails->credit_card_no = $request->input('credit_card_no');
        $voucherDetails->bank_id = $request->input('bank_id');
        $voucherDetails->bank_account = $request->input('bank_account');
        $voucherDetails->updated_at = date('Y-m-d H:i:s');
        $voucherDetails->save();

        $case_id = $voucherMain->case_id;
        $sumTrust = 0;

        $voucherDetails = VoucherDetails::where('case_id', '=', $case_id)->where('status', '=', 3)->get();

        for ($i = 0; $i < count($voucherDetails); $i++) {
            $sumTrust += $voucherDetails[$i]->amount;
        }

        $loanCase = LoanCaseTrustMain::where('case_id', '=', $case_id)->first();

        // $total_trust = (float)($loanCase->total_trust) + (float)($request->input('amount'));

        // $loanCase->collected_trust = $collected_trust;

        $loanCase->total_received = $sumTrust;
        $loanCase->updated_at = date('Y-m-d H:i:s');
        $loanCase->save();

        return response()->json(['status' => 1, 'data' => 'Data updated']);
    }

    public function updateBillReceiveValue(Request $request, $id)
    {
        $loanCaseTrust = VoucherMain::where('id', '=', $id)->first();

        $current_user = auth()->user();

        $loanCaseTrust->payment_type =  $request->input('payment_type');
        $loanCaseTrust->cheque_no =  $request->input('cheque_no');
        $loanCaseTrust->bank_id =  $request->input('bank_id');
        $loanCaseTrust->bank_account =  $request->input('bank_account');
        $loanCaseTrust->payment_date =  $request->input('payment_date');
        $loanCaseTrust->payee =  $request->input('payee_name');
        $loanCaseTrust->transaction_id =  $request->input('transaction_id');
        $loanCaseTrust->voucher_no =  $request->input('voucher_no');
        $loanCaseTrust->office_account_id =  $request->input('office_account_id');
        $loanCaseTrust->remark =  $request->input('payment_desc');
        // $loanCaseTrust->status =  1;
        // $loanCaseTrust->updated_by = $current_user->id;
        $loanCaseTrust->updated_at = date('Y-m-d H:i:s');
        $loanCaseTrust->save();

        return response()->json(['status' => 1, 'data' => 'Data updated']);
    }

    public function updateBillPrintDetail(Request $request, $id)
    {
        $loanCaseTrust = LoanCaseBillMain::where('id', '=', $id)->first();

        $current_user = auth()->user();

        $loanCaseTrust->payment_type =  $request->input('payment_type');
        $loanCaseTrust->cheque_no =  $request->input('cheque_no');
        $loanCaseTrust->bank_id =  $request->input('bank_id');
        $loanCaseTrust->bank_account =  $request->input('bank_account');
        $loanCaseTrust->payment_date =  $request->input('payment_date');
        $loanCaseTrust->payee =  $request->input('payee_name');
        $loanCaseTrust->transaction_id =  $request->input('transaction_id');
        // $loanCaseTrust->voucher_no =  $request->input('voucher_no');
        $loanCaseTrust->office_account_id =  $request->input('office_account_id');
        $loanCaseTrust->remark =  $request->input('payment_desc');
        // $loanCaseTrust->status =  1;
        // $loanCaseTrust->updated_by = $current_user->id;
        $loanCaseTrust->updated_at = date('Y-m-d H:i:s');
        $loanCaseTrust->save();

        return response()->json(['status' => 1, 'data' => 'Data updated']);
    }

    public function updateTrustValue(Request $request, $id)
    {
        $loanCaseTrust = LoanCaseTrust::where('id', '=', $id)->first();


        $current_user = auth()->user();

        $loanCaseTrust->payment_type =  $request->input('ddl_payment_type_trust_edit');
        $loanCaseTrust->cheque_no =  $request->input('txt_cheque_no_trust_edit');
        $loanCaseTrust->bank_id =  $request->input('txt_bank_name_trust_edit');
        $loanCaseTrust->bank_account =  $request->input('txt_bank_account_trust_edit');
        $loanCaseTrust->payment_date =  $request->input('voucher_payment_time_trust_edit');
        $loanCaseTrust->item_name =  $request->input('payment_name_edit');
        $loanCaseTrust->item_code =  $request->input('transaction_id_edit');
        $loanCaseTrust->voucher_no =  $request->input('txt_voucher_no_trust_edit');
        $loanCaseTrust->office_account_id =  $request->input('OfficeBankAccount_id_trust_edit');
        $loanCaseTrust->remark =  $request->input('payment_desc_edit');
        // $loanCaseTrust->status =  1;
        $loanCaseTrust->updated_by = $current_user->id;
        $loanCaseTrust->updated_at = date('Y-m-d H:i:s');
        $loanCaseTrust->save();

        return response()->json(['status' => 1, 'data' => 'Data updated']);
    }

    public function SaveSummaryInfo(Request $request, $id)
    {
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();


        $current_user = auth()->user();

        $LoanCaseBillMain->referral_a1_id =  $request->input('referral_name_1');
        $LoanCaseBillMain->referral_a1_payment_date =  $request->input('ref_a1_payment_date');
        $LoanCaseBillMain->referral_a1_trx_id =  $request->input('ref_a1_payment_trx_id');

        $LoanCaseBillMain->referral_a2_id =  $request->input('referral_name_2');
        $LoanCaseBillMain->referral_a2_payment_date =  $request->input('ref_a2_payment_date');
        $LoanCaseBillMain->referral_a2_trx_id =  $request->input('ref_a2_payment_trx_id');

        $LoanCaseBillMain->referral_a3_id =  $request->input('referral_name_3');
        $LoanCaseBillMain->referral_a3_payment_date =  $request->input('ref_a3_payment_date');
        $LoanCaseBillMain->referral_a3_trx_id =  $request->input('ref_a3_payment_trx_id');

        $LoanCaseBillMain->referral_a4_id =  $request->input('referral_name_4');
        $LoanCaseBillMain->referral_a4_payment_date =  $request->input('ref_a4_payment_date');
        $LoanCaseBillMain->referral_a4_trx_id =  $request->input('ref_a4_payment_trx_id');

        $LoanCaseBillMain->marketing_id =  $request->input('sales_id');
        $LoanCaseBillMain->marketing_payment_date =  $request->input('sales_payment_date');
        $LoanCaseBillMain->marketing_trx_id =  $request->input('sales_payment_trx_id');

        $LoanCaseBillMain->sst_payment_date =  $request->input('sales_payment_date');
        $LoanCaseBillMain->sst_trx_id =  $request->input('sales_payment_trx_id');

        $LoanCaseBillMain->pfee1_receipt_date =  $request->input('pfee1_receipt_date');
        $LoanCaseBillMain->pfee1_receipt_trx_id =  $request->input('pfee1_receipt_trx_id');

        $LoanCaseBillMain->pfee2_receipt_date =  $request->input('pfee2_receipt_date');
        $LoanCaseBillMain->pfee2_receipt_trx_id =  $request->input('pfee2_receipt_trx_id');
        // $loanCaseTrust->status =  1;
        // $LoanCaseBillMain->updated_by = $current_user->id;
        $LoanCaseBillMain->updated_at = date('Y-m-d H:i:s');
        $LoanCaseBillMain->save();

        return response()->json(['status' => 1, 'data' => 'Data updated']);
    }

    public function submitNotes(Request $request, $id)
    {
        

        $current_user = auth()->user();

        if ($request->input('note_type') == 1)
        {
            $LoanCaseNotes = new LoanCaseNotes();

            $LoanCaseNotes->case_id =  $id;
            $LoanCaseNotes->notes =  $request->input('notes_msg');
            $LoanCaseNotes->label =  '';
            $LoanCaseNotes->role =  $current_user->menuroles . "|admin|management";
            $LoanCaseNotes->created_at = date('Y-m-d H:i:s');
            $LoanCaseNotes->created_by = $current_user->id;
            $LoanCaseNotes->save();
        }
        else
        {
            $LoanCaseKivNotes = new LoanCaseKivNotes();

            $LoanCaseKivNotes->case_id =  $id;
            $LoanCaseKivNotes->notes =  $request->input('notes_msg');
            $LoanCaseKivNotes->label =  '';
            $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
            $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');
            
            $LoanCaseKivNotes->status =  1;
            $LoanCaseKivNotes->created_by = $current_user->id;
            $LoanCaseKivNotes->save();
        }

        

        return response()->json(['status' => 1, 'data' => 'Notes updated']);
    }



    public function deleteReceiptFile(Request $request)
    {

        $delete_path = $request->input('delete_path');
        if (File::exists(public_path($delete_path))) {
            File::delete(public_path($delete_path));
        }
    }

    function numberTowords($num)
    {
        $ones = array(
            1 => "one",
            2 => "two",
            3 => "three",
            4 => "four",
            5 => "five",
            6 => "six",
            7 => "seven",
            8 => "eight",
            9 => "nine",
            10 => "ten",
            11 => "eleven",
            12 => "twelve",
            13 => "thirteen",
            14 => "fourteen",
            15 => "fifteen",
            16 => "sixteen",
            17 => "seventeen",
            18 => "eighteen",
            19 => "nineteen"
        );
        $tens = array(
            1 => "ten",
            2 => "twenty",
            3 => "thirty",
            4 => "forty",
            5 => "fifty",
            6 => "sixty",
            7 => "seventy",
            8 => "eighty",
            9 => "ninety"
        );
        $hundreds = array(
            "hundred",
            "thousand",
            "million",
            "billion",
            "trillion",
            "quadrillion"
        );
        $num = number_format($num, 2, ".", ",");
        $num_arr = explode(".", $num);
        $wholenum = $num_arr[0];
        $decnum = $num_arr[1];
        $whole_arr = array_reverse(explode(",", $wholenum));
        krsort($whole_arr);
        $words = "";
        foreach ($whole_arr as $key => $i) {
            if ($i == 0) {
                continue;
            }
            if ($i < 20) {
                $words .= $ones[intval($i)];
            } elseif ($i < 100) {
                if (substr($i, 0, 1) == 0 && strlen($i) == 3) {
                    $words .= $tens[substr($i, 1, 1)];
                    if (substr($i, 2, 1) != 0) {
                        $words .= " " . $ones[substr($i, 2, 1)];
                    }
                } else {
                    $words .= $tens[substr($i, 0, 1)];
                    if (substr($i, 1, 1) != 0) {
                        $words .= " " . $ones[substr($i, 1, 1)];
                    }
                }
            } else {
                // $words .= $ones[substr($i,0,1)]." ".$hundreds[0].' and ';
                if (substr($i, 1, 1) != 0 || substr($i, 2, 1) != 0) {
                    $words .= $ones[substr($i, 0, 1)] . " " . $hundreds[0] . ' and ';
                } else {
                    $words .= $ones[substr($i, 0, 1)] . " " . $hundreds[0];
                }
                if (substr($i, 1, 2) < 20 && substr($i, 1, 1) != 0) {
                    $words .= " " . $ones[(substr($i, 1, 2))];
                } else {
                    if (substr($i, 1, 1) != 0) {
                        $words .= " " . $tens[substr($i, 1, 1)];
                    }
                    if (substr($i, 2, 1) != 0) {
                        $words .= " " . $ones[substr($i, 2, 1)];
                    }
                }
            }
            if ($key > 0) {
                $words .= " " . $hundreds[$key] . " ";
            }
        }
        // $words .= $unit ?? ' units';
        // if ($decnum > 0) {
        //     $words .= " and ";
        //     if ($decnum < 20) {
        //         $words .= $ones[intval($decnum)];
        //     } elseif ($decnum < 100) {
        //         $words .= $tens[substr($decnum, 0, 1)];
        //         if (substr($decnum, 1, 1) != 0) {
        //             $words .= " " . $ones[substr($decnum, 1, 1)];
        //         }
        //     }
        //     $words .= $subunit ?? ' subunits';
        // }
        return $words;
    }
}

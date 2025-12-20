<?php

namespace App\Http\Controllers;

use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\Users;
use App\Models\Banks;
use App\Models\Customer;
use App\Models\Parameter;
use App\Models\caseTemplate;
use App\Models\perm;
use Illuminate\Http\Request;
use App\Models\MenusLang;
use App\Http\Helper\Helper;
use App\Models\BonusRequestList;
use App\Models\Branch;
use App\Models\LoanCase;
use App\Models\LoanCaseBillMain;
use App\Models\RptCase;
use App\Models\VoucherMain;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $current_user = auth()->user();
        
        // Cache branch info for 1 hour
        $branchInfo = Cache::remember("branch_access_{$current_user->id}", 3600, function () {
            return BranchController::manageBranchAccess();
        });

        // Cache parameter for 1 hour
        $parameter = Cache::remember('case_file_path_parameter', 3600, function () {
            return Parameter::where('parameter_type', '=', 'case_file_path')->first();
        });
        $case_path = $parameter ? $parameter->parameter_value_1 : '';

        // Cache branches for 1 hour
        $Branch = Cache::remember('active_branches', 3600, function () {
            return Branch::where('status', 1)->get();
        });

        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        $date = Carbon::now()->subDays(7)->startOfDay();
        $Last7Days = Carbon::now()->subDays(7)->startOfDay();

        if($current_user->id ==22)
        {
            $date = Carbon::now()->subDays(30)->startOfDay();
        }

        // Cache access info for 5 minutes
        $accessInfo = Cache::remember("access_info_{$current_user->id}", 300, function () {
            return AccessController::manageAccess();
        });

        // Limit initial data load - load notes via AJAX for better performance
        // Load up to 100 notes initially
        $kiv_note = $this->getKivNotes($current_user, $date, $accessInfo, 100);

        // Limit initial data load - load notes via AJAX
        $pnc_note = $this->getPncNotes($current_user, $date, 100);
        
        // Limit marketing notes initial load
        $LoanMarketingNotes = [];
        if (in_array($current_user->menuroles, ['account', 'admin', 'sales', 'maker'])) {
            $LoanMarketingNotes = $this->getMarketingNotes($current_user, $Last7Days, $accessInfo, 100);
        }

        // Load case files via AJAX for better performance - limit initial load
        $LoanAttachmentFrame = $this->getCaseFiles($current_user, $date, $branchInfo, 100);

        // Case counts will be loaded via AJAX for better performance
        // Initial load with minimal data
        $InProgressCaseCount = 0;
        $openCaseCount = 0;
        $closedCaseCount = 0;
        $OverdueCaseCount = 0;

        // Load these via AJAX for better performance
        $B2022PendingCloseCases = 0;
        $B2022AllCases = 0;
        $B2022ClosedCases = 0;
        $B2022ActiveCases = 0;
        $totalAcount = 0;
        $totalAssigned = 0;
        $totalUpdated = 0;
        $BonusRequestList = [];
        $LoanCaseChecklistDetails = [];
        $cases = [];

        // Get today message count - no caching
        $today_message_count = $this->getTodayMessageCount($current_user);


        return view('dashboard.home', [
            'current_user' => $current_user,
            'cases' => $cases,
            'Branch' => $Branch,
            'kiv_note' => $kiv_note,
            'pnc_note' => $pnc_note,
            'LoanMarketingNotes' => $LoanMarketingNotes,
            'today_message_count' => $today_message_count,
            'case_file' => $LoanAttachmentFrame,
            'case_path' => $case_path,
            'LoanCaseChecklistDetails' => $LoanCaseChecklistDetails,
            'OverdueCaseCount' => $OverdueCaseCount,
            'totalAssigned' => $totalAssigned,
            'totalAcount' => $totalAcount,
            'totalUpdated' => $totalUpdated,
            'B2022AllCases' => $B2022AllCases,
            'B2022ClosedCases' => $B2022ClosedCases,
            'B2022ActiveCases' => $B2022ActiveCases,
            'BonusRequestList' => $BonusRequestList,
            'B2022PendingCloseCases' => $B2022PendingCloseCases
        ]);
    }

    /**
     * Get KIV notes with permission control
     */
    private function getKivNotes($current_user, $date, $accessInfo, $limit = null)
    {
        $query = DB::table('loan_case_kiv_notes as n')
            ->join('loan_case as l', 'l.id', '=', 'n.case_id')
            ->join('users as u', 'u.id', '=', 'n.created_by')
            ->where('n.status', '=', 1)
            ->where('l.status', '<>', 99)
            ->select('n.*', 'l.case_ref_no', 'u.name as name', 'u.name as user_name', 'u.menuroles')
            ->where('n.created_at', '>=', $date);

        if (in_array($current_user->menuroles, ['clerk', 'lawyer', 'chambering'])) {
            $query = $query->where(function ($q) use ($current_user) {
                $q->where('l.lawyer_id', $current_user->id)
                    ->orWhere('l.clerk_id', $current_user->id);
            });
        } elseif (in_array($current_user->menuroles, ['sales'])) {
            if (in_array($current_user->id, [51,127])) {
                $query = $query->whereIn('l.sales_user_id', [32, 51,127]);
            } else if (in_array($current_user->id, [144])) {
                $query = $query->whereIn('l.sales_user_id', [$current_user->id, 29]);
            } else {
                $query = $query->where('l.sales_user_id', '=', $current_user->id);
            }
        }

        $query = $query->where(function ($q) use ($accessInfo) {
            $q->whereIn('l.branch_id',  $accessInfo['brancAccessList'])
                ->orWhereIn('sales_user_id', $accessInfo['user_list'])
                ->orWhereIn('clerk_id', $accessInfo['user_list'])
                ->orWhereIn('lawyer_id', $accessInfo['user_list']);
        });

        $query = $query->orderBy('n.created_at', 'DESC');
        
        if ($limit) {
            $query = $query->limit($limit);
        }
        
        return $query->get();
    }

    /**
     * Get PNC notes with permission control
     */
    private function getPncNotes($current_user, $date, $limit = null)
    {
        $query = DB::table('loan_case_pnc_notes as n')
            ->join('loan_case as l', 'l.id', '=', 'n.case_id')
            ->join('users as u', 'u.id', '=', 'n.created_by')
            ->where('n.status', '=', 1)
            ->where('l.status', '<>', 99)
            ->select('n.*', 'l.case_ref_no', 'u.name as name', 'u.name as user_name', 'u.menuroles')
            ->where('n.created_at', '>=', $date)
            ->orderBy('n.created_at', 'DESC');
            
        if ($limit) {
            $query = $query->limit($limit);
        }
        
        return $query->get();
    }

    /**
     * Get marketing notes with permission control
     */
    private function getMarketingNotes($current_user, $Last7Days, $accessInfo, $limit = null)
    {
        $query = DB::table('loan_case_notes AS n')
            ->join('loan_case as l', 'l.id', '=', 'n.case_id')
            ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
            ->where('l.status', '<>', 99)
            ->where('n.status', '<>', 99)
            ->where('n.created_at', '>=', $Last7Days)
            ->select('n.*',  'l.case_ref_no', 'u.name as user_name', 'u.menuroles');

        if (in_array($current_user->menuroles, ['sales'])) {
            if (in_array($current_user->id, [51,127])) {
                $query = $query->whereIn('l.sales_user_id', [32, 51,127]);
            } else if (in_array($current_user->id, [144])) {
                $query = $query->whereIn('l.sales_user_id', [$current_user->id, 29]);
            } else {
                $query = $query->whereIn('l.sales_user_id', [$current_user->id]);
            }
        } else if (in_array($current_user->menuroles, ['maker'])) {
            $query = $query->where(function ($q) use ($accessInfo) {
                $q->whereIn('l.branch_id',  $accessInfo['brancAccessList'])
                    ->orWhereIn('sales_user_id', $accessInfo['user_list'])
                    ->orWhereIn('clerk_id', $accessInfo['user_list'])
                    ->orWhereIn('lawyer_id', $accessInfo['user_list']);
            });
        }

        $query = $query->orderBy('n.created_at', 'DESC');
        
        if ($limit) {
            $query = $query->limit($limit);
        }
        
        return $query->get();
    }

    /**
     * Get case files with permission control
     */
    private function getCaseFiles($current_user, $date, $branchInfo, $limit = null)
    {
        // Ensure date is in correct format for query - use start of day to include entire day
        if ($date instanceof \Carbon\Carbon) {
            $dateString = $date->copy()->startOfDay()->toDateTimeString();
        } else {
            $dateString = is_string($date) ? $date : Carbon::parse($date)->startOfDay()->toDateTimeString();
        }
        
        // Debug: Log the date being used (check storage/logs/laravel.log after page load)
        if (function_exists('logger')) {
            logger()->info("Dashboard getCaseFiles - Date filter: {$dateString}, Today: " . Carbon::now()->format('Y-m-d H:i:s'));
        }
        
        $case_file = DB::table('loan_attachment as a')
            ->join('loan_case as l', 'l.id', '=', 'a.case_id')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->where('a.status', '=', 1)
            ->where('a.created_at', '>=', $dateString)
            ->select(
                'a.id',
                'a.s3_file_name',
                'a.filename',
                'a.type',
                'a.receipt_done',
                'a.display_name',
                'a.attachment_type',
                'a.case_id',
                'a.remark',
                'a.created_at', 'l.case_ref_no', 'u.name as user_name');

        $case_file2 = DB::table('loan_case_account_files as a')
            ->join('loan_case as l', 'l.id', '=', 'a.case_id')
            ->join('users as u', 'u.id', '=', 'a.created_by')
            ->where('a.status', '=', 1)
            ->where('a.created_at', '>=', $dateString)
            ->select(
                'a.id',
                'a.s3_file_name',
                'a.ori_name as display_name',
                'a.ori_name as filename',
                'a.type',
                'a.receipt_done',
                'a.ori_name',
                'a.type as attachment_type',
                'a.case_id',
                'a.remarks as remark',
                'a.created_at', 'l.case_ref_no', 'u.name as user_name');

        if ($current_user->branch_id == 3) {
            $case_file = $case_file->where('l.branch_id', '=', $current_user->branch_id);
            $case_file2 = $case_file2->where('l.branch_id', '=', $current_user->branch_id);
        }

        if ($current_user->menuroles == 'lawyer' || $current_user->menuroles == 'chambering') {
            $case_file = $case_file->where('l.lawyer_id', '=', $current_user->id)
                ->whereNotIn('a.attachment_type', [5]);
            $case_file2 = $case_file2->where('l.lawyer_id', '=', $current_user->id);
        } else if ($current_user->menuroles == 'clerk') {
            $case_file = $case_file->where('l.clerk_id', '=', $current_user->id)
                ->whereNotIn('a.attachment_type', [5]);
            $case_file2 = $case_file2->where('l.clerk_id', '=', $current_user->id);
        } else if ($current_user->menuroles == 'sales') {
            if (in_array($current_user->id, [51,127])) {
                $case_file = $case_file->whereIn('l.sales_user_id', [32, 51,127])
                    ->whereNotIn('a.attachment_type', [5]);
                $case_file2 = $case_file2->whereIn('l.sales_user_id', [32, 51,127]);
            } else if (in_array($current_user->id, [144])) {
                $case_file = $case_file->whereIn('l.sales_user_id', [$current_user->id, 29])
                    ->whereNotIn('a.attachment_type', [5]);
                $case_file2 = $case_file2->whereIn('l.sales_user_id', [$current_user->id, 29]);
            } else {
                $case_file = $case_file->where('l.sales_user_id', '=', $current_user->id)
                    ->whereNotIn('a.attachment_type', [5]);
                $case_file2 = $case_file2->where('l.sales_user_id', '=', $current_user->id);
            }
        } else if ($current_user->menuroles == 'maker') {
            if (in_array($current_user->branch_id, [2])) {
                $case_file = $case_file->where('l.sales_user_id', 13);
                $case_file2 = $case_file2->where('l.sales_user_id', 13);
            } else {
                $case_file = $case_file->Where(function ($q) use ($branchInfo) {
                    $q->whereIn('l.branch_id', $branchInfo['brancAccessList'])->where('a.status', '<>','99');
                });
                $case_file2 = $case_file2->Where(function ($q) use ($branchInfo) {
                    $q->whereIn('l.branch_id', $branchInfo['brancAccessList'])->where('a.status', '<>','99');
                });
            }
        }

        $case_file = $case_file->orderBy('a.created_at', 'DESC');
        $case_file2 = $case_file2->orderBy('a.created_at', 'DESC');
        
        if ($limit) {
            $case_file = $case_file->limit($limit);
            $case_file2 = $case_file2->limit($limit);
        }
        
        $case_file = $case_file->get();
        $case_file2 = $case_file2->get();

        return $case_file->merge($case_file2)->sortByDesc('created_at');
    }

    /**
     * Get today's message count with permission control
     */
    private function getTodayMessageCount($current_user)
    {
        $query = DB::table('loan_case_kiv_notes as n')
            ->join('loan_case as l', 'l.id', '=', 'n.case_id')
            ->where('n.status', '=', 1)
            ->where('n.created_at', '>=', Carbon::today());

        if (in_array($current_user->menuroles,  ['lawyer', 'clerk', 'chambering','sales'])) {
            $query = $query->where(function ($q) use ($current_user) {
                $q->where('l.lawyer_id', $current_user->id)
                    ->orWhere('l.clerk_id', $current_user->id)
                    ->orWhere('l.sales_user_id', $current_user->id);
            });
        }

        if (in_array($current_user->menuroles, ['maker'])) {
            $query = $query->where(function ($q) use ($current_user) {
                $q->where('l.branch_id', '=', $current_user->branch_id);
            });
        }

        return $query->count();
    }

    public function getTodoList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();

            // $LoanCaseChecklistDetails = DB::table('loan_case_checklist_details as d')
            //     ->join('loan_case as l', 'l.id', '=', 'd.case_id')
            //     ->select('d.*', 'l.case_ref_no')
            //     ->where('d.pic_id', '=',  $current_user->id)
            //     ->where('d.status', '=',  0)
            //     ->whereDate('d.target_close_date', '=', Carbon::now())
            //     ->orderBy('d.target_close_date', 'ASC')
            //     ->get();

                 $LoanCaseChecklistDetails = [];



            return DataTables::of($LoanCaseChecklistDetails)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a target="_blank" href="/case/' . $row->case_id . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Download"><i class="cil-chevron-double-right"></i></a>
                   ';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function getDashboardCaseChart(Request $request)
    {
        $now = Carbon::now();
        $result = [];

        $current_user = auth()->user();

        // Get all case data for the specified year
        $RptCase = RptCase::where('fiscal_year', '=', $request->input('year'));

        if (in_array($current_user->id, [80, 38])) {
            $RptCase = $RptCase->where('branch_id', $current_user->branch_id);
        }

        if (in_array($current_user->id, [51, 32])) {
            $RptCase = $RptCase->where('branch_id', 5);
        }

        $RptCase = $RptCase->get();

        $all_count = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        for ($j = 0; $j < count($RptCase); $j++) {
            $all_count[$RptCase[$j]->fiscal_mon - 1] += $RptCase[$j]->count;
        }

        array_push($result, array('branch' => 'All', 'count' => $all_count));

        // Add monthly totals for verification
        array_push($result, array('branch' => 'Monthly Total', 'count' => $all_count));

        return response()->json(['status' => 1, 'data' => $result]);
    }

    public function getDashboardCaseChartByBranch(Request $request)
    {
        $now = Carbon::now();
        $result = [];

        $current_user = auth()->user();

        // Get all branches that have case data for the specified year
        $branchesWithData = RptCase::where('fiscal_year', '=', $request->input('year'))
            ->select('branch_id')
            ->distinct()
            ->pluck('branch_id')
            ->toArray();

        // Get branch information for branches with data
        if ($current_user->id == 80) {
            $Branch = Branch::where('id', '=', '3')->get();
        } else {
            // Use branches with data instead of just active branches
            $Branch = Branch::whereIn('id', $branchesWithData)->get();
        }

        // Calculate monthly totals for verification
        $monthlyTotals = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        if (count($Branch) > 0) {
            for ($i = 0; $i < count($Branch); $i++) {
                $case_count_per_year = [];

                for ($j = 0; $j < 12; $j++) {
                    $monthData = RptCase::where('fiscal_year', '=', $request->input('year'))
                        ->where('branch_id', '=', $Branch[$i]->id)
                        ->where('fiscal_mon', '=', ($j+1))
                        ->first();
                    
                    $count = 0;
                    if($monthData) {
                        $count = $monthData->count;
                    }

                    array_push($case_count_per_year, $count);
                    $monthlyTotals[$j] += $count; // Add to monthly totals
                }

                array_push($result, array('branch' => $Branch[$i]->name, 'count' => $case_count_per_year));
            }
        }

        // Add monthly totals to the response
        array_push($result, array('branch' => 'Monthly Total', 'count' => $monthlyTotals));

        return response()->json(['status' => 1, 'data' => $result]);
    }

    public function getDashboardCaseChartByStaff(Request $request)
    {
        $now = Carbon::now();
        $result = [];

        $staffList = [];
        $lawyerList = [];
        $clerkList = [];
        $count = [];
        $lawyercount = [];
        $clerkcount = [];
        $staff = [];

        $current_user = auth()->user();


        if ($request->input("role") == 'lawyer') {
            $lawyer = Users::where('status', 1)->where('status', '<>', 88)->whereIn('menuroles', ['lawyer', 'chambering'])->orderBy('name', 'asc');
        } else if ($request->input("role") == 'clerk') {
            $lawyer = Users::where('status', 1)->where('status', '<>', 88)->whereIn('menuroles', ['clerk'])->orderBy('name', 'asc');
        } else {
            $lawyer = Users::where('status', 1)->where('status', '<>', 88)->whereIn('menuroles', ['lawyer', 'chambering', 'clerk'])->orderBy('name', 'asc');
        }

        if (in_array($current_user->id, [80, 89, 38])) {
            $lawyer = $lawyer->where('branch_id', $current_user->branch_id);
        }


        if (in_array($current_user->menuroles, ['sales'])) {
            $branch_list = [];
            $sales_user_id = $current_user->id;

            if (in_array($current_user->id, [144,127])) {
                $sales_user_id  = 32;
            }
            

            $branchInfo = BranchController::manageBranchAccess();
            // return $branchInfo['branch'];

            $branchs = $branchInfo['branch'];


            for ($i = 0; $i < count($branchs); $i++) {
                array_push($branch_list,  $branchs[$i]->id);
            }


            $lawyer = $lawyer->whereIn('branch_id', $branch_list);
        }

        if($request->input("branch") != 0)
        {
            $lawyer = $lawyer->where('branch_id', $request->input("branch"));
        }

        if (in_array($current_user->id, [51, 32,127])) {
            $lawyer = $lawyer->whereIn('branch_id', [1, 2, 5]);
        }

        $lawyer = $lawyer->get();

        



        for ($i = 0; $i < count($lawyer); $i++) {
            array_push($lawyerList,  $lawyer[$i]->name);

            $caseCount = LoanCase::whereMonth('created_at', $request->input("month"))
                ->where('status', '<>', 99)
                ->whereYear('created_at', $request->input("year"))
                ->where(function ($q) use ($lawyer, $i) {
                    $q->where('lawyer_id', $lawyer[$i]->id)
                        ->orWhere('clerk_id', $lawyer[$i]->id);
                });

                // return $caseCount->count();
                
            if($request->input("branch") != 0)
            {
                $caseCount = $caseCount->where('branch_id',  $request->input("branch"));
            }

            if (in_array($current_user->menuroles, ['sales'])) {
                if (in_array($current_user->id, [144])) {
                    $caseCount = $caseCount->whereIn('sales_user_id', [$current_user->id,29]);
                }
                else if (in_array($current_user->id, [32])) {
                    // $caseCount = $caseCount->whereIn('sales_user_id', [$current_user->id,29]);
            $caseCount = $caseCount->whereIn('branch_id', [1, 2, 5]);
                }
                else
                {
                    $caseCount = $caseCount->where('sales_user_id', $current_user->id);
                }
            }

            $caseCount = $caseCount->count();



            array_push($lawyercount,  $caseCount);
        }

        return [
            'status' => 1,
            'staffList' => $staffList,
            'clerkList' => $clerkList,
            'lawyerList' => $lawyerList,
            'count' => $count,
            'lawyercount' => $lawyercount,
            'clerkcount' => $clerkcount,
        ];
    }

    public function getDashboardCaseChartBySales(Request $request)
    {
        $now = Carbon::now();
        $result = [];

        $salesList = [];
        $lawyerList = [];
        $clerkList = [];
        $count = [];
        $caseCount = [];
        $clerkcount = [];
        $staff = [];

        $current_user = auth()->user();

        // $sales2 = Users::where(function ($q) {
        //     $q->where('menuroles', '=', 'sales')
        //         ->where('id', '<>', 51)
        //         ->Where('id', '<>', 127)
        //         ->orWhere('id', '=', 2)
        //         ->orWhere('id', '=', 3)
        //         ->orWhere('id', '=', 13)
        //         ->orWhere('id', '=', 88)
        //         ->orWhere('id', '=', 89)
        //         ->orWhere('id', '=', 143)
        //         ->orWhere('id', '=', 118)
        //         ->orWhere('id', '=', 122);
        // });

        $sales2 = Users::where('is_sales', 1);


        if (in_array($current_user->menuroles, ['sales'])) {

            if (in_array($current_user->id, [51,127])) {
                $sales2 = $sales2->where('id', '=', 32);
            } elseif ($current_user->id == 80) {
                $sales2 = $sales2->where('branch_id', '=', 3);
            }elseif ($current_user->id == 32) {
                $sales2 = $sales2->where('branch_id',5);
            } else {
                $sales2 = $sales2->where('id', '=', $current_user->id);
            }
        }

        if($request->input("branch"))
        {
            $sales = $sales2->where('branch_id', $request->input("branch"));
        }
        
        $sales = $sales2->where('status', 1)->get();

        for ($i = 0; $i < count($sales); $i++) {
            array_push($salesList,  $sales[$i]->name);

            $LoanCaseCount = LoanCase::where('status', '<>', 99)->where('sales_user_id', '=', $sales[$i]->id)
            ->whereMonth('created_at', $request->input("month"))->whereYear('created_at', $request->input("year"))->count();

            array_push($caseCount,  $LoanCaseCount);
        }

        return [
            'status' => 1,
            'salesList' => $salesList,
            'caseCount' => $caseCount,
        ];
    }

    public function getDashboardReport(Request $request)
    {
        $now = Carbon::now();
        
        $total_receipt = 0;
        $uncollected = 0;
        $bal_disb = 0;
        $actual_bal = 0;

        $current_user = auth()->user();





        // $total_receipt = DB::table('voucher_main as a')
        //     ->leftJoin('loan_case as b', 'a.case_id', '=', 'b.id')
        //     ->where('voucher_type', 4)
        //     ->where('a.status',  '<>', 99)
        //     ->whereMonth('payment_date', $request->input("month"))
        //     ->whereYear('payment_date', $request->input("year")); 

        // if($request->input("branch") != 0)
        // {
        //     $total_receipt = $total_receipt->where('b.branch_id', $request->input("branch"));
        // }

        // $total_receipt = $total_receipt->sum('total_amount');

        


        $total_trust_receive = DB::table('voucher_main as a')
        ->leftJoin('loan_case as b', 'a.case_id', '=', 'b.id')
        ->where('voucher_type', 3)
        ->where('a.status',  '<>', 99)
        ->whereMonth('payment_date', $request->input("month"))
        ->whereYear('payment_date', $request->input("year")); 

        if($request->input("branch") != 0)
        {
            $total_trust_receive = $total_trust_receive->where('b.branch_id', $request->input("branch"));
        }

        $total_trust_receive = $total_trust_receive->sum('total_amount');


        // $uncollected = LoanCaseBillMain::where('status', '<>', 99)
        //     ->where('status', '<>', 99) 
        //     ->whereMonth('created_at', $request->input("month"))
        //     ->whereYear('created_at', $request->input("year"))->sum('uncollected');


        $uncollected = DB::table('loan_case_bill_main as a')
        ->leftJoin('loan_case as b', 'a.case_id', '=', 'b.id')
        ->where('a.status',  '<>', 99)
        ->whereMonth('a.created_at', $request->input("month"))
        ->whereYear('a.created_at', $request->input("year")); 

        if($request->input("branch") != 0)
        {
            $uncollected = $uncollected->where('b.branch_id', $request->input("branch"));
        }

        $uncollected = $uncollected->sum('uncollected');

        $disb = LoanCaseBillMain::where('status', '<>', 99)
            ->where('status', '<>', 99)
            ->whereMonth('created_at', $request->input("month"))
            ->whereYear('created_at', $request->input("year"))->sum('disb');


        $disb = DB::table('loan_case_bill_main as a')
            ->leftJoin('loan_case as b', 'a.case_id', '=', 'b.id')
            ->where('a.status',  '<>', 99)
            ->whereMonth('a.created_at', $request->input("month"))
            ->whereYear('a.created_at', $request->input("year")); 
    
        if($request->input("branch") != 0)
        {
            $disb = $disb->where('b.branch_id', $request->input("branch"));
        }

        $disb = $disb->sum('disb');


        // $disb = DB::table('voucher_main as a')
        // ->leftJoin('loan_case as b', 'a.case_id', '=', 'b.id')
        // ->where('voucher_type', 1)
        // ->where('a.status',  '<>', 99)
        // ->whereIn('a.account_approval',  [1])
        // ->whereMonth('payment_date', $request->input("month"))
        // ->whereYear('payment_date', $request->input("year")); 

        // if($request->input("branch") != 0)
        // {
        //     $disb = $disb->where('b.branch_id', $request->input("branch"));
        // }

        // $disb = $disb->sum('total_amount');


        // $used_amt = LoanCaseBillMain::where('status', '<>', 99)
        //     ->whereMonth('created_at', $request->input("month"))
        //     ->whereYear('created_at', $request->input("year"))->sum('used_amt');


        $used_amt = DB::table('loan_case_bill_main as a')
            ->leftJoin('loan_case as b', 'a.case_id', '=', 'b.id')
            ->where('a.status',  '<>', 99)
            ->whereMonth('a.created_at', $request->input("month"))
            ->whereYear('a.created_at', $request->input("year")); 
    
        if($request->input("branch") != 0)
        {
            $used_amt = $used_amt->where('b.branch_id', $request->input("branch"));
        }

        $used_amt = $used_amt->sum('used_amt');


        $bal_disb = $disb - $used_amt;
        // $bal_disb = $disb;


        // $pfee1 = LoanCaseBillMain::where('status', '<>', 99)
        //     ->whereMonth('created_at', $request->input("month"))
        //     ->whereYear('created_at', $request->input("year"))->sum('pfee1');

        $pfee1 = DB::table('loan_case_bill_main as a')
            ->leftJoin('loan_case as b', 'a.case_id', '=', 'b.id')
            ->where('a.status',  '<>', 99)
            ->whereMonth('a.created_at', $request->input("month"))
            ->whereYear('a.created_at', $request->input("year")); 

        $LoanCaseBillMain = DB::table('loan_case_bill_main as a')
            ->leftJoin('loan_case as b', 'a.case_id', '=', 'b.id')
            ->where('a.status',  '<>', 99)
            ->where('a.bln_invoice',  1)
            ->whereMonth('a.invoice_date', $request->input("month"))
            ->whereYear('a.invoice_date', $request->input("year")); 


        if($request->input("branch") != 0)
        {
            $LoanCaseBillMain = $LoanCaseBillMain->where('b.branch_id', $request->input("branch"));
        }

        $pfee1_inv = $LoanCaseBillMain->sum('pfee1_inv');
        $pfee2_inv = $LoanCaseBillMain->sum('pfee2_inv');
        $referral_a1 = $LoanCaseBillMain->sum('referral_a1');
        $referral_a2 = $LoanCaseBillMain->sum('referral_a2');
        $referral_a3 = $LoanCaseBillMain->sum('referral_a3');
        $referral_a4 = $LoanCaseBillMain->sum('referral_a4');
        $marketing = $LoanCaseBillMain->sum('marketing');
        // $uncollected = $LoanCaseBillMain->sum('uncollected');
        $disb = $LoanCaseBillMain->sum('disb_inv');
        $used_amt = $LoanCaseBillMain->sum('used_amt');
        $total_receive_inv = $LoanCaseBillMain->sum('total_amt_inv');
        $total_staff_bonus_2_per = $LoanCaseBillMain->sum('total_staff_bonus_2_per');
        $total_staff_bonus_3_per = $LoanCaseBillMain->sum('total_staff_bonus_3_per');
        $total_receipt = $LoanCaseBillMain->sum('total_amt_inv');
        $total_sst = $LoanCaseBillMain->sum('sst_inv');

        // $bal_disb = $disb - $used_amt;
        $bal_disb = $disb;
        // $actual_bal = ($pfee1_inv + $pfee2_inv) - $referral_a1 - $referral_a2 - $referral_a3 - $referral_a4 - $marketing - $total_staff_bonus_2_per - $total_staff_bonus_3_per;
        $close_file_bal = $LoanCaseBillMain->sum('disb') - $LoanCaseBillMain->sum('used_amt') - $LoanCaseBillMain->sum('disb_amt_manual');
        $actual_bal = ($pfee1_inv + $pfee2_inv);
        // $close_file_bal = $LoanCaseBillMain->sum('disb_amt_manual');
        $close_file_bal = round(($LoanCaseBillMain->sum('disb')), 2) -  round(($LoanCaseBillMain->sum('used_amt')), 2) - round(($LoanCaseBillMain->sum('disb_amt_manual')), 2);

        
        $total_check = ($pfee1_inv + $pfee2_inv) + $bal_disb + $total_sst;
        // $total_receive_inv = ($pfee1_inv + $pfee2_inv) + $bal_disb + $total_sst;
        //==================================quotation 
        $LoanCaseBillMainQuotation = DB::table('loan_case_bill_main as a')
        ->leftJoin('loan_case as b', 'a.case_id', '=', 'b.id')
        ->where('a.status',  '<>', 99)
        ->where('a.bln_invoice',  0)
        ->whereMonth('a.created_at', $request->input("month"))
        ->whereYear('a.created_at', $request->input("year")); 

        
        if($request->input("branch") != 0)
        {
            $LoanCaseBillMainQuotation = $LoanCaseBillMainQuotation->where('b.branch_id', $request->input("branch"));
        }

        $pfee1 = $LoanCaseBillMainQuotation->sum('pfee1');
        $pfee2 = $LoanCaseBillMainQuotation->sum('pfee2');
        $referral_a1 = $LoanCaseBillMainQuotation->sum('referral_a1');
        $referral_a2 = $LoanCaseBillMainQuotation->sum('referral_a2');
        $referral_a3 = $LoanCaseBillMainQuotation->sum('referral_a3');
        $referral_a4 = $LoanCaseBillMainQuotation->sum('referral_a4');
        $marketing = $LoanCaseBillMainQuotation->sum('marketing');
        $uncollected = $LoanCaseBillMainQuotation->sum('uncollected');
        $disb = $LoanCaseBillMainQuotation->sum('disb');
        $used_amt = $LoanCaseBillMainQuotation->sum('used_amt');
        $total_receive_q = $LoanCaseBillMainQuotation->sum('collected_amt');
        $total_staff_bonus_2_per_q = $LoanCaseBillMainQuotation->sum('total_staff_bonus_2_per');
        $total_staff_bonus_3_per_q = $LoanCaseBillMainQuotation->sum('total_staff_bonus_3_per');


        $total_receive_q = $pfee1 + $pfee2;
        

        $actual_bal_q = ($pfee1 + $pfee2) - $referral_a1 - $referral_a2 - $referral_a3 - $referral_a4 - $marketing - $uncollected - $total_staff_bonus_2_per_q - $total_staff_bonus_3_per_q;
        $bal_disb_q = $disb - $used_amt;



        

        $SumBonus3Per = DB::table('bonus_request_records as a')
                ->leftJoin('bonus_request_list as b', 'a.bonus_request_list_id', '=', 'b.id')
                ->leftJoin('loan_case as c', 'b.case_id', '=', 'c.id')
                ->where('a.status', '=',  1)
                ->whereMonth('a.created_at', $request->input("month"))
                ->where('a.percentage', '=',  3)
                ->whereYear('a.created_at', $request->input("year"));

        if($request->input("branch") != 0)
        {
            $SumBonus3Per = $SumBonus3Per->where('c.branch_id', $request->input("branch"));
        }

        $SumBonus3Per = $SumBonus3Per->sum('amount'); 

        $SumBonus5Per = DB::table('bonus_request_records')
            ->where('status', '=',  1)
            ->whereMonth('created_at', $request->input("month"))
            ->where('percentage', '=',  5)
            ->whereYear('created_at', $request->input("year"))->sum('amount');

        $SumBonus5Per = DB::table('bonus_request_records as a')
            ->leftJoin('bonus_request_list as b', 'a.bonus_request_list_id', '=', 'b.id')
            ->leftJoin('loan_case as c', 'b.case_id', '=', 'c.id')
            ->where('a.status', '=',  1)
            ->whereMonth('a.created_at', $request->input("month"))
            ->where('a.percentage', '=',  5)
            ->whereYear('a.created_at', $request->input("year"));

        if($request->input("branch") != 0)
        {
            $SumBonus5Per = $SumBonus5Per->where('c.branch_id', $request->input("branch"));
        }

        $SumBonus5Per = $SumBonus5Per->sum('amount');

        return [
            'status' => 1,
            'total_receipt' => $total_receipt,
            'total_trust_receive' => $total_trust_receive,
            'uncollected' => $uncollected,
            'bal_disb' => $bal_disb,
            'bal_disb_q' => $bal_disb_q,
            'close_file_bal' => $close_file_bal,
            'disb' => $disb,
            'used_amt' => $used_amt,
            'SumBonus3Per' => $SumBonus3Per,
            'SumBonus5Per' => $SumBonus5Per,
            'actual_bal' => $actual_bal,
            'actual_bal_q' => $actual_bal_q,
            'total_receive_inv' => $total_receive_inv,
            'total_sst' => $total_sst,
            'total_check' => $total_check,
            'total_receive_q' => $total_receive_q,
            'total_staff_bonus_2_per' => $total_staff_bonus_2_per,
            'total_staff_bonus_3_per' => $total_staff_bonus_2_per,
            'total_staff_bonus_2_per_q' => $total_staff_bonus_2_per_q,
            'total_staff_bonus_3_per_q' => $total_staff_bonus_2_per_q,
        ];
    }

    public function getDashboardCaseCount(Request $request)
    {
        $now = Carbon::now();
        $result = [];
        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;
        $branchInfo = BranchController::manageBranchAccess();
        $accessInfo = AccessController::manageAccess();

        $case_list = DB::table('loan_case');

        $openCaseCount = DB::table('loan_case');
        $abortCaseCount = DB::table('loan_case')->whereIn('status', [99]);
        $InProgressCaseCount = DB::table('loan_case')->whereIn('status', [1, 2, 3]);
        $closedCaseCount = DB::table('loan_case')->where('status', '=', 0);
        $OverdueCaseCount = DB::table('loan_case')->where('status', '=', 4);

        if (!in_array($userRoles, ['admin', 'management', 'account'])) {
            $userList = $accessInfo['user_list'];

            // if ($userList) {
            //     // $openCaseCount = $openCaseCount->where(function ($q) use ($userList, $accessInfo) {
            //     //     $q->whereIn('lawyer_id', $userList)
            //     //         ->orWhereIn('clerk_id', $userList)
            //     //         ->orWhereIn('sales_user_id', $userList)
            //     //         ->orWhereIn('id', $accessInfo['case_list']);
            //     // });

            //     $accessCaseList = CaseController::caseManagementEngine();

            //     $openCaseCount = $openCaseCount->whereIn('id', $accessCaseList);
            //     $InProgressCaseCount = $InProgressCaseCount->whereIn('id', $accessCaseList);
            //     $OverdueCaseCount = $OverdueCaseCount->whereIn('id', $accessCaseList);
            //     $closedCaseCount = $closedCaseCount->whereIn('id', $accessCaseList);

            //     $InProgressCaseCount = $InProgressCaseCount->where(function ($q) use ($userList, $accessInfo) {
            //         $q->whereIn('branch_id', $accessInfo['brancAccessList'])
            //             ->whereIn('lawyer_id', $userList)
            //             ->orWhereIn('clerk_id', $userList)
            //             ->orWhereIn('sales_user_id', $userList)
            //             ->orWhereIn('id', $accessInfo['case_list']);
            //     });

            //     // $OverdueCaseCount = $OverdueCaseCount->where(function ($q) use ($userList, $accessInfo) {
            //     //     $q->whereIn('lawyer_id', $userList)
            //     //         ->orWhereIn('clerk_id', $userList)
            //     //         ->orWhereIn('sales_user_id', $userList)
            //     //         ->orWhereIn('id', $accessInfo['case_list']);
            //     // });

            //     // $closedCaseCount = $closedCaseCount->where(function ($q) use ($userList, $accessInfo) {
            //     //     $q->whereIn('lawyer_id', $userList)
            //     //         ->orWhereIn('clerk_id', $userList)
            //     //         ->orWhereIn('sales_user_id', $userList)
            //     //         ->orWhereIn('id', $accessInfo['case_list']);
            //     // });

            //     // $abortCaseCount = $abortCaseCount->where(function ($q) use ($userList, $accessInfo) {
            //     //     $q->whereIn('lawyer_id', $userList)
            //     //         ->orWhereIn('clerk_id', $userList)
            //     //         ->orWhereIn('sales_user_id', $userList)
            //     //         ->orWhereIn('id', $accessInfo['case_list']);
            //     // });

            // } else {
            //     // if (in_array($current_user->id, [13])) {
            //     //     $openCaseCount = $openCaseCount->where(function ($q) use ($userList, $accessInfo) {
            //     //         $q->whereIn('lawyer_id', $userList)
            //     //             ->orWhereIn('clerk_id', $userList)
            //     //             ->orWhereIn('sales_user_id', $userList)
            //     //             ->orWhereIn('id', $accessInfo['case_list']);
            //     //     });
    
            //     //     $InProgressCaseCount = $InProgressCaseCount->where(function ($q) use ($userList, $accessInfo) {
            //     //         $q->whereIn('lawyer_id', $userList)
            //     //             ->orWhereIn('clerk_id', $userList)
            //     //             ->orWhereIn('sales_user_id', $userList)
            //     //             ->orWhereIn('id', $accessInfo['case_list']);
            //     //     });
    
            //     //     $OverdueCaseCount = $OverdueCaseCount->where(function ($q) use ($userList, $accessInfo) {
            //     //         $q->whereIn('lawyer_id', $userList)
            //     //             ->orWhereIn('clerk_id', $userList)
            //     //             ->orWhereIn('sales_user_id', $userList)
            //     //             ->orWhereIn('id', $accessInfo['case_list']);
            //     //     });
    
            //     //     $closedCaseCount = $closedCaseCount->where(function ($q) use ($userList, $accessInfo) {
            //     //         $q->whereIn('lawyer_id', $userList)
            //     //             ->orWhereIn('clerk_id', $userList)
            //     //             ->orWhereIn('sales_user_id', $userList)
            //     //             ->orWhereIn('id', $accessInfo['case_list']);
            //     //     });
    
            //     //     $abortCaseCount = $abortCaseCount->where(function ($q) use ($userList, $accessInfo) {
            //     //         $q->whereIn('lawyer_id', $userList)
            //     //             ->orWhereIn('clerk_id', $userList)
            //     //             ->orWhereIn('sales_user_id', $userList)
            //     //             ->orWhereIn('id', $accessInfo['case_list']);
            //     //     });
            //     // }
            //     // else
            //     // {
            //     //     $openCaseCount = $openCaseCount->whereIn('branch_id', $accessInfo['brancAccessList']);
            //     //     $InProgressCaseCount = $InProgressCaseCount->whereIn('branch_id', $accessInfo['brancAccessList']);
            //     //     $closedCaseCount = $closedCaseCount->whereIn('branch_id', $accessInfo['brancAccessList']);
            //     //     $OverdueCaseCount = $OverdueCaseCount->whereIn('branch_id', $accessInfo['brancAccessList']);
            //     //     $abortCaseCount = $abortCaseCount->whereIn('branch_id', $accessInfo['brancAccessList']);
            //     // }

            //     $openCaseCount = $openCaseCount->whereIn('branch_id', $accessInfo['brancAccessList']);
            //     $InProgressCaseCount = $InProgressCaseCount->whereIn('branch_id', $accessInfo['brancAccessList']);
            //     $closedCaseCount = $closedCaseCount->whereIn('branch_id', $accessInfo['brancAccessList']);
            //     $OverdueCaseCount = $OverdueCaseCount->whereIn('branch_id', $accessInfo['brancAccessList']);
            //     $abortCaseCount = $abortCaseCount->whereIn('branch_id', $accessInfo['brancAccessList']);
                
            // }

            $accessCaseList = CaseController::caseManagementEngine();

                $openCaseCount = $openCaseCount->whereIn('id', $accessCaseList);
                $InProgressCaseCount = $InProgressCaseCount->whereIn('id', $accessCaseList);
                $OverdueCaseCount = $OverdueCaseCount->whereIn('id', $accessCaseList);
                $closedCaseCount = $closedCaseCount->whereIn('id', $accessCaseList);
                $abortCaseCount = $abortCaseCount->whereIn('id', $accessCaseList);

          
        }

        if( $request->input("month") != 0)
        {
            $openCaseCount = $openCaseCount->whereMonth('created_at', $request->input("month"));
            $InProgressCaseCount = $InProgressCaseCount->whereMonth('created_at', $request->input("month"));
            $closedCaseCount = $closedCaseCount->whereMonth('created_at', $request->input("month"));
            $OverdueCaseCount = $OverdueCaseCount->whereMonth('created_at', $request->input("month"));
            $abortCaseCount = $abortCaseCount->whereMonth('created_at', $request->input("month"));

            // $openCaseCount = $openCaseCount->whereMonth('created_at', $request->input("month"));
            // $InProgressCaseCount = $InProgressCaseCount->whereMonth('created_at', $request->input("month"));
            // $closedCaseCount = $closedCaseCount->whereMonth('close_date', $request->input("month"));
            // $OverdueCaseCount = $OverdueCaseCount->whereMonth('pending_close_date', $request->input("month"));
            // $abortCaseCount = $abortCaseCount->whereMonth('abort_date', $request->input("month"));
        }

        if( $request->input("year") != 0)
        {
            // $openCaseCount = $openCaseCount->whereYear('created_at', $request->input("year"));
            // $InProgressCaseCount = $InProgressCaseCount->whereYear('created_at', $request->input("year"));
            // $closedCaseCount = $closedCaseCount->whereYear('close_date', $request->input("year"));
            // $OverdueCaseCount = $OverdueCaseCount->whereYear('pending_close_date', $request->input("year"));
            // $abortCaseCount = $abortCaseCount->whereYear('abort_date', $request->input("year")); 

            $openCaseCount = $openCaseCount->whereYear('created_at', $request->input("year"));
            $InProgressCaseCount = $InProgressCaseCount->whereYear('created_at', $request->input("year"));
            $closedCaseCount = $closedCaseCount->whereYear('created_at', $request->input("year"));
            $OverdueCaseCount = $OverdueCaseCount->whereYear('created_at', $request->input("year"));
            $abortCaseCount = $abortCaseCount->whereYear('created_at', $request->input("year"));
        }

        if($request->input("branch") != 0)
        {
            $openCaseCount = $openCaseCount->where('branch_id', $request->input("branch"));
            $InProgressCaseCount = $InProgressCaseCount->where('branch_id', $request->input("branch"));
            $closedCaseCount = $closedCaseCount->where('branch_id', $request->input("branch"));
            $OverdueCaseCount = $OverdueCaseCount->where('branch_id', $request->input("branch"));
            $abortCaseCount = $abortCaseCount->where('branch_id', $request->input("branch"));
            
        }


        $openCaseCount = $openCaseCount->count();
        $InProgressCaseCount = $InProgressCaseCount->whereIn('status', [1, 2, 3])->count();
        $closedCaseCount = $closedCaseCount->where('status', '=', 0)->count();
        $OverdueCaseCount = $OverdueCaseCount->where('status', '=', 4)->count();
        $abortCaseCount = $abortCaseCount->whereIn('status', [99])->count();


        return response()->json([
            'view' => view('dashboard.dashboard.dashboard-legal-case', compact('abortCaseCount','openCaseCount','InProgressCaseCount','closedCaseCount','OverdueCaseCount', 'current_user'))->render()
        ]);


        $current_user = auth()->user();

        if ($current_user->id == 80) {
            $Branch = Branch::where('id', '=', '3')->get();
        } else {
            $Branch = Branch::where('status', '=', '1')->get();
        }

        if (count($Branch) > 0) {
            for ($i = 0; $i < count($Branch); $i++) {
                $RptCase = RptCase::where('fiscal_year', '=', $now->year)->where('branch_id', '=', $Branch[$i]->id)->orderby('fiscal_mon', 'asc')->get();
                $case_count_per_year = [];

                for ($j = 0; $j < count($RptCase); $j++) {
                    // $RptCase = RptCase::where('fiscal_year', '=',$now->year)->where('branch_id', '=', $Branch[$i]->id)->get();


                    array_push($case_count_per_year, $RptCase[$j]->count);
                }

                array_push($result, array('branch' => $Branch[$i]->name, 'count' => $case_count_per_year));
            }
        }


        $RptCase = RptCase::where('fiscal_year', '=', $now->year)->get();

        if ($current_user->id == 80) {
            $RptCase = RptCase::where('fiscal_year', '=', $now->year)->where('branch_id', '=', 3)->get();
        } else {
            $RptCase = RptCase::where('fiscal_year', '=', $now->year)->get();
        }

        $mon_count = 1;
        $all_count = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        for ($j = 0; $j < count($RptCase); $j++) {
            // $mon_count = 1;
            $all_count[$RptCase[$j]->fiscal_mon - 1] += $RptCase[$j]->count;

            // array_push($case_count_per_year, $RptCase[$j]->count);
        }

        array_push($result, array('branch' => 'All', 'count' => $all_count));




        return response()->json(['status' => 1, 'data' => $result]);

        // return $RptCase;
    }

    public function getPrevYeraDashboardCaseCount()
    {
        $now = Carbon::now();
        $result = [];
        $Branch = Branch::where('status', '=', '1')->get();

        $current_user = auth()->user();

        if ($current_user->id == 80) {
            $Branch = Branch::where('id', '=', '3')->get();
        } else {
            $Branch = Branch::where('status', '=', '1')->get();
        }

        if (count($Branch) > 0) {
            for ($i = 0; $i < count($Branch); $i++) {
                $RptCase = RptCase::where('fiscal_year', '=', 2022)->where('branch_id', '=', $Branch[$i]->id)->orderby('fiscal_mon', 'asc')->get();
                $case_count_per_year = [];

                for ($j = 0; $j < count($RptCase); $j++) {
                    // $RptCase = RptCase::where('fiscal_year', '=',$now->year)->where('branch_id', '=', $Branch[$i]->id)->get();


                    array_push($case_count_per_year, $RptCase[$j]->count);
                }

                array_push($result, array('branch' => $Branch[$i]->name, 'count' => $case_count_per_year));
            }
        }

        if ($current_user->id == 80) {
            $RptCase = RptCase::where('fiscal_year', '=', 2022)->where('branch_id', '=', 3)->get();
        } else {
            $RptCase = RptCase::where('fiscal_year', '=', 2022)->get();
        }

        // $RptCase = RptCase::where('fiscal_year', '=', 2022)->get();
        $mon_count = 1;
        $all_count = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        for ($j = 0; $j < count($RptCase); $j++) {
            // $mon_count = 1;
            $all_count[$RptCase[$j]->fiscal_mon - 1] += $RptCase[$j]->count;

            // array_push($case_count_per_year, $RptCase[$j]->count);
        }

        array_push($result, array('branch' => 'All', 'count' => $all_count));


        return response()->json(['status' => 1, 'data' => $result]);

        // return $RptCase;
    }

    /**
     * AJAX endpoint to load dashboard case counts
     */
    public function loadDashboardCounts(Request $request)
    {
        $current_user = auth()->user();
        
        $cacheKey = "dashboard_counts_{$current_user->id}";
        
        $data = Cache::remember($cacheKey, 300, function () use ($current_user) {
            $InProgressCaseCount = DB::table('loan_case')->whereIn('status', [1, 2, 3]);
            $openCaseCount = DB::table('loan_case');
            $closedCaseCount = DB::table('loan_case')->where('status', '=', 0);
            $OverdueCaseCount = DB::table('loan_case')->where('status', '=', 4);

            // Apply permission filters
            if ($current_user->menuroles == 'lawyer' || $current_user->menuroles == 'chambering') {
                $InProgressCaseCount = $InProgressCaseCount->where('lawyer_id', '=', $current_user->id);
                $openCaseCount = $openCaseCount->where('lawyer_id', '=', $current_user->id);
                $closedCaseCount = $closedCaseCount->where('lawyer_id', '=', $current_user->id);
                $OverdueCaseCount = $OverdueCaseCount->where('lawyer_id', '=', $current_user->id);
            } else if ($current_user->menuroles == 'clerk') {
                $InProgressCaseCount = $InProgressCaseCount->where('clerk_id', '=', $current_user->id);
                $openCaseCount = $openCaseCount->where('clerk_id', '=', $current_user->id);
                $closedCaseCount = $closedCaseCount->where('clerk_id', '=', $current_user->id);
                $OverdueCaseCount = $OverdueCaseCount->where('clerk_id', '=', $current_user->id);
            } else if ($current_user->menuroles == 'sales') {
                if (in_array($current_user->id, [51,127])) {
                    $InProgressCaseCount = $InProgressCaseCount->where('sales_user_id', '=', 32);
                    $openCaseCount = $openCaseCount->where('sales_user_id', '=', 32);
                    $closedCaseCount = $closedCaseCount->where('sales_user_id', '=', 32);
                    $OverdueCaseCount = $OverdueCaseCount->where('sales_user_id', '=', 32);
                } else if ($current_user->id == 80) {
                    $InProgressCaseCount = $InProgressCaseCount->where('branch_id', '=', 3)->where('sales_user_id', '<>', 1);
                    $openCaseCount = $openCaseCount->where('branch_id', '=', 3)->where('sales_user_id', '<>', 1);
                    $closedCaseCount = $closedCaseCount->where('branch_id', '=', 3)->where('sales_user_id', '<>', 1);
                    $OverdueCaseCount = $OverdueCaseCount->where('branch_id', '=', 3)->where('sales_user_id', '<>', 1);
                } else {
                    $InProgressCaseCount = $InProgressCaseCount->where('sales_user_id', '=', $current_user->id);
                    $openCaseCount = $openCaseCount->where('sales_user_id', '=', $current_user->id);
                    $closedCaseCount = $closedCaseCount->where('sales_user_id', '=', $current_user->id);
                    $OverdueCaseCount = $OverdueCaseCount->where('sales_user_id', '=', $current_user->id);
                }
            } else if ($current_user->menuroles == 'maker') {
                $InProgressCaseCount = $InProgressCaseCount->where('branch_id', '=', $current_user->branch_id);
                $openCaseCount = $openCaseCount->where('branch_id', '=', $current_user->branch_id);
                $closedCaseCount = $closedCaseCount->where('branch_id', '=', $current_user->branch_id);
                $OverdueCaseCount = $OverdueCaseCount->where('branch_id', '=', $current_user->branch_id);
            }

            return [
                'InProgressCaseCount' => $InProgressCaseCount->count(),
                'openCaseCount' => $openCaseCount->count(),
                'closedCaseCount' => $closedCaseCount->count(),
                'OverdueCaseCount' => $OverdueCaseCount->count(),
            ];
        });

        return response()->json($data);
    }

    /**
     * AJAX endpoint to load all notes data
     */
    public function loadNotesData(Request $request)
    {
        $current_user = auth()->user();
        $date = Carbon::now()->subDays(7)->startOfDay();
        $Last7Days = Carbon::now()->subDays(7)->startOfDay();
        
        if($current_user->id == 22) {
            $date = Carbon::now()->subDays(30)->startOfDay();
        }

        $accessInfo = Cache::remember("access_info_{$current_user->id}", 300, function () {
            return AccessController::manageAccess();
        });

        $limit = $request->input('limit', 100); // Allow loading more via AJAX
        
        $kiv_note = $this->getKivNotes($current_user, $date, $accessInfo, $limit);
        $pnc_note = $this->getPncNotes($current_user, $date, $limit);
        
        $LoanMarketingNotes = [];
        if (in_array($current_user->menuroles, ['account', 'admin', 'sales', 'maker'])) {
            $LoanMarketingNotes = $this->getMarketingNotes($current_user, $Last7Days, $accessInfo, $limit);
        }

        return response()->json([
            'kiv_note' => $kiv_note,
            'pnc_note' => $pnc_note,
            'LoanMarketingNotes' => $LoanMarketingNotes,
        ]);
    }

    /**
     * AJAX endpoint to load case files
     */
    public function loadCaseFiles(Request $request)
    {
        $current_user = auth()->user();
        $date = Carbon::now()->subDays(7)->startOfDay();
        
        if($current_user->id == 22) {
            $date = Carbon::now()->subDays(30)->startOfDay();
        }

        $branchInfo = Cache::remember("branch_access_{$current_user->id}", 3600, function () {
            return BranchController::manageBranchAccess();
        });

        $limit = $request->input('limit', 100);
        $case_files = $this->getCaseFiles($current_user, $date, $branchInfo, $limit);

        return response()->json([
            'case_files' => $case_files,
        ]);
    }

    /**
     * AJAX endpoint to load B2022 cases data
     */
    public function loadB2022Cases(Request $request)
    {
        $current_user = auth()->user();
        
        if ($current_user->menuroles == 'manager' || $current_user->menuroles == 'admin' || $current_user->menuroles == 'account') {
            $data = [
                'B2022PendingCloseCases' => DB::table('cases_outside_system')->where('status', '=', 3)->count(),
                'B2022AllCases' => DB::table('cases_outside_system')->count(),
                'B2022ClosedCases' => DB::table('cases_outside_system')->where('status', '=', 2)->count(),
                'B2022ActiveCases' => DB::table('cases_outside_system')->where('status', '=', 1)->count(),
            ];
        } else {
            $data = [
                'totalAcount' => DB::table('cases_outside_system')->where('status', '=', 1)->where('old_pic_id', '=', $current_user->id)->count(),
                'totalAssigned' => DB::table('cases_outside_system')->where('new_pic_id', '<>', 0)->where('old_pic_id', '=', $current_user->id)->count(),
                'totalUpdated' => DB::table('cases_outside_system')->where('old_pic_id', '=', $current_user->id)->where('remarks', '<>', '')->count(),
            ];
        }

        return response()->json($data);
    }

    function searchCase(Request $request)
    {
        if (!$request->has('case_ref_no') && !$request->has('ic') && !$request->has('name') && !$request->has('tel_no')) {
            $cases = [];
        } else {
            $cases = DB::table('loan_case')
                ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
                ->leftJoin('client', 'client.id', '=', 'loan_case.customer_id')
                ->leftJoin('users as lawyer', 'lawyer.id', '=', 'loan_case.lawyer_id')
                ->leftJoin('users as clerk', 'clerk.id', '=', 'loan_case.clerk_id')
                ->select(array(
                    'loan_case.*', 'case_type.name AS type_name', 'client.name AS client_name', 'client.phone_no AS client_phone_no',
                    'lawyer.name AS lawyer_name', 'clerk.name AS clerk_name'
                ))
                // ->where('loan_case.case_ref_no', '=',   $request->input('case_ref_no_search'))
                // ->orWhere('client.name', 'like',   '%'.$request->input('case_ref_no_search').'%')
                ->when($request->has('case_ref_no'), function ($cases) use ($request) {
                    // $cases->where('loan_case.case_ref_no', '=', $request->input('case_ref_no'));
                    $cases->orWhere('loan_case.case_ref_no', 'like',   '%' . $request->input('case_ref_no') . '%');
                })
                ->when($request->has('ic'), function ($cases) use ($request) {
                    $cases->where('client.ic_no', '=', $request->input('ic'));
                })
                ->when($request->has('name'), function ($cases) use ($request) {
                    $cases->orWhere('client.name', 'like',   '%' . $request->input('name') . '%');
                    $cases->orWhere('client.name', 'like',   $request->input('name') . '%');
                    $cases->orWhere('client.name', 'like',   '%' . $request->input('name'));
                })
                ->when($request->has('tel_no'), function ($cases) use ($request) {
                    $cases->Where('client.phone_no', '=', '%' . $request->input('tel_no') . '%');
                })
                ->get();
        }



        // $cases = DB::table('loan_case')
        // ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
        // ->leftJoin('client', 'client.id', '=', 'loan_case.customer_id')
        // ->select(array('loan_case.*', 'case_type.name AS type_name', 'client.name AS client_name'))
        // ->where('loan_case.case_ref_no', '=',   $request->input('case_ref_no_search'))
        // ->orWhere('client.name', 'like',   '%'.$request->input('name').'%')
        // ->orWhere('client.name', 'like',   $request->input('name').'%')
        // ->orWhere('client.name', 'like',   '%'.$request->input('name'))
        // ->Where('client.ic_no', '=',   $request->input('ic'))
        // ->Where('client.phone_no', '=',   $request->input('tel_no'))
        // ->get();

        return response()->json([
            'view' => view('dashboard.case.table.tbl-case-search-list', compact('cases'))->render()
        ]);
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

        // $query = DB::table('users')
        // ->leftJoin('loan_case', 'users.id', '=', 'loan_case.lawyer_id')
        // ->select(array('users.*', DB::raw('COUNT(loan_case.lawyer_id) as followers')))
        // ->groupBy('users.id')
        // ->orderByDesc('followers')
        // ->get();



        return view('dashboard.todolist.create', ['templates' => CaseTemplate::all(), 'lawyers' => $lawyer, 'sales' => $sales, 'banks' => $banks]);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $client_name =  $request->input('client_name');
        $customer = new Customer();

        $current_user = auth()->user();

        $client_short_code = Helper::generateNickName($client_name);


        $case_ref_no = '[sales]/[lawyer]/[bank]/[running_no]/[client]/[clerk]';
        $lawyer = $this->assignTask('lawyer');
        $clerk = $this->assignTask('clerk');

        $bank = Banks::where('id', '=', 1)->get();
        $running_no = Parameter::where('parameter_type', '=', 'case_running_no')->get();

        $current_user = auth()->user();

        $case_ref_no = str_replace("[sales]", $current_user->nick_name, $case_ref_no);
        $case_ref_no = str_replace("[bank]", $bank[0]->short_code, $case_ref_no);
        $case_ref_no = str_replace("[running_no]", $running_no[0]->parameter_value_1, $case_ref_no);
        $case_ref_no = str_replace("[client]", $client_short_code, $case_ref_no);

        if (count($lawyer)) {
            $case_ref_no = str_replace("[lawyer]", $lawyer[0]->nick_name, $case_ref_no);
        }

        if (count($clerk)) {
            $case_ref_no = str_replace("[clerk]", $clerk[0]->nick_name, $case_ref_no);
        }

        $loanCase = new TodoList();
        $loanCase->case_ref_no = $case_ref_no;
        $loanCase->property_address = $request->input('property_address');
        $loanCase->referral_name = $request->input('referral_name');
        $loanCase->referral_phone_no = $request->input('referral_phone_no');
        $loanCase->referral_email = $request->input('referral_email');
        $loanCase->purchase_price = $request->input('purchase_price');
        $loanCase->remark = $request->input('remark');
        $loanCase->sales_user_id = $current_user->id;
        $loanCase->bank_id = $request->input('bank');
        $loanCase->lawyer_id = $lawyer[0]->id;
        $loanCase->clerk_id = $clerk[0]->id;
        $loanCase->status = "1";
        $loanCase->created_at = now();


        $loanCase->save();

        if ($loanCase) {
            $customer = $this->createCustomer($request, $case_ref_no);
        }

        if ($customer) {
            $customer = $this->createCustomer($request, $case_ref_no);
        } else {
        }

        $request->session()->flash('message', 'Successfully created new case');

        return redirect()->route('todolist.index', ['cases' => TodoList::all()]);

        // return view('dashboard.form.create');

        // return $current_user->nick_name;
        // $nickName  = Helper::generateNickName($name);

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
        // return redirect()->route('todolist.index', ['cases' => TodoList::all()]);
    }

    public function createCustomer($request, $case_ref_no)
    {
        $customer = new Customer();

        $customer->case_ref_no = $case_ref_no;
        $customer->name = $request->input('client_name');
        $customer->phone_no = $request->input('client_phone_no');
        $customer->status = "1";
        $customer->created_at = now();
        // $customer->name = $request->input('name');
        // $customer->name = $request->input('name');
        // $customer->name = $request->input('name');

        $customer->save();

        return $customer;
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('dashboard.todolist.show', [
            'lang' => MenuLangList::where('id', '=', $id)->first()
        ]);
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

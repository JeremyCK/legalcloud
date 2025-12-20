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
use App\Http\Controllers\AccessController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CaseController;

class DashboardV2Controller extends Controller
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


        return view('dashboard.v2.index', [
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
     * Get KIV notes with permission control - Optimized with caching
     */
    private function getKivNotes($current_user, $date, $accessInfo, $limit = null)
    {
        $cacheKey = "kiv_notes_{$current_user->id}_" . ($limit ? "limit_{$limit}" : "all") . "_" . md5($date);
        
        return Cache::remember($cacheKey, 180, function () use ($current_user, $date, $accessInfo, $limit) {
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
        });
    }

    /**
     * Get PNC notes with permission control - Optimized with caching
     */
    private function getPncNotes($current_user, $date, $limit = null)
    {
        $cacheKey = "pnc_notes_{$current_user->id}_" . ($limit ? "limit_{$limit}" : "all") . "_" . md5($date);
        
        return Cache::remember($cacheKey, 180, function () use ($current_user, $date, $limit) {
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
        });
    }

    /**
     * Get marketing notes with permission control - Optimized with caching
     */
    private function getMarketingNotes($current_user, $Last7Days, $accessInfo, $limit = null)
    {
        $cacheKey = "marketing_notes_{$current_user->id}_" . ($limit ? "limit_{$limit}" : "all") . "_" . md5($Last7Days);
        
        return Cache::remember($cacheKey, 180, function () use ($current_user, $Last7Days, $accessInfo, $limit) {
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
        });
    }

    /**
     * Get case files with permission control - Optimized with caching
     */
    private function getCaseFiles($current_user, $date, $branchInfo, $limit = null)
    {
        // No caching - always get fresh data for past 7 days
        // Ensure date is in correct format for query
        $dateString = is_string($date) ? $date : $date->toDateTimeString();
        
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
     * Get today's message count with permission control - Matching original dashboard logic
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

    /**
     * AJAX endpoint to load dashboard case counts - Matching original dashboard logic exactly
     */
    public function getDashboardCaseCount(Request $request)
    {
        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;
        $branchInfo = BranchController::manageBranchAccess();
        $accessInfo = AccessController::manageAccess();

        $openCaseCount = DB::table('loan_case');
        $abortCaseCount = DB::table('loan_case')->whereIn('status', [99]);
        $InProgressCaseCount = DB::table('loan_case')->whereIn('status', [1, 2, 3]);
        $closedCaseCount = DB::table('loan_case')->where('status', '=', 0);
        $OverdueCaseCount = DB::table('loan_case')->where('status', '=', 4);

        if (!in_array($userRoles, ['admin', 'management', 'account'])) {
            $accessCaseList = CaseController::caseManagementEngine();
            $openCaseCount = $openCaseCount->whereIn('id', $accessCaseList);
            $InProgressCaseCount = $InProgressCaseCount->whereIn('id', $accessCaseList);
            $OverdueCaseCount = $OverdueCaseCount->whereIn('id', $accessCaseList);
            $closedCaseCount = $closedCaseCount->whereIn('id', $accessCaseList);
            $abortCaseCount = $abortCaseCount->whereIn('id', $accessCaseList);
        }

        if( $request->input("month") != 0) {
            $openCaseCount = $openCaseCount->whereMonth('created_at', $request->input("month"));
            $InProgressCaseCount = $InProgressCaseCount->whereMonth('created_at', $request->input("month"));
            $closedCaseCount = $closedCaseCount->whereMonth('created_at', $request->input("month"));
            $OverdueCaseCount = $OverdueCaseCount->whereMonth('created_at', $request->input("month"));
            $abortCaseCount = $abortCaseCount->whereMonth('created_at', $request->input("month"));
        }

        if( $request->input("year") != 0) {
            $openCaseCount = $openCaseCount->whereYear('created_at', $request->input("year"));
            $InProgressCaseCount = $InProgressCaseCount->whereYear('created_at', $request->input("year"));
            $closedCaseCount = $closedCaseCount->whereYear('created_at', $request->input("year"));
            $OverdueCaseCount = $OverdueCaseCount->whereYear('created_at', $request->input("year"));
            $abortCaseCount = $abortCaseCount->whereYear('created_at', $request->input("year"));
        }

        if($request->input("branch") != 0) {
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
            'view' => view('dashboard.dashboard.dashboard-legal-case', compact('abortCaseCount','openCaseCount','InProgressCaseCount','closedCaseCount','OverdueCaseCount', 'current_user'))->render(),
            'data' => [
                'openCaseCount' => $openCaseCount,
                'InProgressCaseCount' => $InProgressCaseCount,
                'closedCaseCount' => $closedCaseCount,
                'OverdueCaseCount' => $OverdueCaseCount,
                'abortCaseCount' => $abortCaseCount,
            ]
        ]);
    }

    /**
     * AJAX endpoint to load dashboard case counts - Optimized with caching (for initial load)
     */
    public function loadDashboardCounts(Request $request)
    {
        $current_user = auth()->user();
        
        $cacheKey = "dashboard_counts_v2_{$current_user->id}_" . 
                    ($request->input('year') ?: 'all') . '_' . 
                    ($request->input('month') ?: 'all') . '_' . 
                    ($request->input('branch') ?: 'all');
        
        $data = Cache::remember($cacheKey, 300, function () use ($current_user, $request) {
            $userRoles = $current_user->menuroles;
            $accessInfo = AccessController::manageAccess();
            $accessCaseList = CaseController::caseManagementEngine();
            
            $openCaseCount = DB::table('loan_case');
            $InProgressCaseCount = DB::table('loan_case')->whereIn('status', [1, 2, 3]);
            $closedCaseCount = DB::table('loan_case')->where('status', '=', 0);
            $OverdueCaseCount = DB::table('loan_case')->where('status', '=', 4);
            $abortCaseCount = DB::table('loan_case')->whereIn('status', [99]);

            if (!in_array($userRoles, ['admin', 'management', 'account'])) {
                $openCaseCount = $openCaseCount->whereIn('id', $accessCaseList);
                $InProgressCaseCount = $InProgressCaseCount->whereIn('id', $accessCaseList);
                $closedCaseCount = $closedCaseCount->whereIn('id', $accessCaseList);
                $OverdueCaseCount = $OverdueCaseCount->whereIn('id', $accessCaseList);
                $abortCaseCount = $abortCaseCount->whereIn('id', $accessCaseList);
            }

            if($request->input("month") != 0) {
                $openCaseCount = $openCaseCount->whereMonth('created_at', $request->input("month"));
                $InProgressCaseCount = $InProgressCaseCount->whereMonth('created_at', $request->input("month"));
                $closedCaseCount = $closedCaseCount->whereMonth('created_at', $request->input("month"));
                $OverdueCaseCount = $OverdueCaseCount->whereMonth('created_at', $request->input("month"));
                $abortCaseCount = $abortCaseCount->whereMonth('created_at', $request->input("month"));
            }

            if($request->input("year") != 0) {
                $openCaseCount = $openCaseCount->whereYear('created_at', $request->input("year"));
                $InProgressCaseCount = $InProgressCaseCount->whereYear('created_at', $request->input("year"));
                $closedCaseCount = $closedCaseCount->whereYear('created_at', $request->input("year"));
                $OverdueCaseCount = $OverdueCaseCount->whereYear('created_at', $request->input("year"));
                $abortCaseCount = $abortCaseCount->whereYear('created_at', $request->input("year"));
            }

            if($request->input("branch") != 0) {
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

            return [
                'openCaseCount' => $openCaseCount,
                'InProgressCaseCount' => $InProgressCaseCount,
                'closedCaseCount' => $closedCaseCount,
                'OverdueCaseCount' => $OverdueCaseCount,
                'abortCaseCount' => $abortCaseCount,
            ];
        });

        return response()->json($data);
    }

    /**
     * AJAX endpoint to load all notes data - Optimized with caching
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
     * AJAX endpoint to load case files - Optimized with caching
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

        $limit = $request->input('limit', 100); // Allow loading more via AJAX
        $case_files = $this->getCaseFiles($current_user, $date, $branchInfo, $limit);

        return response()->json([
            'case_files' => $case_files,
        ]);
    }

    /**
     * AJAX endpoint to load B2022 cases data - Optimized with caching
     */
    public function loadB2022Cases(Request $request)
    {
        $current_user = auth()->user();
        
        $cacheKey = "b2022_cases_{$current_user->id}";
        
        $data = Cache::remember($cacheKey, 300, function () use ($current_user) {
            if ($current_user->menuroles == 'manager' || $current_user->menuroles == 'admin' || $current_user->menuroles == 'account') {
                return [
                    'B2022PendingCloseCases' => DB::table('cases_outside_system')->where('status', '=', 3)->count(),
                    'B2022AllCases' => DB::table('cases_outside_system')->count(),
                    'B2022ClosedCases' => DB::table('cases_outside_system')->where('status', '=', 2)->count(),
                    'B2022ActiveCases' => DB::table('cases_outside_system')->where('status', '=', 1)->count(),
                ];
            } else {
                return [
                    'totalAcount' => DB::table('cases_outside_system')->where('status', '=', 1)->where('old_pic_id', '=', $current_user->id)->count(),
                    'totalAssigned' => DB::table('cases_outside_system')->where('new_pic_id', '<>', 0)->where('old_pic_id', '=', $current_user->id)->count(),
                    'totalUpdated' => DB::table('cases_outside_system')->where('old_pic_id', '=', $current_user->id)->where('remarks', '<>', '')->count(),
                ];
            }
        });

        return response()->json($data);
    }


    /**
     * Clear cache for dashboard V2 - Useful for testing or when data needs refresh
     */
    public function clearCache(Request $request)
    {
        $current_user = auth()->user();
        
        // Clear all dashboard-related cache for this user
        Cache::forget("dashboard_counts_v2_{$current_user->id}");
        Cache::forget("b2022_cases_{$current_user->id}");
        Cache::forget("today_message_count_{$current_user->id}");
        Cache::forget("access_info_{$current_user->id}");
        
        // Clear notes and attachments cache - need to clear for all possible date combinations
        // Since cache keys include date hash, we'll clear common patterns
        $date7Days = Carbon::now()->subDays(7);
        $date4Days = Carbon::now()->subDays(4); // Old value
        $date14Days = Carbon::now()->subDays(14); // Old value
        
        // Clear case files cache
        Cache::forget("case_files_{$current_user->id}_limit_20_" . md5($date7Days));
        Cache::forget("case_files_{$current_user->id}_limit_50_" . md5($date7Days));
        Cache::forget("case_files_{$current_user->id}_limit_20_" . md5($date4Days));
        Cache::forget("case_files_{$current_user->id}_limit_50_" . md5($date4Days));
        
        // Clear notes cache
        Cache::forget("kiv_notes_{$current_user->id}_limit_10_" . md5($date7Days));
        Cache::forget("kiv_notes_{$current_user->id}_limit_50_" . md5($date7Days));
        Cache::forget("kiv_notes_{$current_user->id}_limit_10_" . md5($date4Days));
        Cache::forget("kiv_notes_{$current_user->id}_limit_50_" . md5($date4Days));
        
        Cache::forget("pnc_notes_{$current_user->id}_limit_10_" . md5($date7Days));
        Cache::forget("pnc_notes_{$current_user->id}_limit_50_" . md5($date7Days));
        Cache::forget("pnc_notes_{$current_user->id}_limit_10_" . md5($date4Days));
        Cache::forget("pnc_notes_{$current_user->id}_limit_50_" . md5($date4Days));
        
        Cache::forget("marketing_notes_{$current_user->id}_limit_10_" . md5($date7Days));
        Cache::forget("marketing_notes_{$current_user->id}_limit_50_" . md5($date7Days));
        Cache::forget("marketing_notes_{$current_user->id}_limit_10_" . md5($date14Days));
        Cache::forget("marketing_notes_{$current_user->id}_limit_50_" . md5($date14Days));
        
        // Note: Pattern-based cache clearing would require cache tags or manual iteration
        // For now, we'll clear the most common ones
        
        return response()->json(['status' => 'success', 'message' => 'Cache cleared successfully']);
    }
}

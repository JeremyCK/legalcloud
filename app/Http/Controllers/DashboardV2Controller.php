<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\LoanCase;
use App\Models\RptCase;
use App\Models\Users;
use App\Models\VoucherMain;
use App\Models\LoanCaseBillMain;
use App\Models\BonusRequestList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CaseController;

class DashboardV2Controller extends Controller
{
    /**
     * Display the enhanced dashboard V2
     */
    public function index()
    {
        $current_user = auth()->user();
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month; // Current month (December = 12)
        
        // Get branches with caching for better performance
        $branches = Cache::remember('active_branches', 3600, function () {
            return Branch::where('status', 1)->get();
        });

        // Load dashboard summary with case counts for current month and year
        // Default to current month (December) and current year (2025)
        $dashboardSummary = $this->getDashboardSummary($current_user, $currentYear, $currentMonth);
        
        // Defer other heavy data loading - load via AJAX for better initial page load
        $recentActivities = [];
        $performanceMetrics = [];

        // Lazy load notes data - only load minimal data initially, rest via AJAX
        // Limit initial load to 10 items for faster page load
        $notesData = $this->getNotesData($current_user, 10);
        $kiv_note = $notesData['kiv_note'] ?? collect([]);
        $pnc_note = $notesData['pnc_note'] ?? collect([]);
        $LoanMarketingNotes = $notesData['LoanMarketingNotes'] ?? collect([]);
        
        // Get today's message count - cached
        $today_message_count = Cache::remember("today_message_count_{$current_user->id}", 300, function () use ($current_user) {
            return $this->getTodayMessageCount($current_user);
        });

        // Get B2022 cases data (for admin/account roles)
        $b2022Cases = $this->getB2022Cases($current_user);
        
        // Extract B2022 variables to match original dashboard structure
        $B2022AllCases = $b2022Cases['B2022AllCases'] ?? 0;
        $B2022ClosedCases = $b2022Cases['B2022ClosedCases'] ?? 0;
        $B2022ActiveCases = $b2022Cases['B2022ActiveCases'] ?? 0;
        $B2022PendingCloseCases = $b2022Cases['B2022PendingCloseCases'] ?? 0;
        $totalAcount = $b2022Cases['totalAcount'] ?? 0;
        $totalAssigned = $b2022Cases['totalAssigned'] ?? 0;
        $totalUpdated = $b2022Cases['totalUpdated'] ?? 0;

        // Additional variables that might be needed by the view
        $case_file = collect([]);
        $case_path = '';
        $LoanCaseChecklistDetails = [];
        $BonusRequestList = [];
        $cases = [];
        $OverdueCaseCount = 0;
        
        // Get case path parameter
        $parameter = Cache::remember('case_file_path_parameter', 3600, function () {
            return \App\Models\Parameter::where('parameter_type', '=', 'case_file_path')->first();
        });
        if ($parameter) {
            $case_path = $parameter->parameter_value_1;
        }
        
        // Use $branches as $Branch to match view expectations
        $Branch = $branches;

        return view('dashboard.v2.index', compact(
            'current_user',
            'branches',
            'Branch',
            'dashboardSummary',
            'recentActivities',
            'performanceMetrics',
            'currentYear',
            'currentMonth',
            'notesData',
            'kiv_note',
            'pnc_note',
            'LoanMarketingNotes',
            'today_message_count',
            'B2022AllCases',
            'B2022ClosedCases',
            'B2022ActiveCases',
            'B2022PendingCloseCases',
            'totalAcount',
            'totalAssigned',
            'totalUpdated',
            'case_file',
            'case_path',
            'LoanCaseChecklistDetails',
            'BonusRequestList',
            'cases',
            'OverdueCaseCount'
        ));
    }

    /**
     * Get enhanced dashboard summary with better performance
     * @param int|null $year Year filter (defaults to current year)
     * @param int|null $month Month filter (defaults to current month, 0 = all months)
     */
    private function getDashboardSummary($user, $year = null, $month = null)
    {
        if ($year === null) {
            $year = Carbon::now()->year;
        }
        if ($month === null) {
            $month = Carbon::now()->month;
        }
        
        $cacheKey = "dashboard_summary_{$user->id}_{$year}_{$month}";
        
        return Cache::remember($cacheKey, 300, function () use ($user, $year, $month) {
            $summary = [];
            
            // Get case counts with optimized queries
            $caseCounts = DB::table('loan_case')
                ->selectRaw('
                    COUNT(*) as total_cases,
                    SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as closed_cases,
                    SUM(CASE WHEN status IN (1,2,3) THEN 1 ELSE 0 END) as in_progress_cases,
                    SUM(CASE WHEN status = 4 THEN 1 ELSE 0 END) as overdue_cases,
                    SUM(CASE WHEN status = 99 THEN 1 ELSE 0 END) as aborted_cases
                ')
                ->where('status', '<>', 99)
                ->whereYear('created_at', $year);
            
            // Apply month filter if specified (0 means all months)
            if ($month > 0) {
                $caseCounts = $caseCounts->whereMonth('created_at', $month);
            }

            // Apply user access restrictions with permission control
            if (!in_array($user->menuroles, ['admin', 'management', 'account'])) {
                $accessCaseList = CaseController::caseManagementEngine();
                $caseCounts = $caseCounts->whereIn('id', $accessCaseList);
            }

            $caseData = $caseCounts->first();
            
            $summary['total_cases'] = $caseData->total_cases ?? 0;
            $summary['closed_cases'] = $caseData->closed_cases ?? 0;
            $summary['in_progress_cases'] = $caseData->in_progress_cases ?? 0;
            $summary['overdue_cases'] = $caseData->overdue_cases ?? 0;
            $summary['aborted_cases'] = $caseData->aborted_cases ?? 0;
            $summary['open_cases'] = $summary['total_cases'] - $summary['closed_cases'] - $summary['aborted_cases'];
            
            // Get monthly case trends (for current year)
            $summary['monthly_trends'] = $this->getMonthlyCaseTrends($user, $year);
            
            // Get branch performance (for current year)
            $summary['branch_performance'] = $this->getBranchPerformance($user, $year);
            
            return $summary;
        });
    }

    /**
     * Get monthly case trends with optimized query
     */
    private function getMonthlyCaseTrends($user, $year)
    {
        $cacheKey = "monthly_trends_{$user->id}_{$year}";
        
        return Cache::remember($cacheKey, 300, function () use ($user, $year) {
            $query = DB::table('rpt_case')
                ->selectRaw('fiscal_mon, SUM(count) as total_count')
                ->where('fiscal_year', $year)
                ->groupBy('fiscal_mon')
                ->orderBy('fiscal_mon');

            // Apply user access restrictions
            if (!in_array($user->menuroles, ['admin', 'management', 'account'])) {
                $accessibleBranches = $this->getUserAccessibleBranches($user);
                $query = $query->whereIn('branch_id', $accessibleBranches);
            }

            $monthlyData = $query->get();
            
            // Initialize array with 12 months
            $trends = array_fill(1, 12, 0);
            
            foreach ($monthlyData as $data) {
                $trends[$data->fiscal_mon] = $data->total_count;
            }
            
            return array_values($trends);
        });
    }

    /**
     * Get branch performance data
     */
    private function getBranchPerformance($user, $year)
    {
        $cacheKey = "branch_performance_{$user->id}_{$year}";
        
        return Cache::remember($cacheKey, 300, function () use ($user, $year) {
            $query = DB::table('rpt_case as rc')
                ->join('branch as b', 'b.id', '=', 'rc.branch_id')
                ->selectRaw('b.id, b.name, rc.fiscal_mon, rc.count')
                ->where('rc.fiscal_year', $year)
                ->orderBy('b.name')
                ->orderBy('rc.fiscal_mon');

            // Apply user access restrictions
            if (!in_array($user->menuroles, ['admin', 'management', 'account'])) {
                $accessibleBranches = $this->getUserAccessibleBranches($user);
                $query = $query->whereIn('rc.branch_id', $accessibleBranches);
            }

            $branchData = $query->get();
            
            $performance = [];
            foreach ($branchData as $data) {
                if (!isset($performance[$data->name])) {
                    $performance[$data->name] = array_fill(1, 12, 0);
                }
                $performance[$data->name][$data->fiscal_mon] = $data->count;
            }
            
            return $performance;
        });
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities($user)
    {
        $cacheKey = "recent_activities_{$user->id}";
        
        return Cache::remember($cacheKey, 180, function () use ($user) {
            $activities = [];
            
            // Get recent case updates
            $recentCases = DB::table('loan_case as lc')
                ->join('users as u', 'u.id', '=', 'lc.lawyer_id')
                ->select('lc.case_ref_no', 'lc.updated_at', 'u.name as lawyer_name')
                ->where('lc.updated_at', '>=', Carbon::now()->subDays(7))
                ->orderBy('lc.updated_at', 'desc')
                ->limit(5)
                ->get();
            
            foreach ($recentCases as $case) {
                $activities[] = [
                    'type' => 'case_update',
                    'message' => "Case {$case->case_ref_no} updated by {$case->lawyer_name}",
                    'time' => $case->updated_at,
                    'icon' => 'fas fa-file-alt'
                ];
            }
            
            // Get recent bonus requests
            $recentBonusRequests = BonusRequestList::where('created_at', '>=', Carbon::now()->subDays(7))
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
            
            foreach ($recentBonusRequests as $bonus) {
                $activities[] = [
                    'type' => 'bonus_request',
                    'message' => "New bonus request: {$bonus->ref_no}",
                    'time' => $bonus->created_at,
                    'icon' => 'fas fa-gift'
                ];
            }
            
            // Sort by time
            usort($activities, function($a, $b) {
                return strtotime($b['time']) - strtotime($a['time']);
            });
            
            return array_slice($activities, 0, 8);
        });
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics($user, $year)
    {
        $cacheKey = "performance_metrics_{$user->id}_{$year}";
        
        return Cache::remember($cacheKey, 300, function () use ($user, $year) {
            $metrics = [];
            
            // Case completion rate
            $totalCases = DB::table('loan_case')
                ->whereYear('created_at', $year)
                ->where('status', '<>', 99)
                ->count();
            
            $completedCases = DB::table('loan_case')
                ->whereYear('created_at', $year)
                ->where('status', 0)
                ->count();
            
            $metrics['completion_rate'] = $totalCases > 0 ? round(($completedCases / $totalCases) * 100, 2) : 0;
            
            // Average case processing time
            $avgProcessingTime = DB::table('loan_case')
                ->selectRaw('AVG(DATEDIFF(updated_at, created_at)) as avg_days')
                ->whereYear('created_at', $year)
                ->where('status', 0)
                ->whereNotNull('updated_at')
                ->first();
            
            $metrics['avg_processing_days'] = round($avgProcessingTime->avg_days ?? 0, 1);
            
            // Monthly growth rate
            $currentMonth = Carbon::now()->month;
            $currentMonthCases = DB::table('loan_case')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $currentMonth)
                ->count();
            
            $previousMonthCases = DB::table('loan_case')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $currentMonth - 1)
                ->count();
            
            if ($previousMonthCases > 0) {
                $metrics['growth_rate'] = round((($currentMonthCases - $previousMonthCases) / $previousMonthCases) * 100, 2);
            } else {
                $metrics['growth_rate'] = 0;
            }
            
            return $metrics;
        });
    }

    /**
     * Get notes data for dashboard - matching original dashboard logic
     * @param int $limit Limit number of records to load (for lazy loading)
     */
    private function getNotesData($user, $limit = null)
    {
        $cacheKey = "notes_data_{$user->id}" . ($limit ? "_limit_{$limit}" : "");
        
        return Cache::remember($cacheKey, 180, function () use ($user, $limit) {
            $notesData = [];
            
            $date = Carbon::now()->subDays(4);
            if($user->id == 22) {
                $date = Carbon::now()->subDays(30);
            }
            
            $accessInfo = Cache::remember("access_info_{$user->id}", 300, function () {
                return AccessController::manageAccess();
            });
            
            // Get KIV notes - matching original dashboard logic
            $kivNotes = DB::table('loan_case_kiv_notes as n')
                ->join('loan_case as l', 'l.id', '=', 'n.case_id')
                ->join('users as u', 'u.id', '=', 'n.created_by')
                ->where('n.status', '=', 1)
                ->where('l.status', '<>', 99)
                ->select('n.*', 'l.case_ref_no', 'u.name as name', 'u.name as user_name', 'u.menuroles')
                ->where('n.created_at', '>=', $date);

            if (in_array($user->menuroles, ['clerk', 'lawyer', 'chambering'])) {
                $kivNotes = $kivNotes->where(function ($q) use ($user) {
                    $q->where('l.lawyer_id', $user->id)
                        ->orWhere('l.clerk_id', $user->id);
                });
            } elseif (in_array($user->menuroles, ['sales'])) {
                if (in_array($user->id, [51,127])) {
                    $kivNotes = $kivNotes->whereIn('l.sales_user_id', [32, 51,127]);
                } else if (in_array($user->id, [144])) {
                    $kivNotes = $kivNotes->whereIn('l.sales_user_id', [$user->id, 29]);
                } else {
                    $kivNotes = $kivNotes->where('l.sales_user_id', '=', $user->id);
                }
            }

            $kivNotes = $kivNotes->where(function ($q) use ($accessInfo) {
                $q->whereIn('l.branch_id',  $accessInfo['brancAccessList'])
                    ->orWhereIn('sales_user_id', $accessInfo['user_list'])
                    ->orWhereIn('clerk_id', $accessInfo['user_list'])
                    ->orWhereIn('lawyer_id', $accessInfo['user_list']);
            });

            $kivNotes = $kivNotes->orderBy('n.created_at', 'DESC');
            if ($limit) {
                $kivNotes = $kivNotes->limit($limit);
            }
            $notesData['kiv_note'] = $kivNotes->get();

            // Get PNC notes
            $pncNotes = DB::table('loan_case_pnc_notes as n')
                ->join('loan_case as l', 'l.id', '=', 'n.case_id')
                ->join('users as u', 'u.id', '=', 'n.created_by')
                ->where('n.status', '=', 1)
                ->where('l.status', '<>', 99)
                ->select('n.*', 'l.case_ref_no', 'u.name as name', 'u.name as user_name', 'u.menuroles')
                ->where('n.created_at', '>=', $date)
                ->orderBy('n.created_at', 'DESC');
            
            if ($limit) {
                $pncNotes = $pncNotes->limit($limit);
            }
            
            $notesData['pnc_note'] = $pncNotes->get();

            // Get Marketing notes - matching original dashboard logic
            $LoanMarketingNotes = collect([]);
            if (in_array($user->menuroles, ['account', 'admin', 'sales', 'maker'])) {
                $Last7Days = Carbon::now()->subDays(14);
                $marketingNotes = DB::table('loan_case_notes AS n')
                    ->join('loan_case as l', 'l.id', '=', 'n.case_id')
                    ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
                    ->where('l.status', '<>', 99)
                    ->where('n.status', '<>', 99)
                    ->where('n.created_at', '>=', $Last7Days)
                    ->select('n.*',  'l.case_ref_no', 'u.name as user_name', 'u.menuroles');

                if (in_array($user->menuroles, ['sales'])) {
                    if (in_array($user->id, [51,127])) {
                        $marketingNotes = $marketingNotes->whereIn('l.sales_user_id', [32, 51,127]);
                    } else if (in_array($user->id, [144])) {
                        $marketingNotes = $marketingNotes->whereIn('l.sales_user_id', [$user->id, 29]);
                    } else {
                        $marketingNotes = $marketingNotes->whereIn('l.sales_user_id', [$user->id]);
                    }
                } else if (in_array($user->menuroles, ['maker'])) {
                    $marketingNotes = $marketingNotes->where(function ($q) use ($accessInfo) {
                        $q->whereIn('l.branch_id',  $accessInfo['brancAccessList'])
                            ->orWhereIn('sales_user_id', $accessInfo['user_list'])
                            ->orWhereIn('clerk_id', $accessInfo['user_list'])
                            ->orWhereIn('lawyer_id', $accessInfo['user_list']);
                    });
                }

                $marketingNotes = $marketingNotes->orderBy('n.created_at', 'DESC');
                if ($limit) {
                    $marketingNotes = $marketingNotes->limit($limit);
                }
                $LoanMarketingNotes = $marketingNotes->get();
            }
            
            $notesData['LoanMarketingNotes'] = $LoanMarketingNotes;

            return $notesData;
        });
    }

    /**
     * Get today's message count
     */
    private function getTodayMessageCount($user)
    {
        $cacheKey = "today_message_count_{$user->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            $query = DB::table('loan_case_kiv_notes as n')
                ->leftJoin('loan_case as l', 'n.case_id', '=', 'l.id')
                ->where('n.status', '<>', 99)
                ->where('l.status', '<>', 99)
                ->whereDate('n.created_at', Carbon::today());

            // Apply user access restrictions
            if (in_array($user->menuroles, ['sales'])) {
                if (in_array($user->id, [144, 127])) {
                    $query = $query->whereIn('l.sales_user_id', [32, 51, 127]);
                } elseif (in_array($user->id, [32])) {
                    $query = $query->whereIn('l.sales_user_id', [$user->id, 29]);
                } else {
                    $query = $query->where('l.sales_user_id', $user->id);
                }
            }

            return $query->count();
        });
    }

    /**
     * Get user accessible branches
     */
    private function getUserAccessibleBranches($user)
    {
        if (in_array($user->menuroles, ['admin', 'management', 'account'])) {
            return Branch::where('status', 1)->pluck('id')->toArray();
        }
        
        if ($user->menuroles === 'lawyer' || $user->menuroles === 'clerk') {
            return [$user->branch_id];
        }
        
        if ($user->menuroles === 'sales') {
            // Add sales-specific branch logic here
            return [$user->branch_id];
        }
        
        return [$user->branch_id];
    }

    /**
     * API endpoint for chart data with better performance
     */
    public function getChartData(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $type = $request->input('type', 'cases');
        $user = auth()->user();
        
        $cacheKey = "chart_data_{$type}_{$user->id}_{$year}";
        
        return Cache::remember($cacheKey, 300, function () use ($type, $user, $year) {
            switch ($type) {
                case 'cases':
                    return $this->getCasesChartData($user, $year);
                case 'branch':
                    return $this->getBranchChartData($user, $year);
                case 'performance':
                    return $this->getPerformanceChartData($user, $year);
                default:
                    return response()->json(['error' => 'Invalid chart type'], 400);
            }
        });
    }

    /**
     * Get cases chart data
     */
    private function getCasesChartData($user, $year)
    {
        $monthlyData = $this->getMonthlyCaseTrends($user, $year);
        
        return response()->json([
            'status' => 1,
            'data' => [
                [
                    'branch' => 'All Cases',
                    'count' => $monthlyData
                ],
                [
                    'branch' => 'Monthly Total',
                    'count' => $monthlyData
                ]
            ]
        ]);
    }

    /**
     * Get branch chart data
     */
    private function getBranchChartData($user, $year)
    {
        $branchPerformance = $this->getBranchPerformance($user, $year);
        $result = [];
        
        foreach ($branchPerformance as $branchName => $monthlyData) {
            $result[] = [
                'branch' => $branchName,
                'count' => array_values($monthlyData)
            ];
        }
        
        // Add monthly totals
        $monthlyTotals = array_fill(0, 12, 0);
        foreach ($branchPerformance as $monthlyData) {
            foreach (array_values($monthlyData) as $index => $count) {
                $monthlyTotals[$index] += $count;
            }
        }
        
        $result[] = [
            'branch' => 'Monthly Total',
            'count' => $monthlyTotals
        ];
        
        return response()->json([
            'status' => 1,
            'data' => $result
        ]);
    }

    /**
     * Get performance chart data
     */
    private function getPerformanceChartData($user, $year)
    {
        // Implementation for performance metrics chart
        return response()->json([
            'status' => 1,
            'data' => []
        ]);
    }

    /**
     * Clear dashboard cache
     */
    public function clearCache()
    {
        $user = auth()->user();
        $currentYear = Carbon::now()->year;
        
        Cache::forget("dashboard_summary_{$user->id}_{$currentYear}");
        Cache::forget("monthly_trends_{$user->id}_{$currentYear}");
        Cache::forget("branch_performance_{$user->id}_{$currentYear}");
        Cache::forget("recent_activities_{$user->id}");
        Cache::forget("performance_metrics_{$user->id}_{$currentYear}");
        
        return response()->json(['status' => 1, 'message' => 'Cache cleared successfully']);
    }

    /**
     * Get dashboard case count (for legal cases section) - matching original dashboard logic
     */
    public function getDashboardCaseCount(Request $request)
    {
        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;
        
        // Get access info with caching
        $accessInfo = Cache::remember("access_info_{$current_user->id}", 300, function () {
            return AccessController::manageAccess();
        });
        
        $branchInfo = Cache::remember("branch_access_{$current_user->id}", 3600, function () {
            return BranchController::manageBranchAccess();
        });

        $openCaseCount = DB::table('loan_case');
        $abortCaseCount = DB::table('loan_case')->whereIn('status', [99]);
        $InProgressCaseCount = DB::table('loan_case')->whereIn('status', [1, 2, 3]);
        $closedCaseCount = DB::table('loan_case')->where('status', '=', 0);
        $OverdueCaseCount = DB::table('loan_case')->where('status', '=', 4);

        // Apply permission controls - matching original dashboard logic
        if (!in_array($userRoles, ['admin', 'management', 'account'])) {
            $accessCaseList = CaseController::caseManagementEngine();

            $openCaseCount = $openCaseCount->whereIn('id', $accessCaseList);
            $InProgressCaseCount = $InProgressCaseCount->whereIn('id', $accessCaseList);
            $OverdueCaseCount = $OverdueCaseCount->whereIn('id', $accessCaseList);
            $closedCaseCount = $closedCaseCount->whereIn('id', $accessCaseList);
            $abortCaseCount = $abortCaseCount->whereIn('id', $accessCaseList);
        }

        // Apply filters
        if ($request->input("month") != 0) {
            $openCaseCount = $openCaseCount->whereMonth('created_at', $request->input("month"));
            $InProgressCaseCount = $InProgressCaseCount->whereMonth('created_at', $request->input("month"));
            $closedCaseCount = $closedCaseCount->whereMonth('created_at', $request->input("month"));
            $OverdueCaseCount = $OverdueCaseCount->whereMonth('created_at', $request->input("month"));
            $abortCaseCount = $abortCaseCount->whereMonth('created_at', $request->input("month"));
        }

        if ($request->input("year") != 0) {
            $openCaseCount = $openCaseCount->whereYear('created_at', $request->input("year"));
            $InProgressCaseCount = $InProgressCaseCount->whereYear('created_at', $request->input("year"));
            $closedCaseCount = $closedCaseCount->whereYear('created_at', $request->input("year"));
            $OverdueCaseCount = $OverdueCaseCount->whereYear('created_at', $request->input("year"));
            $abortCaseCount = $abortCaseCount->whereYear('created_at', $request->input("year"));
        }

        if ($request->input("branch") != 0) {
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
    }

    /**
     * Get dashboard case chart (for all cases chart)
     */
    public function getDashboardCaseChart(Request $request)
    {
        // Implementation for all cases chart
        return response()->json(['status' => 1, 'message' => 'Not implemented yet']);
    }

    /**
     * Get dashboard case chart by branch
     */
    public function getDashboardCaseChartByBranch(Request $request)
    {
        // Implementation for branch chart
        return response()->json(['status' => 1, 'message' => 'Not implemented yet']);
    }

    /**
     * Get dashboard case chart by staff
     */
    public function getDashboardCaseChartByStaff(Request $request)
    {
        $current_user = auth()->user();
        $lawyerList = [];
        $lawyercount = [];

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

            $branchInfo = \App\Http\Controllers\BranchController::manageBranchAccess();
            $branchs = $branchInfo['branch'];

            for ($i = 0; $i < count($branchs); $i++) {
                array_push($branch_list,  $branchs[$i]->id);
            }

            $lawyer = $lawyer->whereIn('branch_id', $branch_list);
        }

        if($request->input("branch") != 0) {
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

            if($request->input("branch") != 0) {
                $caseCount = $caseCount->where('branch_id',  $request->input("branch"));
            }

            if (in_array($current_user->menuroles, ['sales'])) {
                if (in_array($current_user->id, [144])) {
                    $caseCount = $caseCount->whereIn('sales_user_id', [$current_user->id,29]);
                } else if (in_array($current_user->id, [32])) {
                    $caseCount = $caseCount->whereIn('branch_id', [1, 2, 5]);
                } else {
                    $caseCount = $caseCount->where('sales_user_id', $current_user->id);
                }
            }

            $caseCount = $caseCount->count();
            array_push($lawyercount,  $caseCount);
        }

        return response()->json([
            'status' => 1,
            'lawyerList' => $lawyerList,
            'lawyercount' => $lawyercount,
        ]);
    }

    /**
     * Get dashboard case chart by sales
     */
    public function getDashboardCaseChartBySales(Request $request)
    {
        $salesList = [];
        $caseCount = [];
        $current_user = auth()->user();

        $sales2 = Users::where('is_sales', 1);

        if (in_array($current_user->menuroles, ['sales'])) {
            if (in_array($current_user->id, [51,127])) {
                $sales2 = $sales2->where('id', '=', 32);
            } elseif ($current_user->id == 80) {
                $sales2 = $sales2->where('branch_id', '=', 3);
            } elseif ($current_user->id == 32) {
                $sales2 = $sales2->where('branch_id',5);
            } else {
                $sales2 = $sales2->where('id', '=', $current_user->id);
            }
        }

        if($request->input("branch")) {
            $sales = $sales2->where('branch_id', $request->input("branch"));
        }
        
        $sales = $sales2->where('status', 1)->get();

        for ($i = 0; $i < count($sales); $i++) {
            array_push($salesList,  $sales[$i]->name);

            $LoanCaseCount = LoanCase::where('status', '<>', 99)
                ->where('sales_user_id', '=', $sales[$i]->id)
                ->whereMonth('created_at', $request->input("month"))
                ->whereYear('created_at', $request->input("year"))
                ->count();

            array_push($caseCount,  $LoanCaseCount);
        }

        return response()->json([
            'status' => 1,
            'salesList' => $salesList,
            'caseCount' => $caseCount,
        ]);
    }

    /**
     * Get B2022 cases data with permission control
     */
    private function getB2022Cases($user)
    {
        $data = [
            'B2022AllCases' => 0,
            'B2022ClosedCases' => 0,
            'B2022ActiveCases' => 0,
            'B2022PendingCloseCases' => 0,
            'totalAcount' => 0,
            'totalAssigned' => 0,
            'totalUpdated' => 0,
        ];

        if ($user->menuroles == 'manager' || $user->menuroles == 'admin' || $user->menuroles == 'account') {
            $cacheKey = "b2022_cases_admin_{$user->id}";
            
            $casesData = Cache::remember($cacheKey, 300, function () {
                return [
                    'B2022PendingCloseCases' => DB::table('cases_outside_system')->where('status', '=', 3)->count(),
                    'B2022AllCases' => DB::table('cases_outside_system')->count(),
                    'B2022ClosedCases' => DB::table('cases_outside_system')->where('status', '=', 2)->count(),
                    'B2022ActiveCases' => DB::table('cases_outside_system')->where('status', '=', 1)->count(),
                ];
            });

            $data = array_merge($data, $casesData);
        } else {
            $cacheKey = "b2022_cases_user_{$user->id}";
            
            $casesData = Cache::remember($cacheKey, 300, function () use ($user) {
                return [
                    'totalAcount' => DB::table('cases_outside_system')->where('status', '=', 1)->where('old_pic_id', '=', $user->id)->count(),
                    'totalAssigned' => DB::table('cases_outside_system')->where('new_pic_id', '<>', 0)->where('old_pic_id', '=', $user->id)->count(),
                    'totalUpdated' => DB::table('cases_outside_system')->where('old_pic_id', '=', $user->id)->where('remarks', '<>', '')->count(),
                ];
            });

            $data = array_merge($data, $casesData);
        }

        return $data;
    }

    /**
     * AJAX endpoint to load all notes data (for lazy loading)
     */
    public function loadAllNotes(Request $request)
    {
        $current_user = auth()->user();
        $notesData = $this->getNotesData($current_user); // Load all without limit
        
        return response()->json([
            'kiv_note' => $notesData['kiv_note'],
            'pnc_note' => $notesData['pnc_note'],
            'LoanMarketingNotes' => $notesData['LoanMarketingNotes'],
        ]);
    }

    /**
     * AJAX endpoint to load dashboard summary
     */
    public function loadDashboardSummary(Request $request)
    {
        $current_user = auth()->user();
        $currentYear = $request->input('year', Carbon::now()->year);
        
        $dashboardSummary = $this->getDashboardSummary($current_user, $currentYear);
        
        return response()->json($dashboardSummary);
    }

    /**
     * AJAX endpoint to load recent activities
     */
    public function loadRecentActivities(Request $request)
    {
        $current_user = auth()->user();
        $recentActivities = $this->getRecentActivities($current_user);
        
        return response()->json(['activities' => $recentActivities]);
    }

    /**
     * AJAX endpoint to load performance metrics
     */
    public function loadPerformanceMetrics(Request $request)
    {
        $current_user = auth()->user();
        $currentYear = $request->input('year', Carbon::now()->year);
        
        $performanceMetrics = $this->getPerformanceMetrics($current_user, $currentYear);
        
        return response()->json($performanceMetrics);
    }
}


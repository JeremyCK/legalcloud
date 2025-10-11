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

class DashboardV2Controller extends Controller
{
    /**
     * Display the enhanced dashboard V2
     */
    public function index()
    {
        $current_user = auth()->user();
        $currentYear = Carbon::now()->year;
        
        // Get branches with caching for better performance
        $branches = Cache::remember('active_branches', 3600, function () {
            return Branch::where('status', 1)->get();
        });

        // Get dashboard summary data with caching
        $dashboardSummary = $this->getDashboardSummary($current_user, $currentYear);
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities($current_user);
        
        // Get performance metrics
        $performanceMetrics = $this->getPerformanceMetrics($current_user, $currentYear);

        // Get notes data (KIV, Marketing, PNC)
        $notesData = $this->getNotesData($current_user);
        
        // Get today's message count
        $todayMessageCount = $this->getTodayMessageCount($current_user);

        return view('dashboard.v2.index', compact(
            'current_user',
            'branches',
            'dashboardSummary',
            'recentActivities',
            'performanceMetrics',
            'currentYear',
            'notesData',
            'todayMessageCount'
        ));
    }

    /**
     * Get enhanced dashboard summary with better performance
     */
    private function getDashboardSummary($user, $year)
    {
        $cacheKey = "dashboard_summary_{$user->id}_{$year}";
        
        return Cache::remember($cacheKey, 300, function () use ($user, $year) {
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
                ->whereYear('created_at', $year)
                ->where('status', '<>', 99);

            // Apply user access restrictions
            if (!in_array($user->menuroles, ['admin', 'management', 'account'])) {
                $accessibleBranches = $this->getUserAccessibleBranches($user);
                $caseCounts = $caseCounts->whereIn('branch_id', $accessibleBranches);
            }

            $caseData = $caseCounts->first();
            
            $summary['total_cases'] = $caseData->total_cases ?? 0;
            $summary['closed_cases'] = $caseData->closed_cases ?? 0;
            $summary['in_progress_cases'] = $caseData->in_progress_cases ?? 0;
            $summary['overdue_cases'] = $caseData->overdue_cases ?? 0;
            $summary['aborted_cases'] = $caseData->aborted_cases ?? 0;
            $summary['open_cases'] = $summary['total_cases'] - $summary['closed_cases'] - $summary['aborted_cases'];
            
            // Get monthly case trends
            $summary['monthly_trends'] = $this->getMonthlyCaseTrends($user, $year);
            
            // Get branch performance
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
     * Get notes data for dashboard
     */
    private function getNotesData($user)
    {
        $cacheKey = "notes_data_{$user->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            $notesData = [];
            
            // Get KIV notes
            $kivNotes = DB::table('loan_case_kiv_notes as n')
                ->leftJoin('loan_case as l', 'n.case_id', '=', 'l.id')
                ->leftJoin('users as u', 'n.created_by', '=', 'u.id')
                ->select('n.*', 'l.case_ref_no', 'l.branch_id', 'l.sales_user_id', 'u.name as user_name')
                ->where('n.status', '<>', 99)
                ->where('l.status', '<>', 99);

            // Apply user access restrictions
            if (in_array($user->menuroles, ['sales'])) {
                if (in_array($user->id, [144, 127])) {
                    $kivNotes = $kivNotes->whereIn('l.sales_user_id', [32, 51, 127]);
                } elseif (in_array($user->id, [32])) {
                    $kivNotes = $kivNotes->whereIn('l.sales_user_id', [$user->id, 29]);
                } else {
                    $kivNotes = $kivNotes->where('l.sales_user_id', $user->id);
                }
            }

            $notesData['kiv_note'] = $kivNotes->orderBy('n.created_at', 'DESC')->get();

            // Get Marketing notes
            $marketingNotes = DB::table('loan_case_kiv_notes as n')
                ->leftJoin('loan_case as l', 'n.case_id', '=', 'l.id')
                ->leftJoin('users as u', 'n.created_by', '=', 'u.id')
                ->select('n.*', 'l.case_ref_no', 'l.branch_id', 'l.sales_user_id', 'u.name as user_name')
                ->where('n.status', '<>', 99)
                ->where('l.status', '<>', 99)
                ->where('n.label', 'operation|marketing');

            if (in_array($user->menuroles, ['sales'])) {
                if (in_array($user->id, [144, 127])) {
                    $marketingNotes = $marketingNotes->whereIn('l.sales_user_id', [32, 51, 127]);
                } elseif (in_array($user->id, [32])) {
                    $marketingNotes = $marketingNotes->whereIn('l.sales_user_id', [$user->id, 29]);
                } else {
                    $marketingNotes = $marketingNotes->where('l.sales_user_id', $user->id);
                }
            }

            $notesData['LoanMarketingNotes'] = $marketingNotes->orderBy('n.created_at', 'DESC')->get();

            // Get PNC notes (for management users)
            if ($user->management == 1) {
                $pncNotes = DB::table('loan_case_kiv_notes as n')
                    ->leftJoin('loan_case as l', 'n.case_id', '=', 'l.id')
                    ->leftJoin('users as u', 'n.created_by', '=', 'u.id')
                    ->select('n.*', 'l.case_ref_no', 'l.branch_id', 'l.sales_user_id', 'u.name as user_name')
                    ->where('n.status', '<>', 99)
                    ->where('l.status', '<>', 99)
                    ->where('n.label', 'operation|pnc');

                $notesData['pnc_note'] = $pncNotes->orderBy('n.created_at', 'DESC')->get();
            } else {
                $notesData['pnc_note'] = collect([]);
            }

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
     * Get dashboard case count (for legal cases section)
     */
    public function getDashboardCaseCount(Request $request)
    {
        // Implementation for legal case count
        return response()->json(['status' => 1, 'message' => 'Not implemented yet']);
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
}

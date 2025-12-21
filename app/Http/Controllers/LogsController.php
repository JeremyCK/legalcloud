<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\AccountItem;
use App\Models\AccountLog;
use App\Models\Adjudication;
use App\Models\Branch;
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
use App\Models\CHKT;
use App\Models\LegalCloudCaseActivityLog;
use App\Models\LoanCaseAccount;
use App\Models\LoanCase;
use App\Models\LoanCaseKivNotes;
use App\Models\SafeKeeping;
use App\Models\User;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\PermissionController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Barryvdh\DomPDF\Facade\Pdf;

class LogsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $current_user = auth()->user();

        $branch = DB::table('branch')->where('status', '=', 1)->get();

        return view('dashboard.chkt.index', [
            'branches' => $branch,
            'current_user' => $current_user
        ]);
    }

    public function accountLog()
    {
        $current_user = auth()->user();

        $branch = DB::table('branch')->where('status', '=', 1)->get();
        $user = User::whereIn('menuroles', ['clerk','account','clerk','chambering','lawyer'])->orderBy('name', 'ASC')->get();

        return view('dashboard.logs.account_log.index', [
            'branches' => $branch,
            'users' => $user,
            'current_user' => $current_user
        ]);
    }

    public function getAccountLog(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            // $d = date_parse_from_format("Y-m-d", $request->input("recon_date"));

            $safe_keeping = DB::table('account_log as a')
                ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
                ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'a.bill_id')
                ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
                ->select('a.*', 'u.name as perform_by', 'l.case_ref_no', 'b.bill_no')
                // ->whereBetween('a.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->whereMonth('a.created_at', 8)
                ->where('a.status', '<>', 99);



            // if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
            //     $safe_keeping = $safe_keeping->whereBetween('a.created_at', [$request->input("date_from"), $request->input("date_to")]);
            // } else {
            //     if ($request->input("date_from") <> null) {
            //         $safe_keeping = $safe_keeping->where('a.created_at', '>=', $request->input("date_from"));
            //     }

            //     if ($request->input("date_to") <> null) {
            //         $safe_keeping = $safe_keeping->where('a.created_at', '<=', $request->input("date_to"));
            //     }
            // }

            // if ($request->input("status") <> 99) {
            //     $safe_keeping->where('a.file_ori_name', '=', $request->input("status") );
            // }

            if ($request->input("ref_no") != "") {
                $safe_keeping->where('l.case_ref_no', 'like', '%'.$request->input("ref_no").'%');
            }


            if ($request->input("user") <> 0) {
                $safe_keeping->where('a.user_id', '=', $request->input("user"));
            }

            $safe_keeping = $safe_keeping->orderBy('a.created_at', 'DESC')->get();


            return DataTables::of($safe_keeping)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a target="_blank" href="/chkt/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>';
                    return $actionBtn;
                })
                ->addColumn('desc', function ($row) {
                    $actionBtn = '<div>' . $row->desc . '</div>';
                    return $actionBtn;
                })
                ->editColumn('case_ref_no', function ($row) {
                    if ($row->case_id != 0) {
                        $actionBtn = ' <a target="_blank" href="/case/' . $row->case_id . '" class="  " >' . $row->case_ref_no . ' >> </a>';
                    } else {
                        $actionBtn = $row->case_ref;
                    }

                    return $actionBtn;
                })
                ->rawColumns(['action', 'case_ref_no', 'desc'])
                ->make(true);
        }
    }

    /**
     * Display audit trail page
     */
    public function auditTrail()
    {
        $current_user = auth()->user();
        
        // Check permission using UserAccessControl table
        if (AccessController::UserAccessPermissionController(PermissionController::AuditTrailPermission()) == false) {
            return redirect()->route('dashboard.index')->with('error', 'You do not have permission to access audit trail.');
        }
        
        $users = User::whereIn('menuroles', ['clerk','account','chambering','lawyer','admin','management'])->orderBy('name', 'ASC')->get();

        return view('dashboard.logs.audit_trail.index', [
            'users' => $users,
            'current_user' => $current_user
        ]);
    }

    /**
     * Get audit trail data (unified from both tables)
     */
    public function getAuditTrail(Request $request)
    {
        if ($request->ajax()) {
            $case_id = $request->input('case_id');
            $current_user = auth()->user();
            $userRoles = $current_user->menuroles;
            
            // Require case_id to be provided
            if (empty($case_id) || $case_id == 0) {
                return DataTables::of(collect([]))
                    ->addIndexColumn()
                    ->make(true);
            }

            // Check if user has access to this case
            if (!in_array($userRoles, ['admin', 'management', 'account'])) {
                $accessCaseList = CaseController::caseManagementEngine();
                
                if (!empty($accessCaseList) && !in_array($case_id, $accessCaseList)) {
                    // User doesn't have access to this case
                    return DataTables::of(collect([]))
                        ->addIndexColumn()
                        ->make(true);
                } elseif (empty($accessCaseList)) {
                    // User has no accessible cases
                    return DataTables::of(collect([]))
                        ->addIndexColumn()
                        ->make(true);
                }
            }

            // Get activity logs
            $activityLogs = DB::table('legalcloud_case_activity_log as a')
                ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
                ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
                ->select(
                    'a.id',
                    'a.created_at',
                    'a.action',
                    'a.desc',
                    'a.ori_text',
                    'a.edit_text',
                    'a.object_id',
                    'a.object_id_2',
                    'u.name as user_name',
                    'l.case_ref_no',
                    DB::raw("NULL as bill_no"),
                    DB::raw("'activity' as log_type")
                )
                ->where('a.case_id', '=', $case_id)
                ->where('a.status', '<>', 99);

            // Get account logs
            $accountLogs = DB::table('account_log as a')
                ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
                ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'a.bill_id')
                ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
                ->select(
                    'a.id',
                    'a.created_at',
                    'a.action',
                    'a.desc',
                    'a.ori_amt as ori_text',
                    'a.new_amt as edit_text',
                    'a.object_id',
                    DB::raw("NULL as object_id_2"),
                    'u.name as user_name',
                    'l.case_ref_no',
                    'b.bill_no',
                    DB::raw("'account' as log_type")
                )
                ->where('a.case_id', '=', $case_id)
                ->where('a.status', '<>', 99);

            // Apply user filter
            if ($request->input("user") != "" && $request->input("user") != "0") {
                $activityLogs->where('a.user_id', '=', $request->input("user"));
                $accountLogs->where('a.user_id', '=', $request->input("user"));
            }

            // Apply action filter
            if ($request->input("action_type") != "" && $request->input("action_type") != "all") {
                $activityLogs->where('a.action', '=', $request->input("action_type"));
                $accountLogs->where('a.action', '=', $request->input("action_type"));
            }

            // Union both queries and order by created_at DESC
            $combinedLogs = $activityLogs->union($accountLogs)
                ->orderBy('created_at', 'DESC')
                ->get();

            return DataTables::of($combinedLogs)
                ->addIndexColumn()
                ->addColumn('log_type_badge', function ($row) {
                    if ($row->log_type == 'activity') {
                        return '<span class="badge badge-primary">Activity</span>';
                    } else {
                        return '<span class="badge badge-info">Account</span>';
                    }
                })
                ->addColumn('changes', function ($row) {
                    $changes = '';
                    
                    if ($row->log_type == 'account') {
                        // For account logs, show amount changes only if meaningful
                        $oriAmt = !empty($row->ori_text) ? floatval($row->ori_text) : 0;
                        $newAmt = !empty($row->edit_text) ? floatval($row->edit_text) : 0;
                        
                        // Only show if amounts are different and at least one is not zero
                        if ($oriAmt != $newAmt && ($oriAmt != 0 || $newAmt != 0)) {
                            $changes = '<small class="text-muted">From: RM ' . number_format($oriAmt, 2) . ' → To: RM ' . number_format($newAmt, 2) . '</small>';
                        } elseif ($oriAmt != 0 && $newAmt != 0 && $oriAmt == $newAmt) {
                            // If both are the same non-zero value, show it once
                            $changes = '<small class="text-info">Amount: RM ' . number_format($oriAmt, 2) . '</small>';
                        }
                        // If both are 0 or empty, don't show anything
                    } else {
                        // For activity logs, show text changes only if meaningful
                        $oriText = trim($row->ori_text ?? '');
                        $editText = trim($row->edit_text ?? '');
                        
                        if (!empty($oriText) && !empty($editText)) {
                            if ($oriText !== $editText) {
                                // Different values - show change
                                $changes = '<small class="text-muted">From: ' . htmlspecialchars($oriText) . ' → To: ' . htmlspecialchars($editText) . '</small>';
                            }
                            // If same, don't show
                        } elseif (!empty($editText) && empty($oriText)) {
                            // New value added
                            $changes = '<small class="text-success">New: ' . htmlspecialchars($editText) . '</small>';
                        } elseif (!empty($oriText) && empty($editText)) {
                            // Value removed
                            $changes = '<small class="text-danger">Removed: ' . htmlspecialchars($oriText) . '</small>';
                        }
                        // If both empty, don't show anything
                    }
                    
                    return $changes;
                })
                ->addColumn('action_badge', function ($row) {
                    $badgeClass = 'badge-secondary';
                    $actionText = $row->action;
                    
                    // Color code based on action type
                    if (strpos(strtolower($actionText), 'create') !== false || strpos(strtolower($actionText), 'add') !== false) {
                        $badgeClass = 'badge-success';
                    } elseif (strpos(strtolower($actionText), 'update') !== false || strpos(strtolower($actionText), 'edit') !== false) {
                        $badgeClass = 'badge-warning';
                    } elseif (strpos(strtolower($actionText), 'delete') !== false || strpos(strtolower($actionText), 'remove') !== false) {
                        $badgeClass = 'badge-danger';
                    }
                    
                    return '<span class="badge ' . $badgeClass . '">' . htmlspecialchars($actionText) . '</span>';
                })
                ->editColumn('desc', function ($row) {
                    $desc = $row->desc;
                    if ($row->log_type == 'account' && isset($row->bill_no) && !empty($row->bill_no)) {
                        $desc .= ' <small class="text-muted">(Bill: ' . $row->bill_no . ')</small>';
                    }
                    return '<div>' . $desc . '</div>';
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
                })
                ->rawColumns(['log_type_badge', 'action_badge', 'desc', 'changes'])
                ->make(true);
        }
    }

    /**
     * Search cases for autocomplete (with access control)
     */
    public function searchCaseForAudit(Request $request)
    {
        if ($request->ajax()) {
            $searchTerm = $request->input('term', '');
            
            if (empty($searchTerm)) {
                return response()->json([]);
            }

            $current_user = auth()->user();
            $userRoles = $current_user->menuroles;

            $cases = DB::table('loan_case as l')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->select('l.id', 'l.case_ref_no', 'l.bank_ref', 'c.name as client_name')
                ->where('l.status', '<>', 99)
                ->where(function($query) use ($searchTerm) {
                    $query->where('l.case_ref_no', 'like', '%' . $searchTerm . '%')
                          ->orWhere('l.bank_ref', 'like', '%' . $searchTerm . '%')
                          ->orWhere('c.name', 'like', '%' . $searchTerm . '%');
                });

            // Apply access control - only show cases user can access
            if (!in_array($userRoles, ['admin', 'management', 'account'])) {
                $accessCaseList = CaseController::caseManagementEngine();
                
                if (!empty($accessCaseList)) {
                    $cases = $cases->whereIn('l.id', $accessCaseList);
                } else {
                    // If user has no accessible cases, return empty
                    return response()->json([]);
                }
            }

            $cases = $cases->orderBy('l.case_ref_no', 'ASC')
                ->limit(20)
                ->get();

            $results = [];
            foreach ($cases as $case) {
                $results[] = [
                    'id' => $case->id,
                    'value' => $case->case_ref_no,
                    'label' => $case->case_ref_no . ' - ' . ($case->client_name ?? 'N/A') . ($case->bank_ref ? ' (' . $case->bank_ref . ')' : ''),
                    'case_ref_no' => $case->case_ref_no,
                    'client_name' => $case->client_name,
                    'bank_ref' => $case->bank_ref
                ];
            }

            return response()->json($results);
        }
    }

    /**
     * Export audit trail to Excel
     */
    public function exportAuditTrailExcel(Request $request)
    {
        $case_id = $request->input('case_id');
        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;

        // Check permission
        if (AccessController::UserAccessPermissionController(PermissionController::AuditTrailPermission()) == false) {
            abort(403, 'Unauthorized access to audit trail');
        }

        // Check if user has access to this case
        if (!in_array($userRoles, ['admin', 'management', 'account'])) {
            $accessCaseList = CaseController::caseManagementEngine();
            if (empty($accessCaseList) || !in_array($case_id, $accessCaseList)) {
                abort(403, 'You do not have access to this case');
            }
        }

        if (empty($case_id) || $case_id == 0) {
            return redirect()->back()->with('error', 'Please select a case first');
        }

        // Get case info
        $case = LoanCase::where('id', $case_id)->first();
        if (!$case) {
            return redirect()->back()->with('error', 'Case not found');
        }

        // Get audit trail data (same query as getAuditTrail but get all records)
        $activityLogs = DB::table('legalcloud_case_activity_log as a')
            ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->select(
                'a.id',
                'a.created_at',
                'a.action',
                'a.desc',
                'a.ori_text',
                'a.edit_text',
                'a.object_id',
                'a.object_id_2',
                'u.name as user_name',
                'l.case_ref_no',
                DB::raw("NULL as bill_no"),
                DB::raw("'activity' as log_type")
            )
            ->where('a.case_id', '=', $case_id)
            ->where('a.status', '<>', 99);

        $accountLogs = DB::table('account_log as a')
            ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
            ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'a.bill_id')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->select(
                'a.id',
                'a.created_at',
                'a.action',
                'a.desc',
                'a.ori_amt as ori_text',
                'a.new_amt as edit_text',
                'a.object_id',
                DB::raw("NULL as object_id_2"),
                'u.name as user_name',
                'l.case_ref_no',
                'b.bill_no',
                DB::raw("'account' as log_type")
            )
            ->where('a.case_id', '=', $case_id)
            ->where('a.status', '<>', 99);

        // Apply filters
        if ($request->input("user") != "" && $request->input("user") != "0") {
            $activityLogs->where('a.user_id', '=', $request->input("user"));
            $accountLogs->where('a.user_id', '=', $request->input("user"));
        }

        if ($request->input("action_type") != "" && $request->input("action_type") != "all") {
            $activityLogs->where('a.action', '=', $request->input("action_type"));
            $accountLogs->where('a.action', '=', $request->input("action_type"));
        }

        $combinedLogs = $activityLogs->union($accountLogs)
            ->orderBy('created_at', 'DESC')
            ->get();

        // Prepare data for Excel
        $data = [];
        $rowNum = 1;
        foreach ($combinedLogs as $log) {
            $changes = '';
            if ($log->log_type == 'account') {
                $oriAmt = !empty($log->ori_text) ? floatval($log->ori_text) : 0;
                $newAmt = !empty($log->edit_text) ? floatval($log->edit_text) : 0;
                if ($oriAmt != $newAmt && ($oriAmt != 0 || $newAmt != 0)) {
                    $changes = 'From: RM ' . number_format($oriAmt, 2) . ' → To: RM ' . number_format($newAmt, 2);
                }
            } else {
                $oriText = trim($log->ori_text ?? '');
                $editText = trim($log->edit_text ?? '');
                if (!empty($oriText) && !empty($editText) && $oriText !== $editText) {
                    $changes = 'From: ' . $oriText . ' → To: ' . $editText;
                } elseif (!empty($editText) && empty($oriText)) {
                    $changes = 'New: ' . $editText;
                } elseif (!empty($oriText) && empty($editText)) {
                    $changes = 'Removed: ' . $oriText;
                }
            }

            $data[] = [
                'No' => $rowNum++,
                'Timestamp' => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
                'Type' => ucfirst($log->log_type),
                'User' => $log->user_name ?? 'N/A',
                'Action' => $log->action,
                'Description' => $log->desc,
                'Changes' => $changes
            ];
        }

        // Create Excel file
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set title
        $sheet->setCellValue('A1', 'Audit Trail Report');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Case info
        $sheet->setCellValue('A2', 'Case Reference: ' . $case->case_ref_no);
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getFont()->setBold(true);
        
        // Headers
        $headers = ['No', 'Timestamp', 'Type', 'User', 'Action', 'Description', 'Changes'];
        $col = 'A';
        $row = 4;
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->getStyle($col . $row)->getFont()->setBold(true);
            $sheet->getStyle($col . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E0E0E0');
            $col++;
        }
        
        // Add data
        $row = 5;
        foreach ($data as $item) {
            $col = 'A';
            foreach ($item as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }
        
        // Style data rows
        $dataRange = 'A5:G' . ($row - 1);
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);
        
        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set response headers - sanitize filename to remove invalid characters
        $sanitizedCaseRef = str_replace(['/', '\\'], '_', $case->case_ref_no);
        $filename = 'Audit_Trail_' . $sanitizedCaseRef . '_' . date('Y-m-d') . '.xlsx';
        $response = response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename);
        
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');
        
        return $response;
    }

    /**
     * Export audit trail to PDF
     */
    public function exportAuditTrailPDF(Request $request)
    {
        $case_id = $request->input('case_id');
        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;

        // Check permission
        if (AccessController::UserAccessPermissionController(PermissionController::AuditTrailPermission()) == false) {
            abort(403, 'Unauthorized access to audit trail');
        }

        // Check if user has access to this case
        if (!in_array($userRoles, ['admin', 'management', 'account'])) {
            $accessCaseList = CaseController::caseManagementEngine();
            if (empty($accessCaseList) || !in_array($case_id, $accessCaseList)) {
                abort(403, 'You do not have access to this case');
            }
        }

        if (empty($case_id) || $case_id == 0) {
            return redirect()->back()->with('error', 'Please select a case first');
        }

        // Get case info
        $case = LoanCase::where('id', $case_id)->first();
        if (!$case) {
            return redirect()->back()->with('error', 'Case not found');
        }

        // Get audit trail data (same query as getAuditTrail)
        $activityLogs = DB::table('legalcloud_case_activity_log as a')
            ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->select(
                'a.id',
                'a.created_at',
                'a.action',
                'a.desc',
                'a.ori_text',
                'a.edit_text',
                'a.object_id',
                'a.object_id_2',
                'u.name as user_name',
                'l.case_ref_no',
                DB::raw("NULL as bill_no"),
                DB::raw("'activity' as log_type")
            )
            ->where('a.case_id', '=', $case_id)
            ->where('a.status', '<>', 99);

        $accountLogs = DB::table('account_log as a')
            ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
            ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'a.bill_id')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->select(
                'a.id',
                'a.created_at',
                'a.action',
                'a.desc',
                'a.ori_amt as ori_text',
                'a.new_amt as edit_text',
                'a.object_id',
                DB::raw("NULL as object_id_2"),
                'u.name as user_name',
                'l.case_ref_no',
                'b.bill_no',
                DB::raw("'account' as log_type")
            )
            ->where('a.case_id', '=', $case_id)
            ->where('a.status', '<>', 99);

        // Apply filters
        if ($request->input("user") != "" && $request->input("user") != "0") {
            $activityLogs->where('a.user_id', '=', $request->input("user"));
            $accountLogs->where('a.user_id', '=', $request->input("user"));
        }

        if ($request->input("action_type") != "" && $request->input("action_type") != "all") {
            $activityLogs->where('a.action', '=', $request->input("action_type"));
            $accountLogs->where('a.action', '=', $request->input("action_type"));
        }

        $combinedLogs = $activityLogs->union($accountLogs)
            ->orderBy('created_at', 'DESC')
            ->get();

        // Prepare data for PDF
        $data = [];
        $rowNum = 1;
        foreach ($combinedLogs as $log) {
            $changes = '';
            if ($log->log_type == 'account') {
                $oriAmt = !empty($log->ori_text) ? floatval($log->ori_text) : 0;
                $newAmt = !empty($log->edit_text) ? floatval($log->edit_text) : 0;
                if ($oriAmt != $newAmt && ($oriAmt != 0 || $newAmt != 0)) {
                    $changes = 'From: RM ' . number_format($oriAmt, 2) . ' → To: RM ' . number_format($newAmt, 2);
                }
            } else {
                $oriText = trim($log->ori_text ?? '');
                $editText = trim($log->edit_text ?? '');
                if (!empty($oriText) && !empty($editText) && $oriText !== $editText) {
                    $changes = 'From: ' . $oriText . ' → To: ' . $editText;
                } elseif (!empty($editText) && empty($oriText)) {
                    $changes = 'New: ' . $editText;
                } elseif (!empty($oriText) && empty($editText)) {
                    $changes = 'Removed: ' . $oriText;
                }
            }

            $data[] = [
                'no' => $rowNum++,
                'timestamp' => Carbon::parse($log->created_at)->format('Y-m-d H:i:s'),
                'type' => ucfirst($log->log_type),
                'user' => $log->user_name ?? 'N/A',
                'action' => $log->action,
                'description' => $log->desc,
                'changes' => $changes
            ];
        }

        // Generate PDF - sanitize filename to remove invalid characters
        $sanitizedCaseRef = str_replace(['/', '\\'], '_', $case->case_ref_no);
        $filename = 'Audit_Trail_' . $sanitizedCaseRef . '_' . date('Y-m-d') . '.pdf';
        
        $pdf = Pdf::loadView('dashboard.logs.audit_trail.export-pdf', [
            'data' => $data,
            'case' => $case,
            'generated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'generated_by' => $current_user->name
        ]);
        
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download($filename);
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
        $loan_case = DB::table('loan_case as l')
            ->join('client as c', 'c.id', '=', 'l.customer_id')
            ->select('l.*', 'c.name')
            ->where('l.status', '=', 1)
            ->get();

        $branch = Branch::where('status', '=', 1)->orderBy('id', 'ASC')->get();

        return view('dashboard.chkt.create', [
            'loan_case' => $loan_case,
            'branch' => $branch,
        ]);
    }

    public function store(Request $request)
    {
        // $validatedData = $request->validate([
        //     'case_id'             => 'required',
        //     'client_id'        => 'required',
        // ]);

        $case_id = 0;

        $current_user = auth()->user();

        $chkt  = new CHKT();

        $running_no = (int)filter_var($request->input('case_ref_no'), FILTER_SANITIZE_NUMBER_INT);

        if (LoanCase::where('case_ref_no', 'like', '%'.$running_no.'%')->count() > 0) {
            $case_id = $request->input('case_id');
        }


        $chkt->case_id = $case_id;
        $chkt->case_ref = $request->input('case_ref_no');
        $chkt->client_id = 0;
        $chkt->client_name = $request->input('client');
        $chkt->last_spa_date = $request->input('last_spa_date');
        $chkt->current_spa_date = $request->input('current_spa_date');
        $chkt->chkt_filled_on = $request->input('chkt_filled_on');
        $chkt->per3_rpgt_paid = $request->input('per3_rpgt_paid');
        $chkt->created_by = $current_user->id;
        $chkt->branch = $request->input('branch');
        $chkt->remark = $request->input('remark');
        $chkt->status = 1;
        $chkt->created_at = date('Y-m-d H:i:s');
        $chkt->save();

        $file = $request->file('attachment_file');

        if ($file) {
            $oriFilename = $file->getClientOriginalName();
            $filename = time() . '_chkt_' . $file->getClientOriginalName();

            $location = 'app/documents/chkt/';

            // Upload file
            $file->move($location, $filename);


            $chkt->file_ori_name = $oriFilename;
            $chkt->file_new_name = $filename;
            $chkt->save();
        }

        $status_span = '';

        if ($chkt->per3_rpgt_paid == '1') {
            $status_span = '<span class="label bg-success">Yes</span>';
        } else {
            $status_span = '<span class="label bg-warning">No</span>';
        }

        $message = '
        <a href="/chkt/' . $chkt->id . '/edit" target="_blank">[Created&nbsp;<b>CHKT</b> record]</a><br />
        <strong>Last SPA Date</strong>:&nbsp;' . $request->input('last_spa_date') . '<br />
        <strong>Current SPA Date</strong>:&nbsp;' . $request->input('current_spa_date') . '<br />
        <strong>CHKT Filed On</strong>:&nbsp;' . $request->input('chkt_filled_on') . '<br />
        <strong>Remark</strong>:&nbsp;' . $request->input('remark') . '<br />
        <strong>Received Notis Taksiran</strong>:&nbsp;<a target="_blank" href="/app/documents/chkt/' . $chkt->file_new_name . '" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>' . $chkt->file_ori_name . '</a><br />
        <strong>3% RPGT Paid</strong>:&nbsp;' . $status_span;

        $LoanCaseKivNotes = new LoanCaseKivNotes();

        $LoanCaseKivNotes->case_id =  $case_id;
        $LoanCaseKivNotes->notes =  $message;
        $LoanCaseKivNotes->label =  'operation|chkt';
        $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
        $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');

        $LoanCaseKivNotes->status =  1;
        $LoanCaseKivNotes->object_id_1 =  $chkt->id;
        $LoanCaseKivNotes->created_by = $current_user->id;
        $LoanCaseKivNotes->save();

        $request->session()->flash('message', 'Successfully created new record');
        return redirect()->route('chkt.index');
    }

    public function edit($id)
    {
        $Adjudication = DB::table('chkt as a')
            ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
            ->join('users as u', 'u.id', '=', 'a.created_by')
            ->select('a.*', 'l.case_ref_no', 'u.name as assign_by')
            ->where('a.id', '=', $id)
            ->first();

        $loan_case = DB::table('loan_case as l')
            ->join('client as c', 'c.id', '=', 'l.customer_id')
            ->select('l.*', 'c.name')
            ->where('l.status', '=', 1)
            ->get();


        $branch = Branch::where('status', '=', 1)->orderBy('id', 'ASC')->get();

        return view('dashboard.chkt.edit', [
            'loan_case' => $loan_case,
            'branch' => $branch,
            'adjudication' => $Adjudication
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
        //     'case_id'             => 'required',
        //     'client_id'        => 'required',
        // ]);

        $current_user = auth()->user();
        $chkt = CHKT::where('id', '=', $id)->first();

        $case_id = 0;

        if (LoanCase::where('case_ref_no', '=', $request->input('case_ref_no'))->count() > 0) {
            $case_id = $request->input('case_id');
        }


        if ($chkt) {

            $chkt->case_id = $case_id;
            $chkt->case_ref = $request->input('case_ref_no');
            $chkt->client_id = 0;
            $chkt->client_name = $request->input('client');
            $chkt->last_spa_date = $request->input('last_spa_date');
            $chkt->current_spa_date = $request->input('current_spa_date');
            $chkt->per3_rpgt_paid = $request->input('per3_rpgt_paid');
            $chkt->created_by = $current_user->id;
            $chkt->branch = $request->input('branch');
            $chkt->remark = $request->input('remark');
            $chkt->status = 1;
            $chkt->created_at = date('Y-m-d H:i:s');
            $chkt->save();

            $file = $request->file('attachment_file');

            if ($file) {

                $delete_path = 'app/documents/chkt/' . $chkt->file_new_name;
                if (File::exists(public_path($delete_path))) {
                    File::delete(public_path($delete_path));
                }


                $oriFilename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = 'CHKT_' . $case_id . '_' . time() . '.' . $extension;

                $location = 'app/documents/chkt/';

                // Upload file
                $file->move($location, $filename);


                $chkt->file_ori_name = $oriFilename;
                $chkt->file_new_name = $filename;
                $chkt->save();
            }

            $LoanCaseKivNotes = LoanCaseKivNotes::where('object_id_1', '=', $id)->where('label', '=', 'operation|chkt')->first();

            if ($LoanCaseKivNotes) {
                $status_span = '';

                if ($chkt->per3_rpgt_paid == '1') {
                    $status_span = '<span class="label bg-success">Yes</span>';
                } else {
                    $status_span = '<span class="label bg-warning">No</span>';
                }

                $message = '
                <a href="/chkt/' . $chkt->id . '/edit" target="_blank">[Created&nbsp;<b>CHKT</b> record]</a><br />
                <strong>Last SPA Date</strong>:&nbsp;' . $request->input('last_spa_date') . '<br />
                <strong>Current SPA Date</strong>:&nbsp;' . $request->input('current_spa_date') . '<br />
                <strong>CHKT Filed On</strong>:&nbsp;' . $request->input('chkt_filled_on') . '<br />
                <strong>Remark</strong>:&nbsp;' . $request->input('remark') . '<br />
                <strong>Received Notis Taksiran</strong>:&nbsp;<a target="_blank" href="/app/documents/chkt/' . $chkt->file_new_name . '" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>' . $chkt->file_ori_name . '</a><br />
                <strong>3% RPGT Paid</strong>:&nbsp;' . $status_span;

                $LoanCaseKivNotes->notes =  $message;
                $LoanCaseKivNotes->updated_at = date('Y-m-d H:i:s');
                $LoanCaseKivNotes->updated_by = $current_user->id;
                $LoanCaseKivNotes->save();
            }
        }

        $request->session()->flash('message', 'Successfully updated record');
        return redirect()->route('chkt.index');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    public static function createAccountLog($param_log)
    {
        $current_user = auth()->user();
        $AccountLog = new AccountLog();

        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $param_log['case_id'];
        $AccountLog->bill_id = $param_log['bill_id'];
        $AccountLog->object_id = $param_log['object_id'];
        $AccountLog->ori_amt = $param_log['ori_amt'];
        $AccountLog->new_amt = $param_log['new_amt'];
        $AccountLog->action = $param_log['action'];
        $AccountLog->desc = $param_log['desc'];
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();
    }

    public static function generateLog($param_log)
    {
        $current_user = auth()->user();

        $LegalCloudCaseActivityLog  = new LegalCloudCaseActivityLog();
        $LegalCloudCaseActivityLog->user_id = $current_user->id;
        $LegalCloudCaseActivityLog->case_id = $param_log['case_id'];
        $LegalCloudCaseActivityLog->action = $param_log['action'];
        $LegalCloudCaseActivityLog->desc = $current_user->name.$param_log['desc'];
        $LegalCloudCaseActivityLog->status = 1;
        $LegalCloudCaseActivityLog->object_id = $param_log['object_id'];

        if(isset($param_log['object_id_2']))
        {
            $LegalCloudCaseActivityLog->object_id_2 = $param_log['object_id_2'];
        }

        if(isset($param_log['ori_text']))
        {
            $LegalCloudCaseActivityLog->ori_text = $param_log['ori_text'];
        }

        if(isset($param_log['edit_text']))
        {
            $LegalCloudCaseActivityLog->edit_text = $param_log['edit_text'];
        }
        
        // $LegalCloudCaseActivityLog->created_at = date('Y-m-d H:i:s');    
        $LegalCloudCaseActivityLog->save();
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

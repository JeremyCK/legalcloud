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
use App\Models\ActivityLog;
use App\Models\Adjudication;
use App\Models\Bonus;
use App\Models\Branch;
use App\Models\CaseTransferLog;
use App\Models\CHKT;
use App\Models\Dispatch;
use App\Models\DocumentTemplateFileFolder;
use App\Models\LandOffice;
use App\Models\LegalCloudCaseActivityLog;
use App\Models\LoanCaseAccountFiles;
use App\Models\LoanCaseBillAccount;
use App\Models\LoanCaseInvoiceDetails;
use App\Models\LoanCaseKivNotes;
use App\Models\LoanCaseTrustMain;
use App\Models\Notification;
use App\Models\OfficeBankAccount;
use App\Models\PrepareDocs;
use App\Models\Referral;
use App\Models\ReturnCall;
use App\Models\RptCase;
use App\Models\SafeKeeping;
use App\Models\TeamPortfolios;
use Carbon\Carbon;
use Illuminate\Support\Arr;
// use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use NumberFormatter;
use PhpOffice\PhpSpbln_readsheet\Style\NumberFormat\NumberFormatter as NumberFormatNumberFormatter;
use PhpOffice\PhpWord\TemplateProcessor;
use Yajra\DataTables\Facades\DataTables;

use App\Http\Controllers\QueryController;
use App\Http\Controllers\ImageController;
use App\Models\BonusEstimate;
use App\Models\BonusRequestList;
use App\Models\CaseArchive;
use App\Models\CaseMasterListMainCat;
use App\Models\CheckListDetails;
use App\Models\CheckListMain;
use App\Models\CheckListTemplateDetailsV2;
use App\Models\CheckListTemplateMainV2;
use App\Models\CheckTemplateMainV2;
use App\Models\ClaimRequest;
use App\Models\InvoiceBillingParty;
use App\Models\LedgerEntries;
use App\Models\LedgerEntriesV2;
use App\Models\LoanCaseBillReferrals;
use App\Models\LoanCaseChecklistDetailsV2;
use App\Models\LoanCaseChecklistMainV2;
use App\Models\LoanCaseInvoiceMain;
use App\Models\LoanCasePncNotes;
use App\Models\OperationAttachments;
use App\Models\QuotationGeneratorMain;
use App\Models\ReferralFee;
use App\Models\ReferralFormula;
use App\Models\Roles;
use App\Models\TransferFeeDetails;
use App\Models\UserAccessControl;
use DateTime;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Intervention\Image\Facades\Image;

class CaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->cases('active');
    }

    public function cases($status)
    {
        $allowCreateCase = "false";
        $openCaseCount = 0;
        $closedCaseCount = 0;
        $InProgressCaseCount = 0;
        $OverdueCaseCount = 0;
        $current_user = auth()->user();

        $case_status_text = 'Closed';
        $case_status_label = 'success';
        $case_status = 0;


        switch (strtolower($status)) {
            case 'active':
                $case_status_text = 'Active';
                $case_status_label = 'info';
                $case_status = 1;
                break;
            case 'closed':
                $case_status_text = 'Closed';
                $case_status_label = 'success';
                $case_status = 0;
                break;
            case 'pendingclose':
                $case_status_text = 'Pending Close';
                $case_status_label = 'warning';
                $case_status = 4;
                break;
            case 'reviewing':
                $case_status_text = 'Reviewing';
                $case_status_label = 'purple';
                $case_status = 7;
                break;
            case 'aborted':
                $case_status_text = 'Aborted';
                $case_status_label = 'danger';
                $case_status = 99;
                break;
            default:
        }

        $allowCreateCase = AccessController::UserAccessPermissionController(PermissionController::CreateCasePermission());
        $allowTransferSales = AccessController::UserAccessPermissionController(PermissionController::TransferSalesPermission());

        $case_type = CaseType::where('status', '=', 1)->get();
        $portfolio = Portfolio::where('status', '=', 1)->orderBy('name', 'ASC')->get();

        $branchInfo = BranchController::manageBranchAccess();

        if (in_array($current_user->id, [1, 2, 14])) {
            $lawyerList = Users::whereIn('menuroles', ['lawyer', 'chambering'])->where('status', '<>', 99)->whereIn('branch_id',  $branchInfo['brancAccessList'])->orWhere('id', 2)->orderBy('name', 'ASC')->get();
        } else {
            $lawyerList = Users::whereIn('menuroles', ['lawyer', 'chambering'])->where('status', '<>', 99)->whereIn('branch_id',  $branchInfo['brancAccessList'])->orderBy('name', 'ASC')->get();
        }

        $clerkList = Users::where('menuroles', '=', 'clerk')->whereIn('branch_id',  $branchInfo['brancAccessList'])->where('status', '<>', 99)->orderBy('name', 'ASC')->get();
        $chamberList = Users::where('menuroles', '=', 'chambering')->whereIn('branch_id',  $branchInfo['brancAccessList'])->where('status', '<>', 99)->orderBy('name', 'ASC')->get();
        $salesList = Users::where('menuroles', '=', 'sales')->orWhereIn('id', [13, 88])->where('status', '<>', 99)->orderBy('name', 'ASC')->get();

        if (in_array($current_user->id, [1, 2, 14])) {
            // $salesList = Users::where('menuroles', '=', 'sales')->orWhereIn('id', [13, 88, 2,3, 38])->where('status', '<>', 99)->orderBy('name', 'ASC')->get();
            $salesList = Users::where('menuroles', '=', 'sales')->orWhereIn('is_sales', [1])->where('status', '<>', 99)->orderBy('name', 'ASC')->get();
        } else {
            if (in_array($current_user->id, [118])) {
                $salesList = Users::where('menuroles', '=', 'sales')->whereIn('branch_id',  $branchInfo['brancAccessList'])->orWhereIn('id', [118])->where('status', '<>', 99)->orderBy('name', 'ASC')->get();
            } else if (in_array($current_user->id, [32])) {
                $salesList = Users::where('menuroles', '=', 'sales')->whereIn('branch_id',  $branchInfo['brancAccessList'])->orWhereIn('id', [118, 143])->where('status', '<>', 99)->orderBy('name', 'ASC')->get();
            } else if (in_array($current_user->id, [143])) {
                $salesList = Users::where('menuroles', '=', 'sales')->whereIn('branch_id',  $branchInfo['brancAccessList'])->orWhereIn('id', [118, 143])->where('status', '<>', 99)->orderBy('name', 'ASC')->get();
            } else {
                $salesList = Users::where('menuroles', '=', 'sales')->whereIn('branch_id',  $branchInfo['brancAccessList'])->where('status', '<>', 99)->orderBy('name', 'ASC')->get();
            }
        }


        if (in_array($current_user->menuroles, ['admin', 'management', 'account'])) {
            $referralList = Referral::where('status', '<>', 99)->orderBy('name', 'ASC')->get();
        } else if (in_array($current_user->id, [51, 127])) {
            $referralList = Referral::where('status', '<>', 99)->where('created_by', 32)->orderBy('name', 'ASC')->get();
        } else if (in_array($current_user->branch_id, [3])) {
            $referralList = Referral::where('status', '<>', 99)->whereIn('created_by', [80, 89])->orderBy('name', 'ASC')->get();
        } else if (in_array($current_user->id, [144, 29])) {
            $referralList = Referral::where('status', '<>', 99)->whereIn('created_by', [$current_user->id, 29, 144])->orderBy('name', 'ASC')->get();
        } else {
            $referralList = Referral::where('status', '<>', 99)->where('created_by', $current_user->id)->orderBy('name', 'ASC')->get();
        }


        return view('dashboard.case.index', [
            'openCaseCount' => $openCaseCount,
            'InProgressCaseCount' => $InProgressCaseCount,
            'allowTransferSales' => $allowTransferSales,
            'closedCaseCount' => $closedCaseCount,
            'OverdueCaseCount' => $OverdueCaseCount,
            'allowCreateCase' => $allowCreateCase,
            'lawyerList' => $lawyerList,
            'chamberList' => $chamberList,
            'current_user' => $current_user,
            'clerkList' => $clerkList,
            'salesList' => $salesList,
            'referralList' => $referralList,
            'branchs' => $branchInfo['branch'],
            'branchInfo' => $branchInfo,
            'portfolio' => $portfolio,
            'case_type' => $case_type,
            'case_status_text' => $case_status_text,
            'case_status_label' => $case_status_label,
            'case_status' => $case_status
        ]);
    }

    public static function  caseManagementEngine()
    {
        $current_user = auth()->user();
        // $userList = $accessInfo['user_list'];
        $accessCaseList = [];
        $user_id_list = [$current_user->id];

        $access_branch = json_decode($current_user->branch_case);
        $link_user_case = json_decode($current_user->link_user_case);
        $special_access_case = json_decode($current_user->special_access_case);

        if (is_array($access_branch)) {
            if ($current_user->branch_id == 2) {
                $branchCaseList = LoanCase::whereIn('branch_id', $access_branch)->where('old_branch', '!=', 1)->where('pnc_case', 0)->pluck('id')->toArray();
            } else {
                $branchCaseList = LoanCase::whereIn('branch_id', $access_branch)->where('pnc_case', 0)->pluck('id')->toArray();
            }

            $accessCaseList = array_merge($accessCaseList, $branchCaseList);
        }

        if (is_array($link_user_case)) {
            $user_id_list = array_merge($user_id_list, $link_user_case);
        }

        $userCaseList = LoanCase::where(function ($q) use ($user_id_list) {
            $q->whereIn('lawyer_id', $user_id_list)
                ->orWhereIn('clerk_id', $user_id_list)
                ->orWhereIn('sales_user_id', $user_id_list);
        })->where('pnc_case', 0)->pluck('id')->toArray();

        $accessCaseList = array_merge($accessCaseList, $userCaseList);

        if (is_array($special_access_case)) {
            $branchCaseList = LoanCase::whereIn('id', $special_access_case)->pluck('id')->toArray();
            $accessCaseList = array_merge($accessCaseList, $branchCaseList);
        }

        return $accessCaseList;
    }

    public function getCaseList(Request $request)
    {
        // return $this->getCaseListBak($request);
        $accessStandalone = ['0'];
        if ($request->ajax()) {
            //Default branch
            $branch_id = 1;
            $search_case_id = [];
            $branchInfo = BranchController::manageBranchAccess();
            $accessInfo = AccessController::manageAccess();

            if (!empty($request->input('branch_id'))) {
                $branch_id = $request->input('branch_id');
            }

            if (!empty($request->input('parties'))) {
                $LoanCaseMasterList = LoanCaseMasterList::where('value', 'like', '%' . $request->input('parties') . '%')->get();
                $Customer = Customer::where('name', 'like', '%' . $request->input('parties') . '%')->get();

                if (count($LoanCaseMasterList) > 0) {
                    for ($i = 0; $i < count($LoanCaseMasterList); $i++) {
                        array_push($search_case_id, $LoanCaseMasterList[$i]->case_id);
                    }
                }

                if (count($Customer) > 0) {
                    for ($i = 0; $i < count($Customer); $i++) {

                        $LoanCase = LoanCase::where('customer_id', '=', $Customer[$i]->id)->get();

                        for ($j = 0; $j < count($LoanCase); $j++) {

                            array_push($search_case_id, $LoanCase[$j]->id);
                        }
                    }
                }
            }

            $current_user = auth()->user();
            $userRoles = $current_user->menuroles;

            $case_list = DB::table('loan_case as l')
                ->leftJoin('case_type', 'case_type.id', '=', 'l.case_type_id')
                ->leftJoin('client', 'client.id', '=', 'l.customer_id')
                ->leftJoin('users as u1', 'u1.id', '=', 'l.lawyer_id')
                ->leftJoin('users as u2', 'u2.id', '=', 'l.clerk_id')
                ->leftJoin('users as u3', 'u3.id', '=', 'l.sales_user_id')
                ->leftJoin('referral as r', 'r.id', '=', 'l.referral_id')
                ->leftJoin('branch as b', 'b.id', '=', 'l.branch_id')
                ->select(
                    'l.*',
                    'case_type.name AS type_name',
                    'client.name AS client_name',
                    'l.id AS case_id',
                    'u1.name as lawyer_name',
                    'u2.name as clerk_name',
                    'u3.name as sales',
                    'b.name as branch',
                    'r.name as referral_name'
                );

            if (!empty($request->input('parties'))) {
                $case_list = $case_list->whereIn('l.id', $search_case_id);
            } else {

                if ($request->input('lawyer')) {
                    if ($request->input('lawyer') <> 0) {
                        // $case_list = $case_list->where('l.lawyer_id', '=', $request->input('lawyer'));

                        $case_list = $case_list->where(function ($q) use ($request) {
                            $q->where('l.lawyer_id', $request->input('lawyer'))
                                ->orWhere('l.clerk_id', $request->input('lawyer'));
                        });
                    }
                }

                if ($request->input('clerk')) {
                    if ($request->input('clerk') <> 0) {

                        $case_list = $case_list->where(function ($q) use ($request) {
                            $q->where('l.lawyer_id', $request->input('clerk'))
                                ->orWhere('l.clerk_id', $request->input('clerk'));
                        });
                    }
                }

                if ($request->input('sales')) {
                    if ($request->input('sales') <> 0) {
                        $case_list = $case_list->where('sales_user_id', '=', $request->input('sales'));
                    }
                }

                if ($request->input('referral')) {
                    if ($request->input('referral') <> 0) {
                        $case_list = $case_list->where('referral_id', '=', $request->input('referral'));
                    }
                }

                if ($request->input('chambering')) {
                    if ($request->input('chambering') <> 0) {
                        $case_list = $case_list->where(function ($q) use ($request) {
                            $q->where('l.lawyer_id', $request->input('chambering'))
                                ->orWhere('l.clerk_id', $request->input('chambering'));
                        });
                    }
                }

                if ($request->input('month')) {
                    if ($request->input('month') <> 0) {
                        $case_list = $case_list->whereRaw('MONTH(l.created_at) = ?', $request->input('month'));
                    }
                }

                if ($request->input('year')) {
                    if ($request->input('year') <> 0) {
                        $case_list = $case_list->whereRaw('YEAR(l.created_at) = ?', $request->input('year'));
                    }
                }


                if ($request->input('status') <> 1) {
                    $case_list = $case_list->where('l.status', '=', $request->input('status'));
                    // return 1;
                } else {
                    // $case_list = $case_list->whereNotIn('l.status', [0, 4, 99,7, 6]);
                    $case_list = $case_list->whereIn('l.status', [1, 2, 3]);
                }

                if ($request->input('case_type')) {
                    if ($request->input('case_type') <> 0) {
                        $case_list = $case_list->where('l.bank_id', '=', $request->input('case_type'));
                    }
                }


                if ($request->input('branch')) {
                    if ($request->input('branch') <> 0) {
                        $case_list = $case_list->where('l.branch_id', '=', $request->input('branch'));
                    }
                }
            }

            if ($request->input('branch')) {
                if ($request->input('branch') <> 0) {
                    $case_list = $case_list->where('l.branch_id', '=', $request->input('branch'));
                }
            }


            if (!in_array($userRoles, ['admin', 'management', 'account'])) {
                $accessCaseList = $this->caseManagementEngine();

                $case_list = $case_list->whereIn('l.id', $accessCaseList);
            }

            $case_list = $case_list->orderBy('l.created_at', 'DESC')->get();

            $transferCasePermission = AccessController::UserAccessPermissionController(PermissionController::TransferCasePermission());

            $SetCasePendingClosePermission = AccessController::UserAccessController(PermissionController::getSetCasePendingClosePermission());
            $setCaseReviewingPermission = AccessController::UserAccessController(PermissionController::getSetCaseReviewingPermission());
            $SetCaseReopenPermission = AccessController::UserAccessPermissionController(PermissionController::SetCaseReopenPermission());

            $current_user_role = Roles::where('name', $current_user->menuroles)->first();

            return DataTables::of($case_list, $request)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($current_user, $transferCasePermission, $SetCasePendingClosePermission, $setCaseReviewingPermission, $SetCaseReopenPermission, $current_user_role) {

                    $action = '';
                    $actionBtn = '';

                    if ($transferCasePermission == true) {

                        if (in_array($row->status, [1, 2, 3, 4])) {
                            if ($current_user_role->hierarchy >= 4) {

                                // if($current_user->id == 14)
                                // {
                                //     $action .= ' <div class="dropdown-divider"></div>
                                //     <a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="transferModal(' . $row->id . ',\'' . $row->lawyer_id . '\',\'' . $row->clerk_id . '\',\'' . $row->sales_user_id . '\')" data-toggle="modal" data-target="#modalTransfer" ><i style="margin-right: 10px;" class="cil-transfer"></i>Transfer case</a>
                                //     ';
                                // }
                                // else
                                // {
                                //     if($current_user->branch_id == $row->branch_id)
                                //     {
                                //         $action .= ' <div class="dropdown-divider"></div>
                                //         <a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="transferModal(' . $row->id . ',\'' . $row->lawyer_id . '\',\'' . $row->clerk_id . '\',\'' . $row->sales_user_id . '\')" data-toggle="modal" data-target="#modalTransfer" ><i style="margin-right: 10px;" class="cil-transfer"></i>Transfer case</a>
                                //         ';
                                //     }

                                // }

                                $action .= ' <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="transferModal(' . $row->id . ',\'' . $row->lawyer_id . '\',\'' . $row->clerk_id . '\',\'' . $row->sales_user_id . '\')" data-toggle="modal" data-target="#modalTransfer" ><i style="margin-right: 10px;" class="cil-transfer"></i>Transfer case</a>
                                ';
                            } else {
                                $action .= ' <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="transferModal(' . $row->id . ',\'' . $row->lawyer_id . '\',\'' . $row->clerk_id . '\',\'' . $row->sales_user_id . '\')" data-toggle="modal" data-target="#modalTransfer" ><i style="margin-right: 10px;" class="cil-transfer"></i>Transfer case</a>
                                ';
                            }
                        }
                    }


                    if ($SetCasePendingClosePermission == true) {

                        if (in_array($row->status, [7])) {
                            $action .= ' <div class="dropdown-divider"></div> 
                            <a class="dropdown-item" href="javascript:void(0)" onclick="updateCaseStatus(' . $row->id . ',\'PENDINGCLOSED\')" ><i style="margin-right: 10px;" class="cil-x-circle"></i>Pending Close Case</a>
                            ';
                        }
                    }

                    if ($setCaseReviewingPermission == true) {

                        if (in_array($row->status, [1, 2, 3])) {
                            if (in_array($row->branch_id, [1]) || in_array($current_user->id, [22, 21, 94, 25, 2])) {
                                $action .= '  <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="updateCaseStatus(' . $row->id . ',\'REVIEWING\')" ><i style="margin-right: 10px;" class="cil-x-circle"></i>Request Close Case</a>
                                ';
                            } else {
                                $action .= ' <div class="dropdown-divider"></div> 
                            <a class="dropdown-item" href="javascript:void(0)" onclick="updateCaseStatus(' . $row->id . ',\'PENDINGCLOSED\')" ><i style="margin-right: 10px;" class="cil-x-circle"></i>Pending Close Case</a>
                            ';
                            }
                        }
                    }

                    if ($SetCaseReopenPermission == true) {

                        if (in_array($row->status, [4, 7])) {
                            $action .= ' <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="javascript:void(0)" onclick="updateCaseStatus(' . $row->id . ',\'REOPEN\')"  ><i style="margin-right: 10px;" class="cil-chevron-double-right"></i>Reopen case</a>
                           
                            
                            ';
                        }
                    }

                    $actionBtn .= '<div class="btn-group">
                    <button type="button" class="btn btn-info btn-flat">Action</button>
                    <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                      <span class="caret"></span>
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="/case/' . $row->id . '" ><i style="margin-right: 10px;" class="cil-chevron-double-right"></i>Go to case</a>
                     ' . $action . '
                     </div>
                  </div>';


                    return $actionBtn;
                })
                ->editColumn('case_ref_no', function ($data) {

                    return '<a href="/case/' . $data->id . '">' . $data->case_ref_no . '</a> ';
                })
                ->editColumn('created_at', function ($data) {
                    return  date('Y-m-d', strtotime($data->created_at));
                })

                ->rawColumns(['status', 'action', 'case_ref_no', 'notes', 'date_info', 'created_at', 'latest_notes'])
                ->make(true);
        }
    }

    public function getCaseListBak(Request $request)
    {
        $accessStandalone = ['0'];
        if ($request->ajax()) {
            //Default branch
            $branch_id = 1;
            $search_case_id = [];
            $branchInfo = BranchController::manageBranchAccess();
            $accessInfo = AccessController::manageAccess();

            if (!empty($request->input('branch_id'))) {
                $branch_id = $request->input('branch_id');
            }

            if (!empty($request->input('parties'))) {
                $LoanCaseMasterList = LoanCaseMasterList::where('value', 'like', '%' . $request->input('parties') . '%')->get();
                $Customer = Customer::where('name', 'like', '%' . $request->input('parties') . '%')->get();

                if (count($LoanCaseMasterList) > 0) {
                    for ($i = 0; $i < count($LoanCaseMasterList); $i++) {
                        array_push($search_case_id, $LoanCaseMasterList[$i]->case_id);
                    }
                }

                if (count($Customer) > 0) {
                    for ($i = 0; $i < count($Customer); $i++) {

                        $LoanCase = LoanCase::where('customer_id', '=', $Customer[$i]->id)->get();

                        for ($j = 0; $j < count($LoanCase); $j++) {

                            array_push($search_case_id, $LoanCase[$j]->id);
                        }
                    }
                }
            }


            $current_user = auth()->user();
            $userRoles = $current_user->menuroles;

            $case_list = DB::table('loan_case as l')
                ->leftJoin('case_type', 'case_type.id', '=', 'l.case_type_id')
                ->leftJoin('client', 'client.id', '=', 'l.customer_id')
                ->leftJoin('users as u1', 'u1.id', '=', 'l.lawyer_id')
                ->leftJoin('users as u2', 'u2.id', '=', 'l.clerk_id')
                ->leftJoin('users as u3', 'u3.id', '=', 'l.sales_user_id')
                ->leftJoin('referral as r', 'r.id', '=', 'l.referral_id')
                ->leftJoin('branch as b', 'b.id', '=', 'l.branch_id')
                // ->leftjoin('loan_case_kiv_notes as n', function ($join) {
                //     $join->on('n.case_id', '=', 'l.id')
                //         ->where('n.status', '<>', 99)
                //         ->where('n.id', '=', DB::raw("(SELECT max(id) from loan_case_kiv_notes WHERE case_id = l.id and status <> 99)"));
                // })
                // ->leftJoin('users as u', 'u.id', '=', 'n.created_by')
                // ->select('l.*', 'case_type.name AS type_name', 'client.name AS client_name', 'l.id AS case_id', 'n.notes', 'n.created_at as meg_time', 'u.name as msg_user', 'u.menuroles as menuroles', 'u1.name as lawyer_name', 'u2.name as clerk_name', 'u3.name as sales', 'b.name as branch', 'r.name as referral_name');
                ->select('l.*', 'case_type.name AS type_name', 'client.name AS client_name', 'l.id AS case_id',  'u1.name as lawyer_name', 'u2.name as clerk_name', 'u3.name as sales', 'b.name as branch', 'r.name as referral_name');

            if (!empty($request->input('parties'))) {
                $case_list = $case_list->whereIn('l.id', $search_case_id);
            } else {

                if ($request->input('lawyer')) {
                    if ($request->input('lawyer') <> 0) {
                        // $case_list = $case_list->where('l.lawyer_id', '=', $request->input('lawyer'));

                        $case_list = $case_list->where(function ($q) use ($request) {
                            $q->where('l.lawyer_id', $request->input('lawyer'))
                                ->orWhere('l.clerk_id', $request->input('lawyer'));
                        });
                    }
                }

                if ($request->input('clerk')) {
                    if ($request->input('clerk') <> 0) {
                        // $case_list = $case_list->where('l.clerk_id', '=', $request->input('clerk'));

                        $case_list = $case_list->where(function ($q) use ($request) {
                            $q->where('l.lawyer_id', $request->input('clerk'))
                                ->orWhere('l.clerk_id', $request->input('clerk'));
                        });
                    }
                }

                if ($request->input('sales')) {
                    if ($request->input('sales') <> 0) {
                        $case_list = $case_list->where('sales_user_id', '=', $request->input('sales'));
                    }
                }

                if ($request->input('referral')) {
                    if ($request->input('referral') <> 0) {
                        $case_list = $case_list->where('referral_id', '=', $request->input('referral'));
                    }
                }

                if ($request->input('chambering')) {
                    if ($request->input('chambering') <> 0) {
                        $case_list = $case_list->where(function ($q) use ($request) {
                            $q->where('l.lawyer_id', $request->input('chambering'))
                                ->orWhere('l.clerk_id', $request->input('chambering'));
                        });
                    }
                }

                if ($request->input('month')) {
                    if ($request->input('month') <> 0) {
                        $case_list = $case_list->whereRaw('MONTH(l.created_at) = ?', $request->input('month'));
                    }
                }

                if ($request->input('year')) {
                    if ($request->input('year') <> 0) {
                        $case_list = $case_list->whereRaw('YEAR(l.created_at) = ?', $request->input('year'));
                    }
                }


                if ($request->input('status') <> 1) {
                    $case_list = $case_list->where('l.status', '=', $request->input('status'));
                    // return 1;
                } else {
                    // $case_list = $case_list->whereNotIn('l.status', [0, 4, 99,7, 6]);
                    $case_list = $case_list->whereIn('l.status', [1, 2, 3]);
                }

                if ($request->input('case_type')) {
                    if ($request->input('case_type') <> 0) {
                        $case_list = $case_list->where('l.bank_id', '=', $request->input('case_type'));
                    }
                }


                if ($request->input('branch')) {
                    if ($request->input('branch') <> 0) {
                        $case_list = $case_list->where('l.branch_id', '=', $request->input('branch'));
                    }
                }
            }

            if ($request->input('branch')) {
                if ($request->input('branch') <> 0) {
                    $case_list = $case_list->where('l.branch_id', '=', $request->input('branch'));
                }
            }


            if (!in_array($userRoles, ['admin', 'management', 'account'])) {
                $userList = $accessInfo['user_list'];

                if ($userList) {
                    $case_list = $case_list->where(function ($q) use ($userList, $accessInfo) {
                        $q->whereIn('l.branch_id', $accessInfo['brancAccessList'])
                            ->whereIn('l.lawyer_id', $userList)
                            ->orWhereIn('l.clerk_id', $userList)
                            ->orWhereIn('l.sales_user_id', $userList)
                            ->orWhereIn('l.id', $accessInfo['case_list']);
                    });
                } else {
                    $case_list = $case_list->whereIn('l.branch_id', $accessInfo['brancAccessList']);
                }
            }

            $case_list = $case_list->orderBy('l.created_at', 'DESC')->get();

            $transferCasePermission = AccessController::UserAccessPermissionController(PermissionController::TransferCasePermission());
            $SetCasePendingClosePermission = AccessController::UserAccessController(PermissionController::getSetCasePendingClosePermission());
            $setCaseReviewingPermission = AccessController::UserAccessController(PermissionController::getSetCaseReviewingPermission());
            $SetCaseReopenPermission = AccessController::UserAccessController(PermissionController::SetCaseReopenPermission());

            return DataTables::of($case_list, $request)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($current_user, $transferCasePermission, $SetCasePendingClosePermission, $setCaseReviewingPermission, $SetCaseReopenPermission) {

                    $action = '';
                    $actionBtn = '';

                    if ($transferCasePermission == true) {
                        if (in_array($row->status, [1, 2, 3])) {
                            $action .= ' <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="transferModal(' . $row->id . ',\'' . $row->lawyer_id . '\',\'' . $row->clerk_id . '\',\'' . $row->sales_user_id . '\')" data-toggle="modal" data-target="#modalTransfer" ><i style="margin-right: 10px;" class="cil-transfer"></i>Transfer case</a>
                            ';
                        }
                    }


                    if ($SetCasePendingClosePermission == true) {

                        if (in_array($row->status, [7])) {
                            $action .= ' <div class="dropdown-divider"></div> 
                            <a class="dropdown-item" href="javascript:void(0)" onclick="updateCaseStatus(' . $row->id . ',\'PENDINGCLOSED\')" ><i style="margin-right: 10px;" class="cil-x-circle"></i>Pending Close Case</a>
                            ';
                        }
                    }

                    if ($setCaseReviewingPermission == true) {

                        if (in_array($row->status, [1, 2, 3])) {
                            if (in_array($row->branch_id, [1]) || in_array($current_user->id, [22, 21, 94, 25, 2])) {
                                $action .= '  <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)" onclick="updateCaseStatus(' . $row->id . ',\'REVIEWING\')" ><i style="margin-right: 10px;" class="cil-x-circle"></i>Request Close Case</a>
                                ';
                            } else {
                                $action .= ' <div class="dropdown-divider"></div> 
                            <a class="dropdown-item" href="javascript:void(0)" onclick="updateCaseStatus(' . $row->id . ',\'PENDINGCLOSED\')" ><i style="margin-right: 10px;" class="cil-x-circle"></i>Pending Close Case</a>
                            ';
                            }
                        }
                    }

                    if ($SetCaseReopenPermission == true) {

                        if (in_array($row->status, [4, 7])) {
                            $action .= ' <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="javascript:void(0)" onclick="updateCaseStatus(' . $row->id . ',\'REOPEN\')"  ><i style="margin-right: 10px;" class="cil-chevron-double-right"></i>Reopen case</a>
                           
                            
                            ';
                        }
                    }

                    $actionBtn .= '<div class="btn-group">
                    <button type="button" class="btn btn-info btn-flat">Action</button>
                    <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                      <span class="caret"></span>
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="/case/' . $row->id . '" ><i style="margin-right: 10px;" class="cil-chevron-double-right"></i>Go to case</a>
                     ' . $action . '
                     </div>
                  </div>';


                    return $actionBtn;
                })
                ->editColumn('case_ref_no', function ($data) {

                    return '<a href="/case/' . $data->id . '">' . $data->case_ref_no . '</a> ';
                })
                ->editColumn('created_at', function ($data) {
                    return  date('Y-m-d', strtotime($data->created_at));
                })

                ->rawColumns(['status', 'action', 'case_ref_no', 'notes', 'date_info', 'created_at', 'latest_notes'])
                ->make(true);
        }
    }

    public static function getCaseListHub($request_type = 'list', $request_query = '')
    {

        $accessInfo = AccessController::manageAccess();
        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;

        $case_list = DB::table('loan_case as l')
            ->join('client as c', 'c.id', '=', 'l.customer_id')
            ->select('l.*', 'c.name')
            ->where('l.status', '<>', 99);

        if (!in_array($userRoles, ['admin', 'management', 'account'])) {
            $accessCaseList = CaseController::caseManagementEngine();

            $case_list = $case_list->whereIn('l.id', $accessCaseList);
        }

        // if (!in_array($userRoles, ['admin', 'management', 'account'])) {
        //     $userList = $accessInfo['user_list'];

        //     if ($userList) {
        //         $case_list = $case_list->where(function ($q) use ($userList, $accessInfo) {
        //             $q->whereIn('l.branch_id', $accessInfo['brancAccessList'])
        //                 ->whereIn('l.lawyer_id', $userList)
        //                 ->orWhereIn('l.clerk_id', $userList)
        //                 ->orWhereIn('l.sales_user_id', $userList)
        //                 ->orWhereIn('l.id', $accessInfo['case_list']);
        //         });
        //     } else {
        //         $case_list = $case_list->whereIn('l.branch_id', $accessInfo['brancAccessList']);
        //     }
        // }

        if ($request_type == 'list') {
            $case_list = $case_list->orderBy('l.created_at', 'DESC')->get();
        } else {
            $case_list =  $case_list->pluck($request_query)->toArray();
        }



        return $case_list;
    }


    public function searchCase(Request $request)
    {
        if ($request->ajax()) {
            //Default branch
            $branch_id = 1;

            if (!empty($request->input('branch_id'))) {
                $branch_id = $request->input('branch_id');
            }

            $current_user = auth()->user();
            $userRoles = $current_user->menuroles;

            $case_list = DB::table('loan_case as l')
                ->leftJoin('case_type', 'case_type.id', '=', 'l.case_type_id')
                ->leftJoin('client', 'client.id', '=', 'l.customer_id')
                ->leftjoin('loan_case_kiv_notes as n', function ($join) {
                    $join->on('n.case_id', '=', 'l.id')
                        ->where('n.id', '=', DB::raw("(SELECT max(id) from loan_case_kiv_notes WHERE case_id = l.id)"));
                })
                ->leftJoin('users as u', 'u.id', '=', 'n.created_by')
                ->select('l.*', 'case_type.name AS type_name', 'client.name AS client_name', 'l.id AS case_id', 'n.notes', 'n.created_at as meg_time', 'u.name as msg_user');

            if ($userRoles == "lawyer" || $userRoles == "clerk") {
                $case_list = $case_list->where('l.' . $userRoles . '_id', '=', $current_user->id);
            } else if ($userRoles == "chambering") {
                $case_list = $case_list->where('l.lawyer_id', '=', $current_user->id)
                    ->orWhere('l.clerk_id', '=', $current_user->id);
            } else if ($userRoles == "sales") {

                // temporary allow max to access stanley case
                // if ($current_user->id == 30) {
                //     $case_list = $case_list->whereIn('l.sales_user_id', [$current_user->id, 3]);
                // } else {

                //     $case_list = $case_list->where('l.sales_user_id', '=', $current_user->id);
                // }

                //    temporary allow max to access stanley case
                if ($current_user->id == 29) {
                    // $case_list = $case_list->orWhere('l.id','=', 685);
                    $case_list = $case_list->where(function ($q) use ($current_user) {
                        $q->whereIn('l.sales_user_id', [$current_user->id, 3, 2])
                            ->orWhere('l.id', '=', [452])
                            ->orWhere('l.id', '=', [453])
                            ->orWhere('l.id', '=', [685]);
                    });
                } else {
                    $case_list = $case_list->where('l.sales_user_id', '=', $current_user->id);
                }
            }



            if ($request->input('lawyer')) {
                if ($request->input('lawyer') <> 0) {
                    $case_list = $case_list->where('l.lawyer_id', '=', $request->input('lawyer'));
                }
            }

            if ($request->input('clerk')) {
                if ($request->input('clerk') <> 0) {
                    $case_list = $case_list->where('l.clerk_id', '=', $request->input('clerk'));
                }
            }

            if ($request->input('chambering')) {
                if ($request->input('chambering') <> 0) {
                    $case_list = $case_list->where(function ($q) use ($request) {
                        $q->where('l.lawyer_id', $request->input('chambering'))
                            ->orWhere('l.clerk_id', $request->input('chambering'));
                    });
                }
            }

            if ($request->input('month')) {
                if ($request->input('month') <> 0) {
                    $case_list = $case_list->whereRaw('MONTH(l.created_at) = ?', $request->input('month'));
                }
            }

            if ($request->input('status') <> '') {
                $case_list = $case_list->where('l.status', '=', $request->input('status'));
                // return 1;
            } else {
                $case_list = $case_list->whereNotIn('l.status', [0, 99]);
            }

            if ($request->input('case_type')) {
                if ($request->input('case_type') <> 0) {
                    $case_list = $case_list->where('l.bank_id', '=', $request->input('case_type'));
                }
            }

            if ($request->input('branch')) {
                if ($request->input('branch') <> 0) {
                    $case_list = $case_list->where('l.branch_id', '=', $request->input('branch'));
                }
            }

            if (in_array($userRoles, ['receptionist', 'account', 'sales'])) {
                if ($current_user->branch_id == 3) {
                    $case_list = $case_list->where('l.branch_id', '=', $current_user->branch_id);
                }
            }

            $case_list = $case_list
                // ->where('l.status', '!=', 99)
                ->orderBy('l.id', 'DESC')->get();

            return DataTables::of($case_list, $request)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($current_user) {
                    if ($current_user->menuroles == 'admin' || $current_user->menuroles == 'management') {
                        $actionBtn = '<div class="btn-group">
                        <button type="button" class="btn btn-info btn-flat">Action</button>
                        <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                          <span class="caret"></span>
                          <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="/case/' . $row->id . '" ><i style="margin-right: 10px;" class="cil-chevron-double-right"></i>Go to case</a>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item" href="javascript:void(0)" onclick="updateCaseStatus(' . $row->id . ',\'CLOSED\')" ><i style="margin-right: 10px;" class="cil-check-circle"></i>Close case</a>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item" href="javascript:void(0)" onclick="updateCaseStatus(' . $row->id . ',\'ABORTED\')" ><i style="margin-right: 10px;" class="cil-x-circle"></i>Abort case</a>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item" href="javascript:void(0)" onclick="updateCaseStatus(' . $row->id . ',\'PENDINGCLOSED\')" ><i style="margin-right: 10px;" class="cil-x-circle"></i>Pending Close</a>
                        </div>
                      </div>';
                    } else {
                        $actionBtn = ' <a  href="/case/' . $row->id . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>';
                    }
                    return $actionBtn;
                })
                ->editColumn('case_ref_no', function ($data) {

                    $status_lbl = '';
                    if ($data->status === '2')
                        $status_lbl = '<span class="label bg-info">Open</span>';
                    elseif ($data->status === '0')
                        $status_lbl = '<span class="label bg-success">Closed</span>';
                    elseif ($data->status === '1')
                        $status_lbl = '<span class="label bg-purple">Running</span>';
                    elseif ($data->status === '3')
                        $status_lbl = '<span class="label bg-warning">KIV</span>';
                    elseif ($data->status === '99')
                        $status_lbl = '<span class="label bg-danger">Aborted</span>';
                    elseif ($data->status === 7)
                        $status_lbl = '<span class="label bg-purple">Reviewing</span>';
                    else
                        $status_lbl = '<span class="label bg-danger">Overdue</span>';


                    return '<a href="/case/' . $data->id . '">' . $data->case_ref_no . '</a> ';
                })
                ->editColumn('percentage', function ($data) {
                    if ($data->status === '0')
                        return  '100 %';
                    else
                        return  $data->percentage . ' %';
                })
                ->editColumn('notes', function ($data) {
                    return  '<div><span class="text-info">' . $data->msg_user . '</span><br/><span class="text-warning">' . $data->meg_time . '</span><br/><br/>' . $data->notes . '</div>';
                })
                ->editColumn('status', function ($data) {
                    if ($data->status === '2')
                        return '<span class="label bg-info">Open</span>';
                    elseif ($data->status === '0')
                        return '<span class="label bg-success">Closed</span>';
                    elseif ($data->status === '1')
                        return '<span class="label bg-purple">Running</span>';
                    elseif ($data->status === '3')
                        return '<span class="label bg-warning">KIV</span>';
                    elseif ($data->status === 7)
                        return '<span class="label bg-danger">Aborted</span>';
                    elseif ($data->status === '99')
                        return '<span class="label bg-purple">Reviewing</span>';
                    else
                        return '<span class="label bg-danger">Overdue</span>';
                })
                ->rawColumns(['status', 'action', 'case_ref_no', 'notes'])
                ->make(true);
        }
    }

    public function transferSystemCase(Request $request, $id)
    {
        $current_user = auth()->user();

        $LoanCase = LoanCase::where('id', '=', $id)->first();
        $lawyer_id = 0;
        $clerk_id = 0;
        $sales_id = 0;

        if ($LoanCase) {
            if ($LoanCase->lawyer_id != null) {
                $lawyer_id = $LoanCase->lawyer_id;
            }

            if ($LoanCase->clerk_id != null) {
                $clerk_id = $LoanCase->clerk_id;
            }

            $sales_id = $LoanCase->sales_user_id;
        }

        // return $this->updateNewRefNo($LoanCase, $request->input('lawyer_id'), 0);;

        if ($request->input('skip_lawyer') == 1) {
            if ($request->input('lawyer_id') !=  $lawyer_id) {

                $previous_lawyer_name = '-';

                $previous_lawyer = Users::where('id', '=', $lawyer_id)->first();
                $current_lawyer = Users::where('id', '=', $request->input('lawyer_id'))->first();

                if ($previous_lawyer) {
                    $previous_lawyer_name = $previous_lawyer->name;
                }

                $CaseTransferLog = new CaseTransferLog();
                $CaseTransferLog->user_id =  $current_user->id;
                $CaseTransferLog->case_id =  $LoanCase->id;
                $CaseTransferLog->action =  'Transfer';
                $CaseTransferLog->desc =  null;
                $CaseTransferLog->status =  1;
                $CaseTransferLog->created_at = date('Y-m-d H:i:s');
                $CaseTransferLog->ori_user = $lawyer_id;
                $CaseTransferLog->new_user = $request->input('lawyer_id');
                $CaseTransferLog->object_id = 7; //role id
                $CaseTransferLog->prev_ref_no =  $LoanCase->case_ref_no;
                $CaseTransferLog->prev_branch =  0;
                $CaseTransferLog->current_branch =  0;
                $CaseTransferLog->save();

                $LoanCase->lawyer_id =  $request->input('lawyer_id');
                $LoanCase->updated_at = date('Y-m-d H:i:s');
                $LoanCase->save();

                $LoanCaseKivNotes = new LoanCaseKivNotes();

                $LoanCaseKivNotes->case_id =  $id;
                $LoanCaseKivNotes->notes =  '[Transfer lawyer from ' . $previous_lawyer_name . ' to ' . $current_lawyer->name . ']';
                $LoanCaseKivNotes->label =  'transfer';
                $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
                $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');

                $LoanCaseKivNotes->status =  1;
                $LoanCaseKivNotes->created_by = $current_user->id;
                $LoanCaseKivNotes->save();

                $LoanCase->case_ref_no = $this->updateNewRefNo($LoanCase, $request->input('lawyer_id'), 0, 0);
                $LoanCase->save();

                $CaseTransferLog->current_ref_no =  $LoanCase->case_ref_no;
                $CaseTransferLog->save();
            }
        }

        if ($request->input('clerk_id') != $clerk_id) {

            $previous_clerk_name = 'no clerk';
            $current_clerk_name = 'no clerk';

            if ($request->input('clerk_id') == 0) {
            }

            $previous_clerk = Users::where('id', '=', $clerk_id)->first();
            $current_clerk = Users::where('id', '=', $request->input('clerk_id'))->first();

            if ($previous_clerk) {

                // $previous_clerk_name = $previous_clerk->name;

                if ($previous_clerk->id == 0) {
                    $current_clerk_name = 'no clerk';
                } else {
                    $previous_clerk_name = $previous_clerk->name;
                }
            }

            if ($current_clerk) {

                if ($request->input('clerk_id') == 0) {
                    $current_clerk_name = 'no clerk';
                } else {
                    $current_clerk_name = $current_clerk->name;
                }
            }

            $current_branch_id = 0;
            $prev_branch_id = 0;

            $CaseTransferLog = new CaseTransferLog();
            $CaseTransferLog->user_id =  $current_user->id;
            $CaseTransferLog->case_id =  $LoanCase->id;
            $CaseTransferLog->action =  'Transfer';
            $CaseTransferLog->desc =  null;
            $CaseTransferLog->status =  1;
            $CaseTransferLog->created_at = date('Y-m-d H:i:s');
            $CaseTransferLog->ori_user = $clerk_id;
            $CaseTransferLog->new_user = $request->input('clerk_id');
            $CaseTransferLog->object_id = 8; //role id
            $CaseTransferLog->prev_ref_no =  $LoanCase->case_ref_no;
            $CaseTransferLog->prev_branch =  $prev_branch_id;
            $CaseTransferLog->current_branch =  $current_branch_id;
            $CaseTransferLog->save();

            $LoanCase->clerk_id =  $request->input('clerk_id');
            $LoanCase->updated_at = date('Y-m-d H:i:s');
            $LoanCase->save();

            $LoanCaseKivNotes = new LoanCaseKivNotes();

            $LoanCaseKivNotes->case_id =  $id;
            $LoanCaseKivNotes->notes =  '[Transfer clerk from ' . $previous_clerk_name . ' to ' .  $current_clerk_name . ']';
            $LoanCaseKivNotes->label =  'transfer';
            $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
            $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');

            $LoanCaseKivNotes->status =  1;
            $LoanCaseKivNotes->created_by = $current_user->id;
            $LoanCaseKivNotes->save();

            $LoanCase->case_ref_no = $this->updateNewRefNo($LoanCase, 0, $request->input('clerk_id'), 0);
            $LoanCase->save();

            $CaseTransferLog->current_ref_no =  $LoanCase->case_ref_no;
            $CaseTransferLog->save();
        }

        if ($request->input('sales_id') != "null" && $request->input('sales_id') != null) {
            if ($request->input('sales_id') != $sales_id) {

                $previous_sales = Users::where('id', '=', $sales_id)->first();
                $current_sales = Users::where('id', '=', $request->input('sales_id'))->first();

                $previous_sales_name = $previous_sales->name;
                $current_sales_name = $current_sales->name;

                $current_branch_id = 0;
                $prev_branch_id = 0;

                $CaseTransferLog = new CaseTransferLog();
                $CaseTransferLog->user_id =  $current_user->id;
                $CaseTransferLog->case_id =  $LoanCase->id;
                $CaseTransferLog->action =  'Transfer';
                $CaseTransferLog->desc =  null;
                $CaseTransferLog->status =  1;
                $CaseTransferLog->created_at = date('Y-m-d H:i:s');
                $CaseTransferLog->ori_user = $sales_id;
                $CaseTransferLog->new_user = $request->input('sales_id');
                $CaseTransferLog->object_id = 6; //role id
                $CaseTransferLog->prev_ref_no =  $LoanCase->case_ref_no;
                $CaseTransferLog->prev_branch =  $prev_branch_id;
                $CaseTransferLog->current_branch =  $current_branch_id;
                $CaseTransferLog->save();

                $LoanCase->sales_user_id =  $request->input('sales_id');
                $LoanCase->updated_at = date('Y-m-d H:i:s');
                $LoanCase->save();

                $LoanCaseKivNotes = new LoanCaseKivNotes();

                $LoanCaseKivNotes->case_id =  $id;
                $LoanCaseKivNotes->notes =  '[Transfer sales from ' . $previous_sales_name . ' to ' .  $current_sales_name . ']';
                $LoanCaseKivNotes->label =  'transfer';
                $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
                $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');

                $LoanCaseKivNotes->status =  1;
                $LoanCaseKivNotes->created_by = $current_user->id;
                $LoanCaseKivNotes->save();

                $LoanCase->case_ref_no = $this->updateNewRefNo($LoanCase, 0, 0, $request->input('sales_id'));
                $LoanCase->save();

                $CaseTransferLog->current_ref_no =  $LoanCase->case_ref_no;
                $CaseTransferLog->save();
            }
        }



        return response()->json(['status' => 1, 'data' => 'success']);
    }


    public function updateNewRefNo($LoanCase, $new_lawyer_id, $new_clerk_id, $new_sales_id)
    {
        $case_ref_no = '[sales]/[lawyer]/[bank]/[running_no]/[client]/[clerk]';
        $lawyer_id = 0;
        $clerk_id = 0;
        $sales_id = 0;

        if ($new_lawyer_id <> 0) {
            $lawyer_id = $new_lawyer_id;
        }

        if ($new_clerk_id <> 0) {
            $clerk_id = $new_clerk_id;
        }

        if ($new_sales_id <> 0) {
            $sales_id = $new_sales_id;
        }


        if ($clerk_id == 0) {
            $clerk_id = $LoanCase->clerk_id;
        }

        if ($lawyer_id == 0) {
            $lawyer_id = $LoanCase->lawyer_id;
        }

        if ($sales_id == 0) {
            $sales_id = $LoanCase->sales_user_id;
        }

        $lawyer_user = User::where('id', '=', $lawyer_id)->first();

        $client = Customer::where('id', '=', $LoanCase->customer_id)->first();
        $client_short_code = Helper::generateNickName($client->name);

        $sales_user = User::where('id', '=', $sales_id)->first();

        $bank = Portfolio::where('id', '=', $LoanCase->bank_id)->first();

        // $extractRefNo = str_replace('-','', $LoanCase->case_ref_no);
        // $extractRefNo = str_replace('N1','', $LoanCase->case_ref_no);
        // $running_no = (int)filter_var($extractRefNo, FILTER_SANITIZE_NUMBER_INT);

        // if($running_no < 0)
        // {
        //     $running_no = $running_no* -1;
        // }



        $case_ref_no = str_replace("[sales]", $sales_user->nick_name, $case_ref_no);
        $case_ref_no = str_replace("[bank]", $bank->short_code, $case_ref_no);

        if ($LoanCase->branch_id <> 1) {
            $Branch = Branch::where('id', '=', $LoanCase->branch_id)->first();
            $case_ref_no = $Branch->short_code . "/" . $case_ref_no;
        }

        $case_ref_no = str_replace("[running_no]", $LoanCase->case_running_no, $case_ref_no);
        $case_ref_no = str_replace("[client]", $client_short_code, $case_ref_no);

        $case_ref_no = str_replace("[lawyer]", $lawyer_user->nick_name, $case_ref_no);

        if ($clerk_id != 0) {
            $clerk_user = User::where('id', '=', $clerk_id)->first();
            $case_ref_no = str_replace("[clerk]", $clerk_user->nick_name, $case_ref_no);
        } else {
            $case_ref_no = str_replace("/[clerk]", '', $case_ref_no);
        }

        return $case_ref_no;
    }


    public function getSearchCase(Request $request)
    {
        $search_case_id = [];
        $case_count = 0;
        $current_user = auth()->user();

        $accessInfo = AccessController::manageAccess();

        if ($request->ajax()) {
            //Default branch
            $branch_id = 1;

            if (!empty($request->input('branch_id'))) {
                $branch_id = $request->input('branch_id');
            }

            if (!empty($request->input('parties'))) {

                $LoanCaseMasterList = DB::table('loan_case_masterlist as m')
                    ->leftJoin('case_masterlist_field as f', 'f.id', '=', 'm.masterlist_field_id')
                    ->select('m.*')
                    ->where('value', 'like', '%' . $request->input('parties') . '%')
                    ->whereNotIn('master_list_type', ['exclude'])
                    ->get();


                // $LoanCaseMasterList = LoanCaseMasterList::where('value', 'like', '%' . $request->input('parties') . '%')
                // ->where('master_list_type', 'exclude')
                // ->get();ds

                $Customer = Customer::where('name', 'like', '%' . $request->input('parties') . '%')->get();
                $Referral = Referral::where('name', 'like', '%' . $request->input('parties') . '%')->get();

                if (is_numeric($request->input('parties'))) {
                    $searchCase = LoanCase::where('case_ref_no', 'like', '%' . $request->input('parties') . '%')
                        ->OrWhere('bank_ref', 'like', '%' . $request->input('parties') . '%')->get();
                } else {
                    $searchCase = LoanCase::where('bank_ref', 'like', '%' . $request->input('parties') . '%')->get();
                }

                $LoanCaseBillMain = LoanCaseBillMain::where('bill_no', 'like', '%' . $request->input('parties') . '%')
                    ->OrWhere('invoice_no', 'like', '%' . $request->input('parties') . '%')->get();

                if (count($LoanCaseMasterList) > 0) {
                    for ($i = 0; $i < count($LoanCaseMasterList); $i++) {
                        array_push($search_case_id, $LoanCaseMasterList[$i]->case_id);
                    }
                }

                $LoanCase = LoanCase::where('property_address', 'like', '%' . $request->input('parties') . '%')->get();

                for ($j = 0; $j < count($LoanCase); $j++) {

                    array_push($search_case_id, $LoanCase[$j]->id);
                }

                if (count($Customer) > 0) {
                    for ($i = 0; $i < count($Customer); $i++) {

                        $LoanCase = LoanCase::where('customer_id', '=', $Customer[$i]->id)->get();

                        for ($j = 0; $j < count($LoanCase); $j++) {

                            array_push($search_case_id, $LoanCase[$j]->id);
                        }
                    }
                }

                if (count($Referral) > 0) {
                    for ($i = 0; $i < count($Referral); $i++) {

                        $LoanCase = LoanCase::where('referral_id', '=', $Referral[$i]->id)->get();

                        for ($j = 0; $j < count($LoanCase); $j++) {
                            array_push($search_case_id, $LoanCase[$j]->id);
                        }
                    }
                }

                if (count($searchCase) > 0) {
                    for ($i = 0; $i < count($searchCase); $i++) {

                        $LoanCase = LoanCase::where('id', '=', $searchCase[$i]->id)->get();

                        for ($j = 0; $j < count($LoanCase); $j++) {
                            array_push($search_case_id, $LoanCase[$j]->id);
                        }
                    }
                }

                if (count($LoanCaseBillMain) > 0) {
                    for ($i = 0; $i < count($LoanCaseBillMain); $i++) {

                        $LoanCase = LoanCase::where('id', '=', $LoanCaseBillMain[$i]->case_id)->get();

                        for ($j = 0; $j < count($LoanCase); $j++) {
                            array_push($search_case_id, $LoanCase[$j]->id);
                        }
                    }
                }



                // if (count($search_case_id) > 0)
                // {
                //     $search_case = LoanCase::whereIn('id', $search_case_id)->get();
                // }


                $search_case = LoanCase::whereIn('id', $search_case_id)->get();

                $search_case = DB::table('loan_case as l')
                    ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                    ->leftJoin('referral as r', 'r.id', '=', 'l.referral_id')
                    ->leftJoin('users as u1', 'u1.id', '=', 'l.lawyer_id')
                    ->leftJoin('users as u2', 'u2.id', '=', 'l.clerk_id')
                    ->leftJoin('users as u3', 'u3.id', '=', 'l.sales_user_id')
                    ->leftJoin('branch as b', 'b.id', '=', 'l.branch_id')
                    ->select('l.*', 'c.name as client_name', 'r.name as referral_name', 'u1.name as lawyer_name', 'u2.name as clerk_name', 'u3.name as sales_name', 'b.name as branch_name')
                    // ->where('value', 'like', '%' . $request->input('parties') . '%')
                    ->whereIn('l.id', $search_case_id);

                // if (in_array($current_user->menuroles, ['maker'])) {
                //     if ($current_user->branch_id == 3) {
                //         $search_case =  $search_case->where('l.branch_id', '=', 3);
                //     }
                // }

                $userList = $accessInfo['user_list'];

                if (!in_array($current_user->menuroles, ['account', 'admin', 'management', 'receptionist']) && !in_array($current_user->id, [14])) {
                    if (in_array($current_user->id, [32]) || in_array($current_user->menuroles, ['sales'])) {
                        $search_case = $search_case->where(function ($q) use ($userList, $accessInfo) {
                            $q->whereIn('l.lawyer_id', $userList)
                                ->orWhereIn('l.clerk_id', $userList)
                                ->orWhereIn('l.sales_user_id', $userList)
                                ->orWhereIn('l.id', $accessInfo['case_list']);
                        });
                    } else {
                        $search_case = $search_case->where(function ($q) use ($userList, $accessInfo) {
                            $q->whereIn('l.branch_id', $accessInfo['brancAccessList'])
                                ->orWhereIn('l.lawyer_id', $userList)
                                ->orWhereIn('l.clerk_id', $userList)
                                ->orWhereIn('l.sales_user_id', $userList)
                                ->orWhereIn('l.id', $accessInfo['case_list']);
                        });
                    }
                }



                $search_case =  $search_case->get();


                // $search_case_oos = CaseArchive::where('ref_no', 'like', '%' . $request->input('parties') . '%')
                $search_case_oos = DB::table('cases_outside_system as l')
                    ->leftJoin('users as u1', 'u1.id', '=', 'l.old_pic_id')
                    ->leftJoin('users as u2', 'u2.id', '=', 'l.new_pic_id')
                    ->leftJoin('users as u3', 'u3.id', '=', 'l.lawyer_id')
                    ->where('ref_no', 'like', '%' . $request->input('parties') . '%')
                    ->orWhere('client_name_p', 'like', '%' . $request->input('parties') . '%')
                    ->orWhere('client_name_v', 'like', '%' . $request->input('parties') . '%')
                    ->select('l.*', 'u1.name as old_pic_name', 'u2.name as new_pic_name', 'u3.name as lawyer_name');

                // if (in_array($current_user->menuroles, ['maker'])) {
                //     if ($current_user->branch_id == 3)
                //     {
                //         $search_case_oos =  $search_case_oos->where('l.branch_id','=',3);
                //     }

                // }

                $search_case_oos =  $search_case_oos->get();

                $case_count = count($search_case) + count($search_case_oos);
            }

            return response()->json([
                'status' => 1,
                'view' => view('dashboard.case.table.tbl-case-search', compact('search_case', 'search_case_oos'))->render(),
                'search_case' => $search_case,
                'case_count' => $case_count
            ]);

            $current_user = auth()->user();
            $userRoles = $current_user->menuroles;

            $loan_case_masterlist = DB::table('loan_case_masterlist as m')
                ->where('m.value', 'like', '%' . $request->input('case_ref_no_search') . '%');

            return $loan_case_masterlist;



            $case_list = DB::table('loan_case as l')
                ->leftJoin('case_type', 'case_type.id', '=', 'l.case_type_id')
                ->leftJoin('client', 'client.id', '=', 'l.customer_id')
                ->select('l.*', 'case_type.name AS type_name', 'client.name AS client_name', 'l.id AS case_id');

            if ($userRoles == "lawyer" || $userRoles == "clerk") {
                $case_list = $case_list->where('l.' . $userRoles . '_id', '=', $current_user->id);
            } else if ($userRoles == "chambering") {
                $case_list = $case_list->where('l.lawyer_id', '=', $current_user->id)
                    ->orWhere('l.clerk_id', '=', $current_user->id);
            } else if ($userRoles == "sales") {
                $case_list = $case_list->where('l.sales_user_id', '=', $current_user->id);
            }



            if ($request->input('lawyer')) {
                if ($request->input('lawyer') <> 0) {
                    $case_list = $case_list->where('l.lawyer_id', '=', $request->input('lawyer'));
                }
            }

            if ($request->input('clerk')) {
                if ($request->input('clerk') <> 0) {
                    $case_list = $case_list->where('l.clerk_id', '=', $request->input('clerk'));
                }
            }

            if ($request->input('chambering')) {
                if ($request->input('chambering') <> 0) {
                    $case_list = $case_list->where(function ($q) use ($request) {
                        $q->where('l.lawyer_id', $request->input('chambering'))
                            ->orWhere('l.clerk_id', $request->input('chambering'));
                    });
                }
            }

            if ($request->input('month')) {
                if ($request->input('month') <> 0) {
                    $case_list = $case_list->whereRaw('MONTH(l.created_at) = ?', $request->input('month'));
                }
            }

            if (!empty($request->input('status'))) {
                if ($request->input('status') <> 99) {
                    $case_list = $case_list->where('l.status', '=', $request->input('status'));
                }
            }

            if ($request->input('case_type')) {
                if ($request->input('case_type') <> 0) {
                    $case_list = $case_list->where('l.bank_id', '=', $request->input('case_type'));
                }
            }

            $case_list = $case_list
                ->where('l.status', '!=', 99)
                ->orderBy('l.id', 'DESC')->get();



            return DataTables::of($case_list, $request)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a  href="/case/' . $row->id . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>
                    ';
                    return $actionBtn;
                })
                ->editColumn('case_ref_no', function ($data) {
                    return '<a href="/case/' . $data->id . '">' . $data->case_ref_no . '</a>';
                })
                ->editColumn('percentage', function ($data) {
                    return  $data->percentage . ' %';
                })
                ->editColumn('status', function ($data) {




                    if ($data->status === '2')
                        return '<span class="label bg-info">Open</span>';
                    elseif ($data->status === '0')
                        return '<span class="label bg-success">Closed</span>';
                    elseif ($data->status === '1')
                        return '<span class="label bg-purple">Running</span>';
                    elseif ($data->status === '3')
                        return '<span class="label bg-warning">KIV</span>';
                    elseif ($data->status === '4')
                        return  '<span class="label" style="background-color:orange">Pending Close</span>';
                    elseif ($data->status === '99')
                        return '<span class="label bg-danger">Aborted</span>';
                    elseif ($data->status === 7)
                        return '<span class="label bg-purple">Reviewing</span>';
                    else
                        return '<span class="label bg-danger">Overdue</span>';
                })
                ->rawColumns(['status', 'action', 'case_ref_no'])
                ->make(true);
        }
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
            ->where('loan_case.status', '<>', 99)
            ->orderBy('loan_case.id', 'ASC')
            ->paginate(100);


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
                ->where('loan_case.status', '<>',   99)
                ->orderBy('loan_case.id', 'ASC')
                ->paginate(500);
        } else if ($request->input('role_id') == "11") {
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
                ->orWhere('loan_case.clerk_id', '=',   $request->input('id'))
                ->where('loan_case.status', '<>',   99)
                ->orderBy('loan_case.id', 'ASC')
                ->paginate(500);
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
                ->where('loan_case.status', '<>',   99)
                ->orderBy('loan_case.id', 'ASC')
                ->paginate(500);
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
        $is_manual = 1;

        $current_user = auth()->user();

        if (in_array($current_user->id, [32, 51, 38, 127])) {
            $is_manual = 1;
        }

        $lawyer = Users::where('menuroles', '=', 'lawyer')->where('status', '<>', 99);
        $clerk = Users::where('menuroles', '=', 'clerk')->where('status', '<>', 99);

        $sales = Users::where('id', '=', 6)->get();
        $banks = Banks::where('status', '=', 1)->get();
        $portfolios = Portfolio::where('status', '=', 1)->orderBy('name')->get();
        $case_type = CaseType::where('status', '=', 1)->orderByDesc('name')->get();
        // $referrals = Referral::where('status', '=', 1)->get();

        $referrals = DB::table('referral as r')
            ->leftJoin('banks as b', 'b.id', '=', 'r.bank_id')
            ->select('r.*', 'b.name as bank_name')
            ->where('r.status', '=', 1);

        if ($current_user->branch_id == 3) {
            $referrals = $referrals->where('created_by', '=', $current_user->id);
        }

        $referrals = $referrals->get();


        $userRoles = $current_user->menuroles;

        $branchInfo = BranchController::manageBranchAccess();


        $clerk = Users::where('menuroles', '=', 'clerk')->where('status', '<>', 99);

        // return $branchInfo;

        // $lawyer = DB::table('team_members as t')
        //     ->leftJoin('users as u', 'u.id', '=', 't.user_id')
        //     ->select('t.*', 'u.name', 'u.branch_id', 'u.id as user_id')
        //     ->where('t.status', '<>', 0)
        //     ->where('t.leader', '=', 1)
        //     ->where('u.status', '<>', 99) 
        //     ->orderBy('name', 'asc');

        $lawyer = DB::table('users as u')
            ->select('u.*')
            ->where('u.status', 1)
            ->where('u.is_lawyer', 1)
            ->orderBy('name', 'asc');

        // $clerk = DB::table('team_members as t')
        //     ->leftJoin('users as u', 'u.id', '=', 't.user_id')
        //     ->select('t.*', 'u.name', 'u.branch_id', 'u.id as user_id')
        //     ->where('t.status', '<>', 0)
        //     ->where('t.leader', '=', 0)
        //     ->where('u.status', '<>', 99)
        //     ->orderBy('name', 'asc');

        $clerk = DB::table('users as u')
            ->select('u.*')
            ->where('u.status', 1)
            ->where('u.menuroles', 'clerk')
            ->orderBy('name', 'asc');

        if ($current_user->branch_id == 3) {
            $lawyer = $lawyer->where('u.id', '<>', 32);
            // $clerk = $clerk->where('branch_id', '=', 3);
        }

        $lawyer = $lawyer->orWhereIn('u.id', [2]);

        $lawyer = $lawyer->get();
        $clerk = $clerk->get();
        // $Branchs = $Branchs->get(); 

        $CreateCasePermission = UserAccessControl::where('code', 'CreateCasePermission')->first();
        $user_id_list = explode(',', $CreateCasePermission->user_id_list);

        $sales_list = Users::where(function ($q) use ($user_id_list) {
            $q->whereIn('id', $user_id_list)
                ->orWhere('menuroles', 'sales');
        })->where('branch_id', $current_user->branch_id)->where('status', '<>', 99)->get();

        return view('dashboard.case.create', [
            'templates' => CaseTemplate::all(),
            'lawyers' => $lawyer,
            'clerks' => $clerk,
            'sales' => $sales,
            'sales_list' => $sales_list,
            'is_manual' => $is_manual,
            'banks' => $banks,
            'current_user' => $current_user,
            'referrals' => $referrals,
            'portfolios' => $portfolios,
            'Branchs' => $branchInfo['branch'],
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

        return 1;



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
        $loanCase->targeted_collect_amount = $request->input('targeted_collect_amount');
        $loanCase->agreed_fee = $request->input('agreed_fee');
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

        // return redirect()->route('case.index', ['cases' => TodoList::all()]);
        return redirect()->route('cases.list', 'active');
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

        if (count($group) <= 0) {
            return response()->json(['status' => 0, 'message' => 'No such team handle this case type']);
        }
        // Assign task
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
        $loanCase->remark = $request->input('desc');
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

        if ($request->input('desc') != null) {
            $LoanCaseKivNotes = new LoanCaseKivNotes();

            $LoanCaseKivNotes->case_id =  $loanCase->id;
            $LoanCaseKivNotes->notes =  '<b>New Case</b><br/>' . $request->input('desc');
            $LoanCaseKivNotes->label =  'createcase';
            $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
            $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');
            $LoanCaseKivNotes->status =  1;
            $LoanCaseKivNotes->created_by = $current_user->id;
            $LoanCaseKivNotes->save();
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

        // Temporary stop case for uptown
        // 

        if ($portfolio == 14 || $portfolio ==  45 || $portfolio ==  24) {
            $branch_id = 1;
        } else {

            $branch_id = 2;
        }

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
        $team_id = DB::table('team_members as m')->select('m.team_main_id')->where('m.user_id', '=',  $lawyerID)->where('m.status', '=',  1)->get();

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


        // // check race
        // if ($blnCheckRace == 1) {
        //     $memberPortfolio = DB::table('member_portfolio as m')
        //         ->leftJoin('portfolio as p', 'p.id', '=', 'm.portfolio_id')
        //         ->leftJoin('users as u', 'u.id', '=', 'm.user_id')
        //         ->select(array('m.*', 'p.name', 'u.race'))
        //         ->whereIn('m.user_id',  $member_id)
        //         ->where('u.race', '=',  $race)
        //         ->where('u.status', '=',  1)
        //         ->where('m.portfolio_id', '=',  $portfolio)
        //         ->get();
        // }

        $memberPortfolio = DB::table('member_portfolio as m')
            ->leftJoin('portfolio as p', 'p.id', '=', 'm.portfolio_id')
            ->leftJoin('users as u', 'u.id', '=', 'm.user_id')
            ->select(array('m.*', 'p.name', 'u.race'))
            ->whereIn('m.user_id',  $member_id)
            ->where('m.portfolio_id', '=',  $portfolio)
            ->where('u.status', '=',  1)
            ->get();

        // return $member_id;

        // get the lawyer for the case
        $member_id = [];

        for ($i = 0; $i < count($memberPortfolio); $i++) {

            array_push($member_id, $memberPortfolio[$i]->user_id);
        }

        $now = Carbon::now();

        $clerk = DB::table('users as u')
            ->leftJoin('loan_case', function ($join) use ($member_id, $now) {
                $join->on('u.id', '=', 'loan_case.clerk_id')
                    ->whereIn('loan_case.clerk_id',  $member_id)
                    ->whereRaw('MONTH(loan_case.created_at) = ?', $now->month);
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
    public function show(Request $request, $id)
    {
        $current_user = auth()->user();

        if (!in_array($current_user->menuroles, ['admin', 'management', 'account'])) {
            $accessCaseList = $this->caseManagementEngine();

            if (!in_array($id, $accessCaseList)) {
                return redirect()->route('cases.list', 'active');
            }
        }

        $case = DB::table('loan_case as l')
            ->leftJoin('portfolio as p', 'l.bank_id', '=', 'p.id')
            ->leftJoin('branch as b', 'l.branch_id', '=', 'b.id')
            ->select('l.*', 'p.name as portfolio', 'b.name as branch_name')
            ->where('l.id', '=', $id)
            ->first();

        if (!$case) {
            return redirect()->route('cases.list', 'active');
        }

        $now = time(); // or your date as well
        $your_date = strtotime($case->created_at);
        $datediff = $now - $your_date;
        $datediff = ($datediff / (60 * 60 * 24));
        $datediff = number_format($datediff);

        // get PIC
        $lawyer = Users::where('id', '=', $case->lawyer_id)->first();
        $clerk = Users::where('id', '=', $case->clerk_id)->first();
        $sales = Users::where('id', '=', $case->sales_user_id)->first();


        $caseMasterListCategory = CaseMasterListCategory::where('status', '=', 1)->orderBy('name', 'ASC')->get();

        $caseMasterListField = DB::table('case_masterlist_field')
            ->leftJoin('loan_case_masterlist',  function ($join) use ($id) {
                $join->on('loan_case_masterlist.masterlist_field_id', '=', 'case_masterlist_field.id');
                $join->where('loan_case_masterlist.case_id', '=', $id);
            })
            ->select('case_masterlist_field.*', 'loan_case_masterlist.value')
            ->orderBy('case_masterlist_field.id')
            ->get();

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

        // get parameter
        $parameter_controller = new ParameterController;
        $parameters = $parameter_controller->getParameter('payment_type');

        // get client data
        $customer = Customer::where('id', '=', $case->customer_id)->first();
        $referral = Referral::where('id', '=', $case->referral_id)->first();

        // get bank list

        $banks = Banks::where('status', '=', 1)->orderBy('name', 'asc')->get();

        $loan_case_trust_main_receive = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->leftJoin('office_bank_account as o', 'o.id', '=', 'v.office_account_id')
            ->leftJoin('users as u', 'v.user_id', '=', 'u.id')
            ->select('v.*', 'o.name as office_account', 'o.account_no as office_account_no', 'u.name as requestor')
            ->where('v.case_id', '=', $id)
            ->where('v.voucher_type', '=', 3)
            ->where('v.status', '<>', 99)
            ->get();

        $loan_case_trust_main_dis = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->leftJoin('users as u', 'v.user_id', '=', 'u.id')
            ->select('v.*', 'u.name as requestor')
            ->where('v.case_id', '=', $id)
            ->where('v.voucher_type', '=', 2)
            ->where('v.status', '<>', 99)
            ->get();

        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [5, 6])) {
                $OfficeBankAccountOA = OfficeBankAccount::where('status', '=', 1)->where('account_type', 'OA')->whereIn('branch_id', [5, 6])->get();
                $OfficeBankAccountCA = OfficeBankAccount::where('status', '=', 1)->where('account_type', 'CA')->whereIn('branch_id', [5, 6])->get();
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id', [5, 6])->get();
            } else {
                $OfficeBankAccountOA = OfficeBankAccount::where('status', '=', 1)->where('account_type', 'OA')->where('branch_id', '=', $current_user->branch_id)->get();
                $OfficeBankAccountCA = OfficeBankAccount::where('status', '=', 1)->where('account_type', 'CA')->where('branch_id', '=', $current_user->branch_id)->get();
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
            }
        } else {

            if ($current_user->branch_id == 3) {
                $OfficeBankAccountOA = OfficeBankAccount::where('status', '=', 1)->where('account_type', 'OA')->where('branch_id', '=', $current_user->branch_id)->get();
                $OfficeBankAccountCA = OfficeBankAccount::where('status', '=', 1)->where('account_type', 'CA')->where('branch_id', '=', $current_user->branch_id)->get();
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
            } else if ($current_user->branch_id == 5 || in_array($current_user->id, [51, 127])) {
                $OfficeBankAccountOA = OfficeBankAccount::where('status', '=', 1)->where('account_type', 'OA')->where('branch_id', '=', 5)->get();
                $OfficeBankAccountCA = OfficeBankAccount::where('status', '=', 1)->where('account_type', 'CA')->where('branch_id', '=', 5)->get();
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 5)->get();
            } else {
                $OfficeBankAccountOA = OfficeBankAccount::where('status', '=', 1)->where('account_type', 'OA')->get();
                $OfficeBankAccountCA = OfficeBankAccount::where('status', '=', 1)->where('account_type', 'CA')->get();
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
            }
        }

        $quotation_template = QuotationTemplateMain::where('status', '=', 1)->get();

        $loanCaseBillMain = DB::table('loan_case_bill_main AS m')
            ->leftJoin('users AS u', 'u.id', '=', 'm.created_by')
            ->select('m.*', 'u.name as prepare_by')
            ->where('case_id', '=', $id)
            ->where('m.status', '=',  '1')
            ->get();

        $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $id)->first();

        // Get generated document
        $fileFolder = DocumentTemplateFileFolder::where('status', '=', 1)->get();
        $documentTemplateFilev2 = DB::table('document_template_file_main AS m')
            ->select('m.*')
            ->where('m.status', '=',  '1')
            ->orderBy('m.name', 'ASC')
            ->get();

        for ($j = 0; $j < count($fileFolder); $j++) {

            $fileFolder[$j]->count = DocumentTemplateFileMain::where('folder_id', $fileFolder[$j]->id)->count();
        }

        for ($j = 0; $j < count($documentTemplateFilev2); $j++) {

            $documentTemplateFilev2[$j]->count = DocumentTemplateFileDetails::where('document_template_file_main_id', $documentTemplateFilev2[$j]->id)->count();
        }


        // Get all notes
        $LoanCaseKIVNotes = DB::table('loan_case_kiv_notes AS n')
            ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
            ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
            ->where('n.case_id', '=',  $id)
            ->where('n.status', '<>',  99)
            ->orderBy('n.created_at', 'DESC')
            ->get();

        $LoanAttachment = QueryController::getCaseAttachment($id, 1);

        $referrals = DB::table('referral as r')
            ->leftJoin('banks as b', 'b.id', '=', 'r.bank_id')
            ->select('r.*', 'b.name as bank_name')
            ->where('r.status', '=', 1)
            ->get();

        $bank_lo_date = DB::table('loan_case_masterlist as m')
            ->where('m.case_id', '=', $id)
            ->where('m.masterlist_field_id', '=', 398)
            ->first();

        $parties_list = array();

        if ($customer) {
            array_push($parties_list,  array('party' => 'Client', 'name' => $customer->name));
        }


        $party_masterlist = DB::table('loan_case_masterlist as m')
            ->leftJoin('case_masterlist_field AS f', 'f.id', '=', 'm.masterlist_field_id')
            ->leftJoin('case_masterlist_field_category AS c', 'c.id', '=', 'f.case_field_id')
            ->select('m.*', 'c.name as master_cat_name')
            ->where('m.case_id', '=', $id)
            ->where('f.master_list_type', '=', 'parties_name')
            ->get();


        for ($i = 0; $i < count($party_masterlist); $i++) {
            array_push($parties_list,  array('party' =>  $party_masterlist[$i]->master_cat_name, 'name' => $party_masterlist[$i]->value));
        }

        // get parameter
        $attachment_type = $parameter_controller->getParameter('attachment_type');

        $Portfolio = DB::table('portfolio')->where('status', '=', 1)->get();

        $BonusRequestListSent = BonusRequestList::where('case_id', '=', $id)->where('bonus_type', '=', 'CLOSEDCASE')->count();
        $SMPBonusRequestListSent = BonusRequestList::where('case_id', '=', $id)->where('bonus_type', '=', 'SMPSIGNED')->count();

        $closeFileEntry = LedgerEntriesV2::where('case_id', '=', $id)->where('type', 'CLOSEFILE_OUT')->first();
        $closeFileEntry_in = LedgerEntriesV2::where('case_id', '=', $id)->where('type', 'CLOSEFILE_IN')->first();

        $CheckListMain = CheckListMain::where('status', 1)->get();
        $CheckListDetails = CheckListDetails::where('status', 1)->get();

        $QuotationGeneratorMain = null;
        $claims = null;
        $ledgers = null;
        $LoanCaseNotes = null;
        $LoanAttachmentMarketing = null;
        $LoanCasePNCNotes = null;

        if (AccessController::UserAccessPermissionController(PermissionController::QuotationGeneratorPermission()) == true) {
            $QuotationGeneratorMain = QuotationGeneratorMain::where('user_id', $current_user->id)->get();
        }

        if (AccessController::UserAccessPermissionController(PermissionController::ClaimsPermission()) == true) {
            $claims = DB::table('claims_type as a')
                ->leftjoin('claims_request as b', function ($join) use ($id) {
                    $join->on('b.claims_type', '=', 'a.id')
                        ->where('b.case_id', '=', $id);
                })
                ->select('a.*', 'b.status as claims_status', 'b.created_at as submit_date', 'b.amount as amount')
                ->get();
        }

        if (AccessController::UserAccessPermissionController(PermissionController::LedgerPermission()) == true) {
            $ledgers = DB::table('ledger_entries_v2 as a')
                ->leftJoin('office_bank_account as c', 'c.id', '=', 'a.bank_id')
                ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'a.loan_case_main_bill_id')
                ->leftJoin('loan_case as e', 'e.id', '=', 'a.case_id')
                ->leftJoin('voucher_main as f', 'f.id', '=', 'a.key_id')
                // ->leftJoin('voucher_details as vd', 'f.id', '=', 'vd.voucher_main_id')
                // ->leftJoin('loan_case_bill_details as lb', 'lb.id', '=', 'vd.account_details_id')
                // ->leftJoin('account_item as ai', 'ai.id', '=', 'lb.account_item_id')
                ->select('a.*', 'c.name as bank_name', 'c.account_no as bank_account_no', 'e.case_ref_no', 'e.id as case_id', 'f.voucher_no as voucher_no', 'f.payee as payee_voucher',)
                ->where('e.id', '=',  $id)
                ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN', 'ABORTFILE_IN'])
                ->where('a.status', '<>',  99)
                ->orderBy('a.date', 'ASC')
                ->orderBy('a.last_row_entry', 'asc')
                ->get();

            for ($i = 0; $i < count($ledgers); $i++) {
                if (in_array($ledgers[$i]->type, ['BILL_DISB',])) {
                    $accoutItem = DB::table('voucher_main as a')
                        ->leftJoin('voucher_details as vd', 'a.id', '=', 'vd.voucher_main_id')
                        ->leftJoin('loan_case_bill_details as lb', 'lb.id', '=', 'vd.account_details_id')
                        ->leftJoin('account_item as ai', 'ai.id', '=', 'lb.account_item_id')
                        ->select('a.*', 'ai.name as ItemName')
                        ->where('a.id', '=',  $ledgers[$i]->key_id)
                        ->where('a.status', '<>',  99)
                        ->get();

                    $itemName = '';

                    for ($j = 0; $j < count($accoutItem); $j++) {

                        $itemName = $itemName . '- ' . $accoutItem[$j]->ItemName . '<br/>';
                        //$itemName = '- '.$ledgers[$i]->key_id.'<br/>';
                    }

                    //$ledgers[$i]->remark.'<br/>'.$itemName;
                    if ($ledgers[$i]->remark != '') {
                        $ledgers[$i]->remark = $ledgers[$i]->remark . '<br/>' . $itemName;
                    } else {
                        $ledgers[$i]->remark = $itemName;
                    }
                }
            }
        }

        if (AccessController::UserAccessPermissionController(PermissionController::MarketingNotePermission()) == true) {
            // get Markting Notes
            $LoanCaseNotes = DB::table('loan_case_notes AS n')
                ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
                ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
                ->where('n.case_id', '=',  $id)
                ->where('n.status', '<>',  99)
                ->orderBy('n.created_at', 'DESC')
                ->get();
        }

        if (AccessController::UserAccessPermissionController(PermissionController::MarketingBillPermission()) == true) {
            $LoanAttachmentMarketing = QueryController::getCaseAttachment($id, 2);
        }

        if ($current_user->management == 1) {
            // Get PNC Notes
            $LoanCasePNCNotes = DB::table('loan_case_pnc_notes AS n')
                ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
                ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
                ->where('n.case_id', '=',  $id)
                ->where('n.status', '<>',  99)
                ->orderBy('n.created_at', 'DESC')
                ->get();
        }

        $client = ClientsController::getCustomerDetails($case->customer_id);

        return view('dashboard.case.show', [
            'QuotationGeneratorMain' => $QuotationGeneratorMain,
            'CheckListMain' => $CheckListMain,
            'CheckListDetails' => $CheckListDetails,
            'case' => $case,
            'claims' => $claims,
            'closeFileEntry' => $closeFileEntry,
            'closeFileEntry_in' => $closeFileEntry_in,
            'Portfolio' => $Portfolio,
            'referrals' => $referrals,
            'current_user' => $current_user,
            'caseMasterListCategory' => $caseMasterListCategory,
            'caseMasterListField' => $caseMasterListField,
            'datediff' => $datediff,
            'bank_lo_date' => $bank_lo_date,
            'couriers' => $couriers,
            'customer' => $client['customer'],
            'referral' => $referral,
            'parameters' => $parameters,
            'banks' => $banks,
            'sales' => $sales,
            'lawyer' => $lawyer,
            'clerk' => $clerk,
            'fileFolder' => $fileFolder,
            'loan_case_trust_main_dis' => $loan_case_trust_main_dis,
            'loan_case_trust_main_receive' => $loan_case_trust_main_receive,
            'loanCaseBillMain' => $loanCaseBillMain,
            'quotation_template' => $quotation_template,
            'account_template_with_cat' => $joinData,
            'OfficeBankAccountOA' => $OfficeBankAccountOA,
            'OfficeBankAccountCA' => $OfficeBankAccountCA,
            'OfficeBankAccount' => $OfficeBankAccount,
            'LoanCaseTrustMain' => $LoanCaseTrustMain,
            'documentTemplateFilev2' => $documentTemplateFilev2,
            'LoanCaseNotes' => $LoanCaseNotes,
            'LoanCaseKIVNotes' => $LoanCaseKIVNotes,
            'LoanCasePNCNotes' => $LoanCasePNCNotes,
            'LoanAttachment' => $LoanAttachment,
            'LoanAttachmentMarketing' => $LoanAttachmentMarketing,
            'attachment_type' => $attachment_type,
            'parties_list' => $parties_list,
            'ledgers' => $ledgers,
            'BonusRequestListSent' => $BonusRequestListSent,
            'SMPBonusRequestListSent' => $SMPBonusRequestListSent,
            'ClientOtherLoanCase' => $client['ClientOtherLoanCase'],
        ]);
    }

    public function showBak4(Request $request, $id)
    {

        $current_user = auth()->user();
        $role = $current_user->menuroles;
        $accessInfo = AccessController::manageAccess();

        $user_id = [];
        $claims = [];

        $accessSummaryReportReferral = 0;


        if (!in_array($current_user->menuroles, ['admin', 'management', 'account'])) {
            $userList = $accessInfo['user_list'];

            $case_check = DB::table('loan_case')
                ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
                ->leftJoin('client', 'client.id', '=', 'loan_case.customer_id')
                ->select(array('loan_case.*', 'case_type.name AS type_name', 'client.name AS client_name'))
                ->where('loan_case.id', '=', $id);

            if ($userList) {
                $case_check = $case_check->where(function ($q) use ($userList, $accessInfo, $current_user) {

                    if (count($accessInfo['brancAccessList']) > 0) {
                        if ($current_user->id == 136) {
                            $q->whereIn('branch_id', $accessInfo['brancAccessList'])
                                ->orWhereIn('lawyer_id', $userList)
                                ->orWhereIn('clerk_id', $userList)
                                ->orWhereIn('sales_user_id', $userList)
                                ->orWhereIn('loan_case.id', $accessInfo['case_list']);
                        } else {
                            $q
                                ->orWhereIn('lawyer_id', $userList)
                                ->orWhereIn('clerk_id', $userList)
                                ->orWhereIn('sales_user_id', $userList)
                                ->orWhereIn('loan_case.id', $accessInfo['case_list']);
                        }
                    } else {
                        $q->whereIn('lawyer_id', $userList)
                            ->orWhereIn('clerk_id', $userList)
                            ->orWhereIn('sales_user_id', $userList)
                            ->orWhereIn('loan_case.id', $accessInfo['case_list']);
                    }
                });
            } else {
                $case_check = $case_check->whereIn('branch_id', $accessInfo['brancAccessList']);
            }

            $case_check = $case_check->first();

            if (!$case_check) {
                return redirect()->route('cases.list', 'active');
            }
        }

        $case = DB::table('loan_case as l')
            ->leftJoin('portfolio as p', 'l.bank_id', '=', 'p.id')
            ->leftJoin('branch as b', 'l.branch_id', '=', 'b.id')
            ->select('l.*', 'p.name as portfolio', 'b.name as branch_name')
            ->where('l.id', '=', $id)
            ->first();

        if (!$case) {
            return redirect()->route('cases.list', 'active');
        }

        //Check access to sales referral report
        if (in_array($current_user->menuroles, ['admin', 'management', 'sales', 'account', 'maker'])) {
            $accessSummaryReportReferral = 1;
        } else {
            if ($current_user->id == $case->sales_user_id) {
                $accessSummaryReportReferral = 1;
            } else {
                if (in_array($current_user->id, [88, 13])) {
                    $accessSummaryReportReferral = 1;
                }
            }
        }

        $now = time(); // or your date as well
        $your_date = strtotime($case->created_at);
        $datediff = $now - $your_date;
        $datediff = ($datediff / (60 * 60 * 24));
        $datediff = number_format($datediff);

        // get PIC
        $lawyer = Users::where('id', '=', $case->lawyer_id)->first();
        $clerk = Users::where('id', '=', $case->clerk_id)->first();
        $sales = Users::where('id', '=', $case->sales_user_id)->first();

        $caseTemplateCategories = CaseTemplateCategories::all();
        $caseTemplate = CaseTemplateMain::all();

        $caseMasterListCategory = CaseMasterListCategory::where('status', '=', 1)->orderBy('name', 'ASC')->get();

        $caseMasterListField = DB::table('case_masterlist_field')
            ->leftJoin('loan_case_masterlist',  function ($join) use ($id) {
                $join->on('loan_case_masterlist.masterlist_field_id', '=', 'case_masterlist_field.id');
                $join->where('loan_case_masterlist.case_id', '=', $id);
            })
            ->select('case_masterlist_field.*', 'loan_case_masterlist.value')
            ->orderBy('case_masterlist_field.id')
            ->get();

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

        $loan_dispatch = DB::table('loan_case_dispatch')
            ->leftJoin('courier', 'loan_case_dispatch.courier_id', '=', 'courier.id')
            ->select('loan_case_dispatch.*', 'courier.name AS courier_name')
            ->where('case_id', '=', $id)
            ->get();

        // get parameter
        $parameter_controller = new ParameterController;
        $parameters = $parameter_controller->getParameter('payment_type');

        // get client data
        $customer = Customer::where('id', '=', $case->customer_id)->first();
        $referral = Referral::where('id', '=', $case->referral_id)->first();

        // get bank list

        $banks = Banks::where('status', '=', 1)->orderBy('name', 'asc')->get();

        $loan_case_trust_main_receive = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->leftJoin('office_bank_account as o', 'o.id', '=', 'v.office_account_id')
            ->leftJoin('users as u', 'v.user_id', '=', 'u.id')
            ->select('v.*', 'o.name as office_account', 'o.account_no as office_account_no', 'u.name as requestor')
            ->where('v.case_id', '=', $id)
            ->where('v.voucher_type', '=', 3)
            ->where('v.status', '<>', 99)
            ->get();

        $loan_case_trust_main_dis = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->leftJoin('users as u', 'v.user_id', '=', 'u.id')
            ->select('v.*', 'u.name as requestor')
            ->where('v.case_id', '=', $id)
            ->where('v.voucher_type', '=', 2)
            ->where('v.status', '<>', 99)
            ->get();

        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [5, 6])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id', [5, 6])->get();
            } else {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
            }
        } else {

            if ($current_user->branch_id == 3) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
            } else if ($current_user->branch_id == 5 || in_array($current_user->id, [51, 127])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 5)->get();
            } else {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
            }
        }

        $quotation_template = QuotationTemplateMain::where('status', '=', 1)->get();

        $loanCaseBillMain = DB::table('loan_case_bill_main AS m')
            ->leftJoin('users AS u', 'u.id', '=', 'm.created_by')
            ->select('m.*', 'u.name as prepare_by')
            ->where('case_id', '=', $id)
            ->where('m.status', '=',  '1')
            ->get();

        // $loanCaseBillMainV2 = DB::table('loan_case_bill_main AS m') 
        //     ->leftJoin('users AS u', 'u.id', '=', 'm.created_by')
        //     ->select('m.*', 'u.name as prepare_by')
        //     ->where('case_id', '=', $id)
        //     ->where('m.status', '=',  '1')
        //     ->get();

        $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $id)->first();

        // Get generated document
        $fileFolder = DocumentTemplateFileFolder::where('status', '=', 1)->get();
        $documentTemplateFilev2 = DB::table('document_template_file_main AS m')
            ->select('m.*')
            ->where('m.status', '=',  '1')
            ->orderBy('m.name', 'ASC')
            ->get();

        // get Markting Notes
        $LoanCaseNotes = DB::table('loan_case_notes AS n')
            ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
            ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
            ->where('n.case_id', '=',  $id)
            ->where('n.status', '<>',  99)
            ->orderBy('n.created_at', 'DESC')
            ->get();

        // Get all notes
        $LoanCaseKIVNotes = DB::table('loan_case_kiv_notes AS n')
            ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
            ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
            ->where('n.case_id', '=',  $id)
            ->where('n.status', '<>',  99)
            ->orderBy('n.created_at', 'DESC')
            ->get();

        // Get PNC Notes
        $LoanCasePNCNotes = DB::table('loan_case_pnc_notes AS n')
            ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
            ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
            ->where('n.case_id', '=',  $id)
            ->where('n.status', '<>',  99)
            ->orderBy('n.created_at', 'DESC')
            ->get();

        $LoanAttachmentMarketing = QueryController::getCaseAttachment($id, 2);
        $LoanAttachment = QueryController::getCaseAttachment($id, 1);

        $referrals = DB::table('referral as r')
            ->leftJoin('banks as b', 'b.id', '=', 'r.bank_id')
            ->select('r.*', 'b.name as bank_name')
            ->where('r.status', '=', 1)
            ->get();

        $property_details = '-';
        $full_loan_details = '-';

        $property_masterlist = DB::table('loan_case_masterlist as m')
            ->where('m.case_id', '=', $id)
            ->where('m.masterlist_field_id', '=', 116)
            ->first();

        $bank_lo_date = DB::table('loan_case_masterlist as m')
            ->where('m.case_id', '=', $id)
            ->where('m.masterlist_field_id', '=', 398)
            ->first();

        if ($property_masterlist) {
            $property_details = $property_masterlist->value;
        }

        $property_masterlist_loan_details = DB::table('loan_case_masterlist as m')
            ->where('m.case_id', '=', $id)
            ->where('m.masterlist_field_id', '=', 141)
            ->first();

        if ($property_masterlist_loan_details) {
            $full_loan_details = $property_masterlist_loan_details->value;
        }

        $parties_list = array();

        if ($customer) {
            array_push($parties_list,  array('party' => 'Client', 'name' => $customer->name));
        }


        $party_masterlist = DB::table('loan_case_masterlist as m')
            ->leftJoin('case_masterlist_field AS f', 'f.id', '=', 'm.masterlist_field_id')
            ->leftJoin('case_masterlist_field_category AS c', 'c.id', '=', 'f.case_field_id')
            ->select('m.*', 'c.name as master_cat_name')
            ->where('m.case_id', '=', $id)
            ->where('f.master_list_type', '=', 'parties_name')
            ->get();


        for ($i = 0; $i < count($party_masterlist); $i++) {
            array_push($parties_list,  array('party' =>  $party_masterlist[$i]->master_cat_name, 'name' => $party_masterlist[$i]->value));
        }

        // get parameter
        $attachment_type = $parameter_controller->getParameter('attachment_type');

        $financed_fee = 0;
        $loan_caseBillMain = DB::table('loan_case_bill_main as m')
            ->where('m.name', '<>', 'Vendor (title & Master Title)')
            ->where('m.case_id', '=', $id)
            ->first();

        $ledgers = DB::table('ledger_entries_v2 as a')
            ->leftJoin('office_bank_account as c', 'c.id', '=', 'a.bank_id')
            ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'a.loan_case_main_bill_id')
            ->leftJoin('loan_case as e', 'e.id', '=', 'a.case_id')
            ->leftJoin('voucher_main as f', 'f.id', '=', 'a.key_id')
            ->select('a.*', 'c.name as bank_name', 'c.account_no as bank_account_no', 'e.case_ref_no', 'e.id as case_id', 'f.voucher_no as voucher_no', 'f.payee as payee_voucher')
            ->where('e.id', '=',  $id)
            ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN', 'ABORTFILE_IN'])
            ->where('a.status', '<>',  99)
            ->orderBy('a.date', 'ASC')
            ->orderBy('a.last_row_entry', 'desc')
            ->get();

        $Portfolio = DB::table('portfolio')->where('status', '=', 1)->get();

        $BonusRequestListSent = BonusRequestList::where('case_id', '=', $id)->where('bonus_type', '=', 'CLOSEDCASE')->count();
        $SMPBonusRequestListSent = BonusRequestList::where('case_id', '=', $id)->where('bonus_type', '=', 'SMPSIGNED')->count();

        $closeFileEntry = LedgerEntries::where('case_id', '=', $id)->where('type', 'CLOSEFILEOUT')->first();
        $closeFileEntry = LedgerEntriesV2::where('case_id', '=', $id)->where('type', 'CLOSEFILE_OUT')->first();
        $closeFileEntry_in = LedgerEntriesV2::where('case_id', '=', $id)->where('type', 'CLOSEFILE_IN')->first();

        $Branch = Branch::where('id', '=', $case->branch_id)->first();

        $Lawyer_claims = AccessController::UserAccessController(ClaimsController::getAccessCode());

        if ($Lawyer_claims == true) {
            $claims = DB::table('claims_type as a')
                ->leftjoin('claims_request as b', function ($join) use ($id) {
                    $join->on('b.claims_type', '=', 'a.id')
                        ->where('b.case_id', '=', $id);
                })
                ->select('a.*', 'b.status as claims_status', 'b.created_at as submit_date', 'b.amount as amount')
                ->get();
        }

        $CheckListMain = CheckListMain::where('status', 1)->get();
        $CheckListDetails = CheckListDetails::where('status', 1)->get();

        $QuotationGeneratorMain = null;

        if (AccessController::UserAccessPermissionController(PermissionController::QuotationGeneratorPermission()) == false) {
            $QuotationGeneratorMain = QuotationGeneratorMain::where('user_id', $current_user->id)->get();
        } else {
            $QuotationGeneratorMain = QuotationGeneratorMain::where('user_id', $current_user->id)->get();
        }

        $EditClientPermission = AccessController::UserAccessPermissionController(PermissionController::EditClientPermission());

        $masterlistValue = $this->loadMasterListUpdateValue($id);

        // return $masterlistValue;

        $client = ClientsController::getCustomerDetails($case->customer_id);

        return view('dashboard.case.show', [
            // 'masterlistValue' => $masterlistValue,
            'QuotationGeneratorMain' => $QuotationGeneratorMain,
            'EditClientPermission' => $EditClientPermission,
            'CheckListMain' => $CheckListMain,
            'CheckListDetails' => $CheckListDetails,
            'case' => $case,
            'Branch' => $Branch,
            'claims' => $claims,
            'closeFileEntry' => $closeFileEntry,
            'closeFileEntry_in' => $closeFileEntry_in,
            'Portfolio' => $Portfolio,
            'referrals' => $referrals,
            'caseTemplate' => $caseTemplate,
            'caseTemplateCategories' => $caseTemplateCategories,
            'current_user' => $current_user,
            'caseMasterListCategory' => $caseMasterListCategory,
            'caseMasterListField' => $caseMasterListField,
            'datediff' => $datediff,
            'bank_lo_date' => $bank_lo_date,
            'couriers' => $couriers,
            'loan_dispatch' => $loan_dispatch,
            'customer' => $client['customer'],
            'referral' => $referral,
            'parameters' => $parameters,
            'banks' => $banks,
            'sales' => $sales,
            'lawyer' => $lawyer,
            'clerk' => $clerk,
            'role' => $role,
            'fileFolder' => $fileFolder,
            'loan_case_trust_main_dis' => $loan_case_trust_main_dis,
            'loan_case_trust_main_receive' => $loan_case_trust_main_receive,
            'loanCaseBillMain' => $loanCaseBillMain,
            'quotation_template' => $quotation_template,
            'account_template_with_cat' => $joinData,
            'OfficeBankAccount' => $OfficeBankAccount,
            'LoanCaseTrustMain' => $LoanCaseTrustMain,
            'documentTemplateFilev2' => $documentTemplateFilev2,
            'LoanCaseNotes' => $LoanCaseNotes,
            'LoanCaseKIVNotes' => $LoanCaseKIVNotes,
            'LoanCasePNCNotes' => $LoanCasePNCNotes,
            'LoanAttachment' => $LoanAttachment,
            'LoanAttachmentMarketing' => $LoanAttachmentMarketing,
            'property_details' => $property_details,
            'full_loan_details' => $full_loan_details,
            'attachment_type' => $attachment_type,
            'parties_list' => $parties_list,
            'ledgers' => $ledgers,
            'BonusRequestListSent' => $BonusRequestListSent,
            'SMPBonusRequestListSent' => $SMPBonusRequestListSent,
            // 'ClientOtherLoanCase' => $ClientOtherLoanCase,
            'ClientOtherLoanCase' => $client['ClientOtherLoanCase'],
            'accessSummaryReportReferral' => $accessSummaryReportReferral,
            'Lawyer_claims' => $Lawyer_claims
        ]);
    }


    public function loadMasterListUpdateValue($id)
    {
        $vendor_name = '';
        $purchaser_name = '';

        $CaseMasterListMainCat = CaseMasterListMainCat::where('letter_head', 1)->orderBy('order', 'asc')->get();

        for ($i = 0; $i < count($CaseMasterListMainCat); $i++) {

            $master_list = DB::table('loan_case_masterlist as m')
                ->leftJoin('case_masterlist_field AS f', 'f.id', '=', 'm.masterlist_field_id')
                ->leftJoin('case_masterlist_field_category AS c', 'c.id', '=', 'f.case_field_id')
                ->select('m.*')
                ->where('m.case_id', '=', $id)
                ->where('f.letter_head', 1)
                ->where('f.master_list_code', $CaseMasterListMainCat[$i]->code)
                ->get();

            $CaseMasterListMainCat[$i]->details = $master_list;
        }


        return ([
            'CaseMasterListMainCat' =>  $CaseMasterListMainCat,
        ]);
    }

    public function loadMasterlistPrintInfo($id)
    {
        $property_details = '-';
        $full_loan_details = '-';

        $case = DB::table('loan_case as l')
            ->leftJoin('portfolio as p', 'l.bank_id', '=', 'p.id')
            ->leftJoin('branch as b', 'l.branch_id', '=', 'b.id')
            ->select('l.*', 'p.name as portfolio', 'b.name as branch_name')
            ->where('l.id', '=', $id)
            ->first();

        $property_masterlist = DB::table('loan_case_masterlist as m')
            ->where('m.case_id', '=', $id)
            ->where('m.masterlist_field_id', '=', 116)
            ->first();

        $bank_lo_date = DB::table('loan_case_masterlist as m')
            ->where('m.case_id', '=', $id)
            ->where('m.masterlist_field_id', '=', 398)
            ->first();

        if ($property_masterlist) {
            $property_details = $property_masterlist->value;
        }

        $property_masterlist_loan_details = DB::table('loan_case_masterlist as m')
            ->where('m.case_id', '=', $id)
            ->where('m.masterlist_field_id', '=', 141)
            ->first();

        $file_ref = DB::table('loan_case_masterlist as m')
            ->where('m.case_id', '=', $id)
            ->where('m.masterlist_field_id', '=', 160)
            ->first();


        if ($property_masterlist_loan_details) {
            $full_loan_details = $property_masterlist_loan_details->value;
        }

        $parties_list = array();
        $customer = Customer::where('id', '=', $case->customer_id)->first();
        if ($customer) {
            array_push($parties_list,  array('party' => 'Client', 'name' => $customer->name));
        }

        $party_masterlist = DB::table('loan_case_masterlist as m')
            ->leftJoin('case_masterlist_field AS f', 'f.id', '=', 'm.masterlist_field_id')
            ->leftJoin('case_masterlist_field_category AS c', 'c.id', '=', 'f.case_field_id')
            ->select('m.*', 'c.name as master_cat_name')
            ->where('m.case_id', '=', $id)
            ->where('f.master_list_type', '=', 'parties_name')
            ->get();


        for ($i = 0; $i < count($party_masterlist); $i++) {
            array_push($parties_list,  array('party' =>  $party_masterlist[$i]->master_cat_name, 'name' => $party_masterlist[$i]->value, 'id' => $party_masterlist[$i]->masterlist_field_id));
        }

        $masterlistValue = $this->loadMasterListUpdateValue($id);



        return response([
            'view' => view('dashboard.case.section.d-print-info', compact('masterlistValue', 'property_details', 'full_loan_details', 'case', 'file_ref'))->render(),
            'ddl' => view('dashboard.case.section.d-billto-party-option', compact('masterlistValue', 'parties_list'))->render(),
            'file_ref' => $file_ref,
        ]);
    }

    public function caseDetails(Request $request, $id)
    {

        $current_user = auth()->user();
        $role = $current_user->menuroles;
        $accessInfo = AccessController::manageAccess();

        $user_id = [];
        $claims = [];

        $accessSummaryReportReferral = 0;


        if (!in_array($current_user->menuroles, ['admin', 'management', 'account'])) {
            $userList = $accessInfo['user_list'];

            $case_check = DB::table('loan_case')
                ->leftJoin('case_type', 'case_type.id', '=', 'loan_case.case_type_id')
                ->leftJoin('client', 'client.id', '=', 'loan_case.customer_id')
                ->select(array('loan_case.*', 'case_type.name AS type_name', 'client.name AS client_name'))
                ->where('loan_case.id', '=', $id);

            if ($userList) {
                $case_check = $case_check->where(function ($q) use ($userList, $accessInfo, $current_user) {

                    if (count($accessInfo['brancAccessList']) > 0) {
                        if ($current_user->id == 136) {
                            $q->whereIn('branch_id', $accessInfo['brancAccessList'])
                                ->orWhereIn('lawyer_id', $userList)
                                ->orWhereIn('clerk_id', $userList)
                                ->orWhereIn('sales_user_id', $userList)
                                ->orWhereIn('loan_case.id', $accessInfo['case_list']);
                        } else {
                            $q
                                ->orWhereIn('lawyer_id', $userList)
                                ->orWhereIn('clerk_id', $userList)
                                ->orWhereIn('sales_user_id', $userList)
                                ->orWhereIn('loan_case.id', $accessInfo['case_list']);
                        }
                    } else {
                        $q->whereIn('lawyer_id', $userList)
                            ->orWhereIn('clerk_id', $userList)
                            ->orWhereIn('sales_user_id', $userList)
                            ->orWhereIn('loan_case.id', $accessInfo['case_list']);
                    }
                });
            } else {
                $case_check = $case_check->whereIn('branch_id', $accessInfo['brancAccessList']);
            }

            $case_check = $case_check->first();

            if (!$case_check) {
                return redirect()->route('cases.list', 'active');
            }
        }

        $case = DB::table('loan_case as l')
            ->leftJoin('portfolio as p', 'l.bank_id', '=', 'p.id')
            ->leftJoin('branch as b', 'l.branch_id', '=', 'b.id')
            ->select('l.*', 'p.name as portfolio', 'b.name as branch_name')
            ->where('l.id', '=', $id)
            ->first();

        if (!$case) {
            return redirect()->route('cases.list', 'active');
        }

        //Check access to sales referral report
        if (in_array($current_user->menuroles, ['admin', 'management', 'sales', 'account', 'maker'])) {
            $accessSummaryReportReferral = 1;
        } else {
            if ($current_user->id == $case->sales_user_id) {
                $accessSummaryReportReferral = 1;
            } else {
                if (in_array($current_user->id, [88, 13])) {
                    $accessSummaryReportReferral = 1;
                }
            }
        }

        $now = time(); // or your date as well
        $your_date = strtotime($case->created_at);
        $datediff = $now - $your_date;
        $datediff = ($datediff / (60 * 60 * 24));
        $datediff = number_format($datediff);

        // get PIC
        $lawyer = Users::where('id', '=', $case->lawyer_id)->first();
        $clerk = Users::where('id', '=', $case->clerk_id)->first();
        $sales = Users::where('id', '=', $case->sales_user_id)->first();

        $caseTemplateCategories = CaseTemplateCategories::all();
        $caseTemplate = CaseTemplateMain::all();

        $caseMasterListCategory = CaseMasterListCategory::where('status', '=', 1)->orderBy('name', 'ASC')->get();

        $caseMasterListField = DB::table('case_masterlist_field')
            ->leftJoin('loan_case_masterlist',  function ($join) use ($id) {
                $join->on('loan_case_masterlist.masterlist_field_id', '=', 'case_masterlist_field.id');
                $join->where('loan_case_masterlist.case_id', '=', $id);
            })
            ->select('case_masterlist_field.*', 'loan_case_masterlist.value')
            ->orderBy('case_masterlist_field.id')
            ->get();

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

        $loan_dispatch = DB::table('loan_case_dispatch')
            ->leftJoin('courier', 'loan_case_dispatch.courier_id', '=', 'courier.id')
            ->select('loan_case_dispatch.*', 'courier.name AS courier_name')
            ->where('case_id', '=', $id)
            ->get();

        // get parameter
        $parameter_controller = new ParameterController;
        $parameters = $parameter_controller->getParameter('payment_type');

        // get client data
        $customer = Customer::where('id', '=', $case->customer_id)->first();
        $referral = Referral::where('id', '=', $case->referral_id)->first();

        // get bank list

        $banks = Banks::where('status', '=', 1)->orderBy('name', 'asc')->get();

        $loan_case_trust_main_receive = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->leftJoin('office_bank_account as o', 'o.id', '=', 'v.office_account_id')
            ->leftJoin('users as u', 'v.user_id', '=', 'u.id')
            ->select('v.*', 'o.name as office_account', 'o.account_no as office_account_no', 'u.name as requestor')
            ->where('v.case_id', '=', $id)
            ->where('v.voucher_type', '=', 3)
            ->where('v.status', '<>', 99)
            ->get();

        $loan_case_trust_main_dis = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->leftJoin('users as u', 'v.user_id', '=', 'u.id')
            ->select('v.*', 'u.name as requestor')
            ->where('v.case_id', '=', $id)
            ->where('v.voucher_type', '=', 2)
            ->where('v.status', '<>', 99)
            ->get();

        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [5, 6])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->whereIn('branch_id', [5, 6])->get();
            } else {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
            }
        } else {

            if ($current_user->branch_id == 3) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', $current_user->branch_id)->get();
            } else if ($current_user->branch_id == 5 || in_array($current_user->id, [51, 127])) {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->where('branch_id', '=', 5)->get();
            } else {
                $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
            }
        }

        $quotation_template = QuotationTemplateMain::where('status', '=', 1)->get();

        $loanCaseBillMain = DB::table('loan_case_bill_main AS m')
            ->leftJoin('users AS u', 'u.id', '=', 'm.created_by')
            ->select('m.*', 'u.name as prepare_by')
            ->where('case_id', '=', $id)
            ->where('m.status', '=',  '1')
            ->get();

        $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $id)->first();

        // Get generated document
        $fileFolder = DocumentTemplateFileFolder::where('status', '=', 1)->get();
        $documentTemplateFilev2 = DB::table('document_template_file_main AS m')
            ->select('m.*')
            ->where('m.status', '=',  '1')
            ->orderBy('m.name', 'ASC')
            ->get();

        // get Markting Notes
        $LoanCaseNotes = DB::table('loan_case_notes AS n')
            ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
            ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
            ->where('n.case_id', '=',  $id)
            ->where('n.status', '<>',  99)
            ->orderBy('n.created_at', 'DESC')
            ->get();

        // Get all notes
        $LoanCaseKIVNotes = DB::table('loan_case_kiv_notes AS n')
            ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
            ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
            ->where('n.case_id', '=',  $id)
            ->where('n.status', '<>',  99)
            ->orderBy('n.created_at', 'DESC')
            ->get();

        // Get PNC Notes
        $LoanCasePNCNotes = DB::table('loan_case_pnc_notes AS n')
            ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
            ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
            ->where('n.case_id', '=',  $id)
            ->where('n.status', '<>',  99)
            ->orderBy('n.created_at', 'DESC')
            ->get();

        $LoanAttachmentMarketing = QueryController::getCaseAttachment($id, 2);
        $LoanAttachment = QueryController::getCaseAttachment($id, 1);

        $referrals = DB::table('referral as r')
            ->leftJoin('banks as b', 'b.id', '=', 'r.bank_id')
            ->select('r.*', 'b.name as bank_name')
            ->where('r.status', '=', 1)
            ->get();

        $property_details = '-';
        $full_loan_details = '-';

        $property_masterlist = DB::table('loan_case_masterlist as m')
            ->where('m.case_id', '=', $id)
            ->where('m.masterlist_field_id', '=', 116)
            ->first();

        $bank_lo_date = DB::table('loan_case_masterlist as m')
            ->where('m.case_id', '=', $id)
            ->where('m.masterlist_field_id', '=', 398)
            ->first();

        if ($property_masterlist) {
            $property_details = $property_masterlist->value;
        }

        $property_masterlist_loan_details = DB::table('loan_case_masterlist as m')
            ->where('m.case_id', '=', $id)
            ->where('m.masterlist_field_id', '=', 141)
            ->first();

        if ($property_masterlist_loan_details) {
            $full_loan_details = $property_masterlist_loan_details->value;
        }

        $parties_list = array();

        if ($customer) {
            array_push($parties_list,  array('party' => 'Client', 'name' => $customer->name));
        }


        $party_masterlist = DB::table('loan_case_masterlist as m')
            ->leftJoin('case_masterlist_field AS f', 'f.id', '=', 'm.masterlist_field_id')
            ->leftJoin('case_masterlist_field_category AS c', 'c.id', '=', 'f.case_field_id')
            ->select('m.*', 'c.name as master_cat_name')
            ->where('m.case_id', '=', $id)
            ->where('f.master_list_type', '=', 'parties_name')
            ->get();


        for ($i = 0; $i < count($party_masterlist); $i++) {
            array_push($parties_list,  array('party' =>  $party_masterlist[$i]->master_cat_name, 'name' => $party_masterlist[$i]->value));
        }

        // get parameter
        $attachment_type = $parameter_controller->getParameter('attachment_type');

        $financed_fee = 0;
        $loan_caseBillMain = DB::table('loan_case_bill_main as m')
            ->where('m.name', '<>', 'Vendor (title & Master Title)')
            ->where('m.case_id', '=', $id)
            ->first();

        if ($loan_caseBillMain) {
            $financed_fee = $loan_caseBillMain->financed_fee;
        }

        $ledgers = DB::table('ledger_entries_v2 as a')
            ->leftJoin('office_bank_account as c', 'c.id', '=', 'a.bank_id')
            ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'a.loan_case_main_bill_id')
            ->leftJoin('loan_case as e', 'e.id', '=', 'a.case_id')
            ->leftJoin('voucher_main as f', 'f.id', '=', 'a.key_id')
            ->select('a.*', 'c.name as bank_name', 'c.account_no as bank_account_no', 'e.case_ref_no', 'e.id as case_id', 'f.voucher_no as voucher_no', 'f.payee as payee_voucher')
            ->where('e.id', '=',  $id)
            ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN', 'ABORTFILE_IN'])
            ->where('a.status', '<>',  99)
            ->orderBy('a.date', 'desc')
            ->orderBy('a.last_row_entry', 'desc')
            ->get();

        $transfer_fee = DB::table('transfer_fee_main as m')
            ->leftJoin('transfer_fee_details as d', 'm.id', '=', 'd.transfer_fee_main_id')
            ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'd.loan_case_main_bill_id')
            ->leftJoin('office_bank_account as o', 'o.id', '=', 'm.transfer_from')
            ->select('m.*', 'd.transfer_amount', 'o.name as bank_name', 'b.pfee1_inv', 'b.pfee2_inv', 'b.sst_inv', 'o.account_no as bank_account_no')
            ->where('b.case_id', '=', $id)
            ->where('m.status', '<>', 99)
            ->get();

        $client_id = '';

        if ($customer->ic_no) {
            $client_id = $customer->ic_no;
        } else {
            $client_id = $customer->company_ref_no;
        }


        // get same case client
        $clientList = explode('&', $client_id);
        $Customer_ic = [];
        $clientList = $clientList[0];
        $clientList = explode(',', $clientList);

        if (count($clientList) > 0) {
            for ($i = 0; $i < count($clientList); $i++) {
                if ($clientList[$i] != '') {
                    $client_ic = trim(str_replace("-", "", $clientList[$i]));

                    if ($client_ic != '') {
                        $Customer = Customer::whereRaw("TRIM(REPLACE(ic_no,'-','')) like ?", '%' . $client_ic . '%')->get();
                        if (count($Customer) > 0) {
                            for ($j = 0; $j < count($Customer); $j++) {
                                array_push($Customer_ic, $Customer[$j]->id);
                            }
                        }


                        $Customer = Customer::whereRaw("TRIM(REPLACE(company_ref_no,'-','')) like ?", '%' . $client_ic . '%')->get();
                        if (count($Customer) > 0) {
                            for ($j = 0; $j < count($Customer); $j++) {
                                array_push($Customer_ic, $Customer[$j]->id);
                            }
                        }
                    }
                }
            }
        }

        if (in_array($current_user->menuroles, ['admin', 'management', 'account'])) {
            // $ClientOtherLoanCase = [];
            $ClientOtherLoanCase = DB::table('loan_case as l')
                ->whereIn('l.customer_id',  $Customer_ic)
                ->where('l.id', '<>', $id)
                ->get();
        } else {
            $ClientOtherLoanCase = DB::table('loan_case as l')
                ->whereIn('l.customer_id',  $Customer_ic)
                ->where('l.id', '<>', $id)
                ->where('l.sales_user_id', '<>', 1)
                ->get();
        }

        $Portfolio = DB::table('portfolio')->where('status', '=', 1)->get();

        $BonusRequestListSent = BonusRequestList::where('case_id', '=', $id)->where('bonus_type', '=', 'CLOSEDCASE')->count();
        $SMPBonusRequestListSent = BonusRequestList::where('case_id', '=', $id)->where('bonus_type', '=', 'SMPSIGNED')->count();

        $closeFileEntry = LedgerEntries::where('case_id', '=', $id)->where('type', 'CLOSEFILEOUT')->first();
        $closeFileEntry = LedgerEntriesV2::where('case_id', '=', $id)->where('type', 'CLOSEFILE_OUT')->first();

        $Branch = Branch::where('id', '=', $case->branch_id)->first();

        $Lawyer_claims = AccessController::UserAccessController(ClaimsController::getAccessCode());

        if ($Lawyer_claims == true) {
            $claims = DB::table('claims_type as a')
                ->leftjoin('claims_request as b', function ($join) use ($id) {
                    $join->on('b.claims_type', '=', 'a.id')
                        ->where('b.case_id', '=', $id);
                })
                ->select('a.*', 'b.status as claims_status', 'b.created_at as submit_date', 'b.amount as amount')
                ->get();
        }

        $CheckListMain = CheckListMain::where('status', 1)->get();
        $CheckListDetails = CheckListDetails::where('status', 1)->get();

        $QuotationGeneratorMain = null;

        if (AccessController::UserAccessPermissionController(PermissionController::QuotationGeneratorPermission()) == false) {
            $QuotationGeneratorMain = QuotationGeneratorMain::where('user_id', $current_user->id)->get();
        } else {
            $QuotationGeneratorMain = QuotationGeneratorMain::where('user_id', $current_user->id)->get();
        }

        $EditClientPermission = AccessController::UserAccessPermissionController(PermissionController::EditClientPermission());

        $loan_case_checklist_main = DB::table('loan_case_checklist_main')
            ->select('loan_case_checklist_main.*')
            ->where('case_id', '=', $id)
            ->get();


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

        return view('legal.case.case-details', [
            'loan_case_checklist_main' => $loan_case_checklist_main,
            'QuotationGeneratorMain' => $QuotationGeneratorMain,
            'EditClientPermission' => $EditClientPermission,
            'CheckListMain' => $CheckListMain,
            'CheckListDetails' => $CheckListDetails,
            'case' => $case,
            'Branch' => $Branch,
            'claims' => $claims,
            'closeFileEntry' => $closeFileEntry,
            'Portfolio' => $Portfolio,
            'referrals' => $referrals,
            'caseTemplate' => $caseTemplate,
            'caseTemplateCategories' => $caseTemplateCategories,
            'current_user' => $current_user,
            'caseMasterListCategory' => $caseMasterListCategory,
            'caseMasterListField' => $caseMasterListField,
            'datediff' => $datediff,
            'bank_lo_date' => $bank_lo_date,
            'couriers' => $couriers,
            'loan_dispatch' => $loan_dispatch,
            'customer' => $customer,
            'referral' => $referral,
            'parameters' => $parameters,
            'banks' => $banks,
            'sales' => $sales,
            'lawyer' => $lawyer,
            'clerk' => $clerk,
            'role' => $role,
            'fileFolder' => $fileFolder,
            'loan_case_trust_main_dis' => $loan_case_trust_main_dis,
            'loan_case_trust_main_receive' => $loan_case_trust_main_receive,
            'loanCaseBillMain' => $loanCaseBillMain,
            'quotation_template' => $quotation_template,
            'account_template_with_cat' => $joinData,
            'OfficeBankAccount' => $OfficeBankAccount,
            'LoanCaseTrustMain' => $LoanCaseTrustMain,
            'documentTemplateFilev2' => $documentTemplateFilev2,
            'LoanCaseNotes' => $LoanCaseNotes,
            'LoanCaseKIVNotes' => $LoanCaseKIVNotes,
            'LoanCasePNCNotes' => $LoanCasePNCNotes,
            'LoanAttachment' => $LoanAttachment,
            'LoanAttachmentMarketing' => $LoanAttachmentMarketing,
            'property_details' => $property_details,
            'full_loan_details' => $full_loan_details,
            'attachment_type' => $attachment_type,
            'parties_list' => $parties_list,
            'financed_fee' => $financed_fee,
            'ledgers' => $ledgers,
            'transfer_fee' => $transfer_fee,
            'BonusRequestListSent' => $BonusRequestListSent,
            'SMPBonusRequestListSent' => $SMPBonusRequestListSent,
            'ClientOtherLoanCase' => $ClientOtherLoanCase,
            'accessSummaryReportReferral' => $accessSummaryReportReferral,
            'Lawyer_claims' => $Lawyer_claims
        ]);
    }


    // public function get

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


                    if ($row->s3_file_name) {

                        $actionBtn = ' <a href="javascript:void(0)" onclick="openFileFromS3(\'' . $row->s3_file_name . '\')" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Download"><i class="cil-cloud-download"></i></a>
                    <a href="javascript:void(0)" onclick="deleteFile(' . $row->id . ')" class="btn btn-danger"><i class="cil-x"></i></a>';
                    } else {
                        $actionBtn = ' <a target="_blank" href="/' . $case_path . 'file_case_' . $id . '/' . $row->name . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Download"><i class="cil-cloud-download"></i></a>
                        <a href="javascript:void(0)" onclick="deleteFile(' . $row->id . ')" class="btn btn-danger"><i class="cil-x"></i></a>';
                    }

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
                ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
                ->select('m.*', 'a.name as account_name', 'm.id as voucher_id')
                ->select('m.*', 'm.id as voucher_id', 'm.total_amount as amount', 'b.name as bank_account', 'b.account_no as bank_account_no')
                ->where('m.case_id', '=', $id)
                // ->where('m.lawyer_approval', '=', 1)
                ->where('m.account_approval', '=', 1)
                ->where('m.status', '<>', 99)
                ->orderBy('m.payment_date', 'ASC')
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
                ->editColumn('bank_account', function ($data) {
                    return $data->bank_account . ' (' . $data->bank_account_no . ')';
                })
                ->editColumn('account_approval', function ($data) {
                    if ($data->account_approval == '0')
                        return '<span class="label bg-warning">Pending</span>';
                    elseif ($data->account_approval == '1')
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

    public function getReferralFile(Request $request, $id)
    {
        if ($request->ajax()) {

            // $parameter = Parameter::where('parameter_type', '=', 'case_file_path')->first();
            // $case_path = $parameter->parameter_value_1;

            $loan_case_files = DB::table('loan_case_files')
                ->select('loan_case_files.*')
                ->where('case_id', '=', $id)
                ->get();

            return DataTables::of($loan_case_files)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($id) {
                    $actionBtn = ' <a target="_blank" href="/file_case_' . $id . '/' . $row->name . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Download"><i class="cil-cloud-download"></i></a>
                    <a href="javascript:void(0)" onclick="deleteFile(' . $row->id . ')" class="btn btn-danger"><i class="cil-x"></i></a>';
                    return $actionBtn;
                })
                ->rawColumns(['action'])
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

        if ($loanCaseFiles->s3_file_name) {

            if (Storage::disk('Wasabi')->exists($loanCaseFiles->s3_file_name)) {
                Storage::disk('Wasabi')->delete($loanCaseFiles->s3_file_name);
            }
        } else {
            if (File::exists(public_path($case_path . 'file_case_' . $case_id . '/' . $file_name))) {
                File::delete(public_path($case_path . 'file_case_' . $case_id . '/' . $file_name));
            }
        }

        $loanCaseFiles->delete();







        return response()->json(['status' => 1, 'message' => 'Deleted the file',]);
    }

    public function deleteMarketingBill($id)
    {
        $LoanAttachmentSearch = LoanAttachment::where('id', '=', $id)->first();

        $date = new DateTime($LoanAttachmentSearch->created_at);
        $diff = (new DateTime)->diff($date)->days;

        if ($diff > 7) {
            return response()->json(['status' => 0, 'data' => 'Notes updated', 'return_id' => $id, 'message' => 'Not allow to delete note that created more than 7 days']);
        }

        $LoanCase = LoanCase::where('id', '=', $LoanAttachmentSearch->case_id)->first();

        if (in_array($LoanCase->status, [0, 4])) {
            return response()->json(['status' => 0, 'data' => 'Notes updated', 'return_id' => $id, 'message' => 'Not allow to delete due to file already closed']);
        }

        $filename = $LoanAttachmentSearch->filename;

        $LoanAttachmentSearch->status = 99;
        $LoanAttachmentSearch->save();

        if ($LoanAttachmentSearch->s3_file_name) {

            if (Storage::disk('Wasabi')->exists($LoanAttachmentSearch->s3_file_name)) {
                Storage::disk('Wasabi')->delete($LoanAttachmentSearch->s3_file_name);
            }
        } else {
            if (File::exists(public_path($filename))) {
                File::delete(public_path($filename));
            }
        }

        $current_user = auth()->user();

        $LegalCloudCaseActivityLog  = new LegalCloudCaseActivityLog();
        $LegalCloudCaseActivityLog->user_id = $current_user->id;
        $LegalCloudCaseActivityLog->case_id = $LoanAttachmentSearch->case_id;
        $LegalCloudCaseActivityLog->action = 'DeleteAttachment';
        $LegalCloudCaseActivityLog->desc = $current_user->name . ' Deleted file (' .  $LoanAttachmentSearch->display_name . ')';
        $LegalCloudCaseActivityLog->status = 1;
        $LegalCloudCaseActivityLog->object_id = $LoanAttachmentSearch->id;
        $LegalCloudCaseActivityLog->created_at = date('Y-m-d H:i:s');
        $LegalCloudCaseActivityLog->save();

        $LoanAttachment = QueryController::getCaseAttachment($LoanAttachmentSearch->case_id, 1);
        $LoanAttachmentMarketing = QueryController::getCaseAttachment($LoanAttachmentSearch->case_id, 2);

        $CheckListMain = CheckListMain::where('status', 1)->get();
        $CheckListDetails = CheckListDetails::where('status', 1)->get();

        return response()->json([
            'status' => 1,
            'message' => 'Deleted the file',
            'LoanAttachment' => view('dashboard.case.table.tbl-case-attachment', compact('LoanAttachment', 'current_user'))->render(),
            'LoanAttachmentMarketing' => view('dashboard.case.table.tbl-case-marketing-attachment', compact('LoanAttachmentMarketing', 'current_user'))->render(),
            'CheckListMain' => view('dashboard.case.tabs.tab-case3', compact('LoanAttachment', 'CheckListMain', 'CheckListDetails', 'current_user'))->render(),
        ]);
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


        // $file = $request->file('inp_file');

        // $downloadOnly = $request->input('downloadOnly');

        // $filename = time() . '_' . $file->getClientOriginalName();


        // $current_user = auth()->user();

        // $parameter = Parameter::where('parameter_type', '=', 'template_file_path')->first();
        // $template_path = $parameter->parameter_value_1;

        // File extension
        // $extension = $file->getClientOriginalExtension();

        // if($request->input('downloadOnly') == 'false')
        // {
        //     if($extension != 'docx')
        //     {
        //         return response()->json(['status' => 0, 'data' => 'Please make sure the file extension is docx']);
        //     }
        // }


        if (count($array) > 0) {
            for ($i = 0; $i < count($array); $i++) {

                $file_folder_name_temp = $case_path . 'file_case_' . $id;
                $file_folder_name_public = public_path($file_folder_name_temp);

                if (!File::isDirectory($file_folder_name_public)) {
                    File::makeDirectory($file_folder_name_public, 0777, true, true);
                }

                $documentTemplateFile = DB::table('document_template_file_main AS m')
                    ->leftJoin('document_template_file_details AS d', 'm.id', '=', 'd.document_template_file_main_id')
                    ->select('m.*', 'd.file_name', 'd.type')
                    ->where('m.id', '=',  $array[$i])
                    ->where('d.status', '=',  '1')
                    ->first();

                if ($documentTemplateFile->type == 'docx') {
                    $genFileName = time() . '_' . str_replace(" ", "_", $documentTemplateFile->name) . '.docx';

                    $template_folder_name_temp = $template_path . 'file_template_' . $array[$i] . '/' . $documentTemplateFile->file_name;
                    $file_folder_name_temp = $case_path . 'file_case_' . $id . '/' . $genFileName;

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

                    // $case = DB::table('loan_case')->select('case_ref_no')->where('id', '=', $id)->first();

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

                    // update lawyer cover letter
                    $lawyers = User::where('is_lawyer', 1)->orderBy('name', 'asc')->get();

                    $parameter = Parameter::where('parameter_type', '=', 'lawyer_ic_short_code')->first();
                    $lawyer_ic_short_code = $parameter->parameter_value_1;

                    for ($j = 0; $j < count($lawyers); $j++) {
                        if ($lawyers[$j]->ic_name == null) {
                            $templateWord->setValue($lawyer_ic_short_code . $lawyers[$j]->id, '-');
                        } else {
                            $templateWord->setValue($lawyer_ic_short_code . $lawyers[$j]->id, htmlspecialchars($lawyers[$j]->ic_name));
                        }
                    }



                    $templateWord->setValue('case_ref_no', htmlspecialchars($case_ref_no));
                    $templateWord->setValue('file_ref', htmlspecialchars($case_ref_no));
                    $templateWord->setValue('bc_no', htmlspecialchars($loanCase->bc_no));
                    $templateWord->setValue('current_date', date('Y-m-d'));

                    if ($lawyer->ic_name != null && $lawyer->ic_name != '') {
                        $templateWord->setValue('lawyer', $lawyer->ic_name);
                    } else {
                        $templateWord->setValue('lawyer', $lawyer->name);
                    }


                    ob_clean();
                    $templateWord->saveAs($file_folder_name_temp);

                    $location = 'cases/' . $id . '/documents';

                    $file = file_get_contents($file_folder_name_temp);

                    // $path = Storage::disk('Wasabi')->put(
                    //     $location . '/' . $genFileName,
                    //     $file
                    // );


                } else {
                    $genFileName = time() . '_' . str_replace(" ", "_", $documentTemplateFile->name) . '.' . $documentTemplateFile->type;
                    $location = 'cases/' . $id . '/documents';

                    try {
                        $file_folder_name_temp = $template_path . 'file_template_' . $documentTemplateFile->folder_id . '/' . $documentTemplateFile->file_name;
                        $file = file_get_contents($file_folder_name_temp);
                    } catch (\Throwable $e) {
                        $file_folder_name_temp = $template_path . 'file_template/' . $documentTemplateFile->file_name;
                        $file = file_get_contents($file_folder_name_temp);
                    }
                }

                $path = Storage::disk('Wasabi')->put(
                    $location . '/' . $genFileName,
                    $file
                );

                if ($path) {
                    if (File::exists(public_path($file_folder_name_temp))) {
                        File::delete(public_path($file_folder_name_temp));
                    }
                }


                $loanCaseFile = new LoanCaseFiles();

                $loanCaseFile->case_id = $id;
                $loanCaseFile->name = $genFileName;
                $loanCaseFile->s3_file_name = $location . '/' . $genFileName;
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

    public function saveDocumentAsVersion($request) {}

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

                        $start = 0;

                        if ($caseTemplateItems[$j]->start <> 0) {
                            $start = $this->getChecklistStartFromDay($caseTemplateItems[$j]->start);
                        }

                        $strDatetime = "+" . intval($caseTemplateItems[$j]->days) . " days";

                        $date = Carbon::createFromFormat('Y-m-d H:i:s', $loanCase->created_at);
                        $daysToAdd = intval($caseTemplateItems[$j]->days + $start);
                        $date = $date->addDays($daysToAdd);



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
                        // $loanCaseChecklistDetails->target_close_date = date('Y-m-d H:i:s', strtotime($strDatetime));
                        $loanCaseChecklistDetails->target_close_date = $date;
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

    public function updateAllcheckListDate()
    {
        // $loanCase = LoanCase::where('template_id', '<>=', 0)->get();
        // for ($i = 0; $i < count($loanCase); $i++) {
        //     $this->updateAceptCaseDate($loanCase[$i]->id);
        // }

        $this->updateAceptCaseDate(28);
    }

    public function updateAceptCaseDate($id)
    {
        $status = 1;
        $message = 'Accepted the case';

        try {

            $loanCase = LoanCase::where('id', '=', $id)->first();

            $LoanCaseChecklistDetails = LoanCaseChecklistDetails::where('case_id', '=', $id)->get();

            if (count($LoanCaseChecklistDetails) > 0) {
                for ($i = 0; $i < count($LoanCaseChecklistDetails); $i++) {


                    $start = 0;
                    if ($LoanCaseChecklistDetails[$i]->start <> 0) {
                        $start = $this->getChecklistStartFromDay($LoanCaseChecklistDetails[$i]->start);
                    }


                    $date = Carbon::createFromFormat('Y-m-d H:i:s', $loanCase->created_at);
                    $daysToAdd = intval($LoanCaseChecklistDetails[$i]->days + $start);
                    $date = $date->addDays($daysToAdd);

                    $LoanCaseChecklistDetails[$i]->target_close_date = $date;
                    $LoanCaseChecklistDetails[$i]->save();
                }
            }
        } catch (\Throwable $e) {
            $status = 3;
            $message = $e;
        }

        // return response()->json(['status' => $status, 'message' => $message]);
    }

    public function getChecklistStartFromDay($id)
    {
        $stop = false;
        $day = 0;
        $searchId = $id;
        $count = 10;

        while ($stop == false) {
            $caseTemplateItems = CaseTemplateItems::where('id', '=', $searchId)->first();

            if ($caseTemplateItems) {
                if ($caseTemplateItems->start == 0) {
                    $stop = true;
                } else {

                    $searchId = $caseTemplateItems->start;
                    $day += $caseTemplateItems->days;
                }
            } else {
                $stop = true;
            }
            $count -= 1;

            if ($count == 0) {
                break;
            }
        }

        return $day;
    }


    public function acceptCaseBak(Request $request, $id)
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

        $case_id = 0;

        $loanCaseDetails = LoanCaseChecklistDetails::where('id', '=', $request->input('selected_id'))->first();
        $case_id = $loanCaseDetails->case_id;

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

        $loan_case_checklist_main = DB::table('loan_case_checklist_main')
            ->select('loan_case_checklist_main.*')
            ->where('case_id', '=', $case_id)
            ->get();

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

        $cases = LoanCase::where('id', '=', $case_id)->get();



        return response()->json([
            'status' => $status,
            'data' => $update_date,

            'view' => view('dashboard.case.tabs.tab-case', compact('loan_case_checklist_main', 'cases'))->render()
        ]);
    }

    public function updateCheckListBulk(Request $request, $id)
    {
        $status = 1;
        $type = 0;
        $checklist_id = [];

        // $remark = $request->input('remarks');
        // $check_list_status = $request->input('check_list_status');
        // $loanCaseDetails = LoanCaseDetails::where('id', '=', $request->input('case_id_action'))->first();
        $current_user = auth()->user();

        if ($request->input('checklist_id') != null) {
            $checklist_id = json_decode($request->input('checklist_id'), true);
        }

        if (count($checklist_id) > 0) {

            for ($i = 0; $i < count($checklist_id); $i++) {
                $loanCaseDetails = LoanCaseChecklistDetails::where('id', '=', $checklist_id[$i]['itemID'])->first();



                if ($loanCaseDetails) {
                    $loanCaseDetails->remark = $checklist_id[$i]['remarks'];
                    // $loanCaseDetails->updated_by = $current_user->id;
                    $loanCaseDetails->updated_at = date('Y-m-d H:i:s');


                    if ($checklist_id[$i]['notApplicable'] == 0) {

                        if ($loanCaseDetails->status == 1) {
                            if ($checklist_id[$i]['status'] == 0 || $checklist_id[$i]['status'] == 99) {
                                $type = 2;
                                $loanCaseDetails->status = $checklist_id[$i]['status'];
                            }
                        } else {
                            if ($checklist_id[$i]['status'] == 1) {
                                $type = 1;
                                $loanCaseDetails->complete_date = date('Y-m-d H:i:s');
                                $loanCaseDetails->status = 1;
                            }
                        }
                    } else {

                        if ($loanCaseDetails->status == 1) {
                            $type = 2;
                        } else {
                            $type = 0;
                        }

                        $loanCaseDetails->status = 99;
                    }

                    $loanCaseDetails->save();

                    // $activityLog = [];

                    // $activityLog['action'] = 'update';
                    // $activityLog['case_id'] = $id;
                    // $activityLog['checklist_id'] = $checklist_id[$i]['itemID'];
                    // $activityLog['desc'] = $loanCaseDetails->name;

                    // $activity_controller = new ActivityLogController;
                    // $activity_controller->storeActivityLog($activityLog);




                    if ($type != 0) {
                        $this->updateKPIScore($loanCaseDetails, $type, $id);
                    }
                }
            }

            if ($this->checkStep($loanCaseDetails->loan_case_main_id) == 0) {
                $loanCaseMain = LoanCaseChecklistMain::where('id', '=', $loanCaseDetails->loan_case_main_id)->first();
                $loanCaseMain->status = 1;

                $loanCaseMain->save();
            }

            $this->updatePercentage($id);
        }


        $loan_case_checklist_main = DB::table('loan_case_checklist_main')
            ->select('loan_case_checklist_main.*')
            ->where('case_id', '=', $id)
            ->get();

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

        $cases = LoanCase::where('id', '=', $id)->get();



        return response()->json([
            'status' => $status,
            'data' => 'teste',

            'view' => view('dashboard.case.tabs.tab-case', compact('loan_case_checklist_main', 'cases'))->render()
        ]);
    }

    public function updateCheckListBulkV2(Request $request, $id)
    {
        $status = 1;
        $type = 0;
        $checklist_id = [];

        $LoanCase = LoanCase::where('id', '=', $id)->first();
        $caseType = Portfolio::where('id', $LoanCase->bank_id)->pluck('category')->first();

        $checklistCat = 0;

        if ($caseType == 1) {
            $checklistCat = 2;
        } else if (in_array($caseType, [2, 3])) {
            $checklistCat = 3;
        }

        $current_user = auth()->user();

        if ($request->input('checklist_id') != null) {
            $checklist_id = json_decode($request->input('checklist_id'), true);
        }

        if (count($checklist_id) > 0) {

            $LoanCaseChecklistMainV2 = LoanCaseChecklistMainV2::where('case_id', '=', $id)->get();

            //Create main check list is not exist
            if (count($LoanCaseChecklistMainV2) <= 0) {
                $CheckListTemplateMainV2 = CheckListTemplateMainV2::where('status', '<>', 99)->whereIn('checklist_type', [1, $checklistCat])->get();

                if (count($CheckListTemplateMainV2) > 0) {
                    for ($i = 0; $i < count($CheckListTemplateMainV2); $i++) {
                        $LoanCaseChecklistMainV2 = new LoanCaseChecklistMainV2();

                        $LoanCaseChecklistMainV2->case_id = $id;
                        $LoanCaseChecklistMainV2->checklist_main_id = $CheckListTemplateMainV2[$i]->id;
                        $LoanCaseChecklistMainV2->name = $CheckListTemplateMainV2[$i]->name;
                        $LoanCaseChecklistMainV2->status = 0;

                        $LoanCaseChecklistMainV2->save();
                    }
                }
            }

            for ($i = 0; $i < count($checklist_id); $i++) {

                $LoanCaseChecklistDetailsV2 = LoanCaseChecklistDetailsV2::where('id', $checklist_id[$i]['checklist_details_id'])->first();

                if (!$LoanCaseChecklistDetailsV2) {
                    $LoanCaseChecklistDetailsV2 = new LoanCaseChecklistDetailsV2();
                }

                $CheckListTemplateDetailsV2 = CheckListTemplateDetailsV2::where('id', $checklist_id[$i]['itemID'])->first();

                $LoanCaseChecklistDetailsV2->checklist_main_id = $CheckListTemplateDetailsV2->checklist_main_id;
                $LoanCaseChecklistDetailsV2->checklist_item_id = $CheckListTemplateDetailsV2->id;
                $LoanCaseChecklistDetailsV2->name = $CheckListTemplateDetailsV2->name;
                $LoanCaseChecklistDetailsV2->case_id = $id;
                $LoanCaseChecklistDetailsV2->order = $CheckListTemplateDetailsV2->order;
                $LoanCaseChecklistDetailsV2->status = $checklist_id[$i]['status'];

                if (in_array($checklist_id[$i]['status'], [0, 99])) {
                    $LoanCaseChecklistDetailsV2->complete_date = null;
                } else {
                    $LoanCaseChecklistDetailsV2->complete_date = date('Y-m-d H:i:s');;
                }

                $LoanCaseChecklistDetailsV2->save();
            }

            $this->checkStepV2($id);

            $cases = LoanCase::where('id', '=', $id)->get();


            $CheckListTemplateMainV2 = DB::table('checklist_template_main_v2 as a')
                ->leftJoin('loan_case_checklist_main_v2 as b', function ($join) use ($id) {
                    $join->on('b.checklist_main_id', '=', 'a.id');
                    $join->where('b.case_id', '=', $id);
                })
                ->select('a.*', 'b.status as MainCheckStatus')
                ->where('a.status', '<>', 99)
                ->whereIn('a.checklist_type', [1, $checklistCat])
                ->get();

            // get latest checklist
            for ($i = 0; $i < count($CheckListTemplateMainV2); $i++) {

                $CheckListTemplateDetailsV2 = DB::table('checklist_template_details_v2 as a')
                    ->leftJoin('loan_case_checklist_details_v2 as b', function ($join) use ($id, $CheckListTemplateMainV2, $i) {
                        $join->on('b.checklist_item_id', '=', 'a.id');
                        $join->where('b.case_id', '=', $id);
                    })
                    ->select('a.*', 'b.status as subCheckStatus', 'b.id as checklist_details_id')
                    ->where('a.status', '<>', 99)
                    ->where('a.checklist_main_id', '=', $CheckListTemplateMainV2[$i]->id)
                    ->get();

                $CheckListTemplateMainV2[$i]->details = $CheckListTemplateDetailsV2;
            }
        }

        return response()->json([
            'status' => $status,
            'data' => 'teste',
            'view' => view('dashboard.case.tabs.tab-checklist', compact('CheckListTemplateMainV2', 'CheckListTemplateDetailsV2'))->render()
        ]);
    }

    public function checkStepV2($case_id)
    {
        $LoanCaseChecklistMainV2 = LoanCaseChecklistMainV2::where('case_id', '=', $case_id)->where('status', '=', 0)->get();

        if (count($LoanCaseChecklistMainV2) > 0) {
            for ($i = 0; $i < count($LoanCaseChecklistMainV2); $i++) {

                $detailCount = DB::table('checklist_template_details_v2 as a')
                    ->leftJoin('loan_case_checklist_details_v2 as b', function ($join) use ($case_id, $LoanCaseChecklistMainV2, $i) {
                        $join->on('b.checklist_item_id', '=', 'a.id');
                        $join->where('b.case_id', '=', $case_id);;
                    })
                    ->select('a.*', 'b.status as subCheckStatus', 'b.id as checklist_details_id')
                    ->where('a.status', '<>', 99)
                    ->where('b.status', 0)->orWhereNull('b.status')
                    ->where('a.checklist_main_id', '=', $LoanCaseChecklistMainV2[$i]->id)
                    ->count();

                if ($detailCount == 0) {
                    $LoanCaseChecklistMainV2[$i]->status = 1;
                    $LoanCaseChecklistMainV2[$i]->save();
                }
            }
        }
    }

    public function updatePercentage($case_id)
    {
        $loanCaseDetailsAll = LoanCaseChecklistDetails::where('case_id', '=', $case_id)->get();
        $loanCaseDetailsDone = LoanCaseChecklistDetails::where('case_id', '=', $case_id)->whereIn('status', [1, 99])->get();

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


        // if ($date_now >= date('2022-02-17 20:56:13')) {
        //     if ($date_now > $loanCaseDetails->target_close_date) {
        //         $blnOver = 1;
        //     } else {
        //         $blnOver = 0;
        //     }
        // }

        if ($date_now > $loanCaseDetails->target_close_date) {
            $blnOver = 1;
        } else {
            $blnOver = 0;
        }

        $current_user = auth()->user();
        $user = User::where('id', '=', $current_user->id)->first();


        if ($type == 1) {

            if ($blnOver == 1) {
                $points = 0 - $points;
                $user->kpi_miss = (int)$user->kpi_miss + $points;
            } else {
                $user->kpi_get = (int)$user->kpi_get + $points;
            }
        } else if ($type == 2) {
            // $points = 0 - $points;
            $user->kpi_get = (int)$user->kpi_get - $points;
        }


        $userKpiHistory = new UserKpiHistory();


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

        $isImage =  ImageController::verifyImage($file);

        // File extension 
        $extension = $file->getClientOriginalExtension();
        $case_ref_no =  $request->input('case_ref_no');
        $file_type =  $request->input('file_type');

        // File upload location
        $case_ref_no = str_replace("/", "_", $case_ref_no);
        if ($file_type == 1) {
            // $location = 'documents/cases/' . $case_ref_no . '/';
            // $location = 'documents/cases/' . $request->input('case_id') . '/';
            $location = 'cases/' . $request->input('case_id') . '';
        } else {
            // $location = 'documents/cases/' . $case_ref_no . '/marketing/';
            // $location = 'documents/cases/' . $request->input('case_id') . '/marketing/';
            $location = 'cases/' . $request->input('case_id') . '/marketing';
        }

        $disk = Storage::disk('Wasabi');
        $s3_file_name = '';

        if ($isImage == true) {
            $s3_file_name = ImageController::resizeImg($file, $location, $filename);
        } else {
            // $file->move($location, $filename);
            // $filepath = url($location . $filename);

            $s3_file_name =  $disk->put($location, $file);
        }

        $LoanAttachment = new LoanAttachment();

        $LoanAttachment->case_id =  $request->input('case_id');
        $LoanAttachment->checklist_id = $request->input('selected_id');
        $LoanAttachment->display_name = $file->getClientOriginalName();
        $LoanAttachment->filename = $s3_file_name;
        $LoanAttachment->s3_file_name = $s3_file_name;
        $LoanAttachment->type = $extension;
        $LoanAttachment->file_type = $file_type;
        $LoanAttachment->attachment_type = $request->input('attachment_type');
        $LoanAttachment->remark = $request->input('remark');
        $LoanAttachment->user_id = $current_user->id;
        $LoanAttachment->status = 1;
        $LoanAttachment->created_at = date('Y-m-d H:i:s');
        $LoanAttachment->save();

        $LegalCloudCaseActivityLog  = new LegalCloudCaseActivityLog();
        $LegalCloudCaseActivityLog->user_id = $current_user->id;
        $LegalCloudCaseActivityLog->case_id = $request->input('case_id');
        $LegalCloudCaseActivityLog->action = 'UploadAttachment';
        $LegalCloudCaseActivityLog->desc = $current_user->name . ' uploaded file (' .  $LoanAttachment->display_name . ')';
        $LegalCloudCaseActivityLog->status = 1;
        $LegalCloudCaseActivityLog->object_id = $LoanAttachment->id;
        $LegalCloudCaseActivityLog->created_at = date('Y-m-d H:i:s');
        $LegalCloudCaseActivityLog->save();

        $LoanAttachment = QueryController::getCaseAttachment($request->input('case_id'), 1);
        $LoanAttachmentMarketing = QueryController::getCaseAttachment($request->input('case_id'), 2);

        return response()->json([
            'status' => $status,
            'data' => $data,
            'LoanAttachment' => view('dashboard.case.table.tbl-case-attachment', compact('LoanAttachment', 'current_user'))->render(),
            'LoanAttachmentMarketing' => view('dashboard.case.table.tbl-case-marketing-attachment', compact('LoanAttachmentMarketing', 'current_user'))->render(),
        ]);
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


    public function requestTrustDisbusement(Request $request, $id)
    {
        $status = 1;
        $data = '';
        $total_trust_receive = 0;
        $total_trust_disburse = 0;

        $office_account_id = 0;
        $trx_id = '';

        $loanCase = LoanCase::where('id', '=', $id)->first();

        $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $id)->first();

        if ($request->input('office_account_id') != 'undefined') {
            $office_account_id = $request->input('office_account_id');
        }

        if ($request->input('transaction_id') != 'undefined') {
            $trx_id = $request->input('transaction_id');
        }

        if ($LoanCaseTrustMain->total_received <= 0) {
            return response()->json(['status' => 2, 'message' => 'No trust fund received yet']);
        }

        $total_trust_receive = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->select('v.*')
            ->where('v.case_id', '=', $id)
            ->where('v.voucher_type', '=', 3)
            ->where('v.status', '<>', 99)
            ->sum('total_amount');

        $total_trust_disburse = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->select('v.*')
            ->where('v.case_id', '=', $id)
            ->where('v.voucher_type', '=', 2)
            // ->where('v.account_approval', '=', 1)
            ->whereNotIn('v.account_approval', [2])
            ->where('v.status', '<>', 99)
            ->sum('total_amount');

        // return $total_trust_disburse;

        $remaining_amt = $total_trust_receive - $total_trust_disburse;
        $amt = (float)$request->input('amount');

        $result =  bcsub($remaining_amt, $amt, 2);

        if ($result < 0) {
            return response()->json(['status' => 2, 'message' => 'No enough trust fund']);
        }

        // return 2222;

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
        // $loanCaseTrust->transaction_id =  $trx_id;
        $loanCaseTrust->amount =  $request->input('amount');
        $loanCaseTrust->office_account_id =  $office_account_id;
        $loanCaseTrust->remark =  $request->input('payment_desc');
        $loanCaseTrust->status =  1;
        $loanCaseTrust->created_at = date('Y-m-d H:i:s');
        $loanCaseTrust->created_by = $current_user->id;
        $loanCaseTrust->save();

        $collected_trust  = 0;

        $collected_trust = (float)($LoanCaseTrustMain->total_received) - (float)($request->input('amount'));
        // $total_trust = (float)($loanCase->total_trust) - (float)($request->input('amount'));

        $loanCase->collected_trust = $collected_trust;

        // $loanCase->total_trust = $collected_trust;
        $loanCase->updated_at = date('Y-m-d H:i:s');
        $loanCase->save();


        // $this->updateLoanCaseTrustMain($request, $id);

        // check loantrustcasemain exist
        $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $id)->first();

        if ($LoanCaseTrustMain == null) {
            $LoanCaseTrustMain = new LoanCaseTrustMain();
            $LoanCaseTrustMain->case_id =  $id;
            $LoanCaseTrustMain->payment_type =  $request->input('payment_type');
            $LoanCaseTrustMain->payment_date =  $request->input('payment_date');
            $LoanCaseTrustMain->transaction_id =  $trx_id;
            $LoanCaseTrustMain->office_account_id =  $office_account_id;
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
        $voucherMain->remark = $request->input('payment_desc');
        $voucherMain->created_by = $current_user->id;
        $voucherMain->transaction_id =  $trx_id;
        $voucherMain->bank_account = $request->input('bank_account');

        $voucherMain->office_account_id = $office_account_id;

        $voucherMain->payment_date = $request->input('payment_date');
        $voucherMain->adjudication_no = $request->input('adjudication_no');
        $voucherMain->email = $request->input('email');
        $voucherMain->total_amount = $request->input('amount');
        $voucherMain->voucher_type = 2;
        $voucherMain->item_code = $loanCaseTrust->id;

        if ($current_user->menuroles == 'lawyer' || $current_user->menuroles == 'account') {
            $voucherMain->lawyer_approval = 1;
            $voucherMain->lawyer_id = $current_user->id;
            $voucherMain->lawyer_approval_date = date('Y-m-d H:i:s');
        }

        // if($current_user->menuroles == 'acccount')
        // {
        //     $voucherMain->accojunt_approval = 1;
        // }

        $voucherMain->status = 1;
        $voucherMain->created_at = date('Y-m-d H:i:s');
        $voucherMain->save();

        //upload file
        $file = $request->file('trust_attachment_file');

        if ($file != null) {
            $filename = time() . '_' . $file->getClientOriginalName();

            $current_user = auth()->user();

            // File extension
            $extension = $file->getClientOriginalExtension();
            $case_id =  $id;
            $remarks =  $request->input('remark');

            // File upload location
            $location = 'documents/cases/' . $id . '/voucher/';
            $location = 'cases/' . $id . '/voucher';

            $res = str_replace(array('\'', '"', ',', ';', '<', '>', ' ', '&'), '_', $file->getClientOriginalName());

            $filename = time() . '_' . $res;
            $current_user = auth()->user();

            $isImage =  ImageController::verifyImage($file);

            $disk = Storage::disk('Wasabi');
            $s3_file_name = '';

            if ($isImage == true) {
                $s3_file_name = ImageController::resizeImg($file, $location, $filename);
            } else {
                $s3_file_name =  $disk->put($location, $file);
            }

            $LoanCaseAccountFiles = new LoanCaseAccountFiles();

            $LoanCaseAccountFiles->main_id =  $voucherMain->id;
            $LoanCaseAccountFiles->case_id =  $case_id;
            $LoanCaseAccountFiles->file_name = $s3_file_name;
            $LoanCaseAccountFiles->ori_name = $file->getClientOriginalName();
            $LoanCaseAccountFiles->s3_file_name = $s3_file_name;
            $LoanCaseAccountFiles->type = $extension;
            $LoanCaseAccountFiles->remarks = $remarks;
            $LoanCaseAccountFiles->status = 1;
            $LoanCaseAccountFiles->created_by = $current_user->id;
            $LoanCaseAccountFiles->created_at = date('Y-m-d H:i:s');
            $LoanCaseAccountFiles->save();
        }

        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;

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
            $Notification->module = 'voucher|trust';
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
            $Notification->module = 'voucher|trust';
            $Notification->bln_read = 0;
            $Notification->status = 1;
            $Notification->created_at = now();
            $Notification->created_by = $current_user->id;
            $Notification->save();
        }

        VoucherController::reverseTrustDisburse($id);


        return response()->json(['status' => $status, 'data' => $data]);
    }

    public function receiveTrustDisbusement(Request $request, $id)
    {
        $status = 1;
        $data = '';

        $current_user = auth()->user();

        $loanCase = LoanCase::where('id', '=', $id)->first();

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
            $LoanCaseTrustMain->save();
        }

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
        $voucherMain->case_bill_main_id =  $request->input('bill_id');
        $voucherMain->payment_status =  $request->input('payment_status');
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

        // $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $id)->first();

        // $loan_case_trust_main_receive = DB::table('voucher_main as v')
        //     ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
        //     ->select('v.*')
        //     ->where('v.case_id', '=', $id)
        //     ->where('v.voucher_type', '=', 3)
        //     ->where('v.status', '<>', 99)
        //     ->get();

        // $total_sum = 0;

        // if (count($loan_case_trust_main_receive) > 0) {
        //     for ($i = 0; $i < count($loan_case_trust_main_receive); $i++) {
        //         $total_sum += $loan_case_trust_main_receive[$i]->total_amount;
        //     }
        // }

        // $LoanCaseTrustMain->total_received = $total_sum;
        // $LoanCaseTrustMain->updated_at = date('Y-m-d H:i:s');
        // $LoanCaseTrustMain->save();

        VoucherController::reverseTrustDisburse($voucherMain->id);

        $file = $request->file('trust_attachment_file');

        if ($file != null) {
            $filename = time() . '_' . $file->getClientOriginalName();

            $current_user = auth()->user();

            // File extension
            $extension = $file->getClientOriginalExtension();
            $case_id =  $id;
            $remarks =  $request->input('payment_desc');

            // File upload location
            $location = 'documents/cases/' . $id . '/voucher/';
            $location = 'cases/' . $id . '/voucher';

            $res = str_replace(array('\'', '"', ',', ';', '<', '>', ' ', '&'), '_', $file->getClientOriginalName());

            $filename = time() . '_' . $res;
            $current_user = auth()->user();

            $isImage =  ImageController::verifyImage($file);

            $disk = Storage::disk('Wasabi');
            $s3_file_name = '';

            if ($isImage == true) {
                $s3_file_name = ImageController::resizeImg($file, $location, $filename);
            } else {
                $s3_file_name =  $disk->put($location, $file);
            }

            $LoanCaseAccountFiles = new LoanCaseAccountFiles();

            $LoanCaseAccountFiles->main_id =  $voucherMain->id;
            $LoanCaseAccountFiles->case_id =  $case_id;
            $LoanCaseAccountFiles->file_name = $s3_file_name;
            $LoanCaseAccountFiles->ori_name = $file->getClientOriginalName();
            $LoanCaseAccountFiles->s3_file_name = $s3_file_name;
            $LoanCaseAccountFiles->type = $extension;
            $LoanCaseAccountFiles->remarks = $remarks;
            $LoanCaseAccountFiles->status = 1;
            $LoanCaseAccountFiles->created_by = $current_user->id;
            $LoanCaseAccountFiles->created_at = date('Y-m-d H:i:s');
            $LoanCaseAccountFiles->save();
        }

        $LedgerEntries = new LedgerEntries();

        $transaction_id = '';

        if ($voucherMain->transaction_id != null) {
            $transaction_id = $voucherMain->transaction_id;
        }

        $LedgerEntries->transaction_id = $transaction_id;
        $LedgerEntries->case_id = $id;
        $LedgerEntries->loan_case_main_bill_id = $voucherMain->case_bill_main_id;
        $LedgerEntries->user_id = $current_user->id;
        $LedgerEntries->key_id = $voucherMain->id;
        $LedgerEntries->transaction_type = 'D';
        $LedgerEntries->amount = $voucherMain->total_amount;
        $LedgerEntries->bank_id = $voucherMain->office_account_id;
        $LedgerEntries->remark = $voucherMain->remark;
        $LedgerEntries->status = 1;
        $LedgerEntries->created_at = date('Y-m-d H:i:s');
        $LedgerEntries->date = $voucherMain->payment_date;
        $LedgerEntries->type = 'TRUSTRECEIVE';
        $LedgerEntries->save();

        $LedgerEntries = new LedgerEntriesV2();

        $LedgerEntries->transaction_id = $transaction_id;
        $LedgerEntries->case_id = $id;
        $LedgerEntries->loan_case_main_bill_id = 0;
        $LedgerEntries->user_id = $current_user->id;
        $LedgerEntries->cheque_no = $voucherMain->voucher_no;
        $LedgerEntries->key_id = $voucherMain->id;
        $LedgerEntries->transaction_type = 'D';
        $LedgerEntries->amount = $voucherMain->total_amount;
        $LedgerEntries->bank_id = $voucherMain->office_account_id;
        $LedgerEntries->remark = $voucherMain->remark;
        $LedgerEntries->payee = $voucherMain->payee;
        $LedgerEntries->is_recon = 0;
        $LedgerEntries->status = 1;
        $LedgerEntries->created_at = date('Y-m-d H:i:s');
        $LedgerEntries->date = $voucherMain->payment_date;
        $LedgerEntries->type = 'TRUST_RECV';
        $LedgerEntries->save();

        $LoanCase = LoanCase::where('id', $id)->first();
        CaseController::adminUpdateClientLedger($LoanCase);

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


    public function setKIV(Request $request, $id)
    {
        $status = 1;
        $data = '';

        $loanCase = LoanCase::where('id', '=', $id)->first();

        $loanCase->status = 3; //kiv
        $loanCase->kiv_remark = $request->input('reason');
        $loanCase->save();

        if ($request->input('reason') != null) {
            $LoanCaseKivNotes = new LoanCaseKivNotes();

            $current_user = auth()->user();
            $userRoles = $current_user->menuroles;

            $LoanCaseKivNotes->case_id =  $loanCase->id;
            $LoanCaseKivNotes->notes =  '<b>KIV</b><br/>' . $request->input('reason');
            $LoanCaseKivNotes->label =  'setkiv';
            $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
            $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');
            $LoanCaseKivNotes->status =  1;
            $LoanCaseKivNotes->created_by = $current_user->id;
            $LoanCaseKivNotes->save();
        }

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
                    if ($value != '') {
                        $loanCaseMasterList->case_id = $id;
                        $loanCaseMasterList->masterlist_field_id = $key;
                        $loanCaseMasterList->value = $value;
                        $loanCaseMasterList->updated_at = date('Y-m-d H:i:s');
                        $loanCaseMasterList->save();
                    } else {
                        $loanCaseMasterList->delete();
                    }
                } else {
                    $loanCaseMasterList = new LoanCaseMasterList();

                    if ($value != '') {
                        $loanCaseMasterList->case_id = $id;
                        $loanCaseMasterList->masterlist_field_id = $key;
                        $loanCaseMasterList->value = $value;
                        $loanCaseMasterList->created_at = date('Y-m-d H:i:s');
                        $loanCaseMasterList->save();
                    }
                }

                if ($key == 141) {
                    $loanCase->loan_sum = $value;
                    $loanCase->save();
                }

                if ($key == 129) {
                    $loanCase->purchase_price = $value;
                    $loanCase->save();
                }

                if ($key == 147) {
                    $loanCase->spa_date = $value;
                    $loanCase->save();
                }

                if ($key == 148) {
                    $loanCase->completion_date = $value;
                    $loanCase->save();
                }
            }
        } catch (\Throwable $e) {
            $status = 0;
            $message = $e;
        }

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

        $cases = LoanCase::where('id', '=', $id)->get();
        $case = LoanCase::where('id', '=', $id)->first();

        $parties_list = array();

        $customer = Customer::where('id', '=', $cases[0]->customer_id)->first();
        if ($customer) {
            array_push($parties_list,  array('party' => 'Client', 'name' => $customer->name));
        }


        $party_masterlist = DB::table('loan_case_masterlist as m')
            ->leftJoin('case_masterlist_field AS f', 'f.id', '=', 'm.masterlist_field_id')
            ->leftJoin('case_masterlist_field_category AS c', 'c.id', '=', 'f.case_field_id')
            ->select('m.*', 'c.name as master_cat_name')
            ->where('m.case_id', '=', $id)
            ->where('f.master_list_type', '=', 'parties_name')
            ->get();


        for ($i = 0; $i < count($party_masterlist); $i++) {
            array_push($parties_list,  array('party' =>  $party_masterlist[$i]->master_cat_name, 'name' => $party_masterlist[$i]->value));
        }

        return response()->json([
            'status' => $status,
            'data' => $message,
            'parties_list' => $parties_list,
            'cases' => $cases,
            'view' => view('dashboard.case.tabs.tab-master-list', compact('caseMasterListField', 'caseMasterListCategory', 'case'))->render(),
            'summary' => $this->viewCaseSummary($id)
        ]);
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
            $voucherMain->case_bill_main_id = $request->input('bill_main_id');
            $voucherMain->payee = $request->input('payee');
            $voucherMain->remark = $request->input('remark');
            $voucherMain->email = $request->input('email');
            $voucherMain->created_by = $current_user->id;
            $voucherMain->bank_account = $request->input('bank_account');
            $voucherMain->adjudication_no = $request->input('adjudication_no');
            $voucherMain->lawyer_approval = 1;
            $voucherMain->lawyer_approval_date = date('Y-m-d H:i:s');

            $voucherMain->payment_date = $request->input('payment_date');
            $voucherMain->total_amount = 0;
            $voucherMain->status = 1;
            $voucherMain->created_at = date('Y-m-d H:i:s');
            $voucherMain->save();

            if ($voucherMain) {
                $status = 1;
                $data = '';

                $file = $request->file('voucher_file');

                if ($file != null) {
                    $filename = time() . '_' . $file->getClientOriginalName();

                    $current_user = auth()->user();

                    // File extension
                    $extension = $file->getClientOriginalExtension();
                    $case_id =  $id;
                    $remarks =  $request->input('remark');

                    // File upload location
                    $case_ref_no = str_replace("/", "_", $loanCase->case_ref_no);
                    // $location = 'documents/cases/' . $case_ref_no . '/voucher/';
                    $location = 'cases/' . $id . '/voucher';

                    $res = str_replace(array('\'', '"', ',', ';', '<', '>', ' ', '&'), '_', $file->getClientOriginalName());

                    $filename = time() . '_' . $res;
                    $current_user = auth()->user();

                    $isImage =  ImageController::verifyImage($file);

                    $disk = Storage::disk('Wasabi');
                    $s3_file_name = '';


                    if ($isImage == true) {
                        $s3_file_name = ImageController::resizeImg($file, $location, $filename);
                    } else {
                        // $file->move($location, $filename);
                        // $filepath = url($location . $filename);

                        $s3_file_name =  $disk->put($location, $file);
                    }

                    // Upload file
                    // $file->move($location, $filename); 

                    // File path
                    // $filepath = url($location . $filename);

                    $LoanCaseAccountFiles = new LoanCaseAccountFiles();

                    $LoanCaseAccountFiles->main_id =  $voucherMain->id;
                    $LoanCaseAccountFiles->case_id =  $case_id;
                    $LoanCaseAccountFiles->file_name = $s3_file_name;
                    $LoanCaseAccountFiles->ori_name = $file->getClientOriginalName();
                    $LoanCaseAccountFiles->s3_file_name = $s3_file_name;
                    $LoanCaseAccountFiles->type = $extension;
                    $LoanCaseAccountFiles->remarks = $remarks;
                    $LoanCaseAccountFiles->status = 1;
                    $LoanCaseAccountFiles->created_by = $current_user->id;
                    $LoanCaseAccountFiles->created_at = date('Y-m-d H:i:s');
                    $LoanCaseAccountFiles->save();
                }
            }



            // temporary auto approve voucher if lawyer sent
            $Notification  = new Notification();
            $Notification->name = $current_user->name;
            $Notification->desc = 'request voucher ' . $voucher_running_no;
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
                    $loanCaseAccount->amount = $loanCaseAccount->amount - $voucherList[$i]['amount'];
                    $loanCaseAccount->save();

                    $this->updateBillCaseBillDisb($id, $request->input('bill_main_id'));
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

    public function requestVoucherV2(Request $request, $id)
    {
        $status = 1;
        $need_approval = 0;
        $totalAmount = 0;
        $message = 'Voucher requested';
        $voucherList = [];
        $current_user = auth()->user();

        if ($request->input('voucher_list') != null) {
            $voucherList = json_decode($request->input('voucher_list'), true);
        }

        if (count($voucherList) > 0) {

            $Parameter = Parameter::where('parameter_type', '=', 'voucher_running_no')->first();
            $voucher_running_no = (int)$Parameter->parameter_value_1;

            $Parameter->parameter_value_1 = (int)$Parameter->parameter_value_1 + 1;
            $Parameter->save();

            $current_user = auth()->user();
            $loanCase = LoanCase::where('id', '=', $id)->first();

            $voucherMain = new VoucherMain();

            $voucherMain->user_id = $current_user->id;
            $voucherMain->case_id = $id;
            $voucherMain->payment_type = $request->input('payment_type');
            $voucherMain->voucher_no = $voucher_running_no;
            $voucherMain->cheque_no = $request->input('cheque_no');
            $voucherMain->credit_card_no = $request->input('credit_card_no');
            $voucherMain->bank_id = $request->input('bank_id');
            $voucherMain->case_bill_main_id = $request->input('bill_main_id');
            $voucherMain->payee = $request->input('payee');
            $voucherMain->remark = $request->input('remark');
            $voucherMain->email = $request->input('email');
            $voucherMain->created_by = $current_user->id;
            $voucherMain->bank_account = $request->input('bank_account');
            $voucherMain->adjudication_no = $request->input('adjudication_no');
            $voucherMain->lawyer_approval = 1;
            $voucherMain->lawyer_approval_date = date('Y-m-d H:i:s');

            $voucherMain->payment_date = $request->input('payment_date');
            $voucherMain->total_amount = 0;
            $voucherMain->status = 1;
            $voucherMain->created_at = date('Y-m-d H:i:s');
            $voucherMain->save();

            if ($voucherMain) {
                $status = 1;
                $data = '';

                $file = $request->file('voucher_file');

                if ($file != null) {
                    $filename = time() . '_' . $file->getClientOriginalName();

                    $current_user = auth()->user();

                    // File extension
                    $extension = $file->getClientOriginalExtension();
                    $case_id =  $id;
                    $remarks =  $request->input('remark');

                    // File upload location
                    $case_ref_no = str_replace("/", "_", $loanCase->case_ref_no);
                    // $location = 'documents/cases/' . $case_ref_no . '/voucher/';
                    $location = 'cases/' . $id . '/voucher';

                    $res = str_replace(array('\'', '"', ',', ';', '<', '>', ' ', '&'), '_', $file->getClientOriginalName());

                    $filename = time() . '_' . $res;
                    $current_user = auth()->user();

                    $isImage =  ImageController::verifyImage($file);

                    $disk = Storage::disk('Wasabi');
                    $s3_file_name = '';

                    if ($isImage == true) {
                        $s3_file_name = ImageController::resizeImg($file, $location, $filename);
                    } else {
                        $s3_file_name =  $disk->put($location, $file);
                    }

                    $LoanCaseAccountFiles = new LoanCaseAccountFiles();

                    $LoanCaseAccountFiles->main_id =  $voucherMain->id;
                    $LoanCaseAccountFiles->case_id =  $case_id;
                    $LoanCaseAccountFiles->file_name = $s3_file_name;
                    $LoanCaseAccountFiles->ori_name = $file->getClientOriginalName();
                    $LoanCaseAccountFiles->s3_file_name = $s3_file_name;
                    $LoanCaseAccountFiles->type = $extension;
                    $LoanCaseAccountFiles->remarks = $remarks;
                    $LoanCaseAccountFiles->status = 1;
                    $LoanCaseAccountFiles->created_by = $current_user->id;
                    $LoanCaseAccountFiles->created_at = date('Y-m-d H:i:s');
                    $LoanCaseAccountFiles->save();
                }
            }

            // temporary auto approve voucher if lawyer sent
            $Notification  = new Notification();
            $Notification->name = $current_user->name;
            $Notification->desc = 'request voucher ' . $voucher_running_no;
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

                $loanCaseAccount = LoanCaseBillDetails::where('id', '=', $voucherList[$i]['account_details_id'])->first();

                if ($loanCaseAccount->need_approval != 1) {
                    $loanCaseAccount->amount = $loanCaseAccount->amount - $voucherList[$i]['amount'];
                    $loanCaseAccount->save();

                    $this->updateBillCaseBillDisb($id, $request->input('bill_main_id'));
                }
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
        $voucherMain->case_bill_main_id = $billMainTempId;
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
        $voucherMain->voucher_type = 4;
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

        //create ledger entries
        $LedgerEntries = new LedgerEntries();

        $transaction = '';

        if ($voucherMain->transaction_id != null) {
            $transaction = $voucherMain->transaction_id;
        }

        $LedgerEntries->transaction_id = $transaction;
        $LedgerEntries->case_id = $id;
        $LedgerEntries->loan_case_main_bill_id = $billMainTempId;
        $LedgerEntries->user_id = $current_user->id;
        $LedgerEntries->key_id = $voucherMain->id;
        $LedgerEntries->transaction_type = 'D';
        $LedgerEntries->amount = $voucherMain->total_amount;
        $LedgerEntries->bank_id = $voucherMain->office_account_id;
        $LedgerEntries->remark = $voucherMain->remark;
        $LedgerEntries->status = 1;
        $LedgerEntries->updated_at = date('Y-m-d H:i:s');
        $LedgerEntries->date = $voucherMain->payment_date;
        $LedgerEntries->type = 'BILLRECEIVE';
        $LedgerEntries->save();



        $LedgerEntries = new LedgerEntriesV2();

        $transaction = '';

        if ($voucherMain->transaction_id != null) {
            $transaction = $voucherMain->transaction_id;
        }


        $LedgerEntries->transaction_id = $transaction;
        $LedgerEntries->case_id = $id;
        $LedgerEntries->loan_case_main_bill_id = $billMainTempId;
        $LedgerEntries->cheque_no = $voucherMain->voucher_no;
        $LedgerEntries->user_id = $current_user->id;
        $LedgerEntries->key_id = $voucherMain->id;
        $LedgerEntries->transaction_type = 'D';
        $LedgerEntries->amount = $voucherMain->total_amount;
        $LedgerEntries->bank_id = $voucherMain->office_account_id;
        $LedgerEntries->remark = $voucherMain->remark;
        $LedgerEntries->is_recon = 0;
        $LedgerEntries->payee  = $voucherMain->payee;
        $LedgerEntries->status = 1;
        $LedgerEntries->created_at = date('Y-m-d H:i:s');
        $LedgerEntries->date = $voucherMain->payment_date;
        $LedgerEntries->type = 'BILL_RECV';
        $LedgerEntries->save();

        $loanCaseBillMain = LoanCaseBillMain::where('id', '=', $billMainTempId)->first();

        // $loanCaseBillMain->collected_amt = $loanCaseBillMain->collected_amt + $request->input('payment_amt');
        if ($request->input('payment_date')) {
            $loanCaseBillMain->payment_receipt_date = $request->input('payment_date');
        }

        $LoanCase = LoanCase::where('id', $id)->first();
        CaseController::adminUpdateClientLedger($LoanCase);

        $loanCaseBillMain->save();

        $loanCase = LoanCase::where('id', '=', $id)->first();
        // $loanCase->collected_bill = $loanCase->collected_bill + $request->input('payment_amt');
        $loanCase->save();


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

        $bill_receive = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'vm.office_account_id')
            ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->leftJoin('users AS u', 'u.id', '=', 'vm.created_by')
            ->select('vd.*', 'a.name as account_name', 'u.name as requestor', 'vm.id as voucher_id', 'vm.transaction_id as trx_id', 'vm.voucher_no', 'b.short_code as bank_short_code', 'vm.payee', 'vm.remark as remark', 'b.name as client_bank_name', 'vm.payment_date as payment_date')
            // ->where('vd.case_id', '=',  $request->input('case_id'))
            ->where('vm.case_bill_main_id', '=',  $id)
            // ->where('vd.status', '=',  4)
            ->where('vm.status', '=',  4)
            ->where('vm.status', '<>',  99)
            ->get();


        $this->updateBillandCaseFigure($id, $billMainTempId);

        return response()->json([
            'status' => $status,
            'data' => $message,
            'receive' => view('dashboard.case.table.tbl-bill-receive-list', compact('bill_receive', 'current_user'))->render(),
        ]);
    }

    public function updateBillandCaseFigure($case_id, $bill_id)
    {
        $bill_receive_sum = 0;

        $bill_receive = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->select('v.*', 'vd.amount')
            ->where('vd.status', '=',  4)
            ->where('v.case_bill_main_id', '=', $bill_id)
            ->where('v.status', '<>', 99)
            ->get();

        for ($i = 0; $i < count($bill_receive); $i++) {
            $bill_receive_sum += $bill_receive[$i]->amount;
        }

        $LoanCaseBillMain = LoanCaseBillMain::where('id', $bill_id)->first();

        $LoanCaseBillMain->collected_amt = $bill_receive_sum;
        $LoanCaseBillMain->updated_at = date('Y-m-d H:i:s');
        $LoanCaseBillMain->save();

        $bill_receive = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->select('v.*', 'vd.amount')
            ->where('v.case_id', '=', $case_id)
            ->where('vd.status', '=',  4)
            ->where('v.status', '<>', 99)
            ->get();

        $bill_receive_sum = 0;

        for ($i = 0; $i < count($bill_receive); $i++) {
            $bill_receive_sum += $bill_receive[$i]->amount;
        }

        $LoanCase = LoanCase::where('id', $case_id)->first();

        $LoanCase->collected_bill = $bill_receive_sum;
        $LoanCase->updated_at = date('Y-m-d H:i:s');
        $LoanCase->save();

        return;
    }

    public static function updateBillCaseBillDisb($case_id, $bill_id)
    {
        $bill_disb_sum = 0;

        $bill_disb_sum = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->select('v.*', 'vd.amount')
            ->where('vd.status', '=',  1)
            ->where('v.voucher_type', '=',  1)
            ->whereNotIn('v.account_approval', [2])
            ->where('v.case_bill_main_id', '=', $bill_id)
            ->where('v.status', '<>', 99)
            ->sum('amount');

        $LoanCaseBillMain = LoanCaseBillMain::where('id', $bill_id)->first();

        $LoanCaseBillMain->used_amt = $bill_disb_sum;
        $LoanCaseBillMain->updated_at = date('Y-m-d H:i:s');
        $LoanCaseBillMain->save();

        $bill_disb_sum = 0;

        $bill_disb_sum = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->select('v.*', 'vd.amount')
            ->where('v.case_id', '=', $case_id)
            ->where('v.voucher_type', '=',  1)
            ->whereNotIn('v.account_approval', [2])
            ->where('vd.status', '=',  1)
            ->where('v.status', '<>', 99)
            ->sum('amount');

        $LoanCase = LoanCase::where('id', $case_id)->first();

        $LoanCase->total_bill = $bill_disb_sum;
        $LoanCase->updated_at = date('Y-m-d H:i:s');
        $LoanCase->save();

        return;
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
        $sst_rate = $request->input('sst');

        $category = AccountCategory::where('status', '=', 1)->orderBy('order', 'asc')->get();
        $QuotationTemplateMain = QuotationTemplateMain::where('id', '=', $id)->first();

        $current_user = auth()->user();

        $quotation = array();


        for ($i = 0; $i < count($category); $i++) {

            // $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)
            //     ->where('account_cat_id', '=', $category[$i]->id) 
            //     ->get();

            $QuotationTemplateDetails = DB::table('quotation_template_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.name_cn as account_name_cn', 'a.formula as account_formula', 'a.min as account_min', 'a.max as max', 'a.amount as account_amt', 'a.id as account_item_id', 'a.account_cat_id as account_cat_id', 'a.mandatory as mandatory', 'a.pfee1_item as pfee1_item', 'a.remark as item_desc', 'qd.remark as item_remark', 'a.pfee_item_desc')
                ->where('qd.acc_main_template_id', '=',  $id)
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->orderBy('order_no', 'ASC')
                ->get();

            array_push($quotation,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));
        }


        return response()->json([
            // 'view' => view('dashboard.case.table.tbl-bill-list', compact('quotation', 'loanCase', 'current_user', 'id'))->render()
            'view' => view('dashboard.case.table.tbl-bill-list', compact('quotation', 'loanCase', 'current_user', 'id', 'sst_rate', 'QuotationTemplateMain'))->render()
        ]);

        // return  $users;
    }

    public function loadMyQuotationTemplate(Request $request, $id)
    {
        // $caseTemplateDetail = AccountCategory::where('template_main_id', '=', $request->input('template_id'))->get()->sortBy('process_number');

        $loanCase = LoanCase::where('id', '=', $request->input('case_id'))->first();
        $sst_rate = $request->input('sst');

        $category = AccountCategory::where('status', '=', 1)->orderBy('order', 'asc')->get();
        $QuotationGeneratorMain = QuotationGeneratorMain::where('id', '=', $id)->first();
        $QuotationTemplateMain = QuotationTemplateMain::where('id', '=', $QuotationGeneratorMain->template_id)->first();

        $current_user = auth()->user();

        $quotation = array();

        for ($i = 0; $i < count($category); $i++) {

            // $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)
            //     ->where('account_cat_id', '=', $category[$i]->id)
            //     ->get();

            $QuotationTemplateDetails = DB::table('quotation_generator_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.formula as account_formula', 'a.min as account_min', 'a.max as max', 'a.amount as account_amt', 'a.id as account_item_id', 'a.account_cat_id as account_cat_id', 'a.mandatory as mandatory', 'a.pfee1_item as pfee1_item', 'a.remark as item_desc', 'qd.remark as item_remark', 'a.pfee_item_desc')
                ->where('qd.quo_gen_main_template_id', '=',  $id)
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->orderBy('order_no', 'ASC')
                ->get();

            array_push($quotation,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));
        }



        return response()->json([
            // 'view' => view('dashboard.case.table.tbl-bill-list', compact('quotation', 'loanCase', 'current_user', 'id'))->render()
            'view' => view('dashboard.case.table.tbl-bill-list', compact('quotation', 'loanCase', 'current_user', 'id', 'sst_rate', 'QuotationTemplateMain'))->render()
        ]);

        // return  $users;
    }

    public function loadCaseBill(Request $request, $id)
    {
        $blnCommPaid = 0;

        $case = LoanCase::where('id', '=', $request->input('case_id'))->first();
        $quotation_template_id = 0;

        $client = Customer::where('id', '=', $case->customer_id)->first();
        $category = AccountCategory::where('status', '=', 1)->orderBy('order', 'asc')->get();
        // $category = AccountCategory::where('status', '=', 1)->get();
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        if ($LoanCaseBillMain->name == 'Loan (Title & Master Title)') {
            $quotation_template_id = 13;
        } else if ($LoanCaseBillMain->name == 'Purchaser (Title & Master Title)') {
            $quotation_template_id = 14;
        } else if ($LoanCaseBillMain->name == 'Loan (Title & Master Title) - RHB Islamic') {
            $quotation_template_id = 15;
        } else if ($LoanCaseBillMain->name == 'Vendor (title & Master Title)') {
            $quotation_template_id = 16;
        } else {
            $quotation_template_id = $LoanCaseBillMain->quotation_template_id;
        }

        $LoanCaseBillMain = DB::table('loan_case_bill_main AS mb')
            ->leftJoin('quotation_template_main AS q', 'q.id', '=', 'mb.quotation_template_id')
            ->leftJoin('referral AS r1', 'r1.id', '=', 'mb.referral_a1_ref_id')
            ->leftJoin('referral AS r2', 'r2.id', '=', 'mb.referral_a2_ref_id')
            ->leftJoin('referral AS r3', 'r3.id', '=', 'mb.referral_a3_ref_id')
            ->leftJoin('referral AS r4', 'r4.id', '=', 'mb.referral_a4_ref_id')
            ->select(
                'mb.*',
                'q.pf_desc',
                'q.isChinese',
                'r1.bank_id as r1_bank',
                'r1.bank_account as r1_bank_account',
                'r2.bank_id as r2_bank',
                'r2.bank_account as r2_bank_account',
                'r3.bank_id as r3_bank',
                'r3.bank_account as r3_bank_account',
                'r4.bank_id as r4_bank',
                'r4.bank_account as r4_bank_account'
            )
            ->where('mb.id', '=',  $id)
            ->first();

        if ($LoanCaseBillMain->referral_a1_trx_id <> 0 || $LoanCaseBillMain->referral_a1_trx_id <> null) {
            $blnCommPaid = 1;
        }

        if ($LoanCaseBillMain->referral_a2_trx_id <> 0 || $LoanCaseBillMain->referral_a2_trx_id <> null) {
            $blnCommPaid = 1;
        }

        if ($LoanCaseBillMain->referral_a3_trx_id <> 0 || $LoanCaseBillMain->referral_a3_trx_id <> null) {
            $blnCommPaid = 1;
        }

        if ($LoanCaseBillMain->referral_a4_trx_id <> 0 || $LoanCaseBillMain->referral_a4_trx_id <> null) {
            $blnCommPaid = 1;
        }

        if ($LoanCaseBillMain->marketing > 0) {
            $blnCommPaid = 1;
        }

        // return $LoanCaseBillMain->referral_a2_trx_id;

        $bill_disburse = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'vm.office_account_id')
            ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
            ->leftJoin('users AS u', 'u.id', '=', 'vm.created_by')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->select(
                'vd.*',
                'a.name as account_name',
                'vm.id as voucher_id',
                'vm.voucher_no',
                'b.name as client_bank_name',
                'b.short_code as bank_short_code',
                'vm.lawyer_approval as lawyer_approval',
                'vm.account_approval as account_approval',
                'vm.remark as remark',
                'vm.payment_date',
                'vm.transaction_id as transaction_id',
                'u.name as requestor'
            )
            ->where('vd.case_id', '=',  $request->input('case_id'))
            ->where('bd.loan_case_main_bill_id', '=',  $id)
            ->where('vm.voucher_type', 1)
            ->where('vd.status', '<>',  99)
            ->where('vm.status', '<>',  99)
            ->orderBy('vm.created_at', 'desc')
            ->get();

        $bill_receive = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'vm.office_account_id')
            ->leftJoin('users AS u', 'u.id', '=', 'vm.created_by')
            ->select('vd.*', 'u.name as requestor', 'vm.id as voucher_id', 'vm.transaction_id as trx_id', 'vm.voucher_no', 'b.short_code as bank_short_code', 'vm.payee', 'vm.remark as remark', 'b.name as client_bank_name', 'vm.payment_date as payment_date')
            ->where('vm.case_bill_main_id', '=',  $id)
            ->where('vm.voucher_type', '=',  4)
            ->where('vm.status', '<>',  99)
            ->get();

        $quotation = array();
        $quotation_v3 = array();
        $item_id = array();

        for ($i = 0; $i < count($category); $i++) {

            $QuotationTemplateDetails = DB::table('loan_case_bill_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name',  'a.name_cn as account_name_cn', 'a.formula as account_formula', 'a.min as account_min', 'a.id as account_item_id', 'a.pfee1_item', 'a.mandatory', 'a.remark as item_desc', 'qd.remark as item_remark')
                ->where('qd.loan_case_main_bill_id', '=',  $id)
                ->where('qd.status', '=',  1)
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->get();

            if (count($QuotationTemplateDetails) > 0) {
                array_push($quotation_v3,  array('row' => 'title', 'category' => $category[$i], 'account_details' => []));

                for ($j = 0; $j < count($QuotationTemplateDetails); $j++) {
                    array_push($quotation_v3,  array('row' => 'item', 'category' => $category[$i], 'account_details' => $QuotationTemplateDetails[$j]));
                }

                array_push($quotation_v3,  array('row' => 'subtotal', 'category' => $category[$i], 'account_details' => []));
            }



            array_push($quotation,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));

            for ($j = 0; $j < count($QuotationTemplateDetails); $j++) {
                array_push($item_id,  $QuotationTemplateDetails[$j]->account_item_id);
            }
        }

        $pieces = array_chunk($quotation_v3, 30);

        // new quotation
        $quotationlistCount = DB::table('loan_case_bill_details AS qd')
            ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
            ->select('qd.*', 'a.name as account_name', 'a.name_cn as account_name_cn',  'a.formula as account_formula', 'a.min as account_min', 'a.id as account_item_id', 'a.pfee1_item', 'a.mandatory', 'a.remark as item_desc', 'qd.remark as item_remark')
            ->where('qd.loan_case_main_bill_id', '=',  $id)
            ->where('qd.status', '=',  1)
            ->count();

        $current_user = auth()->user();
        $QuotationTemplate = [];

        if ($quotation_template_id != 0) {
            $QuotationTemplate = DB::table('quotation_template_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.name_cn as account_name_cn', 'a.account_cat_id as account_cat_id', 'a.min as account_min', 'a.id as account_item_id', 'a.amount as default_amt')
                ->where('qd.acc_main_template_id', '=',  $quotation_template_id)
                ->whereNotIn('qd.account_item_id', $item_id)
                ->where('qd.status', '=',  1)
                ->get();
        }

        $invoice = array();
        $item_id = array();
        $invoice_v2 = array();


        $invoice_main_id = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->pluck('id')->first();


        for ($i = 0; $i < count($category); $i++) {


            array_push($invoice_v2,  array('row' => 'title', 'category' => $category[$i], 'account_details' => []));



            $QuotationTemplateDetails = DB::table('loan_case_invoice_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.name_cn as account_name_cn', 'a.formula as account_formula', 'a.min as account_min', 'a.id as account_item_id', 'a.pfee1_item', 'a.remark as item_desc', 'qd.remark as item_remark')
                // ->where('qd.loan_case_main_bill_id', '=',  $id)
                ->where('qd.invoice_main_id', '=',  $invoice_main_id)
                ->where('qd.status', '=',  1)
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->get();

            for ($j = 0; $j < count($QuotationTemplateDetails); $j++) {
                array_push($invoice_v2,  array('row' => 'item', 'category' => $category[$i], 'account_details' => $QuotationTemplateDetails[$j]));
            }

            array_push($invoice,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));

            for ($j = 0; $j < count($QuotationTemplateDetails); $j++) {
                array_push($item_id,  $QuotationTemplateDetails[$j]->account_item_id);
            }


            array_push($invoice_v2,  array('row' => 'subtotal', 'category' => $category[$i], 'account_details' => []));
        }

        $pieces_inv = array_chunk($invoice_v2, 30);

        $invoiceTemplate = [];

        if ($quotation_template_id != 0) {
            $invoiceTemplate = DB::table('quotation_template_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.name_cn as account_name_cn',  'a.account_cat_id as account_cat_id', 'a.min as account_min', 'a.id as account_item_id', 'a.amount as default_amt')
                ->where('qd.acc_main_template_id', '=',  $quotation_template_id)
                ->whereNotIn('qd.account_item_id', $item_id)
                ->where('qd.status', '=',  1)
                ->get();
        }

        if (count($invoice) > 0) {
            if ($LoanCaseBillMain->invoice_date == null) {
                $invoice_main_id = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->pluck('id')->first();
                // $LoanCaseInvoiceDetails = LoanCaseInvoiceDetails::where('loan_case_main_bill_id', '=', $id)->first();
                $LoanCaseInvoiceDetails = LoanCaseInvoiceDetails::where('invoice_main_id', '=', $invoice_main_id)->first();

                if ($LoanCaseInvoiceDetails) {
                    $updateLoanCaseBill = LoanCaseBillMain::where('id', '=', $id)->first();
                    $updateLoanCaseBill->invoice_date = $LoanCaseInvoiceDetails->created_at;
                    $updateLoanCaseBill->save();

                    $LoanCaseBillMain->invoice_date = $LoanCaseInvoiceDetails->created_at;
                }
            }
        }

        $case_bill = LoanCaseBillMain::where('case_id', '=', $request->input('case_id'))->get();

        $loanCaseBillMain = $this->extractMasterListInfo($LoanCaseBillMain, 'bill');
        $loanCaseBillMain = $this->extractMasterListInfo($LoanCaseBillMain, 'invoice');

        $parties_list = array();
        $customer = Customer::where('id', '=', $case->customer_id)->first();
        if ($customer) {
            array_push($parties_list,  array('party' => 'Client', 'name' => $customer->name));
        }

        $party_masterlist = DB::table('loan_case_masterlist as m')
            ->leftJoin('case_masterlist_field AS f', 'f.id', '=', 'm.masterlist_field_id')
            ->leftJoin('case_masterlist_field_category AS c', 'c.id', '=', 'f.case_field_id')
            ->select('m.*', 'c.name as master_cat_name')
            ->where('m.case_id', '=', $id)
            ->where('f.master_list_type', '=', 'parties_name')
            ->get();


        for ($i = 0; $i < count($party_masterlist); $i++) {
            array_push($parties_list,  array('party' =>  $party_masterlist[$i]->master_cat_name, 'name' => $party_masterlist[$i]->value));
        }

        $Branch = Branch::where('id', '=', $case->branch_id)->first();

        $account_template_with_cat = array();

        $account_template_cat = DB::table('loan_case_account')
            ->join('account_category', 'loan_case_account.account_cat_id', '=', 'account_category.id')
            ->select('account_category.id', 'account_category.category', 'taxable', 'percentage')
            ->distinct()
            ->groupBy('loan_case_account.id')
            ->where('loan_case_account.case_id', '=', $case->id)
            ->get();

        for ($i = 0; $i < count($account_template_cat); $i++) {

            $account_template_details_by_cat = LoanCaseAccount::where('case_id', '=', $case->id)
                ->where('account_cat_id', '=', $account_template_cat[$i]->id)
                ->get();
            array_push($account_template_with_cat,  array('category' => $account_template_cat[$i], 'account_details' => $account_template_details_by_cat));
        }

        $current_bill_tab = $request->input('current_bill_tab');
        $InvoiceBillingParty = InvoiceBillingParty::where('loan_case_main_bill_id', $id)->get();

        //   $InvoiceBillingParty = DB::table('invoice_billing_party as bp')
        //     ->leftJoin('loan_case_invoice_main as im', 'im.id', '=', 'bp.invoice_main_id')
        //     ->select('bp.*', 'im.invoice_no', 'im.id as invoice_id')
        //     ->where('bp.loan_case_main_bill_id', $id)
        //     ->get();

        $InvoiceBillingParty = DB::table('loan_case_invoice_main as im')
            ->leftJoin('invoice_billing_party as bp', 'im.bill_party_id', '=', 'bp.id')
            ->leftJoin('loan_case_bill_main as m', 'm.id', '=', 'im.loan_case_main_bill_id')
            ->select('bp.*', 'im.invoice_no', 'im.id as invoice_id', 'bp.id as bill_party_id', 'm.invoice_no as main_invoice_no')
            ->where('im.loan_case_main_bill_id', $id)
            ->get();

        $InvBillto = view('dashboard.case.section.d-invoice-billto', compact('InvoiceBillingParty'))->render();

        // Get the main invoice (first invoice or the one matching main invoice_no)
        $invoiceMain = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)
            ->where('invoice_no', $LoanCaseBillMain->invoice_no)
            ->first();
        
        // If not found, get the first invoice for this bill
        if (!$invoiceMain) {
            $invoiceMain = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->first();
        }

        return response()->json([
            'view' => view('dashboard.case.table.tbl-case-bill-list', compact('quotation', 'current_user', 'LoanCaseBillMain', 'blnCommPaid'))->render(),
            'view4' => view('dashboard.case.table.tbl-case-invoice-list', compact('invoice', 'current_user', 'LoanCaseBillMain'))->render(),
            'view2' => view('dashboard.case.table.tbl-case-quotation-p', compact('quotation', 'LoanCaseBillMain'))->render(),
            'view3' => view('dashboard.case.table.tbl-case-invoice-p', compact('invoice', 'LoanCaseBillMain'))->render(),
            'disburse' => view('dashboard.case.table.tbl-bill-disburse-list', compact('bill_disburse', 'current_user',))->render(),
            'receive' => view('dashboard.case.table.tbl-bill-receive-list', compact('bill_receive', 'current_user'))->render(),
            'summary' => view('dashboard.case.tabs.tab-bill-summary-report', compact('loanCaseBillMain', 'current_user'))->render(),
            'tab' => view('dashboard.case.tabs.bill.tab-bill-tablist', compact('LoanCaseBillMain', 'current_user', 'current_bill_tab'))->render(),
            'invoiceView' => view('dashboard.case.tabs.bill.tab-invoice', compact('LoanCaseBillMain', 'current_user', 'invoice', 'case', 'InvoiceBillingParty', 'invoiceMain'))->render(),
            'invoicePrint' => view('dashboard.case.d-invoice-print', compact('LoanCaseBillMain', 'current_user', 'case', 'Branch', 'invoice_v2', 'pieces_inv',))->render(),
            'billPrint' => view('dashboard.case.d-quotation-print', compact('LoanCaseBillMain', 'current_user', 'quotation', 'quotation_v3', 'pieces', 'case', 'Branch'))->render(),
            // 'billSummary' => view('dashboard.case.section.d-bill-summary-details', compact('LoanCaseBillMain', 'current_user', 'quotation', 'case', 'Branch'))->render(),
            'billSummary' => view('dashboard.case.section.d-bill-summary-details', compact('LoanCaseBillMain'))->render(),
            'InvoiceSummary' => view('dashboard.case.section.d-invoice-summary-details', compact('LoanCaseBillMain', 'current_user', 'quotation', 'case', 'Branch'))->render(),
            'billView' => view('dashboard.case.tabs.bill.tab-created-bill', compact('LoanCaseBillMain', 'current_user', 'account_template_with_cat', 'case'))->render(),
            'current_user' => $current_user,
            'blnCommPaid' => $blnCommPaid,
            'QuotationTemplate' => $QuotationTemplate,
            'invoiceTemplate' => $invoiceTemplate,
            'InvBillto' => $InvBillto,
            'InvoiceBillingParty' => $InvoiceBillingParty,
            'LoanCaseBillMain' => $LoanCaseBillMain
        ]);

        // return  $users;
    }

    public function loadPrintPartyInfo() {}

    public function loadCaseBillBak(Request $request, $id)
    {
        // $caseTemplateDetail = AccountCategory::where('template_main_id', '=', $request->input('template_id'))->get()->sortBy('process_number');

        $blnCommPaid = 0;

        $case = LoanCase::where('id', '=', $request->input('case_id'))->first();
        $quotation_template_id = 0;

        $client = Customer::where('id', '=', $case->customer_id)->first();

        $category = AccountCategory::where('status', '=', 1)->get();

        // $loanCaseBillDetails = LoanCaseBillDetails::where('id', '=', $id)->where('status', '=', 1)->get();
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();


        if ($LoanCaseBillMain->name == 'Loan (Title & Master Title)') {
            $quotation_template_id = 13;
        } else if ($LoanCaseBillMain->name == 'Purchaser (Title & Master Title)') {
            $quotation_template_id = 14;
        } else if ($LoanCaseBillMain->name == 'Loan (Title & Master Title) - RHB Islamic') {
            $quotation_template_id = 15;
        } else if ($LoanCaseBillMain->name == 'Vendor (title & Master Title)') {
            $quotation_template_id = 16;
        } else {
            $quotation_template_id = $LoanCaseBillMain->quotation_template_id;
        }



        $LoanCaseBillMain = DB::table('loan_case_bill_main AS mb')
            ->leftJoin('quotation_template_main AS q', 'q.id', '=', 'mb.quotation_template_id')
            ->leftJoin('referral AS r1', 'r1.id', '=', 'mb.referral_a1_ref_id')
            ->leftJoin('referral AS r2', 'r2.id', '=', 'mb.referral_a2_ref_id')
            ->leftJoin('referral AS r3', 'r3.id', '=', 'mb.referral_a3_ref_id')
            ->leftJoin('referral AS r4', 'r4.id', '=', 'mb.referral_a4_ref_id')
            ->select(
                'mb.*',
                'q.pf_desc',
                'r1.bank_id as r1_bank',
                'r1.bank_account as r1_bank_account',
                'r2.bank_id as r2_bank',
                'r2.bank_account as r2_bank_account',
                'r3.bank_id as r3_bank',
                'r3.bank_account as r3_bank_account',
                'r4.bank_id as r4_bank',
                'r4.bank_account as r4_bank_account'
            )
            ->where('mb.id', '=',  $id)
            ->first();

        if ($LoanCaseBillMain->referral_a1_trx_id <> 0 || $LoanCaseBillMain->referral_a1_trx_id <> null) {
            $blnCommPaid = 1;
        }

        if ($LoanCaseBillMain->referral_a2_trx_id <> 0 || $LoanCaseBillMain->referral_a2_trx_id <> null) {
            $blnCommPaid = 1;
        }

        if ($LoanCaseBillMain->referral_a3_trx_id <> 0 || $LoanCaseBillMain->referral_a3_trx_id <> null) {
            $blnCommPaid = 1;
        }

        if ($LoanCaseBillMain->referral_a4_trx_id <> 0 || $LoanCaseBillMain->referral_a4_trx_id <> null) {
            $blnCommPaid = 1;
        }

        if ($LoanCaseBillMain->marketing > 0) {
            $blnCommPaid = 1;
        }

        $voucher = DB::table('voucher_main AS m')
            ->leftJoin('voucher_details AS d', 'd.voucher_main_id', '=', 'm.id')
            ->leftJoin('loan_case_bill_details AS qd', 'qd.id', '=', 'd.account_details_id')
            ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
            ->select('m.*', 'a.name as account_name')
            ->where('m.case_id', '=',  $request->input('case_id'))
            ->get();

        $bill_disburse = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'vm.office_account_id')
            ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
            ->leftJoin('users AS u', 'u.id', '=', 'vm.created_by')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->select(
                'vd.*',
                'a.name as account_name',
                'vm.id as voucher_id',
                'vm.voucher_no',
                'b.name as client_bank_name',
                'b.short_code as bank_short_code',
                'vm.lawyer_approval as lawyer_approval',
                'vm.account_approval as account_approval',
                'vm.remark as remark',
                'vm.payment_date',
                'vm.transaction_id as transaction_id',
                'u.name as requestor'
            )
            ->where('vd.case_id', '=',  $request->input('case_id'))
            ->where('bd.loan_case_main_bill_id', '=',  $id)
            ->where('vd.status', '<>',  4)
            ->where('vd.status', '<>',  99)
            ->where('vm.status', '<>',  99)
            ->orderBy('vm.created_at', 'desc')
            ->get();

        $bill_disburse_count = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
            ->where('vm.case_id', '=',  $request->input('case_id'))
            ->where('bd.loan_case_main_bill_id', '=',  $id)
            ->whereNotIn('vm.status', [2, 99])
            ->whereNotIn('vm.account_approval', [2, 99])
            ->sum('vd.amount');



        $bill_receive = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'vm.office_account_id')
            ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->leftJoin('users AS u', 'u.id', '=', 'vm.created_by')
            ->select('vd.*', 'a.name as account_name', 'u.name as requestor', 'vm.id as voucher_id', 'vm.transaction_id as trx_id', 'vm.voucher_no', 'b.short_code as bank_short_code', 'vm.payee', 'vm.remark as remark', 'b.name as client_bank_name', 'vm.payment_date as payment_date')
            // ->where('vd.case_id', '=',  $request->input('case_id'))
            ->where('vm.case_bill_main_id', '=',  $id)
            // ->where('vd.status', '=',  4)
            ->where('vm.status', '=',  4)
            ->where('vm.status', '<>',  99)
            ->get();



        $quotation = array();
        $item_id = array();

        for ($i = 0; $i < count($category); $i++) {

            // $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)
            //     ->where('account_cat_id', '=', $category[$i]->id) 
            //     ->get();

            $QuotationTemplateDetails = DB::table('loan_case_bill_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.formula as account_formula', 'a.min as account_min', 'a.id as account_item_id', 'a.pfee1_item', 'a.mandatory', 'a.remark as item_desc', 'qd.remark as item_remark')
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

        $invoice = array();
        $item_id = array();


        for ($i = 0; $i < count($category); $i++) {


            $QuotationTemplateDetails = DB::table('loan_case_invoice_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.formula as account_formula', 'a.min as account_min', 'a.id as account_item_id', 'a.pfee1_item', 'a.remark as item_desc', 'qd.remark as item_remark')
                ->where('qd.loan_case_main_bill_id', '=',  $id)
                ->where('qd.status', '=',  1)
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->get();

            array_push($invoice,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));

            for ($j = 0; $j < count($QuotationTemplateDetails); $j++) {
                array_push($item_id,  $QuotationTemplateDetails[$j]->account_item_id);
            }
        }

        $invoiceTemplate = [];

        if ($quotation_template_id != 0) {
            $invoiceTemplate = DB::table('quotation_template_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.account_cat_id as account_cat_id', 'a.min as account_min', 'a.id as account_item_id', 'a.amount as default_amt')
                ->where('qd.acc_main_template_id', '=',  $quotation_template_id)
                ->whereNotIn('qd.account_item_id', $item_id)
                ->where('qd.status', '=',  1)
                ->get();
        }

        if (count($invoice) > 0) {
            if ($LoanCaseBillMain->invoice_date == null) {
                $LoanCaseInvoiceDetails = LoanCaseInvoiceDetails::where('loan_case_main_bill_id', '=', $id)->first();

                if ($LoanCaseInvoiceDetails) {
                    $updateLoanCaseBill = LoanCaseBillMain::where('id', '=', $id)->first();
                    $updateLoanCaseBill->invoice_date = $LoanCaseInvoiceDetails->created_at;
                    $updateLoanCaseBill->save();

                    $LoanCaseBillMain->invoice_date = $LoanCaseInvoiceDetails->created_at;
                }


                // $LoanCaseBillMain->save();
            }
        }

        $case_bill = LoanCaseBillMain::where('case_id', '=', $request->input('case_id'))->get();

        $loanCaseBillMain = $LoanCaseBillMain;


        $parties_list = array();
        $customer = Customer::where('id', '=', $case->customer_id)->first();
        if ($customer) {
            array_push($parties_list,  array('party' => 'Client', 'name' => $customer->name));
        }


        $party_masterlist = DB::table('loan_case_masterlist as m')
            ->leftJoin('case_masterlist_field AS f', 'f.id', '=', 'm.masterlist_field_id')
            ->leftJoin('case_masterlist_field_category AS c', 'c.id', '=', 'f.case_field_id')
            ->select('m.*', 'c.name as master_cat_name')
            ->where('m.case_id', '=', $id)
            ->where('f.master_list_type', '=', 'parties_name')
            ->get();


        for ($i = 0; $i < count($party_masterlist); $i++) {
            array_push($parties_list,  array('party' =>  $party_masterlist[$i]->master_cat_name, 'name' => $party_masterlist[$i]->value));
        }

        $Branch = Branch::where('id', '=', $case->branch_id)->first();

        // Get the main invoice (first invoice or the one matching main invoice_no)
        $invoiceMain = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)
            ->where('invoice_no', $LoanCaseBillMain->invoice_no)
            ->first();
        
        // If not found, get the first invoice for this bill
        if (!$invoiceMain) {
            $invoiceMain = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->first();
        }

        return response()->json([
            'view' => view('dashboard.case.table.tbl-case-bill-list', compact('quotation', 'current_user', 'LoanCaseBillMain', 'blnCommPaid'))->render(),
            'view4' => view('dashboard.case.table.tbl-case-invoice-list', compact('invoice', 'current_user', 'LoanCaseBillMain'))->render(),
            'view2' => view('dashboard.case.table.tbl-case-quotation-p', compact('quotation', 'LoanCaseBillMain'))->render(),
            'view3' => view('dashboard.case.table.tbl-case-invoice-p', compact('invoice', 'LoanCaseBillMain'))->render(),
            'disburse' => view('dashboard.case.table.tbl-bill-disburse-list', compact('bill_disburse', 'current_user',))->render(),
            'receive' => view('dashboard.case.table.tbl-bill-receive-list', compact('bill_receive', 'current_user'))->render(),
            'summary' => view('dashboard.case.tabs.tab-bill-summary-report', compact('loanCaseBillMain', 'current_user'))->render(),
            'tab' => view('dashboard.case.tabs.bill.tab-bill-tablist', compact('LoanCaseBillMain', 'current_user'))->render(),
            'invoiceView' => view('dashboard.case.tabs.bill.tab-invoice', compact('LoanCaseBillMain', 'current_user', 'invoice', 'case', 'invoiceMain'))->render(),
            'invoicePrint' => view('dashboard.case.d-invoice-print', compact('LoanCaseBillMain', 'current_user', 'invoice', 'case', 'Branch'))->render(),
            'billPrint' => view('dashboard.case.d-quotation-print', compact('LoanCaseBillMain', 'current_user', 'quotation', 'case', 'Branch'))->render(),
            'billSummary' => view('dashboard.case.section.d-bill-summary-details', compact('LoanCaseBillMain', 'current_user', 'quotation', 'case', 'Branch'))->render(),
            // 'client' => $client,
            // 'invoice' => $invoice,
            'current_user' => $current_user,
            // 'blnCommPaid' => $blnCommPaid,
            // 'case_bill' => $case_bill,
            // 'bill_disburse_count' => $bill_disburse_count,
            'QuotationTemplate' => $QuotationTemplate,
            'invoiceTemplate' => $invoiceTemplate,
            'LoanCaseBillMain' => $LoanCaseBillMain
        ]);

        // return  $users;
    }

    public function loadQuotationToInvoice($id)
    {
        $current_user = auth()->user();
        $LoanCaseBillDetails = LoanCaseBillDetails::where('loan_case_main_bill_id', '=', $id)->get();
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        if ($LoanCaseBillMain->invoice_no) {
        } else {
            $this->checkInvRunningNoUsed($id, false);
        }

        //Add invoice main data 
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->where('invoice_no', $LoanCaseBillMain->invoice_no)->first();


        if (!$LoanCaseInvoiceMain) {
            $loanCaseInvoiceMain = new LoanCaseInvoiceMain();

            $loanCaseInvoiceMain->loan_case_main_bill_id = $id;
            $loanCaseInvoiceMain->invoice_no = $LoanCaseBillMain->invoice_no;
            $loanCaseInvoiceMain->bill_party_id = 0;
            $loanCaseInvoiceMain->remark = "";
            $loanCaseInvoiceMain->Invoice_date = $LoanCaseBillMain->Invoice_date;
            $loanCaseInvoiceMain->amount = $LoanCaseBillMain->total_amt;
            $loanCaseInvoiceMain->pfee1_inv = $LoanCaseBillMain->pfee1_inv;
            $loanCaseInvoiceMain->pfee2_inv = $LoanCaseBillMain->pfee2_inv;
            $loanCaseInvoiceMain->sst_inv = $LoanCaseBillMain->sst;
            $loanCaseInvoiceMain->created_by = $current_user->id;
            $loanCaseInvoiceMain->status = 1;
            $loanCaseInvoiceMain->created_at = date('Y-m-d H:i:s');

            $loanCaseInvoiceMain->save();
        }



        $party_count = EInvoiceContoller::getPartyCount($id);

        if ($party_count > 1) {
            LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)
                ->update([
                    'Invoice_date' => $LoanCaseBillMain->Invoice_date,
                    'amount' => $LoanCaseBillMain->total_amt / $party_count,
                    'created_by' => $current_user->id,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
        }



        $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)
            ->get();

        if (count($LoanCaseInvoiceMain) > 0) {
            for ($j = 0; $j < count($LoanCaseInvoiceMain); $j++) {
                $LoanCaseBillDetails = LoanCaseBillDetails::where('loan_case_main_bill_id', $id)->get();

                if (count($LoanCaseBillDetails) > 0) {
                    for ($i = 0; $i < count($LoanCaseBillDetails); $i++) {

                        $LoanCaseInvoiceDetails = new LoanCaseInvoiceDetails();

                        $LoanCaseInvoiceDetails->loan_case_main_bill_id = $id;
                        $LoanCaseInvoiceDetails->account_item_id = $LoanCaseBillDetails[$i]->account_item_id;
                        $LoanCaseInvoiceDetails->quotation_item_id = $LoanCaseBillDetails[$i]->id;
                        $LoanCaseInvoiceDetails->invoice_main_id = $LoanCaseInvoiceMain[$j]->id;
                        $LoanCaseInvoiceDetails->amount = $LoanCaseBillDetails[$i]->quo_amount_no_sst / $party_count;
                        $LoanCaseInvoiceDetails->ori_invoice_amt = $LoanCaseBillDetails[$i]->quo_amount_no_sst;
                        $LoanCaseInvoiceDetails->quo_amount = $LoanCaseBillDetails[$i]->quo_amount_no_sst;
                        $LoanCaseInvoiceDetails->remark = $LoanCaseBillDetails[$i]->remark;
                        $LoanCaseInvoiceDetails->created_by = $current_user->id;
                        $LoanCaseInvoiceDetails->status = 1;
                        $LoanCaseInvoiceDetails->created_at = date('Y-m-d H:i:s');

                        $LoanCaseInvoiceDetails->save();
                    }
                }
            }
        }

        $this->updatePfeeDisbAmountINV($id);

        $invoice = array();
        $item_id = array();

        $category = AccountCategory::where('status', '=', 1)->get();
        for ($i = 0; $i < count($category); $i++) {


            $QuotationTemplateDetails = DB::table('loan_case_invoice_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.formula as account_formula', 'a.min as account_min', 'a.id as account_item_id', 'a.pfee1_item', 'a.remark as item_desc', 'qd.remark as item_remark')
                ->where('qd.loan_case_main_bill_id', '=',  $id)
                ->where('qd.status', '=',  1)
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->get();

            array_push($invoice,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));
        }

        return response()->json([
            'status' => 1,
            'data' => 'Loaded quotation into invoice',
            'view4' => view('dashboard.case.table.tbl-case-invoice-list', compact('invoice', 'current_user', 'LoanCaseBillMain'))->render(),
        ]);
    }

    public function checkInvRunningNoUsed($id, $is_revert, $revert_invoice_count = 1)
    {
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        $LoanCase = LoanCase::where('id', '=', $LoanCaseBillMain->case_id)->first();

        if ($LoanCase->branch_id == 2) {
            $running_no = $LoanCase->case_running_no;
            $newPuchong =  substr($running_no, 0, 1) === "7";

            if ($newPuchong == 1) {
                $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)
                    ->where('parameter_value_2', 'A')->first();

                if ($LoanCaseBillMain->sst_rate == 6) {
                    $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)
                        ->where('parameter_value_2', 'A')->first();
                } else if ($LoanCaseBillMain->sst_rate == 8) {
                    $parameter = Parameter::where('parameter_type', 'like', '%8_invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)
                        ->where('parameter_value_2', 'A')->first();
                }
            } else {
                if ($LoanCaseBillMain->sst_rate == 6) {
                    $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)->first();
                } else if ($LoanCaseBillMain->sst_rate == 8) {
                    $parameter = Parameter::where('parameter_type', 'like', '%8_invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)->first();
                }
            }
        } else {
            // $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)->first();

            if ($LoanCaseBillMain->sst_rate == 6) {
                $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)->first();
            } else if ($LoanCaseBillMain->sst_rate == 8) {
                $parameter = Parameter::where('parameter_type', 'like', '%8_invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)->first();
            }
        }



        $blnFound = 0;
        $breakCount = 0;

        $running_no = (int)$parameter->parameter_value_1;

        if ($is_revert == true) {
            $running_no = (int)$parameter->parameter_value_1;
            $running_no = $running_no - $revert_invoice_count;
        } else {
            while ($blnFound == 0 && $breakCount < 20) {
                $running_no += 1;

                // $LoanCaseBillMainCheck = LoanCaseBillMain::where('invoice_no', $parameter->parameter_value_2 . $running_no)->count();
                $LoanCaseBillMainCheck = LoanCaseInvoiceMain::where('invoice_no', $parameter->parameter_value_2 . $running_no)->count();

                if ($LoanCaseBillMainCheck == 0) {
                    $blnFound = 1;
                }

                $breakCount += 1;
            }

            $LoanCaseBillMain->invoice_no = $parameter->parameter_value_2 . $running_no;
            $LoanCaseBillMain->save();
        }

        $parameter->parameter_value_1 = $running_no;
        $parameter->save();
    }


    public function createBill(Request $request, $id)
    {
        $status = 1;
        $need_approval = 0;
        $pfee1_item = 0;
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

            $QuotationTemplateMain = QuotationTemplateMain::where('id', $request->input('hidden_quotation_template_id'))->first();

            $quotation_name = $QuotationTemplateMain->name;
            $quotation_name = str_replace("Quotation - ", "", $quotation_name);

            $running_no = (int)$parameter->parameter_value_1 + 1;
            $parameter->parameter_value_1 = $running_no;
            $parameter->save();

            $loanCaseBillMain = new LoanCaseBillMain();

            $loanCaseBillMain->case_id = $id;
            $loanCaseBillMain->bill_no = $running_no;
            $loanCaseBillMain->name = $quotation_name;
            $loanCaseBillMain->status = 1;
            $loanCaseBillMain->quotation_template_id = $request->input('hidden_quotation_template_id');
            $loanCaseBillMain->created_at = date('Y-m-d H:i:s');
            $loanCaseBillMain->created_by = $current_user->id;
            $loanCaseBillMain->bill_to = $request->input('bill_to');
            $loanCaseBillMain->sst_rate = $request->input('sst_rate');
            $loanCaseBillMain->save();

            $ssr_rate = $request->input('sst_rate') * 0.01;
            // $ssr_rate = 0.06;


            for ($i = 0; $i < count($billList); $i++) {

                $loanCaseBillDetails = new LoanCaseBillDetails();
                $sst = 0;

                $loanCaseBillDetails->loan_case_main_bill_id = $loanCaseBillMain->id;
                $loanCaseBillDetails->account_item_id = $billList[$i]['account_item_id'];
                $loanCaseBillDetails->min = $billList[$i]['min'];
                $loanCaseBillDetails->max = $billList[$i]['max'];


                $loanCaseBillDetails->need_approval = $billList[$i]['need_approval'];
                if ($billList[$i]['cat_id'] == 1 || $billList[$i]['cat_id'] == 4) {
                    // $sst = round($billList[$i]['amount'] * 0.06, 2);
                    $sst = round($billList[$i]['amount'] * $ssr_rate, 2);
                    $loanCaseBillDetails->sst = $sst;
                    $loanCaseBillDetails->quo_amount = (float)$billList[$i]['amount'] + $sst;
                    $loanCaseBillDetails->amount = (float)$billList[$i]['amount'];
                    if ($billList[$i]['cat_id'] == 1) {
                        $loanCaseBillDetails->remark = $billList[$i]['desc'];
                    }

                    $AccountItemCount = AccountItem::where('id', '=', $billList[$i]['account_item_id'])->where('pfee1_item', '=', 1)->count();

                    if ($AccountItemCount > 0) {
                        $LoanCase = LoanCase::where('id', '=', $id)->first();

                        if ($LoanCase) {
                            $loanCaseBillMain->bln_main_bill = 1;
                            $loanCaseBillMain->referral_a1_id = $LoanCase->referral_name;
                            $loanCaseBillMain->referral_a1_ref_id = $LoanCase->referral_id;
                            $loanCaseBillMain->save();
                        }
                    }

                    // $pfee1_item

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

            // $loanCaseBillMain->total_amt = $totalAmount;
            // $loanCaseBillMain->total_amount_without_sst = $totalAmountNoSST;
            // $loanCaseBillMain->save();

            // $loanCase = LoanCase::where('id', '=', $loanCaseBillMain->case_id)->first();


            // $loanCase->targeted_bill += $totalAmount;
            // $loanCase->save();
            // $this->updateLoanCaseBillInfo($loanCaseBillMain->id); 
            $this->updatePfeeDisbAmount($loanCaseBillMain->id);



            // $this->updateBillSummary($request, $loanCaseBillMain->id);
        }

        return response()->json([
            'status' => $status,
            'data' => $message,
            'view' => $this->loadMainBillTable($id)
        ]);
    }

    public function loadMainBillTable($id)
    {
        $current_user = auth()->user();

        $loanCaseBillMain = DB::table('loan_case_bill_main AS m')
            ->leftJoin('users AS u', 'u.id', '=', 'm.created_by')
            ->select('m.*', 'u.name as prepare_by')
            ->where('case_id', '=', $id)
            ->where('m.status', '=',  '1')
            ->get();

        $case = DB::table('loan_case as l')
            ->leftJoin('portfolio as p', 'l.bank_id', '=', 'p.id')
            ->leftJoin('branch as b', 'l.branch_id', '=', 'b.id')
            ->select('l.*', 'p.name as portfolio', 'b.name as branch_name')
            ->where('l.id', '=', $id)
            ->first();

        return response()->json([
            'total_sum' => view('dashboard.case.section.d-bill-summary', compact('loanCaseBillMain', 'case', 'current_user'))->render(),
            'view' => view('dashboard.case.table.tbl-created-bill-list', compact('loanCaseBillMain', 'case', 'current_user'))->render()
        ]);

        // return view('dashboard.case.table.tbl-created-bill-list', compact('loanCaseBillMain','case'))->render();

    }

    public function adminInsertAccountCodeNo()
    {
        for ($i = 1; $i <= 2; $i++) {
            $number = str_pad($i, 6, '0', STR_PAD_LEFT); // pad with zeros to create a 6-digit number
            DB::table('your_table')->insert([
                'your_column' => $number
            ]);
        }
    }

    public static function compileNotesUpdatePatch($ObjCollection, $operation_code, $request, $record_id)
    {
        $status_span = '';
        $current_user = auth()->user();
        $message = '';

        if ($ObjCollection->received == '1') {
            $status_span = '<span class="label bg-success">Received</span>';
        } else {
            $status_span = '<span class="label bg-warning">Pending</span>';
        }

        switch (strtolower($operation_code)) {
            case 'safekeeping':
                $message = '
                <a href="/safe-keeping/' . $ObjCollection->id . '/edit" target="_blank">[Created&nbsp;<b>Safe Keeping</b> record]</a><br />
                <strong>Document Sent</strong>:&nbsp;' . $ObjCollection->document_sent  . '<br />
                <strong>Attention To</strong>:&nbsp;' . $ObjCollection->attention_to . '<br />
                <strong>Received</strong>:&nbsp;' . $status_span;
                break;
            case 'landoffice':

                if ($ObjCollection->received == '1') {
                    $status_span = '<span class="label bg-success">Received</span>';
                } else {
                    $status_span = '<span class="label bg-warning">Pending</span>';
                }

                $message = '
                <a href="/land-office/' . $ObjCollection->id . '/edit" target="_blank">[Created&nbsp;<b>Land Office</b> record]</a><br />
                <strong>Land Office</strong>:&nbsp;' . $ObjCollection->land_office  . '<br />
                <strong>Smartbox No</strong>:&nbsp;' . $ObjCollection->smartbox_no  . '<br />
                <strong>Receipt No</strong>:&nbsp;' . $ObjCollection->receipt_no  . '<br />
                <strong>Matter</strong>:&nbsp;' . $ObjCollection->matter . '<br />
                <strong>Done</strong>:&nbsp;' . $status_span;

                break;
            case 'chkt':

                if ($ObjCollection->per3_rpgt_paid == '1') {
                    $status_span = '<span class="label bg-success">Yes</span>';
                } else {
                    $status_span = '<span class="label bg-warning">No</span>';
                }

                $message = '
                <a href="/chkt/' . $ObjCollection->id . '/edit" target="_blank">[Created&nbsp;<b>CHKT</b> record]</a><br />
                <strong>Last SPA Date</strong>:&nbsp;' . $ObjCollection->last_spa_date . '<br />
                <strong>Current SPA Date</strong>:&nbsp;' . $ObjCollection->current_spa_date  . '<br />
                <strong>CHKT Filed On</strong>:&nbsp;' . $ObjCollection->chkt_filled_on . '<br />
                <strong>Remark</strong>:&nbsp;' . $ObjCollection->remark  . '<br />
                <strong>3% RPGT Paid</strong>:&nbsp;' . $status_span;

                break;

            case 'dispatch':
                $courier = Courier::where('id', '=', $ObjCollection->courier_id)->first();
                $dispatch_name = '';

                if ($courier) {
                    $dispatch_name = $courier->name;
                }

                $dispatch_type = '';

                // if ($request->input('dispatch_type') != '') {
                //     if ($request->input('dispatch_type') == 1) {
                //         $dispatch_type = 'Outgoing';
                //     } else if ($request->input('dispatch_type') == 2) {
                //         $dispatch_type = 'Incoming';
                //     }
                // }

                if ($ObjCollection->dispatch_type == '1') {
                    $dispatch_type = 'Outgoing';
                } else {
                    $dispatch_type = 'Incoming';
                }

                if ($ObjCollection->status == '1') {
                    $status_span = '<span class="label bg-success">Completed</span>';
                } else if ($ObjCollection->status == '0') {
                    $status_span = '<span class="label bg-warning">Sending</span>';
                } else {
                    $status_span = '<span class="label bg-info">In Progress</span>';
                }

                $message = '
                <a href="/dispatch/' . $ObjCollection->id . '/edit" target="_blank">[Created&nbsp;<b>Dispatch - ' . $dispatch_type . '</b> record]</a><br />
                <strong>Send To / Receive From</strong>:&nbsp;' . $ObjCollection->send_to . '<br />
                <strong>Dispatch Name</strong>:&nbsp;' . $dispatch_name . '<br />
                <strong>Returned To Office</strong>:&nbsp;' . $ObjCollection->return_to_office_datetime  . '<br />
                <strong>Job Description</strong>:&nbsp;' . $ObjCollection->job_desc . '<br />
                <strong>Remark</strong>:&nbsp;' . $ObjCollection->remark  . '<br />
                <strong>Status</strong>:&nbsp;' . $status_span;

                break;
            default:
        }


        $OperationAttachments = OperationAttachments::where('key_id', $ObjCollection->id)->where('attachment_type', strtolower($operation_code))->where('status', 1)->get();

        if (count($OperationAttachments) > 0) {

            $attachment = '<br/><strong>Attachment</strong>:&nbsp;<br />';
            foreach ($OperationAttachments as $file) {
                $attachment .= '<a  href="javascript:void(0)" onclick="openFileFromS3(\'' . $file->s3_file_name . '\')"  class="mailbox-attachment-name "><i class="fa fa-paperclip"></i>' . $file->file_ori_name . '</a><br />';
            }

            $message = $message . $attachment;
        }


        $LoanCaseKivNotes = LoanCaseKivNotes::where('object_id_1', '=', $record_id)->where('label', '=', 'operation|' . $operation_code)->first();

        if ($LoanCaseKivNotes) {
            $LoanCaseKivNotes->notes =  $message;
            $LoanCaseKivNotes->updated_at = date('Y-m-d H:i:s');
            $LoanCaseKivNotes->updated_by = $current_user->id;
            $LoanCaseKivNotes->save();
        } else {
            Log::info('test:' . $record_id . '--' . $operation_code . '<br/>');
        }
    }


    public function transferSystemCaseAdmin(Request $request, $id)
    {
        $current_user = auth()->user();

        $LoanCase = LoanCase::where('id', '=', $id)->first();
        $lawyer_id = 0;
        $clerk_id = 0;

        if ($LoanCase) {
            if ($LoanCase->lawyer_id != null) {
                $lawyer_id = $LoanCase->lawyer_id;
            }

            if ($LoanCase->clerk_id != null) {
                $clerk_id = $LoanCase->clerk_id;
            }
        }

        // return $this->updateNewRefNo($LoanCase, $request->input('lawyer_id'), 0);;

        if ($request->input('lawyer_id') !=  $lawyer_id) {

            $previous_lawyer_name = '-';

            $previous_lawyer = Users::where('id', '=', $lawyer_id)->first();
            $current_lawyer = Users::where('id', '=', $request->input('lawyer_id'))->first();

            if ($previous_lawyer) {
                $previous_lawyer_name = $previous_lawyer->name;
            }

            $CaseTransferLog = new CaseTransferLog();
            $CaseTransferLog->user_id =  $current_user->id;
            $CaseTransferLog->case_id =  $LoanCase->id;
            $CaseTransferLog->action =  'Transfer';
            $CaseTransferLog->desc =  null;
            $CaseTransferLog->status =  1;
            $CaseTransferLog->created_at = date('Y-m-d H:i:s');
            $CaseTransferLog->ori_user = $lawyer_id;
            $CaseTransferLog->new_user = $request->input('lawyer_id');
            $CaseTransferLog->object_id = 7; //role id
            $CaseTransferLog->prev_ref_no =  $LoanCase->case_ref_no;
            $CaseTransferLog->prev_branch =  0;
            $CaseTransferLog->current_branch =  0;
            $CaseTransferLog->save();

            $LoanCase->lawyer_id =  $request->input('lawyer_id');
            $LoanCase->updated_at = date('Y-m-d H:i:s');
            $LoanCase->save();

            $LoanCaseKivNotes = new LoanCaseKivNotes();

            $LoanCaseKivNotes->case_id =  $id;
            $LoanCaseKivNotes->notes =  '[Transfer case from ' . $previous_lawyer_name . ' to ' . $current_lawyer->name . ']';
            $LoanCaseKivNotes->label =  'transfer';
            $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
            $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');

            $LoanCaseKivNotes->status =  1;
            $LoanCaseKivNotes->created_by = $current_user->id;
            $LoanCaseKivNotes->save();

            $LoanCase->case_ref_no = $this->updateNewRefNo($LoanCase, $request->input('lawyer_id'), 0);
            $LoanCase->save();

            $CaseTransferLog->current_ref_no =  $LoanCase->case_ref_no;
            $CaseTransferLog->save();
        }


        return response()->json(['status' => 1, 'data' => 'success']);
    }

    public function adminBulkTransferCase(Request $request)
    {
        // // Transfer cases bulk
        // $LoanCase = LoanCase::where('lawyer_id', '=', 106)->where('status', 2)->get();

        // Transfer cases bulk
        $LoanCase = LoanCase::where('clerk_id', '=', 132)->where('status', '<>', 99)->get();



        for ($j = 0; $j < count($LoanCase); $j++) {

            $this->transferSystemCase($request, $LoanCase[$j]->id);
        }

        return 0;


        $voucher_item = '';

        // $LedgerEntriesV2 = LedgerEntriesV2::where('type', 'BILL_DISB')->whereBetween('id', [23126, 32907])->get();
        $LedgerEntriesV2 = LedgerEntriesV2::where('type', 'BILL_DISB')->whereBetween('id', [54170, 55765])->get();

        for ($j = 0; $j < count($LedgerEntriesV2); $j++) {
            $voucher_item = '';

            $VoucherDetails = DB::table('voucher_details as a')
                ->join('loan_case_bill_details as b', 'b.id', '=', 'a.account_details_id')
                ->join('account_item as c', 'c.id', '=', 'b.account_item_id')
                ->select('a.*', 'c.name as account_name')
                ->where('voucher_main_id', '=', $LedgerEntriesV2[$j]->key_id)->get();

            if (count($VoucherDetails) > 0) {
                for ($i = 0; $i < count($VoucherDetails); $i++) {
                    $voucher_item = $voucher_item . '- ' . $VoucherDetails[$i]->account_name . '=' . number_format((float)$VoucherDetails[$i]->amount, 2, '.', ',') . '<br/>';
                }

                $LedgerEntriesV2[$j]->desc_1 = $voucher_item;
                $LedgerEntriesV2[$j]->desc_3 = $voucher_item;
                $LedgerEntriesV2[$j]->save();
            }
        }

        return $LedgerEntriesV2;

        $voucher_item = '';

        $VoucherDetails = DB::table('voucher_details as a')
            ->join('loan_case_bill_details as b', 'b.id', '=', 'a.account_details_id')
            ->join('account_item as c', 'c.id', '=', 'b.account_item_id')
            ->select('a.*', 'c.name as account_name')
            ->where('voucher_main_id', '=', 20648)->get();

        if (count($VoucherDetails) > 0) {
            for ($i = 0; $i < count($VoucherDetails); $i++) {
                $voucher_item = $voucher_item . '- ' . $VoucherDetails[$i]->account_name . '=' . number_format((float)$VoucherDetails[$i]->amount, 2, '.', ',') . '<br/>';
            }
        }

        return $voucher_item;

        $LoanCaseBillMain = LoanCaseBillMain::where('transferred_to_office_bank', 0)->where('bln_invoice', 1)->get();

        // $pf1 = number_format((float)$data->pfee1_inv, 2, '.', '');
        // $pf2 = number_format((float)$data->pfee2_inv, 2, '.', '');
        // $pftf = number_format((float)$data->transferred_pfee_amt, 2, '.', '');


        // // $bal_to_transfer = (float)($data->pfee1_inv) + (float)($data->pfee2_inv)  - (float)($data->transferred_pfee_amt);

        // $bal_to_transfer = (float)($pf1) + (float)($pf2)  - (float)($pftf);



        for ($j = 0; $j < count($LoanCaseBillMain); $j++) {

            if ($LoanCaseBillMain[$j]->transferred_pfee_amt != 0) {
                $pf1 = number_format((float)$LoanCaseBillMain[$j]->pfee1_inv, 2, '.', '');
                $pf2 = number_format((float)$LoanCaseBillMain[$j]->pfee2_inv, 2, '.', '');
                $pftf = number_format((float)$LoanCaseBillMain[$j]->transferred_pfee_amt, 2, '.', '');

                // $pf1 = round((float)$LoanCaseBillMain[$j]->pfee1_inv, 2);
                // $pf2 = round((float)$LoanCaseBillMain[$j]->pfee2_inv, 2);
                // $pftf = round((float)$LoanCaseBillMain[$j]->transferred_pfee_amt, 2);

                // $bal_to_transfer =  (float)($pf1) + (float)($pf2)  - (float)($pftf);


                $bal_to_transfer =  bcsub(bcadd($pf1, $pf2, 2), $pftf, 2);


                if ($bal_to_transfer <= 0) {
                    $LoanCaseBillMain[$j]->transferred_to_office_bank = 1;
                    $LoanCaseBillMain[$j]->save();
                }
            }
        }

        return $j;



        // for ($j = 0; $j < count($LoanCase); $j++) {

        //     $this->transferSystemCaseAdmin($request, $LoanCase[$j]->id);
        // }

        // return 'Done';

        //update latest note
        $LoanCase = LoanCase::whereBetween('id', [2001, 3000])->get();

        for ($j = 0; $j < count($LoanCase); $j++) {

            $latest_note = LoanCaseKivNotes::where('case_id', $LoanCase[$j]->id)->where('status', 1)->orderBy('created_at', 'desc')->take(1)->first();

            if ($latest_note) {
                LoanCase::where('id', $LoanCase[$j]->id)->update(['latest_notes' => $latest_note->notes]);
            }
        }

        return 'Yes';


        $SafeKeeping = Dispatch::where('created_at', '>=', '2023-09-05')->get();

        for ($j = 0; $j < count($SafeKeeping); $j++) {

            $this->compileNotesUpdatePatch($SafeKeeping[$j], 'dispatch', null, $SafeKeeping[$j]->id);
        }

        return $SafeKeeping;



        // $LoanCase = LoanCase::whereBetween('id', [1501, 2000])->get();
        $LoanCase = LoanCase::whereBetween('id', [1, 3000])->get();

        for ($j = 0; $j < count($LoanCase); $j++) {

            // $number = str_pad($j+1, 6, '0', STR_PAD_LEFT);

            // $LoanCase[$j]->client_ledger_account_code = $number;
            // $LoanCase[$j]->save();

            $this->adminUpdateClientLedger($LoanCase[$j]);
        }

        return 1;



        $BonusRequestList = BonusRequestList::where('admin_import', 1)->get();


        // $LoanCaseKivNotes = LoanCaseKivNotes::where('label', 'case_status')->where('status', 1)->where('case_id',1821)
        //     ->where('notes','like', '%update case status to PENDINGCLOSE%')->take(1)->first();

        //     return $LoanCaseKivNotes;

        for ($j = 0; $j < count($BonusRequestList); $j++) {
            $LoanCaseKivNotes = LoanCaseKivNotes::where('label', 'case_status')->where('status', 1)->where('case_id', $BonusRequestList[$j]->case_id)
                ->where('notes', 'like', '%PENDINGCLOSE%')->take(1)->first();

            // return $LoanCaseKivNotes;
            if ($LoanCaseKivNotes) {
                $BonusRequestList[$j]->created_at = $LoanCaseKivNotes->created_at;
                $BonusRequestList[$j]->admin_import = 2;
                $BonusRequestList[$j]->save();
            }
        }

        return $LoanCaseKivNotes;


        $LoanCase = LoanCase::whereBetween('id', [1501, 2000])->get();
        // $LoanCase = LoanCase::where('client_ledger_amount', '<',0)->get();
        // return $LoanCase;

        for ($j = 0; $j < count($LoanCase); $j++) {

            // $number = str_pad($j+1, 6, '0', STR_PAD_LEFT);

            // $LoanCase[$j]->client_ledger_account_code = $number;
            // $LoanCase[$j]->save();

            $this->adminUpdateClientLedger($LoanCase[$j]);
        }

        return 222;

        return $this->adminUpdateClientLedger(1492);

        return $this->adminCreateReferralBillDetails();

        return;

        // Transfer cases bulk
        $LoanCase = LoanCase::where('lawyer_id', '=', 90)->where('status', '<>', 99)->get();



        for ($j = 0; $j < count($LoanCase); $j++) {

            $this->transferSystemCase($request, $LoanCase[$j]->id);
        }

        return 1;
    }

    public static function adminUpdateClientLedger($loancase)
    {
        // $credit = DB::table('ledger_entries_v2 as a')
        //     ->leftJoin('office_bank_account as c', 'c.id', '=', 'a.bank_id')
        //     ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'a.loan_case_main_bill_id')
        //     ->leftJoin('loan_case as e', 'e.id', '=', 'a.case_id')
        //     ->leftJoin('voucher_main as f', 'f.id', '=', 'a.key_id')
        //     ->select('a.*', 'c.name as bank_name', 'c.account_no as bank_account_no', 'e.case_ref_no', 'e.id as case_id', 'f.voucher_no as voucher_no', 'f.payee as payee_voucher')
        //     ->where('e.id', '=',  $loancase->id)
        //     ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN'])
        //     ->where('a.transaction_type', 'C')
        //     ->where('a.status', '<>',  99)
        //     ->orderBy('a.date', 'ASC')
        //     ->sum('amount');


        // $debit = DB::table('ledger_entries_v2 as a')
        //     ->leftJoin('office_bank_account as c', 'c.id', '=', 'a.bank_id')
        //     ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'a.loan_case_main_bill_id')
        //     ->leftJoin('loan_case as e', 'e.id', '=', 'a.case_id')
        //     ->leftJoin('voucher_main as f', 'f.id', '=', 'a.key_id')
        //     ->select('a.*', 'c.name as bank_name', 'c.account_no as bank_account_no', 'e.case_ref_no', 'e.id as case_id', 'f.voucher_no as voucher_no', 'f.payee as payee_voucher')
        //     ->where('e.id', '=',  $loancase->id)
        //     ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN'])
        //     ->where('a.transaction_type', 'D')
        //     ->where('a.status', '<>',  99)
        //     ->orderBy('a.date', 'ASC')
        //     ->sum('amount');

        $credit = DB::table('ledger_entries_v2 as a')
            ->leftJoin('office_bank_account as c', 'c.id', '=', 'a.bank_id')
            ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'a.loan_case_main_bill_id')
            ->leftJoin('loan_case as e', 'e.id', '=', 'a.case_id')
            ->leftJoin('voucher_main as f', 'f.id', '=', 'a.key_id')
            ->select('a.*', 'c.name as bank_name', 'c.account_no as bank_account_no', 'e.case_ref_no', 'e.id as case_id', 'f.voucher_no as voucher_no', 'f.payee as payee_voucher')
            ->where('e.id', '=',  $loancase->id)
            ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN', 'ABORTFILE_IN'])
            ->where('a.transaction_type', 'C')
            ->where('a.status', '<>',  99)
            ->orderBy('a.date', 'ASC')
            ->sum('amount');


        $debit = DB::table('ledger_entries_v2 as a')
            ->leftJoin('office_bank_account as c', 'c.id', '=', 'a.bank_id')
            ->leftJoin('loan_case_bill_main as d', 'd.id', '=', 'a.loan_case_main_bill_id')
            ->leftJoin('loan_case as e', 'e.id', '=', 'a.case_id')
            ->leftJoin('voucher_main as f', 'f.id', '=', 'a.key_id')
            ->select('a.*', 'c.name as bank_name', 'c.account_no as bank_account_no', 'e.case_ref_no', 'e.id as case_id', 'f.voucher_no as voucher_no', 'f.payee as payee_voucher')
            ->where('e.id', '=',  $loancase->id)
            ->whereNotIn('a.type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN', 'ABORTFILE_IN'])
            ->where('a.transaction_type', 'D')
            ->where('a.status', '<>',  99)
            ->orderBy('a.date', 'ASC')
            ->sum('amount');



        $loancase->client_ledger_amount = $credit - $debit;
        // $loancase->client_ledger_amount_2 = $credit - $debit;
        $loancase->save();

        return;
    }

    public function adminUpdateBillSum()
    {
        // $LoanCaseBillMainAll = LoanCaseBillMain::where('status', '=', 1)->where('invoice_no', '<>', null)->get();
        $LocanCase = LoanCase::get();
        // $LocanCase = LoanCase::where('id', '=', 282)->get();

        for ($k = 0; $k < count($LocanCase); $k++) {
            $totalcount = 0;
            $LoanCaseBillMainAll = LoanCaseBillMain::where('case_id', '=', $LocanCase[$k]->id)->get();


            if (count($LoanCaseBillMainAll)) {
                for ($j = 0; $j < count($LoanCaseBillMainAll); $j++) {
                    $voucher = DB::table('voucher_main as v')
                        ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
                        ->select('v.*', 'vd.amount as vd_amt')
                        ->where('v.case_id', '=', $LoanCaseBillMainAll[$j]->case_id)
                        ->where('v.case_bill_main_id', '=', $LoanCaseBillMainAll[$j]->id)
                        ->where('v.voucher_type', '=', 1)
                        ->where('v.account_approval', '<>', 2)
                        ->where('v.status', '<>', 99)
                        ->get();

                    // return $voucher;

                    $count = 0;

                    for ($i = 0; $i < count($voucher); $i++) {
                        // echo $voucher[$i]->total_amount.'<br/>';
                        $count += $voucher[$i]->vd_amt;
                    }

                    $totalcount += $count;

                    $LoanCaseBillMainAll[$j]->used_amt = $count;
                    $LoanCaseBillMainAll[$j]->save();
                }

                $LocanCase[$k]->total_bill = $totalcount;
                $LocanCase[$k]->save();
            }
        }

        return 1;

        // $LoanCaseBillMainAll = LoanCaseBillMain::where('case_id', '=', 282)->get();



        // for ($j = 0; $j < count($LoanCaseBillMainAll); $j++) {
        //     $voucher = DB::table('voucher_main as v')
        //         ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
        //         ->select('v.*', 'vd.amount as vd_amt')
        //         ->where('v.case_id', '=', $LoanCaseBillMainAll[$j]->case_id)
        //         ->where('v.case_bill_main_id', '=', $LoanCaseBillMainAll[$j]->id)
        //         ->where('v.voucher_type', '=', 1)
        //         ->where('v.account_approval', '<>', 2)
        //         ->where('v.status', '<>', 99)
        //         ->get();

        //         // return $voucher;

        //         for ($i = 0; $i < count($voucher); $i++) 
        //         {
        //             echo $voucher[$i]->total_amount.'<br/>';
        //             $count += $voucher[$i]->name;
        //         }
        // }

        // return $count;
    }

    public function adminUpdateValue()
    {



        // update pfees, disb,sst, bill total numer
        $LoanCaseBillMainAll = LoanCaseBillMain::where('status', '=', 1)->where('invoice_no', '<>', null)->get();

        for ($j = 0; $j < count($LoanCaseBillMainAll); $j++) {
            $this->updatePfeeDisbAmount($LoanCaseBillMainAll[$j]->id);
            // $this->updatePfeeDisbAmountINV($LoanCaseBillMainAll[$j]->id);


            // $this->updateBillSummaryAllByAdmin($LoanCaseBillMainAll[$j]->id);

        }


        // // update loancase targer bill
        // $LoanCase = LoanCase::where('status', '<>', 99)->get();

        // for ($j = 0; $j < count($LoanCase); $j++) {

        //     $caseTargetBill = 0;

        //     $LoanCaseBillMainAll = LoanCaseBillMain::where('case_id', '=', $LoanCase[$j]->id)->get();

        //     for ($i = 0; $i < count($LoanCaseBillMainAll); $i++) {

        //         // $LoanCaseBillMain = LoanCaseBillMain::where('case_id', '=', $LoanCase[$j]->id)->get();

        //         $caseTargetBill += $LoanCaseBillMainAll[$i]->total_amt;
        //     }

        //     $LoanCase[$j]->targeted_bill = $caseTargetBill;
        //     $LoanCase[$j]->save();
        // }


        // update pfees, disb,sst, bill total numer
        //  $LoanCaseBillMainAll = LoanCaseBillMain::where('status', '=', 1)->get();

        //  for ($j = 0; $j < count($LoanCaseBillMainAll); $j++) {

        //  }




    }

    public function adminCreateReferralBillDetails()
    {
        $LoanCaseBillMain = LoanCaseBillMain::where('status', 1)->get();

        for ($j = 0; $j < count($LoanCaseBillMain); $j++) {



            if ($LoanCaseBillMain[$j]->referral_a1_ref_id > 0) {
                $LoanCaseBillReferrals = new LoanCaseBillReferrals();

                $LoanCaseBillReferrals->main_id = $LoanCaseBillMain[$j]->id;
                $LoanCaseBillReferrals->case_id = $LoanCaseBillMain[$j]->case_id;
                $LoanCaseBillReferrals->user_id = $LoanCaseBillMain[$j]->referral_a1_ref_id;
                $LoanCaseBillReferrals->trx_id = $LoanCaseBillMain[$j]->referral_a1_trx_id;
                $LoanCaseBillReferrals->payment_date = $LoanCaseBillMain[$j]->referral_a1_payment_date;
                $LoanCaseBillReferrals->type = 1;
                $LoanCaseBillReferrals->created_by = 1;

                $LoanCaseBillReferrals->save();
            }

            if ($LoanCaseBillMain[$j]->referral_a2_ref_id > 0) {
                $LoanCaseBillReferrals = new LoanCaseBillReferrals();

                $LoanCaseBillReferrals->main_id = $LoanCaseBillMain[$j]->id;
                $LoanCaseBillReferrals->case_id = $LoanCaseBillMain[$j]->case_id;
                $LoanCaseBillReferrals->user_id = $LoanCaseBillMain[$j]->referral_a2_ref_id;
                $LoanCaseBillReferrals->trx_id = $LoanCaseBillMain[$j]->referral_a2_trx_id;
                $LoanCaseBillReferrals->payment_date = $LoanCaseBillMain[$j]->referral_a2_payment_date;
                $LoanCaseBillReferrals->type = 2;
                $LoanCaseBillReferrals->created_by = 1;

                $LoanCaseBillReferrals->save();
            }

            if ($LoanCaseBillMain[$j]->referral_a3_ref_id > 0) {
                $LoanCaseBillReferrals = new LoanCaseBillReferrals();

                $LoanCaseBillReferrals->main_id = $LoanCaseBillMain[$j]->id;
                $LoanCaseBillReferrals->case_id = $LoanCaseBillMain[$j]->case_id;
                $LoanCaseBillReferrals->user_id = $LoanCaseBillMain[$j]->referral_a3_ref_id;
                $LoanCaseBillReferrals->trx_id = $LoanCaseBillMain[$j]->referral_a3_trx_id;
                $LoanCaseBillReferrals->payment_date = $LoanCaseBillMain[$j]->referral_a3_payment_date;
                $LoanCaseBillReferrals->type = 3;
                $LoanCaseBillReferrals->created_by = 1;

                $LoanCaseBillReferrals->save();
            }

            if ($LoanCaseBillMain[$j]->referral_a4_ref_id > 0) {
                $LoanCaseBillReferrals = new LoanCaseBillReferrals();

                $LoanCaseBillReferrals->main_id = $LoanCaseBillMain[$j]->id;
                $LoanCaseBillReferrals->case_id = $LoanCaseBillMain[$j]->case_id;
                $LoanCaseBillReferrals->user_id = $LoanCaseBillMain[$j]->referral_a4_ref_id;
                $LoanCaseBillReferrals->trx_id = $LoanCaseBillMain[$j]->referral_a4_trx_id;
                $LoanCaseBillReferrals->payment_date = $LoanCaseBillMain[$j]->referral_a4_payment_date;
                $LoanCaseBillReferrals->type = 4;
                $LoanCaseBillReferrals->created_by = 1;

                $LoanCaseBillReferrals->save();
            }

            if ($LoanCaseBillMain[$j]->marketing_id > 0) {
                $LoanCaseBillReferrals = new LoanCaseBillReferrals();

                $LoanCaseBillReferrals->main_id = $LoanCaseBillMain[$j]->id;
                $LoanCaseBillReferrals->case_id = $LoanCaseBillMain[$j]->case_id;
                $LoanCaseBillReferrals->user_id = $LoanCaseBillMain[$j]->marketing_id;
                $LoanCaseBillReferrals->trx_id = $LoanCaseBillMain[$j]->marketing_trx_id;
                $LoanCaseBillReferrals->payment_date = $LoanCaseBillMain[$j]->marketing_payment_date;
                $LoanCaseBillReferrals->type = 5;
                $LoanCaseBillReferrals->created_by = 1;

                $LoanCaseBillReferrals->save();
            }
        }

        return $LoanCaseBillMain;
    }

    public function adminUpdateReconLedger()
    {
        $LedgerEntries = LedgerEntries::whereIn('type', ['RECONADD', 'RECONLESS'])->get();

        for ($j = 0; $j < count($LedgerEntries); $j++) {

            if ($LedgerEntries[$j]->key_id_2) {
                $VoucherDetails = VoucherDetails::where('id', '=', $LedgerEntries[$j]->key_id_2)->first();

                if ($VoucherDetails) {
                    $LedgerEntries[$j]->amount = $VoucherDetails->amount;
                    $LedgerEntries[$j]->save();
                }
            }
        }

        return $LedgerEntries;
    }

    public function adminCreateLedgerRecordForBankRecon()
    {
        // $transfer_fee = DB::table('voucher_main as m')
        // ->leftJoin('voucher_details as d', 'm.id', '=', 'd.voucher_main_id')
        // ->leftJoin('loan_case_bill_details as db', 'db.id', '=', 'd.account_details_id')
        // ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
        // ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
        // ->select('m.*',  'l.case_ref_no as case_ref_no',  'l.id as case_id',  'db.loan_case_main_bill_id as bill_id')
        // ->where('m.status', '<>', 99)
        // // ->where('m.is_recon', '=', 1)
        // ->where('m.voucher_type', '=', 1)
        // ->where('m.case_bill_main_id', '=', 0)
        // ->where('m.account_approval', '=', 1)->get();

        // for ($j = 0; $j < count($transfer_fee); $j++)
        // {
        //     $voucher_main = VoucherMain::where('id', '=', $transfer_fee[$j]->id)->first();

        //     if ($voucher_main) {

        //         $voucher_main->case_bill_main_id = $transfer_fee[$j]->bill_id;
        //         $voucher_main->save();
        //     }
        // }

        // return $transfer_fee;

        $transfer_fee = DB::table('voucher_main as m')
            ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'm.id')
            ->leftJoin('loan_case_bill_details as bd', 'bd.id', '=', 'd.account_details_id')
            ->leftJoin('account_item as a', 'a.id', '=', 'bd.account_item_id')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
            ->select('m.*', 'd.amount as detail_amt',  'd.id as detail_id',  'a.name as account_item',  'l.case_ref_no as case_ref_no',  'l.id as case_id')
            ->where('m.status', '<>', 99)
            ->where('m.is_recon', '=', 1)
            ->whereIn('m.voucher_type',  [3, 4])
            ->where('m.account_approval', '=', 1)->get();

        // return $transfer_fee;

        for ($j = 0; $j < count($transfer_fee); $j++) {
            $LedgerEntries = new LedgerEntries();


            $LedgerEntries->transaction_id = $transfer_fee[$j]->transaction_id;
            $LedgerEntries->case_id = $transfer_fee[$j]->case_id;
            $LedgerEntries->loan_case_main_bill_id = $transfer_fee[$j]->case_bill_main_id;
            $LedgerEntries->user_id = $transfer_fee[$j]->created_by;
            $LedgerEntries->key_id = $transfer_fee[$j]->id;
            $LedgerEntries->key_id_2 = $transfer_fee[$j]->detail_id;
            $LedgerEntries->cheque_no = $transfer_fee[$j]->voucher_no;
            $LedgerEntries->transaction_type = $transfer_fee[$j]->voucher_type;
            $LedgerEntries->amount = $transfer_fee[$j]->detail_amt;
            $LedgerEntries->bank_id = $transfer_fee[$j]->office_account_id;
            $LedgerEntries->remark = $transfer_fee[$j]->remark;
            $LedgerEntries->payee = $transfer_fee[$j]->payee;
            $LedgerEntries->sys_desc = $transfer_fee[$j]->account_item;
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = $transfer_fee[$j]->recon_date;
            $LedgerEntries->date = $transfer_fee[$j]->recon_date;
            $LedgerEntries->type = 'RECONADD';
            $LedgerEntries->save();

            $LedgerEntries = new LedgerEntries();
        }

        $transfer_fee = DB::table('voucher_main as m')
            ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'm.id')
            ->leftJoin('loan_case_bill_details as bd', 'bd.id', '=', 'd.account_details_id')
            ->leftJoin('account_item as a', 'a.id', '=', 'bd.account_item_id')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
            ->select('m.*', 'd.amount as detail_amt',  'd.id as detail_id',  'a.name as account_item',  'l.case_ref_no as case_ref_no',  'l.id as case_id')
            ->where('m.status', '<>', 99)
            ->whereIn('m.voucher_type',  [1, 2])
            ->where('m.is_recon', '=', 1)
            ->where('m.account_approval', '=', 1)->get();

        // return $transfer_fee ;

        for ($j = 0; $j < count($transfer_fee); $j++) {
            $LedgerEntries = new LedgerEntries();

            $LedgerEntries->transaction_id = $transfer_fee[$j]->transaction_id;
            $LedgerEntries->case_id = $transfer_fee[$j]->case_id;
            $LedgerEntries->loan_case_main_bill_id = $transfer_fee[$j]->case_bill_main_id;
            $LedgerEntries->user_id = $transfer_fee[$j]->created_by;
            $LedgerEntries->key_id = $transfer_fee[$j]->id;
            $LedgerEntries->key_id_2 = $transfer_fee[$j]->detail_id;
            $LedgerEntries->cheque_no = $transfer_fee[$j]->voucher_no;
            $LedgerEntries->transaction_type = $transfer_fee[$j]->voucher_type;
            $LedgerEntries->amount = $transfer_fee[$j]->total_amount;
            $LedgerEntries->bank_id = $transfer_fee[$j]->office_account_id;
            $LedgerEntries->remark = $transfer_fee[$j]->remark;
            $LedgerEntries->payee = $transfer_fee[$j]->payee;
            $LedgerEntries->sys_desc = $transfer_fee[$j]->account_item;
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = $transfer_fee[$j]->recon_date;
            $LedgerEntries->date = $transfer_fee[$j]->recon_date;
            $LedgerEntries->type = 'RECONLESS';
            $LedgerEntries->save();

            $LedgerEntries = new LedgerEntries();
        }

        return 1;
    }


    public function adminUpdateLedgerV2()
    {
        // // Journal Entry========================================================================

        // $ledgers = DB::table('journal_entry_details as m')
        //     ->leftJoin('journal_entry_main as m1', 'm1.id', '=', 'm.journal_entry_main_id')
        //     ->leftJoin('account_code as c', 'c.id', '=', 'm.account_code_id')
        //     ->select('m.*', 'm1.id as journal_main_id', 'c.key_id as office_bank_account_id', 'm1.case_id as main_case_id', 'm1.transaction_id', 'm1.journal_no', 
        //     'm1.created_by as created_user', 'm1.name', 'm1.remarks as main_remarks', 'm1.date as main_date')
        //     ->where('m.status', '<>', 99)
        //     ->orderBy('m.created_at', 'ASC')
        //     ->get();



        //     for ($j = 0; $j < count($ledgers); $j++) {
        //         $LedgerEntries = new LedgerEntriesV2();

        //         $transaction = '';
        //         $type = '';

        //         if ($ledgers[$j]->transaction_id != null) {
        //             $transaction = $ledgers[$j]->transaction_id;
        //         }

        //         if ($ledgers[$j]->transaction_type == 'D') {
        //             $type = 'JOURNAL_IN';
        //         }
        //         else
        //         {
        //             $type = 'JOURNAL_OUT';
        //         }

        //         $LedgerEntries->transaction_id = $ledgers[$j]->transaction_id;
        //         $LedgerEntries->case_id = $ledgers[$j]->main_case_id;
        //         $LedgerEntries->loan_case_main_bill_id = 0;
        //         $LedgerEntries->cheque_no = $ledgers[$j]->journal_no;
        //         $LedgerEntries->user_id = $ledgers[$j]->created_user;
        //         $LedgerEntries->key_id = $ledgers[$j]->journal_main_id;
        //         $LedgerEntries->key_id_2 = $ledgers[$j]->id;
        //         $LedgerEntries->key_id_3 = $ledgers[$j]->account_code_id;
        //         $LedgerEntries->transaction_type = $ledgers[$j]->transaction_type;
        //         $LedgerEntries->amount = $ledgers[$j]->amount + $ledgers[$j]->sst_amount;
        //         $LedgerEntries->bank_id = $ledgers[$j]->office_bank_account_id;
        //         // $LedgerEntries->remark = $ledgers[$j]->remark;
        //         $LedgerEntries->payee = $ledgers[$j]->name;
        //         $LedgerEntries->remark = $ledgers[$j]->main_remarks;
        //         $LedgerEntries->desc_1 = $ledgers[$j]->remarks;
        //         // $LedgerEntries->desc_2 = $ledgers[$j]->remark;
        //         $LedgerEntries->status = 1;
        //         $LedgerEntries->is_recon = 0;
        //         $LedgerEntries->created_at = $ledgers[$j]->created_at;
        //         $LedgerEntries->date = $ledgers[$j]->main_date;
        //         $LedgerEntries->type =  $type ;
        //         $LedgerEntries->save();
        //     }





        // // Transfer Fee ========================================================================
        // $ledgers = DB::table('transfer_fee_details as m')
        //     ->leftJoin('transfer_fee_main as m1', 'm1.id', '=', 'm.transfer_fee_main_id')
        //     ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'm.loan_case_main_bill_id')
        //     ->select('m.*', 'm1.transfer_date', 'm1.transaction_id', 'm1.transfer_from', 'm1.transfer_to', 'b.case_id', 'm1.purpose', 'm1.is_recon as main_is_recon')
        //     ->where('m.status', '<>', 99)
        //     ->where('m1.status', '<>', null)
        //     ->orderBy('m.created_at', 'ASC')
        //     ->get();

        //     // return $ledgers;


        // for ($j = 0; $j < count($ledgers); $j++) {
        //     $LedgerEntries = new LedgerEntriesV2();

        //     $transaction = '';
        //     $type = '';


        //     $LedgerEntries->transaction_id = $ledgers[$j]->transaction_id;
        //     $LedgerEntries->case_id = $ledgers[$j]->case_id;
        //     $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->loan_case_main_bill_id;
        //     $LedgerEntries->user_id = $ledgers[$j]->created_by;
        //     $LedgerEntries->key_id = $ledgers[$j]->transfer_fee_main_id;
        //     $LedgerEntries->key_id_2 = $ledgers[$j]->id;
        //     $LedgerEntries->transaction_type = 'C';
        //     $LedgerEntries->amount = $ledgers[$j]->transfer_amount;
        //     $LedgerEntries->bank_id = $ledgers[$j]->transfer_from;
        //     $LedgerEntries->remark = $ledgers[$j]->purpose;
        //     $LedgerEntries->status = 1;
        //     $LedgerEntries->is_recon = $ledgers[$j]->main_is_recon;
        //     $LedgerEntries->created_at = $ledgers[$j]->created_at;
        //     $LedgerEntries->date = $ledgers[$j]->transfer_date;
        //     $LedgerEntries->type =  'TRANSFER_OUT';
        //     $LedgerEntries->save();


        //     $LedgerEntries = new LedgerEntriesV2();

        //     $LedgerEntries->transaction_id = $ledgers[$j]->transaction_id;
        //     $LedgerEntries->case_id = $ledgers[$j]->case_id;
        //     $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->loan_case_main_bill_id;
        //     $LedgerEntries->user_id = $ledgers[$j]->created_by;
        //     $LedgerEntries->key_id = $ledgers[$j]->transfer_fee_main_id;
        //     $LedgerEntries->key_id_2 = $ledgers[$j]->id;
        //     $LedgerEntries->transaction_type = 'C';
        //     $LedgerEntries->amount = $ledgers[$j]->sst_amount;
        //     $LedgerEntries->bank_id = $ledgers[$j]->transfer_from;
        //     $LedgerEntries->remark = $ledgers[$j]->purpose;
        //     $LedgerEntries->status = 1;
        //     $LedgerEntries->is_recon = $ledgers[$j]->main_is_recon;
        //     $LedgerEntries->created_at = $ledgers[$j]->created_at;
        //     $LedgerEntries->date = $ledgers[$j]->transfer_date;
        //     $LedgerEntries->type =  'SST_OUT';
        //     $LedgerEntries->save();


        //     $LedgerEntries = new LedgerEntriesV2();

        //     $LedgerEntries->transaction_id = $ledgers[$j]->transaction_id;
        //     $LedgerEntries->case_id = $ledgers[$j]->case_id;
        //     $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->loan_case_main_bill_id;
        //     $LedgerEntries->user_id = $ledgers[$j]->created_by;
        //     $LedgerEntries->key_id = $ledgers[$j]->transfer_fee_main_id;
        //     $LedgerEntries->key_id_2 = $ledgers[$j]->id;
        //     $LedgerEntries->transaction_type = 'D';
        //     $LedgerEntries->amount = $ledgers[$j]->transfer_amount;
        //     $LedgerEntries->bank_id = $ledgers[$j]->transfer_to;
        //     $LedgerEntries->remark = $ledgers[$j]->purpose;
        //     $LedgerEntries->status = 1;
        //     $LedgerEntries->is_recon = $ledgers[$j]->main_is_recon;
        //     $LedgerEntries->created_at = $ledgers[$j]->created_at;
        //     $LedgerEntries->date = $ledgers[$j]->transfer_date;
        //     $LedgerEntries->type =  'TRANSFER_IN';
        //     $LedgerEntries->save();


        //     $LedgerEntries = new LedgerEntriesV2();

        //     $LedgerEntries->transaction_id = $ledgers[$j]->transaction_id;
        //     $LedgerEntries->case_id = $ledgers[$j]->case_id;
        //     $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->loan_case_main_bill_id;
        //     $LedgerEntries->user_id = $ledgers[$j]->created_by;
        //     $LedgerEntries->key_id = $ledgers[$j]->transfer_fee_main_id;
        //     $LedgerEntries->key_id_2 = $ledgers[$j]->id;
        //     $LedgerEntries->transaction_type = 'D';
        //     $LedgerEntries->amount = $ledgers[$j]->sst_amount;
        //     $LedgerEntries->bank_id = $ledgers[$j]->transfer_to;
        //     $LedgerEntries->remark = $ledgers[$j]->purpose;
        //     $LedgerEntries->status = 1;
        //     $LedgerEntries->is_recon = $ledgers[$j]->main_is_recon;
        //     $LedgerEntries->created_at = $ledgers[$j]->created_at;
        //     $LedgerEntries->date = $ledgers[$j]->transfer_date;
        //     $LedgerEntries->type =  'SST_IN';
        //     $LedgerEntries->save();
        // }



        // // Close File ========================================================================

        // $ledgers = DB::table('ledger_entries as m')
        //     ->select('m.*')
        //     ->where('type', 'CLOSEFILEOUT')
        //     ->orderBy('m.created_at', 'ASC')
        //     ->get();

        // for ($j = 0; $j < count($ledgers); $j++) {
        //     $LedgerEntries = new LedgerEntriesV2();

        //     $LedgerEntries->transaction_id = $ledgers[$j]->transaction_id;
        //     $LedgerEntries->case_id = $ledgers[$j]->case_id;
        //     $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->loan_case_main_bill_id;
        //     $LedgerEntries->user_id = $ledgers[$j]->user_id;
        //     $LedgerEntries->transaction_type = 'C';
        //     $LedgerEntries->amount = $ledgers[$j]->amount;
        //     $LedgerEntries->bank_id = $ledgers[$j]->bank_id;
        //     $LedgerEntries->remark = $ledgers[$j]->remark;
        //     $LedgerEntries->status = 1;
        //     $LedgerEntries->is_recon = 0;
        //     $LedgerEntries->created_at = $ledgers[$j]->created_at;
        //     $LedgerEntries->date = $ledgers[$j]->date;
        //     $LedgerEntries->type =  'CLOSEFILE_OUT';
        //     $LedgerEntries->save();
        // }

        // $ledgers = DB::table('ledger_entries as m')
        //     ->select('m.*')
        //     ->where('type', 'CLOSEFILEIN')
        //     ->orderBy('m.created_at', 'ASC')
        //     ->get();

        // for ($j = 0; $j < count($ledgers); $j++) {
        //     $LedgerEntries = new LedgerEntriesV2();

        //     $LedgerEntries->transaction_id = $ledgers[$j]->transaction_id;
        //     $LedgerEntries->case_id = $ledgers[$j]->case_id;
        //     $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->loan_case_main_bill_id;
        //     $LedgerEntries->user_id = $ledgers[$j]->user_id;
        //     $LedgerEntries->transaction_type = 'D';
        //     $LedgerEntries->amount = $ledgers[$j]->amount;
        //     $LedgerEntries->bank_id = $ledgers[$j]->bank_id;
        //     $LedgerEntries->remark = $ledgers[$j]->remark;
        //     $LedgerEntries->status = 1;
        //     $LedgerEntries->is_recon = 0;
        //     $LedgerEntries->created_at = $ledgers[$j]->created_at;
        //     $LedgerEntries->date = $ledgers[$j]->date;
        //     $LedgerEntries->type =  'CLOSEFILE_IN';
        //     $LedgerEntries->save();
        // }

        // //receive bill ========================================================================
        // $ledgers = DB::table('voucher_main as m')
        //     ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
        //     ->select('m.*', 'a.name as account_name', 'm.id as voucher_id')
        //     ->select('m.*', 'm.id as voucher_id', 'm.total_amount as amount', 'b.name as bank_account', 'b.account_no as bank_account_no')
        //     ->where('m.account_approval', '=', 1)
        //     ->where('m.voucher_type', '=', 4)
        //     ->where('m.status', '<>', 99)
        //     ->orderBy('m.payment_date', 'ASC')
        //     ->get();

        // for ($j = 0; $j < count($ledgers); $j++) {
        //     $LedgerEntries = new LedgerEntriesV2();

        //     $transaction = '';

        //     if ($ledgers[$j]->transaction_id != null) {
        //         $transaction = $ledgers[$j]->transaction_id;
        //     }

        //     $LedgerEntries->transaction_id = $transaction;
        //     $LedgerEntries->case_id = $ledgers[$j]->case_id;
        //     $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->case_bill_main_id;
        //     $LedgerEntries->cheque_no = $ledgers[$j]->voucher_no;
        //     $LedgerEntries->user_id = $ledgers[$j]->user_id;
        //     $LedgerEntries->key_id = $ledgers[$j]->id;
        //     $LedgerEntries->transaction_type = 'D';
        //     $LedgerEntries->amount = $ledgers[$j]->total_amount;
        //     $LedgerEntries->bank_id = $ledgers[$j]->office_account_id;
        //     $LedgerEntries->remark = $ledgers[$j]->remark;
        //     $LedgerEntries->payee = $ledgers[$j]->payee;
        //     $LedgerEntries->is_recon = $ledgers[$j]->is_recon;
        //     $LedgerEntries->status = 1;
        //     $LedgerEntries->created_at = date('Y-m-d H:i:s');
        //     $LedgerEntries->date = $ledgers[$j]->payment_date;
        //     $LedgerEntries->type = 'BILL_RECV';
        //     $LedgerEntries->save();
        // }



        // //disb bill==============================================================================================================
        // $ledgers = DB::table('voucher_main as m')
        //     ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
        //     ->select('m.*', 'a.name as account_name', 'm.id as voucher_id')
        //     ->select('m.*', 'm.id as voucher_id', 'm.total_amount as amount', 'b.name as bank_account', 'b.account_no as bank_account_no')
        //     ->where('m.account_approval', '=', 1)
        //     ->where('m.voucher_type', '=', 1)
        //     ->where('m.status', '<>', 99)
        //     ->orderBy('m.payment_date', 'ASC')
        //     ->get();

        // for ($j = 0; $j < count($ledgers); $j++) {
        //     $LedgerEntries = new LedgerEntriesV2();

        //     $transaction = '';
        //     $voucher_item = '';

        //     if ($ledgers[$j]->transaction_id != null) {
        //         $transaction = $ledgers[$j]->transaction_id;
        //     }

        //     //   $VoucherDetails = VoucherDetails::where('voucher_main_id', '=', $ledgers[$j]->id)->get();

        //     $VoucherDetails = DB::table('voucher_details as a')
        //         ->join('loan_case_bill_details as b', 'b.id', '=', 'a.account_details_id')
        //         ->join('account_item as c', 'c.id', '=', 'b.account_item_id')
        //         ->select('a.*', 'c.name as account_name')
        //         ->where('voucher_main_id', '=', $ledgers[$j]->id)->get();

        //     if (count($VoucherDetails) > 0) {
        //         for ($i = 0; $i < count($VoucherDetails); $i++) {
        //             $voucher_item = $voucher_item . '- ' . $VoucherDetails[$i]->account_name . '=' . number_format((float)$VoucherDetails[$i]->amount, 2, '.', ',') . '<br/>';
        //         }
        //     }

        //     $LedgerEntries->transaction_id = $transaction;
        //     $LedgerEntries->case_id = $ledgers[$j]->case_id;
        //     $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->case_bill_main_id;
        //     $LedgerEntries->user_id = $ledgers[$j]->user_id;
        //     $LedgerEntries->cheque_no = $ledgers[$j]->voucher_no;
        //     $LedgerEntries->key_id = $ledgers[$j]->id;
        //     $LedgerEntries->transaction_type = 'C';
        //     $LedgerEntries->amount = $ledgers[$j]->total_amount;
        //     $LedgerEntries->bank_id = $ledgers[$j]->office_account_id;
        //     $LedgerEntries->remark = $ledgers[$j]->remark;
        //     $LedgerEntries->desc_1 = $voucher_item;
        //     $LedgerEntries->payee = $ledgers[$j]->payee;
        //     $LedgerEntries->is_recon = $ledgers[$j]->is_recon;
        //     $LedgerEntries->recon_date = $ledgers[$j]->recon_date;
        //     $LedgerEntries->status = 1;
        //     $LedgerEntries->created_at = date('Y-m-d H:i:s');
        //     $LedgerEntries->date = $ledgers[$j]->payment_date;
        //     $LedgerEntries->type = 'BILL_DISB';
        //     $LedgerEntries->save();
        // }



        // //trust disb==============================================================================================================
        // $ledgers = DB::table('voucher_main as m')
        //     ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
        //     ->select('m.*', 'a.name as account_name', 'm.id as voucher_id')
        //     ->select('m.*', 'm.id as voucher_id', 'm.total_amount as amount', 'b.name as bank_account', 'b.account_no as bank_account_no')
        //     ->where('m.account_approval', '=', 1)
        //     ->where('m.voucher_type', '=', 2)
        //     ->where('m.status', '<>', 99)
        //     ->orderBy('m.payment_date', 'ASC')
        //     ->get();

        // for ($j = 0; $j < count($ledgers); $j++) {
        //     $LedgerEntries = new LedgerEntriesV2();

        //     $transaction = '';

        //     if ($ledgers[$j]->transaction_id != null) {
        //         $transaction = $ledgers[$j]->transaction_id;
        //     }

        //     $LedgerEntries->transaction_id = $transaction;
        //     $LedgerEntries->case_id = $ledgers[$j]->case_id;
        //     $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->case_bill_main_id;
        //     $LedgerEntries->user_id = $ledgers[$j]->user_id;
        //     $LedgerEntries->cheque_no = $ledgers[$j]->voucher_no;
        //     $LedgerEntries->key_id = $ledgers[$j]->id;
        //     $LedgerEntries->transaction_type = 'C';
        //     $LedgerEntries->amount = $ledgers[$j]->total_amount;
        //     $LedgerEntries->bank_id = $ledgers[$j]->office_account_id;
        //     $LedgerEntries->remark = $ledgers[$j]->remark;
        //     $LedgerEntries->payee = $ledgers[$j]->payee;
        //     $LedgerEntries->is_recon = $ledgers[$j]->is_recon;
        //     $LedgerEntries->recon_date = $ledgers[$j]->recon_date;
        //     $LedgerEntries->status = 1;
        //     $LedgerEntries->created_at = date('Y-m-d H:i:s');
        //     $LedgerEntries->date = $ledgers[$j]->payment_date;
        //     $LedgerEntries->type = 'TRUST_DISB';
        //     $LedgerEntries->save();
        // }


        //trust recv==============================================================================================================
        $ledgers = DB::table('voucher_main as m')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
            ->select('m.*', 'a.name as account_name', 'm.id as voucher_id')
            ->select('m.*', 'm.id as voucher_id', 'm.total_amount as amount', 'b.name as bank_account', 'b.account_no as bank_account_no')
            ->where('m.account_approval', '=', 1)
            ->where('m.voucher_type', '=', 3)
            ->where('m.status', '<>', 99)
            ->orderBy('m.payment_date', 'ASC')
            ->get();

        for ($j = 0; $j < count($ledgers); $j++) {
            $LedgerEntries = new LedgerEntriesV2();

            $LedgerEntries->transaction_id = $ledgers[$j]->transaction_id;
            $LedgerEntries->case_id = $ledgers[$j]->case_id;
            $LedgerEntries->loan_case_main_bill_id = 0;
            $LedgerEntries->user_id = $ledgers[$j]->user_id;
            $LedgerEntries->cheque_no = $ledgers[$j]->voucher_no;
            $LedgerEntries->key_id = $ledgers[$j]->id;
            $LedgerEntries->transaction_type = 'D';
            $LedgerEntries->amount = $ledgers[$j]->total_amount;
            $LedgerEntries->bank_id = $ledgers[$j]->office_account_id;
            $LedgerEntries->remark = $ledgers[$j]->remark;
            $LedgerEntries->payee = $ledgers[$j]->payee;
            $LedgerEntries->is_recon = $ledgers[$j]->is_recon;
            $LedgerEntries->recon_date = $ledgers[$j]->recon_date;
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $ledgers[$j]->payment_date;
            $LedgerEntries->type = 'TRUST_RECV';
            $LedgerEntries->save();
        }

        return $ledgers;

        // $transfer_fee = DB::table('voucher_main as m')
        //     ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'm.id')
        //     ->leftJoin('loan_case_bill_details as bd', 'bd.id', '=', 'd.account_details_id')
        //     ->leftJoin('account_item as a', 'a.id', '=', 'bd.account_item_id')
        //     ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
        //     ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
        //     ->select('m.*', 'd.amount as detail_amt',  'd.id as detail_id',  'a.name as account_item',  'l.case_ref_no as case_ref_no',  'l.id as case_id')
        //     ->where('m.status', '<>', 99)
        //     ->where('m.is_recon', '=', 1)
        //     ->whereIn('m.voucher_type',  [3, 4])
        //     ->where('m.account_approval', '=', 1)->get();

        // // return $transfer_fee;

        // for ($j = 0; $j < count($transfer_fee); $j++) {
        //     $LedgerEntries = new LedgerEntriesV2();


        //     $LedgerEntries->transaction_id = $transfer_fee[$j]->transaction_id;
        //     $LedgerEntries->case_id = $transfer_fee[$j]->case_id;
        //     $LedgerEntries->loan_case_main_bill_id = $transfer_fee[$j]->case_bill_main_id;
        //     $LedgerEntries->user_id = $transfer_fee[$j]->created_by;
        //     $LedgerEntries->key_id = $transfer_fee[$j]->id;
        //     $LedgerEntries->key_id_2 = $transfer_fee[$j]->detail_id;
        //     $LedgerEntries->cheque_no = $transfer_fee[$j]->voucher_no;
        //     $LedgerEntries->transaction_type = $transfer_fee[$j]->voucher_type;
        //     $LedgerEntries->amount = $transfer_fee[$j]->detail_amt;
        //     $LedgerEntries->bank_id = $transfer_fee[$j]->office_account_id;
        //     $LedgerEntries->remark = $transfer_fee[$j]->remark;
        //     $LedgerEntries->payee = $transfer_fee[$j]->payee;
        //     $LedgerEntries->sys_desc = $transfer_fee[$j]->account_item;
        //     $LedgerEntries->status = 1;
        //     $LedgerEntries->created_at = $transfer_fee[$j]->recon_date;
        //     $LedgerEntries->date = $transfer_fee[$j]->recon_date;
        //     $LedgerEntries->type = 'RECONADD';
        //     $LedgerEntries->save();
        // }

        // $transfer_fee = DB::table('voucher_main as m')
        //     ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'm.id')
        //     ->leftJoin('loan_case_bill_details as bd', 'bd.id', '=', 'd.account_details_id')
        //     ->leftJoin('account_item as a', 'a.id', '=', 'bd.account_item_id')
        //     ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
        //     ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
        //     ->select('m.*', 'd.amount as detail_amt',  'd.id as detail_id',  'a.name as account_item',  'l.case_ref_no as case_ref_no',  'l.id as case_id')
        //     ->where('m.status', '<>', 99)
        //     ->whereIn('m.voucher_type',  [1, 2])
        //     ->where('m.is_recon', '=', 1)
        //     ->where('m.account_approval', '=', 1)->get();

        // // return $transfer_fee ;

        // for ($j = 0; $j < count($transfer_fee); $j++) {
        //     $LedgerEntries = new LedgerEntriesV2();

        //     $LedgerEntries->transaction_id = $transfer_fee[$j]->transaction_id;
        //     $LedgerEntries->case_id = $transfer_fee[$j]->case_id;
        //     $LedgerEntries->loan_case_main_bill_id = $transfer_fee[$j]->case_bill_main_id;
        //     $LedgerEntries->user_id = $transfer_fee[$j]->created_by;
        //     $LedgerEntries->key_id = $transfer_fee[$j]->id;
        //     $LedgerEntries->key_id_2 = $transfer_fee[$j]->detail_id;
        //     $LedgerEntries->cheque_no = $transfer_fee[$j]->voucher_no;
        //     $LedgerEntries->transaction_type = $transfer_fee[$j]->voucher_type;
        //     $LedgerEntries->amount = $transfer_fee[$j]->total_amount;
        //     $LedgerEntries->bank_id = $transfer_fee[$j]->office_account_id;
        //     $LedgerEntries->remark = $transfer_fee[$j]->remark;
        //     $LedgerEntries->payee = $transfer_fee[$j]->payee;
        //     $LedgerEntries->sys_desc = $transfer_fee[$j]->account_item;
        //     $LedgerEntries->status = 1;
        //     $LedgerEntries->created_at = $transfer_fee[$j]->recon_date;
        //     $LedgerEntries->date = $transfer_fee[$j]->recon_date;
        //     $LedgerEntries->type = 'RECONLESS';
        //     $LedgerEntries->save();

        //     $LedgerEntries = new LedgerEntries();
        // }


        //====================================================================

        $ledgers = DB::table('voucher_main as m')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
            ->select('m.*', 'a.name as account_name', 'm.id as voucher_id')
            ->select('m.*', 'm.id as voucher_id', 'm.total_amount as amount', 'b.name as bank_account', 'b.account_no as bank_account_no')
            ->where('m.account_approval', '=', 1)
            ->where('m.voucher_type', '=', 1)
            ->where('m.status', '<>', 99)
            ->orderBy('m.payment_date', 'ASC')
            ->get();

        for ($j = 0; $j < count($ledgers); $j++) {
            $LedgerEntries = new LedgerEntriesV2();

            $transaction = '';

            if ($ledgers[$j]->transaction_id != null) {
                $transaction = $ledgers[$j]->transaction_id;
            }

            $LedgerEntries->transaction_id = $transaction;
            $LedgerEntries->case_id = $ledgers[$j]->case_id;
            $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->case_bill_main_id;
            $LedgerEntries->cheque_no = $ledgers[$j]->voucher_no;
            $LedgerEntries->user_id = $ledgers[$j]->user_id;
            $LedgerEntries->key_id = $ledgers[$j]->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $ledgers[$j]->total_amount;
            $LedgerEntries->bank_id = $ledgers[$j]->office_account_id;
            // $LedgerEntries->remark = $ledgers[$j]->remark;
            $LedgerEntries->payee = $ledgers[$j]->payee;
            $LedgerEntries->desc_1 = $ledgers[$j]->remark;
            $LedgerEntries->status = 1;
            $LedgerEntries->is_recon = $ledgers[$j]->is_recon;
            $LedgerEntries->recon_date = $ledgers[$j]->recon_date;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $ledgers[$j]->payment_date;
            $LedgerEntries->type = 'BILLDISB';
            $LedgerEntries->save();
        }

        return 1;

        $ledgers = DB::table('voucher_main as m')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
            ->select('m.*', 'a.name as account_name', 'm.id as voucher_id')
            ->select('m.*', 'm.id as voucher_id', 'm.total_amount as amount', 'b.name as bank_account', 'b.account_no as bank_account_no')
            ->where('m.account_approval', '=', 1)
            ->where('m.voucher_type', '=', 2)
            ->where('m.status', '<>', 99)
            ->orderBy('m.payment_date', 'ASC')
            ->get();

        for ($j = 0; $j < count($ledgers); $j++) {
            $LedgerEntries = new LedgerEntries();

            $transaction = '';

            if ($ledgers[$j]->transaction_id != null) {
                $transaction = $ledgers[$j]->transaction_id;
            }

            $LedgerEntries->transaction_id = $transaction;
            $LedgerEntries->case_id = $ledgers[$j]->case_id;
            $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->case_bill_main_id;
            $LedgerEntries->user_id = $ledgers[$j]->user_id;
            $LedgerEntries->key_id = $ledgers[$j]->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $ledgers[$j]->total_amount;
            $LedgerEntries->bank_id = $ledgers[$j]->office_account_id;
            $LedgerEntries->remark = $ledgers[$j]->remark;
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $ledgers[$j]->payment_date;
            $LedgerEntries->type = 'TRUSTDISB';
            $LedgerEntries->save();
        }

        //receive bill
        $ledgers = DB::table('voucher_main as m')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
            ->select('m.*', 'a.name as account_name', 'm.id as voucher_id')
            ->select('m.*', 'm.id as voucher_id', 'm.total_amount as amount', 'b.name as bank_account', 'b.account_no as bank_account_no')
            ->where('m.account_approval', '=', 1)
            ->where('m.voucher_type', '=', 4)
            ->where('m.status', '<>', 99)
            ->orderBy('m.payment_date', 'ASC')
            ->get();

        for ($j = 0; $j < count($ledgers); $j++) {
            $LedgerEntries = new LedgerEntries();

            $transaction = '';

            if ($ledgers[$j]->transaction_id != null) {
                $transaction = $ledgers[$j]->transaction_id;
            }

            $LedgerEntries->transaction_id = $transaction;
            $LedgerEntries->case_id = $ledgers[$j]->case_id;
            $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->case_bill_main_id;
            $LedgerEntries->user_id = $ledgers[$j]->user_id;
            $LedgerEntries->key_id = $ledgers[$j]->id;
            $LedgerEntries->transaction_type = 'D';
            $LedgerEntries->amount = $ledgers[$j]->total_amount;
            $LedgerEntries->bank_id = $ledgers[$j]->office_account_id;
            $LedgerEntries->remark = $ledgers[$j]->remark;
            // $LedgerEntries->sys_desc = 'Trust Acc Payment';
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $ledgers[$j]->payment_date;
            $LedgerEntries->type = 'BILLRECEIVE';
            $LedgerEntries->save();
        }

        //disb bill
        $ledgers = DB::table('voucher_main as m')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
            ->select('m.*', 'a.name as account_name', 'm.id as voucher_id')
            ->select('m.*', 'm.id as voucher_id', 'm.total_amount as amount', 'b.name as bank_account', 'b.account_no as bank_account_no')
            ->where('m.account_approval', '=', 1)
            ->where('m.voucher_type', '=', 1)
            ->where('m.status', '<>', 99)
            ->orderBy('m.payment_date', 'ASC')
            ->get();

        for ($j = 0; $j < count($ledgers); $j++) {
            $LedgerEntries = new LedgerEntries();

            $transaction = '';
            $voucher_item = '';

            if ($ledgers[$j]->transaction_id != null) {
                $transaction = $ledgers[$j]->transaction_id;
            }

            //   $VoucherDetails = VoucherDetails::where('voucher_main_id', '=', $ledgers[$j]->id)->get();

            $VoucherDetails = DB::table('voucher_details as a')
                ->join('loan_case_bill_details as b', 'b.id', '=', 'a.account_details_id')
                ->join('account_item as c', 'c.id', '=', 'b.account_item_id')
                //   ->leftJoin('account_item as b', 'b.id', '=', 'a.account_item_id')
                ->select('a.*', 'c.name as account_name')
                ->where('voucher_main_id', '=', $ledgers[$j]->id)->get();

            if (count($VoucherDetails) > 0) {
                for ($i = 0; $i < count($VoucherDetails); $i++) {
                    $voucher_item = $voucher_item . '- ' . $VoucherDetails[$i]->account_name . '=' . number_format((float)$VoucherDetails[$i]->amount, 2, '.', ',') . '<br/>';
                }
            }

            $LedgerEntries->transaction_id = $transaction;
            $LedgerEntries->case_id = $ledgers[$j]->case_id;
            $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->case_bill_main_id;
            $LedgerEntries->user_id = $ledgers[$j]->user_id;
            $LedgerEntries->key_id = $ledgers[$j]->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $ledgers[$j]->total_amount;
            $LedgerEntries->bank_id = $ledgers[$j]->office_account_id;
            $LedgerEntries->remark = $ledgers[$j]->remark;
            $LedgerEntries->sys_desc = $voucher_item;
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $ledgers[$j]->payment_date;
            $LedgerEntries->type = 'BILLDISB';
            $LedgerEntries->save();
        }
    }

    public function adminMigrateLedger()
    {

        return $this->updateUsedQuotationAmount();
        return $this->updateTransferSSTAmount();
        return $this->updateBillTransferAmount();
        return $this->checkTrustLedger();
        return $this->adminUpdateReconLedger();
        // return $this->adminCreateLedgerRecordForBankRecon();

        return;
        // $transfer_fee = DB::table('transfer_fee_main as m')
        //     ->leftJoin('transfer_fee_details as d', 'm.id', '=', 'd.transfer_fee_main_id')
        //     ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'd.loan_case_main_bill_id')
        //     ->leftJoin('office_bank_account as o', 'o.id', '=', 'm.transfer_from')
        //     ->select('m.*', 'd.transfer_amount', 'o.name as bank_name', 'b.pfee1_inv', 'b.pfee2_inv', 'b.sst_inv', 'o.account_no as bank_account_no')
        //     ->where('b.case_id', '=', $id)
        //     ->where('m.status', '<>', 99)
        //     ->get();

        $transfer_fee = DB::table('transfer_fee_details as a')
            ->leftJoin('transfer_fee_main as b', 'b.id', '=', 'a.transfer_fee_main_id')
            ->leftJoin('loan_case_bill_main as c', 'c.id', '=', 'a.loan_case_main_bill_id')
            ->leftJoin('office_bank_account as o', 'o.id', '=', 'b.transfer_from')
            ->select('a.*', 'b.transaction_id', 'o.name as bank_name', 'c.pfee1_inv', 'c.pfee2_inv', 'c.sst_inv', 'o.account_no as bank_account_no', 'c.case_id', 'b.purpose', 'b.transfer_date', 'b.transfer_by', 'b.transfer_to', 'b.transfer_from')
            ->where('a.status', '<>', 99)
            ->where('b.id', '<>', null)
            ->get();

        for ($j = 0; $j < count($transfer_fee); $j++) {

            // transfer out

            $LedgerEntries = new LedgerEntries();

            $LedgerEntries->transaction_id = $transfer_fee[$j]->transaction_id;
            $LedgerEntries->case_id = $transfer_fee[$j]->case_id;
            $LedgerEntries->loan_case_main_bill_id = $transfer_fee[$j]->loan_case_main_bill_id;
            $LedgerEntries->user_id = $transfer_fee[$j]->transfer_by;
            $LedgerEntries->key_id = $transfer_fee[$j]->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $transfer_fee[$j]->pfee1_inv + $transfer_fee[$j]->pfee2_inv;
            $LedgerEntries->bank_id = $transfer_fee[$j]->transfer_from;
            $LedgerEntries->remark = $transfer_fee[$j]->purpose;
            // $LedgerEntries->sys_desc = 'Transfer of Pro Fees to OA';
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $transfer_fee[$j]->transfer_date;
            $LedgerEntries->type = 'TRANSFEROUT';
            $LedgerEntries->save();

            $LedgerEntries = new LedgerEntries();

            $LedgerEntries->transaction_id = $transfer_fee[$j]->transaction_id;
            $LedgerEntries->case_id = $transfer_fee[$j]->case_id;
            $LedgerEntries->loan_case_main_bill_id = $transfer_fee[$j]->loan_case_main_bill_id;
            $LedgerEntries->user_id = $transfer_fee[$j]->transfer_by;
            $LedgerEntries->key_id = $transfer_fee[$j]->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $transfer_fee[$j]->sst_inv;
            $LedgerEntries->bank_id = $transfer_fee[$j]->transfer_from;
            $LedgerEntries->remark = $transfer_fee[$j]->purpose;
            // $LedgerEntries->sys_desc = 'Transfer of G/SST to OA';
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $transfer_fee[$j]->transfer_date;
            $LedgerEntries->type = 'SSTOUT';
            $LedgerEntries->save();


            // transfer in

            $LedgerEntries = new LedgerEntries();

            $LedgerEntries->transaction_id = $transfer_fee[$j]->transaction_id;
            $LedgerEntries->case_id = $transfer_fee[$j]->case_id;
            $LedgerEntries->loan_case_main_bill_id = $transfer_fee[$j]->loan_case_main_bill_id;
            $LedgerEntries->user_id = $transfer_fee[$j]->transfer_by;
            $LedgerEntries->key_id = $transfer_fee[$j]->id;
            $LedgerEntries->transaction_type = 'D';
            $LedgerEntries->amount = $transfer_fee[$j]->pfee1_inv + $transfer_fee[$j]->pfee2_inv;
            $LedgerEntries->bank_id = $transfer_fee[$j]->transfer_to;
            $LedgerEntries->remark = $transfer_fee[$j]->purpose;
            // $LedgerEntries->sys_desc = 'Transfer of Pro Fees to OA';
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $transfer_fee[$j]->transfer_date;
            $LedgerEntries->type = 'TRANSFERIN';
            $LedgerEntries->save();

            $LedgerEntries = new LedgerEntries();

            $LedgerEntries->transaction_id = $transfer_fee[$j]->transaction_id;
            $LedgerEntries->case_id = $transfer_fee[$j]->case_id;
            $LedgerEntries->loan_case_main_bill_id = $transfer_fee[$j]->loan_case_main_bill_id;
            $LedgerEntries->user_id = $transfer_fee[$j]->transfer_by;
            $LedgerEntries->key_id = $transfer_fee[$j]->id;
            $LedgerEntries->transaction_type = 'D';
            $LedgerEntries->amount = $transfer_fee[$j]->sst_inv;
            $LedgerEntries->bank_id = $transfer_fee[$j]->transfer_to;
            $LedgerEntries->remark = $transfer_fee[$j]->purpose;
            // $LedgerEntries->sys_desc = 'Transfer of G/SST to OA';
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $transfer_fee[$j]->transfer_date;
            $LedgerEntries->type = 'SSTIN';
            $LedgerEntries->save();
        }

        //receive trust
        $ledgers = DB::table('voucher_main as m')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
            ->select('m.*', 'a.name as account_name', 'm.id as voucher_id')
            ->select('m.*', 'm.id as voucher_id', 'm.total_amount as amount', 'b.name as bank_account', 'b.account_no as bank_account_no')
            ->where('m.account_approval', '=', 1)
            ->where('m.voucher_type', '=', 3)
            ->where('m.status', '<>', 99)
            ->orderBy('m.payment_date', 'ASC')
            ->get();

        for ($j = 0; $j < count($ledgers); $j++) {
            // $LedgerEntries = new LedgerEntries();

            // $LedgerEntries->transaction_id = $ledgers[$j]->transaction_id;
            // $LedgerEntries->case_id = $ledgers[$j]->case_id;
            // $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->case_bill_main_id;
            // $LedgerEntries->user_id = $ledgers[$j]->user_id;
            // $LedgerEntries->key_id = $ledgers[$j]->id;
            // $LedgerEntries->transaction_type = 'D';
            // $LedgerEntries->amount = $ledgers[$j]->total_amount;
            // $LedgerEntries->bank_id = $ledgers[$j]->office_account_id;
            // $LedgerEntries->remark = $ledgers[$j]->remark;
            // // $LedgerEntries->sys_desc = 'Trust Acc Payment';
            // $LedgerEntries->status = 1;
            // $LedgerEntries->created_at = date('Y-m-d H:i:s');
            // $LedgerEntries->date = $ledgers[$j]->payment_date;
            // $LedgerEntries->type = 'TRUSTRECEIVE';
            // $LedgerEntries->save();
        }

        //contra
        $ledgers = DB::table('voucher_main as m')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
            ->select('m.*', 'a.name as account_name', 'm.id as voucher_id')
            ->select('m.*', 'm.id as voucher_id', 'm.total_amount as amount', 'b.name as bank_account', 'b.account_no as bank_account_no')
            ->where('m.account_approval', '=', 1)
            ->where('m.voucher_type', '=', 2)
            ->where('m.status', '<>', 99)
            ->orderBy('m.payment_date', 'ASC')
            ->get();

        for ($j = 0; $j < count($ledgers); $j++) {
            $LedgerEntries = new LedgerEntries();

            $transaction = '';

            if ($ledgers[$j]->transaction_id != null) {
                $transaction = $ledgers[$j]->transaction_id;
            }

            $LedgerEntries->transaction_id = $transaction;
            $LedgerEntries->case_id = $ledgers[$j]->case_id;
            $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->case_bill_main_id;
            $LedgerEntries->user_id = $ledgers[$j]->user_id;
            $LedgerEntries->key_id = $ledgers[$j]->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $ledgers[$j]->total_amount;
            $LedgerEntries->bank_id = $ledgers[$j]->office_account_id;
            $LedgerEntries->remark = $ledgers[$j]->remark;
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $ledgers[$j]->payment_date;
            $LedgerEntries->type = 'TRUSTDISB';
            $LedgerEntries->save();
        }

        //receive bill
        $ledgers = DB::table('voucher_main as m')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
            ->select('m.*', 'a.name as account_name', 'm.id as voucher_id')
            ->select('m.*', 'm.id as voucher_id', 'm.total_amount as amount', 'b.name as bank_account', 'b.account_no as bank_account_no')
            ->where('m.account_approval', '=', 1)
            ->where('m.voucher_type', '=', 4)
            ->where('m.status', '<>', 99)
            ->orderBy('m.payment_date', 'ASC')
            ->get();

        for ($j = 0; $j < count($ledgers); $j++) {
            $LedgerEntries = new LedgerEntries();

            $transaction = '';

            if ($ledgers[$j]->transaction_id != null) {
                $transaction = $ledgers[$j]->transaction_id;
            }

            $LedgerEntries->transaction_id = $transaction;
            $LedgerEntries->case_id = $ledgers[$j]->case_id;
            $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->case_bill_main_id;
            $LedgerEntries->user_id = $ledgers[$j]->user_id;
            $LedgerEntries->key_id = $ledgers[$j]->id;
            $LedgerEntries->transaction_type = 'D';
            $LedgerEntries->amount = $ledgers[$j]->total_amount;
            $LedgerEntries->bank_id = $ledgers[$j]->office_account_id;
            $LedgerEntries->remark = $ledgers[$j]->remark;
            // $LedgerEntries->sys_desc = 'Trust Acc Payment';
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $ledgers[$j]->payment_date;
            $LedgerEntries->type = 'BILLRECEIVE';
            $LedgerEntries->save();
        }

        //disb bill
        $ledgers = DB::table('voucher_main as m')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
            ->select('m.*', 'a.name as account_name', 'm.id as voucher_id')
            ->select('m.*', 'm.id as voucher_id', 'm.total_amount as amount', 'b.name as bank_account', 'b.account_no as bank_account_no')
            ->where('m.account_approval', '=', 1)
            ->where('m.voucher_type', '=', 1)
            ->where('m.status', '<>', 99)
            ->orderBy('m.payment_date', 'ASC')
            ->get();

        for ($j = 0; $j < count($ledgers); $j++) {
            $LedgerEntries = new LedgerEntries();

            $transaction = '';
            $voucher_item = '';

            if ($ledgers[$j]->transaction_id != null) {
                $transaction = $ledgers[$j]->transaction_id;
            }

            //   $VoucherDetails = VoucherDetails::where('voucher_main_id', '=', $ledgers[$j]->id)->get();

            $VoucherDetails = DB::table('voucher_details as a')
                ->join('loan_case_bill_details as b', 'b.id', '=', 'a.account_details_id')
                ->join('account_item as c', 'c.id', '=', 'b.account_item_id')
                //   ->leftJoin('account_item as b', 'b.id', '=', 'a.account_item_id')
                ->select('a.*', 'c.name as account_name')
                ->where('voucher_main_id', '=', $ledgers[$j]->id)->get();

            if (count($VoucherDetails) > 0) {
                for ($i = 0; $i < count($VoucherDetails); $i++) {
                    $voucher_item = $voucher_item . '- ' . $VoucherDetails[$i]->account_name . '=' . number_format((float)$VoucherDetails[$i]->amount, 2, '.', ',') . '<br/>';
                }
            }

            $LedgerEntries->transaction_id = $transaction;
            $LedgerEntries->case_id = $ledgers[$j]->case_id;
            $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->case_bill_main_id;
            $LedgerEntries->user_id = $ledgers[$j]->user_id;
            $LedgerEntries->key_id = $ledgers[$j]->id;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $ledgers[$j]->total_amount;
            $LedgerEntries->bank_id = $ledgers[$j]->office_account_id;
            $LedgerEntries->remark = $ledgers[$j]->remark;
            $LedgerEntries->sys_desc = $voucher_item;
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $ledgers[$j]->payment_date;
            $LedgerEntries->type = 'BILLDISB';
            $LedgerEntries->save();
        }

        return $transfer_fee;
    }

    public function updateUsedQuotationAmount()
    {
        $key_id = [];
        $key_id2 = [];
        $key_id3 = [];

        $VoucherDetails = DB::table('voucher_details as a')
            ->join('voucher_main as b', 'b.id', '=', 'a.voucher_main_id')
            ->select('account_details_id')
            ->where('voucher_type', 1)->where('b.Status', 1)->whereMonth('b.created_at', 5)->whereIn('account_approval', [0, 1, 6])->distinct()->orderBy('account_details_id', 'desc')->get();
        // ->where('voucher_type', 1)->where('account_details_id', '<',33024)->where('b.Status', 1)->whereIn('account_approval', [1, 6])->distinct()->orderBy('account_details_id','desc')->get();

        // 33024

        for ($i = 0; $i < count($VoucherDetails); $i++) {
            array_push($key_id, $VoucherDetails[$i]->account_details_id);
        }

        for ($i = 0; $i < count($key_id); $i++) {
            // $sum = VoucherDetails::where('account_details_id', $key_id[$i])->where('Status', 1)->sum('amount');

            $sum = DB::table('voucher_details as a')
                ->join('voucher_main as b', 'b.id', '=', 'a.voucher_main_id')
                ->select('account_details_id')
                ->where('account_details_id', $key_id[$i])
                ->where('voucher_type', 1)->where('b.Status', 1)->whereIn('account_approval', [0, 1, 6])->sum('amount');

            $LoanCaseBillDetails = LoanCaseBillDetails::where('id', $key_id[$i])->first();

            $sum = $LoanCaseBillDetails->quo_amount_no_sst - $sum;
            $sum = number_format((float)$sum, 2, '.', '');
            array_push($key_id2, $sum);

            $LoanCaseBillDetails->amount = $sum;
            $LoanCaseBillDetails->save();

            if ($sum < 0) {
                array_push($key_id3, $key_id[$i]);
            }
        }

        return $key_id3;

        // $LoanCaseBillMainAll = LoanCaseBillMain::where('case_id', 1137)->get();
        // $LoanCaseBillDetails = LoanCaseBillDetails::whereIn('id', $key_id)->get();

        // for ($j = 0; $j < count($LoanCaseBillMainAll); $j++) {


        //     $LoanCaseBillDetails = LoanCaseBillDetails::where('loan_case_main_bill_id', $LoanCaseBillMainAll[$j]->id)->get();

        //     for ($i = 0; $i < count($LoanCaseBillDetails); $i++) {
        //         $VoucherDetails = VoucherDetails::where('voucher_type', 1)->where('Status', 1)->whereIn('account_approval', [1,6])->get();
        //     }


        // }

    }

    public function updateTransferSSTAmount()
    {
        // $LoanCaseBillMainAll = LoanCaseBillMain::where('status', '<>', 99)->where('transferred_to_office_bank', '=', 1)->get();


        $TransferFeeDetails = TransferFeeDetails::where('status', '<>', 99)->whereNotIn('loan_case_main_bill_id', [86, 128, 140, 740])->get();

        for ($j = 0; $j < count($TransferFeeDetails); $j++) {

            $pfee_sum = 0;
            $sst_sum = 0;

            $LoanCaseBillMain = LoanCaseBillMain::where('id', $TransferFeeDetails[$j]->loan_case_main_bill_id)->first();

            if (($LoanCaseBillMain)) {
                if ($TransferFeeDetails[$j]->sst_amount <= 0) {
                    $TransferFeeDetails[$j]->transfer_amount = $TransferFeeDetails[$j]->transfer_amount - $LoanCaseBillMain->sst_inv;
                    $LoanCaseBillMain->transferred_pfee_amt = $TransferFeeDetails[$j]->transfer_amount;
                }

                $TransferFeeDetails[$j]->sst_amount = $LoanCaseBillMain->sst_inv;

                $TransferFeeDetails[$j]->save();

                $LoanCaseBillMain->transferred_sst_amt = $TransferFeeDetails[$j]->sst_amount;
                $LoanCaseBillMain->save();
            }
        }
    }

    public function updateBillTransferAmount()
    {
        $LoanCaseBillMainAll = LoanCaseBillMain::where('status', '<>', 99)->where('invoice_no', '<>', null)->get();

        for ($j = 0; $j < count($LoanCaseBillMainAll); $j++) {

            $pfee_sum = 0;
            $sst_sum = 0;

            $TransferFeeDetails = TransferFeeDetails::where('loan_case_main_bill_id', $LoanCaseBillMainAll[$j]->id)->get();

            if (count($TransferFeeDetails)) {

                for ($i = 0; $i < count($TransferFeeDetails); $i++) {
                    $pfee_sum += $TransferFeeDetails[$i]->transfer_amount;
                    $sst_sum += $TransferFeeDetails[$i]->sst_amount;
                }
            }

            if ($pfee_sum > 0 || $sst_sum > 0) {
                $LoanCaseBillMainAll[$j]->transferred_pfee_amt = $pfee_sum;
                $LoanCaseBillMainAll[$j]->transferred_sst_amt = $sst_sum;

                $LoanCaseBillMainAll[$j]->save();
            }
        }
    }

    public function checkTrustLedger()
    {

        $key_id = [];

        //receive trust
        $ledgers = DB::table('ledger_entries as m')
            ->where('m.type', '=', 'TRUSTRECEIVE')
            ->get();

        for ($i = 0; $i < count($ledgers); $i++) {
            array_push($key_id, $ledgers[$i]->key_id);
        }






        //receive trust
        $ledgers = DB::table('voucher_main as m')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'm.office_account_id')
            ->select('m.*', 'a.name as account_name', 'm.id as voucher_id')
            ->select('m.*', 'm.id as voucher_id', 'm.total_amount as amount', 'b.name as bank_account', 'b.account_no as bank_account_no')
            ->where('m.account_approval', '=', 1)
            ->where('m.voucher_type', '=', 3)
            ->whereNotIn('m.id', $key_id)
            ->where('m.status', '<>', 99)
            ->orderBy('m.payment_date', 'ASC')
            ->get();


        for ($j = 0; $j < count($ledgers); $j++) {
            $LedgerEntries = new LedgerEntries();

            $LedgerEntries->transaction_id = $ledgers[$j]->transaction_id;
            $LedgerEntries->case_id = $ledgers[$j]->case_id;
            $LedgerEntries->loan_case_main_bill_id = $ledgers[$j]->case_bill_main_id;
            $LedgerEntries->user_id = $ledgers[$j]->user_id;
            $LedgerEntries->key_id = $ledgers[$j]->id;
            $LedgerEntries->transaction_type = 'D';
            $LedgerEntries->amount = $ledgers[$j]->total_amount;
            $LedgerEntries->bank_id = $ledgers[$j]->office_account_id;
            $LedgerEntries->remark = $ledgers[$j]->remark;
            // $LedgerEntries->sys_desc = 'Trust Acc Payment';
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $ledgers[$j]->payment_date;
            $LedgerEntries->type = 'TRUSTRECEIVE';
            $LedgerEntries->save();
        }

        return 1;
    }

    public function adminResizeImage()
    {
        $count = 0;

        $ImagesDirectory = public_path('documents\cases\1000\\');

        $files = File::allFiles($ImagesDirectory);
        // $files = File::files($ImagesDirectory);

        return $files;


        // // $a = scandir(public_path('documents\cases\1123\\'));

        // // return $a;
        // foreach (\Illuminate\Support\Facades\Storage::files('documents\cases\1123\\') as $filename) {
        //     $file = \Illuminate\Support\Facades\Storage::get($filename);
        //     // do whatever with $file;

        //     $count +=1;

        //     // return $file;
        // }

        // return $count ;

        // $ImagesDirectory	= '/documents/cases/1123';
        // $ImagesDirectory = '\public\documents\cases\1000\\';
        if ($dir = opendir($ImagesDirectory)) {
            while (($file = readdir($dir)) !== false) {



                if ($file != '.' && $file != '..') {
                    $imagePath = $ImagesDirectory . $file;
                    // $files = File::files($imagePath);

                    $contents = Storage::get($file);

                    return $contents;

                    $file_imae = Storage::get($imagePath);
                    // $imagePath = $ImagesDirectory.$file;
                    // return $imagePath;
                    $isImage =  ImageController::verifyImage($file_imae);

                    if ($isImage == true) {
                        $count += 1;
                        // ImageController::resizeImg($file, $ImagesDirectory, 'test');
                    }

                    // $file = $ImagesDirectory.$file;

                    // $contents = Storage::get($file );

                    // $isImage =  ImageController::verifyImage($contents);

                    $count += 1;
                }


                // if ($isImage == true)
                // {
                //     ImageController::resizeImg($file, $ImagesDirectory, 'test');
                // }
            }
        }

        return $count;
    }

    public function adminUpdateInvoiceBranch()
    {

        // delete folder
        File::deleteDirectory(public_path('app/documents/Land_office'));

        return 1;

        // update pfees, disb,sst, bill total numer
        $LoanCaseBillMainAll = LoanCaseBillMain::where('status', '=', 1)->where('invoice_no', '<>', null)->get();

        for ($j = 0; $j < count($LoanCaseBillMainAll); $j++) {



            $LoanCaseInvoiceDetails = LoanCaseInvoiceDetails::where('loan_case_main_bill_id', '=', $LoanCaseBillMainAll[$j]->id)->get();

            // $CaseTransferLog = CaseTransferLog::where('case_id', '=', $LoanCaseBillMainAll[$j]->case_id)->where('created_at', '>=', $LoanCaseInvoiceDetails[0]->created_at)->orderBy('id', 'asc')->get();
            $CaseTransferLog = CaseTransferLog::where('case_id', '=', $LoanCaseBillMainAll[$j]->case_id)->orderBy('id', 'asc')->get();

            if (count($CaseTransferLog) > 0) {

                if ($CaseTransferLog) {
                    $LoanCaseBillMainAll[$j]->invoice_branch_id = $CaseTransferLog[0]->prev_branch;
                    $LoanCaseBillMainAll[$j]->save();
                }
            } else {

                if ($LoanCaseBillMainAll[$j]->invoice_branch_id == 0) {
                    $LoanCase = LoanCase::where('id', '=', $LoanCaseBillMainAll[$j]->case_id)->first();
                    $LoanCaseBillMainAll[$j]->invoice_branch_id = $LoanCase->branch_id;
                    $LoanCaseBillMainAll[$j]->save();
                }
            }
        }
    }

    public function adminUpdateCaseCount()
    {


        $LoanCase = LoanCase::select('id', 'created_at')
            ->where('branch_id', '=', '1')
            ->where('status', '<>', '99')
            ->get()
            ->groupBy(function ($date) {
                //return Carbon::parse($date->created_at)->format('Y'); // grouping by years
                return Carbon::parse($date->created_at)->format('m'); // grouping by months
            });

        $usermcount = [];
        $userArr = [];

        foreach ($LoanCase as $key => $value) {
            $usermcount[(int)$key] = count($value);
        }

        for ($i = 1; $i <= 12; $i++) {
            $RptCase = new RptCase();

            $RptCase->fiscal_year = 2022;
            $RptCase->fiscal_mon = $i;
            $RptCase->branch_id = 1;
            $RptCase->status = 1;
            $RptCase->created_at = date('Y-m-d H:i:s');
            if (!empty($usermcount[$i])) {
                $userArr[$i] = $usermcount[$i];

                $RptCase->count = $usermcount[$i];
            } else {
                $userArr[$i] = 0;
                $RptCase->count = 0;
            }

            $RptCase->save();
        }


        // create for puchong branch
        $LoanCase = LoanCase::select('id', 'created_at')
            ->where('branch_id', '=', '2')
            ->where('status', '<>', '99')
            ->get()
            ->groupBy(function ($date) {
                //return Carbon::parse($date->created_at)->format('Y'); // grouping by years
                return Carbon::parse($date->created_at)->format('m'); // grouping by months
            });

        $usermcount = [];
        $userArr = [];

        foreach ($LoanCase as $key => $value) {
            $usermcount[(int)$key] = count($value);
        }

        for ($i = 1; $i <= 12; $i++) {
            $RptCase = new RptCase();

            $RptCase->fiscal_year = 2022;
            $RptCase->fiscal_mon = $i;
            $RptCase->branch_id = 2;
            $RptCase->status = 1;
            $RptCase->created_at = date('Y-m-d H:i:s');
            if (!empty($usermcount[$i])) {
                $userArr[$i] = $usermcount[$i];

                $RptCase->count = $usermcount[$i];
            } else {
                $userArr[$i] = 0;
                $RptCase->count = 0;
            }

            $RptCase->save();
        }


        return $userArr;
    }

    public function adminUpdateOperation()
    {
        // $LoanCase = LoanCase::where('status', '<>', 99)->where('remark', '<>', 99)->get();

        $Dispatch = Dispatch::where('status', '<>', 99)->get();


        if ($Dispatch) {

            for ($j = 0; $j < count($Dispatch); $j++) {

                $status_span = '';
                $dispatch_name = '';
                $dispatch_type = '';

                if ($Dispatch[$j]->dispatch_type != '') {
                    if ($Dispatch[$j]->dispatch_type == 1) {
                        $dispatch_type = 'Outgoing';
                    } else if ($Dispatch[$j]->dispatch_type == 2) {
                        $dispatch_type = 'Incoming';
                    }
                }

                if ($Dispatch[$j]->status == '1') {
                    $status_span = '<span class="label bg-success">Completed</span>';
                } else if ($Dispatch[$j]->status == '0') {
                    $status_span = '<span class="label bg-warning">Sending</span>';
                } else {
                    $status_span = '<span class="label bg-info">In Progress</span>';
                }

                if ($Dispatch[$j]->courier_id != '') {
                    $courier = Courier::where('id', '=', $Dispatch[$j]->courier_id)->first();

                    if ($courier) {
                        $dispatch_name = $courier->name;
                    }
                }

                $message = '
        <a href="/dispatch/' . $Dispatch[$j]->id . '/edit" target="_blank">[Created&nbsp;<b>Dispatch - ' . $dispatch_type . '</b> record]</a><br />
        <strong>Send To / Receive From</strong>:&nbsp;' . $Dispatch[$j]->send_to . '<br />
        <strong>Dispatch Name</strong>:&nbsp;' . $dispatch_name . '<br />
        <strong>Returned To Office</strong>:&nbsp;' . $Dispatch[$j]->return_to_office_datetime . '<br />
        <strong>Job Description</strong>:&nbsp;' . $Dispatch[$j]->job_desc . '<br />
        <strong>Attachment</strong>:&nbsp;<a target="_blank" href="/app/documents/dispatch/' . $Dispatch[$j]->file_new_name . '" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>' . $Dispatch[$j]->file_ori_name . '</a><br />
        <strong>Remark</strong>:&nbsp;' . $Dispatch[$j]->inputremark . '<br />
        <strong>Status</strong>:&nbsp;' . $status_span;

                $LoanCaseKivNotes = new LoanCaseKivNotes();

                $LoanCaseKivNotes->case_id =  $Dispatch[$j]->case_id;
                $LoanCaseKivNotes->notes =  $message;
                $LoanCaseKivNotes->label =  'operation|dispatch';
                $LoanCaseKivNotes->role =  "|admin|management";
                $LoanCaseKivNotes->created_at = $Dispatch[$j]->created_at;

                $LoanCaseKivNotes->status =  1;
                $LoanCaseKivNotes->object_id_1 =  $Dispatch[$j]->id;
                $LoanCaseKivNotes->created_by = $Dispatch[$j]->created_by;
                $LoanCaseKivNotes->save();
            }
        }




        // $SafeKeeping = SafeKeeping::where('status', '<>', 99)->get();


        // if ($SafeKeeping) {

        //     for ($j = 0; $j < count($SafeKeeping); $j++) {

        //         $status_span = '';

        //         if ($SafeKeeping[$j]->received == '1') {
        //             $status_span = '<span class="label bg-success">Received</span>';
        //         } else {
        //             $status_span = '<span class="label bg-warning">Pending</span>';
        //         }

        //         $message = '
        //     <a href="/safe-keeping/' . $SafeKeeping[$j]->id . '/edit" target="_blank">[Created&nbsp;<b>Safe Keeping</b> record]</a><br />
        //     <strong>Document Sent</strong>:&nbsp;' . $SafeKeeping[$j]->document_sent . '<br />
        //     <strong>Attention To</strong>:&nbsp;' . $SafeKeeping[$j]->attention_to . '<br />
        //     <strong>Attachment</strong>:&nbsp;<a target="_blank" href="/app/documents/safe_keeping/' . $SafeKeeping[$j]->file_new_name . '" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>' . $SafeKeeping[$j]->file_ori_name . '</a><br />
        //     <strong>Received</strong>:&nbsp;' . $status_span;

        //         $LoanCaseKivNotes = new LoanCaseKivNotes();

        //         $LoanCaseKivNotes->case_id =  $SafeKeeping[$j]->case_id;
        //         $LoanCaseKivNotes->notes =  $message;
        //         $LoanCaseKivNotes->label =  'operation|safekeeping';
        //         $LoanCaseKivNotes->role =  "|admin|management";
        //         $LoanCaseKivNotes->created_at = $SafeKeeping[$j]->created_at;

        //         $LoanCaseKivNotes->status =  1;
        //         $LoanCaseKivNotes->object_id_1 =  $SafeKeeping[$j]->id;
        //         $LoanCaseKivNotes->created_by = $SafeKeeping[$j]->created_by;
        //         $LoanCaseKivNotes->save();
        //     }
        // }


        // $ReturnCall = ReturnCall::where('status', '<>', 99)->get();

        // if ($ReturnCall) {

        //     for ($j = 0; $j < count($ReturnCall); $j++) {

        //         $status_span = '';

        //         $status_span = '';

        //         if ($ReturnCall[$j]->return_call == '1') {
        //             $status_span = '<span class="label bg-success">Yes</span>';
        //         } else {
        //             $status_span = '<span class="label bg-warning">Pending</span>';
        //         }

        //         $message = '
        // <a href="/return-call/' . $ReturnCall[$j]->id . '/edit" target="_blank">[Created&nbsp;<b>Return Call</b> record]</a><br />
        // <strong>Attention</strong>:&nbsp;' . $ReturnCall[$j]->attention . '<br />
        // <strong>Contact No</strong>:&nbsp;' . $ReturnCall[$j]->contact_no. '<br />
        // <strong>Enquiry</strong>:&nbsp;' . $ReturnCall[$j]->enquiry . '<br />
        // <strong>Remark</strong>:&nbsp;' . $ReturnCall[$j]->remark . '<br />
        // <strong>Return Call</strong>:&nbsp;' . $status_span;

        //         $LoanCaseKivNotes = new LoanCaseKivNotes();

        //         $LoanCaseKivNotes->case_id =  $ReturnCall[$j]->case_id;
        //         $LoanCaseKivNotes->notes =  $message;
        //         $LoanCaseKivNotes->label =  'operation|returncall';
        //         $LoanCaseKivNotes->role =  "|admin|management";
        //         $LoanCaseKivNotes->created_at = $ReturnCall[$j]->created_at;

        //         $LoanCaseKivNotes->status =  1;
        //         $LoanCaseKivNotes->object_id_1 =  $ReturnCall[$j]->id;
        //         $LoanCaseKivNotes->created_by = $ReturnCall[$j]->created_by;
        //         $LoanCaseKivNotes->save();
        //     }
        // }

        // $PrepareDocs = PrepareDocs::where('status', '<>', 99)->get();

        // if ($PrepareDocs) {

        //     for ($j = 0; $j < count($PrepareDocs); $j++) {

        //         $status_span = '';

        //         if ($PrepareDocs[$j]->done == '1') {
        //             $status_span = '<span class="label bg-success">Yes</span>';
        //         } else {
        //             $status_span = '<span class="label bg-warning">No</span>';
        //         }

        //         $message = '
        //         <a href="/prepare-docs/' . $PrepareDocs[$j]->id . '/edit" target="_blank">[Created&nbsp;<b>Prepare Docs</b> record]</a><br />
        //         <strong>Signing Date</strong>:&nbsp;' . $PrepareDocs[$j]->signing_date . '<br />
        //         <strong>Docs Prepared</strong>:&nbsp;' . $PrepareDocs[$j]->docs_prepared . '<br />
        //         <strong>Done</strong>:&nbsp;' . $status_span;

        //         $LoanCaseKivNotes = new LoanCaseKivNotes();

        //         $LoanCaseKivNotes->case_id = $PrepareDocs[$j]->case_id;
        //         $LoanCaseKivNotes->notes =  $message;
        //         $LoanCaseKivNotes->label =  'operation|preparedoc';
        //         $LoanCaseKivNotes->role =  "|admin|management";
        //         $LoanCaseKivNotes->created_at = $PrepareDocs[$j]->created_at;

        //         $LoanCaseKivNotes->status =  1;
        //         $LoanCaseKivNotes->object_id_1 =  $PrepareDocs[$j]->id;
        //         $LoanCaseKivNotes->created_by = $PrepareDocs[$j]->created_by;
        //         $LoanCaseKivNotes->save();
        //     }
        // }

        // $LandOffice = LandOffice::where('status', '<>', 99)->get();

        // if ($LandOffice) {

        //     for ($j = 0; $j < count($LandOffice); $j++) {

        //         $status_span = '';

        //         if ($LandOffice[$j]->received == '1') {
        //             $status_span = '<span class="label bg-success">Received</span>';
        //         } else {
        //             $status_span = '<span class="label bg-warning">Pending</span>';
        //         }

        //         $message = '
        //         <a href="/land-office/' . $LandOffice[$j]->id . '/edit" target="_blank">[Created&nbsp;<b>Land Office</b> record]</a><br />
        //         <strong>Land Office</strong>:&nbsp;' . $LandOffice[$j]->land_office . '<br />
        //         <strong>Smartbox No</strong>:&nbsp;' . $LandOffice[$j]->smartbox_no. '<br />
        //         <strong>Receipt No</strong>:&nbsp;' . $LandOffice[$j]->receipt_no . '<br />
        //         <strong>Matter</strong>:&nbsp;' . $LandOffice[$j]->matter . '<br />
        //         <strong>Attachment</strong>:&nbsp;<a target="_blank" href="/app/documents/land_office/' . $LandOffice[$j]->file_new_name . '" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>' . $LandOffice[$j]->file_ori_name . '</a><br />
        //         <strong>Done</strong>:&nbsp;' . $status_span;

        //         $LoanCaseKivNotes = new LoanCaseKivNotes();

        //         $LoanCaseKivNotes->case_id =  $LandOffice[$j]->case_id;
        //         $LoanCaseKivNotes->notes =  $message;
        //         $LoanCaseKivNotes->label =  'operation|landoffice';
        //         $LoanCaseKivNotes->role =   "|admin|management";
        //         $LoanCaseKivNotes->created_at = $LandOffice[$j]->created_at;

        //         $LoanCaseKivNotes->status =  1;
        //         $LoanCaseKivNotes->object_id_1 =  $LandOffice[$j]->id;
        //         $LoanCaseKivNotes->created_by = $LandOffice[$j]->created_by;
        //         $LoanCaseKivNotes->save();
        //     }
        // }

        // $chkt = CHKT::where('status', '<>', 99)->get();

        // if ($chkt) {

        //     for ($j = 0; $j < count($chkt); $j++) {

        //         $status_span = '';

        //         if ($chkt[$j]->per3_rpgt_paid == '1') {
        //             $status_span = '<span class="label bg-success">Yes</span>';
        //         } else {
        //             $status_span = '<span class="label bg-warning">No</span>';
        //         }

        //         $message = '
        //         <a href="/chkt/' . $chkt[$j]->id . '/edit" target="_blank">[Created&nbsp;<b>CHKT</b> record]</a><br />
        //         <strong>Last SPA Date</strong>:&nbsp;' . $chkt[$j]->last_spa_date . '<br />
        //         <strong>Current SPA Date</strong>:&nbsp;' . $chkt[$j]->current_spa_date . '<br />
        //         <strong>CHKT Filed On</strong>:&nbsp;' . $chkt[$j]->chkt_filled_on . '<br />
        //         <strong>Remark</strong>:&nbsp;' . $chkt[$j]->remark . '<br />
        //         <strong>Received Notis Taksiran</strong>:&nbsp;<a target="_blank" href="/app/documents/chkt/' . $chkt[$j]->file_new_name . '" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>' . $chkt[$j]->file_ori_name . '</a><br />
        //         <strong>3% RPGT Paid</strong>:&nbsp;' . $status_span;

        //         $LoanCaseKivNotes = new LoanCaseKivNotes();

        //         $LoanCaseKivNotes->case_id =  $chkt[$j]->case_id;
        //         $LoanCaseKivNotes->notes =  $message;
        //         $LoanCaseKivNotes->label =  'operation|chkt';
        //         $LoanCaseKivNotes->role =  "|admin|management";
        //         $LoanCaseKivNotes->created_at = $chkt[$j]->created_at;

        //         $LoanCaseKivNotes->status =  1;
        //         $LoanCaseKivNotes->object_id_1 =  $chkt[$j]->id;
        //         $LoanCaseKivNotes->created_by = $chkt[$j]->created_by;
        //         $LoanCaseKivNotes->save();
        //     }
        // }

        // $Adjudication = Adjudication::where('status', '<>', 99)->get();

        // if ($Adjudication) {

        //     for ($j = 0; $j < count($Adjudication); $j++) {

        //         $status_span = '';

        //         if ($Adjudication[$j]->stamp_duty_paid == '1') {
        //             $status_span = '<span class="label bg-success">Paid</span>';
        //         } else {
        //             $status_span = '<span class="label bg-warning">Exempted</span>';
        //         }

        //         $message = '
        //         <a href="/adjudication/' . $Adjudication[$j]->id . '/edit" target="_blank">[Created&nbsp;<b>Adjudication</b> record]</a><br />
        //         <strong>First House</strong>:&nbsp;' . $Adjudication[$j]->first_house . '<br />
        //         <strong>Adju No</strong>:&nbsp;' . $Adjudication[$j]->adju_no . '<br />
        //         <strong>Adju Doc</strong>:&nbsp;' . $Adjudication[$j]->adju_doc. '<br />
        //         <strong>Adju Date</strong>:&nbsp;' . $Adjudication[$j]->adju_date . '<br />
        //         <strong>Date of Notis</strong>:&nbsp;' . $Adjudication[$j]->notis_date . '<br />
        //         <strong>Remark</strong>:&nbsp;' . $Adjudication[$j]->remark . '<br />
        //         <strong>Stamp Duty Paid</strong>:&nbsp;' . $status_span;

        //         $LoanCaseKivNotes = new LoanCaseKivNotes();

        //         $LoanCaseKivNotes->case_id =  $Adjudication[$j]->case_id;
        //         $LoanCaseKivNotes->notes =  $message;
        //         $LoanCaseKivNotes->label =  'operation|adju';
        //         $LoanCaseKivNotes->role =  "|admin|management";
        //         $LoanCaseKivNotes->created_at = $Adjudication[$j]->created_at;

        //         $LoanCaseKivNotes->status =  1;
        //         $LoanCaseKivNotes->object_id_1 =  $Adjudication[$j]->id;
        //         $LoanCaseKivNotes->created_by = $Adjudication[$j]->created_by;
        //         $LoanCaseKivNotes->save();
        //     }
        // }
    }

    public function adminUpdateDate()
    {
        $LoanCaseMasterList = LoanCaseMasterList::where('masterlist_field_id', '=', 147)->where('value', '<>', null)->where('id', '<>', 715)->get();

        for ($j = 0; $j < count($LoanCaseMasterList); $j++) {
            $LoanCase = LoanCase::where('id', '=', $LoanCaseMasterList[$j]->case_id)->first();

            if ($LoanCase) {

                $LoanCase->spa_date = $LoanCaseMasterList[$j]->value;
                $LoanCase->save();
            }
        }

        $LoanCaseMasterList = LoanCaseMasterList::where('masterlist_field_id', '=', 148)->where('id', '<>', 716)->where('value', '<>', null)->get();

        for ($j = 0; $j < count($LoanCaseMasterList); $j++) {
            $LoanCase = LoanCase::where('id', '=', $LoanCaseMasterList[$j]->case_id)->first();

            if ($LoanCase) {

                $LoanCase->completion_date = $LoanCaseMasterList[$j]->value;
                $LoanCase->save();
            }
        }
    }

    public function adminUpdateReceiveDateInvoice()
    {
        $LoanCaseBillMainAll = LoanCaseBillMain::where('status', '=', 1)->get();


        for ($j = 0; $j < count($LoanCaseBillMainAll); $j++) {
            $voucher_main = VoucherMain::where('case_bill_main_id', '=', $LoanCaseBillMainAll[$j]->id)->where('status', '=', 4)->orderBy('payment_date', 'DESC')->take(1)->first();

            if ($voucher_main) {

                $LoanCaseBillMainAll[$j]->payment_receipt_date = $voucher_main->payment_date;
                $LoanCaseBillMainAll[$j]->save();
            }
        }
    }

    public function updatePfeeDisbAmount($id)
    {
        $pfee = 0;
        $pfee1 = 0;
        $pfee2 = 0;
        $disb = 0;
        $sst = 0;
        $newAmount = 0;

        $pfee = 0;
        $pfee1 = 0;
        $pfee2 = 0;
        $disb = 0;
        $total_sst = 0;

        $referral_a1 = 0;
        $referral_a2 = 0;
        $referral_a3 = 0;
        $referral_a4 = 0;
        $marketing = 0;


        $bill_recv = 0;
        $bill_dis = 0;
        $trust_dis = 0;
        $trust_recv = 0;

        $newAmount = 0;

        $loanBillDetails = DB::table('loan_case_bill_details AS bd')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->select('bd.*', 'a.account_cat_id', 'a.pfee1_item')
            ->where('bd.loan_case_main_bill_id', '=',  $id)
            ->where('bd.status', '<>',  99)
            ->get();

        $LoanCaseBillMain = LoanCaseBillMain::where('id', $id)->first();


        for ($i = 0; $i < count($loanBillDetails); $i++) {
            if ($loanBillDetails[$i]->account_cat_id == 1 || $loanBillDetails[$i]->account_cat_id == 4) {
                $pfee += $loanBillDetails[$i]->quo_amount_no_sst;
                $sst = round($loanBillDetails[$i]->quo_amount_no_sst * ($LoanCaseBillMain->sst_rate * 0.01), 2);

                  if ($loanBillDetails[$i]->account_cat_id == 1) {
                    if ($loanBillDetails[$i]->pfee1_item == 1) {
                        $pfee1 += $loanBillDetails[$i]->quo_amount_no_sst;
                    } else {
                        $pfee2 += $loanBillDetails[$i]->quo_amount_no_sst;
                    }
                }

                // $newAmount += $loanBillDetails[$i]->quo_amount_no_sst * 1.06;
                $newAmount += $loanBillDetails[$i]->quo_amount_no_sst + $sst;
                $total_sst += $sst;
            }
            
            // if ($loanBillDetails[$i]->account_cat_id == 4) {
            //     // Handle account_cat_id == 4 separately (not included in pfee1/pfee2 calculation)
            //     $newAmount += $loanBillDetails[$i]->quo_amount_no_sst;
            // }

            if ($loanBillDetails[$i]->account_cat_id == 3 || $loanBillDetails[$i]->account_cat_id == 2) {
                $disb += $loanBillDetails[$i]->quo_amount_no_sst;
                $newAmount += $loanBillDetails[$i]->quo_amount_no_sst;
            }
        }

        // $sst = $pfee * 0.06; 
        $total_sst = number_format((float)$total_sst, 2, '.', '');

        // update main Bill value
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        $LoanCaseBillMain->pfee1 = $pfee1;
        $LoanCaseBillMain->pfee2 = $pfee2;
        $LoanCaseBillMain->pfee = $pfee;
        $LoanCaseBillMain->disb = $disb;
        $LoanCaseBillMain->sst = $total_sst;

        $LoanCaseBillMain->bill_dis = $bill_dis;
        $LoanCaseBillMain->bill_recv = $bill_recv;

        $referral_a1 = $LoanCaseBillMain->referral_a1;
        $referral_a2 = $LoanCaseBillMain->referral_a2;
        $referral_a3 = $LoanCaseBillMain->referral_a3;
        $referral_a4 = $LoanCaseBillMain->referral_a4;
        $marketing = $LoanCaseBillMain->marketing;
        $uncollected = $LoanCaseBillMain->uncollected;

        $prof_bal = 0;
        $prof_bal = $pfee1 + $pfee2 - $referral_a1 - $referral_a2 - $referral_a3 - $referral_a4 - $marketing - $uncollected;

        $prof_bal2 = 0;
        $prof_bal2 = $pfee1 - $referral_a1 - $referral_a2 - $referral_a3 - $referral_a4 - $marketing - $uncollected;


        $LoanCaseBillMain->collected_amt_new = $bill_recv;
        $LoanCaseBillMain->prof_balance = $prof_bal;


        $LoanCaseBillMain->staff_bonus_2_per = $prof_bal * 0.02;
        $LoanCaseBillMain->staff_bonus_3_per = $prof_bal * 0.03;

        $LoanCaseBillMain->lawyer_bonus_2_per = $prof_bal * 0.02;
        $LoanCaseBillMain->lawyer_bonus_3_per = $prof_bal * 0.03;

        $LoanCaseBillMain->staff_bonus_2_per_p1 = $prof_bal2 * 0.02;
        $LoanCaseBillMain->staff_bonus_3_per_p1 = $prof_bal2 * 0.03;

        $LoanCaseBillMain->lawyer_bonus_2_per_p1 = $prof_bal2 * 0.02;
        $LoanCaseBillMain->lawyer_bonus_3_per_p1 = $prof_bal2 * 0.03;


        $LoanCaseBillMain->staff_bonus_25_per = $prof_bal * 0.02;

        $LoanCaseBillMain->targeted_amt_new = $newAmount;


        // $LoanCaseBillMain->prof_balance= $prof_bal;



        $collected_amt = $LoanCaseBillMain->collected_amt_new;
        $LoanCaseBillMain->disb_balance = $disb - $bill_dis;
        $LoanCaseBillMain->actual_balance = $LoanCaseBillMain->disb_balance + $LoanCaseBillMain->prof_balance;

        $LoanCaseBillMain->trust_disb = $trust_dis;
        $LoanCaseBillMain->trust_recv = $trust_recv;
        $LoanCaseBillMain->adv = $trust_dis + $bill_dis;

        $LoanCaseBillMain->total_amt = $newAmount;

        $LoanCaseBillMain->save();


        // update case collected bill
        $total = 0;
        $LoanCase = LoanCase::where('id', '=', $LoanCaseBillMain->case_id)->first();

        // //Update Bonus
        // $this->calculateEstimateBonus($LoanCaseBillMain->case_id, $LoanCaseBillMain->id);

        // //Update Referral
        // $this->calculateReferralFee($LoanCaseBillMain->case_id, $LoanCaseBillMain->id,0);

        $this->calculateEstimateBonus($LoanCaseBillMain->case_id, $LoanCaseBillMain->id);


        $LoanCaseBillMain = LoanCaseBillMain::where('case_id', '=', $LoanCaseBillMain->case_id)->where('status', '<>', 99)->get();

        for ($i = 0; $i < count($LoanCaseBillMain); $i++) {
            $total += $LoanCaseBillMain[$i]->total_amt;
        }

        $LoanCase->targeted_bill = $total;
        $LoanCase->save();
    }

    public function adminBonusCalculation()
    {

        // $LoanCase = LoanCase::where('status', '<>', 99)
        // ->select('id')
        // ->whereNotIn('id', function($query) {
        //     $query->select('case_id')
        //     ->from(with(new CaseTransferLog())->getTable());
        // })->get();

        // $Loan2 = LoanCase::where('status', '<>', 99)
        // ->whereIn('id', $LoanCase)->get();

        // return $Loan2;

        // $loanCaseBill = DB::table('loan_case AS l')
        //     ->leftJoin('loan_case_bill_main AS m', 'm.case_id', '=', 'l.id')
        //     ->select('m.*', 'l.lawyer_id', 'l.clerk_id')
        //     ->where('l.status', '<>', 99)
        //     ->where('m.status', '<>', 99)
        //     ->whereNotIn('l.id', function ($query) {
        //         $query->select('case_id')
        //             ->from(with(new CaseTransferLog())->getTable());
        //     })->get();

        $loanCaseBill = DB::table('loan_case AS l')
            ->leftJoin('loan_case_bill_main AS m', 'm.case_id', '=', 'l.id')
            ->select('m.*', 'l.lawyer_id', 'l.clerk_id')
            ->where('l.status', '<>', 99)
            ->where('m.status', '<>', 99)->get();

        if (count($loanCaseBill) > 0) {

            for ($i = 0; $i < count($loanCaseBill); $i++) {


                $this->calculateEstimateBonus($loanCaseBill[$i]->case_id, $loanCaseBill[$i]->id);

                // if ($loanCaseBill[$i]->lawyer_id != null && $loanCaseBill[$i]->lawyer_id != 0) {
                //     $Bonus = new Bonus();
                //     $Bonus->user_id = $loanCaseBill[$i]->lawyer_id;
                //     $Bonus->case_id = $loanCaseBill[$i]->case_id;
                //     $Bonus->bill_id = $loanCaseBill[$i]->id;
                //     $Bonus->bonus_2_percent = $loanCaseBill[$i]->staff_bonus_2_per;
                //     $Bonus->bonus_3_percent = $loanCaseBill[$i]->staff_bonus_3_per;
                //     $Bonus->p1_bonus_2_percent = $loanCaseBill[$i]->staff_bonus_2_per_p1;
                //     $Bonus->p1_bonus_3_percent = $loanCaseBill[$i]->staff_bonus_3_per_p1;
                //     $Bonus->case_transferred = 0;

                //     $Bonus->save();
                // }

                // if ($loanCaseBill[$i]->clerk_id != null && $loanCaseBill[$i]->clerk_id != 0) {
                //     $Bonus = new Bonus();
                //     $Bonus->user_id = $loanCaseBill[$i]->clerk_id;
                //     $Bonus->case_id = $loanCaseBill[$i]->case_id;
                //     $Bonus->bill_id = $loanCaseBill[$i]->id;
                //     $Bonus->bonus_2_percent = $loanCaseBill[$i]->staff_bonus_2_per;
                //     $Bonus->bonus_3_percent = $loanCaseBill[$i]->staff_bonus_3_per;
                //     $Bonus->p1_bonus_2_percent = $loanCaseBill[$i]->staff_bonus_2_per_p1;
                //     $Bonus->p1_bonus_3_percent = $loanCaseBill[$i]->staff_bonus_3_per_p1;
                //     $Bonus->case_transferred = 0;

                //     $Bonus->save();
                // }
            }
        }

        return 1;

        // $loanCase = new TodoList();
        // $loanCase->case_ref_no = $case_ref_no;
        // $loanCase->property_address = 'test';

        // return $loanBillDetails;


        $User = User::where('id', '=', 75)->first();




        $LoanCase = LoanCase::where(function ($q) use ($User) {
            $q->where('lawyer_id', '=', $User->id)
                ->orWhere('clerk_id', '=', $User->id);
        })
            ->whereNotIn('id', function ($query) use ($User) {
                $query->select('case_id')
                    ->from(with(new CaseTransferLog())->getTable())
                    ->where('new_user', '=', $User->id);
            })->get();

        $LoanCase2 = LoanCase::where(function ($q) use ($User) {
            $q->where('lawyer_id', '=', $User->id)
                ->orWhere('clerk_id', '=', $User->id);
        })
            ->whereIn('id', function ($query) use ($User) {
                $query->select('case_id')
                    ->from(with(new CaseTransferLog())->getTable())
                    ->where('ori_user', '=', $User->id);
            })->get();

        $allItems = new \Illuminate\Database\Eloquent\Collection; //Create empty collection which we know has the merge() method
        $allItems = $allItems->merge($LoanCase);
        $allItems = $allItems->merge($LoanCase2);

        return $allItems;

        // $joinData = array();

        // for ($i = 0; $i < count($account_template_cat); $i++) {

        //     $account_template_details_by_cat = LoanCaseAccount::where('case_id', '=', $id)
        //         ->where('account_cat_id', '=', $account_template_cat[$i]->id)
        //         ->get();
        //     array_push($joinData,  array('category' => $account_template_cat[$i], 'account_details' => $account_template_details_by_cat));
        // }


        $pfee = 0;
        $pfee1 = 0;
        $pfee2 = 0;
        $disb = 0;
        $sst = 0;
        $newAmount = 0;

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


        $bill_recv = 0;
        $bill_dis = 0;
        $trust_dis = 0;
        $trust_recv = 0;

        $newAmount = 0;

        $loanBillDetails = DB::table('loan_case_bill_details AS bd')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->select('bd.*', 'a.account_cat_id', 'a.pfee1_item')
            ->where('bd.loan_case_main_bill_id', '=',  $id)
            ->where('bd.status', '<>',  99)
            ->get();


        for ($i = 0; $i < count($loanBillDetails); $i++) {
            if ($loanBillDetails[$i]->account_cat_id == 1) {
                $pfee += $loanBillDetails[$i]->quo_amount_no_sst;

                if ($loanBillDetails[$i]->pfee1_item == 1) {
                    $pfee1 += $loanBillDetails[$i]->quo_amount_no_sst;
                } else {
                    $pfee2 += $loanBillDetails[$i]->quo_amount_no_sst;
                }
                $newAmount += $loanBillDetails[$i]->quo_amount_no_sst * 1.06;
            }

            if ($loanBillDetails[$i]->account_cat_id == 3 || $loanBillDetails[$i]->account_cat_id == 2) {
                $disb += $loanBillDetails[$i]->quo_amount_no_sst;
                $newAmount += $loanBillDetails[$i]->quo_amount_no_sst;
            }
        }

        $sst = $pfee * 0.06;
        $sst = number_format((float)$sst, 2, '.', '');

        // update main Bill value
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        $LoanCaseBillMain->pfee1 = $pfee1;
        $LoanCaseBillMain->pfee2 = $pfee2;
        $LoanCaseBillMain->pfee = $pfee;
        $LoanCaseBillMain->disb = $disb;
        $LoanCaseBillMain->sst = $sst;

        $LoanCaseBillMain->bill_dis = $bill_dis;
        $LoanCaseBillMain->bill_recv = $bill_recv;

        $referral_a1 = $LoanCaseBillMain->referral_a1;
        $referral_a2 = $LoanCaseBillMain->referral_a2;
        $referral_a3 = $LoanCaseBillMain->referral_a3;
        $referral_a4 = $LoanCaseBillMain->referral_a4;
        $marketing = $LoanCaseBillMain->marketing;
        $uncollected = $LoanCaseBillMain->uncollected;

        $prof_bal = 0;
        $prof_bal = $pfee1 + $pfee2 - $referral_a1 - $referral_a2 - $referral_a3 - $referral_a4 - $marketing - $uncollected;

        $prof_bal2 = 0;
        $prof_bal2 = $pfee1 - $referral_a1 - $referral_a2 - $referral_a3 - $referral_a4 - $marketing - $uncollected;


        $LoanCaseBillMain->collected_amt_new = $bill_recv;
        $LoanCaseBillMain->prof_balance = $prof_bal;


        $LoanCaseBillMain->staff_bonus_2_per = $prof_bal * 0.02;
        $LoanCaseBillMain->staff_bonus_3_per = $prof_bal * 0.03;

        $LoanCaseBillMain->lawyer_bonus_2_per = $prof_bal * 0.02;
        $LoanCaseBillMain->lawyer_bonus_3_per = $prof_bal * 0.03;

        $LoanCaseBillMain->staff_bonus_2_per_p1 = $prof_bal2 * 0.02;
        $LoanCaseBillMain->staff_bonus_3_per_p1 = $prof_bal2 * 0.03;

        $LoanCaseBillMain->lawyer_bonus_2_per_p1 = $prof_bal2 * 0.02;
        $LoanCaseBillMain->lawyer_bonus_3_per_p1 = $prof_bal2 * 0.03;

        $LoanCaseBillMain->targeted_amt_new = $newAmount;


        // $LoanCaseBillMain->prof_balance= $prof_bal;



        $collected_amt = $LoanCaseBillMain->collected_amt_new;
        $LoanCaseBillMain->disb_balance = $disb - $bill_dis;
        $LoanCaseBillMain->actual_balance = $LoanCaseBillMain->disb_balance + $LoanCaseBillMain->prof_balance;

        $LoanCaseBillMain->trust_disb = $trust_dis;
        $LoanCaseBillMain->trust_recv = $trust_recv;
        $LoanCaseBillMain->adv = $trust_dis + $bill_dis;

        $LoanCaseBillMain->total_amt = $newAmount;

        $LoanCaseBillMain->save();


        // update case collected bill
        $total = 0;
        $LoanCase = LoanCase::where('id', '=', $LoanCaseBillMain->case_id)->first();


        $LoanCaseBillMain = LoanCaseBillMain::where('case_id', '=', $LoanCaseBillMain->case_id)->where('status', '<>', 99)->get();

        for ($i = 0; $i < count($LoanCaseBillMain); $i++) {
            $total += $LoanCaseBillMain[$i]->total_amt;
        }

        $LoanCase->targeted_bill = $total;
        $LoanCase->save();
    }


    public function updatePfeeDisbAmountINV($id)
    {
        // Updated function that calculates from details and updates both invoices and bills
        $this->updatePfeeDisbAmountINVFromDetails($id);
    }

    /**
     * Comprehensive function to update invoice and bill amounts from details
     * Implements the same logic as our SQL comparison script
     */
    public function updatePfeeDisbAmountINVFromDetails($id)
    {
        // Get the main bill record
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();
        
        if (!$LoanCaseBillMain) {
            return;
        }

        // Get all invoices for this bill
        $invoices = DB::table('loan_case_invoice_main')
            ->where("loan_case_main_bill_id", $id)
            ->where("status", "<>", 99)
            ->where("bln_invoice", "=", 1)  // Only show active invoices (not reverted)
            ->get();

        $total_pfee1 = 0;
        $total_pfee2 = 0;
        $total_sst = 0;
        $total_reimbursement_amount = 0;
        $total_reimbursement_sst = 0;
        $total_amount = 0;

        // Update each invoice from its details
        foreach ($invoices as $invoice) {
            $invoiceCalculations = $this->calculateInvoiceAmountsFromDetails($invoice->id, $LoanCaseBillMain->sst_rate);
            
            // Update the invoice record
            DB::table('loan_case_invoice_main')
                ->where('id', $invoice->id)
                ->update([
                    'pfee1_inv' => $invoiceCalculations['pfee1'],
                    'pfee2_inv' => $invoiceCalculations['pfee2'],
                    'sst_inv' => $invoiceCalculations['sst'],
                    'reimbursement_amount' => $invoiceCalculations['reimbursement_amount'],
                    'reimbursement_sst' => $invoiceCalculations['reimbursement_sst'],
                    'amount' => $invoiceCalculations['total'],
                    'updated_at' => now()
                ]);

            // Add to bill totals
            $total_pfee1 += $invoiceCalculations['pfee1'];
            $total_pfee2 += $invoiceCalculations['pfee2'];
            $total_sst += $invoiceCalculations['sst'];
            $total_reimbursement_amount += $invoiceCalculations['reimbursement_amount'];
            $total_reimbursement_sst += $invoiceCalculations['reimbursement_sst'];
            $total_amount += $invoiceCalculations['total'];
        }

        // Update the bill record
        $LoanCaseBillMain->pfee1_inv = $total_pfee1;
        $LoanCaseBillMain->pfee2_inv = $total_pfee2;
        $LoanCaseBillMain->sst_inv = $total_sst;
        $LoanCaseBillMain->reimbursement_amount = $total_reimbursement_amount;
        $LoanCaseBillMain->reimbursement_sst = $total_reimbursement_sst;
        $LoanCaseBillMain->total_amt_inv = $total_amount;
        $LoanCaseBillMain->total_amt = $total_amount;  // Update main total amount field
        $LoanCaseBillMain->save();
    }

    /**
     * Calculate invoice amounts from details using the same logic as our SQL script
     */
    private function calculateInvoiceAmountsFromDetails($invoiceId, $sstRate)
    {
        $details = DB::table('loan_case_invoice_details as ild')
            ->leftJoin('account_item as ai', 'ild.account_item_id', '=', 'ai.id')
            ->where('ild.invoice_main_id', $invoiceId)
            ->where('ild.status', '<>', 99)
            ->select('ild.amount', 'ai.account_cat_id', 'ai.pfee1_item')
            ->get();

        $pfee1 = 0;
        $pfee2 = 0;
        $sst = 0;
        $reimbursement_amount = 0;
        $reimbursement_sst = 0;
        $total = 0;

        foreach ($details as $detail) {
            if ($detail->account_cat_id == 1) {
                // Calculate pfee1 and pfee2 for professional fees
                if ($detail->pfee1_item == 1) {
                    $pfee1 += $detail->amount;
                } else {
                    $pfee2 += $detail->amount;
                }
                
                // Calculate SST and total for account_cat_id == 1 (base amount + SST)
                $sst += $detail->amount * ($sstRate / 100);
                $total += $detail->amount * (($sstRate / 100) + 1);
            } elseif ($detail->account_cat_id == 4) {
                // Calculate reimbursement amounts for account_cat_id == 4
                $reimbursement_amount += $detail->amount;
                $reimbursement_sst += $detail->amount * ($sstRate / 100);
                $total += $detail->amount * (($sstRate / 100) + 1);
            } else {
                // For other account categories, add amount directly to total
                $total += $detail->amount;
            }
        }

        return [
            'pfee1' => round($pfee1, 2),
            'pfee2' => round($pfee2, 2),
            'sst' => round($sst, 2),
            'reimbursement_amount' => round($reimbursement_amount, 2),
            'reimbursement_sst' => round($reimbursement_sst, 2),
            'total' => round($total, 2)
        ];
    }

    /**
     * System-wide function to update all invoices and bills from details
     * Equivalent to running our SQL update script
     */
    public function updateAllInvoiceAmountsFromDetails()
    {
        // Get all bills that have invoices
        $bills = DB::table('loan_case_bill_main')
            ->where('status', '<>', 99)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('loan_case_invoice_main')
                    ->whereColumn('loan_case_invoice_main.loan_case_main_bill_id', 'loan_case_bill_main.id')
                    ->where('loan_case_invoice_main.status', '<>', 99);
            })
            ->get();

        $updatedBills = 0;
        $updatedInvoices = 0;

        foreach ($bills as $bill) {
            $this->updatePfeeDisbAmountINVFromDetails($bill->id);
            $updatedBills++;
            
            // Count invoices updated for this bill
            $invoiceCount = DB::table('loan_case_invoice_main')
                ->where('loan_case_main_bill_id', $bill->id)
                ->where('status', '<>', 99)
                ->count();
            
            $updatedInvoices += $invoiceCount;
        }

        return [
            'success' => true,
            'updated_bills' => $updatedBills,
            'updated_invoices' => $updatedInvoices,
            'message' => "Successfully updated {$updatedBills} bills and {$updatedInvoices} invoices from details"
        ];
    }

    public function updatePfeeDisbAmountINVV2($id)
    {
        $pfee = 0;
        $pfee1 = 0;
        $pfee2 = 0;
        $disb = 0;
        $sst = 0;
        $newAmount = 0;

        $loanBillDetails = DB::table('loan_case_invoice_details AS bd')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->select('bd.*', 'a.account_cat_id', 'a.pfee1_item')
            ->where('bd.loan_case_main_bill_id', '=',  $id)
            ->get();

        // update main Bill value
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();


        for ($i = 0; $i < count($loanBillDetails); $i++) {
            if ($loanBillDetails[$i]->account_cat_id == 1) {
                $pfee += $loanBillDetails[$i]->amount;

                if ($loanBillDetails[$i]->pfee1_item == 1) {
                    $pfee1 += $loanBillDetails[$i]->amount;
                } else {
                    $pfee2 += $loanBillDetails[$i]->amount;
                }
                $newAmount += $loanBillDetails[$i]->amount * (1 + ($LoanCaseBillMain->sst_rate * 0.01));
            }

            if ($loanBillDetails[$i]->account_cat_id == 3 || $loanBillDetails[$i]->account_cat_id == 2) {
                $disb += $loanBillDetails[$i]->amount;
                $newAmount += $loanBillDetails[$i]->amount;
            }
        }

        $sst = $pfee * ($LoanCaseBillMain->sst_rate * 0.01);
        $sst = number_format((float)$sst, 2, '.', '');

        // // update main Bill value
        // $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        $LoanCaseBillMain->pfee1_inv = $pfee1;
        $LoanCaseBillMain->pfee2_inv = $pfee2;
        // $LoanCaseBillMain->pfee_inv = $pfee;
        $LoanCaseBillMain->disb_inv = $disb;
        $LoanCaseBillMain->sst_inv = $sst;

        $LoanCaseBillMain->total_amt_inv = $newAmount;

        $LoanCaseBillMain->save();
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


        $bill_recv = 0;
        $bill_dis = 0;
        $trust_dis = 0;

        $newAmount = 0;

        $LoanCaseBillMain->referral_a1 = $request->input('referral_a1');
        $LoanCaseBillMain->referral_a2 = $request->input('referral_a2');
        $LoanCaseBillMain->referral_a3 = $request->input('referral_a3');
        $LoanCaseBillMain->referral_a4 = $request->input('referral_a4');
        $LoanCaseBillMain->marketing = $request->input('marketing');
        $LoanCaseBillMain->uncollected = $request->input('uncollected');

        $LoanCaseBillMain->save();

        return response()->json(['status' => 1, 'data' => 'Updated bill details']);


        // $sst = $pfee * 0.06;
        // $sst = number_format((float)$sst, 2, '.', '');

        // $LoanCaseBillMain->pfee1 = $pfee1;
        // $LoanCaseBillMain->pfee2 = $pfee2;
        // $LoanCaseBillMain->pfee = $pfee;
        // $LoanCaseBillMain->disb = $disb;
        // $LoanCaseBillMain->sst = $sst;




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




    public function updateBillSummaryAllByAdmin()
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

        // $LoanCaseBillMainAll = LoanCaseBillMain::where('id', '=', 524)->get();

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


            $bill_recv = 0;
            $bill_dis = 0;
            $trust_dis = 0;
            $trust_recv = 0;

            $newAmount = 0;



            for ($i = 0; $i < count($loanBillDetails); $i++) {

                if ($loanBillDetails[$i]->account_cat_id == 1) {
                    $pfee += $loanBillDetails[$i]->quo_amount_no_sst;

                    if ($loanBillDetails[$i]->pfee1_item == 1) {
                        $pfee1 += $loanBillDetails[$i]->quo_amount_no_sst;
                    } else {
                        $pfee2 += $loanBillDetails[$i]->quo_amount_no_sst;
                    }
                    $newAmount += $loanBillDetails[$i]->quo_amount_no_sst * 1.06;
                }

                if ($loanBillDetails[$i]->account_cat_id == 3 || $loanBillDetails[$i]->account_cat_id == 2) {
                    $disb += $loanBillDetails[$i]->quo_amount_no_sst;
                    $newAmount += $loanBillDetails[$i]->quo_amount_no_sst;
                }
            }

            $sst = $pfee * 0.06;
            $sst = number_format((float)$sst, 2, '.', '');

            $LoanCaseBillMain->pfee1 = $pfee1;
            $LoanCaseBillMain->pfee2 = $pfee2;
            $LoanCaseBillMain->pfee = $pfee;
            $LoanCaseBillMain->disb = $disb;
            $LoanCaseBillMain->sst = $sst;


            $bill_disburse = DB::table('voucher_details AS vd')
                ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
                ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
                ->leftJoin('loan_case_bill_main AS lm', 'lm.id', '=', 'bd.loan_case_main_bill_id')
                ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
                ->select('vd.*', 'a.name as account_name', 'vm.id as voucher_id', 'vm.voucher_no', 'vm.lawyer_approval as lawyer_approval', 'vm.account_approval as account_approval')
                ->where('vd.case_id', '=',  $LoanCaseBillMainAll[$j]->case_id)
                ->where('bd.loan_case_main_bill_id', '=',  $LoanCaseBillMainAll[$j]->id)
                ->where('vm.voucher_type', '=',  1)
                ->where('vm.status', '<>',  99)
                ->where('vm.account_approval', '=', 1)
                ->get();

            for ($i = 0; $i < count($bill_disburse); $i++) {
                $bill_dis += $bill_disburse[$i]->amount;
            }





            // $bill_receive = DB::table('voucher_details AS vd')
            //     ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            //     ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
            //     ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            //     ->select('vd.*', 'a.name as account_name', 'vm.id as voucher_id', 'vm.voucher_no', 'vm.payee', 'vm.remark as remark')
            //     ->where('vd.case_id', '=',  $LoanCaseBillMainAll[$j]->case_id)
            //     ->where('vd.status', '=',  4)
            //     ->get();

            $bill_receive = DB::table('voucher_details AS vd')
                ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
                ->leftJoin('loan_case_bill_main AS lm', 'lm.id', '=', 'vm.case_bill_main_id')
                // ->where('vm.case_id', '=',  $LoanCaseBillMainAll[$j]->case_id)
                ->where('lm.id', '=',  $LoanCaseBillMainAll[$j]->id)
                ->where('vm.voucher_type', '=',  4)
                ->where('vm.status', '<>',  99)
                ->where('vd.status', '<>',  99)
                ->where('lm.status', '=',  1)
                ->get();


            for ($i = 0; $i < count($bill_receive); $i++) {
                $bill_recv += $bill_receive[$i]->amount;
            }


            if ($LoanCaseBillMainAll[$j]->name <> 'Vendor (title & Master Title)') {
                $loan_case_trust_main_dis = DB::table('voucher_main as v')
                    ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
                    ->select('v.*', 'vd.amount as detail_amount')
                    ->where('v.case_id', '=', $LoanCaseBillMainAll[$j]->case_id)
                    ->where('v.account_approval', '=', 1)
                    ->where('v.voucher_type', '=', 2)
                    ->where('v.status', '<>', 99)
                    ->get();

                for ($i = 0; $i < count($loan_case_trust_main_dis); $i++) {

                    $trust_dis += $loan_case_trust_main_dis[$i]->detail_amount;
                }

                $loan_case_trust_main_dis = DB::table('voucher_main as v')
                    ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
                    ->select('v.*', 'vd.amount as detail_amount')
                    ->where('v.case_id', '=', $LoanCaseBillMainAll[$j]->case_id)
                    ->where('v.account_approval', '=', 1)
                    ->where('v.voucher_type', '=', 3)
                    ->where('v.status', '<>', 99)
                    ->get();

                for ($i = 0; $i < count($loan_case_trust_main_dis); $i++) {
                    $trust_recv += $loan_case_trust_main_dis[$i]->detail_amount;
                }
            }





            // $LoanCaseBillMain->referral_a1 = $request->input('referral_a1');
            //     $LoanCaseBillMain->referral_a2 = $request->input('referral_a2');
            //     $LoanCaseBillMain->referral_a3 = $request->input('referral_a3');
            //     $LoanCaseBillMain->referral_a4 = $request->input('referral_a4');
            //     $LoanCaseBillMain->marketing = $request->input('marketing');

            $referral_a1 = $LoanCaseBillMain->referral_a1;
            $referral_a2 = $LoanCaseBillMain->referral_a2;
            $referral_a3 = $LoanCaseBillMain->referral_a3;
            $referral_a4 = $LoanCaseBillMain->referral_a4;
            $marketing = $LoanCaseBillMain->marketing;
            $uncollected = $LoanCaseBillMain->uncollected;

            $collected_amt = $LoanCaseBillMain->collected_amt;
            $collected_amt_sum = $collected_amt;

            $LoanCaseBillMain->bill_dis = $bill_dis;
            $LoanCaseBillMain->bill_recv = $bill_recv;

            $prof_bal = 0;
            $prof_bal = $pfee1 + $pfee2 - $referral_a1 - $referral_a2 - $referral_a3 - $referral_a4 - $marketing - $uncollected;
            $prof_bal2 = 0;
            $prof_bal2 = $pfee1 - $referral_a1 - $referral_a2 - $referral_a3 - $referral_a4 - $marketing - $uncollected;

            $LoanCaseBillMain->collected_amt_new = $bill_recv;
            $LoanCaseBillMain->prof_balance = $prof_bal;


            $LoanCaseBillMain->staff_bonus_2_per = $prof_bal * 0.02;
            $LoanCaseBillMain->staff_bonus_3_per = $prof_bal * 0.03;

            $LoanCaseBillMain->lawyer_bonus_2_per = $prof_bal * 0.02;
            $LoanCaseBillMain->lawyer_bonus_3_per = $prof_bal * 0.03;

            $LoanCaseBillMain->staff_bonus_2_per_p1 = $prof_bal2 * 0.02;
            $LoanCaseBillMain->staff_bonus_3_per_p1 = $prof_bal2 * 0.03;

            $LoanCaseBillMain->lawyer_bonus_2_per_p1 = $prof_bal2 * 0.02;
            $LoanCaseBillMain->lawyer_bonus_3_per_p1 = $prof_bal2 * 0.03;

            $LoanCaseBillMain->targeted_amt_new = $newAmount;


            // $LoanCaseBillMain->prof_balance= $prof_bal;



            $collected_amt = $LoanCaseBillMain->collected_amt_new;
            $LoanCaseBillMain->disb_balance = $disb - $bill_dis;
            $LoanCaseBillMain->actual_balance = $LoanCaseBillMain->disb_balance + $LoanCaseBillMain->prof_balance;

            $LoanCaseBillMain->trust_disb = $trust_dis;
            $LoanCaseBillMain->trust_recv = $trust_recv;
            $LoanCaseBillMain->adv = $trust_dis + $bill_dis;


            if ($collected_amt >= 0) {

                if (($collected_amt - $disb) >= 0) {
                    $collected_amt = $collected_amt - $disb;
                    $LoanCaseBillMain->disb_recv = $disb;
                } else {
                    $LoanCaseBillMain->disb_recv = $collected_amt;
                    $collected_amt = 0;
                }
            }

            $LoanCaseBillMain->save();
        }


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

        $template_file_name = 'receipt_template.docx';

        if (in_array($loanCase->branch_id, [2, 3, 4, 5, 6])) {
            if ($loanCase->branch_id == 2) {
                $template_file_name = 'receipt_template_p.docx';
            } else if ($loanCase->branch_id == 3) {
                $template_file_name = 'receipt_template_dpc.docx';
            } else if ($loanCase->branch_id == 4) {
                $template_file_name = 'receipt_template_r.docx';
            } else if ($loanCase->branch_id == 5) {
                $template_file_name = 'receipt_template_dp.docx';
            } else if ($loanCase->branch_id == 6) {
                $template_file_name = 'receipt_template_il.docx';
            }
        }

        $template_folder_name_temp = $template_path . $template_file_name;
        // $template_folder_name_temp = $template_path . 'receipt_template.docx';

        $file_folder_name_temp = $case_path . 'file_case_' . $case_id . '/account/' . $genFileName;
        $file_folder_name_temp_pdf = $case_path . 'file_case_' . $case_id . '/account/' . $genFileNamepdf;

        $domPdfPath = base_path('vendor/dompdf/dompdf');
        \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');

        // $templateWord = new \PhpOffice\PhpWord\TemplateProcessor($template_folder_name_temp);
        $templateWord = new TemplateProcessor($template_folder_name_temp);

        $templateWord->setValue('case_ref_no', htmlspecialchars($case_ref_no));

        // $LoanCaseTrust = LoanCaseTrust::where('id', '=', $id)->first();
        $LoanCaseTrust = VoucherMain::where('id', '=', $id)->first();

        if ($LoanCaseTrust->office_account_id != 0) {
            $OfficeBankAccount = OfficeBankAccount::where('id', '=', $LoanCaseTrust->office_account_id)->first();
            $templateWord->setValue('bank_account', $OfficeBankAccount->account_no);
        }

        $bill_disburse = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->select('vd.*', 'a.name as account_name', 'vm.transaction_id as transaction_id',  'vm.id as voucher_id', 'vm.voucher_no', 'vm.remark as remark', 'vm.office_account_id as v_office_account_id', 'vm.payee as payee', 'vm.payment_date as payment_date')
            ->where('vm.id', '=',  $id)
            ->first();

        $templateWord->setValue('amount',  number_format($LoanCaseTrust->total_amount, 2, ".", ","));
        $templateWord->setValue('payee_name', htmlspecialchars($LoanCaseTrust->payee));
        $templateWord->setValue('cheque_no', htmlspecialchars($LoanCaseTrust->transaction_id));
        $templateWord->setValue('date', htmlspecialchars($LoanCaseTrust->payment_date));
        $templateWord->setValue('receipt_no', htmlspecialchars($bill_disburse->voucher_no));
        $templateWord->setValue('receipt_no', htmlspecialchars($running_no));
        $templateWord->setValue('payment_desc', htmlspecialchars($LoanCaseTrust->remark));

        // $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);

        $amount_in_en = $this->numberTowords($LoanCaseTrust->total_amount);

        if ($amount_in_en == null) {
            $amount_in_en = "Zero";
        }


        $templateWord->setValue('amount_en',  strtoupper($amount_in_en));

        $file_folder_name_temp = 'app/documents/tempfile/' . $genFileName;

        $templateWord->saveAs($file_folder_name_temp);


        $location = 'cases/' . $id . '/account';

        // $file = file_get_contents($file_folder_name_temp);

        // $path = Storage::disk('Wasabi')->put(
        //     $location . '/' . $genFileName,
        //     $file
        // );

        // if (File::exists(public_path($file_folder_name_temp))) {
        //     File::delete(public_path($file_folder_name_temp));
        // }


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
        $current_user = auth()->user();
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


        // $running_no = (int)$parameter->parameter_value_1 + 1;
        // $parameter->parameter_value_1 = $running_no;
        // $parameter->save();

        $genFileName = time() . '_' . 'receipt' . '.docx';
        $genFileNamepdf = time() . '_' . 'receipt' . '.pdf';

        $template_file_name = 'receipt_template.docx';

        if (in_array($loanCase->branch_id, [2, 3, 4, 5, 6])) {
            if ($loanCase->branch_id == 2) {
                $template_file_name = 'receipt_template_p.docx';
            } else if ($loanCase->branch_id == 3) {
                $template_file_name = 'receipt_template_dpc.docx';
            } else if ($loanCase->branch_id == 4) {
                $template_file_name = 'receipt_template_r.docx';
            } else if ($loanCase->branch_id == 5) {
                $template_file_name = 'receipt_template_dp.docx';
            } else if ($loanCase->branch_id == 6) {
                $template_file_name = 'receipt_template_il.docx';
            }
        }

        $template_folder_name_temp = $template_path . $template_file_name;
        // $template_folder_name_temp = $template_path . 'receipt_template.docx';
        $file_folder_name_temp = $case_path . 'file_case_' . $case_id . '/account/' . $genFileName;
        $file_folder_name_temp_pdf = $case_path . 'file_case_' . $case_id . '/account/' . $genFileNamepdf;

        $domPdfPath = base_path('vendor/dompdf/dompdf');
        \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');

        // $templateWord = new \PhpOffice\PhpWord\TemplateProcessor($template_folder_name_temp);
        $templateWord = new TemplateProcessor($template_folder_name_temp);

        $templateWord->setValue('case_ref_no', htmlspecialchars($case_ref_no));

        // $LoanCaseTrust = LoanCaseTrust::where('id', '=', $id)->first();

        $bill_disburse = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->select('vd.*', 'a.name as account_name', 'vm.transaction_id as transaction_id',  'vm.id as voucher_id', 'vm.voucher_no', 'vm.remark as remark', 'vm.office_account_id as v_office_account_id', 'vm.payee as payee', 'vm.payment_date as payment_date')
            ->where('vm.id', '=',  $id)
            ->where('vm.voucher_type', '=',  4)
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
        $templateWord->setValue('receipt_no', htmlspecialchars($bill_disburse->voucher_no));
        $templateWord->setValue('payment_desc', htmlspecialchars($bill_disburse->remark));

        // $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);

        $amount_in_en = $this->numberTowords($bill_disburse->amount);

        if ($amount_in_en == null) {
            $amount_in_en = "Zero";
        }


        $templateWord->setValue('amount_en',  strtoupper($amount_in_en));


        $file_folder_name_temp = 'app/documents/tempfile/' . $genFileName;

        $templateWord->saveAs($file_folder_name_temp);



        // $Content = \PhpOffice\PhpWord\IOFactory::load($file_folder_name_temp);

        // $PDFWriter = \PhpOffice\PhpWord\IOFactory::createWriter($Content,'PDF');
        // $PDFWriter->save($file_folder_name_temp_pdf); 



        // return;


        return response()->json(['status' => $status, 'data' => $file_folder_name_temp]);
    }

    public function generateReceiptController(Request $request, $case_id)
    {
        $status = 1;
        $message = '';
        $transaction_id = '';
        $receipt_no = '';
        $account_no = '';
        $file_name = '';
        $obj = [];
        $amount = 0;
        $fileTemplateId = $request->input('obj_id');
        $array = (array) $fileTemplateId;


        // get loan caseref no
        $case = LoanCase::where('id', '=', $case_id)->first();
        $Branch = Branch::where('id', '=', $case->branch_id)->first();
        $case_ref_no = $case->case_ref_no;

        $lawyer = Users::where('id', '=', $case->lawyer_id)->first();
        $clerk = Users::where('id', '=', $case->clerk_id)->first();
        //=====================================================

        $parameter = Parameter::where('parameter_type', '=', 'receipt_running_no')->first();




        $file_name = time() . '_' . 'receipt';

        if ($request->input('type') == 'BILL') {
            $obj = DB::table('voucher_details AS vd')
                ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
                ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
                ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
                ->select('vd.*', 'a.name as account_name', 'vm.transaction_id as transaction_id',  'vm.id as voucher_id', 'vm.voucher_no', 'vm.remark as remark', 'vm.office_account_id as office_account_id', 'vm.payee as payee', 'vm.payment_date as payment_date')
                ->where('vm.id', '=',  $request->input('obj_id'))
                // ->where('vd.status', '=',  4)
                ->first();


            $amount = $obj->amount;
            $transaction_id = $obj->transaction_id;
            $receipt_no = $obj->voucher_no;;
            $account_no = $obj->transaction_id;
        } else {
            // $this->updateLoanCaseTrustMain($request, $case_id);
            $obj = LoanCaseTrustMain::where('case_id', '=', $case_id)->first();


            if ($obj->cheque_no) {
                $running_no = $obj->cheque_no;
            } else {
                $running_no = (int)$parameter->parameter_value_1 + 1;
                $parameter->parameter_value_1 = $running_no;
                $parameter->save();

                $obj->cheque_no = $running_no;
                $obj->save();
            }





            $amount = $request->input('sum_amount');
            $transaction_id = $obj->transaction_id;
            $receipt_no = $running_no;
        }


        if ($obj->office_account_id != 0) {
            $OfficeBankAccount = OfficeBankAccount::where('id', '=', $obj->office_account_id)->first();

            if ($OfficeBankAccount) {
                $account_no = $OfficeBankAccount->account_no;
            }
        }

        $OfficeBankAccount = OfficeBankAccount::where('id', '=', $obj->office_account_id)->first();


        $amount_in_en = $this->numberTowords($amount);

        if ($amount_in_en == null) {
            $amount_in_en = "Zero";
        }

        $amount_in_en = strtoupper($amount_in_en);

        return response()->json([
            'view' => view('dashboard.case.section.d-receipt-ori', compact('obj', 'account_no', 'amount_in_en', 'case', 'Branch', 'file_name', 'amount', 'transaction_id', 'receipt_no'))->render(),
        ]);

        // return response()->json(['status' => $status, 'data' => $file_folder_name_temp]);
    }

    public function generateBillLumdReceipt(Request $request, $id, $case_id)
    {
        $status = 1;
        $message = '';
        $account_no = '';
        $file_name = '';
        $amount = $request->input('amount');
        // $array = (array) $fileTemplateId;

        $this->updateBillPrintDetail($request, $id);


        // get loan caseref no
        $loanCase = LoanCase::where('id', '=', $case_id)->first();
        $case_ref_no = $loanCase->case_ref_no;
        //=====================================================


        if (in_array($loanCase->branch_id, [2, 3, 4, 5, 6])) {
            if ($loanCase->branch_id == 2) {
                $template_file_name = 'receipt_template_p.docx';
            } else if ($loanCase->branch_id == 3) {
                $template_file_name = 'receipt_template_dpc.docx';
            } else if ($loanCase->branch_id == 4) {
                $template_file_name = 'receipt_template_r.docx';
            } else if ($loanCase->branch_id == 5) {
                $template_file_name = 'receipt_template_dp.docx';
            } else if ($loanCase->branch_id == 6) {
                $template_file_name = 'receipt_template_il.docx';
            }
        }


        $case = LoanCase::where('id', '=', $case_id)->first();
        $Branch = Branch::where('id', '=', $case->branch_id)->first();
        $case_ref_no = $case->case_ref_no;

        $file_name = time() . '_' . 'receipt';

        $obj = LoanCaseBillMain::where('id', '=', $id)->first();
        // $OfficeBankAccount = OfficeBankAccount::where('id', '=', $obj->office_account_id)->first();
        $amount = $request->input('amount');
        $transaction_id = $request->input('transaction_id');
        $receipt_no = $request->input('voucher_no');

        if ($obj->office_account_id != 0) {
            $OfficeBankAccount = OfficeBankAccount::where('id', '=', $obj->office_account_id)->first();

            if ($OfficeBankAccount) {
                $account_no = $OfficeBankAccount->account_no;
            }
        }

        // $templateWord->setValue('amount',   number_format($amount, 2, ".", ","));
        // $templateWord->setValue('payee_name', htmlspecialchars($LoanCaseTrustMain->payee));
        // $templateWord->setValue('cheque_no', htmlspecialchars($request->input('transaction_id')));
        // $templateWord->setValue('date', htmlspecialchars($LoanCaseTrustMain->payment_date));
        // $templateWord->setValue('receipt_no', htmlspecialchars($request->input('voucher_no')));
        // $templateWord->setValue('payment_desc', htmlspecialchars($LoanCaseTrustMain->remark));

        // $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);

        $amount_in_en = $this->numberTowords($amount);


        if ($amount_in_en == null) {
            $amount_in_en = "Zero";
        }
        $amount_in_en = strtoupper($amount_in_en);

        return response()->json([
            'view' => view('dashboard.case.section.d-receipt-ori', compact('obj', 'account_no', 'amount_in_en', 'case', 'Branch', 'amount', 'transaction_id', 'receipt_no', 'file_name'))->render(),
        ]);
    }

    public function generateBillLumdReceiptBak(Request $request, $id, $case_id)
    {
        $status = 1;
        $message = '';
        $amount = $request->input('amount');
        // $array = (array) $fileTemplateId;

        $this->updateBillPrintDetail($request, $id);



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


        // $running_no = (int)$parameter->parameter_value_1 + 1;
        // $parameter->parameter_value_1 = $running_no;
        // $parameter->save();

        $genFileName = time() . '_' . 'receipt' . '.docx';
        $genFileNamepdf = time() . '_' . 'receipt' . '.pdf';

        $template_file_name = 'receipt_template.docx';

        if (in_array($loanCase->branch_id, [2, 3, 4, 5, 6])) {
            if ($loanCase->branch_id == 2) {
                $template_file_name = 'receipt_template_p.docx';
            } else if ($loanCase->branch_id == 3) {
                $template_file_name = 'receipt_template_dpc.docx';
            } else if ($loanCase->branch_id == 4) {
                $template_file_name = 'receipt_template_r.docx';
            } else if ($loanCase->branch_id == 5) {
                $template_file_name = 'receipt_template_dp.docx';
            } else if ($loanCase->branch_id == 6) {
                $template_file_name = 'receipt_template_il.docx';
            }
        }

        $template_folder_name_temp = $template_path . $template_file_name;
        // $template_folder_name_temp = $template_path . 'receipt_template.docx';
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
        $templateWord->setValue('cheque_no', htmlspecialchars($request->input('transaction_id')));
        $templateWord->setValue('date', htmlspecialchars($LoanCaseTrustMain->payment_date));
        $templateWord->setValue('receipt_no', htmlspecialchars($request->input('voucher_no')));
        $templateWord->setValue('payment_desc', htmlspecialchars($LoanCaseTrustMain->remark));

        // $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);

        $amount_in_en = $this->numberTowords($amount);

        if ($amount_in_en == null) {
            $amount_in_en = "Zero";
        }


        $templateWord->setValue('amount_en',  strtoupper($amount_in_en));


        $file_folder_name_temp = 'app/documents/tempfile/' . $genFileName;

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

        $template_file_name = 'receipt_template.docx';

        if (in_array($loanCase->branch_id, [2, 3, 4, 5, 6])) {
            if ($loanCase->branch_id == 2) {
                $template_file_name = 'receipt_template_p.docx';
            } else if ($loanCase->branch_id == 3) {
                $template_file_name = 'receipt_template_dpc.docx';
            } else if ($loanCase->branch_id == 4) {
                $template_file_name = 'receipt_template_r.docx';
            } else if ($loanCase->branch_id == 5) {
                $template_file_name = 'receipt_template_dp.docx';
            } else if ($loanCase->branch_id == 6) {
                $template_file_name = 'receipt_template_il.docx';
            }
        }

        $template_folder_name_temp = $template_path . $template_file_name;
        // $template_folder_name_temp = $template_path . 'receipt_template.docx';
        $file_folder_name_temp = $case_path . 'file_case_' . $case_id . '/account/' . $genFileName;
        $file_folder_name_temp_pdf = $case_path . 'file_case_' . $case_id . '/account/' . $genFileNamepdf;

        $domPdfPath = base_path('vendor/dompdf/dompdf');
        \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');

        // $templateWord = new \PhpOffice\PhpWord\TemplateProcessor($template_folder_name_temp);
        $templateWord = new TemplateProcessor($template_folder_name_temp);

        $templateWord->setValue('case_ref_no', htmlspecialchars($case_ref_no));

        $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $case_id)->first();

        if ($LoanCaseTrustMain->office_account_id != 0) {
            $OfficeBankAccount = OfficeBankAccount::where('id', '=', $LoanCaseTrustMain->office_account_id)->first();
            $templateWord->setValue('bank_account', $OfficeBankAccount->account_no);
        }

        // $templateWord->setValue('amount',  number_format($LoanCaseTrustMain->total_received, 2, ".", ","));
        $templateWord->setValue('amount',  number_format($request->input('sum_amount'), 2, ".", ","));
        $templateWord->setValue('payee_name', htmlspecialchars($LoanCaseTrustMain->payee));
        $templateWord->setValue('cheque_no', htmlspecialchars($LoanCaseTrustMain->transaction_id));
        $templateWord->setValue('date', htmlspecialchars($LoanCaseTrustMain->payment_date));
        $templateWord->setValue('receipt_no', htmlspecialchars($running_no));
        $templateWord->setValue('payment_desc', htmlspecialchars($LoanCaseTrustMain->remark));

        // $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);

        $amount_in_en = $this->numberTowords($request->input('sum_amount'));

        if ($amount_in_en == null) {
            $amount_in_en = "Zero";
        }


        $templateWord->setValue('amount_en',  strtoupper($amount_in_en));


        $file_folder_name_temp = 'app/documents/tempfile/' . $genFileName;

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

        if ($LoanCaseBillMain->bln_invoice == 1) {
            return response()->json(['status' => 2, 'message' => 'Current quotation already converted to invoice']);
        }

        $LoanCase = LoanCase::where('id', '=', $LoanCaseBillMain->case_id)->first();

        $LoanCaseBillMain->bln_invoice = 1;
        $LoanCaseBillMain->invoice_branch_id = $LoanCase->branch_id;
        $LoanCaseBillMain->invoice_date = date('Y-m-d H:i:s');
        $LoanCaseBillMain->invoice_to = $LoanCaseBillMain->bill_to;
        $LoanCaseBillMain->save();

        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $LoanCaseBillMain->case_id;
        $AccountLog->bill_id = $id;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->action = 'convertQuotationToInvoice';
        $AccountLog->desc = $current_user->name . ' converted bill(' . $LoanCaseBillMain->bill_no . ') to invoice ';
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        $this->loadQuotationToInvoice($id);

        return response()->json(['status' => 1, 'message' => 'Converted to Invoice']);
    }

    function SplitInvoice(Request $request, $id)
    {

        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        if ($LoanCaseBillMain->bln_invoice == 1) {
            return response()->json(['status' => 2, 'message' => 'Current quotation already converted to invoice']);
        }

        $LoanCase = LoanCase::where('id', '=', $LoanCaseBillMain->case_id)->first();

        $LoanCaseBillMain->bln_invoice = 1;
        $LoanCaseBillMain->invoice_branch_id = $LoanCase->branch_id;
        $LoanCaseBillMain->invoice_date = date('Y-m-d H:i:s');
        $LoanCaseBillMain->invoice_to = $LoanCaseBillMain->bill_to;
        $LoanCaseBillMain->save();

        // Sync bln_invoice to invoice records
        LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->update(['bln_invoice' => 1]);

        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $LoanCaseBillMain->case_id;
        $AccountLog->bill_id = $id;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->action = 'convertQuotationToInvoice';
        $AccountLog->desc = $current_user->name . ' converted bill(' . $LoanCaseBillMain->bill_no . ') to invoice ';
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        $this->loadQuotationToInvoice($id);

        return response()->json(['status' => 1, 'message' => 'Converted to Invoice']);
    }

    function RevertInvoiceBacktoQuotation(Request $request, $id)
    {
        $revert_invoice_count = 1;

        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        if ($LoanCaseBillMain->bln_invoice == 0) {
            return response()->json(['status' => 2, 'message' => 'Current invoice already reverted to bill']);
        }

        $LoanCaseBillMain->bln_invoice = 0;
        $LoanCaseBillMain->total_amt_inv = 0;

        if ($request->input('is_reserve_runningno') == 0) {
            $LoanCaseBillMain->invoice_no = '';

            $revert_invoice_count = LoanCaseInvoiceMain::where('loan_case_main_bill_id',  $id)->count();

            LoanCaseInvoiceMain::where('loan_case_main_bill_id',  $id)->delete();
        } else {
            LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->update([
                'bill_party_id' => 0,
                'bln_invoice' => 0  // Sync bln_invoice when reverting
            ]);
        }
            LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->update(['bill_party_id' => 0]);
        }

        $LoanCaseBillMain->save();

        // delete invoice details
        LoanCaseInvoiceDetails::where('loan_case_main_bill_id',  $id)->delete();

        InvoiceBillingParty::where('loan_case_main_bill_id', $id)->update(['loan_case_main_bill_id' => 0, 'invoice_main_id' => 0]);
        // LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->update(['bill_party_id' => 0, 'invoice_main_id' => 0]);

        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $LoanCaseBillMain->case_id;
        $AccountLog->bill_id = $id;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->action = 'ReverrtInvoice';
        $AccountLog->desc = $current_user->name . ' reverted Invoice (' . $LoanCaseBillMain->invoice_no . ') to bill ';
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        $this->checkInvRunningNoUsed($id, true, $revert_invoice_count);

        // //Revert the running no
        // $LoanCase = LoanCase::where('id', '=', $LoanCaseBillMain->case_id)->first();

        // if($LoanCase->branch_id == 2)
        // {
        //     $running_no = (int)filter_var($LoanCase->case_ref_no, FILTER_SANITIZE_NUMBER_INT);
        //     $newPuchong =  substr( $running_no, 0, 1 ) === "7";

        //     if($newPuchong == 1)
        //     {
        //         $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)
        //         ->where('parameter_value_2', 'A')->first();
        //     }
        //     else
        //     {
        //         $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)->first();

        //     }
        // }
        // else
        // {
        //     $parameter = Parameter::where('parameter_type', 'like', '%invoice_running_no%')->where('parameter_value_3', $LoanCase->branch_id)->first();
        // }



        // $running_no = (int)$parameter->parameter_value_1;
        // $parameter->parameter_value_1 = $running_no-1;
        // $parameter->save();


        return response()->json(['status' => 1, 'message' => 'Reverted the invoice']);
    }

    function convertToSST(Request $request, $id)
    {
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        $LoanCaseBillMain->bln_sst = 1;
        $LoanCaseBillMain->save();

        // Sync bln_sst to invoice records
        LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->update(['bln_sst' => 1]);

        return response()->json(['status' => 1, 'message' => 'Converted to SST']);
    }

    function loadBillDisb(Request $request)
    {
        $current_user = auth()->user();

        $bill_disburse = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->leftJoin('office_bank_account as b', 'b.id', '=', 'vm.office_account_id')
            ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
            ->leftJoin('users AS u', 'u.id', '=', 'vm.created_by')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->select(
                'vd.*',
                'a.name as account_name',
                'vm.id as voucher_id',
                'vm.voucher_no',
                'b.name as client_bank_name',
                'b.short_code as bank_short_code',
                'vm.lawyer_approval as lawyer_approval',
                'vm.account_approval as account_approval',
                'vm.remark as remark',
                'vm.payment_date',
                'vm.transaction_id as transaction_id',
                'u.name as requestor'
            )
            // ->where('vd.case_id', '=',  $request->input('case_id'))
            ->where('bd.loan_case_main_bill_id', '=',  $request->input('bill_id'))
            ->where('vd.status', '<>',  4)
            ->where('vd.status', '<>',  99)
            ->where('vm.status', '<>',  99)
            ->orderBy('vm.created_at', 'desc')
            ->get();

        $LoanCaseBillMain = LoanCaseBillMain::where('case_id', $request->input('case_id'))
            ->where('status', '<>', 99)
            ->where('id', '<>', $request->input('bill_id'))
            ->get();


        return response()->json([
            'disburse' => view('dashboard.case.table.tbl-bill-disburse-move-list', compact('bill_disburse', 'current_user',))->render(),
            'bill' => view('dashboard.case.section.d-move-bill', compact('LoanCaseBillMain', 'current_user',))->render(),
            'LoanCaseBillMain' => $LoanCaseBillMain,
        ]);
    }

    public function MoveBill(Request $request)
    {
        $error_check = [];
        // $AccountDetailsId = VoucherDetails::whereIn('id', json_decode($request->input('movelist')))->pluck('account_details_id');

        // $AccountItemID = LoanCaseBillDetails::whereIn('id', $AccountDetailsId)->pluck('account_item_id');
        // $accountItemCheck = LoanCaseBillDetails::where('loan_case_main_bill_id', $request->input('target_bill'))->whereNotIn('account_item_id', $AccountItemID)->count();


        //     return response()->json(['status' => 2, 'message' =>  $accountItemCheck]);

        // if (count($AccountDetailsId) != $accountItemCheck) {
        //     return response()->json(['status' => 2, 'message' => 'Some account item not found in the destination bill']);
        // }

        $accountItemIDs = LoanCaseBillDetails::whereIn('id', function ($query) use ($request) {
            $query->select('account_details_id')
                ->from('voucher_details')
                ->whereIn('id', json_decode($request->input('movelist')));
        })->pluck('account_item_id')->unique();

        // Step 2: Get all account_item_id that already exist in the target_bill
        $targetBillItemIDs = LoanCaseBillDetails::where('loan_case_main_bill_id', $request->input('target_bill'))
            ->pluck('account_item_id')
            ->unique();

        // Step 3: Compare to see if all accountItemIDs exist in target bill
        $missingItems = $accountItemIDs->diff($targetBillItemIDs);

        if ($missingItems->isNotEmpty()) {
            return response()->json(['status' => 2, 'message' => 'Some account items not found in the destination bill']);
        }

        // return bill details to current and update the target bill details
        $VoucherDetails = VoucherDetails::whereIn('id', json_decode($request->input('movelist')))->get();

        DB::beginTransaction();

        for ($i = 0; $i < count($VoucherDetails); $i++) {
            $currentBillDetails = LoanCaseBillDetails::where('id', $VoucherDetails[$i]->account_details_id)->first();

            if ($currentBillDetails) {
                $currentBillDetails->amount += $VoucherDetails[$i]->amount;
                $currentBillDetails->save();

                $targetBillDetails = LoanCaseBillDetails::where('loan_case_main_bill_id', $request->input('target_bill'))->where('account_item_id', $currentBillDetails->account_item_id)->first();

                if ($targetBillDetails) {


                    $targetBillDetails->amount -= $VoucherDetails[$i]->amount;

                    if ($targetBillDetails->amount < 0) {
                        array_push($error_check, $VoucherDetails[$i]->id);
                    }

                    $targetBillDetails->save();

                    $VoucherDetails[$i]->account_details_id = $targetBillDetails->id;
                    $VoucherDetails[$i]->save();
                }
            }
        }

        // Update case main bill id in voucher main & ledger
        $VoucherMainID = VoucherDetails::whereIn('id', json_decode($request->input('movelist')))->pluck('voucher_main_id');

        VoucherMain::whereIn('id', $VoucherMainID)->update(['case_bill_main_id' => $request->input('target_bill')]);

        LedgerEntriesV2::whereIn('key_id', $VoucherMainID)->where('type', 'BILL_DISB')
            ->where('loan_case_main_bill_id', $request->input('current_bill'))->update(['loan_case_main_bill_id' => $request->input('target_bill')]);


        $this->updateBillCaseBillDisb($request->input('case_id'), $request->input('current_bill'));
        $this->updateBillCaseBillDisb($request->input('case_id'), $request->input('target_bill'));


        //Get info and create account log
        $VoucherMainVoucherNo = VoucherDetails::whereIn('id', json_decode($request->input('movelist')))->pluck('voucher_no');

        $currentBill = LoanCaseBillMain::where('id', $request->input('current_bill'))->first();
        $targetBill = LoanCaseBillMain::where('id', $request->input('target_bill'))->first();

        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $request->input('case_id');
        $AccountLog->bill_id = $request->input('current_bill');
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->action = 'Move_Bill';
        $AccountLog->desc = $current_user->name . ' Move items (' . json_encode($VoucherMainVoucherNo) . ') from Bill no:[' . $currentBill->bill_no . '] to Bill no:[' . $targetBill->bill_no . ']';
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        if (count($error_check) > 0) {
            return response()->json(['status' => 2, 'error_check' => $error_check, 'message' => 'Some account item balance not sufficient in the destination bill']);
        } else {
            DB::commit();
        }

        return response()->json(['status' => 1, 'message' => 'Voucher item(s) move to destination bill']);
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

        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        $loanCaseBillDetails->loan_case_main_bill_id = $id;
        $loanCaseBillDetails->account_item_id = $request->input('details_id');
        $loanCaseBillDetails->min = 0;
        $loanCaseBillDetails->max = 0;
        $loanCaseBillDetails->need_approval = 0;
        if ($request->input('catID') == 1) {
            // $loanCaseBillDetails->quo_amount = (float)$request->input('NewAmount')  * 1.06;
            // $loanCaseBillDetails->amount = (float)$request->input('NewAmount')  * 1.06;
            $loanCaseBillDetails->quo_amount = (float)$request->input('NewAmount');
            $loanCaseBillDetails->amount = (float)$request->input('NewAmount');

            $loanCaseBillDetails->sst = (float)$request->input('NewAmount')  * ($LoanCaseBillMain->sst_rate * 0.01);
        } else {
            $loanCaseBillDetails->quo_amount = (float)$request->input('NewAmount');
            $loanCaseBillDetails->amount = (float)$request->input('NewAmount');
        }

        $loanCaseBillDetails->quo_amount_no_sst = $request->input('NewAmount');
        $loanCaseBillDetails->status = 1;
        $loanCaseBillDetails->created_at = date('Y-m-d H:i:s');
        $loanCaseBillDetails->save();




        // // update all vaue
        $this->updatePfeeDisbAmount($id);

        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();
        $case_id = $LoanCaseBillMain->case_id;

        $AccountItem = AccountItem::where('id', '=', $request->input('details_id'))->first();

        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $case_id;
        $AccountLog->bill_id = $id;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = $request->input('NewAmount');
        $AccountLog->action = 'Add';
        $AccountLog->desc = $current_user->name . ' add new item (' . $AccountItem->name . ') for ' . $request->input('NewAmount');
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        return response()->json(['status' => 1, 'message' => 'yes']);
    }

    public function deleteQuotationItem(Request $request, $id)
    {
        $disburse_amt = 0;
        $account_item = 0;
        // $VoucherDetails = VoucherDetails::where('account_details_id', '=', $request->input('details_id'))->where('voucher_type', '=', 1)->get();

        $VoucherDetails = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->select('vd.*')
            ->where('account_details_id', '=', $request->input('details_id'))
            ->where('vm.status', '<>', 99)
            ->where('vm.voucher_type', '=', 1)
            ->get();

        // $VoucherDetails = DB::table('voucher_details AS vd')
        //     ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
        //     ->select('vm.*', 'vd.amount')
        //     ->where('account_details_id', '=', $request->input('details_id'))
        //     ->where('vm.status', '=',  1)
        //     ->get();

        if (count($VoucherDetails)  > 0) {
            for ($i = 0; $i < count($VoucherDetails); $i++) {
                $disburse_amt += $VoucherDetails[$i]->amount;
            }
        }


        if ($disburse_amt > 0) {
            return response()->json(['status' => 2, 'message' => 'This item already have disbursement, not allow to delete']);
        }

        $LoanCaseBillDetails = LoanCaseBillDetails::where('id', '=', $request->input('details_id'))->first();
        $account_item = $LoanCaseBillDetails->account_item_id;

        $LoanCaseBillDetails->delete();

        // // update all vaue
        $this->updatePfeeDisbAmount($id);

        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();
        $case_id = $LoanCaseBillMain->case_id;

        $AccountItem = AccountItem::where('id', '=', $account_item)->first();

        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $case_id;
        $AccountLog->bill_id = $id;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->action = 'Delete';
        $AccountLog->desc = $current_user->name . ' deleted item (' . $AccountItem->name . ')';
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        return response()->json(['status' => 1, 'message' => 'yes']);
    }

    public function addInvoiceItem(Request $request, $id)
    {
        $current_user = auth()->user();

        $party_count = EInvoiceContoller::getPartyCount($id);

        $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->get();

        if (count($LoanCaseInvoiceMain) > 0) {
            for ($i = 0; $i < count($LoanCaseInvoiceMain); $i++) {
                $LoanCaseInvoiceDetails = new LoanCaseInvoiceDetails();

                $LoanCaseInvoiceDetails->loan_case_main_bill_id = $id;
                $LoanCaseInvoiceDetails->invoice_main_id = $LoanCaseInvoiceMain[$i]->id;
                $LoanCaseInvoiceDetails->account_item_id = $request->input('details_id');
                $LoanCaseInvoiceDetails->quotation_item_id = 0;
                $LoanCaseInvoiceDetails->amount = $request->input('NewAmount') / $party_count;
                $LoanCaseInvoiceDetails->ori_invoice_amt = $request->input('NewAmount');
                $LoanCaseInvoiceDetails->quo_amount = $request->input('NewAmount');
                $LoanCaseInvoiceDetails->remark = '';
                $LoanCaseInvoiceDetails->created_by = $current_user->id;
                $LoanCaseInvoiceDetails->status = 1;
                $LoanCaseInvoiceDetails->created_at = date('Y-m-d H:i:s');

                $LoanCaseInvoiceDetails->save();
            }
        }

        $this->updatePfeeDisbAmountINV($id);

        return response()->json(['status' => 1, 'message' => 'yes']);
    }

    public function deleteInvoiceItem(Request $request, $id)
    {
        $disburse_amt = 0;
        $account_item = 0;

        $LoanCaseInvoiceDetails = LoanCaseInvoiceDetails::where('id', '=', $request->input('details_id'))->first();

        LoanCaseInvoiceDetails::where('loan_case_main_bill_id', $LoanCaseInvoiceDetails->loan_case_main_bill_id)
            ->where('account_item_id', $LoanCaseInvoiceDetails->account_item_id)->delete();


        $this->updatePfeeDisbAmountINV($LoanCaseInvoiceDetails->loan_case_main_bill_id);

        return response()->json(['status' => 1, 'message' => 'yes']);
    }

    public function updateQuotationValue(Request $request)
    {
        $new_quo_amount = 0;
        $new_amount = 0;
        $addtional_value = 0;
        $main_bill_id = 0;
        $current_user = auth()->user();

        $disburse_amt = 0;

        // if ($request->input('typeID') == 2) {
        //     $LoanCaseBillDetails = LoanCaseBillDetails::where('id', '=', $request->input('details_id'))->first();

        //     if ($request->input('catID') == 1) {
        //         $new_quo_amount =  (float)$request->input('NewAmount')  * 1.06;
        //         $LoanCaseBillDetails->invoice_amount = $new_quo_amount;
        //     } else {
        //         $new_quo_amount = $request->input('NewAmount');
        //         $LoanCaseBillDetails->invoice_amount = $new_quo_amount;
        //     }
        //     $LoanCaseBillDetails->invoice_amount_no_sst = $request->input('NewAmount');
        //     $LoanCaseBillDetails->save();


        //     return response()->json(['status' => 1, 'message' => 'yes']);
        // }


        if ($request->input('NewAmount') != null) {
            $LoanCaseBillDetails = LoanCaseBillDetails::where('id', '=', $request->input('details_id'))->first();


            $AccountItem = AccountItem::where('id', '=', $LoanCaseBillDetails->account_item_id)->first();

            if (!in_array($current_user->branch_id, [1])) {
                if (!in_array($current_user->menuroles, ['account', 'maker', 'admin']) && !in_array($current_user->id, [122,158,165,167,177,176]))
                {
                    if ($request->input('NewAmount') < $AccountItem->min) {
                        return response()->json(['status' => 2, 'message' => 'Edited amount cannot be lesser than mimimun value']);
                    }
                }
            } 





            // $VoucherDetails = VoucherDetails::where('account_details_id', '=', $request->input('details_id'))->where('account_approval', '=', $request->input('details_id'))->get();

            $VoucherDetails = DB::table('voucher_details AS vd')
                ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
                ->select('vm.*', 'vd.amount')
                ->where('account_details_id', '=', $request->input('details_id'))
                ->where('vm.voucher_type', '=', 1)
                ->where('vm.status', '<>', 99)
                ->where('vm.account_approval', '<>',  2)
                ->get();

            if (count($VoucherDetails)  > 0) {
                for ($i = 0; $i < count($VoucherDetails); $i++) {
                    $disburse_amt += $VoucherDetails[$i]->amount;
                }
            }



            if ($LoanCaseBillDetails) {
                $main_bill_id = $LoanCaseBillDetails->loan_case_main_bill_id;
                $quo_amount_no_sst = $LoanCaseBillDetails->quo_amount_no_sst;
                $quo_amount = $LoanCaseBillDetails->quo_amount;
                $amount = $LoanCaseBillDetails->amount;
                $sst = 0;

                if ($request->input('catID') == 1 || $request->input('catID') == 4) {
                    $new_quo_amount =  (float)$request->input('NewAmount')  * 1.06;
                } else {
                    $new_quo_amount = $request->input('NewAmount');
                }

                if ($request->input('NewAmount') >= $disburse_amt) {
                    $LoanCaseBillDetails->quo_amount_no_sst = $request->input('NewAmount');
                    $LoanCaseBillDetails->quo_amount = $new_quo_amount;
                    $LoanCaseBillDetails->amount = $request->input('NewAmount') - $disburse_amt;
                    $LoanCaseBillDetails->save();
                } else {
                    return response()->json(['status' => 2, 'message' => 'Edited amount cannot be lesser than disburse amount']);
                }
            }

            // update all value
            $sumTotalAmount = 0;
            $sumTotalAmountCase = 0;
            $case_id = 0;

            $this->updatePfeeDisbAmount($main_bill_id);


            $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $main_bill_id)->first();
            $case_id = $LoanCaseBillMain->case_id;


            $this->updateLoanCaseBillInfo($case_id);

            $current_user = auth()->user();
            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = $case_id;
            $AccountLog->bill_id = $main_bill_id;
            $AccountLog->ori_amt = $quo_amount;
            $AccountLog->new_amt = $new_quo_amount;
            $AccountLog->bill_id = $main_bill_id;
            $AccountLog->action = 'Update';
            $AccountLog->desc = $current_user->name . ' update item (' . $request->input('item_name') . ') from ' . $quo_amount . ' to ' . $new_quo_amount;
            $AccountLog->status = 1;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();

            return response()->json(['status' => 1, 'message' => 'yes']);
        }
    }


    public function updateInvoiceValue(Request $request)
    {
        if ($request->input('NewAmount') != null) {
            $LoanCaseInvoiceDetails = LoanCaseInvoiceDetails::where('id', '=', $request->input('details_id'))->first();

            if ($LoanCaseInvoiceDetails) {
                // $LoanCaseInvoiceDetails->amount = $request->input('NewAmount');
                $LoanCaseInvoiceDetails->ori_invoice_amt = $request->input('NewAmount');
                $LoanCaseInvoiceDetails->save();

                $party_count = EInvoiceContoller::getPartyCount($LoanCaseInvoiceDetails->loan_case_main_bill_id);

                // FIXED: Only update the specific item that was changed, not all items in the bill
                $LoanCaseInvoiceDetails->amount = $request->input('NewAmount') / $party_count;
                $LoanCaseInvoiceDetails->save();

                // Use the new calculation method that properly handles all account categories and SST
                $this->updatePfeeDisbAmountINV($LoanCaseInvoiceDetails->loan_case_main_bill_id);
                
                // EInvoiceContoller::updateInvoiceDetailsAmt(0, $LoanCaseInvoiceDetails->loan_case_main_bill_id, 0);
            }

            return response()->json(['status' => 1, 'message' => 'yes']);
        }
    }


    public function updateQuotationValuebak(Request $request)
    {
        $new_quo_amount = 0;
        $new_amount = 0;
        $addtional_value = 0;
        $main_bill_id = 0;


        $disburse_amt = 0;



        if ($request->input('typeID') == 2) {
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


            $VoucherDetails = VoucherDetails::where('account_details_id', '=', $request->input('details_id'))->get();

            if (count($VoucherDetails)  > 0) {
                for ($i = 0; $i < count($VoucherDetails); $i++) {
                    $disburse_amt += $VoucherDetails[$i]->amount;
                }
            }


            if ($LoanCaseBillDetails) {
                $main_bill_id = $LoanCaseBillDetails->loan_case_main_bill_id;
                $quo_amount_no_sst = $LoanCaseBillDetails->quo_amount_no_sst;
                $quo_amount = $LoanCaseBillDetails->quo_amount;
                $amount = $LoanCaseBillDetails->amount;

                if ($request->input('catID') == 1) {
                    $new_quo_amount =  (float)$request->input('NewAmount')  * 1.06;
                    $new_quo_amount = $request->input('NewAmount');
                    // return $new_quo_amount;
                    // $new_quo_amount = $request->input('NewAmount');
                    $addtional_value = $new_quo_amount - $quo_amount;
                } else {
                    $new_quo_amount = $request->input('NewAmount');
                    $addtional_value = $new_quo_amount - $quo_amount_no_sst;
                }



                if ($new_quo_amount >= $quo_amount_no_sst) {

                    $addtional_value = $request->input('NewAmount') - $quo_amount;

                    return $addtional_value;
                    $LoanCaseBillDetails->quo_amount_no_sst = $request->input('NewAmount');
                    $LoanCaseBillDetails->quo_amount = $new_quo_amount;
                    $LoanCaseBillDetails->amount = $LoanCaseBillDetails->amount + $addtional_value;
                    $LoanCaseBillDetails->save();
                } else {
                    $VoucherDetails = VoucherDetails::where('account_details_id', '=', $request->input('details_id'))->get();
                    $voucherSum = 0;

                    return 222;

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

            $this->updatePfeeDisbAmount($main_bill_id);

            // $LoanCaseBillDetails = LoanCaseBillDetails::where('loan_case_main_bill_id', '=', $main_bill_id)->get();

            // for ($i = 0; $i < count($LoanCaseBillDetails); $i++) {
            //     $sumTotalAmount += $LoanCaseBillDetails[$i]->amount;
            // }


            $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $main_bill_id)->first();
            // $LoanCaseBillMain->total_amt = $sumTotalAmount;
            $case_id = $LoanCaseBillMain->case_id;
            // $LoanCaseBillMain->save();

            // if ($case_id != 0 && $case_id != null) {
            //     $LoanCaseBillMain = LoanCaseBillMain::where('case_id', '=', $case_id)->get();

            //     for ($i = 0; $i < count($LoanCaseBillMain); $i++) {
            //         $sumTotalAmountCase += $LoanCaseBillMain[$i]->total_amt;
            //     }

            //     $LoanCase = LoanCase::where('id', '=', $case_id)->first();
            //     $LoanCase->targeted_bill = $sumTotalAmountCase;
            //     $LoanCase->save();
            // }

            $AccountItem = AccountItem::where('id', '=', $request->input('details_id'))->first();

            $current_user = auth()->user();
            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = $case_id;
            $AccountLog->bill_id = $main_bill_id;
            // $AccountLog->ori_amt = $quo_amount;
            $AccountLog->new_amt = $new_quo_amount;
            $AccountLog->bill_id = $main_bill_id;
            $AccountLog->action = 'Update';
            $AccountLog->desc = $current_user->name . ' update item (' . $request->input('item_name') . ') from ' . $quo_amount . ' to ' . $new_quo_amount;
            $AccountLog->status = 1;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();

            return response()->json(['status' => 1, 'message' => 'yes']);
        }
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
            ->where('vm.voucher_type', '=',  4)
            ->first();


        return response()->json(['status' => 1, 'data' => $bill_disburse]);
    }

    public function updateTrustValueV2(Request $request, $id)
    {
        // $loanCaseTrust = LoanCaseTrust::where('id', '=', $id)->first();

        $current_user = auth()->user();
        $original_val = 0;

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

        $original_val = $voucherDetails->amount;

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



        if ($voucherMain->voucher_type == 2) {
            // $voucherDetails = VoucherDetails::where('case_id', '=', $case_id)->where('status', '=', 3)->get();

            // $loan_case_trust_main_dis = DB::table('voucher_main as v')
            //     ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            //     ->select('v.*', 'vd.amount')
            //     ->where('v.case_id', '=', $case_id)
            //     ->where('v.voucher_type', '=', 2)
            //     ->where('v.status', '<>', 99)
            //     ->get();

            $loan_case_trust_main_dis = DB::table('voucher_main as v')
                ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
                ->select('v.*', 'vd.amount')
                ->where('v.case_id', '=', $case_id)
                ->where('v.voucher_type', '=', 2)
                ->where('v.account_approval', '=', 1)
                ->where('v.status', '<>', 99)
                ->get();

            for ($i = 0; $i < count($loan_case_trust_main_dis); $i++) {
                $sumTrust += $loan_case_trust_main_dis[$i]->amount;
            }


            $loanCase = LoanCaseTrustMain::where('case_id', '=', $case_id)->first();

            // $total_trust = (float)($loanCase->total_trust) + (float)($request->input('amount'));

            // $loanCase->collected_trust = $collected_trust;

            $loanCase->total_used = $sumTrust;
            $loanCase->updated_at = date('Y-m-d H:i:s');
            $loanCase->save();

            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = $case_id;
            $AccountLog->bill_id = 0;
            $AccountLog->object_id = $id;
            $AccountLog->object_id_2 = 0;
            $AccountLog->ori_amt = $original_val;
            $AccountLog->new_amt = $voucherDetails->amount;
            $AccountLog->action = 'ChangeVoucherValue';
            $AccountLog->desc = $current_user->name . ' Update voucher (' . $voucherMain->voucher_no . ') - Trust  (RM ' . $original_val . ' --> RM ' . $voucherDetails->amount . ')';;
            $AccountLog->status = 1;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();
        } else if ($voucherMain->voucher_type == 3) {
            // $voucherDetails = VoucherDetails::where('case_id', '=', $case_id)->where('status', '=', 3)->get();

            $loan_case_trust_main_dis = DB::table('voucher_main as v')
                ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
                ->select('v.*', 'vd.amount')
                ->where('v.case_id', '=', $case_id)
                ->where('v.voucher_type', '=', 3)
                ->where('v.status', '<>', 99)
                ->get();

            for ($i = 0; $i < count($loan_case_trust_main_dis); $i++) {
                $sumTrust += $loan_case_trust_main_dis[$i]->amount;
            }
            $loanCase = LoanCaseTrustMain::where('case_id', '=', $case_id)->first();

            // $total_trust = (float)($loanCase->total_trust) + (float)($request->input('amount'));

            // $loanCase->collected_trust = $collected_trust;

            $loanCase->total_received = $sumTrust;
            $loanCase->updated_at = date('Y-m-d H:i:s');
            $loanCase->save();

            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = $case_id;
            $AccountLog->bill_id = 0;
            $AccountLog->object_id = $id;
            $AccountLog->object_id_2 = 0;
            $AccountLog->ori_amt = $original_val;
            $AccountLog->new_amt = $voucherDetails->amount;
            $AccountLog->action = 'ChangeVoucherValue';
            $AccountLog->desc = $current_user->name . ' Update voucher (' . $voucherMain->voucher_no . ') - Trust  (RM ' . $original_val . ' --> RM ' . $voucherDetails->amount . ')';;
            $AccountLog->status = 1;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();



            //update ledger entries
            $LedgerEntries = LedgerEntries::where('key_id', '=', $id)->where('type', '=', 'TRUSTRECEIVE')->first();

            if ($LedgerEntries) {
                $transaction_id = '';

                if ($voucherMain->transaction_id != null) {
                    $transaction_id = $voucherMain->transaction_id;
                }


                $LedgerEntries->transaction_id = $transaction_id;
                $LedgerEntries->amount = $voucherMain->total_amount;
                $LedgerEntries->bank_id = $voucherMain->office_account_id;
                $LedgerEntries->remark = $voucherMain->remark;
                $LedgerEntries->status = 1;
                $LedgerEntries->updated_at = date('Y-m-d H:i:s');
                $LedgerEntries->date = $voucherMain->payment_date;
                $LedgerEntries->type = 'TRUSTRECEIVE';
                $LedgerEntries->save();
            }


            $LedgerEntries = LedgerEntriesV2::where('key_id', '=', $id)->where('status', 1)->where('type', '=', 'TRUST_RECV')->first();

            if ($LedgerEntries) {
                $transaction_id = '';

                if ($voucherMain->transaction_id != null) {
                    $transaction_id = $voucherMain->transaction_id;
                }


                $LedgerEntries->transaction_id = $transaction_id;
                $LedgerEntries->amount = $voucherMain->total_amount;
                $LedgerEntries->payee = $voucherMain->payee;
                $LedgerEntries->bank_id = $voucherMain->office_account_id;
                $LedgerEntries->remark = $voucherMain->remark;
                $LedgerEntries->status = 1;
                $LedgerEntries->updated_at = date('Y-m-d H:i:s');
                $LedgerEntries->date = $voucherMain->payment_date;
                $LedgerEntries->save();
            }
        }



        // updateallValue


        // if ($voucherMain->voucher_type = 2) {
        //     $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $id)->first();



        //     $loan_case_trust_main_dis = DB::table('voucher_main as v')
        //         ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
        //         ->select('v.*')
        //         ->where('v.case_id', '=', $id)
        //         ->where('v.voucher_type', '=', 2)
        //         ->where('v.status', '<>', 99)
        //         ->get();

        //     $total_sum = 0;

        //     if (count($loan_case_trust_main_dis) > 0) {
        //         for ($i = 0; $i < count($loan_case_trust_main_dis); $i++) {
        //             $total_sum += $loan_case_trust_main_dis[$i]->total_amount;
        //         }
        //     }

        //     $LoanCaseTrustMain->total_used = $total_sum;
        //     $LoanCaseTrustMain->updated_at = date('Y-m-d H:i:s');
        //     $LoanCaseTrustMain->save();
        // } else if ($voucherMain->voucher_type = 3) {
        //     $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $case_id)->first();

        //     $loan_case_trust_main_receive = DB::table('voucher_main as v')
        //         ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
        //         ->select('v.*')
        //         ->where('v.case_id', '=', $case_id)
        //         ->where('v.voucher_type', '=', 3)
        //         ->where('v.status', '<>', 99)
        //         ->get();

        //     $total_sum = 0;

        //     if (count($loan_case_trust_main_receive) > 0) {
        //         for ($i = 0; $i < count($loan_case_trust_main_receive); $i++) {
        //             $total_sum += $loan_case_trust_main_receive[$i]->total_amount;
        //         }
        //     }

        //     if ($LoanCaseTrustMain == null) {
        //         $LoanCaseTrustMain = new LoanCaseTrustMain();
        //         $LoanCaseTrustMain->case_id =  $id;
        //         // $LoanCaseTrustMain->payment_type =  $request->input('payment_type');
        //         // $LoanCaseTrustMain->payment_date =  $request->input('payment_date');
        //         // $LoanCaseTrustMain->transaction_id =  $request->input('transaction_id');
        //         // $LoanCaseTrustMain->office_account_id =  $request->input('office_account_id');
        //         $LoanCaseTrustMain->status =  1;
        //         $LoanCaseTrustMain->total_received = $total_sum;
        //         $LoanCaseTrustMain->updated_by = $current_user->id;
        //         $LoanCaseTrustMain->updated_at = date('Y-m-d H:i:s');
        //         $LoanCaseTrustMain->created_at = date('Y-m-d H:i:s');
        //         $LoanCaseTrustMain->total_received = $total_sum;
        //         $LoanCaseTrustMain->save();
        //         // $LoanCaseTrustMain->save();
        //     } else {

        //         $LoanCaseTrustMain->total_received = $total_sum;
        //         $LoanCaseTrustMain->updated_at = date('Y-m-d H:i:s');
        //         $LoanCaseTrustMain->save();
        //     }
        // }






        return response()->json(['status' => 1, 'data' => 'Data updated']);
    }

    public function updateBillReceiveValue(Request $request, $id, $bill_id)
    {
        $loanCaseTrust = VoucherMain::where('id', '=', $id)->first();

        $current_user = auth()->user();

        // $loanCaseTrust->payment_type =  $request->input('payment_type');
        // $loanCaseTrust->cheque_no =  $request->input('cheque_no');
        // $loanCaseTrust->bank_id =  $request->input('bank_id');
        // $loanCaseTrust->bank_account =  $request->input('bank_account');
        // $loanCaseTrust->payment_date =  $request->input('payment_date');
        // $loanCaseTrust->payee =  $request->input('payee_name');
        // $loanCaseTrust->transaction_id =  $request->input('transaction_id');
        // $loanCaseTrust->voucher_no =  $request->input('voucher_no');
        // $loanCaseTrust->office_account_id =  $request->input('office_account_id');
        // $loanCaseTrust->remark =  $request->input('payment_desc');
        // // $loanCaseTrust->status =  1;
        // // $loanCaseTrust->updated_by = $current_user->id;
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

        //update ledger entries
        $LedgerEntries = LedgerEntries::where('key_id', '=', $id)->where('type', '=', 'BILLRECEIVE')->first();

        $transaction = '';

        if ($voucherMain->transaction_id != null) {
            $transaction = $voucherMain->transaction_id;
        }

        $LedgerEntries->transaction_id = $transaction;
        $LedgerEntries->amount = $voucherMain->total_amount;
        $LedgerEntries->bank_id = $voucherMain->office_account_id;
        $LedgerEntries->remark = $voucherMain->remark;
        $LedgerEntries->status = 1;
        $LedgerEntries->updated_at = date('Y-m-d H:i:s');
        $LedgerEntries->date = $voucherMain->payment_date;
        $LedgerEntries->save();


        $LedgerEntries = LedgerEntriesV2::where('key_id', '=', $id)->where('status', 1)->where('type', '=', 'BILL_RECV')->first();

        $LedgerEntries->transaction_id = $transaction;
        $LedgerEntries->amount = $voucherMain->total_amount;
        $LedgerEntries->payee = $voucherMain->payee;
        $LedgerEntries->bank_id = $voucherMain->office_account_id;
        $LedgerEntries->remark = $voucherMain->remark;
        $LedgerEntries->status = 1;
        $LedgerEntries->updated_at = date('Y-m-d H:i:s');
        $LedgerEntries->date = $voucherMain->payment_date;
        $LedgerEntries->save();


        $case_id = $voucherMain->case_id;
        $sumTrust = 0;

        // $bill_receive = DB::table('voucher_main as v')
        //     ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
        //     ->select('v.*', 'vd.amount')
        //     ->where('v.case_id', '=', $case_id)
        //     ->where('vd.status', '=',  4)
        //     ->where('v.status', '<>', 99)
        //     ->get();

        // for ($i = 0; $i < count($bill_receive); $i++) {
        //     $sumTrust += $bill_receive[$i]->amount;
        // }

        // $loanCase = LoanCase::where('id', '=', $case_id)->first();

        // $loanCase->collected_bill = $sumTrust;
        // $loanCase->updated_at = date('Y-m-d H:i:s');
        // $loanCase->save();

        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $bill_id)->first();

        if ($request->input('payment_date')) {
            $LoanCaseBillMain->payment_receipt_date = $request->input('payment_date');
            $LoanCaseBillMain->save();
        }

        $sumTrust = 0;

        // $bill_receive = DB::table('voucher_main as v')
        //     ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
        //     ->select('v.*', 'vd.amount')
        //     ->where('v.case_id', '=', $case_id)
        //     ->where('vd.status', '=',  4)
        //     ->where('v.case_bill_main_id', '=', $bill_id)
        //     ->where('v.status', '<>', 99)
        //     ->get();

        // for ($i = 0; $i < count($bill_receive); $i++) {
        //     $sumTrust += $bill_receive[$i]->amount;
        // }

        // $LoanCaseBillMain->collected_amt = $sumTrust;
        // $LoanCaseBillMain->updated_at = date('Y-m-d H:i:s');
        // $LoanCaseBillMain->save();

        // $bill_id

        $this->updateBillandCaseFigure($case_id, $bill_id);

        return response()->json(['status' => 1, 'data' => 'Data updated']);
    }

    public function deleteReceivedBill(Request $request, $id, $bill_id)
    {
        $loanCaseTrust = VoucherMain::where('id', '=', $id)->first();

        $current_user = auth()->user();

        $voucherMain = voucherMain::where('id', '=', $id)->first();

        $voucherMain->status = 99;
        $voucherMain->save();

        // delete ledger entries
        $LedgerEntries = LedgerEntries::where('key_id', '=', $id)->where('type', '=', 'BILLRECEIVE')->first();

        if ($LedgerEntries) {
            $LedgerEntries->delete();
        }

        $case_id = $voucherMain->case_id;

        $this->updateBillandCaseFigure($case_id, $bill_id);

        $LedgerEntries = LedgerEntriesV2::where('key_id', '=', $id)->where('status', 1)->where('type', '=', 'BILL_RECV')->first();
        $LedgerEntries->status = 99;
        $LedgerEntries->save();


        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $case_id;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->bill_id = $id;
        $AccountLog->action = 'Delete';
        $AccountLog->desc = $current_user->name . ' deleted received bill (' . $voucherMain->voucher_no . ')';
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        return response()->json(['status' => 1, 'data' => 'Bill deleted']);
    }

    public function deleteReceivedTrust(Request $request, $id)
    {
        $loanCaseTrust = VoucherMain::where('id', '=', $id)->first();

        $current_user = auth()->user();

        $voucherMain = voucherMain::where('id', '=', $id)->first();

        if ($voucherMain->transaction_id != null || $voucherMain->transaction_id != '') {
            return response()->json(['status' => 2, 'message' => 'Transaction ID record created, not allow to delete']);
        }

        if ($voucherMain->is_recon == 1) {
            return response()->json(['status' => 2, 'message' => 'Record already recon, not allow to delete']);
        }

        $voucherMain->status = 99;
        $voucherMain->save();

        $LedgerEntries = LedgerEntries::where('key_id', '=', $id)->where('type', '=', 'TRUSTRECEIVE')->first();

        if ($LedgerEntries) {
            $LedgerEntries->delete();
        }

        $case_id = $voucherMain->case_id;
        $sumTrust = 0;

        VoucherController::reverseTrustDisburse($id);

        // $bill_receive = DB::table('voucher_main as v')
        //     ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
        //     ->select('v.*', 'vd.amount')
        //     ->where('v.case_id', '=', $case_id)
        //     ->where('v.status', '=',  3)
        //     ->where('v.status', '<>', 99)
        //     ->get();


        // for ($i = 0; $i < count($bill_receive); $i++) {
        //     $sumTrust += $bill_receive[$i]->amount;
        // }

        // $loanCase = LoanCase::where('id', '=', $case_id)->first();

        // $loanCase->collected_trust = $sumTrust;
        // $loanCase->updated_at = date('Y-m-d H:i:s');
        // $loanCase->save();

        // $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $case_id)->first();

        // $LoanCaseTrustMain->total_received = $sumTrust;
        // $LoanCaseTrustMain->updated_at = date('Y-m-d H:i:s');
        // $LoanCaseTrustMain->save();

        $LedgerEntries = LedgerEntriesV2::where('key_id', '=', $id)->where('status', 1)->where('type', '=', 'TRUST_RECV')->first();
        $LedgerEntries->status = 99;
        $LedgerEntries->save();

        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $case_id;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->bill_id = $id;
        $AccountLog->action = 'Delete';
        $AccountLog->desc = $current_user->name . ' deleted received trust (' . $voucherMain->voucher_no . ')';
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        return response()->json(['status' => 1, 'data' => 'Trust deleted']);
    }

    public function deleteDisburseTrust(Request $request, $id)
    {
        $loanCaseTrust = VoucherMain::where('id', '=', $id)->first();

        $current_user = auth()->user();

        $voucherMain = voucherMain::where('id', '=', $id)->first();

        if ($voucherMain->transaction_id != null || $voucherMain->transaction_id != '') {
            return response()->json(['status' => 2, 'message' => 'Transaction ID record created, not allow to delete']);
        }

        if ($voucherMain->is_recon == 1) {
            return response()->json(['status' => 2, 'message' => 'Record already recon, not allow to delete']);
        }

        $voucherMain->status = 99;
        $voucherMain->save();

        $LedgerEntries = LedgerEntries::where('key_id', '=', $id)->where('type', '=', 'TRUSTDISB')->first();

        if ($LedgerEntries) {
            $LedgerEntries->delete();
        }

        $LedgerEntries = LedgerEntriesV2::where('key_id', '=', $id)->where('status', 1)->where('type', '=', 'TRUST_DISB')->first();

        if ($LedgerEntries) {
            $LedgerEntries->status = 99;
            $LedgerEntries->save();
        }


        $case_id = $voucherMain->case_id;
        $sumTrust = 0;

        // $bill_disburse = DB::table('voucher_main as v')
        //     ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
        //     ->select('v.*', 'vd.amount')
        //     ->where('v.case_id', '=', $case_id)
        //     ->where('v.status', '=',  2)
        //     ->where('v.status', '<>', 99)
        //     ->get();

        // $bill_disburse = DB::table('voucher_main as v')
        //     ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
        //     ->select('v.*', 'vd.amount')
        //     ->where('v.case_id', '=', $case_id)
        //     ->where('v.voucher_type', '=', 2)
        //     ->where('v.account_approval', '=', 1)
        //     ->where('v.status', '<>', 99)
        //     ->get();

        VoucherController::reverseTrustDisburse($id);

        // for ($i = 0; $i < count($bill_disburse); $i++) {
        //     $sumTrust += $bill_disburse[$i]->amount;
        // }

        // $loanCase = LoanCase::where('id', '=', $case_id)->first();

        // $loanCase->total_trust = $sumTrust;
        // $loanCase->updated_at = date('Y-m-d H:i:s');
        // $loanCase->save();

        // $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $case_id)->first();

        // $LoanCaseTrustMain->total_used = $sumTrust;
        // $LoanCaseTrustMain->updated_at = date('Y-m-d H:i:s');
        // $LoanCaseTrustMain->save();


        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $case_id;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->object_id = $id;
        $AccountLog->action = 'Delete';
        $AccountLog->desc = $current_user->name . ' deleted disburse trust (' . $voucherMain->voucher_no . ')';
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        return response()->json(['status' => 1, 'data' => 'Trust deleted']);
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

        // $LoanCaseBillMain->referral_a1 = $request->input('referral_a1');
        // $LoanCaseBillMain->referral_a2 = $request->input('referral_a2');
        // $LoanCaseBillMain->referral_a3 = $request->input('referral_a3');
        // $LoanCaseBillMain->referral_a4 = $request->input('referral_a4');
        // $LoanCaseBillMain->marketing = $request->input('marketing');
        // $LoanCaseBillMain->uncollected = $request->input('uncollected');

        $referral_a1_id = $LoanCaseBillMain->referral_a1_id;
        $referral_a1_ref_id = $LoanCaseBillMain->referral_a1_ref_id;
        $referral_a1_payment_date = $LoanCaseBillMain->referral_a1_payment_date;
        $referral_a1_trx_id = $LoanCaseBillMain->referral_a1_trx_id;
        $referral_a1 = $LoanCaseBillMain->referral_a1;

        $referral_a2_id = $LoanCaseBillMain->referral_a2_id;
        $referral_a2_ref_id = $LoanCaseBillMain->referral_a2_ref_id;
        $referral_a2_payment_date = $LoanCaseBillMain->referral_a2_payment_date;
        $referral_a2_trx_id = $LoanCaseBillMain->referral_a2_trx_id;
        $referral_a2 = $LoanCaseBillMain->referral_a2;

        $referral_a3_id = $LoanCaseBillMain->referral_a3_id;
        $referral_a3_ref_id = $LoanCaseBillMain->referral_a3_ref_id;
        $referral_a3_payment_date = $LoanCaseBillMain->referral_a3_payment_date;
        $referral_a3_trx_id = $LoanCaseBillMain->referral_a3_trx_id;
        $referral_a3 = $LoanCaseBillMain->referral_a3;

        $referral_a4_id = $LoanCaseBillMain->referral_a4_id;
        $referral_a4_ref_id =  $LoanCaseBillMain->referral_a4_ref_id;
        $referral_a4_payment_date = $LoanCaseBillMain->referral_a4_payment_date;
        $referral_a4_trx_id =  $LoanCaseBillMain->referral_a4_trx_id;
        $referral_a4 = $LoanCaseBillMain->referral_a4;

        $marketing_id = $LoanCaseBillMain->marketing_id;
        $marketing_payment_date = $LoanCaseBillMain->marketing_payment_date;
        $marketing_trx_id = $LoanCaseBillMain->marketing_trx_id;
        $marketing = $LoanCaseBillMain->marketing;
        $uncollected = $LoanCaseBillMain->uncollected;
        $collection_amount = $LoanCaseBillMain->collection_amount;

        $sst_payment_date = $LoanCaseBillMain->sst_payment_date;
        $sst_trx_id = $LoanCaseBillMain->sst_trx_id;

        // $pfee1_receipt_date = $LoanCaseBillMain->pfee1_receipt_date;
        $pfee1_receipt_trx_id = $LoanCaseBillMain->pfee1_receipt_trx_id;

        // $pfee2_receipt_date =  $LoanCaseBillMain->pfee2_receipt_date;
        $pfee2_receipt_trx_id = $LoanCaseBillMain->pfee2_receipt_trx_id;


        $financed_fee = $LoanCaseBillMain->financed_fee;
        $financed_sum = $LoanCaseBillMain->financed_sum;
        $payment_date = $LoanCaseBillMain->payment_date;

        if (in_array($current_user->menuroles, ['admin', 'management', 'account', 'maker'])) {
            $LoanCaseBillMain->referral_a1_payment_date =  $request->input('ref_a1_payment_date');
            $LoanCaseBillMain->referral_a1_trx_id =  $request->input('ref_a1_payment_trx_id');

            $LoanCaseBillMain->referral_a2_payment_date =  $request->input('ref_a2_payment_date');
            $LoanCaseBillMain->referral_a2_trx_id =  $request->input('ref_a2_payment_trx_id');

            $LoanCaseBillMain->referral_a3_payment_date =  $request->input('ref_a3_payment_date');
            $LoanCaseBillMain->referral_a3_trx_id =  $request->input('ref_a3_payment_trx_id');

            $LoanCaseBillMain->referral_a4_payment_date =  $request->input('ref_a4_payment_date');
            $LoanCaseBillMain->referral_a4_trx_id =  $request->input('ref_a4_payment_trx_id');

            $LoanCaseBillMain->sst_payment_date =  $request->input('sst_payment_date');
            $LoanCaseBillMain->sst_trx_id =  $request->input('sst_payment_trx_id');

            $LoanCaseBillMain->pfee1_receipt_date =  $request->input('pfee1_receipt_date');
            $LoanCaseBillMain->pfee1_receipt_trx_id =  $request->input('pfee1_receipt_trx_id');

            $LoanCaseBillMain->pfee2_receipt_date =  $request->input('pfee2_receipt_date');
            $LoanCaseBillMain->pfee2_receipt_trx_id =  $request->input('pfee2_receipt_trx_id');


            $LoanCaseBillMain->disb_name =  $request->input('disb_name');
            $LoanCaseBillMain->disb_amt_manual =  $request->input('disb_amt_manual');
            $LoanCaseBillMain->disb_trx_id =  $request->input('disb_trx_id');
            $LoanCaseBillMain->disb_payment_date =  $request->input('disb_payment_date');
        }




        $LoanCaseBillMain->referral_a1_id =  $request->input('referral_name_1');
        $LoanCaseBillMain->referral_a1_ref_id =  $request->input('referral_id_1');
        // $LoanCaseBillMain->referral_a1_payment_date =  $request->input('ref_a1_payment_date');
        // $LoanCaseBillMain->referral_a1_trx_id =  $request->input('ref_a1_payment_trx_id');
        $LoanCaseBillMain->referral_a1 =  $request->input('ref_a1_amt');



        $LoanCaseBillMain->referral_a2_id =  $request->input('referral_name_2');
        $LoanCaseBillMain->referral_a2_ref_id =  $request->input('referral_id_2');
        // $LoanCaseBillMain->referral_a2_payment_date =  $request->input('ref_a2_payment_date');
        // $LoanCaseBillMain->referral_a2_trx_id =  $request->input('ref_a2_payment_trx_id');
        $LoanCaseBillMain->referral_a2 =  $request->input('ref_a2_amt');

        $LoanCaseBillMain->referral_a3_id =  $request->input('referral_name_3');
        $LoanCaseBillMain->referral_a3_ref_id =  $request->input('referral_id_3');
        // $LoanCaseBillMain->referral_a3_payment_date =  $request->input('ref_a3_payment_date');
        // $LoanCaseBillMain->referral_a3_trx_id =  $request->input('ref_a3_payment_trx_id');
        $LoanCaseBillMain->referral_a3 =  $request->input('ref_a3_amt');


        $LoanCaseBillMain->referral_a4_id =  $request->input('referral_name_4');
        $LoanCaseBillMain->referral_a4_ref_id =  $request->input('referral_id_4');
        // $LoanCaseBillMain->referral_a4_payment_date =  $request->input('ref_a4_payment_date');
        // $LoanCaseBillMain->referral_a4_trx_id =  $request->input('ref_a4_payment_trx_id');
        $LoanCaseBillMain->referral_a4 =  $request->input('ref_a4_amt');


        $LoanCaseBillMain->marketing_id =  $request->input('sales_id');
        $LoanCaseBillMain->marketing_payment_date =  $request->input('sales_payment_date');
        $LoanCaseBillMain->marketing_trx_id =  $request->input('sales_payment_trx_id');
        $LoanCaseBillMain->marketing =  $request->input('marketing_amt');
        $LoanCaseBillMain->uncollected =  $request->input('uncollected_amt');
        $LoanCaseBillMain->collection_amount =  $request->input('collection_amount');



        $LoanCaseBillMain->financed_fee =  $request->input('financed_fee');
        $LoanCaseBillMain->financed_sum =  $request->input('financed_sum');
        $LoanCaseBillMain->payment_date =  $request->input('financed_payment_date');
        // $loanCaseTrust->status =  1;
        // $LoanCaseBillMain->updated_by = $current_user->id;
        $LoanCaseBillMain->updated_at = date('Y-m-d H:i:s');
        $LoanCaseBillMain->save();


        $message = '
        <strong>Referral A1</strong>:&nbsp;' . $referral_a1_id  . '=> ' . $LoanCaseBillMain->referral_a1_id . '<br />
        <strong>Referral A1 Amount</strong>:&nbsp;' . $referral_a1  . '=> ' . $LoanCaseBillMain->referral_a1 . '<br />
        <strong>Referral A1 Payment Date</strong>:&nbsp;' . $referral_a1_payment_date  . '=> ' . $LoanCaseBillMain->referral_a1_payment_date . '<br />
        <strong>Referral A1 TRX ID</strong>:&nbsp;' . $referral_a1_trx_id  . '=> ' . $LoanCaseBillMain->referral_a1_trx_id . '<br /><br />
        <strong>Referral A2</strong>:&nbsp;' . $referral_a2_id  . '=> ' . $LoanCaseBillMain->referral_a2_id . '<br />
        <strong>Referral A2 Amount</strong>:&nbsp;' . $referral_a2  . '=> ' . $LoanCaseBillMain->referral_a2 . '<br />
        <strong>Referral A2 Payment Date</strong>:&nbsp;' . $referral_a2_payment_date  . '=> ' . $LoanCaseBillMain->referral_a2_payment_date . '<br />
        <strong>Referral A2 TRX ID</strong>:&nbsp;' . $referral_a2_trx_id  . '=> ' . $LoanCaseBillMain->referral_a2_trx_id . '<br /><br />
        <strong>Referral A3</strong>:&nbsp;' . $referral_a3_id  . '=> ' . $LoanCaseBillMain->referral_a3_id . '<br />
        <strong>Referral A3 Amount</strong>:&nbsp;' . $referral_a3  . '=> ' . $LoanCaseBillMain->referral_a3 . '<br />
        <strong>Referral A3 Payment Date</strong>:&nbsp;' . $referral_a3_payment_date  . '=> ' . $LoanCaseBillMain->referral_a3_payment_date . '<br />
        <strong>Referral A3 TRX ID</strong>:&nbsp;' . $referral_a3_trx_id  . '=> ' . $LoanCaseBillMain->referral_a3_trx_id . '<br /><br />
        <strong>Referral A4</strong>:&nbsp;' . $referral_a4_id  . '=> ' . $LoanCaseBillMain->referral_a4_id . '<br />
        <strong>Referral A4 Amount</strong>:&nbsp;' . $referral_a4  . '=> ' . $LoanCaseBillMain->referral_a4 . '<br />
        <strong>Referral A4 Payment Date</strong>:&nbsp;' . $referral_a4_payment_date  . '=> ' . $LoanCaseBillMain->referral_a4_payment_date . '<br />
        <strong>Referral A4 TRX ID</strong>:&nbsp;' . $referral_a4_trx_id  . '=> ' . $LoanCaseBillMain->referral_a4_trx_id . '<br /><br />
        <strong>Marketing</strong>:&nbsp;' . $marketing_id  . '=> ' . $LoanCaseBillMain->marketing_id . '<br />
        <strong>Marketing Amount</strong>:&nbsp;' . $marketing  . '=> ' . $LoanCaseBillMain->marketing . '<br />
        <strong>Marketing Payment Date</strong>:&nbsp;' . $marketing_payment_date  . '=> ' . $LoanCaseBillMain->marketing_payment_date . '<br />
        <strong>Marketing TRX ID</strong>:&nbsp;' . $marketing_trx_id  . '=> ' . $LoanCaseBillMain->marketing_trx_id . '<br /><br />
        <strong>uncollected</strong>:&nbsp;' . $uncollected  . '=> ' . $LoanCaseBillMain->uncollected . '<br />
        <strong>collection_amount</strong>:&nbsp;' . $collection_amount  . '=> ' . $LoanCaseBillMain->collection_amount . '<br />
        <strong>SST Payment Date</strong>:&nbsp;' . $sst_payment_date  . '=> ' . $LoanCaseBillMain->sst_payment_date . '<br />
        <strong>SST TRX ID</strong>:&nbsp;' . $sst_trx_id  . '=> ' . $LoanCaseBillMain->sst_trx_id . '<br />
        <strong>PFEE 1 TRX ID</strong>:&nbsp;' . $pfee1_receipt_trx_id  . '=> ' . $LoanCaseBillMain->pfee1_receipt_trx_id . '<br />
        <strong>PFEE 2 TRX ID</strong>:&nbsp;' . $pfee2_receipt_trx_id  . '=> ' . $LoanCaseBillMain->pfee2_receipt_trx_id . '<br />
        <strong>Financed Fee</strong>:&nbsp;' . $financed_fee  . '=> ' . $LoanCaseBillMain->financed_fee . '<br />
        <strong>Financed Sum</strong>:&nbsp;' . $financed_sum  . '=> ' . $LoanCaseBillMain->financed_sum . '<br />
        <strong>Payment Date</strong>:&nbsp;' . $payment_date  . '=> ' . $LoanCaseBillMain->spayment_date . '<br />
        ';


        $this->updatePfeeDisbAmount($id);

        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $LoanCaseBillMain->case_id;
        $AccountLog->bill_id = $LoanCaseBillMain->id;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->action = 'SUMMARY';
        $AccountLog->desc = $message;
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        return response()->json(['status' => 1, 'message' => 'Data updated']);
    }

    public function clearReferral(Request $request, $id)
    {
        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        $current_user = auth()->user();

        if ($request->input('field_type') == 'Referral A1') {
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a1_id', '');
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a1_ref_id', 0);
        }

        if ($request->input('field_type') == 'Referral A2') {
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a2_id', '');
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a2_ref_id', 0);
        }

        if ($request->input('field_type') == 'Referral A3') {
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a3_id', '');
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a3_ref_id', 0);
        }

        if ($request->input('field_type') == 'Referral A4') {
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a4_id', '');
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a4_ref_id', 0);
        }

        return response()->json(['status' => 1, 'message' => 'Referral Removed']);
    }

    public function SaveAccountSummary(Request $request, $id)
    {

        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();
        $LoanCase = LoanCase::where('id', '=', $LoanCaseBillMain->case_id)->first();

        $current_user = auth()->user();

        if ($request->input('field_type') == 'Referral A1') {
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a1_id', $request->input('name'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a1_ref_id', $request->input('referral_id'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a1_payment_date', $request->input('payment_date'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a1_trx_id', $request->input('transaction_id'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a1', $request->input('amount'));
        }

        if ($request->input('field_type') == 'Referral A2') {
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a2_id', $request->input('name'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a2_ref_id', $request->input('referral_id'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a2_payment_date', $request->input('payment_date'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a2_trx_id', $request->input('transaction_id'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a2', $request->input('amount'));
        }

        if ($request->input('field_type') == 'Referral A3') {
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a3_id', $request->input('name'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a3_ref_id', $request->input('referral_id'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a3_payment_date', $request->input('payment_date'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a3_trx_id', $request->input('transaction_id'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a3', $request->input('amount'));
        }

        if ($request->input('field_type') == 'Referral A4') {
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a4_id', $request->input('name'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a4_ref_id', $request->input('referral_id'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a4_payment_date', $request->input('payment_date'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a4_trx_id', $request->input('transaction_id'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'referral_a4', $request->input('amount'));
        }

        if ($request->input('field_type') == 'Marketing') {
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'marketing_id', $LoanCase->sales_user_id);
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'marketing_payment_date', $request->input('payment_date'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'marketing_trx_id', $request->input('transaction_id'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'marketing', $request->input('amount'));
        }

        if ($request->input('field_type') == 'Financed') {
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'financed_fee', $request->input('financed_fee'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'financed_sum', $request->input('financed_sum'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'payment_date', $request->input('payment_date'));
        }


        if ($request->input('field_type') == 'Disb Manual') {
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'disb_amt_manual', $request->input('amount'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'disb_name', $request->input('desc'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'disb_payment_date', $request->input('payment_date'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'disb_trx_id', $request->input('transaction_id'));
        }

        if ($request->input('field_type') == 'Other') {
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'other_amt', $request->input('amount'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'other_name', $request->input('desc'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'other_payment_date', $request->input('payment_date'));
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'other_trx_id', $request->input('transaction_id'));
        }

        if ($request->input('field_type') == 'Collected') {
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'collection_amount', $request->input('amount'));
        }

        if ($request->input('field_type') == 'Uncollected') {
            // return $request->input('amount');
            $this->checkSummaryFieldAndUpdate($LoanCaseBillMain, 'uncollected', $request->input('amount'));
        }

        return response()->json(['status' => 1, 'message' => 'Data updated']);

        // $LoanCaseBillMain->referral_a1 = $request->input('referral_a1');
        // $LoanCaseBillMain->referral_a2 = $request->input('referral_a2');
        // $LoanCaseBillMain->referral_a3 = $request->input('referral_a3');
        // $LoanCaseBillMain->referral_a4 = $request->input('referral_a4');
        // $LoanCaseBillMain->marketing = $request->input('marketing');
        // $LoanCaseBillMain->uncollected = $request->input('uncollected');



        $referral_a2_id = $LoanCaseBillMain->referral_a2_id;
        $referral_a2_ref_id = $LoanCaseBillMain->referral_a2_ref_id;
        $referral_a2_payment_date = $LoanCaseBillMain->referral_a2_payment_date;
        $referral_a2_trx_id = $LoanCaseBillMain->referral_a2_trx_id;
        $referral_a2 = $LoanCaseBillMain->referral_a2;

        $referral_a3_id = $LoanCaseBillMain->referral_a3_id;
        $referral_a3_ref_id = $LoanCaseBillMain->referral_a3_ref_id;
        $referral_a3_payment_date = $LoanCaseBillMain->referral_a3_payment_date;
        $referral_a3_trx_id = $LoanCaseBillMain->referral_a3_trx_id;
        $referral_a3 = $LoanCaseBillMain->referral_a3;

        $referral_a4_id = $LoanCaseBillMain->referral_a4_id;
        $referral_a4_ref_id =  $LoanCaseBillMain->referral_a4_ref_id;
        $referral_a4_payment_date = $LoanCaseBillMain->referral_a4_payment_date;
        $referral_a4_trx_id =  $LoanCaseBillMain->referral_a4_trx_id;
        $referral_a4 = $LoanCaseBillMain->referral_a4;

        $marketing_id = $LoanCaseBillMain->marketing_id;
        $marketing_payment_date = $LoanCaseBillMain->marketing_payment_date;
        $marketing_trx_id = $LoanCaseBillMain->marketing_trx_id;
        $marketing = $LoanCaseBillMain->marketing;
        $uncollected = $LoanCaseBillMain->uncollected;
        $collection_amount = $LoanCaseBillMain->collection_amount;

        $sst_payment_date = $LoanCaseBillMain->sst_payment_date;
        $sst_trx_id = $LoanCaseBillMain->sst_trx_id;

        // $pfee1_receipt_date = $LoanCaseBillMain->pfee1_receipt_date;
        $pfee1_receipt_trx_id = $LoanCaseBillMain->pfee1_receipt_trx_id;

        // $pfee2_receipt_date =  $LoanCaseBillMain->pfee2_receipt_date;
        $pfee2_receipt_trx_id = $LoanCaseBillMain->pfee2_receipt_trx_id;


        $financed_fee = $LoanCaseBillMain->financed_fee;
        $financed_sum = $LoanCaseBillMain->financed_sum;
        $payment_date = $LoanCaseBillMain->payment_date;

        if (in_array($current_user->menuroles, ['admin', 'management', 'account', 'maker'])) {
            $LoanCaseBillMain->referral_a1_payment_date =  $request->input('ref_a1_payment_date');
            $LoanCaseBillMain->referral_a1_trx_id =  $request->input('ref_a1_payment_trx_id');

            $LoanCaseBillMain->referral_a2_payment_date =  $request->input('ref_a2_payment_date');
            $LoanCaseBillMain->referral_a2_trx_id =  $request->input('ref_a2_payment_trx_id');

            $LoanCaseBillMain->referral_a3_payment_date =  $request->input('ref_a3_payment_date');
            $LoanCaseBillMain->referral_a3_trx_id =  $request->input('ref_a3_payment_trx_id');

            $LoanCaseBillMain->referral_a4_payment_date =  $request->input('ref_a4_payment_date');
            $LoanCaseBillMain->referral_a4_trx_id =  $request->input('ref_a4_payment_trx_id');

            $LoanCaseBillMain->sst_payment_date =  $request->input('sst_payment_date');
            $LoanCaseBillMain->sst_trx_id =  $request->input('sst_payment_trx_id');

            $LoanCaseBillMain->pfee1_receipt_date =  $request->input('pfee1_receipt_date');
            $LoanCaseBillMain->pfee1_receipt_trx_id =  $request->input('pfee1_receipt_trx_id');

            $LoanCaseBillMain->pfee2_receipt_date =  $request->input('pfee2_receipt_date');
            $LoanCaseBillMain->pfee2_receipt_trx_id =  $request->input('pfee2_receipt_trx_id');


            $LoanCaseBillMain->disb_name =  $request->input('disb_name');
            $LoanCaseBillMain->disb_amt_manual =  $request->input('disb_amt_manual');
            $LoanCaseBillMain->disb_trx_id =  $request->input('disb_trx_id');
            $LoanCaseBillMain->disb_payment_date =  $request->input('disb_payment_date');
        }




        $LoanCaseBillMain->referral_a1_id =  $request->input('referral_name_1');
        $LoanCaseBillMain->referral_a1_ref_id =  $request->input('referral_id_1');
        // $LoanCaseBillMain->referral_a1_payment_date =  $request->input('ref_a1_payment_date');
        // $LoanCaseBillMain->referral_a1_trx_id =  $request->input('ref_a1_payment_trx_id');
        $LoanCaseBillMain->referral_a1 =  $request->input('ref_a1_amt');



        $LoanCaseBillMain->referral_a2_id =  $request->input('referral_name_2');
        $LoanCaseBillMain->referral_a2_ref_id =  $request->input('referral_id_2');
        // $LoanCaseBillMain->referral_a2_payment_date =  $request->input('ref_a2_payment_date');
        // $LoanCaseBillMain->referral_a2_trx_id =  $request->input('ref_a2_payment_trx_id');
        $LoanCaseBillMain->referral_a2 =  $request->input('ref_a2_amt');

        $LoanCaseBillMain->referral_a3_id =  $request->input('referral_name_3');
        $LoanCaseBillMain->referral_a3_ref_id =  $request->input('referral_id_3');
        // $LoanCaseBillMain->referral_a3_payment_date =  $request->input('ref_a3_payment_date');
        // $LoanCaseBillMain->referral_a3_trx_id =  $request->input('ref_a3_payment_trx_id');
        $LoanCaseBillMain->referral_a3 =  $request->input('ref_a3_amt');


        $LoanCaseBillMain->referral_a4_id =  $request->input('referral_name_4');
        $LoanCaseBillMain->referral_a4_ref_id =  $request->input('referral_id_4');
        // $LoanCaseBillMain->referral_a4_payment_date =  $request->input('ref_a4_payment_date');
        // $LoanCaseBillMain->referral_a4_trx_id =  $request->input('ref_a4_payment_trx_id');
        $LoanCaseBillMain->referral_a4 =  $request->input('ref_a4_amt');


        $LoanCaseBillMain->marketing_id =  $request->input('sales_id');
        $LoanCaseBillMain->marketing_payment_date =  $request->input('sales_payment_date');
        $LoanCaseBillMain->marketing_trx_id =  $request->input('sales_payment_trx_id');
        $LoanCaseBillMain->marketing =  $request->input('marketing_amt');
        $LoanCaseBillMain->uncollected =  $request->input('uncollected_amt');
        $LoanCaseBillMain->collection_amount =  $request->input('collection_amount');



        $LoanCaseBillMain->financed_fee =  $request->input('financed_fee');
        $LoanCaseBillMain->financed_sum =  $request->input('financed_sum');
        $LoanCaseBillMain->payment_date =  $request->input('financed_payment_date');
        // $loanCaseTrust->status =  1;
        // $LoanCaseBillMain->updated_by = $current_user->id;
        $LoanCaseBillMain->updated_at = date('Y-m-d H:i:s');
        $LoanCaseBillMain->save();


        $message = '
        <strong>Referral A1</strong>:&nbsp;' . $referral_a1_id  . '=> ' . $LoanCaseBillMain->referral_a1_id . '<br />
        <strong>Referral A1 Amount</strong>:&nbsp;' . $referral_a1  . '=> ' . $LoanCaseBillMain->referral_a1 . '<br />
        <strong>Referral A1 Payment Date</strong>:&nbsp;' . $referral_a1_payment_date  . '=> ' . $LoanCaseBillMain->referral_a1_payment_date . '<br />
        <strong>Referral A1 TRX ID</strong>:&nbsp;' . $referral_a1_trx_id  . '=> ' . $LoanCaseBillMain->referral_a1_trx_id . '<br /><br />
        <strong>Referral A2</strong>:&nbsp;' . $referral_a2_id  . '=> ' . $LoanCaseBillMain->referral_a2_id . '<br />
        <strong>Referral A2 Amount</strong>:&nbsp;' . $referral_a2  . '=> ' . $LoanCaseBillMain->referral_a2 . '<br />
        <strong>Referral A2 Payment Date</strong>:&nbsp;' . $referral_a2_payment_date  . '=> ' . $LoanCaseBillMain->referral_a2_payment_date . '<br />
        <strong>Referral A2 TRX ID</strong>:&nbsp;' . $referral_a2_trx_id  . '=> ' . $LoanCaseBillMain->referral_a2_trx_id . '<br /><br />
        <strong>Referral A3</strong>:&nbsp;' . $referral_a3_id  . '=> ' . $LoanCaseBillMain->referral_a3_id . '<br />
        <strong>Referral A3 Amount</strong>:&nbsp;' . $referral_a3  . '=> ' . $LoanCaseBillMain->referral_a3 . '<br />
        <strong>Referral A3 Payment Date</strong>:&nbsp;' . $referral_a3_payment_date  . '=> ' . $LoanCaseBillMain->referral_a3_payment_date . '<br />
        <strong>Referral A3 TRX ID</strong>:&nbsp;' . $referral_a3_trx_id  . '=> ' . $LoanCaseBillMain->referral_a3_trx_id . '<br /><br />
        <strong>Referral A4</strong>:&nbsp;' . $referral_a4_id  . '=> ' . $LoanCaseBillMain->referral_a4_id . '<br />
        <strong>Referral A4 Amount</strong>:&nbsp;' . $referral_a4  . '=> ' . $LoanCaseBillMain->referral_a4 . '<br />
        <strong>Referral A4 Payment Date</strong>:&nbsp;' . $referral_a4_payment_date  . '=> ' . $LoanCaseBillMain->referral_a4_payment_date . '<br />
        <strong>Referral A4 TRX ID</strong>:&nbsp;' . $referral_a4_trx_id  . '=> ' . $LoanCaseBillMain->referral_a4_trx_id . '<br /><br />
        <strong>Marketing</strong>:&nbsp;' . $marketing_id  . '=> ' . $LoanCaseBillMain->marketing_id . '<br />
        <strong>Marketing Amount</strong>:&nbsp;' . $marketing  . '=> ' . $LoanCaseBillMain->marketing . '<br />
        <strong>Marketing Payment Date</strong>:&nbsp;' . $marketing_payment_date  . '=> ' . $LoanCaseBillMain->marketing_payment_date . '<br />
        <strong>Marketing TRX ID</strong>:&nbsp;' . $marketing_trx_id  . '=> ' . $LoanCaseBillMain->marketing_trx_id . '<br /><br />
        <strong>uncollected</strong>:&nbsp;' . $uncollected  . '=> ' . $LoanCaseBillMain->uncollected . '<br />
        <strong>collection_amount</strong>:&nbsp;' . $collection_amount  . '=> ' . $LoanCaseBillMain->collection_amount . '<br />
        <strong>SST Payment Date</strong>:&nbsp;' . $sst_payment_date  . '=> ' . $LoanCaseBillMain->sst_payment_date . '<br />
        <strong>SST TRX ID</strong>:&nbsp;' . $sst_trx_id  . '=> ' . $LoanCaseBillMain->sst_trx_id . '<br />
        <strong>PFEE 1 TRX ID</strong>:&nbsp;' . $pfee1_receipt_trx_id  . '=> ' . $LoanCaseBillMain->pfee1_receipt_trx_id . '<br />
        <strong>PFEE 2 TRX ID</strong>:&nbsp;' . $pfee2_receipt_trx_id  . '=> ' . $LoanCaseBillMain->pfee2_receipt_trx_id . '<br />
        <strong>Financed Fee</strong>:&nbsp;' . $financed_fee  . '=> ' . $LoanCaseBillMain->financed_fee . '<br />
        <strong>Financed Sum</strong>:&nbsp;' . $financed_sum  . '=> ' . $LoanCaseBillMain->financed_sum . '<br />
        <strong>Payment Date</strong>:&nbsp;' . $payment_date  . '=> ' . $LoanCaseBillMain->spayment_date . '<br />
        ';


        // LogsController::generateLog(
        //     $param_log = [
        //         'case_id' => $case->id,
        //         'object_id' => $previous_client,
        //         'object_id_2' => $case->customer_id,
        //         'action' => 'change_client',
        //         'desc' => ' Change client to [' . $customer->name . ']',
        //     ]
        // );


        $this->updatePfeeDisbAmount($id);

        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $LoanCaseBillMain->case_id;
        $AccountLog->bill_id = $LoanCaseBillMain->id;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->action = 'SUMMARY';
        $AccountLog->desc = $message;
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        return response()->json(['status' => 1, 'data' => 'Data updated']);
    }

    public function checkSummaryFieldAndUpdate($Object, $field_name, $value)
    {
        $current_user = auth()->user();



        if ($Object->getOriginal($field_name) != $value) {
            $orivalue = $Object->getOriginal($field_name);
            $Object->update([$field_name => $value]);

            LogsController::createAccountLog(
                $param_log = [
                    'case_id' => $Object->case_id,
                    'bill_id' => $Object->id,
                    'object_id' => 0,
                    'ori_amt' => 0,
                    'new_amt' => 0,
                    'action' => 'account_summary',
                    'desc' => $current_user->name . ' update summary [' . $field_name . '] from ' . $orivalue . ' => ' . $value,
                ]

            );
        }
    }

    public function deleteBill(Request $request, $id)
    {
        $disburse_amt = 0;
        $account_item = 0;


        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        if ($LoanCaseBillMain) {
            // $bill_disb = DB::table('voucher_details as v')
            //     ->leftJoin('voucher_main as m', 'm.id', '=', 'v.voucher_main_id')
            //     ->leftJoin('loan_case_bill_details as b', 'v.account_details_id', '=', 'b.id')
            //     ->where('b.loan_case_main_bill_id', '=', $id)
            //     ->where('m.account_approval', '<>', 2)
            //     ->where('v.status', '<>', 99)
            //     ->where('m.status', '<>', 99)
            //     ->get();

            $bill_disb = DB::table('voucher_details AS vd')
                ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
                ->leftJoin('office_bank_account as b', 'b.id', '=', 'vm.office_account_id')
                ->leftJoin('loan_case_bill_details AS bd', 'bd.id', '=', 'vd.account_details_id')
                ->leftJoin('users AS u', 'u.id', '=', 'vm.created_by')
                ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
                ->where('vd.case_id', '=',  $LoanCaseBillMain->case_id)
                ->where('bd.loan_case_main_bill_id', '=',  $id)
                ->where('vd.status', '<>',  4)
                ->where('vd.status', '<>',  99)
                ->where('vm.status', '<>',  99)
                ->get();

            if (count($bill_disb) > 0) {
                return response()->json(['status' => 0, 'message' => 'Disbursement exist, please request account to assist']);
            }

            $bill_receive = DB::table('voucher_main as v')
                ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
                ->select('v.*', 'vd.amount')
                ->where('v.case_bill_main_id', '=', $id)
                ->where([['vd.status', '=',  4], ['vd.status', '<>',  99]])
                ->where('v.status', '<>', 99)
                ->get();

            if (count($bill_receive) > 0) {
                return response()->json(['status' => 0, 'message' => 'Payment exist, please request account to assist']);
            }

            if ($LoanCaseBillMain->collected_amt > 0) {
                return response()->json(['status' => 0, 'message' => 'Payment exist, please request account to assist']);
            }

            $LoanCaseBillDetails = LoanCaseBillDetails::where('loan_case_main_bill_id', $id)->get();

            if (count($LoanCaseBillDetails)) {
                for ($i = 0; $i < count($LoanCaseBillDetails); $i++) {
                    $LoanCaseBillDetails[$i]->status = 99;
                }
            }

            DB::table('loan_case_bill_details')->where('loan_case_main_bill_id', $id)->update(['status' => 99]);
            DB::table('loan_case_bill_main')->where('id', $id)->update(['status' => 99]);

            $this->updateLoanCaseBillInfo($LoanCaseBillMain->case_id);

            // BonusEstimate::where('case_id', $LoanCaseBillMain->case_id)->where('bill_id', $id)->update(['status' => 0]);


            $current_user = auth()->user();
            $AccountLog = new AccountLog();
            $AccountLog->user_id = $current_user->id;
            $AccountLog->case_id = $LoanCaseBillMain->case_id;
            $AccountLog->ori_amt = 0;
            $AccountLog->new_amt = 0;
            $AccountLog->bill_id = $id;
            $AccountLog->action = 'Delete';
            $AccountLog->desc = $current_user->name . ' deleted bill (' . $LoanCaseBillMain->bill_no . ')';
            $AccountLog->status = 1;
            $AccountLog->created_at = date('Y-m-d H:i:s');
            $AccountLog->save();
        } else {
            return response()->json(['status' => 0, 'message' => 'No bill found']);
        }


        return response()->json(['status' => 1, 'message' => 'Bill deleted', 'view' => $this->loadMainBillTable($LoanCaseBillMain->case_id)]);
    }

    public function updateLoanCaseBillInfo($id)
    {
        $totalTargetAmount = 0;
        $totalReceive = 0;
        $LoanCase = LoanCase::where('id', '=', $id)->where('status', '<>', 99)->first();

        if (!$LoanCase) {
            return;
        }

        $LoanCaseBillMain = LoanCaseBillMain::where('case_id', '=', $id)->where('status', '=', 1)->get();

        if (count($LoanCaseBillMain)) {
            for ($i = 0; $i < count($LoanCaseBillMain); $i++) {
                $totalTargetAmount += ($LoanCaseBillMain[$i]->total_amt);
            }
        }

        $LoanCase->targeted_bill = $totalTargetAmount;
        $LoanCase->save();

        return;
    }

    public function submitNotes(Request $request, $id)
    {

        $return_id = 0;
        $current_user = auth()->user();
        $view = null;

        if ($request->input('note_type') == 1) {
            $LoanCaseNotes = new LoanCaseNotes();

            $LoanCaseNotes->case_id =  $id;
            $LoanCaseNotes->notes =  $request->input('notes_msg');
            $LoanCaseNotes->label =  '';
            $LoanCaseNotes->role =  $current_user->menuroles . "|admin|management";
            $LoanCaseNotes->created_at = date('Y-m-d H:i:s');
            $LoanCaseNotes->created_by = $current_user->id;
            $LoanCaseNotes->save();


            $return_id = $LoanCaseNotes->id;

            $LoanCaseNotes = DB::table('loan_case_notes AS n')
                ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
                ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
                ->where('n.case_id', '=',  $id)
                ->where('n.status', '<>',  99)
                ->orderBy('n.created_at', 'desc')
                ->get();

            $view = view('dashboard.case.tabs.tab-notes', compact('LoanCaseNotes', 'current_user'))->render();
        } else if ($request->input('note_type') == 2) {
            $LoanCaseKivNotes = new LoanCaseKivNotes();

            $LoanCaseKivNotes->case_id =  $id;
            $LoanCaseKivNotes->notes =  $request->input('notes_msg');
            $LoanCaseKivNotes->label =  '';
            $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
            $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');

            $LoanCaseKivNotes->status =  1;
            $LoanCaseKivNotes->created_by = $current_user->id;
            $LoanCaseKivNotes->save();

            $return_id = $LoanCaseKivNotes->id;

            // LoanCase::where('id', $id)->update(['latest_notes' => $request->input('notes_msg')]);

            $LoanCaseKIVNotes = DB::table('loan_case_kiv_notes AS n')
                ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
                ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
                ->where('n.case_id', '=',  $id)
                ->where('n.status', '<>',  99)
                ->orderBy('n.created_at', 'desc')
                ->get();

            $view = view('dashboard.case.tabs.tab-notes-all', compact('LoanCaseKIVNotes', 'current_user'))->render();
        } else if ($request->input('note_type') == 3) {
            $LoanCasePNCNotes = new LoanCasePncNotes();

            $LoanCasePNCNotes->case_id =  $id;
            $LoanCasePNCNotes->notes =  $request->input('notes_msg');
            $LoanCasePNCNotes->label =  '';
            $LoanCasePNCNotes->role =  $current_user->menuroles . "|admin|management";
            $LoanCasePNCNotes->created_at = date('Y-m-d H:i:s');

            $LoanCasePNCNotes->status =  1;
            $LoanCasePNCNotes->created_by = $current_user->id;
            $LoanCasePNCNotes->save();

            $return_id = $LoanCasePNCNotes->id;

            $LoanCasePNCNotes = DB::table('loan_case_pnc_notes AS n')
                ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
                ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
                ->where('n.case_id', '=',  $id)
                ->where('n.status', '<>',  99)
                ->orderBy('n.created_at', 'desc')
                ->get();

            $view = view('dashboard.case.tabs.tab-notes-pnc', compact('LoanCasePNCNotes', 'current_user'))->render();
        }


        return response()->json(['status' => 1, 'data' => 'Notes updated', 'return_id' => $return_id, 'view' =>  $view]);
    }

    public function submitEditNotes(Request $request, $id)
    {


        $current_user = auth()->user();




        $LoanCaseKivNotes = LoanCaseKivNotes::where('id', '=', $id)->first();

        $date = new DateTime($LoanCaseKivNotes->updated_at);
        $diff = (new DateTime)->diff($date)->days;

        if ($diff > 3) {
            return response()->json(['status' => 0, 'data' => 'Notes updated', 'return_id' => $id, 'message' => 'Not allow to edit note that created more than 3 days']);
        }


        $LoanCaseKivNotes->notes =  $request->input('notes_msg');
        $LoanCaseKivNotes->updated_at = date('Y-m-d H:i:s');
        $LoanCaseKivNotes->updated_by = $current_user->id;
        $LoanCaseKivNotes->save();

        $LoanCaseKIVNotes = DB::table('loan_case_kiv_notes AS n')
            ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
            ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
            ->where('n.case_id', '=',  $LoanCaseKivNotes->case_id)
            ->where('n.status', '<>',  99)
            ->orderBy('n.created_at', 'DESC')
            ->get();



        return response()->json(['status' => 1, 'data' => 'Notes updated', 'return_id' => $id, 'view' => view('dashboard.case.tabs.tab-notes-all', compact('LoanCaseKIVNotes', 'current_user'))->render()]);
    }

    public function submitEditPncNotes(Request $request, $id)
    {


        $current_user = auth()->user();




        $LoanCasePncNotes = LoanCasePncNotes::where('id', '=', $id)->first();


        $date = new DateTime($LoanCasePncNotes->updated_at);
        $diff = (new DateTime)->diff($date)->days;

        if ($diff > 3) {
            return response()->json(['status' => 0, 'data' => 'Notes updated', 'return_id' => $id, 'message' => 'Not allow to edit note that created more than 3 days']);
        }


        $LoanCasePncNotes->notes =  $request->input('notes_msg');
        $LoanCasePncNotes->updated_at = date('Y-m-d H:i:s');
        $LoanCasePncNotes->updated_by = $current_user->id;
        $LoanCasePncNotes->save();

        $LoanCasePNCNotes = DB::table('loan_case_pnc_notes AS n')
            ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
            ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
            ->where('n.case_id', '=',  $LoanCasePncNotes->case_id)
            ->where('n.status', '<>',  99)
            ->orderBy('n.created_at', 'desc')
            ->get();

        return response()->json(['status' => 1, 'data' => 'Notes updated', 'return_id' => $id, 'view' => view('dashboard.case.tabs.tab-notes-pnc', compact('LoanCasePNCNotes', 'current_user'))->render()]);
    }

    public function deleteNotes(Request $request, $id)
    {


        $current_user = auth()->user();

        $LoanCaseKivNotes = LoanCaseKivNotes::where('id', '=', $id)->first();

        // Check if this is a system-created note (has non-empty label)
        // System-created notes include: 'operation|dispatch', 'setkiv', 'case_status', etc.
        // EXCEPTION: 'createcase' is NOT a system note - it's user-created and can be deleted by the creator
        $labelValue = !empty($LoanCaseKivNotes->label) ? trim($LoanCaseKivNotes->label) : '';
        if ($labelValue !== '' && $labelValue !== 'createcase') {
            // Only admin and management can delete system-created notes
            if (!in_array($current_user->menuroles, ['admin', 'management'])) {
                return response()->json(['status' => 0, 'data' => 'Notes updated', 'return_id' => $id, 'message' => 'System-created notes cannot be deleted. Only administrators can delete these notes.']);
            }
        }

        $date = new DateTime($LoanCaseKivNotes->created_at);
        $diff = (new DateTime)->diff($date)->days;


        if ($diff > 1) {
            return response()->json(['status' => 0, 'data' => 'Notes updated', 'return_id' => $id, 'message' => 'Not allow to delete note that created more than 1 day']);
        }

        $LoanCaseKivNotes->status = 99;
        $LoanCaseKivNotes->deleted_at = date('Y-m-d H:i:s');

        $LoanCaseKivNotes->save();

        $LoanCaseKIVNotes = DB::table('loan_case_kiv_notes AS n')
            ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
            ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
            ->where('n.case_id', '=',  $LoanCaseKivNotes->case_id)
            ->where('n.status', '<>',  99)
            ->orderBy('n.created_at', 'DESC')
            ->get();



        return response()->json(['status' => 1, 'data' => 'Notes deleted', 'return_id' => $id, 'view' => view('dashboard.case.tabs.tab-notes-all', compact('LoanCaseKIVNotes', 'current_user'))->render()]);
    }

    public function deletePncNotes(Request $request, $id)
    {


        $current_user = auth()->user();

        $LoanCaseKivNotes = LoanCasePncNotes::where('id', '=', $id)->first();

        $date = new DateTime($LoanCaseKivNotes->created_at);
        $diff = (new DateTime)->diff($date)->days;


        if ($diff > 1) {
            return response()->json(['status' => 0, 'data' => 'Notes updated', 'return_id' => $id, 'message' => 'Not allow to delete note that created more than 1 day']);
        }

        $LoanCaseKivNotes->status = 99;
        $LoanCaseKivNotes->deleted_at = date('Y-m-d H:i:s');

        $LoanCaseKivNotes->save();

        $LoanCasePNCNotes = DB::table('loan_case_pnc_notes AS n')
            ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
            ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
            ->where('n.case_id', '=',  $LoanCaseKivNotes->case_id)
            ->where('n.status', '<>',  99)
            ->orderBy('n.created_at', 'DESC')
            ->get();



        //return response()->json(['status' => 1, 'data' => 'Notes deleted', 'return_id' => $id, 'view' => view('dashboard.case.tabs.tab-notes-pnc', compact('LoanCaseKIVNotes', 'current_user'))->render()]);
        return response()->json(['status' => 1, 'data' => 'Notes updated', 'return_id' => $id, 'view' => view('dashboard.case.tabs.tab-notes-pnc', compact('LoanCasePNCNotes', 'current_user'))->render()]);
    }

    public function deleteMarketingNotes(Request $request, $id)
    {

        $current_user = auth()->user();

        $LoanCaseNotes = LoanCaseNotes::where('id', '=', $id)->first();

        $date = new DateTime($LoanCaseNotes->updated_at);
        $diff = (new DateTime)->diff($date)->days;

        if ($diff > 3) {
            return response()->json(['status' => 0, 'data' => 'Notes updated', 'return_id' => $id, 'message' => 'Not allow to delete note that created more than 3 days']);
        }

        $LoanCaseNotes->status = 99;
        $LoanCaseNotes->deleted_at = date('Y-m-d H:i:s');
        $LoanCaseNotes->deleted_by = $current_user->id;

        $LoanCaseNotes->save();

        $LoanCaseNotes = DB::table('loan_case_notes AS n')
            ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
            ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
            ->where('n.case_id', '=',  $LoanCaseNotes->case_id)
            ->where('n.status', '<>',  99)
            ->orderBy('n.created_at', 'ASC')
            ->get();



        return response()->json(['status' => 1, 'data' => 'Notes deleted', 'return_id' => $id, 'view' => view('dashboard.case.tabs.tab-notes', compact('LoanCaseNotes', 'current_user'))->render()]);
    }

    public function submitEditMarketingNotes(Request $request, $id)
    {
        $current_user = auth()->user();

        $LoanCaseNotes = LoanCaseNotes::where('id', '=', $id)->first();


        $date = new DateTime($LoanCaseNotes->updated_at);
        $diff = (new DateTime)->diff($date)->days;

        if ($diff > 3) {
            return response()->json(['status' => 0, 'data' => 'Notes updated', 'return_id' => $id, 'message' => 'Not allow to edit note that created more than 3 days']);
        }


        $LoanCaseNotes->notes =  $request->input('notes_msg');
        $LoanCaseNotes->updated_at = date('Y-m-d H:i:s');
        $LoanCaseNotes->updated_by = $current_user->id;
        $LoanCaseNotes->save();

        $LoanCaseNotes = DB::table('loan_case_notes AS n')
            ->leftJoin('users AS u', 'u.id', '=', 'n.created_by')
            ->select('n.*', 'u.name as user_name', 'u.menuroles as menuroles')
            ->where('n.case_id', '=',  $LoanCaseNotes->case_id)
            ->where('n.status', '<>',  99)
            ->orderBy('n.created_at', 'desc')
            ->get();

        return response()->json(['status' => 1, 'data' => 'Notes updated', 'return_id' => $id, 'view' => view('dashboard.case.tabs.tab-notes', compact('LoanCaseNotes', 'current_user'))->render()]);
    }

    public function clientProfileCheckV2(Request $request)
    {

        if ($request->customer_type == 1) {
            // request()->validate([
            //     'client_ic' => ['required',],
            // ]);

            if (!$request->input('client_ic')) {
                return response()->json(['status' => 0, 'message' =>  'Personal type client need to fill in IC No']);
            }

            $regex = '^\\d{6}\\-\\d{2}\\-\\d{4}$^';

            if (!preg_match($regex, $request->input('client_ic'))) {
                return response()->json(['status' => 0, 'message' =>  'Incorrect format for IC Eg: XXXXXX-XX-XXXX']);
            }
        } else if ($request->customer_type == 2) {
            $checking_rule = ['-', 'tba', 'nil', 'na', 'n/a'];

            if (!$request->input('company_reg_no')) {
                return response()->json(['status' => 0, 'message' =>  'Company type client need to fill in Company Reg No']);
            }

            if (in_array(strtolower($request->input('company_reg_no')), $checking_rule)) {
                return response()->json(['status' => 0, 'message' =>  'Please provide proper format of company reg no']);
            }
        }

        $Customer = [];
        $Customer_ic = [];
        $loanCase = [];
        // $_POST[ 'content' ];
        // return $request->input('hidden_remark');
        try {
            if ($request->input('client_ic')) {
                $clientList = explode('&', $request->input('client_ic'));

                if (count($clientList) > 0) {
                    for ($i = 0; $i < count($clientList); $i++) {
                        $client_ic = trim(str_replace("-", "", $clientList[$i]));
                        $Customer = Customer::whereRaw("REPLACE(ic_no,'-','') like ?", '%' . $client_ic . '%')->first();


                        if ($Customer) {
                            array_push($Customer_ic, $Customer->id);
                        }
                    }
                }

                // $client_ic = trim(str_replace("-", "", $request->input('client_ic')));

                // $Customer = Customer::whereRaw("REPLACE(ic_no,'-','') like ?",'%'.$client_ic.'%')->first();

                if (count($Customer_ic) > 0) {
                    $loanCase = DB::table('loan_case as l')
                        ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                        ->leftJoin('users as u1', 'u1.id', '=', 'l.clerk_id')
                        ->leftJoin('users as u2', 'u2.id', '=', 'l.lawyer_id')
                        ->select('l.*', 'c.name as client_name', 'u1.name as clerk_name', 'u2.name as lawyer_name')
                        // ->where('l.customer_id', '=', $Customer->id)
                        ->whereIn('l.customer_id',  $Customer_ic)
                        ->get();
                }
            }
        } catch (\Throwable $e) {
            $status = 0;
            $message = $e;

            return $e;
        }

        if (count($loanCase) > 0) {
            return response()->json(['status' => 2, 'data' =>  [
                "customer" => $Customer,
                "loanCase" =>  $loanCase,
                'view' => view('dashboard.case.table.tbl-client-case', compact('loanCase'))->render()
            ]]);
        } else {
            // return $this->createCaseWithSameTeam($request); 
            return $this->createCaseV2($request);
        }
    }

    public function clientProfileCheck(Request $request)
    {
        $Customer = [];
        $Customer_ic = [];
        $loanCase = [];
        $client = '';


        if ($request->input('customer_type') == 1) {
            if ($request->input('client_ic') == "") {
                return response()->json(['status' => 3, 'message' =>  "Please key in client IC"]);
            }
            $client = $request->input('client_ic');
        } else if ($request->input('customer_type') == 2) {

            if ($request->input('company_reg_no') == "") {
                return response()->json(['status' => 3, 'message' =>  "Please key in company reg no"]);
            }
            $client = $request->input('company_reg_no');
        }
        // $_POST[ 'content' ];
        // return $request->input('hidden_remark');
        try {
            if ($client) {
                $clientList = explode('&', $client);

                if (count($clientList) > 0) {
                    for ($i = 0; $i < count($clientList); $i++) {
                        $client_ic = trim(str_replace("-", "", $clientList[$i]));

                        if ($client_ic == "") {
                            return response()->json(['status' => 3, 'message' =>  "Please key in proper IC/Company Reg No to prevent data overwritten"]);
                        }

                        if ($request->input('customer_type') == 1) {
                            $Customer = Customer::whereRaw("REPLACE(ic_no,'-','') like ?", '%' . $client_ic . '%')->first();
                        } else {
                            $Customer = Customer::whereRaw("REPLACE(company_ref_no,'-','') like ?", '%' . $client_ic . '%')->first();
                        }


                        if ($Customer) {
                            array_push($Customer_ic, $Customer->id);
                        }
                    }
                }

                // $client_ic = trim(str_replace("-", "", $request->input('client_ic')));

                // $Customer = Customer::whereRaw("REPLACE(ic_no,'-','') like ?",'%'.$client_ic.'%')->first();

                if (count($Customer_ic) > 0) {
                    $loanCase = DB::table('loan_case as l')
                        ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                        ->leftJoin('users as u1', 'u1.id', '=', 'l.clerk_id')
                        ->leftJoin('users as u2', 'u2.id', '=', 'l.lawyer_id')
                        ->select('l.*', 'c.name as client_name', 'u1.name as clerk_name', 'u2.name as lawyer_name')
                        // ->where('l.customer_id', '=', $Customer->id)
                        ->whereIn('l.customer_id',  $Customer_ic)
                        ->get();
                }
            }
        } catch (\Throwable $e) {
            $status = 0;
            $message = $e;

            return $e;
        }

        if (count($loanCase) > 0) {
            return response()->json(['status' => 2, 'data' =>  [
                "customer" => $Customer,
                "loanCase" =>  $loanCase,
                'view' => view('dashboard.case.table.tbl-client-case', compact('loanCase'))->render()
            ]]);
        } else {
            // return $this->createCaseWithSameTeam($request);
            return $this->createCaseV2($request);
        }
    }

    public function createCaseWithSameTeam(Request $request)
    {
        $status = 1;
        $data = [];

        $lawyer_id = 0;
        $clerk_id = 0;
        $team_id = 0;
        $bank_ref = '';

        $case_ref_no = '[sales]/[lawyer]/[bank]/[running_no]/[client]/[clerk]';


        $client_name =  $request->input('client_name');
        $bank_id =  $request->input('bank');
        $branch_id =  $request->input('branch');
        $case_type =  $request->input('case_type');
        $race =  $request->input('race');
        $first_house =  $request->input('first_house');
        $other_race =  $request->input('client_race_others');
        $customer = new Customer();
        $current_user = auth()->user();

        if ($request->input('sales') == null) {
            $sales_user_id = $current_user->id;
            $sales_nick_name = $current_user->nick_name;
        } else {
            $selectedSales = Users::where('id', $request->input('sales'))->first();
            $sales_user_id = $selectedSales->id;
            $sales_nick_name = $selectedSales->nick_name;
        }

        if ($request->input('client_id') <> 0) {
            $Customer = Customer::where('id', '=', $request->input('client_id'))->first();

            if ($Customer) {
                $clerk_id = $request->input('clerk_id');
                $lawyer_id = $request->input('lawyer_id');

                $client_short_code = Helper::generateNickName($Customer->name);

                $TeamMembers = TeamMembers::where('user_id', '=', $lawyer_id)->first();

                if ($TeamMembers) {
                    $team_id = $TeamMembers->team_main_id;
                } else {
                    $team_id = 0;
                }
            }
        } else {
            $client_short_code = Helper::generateNickName($client_name);


            if ($request->input('lawyer') == 0) {
                // $group = $this->CaseRotation($request);

                $group = $this->caseAssignAutomation($request, $bank_id);

                // return $group;
                if ($group[0]['status'] == 0) {
                    return response()->json(['status' => 0, 'message' => $group[0]['message']]);
                }

                $clerk_id =  $group[0]['clerk_id'];
                $lawyer_id =  $group[0]['lawyer_id'];

                $team_id = $group[0]['team_id'];
            } else {
                $staff_id = 0;
                if ($request->input('clerk') != 0) {
                    $staff_id = $request->input('clerk');
                } else {
                    $staff_id = $request->input('lawyer');
                }

                // if ($this->checkTeamPortfolio($request, $staff_id) == 0) {
                //     return response()->json(['status' => 0, 'message' => 'Selected team not handle this case type']);
                // }

                // $team_id = $this->checkTeamCaseCount($request, $staff_id);

                // if ($team_id != 0) {
                $clerk_id =  $request->input('clerk');
                $lawyer_id =  $request->input('lawyer');
                // } else {
                //     return response()->json(['status' => 0, 'message' => 'Current team can\'t take on new case due to reach max file per month']);
                // }
            }
        }

        $bank = Portfolio::where('id', '=', $bank_id)->first();

        $bank_ref = $bank->short_code;

        $lawyer_user = User::where('id', '=', $lawyer_id)->first();
        $Cler_user = User::where('id', '=', $clerk_id)->first();

        // if (in_array($lawyer_user->branch_id, [2, 3, 4, 5])) {
        //     $Branch = Branch::where('id', '=', $lawyer_user->branch_id)->first();
        //     $case_ref_no = $Branch->short_code . "/" . $case_ref_no;
        // } else {
        //     if ($lawyer_user->id == 32) {
        //         $Branch = Branch::where('id', '=', 5)->first();
        //         $case_ref_no = $Branch->short_code . "/" . $case_ref_no;
        //     } else {
        //     }
        // }

        if (in_array($branch_id, [2, 3, 4, 5, 6])) {
            $Branch = Branch::where('id', '=', $branch_id)->first();
            $case_ref_no = $Branch->short_code . "/" . $case_ref_no;
        } else {
            if ($lawyer_user->id == 32) {
                $Branch = Branch::where('id', '=', 5)->first();
                $case_ref_no = $Branch->short_code . "/" . $case_ref_no;
            } else {
            }
        }

        $bank = Portfolio::where('id', '=', $bank_id)->get();

        // if ($lawyer_user->branch_id == 3) {
        //     $parameter = Parameter::where('parameter_type', '=', 'case_running_no_dpc')->first();
        // } else if ($lawyer_user->branch_id == 4) {
        //     $parameter = Parameter::where('parameter_type', '=', 'case_running_no_rama')->first();
        // } else if ($lawyer_user->branch_id == 5) {
        //     $parameter = Parameter::where('parameter_type', '=', 'case_running_no_dp')->first();
        // } else if ($lawyer_user->branch_id == 6) {
        //     $parameter = Parameter::where('parameter_type', '=', 'case_running_no_il')->first();
        // } else {
        //     if ($lawyer_user->id == 32) {
        //         $parameter = Parameter::where('parameter_type', '=', 'case_running_no_dp')->first();
        //     } else {
        //         $parameter = Parameter::where('parameter_type', '=', 'case_running_no')->first();
        //     }
        // }

        if ($branch_id == 3) {
            $parameter = Parameter::where('parameter_type', '=', 'case_running_no_dpc')->first();
        } else if ($branch_id == 2) {
            $parameter = Parameter::where('parameter_type', '=', 'case_running_no_p')->first();
        } else if ($branch_id == 4) {
            $parameter = Parameter::where('parameter_type', '=', 'case_running_no_rama')->first();
        } else if ($branch_id == 5) {
            $parameter = Parameter::where('parameter_type', '=', 'case_running_no_dp')->first();
        } else if ($branch_id == 6) {
            $parameter = Parameter::where('parameter_type', '=', 'case_running_no_il')->first();
        } else {
            if ($lawyer_user->id == 32) {
                $parameter = Parameter::where('parameter_type', '=', 'case_running_no_dp')->first();
            } else {
                $parameter = Parameter::where('parameter_type', '=', 'case_running_no')->first();
            }
        }

        $running_no = (int)$parameter->parameter_value_1 + 1;

        // return $lawyer_user->branch_id;
        $parameter->parameter_value_1 = $running_no;
        $parameter->save();

        if ($current_user->id == 14) {
            $sales_user_id = 2;
            $sales_nick_name = 'LHY';
        }

        $parameter = Parameter::where('parameter_type', '=', 'client_ledger_running_no')->first();

        $client_ledger_running_no = (int)$parameter->parameter_value_1 + 1;
        $parameter->parameter_value_1 = $client_ledger_running_no;
        $parameter->save();

        $client_ledger_running_no = str_pad($client_ledger_running_no, 6, '0', STR_PAD_LEFT);

        $case_ref_no = str_replace("[sales]", $sales_nick_name, $case_ref_no);
        $case_ref_no = str_replace("[bank]", $bank_ref, $case_ref_no);
        $case_ref_no = str_replace("[running_no]", $running_no, $case_ref_no);
        $case_ref_no = str_replace("[client]", $client_short_code, $case_ref_no);
        $case_ref_no = str_replace("[lawyer]", $lawyer_user->nick_name, $case_ref_no);

        // Some case only handle by lawyer without clerk
        if ($clerk_id  != "" && $clerk_id  != 0) {
            $case_ref_no = str_replace("[clerk]", $Cler_user->nick_name, $case_ref_no);
        } else {
            $case_ref_no = str_replace("/[clerk]", '', $case_ref_no);
        }

        $property_address = '';

        if ($request->input('property_address') == null) {
            $property_address = '';
        } else {
            $property_address = $request->input('property_address');
        }

        // $branch_id = $this->getStaffBranchID($lawyer_id, $branch_id);

        // if ($lawyer_user->id == 32) {
        //     $branch_id  = 5;
        // }

        $loanCase = new LoanCase();
        $loanCase->case_ref_no = $case_ref_no;
        $loanCase->property_address = $property_address;
        $loanCase->referral_name = $request->input('referral_name');
        $loanCase->referral_phone_no = $request->input('referral_phone_no');
        $loanCase->referral_email = $request->input('referral_email');
        $loanCase->referral_id = $request->input('referral_id');
        $loanCase->purchase_price = $request->input('purchase_price');
        $loanCase->loan_sum = $request->input('loan_sum');
        $loanCase->targeted_collect_amount = $request->input('targeted_collect_amount');
        $loanCase->agreed_fee = $request->input('agreed_fee');
        $loanCase->remark = $request->input('hidden_remark');
        $loanCase->sales_user_id = $sales_user_id;
        $loanCase->handle_group_id = $team_id;
        $loanCase->bank_id = $request->input('bank');
        $loanCase->lawyer_id = $lawyer_id;
        $loanCase->clerk_id = $clerk_id;
        $loanCase->case_type_id =  $case_type;
        $loanCase->first_house =  $first_house;
        $loanCase->branch_id =  $branch_id;
        $loanCase->status = "2";
        $loanCase->case_running_no = $running_no;
        $loanCase->client_ledger_account_code = $client_ledger_running_no;
        $loanCase->created_at = now();

        $loanCase->save();

        if ($request->input('hidden_remark') != null) {
            $LoanCaseKivNotes = new LoanCaseKivNotes();

            $LoanCaseKivNotes->case_id =  $loanCase->id;
            $LoanCaseKivNotes->notes =  '<b>New Case</b><br/>' . $request->input('hidden_remark');
            $LoanCaseKivNotes->label =  'createcase';
            $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
            $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');
            $LoanCaseKivNotes->status =  1;
            $LoanCaseKivNotes->created_by = $current_user->id;
            $LoanCaseKivNotes->save();
        }

        if ($request->input('client_id') == 0) {
            if ($loanCase) {
                $customer = $this->createCustomer($request, $case_ref_no);
                $loanCase->customer_id = $customer->id;
                $loanCase->save();
            }
        } else {
            $loanCase->customer_id = $request->input('client_id');
            $loanCase->save();
        }

        $now = Carbon::now();
        $case_count = 0;
        $RptCase = RptCase::where('fiscal_mon', $now->month)->where('fiscal_year', $now->year)->where('branch_id', '=', $branch_id)->first();

        $caseCount = LoanCase::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->where('branch_id', '=', $branch_id)->where('bank_id', '!=', 0)->where('status', '<>', 99)->count();


        if (!$RptCase) {
            $RptCase = new RptCase();

            $RptCase->fiscal_year = $now->year;
            $RptCase->fiscal_mon = $now->month;
            $RptCase->branch_id = $branch_id;
            $RptCase->status = 1;
            $RptCase->created_at = date('Y-m-d H:i:s');
            $RptCase->count = 1;
        } else {
            // $RptCase->count = $RptCase->count + 1;
            $RptCase->count = $caseCount;
        }

        $RptCase->save();


        return response()->json(['status' => 1, 'message' => 'Successfully created new case']);
    }

    public function updateCaseCountPerBranch()
    {
        $now = Carbon::now();
        $case_count = 0;
        $RptCase = RptCase::where('fiscal_mon', $now->month)->where('fiscal_year', $now->year)->where('branch_id', '=', 3)->first();

        $caseCount = LoanCase::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->where('branch_id', '=', 3)->where('status', '<>', 99)->count();

        return $caseCount;
        if (!$RptCase) {
            $RptCase = new RptCase();

            $RptCase->fiscal_year = $now->year;
            $RptCase->fiscal_mon = $now->month;
            $RptCase->branch_id = 3;
            $RptCase->status = 1;
            $RptCase->created_at = date('Y-m-d H:i:s');
            $RptCase->count = 1;
        } else {
            // $RptCase->count = $RptCase->count + 1;
            $RptCase->count = $caseCount;
        }
    }

    public function createCaseV2(Request $request)
    {
        $status = 1;
        $data = [];

        $lawyer_id = 0;
        $clerk_id = 0;
        $team_id = 0;
        $bank_ref = '';





        $case_ref_no = '[sales]/[lawyer]/[bank]/[running_no]/[client]/[clerk]';

        $client_name =  $request->input('client_name');
        $bank_id =  $request->input('bank');
        $branch_id =  $request->input('branch');
        $case_type =  $request->input('case_type');
        $race =  $request->input('race');
        $first_house =  $request->input('first_house');
        $other_race =  $request->input('client_race_others');
        $customer = new Customer();
        $current_user = auth()->user();

        if ($request->input('sales') == null) {
            $sales_user_id = $current_user->id;
            $sales_nick_name = $current_user->nick_name;
        } else {
            $selectedSales = Users::where('id', $request->input('sales'))->first();
            $sales_user_id = $selectedSales->id;
            $sales_nick_name = $selectedSales->nick_name;
        }

        if ($request->input('client_id') <> 0) {
            $Customer = Customer::where('id', '=', $request->input('client_id'))->first();

            if ($Customer) {
                $clerk_id = $request->input('clerk_id');
                $lawyer_id = $request->input('lawyer_id');

                $client_short_code = Helper::generateNickName($Customer->name);

                $TeamMembers = TeamMembers::where('user_id', '=', $lawyer_id)->first();
                $team_id = $TeamMembers->team_main_id;
            }
        } else {


            $client_short_code = Helper::generateNickName($client_name);

            if ($request->input('lawyer') == 0) {
                $group = $this->caseAssignAutomation($request, $bank_id);

                if ($group[0]['status'] == 0) {
                    return response()->json(['status' => 0, 'message' => $group[0]['message']]);
                }

                $clerk_id =  $group[0]['clerk_id'];
                $lawyer_id =  $group[0]['lawyer_id'];

                $team_id = $group[0]['team_id'];
            } else {
                $clerk_id =  $request->input('clerk');
                $lawyer_id =  $request->input('lawyer');
            }
        }

        $bank = Portfolio::where('id', '=', $bank_id)->first();

        $bank_ref = $bank->short_code;

        $lawyer_user = User::where('id', '=', $lawyer_id)->first();
        $Cler_user = User::where('id', '=', $clerk_id)->first();

        if (in_array($branch_id, [2, 3, 4, 5, 6])) {
            $Branch = Branch::where('id', '=', $branch_id)->first();
            $case_ref_no = $Branch->short_code . "/" . $case_ref_no;
        } else {
            if ($lawyer_user->id == 32) {
                $Branch = Branch::where('id', '=', 5)->first();
                $case_ref_no = $Branch->short_code . "/" . $case_ref_no;
            } else {
            }
        }

        $bank = Portfolio::where('id', '=', $bank_id)->get();



        if ($branch_id == 3) {
            $parameter = Parameter::where('parameter_type', '=', 'case_running_no_dpc')->first();
        } else if ($branch_id == 2) {
            $parameter = Parameter::where('parameter_type', '=', 'case_running_no_p')->first();
        } else if ($branch_id == 4) {
            $parameter = Parameter::where('parameter_type', '=', 'case_running_no_rama')->first();
        } else if ($branch_id == 5) {
            $parameter = Parameter::where('parameter_type', '=', 'case_running_no_dp')->first();
        } else if ($branch_id == 6) {
            $parameter = Parameter::where('parameter_type', '=', 'case_running_no_il')->first();
        } else if ($branch_id == 7) {
            $parameter = Parameter::where('parameter_type', '=', 'case_running_no_klm')->first();
        } else {
            if ($lawyer_user->id == 32) {
                $parameter = Parameter::where('parameter_type', '=', 'case_running_no_dp')->first();
            } else {
                $parameter = Parameter::where('parameter_type', '=', 'case_running_no')->first();
            }
        }

        $running_no = (int)$parameter->parameter_value_1 + 1;
        // return $lawyer_user->branch_id;
        $parameter->parameter_value_1 = $running_no;
        $parameter->save();

        $parameter = Parameter::where('parameter_type', '=', 'client_ledger_running_no')->first();

        $client_ledger_running_no = (int)$parameter->parameter_value_1 + 1;
        $parameter->parameter_value_1 = $client_ledger_running_no;
        $parameter->save();

        $client_ledger_running_no = str_pad($client_ledger_running_no, 6, '0', STR_PAD_LEFT);

        if ($current_user->id == 14) {
            $sales_user_id = 2;
            $sales_nick_name = 'LHY';
        }

        if ($current_user->id == 177) {
            $sales_user_id = 122;
            $sales_nick_name = 'ET';
        }

        $case_ref_no = str_replace("[sales]", $sales_nick_name, $case_ref_no);
        $case_ref_no = str_replace("[bank]", $bank_ref, $case_ref_no);
        $case_ref_no = str_replace("[running_no]", $running_no, $case_ref_no);
        $case_ref_no = str_replace("[client]", $client_short_code, $case_ref_no);
        $case_ref_no = str_replace("[lawyer]", $lawyer_user->nick_name, $case_ref_no);

        // Some case only handle by lawyer without clerk
        if ($clerk_id  != "" && $clerk_id  != 0) {
            $case_ref_no = str_replace("[clerk]", $Cler_user->nick_name, $case_ref_no);
        } else {
            $case_ref_no = str_replace("/[clerk]", '', $case_ref_no);
        }

        $property_address = '';

        if ($request->input('property_address') == null) {
            $property_address = '';
        } else {
            $property_address = $request->input('property_address');
        }

        // $branch_id = $this->getStaffBranchID($lawyer_id, $branch_id);

        // if ($lawyer_user->id == 32) {
        //     $branch_id  = 5;
        // }

        $loanCase = new LoanCase();
        $loanCase->case_ref_no = $case_ref_no;
        $loanCase->property_address = $property_address;
        $loanCase->referral_name = $request->input('referral_name');
        $loanCase->referral_phone_no = $request->input('referral_phone_no');
        $loanCase->referral_email = $request->input('referral_email');
        $loanCase->referral_id = $request->input('referral_id');
        $loanCase->purchase_price = $request->input('purchase_price');
        $loanCase->loan_sum = $request->input('loan_sum');
        $loanCase->targeted_collect_amount = $request->input('targeted_collect_amount');
        $loanCase->agreed_fee = $request->input('agreed_fee');
        $loanCase->remark = $request->input('hidden_remark');
        $loanCase->sales_user_id = $sales_user_id;
        $loanCase->handle_group_id = $team_id;
        $loanCase->bank_id = $request->input('bank');
        $loanCase->lawyer_id = $lawyer_id;
        $loanCase->clerk_id = $clerk_id;
        $loanCase->case_type_id =  $case_type;
        $loanCase->first_house =  $first_house;
        $loanCase->branch_id =  $branch_id;
        $loanCase->status = "2";
        $loanCase->client_ledger_account_code = $client_ledger_running_no;
        $loanCase->case_running_no = $running_no;
        $loanCase->created_at = now();

        $loanCase->save();

        if ($request->input('hidden_remark') != null) {
            $LoanCaseKivNotes = new LoanCaseKivNotes();

            $LoanCaseKivNotes->case_id =  $loanCase->id;
            $LoanCaseKivNotes->notes =  '<b>New Case</b><br/>' . $request->input('hidden_remark');
            $LoanCaseKivNotes->label =  'createcase';
            $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
            $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');
            $LoanCaseKivNotes->status =  1;
            $LoanCaseKivNotes->created_by = $current_user->id;
            $LoanCaseKivNotes->save();
        }

        if ($request->input('client_id') == 0) {
            if ($loanCase) {
                $customer = $this->createCustomer($request, $case_ref_no);
                $loanCase->customer_id = $customer->id;
                $loanCase->save();
            }
        } else {
            $loanCase->customer_id = $request->input('client_id');
            $loanCase->save();
        }

        $now = Carbon::now();
        $case_count = 0;
        $RptCase = RptCase::where('fiscal_mon', $now->month)->where('fiscal_year', $now->year)->where('branch_id', '=', $branch_id)->first();

        // $caseCount = LoanCase::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->where('branch_id', '=', $branch_id)->where('status', '<>', 99)->count();
        $caseCount = LoanCase::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->where('branch_id', '=', $branch_id)->where('bank_id', '!=', 0)->where('status', '<>', 99)->count();

        if (!$RptCase) {
            $RptCase = new RptCase();

            $RptCase->fiscal_year = $now->year;
            $RptCase->fiscal_mon = $now->month;
            $RptCase->branch_id = $branch_id;
            $RptCase->status = 1;
            $RptCase->created_at = date('Y-m-d H:i:s');
            $RptCase->count = 1;
        } else {
            // $RptCase->count = $RptCase->count + 1;
            $RptCase->count = $caseCount;
        }

        $RptCase->save();


        return response()->json(['status' => 1, 'message' => 'Successfully created new case']);
    }

    public function createCaseV3(Request $request)
    {
        $status = 1;
        $data = [];

        $lawyer_id = 0;
        $clerk_id = 0;
        $team_id = 0;
        $bank_ref = '';

        $case_ref_no = '[sales]/[lawyer]/[bank]/[running_no]/[client]/[clerk]';

        $client_name =  $request->input('client_name');
        $bank_id =  $request->input('bank');
        $branch_id =  $request->input('branch');
        $case_type =  $request->input('case_type');
        $race =  $request->input('race');
        $first_house =  $request->input('first_house');
        $other_race =  $request->input('client_race_others');
        $customer = new Customer();
        $current_user = auth()->user();
        $sales_user_id = $current_user->id;
        $sales_nick_name = $current_user->nick_name;

        if ($request->input('client_id') <> 0) {
            $Customer = Customer::where('id', '=', $request->input('client_id'))->first();

            if ($Customer) {
                $clerk_id = $request->input('clerk_id');
                $lawyer_id = $request->input('lawyer_id');

                $client_short_code = Helper::generateNickName($Customer->name);

                $TeamMembers = TeamMembers::where('user_id', '=', $lawyer_id)->first();
                $team_id = $TeamMembers->team_main_id;
            }
        } else {


            $client_short_code = Helper::generateNickName($client_name);

            if ($request->input('lawyer') == 0) {
                $group = $this->caseAssignAutomation($request, $bank_id);

                if ($group[0]['status'] == 0) {
                    return response()->json(['status' => 0, 'message' => $group[0]['message']]);
                }

                $clerk_id =  $group[0]['clerk_id'];
                $lawyer_id =  $group[0]['lawyer_id'];

                $team_id = $group[0]['team_id'];
            } else {
                $clerk_id =  $request->input('clerk');
                $lawyer_id =  $request->input('lawyer');
            }
        }

        $bank = Portfolio::where('id', '=', $bank_id)->first();

        $bank_ref = $bank->short_code;

        $lawyer_user = User::where('id', '=', $lawyer_id)->first();
        $Cler_user = User::where('id', '=', $clerk_id)->first();

        if (in_array($branch_id, [2, 3, 4, 5, 6])) {
            $Branch = Branch::where('id', '=', $branch_id)->first();
            $case_ref_no = $Branch->short_code . "/" . $case_ref_no;
        } else {
            if ($lawyer_user->id == 32) {
                $Branch = Branch::where('id', '=', 5)->first();
                $case_ref_no = $Branch->short_code . "/" . $case_ref_no;
            } else {
            }
        }

        $bank = Portfolio::where('id', '=', $bank_id)->get();



        if ($branch_id == 3) {
            $parameter = Parameter::where('parameter_type', '=', 'case_running_no_dpc')->first();
        } else if ($branch_id == 2) {
            $parameter = Parameter::where('parameter_type', '=', 'case_running_no_p')->first();
        } else if ($branch_id == 4) {
            $parameter = Parameter::where('parameter_type', '=', 'case_running_no_rama')->first();
        } else if ($branch_id == 5) {
            $parameter = Parameter::where('parameter_type', '=', 'case_running_no_dp')->first();
        } else if ($branch_id == 6) {
            $parameter = Parameter::where('parameter_type', '=', 'case_running_no_il')->first();
        } else {
            if ($lawyer_user->id == 32) {
                $parameter = Parameter::where('parameter_type', '=', 'case_running_no_dp')->first();
            } else {
                $parameter = Parameter::where('parameter_type', '=', 'case_running_no')->first();
            }
        }

        $running_no = (int)$parameter->parameter_value_1 + 1;
        // return $lawyer_user->branch_id;
        $parameter->parameter_value_1 = $running_no;
        $parameter->save();

        $parameter = Parameter::where('parameter_type', '=', 'client_ledger_running_no')->first();

        $client_ledger_running_no = (int)$parameter->parameter_value_1 + 1;
        $parameter->parameter_value_1 = $client_ledger_running_no;
        $parameter->save();

        $client_ledger_running_no = str_pad($client_ledger_running_no, 6, '0', STR_PAD_LEFT);

        if (in_array($current_user->id, [51, 118])) {
            $sales_user_id = 32;
            $sales_nick_name = 'T';
        } else if ($current_user->id == 14) {
            $sales_user_id = 2;
            $sales_nick_name = 'LHY';
        }

        $case_ref_no = str_replace("[sales]", $sales_nick_name, $case_ref_no);
        $case_ref_no = str_replace("[bank]", $bank_ref, $case_ref_no);
        $case_ref_no = str_replace("[running_no]", $running_no, $case_ref_no);
        $case_ref_no = str_replace("[client]", $client_short_code, $case_ref_no);
        $case_ref_no = str_replace("[lawyer]", $lawyer_user->nick_name, $case_ref_no);

        // Some case only handle by lawyer without clerk
        if ($clerk_id  != "" && $clerk_id  != 0) {
            $case_ref_no = str_replace("[clerk]", $Cler_user->nick_name, $case_ref_no);
        } else {
            $case_ref_no = str_replace("/[clerk]", '', $case_ref_no);
        }

        $property_address = '';

        if ($request->input('property_address') == null) {
            $property_address = '';
        } else {
            $property_address = $request->input('property_address');
        }

        // $branch_id = $this->getStaffBranchID($lawyer_id, $branch_id);

        // if ($lawyer_user->id == 32) {
        //     $branch_id  = 5;
        // }

        $loanCase = new LoanCase();
        $loanCase->case_ref_no = $case_ref_no;
        $loanCase->property_address = $property_address;
        $loanCase->referral_name = $request->input('referral_name');
        $loanCase->referral_phone_no = $request->input('referral_phone_no');
        $loanCase->referral_email = $request->input('referral_email');
        $loanCase->referral_id = $request->input('referral_id');
        $loanCase->purchase_price = $request->input('purchase_price');
        $loanCase->loan_sum = $request->input('loan_sum');
        $loanCase->targeted_collect_amount = $request->input('targeted_collect_amount');
        $loanCase->agreed_fee = $request->input('agreed_fee');
        $loanCase->remark = $request->input('hidden_remark');
        $loanCase->sales_user_id = $sales_user_id;
        $loanCase->handle_group_id = $team_id;
        $loanCase->bank_id = $request->input('bank');
        $loanCase->lawyer_id = $lawyer_id;
        $loanCase->clerk_id = $clerk_id;
        $loanCase->case_type_id =  $case_type;
        $loanCase->first_house =  $first_house;
        $loanCase->branch_id =  $branch_id;
        $loanCase->status = "2";
        $loanCase->client_ledger_account_code = $client_ledger_running_no;
        $loanCase->created_at = now();

        $loanCase->save();

        if ($request->input('hidden_remark') != null) {
            $LoanCaseKivNotes = new LoanCaseKivNotes();

            $LoanCaseKivNotes->case_id =  $loanCase->id;
            $LoanCaseKivNotes->notes =  '<b>New Case</b><br/>' . $request->input('hidden_remark');
            $LoanCaseKivNotes->label =  'createcase';
            $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
            $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');
            $LoanCaseKivNotes->status =  1;
            $LoanCaseKivNotes->created_by = $current_user->id;
            $LoanCaseKivNotes->save();
        }

        if ($request->input('client_id') == 0) {
            if ($loanCase) {
                $customer = $this->createCustomer($request, $case_ref_no);
                $loanCase->customer_id = $customer->id;
                $loanCase->save();
            }
        } else {
            $loanCase->customer_id = $request->input('client_id');
            $loanCase->save();
        }

        $now = Carbon::now();
        $case_count = 0;
        $RptCase = RptCase::where('fiscal_mon', $now->month)->where('fiscal_year', $now->year)->where('branch_id', '=', $branch_id)->first();

        $caseCount = LoanCase::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->where('branch_id', '=', $branch_id)->where('status', '<>', 99)->count();

        if (!$RptCase) {
            $RptCase = new RptCase();

            $RptCase->fiscal_year = $now->year;
            $RptCase->fiscal_mon = $now->month;
            $RptCase->branch_id = $branch_id;
            $RptCase->status = 1;
            $RptCase->created_at = date('Y-m-d H:i:s');
            $RptCase->count = 1;
        } else {
            // $RptCase->count = $RptCase->count + 1;
            $RptCase->count = $caseCount;
        }

        $RptCase->save();


        return response()->json(['status' => 1, 'message' => 'Successfully created new case']);
    }

    public function caseAssignAutomation(Request $request)
    {

        $bank_id =  $request->input('bank');
        $portolio_id = [];
        $cases_list = [];
        $result = [];

        $selected_id = 0;
        $team_id = 0;


        $current_user = auth()->user();

        // $TeamPortfolios = TeamPortfolios::where('portfolio_id', '=', $request->input('bank'))->get();

        // return $request->input('bank');

        if ($current_user->only_own_case == 1) {
            $lawyer = Users::where('id', '=', $current_user->id)->first();
            $clerk_id = 0;
            $clerk_nick = '';



            $team_main_id = TeamMembers::where('user_id', $current_user->id)->pluck('team_main_id');

            $TeamMembers = TeamMembers::where('team_main_id', $team_main_id)
                ->where('leader', 0)
                ->where('status', 1)->get();




            // return $TeamPortfolios;

            if (count($TeamMembers) > 0) {
                $case_count = 0;

                for ($i = 0; $i < count($TeamMembers); $i++) {
                    $user_id = $TeamMembers[$i]->user_id;

                    $caseCountPerMonth = DB::table('loan_case as l')
                        ->whereMonth('l.created_at', '=', date('m'))
                        ->whereYear('l.created_at', '=', date('Y'))
                        ->where('l.status', '<>', 99)
                        ->where(function ($q) use ($user_id) {
                            $q->where('l.clerk_id', '=', $user_id)
                                ->orWhere('l.lawyer_id', '=', $user_id);
                        })->count();

                    if ($i == 0) {
                        $case_count = $caseCountPerMonth;
                    }

                    if (($caseCountPerMonth <= $case_count)) {
                        $clerk_id = $user_id;
                        $case_count = $caseCountPerMonth;
                    }
                }
            }

            if ($clerk_id != 0) {
                $clerk_nick = Users::where('id', '=', $clerk_id)->pluck('nick_name');
            }

            array_push($result,  ["status" => 1, "team_id" => $team_id, "lawyer_id" =>  $current_user->id, "lawyer_nick" =>  $lawyer->nick_name,  "clerk_id" =>  $clerk_id,  "clerk_nick" =>  $clerk_nick]);

            return $result;
        }

        $accessInfo = AccessController::manageAccess();

        $TeamPortfolios = DB::table('team_portfolios as tp')
            ->leftJoin('team_members as tm', 'tm.user_id', '=', 'tp.user_id')
            ->leftJoin('users as u', 'tm.user_id', '=', 'u.id')
            ->select('tp.user_id as id', 'tm.total_file_per_month', 'tm.team_main_id', 'tm.leader', 'tm.chamber', 'tm.solo')
            ->where('tp.portfolio_id', '=', $request->input('bank'))
            ->where('u.branch_id', '=', $request->input('branch'))
            // ->whereIn('u.branch_id', $accessInfo['brancAccessList'])
            ->where('tm.status', '=', 1)
            ->where('u.only_own_case', 0)
            ->where('u.status', '<>', 99)
            ->where(function ($q) {
                $q->where('tm.leader', '=', 0)
                    ->orWhere('tm.chamber', '=', 1);
            })
            ->get();

        $team_merge = [];

        // return $TeamPortfolios;

        //Temporary allow munirah and stella team go into case assign pool for HQ
        if (in_array($request->input('branch'), [1])) {
            $TeamPortfolios_2 = DB::table('team_portfolios as tp')
                ->leftJoin('team_members as tm', 'tm.user_id', '=', 'tp.user_id')
                ->leftJoin('users as u', 'tm.user_id', '=', 'u.id')
                ->select('tp.user_id as id', 'tm.total_file_per_month', 'tm.team_main_id', 'tm.leader', 'tm.chamber', 'tm.solo')
                ->where('tp.portfolio_id', '=', $request->input('bank'))
                ->whereIn('tm.team_main_id', [8, 9, 10])
                ->where('tm.status', '=', 1)
                ->where('u.only_own_case', 0)
                ->where('u.status', '<>', 99)
                ->where(function ($q) {
                    $q->where('tm.leader', '=', 0)
                        ->orWhere('tm.chamber', '=', 1);
                })
                ->get();

            $TeamPortfolios = $TeamPortfolios->merge($TeamPortfolios_2);
        }



        if (count($TeamPortfolios) <= 0) {
            array_push($result,  ["status" => 0, 'message' => 'No team handle this case type']);
            return $result;
        }

        // return $TeamPortfolios;

        // Log::info('test12: '.json_encode($TeamPortfolios));


        for ($i = 0; $i < count($TeamPortfolios); $i++) {

            $user_id = $TeamPortfolios[$i]->id;

            $caseCountPerMonth = DB::table('loan_case as l')
                ->whereMonth('l.created_at', '=', date('m'))
                ->whereYear('l.created_at', '=', date('Y'))
                ->where('l.status', '<>', 99)
                ->where(function ($q) use ($user_id) {
                    $q->where('l.clerk_id', '=', $user_id)
                        ->orWhere('l.lawyer_id', '=', $user_id);
                })->count();

            if ($TeamPortfolios[$i]->leader == 0 || $TeamPortfolios[$i]->solo == 1) {
                if (($caseCountPerMonth < $TeamPortfolios[$i]->total_file_per_month)) {
                    array_push($portolio_id, [
                        'id' => $TeamPortfolios[$i]->id,
                        'case_count_per_month' => $caseCountPerMonth,
                        'team_id' => $TeamPortfolios[$i]->team_main_id,
                        'leader' => $TeamPortfolios[$i]->leader
                    ]);
                }
            }
        }

        // return $portolio_id;
        // Log::info('test1: '.json_encode($portolio_id));

        if (count($portolio_id) <= 0) {
            array_push($result,  ["status" => 0, 'message' => 'Current branch can\'t take on new case due to reach max file per month']);
            return $result;
        }


        if (count($portolio_id) > 0) {
            $count = 0;
            for ($i = 0; $i < count($portolio_id); $i++) {
                if ($i == 0) {
                    $count = $portolio_id[$i]['case_count_per_month'];
                }



                if ($portolio_id[$i]['case_count_per_month'] <= $count) {

                    // Log::info('test: '. $portolio_id[$i]['case_count_per_month']);

                    if ($portolio_id[$i]['leader'] == 0 || $TeamPortfolios[$i]->solo == 1) {
                        $selected_id = $portolio_id[$i]['id'];
                        $team_id = $portolio_id[$i]['team_id'];

                        $count = $portolio_id[$i]['case_count_per_month'];
                    }
                }
            }
        }

        // return $selected_id;

        $lawyerID = 0;
        $lawyerNick = '';
        $clerkID = 0;
        $clerkNick = '';

        $result = [];

        // get clerk id and name
        $clerk = Users::where('id', '=', $selected_id)->first();

        // return ($clerk);


        if ($clerk) {
            $clerkID  = $clerk->id;
            $clerkNick  =  $clerk->nick_name;
        }

        if ($team_id != 0) {
            $team_leader = TeamMembers::where('team_main_id', '=', $team_id)->where('leader', '=', 1)->first();

            $lawyer = Users::where('id', '=', $team_leader->user_id)->first();

            $lawyerID  = $lawyer->id;
            $lawyerNick  =  $lawyer->nick_name;
        }

        // If chamber clear clerk id
        if ($lawyerID == $clerkID) {
            $clerkID = 0;
            $clerkNick = '';
        }

        array_push($result,  ["status" => 1, "team_id" => $team_id, "lawyer_id" =>  $lawyerID, "lawyer_nick" =>  $lawyerNick,  "clerk_id" =>  $clerkID,  "clerk_nick" =>  $clerkNick]);

        return $result;
    }

    public function caseAssignAutomationV2(Request $request)
    {

        $bank_id =  $request->input('bank');
        $portolio_id = [];
        $cases_list = [];
        $result = [];

        $selected_id = 0;
        $team_id = 0;


        $current_user = auth()->user();

        // $TeamPortfolios = TeamPortfolios::where('portfolio_id', '=', $request->input('bank'))->get();

        // return $request->input('bank');

        if ($current_user->only_own_case == 1) {
            $lawyer = Users::where('id', '=', $current_user->id)->first();
            $clerk_id = 0;
            $clerk_nick = '';



            $team_main_id = TeamMembers::where('user_id', $current_user->id)->pluck('team_main_id');

            $TeamMembers = TeamMembers::where('team_main_id', $team_main_id)
                ->where('leader', 0)
                ->where('status', 1)->get();




            // return $TeamPortfolios;

            if (count($TeamMembers) > 0) {
                $case_count = 0;

                for ($i = 0; $i < count($TeamMembers); $i++) {
                    $user_id = $TeamMembers[$i]->user_id;

                    $caseCountPerMonth = DB::table('loan_case as l')
                        ->whereMonth('l.created_at', '=', date('m'))
                        ->whereYear('l.created_at', '=', date('Y'))
                        ->where('l.status', '<>', 99)
                        ->where(function ($q) use ($user_id) {
                            $q->where('l.clerk_id', '=', $user_id)
                                ->orWhere('l.lawyer_id', '=', $user_id);
                        })->count();

                    if ($i == 0) {
                        $case_count = $caseCountPerMonth;
                    }

                    if (($caseCountPerMonth <= $case_count)) {
                        $clerk_id = $user_id;
                        $case_count = $caseCountPerMonth;
                    }
                }
            }

            if ($clerk_id != 0) {
                $clerk_nick = Users::where('id', '=', $clerk_id)->pluck('nick_name');
            }

            array_push($result,  ["status" => 1, "team_id" => $team_id, "lawyer_id" =>  $current_user->id, "lawyer_nick" =>  $lawyer->nick_name,  "clerk_id" =>  $clerk_id,  "clerk_nick" =>  $clerk_nick]);

            return $result;
        }

        $accessInfo = AccessController::manageAccess();

        $TeamPortfolios = DB::table('team_portfolios as tp')
            ->leftJoin('team_members as tm', 'tm.user_id', '=', 'tp.user_id')
            ->leftJoin('users as u', 'tm.user_id', '=', 'u.id')
            ->select('tp.user_id as id', 'tm.total_file_per_month', 'tm.team_main_id', 'tm.leader', 'tm.chamber', 'tm.solo')
            ->where('tp.portfolio_id', '=', $request->input('bank'))
            ->where('u.branch_id', '=', $request->input('branch'))
            // ->whereIn('u.branch_id', $accessInfo['brancAccessList'])
            ->where('tm.status', '=', 1)
            ->where('u.only_own_case', 0)
            ->where('u.status', '<>', 99)
            ->where(function ($q) {
                $q->where('tm.leader', '=', 0)
                    ->orWhere('tm.chamber', '=', 1);
            })
            ->get();

        $team_merge = [];

        // return $TeamPortfolios;

        //Temporary allow munirah and stella team go into case assign pool for HQ
        if (in_array($request->input('branch'), [1])) {
            $TeamPortfolios_2 = DB::table('team_portfolios as tp')
                ->leftJoin('team_members as tm', 'tm.user_id', '=', 'tp.user_id')
                ->leftJoin('users as u', 'tm.user_id', '=', 'u.id')
                ->select('tp.user_id as id', 'tm.total_file_per_month', 'tm.team_main_id', 'tm.leader', 'tm.chamber', 'tm.solo')
                ->where('tp.portfolio_id', '=', $request->input('bank'))
                ->whereIn('tm.team_main_id', [8, 9, 10])
                ->where('tm.status', '=', 1)
                ->where('u.only_own_case', 0)
                ->where('u.status', '<>', 99)
                ->where(function ($q) {
                    $q->where('tm.leader', '=', 0)
                        ->orWhere('tm.chamber', '=', 1);
                })
                ->get();

            $TeamPortfolios = $TeamPortfolios->merge($TeamPortfolios_2);
        }



        if (count($TeamPortfolios) <= 0) {
            array_push($result,  ["status" => 0, 'message' => 'No team handle this case type']);
            return $result;
        }

        // return $TeamPortfolios;

        // Log::info('test12: '.json_encode($TeamPortfolios));


        for ($i = 0; $i < count($TeamPortfolios); $i++) {

            $user_id = $TeamPortfolios[$i]->id;

            $caseCountPerMonth = DB::table('loan_case as l')
                ->whereMonth('l.created_at', '=', date('m'))
                ->whereYear('l.created_at', '=', date('Y'))
                ->where('l.status', '<>', 99)
                ->where(function ($q) use ($user_id) {
                    $q->where('l.clerk_id', '=', $user_id)
                        ->orWhere('l.lawyer_id', '=', $user_id);
                })->count();

            if ($TeamPortfolios[$i]->leader == 0 || $TeamPortfolios[$i]->solo == 1) {
                if (($caseCountPerMonth < $TeamPortfolios[$i]->total_file_per_month)) {
                    array_push($portolio_id, [
                        'id' => $TeamPortfolios[$i]->id,
                        'case_count_per_month' => $caseCountPerMonth,
                        'team_id' => $TeamPortfolios[$i]->team_main_id,
                        'leader' => $TeamPortfolios[$i]->leader
                    ]);
                }
            }
        }

        // return $portolio_id;
        // Log::info('test1: '.json_encode($portolio_id));

        if (count($portolio_id) <= 0) {
            array_push($result,  ["status" => 0, 'message' => 'Current branch can\'t take on new case due to reach max file per month']);
            return $result;
        }


        if (count($portolio_id) > 0) {
            $count = 0;
            for ($i = 0; $i < count($portolio_id); $i++) {
                if ($i == 0) {
                    $count = $portolio_id[$i]['case_count_per_month'];
                }



                if ($portolio_id[$i]['case_count_per_month'] <= $count) {

                    // Log::info('test: '. $portolio_id[$i]['case_count_per_month']);

                    if ($portolio_id[$i]['leader'] == 0 || $TeamPortfolios[$i]->solo == 1) {
                        $selected_id = $portolio_id[$i]['id'];
                        $team_id = $portolio_id[$i]['team_id'];

                        $count = $portolio_id[$i]['case_count_per_month'];
                    }
                }
            }
        }

        // return $selected_id;

        $lawyerID = 0;
        $lawyerNick = '';
        $clerkID = 0;
        $clerkNick = '';

        $result = [];

        // get clerk id and name
        $clerk = Users::where('id', '=', $selected_id)->first();

        // return ($clerk);


        if ($clerk) {
            $clerkID  = $clerk->id;
            $clerkNick  =  $clerk->nick_name;
        }

        if ($team_id != 0) {
            $team_leader = TeamMembers::where('team_main_id', '=', $team_id)->where('leader', '=', 1)->first();

            $lawyer = Users::where('id', '=', $team_leader->user_id)->first();

            $lawyerID  = $lawyer->id;
            $lawyerNick  =  $lawyer->nick_name;
        }

        // If chamber clear clerk id
        if ($lawyerID == $clerkID) {
            $clerkID = 0;
            $clerkNick = '';
        }

        array_push($result,  ["status" => 1, "team_id" => $team_id, "lawyer_id" =>  $lawyerID, "lawyer_nick" =>  $lawyerNick,  "clerk_id" =>  $clerkID,  "clerk_nick" =>  $clerkNick]);

        return $result;
    }

    public function CaseRotation(Request $request)
    {

        $bank_id =  $request->input('bank');
        $portolio_id = [];
        $cases_list = [];
        $result = [];

        $selected_id = 0;
        $team_id = 0;
        // $TeamPortfolios = TeamPortfolios::where('portfolio_id', '=', $request->input('bank'))->get();

        // return $request->input('bank');

        $TeamPortfolios = DB::table('team_portfolios as tp')
            ->leftJoin('team_members as tm', 'tm.user_id', '=', 'tp.user_id')
            ->leftJoin('users as u', 'tm.user_id', '=', 'u.id')
            ->select('tp.user_id as id', 'tm.total_file_per_month', 'tm.team_main_id', 'tm.leader')
            ->where('tp.portfolio_id', '=', $request->input('bank'))
            ->where('u.branch_id', '=', $request->input('branch'))
            ->where('tm.status', '=', 1)
            ->where(function ($q) {
                $q->where('tm.leader', '=', 0)
                    ->orWhere('tm.chamber', '=', 1);
            })
            ->get();

        // return $TeamPortfolios;

        if (count($TeamPortfolios) <= 0) {
            array_push($result,  ["status" => 0, 'message' => 'No team handle this case type']);
            return $result;
        }



        for ($i = 0; $i < count($TeamPortfolios); $i++) {

            $user_id = $TeamPortfolios[$i]->id;

            $caseCount = DB::table('loan_case as l')
                ->whereMonth('l.created_at', '=', date('m'))
                ->where('l.status', '<>', 99)
                ->where(function ($q) use ($user_id) {
                    $q->where('l.clerk_id', '=', $user_id)
                        ->orWhere('l.lawyer_id', '=', $user_id);
                })
                ->where('l.bank_id', '=', $request->input('bank'))->count();

            $caseCountPerMonth = DB::table('loan_case as l')
                ->whereMonth('l.created_at', '=', date('m'))
                ->where('l.status', '<>', 99)
                ->where(function ($q) use ($user_id) {
                    $q->where('l.clerk_id', '=', $user_id)
                        ->orWhere('l.lawyer_id', '=', $user_id);
                })->count();

            if (($caseCountPerMonth < $TeamPortfolios[$i]->total_file_per_month)) {
                array_push($portolio_id, [
                    'id' => $TeamPortfolios[$i]->id,
                    'portfolio_case_count' => $caseCount,
                    'case_count_per_month' => $caseCountPerMonth,
                    'team_id' => $TeamPortfolios[$i]->team_main_id,
                    'leader' => $TeamPortfolios[$i]->leader
                ]);
            }
        }



        if (count($portolio_id) <= 0) {
            array_push($result,  ["status" => 0, 'message' => 'Current branch can\'t take on new case due to reach max file per month']);
            return $result;
        }


        if (count($portolio_id) > 0) {
            $count = 0;
            for ($i = 0; $i < count($portolio_id); $i++) {
                if ($i == 0) {
                    $count = $portolio_id[$i]['case_count_per_month'];
                }

                if ($portolio_id[$i]['case_count_per_month'] <= $count) {

                    if ($portolio_id[$i]['leader'] == 0) {
                        $selected_id = $portolio_id[$i]['id'];
                        $team_id = $portolio_id[$i]['team_id'];
                    }
                }
            }
        }

        $lawyerID = 0;
        $lawyerNick = '';
        $clerkID = 0;
        $clerkNick = '';

        $result = [];

        // get clerk id and name
        $clerk = Users::where('id', '=', $selected_id)->first();

        // return ($clerk);


        if ($clerk) {
            $clerkID  = $clerk->id;
            $clerkNick  =  $clerk->nick_name;
        }

        if ($team_id != 0) {
            $team_leader = TeamMembers::where('team_main_id', '=', $team_id)->where('leader', '=', 1)->first();

            $lawyer = Users::where('id', '=', $team_leader->user_id)->first();

            $lawyerID  = $lawyer->id;
            $lawyerNick  =  $lawyer->nick_name;
        }

        // If chamber clear clerk id
        if ($lawyerID == $clerkID) {
            $clerkID = 0;
            $clerkNick = '';
        }

        array_push($result,  ["status" => 1, "team_id" => $team_id, "lawyer_id" =>  $lawyerID, "lawyer_nick" =>  $lawyerNick,  "clerk_id" =>  $clerkID,  "clerk_nick" =>  $clerkNick]);

        return $result;
    }

    public function checkTeamCaseCount(Request $request, $user_id)
    {
        $team_id = 0;
        $caseCount = DB::table('loan_case as l')
            ->whereMonth('l.created_at', '=', date('m'))
            ->where('l.status', '<>', 99)
            ->where(function ($q) use ($user_id) {
                $q->where('l.clerk_id', '=', $user_id)
                    ->orWhere('l.lawyer_id', '=', $user_id);
            })
            ->where('l.bank_id', '=', $request->input('bank'))->count();

        $TeamMembers = TeamMembers::where('user_id', '=', $user_id)->first();

        $caseCountPerMonth = DB::table('loan_case as l')
            ->whereMonth('l.created_at', '=', date('m'))
            ->where('l.status', '<>', 99)
            ->where(function ($q) use ($user_id) {
                $q->where('l.clerk_id', '=', $user_id)
                    ->orWhere('l.lawyer_id', '=', $user_id);
            })->count();


        if (($caseCountPerMonth < $TeamMembers->total_file_per_month)) {

            // $TeamMembers = TeamMembers::where('user_id', '=', $user_id)->first();
            $team_id = $TeamMembers->team_main_id;
        }

        return $team_id;
    }

    public function checkTeamPortfolio(Request $request, $user_id)
    {
        $TeamPortfolios = TeamPortfolios::where('user_id', '=', $user_id)->where('portfolio_id', '=', $request->input('bank'))->first();

        if ($TeamPortfolios) {
            return 1;
        }

        return 0;
    }

    public function getStaffBranchID($staff_id, $branch_id)
    {
        $return_branch_id = $branch_id;

        if ($staff_id) {
            $User = User::where('id', '=', $staff_id)->first();

            if ($User) {
                $return_branch_id = $User->branch_id;
            }
        }

        return $return_branch_id;
    }


    public function updateCaseStatus(Request $request, $id)
    {
        $status = 0;
        $text = '';
        $current_user = auth()->user();

        try {
            if ($request->input('type')) {
                if ($request->input('type') == 'CLOSED') {
                    $status = 0;
                    $text = 'closed';
                } elseif ($request->input('type') == 'ABORTED') {
                    $status = 99;
                    $text = 'aborted';
                } elseif ($request->input('type') == 'PENDINGCLOSED') {
                    $status = 4;
                    $text = 'pending close';
                } elseif ($request->input('type') == 'REOPEN') {
                    $status = 1;
                    $text = 'reopen';
                } elseif ($request->input('type') == 'REVIEWING') {
                    $status = 7;
                    $text = 'reviewing';
                }

                $loanCase = LoanCase::where('id', '=', $id)->first();


                if ($loanCase) {
                    $loanCase->status = $status;

                    if ($request->input('type') == 'PENDINGCLOSED') {
                        $loanCase->pending_close_date = date('Y-m-d H:i:s');

                        $data = ['bonus_type' => 'CLOSEDCASE'];
                        $requestNew = Request::create('/example', 'POST', $data);

                        $BonusRequestListCheck = BonusRequestList::where('case_id', $id)->where('bonus_type', 'CLOSEDCASE')->count();

                        if ($BonusRequestListCheck <= 0) {
                            $this->submitBonusReview($requestNew, $id);
                        }
                    }

                    if ($request->input('type') == 'REVIEWING') {
                        $loanCase->request_close_date = date('Y-m-d H:i:s');
                    }

                    $loanCase->save();

                    $LoanCaseKivNotes = new LoanCaseKivNotes();

                    $LoanCaseKivNotes->case_id =  $id;
                    $LoanCaseKivNotes->notes =  '[' . $current_user->name . ' update case status to ' . $text . ']';
                    $LoanCaseKivNotes->label =  'case_status';
                    $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
                    $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');

                    $LoanCaseKivNotes->status =  1;
                    $LoanCaseKivNotes->created_by = $current_user->id;
                    $LoanCaseKivNotes->save();
                }
            }
        } catch (\Throwable $e) {
            $status = 0;
            $message = $e;
        }

        return response()->json(['status' => 1, 'message' => 'Successfully created new case']);
    }

    public function reopenCase($id)
    {
        $status = 0;
        try {
            $loanCase = LoanCase::where('id', '=', $id)->first();
            if ($loanCase) {
                $loanCase->status = 1;
                $loanCase->save();
            }
        } catch (\Throwable $e) {
            $status = 0;
            $message = $e;
        }

        return response()->json(['status' => 1, 'message' => 'Successfully created new case']);
    }

    public function submitBonusReview(Request $request, $id)
    {

        $current_user = auth()->user();
        $BonusRequestList  = new BonusRequestList();

        $BonusRequestList->user_id = $current_user->id;
        $BonusRequestList->case_id = $id;
        $BonusRequestList->bonus_type = $request->input('bonus_type');
        $BonusRequestList->status = 1;
        // $BonusRequestList->bonus_type = 'CLOSEDCASE';
        $BonusRequestList->created_at = date('Y-m-d H:i:s');
        $BonusRequestList->save();

        return response()->json(['status' => 1, 'message' => 'Request sent']);
    }

    public function submitClaimsRequest(Request $request, $id)
    {

        $current_user = auth()->user();

        $ClaimRequest = ClaimRequest::where('case_id', '=', $id)->where('claims_type', '=', $request->input('type'))->first();

        if ($ClaimRequest) {
            return response()->json(['status' => 2, 'message' => 'This claims already submitted']);
        }

        $ClaimRequest  = new ClaimRequest();

        $ClaimRequest->user_id = $current_user->id;
        $ClaimRequest->case_id = $id;
        $ClaimRequest->claims_type = $request->input('type');
        $ClaimRequest->user_role = $current_user->menuroles;
        $ClaimRequest->percentage = $request->input('percentage');
        $ClaimRequest->created_at = date('Y-m-d H:i:s');
        $ClaimRequest->status = 2;
        $ClaimRequest->save();

        $claims = DB::table('claims_type as a')
            ->leftjoin('claims_request as b', function ($join) use ($id) {
                $join->on('b.claims_type', '=', 'a.id')
                    ->where('b.case_id', '=', $id);
            })
            ->select('a.*', 'b.status as claims_status', 'b.created_at as submit_date')
            ->get();

        return response()->json([
            'status' => 1,
            'message' => 'Claims submitted',
            'view' => view('dashboard.case.table.tbl-case-claims', compact('claims'))->render(),
        ]);
    }

    public function updateCaseSummary(Request $request, $id)
    {
        $status = 0;
        $current_user = auth()->user();
        try {

            $case = LoanCase::where('id', '=', $id)->first();


            if ($case) {
                $this->modifiedCheck($case->purchase_price, $request->input('purchase_price'));

                if ($case->purchase_price != $request->input('purchase_price')) {
                    $LegalCloudCaseActivityLog  = new LegalCloudCaseActivityLog();

                    $LegalCloudCaseActivityLog->user_id = $current_user->id;
                    $LegalCloudCaseActivityLog->case_id = $id;
                    $LegalCloudCaseActivityLog->ori_text = $case->purchase_price;
                    $LegalCloudCaseActivityLog->edit_text = $request->input('purchase_price');
                    $LegalCloudCaseActivityLog->action = 'UpdateCaseSummary';
                    $LegalCloudCaseActivityLog->desc = $current_user->name . ' updated purchase price ' .  number_format($case->purchase_price, 2, '.', ',') . ' to ' . number_format($request->input('purchase_price'), 2, '.', ',');
                    $LegalCloudCaseActivityLog->status = 1;
                    $LegalCloudCaseActivityLog->created_at = date('Y-m-d H:i:s');
                    $LegalCloudCaseActivityLog->save();

                    $case->purchase_price = $request->input('purchase_price');
                }

                if ($case->loan_sum != $request->input('loan_sum')) {
                    $LegalCloudCaseActivityLog  = new LegalCloudCaseActivityLog();

                    $LegalCloudCaseActivityLog->user_id = $current_user->id;
                    $LegalCloudCaseActivityLog->case_id = $id;
                    $LegalCloudCaseActivityLog->ori_text = $case->purchase_price;
                    $LegalCloudCaseActivityLog->edit_text = $request->input('loan_sum');
                    $LegalCloudCaseActivityLog->action = 'UpdateCaseSummary';
                    $LegalCloudCaseActivityLog->desc = $current_user->name . ' updated loan sum ' .  number_format($case->loan_sum, 2, '.', ',') . ' to ' . number_format($request->input('loan_sum'), 2, '.', ',');
                    $LegalCloudCaseActivityLog->status = 1;
                    $LegalCloudCaseActivityLog->created_at = date('Y-m-d H:i:s');
                    $LegalCloudCaseActivityLog->save();

                    $case->loan_sum = $request->input('loan_sum');
                }

                if ($case->bank_id != $request->input('portfolio')) {
                    $LegalCloudCaseActivityLog  = new LegalCloudCaseActivityLog();

                    $oldPortfolio = Portfolio::where('id', '=', $case->bank_id)->first();
                    $newPortfolio = Portfolio::where('id', '=', $request->input('portfolio'))->first();

                    $LegalCloudCaseActivityLog->user_id = $current_user->id;
                    $LegalCloudCaseActivityLog->case_id = $id;
                    $LegalCloudCaseActivityLog->ori_text = $oldPortfolio->name;
                    $LegalCloudCaseActivityLog->edit_text = $newPortfolio->name;
                    $LegalCloudCaseActivityLog->action = 'UpdateCaseSummary';
                    $LegalCloudCaseActivityLog->desc = $current_user->name . ' updated case type ' .  $oldPortfolio->name . ' to ' . $newPortfolio->name;
                    $LegalCloudCaseActivityLog->status = 1;
                    $LegalCloudCaseActivityLog->object_id = $case->bank_id;
                    $LegalCloudCaseActivityLog->object_id_2 = $request->input('portfolio');
                    $LegalCloudCaseActivityLog->created_at = date('Y-m-d H:i:s');
                    $LegalCloudCaseActivityLog->save();

                    $case->bank_id = $request->input('portfolio');
                }

                if ($case->bank_ref != $request->input('bank_ref')) {
                    $LegalCloudCaseActivityLog  = new LegalCloudCaseActivityLog();

                    $LegalCloudCaseActivityLog->user_id = $current_user->id;
                    $LegalCloudCaseActivityLog->case_id = $id;
                    $LegalCloudCaseActivityLog->ori_text = $case->bank_ref;
                    $LegalCloudCaseActivityLog->edit_text = $request->input('bank_ref');
                    $LegalCloudCaseActivityLog->action = 'UpdateCaseSummary';
                    $LegalCloudCaseActivityLog->desc = $current_user->name . ' updated bank ref ' .  $case->bank_ref . ' to ' . $request->input('bank_ref');
                    $LegalCloudCaseActivityLog->status = 1;
                    $LegalCloudCaseActivityLog->created_at = date('Y-m-d H:i:s');
                    $LegalCloudCaseActivityLog->save();

                    $case->bank_ref = $request->input('bank_ref');
                }

                if ($case->bank_li_date != $request->input('bank_li_date')) {
                    $LegalCloudCaseActivityLog  = new LegalCloudCaseActivityLog();

                    $LegalCloudCaseActivityLog->user_id = $current_user->id;
                    $LegalCloudCaseActivityLog->case_id = $id;
                    $LegalCloudCaseActivityLog->ori_text = $case->bank_li_date;
                    $LegalCloudCaseActivityLog->edit_text = $request->input('bank_li_date');
                    $LegalCloudCaseActivityLog->action = 'UpdateCaseSummary';
                    $LegalCloudCaseActivityLog->desc = $current_user->name . ' updated bank LI Date ' .  $case->bank_li_date . ' to ' . $request->input('bank_li_date');
                    $LegalCloudCaseActivityLog->status = 1;
                    $LegalCloudCaseActivityLog->created_at = date('Y-m-d H:i:s');
                    $LegalCloudCaseActivityLog->save();

                    $case->bank_li_date = $request->input('bank_li_date');
                }

                if ($case->property_address != $request->input('property_address')) {
                    $LegalCloudCaseActivityLog  = new LegalCloudCaseActivityLog();

                    $LegalCloudCaseActivityLog->user_id = $current_user->id;
                    $LegalCloudCaseActivityLog->case_id = $id;
                    $LegalCloudCaseActivityLog->ori_text = $case->property_address;
                    $LegalCloudCaseActivityLog->edit_text = $request->input('property_address');
                    $LegalCloudCaseActivityLog->action = 'UpdateCaseSummary';
                    $LegalCloudCaseActivityLog->desc = $current_user->name . ' updated property address';
                    $LegalCloudCaseActivityLog->status = 1;
                    $LegalCloudCaseActivityLog->created_at = date('Y-m-d H:i:s');
                    $LegalCloudCaseActivityLog->save();

                    $case->property_address = $request->input('property_address');
                }

                $case->save();

                $case->case_ref_no = $this->updateNewRefNo($case, 0, 0, 0);
                $case->save();
            }
        } catch (\Throwable $e) {
            $status = 0;
            $message = $e;
        }

        $case = DB::table('loan_case as l')
            ->leftJoin('portfolio as p', 'l.bank_id', '=', 'p.id')
            ->select('l.*', 'p.name as portfolio')
            ->where('l.id', '=', $id)
            ->first();

        $now = time(); // or your date as well
        $your_date = strtotime($case->created_at);
        $datediff = $now - $your_date;
        $datediff = ($datediff / (60 * 60 * 24));
        $datediff = number_format($datediff);

        $BonusRequestListSent = BonusRequestList::where('case_id', '=', $id)->where('bonus_type', '=', 'CLOSEDCASE')->count();
        $SMPBonusRequestListSent = BonusRequestList::where('case_id', '=', $id)->where('bonus_type', '=', 'SMPSIGNED')->count();

        $bank_lo_date = DB::table('loan_case_masterlist as m')
            ->where('m.case_id', '=', $id)
            ->where('m.masterlist_field_id', '=', 398)
            ->first();

        $case = DB::table('loan_case as l')
            ->leftJoin('portfolio as p', 'l.bank_id', '=', 'p.id')
            ->leftJoin('branch as b', 'l.branch_id', '=', 'b.id')
            ->select('l.*', 'p.name as portfolio', 'b.name as branch_name')
            ->where('l.id', '=', $id)
            ->first();

        return response()->json([
            'status' => 1,
            'message' => 'Successfully updated case summary',
            'view' => view('dashboard.case.section.d-case-summary', compact('case', 'current_user', 'datediff', 'BonusRequestListSent', 'SMPBonusRequestListSent', 'bank_lo_date'))->render(),
        ]);
    }

    public function saveInvoiceDate(Request $request, $id)
    {
        $status = 0;
        $current_user = auth()->user();

        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();
        $LoanCaseBillMain->invoice_date = $request->input('invoice_date');
        $LoanCaseBillMain->save();

        LoanCaseInvoiceMain::where('loan_case_main_bill_id', $id)->update(['invoice_date' => $request->input('invoice_date')]);

        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'LoanCaseBillMain' => $LoanCaseBillMain
        ]);
    }

    public function saveSSTRate(Request $request, $id)
    {
        $status = 0;
        $current_user = auth()->user();

        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();
        $LoanCaseBillMain->sst_rate = $request->input('sst_rate');
        $LoanCaseBillMain->save();

        $this->updatePfeeDisbAmount($id);

        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'LoanCaseBillMain' => $LoanCaseBillMain
        ]);
    }

    public function calculateEstimateBonus($LoanCaseID, $LoanCaseBillMainID)
    {

        $bonus_2_percent = 0;
        $bonus_3_percent = 0;
        $clerk_id = 0;

        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $LoanCaseBillMainID)->first();
        $LoanCase = LoanCase::where('id', '=', $LoanCaseID)->first();

        $discount = $LoanCase->agreed_fee - $LoanCase->targeted_collect_amount;
        // $discount = $LoanCase->targeted_collect_amount - $LoanCase->agreed_fee;

        // if ($LoanCase->agreed_fee == 0) {
        //     return;
        // }

        // if ($discount < 0) {
        //     return;
        // }


        if ($LoanCaseBillMain->pfee1 > 0) {

            BonusEstimate::where('bill_id', $LoanCaseBillMainID)->update(['status' => 0]);

            $bonus_2_percent = ($LoanCaseBillMain->pfee1 -  $discount) * 0.02;
            $bonus_3_percent = ($LoanCaseBillMain->pfee1 -  $discount) * 0.03;

            if ($bonus_2_percent < 0) {
                $bonus_2_percent = 0;
                $bonus_3_percent = 0;
            }



            // create bonus for lawyer
            $Bonus = new BonusEstimate();
            $Bonus->user_id = $LoanCase->lawyer_id;
            $Bonus->case_id = $LoanCase->id;
            $Bonus->bill_id = $LoanCaseBillMain->id;
            $Bonus->bonus_2_percent = $bonus_2_percent;
            $Bonus->bonus_3_percent = $bonus_3_percent;
            $Bonus->status = 1;
            // $Bonus->created_at = now();
            $Bonus->case_transferred = 0;
            $Bonus->save();

            if ($LoanCase->clerk_id == 0) {
                $clerk_id = $LoanCase->lawyer_id;
            } else {
                $clerk_id = $LoanCase->clerk_id;
            }

            // create bonus for clerk
            $Bonus = new BonusEstimate();
            $Bonus->user_id = $clerk_id;
            $Bonus->case_id = $LoanCase->id;
            $Bonus->bill_id = $LoanCaseBillMain->id;
            $Bonus->bonus_2_percent = $bonus_2_percent;
            $Bonus->bonus_3_percent = $bonus_3_percent;
            $Bonus->status = 1;
            // $Bonus->created_at = now();
            $Bonus->case_transferred = 0;
            $Bonus->save();
        }
    }

    public function calculateReferralFee($LoanCaseID, $LoanCaseBillMainID, $referral_id)
    {

        $bonus_2_percent = 0;
        $bonus_3_percent = 0;
        $clerk_id = 0;
        $referral_fee = 0;

        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $LoanCaseBillMainID)->first();
        $LoanCase = LoanCase::where('id', '=', $LoanCaseID)->first();

        $discount = $LoanCase->agreed_fee - $LoanCaseBillMain->collection_amount;

        if ($referral_id == 0) {
            if ($LoanCase->referral_id == 0) {
                return;
            }

            $referral_id = $LoanCase->referral_id;
        }




        if ($LoanCaseBillMain->pfee1 > 0) {

            $Referral = Referral::where('id', '=', $referral_id)->first();

            if (!$Referral) {
                return;
            }

            $formula = $Referral->referral_formula_id;


            switch ($formula) {
                case 1:
                    $referral_fee = $LoanCaseBillMain->pfee1 * 0.6;
                    break;
                case 2:
                    $referral_fee = ($LoanCaseBillMain->pfee1 * 0.5) - $discount;
                    break;
                case 3:
                    $referral_fee = ($LoanCaseBillMain->pfee1 + $LoanCaseBillMain->pfee2) - $discount;
                    break;
                case 4:
                    $referral_fee = ($LoanCaseBillMain->pfee1 * 0.6) * 0.05;
                    break;
                case 5:
                    $referral_fee = (($LoanCaseBillMain->pfee1 * 0.5) - $discount) * 0.1;
                    break;
                default:
            }

            if ($referral_fee < 0) {
                $referral_fee = 0;
            }

            ReferralFee::where('bill_id', $LoanCaseBillMain->id)->where('referral_id', $referral_id)->update(['status' => 0]);

            $ReferralFee = new ReferralFee();
            $ReferralFee->referral_id = $referral_id;
            $ReferralFee->case_id = $LoanCase->id;
            $ReferralFee->bill_id = $LoanCaseBillMain->id;
            $ReferralFee->amount = $referral_fee;
            $ReferralFee->created_at = now();
            $ReferralFee->save();

            if ($LoanCaseBillMain->referral_a1_trx_id == '' || $LoanCaseBillMain->referral_a1_trx_id == null) {
                if ($LoanCaseBillMain->referral_a1_ref_id == $referral_id) {
                    $LoanCaseBillMain->referral_a1 = $referral_fee;
                    $LoanCaseBillMain->save();
                }
            }



            if ($LoanCaseBillMain->referral_a2_ref_id == $referral_id) {
                $LoanCaseBillMain->referral_a2 = $referral_fee;
                $LoanCaseBillMain->save();
            }

            if ($LoanCaseBillMain->referral_a3_ref_id == $referral_id) {
                $LoanCaseBillMain->referral_a3 = $referral_fee;
                $LoanCaseBillMain->save();
            }

            if ($LoanCaseBillMain->referral_a4_ref_id == $referral_id) {
                $LoanCaseBillMain->referral_a4 = $referral_fee;
                $LoanCaseBillMain->save();
            }
        }
    }

    public function modifiedCheck($DBValue, $newvalue)
    {
        if ($DBValue != $newvalue) {
        }
    }


    public function checkCloseFileBalance($id)
    {
        $bill_id = [];
        $LoanCase = LoanCase::where('id', $id)->first();
        $current_user = auth()->user();

        $LoanCaseBillMain = LoanCaseBillMain::where('case_id', $id)->where('status', 1)->get();

        if (count($LoanCaseBillMain) > 0) {
            for ($i = 0; $i < count($LoanCaseBillMain); $i++) {
                $total_sum = 0;
                $total_sum = LoanCaseBillDetails::where('loan_case_main_bill_id', $LoanCaseBillMain[$i]->id)->where('status', 1)->sum('amount');


                $total_sum = LoanCaseBillDetails::where('loan_case_main_bill_id', $LoanCaseBillMain[$i]->id)->where('status', 1)->sum('amount');
                $total_sum = VoucherMain::where('case_bill_main_id', $LoanCaseBillMain[$i]->id)->where('account_approval', 1)->where('voucher_type', 4)->sum('total_amount');
                $VoucherMainDisburse = VoucherMain::where('case_bill_main_id', $LoanCaseBillMain[$i]->id)->where('account_approval', 1)->where('status', '<>', 99)->where('voucher_type', 1)->sum('total_amount');

                $LoanCaseBillMain[$i]->total_sum = $total_sum;
                $LoanCaseBillMain[$i]->total_disb = $VoucherMainDisburse;
                array_push($bill_id, $LoanCaseBillMain[$i]->id);
            }
        }


        $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', $id)->where('status', 1)->get();

        $TransferFeeDetails = DB::table('transfer_fee_details as a')
            ->leftJoin('transfer_fee_main as b', 'a.transfer_fee_main_id', '=', 'b.id')
            ->select('a.*', 'b.purpose', 'b.id as transfer_id', 'b.transaction_id as transaction_id')
            ->whereIn('loan_case_main_bill_id', $bill_id)
            ->where('a.status', 1)
            ->get();

        // $total_debit = LedgerEntries::where('case_id', $id)->where('status', 1)
        //     ->whereNotIn('type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SSTINRECON', 'TRANSFERINRECON', 'CLOSEFILEIN'])
        //     ->where('transaction_type', 'C')
        //     ->where('status', 1)->sum('amount');

        // $total_credit = LedgerEntries::where('case_id', $id)->where('status', 1)
        //     ->whereNotIn('type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SSTINRECON', 'TRANSFERINRECON', 'CLOSEFILEIN'])
        //     ->where('transaction_type', 'D') 
        //     ->where('status', 1)->sum('amount');

        $total_debit = LedgerEntriesV2::where('case_id', $id)->where('status', 1)
            // ->whereNotIn('type', ['RECONADD', 'RECONLESS', 'SST_IN', 'TRANSFER_IN', 'SSTINRECON', 'TRANSFERINRECON', 'CLOSEFILE_IN'])
            ->whereNotIn('type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN', 'ABORTFILE_IN'])
            ->where('transaction_type', 'C')
            ->where('status', 1)->sum('amount');

        $total_credit = LedgerEntriesV2::where('case_id', $id)->where('status', 1)
            // ->whereNotIn('type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SSTINRECON', 'TRANSFERINRECON', 'CLOSEFILEIN'])
            ->where('transaction_type', 'D')
            ->whereNotIn('type', ['RECONADD', 'RECONLESS', 'SSTIN', 'TRANSFERIN', 'SST_IN', 'TRANSFER_IN', 'CLOSEFILE_IN', 'ABORTFILE_IN'])
            ->where('status', 1)->sum('amount');


        $VoucherMain = VoucherMain::where('case_id', $id)->where('status', '<>', 99)->where('account_approval', 6)->get();

        // $JournalEntry = DB::table('journal_entry_main')->where('case_id', $id)->where('status', '<>', 99)->get();

        $JournalEntry = DB::table('journal_entry_details as a')
            ->leftJoin('journal_entry_main as b', 'a.journal_entry_main_id', '=', 'b.id')
            ->select('b.*', 'a.amount', 'a.transaction_type')
            ->where('a.case_id', $id)
            ->where('a.status', '<>', 99)
            ->get();


        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;

        $case_list = DB::table('loan_case as l')
            ->leftJoin('case_type', 'case_type.id', '=', 'l.case_type_id')
            ->leftJoin('client', 'client.id', '=', 'l.customer_id')
            ->leftJoin('users as u1', 'u1.id', '=', 'l.lawyer_id')
            ->leftJoin('users as u2', 'u2.id', '=', 'l.clerk_id')
            ->leftJoin('users as u3', 'u3.id', '=', 'l.sales_user_id')
            ->leftJoin('referral as r', 'r.id', '=', 'l.referral_id')
            ->leftJoin('branch as b', 'b.id', '=', 'l.branch_id')
            ->select(
                'l.*',
                'case_type.name AS type_name',
                'client.name AS client_name',
                'l.id AS case_id',
                'u1.name as lawyer_name',
                'u2.name as clerk_name',
                'u3.name as sales',
                'b.name as branch',
                'r.name as referral_name'
            );

        if (!in_array($userRoles, ['admin', 'management', 'account'])) {
            $accessCaseList = $this->caseManagementEngine();

            $case_list = $case_list->whereIn('l.id', $accessCaseList);
        }

        $case_list = $case_list->orderBy('l.created_at', 'DESC')->get();
        // return $JournalEntry;

        return response()->json([
            'status' => 1,
            'message' => 'Successfully updated case summary',
            'case_list' => $case_list,
            'view' => view('dashboard.case.table.tbl-close-file-bill-list', compact('LoanCaseBillMain', 'LoanCaseTrustMain', 'TransferFeeDetails', 'VoucherMain', 'total_credit', 'total_debit', 'JournalEntry', 'current_user'))->render(),
        ]);
    }

    public function getCaseListReq()
    {
        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;

        $case_list = DB::table('loan_case as l')
            ->leftJoin('case_type', 'case_type.id', '=', 'l.case_type_id')
            ->leftJoin('client', 'client.id', '=', 'l.customer_id')
            ->leftJoin('users as u1', 'u1.id', '=', 'l.lawyer_id')
            ->leftJoin('users as u2', 'u2.id', '=', 'l.clerk_id')
            ->leftJoin('users as u3', 'u3.id', '=', 'l.sales_user_id')
            ->leftJoin('referral as r', 'r.id', '=', 'l.referral_id')
            ->leftJoin('branch as b', 'b.id', '=', 'l.branch_id')
            ->whereIn('l.status', [1, 2, 3])
            ->select(
                'l.*',
                'case_type.name AS type_name',
                'client.name AS client_name',
                'l.id AS case_id',
                'u1.name as lawyer_name',
                'u2.name as clerk_name',
                'u3.name as sales',
                'b.name as branch',
                'r.name as referral_name'
            );

        if (!in_array($userRoles, ['admin', 'management', 'account'])) {
            $accessCaseList = $this->caseManagementEngine();

            $case_list = $case_list->whereIn('l.id', $accessCaseList);
        }

        $case_list = $case_list->orderBy('l.created_at', 'DESC')->get();


        return DataTables::of($case_list)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $actionBtn = ' <a  href="javascript:void(0)" onclick="selectCase(\'' . $row->case_ref_no . '\', ' . $row->id . ')" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>
                    ';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getBillListReq()
    {
        $current_user = auth()->user();
        $userRoles = $current_user->menuroles;

        // $case_list = DB::table('loan_case as l')
        $case_list = DB::table('loan_case_bill_main as bm')
            ->leftJoin('loan_case as l', 'bm.case_id', '=', 'l.id')
            ->leftJoin('case_type', 'case_type.id', '=', 'l.case_type_id')
            ->leftJoin('client', 'client.id', '=', 'l.customer_id')
            ->leftJoin('users as u1', 'u1.id', '=', 'l.lawyer_id')
            ->leftJoin('users as u2', 'u2.id', '=', 'l.clerk_id')
            ->leftJoin('users as u3', 'u3.id', '=', 'l.sales_user_id')
            ->leftJoin('referral as r', 'r.id', '=', 'l.referral_id')
            ->leftJoin('branch as b', 'b.id', '=', 'l.branch_id')
            ->whereIn('l.status', [1, 2, 3])
            ->whereNotIn('bm.status', [99])
            ->select(
                'l.*',
                'bm.bill_no',
                'bm.id as bill_id',
                'case_type.name AS type_name',
                'client.name AS client_name',
                'l.id AS case_id',
                'u1.name as lawyer_name',
                'u2.name as clerk_name',
                'u3.name as sales',
                'b.name as branch',
                'r.name as referral_name'
            );

        if (!in_array($userRoles, ['admin', 'management', 'account'])) {
            $accessCaseList = $this->caseManagementEngine();

            $case_list = $case_list->whereIn('l.id', $accessCaseList);
        }

        $case_list = $case_list->orderBy('bm.bill_no', 'ASC')->get();


        return DataTables::of($case_list)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $actionBtn = ' <a  href="javascript:void(0)" onclick="selectSearchBill(\'' . $row->bill_no . '\', ' . $row->bill_id . ', ' . $row->case_id . ')" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="View"><i class="cil-pencil"></i></a>
                    ';
                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }



    public function updateCloseFileDate(Request $request, $id)
    {
        $current_user = auth()->user();

        $LedgerEntries = LedgerEntriesV2::where('case_id', $id)->whereIn('type', ['CLOSEFILE_OUT', 'CLOSEFILE_IN', 'ABORTFILE_OUT', 'ABORTFILE_IN'])->get();

        if (count($LedgerEntries) > 0) {
            for ($i = 0; $i < count($LedgerEntries); $i++) {
                $LedgerEntries[$i]->date = $request->input('transfer_date');
                $LedgerEntries[$i]->transaction_id = $request->input('transaction_id');
                $LedgerEntries[$i]->remark = $request->input('remark');
                $LedgerEntries[$i]->save();
            }
        }

        $closeFileOut = LedgerEntriesV2::where('case_id', $id)->whereIn('type', ['CLOSEFILE_OUT', 'CLOSEFILE_IN', 'ABORTFILE_OUT', 'ABORTFILE_IN'])->where('transaction_type', 'C')->first();
        $closeFileIn = LedgerEntriesV2::where('case_id', $id)->whereIn('type', ['CLOSEFILE_OUT', 'CLOSEFILE_IN', 'ABORTFILE_OUT', 'ABORTFILE_IN'])->where('transaction_type', 'D')->first();

        // return AccountController::updateBankReconRecord($closeFileOut->recon_date,  $request->input('transfer_from'));

        if ($closeFileOut->bank_id != $request->input('transfer_from')) {
            $prev_id = $closeFileOut->bank_id;
            $closeFileOut->bank_id = $request->input('transfer_from');
            $closeFileOut->save();

            AccountController::updateBankReconRecord($closeFileOut->recon_date,  $prev_id);
            AccountController::updateBankReconRecord($closeFileOut->recon_date,  $request->input('transfer_from'));
        }

        if ($closeFileIn->bank_id != $request->input('transfer_to')) {
            $prev_id = $closeFileIn->bank_id;
            $closeFileIn->bank_id = $request->input('transfer_to');
            $closeFileIn->save();

            AccountController::updateBankReconRecord($closeFileOut->recon_date,  $prev_id);
            AccountController::updateBankReconRecord($closeFileIn->recon_date,  $request->input('transfer_to'));
        }

        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = $current_user->id;
        $AccountLog->case_id = $closeFileOut->case_id;
        $AccountLog->bill_id = $id;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->action = 'UpdateCloseFileDetails';
        $AccountLog->desc = $current_user->name . ' Updated closed file details';
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();

        return response()->json([
            'status' => 1,
            'message' => 'Close file entries updated'
        ]);
    }

    public function closeFile(Request $request, $id)
    {
        // return $request;
        $current_user = auth()->user();
        $type = 'CLOSEFILE';
        $action_type = 'closed';
        $status_type = 0;

        if ($request->input('close_abort') == 'abort') {
            $type = 'ABORTFILE';
            $action_type = 'aborted';
            $status_type = 99;
        }

        $checkLedger = LedgerEntriesV2::where('case_id', $id)->where('status', '<>', 99)->whereIn('type', ['ABORTFILE_OUT', 'ABORTFILE_IN', 'CLOSEFILE_OUT', 'CLOSEFILE_IN'])->get();
        $LoanCase = LoanCase::where('id', $id)->first();

        if (count($checkLedger) == 0) {
            $LedgerEntries = new LedgerEntries();

            $LedgerEntries->transaction_id = $request->input('trx_id');
            $LedgerEntries->case_id = $id;
            $LedgerEntries->loan_case_main_bill_id = 0;
            $LedgerEntries->user_id = $current_user->id;
            $LedgerEntries->key_id = 0;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $request->input('transfer_amount');
            $LedgerEntries->bank_id = $request->input('transfer_from');
            $LedgerEntries->remark = $request->input('remark');
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $request->input('transfer_date');
            $LedgerEntries->type = $type . 'OUT';

            $LedgerEntries->save();


            $LedgerEntries = new LedgerEntriesV2();

            $LedgerEntries->transaction_id = $request->input('trx_id');
            $LedgerEntries->case_id = $id;
            $LedgerEntries->loan_case_main_bill_id = 0;
            $LedgerEntries->user_id = $current_user->id;
            $LedgerEntries->key_id = 0;
            $LedgerEntries->transaction_type = 'C';
            $LedgerEntries->amount = $request->input('transfer_amount');
            $LedgerEntries->bank_id = $request->input('transfer_from');
            $LedgerEntries->remark = $request->input('remark');
            $LedgerEntries->status = 1;
            $LedgerEntries->last_row_entry = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $request->input('transfer_date');
            $LedgerEntries->type = $type . '_OUT';

            if ($request->input('transfer_type') != null) {
                if ($request->input('transfer_type') == 'bill') {
                    $LedgerEntries->key_id_2 = $request->input('to_case');
                    $LedgerEntries->key_id_3 = $request->input('to_bill');
                    $LedgerEntries->desc_1 = 'Transfer to other case bill';
                    $LedgerEntries->desc_2 = $type . '_OUT_OTHER_BILL';
                } else if ($request->input('transfer_type') == 'trust') {
                    $LedgerEntries->key_id_2 = $request->input('to_case');
                    $LedgerEntries->key_id_3 = 0;
                    $LedgerEntries->desc_1 = 'Transfer to other case trust';
                    $LedgerEntries->desc_2 = $type . '_OUT_OTHER_TRUST';
                }
            }

            $LedgerEntries->save();

            $LedgerEntries = new LedgerEntries();

            $LedgerEntries->transaction_id = $request->input('trx_id');
            $LedgerEntries->case_id = $id;
            $LedgerEntries->loan_case_main_bill_id = 0;
            $LedgerEntries->user_id = $current_user->id;
            $LedgerEntries->key_id = 0;
            $LedgerEntries->transaction_type = 'D';
            $LedgerEntries->amount = $request->input('transfer_amount');
            $LedgerEntries->bank_id = $request->input('transfer_to');
            $LedgerEntries->remark = $request->input('remark');
            // $LedgerEntries->sys_desc = 'Trust Acc Payment';
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $request->input('transfer_date');
            $LedgerEntries->type = $type . 'IN';
            $LedgerEntries->save();

            $LedgerEntries = new LedgerEntriesV2();

            $LedgerEntries->loan_case_main_bill_id = 0;
            $LedgerEntries->user_id = $current_user->id;
            $LedgerEntries->case_id = $id;
            $LedgerEntries->transaction_type = 'D';
            $LedgerEntries->amount = $request->input('transfer_amount');
            $LedgerEntries->bank_id = $request->input('transfer_to');
            $LedgerEntries->remark = $request->input('remark');
            $LedgerEntries->status = 1;
            $LedgerEntries->created_at = date('Y-m-d H:i:s');
            $LedgerEntries->date = $request->input('transfer_date');
            $LedgerEntries->type = $type . '_IN';
            $LedgerEntries->last_row_entry = 1;
            $LedgerEntries->transaction_id = $request->input('trx_id');

            if ($request->input('transfer_type') != null) {
                if ($request->input('transfer_type') == 'bill') {
                    $LedgerEntries->remark = '[Closed file transfer from ' . $LoanCase->case_ref_no . '] ' . $request->input('remark');
                    $LedgerEntries->case_id = $request->input('to_case');
                    $LedgerEntries->loan_case_main_bill_id = $request->input('to_bill');
                    $LedgerEntries->key_id_2 = $id;
                    $LedgerEntries->type = 'BILL_RECV';
                    $LedgerEntries->desc_1 = 'Transfer from closed file';
                    $LedgerEntries->desc_2 = $type . '_IN';
                    $LedgerEntries->last_row_entry = 0;
                } else if ($request->input('transfer_type') == 'trust') {
                    $LedgerEntries->remark = '[Closed file transfer from ' . $LoanCase->case_ref_no . '] ' . $request->input('remark');
                    $LedgerEntries->case_id = $request->input('to_case');
                    $LedgerEntries->key_id_2 = $id;
                    $LedgerEntries->type = 'TRUST_RECV';
                    $LedgerEntries->desc_1 = 'Transfer from closed file';
                    $LedgerEntries->desc_2 = $type . '_IN';
                    $LedgerEntries->last_row_entry = 0;
                }
            }

            $LedgerEntries->save();

            if ($request->input('transfer_type') != null) {
                if ($request->input('transfer_type') == 'bill') {
                    $this->createBillReceiveReq($LedgerEntries, $request->input('to_bill'));
                } else if ($request->input('transfer_type') == 'trust') {
                    $this->createTrustReceiveReq($LedgerEntries);
                }
            }
        }


        if ($LoanCase) {
            if ($LoanCase->status != 99 || $LoanCase->status != 0) {
                $LegalCloudCaseActivityLog  = new LegalCloudCaseActivityLog();

                $LegalCloudCaseActivityLog->user_id = $current_user->id;
                $LegalCloudCaseActivityLog->case_id = $id;
                $LegalCloudCaseActivityLog->ori_text = '';
                $LegalCloudCaseActivityLog->edit_text = '';
                $LegalCloudCaseActivityLog->action = $type;
                $LegalCloudCaseActivityLog->desc = $current_user->name . ' ' . $action_type . ' file (' . $LoanCase->case_ref_no . ')';
                $LegalCloudCaseActivityLog->status = 1;
                $LegalCloudCaseActivityLog->created_at = date('Y-m-d H:i:s');

                if ($request->input('transfer_type') != null) {
                    if ($request->input('transfer_type') != 'oa') {
                        $OtherLoanCase = LoanCase::where('id', $request->input('to_case'))->first();
                        $LegalCloudCaseActivityLog->desc =  '[' . $current_user->name . ' ' . $action_type . ' file - ' . $LoanCase->case_ref_no . '] and transfered balance to [' . $OtherLoanCase->case_ref_no . ']';
                    }
                }

                $LegalCloudCaseActivityLog->save();

                if ($LoanCase) {

                    $LoanCaseKivNotes = new LoanCaseKivNotes();

                    $LoanCaseKivNotes->case_id =  $id;
                    $LoanCaseKivNotes->notes =  '[' . $current_user->name . ' ' . $action_type . ' file - ' . $LoanCase->case_ref_no . ']';



                    $LoanCaseKivNotes->label =  'case_status';
                    $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
                    $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');
                    $LoanCaseKivNotes->status =  1;
                    $LoanCaseKivNotes->created_by = $current_user->id;
                    $LoanCaseKivNotes->save();
                }
            }

            if ($request->input('close_abort') == 'abort') {
                $LoanCase->abort_date = date('Y-m-d H:i:s');
            } else {
                $LoanCase->close_date = date('Y-m-d H:i:s');
            }

            $LoanCase->save();
        }

        LoanCase::where('id', $id)->update(['status' => $status_type, 'aborted_keep_track' => 1]);

        return response()->json([
            'status' => 1,
            'message' => 'Case ' . $action_type
        ]);
    }

    public function createBillReceiveReq($obj, $loanBillMainId)
    {
        $current_user = auth()->user();
        $Parameter = Parameter::where('parameter_type', '=', 'voucher_running_no')->first();
        $voucher_running_no = (int)$Parameter->parameter_value_1;

        $Parameter->parameter_value_1 = (int)$Parameter->parameter_value_1 + 1;
        $Parameter->save();

        $voucherMain = new VoucherMain();

        $voucherMain->user_id = $current_user->id;
        $voucherMain->case_id = $obj->case_id;
        $voucherMain->payment_type = 3; //back transfer
        $voucherMain->voucher_no = $voucher_running_no;
        $voucherMain->case_bill_main_id = $loanBillMainId;
        $voucherMain->cheque_no = '';
        $voucherMain->credit_card_no = '';
        $voucherMain->bank_id = $obj->bank_id;
        $voucherMain->payee = $obj->payee;
        $voucherMain->transaction_id = $obj->transaction_id;
        $voucherMain->created_by = $current_user->id;
        $voucherMain->bank_account = '';
        $voucherMain->payment_date = $obj->date;
        $voucherMain->remark = $obj->remark;
        $voucherMain->total_amount = $obj->amount;
        $voucherMain->status = 4;
        $voucherMain->voucher_type = 4;
        $voucherMain->lawyer_approval = 1;
        $voucherMain->account_approval = 1;
        $voucherMain->office_account_id = $obj->bank_id;
        $voucherMain->created_at = date('Y-m-d H:i:s');
        $voucherMain->save();

        $obj->key_id = $voucherMain->id;
        $obj->cheque_no = $voucherMain->voucher_no;
        $obj->save();

        $voucherDetails = new VoucherDetails();

        $voucherDetails->voucher_main_id = $voucherMain->id;
        $voucherDetails->user_id = $current_user->id;
        $voucherDetails->case_id = $obj->case_id;
        $voucherDetails->account_details_id = 0;
        $voucherDetails->amount = $obj->amount;
        $voucherDetails->payment_type = 3;
        $voucherDetails->voucher_no = $voucher_running_no;
        $voucherDetails->cheque_no = '';
        $voucherDetails->credit_card_no = '';
        $voucherDetails->bank_id = $obj->bank_id;
        $voucherDetails->bank_account = '';
        $voucherDetails->status = 4;
        $voucherDetails->created_at = date('Y-m-d H:i:s');
        $voucherDetails->save();

        $loanCaseBillMain = LoanCaseBillMain::where('id', $loanBillMainId)->first();

        $loanCaseBillMain->payment_receipt_date = $obj->created_at;;

        $LoanCase = LoanCase::where('id', $obj->case_id)->first();
        CaseController::adminUpdateClientLedger($LoanCase);

        $loanCaseBillMain->save();;

        $this->updateBillandCaseFigure($obj->case_id, $loanBillMainId);
    }

    public function createTrustReceiveReq($obj)
    {
        $status = 1;
        $data = '';

        $current_user = auth()->user();

        $LoanCaseTrustMain = LoanCaseTrustMain::where('case_id', '=', $obj->case_id)->first();

        if ($LoanCaseTrustMain == null) {
            $LoanCaseTrustMain = new LoanCaseTrustMain();
            $LoanCaseTrustMain->case_id =  $obj->case_id;
            $LoanCaseTrustMain->transaction_id = $obj->transaction_id;
            $LoanCaseTrustMain->office_account_id = $obj->bank_id;
            $LoanCaseTrustMain->status =  1;
            $LoanCaseTrustMain->updated_by = $current_user->id;
            $LoanCaseTrustMain->updated_at = date('Y-m-d H:i:s');
            $LoanCaseTrustMain->save();
        }

        // temporary add request voucher header_register_callback==================================================

        $Parameter = Parameter::where('parameter_type', '=', 'voucher_running_no')->first();
        $voucher_running_no = (int)$Parameter->parameter_value_1;

        $Parameter->parameter_value_1 = (int)$Parameter->parameter_value_1 + 1;
        $Parameter->save();

        $voucherMain = new VoucherMain();

        $voucherMain->user_id = $current_user->id;
        $voucherMain->case_id = $obj->case_id;
        $voucherMain->payment_type = 3;
        $voucherMain->voucher_no = $voucher_running_no;
        $voucherMain->cheque_no = '';
        $voucherMain->credit_card_no = '';
        $voucherMain->case_bill_main_id =  0;
        $voucherMain->payment_status =  '';
        $voucherMain->bank_id = $obj->bank_id;
        $voucherMain->payee = '';
        $voucherMain->transaction_id = $obj->transaction_id;
        $voucherMain->created_by = $current_user->id;
        $voucherMain->bank_account = '';
        $voucherMain->payment_date = $obj->date;
        $voucherMain->remark = $obj->remark;
        $voucherMain->total_amount = $obj->amount;
        $voucherMain->status = 3;
        $voucherMain->lawyer_approval = 1;
        $voucherMain->account_approval = 1;
        $voucherMain->voucher_type = 3;
        $voucherMain->office_account_id = $obj->bank_id;
        $voucherMain->created_at = date('Y-m-d H:i:s');
        $voucherMain->save();

        $obj->key_id = $voucherMain->id;
        $obj->cheque_no = $voucherMain->voucher_no;
        $obj->save();

        $voucherDetails = new VoucherDetails();

        $voucherDetails->voucher_main_id = $voucherMain->id;
        $voucherDetails->user_id = $current_user->id;
        $voucherDetails->case_id = $obj->case_id;
        $voucherDetails->account_details_id = 0;
        $voucherDetails->amount = $obj->amount;
        $voucherDetails->payment_type = 3;
        $voucherDetails->voucher_no = $voucher_running_no;
        $voucherDetails->cheque_no = '';
        $voucherDetails->credit_card_no = '';
        $voucherDetails->bank_id = $obj->bank_id;
        $voucherDetails->bank_account = '';
        $voucherDetails->status = 3;
        $voucherDetails->created_at = date('Y-m-d H:i:s');
        $voucherDetails->save();

        VoucherController::reverseTrustDisburse($voucherMain->id);

        $LoanCase = LoanCase::where('id', $obj->case_id)->first();
        CaseController::adminUpdateClientLedger($LoanCase);

        return response()->json(['status' => $status, 'data' => $data]);
    }

    function extractMasterListInfo($LoanCaseBillMain, $type)
    {
        $Customer = null;
        $bill_to = 0;
        $bill_to_type = 0;

        if ($type == 'bill') {
            $bill_to = $LoanCaseBillMain->bill_to;
            $bill_to_type = $LoanCaseBillMain->bill_to_type;
        } else {
            $bill_to = $LoanCaseBillMain->invoice_to;
            $bill_to_type = $LoanCaseBillMain->invoice_to_type;
        }

        if ($bill_to_type == 0) {
            $Customer = Customer::where('name', $bill_to)->first();
        }


        if ($Customer) {
            $LoanCaseBillMain->invoice_to_address = $Customer->address;

            if ($type == 'bill') {
                $LoanCaseBillMain->bill_to_address = $Customer->address;
            } else {
                $LoanCaseBillMain->invoice_to_address = $Customer->address;
            }
        } else {
            $masterlist_field_id = LoanCaseMasterList::where('case_id', $LoanCaseBillMain->case_id)->where('value', $bill_to)->pluck('masterlist_field_id');

            $case_field_id = CaseMasterListField::whereIn('id',  $masterlist_field_id)->pluck('case_field_id');

            $Address = CaseMasterListField::whereIn('case_field_id',  $case_field_id)->where('name', 'Address')->pluck('id');
            $tax_no = CaseMasterListField::whereIn('case_field_id',  $case_field_id)->where('name', 'Income Tax No')->pluck('id');

            $LoanCaseMasterList_address = LoanCaseMasterList::whereIn('masterlist_field_id',  $Address)->where('case_id', $LoanCaseBillMain->case_id)->first();
            $LoanCaseMasterList_tax = LoanCaseMasterList::whereIn('masterlist_field_id',  $tax_no)->where('case_id', $LoanCaseBillMain->case_id)->first();

            // $LoanCaseBillMain->bill_to_address = $masterlist_field_id;

            if ($type == 'bill') {
                if ($LoanCaseMasterList_address) {
                    $LoanCaseBillMain->bill_to_address = $LoanCaseMasterList_address->value;
                }

                if ($LoanCaseMasterList_tax) {
                    $LoanCaseBillMain->bill_to_tax_no = $LoanCaseMasterList_tax->value;
                }
            } else {
                // $bill_to = $LoanCaseBillMain->invoice_to;
                // $bill_to_type = $LoanCaseBillMain->invoice_to_type;

                if ($LoanCaseMasterList_address) {
                    $LoanCaseBillMain->invoice_to_address = $LoanCaseMasterList_address->value;
                }

                if ($LoanCaseMasterList_tax) {
                    $LoanCaseBillMain->invoice_to_tax_no = $LoanCaseMasterList_tax->value;
                }
            }
        }

        return $LoanCaseBillMain;
    }


    public function updateQuotationBillTo(Request $request, $bill_id)
    {

        $LoanCaseBillMain = LoanCaseBillMain::where('id', $bill_id)->first();
        $LoanCaseBillMain->bill_to = $request->input('bill_to');
        $LoanCaseBillMain->bill_to_type = $request->input('bill_to_type');
        $LoanCaseBillMain->save();

        $loanCaseBillMain = $this->extractMasterListInfo($LoanCaseBillMain, 'bill');


        return response()->json([
            'status' => 1,
            'message' => 'Bill To updated',
            'view' => view('dashboard.case.section.d-party-info', compact('LoanCaseBillMain'))->render(),
        ]);
    }

    public function updateInvoiceTo(Request $request, $bill_id)
    {
        $LoanCaseBillMain = LoanCaseBillMain::where('id', $bill_id)->first();
        $LoanCaseBillMain->invoice_to = $request->input('bill_to');
        $LoanCaseBillMain->invoice_to_type = $request->input('bill_to_type');
        $LoanCaseBillMain->save();

        // $LoanCaseBillMain = $this->extractMasterListInfo($LoanCaseBillMain);
        $loanCaseBillMain = $this->extractMasterListInfo($LoanCaseBillMain, 'invoice');

        // LoanCaseBillMain::where('id', $bill_id)->update(['invoice_to' => $request->input('bill_to')]);

        $loanCaseBillMain->bill_to = $loanCaseBillMain->invoice_to;

        return response()->json([
            'status' => 1,
            'message' => 'Invoice To updated',
            'view' => view('dashboard.case.section.d-party-invoice-info', compact('LoanCaseBillMain'))->render(),
        ]);
    }

    public function changeClient(Request $request, $id)
    {

        $case = LoanCase::where('id', $id)->first();
        $previous_client = $case->customer_id;

        if ($case->customer_id == $request->input('client_id')) {
            return response()->json(['status' => 0, 'message' => 'Can\'t change to same client']);
        }

        $case->customer_id = $request->input('client_id');
        $case->save();

        $client = ClientsController::getCustomerDetails($case->customer_id);
        $customer = $client['customer'];
        $ClientOtherLoanCase = $client['ClientOtherLoanCase'];

        $this->updateNewRefNo($case, 0, 0, 0);;

        $case->case_ref_no = $this->updateNewRefNo($case, 0, 0, 0);
        $case->save();

        LogsController::generateLog(
            $param_log = [
                'case_id' => $case->id,
                'object_id' => $previous_client,
                'object_id_2' => $case->customer_id,
                'action' => 'change_client',
                'desc' => ' Change client to [' . $customer->name . ']',
            ]
        );

        return response()->json([
            'status' => 1,
            'message' => 'Client updated',
            'view' => view('dashboard.case.section.d-case-client', compact('case', 'customer', 'ClientOtherLoanCase'))->render(),
            'summary' => $this->viewCaseSummary($id),

        ]);
    }

    public function viewCaseSummary($id)
    {
        $case = LoanCase::where('id', $id)->first();
        $current_user = auth()->user();

        $bank_lo_date = DB::table('loan_case_masterlist as m')
            ->where('m.case_id', '=', $id)
            ->where('m.masterlist_field_id', '=', 398)
            ->first();

        $now = time(); // or your date as well
        $your_date = strtotime($case->created_at);
        $datediff = $now - $your_date;
        $datediff = ($datediff / (60 * 60 * 24));
        $datediff = number_format($datediff);

        $BonusRequestListSent = BonusRequestList::where('case_id', '=', $id)->where('bonus_type', '=', 'CLOSEDCASE')->count();
        $SMPBonusRequestListSent = BonusRequestList::where('case_id', '=', $id)->where('bonus_type', '=', 'SMPSIGNED')->count();

        return  view('dashboard.case.section.d-case-summary', compact('case', 'bank_lo_date', 'datediff', 'BonusRequestListSent', 'SMPBonusRequestListSent', 'current_user'))->render();
    }

    public function changeReferral(Request $request, $id)
    {

        $case = LoanCase::where('id', $id)->first();
        $previous_referral = $case->referral_id;

        if ($case->referral_id == $request->input('referral_id')) {
            return response()->json(['status' => 0, 'message' => 'Can\'t change to same referral_id']);
        }

        $referral = Referral::where('id', $request->input('referral_id'))->first();

        $case->referral_id = $request->input('referral_id');
        $case->referral_name = $referral->name;
        $case->save();

        $lawyer = Users::where('id', $case->lawyer_id)->first();
        $clerk = Users::where('id', $case->clerk_id)->first();
        $sales = Users::where('id', $case->sales_user_id)->first();

        LogsController::generateLog(
            $param_log = [
                'case_id' => $case->id,
                'object_id' => $previous_referral,
                'object_id_2' => $case->referral_id,
                'action' => 'change_referral',
                'desc' => ' Change referral to [' . $case->referral_name . ']',
            ]
        );

        return response()->json([
            'status' => 1,
            'message' => 'Referral updated',
            'view' => view('dashboard.case.section.d-case-team', compact('case', 'referral', 'lawyer', 'clerk', 'sales'))->render(),

        ]);
    }

    public static function getTrustReceiveList($case_id)
    {
        $loan_case_trust_main_receive = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->leftJoin('office_bank_account as o', 'o.id', '=', 'v.office_account_id')
            ->leftJoin('users as u', 'v.user_id', '=', 'u.id')
            ->select('v.*', 'o.name as office_account', 'o.account_no as office_account_no', 'u.name as requestor')
            ->where('v.case_id', $case_id)
            ->where('v.voucher_type', VoucherControllerV2::getVoucherType('TRUST_RECV'))
            ->where('v.status', '<>', 99)
            ->get();

        return $loan_case_trust_main_receive;
    }

    public static function getTrustRequestList($case_id)
    {
        $loan_case_trust_main_dis = DB::table('voucher_main as v')
            ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
            ->leftJoin('office_bank_account as o', 'o.id', '=', 'v.office_account_id')
            ->leftJoin('users as u', 'v.user_id', '=', 'u.id')
            ->select('v.*', 'o.name as office_account', 'o.account_no as office_account_no', 'u.name as requestor')
            ->where('v.case_id', $case_id)
            ->where('v.voucher_type', VoucherControllerV2::getVoucherType('TRUST_DISB'))
            ->where('v.status', '<>', 99)
            ->get();

        // $loan_case_trust_main_dis = DB::table('voucher_main as v')
        // ->leftJoin('voucher_details as vd', 'vd.voucher_main_id', '=', 'v.id')
        // ->leftJoin('users as u', 'v.user_id', '=', 'u.id')
        // ->select('v.*', 'u.name as requestor')
        // ->where('v.case_id', '=', $case_id)
        // ->where('v.voucher_type', '=', VoucherControllerV2::getVoucherType('TRUST_DISB'))
        // ->where('v.status', '<>', 99)
        // ->get();

        return $loan_case_trust_main_dis;
    }

    public function deleteReceiptFile(Request $request)
    {

        $delete_path = $request->input('delete_path');
        if (File::exists(public_path($delete_path))) {
            File::delete(public_path($delete_path));
        }
    }

    public function adminMigrateCaseFile()
    {
        $count = 0;
        File::deleteDirectory(public_path('documents/cases'));

        return 1;

        $disk = Storage::disk('Wasabi');

        $LoanAttachment = LoanCaseAccountFiles::whereBetween('id', [349, 10192])->where('status', 1)->get();



        if (count($LoanAttachment) > 0) {


            for ($j = 0; $j < count($LoanAttachment); $j++) {







                if (($LoanAttachment[$j]->s3_file_name == null || $LoanAttachment[$j]->s3_file_name == '')) {
                    if ($LoanAttachment[$j]->ori_name) {
                        // $CaseTransferLog = CaseTransferLog::where('case_id', $LoanAttachment[$j]->case_id)->get();
                        $CaseTransferLog = LoanCase::where('id', $LoanAttachment[$j]->case_id)->get();
                        // return $CaseTransferLog ;
                        for ($i = 0; $i < count($CaseTransferLog); $i++) { {
                                $folder_name = $CaseTransferLog[$i]->case_ref_no;
                                $folder_name = str_replace('/', '_', $folder_name);
                                $folder_name = str_replace('&', '&amp;', $folder_name);
                                $path = 'documents/cases/' . $folder_name . '/voucher/';

                                // return $path;

                                if (File::exists(public_path($path . $LoanAttachment[$j]->file_name))) {
                                    $filename = '';
                                    $file = file_get_contents($path . $LoanAttachment[$j]->file_name);

                                    $location = 'cases/' . $LoanAttachment[$j]->case_id . '/voucher/' . $LoanAttachment[$j]->file_name;
                                    $s3_file_name =  $disk->put($location, $file);

                                    $LoanAttachment[$j]->s3_file_name = $location;
                                    $LoanAttachment[$j]->is_migrated = 1;
                                    $LoanAttachment[$j]->save();

                                    $count += 1;
                                } else {
                                    $LoanAttachment[$j]->no_file = 1;
                                    $LoanAttachment[$j]->save();
                                }
                            }
                        }
                    }
                }
            }
        }


        // $LoanAttachment = Dispatch::whereBetween('id', [1, 14750])->get();

        // if (count($LoanAttachment) > 0) 
        // {
        //     $path = 'app/documents/dispatch/';

        //     for ($j = 0; $j < count($LoanAttachment); $j++) {

        //         if (($LoanAttachment[$j]->s3_file_name == null || $LoanAttachment[$j]->s3_file_name == '')) {
        //             if ($LoanAttachment[$j]->file_new_name)
        //             {
        //                 if (File::exists(public_path($path.$LoanAttachment[$j]->file_new_name)))
        //                 {
        //                     // $file = File::get(public_path($LoanAttachment[$i]->filename));
        //                     $file = file_get_contents($path.$LoanAttachment[$j]->file_new_name);
        //                     $location = 'dispatch/'.$LoanAttachment[$j]->file_new_name;
        //                     // $file = Storage::disk('public')->get('/'.$LoanAttachment[$i]->filename );
        //                     $s3_file_name =  $disk->put($location, $file);

        //                     // if ($s3_file_name) {
        //                     //     File::delete(public_path($LoanAttachment[$j]->filename));
        //                     // }

        //                     $LoanAttachment[$j]->s3_file_name = $location;
        //                     $LoanAttachment[$j]->is_migrated = 1;
        //                     $LoanAttachment[$j]->save();

        //                     $count += 1;

        //                 }
        //                 else{
        //                     $LoanAttachment[$j]->no_file = 1;
        //                     $LoanAttachment[$j]->save();
        //                 }
        //             }
        //             else{
        //                 $LoanAttachment[$j]->no_file = 1;
        //                 $LoanAttachment[$j]->save();
        //             }


        //         }
        //     }

        // }


        // $LoanAttachment = CHKT::whereBetween('id', [1, 200])->get();

        // if (count($LoanAttachment) > 0) 
        // {
        //     $path = 'app/documents/chkt/';

        //     for ($j = 0; $j < count($LoanAttachment); $j++) {

        //         if (($LoanAttachment[$j]->s3_file_name == null || $LoanAttachment[$j]->s3_file_name == '')) {
        //             if ($LoanAttachment[$j]->file_new_name)
        //             {
        //                 if (File::exists(public_path($path.$LoanAttachment[$j]->file_new_name)))
        //                 {
        //                     // $file = File::get(public_path($LoanAttachment[$i]->filename));
        //                     $file = file_get_contents($path.$LoanAttachment[$j]->file_new_name);
        //                     $location = 'chkt/'.$LoanAttachment[$j]->file_new_name;
        //                     // $file = Storage::disk('public')->get('/'.$LoanAttachment[$i]->filename );
        //                     $s3_file_name =  $disk->put($location, $file);

        //                     // if ($s3_file_name) {
        //                     //     File::delete(public_path($LoanAttachment[$j]->filename));
        //                     // }

        //                     $LoanAttachment[$j]->s3_file_name = $location;
        //                     $LoanAttachment[$j]->is_migrated = 1;
        //                     $LoanAttachment[$j]->save();

        //                 }
        //                 else{
        //                     $LoanAttachment[$j]->no_file = 1;
        //                     $LoanAttachment[$j]->save();
        //                 }
        //             }
        //             else{
        //                 $LoanAttachment[$j]->no_file = 1;
        //                 $LoanAttachment[$j]->save();
        //             }


        //         }
        //     }

        // }

        // $LoanCase = LoanCase::whereBetween('id', [1501, 2000])->get();

        // if (count($LoanCase) > 0) {
        //     $disk = Storage::disk('Wasabi');

        // // cases uploaded file
        // for ($i = 0; $i < count($LoanCase); $i++) {

        //     $LoanAttachment = LoanAttachment::where('case_id', $LoanCase[$i]->id)->where('status','<>',99)->get();


        //     if (count($LoanAttachment) > 0) {


        //         for ($j = 0; $j < count($LoanAttachment); $j++) { {
        //                 $s3_file_name = null;


        //                 if (($LoanAttachment[$j]->s3_file_name == null || $LoanAttachment[$j]->s3_file_name == '')) {
        //                     if (File::exists(public_path($LoanAttachment[$j]->filename)))
        //                     {
        //                         // $file = File::get(public_path($LoanAttachment[$i]->filename));
        //                         $file = file_get_contents($LoanAttachment[$j]->filename);
        //                         $location = 'cases/' . $LoanCase[$i]->id . '/'.$LoanAttachment[$j]->display_name;
        //                         // $file = Storage::disk('public')->get('/'.$LoanAttachment[$i]->filename );
        //                         $s3_file_name =  $disk->put($location, $file);

        //                         // if ($s3_file_name) {
        //                         //     File::delete(public_path($LoanAttachment[$j]->filename));
        //                         // }

        //                         $LoanAttachment[$j]->s3_file_name = $location;
        //                         $LoanAttachment[$j]->is_migrated = 1;
        //                         $LoanAttachment[$j]->save();

        //                     }
        //                     else{
        //                         $LoanAttachment[$j]->no_file = 1;
        //                         $LoanAttachment[$j]->save();
        //                     }


        //                 }
        //             }
        //         }
        //     }
        // }


        // for ($i = 0; $i < count($LoanCase); $i++) {

        //     $LoanAttachment = LoanCaseFiles::where('case_id', $LoanCase[$i]->id)->where('status','<>',99)->get();

        //     if (count($LoanAttachment) > 0) {

        //         $path = 'app/documents/cases/file_case_'.$LoanCase[$i]->id.'/';

        //         for ($j = 0; $j < count($LoanAttachment); $j++) { {
        //                 $s3_file_name = null;


        //                 if (($LoanAttachment[$j]->s3_file_name == null || $LoanAttachment[$j]->s3_file_name == '')) {

        //                     if (File::exists(public_path($path.$LoanAttachment[$j]->name)))
        //                     {

        //                         // $file = File::get(public_path($LoanAttachment[$i]->filename));
        //                         $file = file_get_contents($path.$LoanAttachment[$j]->name);
        //                         $location = 'cases/' . $LoanCase[$i]->id . '/documents/'.$LoanAttachment[$j]->name;
        //                         // $file = Storage::disk('public')->get('/'.$LoanAttachment[$i]->filename );
        //                         $s3_file_name =  $disk->put($location, $file);

        //                         // if ($s3_file_name) {
        //                         //     File::delete(public_path($LoanAttachment[$j]->filename));
        //                         // }

        //                         $LoanAttachment[$j]->s3_file_name = $location;
        //                         $LoanAttachment[$j]->is_migrated = 1;
        //                         $LoanAttachment[$j]->save();

        //                         $count += 1;

        //                     }
        //                     else{
        //                         $LoanAttachment[$j]->no_file = 1;
        //                         $LoanAttachment[$j]->save();
        //                     }


        //                 }
        //             }
        //         }
        //     }
        // }
        // }

        // $file_folder_name_temp = $case_path . 'file_case_' . $id . '/' . $genFileName;


        // $loanCaseFile = new LoanCaseFiles();

        //         $loanCaseFile->case_id = $id;
        //         $loanCaseFile->name = $genFileName;
        //         $loanCaseFile->s3_file_name = $location . '/' . $genFileName;
        //         $loanCaseFile->path = $documentTemplateFile->file_name;
        //         $loanCaseFile->type = 1;
        //         $loanCaseFile->status = 1;
        //         $loanCaseFile->created_at = date('Y-m-d H:i:s');

        return $count;
    }

    public function adminUploadExcelFile(Request $request)
    {
        return $this->adminMigrateCaseFile();


        $the_file = $request->file('excel_file');
        // Storage::disk('Wasabi')->put('', $the_file);
        $disk = Storage::disk('Wasabi');
        $location = 'landoffice';

        $s3_file_name =  $disk->put($location, $the_file);

        // $temporarySignedUrl = Storage::disk('Wasabi')->temporaryUrl("9gRrec82ztUG8so4UF2HtkZPb2ZH9Z9f2jD5E9oE.pdf", now()->addMinutes(10));

        return $s3_file_name;


        //         $disk = Storage::disk('Wasabi');
        //          $file =  $disk->put('', $the_file);

        //         // $file =  $disk->put('', $the_file);
        //         $file =  $disk->putFileAs('Wasabi', $the_file, 'photo.xlsx');



        // $url = $disk->url('case_file_shamini_angel.xlsx');





        return $file;

        $the_file = $request->file('excel_file');
        $data = array();

        $spreadsheet = IOFactory::load($the_file->getRealPath());
        $sheet        = $spreadsheet->getActiveSheet();
        $row_limit    = $sheet->getHighestDataRow();
        $column_limit = $sheet->getHighestDataColumn();
        $row_range    = range(2, $row_limit);
        $column_range = range('F', $column_limit);
        $startcount = 2;


        foreach ($row_range as $row) {

            $ref_no = $sheet->getCell('B' . $row)->getValue();
            $agree_fee = $sheet->getCell('C' . $row)->getValue();
            $targered_collection_amt = $sheet->getCell('D' . $row)->getValue();

            if ($ref_no <> '') {
                $running_no = (int)filter_var($ref_no, FILTER_SANITIZE_NUMBER_INT);
                $LoanCase = LoanCase::where('case_ref_no', 'like', '%' . $running_no . '%')->first();

                if ($LoanCase) {
                    $LoanCase->agreed_fee = $agree_fee;
                    $LoanCase->targeted_collect_amount = $targered_collection_amt;
                    $LoanCase->save();
                    $data[] = [
                        'CustomerName' => $sheet->getCell('B' . $row)->getValue(),
                    ];
                } else {
                    // $data[] = [
                    //     'CustomerName' =>$sheet->getCell( 'B' . $row )->getValue(),
                    // ];
                }
            }

            $startcount++;
        }

        return $data;
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
        // $words .= $unit ?? ' ';
        if ($decnum > 0) {
            $words .= " and ";
            if ($decnum < 20) {
                $words .= $ones[intval($decnum)];
            } elseif ($decnum < 100) {
                $words .= $tens[substr($decnum, 0, 1)];
                if (substr($decnum, 1, 1) != 0) {
                    $words .= " " . $ones[substr($decnum, 1, 1)];
                }
            }
            $words .= $subunit ?? ' SEN';
        }
        return $words;
    }
}

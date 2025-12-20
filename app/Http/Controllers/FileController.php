<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\AccountItem;
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
use App\Models\LoanAttachment;
use App\Models\LoanCaseAccount;
use App\Models\LoanCase;
use App\Models\LoanCaseAccountFiles;
use App\Models\LoanCaseKivNotes;
use App\Models\Parameter;
use App\Models\ReturnCall;
use App\Models\SafeKeeping;
use App\Models\User;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class FileController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $current_user = auth()->user();
        $paidCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 1)->count();
        $exemptedCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 2)->count();
        $pendingCount = DB::table('adjudication')->where('stamp_duty_paid', '=', 0)->count();

        $branchInfo = BranchController::manageBranchAccess();

        $attachment_type = Parameter::where('parameter_type', '=', 'attachment_type')->get();

        $date = Carbon::now()->subDays(7);

        $case_file = DB::table('loan_attachment as a')
            ->join('loan_case as l', 'l.id', '=', 'a.case_id')
            ->join('users as u', 'u.id', '=', 'a.user_id')
            ->where('a.status', '=', 1)
            ->where('a.created_at', '>=', $date)
            ->select('a.*', 'l.case_ref_no', 'u.name as user_name');


        return view('dashboard.files.index', [
            'paidCount' => $paidCount,
            'exemptedCount' => $exemptedCount,
            'pendingCount' => $pendingCount,
            'branches' => $branchInfo['branch'],
            'case_file' => $case_file,
            'attachment_type' => $attachment_type,
            'current_user' => $current_user
        ]);
    }

    public function getFileList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $branchInfo = BranchController::manageBranchAccess();
            $accessInfo = AccessController::manageAccess();

            $case_file = DB::table('loan_attachment as a')
                ->join('loan_case as l', 'l.id', '=', 'a.case_id')
                ->join('users as u', 'u.id', '=', 'a.user_id')
                ->where('a.status', '=', 1)
                ->select(
                    'a.id',
                    'a.s3_file_name',
                    'a.type',
                    'a.receipt_done',
                    'a.display_name',
                    'a.attachment_type',
                    'a.case_id',
                    'a.remark',
                    'a.created_at',
                    'l.case_ref_no',
                    'u.name as user_name'
                );
            // ->select('a.*','l.case_ref_no', 'u.name as user_name');

            //     if (in_array($current_user->menuroles, ['clerk', 'lawyer', 'chambering']))
            //     {
            //         $case_file = $case_file->where(function ($q) use($current_user) {c
            //             $q->where('l.clerk_id', '=', $current_user->id)
            //             ->orWhere('l.lawyer_id', '=', $current_user->id)
            //             ->orWhere('a.created_by', '=', $current_user->id)
            //             ->orWhere('a.branch', '=', $current_user->branch_id);
            //         });
            //     }


            if ($request->input("date_from") <> null && $request->input("date_to") <> null) {

                $case_file = $case_file->where(function ($q) use ($request) {
                    $q->where('a.created_at', '>=', $request->input("date_from"))
                        ->WhereDay('a.created_at', '<=', $request->input("date_to"));
                });

                // $case_file = $case_file->where('a.created_at', '>=', $request->input("date_from"));
            } else {
                if ($request->input("date_from") <> null) {
                    $case_file = $case_file->where('a.created_at', '>=', $request->input("date_from"));
                }

                if ($request->input("date_to") <> null) {
                    $case_file = $case_file->where('a.created_at', '<=', $request->input("date_to"));
                }
            }


            if ($request->input("branch") <> 0) {
                $case_file->where('l.branch_id', '=', $request->input("branch"));
            }

            if ($request->input("receipt_done") <> 99) {
                $case_file->where('a.receipt_done', '=', $request->input("receipt_done"));
            }

            if ($request->input("ref_no") <> '') {
                $case_file->where('l.case_ref_no', 'like', '%' . $request->input("ref_no") . '%');
            }

            if ($request->input("type") <> 99) {
                $case_file->where('a.attachment_type', '=', $request->input("type"));
            }

            if (!in_array($current_user->menuroles, ['admin', 'management', 'account'])) {
                $userList = $accessInfo['user_list'];

                if ($userList) {
                    $case_file = $case_file->where(function ($q) use ($userList, $accessInfo) {
                        $q->whereIn('l.branch_id', $accessInfo['brancAccessList'])
                            ->whereIn('l.lawyer_id', $userList)
                            ->orWhereIn('l.clerk_id', $userList)
                            ->orWhereIn('l.sales_user_id', $userList)
                            ->orWhereIn('l.id', $accessInfo['case_list']);
                    });
                } else {
                    $case_file = $case_file->whereIn('l.branch_id', $accessInfo['brancAccessList']);
                }
            }

            // if (in_array($current_user->menuroles, ['receptionist', 'account', 'sales', 'maker'])) {

            //     // $land_office = $land_office->Where(function ($q) use ($branchInfo) {
            //     //     $q->whereIn('l.branch_id', $branchInfo['brancAccessList']); 
            //     // });

            //     if (in_array($current_user->id, [32, 51,127])) {
            //         $case_file = $case_file->Where(function ($q) use ($branchInfo) {
            //             $q->whereIn('l.branch_id', [5])->orWhere('l.sales_user_id', 32)->where('a.status', '<>', '99');
            //         });
            //     } else  if (in_array($current_user->branch_id, [2])) {
            //         $case_file = $case_file->where('l.sales_user_id', 13);
            //     } else {
            //         $case_file = $case_file->Where(function ($q) use ($branchInfo) {
            //             $q->whereIn('l.branch_id', $branchInfo['brancAccessList'])->where('a.status', '<>', '99');
            //         });
            //     }
            // }

            $case_file = $case_file->orderBy('a.created_at', 'DESC')->get();

            if (in_array($request->input("type"),[9,99])) {
                // voucher attachment
                $case_file2 = DB::table('loan_case_account_files as a')
                    ->join('loan_case as l', 'l.id', '=', 'a.case_id')
                    ->join('users as u', 'u.id', '=', 'a.created_by')
                    ->where('a.status', '=', 1)
                    ->select(
                        'a.id',
                        'a.s3_file_name',
                        'a.ori_name as display_name',
                        'a.type',
                        'a.receipt_done',
                        'a.ori_name',
                        'a.type as attachment_type',
                        'a.case_id',
                        'a.remarks as remark',
                        'a.created_at',
                        'l.case_ref_no',
                        'u.name as user_name'
                    );

                if ($request->input("date_from") <> null && $request->input("date_to") <> null) {

                    $case_file2 = $case_file2->where(function ($q) use ($request) {
                        $q->where('a.created_at', '>=', $request->input("date_from"))
                            ->WhereDay('a.created_at', '<=', $request->input("date_to"));
                    });

                    // $case_file = $case_file->where('a.created_at', '>=', $request->input("date_from"));
                } else {
                    if ($request->input("date_from") <> null) {
                        $case_file2 = $case_file2->where('a.created_at', '>=', $request->input("date_from"));
                    }

                    if ($request->input("date_to") <> null) {
                        $case_file2 = $case_file2->where('a.created_at', '<=', $request->input("date_to"));
                    }
                }


                if ($request->input("branch") <> 0) {
                    $case_file2->where('l.branch_id', '=', $request->input("branch"));
                }

                if ($request->input("receipt_done") <> 99) {
                    $case_file2->where('a.receipt_done', '=', $request->input("receipt_done"));
                }

                if ($request->input("ref_no") <> '') {
                    $case_file2->where('l.case_ref_no', 'like', '%' . $request->input("ref_no") . '%');
                }

                if (!in_array($current_user->menuroles, ['admin', 'management', 'account'])) {
                    $userList = $accessInfo['user_list'];
    
                    if ($userList) {
                        $case_file2 = $case_file2->where(function ($q) use ($userList, $accessInfo) {
                            $q->whereIn('l.branch_id', $accessInfo['brancAccessList'])
                                ->whereIn('l.lawyer_id', $userList)
                                ->orWhereIn('l.clerk_id', $userList)
                                ->orWhereIn('l.sales_user_id', $userList)
                                ->orWhereIn('l.id', $accessInfo['case_list']);
                        });
                    } else {
                        $case_file2 = $case_file2->whereIn('l.branch_id', $accessInfo['brancAccessList']);
                    }
                }


                // if (in_array($current_user->menuroles, ['receptionist', 'account', 'sales', 'maker'])) {

                //     // $land_office = $land_office->Where(function ($q) use ($branchInfo) {
                //     //     $q->whereIn('l.branch_id', $branchInfo['brancAccessList']);
                //     // });

                //     if (in_array($current_user->id, [32, 51,127])) {
                //         $case_file2 = $case_file2->Where(function ($q) use ($branchInfo) {
                //             $q->whereIn('l.branch_id', [5])->orWhere('l.sales_user_id', 32)->where('a.status', '<>', '99');
                //         });
                //     } else  if (in_array($current_user->branch_id, [2])) {
                //         $case_file2 = $case_file2->where('l.sales_user_id', 13);
                //     } else {
                //         $case_file2 = $case_file2->Where(function ($q) use ($branchInfo) {
                //             $q->whereIn('l.branch_id', $branchInfo['brancAccessList'])->where('a.status', '<>', '99');
                //         });
                //     }
                // }

                $case_file2 = $case_file2->orderBy('a.created_at', 'DESC')->get();

                $TeamPortfolios = $case_file->merge($case_file2)->sortByDesc('created_at');;
            }
            else
            {
                $TeamPortfolios = $case_file;
            }

            

            // $TeamPortfolios->SortByDesc('created_at');

            return DataTables::of($TeamPortfolios)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = ' <a target="_blank" href="/return-call/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>';

                    if (!in_array($row->attachment_type, [1, 2, 3, 4, 5, 6, 7, 8])) {
                        $actionBtn = '';
                    } else {
                        $actionBtn = ' <a class="btn btn-info shadow sharp mr-1" href="javascript:void(0)" data-backdrop="static" data-keyboard="false"  onclick="getFileID(' . $row->id . ',' . $row->attachment_type . ')" data-toggle="modal" data-target="#fileTypeModal"><i class="cil-transfer"></i>Update Type</a>';
                    }
                    return $actionBtn;
                })
                ->editColumn('display_name', function ($row) {

                    if ($row->s3_file_name) {
                        return '<a  href="javascript:void(0)" onclick="openFileFromS3(\'' . $row->s3_file_name . '\')"><i class="cil-paperclip"></i> ' . $row->display_name . '</a>';
                    } else {
                        return '<a target="_blank" href="/' . $row->filename . '"><i class="cil-paperclip"></i> ' . $row->display_name . '</a>';
                    }
                })
                ->addColumn('type', function ($row) {
                    if ($row->attachment_type == 1) {
                        return '<span  class=" badge badge-pill badge-warning">Correspondences</span>';
                    } else if ($row->attachment_type == 2) {
                        return '<span class=" badge badge-pill badge-info">Documents</span>';
                    } else if ($row->attachment_type == 3) {
                        return '<span class=" badge badge-pill badge-success">Account Receipt</span>';
                    } else if ($row->attachment_type == 4) {
                        return '<span class=" badge badge-pill badge-danger">Adjudicate</span>';
                    } else if ($row->attachment_type == 5) {
                        return '<span class=" badge badge-pill bg-question">Marketing</span>';
                    } else if ($row->attachment_type == 6) {
                        return '<span class=" badge badge-pill bg-question">Official Receipt</span>';
                    } else if ($row->attachment_type == 7) {
                        return '<span class=" badge badge-pill bg-red">Other Receipt</span>';
                    } else if ($row->attachment_type == 8) {
                        return '<span class=" badge badge-pill bg-light-blue">Presentation Receipt</span>';
                    } else if ($row->attachment_type == 9) {
                        return '<span class=" badge badge-pill " style="background-color: rgb(0, 255, 55)">Checklist document</span>';
                    } else {
                        return '<span class=" badge badge-pill bg-light-blue">Payment Voucher</span>';

                        
                    }
                })
                ->editColumn('case_ref_no', function ($row) {
                    if ($row->case_id != 0) {
                        $actionBtn = ' <a targer="_blank" href="/case/' . $row->case_id . '" class="  " >' . $row->case_ref_no . ' >> </a>';
                    } else {
                        $actionBtn = $row->case_ref;
                    }

                    return $actionBtn;
                })
                ->editColumn('receipt_done', function ($row) {

                    $checked = '';
                    $fileType = '';

                    if (in_array($row->attachment_type, [1, 2, 3, 4, 5, 6, 7, 8])) {
                        $fileType = $row->attachment_type;
                    } else {
                        $fileType = 'V';
                    }

                    if ($row->receipt_done == 1) {
                        $checked = 'checked disabled';
                    }
                    $actionBtn = '<div class="checkbox  bulk-edit-mode">
                        <input type="checkbox" name="file" value="' . $row->id . '" id="chk_' . $row->id . '" ' . $checked . '>
                        
                        <label for="chk_' . $row->id . '"></label>
                        <input type="hidden" value="' . $fileType . '" id="filetype_' . $row->id . '" />
                        </div>';

                    return $actionBtn;
                })
                ->rawColumns(['action', 'type', 'case_ref_no', 'display_name', 'receipt_done'])
                ->make(true);
        }
    }

    public function updateReceiptDone(Request $request)
    {
        if ($request->input('file_list') != null) {
            $file_list = json_decode($request->input('file_list'), true);
        }


        $file_type_list = json_decode($request->input('file_type_list'), true);

        if (count($file_list) > 0) {
            for ($i = 0; $i < count($file_list); $i++) {

                if ($file_type_list[$i]['type'] == 'V') {
                    LoanCaseAccountFiles::where('id', '=', $file_list[$i]['id'])->update(['receipt_done' => 1, 'receipt_done_date' => date('Y-m-d H:i:s')]);
                } else {
                    LoanAttachment::where('id', '=', $file_list[$i]['id'])->update(['receipt_done' => 1, 'receipt_done_date' => date('Y-m-d H:i:s')]);
                }
            }
        }

        return response()->json(['status' => 1, 'data' => 'Success']);
    }

    public function updateFileType(Request $request)
    {

        LoanAttachment::where('id', '=', $request->input('fileID'))->update(['attachment_type' => $request->input('type_id')]);


        // $LoanAttachment = LoanAttachment::where('id', '=', $request->input('fileID'))->first();


        // $LoanAttachment->attachment_type = $request->input('type_id');
        // $LoanAttachment->save();

        return response()->json(['status' => 1, 'data' => 'Success']);
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
            ->where('l.status', '<>', 99)
            ->get();

        $branchInfo = BranchController::manageBranchAccess();

        return view('dashboard.return-call.create', [
            'loan_case' => $loan_case,
            'branch' => $branchInfo['branch'],
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

        $ReturnCall  = new ReturnCall();

        $running_no = (int)filter_var($request->input('case_ref_no'), FILTER_SANITIZE_NUMBER_INT);

        if (LoanCase::where('case_ref_no', 'like', '%' . $running_no . '%')->count() > 0) {
            $case_id = $request->input('case_id');
        }


        $ReturnCall->case_id = $case_id;
        $ReturnCall->case_ref = $request->input('case_ref_no');
        $ReturnCall->client_id = 0;
        $ReturnCall->client_name = $request->input('client');
        $ReturnCall->contact_no = $request->input('contact_no');
        $ReturnCall->enquiry = $request->input('enquiry');
        $ReturnCall->attention = $request->input('attention');
        $ReturnCall->return_call = $request->input('return_call');


        if ($request->input('return_call') == 1) {
            $ReturnCall->return_call_time = date('Y-m-d H:i:s');
        }

        $ReturnCall->created_by = $current_user->id;
        $ReturnCall->branch = $request->input('branch');
        $ReturnCall->remark = $request->input('remark');
        $ReturnCall->status = 1;
        $ReturnCall->created_at = date('Y-m-d H:i:s');
        $ReturnCall->save();

        $status_span = '';

        if ($ReturnCall->return_call == '1') {
            $status_span = '<span class="label bg-success">Yes</span>';
        } else {
            $status_span = '<span class="label bg-warning">Pending</span>';
        }

        $message = '
        <a href="/return-call/' . $ReturnCall->id . '/edit" target="_blank">[Created&nbsp;<b>Return Call</b> record]</a><br />
        <strong>Attention</strong>:&nbsp;' . $request->input('attention') . '<br />
        <strong>Contact No</strong>:&nbsp;' . $request->input('contact_no') . '<br />
        <strong>Enquiry</strong>:&nbsp;' . $request->input('enquiry') . '<br />
        <strong>Remark</strong>:&nbsp;' . $request->input('remark') . '<br />
        <strong>Return Call</strong>:&nbsp;' . $status_span;

        $LoanCaseKivNotes = new LoanCaseKivNotes();

        $LoanCaseKivNotes->case_id =  $case_id;
        $LoanCaseKivNotes->notes =  $message;
        $LoanCaseKivNotes->label =  'operation|returncall';
        $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
        $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');

        $LoanCaseKivNotes->status =  1;
        $LoanCaseKivNotes->object_id_1 =  $ReturnCall->id;
        $LoanCaseKivNotes->created_by = $current_user->id;
        $LoanCaseKivNotes->save();

        $request->session()->flash('message', 'Successfully created new record');
        return redirect()->route('return-call.index');
    }

    public function edit($id)
    {
        $Adjudication = DB::table('return_call as a')
            ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
            ->join('users as u', 'u.id', '=', 'a.created_by')
            ->select('a.*', 'l.case_ref_no', 'u.name as assign_by')
            ->where('a.id', '=', $id)
            ->first();

        $loan_case = DB::table('loan_case as l')
            ->join('client as c', 'c.id', '=', 'l.customer_id')
            ->select('l.*', 'c.name')
            ->where('l.status', '<>', 99)
            ->get();

        $branchInfo = BranchController::manageBranchAccess();

        return view('dashboard.return-call.edit', [
            'loan_case' => $loan_case,
            'branch' => $branchInfo['branch'],
            'main_obj' => $Adjudication
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        // $caseTemplateDetail = CaseTemplateDetails::all();

        $docTemplateDetailSelected = DocumentTemplateDetails::where('document_template_main_id', '=', $id)->where('status', '=', 1)->get();
        $caseMasterListCategory = CaseMasterListCategory::all();
        $caseMasterListField = CaseMasterListField::all();

        $docTemplatePages = DB::table('document_template_pages')
            ->leftJoin('users', 'users.id', '=', 'document_template_pages.is_locked')
            ->select('document_template_pages.*', 'users.name')
            ->get();

        $current_user = auth()->user();

        // $docTemplatePage = DocumentTemplatePages::where('document_template_details_id', '=', $docTemplateDetailSelected[0]->id)->get();
        $docTemplateDetail = DocumentTemplateDetails::where('document_template_main_id', '=', $id)->get();
        $docTemplateMain = DocumentTemplateMain::where('id', '=', $id)->get();
        return view('dashboard.documentTemplate.show', [
            'template' => DocumentTemplateMain::where('id', '=', $id)->first(),
            'docTemplatePages' => $docTemplatePages,
            'docTemplateDetail' => $docTemplateDetail,
            'docTemplateMain' => $docTemplateMain,
            'caseMasterListField' => $caseMasterListField,
            'caseMasterListCategory' => $caseMasterListCategory
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
        $ReturnCall = ReturnCall::where('id', '=', $id)->first();

        $case_id = 0;

        if (LoanCase::where('case_ref_no', '=', $request->input('case_ref_no'))->count() > 0) {
            $case_id = $request->input('case_id');
        }


        if ($ReturnCall) {

            $ReturnCall->case_id = $case_id;
            $ReturnCall->case_ref = $request->input('case_ref_no');
            $ReturnCall->client_id = 0;
            $ReturnCall->client_name = $request->input('client');
            $ReturnCall->contact_no = $request->input('contact_no');
            $ReturnCall->enquiry = $request->input('enquiry');
            $ReturnCall->attention = $request->input('attention');
            $ReturnCall->return_call = $request->input('return_call');


            if ($request->input('return_call') == 1) {
                $ReturnCall->return_call_time = date('Y-m-d H:i:s');
            }

            $ReturnCall->created_by = $current_user->id;
            $ReturnCall->branch = $request->input('branch');
            $ReturnCall->remark = $request->input('remark');
            $ReturnCall->updated_at = date('Y-m-d H:i:s');
            $ReturnCall->save();

            $LoanCaseKivNotes = LoanCaseKivNotes::where('object_id_1', '=', $id)->where('label', '=', 'operation|returncall')->first();

            if ($LoanCaseKivNotes) {
                $status_span = '';

                if ($ReturnCall->return_call == '1') {
                    $status_span = '<span class="label bg-success">Yes</span>';
                } else {
                    $status_span = '<span class="label bg-warning">Pending</span>';
                }

                $message = '
                <a href="/return-call/' . $ReturnCall->id . '/edit" target="_blank">[Created&nbsp;<b>Return Call</b> record]</a><br />
                <strong>Attention</strong>:&nbsp;' . $request->input('attention') . '<br />
                <strong>Contact No</strong>:&nbsp;' . $request->input('contact_no') . '<br />
                <strong>Enquiry</strong>:&nbsp;' . $request->input('enquiry') . '<br />
                <strong>Remark</strong>:&nbsp;' . $request->input('remark') . '<br />
                <strong>Return Call</strong>:&nbsp;' . $status_span;

                $LoanCaseKivNotes->notes =  $message;
                $LoanCaseKivNotes->updated_at = date('Y-m-d H:i:s');
                $LoanCaseKivNotes->updated_by = $current_user->id;
                $LoanCaseKivNotes->save();
            }
        }

        $request->session()->flash('message', 'Successfully updated record');
        return redirect()->route('return-call.index');
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

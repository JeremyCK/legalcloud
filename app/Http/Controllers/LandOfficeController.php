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
use App\Models\LandOffice;
use App\Models\LoanCaseAccount;
use App\Models\LoanCase;
use App\Models\LoanCaseKivNotes;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class LandOfficeController extends Controller
{

    public function getOperationCode()
    {
        return config('global.operation.land_office');
    }

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

        $branch = DB::table('branch')->where('status', '=', 1)->get();


        return view('dashboard.land-office.index', [
            'paidCount' => $paidCount,
            'exemptedCount' => $exemptedCount,
            'pendingCount' => $pendingCount,
            'branches' => $branch,
            'current_user' => $current_user
        ]);
    }

    public function getLandOfficeList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $branchInfo = BranchController::manageBranchAccess();

            $land_office = DB::table('land_office as a')
                ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
                ->join('users as u', 'u.id', '=', 'a.created_by')
                ->select('a.*', 'u.name as assign_by', 'l.case_ref_no')
                ->where('a.status', '<>', 99);

                // if ($current_user->menuroles == 'sales') {
                //     $land_office = $land_office->where('l.sales_user_id', '=', $current_user->id);
                // } elseif ($current_user->menuroles == 'clerk' || $current_user->menuroles == 'lawyer') {
                //     $land_office = $land_office->where('l.' . $current_user->menuroles . '_id', '=', $current_user->id);
                // }

                // if ($current_user->menuroles == 'sales') {
                //     $land_office = $land_office->where(function ($q) use($current_user)  {
                //         $q->where('l.sales_user_id', '=', $current_user->id)
                //         ->orWhere('a.created_by', '=', $current_user->id);
                //     });
                // } elseif ($current_user->menuroles == 'clerk' || $current_user->menuroles == 'lawyer') {
                //     $land_office = $land_office->where(function ($q) use($current_user)  {
                //         $q->where('l.' . $current_user->menuroles . '_id', '=', $current_user->id)
                //         ->orWhere('a.created_by', '=', $current_user->id);
                //     });
                // }

                if (in_array($current_user->menuroles, ['clerk', 'lawyer', 'chambering']))
                {
                    $land_office = $land_office->where(function ($q) use($current_user) {
                        $q->where('l.clerk_id', '=', $current_user->id)
                        ->orWhere('l.lawyer_id', '=', $current_user->id)
                        ->orWhere('a.created_by', '=', $current_user->id)
                        ->orWhere('a.branch', '=', $current_user->branch_id);
                    });
                }

            if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
                // $land_office = $land_office->whereBetween('a.created_at', [$request->input("date_from"), $request->input("date_to")]);

                $land_office = $land_office->whereDate('a.created_at', '>=', $request->input("date_from"))
                ->whereDate('a.created_at', '<=', $request->input("date_to"));
            } else {
                if ($request->input("date_from") <> null) {
                    $land_office = $land_office->where('a.created_at', '>=', $request->input("date_from"));
                }

                if ($request->input("date_to") <> null) {
                    $land_office = $land_office->where('a.created_at', '<=', $request->input("date_to"));
                }
            }

            if ($request->input("status") <> 99) {
                $land_office->where('a.received', '=', $request->input("status"));
            }

            if ($request->input("branch") <> 0) {
                $land_office->where('a.branch', '=', $request->input("branch"));
            }

            if (in_array($current_user->menuroles, ['receptionist','account','sales','maker']))
            {

                // $land_office = $land_office->Where(function ($q) use ($branchInfo) {
                //     $q->whereIn('l.branch_id', $branchInfo['brancAccessList']);
                // });

                $land_office = $land_office->Where(function ($q) use ($branchInfo) {
                    $q->whereIn('a.branch', $branchInfo['brancAccessList'])->where('a.status', '<>','99');
                });
            }

            $land_office = $land_office->orderBy('a.created_at', 'DESC')->get();

            return DataTables::of($land_office)
                ->addIndexColumn()
                ->addColumn('action', function ($row) use($current_user) {
                    $actionBtn = ' <a target="_blank" href="/land-office/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>';
                    
                    $actionBtn = '
                    <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                    <i class="cil-settings"></i>
                    </button>
                    <div class="dropdown-menu" style="padding:0">
                      <a class="dropdown-item btn-info" target="_blank" href="/land-office/' . $row->id . '/edit"  style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-pencil"></i>Edit</a>
                      ';

                     if ($row->created_by == $current_user->id && $row->received <> 1)
                     {
                        $actionBtn .= '<div class="dropdown-divider" style="margin:0"></div>
                        <a class="dropdown-item btn-danger" href="javascript:void(0)" onclick="deleteOperation(' . $row->id . ', \'LAND\')"  style="color:white;margin:0"><i style="margin-right: 10px;"  class="cil-x"></i>Delete</a>
                       ';
                     }
                    
                    return $actionBtn;
                })
                ->editColumn('received', function ($data) {
                    if ($data->received == '1')
                        return '<span class="label bg-success">Received</span>';
                    else
                        return '<span class="label bg-warning">Pending</span>';
                })
                ->editColumn('case_ref_no', function ($row) {
                    if ($row->case_id != 0) {
                        $actionBtn = ' <a href="/case/' . $row->case_id . '" class="  " >' . $row->case_ref_no . ' >> </a>';
                    } else {
                        $actionBtn = $row->case_ref;
                    }
                    // $actionBtn = ' <a href="/case/'. $row->case_id . '" class="  " >'. $row->case_ref_no . ' </a>';

                    return $actionBtn;
                })
                ->rawColumns(['action', 'received', 'case_ref_no'])
                ->make(true);
        }
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
        $branchInfo = BranchController::manageBranchAccess();
        $loan_case = CaseController::getCaseListHub();
        $operation = $this->getOperationCode();

        return view('dashboard.land-office.create', [
            'loan_case' => $loan_case,
            'branch' => $branchInfo['branch'],
            'operation' => $operation,
        ]);
    }

    public function store(Request $request)
    {

        return $request->file('attachment_file');

        $case_id = 0;

        $current_user = auth()->user();

        $LandOffice  = new LandOffice();

        $running_no = (int)filter_var($request->input('case_ref_no'), FILTER_SANITIZE_NUMBER_INT);

        if (LoanCase::where('case_ref_no', 'like', '%'.$running_no.'%')->count() > 0) {
            $case_id = $request->input('case_id');
        }


        $LandOffice->case_id = $case_id;
        $LandOffice->case_ref = $request->input('case_ref_no');
        $LandOffice->client_id = 0;
        $LandOffice->client_name = $request->input('client');
        $LandOffice->land_office = $request->input('land_office');
        $LandOffice->matter = $request->input('matter');
        $LandOffice->smartbox_no = $request->input('smartbox_no');
        $LandOffice->receipt_no = $request->input('receipt_no');
        $LandOffice->received = $request->input('received');

        if ($request->input('received') == 1) {
            $LandOffice->received_on = date('Y-m-d H:i:s');
        }

        $LandOffice->created_by = $current_user->id;
        $LandOffice->branch = $request->input('branch');
        $LandOffice->remark = $request->input('remark');
        $LandOffice->status = 1;
        $LandOffice->created_at = date('Y-m-d H:i:s');
        $LandOffice->save();

        $file = $request->file('attachment_file');

        $disk = Storage::disk('Wasabi');
        $s3_file_name = '';

        if ($file) {
            $oriFilename = $file->getClientOriginalName();
            $filename = time() . '_' . $file->getClientOriginalName();

            // $location = 'app/documents/landoffice/';
            $location = 'landoffice';

            $isImage =  ImageController::verifyImage($file);

            if ($isImage == true) {
                $s3_file_name = ImageController::resizeImg($file, $location, $filename);
            } else {
                $s3_file_name =  $disk->put($location, $file);
            }

            $LandOffice->file_ori_name = $oriFilename;
            $LandOffice->file_new_name = $s3_file_name;
            $LandOffice->s3_file_name = $s3_file_name;
            $LandOffice->save();
        }

        $status_span = '';

        if ($LandOffice->received == '1') {
            $status_span = '<span class="label bg-success">Received</span>';
        } else {
            $status_span = '<span class="label bg-warning">Pending</span>';
        }

        $message = '
        <a href="/land-office/' . $LandOffice->id . '/edit" target="_blank">[Created&nbsp;<b>Land Office</b> record]</a><br />
        <strong>Land Office</strong>:&nbsp;' . $request->input('land_office') . '<br />
        <strong>Smartbox No</strong>:&nbsp;' . $request->input('smartbox_no') . '<br />
        <strong>Receipt No</strong>:&nbsp;' . $request->input('receipt_no') . '<br />
        <strong>Matter</strong>:&nbsp;' . $request->input('matter') . '<br />
        <strong>Attachment</strong>:&nbsp;<a  href="javascript:void(0)" onclick="openFileFromS3(\'' . $LandOffice->s3_file_name . '\')"  class="mailbox-attachment-name "><i class="fa fa-paperclip"></i>' . $LandOffice->file_ori_name . '</a><br />
        <strong>Done</strong>:&nbsp;' . $status_span;

        $LoanCaseKivNotes = new LoanCaseKivNotes();

        $LoanCaseKivNotes->case_id =  $case_id;
        $LoanCaseKivNotes->notes =  $message;
        $LoanCaseKivNotes->label =  'operation|landoffice';
        $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
        $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');

        $LoanCaseKivNotes->status =  1;
        $LoanCaseKivNotes->object_id_1 =  $LandOffice->id;
        $LoanCaseKivNotes->created_by = $current_user->id;
        $LoanCaseKivNotes->save();

        $request->session()->flash('message', 'Successfully created new Land Office');
        return redirect()->route('land-office.index');
    }

    public function edit($id)
    {
        $land_office = DB::table('land_office as a')
            ->leftJoin('loan_case as l', 'l.id', '=', 'a.case_id')
            // ->join('client as c', 'c.id', '=', 'a.client_id')
            ->join('users as u', 'u.id', '=', 'a.created_by')
            ->select('a.*', 'l.case_ref_no', 'u.name as assign_by')
            ->where('a.id', '=', $id)
            ->first();

        $Attachment = DB::table('operation_attachment as l')
            ->join('users as c', 'c.id', '=', 'l.created_by')
            ->select('l.file_ori_name','l.file_new_name', 's3_file_name','l.created_at', 'c.name as upload_by','l.id')
            ->where('l.status', '<>', 99)
            ->where('l.key_id', $id)
            ->get();

        $Attachment2 = DB::table('land_office as l')
            ->join('users as c', 'c.id', '=', 'l.created_by')
            ->select('l.file_ori_name','l.file_new_name', 's3_file_name','l.created_at', 'c.name as upload_by','l.id')
            ->where('l.status', '<>', 99)
            ->where('l.id', $id)
            ->get();

        if($Attachment2[0]->file_ori_name)
        {
            $Attachment = $Attachment->merge($Attachment2);
        }

        $operation = $this->getOperationCode();
        $branchInfo = BranchController::manageBranchAccess();
        $loan_case = CaseController::getCaseListHub();

        return view('dashboard.land-office.edit', [
            'loan_case' => $loan_case,
            'branch' => $branchInfo['branch'],
            'land_office' => $land_office,
            'operation' => $operation,
            'Attachment' => $Attachment
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
        $LandOffice = LandOffice::where('id', '=', $id)->first();

        $case_id = 0;

        if (LoanCase::where('case_ref_no', '=', $request->input('case_ref_no'))->count() > 0) {
            $case_id = $request->input('case_id');
        }


        if ($LandOffice) {
            $LandOffice->case_id = $case_id;
            $LandOffice->case_ref = $request->input('case_ref_no');
            $LandOffice->client_id = 0;
            $LandOffice->client_name = $request->input('client');
            $LandOffice->land_office = $request->input('land_office');
            $LandOffice->matter = $request->input('matter');
            $LandOffice->smartbox_no = $request->input('smartbox_no');
            $LandOffice->receipt_no = $request->input('receipt_no');
            $LandOffice->received = $request->input('received');

            if ($request->input('received') == 1) {
                $LandOffice->received_on = date('Y-m-d H:i:s');
            }

            $LandOffice->created_by = $current_user->id;
            $LandOffice->branch = $request->input('branch');
            $LandOffice->remark = $request->input('remark');
            $LandOffice->status = 1;
            $LandOffice->created_at = date('Y-m-d H:i:s');
            $LandOffice->save();

            $file = $request->file('attachment_file');

            if ($file) {

                $delete_path = 'app/documents/landoffice/' . $LandOffice->file_new_name;
                
                if ($LandOffice->s3_file_name)
                {
                    if(Storage::disk('Wasabi')->exists($LandOffice->s3_file_name)) {
                        Storage::disk('Wasabi')->delete($LandOffice->s3_file_name);
                    }
                }
                else
                {
                    if (File::exists(public_path($delete_path))) {
                        File::delete(public_path($delete_path));
                    }
                }

                $disk = Storage::disk('Wasabi');
                $s3_file_name = '';

                $oriFilename = $file->getClientOriginalName();
                $filename = time() . '_' . $file->getClientOriginalName();

                // $location = 'app/documents/landoffice/';
                $location = 'landoffice';

                $isImage =  ImageController::verifyImage($file);

                if ($isImage == true) {
                    $s3_file_name = ImageController::resizeImg($file, $location, $filename);
                } else {
                    // $file->move($location, $filename);
                    // $filepath = url($location . $filename);
    
                    $s3_file_name =  $disk->put($location, $file);
                }

                // Upload file
                // $file->move($location, $filename);


                $LandOffice->file_ori_name = $oriFilename;
                $LandOffice->file_new_name = $s3_file_name;
                $LandOffice->s3_file_name = $s3_file_name;
                $LandOffice->save();
            }

            $LoanCaseKivNotes = LoanCaseKivNotes::where('object_id_1', '=', $id)->where('label', '=', 'operation|landoffice')->first();

            if ($LoanCaseKivNotes) {
                $status_span = '';

                $status_span = '';

                if ($LandOffice->received == '1') {
                    $status_span = '<span class="label bg-success">Received</span>';
                } else {
                    $status_span = '<span class="label bg-warning">Pending</span>';
                }

                $message = '
                <a href="/land-office/' . $LandOffice->id . '/edit" target="_blank">[Created&nbsp;<b>Land Office</b> record]</a><br />
                <strong>Land Office</strong>:&nbsp;' . $request->input('land_office') . '<br />
                <strong>Smartbox No</strong>:&nbsp;' . $request->input('smartbox_no') . '<br />
                <strong>Receipt No</strong>:&nbsp;' . $request->input('receipt_no') . '<br />
                <strong>Matter</strong>:&nbsp;' . $request->input('matter') . '<br />
                <strong>Attachment</strong>:&nbsp;<a href="javascript:void(0)" onclick="openFileFromS3(\'' . $LandOffice->s3_file_name . '\')" class="mailbox-attachment-name "><i class="fa fa-paperclip"></i>' . $LandOffice->file_ori_name . '</a><br />
                <strong>Done</strong>:&nbsp;' . $status_span;

                $LoanCaseKivNotes->notes =  $message;
                $LoanCaseKivNotes->updated_at = date('Y-m-d H:i:s');
                $LoanCaseKivNotes->updated_by = $current_user->id;
                $LoanCaseKivNotes->save();
            }
        }

        $request->session()->flash('message', 'Successfully update land office');
        return redirect()->route('land-office.index');
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

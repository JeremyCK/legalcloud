<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\EmailTemplateMain;
use App\Models\DocumentTemplateMain;
use App\Models\DocumentTemplateDetails;
use App\Models\DocumentTemplatePages;
use App\Models\caseTemplate;
use App\Models\Roles;
use App\Models\AuditLog;
use App\Models\EmailTemplateDetails;
use Illuminate\Http\Request;
use App\Models\MenusLang;
use App\Models\Users;
use Illuminate\Support\Facades\DB;


class ActivityLogController extends Controller
{
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $hierarchy = AuditLog::all()
        // ->orderBy('created_at', 'desc')->get();

        $activityLog = ActivityLog::orderBy('created_at', 'desc')->get();

        $activityLog = DB::table('activity_log AS a')
        ->leftJoin('users AS u', 'u.id', '=', 'a.user_id')
        ->leftJoin('loan_case AS l', 'l.id', '=', 'a.case_id')
        ->select('a.*', 'u.name AS user_name', 'l.case_ref_no')
        ->get();

        return view('dashboard.activityLog.index', ['activityLog' => $activityLog]);
    }

    public function logAction()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Roles::where('status', '=', '1')->get();

        return view('dashboard.documentTemplate.create', [
            'templates' => CaseTemplate::all(),
            'roles' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function store(Request $request)
    {

        $templateEmail  = new EmailTemplateMain();

        $templateEmail->name = $request->input('name');
        $templateEmail->desc = $request->input('desc');
        $templateEmail->code = $request->input('code');
        $templateEmail->subject = $request->input('subject');
        $templateEmail->status =  $request->input('status');
        // $templateEmail->content = $request->input('summary-ckeditor');

        $templateEmail->save();

        
        if ($templateEmail->id != null && $templateEmail->id != '') {
            $templateEmailDetails  = new EmailTemplateDetails();

            $templateEmailDetails->email_template_id = $templateEmail->id;
            $templateEmailDetails->version_name = 'Orinal';
            $templateEmailDetails->content = $request->input('summary-ckeditor');
            $templateEmailDetails->status = $request->input('status');

            $templateEmailDetails->save();
        }



        // $lastId = DB::getPdo()->lastInsertId();

        $request->session()->flash('message', 'Successfully created template');
        return redirect()->route('email-template.index');

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
        // return redirect()->route('todolist.create');
    }

    public static function storeActivityLog($param)
    {
        
        $current_user = auth()->user();

        $activityLog  = new ActivityLog();

        $activityLog->user_id = $current_user->id;
        $activityLog->case_id = $param['case_id'];
        $activityLog->checklist_id = $param['checklist_id'];
        $activityLog->action = $param['action'];
        $activityLog->desc =  $param['desc'];
        $activityLog->status = 1;
        $activityLog->created_at = date('Y-m-d H:i:s');
        
        $activityLog->save();
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

        $docTemplatePages = DB::table('document_template_pages')
        ->leftJoin('users', 'users.id', '=', 'document_template_pages.is_locked')
        ->select('document_template_pages.*', 'users.name')
        ->get();

        $current_user = auth()->user();

        // $docTemplatePage = DocumentTemplatePages::where('document_template_details_id', '=', $docTemplateDetailSelected[0]->id)->get();
        $docTemplateDetail = DocumentTemplateDetails::where('document_template_main_id', '=', $id)->get();
        $docTemplateMain = DocumentTemplateMain::where('id', '=', $id)->get();
        return view('dashboard.documentTemplate.show', [
            'docTemplatePages' => $docTemplatePages, 
            'docTemplateDetail' => $docTemplateDetail, 
            'docTemplateMain' => $docTemplateMain
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $roles = Roles::where('status', '=', '1')->get();
        $templateEmailDetails = DocumentTemplateDetails::where('document_template_main_id', '=', $id)->get();
        return view('dashboard.documentTemplate.edit', [
            'template' => DocumentTemplateMain::where('id', '=', $id)->first(),
            'templateEmailDetails' => $templateEmailDetails
        ]);
    }

    public function checkPageLocked(Request $request)
    {
        $result = "0";

        $docPage = DocumentTemplatePages::where('id', '=', $request->input('id'))->first();

        if ($docPage->is_locked != "0")
        {
            $lock_user = Users::where('id', '=', $docPage->is_locked)->get();
            $result = $lock_user[0]->name." is editing this page";
        }

        

        return $result;
    }

    public function updatePage(Request $request)
    {
        // $request->content;
       

        $templateEmail = DocumentTemplatePages::where('id', '=', $request->input('id'))->first();

        return  $request->page;
    }

    public function updateLockStatus(Request $request)
    {
        // $request->content;
        $current_user = auth()->user();

        $templateEmail = DocumentTemplatePages::where('id', '=', $request->input('id'))->first();

        if ($request->input('is_locked') == '1')
        {
            $templateEmail->is_locked = $current_user->id;;
        }
        else
        {
            $templateEmail->is_locked = 0;
        }
        
        $templateEmail->save();
        

        return  'test';
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
        $templateEmail = EmailTemplateMain::where('id', '=', $request->input('id'))->first();

        $templateEmail->name = $request->input('name');
        $templateEmail->code = $request->input('code');
        $templateEmail->subject = $request->input('subject');
        $templateEmail->to = '';
        $templateEmail->from = '';
        $templateEmail->cc = '';
        $templateEmail->status = '1';
        $templateEmail->content = $request->input('summary-ckeditor');

        $templateEmail->save();




        $to = '';
        $from = '';
        $cc = '';

        if (!empty($request->input('emailTo')))
        {
            $to = implode(",",$request->input('emailTo'));
        }

        if (!empty($request->input('emailFrom')))
        {
            $from = implode(",",$request->input('emailFrom'));
        }

        if (!empty($request->input('emailCC')))
        {
            $cc = implode(",",$request->input('emailCC'));
        }

        $templateEmail = EmailTemplateMain::where('id', '=', $request->input('id'))->first();

        $templateEmail->name = $request->input('name');
        $templateEmail->desc = $request->input('desc');
        $templateEmail->code = $request->input('code');
        $templateEmail->subject = $request->input('subject');
        $templateEmail->to = $to;
        $templateEmail->from = $from;
        $templateEmail->cc =$cc;
        $templateEmail->status =  $request->input('status');
        // $templateEmail->content = $request->input('summary-ckeditor');

        $templateEmail->save();

        
        if ($templateEmail->id != null && $templateEmail->id != '') {
            $templateEmailDetails  = new EmailTemplateDetails();

            $templateEmailDetails->email_template_id = $templateEmail->id;
            $templateEmailDetails->version_name = 'Orinal';
            $templateEmailDetails->content = $request->input('summary-ckeditor');
            $templateEmailDetails->status = $request->input('status');

            $templateEmailDetails->save();
        }


        // $menuLangList->name = $request->input('name');
        // $menuLangList->short_name = $request->input('shortName');
        // if($request->input('is_default') === 'true'){
        //     $menuLangList->is_default = true;
        // }else{
        //     $menuLangList->is_default = false;
        // }
        // $menuLangList->save();
        $request->session()->flash('message', 'Successfully updated language');
        return redirect()->route('email-template.index');
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

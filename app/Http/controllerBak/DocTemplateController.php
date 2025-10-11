<?php

namespace App\Http\Controllers;

use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\EmailTemplateMain;
use App\Models\DocumentTemplateMain;
use App\Models\DocumentTemplateDetails;
use App\Models\DocumentTemplatePages;
use App\Models\CaseTemplate;
use App\Models\Roles;
use App\Models\caseTemplateDetails;
use App\Models\EmailTemplateDetails;
use Illuminate\Http\Request;
use App\Models\MenusLang;
use App\Models\Users;
use App\Models\CaseMasterListCategory;
use App\Models\CaseMasterListField;
use Illuminate\Support\Facades\DB;


class DocTemplateController extends Controller
{
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard.documentTemplate.index', ['templates' => DocumentTemplateMain::all()]);
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
    public function store(Request $request)
    {
        $to = '';
        $from = '';
        $cc = '';

        $documentTemplateMain  = new DocumentTemplateMain();

        $documentTemplateMain->name = $request->input('name');
        $documentTemplateMain->desc = $request->input('desc');
        $documentTemplateMain->code = $request->input('code');
        $documentTemplateMain->status =  $request->input('status');
        $documentTemplateMain->created_at = now();
        // $templateEmail->content = $request->input('summary-ckeditor');

        $documentTemplateMain->save();

        $documentTemplateDetails  = new DocumentTemplateDetails();

        $documentTemplateDetails->document_template_main_id = $documentTemplateMain->id;
        $documentTemplateDetails->version_name = 'version_1';
        $documentTemplateDetails->status =  1;
        $documentTemplateDetails->created_at = now();

        $documentTemplateDetails->save();



        $request->session()->flash('message', 'Successfully created template');
        return redirect()->route('document-template.index');

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

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        // $caseTemplateDetail = CaseTemplateDetails::all();
        
        $docTemplateDetailSelected = DocumentTemplateDetails::where('document_template_main_id', '=', $id)->where('status', '=', 1)->first();
        $caseMasterListCategory = CaseMasterListCategory::all();
        $caseMasterListField = CaseMasterListField::all();

        $docTemplatePages = DB::table('document_template_pages')
        ->leftJoin('users', 'users.id', '=', 'document_template_pages.is_locked')
        ->select('document_template_pages.*', 'users.name')
        ->where('document_template_details_id', '=',  $docTemplateDetailSelected->id)
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

        $docTemplatePages = DB::table('document_template_pages')
        ->leftJoin('users', 'users.id', '=', 'document_template_pages.is_locked')
        ->select('document_template_pages.*', 'users.name')
        ->where('document_template_pages.id', '=',   $request->input('id'))
        ->get();


        // if ($docPage->is_locked != "0")
        // {
        //     $lock_user = Users::where('id', '=', $docPage->is_locked)->get();
        //     $result = $lock_user[0]->name." is editing this page";
        // }

        return $docTemplatePages;
    }

    public function createNewPage(Request $request)
    {

        $page = DB::table('document_template_pages')
        ->select(array('page'))
        ->where('document_template_details_id', '=', $request->input('page'))
        ->orderBy('page','DESC')
        ->first();


        // $templateEmail->content = $request->input('content');
        // $templateEmail->save();


        return $page;
    }

    public function updatePage(Request $request)
    {
        
        $pageId = $request->input('pageId');
        $templateEmail = null;
        $page_no = 1;

        $templateDocumentDetails = DocumentTemplateDetails::where('document_template_main_id', '=', $request->input('template_id'))->first();

        if ($pageId == "0")
        {
            $page = DB::table('document_template_pages')
            ->select(array('page'))
            ->where('document_template_details_id', '=', $request->input('template_id'))
            ->orderBy('page','DESC')
            ->get();

            if(count($page) > 0)
            {
                $page_no = $page[0]->page + 1;
            }

            $templateEmail  = new DocumentTemplatePages();
            $templateEmail->document_template_details_id = $templateDocumentDetails->id;
            $templateEmail->is_locked = 0;
            $templateEmail->page = $page_no;
            $templateEmail->status = 1;
        }
        else
        {
            $templateEmail = DocumentTemplatePages::where('id', '=', $request->input('pageId'))->first();
        }

        $templateEmail->content = $request->input('content');
        $templateEmail->save();


        return $templateEmail;
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
    public function update(Request $request, $id)
    {
        // $validatedData = $request->validate([
        //     'name'             => 'required|min:1|max:64',
        //     'shortName'        => 'required|min:1|max:64',
        //     'is_default'       => 'required|in:true,false'
        // ]);
        $documentTemplate = DocumentTemplateMain::where('id', '=',  $id)->first();

        $documentTemplate->name = $request->input('name');
        $documentTemplate->desc = $request->input('desc');
        $documentTemplate->code = $request->input('code');

        $documentTemplate->save();

        $request->session()->flash('message', 'Successfully updated template');
        return redirect()->route('document-template.index');
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

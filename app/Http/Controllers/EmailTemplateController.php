<?php

namespace App\Http\Controllers;

use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\EmailTemplateMain;
use App\Models\EmailTemplate;
use App\Models\CaseTemplate;
use App\Models\Roles;
use App\Models\caseTemplateDetails;
use App\Models\EmailTemplateDetails;
use Illuminate\Http\Request;
use App\Models\MenusLang;


class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard.emailTemplate.index', ['templates' => EmailTemplateMain::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Roles::where('status', '=', '1')->get();

        return view('dashboard.emailTemplate.create', [
            'templates' => CaseTemplate::all(),
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $to = '';
        $from = '';
        $cc = '';

        // if (!empty($request->input('emailTo')))
        // {
        //     $to = implode(",",$request->input('emailTo'));
        // }

        // if (!empty($request->input('emailFrom')))
        // {
        //     $from = implode(",",$request->input('emailFrom'));
        // }

        // if (!empty($request->input('emailCC')))
        // {
        //     $cc = implode(",",$request->input('emailCC'));
        // }

        $templateEmail  = new EmailTemplateMain();

        $templateEmail->name = $request->input('name');
        $templateEmail->desc = $request->input('desc');
        $templateEmail->code = $request->input('code');
        $templateEmail->subject = $request->input('subject');
        // $templateEmail->to = $to;
        // $templateEmail->from = $from;
        // $templateEmail->cc =$cc;
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

    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        // $caseTemplateDetail = CaseTemplateDetails::all();

        $caseTemplateDetail = CaseTemplateDetails::where('template_main_id', '=', $id)->get();
        $caseTemplateMain = caseTemplate::where('id', '=', $id)->get();
        return view('dashboard.caseTemplate.show', [
            'lang' => MenuLangList::where('id', '=', $id)->first(), 'templates' => $caseTemplateDetail, 'templates_main' => $caseTemplateMain
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $roles = Roles::where('status', '=', '1')->get();
        $templateEmailDetails = EmailTemplateDetails::where('email_template_id', '=', $id)->first();
        return view('dashboard.emailTemplate.edit', [
            'template' => EmailTemplateMain::where('id', '=', $id)->first(),
            'templateEmailDetails' => $templateEmailDetails,
            'roles' => $roles
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MenuLangList  $menuLangList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        // $validatedData = $request->validate([
        //     'name'             => 'required|min:1|max:64',
        //     'shortName'        => 'required|min:1|max:64',
        //     'is_default'       => 'required|in:true,false'
        // ]);

        $templateEmail = EmailTemplateMain::where('id', '=', $id)->first();

        $templateEmail->name = $request->input('name');
        $templateEmail->code = $request->input('code');
        $templateEmail->subject = $request->input('subject');
        $templateEmail->to = '';
        $templateEmail->from = '';
        $templateEmail->cc = '';
        $templateEmail->status = '1';
        // $templateEmail->content = $request->input('summary-ckeditor');

        $templateEmail->save();

        if ($templateEmail->id != null && $templateEmail->id != '') {
            $templateEmailDetails  = new EmailTemplateDetails();
            $templateEmailDetails = EmailTemplateDetails::where('email_template_id', '=', $id)->where('status', '=', '1')->first();

            $templateEmailDetails->email_template_id = $templateEmail->id;
            $templateEmailDetails->version_name = 'Orinal';
            $templateEmailDetails->content = $request->input('summary-ckeditor');
            $templateEmailDetails->status = 1;
            $templateEmailDetails->updated_at = date('Y-m-d H:i:s');

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

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
use App\Models\DocumentTemplateFile;
use App\Models\DocumentTemplateFileDetails;
use App\Models\DocumentTemplateFileFolder;
use App\Models\DocumentTemplateFileMain;
use App\Models\Parameter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class DocTemplateFilev2Controller extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $parameter = Parameter::where('parameter_type', '=', 'template_file_path')->first();
        $template_path = $parameter->parameter_value_1;

        // $documentTemplateFile = DB::table('document_template_file_main AS m')
        // ->leftJoin('document_template_file_details AS d', 'm.id', '=', 'd.document_template_file_main_id')
        // ->select('m.*', 'd.file_name')
        // ->where('d.status', '=',  '1')
        // ->where('m.name', '=',  'Presentation')
        // ->paginate(20);

        $query = '';

        if($request->input('query') != null)
        {
            $query = $request->input('query');
        }

        
        $fileFolder = DocumentTemplateFileFolder::orderBy('no_delete', 'ASC')->orderBy('name', 'ASC')->get();

        for ($j = 0; $j < count($fileFolder); $j++) {

            $fileFolder[$j]->count = DocumentTemplateFileMain::where('folder_id', $fileFolder[$j]->id)->count();
            
        }

        $documentTemplateFilev2 = DB::table('document_template_file_main AS m')
        ->select('m.*')
        // ->where('m.status', '=',  '1')
        ->where('m.name', 'like',  '%'.$query.'%')
        ->orderBy('m.name','ASC')
        ->get();

        for ($j = 0; $j < count($documentTemplateFilev2); $j++) {

            $documentTemplateFilev2[$j]->count = DocumentTemplateFileDetails::where('document_template_file_main_id', $documentTemplateFilev2[$j]->id)->count();
            
        }

        return view('dashboard.documentFile.index', [
            // 'documentTemplateFile' => $documentTemplateFile, 
                                                    'template_path' => $template_path,  
                                                    'documentTemplateFilev2' => $documentTemplateFilev2, 
                                                    'fileFolder' => $fileFolder]);
    }

    public function getDocumentFileMainListBak($selected_folder_id)
    {
        $documentTemplateFilev2 = DB::table('document_template_file_main AS m')
        ->select('m.*')
        ->where('m.folder_id', '!=',  $selected_folder_id)
        ->orderBy('m.name','ASC')
        ->get();

        for ($j = 0; $j < count($documentTemplateFilev2); $j++) {

            $documentTemplateFilev2[$j]->count = DocumentTemplateFileDetails::where('document_template_file_main_id', $documentTemplateFilev2[$j]->id)->count();
        }


        return response()->json(['status' => 1, 
        'table' => view('dashboard.documentFile.table.tbl-move-file', compact('documentTemplateFilev2'))->render(),]);
    }

    public function getDocumentFileMainList(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            
            $documentTemplateFilev2 = DB::table('document_template_file_main AS m')
                ->leftJoin('document_template_file_folder as b', 'b.id', '=', 'm.folder_id')
                ->select('m.*', 'b.name as folder_name')
                ->where('m.folder_id', '!=',  $request->input('folder_id'))
                ->orderBy('m.name','ASC');

            $documentTemplateFilev2 = $documentTemplateFilev2->get();

            for ($j = 0; $j < count($documentTemplateFilev2); $j++) {

                $documentTemplateFilev2[$j]->count = DocumentTemplateFileDetails::where('document_template_file_main_id', $documentTemplateFilev2[$j]->id)->count();
            }

            return DataTables::of($documentTemplateFilev2)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $actionBtn = '
                    <div class="checkbox">
                        <input type="checkbox" name="files" value="' . $data->id . '"
                            id="chk_' . $data->id . '">
                        <label for="chk_' . $data->id . '"> </label>
                    </div>
                    ';
                    return $actionBtn;
                })
                ->rawColumns(['action', 'bal_to_transfer', 'transfer_from_bank', 'transfer_to_bank', 'transferred_to_office_bank', 'case_ref_no', 'is_recon'])
                ->make(true);
        }
    }


    public function getFile(Request $request)
    {
        // take case 1 as example
        $id = 1;

        $template_id =  $request->input('template_id');

        $documentTemplateFile = DocumentTemplateFile::where('id', '=', $template_id)->first();
        $case = DB::table('loan_case')->select('case_ref_no')->where('id', '=', $id)->first();

        $case_ref_no = $case->case_ref_no;

        // get file template path
        $templatePath =  $documentTemplateFile->path;

        // 1. Copy the file from templatepath $templatePath to public\documents\cases
        // 2. replace the content with laster list below and save

        $templatePath = public_path() . "/template/documents/SPA_with_Title_PV_010122.docx";

        $filename = basename($templatePath);
        $newSavePath = storage_path('app/documents/cases/S1_UWD_MBB_1_lll_RLI/'.$filename);
        // $newSavePath = storage_path('/template/documents/test.docx');

        $templateWord = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

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
            $templateWord->setValue($caseMasterListField[$j]->code,$caseMasterListField[$j]->value);
        }


        $templateWord->setValue('case_ref_no',$case_ref_no);
        $templateWord->setValue('File_Ref',$case_ref_no);


        $templateWord->saveAs($newSavePath);
        echo $templatePath;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Roles::where('status', '=', '1')->get();
        $fileFolder = DocumentTemplateFileFolder::get();

        return view('dashboard.documentFile.create', [
            'templates' => CaseTemplate::all(),
            'fileFolder' => $fileFolder
        ]);
    }

    function moveFileFolder(Request $request, $id)
    {
        $billList = [];

        $current_user = auth()->user();

        if ($request->input('bill_list') != null) {
            $billList = json_decode($request->input('bill_list'), true);
        }

        if (count($billList) > 0) {


            for ($i = 0; $i < count($billList); $i++) {

                $DocumentTemplateFileMain = DocumentTemplateFileMain::where('id', '=', $billList[$i]['file_id'])->first();

                $DocumentTemplateFileMain->folder_id = $id;
                $DocumentTemplateFileMain->save();
            }

        }

        
        return response()->json(['status' => 1, 'message' => 'File moved to destination']);
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

        $documentTemplateMain  = new DocumentTemplateFileMain();

        $documentTemplateMain->name = $request->input('name');
        $documentTemplateMain->type = 1;
        $documentTemplateMain->status =  $request->input('status');
        $documentTemplateMain->folder_id =  $request->input('folder_id');
        $documentTemplateMain->created_at = now();
        // $templateEmail->content = $request->input('summary-ckeditor');

        $documentTemplateMain->save();


        $request->session()->flash('message', 'Successfully created template');
        return redirect()->route('document-file.edit', [$documentTemplateMain->id]);

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
        $parameter = Parameter::where('parameter_type', '=', 'template_file_path')->first();
        $template_path = $parameter->parameter_value_1;

        $templateFileMain = DocumentTemplateFileMain::where('id', '=', $id)->first();
        $templateFileDetails = DocumentTemplateFileDetails::where('document_template_file_main_id', '=', $id)->get();
        $fileFolder = DocumentTemplateFileFolder::get();

        // storage_path($file_folder_name_temp.'/'.$filename);

        return view('dashboard.documentFile.edit', [
            'template_path' => $template_path,
            'template' => $templateFileMain,
            'fileFolder' => $fileFolder,
            'details' => $templateFileDetails
        ]);
    }

    public function createFolder(Request $request)
    {
        $DocumentTemplateFileFolder  = new DocumentTemplateFileFolder();

        $DocumentTemplateFileFolder->name = $request->input('name');
        $DocumentTemplateFileFolder->type = 2;
        $DocumentTemplateFileFolder->status =  $request->input('folder_status');
        $DocumentTemplateFileFolder->remarks =  $request->input('remarks');
        $DocumentTemplateFileFolder->created_at = now();
        // $templateEmail->content = $request->input('summary-ckeditor');

        $DocumentTemplateFileFolder->save();

        return response()->json(['status' => 1, 'message' => 'Folder created']);
    }

    public function editFolder(Request $request)
    {
        $DocumentTemplateFileFolder = DocumentTemplateFileFolder::where('id', $request->input('id'))->first();

        $DocumentTemplateFileFolder->name = $request->input('name');
        $DocumentTemplateFileFolder->type = 2;
        $DocumentTemplateFileFolder->status =  $request->input('folder_status');
        $DocumentTemplateFileFolder->remarks =  $request->input('remarks');
        $DocumentTemplateFileFolder->created_at = now();
        // $templateEmail->content = $request->input('summary-ckeditor');

        $DocumentTemplateFileFolder->save();

        return response()->json(['status' => 1, 'message' => 'Folder edited']);
    }


    public function get_file($filename)
    {
          $file_path = storage_path('uploads') . "/" . $filename;
          return Response::download($file_path);
    }

    public function setFileActive(Request $request)
    {
        $data = "0";
        $status = 1;

        // set all file stauts under this template to 0 
        DB::table('document_template_file_details')->where('document_template_file_main_id', '=',   $request->input('id'))->update(['status' => 0]);

        $templateFileDetails = DocumentTemplateFileDetails::where('id', '=', $request->input('file_id'))->first();
        $templateFileDetails->status = 1;
        $templateFileDetails->save();

        return response()->json(['status' => $status, 'data' => $data]);
    }

    public function updateFileTemplateInfo(Request $request, $id)
    {
        $data = "0";
        $status = 1;

        $templateFileMain = DocumentTemplateFileMain::where('id', '=', $id)->first();

        $templateFileMain->name = $request->input('name');
        $templateFileMain->remarks = $request->input('remarks');
        $templateFileMain->status = $request->input('status');

        $templateFileMain->save();

        return response()->json(['status' => $status, 'data' => $data]);
    }

    public function UploadFileTemplate(Request $request, $id)
    {
        $data = "0";
        $status = 1;

        $status = 1;
        $data = '';
        $file = $request->file('inp_file');

        $filename = time() . '_' . $file->getClientOriginalName();


        $current_user = auth()->user();

        $parameter = Parameter::where('parameter_type', '=', 'template_file_path')->first();
        $template_path = $parameter->parameter_value_1;

        // File extension
        $extension = $file->getClientOriginalExtension();

        // if($request->input('downloadOnly') == 'false')
        // {
        //     if($extension != 'docx')
        //     {
        //         return response()->json(['status' => 0, 'data' => 'Please make sure the file extension is docx']);
        //     }
        // }

        $case_ref_no =  $request->input('case_ref_no');

        $file_folder_name_temp = $template_path.'file_template_'.$id;
        // $file_folder_name_temp = $template_path.'file_template';
        $file_folder_name_public = public_path($file_folder_name_temp);

        // if(!File::isDirectory($file_folder_name)){

        //     File::makeDirectory($file_folder_name, 0777, true, true);
           
        // }

        if(!File::isDirectory($file_folder_name_public)){
            File::makeDirectory($file_folder_name_public, 0777, true, true);
        }

        // $file->move($file_folder_name, $filename);
        $file->move($file_folder_name_public, $filename);

        // $path = storage_path($file_folder_name_temp.'/'.$filename);
        // return response()->download($path);

        $documentTemplateFileDetails = new DocumentTemplateFileDetails();

        DB::table('document_template_file_details')->where('document_template_file_main_id', '=',   $id)->update(['status' => 0]);

        $documentTemplateFileDetails->document_template_file_main_id = $id;
        $documentTemplateFileDetails->ori_file_name = $file->getClientOriginalName();
        $documentTemplateFileDetails->file_name = $filename;
        $documentTemplateFileDetails->type = $file->getClientOriginalExtension();;
        $documentTemplateFileDetails->status = 1;
        $documentTemplateFileDetails->created_at = date('Y-m-d H:i:s');
        $documentTemplateFileDetails->save();

        return response()->json(['status' => $status, 'data' => $data]);
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

        
        $documentTemplateMain = DocumentTemplateFileMain::where('id', '=',  $id)->first();

        $documentTemplateMain->name = $request->input('name');
        $documentTemplateMain->remarks = $request->input('remarks');
        $documentTemplateMain->status = $request->input('status');
        $documentTemplateMain->folder_id = $request->input('folder_id');

        $documentTemplateMain->save();

        $request->session()->flash('message', 'Successfully updated template');
        
        return redirect()->route('document-file.edit', $id);
    }

    public function deleteFolder($id)
    {
        
        $templateDocumentDetails = DocumentTemplateFileFolder::where('id', '=', $id)->delete();

        DocumentTemplateFileMain::where('folder_id', $id)->update(['folder_id' => 1]);
        


        return response()->json(['status' => 1, 'message' => 'Deleted the folder']);
    }

    public function deleteFile($id)
    {
        
        $templateDocumentDetails = DocumentTemplateFileDetails::where('document_template_file_main_id', '=', $id)->get();
        

        // return $templateDocumentDetails;

        $templateDocumentDetails->each->delete();

        $documentTemplateMain = DocumentTemplateFileMain::where('id', '=',  $id)->first();

        $documentTemplateMain->delete();


        return response()->json(['status' => 1, 'message' => 'Deleted the template']);
    }

    public function deleteUploadedFile($template_id,$id)
    {
        
        $templateDocumentDetails = DocumentTemplateFileDetails::where('id', '=', $id)->first();

        if ($templateDocumentDetails->status == 1)
        {
            return response()->json(['status' => 2, 'message' => 'Cannot delete active file']);
        }
        

        $parameter = Parameter::where('parameter_type', '=', 'template_file_path')->first();
        $template_path = $parameter->parameter_value_1;


        $file_folder_name_temp = $template_path.'file_template_'.$template_id.'/'.$templateDocumentDetails->file_name;


        if (File::exists(public_path($file_folder_name_temp))) {
            File::delete(public_path($file_folder_name_temp));
        }

        $templateDocumentDetails->delete();

        // $documentTemplateMain = DocumentTemplateFileMain::where('id', '=',  $id)->first();

        // $documentTemplateMain->delete();


        return response()->json(['status' => 1, 'message' => 'Deleted the file']);
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

<?php

namespace App\Http\Controllers;

use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\Users;
use App\Models\Banks;
use App\Models\Customer;
use App\Models\Parameter;
use App\Models\caseTemplate;
use App\Models\LoanCase;
use App\Models\LoanCaseDetails;
use App\Models\LoanCaseNotes;
use App\Models\CaseMasterListCategory;
use App\Models\CaseMasterListField;
use App\Models\perm;
use Illuminate\Http\Request;
use App\Models\MenusLang;
use App\Http\Helper\Helper;
use App\Models\CheckListDetails;
use App\Models\CheckListMain;
use App\Models\CHKT;
use App\Models\Courier;
use App\Models\Dispatch;
use App\Models\LandOffice;
use App\Models\LoanAttachment;
use App\Models\LoanCaseKivNotes;
use App\Models\OperationAttachments;
use App\Models\SafeKeeping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public static function operationUpload(Request $request)
    {
        $current_user = auth()->user();
        $record_id = $request->input('record_id');
        $operation_code = $request->input('operation_code');
        $location = '';
        $ObjCollection = null;

        switch (strtolower($operation_code)) {
            case 'safekeeping':
                $location = 'safekeeping';
                $ObjCollection = SafeKeeping::where('id', $record_id)->first();
                break;
            case 'landoffice':
                $location = 'landoffice';
                $ObjCollection = LandOffice::where('id', $record_id)->first();
                break;
            case 'chkt':
                $location = 'chkt';
                $ObjCollection = CHKT::where('id', $record_id)->first();
                break;
            case 'dispatch':
                $location = 'dispatch';
                $ObjCollection = Dispatch::where('id', $record_id)->first();
                break;
            default:
        }

        if ($record_id == 0 || $location == '') {
            return response()->json(['status' => 0, 'message' => 'Failed to detect record id'], 400);
        }

        // $SafeKeeping = DB::table('safe_keeping')->where('id', $record_id)->first();

        if (!$ObjCollection) {
            return response()->json(['status' => 0, 'message' => 'Failed to detect record'], 400);
        }

        $files = $request->file('file');
        $disk = Storage::disk('Wasabi');

        foreach ($files as $file) {
            $s3_file_name = '';

            if ($file) {
                $oriFilename = $file->getClientOriginalName();
                $res = str_replace(array('\'', '"', ',', ';', '<', '>', ' ', '&'), '_', $file->getClientOriginalName());

                $filename = time() . '_' . $res;

                $isImage =  ImageController::verifyImage($file);

                if ($isImage == true) {
                    $s3_file_name = ImageController::resizeImg($file, $location, $filename);
                } else {
                    $s3_file_name =  $disk->put($location, $file);
                }

                $OperationAttachments  = new OperationAttachments();

                $OperationAttachments->key_id = $ObjCollection->id;
                $OperationAttachments->file_ori_name = $oriFilename;
                $OperationAttachments->file_new_name = $s3_file_name;
                $OperationAttachments->s3_file_name = $s3_file_name;
                $OperationAttachments->created_by = $current_user->id;
                $OperationAttachments->branch = $request->input('branch');
                $OperationAttachments->attachment_type = $location;
                $OperationAttachments->entity = 1;
                $OperationAttachments->save();
            }
        }

        UploadController::compileNotesUpdate($ObjCollection, $operation_code,$request, $record_id);
        

        return response()->json(['status' => 1, 'message' => 'Successfully created new record'], 200);
    }

    public static function CaseFileUpload(Request $request)
    {
        // return $request;; 

        $s3_file_name = '';
        $current_user = auth()->user();

        $checklist_id = 0;
        $files = $request->file('file');
        $remarkList = $request->input('remark');
        $typeList = $request->input('type');
        $file_type =  $request->input('file_type');

        if($request->input('checklist_id') != 0)
        {
            $checklist_id = $request->input('checklist_id');
        }

        $disk = Storage::disk('Wasabi');

        foreach ($files as $index => $file) {
            
            $filename = time() . '_' . $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $res = str_replace(array('\'', '"', ',', ';', '<', '>', ' ', '&'), '_', $file->getClientOriginalName());

            if ($file_type == 1) {
                $location = 'cases/' . $request->input('case_id') . '';
            } else {
                $location = 'cases/' . $request->input('case_id') . '/marketing';
            }

            $isImage =  ImageController::verifyImage($file);

            $target_file = preg_replace("/[^a-z0-9\_\-\.]/i", '', $filename);

            if ($isImage == true) {
                $s3_file_name = ImageController::resizeImg($file, $location, $target_file);
            } else {
                $s3_file_name =  $disk->put($location, $file);

                // Storage::disk('Wasabi')->put(
                //     $location . '/' . $filename ,
                //     $resource
                // );
            }

                $filename = time() . '_' . $res;

            $LoanAttachment = new LoanAttachment();

            $LoanAttachment->case_id =  $request->input('case_id');
            $LoanAttachment->checklist_id = $checklist_id;
            $LoanAttachment->display_name = $file->getClientOriginalName();
            $LoanAttachment->filename = $s3_file_name;
            $LoanAttachment->s3_file_name = $s3_file_name;
            $LoanAttachment->type = $extension;
            $LoanAttachment->file_type = $file_type;
            $LoanAttachment->attachment_type = $typeList[$index];

            if($request->input('checklist_id') != 0)
            {
                $CheckListDetailsCheck = CheckListDetails::where('id', $checklist_id)->first();

                $LoanAttachment->remark = $CheckListDetailsCheck->name;
            }
            else
            {
                $LoanAttachment->remark = $remarkList[$index];
            }    
            
            $LoanAttachment->user_id = $current_user->id;
            $LoanAttachment->status = 1;
            $LoanAttachment->created_at = date('Y-m-d H:i:s');
            $LoanAttachment->save();
        }

        $LoanAttachment = QueryController::getCaseAttachment($request->input('case_id'), 1);
        $LoanAttachmentMarketing = QueryController::getCaseAttachment($request->input('case_id'), 2);

        $CheckListMain = CheckListMain::where('status', 1)->get();
        $CheckListDetails = CheckListDetails::where('status', 1)->get();

        // return $LoanAttachment ;

        return response()->json([
            'status' => 1,
            'LoanAttachment' => view('dashboard.case.table.tbl-case-attachment', compact('LoanAttachment', 'current_user'))->render(),
            'LoanAttachmentMarketing' => view('dashboard.case.table.tbl-case-marketing-attachment', compact('LoanAttachmentMarketing', 'current_user'))->render(),
            'CheckListMain' => view('dashboard.case.tabs.tab-case3', compact('LoanAttachment', 'CheckListMain', 'CheckListDetails', 'current_user'))->render(),
        ]);
    }

    public static function compileNotesUpdate($ObjCollection, $operation_code, $request, $record_id)
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
                <strong>Smartbox No</strong>:&nbsp;' .$ObjCollection->smartbox_no  . '<br />
                <strong>Receipt No</strong>:&nbsp;' .$ObjCollection->receipt_no  . '<br />
                <strong>Matter</strong>:&nbsp;' .$ObjCollection->matter . '<br />
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
                <strong>Current SPA Date</strong>:&nbsp;' .$ObjCollection->current_spa_date  . '<br />
                <strong>CHKT Filed On</strong>:&nbsp;' .$ObjCollection->chkt_filled_on . '<br />
                <strong>Remark</strong>:&nbsp;' .$ObjCollection->remark  . '<br />
                <strong>3% RPGT Paid</strong>:&nbsp;' . $status_span;

                break;
            
            case 'dispatch':
                $courier = Courier::where('id', '=', $ObjCollection->courier_id)->first();
                $dispatch_name = '';

                if($courier)
                {
                    $dispatch_name = $courier->name;
                }

                $dispatch_type= '';

                if ($request->input('dispatch_type') != '') {
                    if ($request->input('dispatch_type') == 1) {
                        $dispatch_type = 'Outgoing';
                    } else if ($request->input('dispatch_type') == 2) {
                        $dispatch_type = 'Incoming';
                    }
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

        $OperationAttachments = OperationAttachments::where('key_id', $ObjCollection->id)->where('attachment_type',strtolower($operation_code))->where('status',1)->get();

        if(count($OperationAttachments) > 0)
        {
            
            $attachment = '<br/><strong>Attachment</strong>:&nbsp;<br />';
            foreach ($OperationAttachments as $file)
            {
                $attachment .= '<a  href="javascript:void(0)" onclick="openFileFromS3(\'' . $file->s3_file_name . '\')"  class="mailbox-attachment-name "><i class="fa fa-paperclip"></i>' . $file->file_ori_name . '</a><br />';

              
            }

            $message = $message.$attachment;
            
        }

        
        $LoanCaseKivNotes = LoanCaseKivNotes::where('object_id_1', '=', $record_id)->where('label', '=', 'operation|' . $operation_code)->first();

        $LoanCaseKivNotes->notes =  $message;
        $LoanCaseKivNotes->updated_at = date('Y-m-d H:i:s');
        $LoanCaseKivNotes->updated_by = $current_user->id;
        $LoanCaseKivNotes->save();
    }
}

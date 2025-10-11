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
use App\Models\CHKT;
use App\Models\Courier;
use App\Models\Dispatch;
use App\Models\LandOffice;
use App\Models\LegalCloudCaseActivityLog;
use App\Models\LoanCaseKivNotes;
use App\Models\OperationAttachments;
use App\Models\SafeKeeping;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OperationController extends Controller
{
    public function storeRecords(Request $request)
    {
        $case_id = 0;
        $current_user = auth()->user();
        $ObjCollection = null;

        $operation_code = $request->input('operation_code');

        switch (strtolower($operation_code)) {
            case 'safekeeping':
                $operation = 'safekeeping';
                $ObjCollection = new SafeKeeping();
                break;
            case 'landoffice':
                $operation = 'landoffice';
                $ObjCollection = new LandOffice();
                break;
            case 'chkt':
                $operation = 'chkt';
                $ObjCollection = new CHKT();
                break;
            case 'dispatch':
                $operation = 'dispatch';
                $ObjCollection = new Dispatch();
                break;
            default:
        }

        $running_no = (int)filter_var($request->input('case_ref_no'), FILTER_SANITIZE_NUMBER_INT);


        if (LoanCase::where('case_ref_no', 'like', '%' . $running_no . '%')->count() > 0) {
            $case_id = $request->input('case_id');
        }
       

        $ObjCollection = $ObjCollection->create($request->merge([
            'created_by' => $current_user->id,
        ])->all());

        switch (strtolower($operation_code)) {
            case 'safekeeping':
                break;
            case 'landoffice':

                if ($request->input('received') == 1) {
                    $ObjCollection->received_on = date('Y-m-d H:i:s');
                    $ObjCollection->save();
                }

                break;
            case 'dispatch':

                $current_timestamp = Carbon::now()->timestamp;
                $courier = Courier::where('id', '=', $request->input('courier_id'))->first();

                if($courier)
                {
                    $dispatch_no = $courier->short_code . $case_id . $current_timestamp;

                    $ObjCollection->dispatch_no = $dispatch_no;
                    $ObjCollection->save();
                }
    
                

                break;
            default:
        }

        $this->compileNotes($ObjCollection, $operation_code,$request);

        // $ObjCollection->case_id = $case_id;
        // $ObjCollection->case_ref = $request->input('case_ref_no');
        // $ObjCollection->client_id = 0;
        // $ObjCollection->client_name = $request->input('client');
        // $ObjCollection->document_sent = $request->input('document_sent');
        // $ObjCollection->attention_to = $request->input('attention_to');
        // $ObjCollection->received = $request->input('received');

        // if ($request->input('received') == 1) {
        //     $ObjCollection->received_on = date('Y-m-d H:i:s');
        // }

        // $ObjCollection->created_by = $current_user->id;
        // $ObjCollection->branch = $request->input('branch');
        // $ObjCollection->remark = $request->input('remark');
        // $ObjCollection->status = 1;
        // $ObjCollection->created_at = date('Y-m-d H:i:s');
        // $ObjCollection->save();

        // $status_span = '';

        // if ($ObjCollection->received == '1') {
        //     $status_span = '<span class="label bg-success">Received</span>';
        // } else {
        //     $status_span = '<span class="label bg-warning">Pending</span>';
        // }


        // $message = '
        // <a href="/safe-keeping/' . $ObjCollection->id . '/edit" target="_blank">[Created&nbsp;<b>Safe Keeping</b> record]</a><br />
        // <strong>Document Sent</strong>:&nbsp;' . $request->input('document_sent') . '<br />
        // <strong>Attention To</strong>:&nbsp;' . $request->input('attention_to') . '<br />
        // <strong>Received</strong>:&nbsp;' . $status_span;

        // $LoanCaseKivNotes = new LoanCaseKivNotes();

        // $LoanCaseKivNotes->case_id =  $case_id;
        // $LoanCaseKivNotes->notes =  $message;
        // $LoanCaseKivNotes->label =  'operation|'.$operation;
        // $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
        // $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');

        // $LoanCaseKivNotes->status =  1;
        // $LoanCaseKivNotes->object_id_1 =  $ObjCollection->id;
        // $LoanCaseKivNotes->created_by = $current_user->id;
        // $LoanCaseKivNotes->save();

        return response()->json(['status' => 1, 'record_id' => $ObjCollection->id, 'message' => 'Successfully created new record'], 200);
    }

    public function compileNotes($ObjCollection, $operation_code, $request)
    {
        $status_span = '';
        $current_user = auth()->user();
        $message = '';
        $attachment = '';

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

        $LoanCaseKivNotes = new LoanCaseKivNotes();

        $LoanCaseKivNotes->case_id =  $ObjCollection->case_id;
        $LoanCaseKivNotes->notes =  $message;
        $LoanCaseKivNotes->label =  'operation|' . $operation_code;
        $LoanCaseKivNotes->role =  $current_user->menuroles . "|admin|management";
        $LoanCaseKivNotes->created_at = date('Y-m-d H:i:s');

        $LoanCaseKivNotes->status =  1;
        $LoanCaseKivNotes->object_id_1 =  $ObjCollection->id;
        $LoanCaseKivNotes->created_by = $current_user->id;
        $LoanCaseKivNotes->save();


        $LegalCloudCaseActivityLog  = new LegalCloudCaseActivityLog();

        $LegalCloudCaseActivityLog->user_id = AUTH()->user()->id;
        $LegalCloudCaseActivityLog->case_id = $ObjCollection->case_id;
        $LegalCloudCaseActivityLog->ori_text = '';
        $LegalCloudCaseActivityLog->edit_text = '';
        $LegalCloudCaseActivityLog->action = 'CreatedOperation';
        $LegalCloudCaseActivityLog->object_id = $ObjCollection->id;
        $LegalCloudCaseActivityLog->desc = AUTH()->user()->name . ' created operation (' . $operation_code . ') ID (' . $ObjCollection->id . ') ';
        $LegalCloudCaseActivityLog->status = 1;
        $LegalCloudCaseActivityLog->created_at = date('Y-m-d H:i:s');
        $LegalCloudCaseActivityLog->save();
    }

    public function compileNotesUpdate($ObjCollection, $operation_code, $request, $record_id, $case_id)
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
        $LoanCaseKivNotes->case_id =  $case_id;
        $LoanCaseKivNotes->updated_at = date('Y-m-d H:i:s');
        $LoanCaseKivNotes->updated_by = $current_user->id;
        $LoanCaseKivNotes->save();
    }


    public function updateRecords(Request $request, $record_id)
    {
        $case_id = 0;
        $current_user = auth()->user();
        $ObjCollection = null;

        $operation_code = $request->input('operation_code');

        switch (strtolower($operation_code)) {
            case 'safekeeping':
                $operation = 'safekeeping';
                $ObjCollection = SafeKeeping::where('id', '=', $record_id)->first();
                break;
            case 'landoffice':
                $operation = 'landoffice';
                $ObjCollection = LandOffice::where('id', '=', $record_id)->first();
                break;
            case 'chkt':
                $operation = 'chkt';
                $ObjCollection = CHKT::where('id', '=', $record_id)->first();
                break;
            case 'dispatch':
                $operation = 'dispatch';
                $ObjCollection = Dispatch::where('id', '=', $record_id)->first();
                break;
            default:
        }

        $running_no = (int)filter_var($request->input('case_ref_no'), FILTER_SANITIZE_NUMBER_INT);

        if (LoanCase::where('case_ref_no', 'like', '%' . $running_no . '%')->count() > 0) {
            $case_id = $request->input('case_id');
        }

        if (!$ObjCollection) {
            return response()->json(['status' => 0, 'message' => 'Failed to detect record'], 400);
        }

        $ObjCollection = $ObjCollection->update($request->merge([
            // 'created_by' => $current_user->id,
        ])->all());

        switch (strtolower($operation_code)) {
            case 'safekeeping':
                $operation = 'safekeeping';
                $ObjCollection = SafeKeeping::where('id', '=', $record_id)->first();
                break;
            case 'landoffice':
                $operation = 'landoffice';
                $ObjCollection = LandOffice::where('id', '=', $record_id)->first();
                break;
            case 'chkt':
                $operation = 'chkt';
                $ObjCollection = CHKT::where('id', '=', $record_id)->first();
                break;
            case 'dispatch':
                $operation = 'dispatch';
                $ObjCollection = Dispatch::where('id', '=', $record_id)->first();
                break;
            default:
        }

        $this->compileNotesUpdate($ObjCollection, $operation_code,$request, $record_id, $case_id);


        // $ObjCollection->case_id = $case_id;
        // $ObjCollection->case_ref = $request->input('case_ref_no');
        // $ObjCollection->client_id = 0;
        // $ObjCollection->client_name = $request->input('client');
        // $ObjCollection->document_sent = $request->input('document_sent');
        // $ObjCollection->attention_to = $request->input('attention_to');
        // $ObjCollection->received = $request->input('received');

        // if ($request->input('received') == 1) {
        //     $ObjCollection->received_on = date('Y-m-d H:i:s');
        // }

        // $ObjCollection->created_by = $current_user->id;
        // $ObjCollection->branch = $request->input('branch');
        // $ObjCollection->remark = $request->input('remark');
        // $ObjCollection->status = 1;
        // $ObjCollection->created_at = date('Y-m-d H:i:s');
        // $ObjCollection->save();

        // $status_span = '';

        // if ($ObjCollection->received == 1) {
        //     $status_span = '<span class="label bg-success">Received</span>';
        // } else {
        //     $status_span = '<span class="label bg-warning">Pending</span>';
        // }

        // $message = '
        // <a href="/safe-keeping/' . $ObjCollection->id . '/edit" target="_blank">[Created&nbsp;<b>Safe Keeping</b> record]</a><br />
        // <strong>Document Sent</strong>:&nbsp;' . $request->input('document_sent') . '<br />
        // <strong>Attention To</strong>:&nbsp;' . $request->input('attention_to') . '<br />
        // <strong>Received</strong>:&nbsp;' . $status_span;

        // $LoanCaseKivNotes = LoanCaseKivNotes::where('object_id_1', '=', $record_id)->where('label', '=', 'operation|' . $operation)->first();

        // $LoanCaseKivNotes->notes =  $message;
        // $LoanCaseKivNotes->updated_at = date('Y-m-d H:i:s');
        // $LoanCaseKivNotes->updated_by = $current_user->id;
        // $LoanCaseKivNotes->save();

        return response()->json(['status' => 1, 'record_id' => $ObjCollection->id, 'message' => 'Successfully created new record'], 200);
    }

    public function deleteOperations(Request $request, $record_id)
    {
        $ObjCollection = null;
        $operation_code = $request->input('operation_code');

        switch (strtolower($operation_code)) {
            case 'safekeeping':
                $operation = 'safekeeping';
                $ObjCollection = SafeKeeping::where('id', '=', $record_id)->first();
                break;
            case 'landoffice':
                $operation = 'landoffice';
                $ObjCollection = LandOffice::where('id', '=', $record_id)->first();
                break;
            case 'chkt':
                $operation = 'chkt';
                $ObjCollection = CHKT::where('id', '=', $record_id)->first();
                break;
            case 'dispatch':
                $operation = 'dispatch';
                $ObjCollection = Dispatch::where('id', '=', $record_id)->first();
                break;
            default:
        }

        if($ObjCollection)
        {
            $date = new DateTime($ObjCollection->created_at);
            $diff = (new DateTime)->diff($date)->days;

    
            if ($diff > 3) {
                return response()->json(['status' => 0, 'message' => 'Not allow to delete the record that created more than 3 days']);
            }

            $OperationAttachments = OperationAttachments::where('key_id', $record_id)->where('attachment_type',$operation)->get();

            if(count($OperationAttachments) > 0)
            {
                foreach ($OperationAttachments as $file)
                {
                    if(Storage::disk('Wasabi')->exists($file->s3_file_name)) {
                        Storage::disk('Wasabi')->delete($file->s3_file_name);
                    }

                    $file->update([
                        'status' => 99,
                        'deleted_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                
            }
            $ObjCollection->update([
                'status' => 99,
            ]);

            LoanCaseKivNotes::where('object_id_1', '=', $ObjCollection->id)->where('label', '=', 'operation|'.$operation)->update([
                'status' => 99,
            ]);

            $LegalCloudCaseActivityLog  = new LegalCloudCaseActivityLog();

            $LegalCloudCaseActivityLog->user_id = AUTH()->user()->id;
            $LegalCloudCaseActivityLog->case_id = $ObjCollection->case_id;
            $LegalCloudCaseActivityLog->ori_text = '';
            $LegalCloudCaseActivityLog->edit_text = '';
            $LegalCloudCaseActivityLog->action = 'DeleteOperation';
            $LegalCloudCaseActivityLog->object_id = $ObjCollection->id;
            $LegalCloudCaseActivityLog->desc = AUTH()->user()->name . ' deleted operation (' . $operation . ') ID (' . $ObjCollection->id . ') ';
            $LegalCloudCaseActivityLog->status = 1;
            $LegalCloudCaseActivityLog->created_at = date('Y-m-d H:i:s');
            $LegalCloudCaseActivityLog->save();
        }

        return response()->json(['status' => 1, 'data' => 'Record deleted']);
    }

    public function deleteOperationAttachment(Request $request, $record_id)
    {
        $ObjCollection = null;
        $operation_code = $request->input('operation_code');
       

        $OperationAttachments = OperationAttachments::where('id', $record_id)->first();


        if($OperationAttachments)
        {
            $date = new DateTime($OperationAttachments->created_at);
            $diff = (new DateTime)->diff($date)->days;
    
    
            if ($diff > 3) {
                return response()->json(['status' => 0, 'message' => 'Not allow to delete the attachment that created more than 3 days']);
            }

            if(Storage::disk('Wasabi')->exists($OperationAttachments->s3_file_name)) {
                Storage::disk('Wasabi')->delete($OperationAttachments->s3_file_name);
            }

            $OperationAttachments->update([
                'status' => 99,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            switch (strtolower($operation_code)) {
                case 'safekeeping':
                    $operation = 'safekeeping';
                    $ObjCollection = SafeKeeping::where('id', '=', $OperationAttachments->key_id)->first();
                    break;
                case 'landoffice':
                    $operation = 'landoffice';
                    $ObjCollection = LandOffice::where('id', '=', $OperationAttachments->key_id)->first();
                    break;
                case 'chkt':
                    $operation = 'chkt';
                    $ObjCollection = CHKT::where('id', '=', $OperationAttachments->key_id)->first();
                    break;
                case 'dispatch':
                    $operation = 'dispatch';
                    $ObjCollection = Dispatch::where('id', '=', $OperationAttachments->key_id)->first();
                    break;
                default:
            }

            $LegalCloudCaseActivityLog  = new LegalCloudCaseActivityLog();

            $LegalCloudCaseActivityLog->user_id = AUTH()->user()->id;
            $LegalCloudCaseActivityLog->case_id = $ObjCollection->case_id;
            $LegalCloudCaseActivityLog->ori_text = '';
            $LegalCloudCaseActivityLog->edit_text = '';
            $LegalCloudCaseActivityLog->action = 'DeleteOperationAttachment';
            $LegalCloudCaseActivityLog->object_id = $ObjCollection->id;
            $LegalCloudCaseActivityLog->desc = AUTH()->user()->name . ' deleted operation attachment (' . $operation . ') ID (' . $ObjCollection->id . ') ';
            $LegalCloudCaseActivityLog->status = 1;
            $LegalCloudCaseActivityLog->created_at = date('Y-m-d H:i:s');
            $LegalCloudCaseActivityLog->save();
            
        }
        else
        {
            return response()->json(['status' => 0, 'message' => 'This record not allow to delete']);
        }

        return response()->json(['status' => 1, 'message' => 'Record deleted']);
    }

    public function updateRecordsBak(Request $request, $record_id)
    {
        $case_id = 0;
        $current_user = auth()->user();
        $ObjCollection = null;

        $operation_code = $request->input('operation_code');

        switch (strtolower($operation_code)) {
            case 'safekeeping':
                $operation = 'safekeeping';
                $ObjCollection = SafeKeeping::where('id', '=', $record_id)->first();
                break;
            case 'land_office':
                $operation = 'landoffice';
                $ObjCollection = LandOffice::where('id', '=', $record_id)->first();
                break;
            default:
        }

        $running_no = (int)filter_var($request->input('case_ref_no'), FILTER_SANITIZE_NUMBER_INT);

        if (LoanCase::where('case_ref_no', 'like', '%' . $running_no . '%')->count() > 0) {
            $case_id = $request->input('case_id');
        }

        if (!$ObjCollection) {
            return response()->json(['status' => 0, 'message' => 'Failed to detect record'], 400);
        }


        $ObjCollection->case_id = $case_id;
        $ObjCollection->case_ref = $request->input('case_ref_no');
        $ObjCollection->client_id = 0;
        $ObjCollection->client_name = $request->input('client');
        $ObjCollection->document_sent = $request->input('document_sent');
        $ObjCollection->attention_to = $request->input('attention_to');
        $ObjCollection->received = $request->input('received');

        if ($request->input('received') == 1) {
            $ObjCollection->received_on = date('Y-m-d H:i:s');
        }

        $ObjCollection->created_by = $current_user->id;
        $ObjCollection->branch = $request->input('branch');
        $ObjCollection->remark = $request->input('remark');
        $ObjCollection->status = 1;
        $ObjCollection->created_at = date('Y-m-d H:i:s');
        $ObjCollection->save();

        $status_span = '';

        if ($ObjCollection->received == '1') {
            $status_span = '<span class="label bg-success">Received</span>';
        } else {
            $status_span = '<span class="label bg-warning">Pending</span>';
        }

        $message = '
        <a href="/safe-keeping/' . $ObjCollection->id . '/edit" target="_blank">[Created&nbsp;<b>Safe Keeping</b> record]</a><br />
        <strong>Document Sent</strong>:&nbsp;' . $request->input('document_sent') . '<br />
        <strong>Attention To</strong>:&nbsp;' . $request->input('attention_to') . '<br />
        <strong>Received</strong>:&nbsp;' . $status_span;

        $LoanCaseKivNotes = LoanCaseKivNotes::where('object_id_1', '=', $record_id)->where('label', '=', 'operation|' . $operation)->first();

        $LoanCaseKivNotes->notes =  $message;
        $LoanCaseKivNotes->updated_at = date('Y-m-d H:i:s');
        $LoanCaseKivNotes->updated_by = $current_user->id;
        $LoanCaseKivNotes->save();

        return response()->json(['status' => 1, 'record_id' => $ObjCollection->id, 'message' => 'Successfully created new record'], 200);
    }
}



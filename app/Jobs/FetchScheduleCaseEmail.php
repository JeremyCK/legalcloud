<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

use App\Models\CaseEmailSchedule;
use App\Models\LoanCase;
use App\Models\EmailTemplateMain;
use App\Models\EmailTemplateDetails;

use App\Services\EmailService;

class FetchScheduleCaseEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $logData = [];
        foreach (CaseEmailSchedule::where('effective_date', '<', date("Y-m-d"))->cursor() as $task) {

            //get all necessary data and send out email
            $taskCase = LoanCase::find($task->case_id);
            $taskEmailTemplate = EmailTemplateMain::find($task->email_template_id);
            $taskEmailTemplateDetail = EmailTemplateDetails::where('email_template_id',$task->email_template_id)->orderByDesc('id')->first();

            // TODO :: process data and fill the variable below
            $toUsers = [];
            $ccUsers = [];
            $bccUsers = [];
            $mailContent = "";

            $emailService = new EmailService();
            $emailService->sendEmail($toUsers,$mailContent,$ccUsers,$bccUsers);

            $logData[] = [
                "case_id"=> $task->case_id,
                'email_template_details_id' => $taskEmailTemplateDetail->id,
                'header_info' => json_encode([
                    'to' => $toUsers,
                    'cc' => $ccUsers,
                    'bcc' => $bccUsers
                ]),
                'content' => $mailContent
            ];
        }

        DB::table('case_email_sent_log')->insert($logData);
    }
}

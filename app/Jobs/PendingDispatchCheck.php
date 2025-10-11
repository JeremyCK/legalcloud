<?php

namespace App\Jobs;

use App\Models\AccountLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PendingDispatchCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userId;
    private $kpiTask;
    private $kpiPoint;


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
        $current_user = auth()->user();
        $AccountLog = new AccountLog();
        $AccountLog->user_id = 1;
        $AccountLog->case_id = 1;
        $AccountLog->bill_id = 1;
        $AccountLog->object_id = 1;
        $AccountLog->ori_amt = 0;
        $AccountLog->new_amt = 0;
        $AccountLog->action = 'Test';
        $AccountLog->desc = 'Test';
        $AccountLog->status = 1;
        $AccountLog->created_at = date('Y-m-d H:i:s');
        $AccountLog->save();
    }
}

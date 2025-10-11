<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogUserKpi implements ShouldQueue
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
    public function __construct($userId,$kpiTask,$kpiPoint)
    {
        $this->userId = $userId;
        $this->kpiTask = $kpiTask;
        $this->kpiPoint = $kpiPoint;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $logKpi = new LogUserKpi;

        $logKpi->type = $this->kpiTask;
        $logKpi->user_id = $this->userId;
        $logKpi->point = $this->kpiPoint;
        $logKpi->status = "active";
        $logKpi->save();
    }
}

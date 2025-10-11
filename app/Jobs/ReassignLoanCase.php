<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReassignLoanCase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $caseId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($caseId)
    {
        $this->caseId = $caseId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /*
            php artisan make:job {name}

            1) received job after x hour schedule reach
            2) random assign another group to handle case
        */

        $case = LoadCase::where("id",$this->caseId)->first();

        if($case){
            if(!$case->is_handle){
                // reassign job
                $hgrp = HandleGroup::select("id")->where("id",'<>',$this->handle_group_id)->inRandomOrder()->first();

                $dftExpired = 4;

                $parm = DB::table('parameter')
                ->where('parameter_type', '=', 'reassign_duration')
                ->first();

                if($parm){
                    $dftExpired = intval($parm->parameter_value_1);
                }

                $case->handle_group_id = $hgrp->id;
                $case->handle_expired = now()->addHours($dftExpired);
                $case->save();
            }

        }

    }
}

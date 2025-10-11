<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Jobs\FetchScheduleCaseEmail;
use App\Jobs\PendingDispatchCheck;
use Aws\Command;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\SendCustomerReminder::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //schedule email send - run everyday 12:00 am
        // $schedule->call(function () {
        //     FetchScheduleCaseEmail::dispatch();
        // })->dailyAt('12:00')->runInBackground();

        // $schedule->call(function () {
        //     PendingDispatchCheck::dispatch();
        // })->everyMinute()->runInBackground();

        $schedule->command('mail:reminder')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

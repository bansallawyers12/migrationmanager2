<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        '\App\Console\Commands\CronJob',
        '\App\Console\Commands\BirthDate',
        //'\App\Console\Commands\CompleteTaskRemoval',

        '\App\Console\Commands\InPersonCompleteTaskRemoval',
        '\App\Console\Commands\ProcessServiceAccountTokens',
        '\App\Console\Commands\MigrateSecondDatabase',
        '\App\Console\Commands\CleanUtf8Data',
        '\App\Console\Commands\BackfillEoiRoiData',
        //'\App\Console\Commands\RandomClientSelectionReward',
        //'\App\Console\Commands\VisaExpireReminderEmail',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
		$schedule->command('CronJob:cronjob')->daily();
		$schedule->command('BirthDate:birthdate')->daily();
        //$schedule->command('CompleteTaskRemoval:daily')->daily();

        //InPerson Complete Task Removal daily 1 time
        $schedule->command('InPersonCompleteTaskRemoval:daily')->daily();
        //Random client selection in each month at 1st day
        //$schedule->command('RandomClientSelectionReward:monthly')->monthly();
        //visa expire Reminder email before 15 days daily at 1 time
        //$schedule->command('VisaExpireReminderEmail:daily')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
       // $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

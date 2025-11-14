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
        //'\App\Console\Commands\CompleteTaskRemoval',

        '\App\Console\Commands\InPersonCompleteTaskRemoval',
        '\App\Console\Commands\ProcessServiceAccountTokens',
        '\App\Console\Commands\MigrateSecondDatabase',
        '\App\Console\Commands\CleanUtf8Data',
        '\App\Console\Commands\BackfillEoiRoiData',
        //'\App\Console\Commands\RandomClientSelectionReward',
        //'\App\Console\Commands\VisaExpireReminderEmail',
        
        // Lead Follow-up System Commands
        '\App\Console\Commands\SendFollowupReminders',
        '\App\Console\Commands\MarkOverdueFollowups',
        
        // Appointment Sync System Commands
        '\App\Console\Commands\SyncBansalAppointments',
        '\App\Console\Commands\SendAppointmentReminders',
        '\App\Console\Commands\TestBansalApiConnection',
        '\App\Console\Commands\BackfillBansalAppointments',
        
        // Signature Management System Commands
        '\App\Console\Commands\ArchiveOldDrafts',
        '\App\Console\Commands\SendSignatureReminders',
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
        //$schedule->command('CompleteTaskRemoval:daily')->daily();

        //InPerson Complete Task Removal daily 1 time
        $schedule->command('InPersonCompleteTaskRemoval:daily')->daily();
        //Random client selection in each month at 1st day
        //$schedule->command('RandomClientSelectionReward:monthly')->monthly();
        //visa expire Reminder email before 15 days daily at 1 time
        //$schedule->command('VisaExpireReminderEmail:daily')->daily();
        
        // Lead Follow-up System - Send reminders every hour for upcoming follow-ups
        $schedule->command('followups:send-reminders')->hourly();
        
        // Lead Follow-up System - Mark overdue follow-ups every 15 minutes
        $schedule->command('followups:mark-overdue')->everyFifteenMinutes();
        
        // Appointment Sync System - Sync from Bansal website every 5 minutes (look back 24 hours)
        $schedule->command('booking:sync-appointments --minutes=1440')
            ->everyFiveMinutes()
            ->withoutOverlapping(5) // Max 5 minutes lock time
            ->appendOutputTo(storage_path('logs/appointment-sync.log'));
        
        // Appointment Sync System - Send reminders daily at 9 AM
        $schedule->command('booking:send-reminders')
            ->dailyAt('09:00')
            ->timezone('Australia/Melbourne')
            ->withoutOverlapping(10)
            ->appendOutputTo(storage_path('logs/appointment-reminders.log'));
        
        // Signature Management - Archive old drafts daily at 2 AM
        $schedule->command('signatures:archive-drafts --days=30')
            ->daily()
            ->at('02:00')
            ->timezone('Australia/Melbourne')
            ->appendOutputTo(storage_path('logs/signature-archive.log'));
        
        // Signature Management - Send auto-reminders daily at 10 AM
        /*$schedule->command('signatures:send-auto-reminders --days=7')
            ->daily()
            ->at('10:00')
            ->timezone('Australia/Melbourne')
            ->withoutOverlapping(30)
            ->appendOutputTo(storage_path('logs/signature-auto-reminders.log'));*/
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

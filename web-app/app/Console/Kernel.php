<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $frequency = \App\Models\Setting::getValue('backup_frequency', 'yearly');
        $autoEnabled = \App\Models\Setting::getValue('backup_auto_enabled', false);

        if ($autoEnabled) {
            $command = $schedule->command('backup:run');

            match ($frequency) {
                'daily' => $command->daily(),
                'weekly' => $command->weekly(),
                'monthly' => $command->monthly(),
                'yearly' => $command->yearly(),
                default => $command->yearly(),
            };
        }
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

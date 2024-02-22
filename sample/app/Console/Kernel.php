<?php

namespace App\Console;

use App\Console\Commands\SampleCommand;
use App\Console\Commands\SendOrdersCommand;
use Carbon\CarbonImmutable;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(SampleCommand::class)
            ->description('サンプルタスク')
            ->everyMinute();

        $schedule->command(
            SendOrdersCommand::class,
            [CarbonImmutable::yesterday()->format('Ymd')]
        )
        ->dailyAt('05:00')
        ->description('購入情報の送信')
        ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

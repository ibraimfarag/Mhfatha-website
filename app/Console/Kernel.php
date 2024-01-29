<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\DailyCheckCommand;
use App\Http\Controllers\UserDiscountController;
use App\Models\Discount;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\DailyCheckCommand::class, // Add the DailyCheckCommand class here
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('daily:check')->everyMinute();

        $schedule
            ->call(function () {
                Discount::whereDate('end_date', '<', today())
                    ->where('discounts_status', '!=', 'end')
                    ->update(['discounts_status' => 'end']);
            })
            ->everyMinute();

        // $schedule->call([UserDiscountController::class, 'checkDiscountsExpiration'])->hourly();
    }


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

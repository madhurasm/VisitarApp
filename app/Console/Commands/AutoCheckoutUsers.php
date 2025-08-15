<?php

namespace App\Console\Commands;

use App\Models\VisitorCheckIn;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoCheckoutUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:checkout-users';



    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically check out visitors who checked in more than 24 hours ago';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Cron Start for Checkout '. now());
        $users = VisitorCheckIn::whereNull('check_out')
            ->where('check_in', '<=', Carbon::now()->subDay())
            ->update(['check_out' => now()]);

        Log::info("Checked out {$users} users successfully.");
    }
}

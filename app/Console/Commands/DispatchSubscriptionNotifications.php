<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Subscriber;
use App\Models\User;
use Carbon\Carbon;

class DispatchSubscriptionNotifications extends Command
{


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch subscription expiry notifications';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        $nearEndDate = Carbon::now()->addDays(7); // Get the end date that is near (e.g., within 7 days)

        $subscribers = Subscriber::where('end_date', '<=', $nearEndDate)->get();


        foreach ($subscribers as $subscriber) {
            $user = User::findOrfail($subscriber['user_id']);
            $user->notify(new SubscriptionExpiryNotification());
        }

        $this->info('Subscription expiry notifications dispatched.');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Subscribtion;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // Subscription with monthly payment
        Subscribtion::create([
            'name' => 'Abbonement mensuel',
            'type' => 'mensuel',
            'duration' => 30, // Example duration, adjust as needed
            'price' => 9.99, // Example price, adjust as needed
        ]);

        // Subscription with quarterly payment
        Subscribtion::create([
            'name' => 'Abbonement trimestriel ',
            'type' => 'trimestriel',
            'duration' => 90, // Example duration, adjust as needed
            'price' => 24.99, // Example price, adjust as needed
        ]);

        // Subscription with annual payment
        Subscribtion::create([
            'name' => 'Abbonement annuel ',
            'type' => 'annuel',
            'duration' => '365 ', // Example duration, adjust as needed
            'price' => 89.99, // Example price, adjust as needed

        ]);
    }
}

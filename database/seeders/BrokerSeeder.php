<?php

namespace Database\Seeders;

use App\Models\Broker;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class BrokerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brokers = [
            'NuInvest',
            'Rico',
        ];

        foreach ($brokers as $broker) {
            Broker::create([
                'name' => $broker,
                'identifier' => Str::slug($broker),
            ]);
        }
    }
}

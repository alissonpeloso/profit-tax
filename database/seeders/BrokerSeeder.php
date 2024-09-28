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
            $slug = Str::slug($broker);

            if (Broker::where('identifier', $slug)->exists()) {
                $this->command->warn("Broker with slug {$slug} already exists, skipping...");

                continue;
            }

            Broker::create([
                'name' => $broker,
                'identifier' => Str::slug($broker),
            ]);
        }

        $this->command->info('Brokers seeded successfully!');
    }
}

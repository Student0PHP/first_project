<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    private $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    public function run(): void
    {
        $recordsPerDay = 7;

        for ($daysAgo = 6; $daysAgo >= 0; $daysAgo--) {
            for ($i = 0; $i < $recordsPerDay; $i++) {


                $hours = $this->faker->numberBetween(0, 12);
                $minutes = $this->faker->numberBetween(0, 59);
                $seconds = $this->faker->numberBetween(0, 59);

                $totalTimeFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

                DB::table('orders')->insert([
                    'provider_id' => $this->faker->randomElement([1, 2]),
                    'service_id' => $this->faker->numberBetween(1, 50),
                    'total_time' => $totalTimeFormatted,
                    'earnings' => $this->faker->numberBetween(1, 1200),
                    'status' => $this->faker->randomElement([
                        'created', 'payed', 'started', 'finished', 'confirmed', 'closed', 'canceled'
                    ]),
                    'created_at' => Carbon::today()->subDays($daysAgo),
                    'updated_at' => Carbon::today()->subDays($daysAgo),
                ]);
            }
        }
    }
}

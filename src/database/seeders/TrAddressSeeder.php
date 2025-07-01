<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TrAddressSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CitySeeder::class,
            DistrictSeeder::class,
            SubdistrictSeeder::class,
            NeighborhoodSeeder::class,
            PostcodeSeeder::class,
        ]);
    }
} 
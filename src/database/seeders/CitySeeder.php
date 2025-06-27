<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TrAddress\Models\City;

class CitySeeder extends Seeder
{
    public function run()
    {
        $jsonPath = config('traddress.default_json_path');
        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);

        $this->command->info("Seeding cities...");
        $this->command->getOutput()->progressStart(count($data));

        foreach ($data as $cityData) {
            City::create(['name' => $cityData['name']]);
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info("Cities seeding completed!");
    }
} 
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TrAddress\Models\City;
use TrAddress\Models\District;

class DistrictSeeder extends Seeder
{
    public function run()
    {
        $jsonPath = config('traddress.default_json_path');
        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);

        $total = 0;
        foreach ($data as $cityData) {
            $total += count($cityData['districts']);
        }

        $this->command->info("Seeding districts...");
        $this->command->getOutput()->progressStart($total);

        foreach ($data as $cityData) {
            $city = City::firstOrCreate(['name' => $cityData['name']]);
            foreach ($cityData['districts'] as $districtData) {
                District::create([
                    'city_id' => $city->id,
                    'name' => $districtData['name'],
                ]);
                $this->command->getOutput()->progressAdvance();
            }
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info("Districts seeding completed!");
    }
} 
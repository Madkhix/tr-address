<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TrAddress\Models\City;
use TrAddress\Models\District;
use TrAddress\Models\Neighborhood;

class NeighborhoodSeeder extends Seeder
{
    public function run()
    {
        $jsonPath = config('traddress.default_json_path');
        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);

        $total = 0;
        foreach ($data as $cityData) {
            foreach ($cityData['districts'] as $districtData) {
                $total += count($districtData['neighborhoods']);
            }
        }

        $this->command->info("Seeding neighborhoods...");
        $this->command->getOutput()->progressStart($total);

        foreach ($data as $cityData) {
            $city = City::firstOrCreate(['name' => $cityData['name']]);
            foreach ($cityData['districts'] as $districtData) {
                $district = District::firstOrCreate([
                    'city_id' => $city->id,
                    'name' => $districtData['name'],
                ]);
                foreach ($districtData['neighborhoods'] as $neighborhoodData) {
                    $parts = array_map('trim', explode('/', $neighborhoodData['name']));
                    $neighborhoodName = $parts[0] ?? null;
                    $subdistrict = $parts[1] ?? null;
                    Neighborhood::create([
                        'district_id' => $district->id,
                        'name' => $neighborhoodName,
                        'subdistrict' => $subdistrict,
                    ]);
                    $this->command->getOutput()->progressAdvance();
                }
            }
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info("Neighborhoods seeding completed!");
    }
} 
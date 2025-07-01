<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TrAddress\Models\City;
use TrAddress\Models\District;
use TrAddress\Models\Subdistrict;

class SubdistrictSeeder extends Seeder
{
    public function run()
    {
        $jsonPath = config('traddress.default_json_path');
        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);

        $subdistrictCount = 0;
        foreach ($data as $cityData) {
            $city = City::firstOrCreate(['name' => $cityData['name']]);
            foreach ($cityData['districts'] as $districtData) {
                $district = District::firstOrCreate([
                    'city_id' => $city->id,
                    'name' => $districtData['name'],
                ]);
                foreach ($districtData['neighborhoods'] as $neighborhoodData) {
                    $parts = array_map('trim', explode('/', $neighborhoodData['name']));
                    $subdistrictName = $parts[1] ?? null;
                    if ($subdistrictName) {
                        Subdistrict::firstOrCreate([
                            'district_id' => $district->id,
                            'name' => $subdistrictName,
                        ]);
                        $subdistrictCount++;
                    }
                }
            }
        }
        $this->command->info("$subdistrictCount subdistricts seeded.");
    }
} 
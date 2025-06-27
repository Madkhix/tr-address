<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TrAddress\Models\City;
use TrAddress\Models\District;
use TrAddress\Models\Neighborhood;
use TrAddress\Models\Postcode;

class PostcodeSeeder extends Seeder
{
    public function run()
    {
        $jsonPath = config('traddress.default_json_path');
        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);

        $total = 0;
        foreach ($data as $cityData) {
            foreach ($cityData['districts'] as $districtData) {
                foreach ($districtData['neighborhoods'] as $neighborhoodData) {
                    $total += count($neighborhoodData['postcodes']);
                }
            }
        }

        $this->command->info("Seeding postcodes...");
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
                    $neighborhood = Neighborhood::firstOrCreate([
                        'district_id' => $district->id,
                        'name' => $neighborhoodName,
                        'subdistrict' => $subdistrict,
                    ]);
                    foreach ($neighborhoodData['postcodes'] as $code) {
                        Postcode::create([
                            'neighborhood_id' => $neighborhood->id,
                            'code' => $code,
                        ]);
                        $this->command->getOutput()->progressAdvance();
                    }
                }
            }
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info("Postcodes seeding completed!");
    }
} 
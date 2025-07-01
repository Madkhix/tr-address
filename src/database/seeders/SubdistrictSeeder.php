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

        $subdistrictNames = [];
        foreach ($data as $cityData) {
            foreach ($cityData['districts'] as $districtData) {
                foreach ($districtData['neighborhoods'] as $neighborhoodData) {
                    $parts = array_map('trim', explode('/', $neighborhoodData['name']));
                    $subdistrictName = $parts[1] ?? null;
                    if ($subdistrictName) {
                        $subdistrictNames[$districtData['name'] . '|' . $subdistrictName] = [
                            'district_name' => $districtData['name'],
                            'subdistrict_name' => $subdistrictName,
                        ];
                    }
                }
            }
        }
        $total = count($subdistrictNames);
        $this->command->info("Seeding subdistricts...");
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
                    $subdistrictName = $parts[1] ?? null;
                    if ($subdistrictName) {
                        Subdistrict::firstOrCreate([
                            'district_id' => $district->id,
                            'name' => $subdistrictName,
                        ]);
                        $this->command->getOutput()->progressAdvance();
                    }
                }
            }
        }
        $this->command->getOutput()->progressFinish();
        $this->command->info("Subdistricts seeding completed!");
    }
} 
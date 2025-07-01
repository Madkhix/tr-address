<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TrAddress\Models\City;
use TrAddress\Models\District;
use TrAddress\Models\Neighborhood;
use TrAddress\Models\Subdistrict;

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
                    $neighborhoodName = isset($parts[0]) ? trim($parts[0]) : null;
                    $subdistrictName = isset($parts[1]) ? trim($parts[1]) : null;
                    $subdistrict = null;
                    if ($subdistrictName) {
                        $normalizedSubdistrictName = mb_strtolower($subdistrictName);
                        $subdistrict = \TrAddress\Models\Subdistrict::where('district_id', $district->id)
                            ->whereRaw('LOWER(TRIM(name)) = ?', [$normalizedSubdistrictName])
                            ->first();
                        if (!$subdistrict) {
                            $this->command->warn("Subdistrict not found: '" . $subdistrictName . "' (District: $district->name)");
                        }
                    }
                    \TrAddress\Models\Neighborhood::create([
                        'district_id' => $district->id,
                        'subdistrict_id' => $subdistrict ? $subdistrict->id : null,
                        'name' => $neighborhoodName,
                    ]);
                    $this->command->getOutput()->progressAdvance();
                }
            }
        }

        $this->command->getOutput()->progressFinish();
        $this->command->info("Neighborhoods seeding completed!");
    }
} 
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TrAddress\Models\City;
use TrAddress\Models\District;
use Illuminate\Support\Facades\DB;

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

        $inserted = 0;
        try {
            DB::beginTransaction();
            foreach ($data as $cityData) {
                $city = City::firstOrCreate(['name' => $cityData['name']]);
                foreach ($cityData['districts'] as $districtData) {
                    $districtName = preg_replace('/\s*\(.*?\)/', '', $districtData['name']);
                    District::create([
                        'city_id' => $city->id,
                        'name' => trim($districtName),
                    ]);
                    $inserted++;
                    $this->command->getOutput()->progressAdvance();
                }
            }
            DB::commit();
            $this->command->getOutput()->progressFinish();
            $this->command->info("Districts seeding completed! Total: $inserted");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('District seeding failed: ' . $e->getMessage());
        }
    }
} 
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TrAddress\Models\City;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run()
    {
        $jsonPath = config('traddress.default_json_path');
        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);

        $this->command->info("Seeding cities...");
        $this->command->getOutput()->progressStart(count($data));

        $inserted = 0;
        try {
            DB::beginTransaction();
            foreach ($data as $cityData) {
                $cityName = preg_replace('/\s*\(.*?\)/', '', $cityData['name']);
                City::create(['name' => trim($cityName)]);
                $inserted++;
                $this->command->getOutput()->progressAdvance();
            }
            DB::commit();
            $this->command->getOutput()->progressFinish();
            $this->command->info("Cities seeding completed! Total: $inserted");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('City seeding failed: ' . $e->getMessage());
        }
    }
} 
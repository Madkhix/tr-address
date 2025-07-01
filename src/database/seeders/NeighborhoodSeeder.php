<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TrAddress\Models\City;
use TrAddress\Models\District;
use TrAddress\Models\Neighborhood;
use TrAddress\Models\Subdistrict;
use Illuminate\Support\Facades\DB;

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
                foreach ($districtData['quarters'] as $quarterData) {
                    $total += count($quarterData['neighborhoods']);
                }
            }
        }

        $this->command->info("Seeding neighborhoods...");
        $this->command->getOutput()->progressStart($total);

        $batch = [];
        $batchSize = 500;
        $inserted = 0;
        try {
            DB::beginTransaction();
            foreach ($data as $cityData) {
                $city = City::firstOrCreate(['name' => $cityData['name']]);
                foreach ($cityData['districts'] as $districtData) {
                    $district = District::firstOrCreate([
                        'city_id' => $city->id,
                        'name' => $districtData['name'],
                    ]);
                    foreach ($districtData['quarters'] as $quarterData) {
                        $subdistrict = Subdistrict::firstOrCreate([
                            'district_id' => $district->id,
                            'name' => $quarterData['name'],
                        ]);
                        foreach ($quarterData['neighborhoods'] as $neighborhoodData) {
                            $batch[] = [
                                'district_id' => $district->id,
                                'subdistrict_id' => $subdistrict->id,
                                'name' => $neighborhoodData['name'],
                            ];
                            if (count($batch) >= $batchSize) {
                                Neighborhood::insert($batch);
                                $inserted += count($batch);
                                $this->command->getOutput()->progressAdvance(count($batch));
                                $batch = [];
                            }
                        }
                    }
                }
            }
            if (count($batch) > 0) {
                Neighborhood::insert($batch);
                $inserted += count($batch);
                $this->command->getOutput()->progressAdvance(count($batch));
            }
            DB::commit();
            $this->command->getOutput()->progressFinish();
            $this->command->info("Neighborhoods seeding completed! Total: $inserted");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Neighborhood seeding failed: ' . $e->getMessage());
        }
    }
} 
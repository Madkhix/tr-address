<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TrAddress\Models\City;
use TrAddress\Models\District;
use TrAddress\Models\Neighborhood;
use TrAddress\Models\Postcode;
use TrAddress\Models\Subdistrict;
use Illuminate\Support\Facades\DB;

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
                foreach ($districtData['quarters'] as $quarterData) {
                    $total += count($quarterData['neighborhoods']);
                }
            }
        }

        $this->command->info("Seeding postcodes...");
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
                            $neighborhood = Neighborhood::firstOrCreate([
                                'district_id' => $district->id,
                                'subdistrict_id' => $subdistrict->id,
                                'name' => $neighborhoodData['name'],
                            ]);
                            $batch[] = [
                                'neighborhood_id' => $neighborhood->id,
                                'code' => $neighborhoodData['postcode'],
                            ];
                            if (count($batch) >= $batchSize) {
                                Postcode::insert($batch);
                                $inserted += count($batch);
                                $this->command->getOutput()->progressAdvance(count($batch));
                                $batch = [];
                            }
                        }
                    }
                }
            }
            if (count($batch) > 0) {
                Postcode::insert($batch);
                $inserted += count($batch);
                $this->command->getOutput()->progressAdvance(count($batch));
            }
            DB::commit();
            $this->command->getOutput()->progressFinish();
            $this->command->info("Postcodes seeding completed! Total: $inserted");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Postcode seeding failed: ' . $e->getMessage());
        }
    }
} 
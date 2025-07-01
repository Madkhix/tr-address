<?php

namespace TrAddress\Console\Commands;

use Illuminate\Console\Command;
use TrAddress\Models\City;
use TrAddress\Models\District;
use TrAddress\Models\Neighborhood;
use TrAddress\Models\Postcode;
use TrAddress\Models\Subdistrict;
use Illuminate\Support\Facades\DB;

class ImportTrAddress extends Command
{
    protected $signature = 'traddress:import {json_path}';
    protected $description = 'Import TRAddress JSON address data into the database.';

    public function handle()
    {
        $jsonPath = $this->argument('json_path');
        if (!file_exists($jsonPath)) {
            $this->error("File not found: $jsonPath");
            return 1;
        }
        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);
        if (!$data) {
            $this->error('JSON could not be read or is invalid.');
            return 1;
        }
        $this->info('Starting data import...');
        $cityCount = $districtCount = $subdistrictCount = $neighborhoodCount = $postcodeCount = 0;
        try {
            DB::beginTransaction();
            foreach ($data as $cityData) {
                $city = City::create(['name' => $cityData['name']]);
                $cityCount++;
                foreach ($cityData['districts'] as $districtData) {
                    $district = District::create([
                        'city_id' => $city->id,
                        'name' => $districtData['name'],
                    ]);
                    $districtCount++;
                    foreach ($districtData['quarters'] as $quarterData) {
                        $subdistrict = Subdistrict::create([
                            'district_id' => $district->id,
                            'name' => $quarterData['name'],
                        ]);
                        $subdistrictCount++;
                        foreach ($quarterData['neighborhoods'] as $neighborhoodData) {
                            $neighborhood = Neighborhood::create([
                                'district_id' => $district->id,
                                'subdistrict_id' => $subdistrict->id,
                                'name' => $neighborhoodData['name'],
                            ]);
                            $neighborhoodCount++;
                            Postcode::create([
                                'neighborhood_id' => $neighborhood->id,
                                'code' => $neighborhoodData['postcode'],
                            ]);
                            $postcodeCount++;
                        }
                    }
                }
            }
            DB::commit();
            $this->info("Data import completed!");
            $this->info("Cities: $cityCount, Districts: $districtCount, Subdistricts: $subdistrictCount, Neighborhoods: $neighborhoodCount, Postcodes: $postcodeCount");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Import failed: ' . $e->getMessage());
            return 1;
        }
        return 0;
    }
} 
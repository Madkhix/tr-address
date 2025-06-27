<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use TrAddress\Models\City;
use TrAddress\Models\District;
use TrAddress\Models\Neighborhood;
use TrAddress\Models\Postcode;

class TrAddressSeeder extends Seeder
{
    public function run()
    {
        $jsonPath = config('traddress.default_json_path');
        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);

        foreach ($data as $cityData) {
            $city = City::create(['name' => $cityData['name']]);
            foreach ($cityData['districts'] as $districtData) {
                $district = District::create([
                    'city_id' => $city->id,
                    'name' => $districtData['name'],
                ]);
                foreach ($districtData['neighborhoods'] as $neighborhoodData) {
                    $neighborhood = Neighborhood::create([
                        'district_id' => $district->id,
                        'name' => $neighborhoodData['name'],
                    ]);
                    foreach ($neighborhoodData['postcodes'] as $code) {
                        Postcode::create([
                            'neighborhood_id' => $neighborhood->id,
                            'code' => $code,
                        ]);
                    }
                }
            }
        }
    }
} 
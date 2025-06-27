<?php

namespace TrAddress\Console\Commands;

use Illuminate\Console\Command;
use TrAddress\Models\City;
use TrAddress\Models\District;
use TrAddress\Models\Neighborhood;
use TrAddress\Models\Postcode;

class ImportTrAddress extends Command
{
    protected $signature = 'traddress:import {json_path}';
    protected $description = 'TRAddress JSON adres verisini veritabanına aktarır.';

    public function handle()
    {
        $jsonPath = $this->argument('json_path');
        if (!file_exists($jsonPath)) {
            $this->error("Dosya bulunamadı: $jsonPath");
            return 1;
        }
        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);
        if (!$data) {
            $this->error('JSON okunamadı veya bozuk.');
            return 1;
        }
        $this->info('Veri aktarımı başlıyor...');
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
        $this->info('Veri aktarımı tamamlandı!');
        return 0;
    }
} 
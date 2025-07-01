<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TrAddress\Models\City;
use TrAddress\Models\District;
use TrAddress\Models\Subdistrict;
use Illuminate\Support\Facades\DB;

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
                foreach ($districtData['quarters'] as $quarterData) {
                    $subdistrictNames[$districtData['name'] . '|' . $quarterData['name']] = [
                        'district_name' => $districtData['name'],
                        'subdistrict_name' => $quarterData['name'],
                    ];
                }
            }
        }
        $total = count($subdistrictNames);
        $this->command->info("Seeding subdistricts...");
        $this->command->getOutput()->progressStart($total);
        $inserted = 0;
        try {
            DB::beginTransaction();
            foreach ($subdistrictNames as $key => $info) {
                $district = District::where('name', $info['district_name'])->first();
                if ($district) {
                    Subdistrict::firstOrCreate([
                        'district_id' => $district->id,
                        'name' => $info['subdistrict_name'],
                    ]);
                    $inserted++;
                }
                $this->command->getOutput()->progressAdvance();
            }
            DB::commit();
            $this->command->getOutput()->progressFinish();
            $this->command->info("Subdistricts seeding completed! Total: $inserted");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Subdistrict seeding failed: ' . $e->getMessage());
        }
    }
} 
<?php

namespace TrAddress\Console\Commands;

use Illuminate\Console\Command;

class PublishJsonData extends Command
{
    protected $signature = 'traddress:publish-json';
    protected $description = 'Copy tr-address-data.json from vendor to project root';

    public function handle()
    {
        $source = base_path('vendor/madkhix/tr-address/tr-address-data.json');
        $destination = base_path('tr-address-data.json');

        if (!file_exists($source)) {
            $this->error('Source JSON file not found in vendor directory.');
            return 1;
        }

        if (copy($source, $destination)) {
            $this->info('tr-address-data.json has been copied to your project root.');
            return 0;
        } else {
            $this->error('Failed to copy the JSON file.');
            return 1;
        }
    }
} 
<?php

namespace TrAddress\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ImportTrAddressWithFetch extends Command
{
    protected $signature = 'traddress:import-with-fetch {--json= : JSON dosya yolu (varsayılan: config/traddress.php)}';
    protected $description = 'Python scraper ile veriyi çekip, ardından veritabanına import eder.';

    public function handle()
    {
        $jsonPath = $this->option('json') ?: config('traddress.default_json_path');
        $this->info('Python scraper çalıştırılıyor...');
        $process = new Process(['python', base_path('fetch_tr_address_data.py')]);
        $process->setTimeout(600); // 10 dakika
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $this->info('Scraper tamamlandı. Import başlıyor...');
        $this->call('traddress:import', ['json_path' => $jsonPath]);
        $this->info('Tüm işlem tamamlandı!');
        return 0;
    }
} 
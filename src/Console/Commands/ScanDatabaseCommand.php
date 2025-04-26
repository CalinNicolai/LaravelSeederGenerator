<?php

namespace CalinNicolai\Seedergen\Console\Commands;

use Illuminate\Console\Command;
use CalinNicolai\Seedergen\Services\DatabaseScanner;
use CalinNicolai\Seedergen\Services\ConfigFormatter;
use Illuminate\Support\Facades\File;

class ScanDatabaseCommand extends Command
{
    protected $signature = 'seedergen:scan';
    protected $description = 'Scan database and update seedergen config file.';

    public function __construct(
        protected DatabaseScanner $databaseScanner,
        protected ConfigFormatter $configFormatter,
    )
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $tablesData = $this->databaseScanner->scan();

        if (empty($tablesData)) {
            $this->warn('Database is empty or cannot fetch tables.');
            return;
        }

        $configPath = config_path('seedergen.php');
        $config = File::exists($configPath) ? include $configPath : [];

        $config['database'] = $tablesData;

        File::put($configPath, $this->configFormatter->format($config));

        $this->info('Database tables and relations have been scanned and updated in seedergen config.');
    }
}

<?php

namespace CalinNicolai\Seedergen\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use CalinNicolai\Seedergen\Services\SeederGeneratorService;
use CalinNicolai\Seedergen\Services\DataLoaderService;
use CalinNicolai\Seedergen\Services\ConfigService;
use Random\RandomException;

class GenerateSeederCommand extends Command
{
    protected $signature = 'seedergen:generate
                            {table? : Таблица, для которой нужно создать сидер (по умолчанию все)}
                            {--new : Генерировать новые данные вместо использования существующих}
                            {--count=10 : Кол-во генерируемых строк (только с --new)}';

    protected $description = 'Generate seeders from existing data or config';

    public function __construct(
        protected SeederGeneratorService $seederGenerator,
        protected DataLoaderService      $dataLoader,
        protected ConfigService          $configService
    )
    {
        parent::__construct();
    }

    /**
     * @throws RandomException
     */
    public function handle(): void
    {
        $useNewData = $this->option('new');
        $count = (int)$this->option('count');
        $table = $this->argument('table');

        $tables = $table
            ? [$table]
            : $this->configService->getEnabledTables();

        foreach ($tables as $tableName) {
            $config = $this->configService->getTableConfig($tableName);
            if (!$config || empty($config['enabled'])) {
                continue;
            }

            $data = $useNewData
                ? $this->dataLoader->generateFakeData($tableName, $config, $count)
                : $this->dataLoader->loadFromDatabase($tableName);

            $className = Str::studly($tableName) . 'Seeder';
            $path = database_path("seeders/{$className}.php");

            File::put($path, $this->seederGenerator->generateSeederContent($className, $tableName, $data));

            $this->info("Seeder for table '$tableName' created.");
        }
    }
}

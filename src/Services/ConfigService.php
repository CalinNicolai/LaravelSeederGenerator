<?php

namespace CalinNicolai\Seedergen\Services;

use Illuminate\Support\Facades\File;

class ConfigService
{
    private array $config;

    public function __construct(private ConfigFormatter $configFormatter)
    {
        $this->config = config('seedergen', []);
    }

    public function getEnabledTables(): array
    {
        return array_keys(array_filter($this->config['database'], fn($table) => $table['enabled'] ?? false));
    }

    public function getTableConfig(string $table): ?array
    {
        return $this->config['database'][$table] ?? null;
    }

    public function updateTableConfig(string $table, array $config): void
    {
        $tableRelations = $this->config['database'][$table]['relations'] ?? null;

        $this->config['database'][$table] = $config;

        if ($tableRelations) {
            $this->config['database'][$table]['relations'] = $tableRelations;
        }

        $configPath = config_path('seedergen.php');

        File::put($configPath, $this->configFormatter->format($this->config));
    }
}

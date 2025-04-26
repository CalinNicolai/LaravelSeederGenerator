<?php

namespace CalinNicolai\Seedergen\Services;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class SeederGeneratorService
{
    protected DataFactoryService $dataFactoryService;
    protected ?Command $command;

    public function __construct(DataFactoryService $dataFactoryService, ?Command $command = null)
    {
        $this->dataFactoryService = $dataFactoryService;
        $this->command = $command;
    }

    /**
     * Получить список таблиц в базе данных
     */
    public function getTables(): array
    {
        $tables = DB::select("SHOW TABLES");
        $databaseName = config('database.connections.mysql.database');

        return array_map(fn($table) => $table->{"Tables_in_{$databaseName}"}, $tables);
    }

    /**
     * Генерация содержимого сидера
     */
    public function generateSeederContent(string $className, string $table, array $data): string
    {
        $formattedData = array_map(fn($row) => (array)$row, $data);

        $formattedDataString = $this->formatArray($formattedData);

        return <<<PHP
        <?php

        namespace Database\Seeders;

        use Illuminate\Database\Seeder;
        use Illuminate\Support\Facades\DB;

        class {$className} extends Seeder
        {
            public function run()
            {
                DB::table('{$table}')->insert({$formattedDataString});
            }
        }
        PHP;
    }

    protected function formatArray(array $data): string
    {
        $result = "[\n";

        foreach ($data as $row) {
            if (!is_array($row)) {
                continue;
            }

            $result .= "    [\n";

            foreach ($row as $key => $value) {
                $formattedValue = $this->formatValue($value);
                $result .= "        '{$key}' => {$formattedValue},\n";
            }

            $result .= "    ],\n";
        }

        $result .= "]";
        return $result;
    }

    protected function formatValue(mixed $value): string
    {
        if (is_null($value)) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_int($value) || is_float($value)) {
            return (string)$value;
        }

        if (is_array($value)) {
            // Преобразуем в JSON строку БЕЗ экранирования слэшей и с нормальной кодировкой
            $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            // Экранируем одинарные кавычки внутри JSON строки
            $json = str_replace("'", "\\'", $json);
            return "'{$json}'";
        }

        // Экранируем одинарные кавычки в обычной строке
        return "'" . str_replace("'", "\\'", $value) . "'";
    }
}

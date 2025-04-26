<?php

namespace CalinNicolai\Seedergen\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseScanner
{

    public function __construct(public DataLoaderService $dataLoaderService)
    {
    }

    public
    function scan(): array
    {
        $tables = DB::select('SHOW TABLES');

        if (empty($tables)) {
            return [];
        }

        $tableKey = 'Tables_in_' . env('DB_DATABASE');
        $tableNames = array_map(static fn($table) => $table->$tableKey, $tables);

        $result = [];

        foreach ($tableNames as $table) {
            $result[$table] = $this->analyzeTable($table);
        }

        return $result;
    }

    private function analyzeTable(string $table): array
    {
        $columns = DB::select("SHOW COLUMNS FROM `$table`");

        $fields = [];
        $relations = [];

        foreach ($columns as $column) {
            $fieldName = $column->Field;

            if (in_array($fieldName, config('seedergen.hide_fields'), true)) {
                continue;
            }

            $fieldType = $this->dataLoaderService->normalizeType($column->Type);

            $fieldTypeAttributes = config("field_attributes.$fieldType");

            if ($fieldType === 'json') {
                $fields[$fieldName] = [
                    'type' => $fieldType,
                    'structure' => [
                        'placeholder' => ['type' => 'string'],
                    ],
                ];
            } elseif (!empty($fieldTypeAttributes)) {
                Log::info($fieldType);
                $fieldAttributes = array_fill_keys(array_keys($fieldTypeAttributes), null);

                $fields[$fieldName] = [
                    'type' => $fieldType,
                    ...$fieldAttributes,
                ];
            }

            if (str_ends_with($fieldName, '_id')) {
                $relations[$fieldName] = [
                    'related_table' => substr($fieldName, 0, -3),
                    'type' => 'belongsTo',
                ];
            }
        }

        return [
            'enabled' => "1",
            'fields' => $fields,
            'relations' => $relations,
        ];
    }
}

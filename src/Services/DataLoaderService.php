<?php

namespace CalinNicolai\Seedergen\Services;

use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Faker\Generator as FakerGenerator;
use Random\RandomException;

class DataLoaderService
{
    /**
     * Загружает данные из таблицы базы данных
     */
    public function loadFromDatabase(string $table): array
    {
        return DB::table($table)->get()->toArray();
    }

    /**
     * Генерирует фейковые данные на основе конфигурации и таблицы
     *
     * @throws RandomException
     */
    public function generateFakeData(string $table, array $config, int $count): array
    {
        $faker = Faker::create();
        $rows = [];

        foreach (range(1, $count) as $_) {
            $row = [];

            foreach ($config['fields'] ?? [] as $field => $settings) {
                $settings = is_array($settings) ? $settings : ['type' => $settings];

                $typeRaw = $settings['type'] ?? 'string';
                $type = $this->normalizeType($typeRaw);

                $row[$field] = $this->generateValue($faker, $type, $field, $settings);
            }

            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Нормализует типы из схемы базы данных
     */
    public function normalizeType(string $dbType): string
    {
        $dbType = strtolower($dbType);

        return match (true) {
            str_contains($dbType, 'char'), str_contains($dbType, 'text') => 'string',
            str_contains($dbType, 'name'), str_contains($dbType, 'title') => 'name',
            str_contains($dbType, 'int') => 'integer',
            str_contains($dbType, 'bool') => 'boolean',
            str_contains($dbType, 'json') => 'json',
            str_contains($dbType, 'date') => 'date',
            str_contains($dbType, 'timestamp') => 'timestamp',
            default => 'string',
        };
    }

    /**
     * Генерирует значение для каждого поля
     */
    private function generateValue(FakerGenerator $faker, string $type, string $field, array $options = [])
    {
        if (isset($options['values'])) {
            return $faker->randomElement($options['values']);
        }

        return match ($type) {
            'title', 'name' => $faker->name,
            'string' => $this->generateString($faker, $options),
            'integer' => $this->generateInteger($faker, $options),
            'boolean' => $faker->boolean,
            'json' => $this->generateJson($faker, $options['structure'] ?? []),
            'date' => $faker->date(),
            'timestamp' => $faker->dateTime()->format('Y-m-d H:i:s'),
            default => null,
        };
    }

    /**
     * Генерация строкового значения с учётом настроек
     */
    private function generateString(FakerGenerator $faker, array $options): string
    {
        $minLength = $options['minLength'] ?? 10;
        $maxLength = $options['maxLength'] ?? 500;

        if ($maxLength < 10) {
            $maxLength = 10;
        }

        $minLength = min($minLength, $maxLength);

        if (!empty($options['numeric'])) {
            $digits = '';
            $length = random_int($minLength, $maxLength);

            for ($i = 0; $i < $length; $i++) {
                $digits .= random_int(0, 9);
            }

            $value = $digits;
        } else {
            $value = $faker->realText(random_int($minLength, $maxLength));
        }

        if (isset($options['prefix'])) {
            $value = $options['prefix'] . $value;
        }
        if (isset($options['suffix'])) {
            $value .= $options['suffix'];
        }

        return $value;
    }

    /**
     * Генерация целочисленного значения с учётом настроек
     */
    private function generateInteger(FakerGenerator $faker, array $options): int
    {
        return $faker->numberBetween(
            $options['min'] ?? 0,
            $options['max'] ?? 1000
        );
    }

    /**
     * Генерация JSON-структуры с учётом настроек
     */
    private function generateJson(FakerGenerator $faker, array $structure): array
    {
        $result = [];

        foreach ($structure as $key => $subOptions) {
            $typeRaw = $subOptions['type'] ?? 'string';
            $type = $this->normalizeType($typeRaw);

            $setLocale = $subOptions['locale'] ?? null;

            $faker = $setLocale ? Faker::create($this->getLocaleCode($setLocale)) : $faker;

            $result[$key] = $this->generateValue($faker, $type, $key, $subOptions);
        }

        return $result;
    }

    private function getLocaleCode(string $locale): string
    {
        return $locale . '_' . strtoupper($locale);
    }

}

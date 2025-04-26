<?php

namespace CalinNicolai\Seedergen\Services;

use Faker\Factory as Faker;
use Faker\Generator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Random\RandomException;

class DataFactoryService
{
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * @throws RandomException
     */
    public function generateFieldValue(string $column, string $type): int|string
    {
        return match (true) {
            str_contains($column, 'name') => $this->faker->name,
            str_contains($column, 'email') => $this->faker->unique()->safeEmail,
            str_contains($column, 'phone') => $this->faker->phoneNumber,
            str_contains($column, 'date') => $this->faker->date,
            str_contains($column, 'text') => $this->faker->text,
            $type === 'integer' => random_int(1, 100),
            $type === 'string' => Str::random(10),
            default => $this->faker->word,
        };
    }
}

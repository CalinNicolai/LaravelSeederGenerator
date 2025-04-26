<?php
return [
    'string' => [
        'maxLength' => [
            'type' => 'number',
            'placeholder' => 'Максимальная длина',
        ],
        'prefix' => [
            'type' => 'text',
            'placeholder' => 'Префикс',
        ],
        'suffix' => [
            'type' => 'text',
            'placeholder' => 'Суффикс'
        ],
    ],
    'text' => [
        'maxLength' => [
            'type' => 'number',
            'placeholder' => 'Максимальная длина',
        ],
        'prefix' => [
            'type' => 'text',
            'placeholder' => 'Префикс',
        ],
        'suffix' => [
            'type' => 'text',
            'placeholder' => 'Суффикс'
        ],
    ],
    'integer' => [
        'min' => [
            'type' => 'number',
            'placeholder' => 'Минимум'
        ],
        'max' => [
            'type' => 'number',
            'placeholder' => 'Максимум'
        ],
    ],
    'boolean' => [
        'default' => [
            'type' => 'dropdown',
            'placeholder' => 'По умолчанию',
            'dropdown_options' => [
                'true' => 'True',
                'false' => 'False',
            ],
        ],
    ],
];

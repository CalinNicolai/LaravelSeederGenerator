<?php

namespace CalinNicolai\Seedergen\Fields;

class FieldOption
{
    public function __construct(
        public string $type,
        public string $placeholder,
        public array  $dropdownOptions = []
    )
    {
    }

    public static function number(string $placeholder): self
    {
        return new self('number', $placeholder);
    }

    public static function text(string $placeholder): self
    {
        return new self('text', $placeholder);
    }

    public static function dropdown(string $placeholder, array $options): self
    {
        return new self('dropdown', $placeholder, $options);
    }
}

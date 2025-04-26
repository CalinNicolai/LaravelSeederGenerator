<?php

namespace CalinNicolai\Seedergen\Services;

class ConfigFormatter
{
    public function format(array $config): string
    {
        return "<?php\n\nreturn " . $this->prettyPrint($config) . ";\n";
    }

    private function prettyPrint($value, int $indentLevel = 0): string
    {
        $indent = str_repeat('    ', $indentLevel);

        if (is_array($value)) {
            $indexed = array_keys($value) === range(0, count($value) - 1);

            $lines = ["["];
            foreach ($value as $key => $item) {
                $keyPart = $indexed ? '' : (is_int($key) ? $key : "'" . addslashes($key) . "'") . ' => ';
                $lines[] = $indent . '    ' . $keyPart . $this->prettyPrint($item, $indentLevel + 1) . ",";
            }
            $lines[] = $indent . "]";

            return implode("\n", $lines);
        }

        if (is_string($value)) {
            return "'" . addslashes($value) . "'";
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return 'null';
        }

        return (string)$value;
    }
}

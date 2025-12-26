<?php

namespace App\Traits;

trait KeysCaseConverter
{
    public function convertKeysToCamelCase($data): mixed
    {
        if (is_array($data)) {
            $newData = [];

            foreach ($data as $key => $value) {
                $newKey = self::toCamelCase($key);
                $newData[$newKey] = self::convertKeysToCamelCase($value);
            }

            return $newData;
        }

        if (is_object($data)) {
            $newData = new \stdClass();

            foreach ($data as $key => $value) {
                $newKey = self::toCamelCase($key);
                $newData->$newKey = self::convertKeysToCamelCase($value);
            }

            return $newData;
        }

        return $data;
    }

    private static function toCamelCase(string $string): string
    {
        $str = str_replace(['-', '_'], ' ', $string);
        $str = ucwords($str);
        $str = str_replace(' ', '', $str);
        return lcfirst($str);
    }

    /**
     * Convert array or object keys to snake_case recursively.
     *
     * @param mixed $data
     * @return mixed
     */
    public function convertKeysToSnakeCase(mixed $data): mixed
    {
        if (is_array($data)) {
            $newData = [];

            foreach ($data as $key => $value) {
                $newKey = $this->toSnakeCase($key);
                $newData[$newKey] = $this->convertKeysToSnakeCase($value);
            }

            return $newData;
        }

        if (is_object($data)) {
            $data = (array)$data;
            return $this->convertKeysToSnakeCase($data);
        }

        return $data;
    }

    /**
     * Convert a string to snake_case.
     *
     * @param string $string
     * @return string
     */
    private function toSnakeCase(string $string): string
    {
        if (!ctype_lower($string)) {
            $string = preg_replace('/\s+/', '', $string);
            $string = preg_replace('/(.)(?=[A-Z])/u', '$1_', $string);
            $string = strtolower($string);
        }

        return $string;
    }
}

<?php

namespace StringToColor\Support;

class ColorParser
{
    protected $colors = [];

    public function __construct()
    {
        $this->colors = json_decode(
            file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'colors.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    public function getHex(string $name)
    {
        $name = strtolower(trim($name));

        foreach ($this->colors as $color) {
            if (strtolower($color['name']) === $name) {
                return $color['value'];
            }
        }

        return '';
    }
}

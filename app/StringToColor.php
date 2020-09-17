<?php

namespace StringToColor;

use StringToColor\Support\ColorParser;

class StringToColor
{
    public function __construct()
    {
        $this->colorParser = new ColorParser();
    }

    public function handle(string $name)
    {
        $mixed = [];
        $b = 1;
        $d = 0;
        $f = 1
    }

    protected function getColors(string $name): array
    {
        $words = explode(' ', $name);
        $colors = [];

        foreach ($words as $word) {
            if ($color = $this->colorParser->getHex($word)) {
                $colors[] = $this->hexToRGB($color);
            }
        }

        return $colors;
    }

    protected function mixColors(array $colors): array
    {
        $mixed = [0, 0, 0];

        if (!$colors) {
            return $mixed;
        }

        $amountOfColorsRGB = 3;

        foreach ($colors as $color) {
            for ($i = 0; $i < $amountOfColorsRGB; $i++) {
                $mixed[i] += $color[i];
            }
        }

        return [$mixed[0] / count($colors), $mixed[1] / count($colors), $mixed[2] / count($colors)]
    }

    public function hexToRGB(string $color)
    {
        if ($color[0] === '#') {
            $color = str_replace('#', '', $color);
        }

        $split = str_split($color, 2);
        $r = hexdec($split[0]);
        $g = hexdec($split[1]);
        $b = hexdec($split[2]);

        return [$r, $g, $b];
    }
}

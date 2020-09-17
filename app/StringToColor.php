<?php

namespace StringToColor;

use StringToColor\Support\ColorParser;

class StringToColor
{
    protected const SEED = 16777215;
    protected const TEXT_WEIGHT = 0.25;
    protected const MIXED_WEIGHT = 0.75;
    protected const FACTOR = 49979693;

    public function __construct()
    {
        $this->colorParser = new ColorParser();
    }

    public function handle(string $name)
    {
        $name = json_encode($name);
        $mixed = [];
        $colors = $this->getColors($name);

        if ($colors) {
            $mixed = $this->mixColors($colors);
        }

        $b = 1;
        $d = 0;

        if ($name !== '') {
            for ($i = 0, $iMax = strlen($name); $i < $iMax; $i++) {
                $char = $name[$i];
                $ord = $this->getOrd($char);

                if ($ord > $d) {
                    $d = $ord;
                }

                $f = (int) (static::SEED / $d);
                $b = ($b + $ord * $f * static::FACTOR) % static::SEED;
            }
        }

        $hex = base_convert(($b * strlen($name)) % static::SEED, 10, 16);
        $hex = str_pad($hex, 6, $hex);

        if ($mixed) {
            $rgb = $this->hexToRGB($hex);

            return '#' . $this->rgbToHex([
                    static::TEXT_WEIGHT * $rgb[0] + static::MIXED_WEIGHT * $mixed[0],
                    static::TEXT_WEIGHT * $rgb[1] + static::MIXED_WEIGHT * $mixed[1],
                    static::TEXT_WEIGHT * $rgb[2] + static::MIXED_WEIGHT * $mixed[2],
                ]);
        }

        return '#' . $hex;
    }

    protected function getOrd(string $char)
    {
        [, $ord] = unpack('N', mb_convert_encoding($char, 'UCS-4BE', 'UTF-8'));

        return $ord;
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
                $mixed[$i] += $color[$i];
            }
        }

        return [$mixed[0] / count($colors), $mixed[1] / count($colors), $mixed[2] / count($colors)];
    }

    public function rgbToHex(array $rgb): string
    {
        if (count($rgb) < 3) {
            throw new \LogicException('Invallid rgb passed');
        }

        return sprintf("#%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2]);
    }

    public function hexToRGB(string $color): array
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

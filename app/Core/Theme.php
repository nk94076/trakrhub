<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Setting;

/**
 * Generates a full Tailwind-style 50–900 color ramp from a single admin-
 * chosen hex value (Settings → Theme), so "brand"/"accent" utility classes
 * stay usable everywhere while the actual color is runtime-controlled via
 * CSS custom properties instead of baked into the compiled stylesheet.
 */
final class Theme
{
    private const LIGHT_STOPS = [50 => 0.95, 100 => 0.90, 200 => 0.75, 300 => 0.60, 400 => 0.35];
    private const DARK_STOPS = [600 => 0.20, 700 => 0.35, 800 => 0.50, 900 => 0.65];

    /**
     * Self-hosted font choices (public/assets/fonts/<slug>/). Values are
     * fixed, known-safe font-family strings — never interpolate the raw
     * `typography.*` setting value directly into CSS, since these get
     * embedded in an unescaped <style> block.
     */
    public const FONT_STACKS = [
        'inter' => "'Inter', ui-sans-serif, system-ui, sans-serif",
        'poppins' => "'Poppins', ui-sans-serif, system-ui, sans-serif",
        'sora' => "'Sora', ui-sans-serif, system-ui, sans-serif",
        'plus-jakarta-sans' => "'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif",
    ];

    public static function cssVariables(): string
    {
        $brand = Setting::get('theme', 'primary_color', '#7C3AED');
        $accent = Setting::get('theme', 'accent_color', '#2563EB');

        $headingFont = Setting::get('typography', 'heading_font', 'sora');
        $bodyFont = Setting::get('typography', 'body_font', 'inter');

        $css = ":root{\n";
        $css .= "  --font-heading: " . (self::FONT_STACKS[$headingFont] ?? self::FONT_STACKS['sora']) . ";\n";
        $css .= "  --font-body: " . (self::FONT_STACKS[$bodyFont] ?? self::FONT_STACKS['inter']) . ";\n";

        foreach (['brand' => self::ramp($brand), 'accent' => self::ramp($accent)] as $prefix => $ramp) {
            foreach ($ramp as $stop => $rgb) {
                $css .= "  --{$prefix}-{$stop}: {$rgb};\n";
            }
        }

        $css .= '}';

        return $css;
    }

    /** @return array<int, string> shade => "r g b" */
    public static function ramp(string $hex): array
    {
        [$r, $g, $b] = self::hexToRgb($hex);
        [$h, $s, $l] = self::rgbToHsl($r, $g, $b);

        $ramp = [];

        foreach (self::LIGHT_STOPS as $stop => $fraction) {
            $ramp[$stop] = self::hslToRgbString($h, $s, $l + (1 - $l) * $fraction);
        }

        $ramp[500] = self::hslToRgbString($h, $s, $l);

        foreach (self::DARK_STOPS as $stop => $fraction) {
            $ramp[$stop] = self::hslToRgbString($h, $s, $l - $l * $fraction);
        }

        ksort($ramp);

        return $ramp;
    }

    /** @return array{0:int,1:int,2:int} */
    private static function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        if (strlen($hex) !== 6 || !ctype_xdigit($hex)) {
            $hex = '7C3AED';
        }

        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }

    /** @return array{0:float,1:float,2:float} h/s/l each 0..1 */
    private static function rgbToHsl(int $r, int $g, int $b): array
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;

        if ($max === $min) {
            return [0.0, 0.0, $l];
        }

        $d = $max - $min;
        $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

        $h = match ($max) {
            $r => ($g - $b) / $d + ($g < $b ? 6 : 0),
            $g => ($b - $r) / $d + 2,
            default => ($r - $g) / $d + 4,
        };

        return [$h / 6, $s, $l];
    }

    private static function hslToRgbString(float $h, float $s, float $l): string
    {
        $l = max(0.0, min(1.0, $l));

        if ($s === 0.0) {
            $v = (int) round($l * 255);

            return "{$v} {$v} {$v}";
        }

        $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
        $p = 2 * $l - $q;

        $r = (int) round(self::hueToRgb($p, $q, $h + 1 / 3) * 255);
        $g = (int) round(self::hueToRgb($p, $q, $h) * 255);
        $b = (int) round(self::hueToRgb($p, $q, $h - 1 / 3) * 255);

        return "{$r} {$g} {$b}";
    }

    private static function hueToRgb(float $p, float $q, float $t): float
    {
        if ($t < 0) {
            $t += 1;
        }

        if ($t > 1) {
            $t -= 1;
        }

        if ($t < 1 / 6) {
            return $p + ($q - $p) * 6 * $t;
        }

        if ($t < 1 / 2) {
            return $q;
        }

        if ($t < 2 / 3) {
            return $p + ($q - $p) * (2 / 3 - $t) * 6;
        }

        return $p;
    }
}

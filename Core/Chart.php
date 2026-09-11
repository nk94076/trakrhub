<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Renders small data visualizations as plain inline SVG — no JS charting
 * library, no external requests. Every value is escaped since chart data
 * ultimately comes from admin-entered page_sections JSON.
 */
final class Chart
{
    /**
     * @param array<int, array{label: string, value: float|int}> $points
     */
    public static function bar(array $points, int $width = 560, int $height = 220): string
    {
        if ($points === []) {
            return '';
        }

        $paddingLeft = 8;
        $paddingBottom = 28;
        $paddingTop = 16;
        $chartHeight = $height - $paddingBottom - $paddingTop;
        $chartWidth = $width - ($paddingLeft * 2);

        $max = max(array_column($points, 'value')) ?: 1;
        $count = count($points);
        $gap = 14;
        $barWidth = ($chartWidth - ($gap * ($count - 1))) / $count;

        $bars = '';
        $labels = '';

        foreach ($points as $i => $point) {
            $barHeight = $max > 0 ? ($point['value'] / $max) * $chartHeight : 0;
            $x = $paddingLeft + $i * ($barWidth + $gap);
            $y = $paddingTop + ($chartHeight - $barHeight);
            $rx = min(6, $barWidth / 2);
            $labelX = $x + $barWidth / 2;
            $isLast = $i === $count - 1;
            $fill = $isLast ? 'url(#chartBarActive)' : 'url(#chartBar)';

            $bars .= sprintf(
                '<rect x="%.2f" y="%.2f" width="%.2f" height="%.2f" rx="%.2f" fill="%s"/>',
                $x,
                $y,
                $barWidth,
                max($barHeight, 2),
                $rx,
                $fill
            );

            $labels .= sprintf(
                '<text x="%.2f" y="%d" text-anchor="middle" font-size="11" fill="currentColor" class="text-slate-400" font-family="inherit">%s</text>',
                $labelX,
                $height - 8,
                htmlspecialchars((string) $point['label'], ENT_QUOTES, 'UTF-8')
            );
        }

        $baselineY = $paddingTop + $chartHeight;

        return <<<SVG
<svg viewBox="0 0 {$width} {$height}" class="w-full h-auto" role="img" aria-label="Bar chart">
  <defs>
    <linearGradient id="chartBar" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="rgb(var(--brand-300))"/>
      <stop offset="100%" stop-color="rgb(var(--brand-100))"/>
    </linearGradient>
    <linearGradient id="chartBarActive" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="rgb(var(--brand-500))"/>
      <stop offset="100%" stop-color="rgb(var(--accent-500))"/>
    </linearGradient>
  </defs>
  <line x1="{$paddingLeft}" y1="{$baselineY}" x2="{$width}" y2="{$baselineY}" stroke="currentColor" class="text-slate-100" stroke-width="1"/>
  {$bars}
  {$labels}
</svg>
SVG;
    }

    /**
     * A simple smoothed line/area chart, same data shape as bar().
     */
    public static function line(array $points, int $width = 560, int $height = 220): string
    {
        if ($points === []) {
            return '';
        }

        $paddingLeft = 8;
        $paddingRight = 8;
        $paddingBottom = 28;
        $paddingTop = 16;
        $chartHeight = $height - $paddingBottom - $paddingTop;
        $chartWidth = $width - $paddingLeft - $paddingRight;

        $max = max(array_column($points, 'value')) ?: 1;
        $min = min(array_column($points, 'value'));
        $range = ($max - $min) ?: 1;
        $count = count($points);
        $step = $count > 1 ? $chartWidth / ($count - 1) : 0;

        $coords = [];

        foreach ($points as $i => $point) {
            $x = $paddingLeft + $i * $step;
            $y = $paddingTop + $chartHeight - (($point['value'] - $min) / $range) * $chartHeight;
            $coords[] = [$x, $y];
        }

        $linePath = 'M ' . implode(' L ', array_map(static fn ($c) => sprintf('%.2f %.2f', $c[0], $c[1]), $coords));
        $areaPath = $linePath . sprintf(' L %.2f %d L %.2f %d Z', end($coords)[0], $paddingTop + $chartHeight, $coords[0][0], $paddingTop + $chartHeight);

        $labels = '';

        foreach ($points as $i => $point) {
            $labels .= sprintf(
                '<text x="%.2f" y="%d" text-anchor="middle" font-size="11" fill="currentColor" class="text-slate-400">%s</text>',
                $coords[$i][0],
                $height - 8,
                htmlspecialchars((string) $point['label'], ENT_QUOTES, 'UTF-8')
            );
        }

        $dots = '';

        foreach ($coords as $c) {
            $dots .= sprintf('<circle cx="%.2f" cy="%.2f" r="3.5" fill="rgb(var(--brand-500))" stroke="white" stroke-width="1.5"/>', $c[0], $c[1]);
        }

        return <<<SVG
<svg viewBox="0 0 {$width} {$height}" class="w-full h-auto" role="img" aria-label="Line chart">
  <defs>
    <linearGradient id="lineArea" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%" stop-color="rgb(var(--brand-400))" stop-opacity="0.35"/>
      <stop offset="100%" stop-color="rgb(var(--brand-400))" stop-opacity="0"/>
    </linearGradient>
  </defs>
  <path d="{$areaPath}" fill="url(#lineArea)"/>
  <path d="{$linePath}" fill="none" stroke="rgb(var(--brand-500))" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
  {$dots}
  {$labels}
</svg>
SVG;
    }
}

<?php

declare(strict_types=1);

namespace App\Core;

/**
 * A small library of original, hand-drawn inline SVG icons (24x24,
 * stroke-based line style) so content sections never fall back to an
 * empty placeholder box. Admin-editable content stores an icon *key*
 * (e.g. "shield-check"); this class turns that key into markup at
 * render time. Unknown/missing keys fall back to a generic dot icon
 * rather than rendering nothing.
 */
final class Icon
{
    private const PATHS = [
        'shield-check' => '<path d="M12 3l7 3v6c0 4.5-3 7.5-7 9-4-1.5-7-4.5-7-9V6l7-3z"/><path d="M9 12l2 2 4-4"/>',
        'chart-bar' => '<path d="M5 20V10"/><path d="M12 20V4"/><path d="M19 20v-7"/>',
        'bolt' => '<path d="M13 3L5 14h5l-1 7 8-11h-5l1-7z" stroke-linejoin="round"/>',
        'puzzle-piece' => '<path d="M9 4h4v2.5a1.5 1.5 0 003 0V4h4v4h-2.5a1.5 1.5 0 000 3H20v4h-4v-2.5a1.5 1.5 0 00-3 0V15H9v-4H6.5a1.5 1.5 0 010-3H9V4z"/>',
        'star' => '<path d="M12 3l2.6 5.6 6.1.6-4.5 4.2 1.3 6-5.5-3.1-5.5 3.1 1.3-6-4.5-4.2 6.1-.6L12 3z" stroke-linejoin="round"/>',
        'rocket' => '<path d="M12 3c3 1 5 4 5 8 0 3-1.5 5.5-3 7l-2 2-2-2c-1.5-1.5-3-4-3-7 0-4 2-7 5-8z"/><circle cx="12" cy="10" r="1.5"/><path d="M9 17l-2 3M15 17l2 3"/>',
        'globe' => '<circle cx="12" cy="12" r="8.5"/><path d="M3.5 12h17M12 3.5c2.5 2.3 4 5.3 4 8.5s-1.5 6.2-4 8.5c-2.5-2.3-4-5.3-4-8.5s1.5-6.2 4-8.5z"/>',
        'users' => '<circle cx="9" cy="8" r="3"/><path d="M3.5 19c0-3 2.5-5.5 5.5-5.5s5.5 2.5 5.5 5.5"/><circle cx="17" cy="9" r="2.3"/><path d="M15 13.2c2.3.3 4 2.3 4 4.8"/>',
        'lock-closed' => '<rect x="5.5" y="11" width="13" height="9" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/>',
        'briefcase' => '<rect x="3.5" y="8" width="17" height="11" rx="2"/><path d="M8.5 8V6a2 2 0 012-2h3a2 2 0 012 2v2"/><path d="M3.5 13h17"/>',
        'trending-up' => '<path d="M4 16l6-6 4 4 6-7"/><path d="M20 7h-4M20 7v4"/>',
        'currency-dollar' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7v10M15 9.5c0-1.4-1.3-2.5-3-2.5s-3 1-3 2.3c0 3 6 1.3 6 4.2 0 1.4-1.3 2.5-3 2.5s-3-1-3-2.5"/>',
        'clock' => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7v5l3.5 2"/>',
        'check-circle' => '<circle cx="12" cy="12" r="8.5"/><path d="M8 12.5l2.5 2.5L16 9"/>',
        'cog' => '<circle cx="12" cy="12" r="3"/><path d="M12 3v2.2M12 18.8V21M21 12h-2.2M5.2 12H3M18.4 5.6l-1.5 1.5M7.1 16.9l-1.5 1.5M18.4 18.4l-1.5-1.5M7.1 7.1L5.6 5.6"/>',
        'chat-bubble' => '<path d="M4 5.5h16v10H9l-4 3.5v-3.5H4v-10z"/>',
        'academic-cap' => '<path d="M12 4l9 4.5-9 4.5-9-4.5 9-4.5z" stroke-linejoin="round"/><path d="M6.5 10.7v4.3c0 1.4 2.5 3 5.5 3s5.5-1.6 5.5-3v-4.3"/>',
        'shopping-bag' => '<path d="M6 8h12l1 12H5L6 8z"/><path d="M9 8V6a3 3 0 016 0v2"/>',
        'paper-airplane' => '<path d="M4 12l16-8-6 16-2.5-6.5L4 12z" stroke-linejoin="round"/>',
        'heart' => '<path d="M12 20s-7-4.5-9.3-9A5 5 0 0112 6.5 5 5 0 0121.3 11c-2.3 4.5-9.3 9-9.3 9z"/>',
        'device-phone' => '<rect x="7" y="3" width="10" height="18" rx="2"/><path d="M11 18h2"/>',
        'truck' => '<rect x="2.5" y="8" width="12" height="8" rx="1"/><path d="M14.5 11h3l3 3v2h-6v-5z"/><circle cx="7" cy="18" r="1.6"/><circle cx="17" cy="18" r="1.6"/>',
        'building-storefront' => '<path d="M4 9l1-4h14l1 4"/><path d="M4 9h16v10H4V9z"/><path d="M9 19v-5h6v5"/>',
        'sparkles' => '<path d="M12 3l1.2 3.8L17 8l-3.8 1.2L12 13l-1.2-3.8L7 8l3.8-1.2L12 3z"/><path d="M19 14l.6 1.9L21.5 16.5l-1.9.6L19 19l-.6-1.9-1.9-.6 1.9-.6L19 14z"/>',
        'tv' => '<rect x="3" y="5" width="18" height="12" rx="2"/><path d="M9 20h6"/>',
        'building-office' => '<rect x="4" y="4" width="16" height="16" rx="1"/><path d="M8 8h1.5M8 12h1.5M8 16h1.5M14.5 8H16M14.5 12H16M14.5 16H16"/>',
        'squares' => '<rect x="3.5" y="3.5" width="7" height="7" rx="1.2"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.2"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.2"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.2"/>',
        'document' => '<path d="M7 3.5h7l4 4v13h-11v-17z"/><path d="M14 3.5v4h4"/><path d="M9.5 13h5M9.5 16.5h5"/>',
        'photo' => '<rect x="3.5" y="4.5" width="17" height="15" rx="2"/><circle cx="9" cy="10" r="1.6"/><path d="M4 17l5-5 3.5 3.5L16 12l4 4"/>',
        'link' => '<path d="M9.5 14.5l5-5"/><path d="M11 6.5l1.4-1.4a4 4 0 015.5 5.7L16.5 12.2"/><path d="M13 17.5l-1.4 1.4a4 4 0 01-5.5-5.7L7.5 11.8"/>',
        'pencil-square' => '<path d="M4 20l1-4.5L15.5 5 19 8.5 8.5 19 4 20z"/><path d="M13.5 6.5L17.5 10.5"/>',
        'envelope' => '<rect x="3.5" y="5.5" width="17" height="13" rx="2"/><path d="M4 6.5l8 6.5 8-6.5"/>',
        'arrow-path' => '<path d="M4.5 12a7.5 7.5 0 0113-5M4.5 12a7.5 7.5 0 0013 5"/><path d="M17 3.5v3.5h-3.5M7 20.5V17h3.5"/>',
        'magnifying-glass' => '<circle cx="10.5" cy="10.5" r="6.5"/><path d="M19.5 19.5l-4.5-4.5"/>',
        'question-mark-circle' => '<circle cx="12" cy="12" r="8.5"/><path d="M9.5 9.3a2.5 2.5 0 014.9.7c0 1.7-2.4 1.7-2.4 3.5"/><circle cx="12" cy="16.3" r="0.15" fill="currentColor"/>',
    ];

    private const DEFAULT_KEY = 'star';

    public static function render(?string $key, string $classes = 'w-6 h-6'): string
    {
        $path = self::PATHS[$key ?? ''] ?? self::PATHS[self::DEFAULT_KEY];

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
            . 'stroke-width="1.6" stroke-linecap="round" class="' . htmlspecialchars($classes, ENT_QUOTES, 'UTF-8') . '">'
            . $path . '</svg>';
    }

    /** @return string[] All valid icon keys, for admin <select> pickers. */
    public static function keys(): array
    {
        return array_keys(self::PATHS);
    }
}

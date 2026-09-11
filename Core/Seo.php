<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Setting;

/**
 * JSON-LD schema.org generators + shared SEO meta resolution. Covers the
 * common, always-present schema types in code (Organization, WebSite,
 * Breadcrumb, FAQPage, Article); anything more specific (Product, Video,
 * Person, LocalBusiness, ...) is admin-editable per-entity via the
 * seo_meta.schema_json passthrough rather than hardcoding every variant.
 */
final class Seo
{
    public static function organizationSchema(): array
    {
        $logo = Setting::get('branding', 'logo', '');

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => Setting::get('business', 'company_name', Setting::get('branding', 'site_name', '')),
            'url' => View::url('/'),
            'logo' => $logo !== '' ? View::url(ltrim((string) $logo, '/')) : null,
            'email' => Setting::get('business', 'email', '') ?: null,
            'telephone' => Setting::get('business', 'phone', '') ?: null,
            'address' => Setting::get('business', 'address', '') ?: null,
            'sameAs' => array_values(array_filter([
                Setting::get('social', 'linkedin_url', ''),
                Setting::get('social', 'twitter_url', ''),
                Setting::get('social', 'instagram_url', ''),
            ])),
        ]);
    }

    public static function websiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => Setting::get('branding', 'site_name', ''),
            'url' => View::url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => View::url('search') . '?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /** @param array<int, array{name: string, url: string}> $items */
    public static function breadcrumbSchema(array $items): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_values(array_map(
                static fn (int $index, array $item) => [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item['name'],
                    'item' => $item['url'],
                ],
                array_keys($items),
                $items
            )),
        ];
    }

    /** @param array<int, array{question: string, answer: string}> $faqs */
    public static function faqSchema(array $faqs): ?array
    {
        if ($faqs === []) {
            return null;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(
                static fn (array $faq) => [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => strip_tags($faq['answer']),
                    ],
                ],
                $faqs
            ),
        ];
    }

    public static function articleSchema(array $post): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post['title'],
            'description' => $post['excerpt'] ?? null,
            'datePublished' => $post['published_at'] ?? null,
            'dateModified' => $post['updated_at'] ?? $post['published_at'] ?? null,
            'author' => !empty($post['author_name']) ? [
                '@type' => 'Person',
                'name' => $post['author_name'],
            ] : null,
            'publisher' => [
                '@type' => 'Organization',
                'name' => Setting::get('branding', 'site_name', ''),
            ],
            'mainEntityOfPage' => View::url('blog/' . $post['slug']),
        ]);
    }

    /** Renders one or more JSON-LD script tags, escaped safely for HTML output. */
    public static function render(array $schemas): string
    {
        $html = '';

        foreach (array_filter($schemas) as $schema) {
            $html .= '<script type="application/ld+json">'
                . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG)
                . '</script>' . "\n";
        }

        return $html;
    }
}

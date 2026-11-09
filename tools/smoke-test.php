<?php

/**
 * Read-only frontend smoke test for the configured local site.
 *
 * Usage: php tools/smoke-test.php
 */
if ('cli' !== PHP_SAPI) {
    exit(1);
}

$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'archdome.test';
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';

require dirname(__DIR__, 4) . '/wp/wp-load.php';

if (! function_exists('pll_home_url')) {
    fwrite(STDERR, "Polylang must be active before running smoke tests.\n");
    exit(1);
}

/** Find one published translated page by its path. */
function archdome_smoke_page_url(string $path, string $language): string
{
    $page = get_page_by_path($path, OBJECT, 'page');

    if (! $page instanceof WP_Post) {
        return '';
    }

    $translation = pll_get_post($page->ID, $language);

    return $translation ? (string) get_permalink($translation) : '';
}

$routes = [
    'home-ar' => [pll_home_url('ar'), 200, 'rtl', 'ar'],
    'home-en' => [pll_home_url('en'), 200, 'ltr', 'en-US'],
    'developments-ar' => [home_url('/developments/'), 200],
    'developments-en' => [trailingslashit(pll_home_url('en')) . 'developments/', 200],
    'properties-ar' => [home_url('/properties/'), 200],
    'properties-en' => [trailingslashit(pll_home_url('en')) . 'properties/', 200],
    'about-ar' => [archdome_smoke_page_url('about', 'ar'), 200],
    'about-en' => [archdome_smoke_page_url('about', 'en'), 200],
    'locations-ar' => [archdome_smoke_page_url('locations', 'ar'), 200],
    'locations-en' => [archdome_smoke_page_url('locations', 'en'), 200],
    'insights-ar' => [archdome_smoke_page_url('insights', 'ar'), 200],
    'insights-en' => [archdome_smoke_page_url('insights', 'en'), 200],
    'contact-ar' => [archdome_smoke_page_url('contact', 'ar'), 200],
    'contact-en' => [archdome_smoke_page_url('contact', 'en'), 200],
    'search' => [home_url('/?s=archdome'), 200, null, null, true],
    'filtered-results' => [home_url('/properties/?bedrooms_min=2&sort=price_asc'), 200, null, null, true],
    'nested-input' => [home_url('/properties/?sort%5B%5D=bad&bedrooms_min%5B%5D=2'), 200],
    'not-found' => [home_url('/archdome-smoke-missing-page/'), 404],
];

$development = get_posts([
    'post_type' => 'development',
    'post_status' => 'publish',
    'posts_per_page' => 1,
    'fields' => 'ids',
    'suppress_filters' => true,
]);

if ($development) {
    $routes['development-single'] = [get_permalink($development[0]), 200];
}

foreach (['ar', 'en'] as $content_language) {
    foreach (['development', 'property'] as $post_type) {
        $records = get_posts([
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'fields' => 'ids',
            'lang' => $content_language,
            'meta_key' => '_archdome_demo_record',
            'meta_value' => '1',
            'suppress_filters' => false,
        ]);

        if ($records) {
            $routes["demo-$post_type-single-$content_language"] = [get_permalink($records[0]), 200];
        }
    }

    $property_count = new WP_Query([
        'post_type' => 'property',
        'post_status' => 'publish',
        'posts_per_page' => 12,
        'fields' => 'ids',
        'lang' => $content_language,
        'meta_key' => '_archdome_demo_record',
        'meta_value' => '1',
    ]);

    if ($property_count->max_num_pages > 1) {
        $base = $content_language === 'en' ? trailingslashit(pll_home_url('en')) : home_url('/');
        $routes["properties-page-2-$content_language"] = [$base . 'properties/page/2/', 200];
    }
}

$failures = [];

foreach ($routes as $name => $route) {
    [$url, $expected_status, $direction, $language, $expect_noindex] = array_pad($route, 5, null);
    if (! $url) {
        $failures[] = "$name has no URL";

        continue;
    }

    $response = wp_remote_get($url, [
        'redirection' => 0,
        'timeout' => 15,
        'user-agent' => 'Archdome local smoke test',
    ]);

    if (is_wp_error($response)) {
        $failures[] = "$name request failed: {$response->get_error_message()}";

        continue;
    }

    $status = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);

    if ($expected_status !== $status) {
        $failures[] = "$name returned $status; expected $expected_status";

        continue;
    }

    if ($status === 200 && preg_match_all('/<h1(?:\s[^>]*)?>/i', $body) !== 1) {
        $failures[] = "$name does not render exactly one H1";
    }

    if (str_starts_with($name, 'home-') && preg_match_all('/<section(?=[^>]*\balignfull\b)(?=[^>]*\bwp-block-archdome-)[^>]*>/i', $body) !== 7) {
        $failures[] = "$name does not render all seven homepage sections at full width";
    }

    if (preg_match('/(?:Fatal error|Uncaught (?:Error|Exception)|Warning:|Notice:).*?\.php/i', $body)) {
        $failures[] = "$name exposes a PHP runtime error";
    }

    if ($direction && ! preg_match('/<html[^>]*\bdir=["\']' . preg_quote($direction, '/') . '["\']/i', $body)) {
        $failures[] = "$name has an incorrect document direction";
    }

    if ($language && ! preg_match('/<html[^>]*\blang=["\']' . preg_quote($language, '/') . '["\']/i', $body)) {
        $failures[] = "$name has an incorrect document language";
    }

    if ($expect_noindex && ! preg_match('/<meta\s+name=["\']robots["\'][^>]*content=["\'][^"\']*noindex/i', $body)) {
        $failures[] = "$name is missing a noindex directive";
    }

    printf("PASS %-22s %d %s\n", $name, $status, $url);
}

if ($failures) {
    foreach ($failures as $failure) {
        fwrite(STDERR, "FAIL $failure\n");
    }

    exit(1);
}

echo "All frontend smoke tests passed.\n";

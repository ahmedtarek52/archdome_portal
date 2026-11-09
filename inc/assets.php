<?php

/**
 * Frontend asset registration.
 */
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Return a cache-safe local asset version.
 *
 * @param  string  $relative_path  Theme-relative asset path.
 */
function archdome_asset_version(string $relative_path): string
{
    $path = get_theme_file_path($relative_path);

    if (wp_get_environment_type() === 'development' && file_exists($path)) {
        return (string) filemtime($path);
    }

    $theme = wp_get_theme();

    return (string) $theme->get('Version');
}

/**
 * Register reusable theme assets before block registration.
 */
function archdome_register_assets(): void
{
    wp_register_style(
        'archdome-cards',
        get_theme_file_uri('/assets/css/components/cards.css'),
        [],
        archdome_asset_version('/assets/css/components/cards.css'),
    );

    wp_register_style(
        'archdome-filters',
        get_theme_file_uri('/assets/css/components/filters.css'),
        [],
        archdome_asset_version('/assets/css/components/filters.css'),
    );
}
add_action('init', 'archdome_register_assets', 8);

/**
 * Enqueue public theme assets.
 */
function archdome_enqueue_assets(): void
{

    if (is_rtl()) {
        wp_enqueue_style(
            'archdome-arabic-fonts',
            'https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;500;600;700&family=Noto+Naskh+Arabic:wght@400;500;600;700&display=swap',
            [],
            null,
        );
    }

    wp_enqueue_style(
        'archdome-portal',
        get_theme_file_uri('/assets/css/main.css'),
        [],
        archdome_asset_version('/assets/css/main.css'),
    );

    if (is_post_type_archive(['development', 'property']) || is_singular(['development', 'property']) || is_tax() || is_search()) {
        wp_enqueue_style('archdome-cards');
    }

    if (is_post_type_archive('property')) {
        wp_enqueue_style('archdome-filters');
    }

    if (has_nav_menu('primary')) {
        wp_enqueue_script(
            'archdome-navigation',
            get_theme_file_uri('/assets/js/navigation.js'),
            [],
            archdome_asset_version('/assets/js/navigation.js'),
            [
                'in_footer' => true,
                'strategy' => 'defer',
            ],
        );
    }
}
add_action('wp_enqueue_scripts', 'archdome_enqueue_assets');

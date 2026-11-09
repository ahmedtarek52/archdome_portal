<?php

/**
 * Native Gutenberg block registration.
 */
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Return the approved custom block directory names.
 *
 * @return string[]
 */
function archdome_block_allowlist(): array
{
    return [
        'hero',
        'about',
        'featured-developments',
        'development-grid',
        'featured-properties',
        'property-grid',
        'property-search',
        'locations',
        'statistics',
        'amenities',
        'gallery',
        'master-plan',
        'testimonials',
        'insights',
        'map-location',
        'call-to-action',
        'inquiry',
    ];
}

/**
 * Register every implemented block from its metadata.
 */
function archdome_register_blocks(): void
{
    foreach (archdome_block_allowlist() as $block_name) {
        $block_directory = get_theme_file_path('/blocks/' . $block_name);

        if (! file_exists($block_directory . '/block.json')) {
            continue;
        }

        register_block_type($block_directory);
    }
}
add_action('init', 'archdome_register_blocks', 20);

/**
 * Add a focused editor category for Archdome components.
 *
 * @param  array<int, array<string, string>>  $categories  Existing categories.
 * @return array<int, array<string, string>>
 */
function archdome_block_categories(array $categories): array
{
    $category = [
        'slug' => 'archdome',
        'title' => esc_html__('Archdome', 'archdome-portal'),
        'icon' => 'building',
    ];

    array_unshift($categories, $category);

    return $categories;
}
add_filter('block_categories_all', 'archdome_block_categories');

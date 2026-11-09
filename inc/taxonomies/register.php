<?php

/**
 * Real-estate taxonomy registration.
 */
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Build translated taxonomy labels.
 *
 * @param  string  $singular  Singular label.
 * @param  string  $plural  Plural label.
 * @return array<string, string>
 */
function archdome_taxonomy_labels(string $singular, string $plural): array
{
    return [
        'name' => $plural,
        'singular_name' => $singular,
        'search_items' => sprintf(
            /* translators: %s: Plural taxonomy label. */
            esc_html__('Search %s', 'archdome-portal'),
            $plural,
        ),
        'all_items' => sprintf(
            /* translators: %s: Plural taxonomy label. */
            esc_html__('All %s', 'archdome-portal'),
            $plural,
        ),
        'edit_item' => sprintf(
            /* translators: %s: Singular taxonomy label. */
            esc_html__('Edit %s', 'archdome-portal'),
            $singular,
        ),
        'update_item' => sprintf(
            /* translators: %s: Singular taxonomy label. */
            esc_html__('Update %s', 'archdome-portal'),
            $singular,
        ),
        'add_new_item' => sprintf(
            /* translators: %s: Singular taxonomy label. */
            esc_html__('Add new %s', 'archdome-portal'),
            $singular,
        ),
        'new_item_name' => sprintf(
            /* translators: %s: Singular taxonomy label. */
            esc_html__('New %s name', 'archdome-portal'),
            $singular,
        ),
        'menu_name' => $plural,
        'not_found' => esc_html__('No terms found.', 'archdome-portal'),
        'back_to_items' => sprintf(
            /* translators: %s: Plural taxonomy label. */
            esc_html__('Back to %s', 'archdome-portal'),
            $plural,
        ),
        'item_link' => sprintf(
            /* translators: %s: Singular taxonomy label. */
            esc_html__('%s link', 'archdome-portal'),
            $singular,
        ),
        'item_link_description' => sprintf(
            /* translators: %s: Singular taxonomy label. */
            esc_html__('A link to a %s.', 'archdome-portal'),
            $singular,
        ),
    ];
}

/**
 * Register taxonomies shared by developments and properties.
 */
function archdome_register_taxonomies(): void
{
    $taxonomies = [
        'location' => [
            'objects' => ['development', 'property'],
            'singular' => esc_html__('Location', 'archdome-portal'),
            'plural' => esc_html__('Locations', 'archdome-portal'),
            'hierarchical' => true,
            'slug' => 'location',
        ],
        'property_type' => [
            'objects' => ['property'],
            'singular' => esc_html__('Property type', 'archdome-portal'),
            'plural' => esc_html__('Property types', 'archdome-portal'),
            'hierarchical' => true,
            'slug' => 'property-type',
        ],
        'amenity' => [
            'objects' => ['development', 'property'],
            'singular' => esc_html__('Amenity', 'archdome-portal'),
            'plural' => esc_html__('Amenities', 'archdome-portal'),
            'hierarchical' => false,
            'slug' => 'amenity',
        ],
        'property_status' => [
            'objects' => ['property'],
            'singular' => esc_html__('Property status', 'archdome-portal'),
            'plural' => esc_html__('Property statuses', 'archdome-portal'),
            'hierarchical' => false,
            'slug' => 'property-status',
        ],
        'development_type' => [
            'objects' => ['development'],
            'singular' => esc_html__('Development type', 'archdome-portal'),
            'plural' => esc_html__('Development types', 'archdome-portal'),
            'hierarchical' => true,
            'slug' => 'development-type',
        ],
        'development_status' => [
            'objects' => ['development'],
            'singular' => esc_html__('Development status', 'archdome-portal'),
            'plural' => esc_html__('Development statuses', 'archdome-portal'),
            'hierarchical' => false,
            'slug' => 'development-status',
        ],
    ];

    foreach ($taxonomies as $taxonomy => $config) {
        register_taxonomy(
            $taxonomy,
            $config['objects'],
            [
                'labels' => archdome_taxonomy_labels($config['singular'], $config['plural']),
                'public' => true,
                'show_in_rest' => true,
                'show_admin_column' => true,
                'hierarchical' => $config['hierarchical'],
                'rewrite' => [
                    'slug' => $config['slug'],
                    'with_front' => false,
                ],
            ],
        );
    }
}
add_action('init', 'archdome_register_taxonomies', 6);

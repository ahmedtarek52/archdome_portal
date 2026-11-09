<?php

/**
 * Real-estate post type registration.
 */
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Register the development and property content types.
 */
function archdome_register_post_types(): void
{
    register_post_type(
        'development',
        [
            'labels' => [
                'name' => esc_html__('Developments', 'archdome-portal'),
                'singular_name' => esc_html__('Development', 'archdome-portal'),
                'menu_name' => esc_html__('Developments', 'archdome-portal'),
                'name_admin_bar' => esc_html__('Development', 'archdome-portal'),
                'add_new' => esc_html__('Add development', 'archdome-portal'),
                'add_new_item' => esc_html__('Add new development', 'archdome-portal'),
                'new_item' => esc_html__('New development', 'archdome-portal'),
                'edit_item' => esc_html__('Edit development', 'archdome-portal'),
                'view_item' => esc_html__('View development', 'archdome-portal'),
                'all_items' => esc_html__('All developments', 'archdome-portal'),
                'search_items' => esc_html__('Search developments', 'archdome-portal'),
                'not_found' => esc_html__('No developments found.', 'archdome-portal'),
                'not_found_in_trash' => esc_html__('No developments found in Trash.', 'archdome-portal'),
                'featured_image' => esc_html__('Development image', 'archdome-portal'),
                'set_featured_image' => esc_html__('Set development image', 'archdome-portal'),
                'remove_featured_image' => esc_html__('Remove development image', 'archdome-portal'),
                'use_featured_image' => esc_html__('Use as development image', 'archdome-portal'),
                'archives' => esc_html__('Development archives', 'archdome-portal'),
            ],
            'public' => true,
            'show_in_rest' => true,
            'has_archive' => true,
            'rewrite' => [
                'slug' => 'developments',
                'with_front' => false,
            ],
            'menu_icon' => 'dashicons-building',
            'menu_position' => 20,
            'supports' => [
                'title',
                'editor',
                'excerpt',
                'thumbnail',
                'revisions',
                'author',
            ],
            'show_in_nav_menus' => true,
            'delete_with_user' => false,
            'template' => [],
        ],
    );

    register_post_type(
        'property',
        [
            'labels' => [
                'name' => esc_html__('Properties', 'archdome-portal'),
                'singular_name' => esc_html__('Property', 'archdome-portal'),
                'menu_name' => esc_html__('Properties', 'archdome-portal'),
                'name_admin_bar' => esc_html__('Property', 'archdome-portal'),
                'add_new' => esc_html__('Add property', 'archdome-portal'),
                'add_new_item' => esc_html__('Add new property', 'archdome-portal'),
                'new_item' => esc_html__('New property', 'archdome-portal'),
                'edit_item' => esc_html__('Edit property', 'archdome-portal'),
                'view_item' => esc_html__('View property', 'archdome-portal'),
                'all_items' => esc_html__('All properties', 'archdome-portal'),
                'search_items' => esc_html__('Search properties', 'archdome-portal'),
                'not_found' => esc_html__('No properties found.', 'archdome-portal'),
                'not_found_in_trash' => esc_html__('No properties found in Trash.', 'archdome-portal'),
                'featured_image' => esc_html__('Property image', 'archdome-portal'),
                'set_featured_image' => esc_html__('Set property image', 'archdome-portal'),
                'remove_featured_image' => esc_html__('Remove property image', 'archdome-portal'),
                'use_featured_image' => esc_html__('Use as property image', 'archdome-portal'),
                'archives' => esc_html__('Property archives', 'archdome-portal'),
            ],
            'public' => true,
            'show_in_rest' => true,
            'has_archive' => true,
            'rewrite' => [
                'slug' => 'properties',
                'with_front' => false,
            ],
            'menu_icon' => 'dashicons-admin-home',
            'menu_position' => 21,
            'supports' => [
                'title',
                'editor',
                'excerpt',
                'thumbnail',
                'revisions',
                'author',
            ],
            'show_in_nav_menus' => true,
            'delete_with_user' => false,
            'template' => [],
        ],
    );
}
add_action('init', 'archdome_register_post_types', 5);

/**
 * Flush rewrite rules once when the theme is activated.
 */
function archdome_flush_rewrite_rules(): void
{
    archdome_register_post_types();
    archdome_register_taxonomies();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'archdome_flush_rewrite_rules');

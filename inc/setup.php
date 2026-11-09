<?php

/**
 * Theme setup and supported WordPress features.
 */
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Configure theme defaults and register supported features.
 */
function archdome_setup(): void
{
    load_theme_textdomain('archdome-portal', get_theme_file_path('/languages'));

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor.css');

    add_theme_support(
        'html5',
        [
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'script',
            'style',
            'navigation-widgets',
        ],
    );

    add_theme_support(
        'custom-logo',
        [
            'height' => 120,
            'width' => 360,
            'flex-height' => true,
            'flex-width' => true,
            'unlink-homepage-logo' => true,
        ],
    );

    register_nav_menus(
        [
            'primary' => esc_html__('Primary navigation', 'archdome-portal'),
            'footer' => esc_html__('Footer navigation', 'archdome-portal'),
        ],
    );

    add_image_size('archdome-card', 960, 720, true);
    add_image_size('archdome-hero', 1920, 1080, true);
}
add_action('after_setup_theme', 'archdome_setup');

/**
 * Set the default content width for embeds and media.
 */
function archdome_set_content_width(): void
{
    $GLOBALS['content_width'] = apply_filters('archdome_content_width', 960);
}
add_action('after_setup_theme', 'archdome_set_content_width', 0);

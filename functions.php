<?php

/**
 * Archdome Portal theme bootstrap.
 */
if (! defined('ABSPATH')) {
    exit;
}

$archdome_modules = [
    '/inc/setup.php',
    '/inc/assets.php',
    '/inc/helpers.php',
    '/inc/localization.php',
    '/inc/queries.php',
    '/inc/seo.php',
    '/inc/post-types/register.php',
    '/inc/taxonomies/register.php',
    '/inc/meta/register.php',
    '/inc/admin/meta-boxes.php',
    '/inc/admin/settings.php',
    '/inc/blocks/register.php',
];

foreach ($archdome_modules as $archdome_module) {
    require_once get_theme_file_path($archdome_module);
}

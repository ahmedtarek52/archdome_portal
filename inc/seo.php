<?php

/**
 * Small SEO safeguards that do not compete with a future SEO plugin.
 */
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Prevent low-value internal result/filter combinations from being indexed.
 *
 * Public canonical archives and single records remain indexable. An approved
 * SEO plugin may add its own directives without the theme emitting duplicate
 * canonical or social metadata.
 *
 * @param  array<string, bool|string>  $robots  Existing directives.
 * @return array<string, bool|string>
 */
function archdome_filter_robots(array $robots): array
{
    $has_archive_filters = is_post_type_archive(['development', 'property']) && (bool) archdome_archive_filter_args();

    if (is_search() || $has_archive_filters) {
        $robots['noindex'] = true;
        unset($robots['index']);
    }

    return $robots;
}
add_filter('wp_robots', 'archdome_filter_robots');

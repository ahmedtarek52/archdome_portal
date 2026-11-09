<?php

/**
 * Public archive query rules.
 */
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Return a scalar public query value, rejecting nested input.
 */
function archdome_request_scalar(string $key): string
{
    if (! isset($_GET[$key]) || ! is_scalar($_GET[$key])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        return '';
    }

    return wp_unslash((string) $_GET[$key]); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}

/**
 * Apply bounded, allowlisted real-estate archive filters.
 */
function archdome_filter_archive_queries(WP_Query $query): void
{
    if (is_admin() || ! $query->is_main_query()) {
        return;
    }

    if ($query->is_post_type_archive('development')) {
        $query->set('posts_per_page', 12);
        archdome_apply_taxonomy_filters(
            $query,
            [
                'development_location' => 'location',
                'development_type' => 'development_type',
                'development_status' => 'development_status',
            ],
        );
    }

    if ($query->is_post_type_archive('property')) {
        $query->set('posts_per_page', 12);
        archdome_apply_taxonomy_filters(
            $query,
            [
                'property_location' => 'location',
                'property_type' => 'property_type',
                'property_status' => 'property_status',
            ],
        );

        $bedrooms_min = min(20, absint(archdome_request_scalar('bedrooms_min')));

        if ($bedrooms_min) {
            $query->set(
                'meta_query',
                [
                    [
                        'key' => '_archdome_bedrooms',
                        'value' => $bedrooms_min,
                        'compare' => '>=',
                        'type' => 'NUMERIC',
                    ],
                ],
            );
        }

        $sort = sanitize_key(archdome_request_scalar('sort')) ?: 'newest';

        if (in_array($sort, ['price_asc', 'price_desc'], true)) {
            $query->set('meta_key', '_archdome_price');
            $query->set('orderby', 'meta_value_num');
            $query->set('order', $sort === 'price_asc' ? 'ASC' : 'DESC');
        }
    }
}
add_action('pre_get_posts', 'archdome_filter_archive_queries');

/**
 * Add validated taxonomy query clauses.
 *
 * @param  WP_Query  $query  Main query.
 * @param  array<string, string>  $filters  Query parameter to taxonomy map.
 */
function archdome_apply_taxonomy_filters(WP_Query $query, array $filters): void
{
    $tax_query = [];

    foreach ($filters as $parameter => $taxonomy) {
        $term_id = absint(archdome_request_scalar($parameter));

        if ($term_id && term_exists($term_id, $taxonomy)) {
            $tax_query[] = [
                'taxonomy' => $taxonomy,
                'field' => 'term_id',
                'terms' => [$term_id],
            ];
        }
    }

    if ($tax_query) {
        $tax_query['relation'] = 'AND';
        $query->set('tax_query', $tax_query);
    }
}

/**
 * Return safe active archive arguments for pagination and reset UI.
 *
 * @return array<string, int|string>
 */
function archdome_archive_filter_args(): array
{
    $allowed = ['development_location', 'development_type', 'development_status', 'property_location', 'property_type', 'property_status', 'bedrooms_min', 'sort'];
    $args = [];

    foreach ($allowed as $key) {
        $value = archdome_request_scalar($key);

        if ($value === '') {
            continue;
        }

        $args[$key] = $key === 'sort' ? sanitize_key($value) : absint($value);
    }

    return array_filter($args, static fn(int|string $value): bool => $value !== '' && $value !== 0);
}

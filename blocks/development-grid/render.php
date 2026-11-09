<?php

if (! defined('ABSPATH')) {
    exit;
}

$tax_query = [];
$filters = [
    'location' => absint($attributes['locationId'] ?? 0),
    'development_type' => absint($attributes['typeId'] ?? 0),
    'development_status' => absint($attributes['statusId'] ?? 0),
];

foreach ($filters as $taxonomy => $term_id) {
    if ($term_id && term_exists($term_id, $taxonomy)) {
        $tax_query[] = ['taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => [$term_id]];
    }
}

if (count($tax_query) > 1) {
    $tax_query['relation'] = 'AND';
}

$args = [
    'post_type' => 'development',
    'post_status' => 'publish',
    'posts_per_page' => min(12, max(1, absint($attributes['limit'] ?? 6))),
    'ignore_sticky_posts' => true,
    'no_found_rows' => true,
];

if ($tax_query) {
    $args['tax_query'] = $tax_query;
}

$query = new WP_Query($args);
$wrapper = get_block_wrapper_attributes(['class' => 'archdome-collection archdome-development-grid']);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>>
    <div class="site-container">
        <?php if (! empty($attributes['heading'])) { ?><h2 class="archdome-collection__title"><?php echo esc_html($attributes['heading']); ?></h2><?php } ?>
        <?php if ($query->have_posts()) { ?>
            <div class="archdome-card-grid">
                <?php while ($query->have_posts()) {
                    $query->the_post(); ?><?php get_template_part('template-parts/cards/development'); ?><?php } ?>
            </div>
        <?php } else { ?>
            <p class="archdome-collection__empty"><?php esc_html_e('No developments match the selected criteria.', 'archdome-portal'); ?></p>
        <?php } ?>
    </div>
</section>
<?php wp_reset_postdata(); ?>

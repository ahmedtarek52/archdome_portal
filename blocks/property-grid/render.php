<?php
if (! defined('ABSPATH')) {
    exit;
}
$tax_query = [];
foreach (['location' => 'locationId', 'property_type' => 'typeId', 'property_status' => 'statusId'] as $taxonomy => $attribute) {
    $term_id = absint($attributes[$attribute] ?? 0);
    if ($term_id && term_exists($term_id, $taxonomy)) {
        $tax_query[] = ['taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => [$term_id]];
    }
}
if (count($tax_query) > 1) {
    $tax_query['relation'] = 'AND';
}
$args = ['post_type' => 'property', 'post_status' => 'publish', 'posts_per_page' => min(12, max(1, absint($attributes['limit'] ?? 6))), 'no_found_rows' => true];
if ($tax_query) {
    $args['tax_query'] = $tax_query;
}
$development_id = archdome_sanitize_development_id($attributes['developmentId'] ?? 0);
if ($development_id) {
    $args['meta_query'] = [['key' => '_archdome_development_id', 'value' => $development_id, 'compare' => '=', 'type' => 'NUMERIC']];
}
$query = new WP_Query($args);
$wrapper = get_block_wrapper_attributes(['class' => 'archdome-collection archdome-property-grid']);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>><div class="site-container">
<?php if (! empty($attributes['heading'])) { ?><h2 class="archdome-collection__title"><?php echo esc_html($attributes['heading']); ?></h2><?php } ?>
<?php if ($query->have_posts()) { ?><div class="archdome-card-grid"><?php while ($query->have_posts()) {
    $query->the_post();
    get_template_part('template-parts/cards/property');
} ?></div>
<?php } else { ?><p class="archdome-collection__empty"><?php esc_html_e('No properties match the selected criteria.', 'archdome-portal'); ?></p><?php } ?>
</div></section><?php wp_reset_postdata(); ?>

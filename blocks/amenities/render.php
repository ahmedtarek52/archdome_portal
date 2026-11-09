<?php
if (! defined('ABSPATH')) {
    exit;
}
$term_ids = array_slice(array_values(array_filter(array_map('absint', $attributes['termIds'] ?? []))), 0, 30);
$post_id = absint($block->context['postId'] ?? get_the_ID());
$terms = $term_ids ? get_terms(['taxonomy' => 'amenity', 'include' => $term_ids, 'hide_empty' => false]) : get_the_terms($post_id, 'amenity');
if (is_wp_error($terms)) {
    $terms = [];
}
$wrapper = get_block_wrapper_attributes(['class' => 'archdome-amenities']);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>><div class="site-container">
<?php if (! empty($attributes['heading'])) { ?><h2><?php echo esc_html($attributes['heading']); ?></h2><?php } ?>
<?php if ($terms) { ?><ul class="archdome-amenities__list"><?php foreach ($terms as $term) { ?><li><?php echo esc_html($term->name); ?></li><?php } ?></ul>
<?php } else { ?><p><?php esc_html_e('No amenities are available.', 'archdome-portal'); ?></p><?php } ?>
</div></section>

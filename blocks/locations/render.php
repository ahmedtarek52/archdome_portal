<?php
if (! defined('ABSPATH')) {
    exit;
}
$terms = get_terms(['taxonomy' => 'location', 'hide_empty' => true, 'number' => min(24, max(1, absint($attributes['limit'] ?? 6)))]);
$wrapper = get_block_wrapper_attributes(['class' => 'archdome-locations']);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>><div class="site-container">
<?php if (! empty($attributes['heading'])) { ?><h2><?php echo esc_html($attributes['heading']); ?></h2><?php } ?>
<?php if (! is_wp_error($terms) && $terms) { ?><div class="archdome-locations__grid"><?php foreach ($terms as $term) {
    $url = get_term_link($term);
    if (is_wp_error($url)) {
        continue;
    } ?><article class="location-card"><p class="location-card__count"><?php printf(esc_html(_n('%s listing', '%s listings', $term->count, 'archdome-portal')), esc_html(number_format_i18n($term->count))); ?></p><h3><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($term->name); ?></a></h3><?php if ($term->description) { ?><p><?php echo esc_html(wp_trim_words($term->description, 22)); ?></p><?php } ?></article><?php } ?></div>
<?php } else { ?><p><?php esc_html_e('No locations are available yet.', 'archdome-portal'); ?></p><?php } ?>
</div></section>

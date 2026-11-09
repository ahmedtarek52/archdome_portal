<?php
if (! defined('ABSPATH')) {
    exit;
}
$post_id = absint($block->context['postId'] ?? get_the_ID());
$latitude = archdome_sanitize_latitude(get_post_meta($post_id, '_archdome_latitude', true));
$longitude = archdome_sanitize_longitude(get_post_meta($post_id, '_archdome_longitude', true));
$locations = $post_id ? get_the_terms($post_id, 'location') : [];
if (empty($attributes['address']) && ! $latitude && (! is_array($locations) || ! $locations)) {
    return;
}
$wrapper = get_block_wrapper_attributes(['class' => 'archdome-map-location']);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>><div class="site-container archdome-map-location__inner"><div>
<?php if (! empty($attributes['heading'])) { ?><h2><?php echo esc_html($attributes['heading']); ?></h2><?php } ?>
<?php if (is_array($locations) && $locations) { ?><p><?php echo esc_html(implode(', ', wp_list_pluck($locations, 'name'))); ?></p><?php } ?>
<?php if (! empty($attributes['address'])) { ?><p><?php echo nl2br(esc_html($attributes['address'])); ?></p><?php } ?>
</div><?php if ($latitude && $longitude) { ?><dl class="archdome-map-location__coordinates" dir="ltr"><div><dt><?php esc_html_e('Latitude', 'archdome-portal'); ?></dt><dd><?php echo esc_html($latitude); ?></dd></div><div><dt><?php esc_html_e('Longitude', 'archdome-portal'); ?></dt><dd><?php echo esc_html($longitude); ?></dd></div></dl><?php } ?></div></section>

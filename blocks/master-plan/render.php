<?php
if (! defined('ABSPATH')) {
    exit;
}
$media_id = archdome_sanitize_attachment_id($attributes['mediaId'] ?? 0);
$post_id = absint($block->context['postId'] ?? get_the_ID());
if (! $media_id && $post_id) {
    $media_id = archdome_sanitize_attachment_id(get_post_meta($post_id, '_archdome_master_plan_id', true));
}
if (! $media_id && get_post_type($post_id) === 'property') {
    $media_id = archdome_sanitize_attachment_id(get_post_meta($post_id, '_archdome_floor_plan_id', true));
}
if (! $media_id) {
    return;
}
$wrapper = get_block_wrapper_attributes(['class' => 'archdome-master-plan']);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>><div class="site-container">
<?php if (! empty($attributes['heading'])) { ?><h2><?php echo esc_html($attributes['heading']); ?></h2><?php } ?>
<figure><a href="<?php echo esc_url(wp_get_attachment_url($media_id)); ?>" aria-label="<?php esc_attr_e('Open the full-size plan', 'archdome-portal'); ?>"><?php echo wp_get_attachment_image($media_id, 'large'); ?></a><?php if (wp_get_attachment_caption($media_id)) { ?><figcaption><?php echo esc_html(wp_get_attachment_caption($media_id)); ?></figcaption><?php } ?></figure>
</div></section>

<?php
if (! defined('ABSPATH')) {
    exit;
}
$contact_url = archdome_contact_url();
$post_id = absint($block->context['postId'] ?? get_the_ID());
$wrapper = get_block_wrapper_attributes(['class' => 'archdome-inquiry']);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>><div class="site-container archdome-inquiry__inner"><div>
<?php if (! empty($attributes['heading'])) { ?><h2><?php echo esc_html($attributes['heading']); ?></h2><?php } ?>
<?php if (! empty($attributes['text'])) { ?><p><?php echo esc_html($attributes['text']); ?></p><?php } ?>
</div><?php if ($contact_url && ! empty($attributes['buttonLabel'])) { ?><a class="button" href="<?php echo esc_url(add_query_arg(['context' => $post_id], $contact_url)); ?>"><?php echo esc_html($attributes['buttonLabel']); ?></a><?php } ?></div></section>

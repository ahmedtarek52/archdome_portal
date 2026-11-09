<?php
if (! defined('ABSPATH')) {
    exit;
}
$items = array_slice(is_array($attributes['items'] ?? null) ? $attributes['items'] : [], 0, 6);
$items = array_filter($items, static fn(mixed $item): bool => is_array($item) && ! empty($item['quote']));
if (! $items) {
    return;
}
$wrapper = get_block_wrapper_attributes(['class' => 'archdome-testimonials']);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>><div class="site-container">
<?php if (! empty($attributes['heading'])) { ?><h2><?php echo esc_html($attributes['heading']); ?></h2><?php } ?>
<div class="archdome-testimonials__grid"><?php foreach ($items as $item) { ?><figure><blockquote><p><?php echo esc_html($item['quote']); ?></p></blockquote><?php if (! empty($item['name'])) { ?><figcaption><strong><?php echo esc_html($item['name']); ?></strong><?php if (! empty($item['role'])) { ?><span><?php echo esc_html($item['role']); ?></span><?php } ?></figcaption><?php } ?></figure><?php } ?></div>
</div></section>

<?php

if (! defined('ABSPATH')) {
    exit;
}

$limit = min(6, max(1, absint($attributes['limit'] ?? 3)));
$args = [
    'post_type' => 'development',
    'post_status' => 'publish',
    'posts_per_page' => $limit,
    'ignore_sticky_posts' => true,
    'no_found_rows' => true,
    'meta_key' => '_archdome_featured',
    'meta_value' => '1',
];
$query = new WP_Query($args);

if (! $query->have_posts()) {
    unset($args['meta_key'], $args['meta_value']);
    $query = new WP_Query($args);
}

$wrapper = get_block_wrapper_attributes(['class' => 'archdome-collection archdome-featured-developments']);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>>
    <div class="site-container">
        <?php if (! empty($attributes['heading']) || (! empty($attributes['linkLabel']) && ! empty($attributes['linkUrl']))) { ?>
            <header class="archdome-collection__header">
                <?php if (! empty($attributes['heading'])) { ?><h2 class="archdome-collection__title"><?php echo esc_html($attributes['heading']); ?></h2><?php } ?>
                <?php if (! empty($attributes['linkLabel']) && ! empty($attributes['linkUrl'])) { ?><a href="<?php echo esc_url($attributes['linkUrl']); ?>"><?php echo esc_html($attributes['linkLabel']); ?></a><?php } ?>
            </header>
        <?php } ?>
        <?php if ($query->have_posts()) { ?>
            <div class="archdome-card-grid">
                <?php while ($query->have_posts()) {
                    $query->the_post(); ?>
                    <?php get_template_part('template-parts/cards/development'); ?>
                <?php } ?>
            </div>
        <?php } else { ?>
            <p class="archdome-collection__empty"><?php esc_html_e('No developments are available yet.', 'archdome-portal'); ?></p>
        <?php } ?>
    </div>
</section>
<?php wp_reset_postdata(); ?>

<?php

/**
 * Insights block renderer.
 */
if (! defined('ABSPATH')) {
    exit;
}

$limit = min(12, max(1, absint($attributes['limit'] ?? 3)));
$args = [
    'post_type' => 'post',
    'post_status' => 'publish',
    'posts_per_page' => $limit,
    'ignore_sticky_posts' => true,
    'no_found_rows' => true,
];

if (! empty($attributes['categoryId'])) {
    $args['cat'] = absint($attributes['categoryId']);
}

$insights = new WP_Query($args);
$wrapper = get_block_wrapper_attributes(['class' => 'archdome-insights']);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>>
    <div class="site-container">
        <?php if (! empty($attributes['heading'])) { ?>
            <h2 class="archdome-insights__title"><?php echo esc_html($attributes['heading']); ?></h2>
        <?php } ?>
        <?php if ($insights->have_posts()) { ?>
            <div class="archdome-insights__grid">
                <?php while ($insights->have_posts()) {
                    $insights->the_post(); ?>
                    <article <?php post_class('archdome-insight-card'); ?>>
                        <?php if (has_post_thumbnail()) { ?>
                            <a class="archdome-insight-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail('archdome-card'); ?></a>
                        <?php } ?>
                        <div class="archdome-insight-card__body">
                            <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time>
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <?php if (! empty($attributes['showExcerpt'])) { ?>
                                <div class="archdome-insight-card__excerpt"><?php the_excerpt(); ?></div>
                            <?php } ?>
                        </div>
                    </article>
                <?php } ?>
            </div>
        <?php } else { ?>
            <p class="archdome-insights__empty"><?php esc_html_e('No insights are available yet.', 'archdome-portal'); ?></p>
        <?php } ?>
    </div>
</section>
<?php wp_reset_postdata(); ?>

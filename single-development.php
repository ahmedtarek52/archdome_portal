<?php

/**
 * Development detail template.
 */
get_header();

while (have_posts()) {
    the_post();
    $development_id = get_the_ID();
    $locations = get_the_terms($development_id, 'location');
    $statuses = get_the_terms($development_id, 'development_status');
    $amenities = get_the_terms($development_id, 'amenity');
    $stats = archdome_sanitize_key_stats(get_post_meta($development_id, '_archdome_key_stats', true));
    $gallery_ids = archdome_sanitize_id_list(get_post_meta($development_id, '_archdome_gallery_ids', true));
    $master_plan_id = archdome_sanitize_attachment_id(get_post_meta($development_id, '_archdome_master_plan_id', true));
    $brochure_id = archdome_sanitize_attachment_id(get_post_meta($development_id, '_archdome_brochure_id', true));
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('detail-page detail-page--development'); ?>>
        <header class="detail-hero<?php echo has_post_thumbnail() ? ' has-media' : ''; ?>">
            <?php if (has_post_thumbnail()) { ?><div class="detail-hero__media" aria-hidden="true"><?php the_post_thumbnail('archdome-hero', ['loading' => 'eager', 'fetchpriority' => 'high', 'alt' => '']); ?></div><div class="detail-hero__overlay" aria-hidden="true"></div><?php } ?>
            <div class="detail-hero__inner site-container">
                <?php if ((is_array($locations) && $locations) || (is_array($statuses) && $statuses)) { ?><div class="detail-hero__status-bar">
                    <?php if (is_array($locations) && $locations) { ?><span><?php echo esc_html($locations[0]->name); ?></span><?php } ?>
                    <?php if (is_array($statuses) && $statuses) { ?><span class="detail-hero__status"><?php echo esc_html($statuses[0]->name); ?></span><?php } ?>
                </div><?php } ?>
                <h1><?php the_title(); ?></h1>
                <?php if (has_excerpt()) { ?><div class="detail-hero__summary"><?php the_excerpt(); ?></div><?php } ?>
            </div>
        </header>

        <div class="site-container detail-content">
            <?php if (trim((string) get_the_content())) { ?><section class="detail-section detail-section--reading"><h2><?php esc_html_e('Overview', 'archdome-portal'); ?></h2><div class="entry__content"><?php the_content(); ?></div></section><?php } ?>

            <?php if ($stats) { ?><section class="detail-section"><h2><?php esc_html_e('Key statistics', 'archdome-portal'); ?></h2><dl class="detail-stats"><?php foreach ($stats as $stat) { ?><div><dd><?php echo esc_html($stat['value']); ?></dd><dt><?php echo esc_html($stat['label']); ?></dt></div><?php } ?></dl></section><?php } ?>

            <?php if ($gallery_ids) { ?><section class="detail-section"><h2><?php esc_html_e('Gallery', 'archdome-portal'); ?></h2><div class="detail-gallery"><?php foreach ($gallery_ids as $gallery_id) { ?><figure><?php echo wp_get_attachment_image($gallery_id, 'large'); ?></figure><?php } ?></div></section><?php } ?>

            <?php
            $properties = new WP_Query([
                'post_type' => 'property', 'post_status' => 'publish', 'posts_per_page' => 6, 'no_found_rows' => true,
                'meta_query' => [['key' => '_archdome_development_id', 'value' => $development_id, 'compare' => '=', 'type' => 'NUMERIC']],
            ]);
    if ($properties->have_posts()) { ?>
                <section class="detail-section"><h2><?php esc_html_e('Available properties', 'archdome-portal'); ?></h2><div class="archdome-card-grid"><?php while ($properties->have_posts()) {
                    $properties->the_post();
                    get_template_part('template-parts/cards/property');
                } ?></div></section>
            <?php }
    wp_reset_postdata(); ?>

            <?php if (is_array($amenities) && $amenities) { ?><section class="detail-section"><h2><?php esc_html_e('Amenities', 'archdome-portal'); ?></h2><ul class="term-list"><?php foreach ($amenities as $amenity) { ?><li><?php echo esc_html($amenity->name); ?></li><?php } ?></ul></section><?php } ?>

            <?php if ($master_plan_id) { ?><section class="detail-section"><h2><?php esc_html_e('Master plan', 'archdome-portal'); ?></h2><figure class="detail-plan"><a href="<?php echo esc_url(wp_get_attachment_url($master_plan_id)); ?>" aria-label="<?php esc_attr_e('Open the full-size master plan', 'archdome-portal'); ?>"><?php echo wp_get_attachment_image($master_plan_id, 'large'); ?></a></figure></section><?php } ?>

            <?php if ($brochure_id) { ?><p><a class="button" href="<?php echo esc_url(wp_get_attachment_url($brochure_id)); ?>"><?php esc_html_e('Download brochure', 'archdome-portal'); ?></a></p><?php } ?>
        </div>
        <?php archdome_render_detail_endcap('development'); ?>
    </article>
<?php }
get_footer(); ?>

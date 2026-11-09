<?php

/**
 * Property detail template.
 */
get_header();

while (have_posts()) {
    the_post();
    $property_id = get_the_ID();
    $price = archdome_property_price_label($property_id);
    $development_id = archdome_sanitize_development_id(get_post_meta($property_id, '_archdome_development_id', true));
    $gallery_ids = archdome_sanitize_id_list(get_post_meta($property_id, '_archdome_gallery_ids', true));
    $floor_plan_id = archdome_sanitize_attachment_id(get_post_meta($property_id, '_archdome_floor_plan_id', true));
    $amenities = get_the_terms($property_id, 'amenity');
    $locations = get_the_terms($property_id, 'location');
    $statuses = get_the_terms($property_id, 'property_status');
    $bedrooms = absint(get_post_meta($property_id, '_archdome_bedrooms', true));
    $bathrooms = archdome_sanitize_non_negative_decimal(get_post_meta($property_id, '_archdome_bathrooms', true));
    $area = archdome_sanitize_non_negative_decimal(get_post_meta($property_id, '_archdome_area', true));
    $area_unit = archdome_sanitize_area_unit(get_post_meta($property_id, '_archdome_area_unit', true));
    $reference = sanitize_text_field(get_post_meta($property_id, '_archdome_reference', true));
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('detail-page detail-page--property'); ?>>
        <header class="property-header site-container">
            <?php if ((is_array($locations) && $locations) || (is_array($statuses) && $statuses)) { ?><div class="property-header__status-bar">
                <?php if (is_array($locations) && $locations) { ?><span class="property-header__location"><?php echo esc_html($locations[0]->name); ?></span><?php } ?>
                <?php if (is_array($statuses) && $statuses) { ?><span class="property-header__status"><?php echo esc_html($statuses[0]->name); ?></span><?php } ?>
            </div><?php } ?>
            <div>
                <h1><?php the_title(); ?></h1>
                <?php if ($development_id) { ?><p class="property-header__development"><?php esc_html_e('Part of', 'archdome-portal'); ?> <a href="<?php echo esc_url(get_permalink($development_id)); ?>"><?php echo esc_html(get_the_title($development_id)); ?></a></p><?php } ?>
            </div>
            <?php if ($price) { ?><p class="property-header__price" dir="ltr"><?php echo esc_html($price); ?></p><?php } ?>
        </header>

        <?php if (has_post_thumbnail()) { ?><figure class="property-featured site-container"><?php the_post_thumbnail('archdome-hero', ['loading' => 'eager', 'fetchpriority' => 'high']); ?></figure><?php } ?>

        <div class="site-container detail-content">
            <?php if ($bedrooms || $bathrooms || $area || $reference) { ?><section class="detail-section" aria-labelledby="property-specifications"><h2 id="property-specifications"><?php esc_html_e('Specifications', 'archdome-portal'); ?></h2><dl class="property-specs">
                <?php if ($bedrooms) { ?><div><dt><?php esc_html_e('Bedrooms', 'archdome-portal'); ?></dt><dd><?php echo esc_html(number_format_i18n($bedrooms)); ?></dd></div><?php } ?>
                <?php if ($bathrooms) { ?><div><dt><?php esc_html_e('Bathrooms', 'archdome-portal'); ?></dt><dd><?php echo esc_html($bathrooms); ?></dd></div><?php } ?>
                <?php if ($area) { ?><div><dt><?php esc_html_e('Area', 'archdome-portal'); ?></dt><dd dir="ltr"><?php echo esc_html($area . ' ' . $area_unit); ?></dd></div><?php } ?>
                <?php if ($reference) { ?><div><dt><?php esc_html_e('Reference', 'archdome-portal'); ?></dt><dd dir="ltr"><?php echo esc_html($reference); ?></dd></div><?php } ?>
            </dl></section><?php } ?>

            <?php if (trim((string) get_the_content())) { ?><section class="detail-section detail-section--reading"><h2><?php esc_html_e('About this property', 'archdome-portal'); ?></h2><div class="entry__content"><?php the_content(); ?></div></section><?php } ?>
            <?php if ($gallery_ids) { ?><section class="detail-section"><h2><?php esc_html_e('Gallery', 'archdome-portal'); ?></h2><div class="detail-gallery"><?php foreach ($gallery_ids as $gallery_id) { ?><figure><?php echo wp_get_attachment_image($gallery_id, 'large'); ?></figure><?php } ?></div></section><?php } ?>
            <?php if (is_array($amenities) && $amenities) { ?><section class="detail-section"><h2><?php esc_html_e('Amenities', 'archdome-portal'); ?></h2><ul class="term-list"><?php foreach ($amenities as $amenity) { ?><li><?php echo esc_html($amenity->name); ?></li><?php } ?></ul></section><?php } ?>
            <?php if ($floor_plan_id) { ?><section class="detail-section"><h2><?php esc_html_e('Floor plan', 'archdome-portal'); ?></h2><figure class="detail-plan"><a href="<?php echo esc_url(wp_get_attachment_url($floor_plan_id)); ?>" aria-label="<?php esc_attr_e('Open the full-size floor plan', 'archdome-portal'); ?>"><?php echo wp_get_attachment_image($floor_plan_id, 'large'); ?></a></figure></section><?php } ?>

            <?php
            $related_args = ['post_type' => 'property', 'post_status' => 'publish', 'posts_per_page' => 3, 'post__not_in' => [$property_id], 'no_found_rows' => true];
    if ($development_id) {
        $related_args['meta_query'] = [['key' => '_archdome_development_id', 'value' => $development_id, 'compare' => '=', 'type' => 'NUMERIC']];
    }
    $related = new WP_Query($related_args);
    if ($related->have_posts()) { ?><section class="detail-section"><h2><?php esc_html_e('Related properties', 'archdome-portal'); ?></h2><div class="archdome-card-grid"><?php while ($related->have_posts()) {
        $related->the_post();
        get_template_part('template-parts/cards/property');
    } ?></div></section><?php }
    wp_reset_postdata(); ?>
        </div>
        <?php archdome_render_detail_endcap('property'); ?>
    </article>
<?php }
get_footer(); ?>

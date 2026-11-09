<?php

/**
 * Property card.
 */
if (! defined('ABSPATH')) {
    exit;
}

$locations = get_the_terms(get_the_ID(), 'location');
$location = is_array($locations) && $locations ? $locations[0]->name : '';
$statuses = get_the_terms(get_the_ID(), 'property_status');
$status = is_array($statuses) && $statuses ? $statuses[0]->name : '';
$price = archdome_property_price_label(get_the_ID());
$bedrooms = absint(get_post_meta(get_the_ID(), '_archdome_bedrooms', true));
$bathrooms = archdome_sanitize_non_negative_decimal(get_post_meta(get_the_ID(), '_archdome_bathrooms', true));
$area = archdome_sanitize_non_negative_decimal(get_post_meta(get_the_ID(), '_archdome_area', true));
$area_unit = archdome_sanitize_area_unit(get_post_meta(get_the_ID(), '_archdome_area_unit', true));
?>
<article <?php post_class('archdome-card archdome-card--property'); ?>>
    <?php if (has_post_thumbnail()) { ?>
        <div class="archdome-card__media" aria-hidden="true"><?php the_post_thumbnail('archdome-card'); ?></div>
    <?php } else { ?>
        <div class="archdome-card__placeholder" aria-hidden="true"><?php esc_html_e('Image unavailable', 'archdome-portal'); ?></div>
    <?php } ?>
    <?php if ($location || $status) { ?><div class="archdome-card__status-bar">
        <?php if ($location) { ?><span class="archdome-card__location"><?php echo esc_html($location); ?></span><?php } ?>
        <?php if ($status) { ?><span class="archdome-card__status"><?php echo esc_html($status); ?></span><?php } ?>
    </div><?php } ?>
    <div class="archdome-card__body">
        <h3 class="archdome-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <?php if ($price) { ?><p class="archdome-card__price" dir="ltr"><?php echo esc_html($price); ?></p><?php } ?>
        <?php if ($bedrooms || $bathrooms || $area) { ?>
            <ul class="archdome-card__facts" aria-label="<?php esc_attr_e('Property details', 'archdome-portal'); ?>">
                <?php if ($bedrooms) { ?><li><?php printf(esc_html__('%d bedrooms', 'archdome-portal'), $bedrooms); ?></li><?php } ?>
                <?php if ($bathrooms) { ?><li><?php printf(esc_html__('%s bathrooms', 'archdome-portal'), esc_html($bathrooms)); ?></li><?php } ?>
                <?php if ($area) { ?><li dir="ltr"><?php echo esc_html($area . ' ' . $area_unit); ?></li><?php } ?>
            </ul>
        <?php } ?>
    </div>
</article>


<?php

/** Property archive. @package Archdome_Portal */
get_header();
?>
<div class="site-container content-layout archive-layout">
    <header class="page-header"><p class="eyebrow"><?php esc_html_e('Find your property', 'archdome-portal'); ?></p><h1><?php post_type_archive_title(); ?></h1><?php the_archive_description('<div class="archive-description">', '</div>'); ?></header>
    <?php get_template_part('template-parts/global/archive-filters', null, ['post_type' => 'property', 'filters' => [
        'property_location' => ['taxonomy' => 'location', 'label' => esc_html__('Location', 'archdome-portal')],
        'property_type' => ['taxonomy' => 'property_type', 'label' => esc_html__('Property type', 'archdome-portal')],
        'property_status' => ['taxonomy' => 'property_status', 'label' => esc_html__('Property status', 'archdome-portal')],
    ]]); ?>
    <?php if (have_posts()) { ?><p class="archive-results" role="status"><?php printf(esc_html(_n('%s property found', '%s properties found', $wp_query->found_posts, 'archdome-portal')), esc_html(number_format_i18n($wp_query->found_posts))); ?></p><div class="archdome-card-grid"><?php while (have_posts()) {
        the_post();
        get_template_part('template-parts/cards/property');
    } ?></div>
    <?php the_posts_pagination(['add_args' => archdome_archive_filter_args()]); ?>
    <?php } else { ?><p class="archdome-collection__empty"><?php esc_html_e('No properties match the selected criteria.', 'archdome-portal'); ?></p><?php } ?>
</div>
<?php get_footer(); ?>

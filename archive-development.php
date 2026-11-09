<?php

/** Development archive. @package Archdome_Portal */
get_header();
?>
<div class="site-container content-layout archive-layout">
    <header class="page-header"><p class="eyebrow"><?php esc_html_e('Our portfolio', 'archdome-portal'); ?></p><h1><?php post_type_archive_title(); ?></h1><?php the_archive_description('<div class="archive-description">', '</div>'); ?></header>
    <?php get_template_part('template-parts/global/archive-filters', null, ['post_type' => 'development', 'filters' => [
        'development_location' => ['taxonomy' => 'location', 'label' => esc_html__('Location', 'archdome-portal')],
        'development_type' => ['taxonomy' => 'development_type', 'label' => esc_html__('Development type', 'archdome-portal')],
        'development_status' => ['taxonomy' => 'development_status', 'label' => esc_html__('Development status', 'archdome-portal')],
    ]]); ?>
    <?php if (have_posts()) { ?><div class="archdome-card-grid"><?php while (have_posts()) {
        the_post();
        get_template_part('template-parts/cards/development');
    } ?></div>
    <?php the_posts_pagination(['add_args' => archdome_archive_filter_args()]); ?>
    <?php } else { ?><p class="archdome-collection__empty"><?php esc_html_e('No developments match the selected criteria.', 'archdome-portal'); ?></p><?php } ?>
</div>
<?php get_footer(); ?>

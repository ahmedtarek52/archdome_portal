<?php
/** Real-estate taxonomy archive. @package Archdome_Portal */
get_header();
?>
<div class="site-container content-layout">
    <header class="page-header"><p class="eyebrow"><?php esc_html_e('Browse', 'archdome-portal'); ?></p><h1><?php single_term_title(); ?></h1><?php the_archive_description('<div class="archive-description">', '</div>'); ?></header>
    <?php if (have_posts()) { ?><div class="archdome-card-grid"><?php while (have_posts()) {
        the_post();
        $type = get_post_type();
        if ($type === 'development' || $type === 'property') {
            get_template_part('template-parts/cards/' . $type);
        } else {
            get_template_part('template-parts/content/content', $type);
        }
    } ?></div><?php the_posts_pagination(); ?>
    <?php } else { ?><p class="archdome-collection__empty"><?php esc_html_e('No matching content is available yet.', 'archdome-portal'); ?></p><?php } ?>
</div>
<?php get_footer(); ?>


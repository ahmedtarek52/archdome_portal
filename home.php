<?php

/** Insights archive. @package Archdome_Portal */
get_header();
?>
<div class="site-container content-layout">
    <header class="page-header"><p class="eyebrow"><?php esc_html_e('News and perspectives', 'archdome-portal'); ?></p><h1><?php esc_html_e('Insights', 'archdome-portal'); ?></h1></header>
    <?php if (have_posts()) { ?><div class="entry-list"><?php while (have_posts()) {
        the_post();
        get_template_part('template-parts/content/content', get_post_type());
    } ?></div><?php the_posts_pagination(); ?>
    <?php } else {
        get_template_part('template-parts/content/content', 'none', ['heading_level' => 2]);
    } ?>
</div>
<?php get_footer(); ?>

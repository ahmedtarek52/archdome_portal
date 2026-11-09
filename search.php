<?php
/** Search results. @package Archdome_Portal */
get_header();
?>
<div class="site-container content-layout">
    <header class="page-header"><p class="eyebrow"><?php esc_html_e('Search', 'archdome-portal'); ?></p><h1><?php printf(esc_html__('Results for “%s”', 'archdome-portal'), esc_html(get_search_query())); ?></h1><?php get_search_form(); ?></header>
    <?php if (have_posts()) { ?><div class="entry-list"><?php while (have_posts()) {
        the_post();
        $type = get_post_type();
        if ($type === 'development' || $type === 'property') {
            get_template_part('template-parts/cards/' . $type);
        } else {
            get_template_part('template-parts/content/content', $type);
        }
    } ?></div><?php the_posts_pagination(); ?>
    <?php } else {
        get_template_part('template-parts/content/content', 'none', ['heading_level' => 2]);
    } ?>
</div>
<?php get_footer(); ?>

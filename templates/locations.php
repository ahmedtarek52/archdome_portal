<?php
/**
 * Template Name: Locations
 */
get_header();
while (have_posts()) {
    the_post(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('content-layout'); ?>>
    <header class="page-header site-container"><p class="eyebrow"><?php esc_html_e('Explore', 'archdome-portal'); ?></p><h1><?php the_title(); ?></h1><?php if (has_excerpt()) {
        the_excerpt();
    } ?></header>
    <div class="entry__content"><?php the_content(); ?></div>
    <?php echo render_block(['blockName' => 'archdome/locations', 'attrs' => ['limit' => 24], 'innerBlocks' => [], 'innerHTML' => '', 'innerContent' => []]); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>
</article>
<?php }
get_footer(); ?>


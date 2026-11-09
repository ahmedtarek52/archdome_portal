<?php

/**
 * Insight single template.
 */
get_header();
while (have_posts()) {
    the_post(); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('site-container content-layout content-layout--reading article'); ?>>
    <header class="entry__header"><p class="eyebrow"><time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time></p><h1 class="entry__title"><?php the_title(); ?></h1><?php if (has_excerpt()) { ?><div class="entry__excerpt"><?php the_excerpt(); ?></div><?php } ?></header>
    <?php if (has_post_thumbnail()) { ?><figure class="article__media"><?php the_post_thumbnail('large'); ?></figure><?php } ?>
    <div class="entry__content"><?php the_content(); ?></div>
    <?php the_post_navigation(); ?>
</article>
<?php }
get_footer(); ?>


<?php

/**
 * Static front-page template.
 *
 * Homepage sections remain editor-managed Gutenberg content.
 */
get_header();

while (have_posts()) {
    the_post();
    $front_page_has_h1 = false;

    foreach (parse_blocks((string) get_the_content()) as $front_page_block) {
        if (
            'archdome/hero' === ($front_page_block['blockName'] ?? '')
            && absint($front_page_block['attrs']['headingLevel'] ?? 1) === 1
            && ! empty($front_page_block['attrs']['heading'])
        ) {
            $front_page_has_h1 = true;
            break;
        }
    }
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class('front-page'); ?>>
        <?php if (! $front_page_has_h1) { ?><h1 class="screen-reader-text"><?php the_title(); ?></h1><?php } ?>
        <div class="front-page__content entry__content">
            <?php the_content(); ?>
        </div>
    </article>
    <?php
}

get_footer();

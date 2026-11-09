<?php
/**
 * Default list entry.
 */
if (! defined('ABSPATH')) {
    exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('entry-card'); ?>>
	<?php if (has_post_thumbnail()) { ?>
		<a class="entry-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail('archdome-card'); ?>
		</a>
	<?php } ?>
	<div class="entry-card__body">
		<h2 class="entry-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>
		<div class="entry-card__excerpt"><?php the_excerpt(); ?></div>
	</div>
</article>


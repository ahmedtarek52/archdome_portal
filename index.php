<?php
/**
 * Main fallback template.
 */
get_header();
?>
<div class="site-container content-layout">
	<?php if (have_posts()) { ?>
		<header class="page-header">
			<h1><?php esc_html_e('Latest updates', 'archdome-portal'); ?></h1>
		</header>

		<div class="entry-list">
			<?php
            while (have_posts()) {
                the_post();
                get_template_part('template-parts/content/content', get_post_type());
            }
	    ?>
		</div>

		<?php the_posts_pagination(); ?>
	<?php } else { ?>
		<?php get_template_part('template-parts/content/content', 'none'); ?>
	<?php } ?>
</div>
<?php
get_footer();

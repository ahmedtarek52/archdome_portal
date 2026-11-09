<?php
/**
 * Not-found template.
 */
get_header();
?>
<div class="site-container content-layout content-layout--reading error-page">
	<p class="eyebrow"><?php esc_html_e('Error 404', 'archdome-portal'); ?></p>
	<h1><?php esc_html_e('This page could not be found.', 'archdome-portal'); ?></h1>
	<p><?php esc_html_e('The address may have changed, or the page may no longer be available. Search the site or return to the homepage.', 'archdome-portal'); ?></p>
	<?php get_search_form(); ?>
	<p>
		<a class="button" href="<?php echo esc_url(home_url('/')); ?>">
			<?php esc_html_e('Return to homepage', 'archdome-portal'); ?>
		</a>
	</p>
</div>
<?php
get_footer();

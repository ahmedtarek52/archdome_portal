<?php
/**
 * Empty content state.
 */
if (! defined('ABSPATH')) {
    exit;
}

$heading_level = absint($args['heading_level'] ?? 1) === 2 ? 'h2' : 'h1';
?>
<section class="empty-state">
	<<?php echo esc_attr($heading_level); ?>><?php esc_html_e('Nothing was found.', 'archdome-portal'); ?></<?php echo esc_attr($heading_level); ?>>
	<p><?php esc_html_e('Try a different search or return to the homepage.', 'archdome-portal'); ?></p>
	<?php get_search_form(); ?>
</section>

<?php
/**
 * Site footer.
 */
if (! defined('ABSPATH')) {
    exit;
}
?>
</main>

<footer class="site-footer">
	<div class="site-footer__inner site-container">
		<div class="site-footer__brand">
			<?php archdome_site_branding(); ?>
		</div>

		<?php if (has_nav_menu('footer')) { ?>
			<nav class="footer-nav" aria-label="<?php esc_attr_e('Footer navigation', 'archdome-portal'); ?>">
				<?php
                wp_nav_menu(
                    [
                        'theme_location' => 'footer',
                        'container' => false,
                        'menu_class' => 'footer-nav__list',
                        'fallback_cb' => false,
                        'depth' => 1,
                    ],
                );
		    ?>
			</nav>
		<?php } ?>

		<p class="site-footer__copyright">
			<?php
		    printf(
		        /* translators: 1: Current year, 2: Site name. */
		        esc_html__('© %1$s %2$s. All rights reserved.', 'archdome-portal'),
		        esc_html(wp_date('Y')),
		        esc_html(get_bloginfo('name')),
		    );
?>
		</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>


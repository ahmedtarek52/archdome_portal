<?php
/**
 * Site header.
 */
if (! defined('ABSPATH')) {
    exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?><?php echo is_rtl() ? '' : ' dir="ltr"'; ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'archdome-portal'); ?></a>

<header class="site-header" data-site-header>
	<div class="site-header__inner site-container">
		<div class="site-branding">
			<?php archdome_site_branding(); ?>
		</div>

		<?php if (has_nav_menu('primary')) { ?>
			<button class="site-menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" data-menu-toggle hidden>
				<span class="site-menu-toggle__label screen-reader-text"><?php esc_html_e('Menu', 'archdome-portal'); ?></span>
				<span class="site-menu-toggle__icon" aria-hidden="true"></span>
			</button>

			<nav class="site-nav" aria-label="<?php esc_attr_e('Primary navigation', 'archdome-portal'); ?>">
				<div class="site-nav__panel" id="primary-navigation" data-menu-panel>
					<?php
                    wp_nav_menu(
                        [
                            'theme_location' => 'primary',
                            'container' => false,
                            'menu_class' => 'site-nav__list',
                            'fallback_cb' => false,
                            'depth' => 2,
                        ],
                    );
		    ?>
					<?php archdome_language_switcher(); ?>
				</div>
			</nav>
		<?php } else { ?>
			<?php archdome_language_switcher(); ?>
		<?php } ?>
	</div>
</header>

<main id="primary" class="site-main">

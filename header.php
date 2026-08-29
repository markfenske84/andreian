<?php
/**
 * Theme header.
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
	<?php get_template_part( 'src/components/root-customizer-vars' ); ?>
</head>
<body <?php body_class(); ?>>

<header id="main-header" class="site-header -position-fixed">
	<div class="accessibility-navigation">
		<a href="#main-header-menu" class="sr-only"><?php esc_html_e( 'Skip to Navigation', 'andreian' ); ?></a>
		<a href="#content" class="sr-only"><?php esc_html_e( 'Skip to Content', 'andreian' ); ?></a>
	</div>

	<div class="_inner _container _flex -justify-between -align-center">
		<div class="header-col _logo">
			<?php get_template_part( 'src/components/site-logo' ); ?>
		</div>

		<div class="header-col _desktop-navigation _display-desktop">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'main-nav',
					'menu_id'        => 'main-header-menu',
				)
			);
			?>
		</div>

		<div class="header-col _mobile-navigation-toggle _display-mobile">
			<button class="mobile-offcanvas-toggle" aria-label="<?php esc_attr_e( 'Open mobile navigation', 'andreian' ); ?>">
				<span class="line-bar"></span>
				<span class="line-bar"></span>
				<span class="line-bar"></span>
			</button>
		</div>
	</div>
</header>

<div id="mobile-offcanvas" class="mobile-offcanvas _display-mobile">
	<div class="mobile-offcanvas__inner">
		<div class="mobile-offcanvas__header _flex -align-center -justify-between">
			<?php get_template_part( 'src/components/site-icon' ); ?>
			<button class="mobile-offcanvas-toggle _close" aria-label="<?php esc_attr_e( 'Close mobile navigation', 'andreian' ); ?>">
				<span class="line-bar"></span>
				<span class="line-bar"></span>
			</button>
		</div>
		<div class="mobile-offcanvas__content">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'main-nav',
					'menu_id'        => 'mobile-header-menu',
				)
			);
			?>
		</div>
	</div>
</div>

<main id="content">

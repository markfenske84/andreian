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
	<script>
		(function () {
			try {
				var theme = localStorage.getItem('andreian-theme');
				if (theme === 'dark' || theme === 'light') {
					document.documentElement.setAttribute('data-theme', theme);
				}
			} catch (error) {}
		})();
	</script>
	<?php wp_head(); ?>
	<?php get_template_part( 'src/components/root-customizer-vars' ); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="main-header" class="site-header">
	<div class="accessibility-navigation">
		<a href="#content" class="sr-only"><?php esc_html_e( 'Skip to Content', 'andreian' ); ?></a>
	</div>

	<div class="site-header__brand _container">
		<?php if ( is_front_page() ) : ?>
			<h1 class="site-wordmark-heading">
				<a class="site-wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php echo esc_html( get_bloginfo( 'name' ) ?: __( 'The Andreia Philosophy', 'andreian' ) ); ?>
				</a>
			</h1>
		<?php else : ?>
			<p class="site-wordmark-heading">
				<a class="site-wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php echo esc_html( get_bloginfo( 'name' ) ?: __( 'The Andreia Philosophy', 'andreian' ) ); ?>
				</a>
			</p>
		<?php endif; ?>
	</div>

	<div class="site-header__navigation">
		<div class="site-header__navigation-inner _container">
			<div class="site-header__leading">
				<button
					class="site-search-toggle"
					type="button"
					aria-controls="site-search-overlay"
					aria-expanded="false"
					aria-label="<?php esc_attr_e( 'Open search', 'andreian' ); ?>">
					<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false">
						<circle cx="11" cy="11" r="7"></circle>
						<path d="m16.5 16.5 4 4"></path>
					</svg>
				</button>

				<button
					class="theme-toggle"
					type="button"
					aria-label="<?php esc_attr_e( 'Switch to dark mode', 'andreian' ); ?>">
					<svg class="theme-toggle__moon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
						<path d="M21 14.3A9 9 0 1 1 9.7 3 7.2 7.2 0 0 0 21 14.3z"></path>
					</svg>
					<svg class="theme-toggle__sun" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
						<circle cx="12" cy="12" r="4"></circle>
						<path d="M12 2.2v2.4M12 19.4v2.4M4.2 4.2l1.7 1.7M18.1 18.1l1.7 1.7M2.2 12h2.4M19.4 12h2.4M4.2 19.8l1.7-1.7M18.1 5.9l1.7-1.7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
					</svg>
				</button>
			</div>

			<nav class="primary-navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'andreian' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'main-nav',
						'menu_id'        => 'main-header-menu',
						'container'      => false,
						'fallback_cb'    => 'andreian_page_menu_fallback',
					)
				);
				?>
			</nav>

			<div class="site-header__tools">
				<?php if ( andreian_get_social_links() ) : ?>
					<nav class="social-navigation" aria-label="<?php esc_attr_e( 'Social links', 'andreian' ); ?>">
						<?php echo andreian_render_social_links(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in renderer. ?>
					</nav>
				<?php endif; ?>
			</div>

			<button
				class="mobile-offcanvas-toggle"
				type="button"
				aria-controls="mobile-offcanvas"
				aria-expanded="false"
				aria-label="<?php esc_attr_e( 'Open navigation', 'andreian' ); ?>">
					<span class="line-bar"></span>
					<span class="line-bar"></span>
					<span class="line-bar"></span>
			</button>
		</div>
	</div>
</header>

<div
	id="site-search-overlay"
	class="site-search-overlay"
	role="dialog"
	aria-modal="true"
	aria-labelledby="site-search-title"
	hidden>
	<div class="site-search-overlay__inner">
		<button class="site-search-overlay__close" type="button" aria-label="<?php esc_attr_e( 'Close search', 'andreian' ); ?>">
			<span aria-hidden="true"></span>
			<span aria-hidden="true"></span>
		</button>
		<div class="site-search-overlay__content">
			<h2 id="site-search-title"><?php esc_html_e( 'Search', 'andreian' ); ?></h2>
			<?php get_search_form(); ?>
		</div>
	</div>
</div>

<div id="mobile-offcanvas" class="mobile-offcanvas" hidden>
	<div class="mobile-offcanvas__inner">
		<div class="mobile-offcanvas__header _flex -align-center -justify-between">
			<a class="site-wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php echo esc_html( get_bloginfo( 'name' ) ?: __( 'The Andreia Philosophy', 'andreian' ) ); ?>
			</a>
			<button class="mobile-offcanvas-toggle _close" type="button" aria-controls="mobile-offcanvas" aria-expanded="true" aria-label="<?php esc_attr_e( 'Close navigation', 'andreian' ); ?>">
				<span class="line-bar"></span>
				<span class="line-bar"></span>
			</button>
		</div>
		<nav class="mobile-offcanvas__content" aria-label="<?php esc_attr_e( 'Mobile navigation', 'andreian' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'main-nav',
					'menu_id'        => 'mobile-header-menu',
					'container'      => false,
					'fallback_cb'    => 'andreian_page_menu_fallback',
				)
			);
			?>
		</nav>
	</div>
</div>

<main id="content">

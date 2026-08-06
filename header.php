<?php
/**
 * Theme Header
 * 
 * The head element along with the opening body tag are within this template partial. This template partial
 * also opens the main element which is eventually closed within footer.php.
 * This PHP file is invoked through the get_header() function.
 * 
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @link https://codex.wordpress.org/Function_Reference/get_header
 * @author Choice Home Warranty <https://www.choicehomewarranty.com/>
 */

?>

<!doctype html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?= bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<?php wp_head(); ?>

		<?php get_template_part('src/components/root-customizer-vars');  ?>
	</head>

	<body <?php body_class(); ?>>

		<header
			id="main-header" 
			class="
				site-header 
				-position-fixed">

			<div 
				class="accessibility-navigation">
				<a 
					href="#main-header-menu" 
					class="sr-only">Skip to Navigation</a>
				<a 
					href="#content" 
					class="sr-only">Skip to Content</a>
			</div>

			<?php get_template_part('src/components/announcement-bar');  ?>

			<div 
				class="
					_inner 
					_container 
					_flex 
					-justify-end 
					-align-center">

				<div 
					class="
						header-col 
						-logo">

					<?php get_template_part('src/components/site-logo'); ?>

				</div>

				<div 
					class="
						header-col 
						-desktop-navigation 
						--main 
						_flex
						_display-desktop">
					<?php wp_nav_menu( array( 
						'theme_location' => 'main-nav', 
						'menu_id' => 'main-header-menu'
					)); ?>
				</div>

				<div 
					class="
						header-col 
						-desktop-navigation 
						--cta 
						_flex 
						_display-desktop">

					<?php wp_nav_menu( array( 
						'cta_menu', 
						'menu_id' => 'cta-header-menu'
					)); ?>

				</div>

				<div 
					class="
						header-col 
						-mobile-navigation-toggle 
						_display-mobile">

					<div class="mobile-header-actions _flex _align-center">
						<?php
						$header_phone = function_exists( 'chw_get_header_phone_link' ) ? chw_get_header_phone_link() : null;
						if ( $header_phone ) :
							?>
						<a
							class="mobile-header-phone"
							href="<?= esc_url( $header_phone['url'] ); ?>"
							aria-label="<?= esc_attr( sprintf( __( 'Call %s', 'chw' ), $header_phone['label'] ) ); ?>">

							<span class="sr-only"><?= esc_html( $header_phone['label'] ); ?></span>

						</a>
						<?php endif; ?>

						<button 
							class="mobile-offcanvas-toggle" 
							aria-label="Open mobile navigation">

							<span class="line-bar"></span>
							<span class="line-bar"></span>
							<span class="line-bar"></span>

						</button>
					</div>

				</div>
			</div>

		</header>
		<script>
			(function () {
				var header = document.getElementById('main-header');
				if (!header || !header.classList.contains('-position-fixed')) return;
				var height = header.offsetHeight + 'px';
				document.documentElement.style.setProperty('--header-height', height);
				document.body.style.setProperty('--header-height', height);
			})();
		</script>

		<div 
			id="mobile-offcanvas" 
			class="
				mobile-offcanvas 
				_display-mobile">

			<div 
				class="mobile-offcanvas__inner">

				<div 
					class="
						mobile-offcanvas__header 
						_flex 
						-align-center 
						-justify-between">

					<?php get_template_part( 'src/components/site-icon' ); ?>

					<button 
						class="
							mobile-offcanvas-toggle 
							-close" 
						aria-label="Close mobile navigation">

						<span class="line-bar"></span>
						<span class="line-bar"></span>

					</button>

				</div>

				<div class="mobile-offcanvas__content">

					<?php wp_nav_menu( array( 
						'theme_location' => 'main-nav', 
						'menu_id' => 'mobile-header-menu'
					)); ?>

					<?php wp_nav_menu( array( 
						'cta_menu', 
						'menu_id' => 'cta-header-menu'
					)); ?>
				</div>
			</div>

		</div>

		<main id="content">
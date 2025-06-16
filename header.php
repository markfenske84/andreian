<?
/**
 * Theme Header
 * 
 * The head element along with the opening body tag are within this template partial. This template partial
 * also opens the main element which is eventually closed within footer.php.
 * This PHP file is invoked through the get_header() function.
 * 
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * @link https://codex.wordpress.org/Function_Reference/get_header
 * @author Webfor <https://www.webfor.com/>
 */

// Theme Settings
$font_file_url = get_theme_mod('font_url_setting');
$header_positioning = get_field('header_positioning'); // Select: fixed, absolute, relative
?>

<!doctype html>
<html <? language_attributes(); ?>>
	<head>
		<meta charset="<?= bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<? wp_head(); ?>

		<? get_template_part('src/components/root-customizer-vars');  ?>

		<? if($font_file_url): ?>
			<link rel="preload" href="<?= $font_file_url; ?>" as="style" />
			<link rel="stylesheet" href="<?= $font_file_url; ?>">
		<? endif; ?>

		<? 
		$settings_header_scripts = get_field('header_scripts', 'option'); 
		$page_scripts = get_field('page_scripts'); 
		if($settings_header_scripts) { echo $settings_header_scripts; } 
		if($page_scripts) {
			$page_header_scripts = $page_scripts['header_scripts'];  
			echo $page_header_scripts; 
		} 
		if(!is_home()) {
			$header_styles = get_field('header_styles');
			if($header_styles) { 
				$background_color = $header_styles['background_color']; // Color picker field
				$backdrop_filter = $header_styles['backdrop_filter']; // Number field
				$transparent_white_logo = $header_styles['transparent_white_logo']; // true / false field
				$main_nav_link_color = $header_styles['main_nav_link_color']; // Color picker field
			} 
		}
		?>

		<? if(!is_home() && $header_positioning != 'relative') { ?>
		<style>
			<? if($transparent_white_logo) { ?> 
			.site-header .header-col .site-logo img {
				filter: brightness(0) invert(1);
			}
			<? } if($main_nav_link_color) { ?>
			.site-header #main-header-menu > li > a,
			.site-header #main-header-menu > li > .toggle-button,
			.site-header #cta-header-menu > li > a  {
				color: <?= $main_nav_link_color; ?>;
			}
			.site-header .line-bar {background-color: <?= $main_nav_link_color; ?>;}
			<? } ?>
		</style>
		<? } ?>
	</head>

	<body <? body_class(); ?>>

		<header 
			id="main-header" 
			class="
				site-header 
				-position-<?= $header_positioning; ?>"
			<? if(!is_home() && $header_positioning != 'relative') { ?> 
			style="
				<? if($background_color) { ?>background-color: <?= $background_color; ?>; <? } ?>
				<? if($backdrop_filter) { ?>backdrop-filter: blur(<?= $backdrop_filter; ?>px);<? } ?>"
			<? } ?>>

			<div 
				class="accessibility-navigation">
				<a 
					href="#main-header-menu" 
					class="sr-only">Skip to Navigation</a>
				<a 
					href="#content" 
					class="sr-only">Skip to Content</a>
			</div>

			<? get_template_part('src/components/announcement-bar');  ?>

			<div 
				class="
					_inner 
					_container 
					-max-width-100 
					_flex 
					-justify-end 
					-align-center">

				<div 
					class="
						header-col 
						-logo">

					<? get_template_part('src/components/site-logo'); ?>

				</div>

				<div 
					class="
						header-col 
						-desktop-navigation 
						--main 
						_flex
						_display-desktop">
					<? wp_nav_menu( array( 
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

					<? wp_nav_menu( array( 
						'cta_menu', 
						'menu_id' => 'cta-header-menu'
					)); ?>

				</div>

				<div 
					class="
						header-col 
						-mobile-navigation-toggle 
						_display-mobile">

					<button 
						class="mobile-offcanvas-toggle" 
						aria-label="Open mobile navigation">

						<span class="line-bar"></span>
						<span class="line-bar"></span>
						<span class="line-bar"></span>

					</button>

				</div>
			</div>

		</header>

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
						-column">

					<button 
						class="
							mobile-offcanvas-toggle 
							-close" 
						aria-label="Close mobile navigation">

						<span class="line-bar"></span>
						<span class="line-bar"></span>

					</button>

					<? get_template_part('src/components/site-logo');  ?>

				</div>

				<div class="mobile-offcanvas__content">

					<? wp_nav_menu( array( 
						'theme_location' => 'main-nav', 
						'menu_id' => 'mobile-header-menu'
					)); ?>

					<? wp_nav_menu( array( 
						'cta_menu', 
						'menu_id' => 'cta-header-menu'
					)); ?>
				</div>
			</div>

		</div>

		<main id="content">
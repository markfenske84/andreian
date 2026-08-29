<?php
/**
 * Theme initialization.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cache-busting version for theme assets.
 *
 * @param string $file_path Absolute path to the asset file.
 * @return string
 */
function andreian_asset_version( $file_path ) {
	if ( is_readable( $file_path ) ) {
		return (string) filemtime( $file_path );
	}

	static $fallback = null;

	if ( null === $fallback ) {
		$fallback = wp_get_theme()->get( 'Version' ) ?: '1.0.0';
	}

	return $fallback;
}

function andreian_initialize() {
	remove_action( 'welcome_panel', 'wp_welcome_panel' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'index_rel_link' );
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	remove_action( 'wp_head', 'wp_generator' );
	remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );

	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 125, 125, true );
	add_theme_support( 'menus' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'align-wide' );
	add_editor_style(
		array(
			'dist/css/theme.min.css',
			'dist/css/editor.min.css',
		)
	);
	add_theme_support( 'widgets' );
	add_theme_support( 'widgets-block-editor' );
}
add_action( 'init', 'andreian_initialize' );

function andreian_styles_and_scripts() {
	wp_register_style(
		'theme',
		get_template_directory_uri() . '/dist/css/theme.min.css',
		array(),
		andreian_asset_version( get_template_directory() . '/dist/css/theme.min.css' )
	);
	wp_enqueue_style( 'theme' );

	wp_register_script(
		'theme',
		get_template_directory_uri() . '/dist/js/theme.min.js',
		array(),
		andreian_asset_version( get_template_directory() . '/dist/js/theme.min.js' ),
		true
	);
	andreian_localize();
	wp_enqueue_script( 'theme' );
}
add_action( 'wp_enqueue_scripts', 'andreian_styles_and_scripts', 999 );

function andreian_login_css() {
	wp_enqueue_style( 'login_css', get_template_directory_uri() . '/dist/css/admin.min.css' );
}
add_action( 'login_head', 'andreian_login_css' );

function andreian_enqueue_gutenberg_styles() {
	wp_enqueue_style(
		'editor_css',
		get_template_directory_uri() . '/dist/css/editor.min.css',
		array(),
		andreian_asset_version( get_template_directory() . '/dist/css/editor.min.css' )
	);
}
add_action( 'enqueue_block_editor_assets', 'andreian_enqueue_gutenberg_styles' );

function andreian_localize() {
	wp_localize_script(
		'theme',
		'andreian_localize',
		array(
			'ajaxurl'            => admin_url( 'admin-ajax.php' ),
			'mobile_menu_layout' => get_theme_mod( 'mobile_menu_layout', 'dropdown' ),
		)
	);
}

function andreian_nav_menus() {
	register_nav_menus(
		array(
			'main-nav' => __( 'Main Menu', 'andreian' ),
		)
	);
}
add_action( 'after_setup_theme', 'andreian_nav_menus' );

include_once 'wp-customization/admin.php';
include_once 'wp-customization/posts.php';
include_once 'wp-customization/users.php';
include_once 'wp-customization/widgets.php';
include_once 'wp-customization/classic-editor-posts.php';
include_once 'wp-customization/page-layout.php';
include_once 'wp-customization/image-monotone-filter.php';
include_once 'wp-customization/columns-reverse-mobile.php';
include_once 'wp-customization/spacer-hide-mobile.php';
include_once 'wp-customization/heading-margin-top.php';

include_once 'functions/duplicate-posts.php';
include_once 'functions/quick-featured-images.php';
include_once 'functions/blog-post.php';
include_once 'functions/slugify.php';
include_once 'functions/svgs.php';
include_once 'functions/page-templates.php';

function andreian_mobile_menu_layout_body_class( $classes ) {
	$layout    = get_theme_mod( 'mobile_menu_layout', 'dropdown' );
	$classes[] = 'mobile-menu-layout-' . sanitize_html_class( $layout );

	if ( andreian_singular_has_sidebar() ) {
		$classes[] = 'has-blog-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'andreian_mobile_menu_layout_body_class' );

function andreian_disable_admin_bar_bump() {
	if ( ! is_admin_bar_showing() ) {
		return;
	}

	remove_action( 'wp_head', '_admin_bar_bump_cb' );
}
add_action( 'get_header', 'andreian_disable_admin_bar_bump', 0 );

function andreian_admin_bar_shim_styles() {
	if ( ! is_admin_bar_showing() ) {
		return;
	}
	?>
	<style id="andreian-admin-bar-shim">
		html {
			margin-top: 0 !important;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'andreian_admin_bar_shim_styles', 101 );

<?php
/**
 * Initialization function for the Webfor theme.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Cache-busting version for theme assets. Avoids fatal errors when dist files are missing.
 *
 * @param string $file_path Absolute path to the asset file.
 * @return string
 */
function chw_asset_version( $file_path ) {
	if ( is_readable( $file_path ) ) {
		return (string) filemtime( $file_path );
	}

	static $fallback = null;

	if ( null === $fallback ) {
		$fallback = wp_get_theme()->get( 'Version' ) ?: '1.0.0';
	}

	return $fallback;
}

function chw_initialize() {
  // Removing default actions and filters from WordPress.
  remove_action( 'welcome_panel', 'wp_welcome_panel' ); // Welcome Panel
  remove_action( 'wp_head', 'rsd_link' ); // EditURI link
  remove_action( 'wp_head', 'wlwmanifest_link' ); // windows live writer
  remove_action( 'wp_head', 'index_rel_link' ); // index link
  remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // previous link
  remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // start link
  remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 ); // links for adjacent posts
  remove_action( 'wp_head', 'wp_generator' ); // WP version
  remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10); // Don't filter oEmbed results.
  remove_action( 'wp_head', 'wp_oembed_add_discovery_links'); // Remove oEmbed discovery links.
  remove_action( 'wp_head', 'wp_oembed_add_host_js'); // Remove oEmbed-specific JavaScript from the front-end and back-end.
  remove_action( 'wp_head', 'print_emoji_detection_script', 7); // Remove wp-emoji-release.min.js
  remove_action( 'wp_print_styles', 'print_emoji_styles'); // Remove wp-emoji-release.min.js

  // Thumbnails
  add_theme_support( 'post-thumbnails' );
  set_post_thumbnail_size(125, 125, true);

  // Theme
  add_theme_support( 'menus' );

  // Title Tag
  add_theme_support( 'title-tag' );

  // Wide/full block alignments (page builder template; constrained on blog/default pages via CSS).
  add_theme_support( 'align-wide' );

  // Load theme + block styles into the editor canvas iframe (enqueue_block_editor_assets
  // only loads in the admin shell; add_editor_style targets the content canvas).
  add_editor_style(
    array(
      'dist/css/theme.min.css',
      'dist/css/blocks.min.css',
    )
  );

  // Widgets
  add_theme_support( 'widgets' );
  add_theme_support( 'widgets-block-editor' );
}
add_action( 'init', 'chw_initialize' );

/**
 * Enqueuing styles and scripts.
 */
function chw_styles_and_scripts() {
  // Theme Styles
  wp_register_style( 'theme', get_template_directory_uri() . '/dist/css/theme.min.css', array(), chw_asset_version( get_template_directory() . '/dist/css/theme.min.css' ) );
  wp_enqueue_style( 'theme' );

  // Theme Scripts
  wp_register_script( 'theme', get_template_directory_uri() . '/dist/js/theme.min.js', array(), chw_asset_version( get_template_directory() . '/dist/js/theme.min.js' ), true );
  chw_localize();
  wp_enqueue_script( 'theme' );

  // Blocks Styles
  wp_register_style( 'blocks', get_template_directory_uri() . '/dist/css/blocks.min.css', array(), chw_asset_version( get_template_directory() . '/dist/css/blocks.min.css' ) );
  wp_enqueue_style( 'blocks' );

  // Blocks Scripts
  wp_register_script( 'blocks', get_template_directory_uri() . '/dist/js/blocks.min.js', array(), chw_asset_version( get_template_directory() . '/dist/js/blocks.min.js' ), true );
  wp_enqueue_script( 'blocks' );
}
add_action( 'wp_enqueue_scripts', 'chw_styles_and_scripts', 999 );

/**
 * Enqueuing styles for the login screen.
 */
function login_css() {
  wp_enqueue_style( 'login_css', get_template_directory_uri() . '/dist/css/admin.min.css' );
}
add_action('login_head', 'login_css');

/**
 * Enqueuing styles for Gutenburg editor.
 */
function enqueue_gutenberg_styles() {
  wp_enqueue_style( 'blocks_css', get_template_directory_uri() . '/dist/css/blocks.min.css', array(), chw_asset_version( get_template_directory() . '/dist/css/blocks.min.css' ) );
  wp_enqueue_style( 'fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css' );
}
add_action('enqueue_block_editor_assets', 'enqueue_gutenberg_styles');

/**
 * Creates an JavaScript object on the DOM window using PHP.
 */
function chw_localize() {
  wp_localize_script( 'theme', 'chw_localize', array(
    'ajaxurl'            => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
    'mobile_menu_layout' => get_theme_mod( 'mobile_menu_layout', 'dropdown' ),
  ));
}

/**
 * Registers navigation menu theme locations.
 */
function chw_nav_menus() {
  register_nav_menus(
    array(
      'main-nav' => __( 'Main Menu', 'chw' ),   // main nav in header
      // 'cta-nav' => __( 'CTA Menu', 'chw' ),   // main nav in header
      // 'footer-nav' => __( 'Footer Navigation', 'chw' ), // footer nav
    )
  );
}
add_action( 'after_setup_theme', 'chw_nav_menus' );

// Core WP Improvements + Edits
include_once 'wp-customization/admin.php';
include_once 'wp-customization/posts.php';
include_once 'wp-customization/users.php';
include_once 'wp-customization/widgets.php';
include_once 'wp-customization/gravity-forms.php';
include_once 'wp-customization/page-document-settings.php';
include_once 'wp-customization/image-monotone-filter.php';
include_once 'wp-customization/columns-reverse-mobile.php';
include_once 'wp-customization/spacer-hide-mobile.php';
// include_once 'wp-customization/dev-tools.php';

// Additional Functionality
include_once 'functions/duplicate-posts.php';
include_once 'functions/quick-featured-images.php';
include_once 'functions/add-blog-slug.php';
include_once 'functions/blog-post.php';
include_once 'functions/block-patterns.php';
include_once 'functions/slugify.php';
include_once 'functions/svgs.php';
include_once 'functions/header-phone.php';
include_once 'functions/social-links.php';
include_once 'functions/editor-color-palette.php';
include_once 'functions/page-templates.php';

/**
 * Append a body class based on the Mobile Menu Layout selected in the Customizer.
 * This makes it easy to scope CSS/JS behavior for each layout option.
 */
function chw_mobile_menu_layout_body_class( $classes ) {
  $layout = get_theme_mod( 'mobile_menu_layout', 'dropdown' ); // 'dropdown' or 'panel'
  $classes[] = 'mobile-menu-layout-' . sanitize_html_class( $layout );

  if ( chw_singular_has_sidebar() ) {
    $classes[] = 'has-blog-sidebar';
  }

  return $classes;
}
add_filter( 'body_class', 'chw_mobile_menu_layout_body_class' );

/**
 * Override WordPress admin-bar bump after core inline styles (wp_head priority 99).
 * Core adds html { margin-top: 32px !important; } which stacks with our fixed-header
 * offset and creates a gap between the site header and main content when logged in.
 */
function chw_disable_admin_bar_bump() {
	if ( ! is_admin_bar_showing() ) {
		return;
	}

	remove_action( 'wp_head', '_admin_bar_bump_cb' );
}
add_action( 'get_header', 'chw_disable_admin_bar_bump', 0 );

function chw_admin_bar_shim_styles() {
	if ( ! is_admin_bar_showing() ) {
		return;
	}
	?>
	<style id="chw-admin-bar-shim">
		html {
			margin-top: 0 !important;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'chw_admin_bar_shim_styles', 101 );

/**
 * Preload the masthead LCP image and above-the-fold Hind font weights so the
 * browser discovers them from the initial HTML (Lighthouse LCP + CLS).
 */
function chw_preload_critical_assets() {
	if ( is_admin() ) {
		return;
	}

	$theme_uri = get_template_directory_uri();

	// Above-the-fold Hind weights used by masthead title (700), body (400), labels (600).
	$font_weights = array( 'Regular', 'SemiBold', 'Bold' );
	foreach ( $font_weights as $weight ) {
		printf(
			'<link rel="preload" as="font" type="font/woff2" href="%s" crossorigin>' . "\n",
			esc_url( $theme_uri . '/dist/webfonts/Hind-' . $weight . '.woff2' )
		);
	}

	$image_url = '';

	if ( ( is_archive() && ! is_home() ) || is_search() ) {
		$image_url = THEME_IMAGES . '/masthead-default.webp';
	} elseif ( is_home() ) {
		$blog_image = get_theme_mod( 'blog_masthead_image' );
		$image_url  = $blog_image ? $blog_image : THEME_IMAGES . '/masthead-default.webp';
	} elseif ( is_page() || ( is_single() && 'post' === get_post_type() ) ) {
		if ( ! chw_is_masthead_disabled( get_the_ID() ) ) {
			$image_url = chw_get_masthead_background( get_the_ID() );
		}
	}

	if ( $image_url ) {
		printf(
			'<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n",
			esc_url( $image_url )
		);
	}
}
add_action( 'wp_head', 'chw_preload_critical_assets', 2 );
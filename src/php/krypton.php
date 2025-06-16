<?
/**
 * Initialization function for the Webfor theme.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function krypton_initialize() {
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

  // Widgets
  add_theme_support( 'widgets' );
  add_theme_support( 'widgets-block-editor' );
}
add_action( 'init', 'krypton_initialize' );

/**
 * Enqueuing styles and scripts.
 */
function krypton_styles_and_scripts() {
  // Theme Styles
  wp_register_style( 'theme', get_template_directory_uri() . '/dist/css/theme.min.css', array(), filemtime(get_template_directory() . '/dist/css/theme.min.css') );
  wp_enqueue_style( 'theme' );

  // Theme Scripts
  wp_register_script( 'theme', get_template_directory_uri() . '/dist/js/theme.min.js', array(), filemtime(get_template_directory() . '/dist/js/theme.min.js'), true );
  krypton_localize();
  wp_enqueue_script( 'theme' );

  // Blocks Styles
  wp_register_style( 'blocks', get_template_directory_uri() . '/dist/css/blocks.min.css', array(), filemtime(get_template_directory() . '/dist/css/blocks.min.css') );
  wp_enqueue_style( 'blocks' );

  // Blocks Scripts
  wp_register_script( 'blocks', get_template_directory_uri() . '/dist/js/blocks.min.js', array(), filemtime(get_template_directory() . '/dist/js/blocks.min.js'), true );
  wp_enqueue_script( 'blocks' );
}
add_action( 'wp_enqueue_scripts', 'krypton_styles_and_scripts', 999 );

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
  wp_enqueue_style( 'blocks_css', get_template_directory_uri() . '/dist/css/blocks.min.css' );
  wp_enqueue_style( 'fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css' );
}
add_action('enqueue_block_editor_assets', 'enqueue_gutenberg_styles');

/**
 * Creates an JavaScript object on the DOM window using PHP.
 */
function krypton_localize() {
  wp_localize_script( 'theme', 'krypton_localize', array(
    'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
  ));
}

/**
 * Registers navigation menu theme locations.
 */
function krypton_nav_menus() {
  register_nav_menus(
    array(
      'main-nav' => __( 'Main Menu', 'krypton' ),   // main nav in header
      // 'cta-nav' => __( 'CTA Menu', 'krypton' ),   // main nav in header
      // 'footer-nav' => __( 'Footer Navigation', 'krypton' ), // footer nav
    )
  );
}
add_action( 'after_setup_theme', 'krypton_nav_menus' );

// Core WP Improvements + Edits
include_once 'wp-customization/admin.php';
include_once 'wp-customization/posts.php';
include_once 'wp-customization/users.php';
include_once 'wp-customization/widgets.php';
include_once 'wp-customization/dev-tools.php';

// Additional Functionality
include_once 'functions/duplicate-posts.php';
include_once 'functions/quick-featured-images.php';
include_once 'functions/add-blog-slug.php';
include_once 'functions/slugify.php';
include_once 'functions/svgs.php';
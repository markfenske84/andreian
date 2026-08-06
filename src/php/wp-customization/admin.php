<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin functions
 * Using hooks, we can run functions only to users that are logged in.
 */

/**
 * Enqueuing administrative scripts + styles.
 */
function chw_admin_styles_and_scripts() {
    // Styles
    wp_register_style( 'admin-theme', get_template_directory_uri() . '/dist/css/admin.min.css', array(), chw_asset_version( get_template_directory() . '/dist/css/admin.min.css' ) );
    wp_enqueue_style( 'admin-theme' );

    // Scripts
    wp_register_script( 'admin-theme', get_template_directory_uri() . '/dist/js/admin.min.js', array(), chw_asset_version( get_template_directory() . '/dist/js/admin.min.js' ), true );
    wp_enqueue_script( 'admin-theme' );
}
add_action('admin_head', 'chw_admin_styles_and_scripts');

/**
 * Remove the comments functionality from the administrative menu.
*/
function remove_comments_admin_menu() {
    remove_menu_page( 'edit-comments.php' );
}
add_action('admin_menu', 'remove_comments_admin_menu');

/**
 * Remove the comments functionality from pages + posts.
*/
function remove_post_page_comments() {
    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'page', 'comments' );
}
add_action('admin_init', 'remove_post_page_comments', 100);

/**
 * Remove the comments functionality from the admin bar.
*/
function remove_comments_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}
add_action('wp_before_admin_bar_render', 'remove_comments_admin_bar');

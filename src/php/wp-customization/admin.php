<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin functions
 * Using hooks, we can run functions only to users that are logged in.
 */

/**
 * Enqueuing administrative scripts + styles.
 */
function andreian_admin_styles_and_scripts() {
    // Styles
    wp_register_style( 'admin-theme', get_template_directory_uri() . '/dist/css/admin.min.css', array(), andreian_asset_version( get_template_directory() . '/dist/css/admin.min.css' ) );
    wp_enqueue_style( 'admin-theme' );

    // Scripts
    wp_register_script( 'admin-theme', get_template_directory_uri() . '/dist/js/admin.min.js', array(), andreian_asset_version( get_template_directory() . '/dist/js/admin.min.js' ), true );
    wp_enqueue_script( 'admin-theme' );
}
add_action('admin_head', 'andreian_admin_styles_and_scripts');

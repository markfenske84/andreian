<?
/**
 * Initialization function for the Webfor theme.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function krypton_customizer_sections($wp_customize) {

    include_once 'customizer/site-logos.php';
    include_once 'customizer/layout-settings.php';
    include_once 'customizer/color-palette.php';
    include_once 'customizer/button-styles.php';
    include_once 'customizer/typography.php';
    include_once 'customizer/announcement-bar.php';
    include_once 'customizer/blog-settings.php';
    // include_once 'customizer/social-links.php'; // Not yet implemented
    
}

add_action('customize_register', 'krypton_customizer_sections');

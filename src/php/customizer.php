<?php
/**
 * Customizer bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function andreian_customizer_sections( $wp_customize ) {
	include_once 'customizer/site-logos.php';
	include_once 'customizer/layout-settings.php';
	include_once 'customizer/social-links.php';
}
add_action( 'customize_register', 'andreian_customizer_sections' );

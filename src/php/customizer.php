<?php
/**
 * Customizer bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Random slideshow rotation speed, in seconds.
 *
 * Default is 5 so the slideshow keeps the previous hard-coded 5000ms interval.
 *
 * @return array{default:int,min:int,max:int}
 */
function andreian_random_slideshow_speed_config() {
	return array(
		'default' => 5,
		'min'     => 1,
		'max'     => 60,
	);
}

/**
 * Sanitize the Random slideshow rotation speed.
 *
 * @param mixed $value Raw Customizer value, in seconds.
 * @return int Whole seconds within the allowed range, or the default.
 */
function andreian_sanitize_random_slideshow_speed( $value ) {
	$config  = andreian_random_slideshow_speed_config();
	$seconds = absint( $value );

	if ( $seconds < $config['min'] || $seconds > $config['max'] ) {
		return $config['default'];
	}

	return $seconds;
}

/**
 * Autoplay interval for the Random slideshow, in milliseconds.
 *
 * @return int
 */
function andreian_get_random_slideshow_autoplay_ms() {
	$config = andreian_random_slideshow_speed_config();

	return andreian_sanitize_random_slideshow_speed(
		get_theme_mod( 'random_slideshow_speed', $config['default'] )
	) * 1000;
}

function andreian_customizer_sections( $wp_customize ) {
	include_once 'customizer/site-logos.php';
	include_once 'customizer/layout-settings.php';
	include_once 'customizer/social-links.php';
	include_once 'customizer/random-slideshow.php';
}
add_action( 'customize_register', 'andreian_customizer_sections' );

<?php
/**
 * Social links shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render configured social links.
 *
 * @return string
 */
function andreian_social_links_shortcode() {
	return andreian_render_social_links();
}
add_shortcode( 'andreian_social_links', 'andreian_social_links_shortcode' );
add_shortcode( 'social_links', 'andreian_social_links_shortcode' );

<?php
/**
 * Server render callback for the Social Links block.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render social links from Customizer settings.
 *
 * @return string
 */
function andreian_render_social_links_block() {
	$output = andreian_render_social_links();

	if ( ! $output && ( is_admin() || wp_is_json_request() ) ) {
		return '<p class="social-links__empty">' . esc_html__( 'Add social links in Customizer → Social Links.', 'andreian' ) . '</p>';
	}

	return $output;
}

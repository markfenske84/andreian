<?php
/**
 * ConvertKit CTA helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the site newsletter ConvertKit form.
 *
 * @param string $wrapper_class Wrapper class for layout styling.
 * @return void
 */
function andreian_render_convertkit_cta( $wrapper_class = 'post-cta' ) {
	if ( ! function_exists( 'do_shortcode' ) ) {
		return;
	}

	printf(
		'<div class="%1$s">%2$s</div>',
		esc_attr( $wrapper_class ),
		do_shortcode( '[convertkit form=9684991]' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ConvertKit plugin output.
	);
}

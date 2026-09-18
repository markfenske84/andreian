<?php
/**
 * ConvertKit CTA helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Keep ConvertKit QuickTags (and its empty <h1> script template) off public pages.
 *
 * Kit loads editor modal markup on wp_enqueue_scripts for frontend editors.
 * Naive crawlers count that empty heading. Visitors never need the modal.
 */
function andreian_disable_convertkit_frontend_quicktags() {
	if ( is_admin() || ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) ) {
		return;
	}

	if ( ! function_exists( 'WP_ConvertKit' ) || ! class_exists( 'ConvertKit_Admin_TinyMCE' ) ) {
		return;
	}

	$tinymce = WP_ConvertKit()->get_class( 'admin_tinymce' );

	if ( ! $tinymce instanceof ConvertKit_Admin_TinyMCE ) {
		return;
	}

	remove_action( 'wp_enqueue_scripts', array( $tinymce, 'register_quicktags' ) );
}
add_action( 'init', 'andreian_disable_convertkit_frontend_quicktags', 20 );

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

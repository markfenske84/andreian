<?php
/**
 * ConvertKit CTA helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANDREIAN_CONVERTKIT_FORM_ID', '9684991' );
define( 'ANDREIAN_CONVERTKIT_FORM_UID', 'f80dc1f686' );

/**
 * Whether a Kit embed URL belongs to the site newsletter form.
 *
 * @param string $src Script URL.
 * @return bool
 */
function andreian_is_site_kit_embed_src( $src ) {
	$parts = wp_parse_url( $src );

	if ( empty( $parts['scheme'] ) || empty( $parts['host'] ) || empty( $parts['path'] ) ) {
		return false;
	}

	if ( ! in_array( $parts['scheme'], array( 'http', 'https' ), true ) ) {
		return false;
	}

	$host = strtolower( $parts['host'] );

	if ( 'kit.com' !== $host && '.kit.com' !== substr( $host, -8 ) ) {
		return false;
	}

	return false !== strpos( $parts['path'], '/' . ANDREIAN_CONVERTKIT_FORM_UID . '/' );
}

/**
 * Embed URL captured while inline tags are reduced to mount points.
 *
 * @param string|null $src URL to store.
 * @return string
 */
function andreian_kit_embed_src( $src = null ) {
	static $stored = '';

	if ( is_string( $src ) && andreian_is_site_kit_embed_src( $src ) ) {
		$stored = $src;
	}

	return $stored;
}

/**
 * Keep each newsletter form, but load its embed script once.
 *
 * Kit's index.js replaces every script[data-uid] for this form. Stripping src
 * leaves those tags as mount points. The footer prints the script one time.
 *
 * @param array $script Script attributes from the Kit plugin.
 * @return array
 */
function andreian_dedupe_kit_embed_script( $script ) {
	if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_feed() ) {
		return $script;
	}

	if ( ! is_array( $script ) || empty( $script['src'] ) || empty( $script['data-uid'] ) ) {
		return $script;
	}

	if ( (string) $script['data-uid'] !== ANDREIAN_CONVERTKIT_FORM_UID ) {
		return $script;
	}

	if ( ! andreian_is_site_kit_embed_src( (string) $script['src'] ) ) {
		return $script;
	}

	andreian_kit_embed_src( (string) $script['src'] );
	unset( $script['src'] );

	return $script;
}
add_filter( 'convertkit_resource_forms_output_script', 'andreian_dedupe_kit_embed_script' );

/**
 * Print the newsletter embed after form mount points are in the document.
 */
function andreian_print_kit_embed_once() {
	$src = andreian_kit_embed_src();

	if ( '' === $src ) {
		return;
	}

	printf(
		'<script id="andreian-kit-embed" async src="%s"></script>' . "\n",
		esc_url( $src )
	);
}
add_action( 'wp_footer', 'andreian_print_kit_embed_once', 20 );

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
		do_shortcode( '[convertkit form=' . ANDREIAN_CONVERTKIT_FORM_ID . ']' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ConvertKit plugin output.
	);
}

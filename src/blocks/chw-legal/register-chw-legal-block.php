<?php
/**
 * CHW Legal: native (non-ACF) dynamic Gutenberg block.
 *
 * Renders the global legal disclaimer from the Customizer with an optional
 * light or dark text style for different background contexts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed text style values.
 *
 * @return array
 */
function chw_legal_text_style_options() {
	return array( 'dark', 'light' );
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_legal_block_attributes() {
	return array(
		'textStyle' => array(
			'type'    => 'string',
			'default' => 'dark',
		),
	);
}

/**
 * Build wrapper class list from block attributes.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function chw_legal_wrapper_classes( $attributes ) {
	$classes = array( 'chw-legal' );

	$text_style = isset( $attributes['textStyle'] ) ? (string) $attributes['textStyle'] : 'dark';
	if ( ! in_array( $text_style, chw_legal_text_style_options(), true ) ) {
		$text_style = 'dark';
	}

	$classes[] = '-text-' . sanitize_html_class( $text_style );

	return implode( ' ', $classes );
}

/**
 * Server-side render callback for the block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function chw_render_chw_legal_block( $attributes ) {
	$attributes = is_array( $attributes ) ? $attributes : array();

	$legal_text = get_theme_mod( 'legal_text', '' );
	if ( empty( $legal_text ) ) {
		return '';
	}

	$wrapper_classes = chw_legal_wrapper_classes( $attributes );

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => $wrapper_classes ) )
		: 'class="' . esc_attr( $wrapper_classes ) . '"';

	ob_start();
	include __DIR__ . '/chw-legal.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_chw_legal_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/chw-legal.js';

	wp_register_script(
		'chw-legal',
		get_template_directory_uri() . '/src/js/editor/chw-legal.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	wp_localize_script(
		'chw-legal',
		'chwLegalData',
		array(
			'legalText' => get_theme_mod( 'legal_text', '' ),
		)
	);

	register_block_type(
		'chw/chw-legal',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_legal_block_attributes(),
			'editor_script'   => 'chw-legal',
			'render_callback' => 'chw_render_chw_legal_block',
		)
	);
}
add_action( 'init', 'chw_register_chw_legal_block' );

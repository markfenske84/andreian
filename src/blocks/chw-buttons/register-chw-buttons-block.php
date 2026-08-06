<?php
/**
 * CHW Buttons: native (non-ACF) dynamic Gutenberg block.
 *
 * A standalone button repeater with per-button style and icon controls.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed button style slugs (maps to ._button modifier classes).
 *
 * @return array
 */
function chw_buttons_style_options() {
	return array(
		'primary',
		'secondary',
		'tertiary',
		'quaternary',
		'outline',
		'outline-text',
		'outline-primary',
		'outline-secondary',
		'outline-tertiary',
		'outline-white',
	);
}

/**
 * Build ._button class list from a style slug and optional icon modifier.
 *
 * @param string $style    Button style slug.
 * @param string $modifier Optional icon modifier (arrow, phone).
 * @return string
 */
function chw_button_classes_from_style( $style, $modifier = '' ) {
	$style = (string) $style;
	if ( ! in_array( $style, chw_buttons_style_options(), true ) ) {
		$style = 'primary';
	}

	$classes = array( '_button' );

	if ( 'outline' === $style ) {
		$classes[] = '-outline';
	} elseif ( 0 === strpos( $style, 'outline-' ) ) {
		$classes[] = '-outline';
		$classes[] = '-' . sanitize_html_class( substr( $style, strlen( 'outline-' ) ) );
	} else {
		$classes[] = '-' . sanitize_html_class( $style );
	}

	$modifier = (string) $modifier;
	if ( $modifier && function_exists( 'chw_masthead_button_modifiers' ) ) {
		$allowed_modifiers = chw_masthead_button_modifiers();
	} else {
		$allowed_modifiers = array( '', 'arrow', 'phone', 'download' );
	}

	if ( $modifier && in_array( $modifier, $allowed_modifiers, true ) ) {
		$classes[] = '-' . sanitize_html_class( $modifier );
	}

	return implode( ' ', $classes );
}

/**
 * Allowed alignment values (empty string = center default).
 *
 * @return array
 */
function chw_buttons_align_options() {
	return array( 'left', 'right' );
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_buttons_block_attributes() {
	return array(
		'align'   => array(
			'type'    => 'string',
			'default' => '',
		),
		'buttons' => array(
			'type'    => 'array',
			'default' => array(),
		),
	);
}

/**
 * Server-side render callback for the block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function chw_render_chw_buttons_block( $attributes ) {
	$attributes = is_array( $attributes ) ? $attributes : array();

	$wrapper_classes = 'chw-buttons';

	$align = isset( $attributes['align'] ) ? (string) $attributes['align'] : '';
	if ( in_array( $align, chw_buttons_align_options(), true ) ) {
		$wrapper_classes .= ' -align-' . sanitize_html_class( $align );
	}

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => $wrapper_classes ) )
		: 'class="' . esc_attr( $wrapper_classes ) . '"';

	ob_start();
	include __DIR__ . '/chw-buttons.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_chw_buttons_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/chw-buttons.js';

	wp_register_script(
		'chw-buttons',
		get_template_directory_uri() . '/src/js/editor/chw-buttons.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	register_block_type(
		'chw/buttons',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_buttons_block_attributes(),
			'editor_script'   => 'chw-buttons',
			'render_callback' => 'chw_render_chw_buttons_block',
		)
	);
}
add_action( 'init', 'chw_register_chw_buttons_block' );

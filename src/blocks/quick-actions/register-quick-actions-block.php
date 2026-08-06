<?php
/**
 * Quick Actions — native (non-ACF) dynamic Gutenberg block.
 *
 * An orange plain eyelash followed by an unlimited repeater of horizontal
 * action-link cards (icon, label, URL, new-tab toggle, arrow direction),
 * laid out four per row with responsive wrapping.
 *
 * Content is edited in the block editor via src/js/editor/quick-actions.js
 * while the front end is rendered from PHP through the render callback below.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed arrow direction values.
 *
 * @return array
 */
function chw_quick_actions_arrow_directions() {
	return array( 'right', 'up-right', 'down' );
}

/**
 * Return inline arrow SVG markup for a quick-action card.
 *
 * @param string $direction Arrow direction slug.
 * @return string
 */
function chw_quick_actions_arrow_icon( $direction = 'right' ) {
	$allowed = chw_quick_actions_arrow_directions();

	if ( ! in_array( $direction, $allowed, true ) ) {
		$direction = 'right';
	}

	if ( 'down' === $direction ) {
		return '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>';
	}

	if ( 'up-right' === $direction ) {
		return '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="M7 17L17 7M17 7H9M17 7v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>';
	}

	return '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>';
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_quick_actions_block_attributes() {
	return array(
		'eyelashText' => array(
			'type'    => 'string',
			'default' => '',
		),
		'links'       => array(
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
function chw_render_quick_actions_block( $attributes ) {
	$wrapper_classes = 'quick-actions';

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => $wrapper_classes ) )
		: 'class="' . esc_attr( $wrapper_classes ) . '"';

	ob_start();
	include __DIR__ . '/quick-actions.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_quick_actions_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/quick-actions.js';

	wp_register_script(
		'chw-quick-actions',
		get_template_directory_uri() . '/src/js/editor/quick-actions.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	register_block_type(
		'chw/quick-actions',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_quick_actions_block_attributes(),
			'editor_script'   => 'chw-quick-actions',
			'render_callback' => 'chw_render_quick_actions_block',
		)
	);
}
add_action( 'init', 'chw_register_quick_actions_block' );

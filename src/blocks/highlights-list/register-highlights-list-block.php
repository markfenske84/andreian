<?php
/**
 * Highlights List — native (non-ACF) dynamic Gutenberg block.
 *
 * A card-style list with a customizable eyebrow (text + SVG icon) and repeatable
 * description/value rows. Content is edited inline in the editor canvas via
 * src/js/editor/highlights-list.js while the front end is rendered from PHP.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_highlights_list_block_attributes() {
	return array(
		'eyebrowIcon' => array(
			'type'    => 'string',
			'default' => '',
		),
		'eyebrowText' => array(
			'type'    => 'string',
			'default' => '',
		),
		'items'       => array(
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
function chw_render_highlights_list_block( $attributes ) {
	$attributes = is_array( $attributes ) ? $attributes : array();

	$wrapper_classes = 'highlights-list';

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => $wrapper_classes ) )
		: 'class="' . esc_attr( $wrapper_classes ) . '"';

	ob_start();
	include __DIR__ . '/highlights-list.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_highlights_list_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/highlights-list.js';

	wp_register_script(
		'chw-highlights-list',
		get_template_directory_uri() . '/src/js/editor/highlights-list.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	register_block_type(
		'chw/highlights-list',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_highlights_list_block_attributes(),
			'editor_script'   => 'chw-highlights-list',
			'render_callback' => 'chw_render_highlights_list_block',
		)
	);
}
add_action( 'init', 'chw_register_highlights_list_block' );

<?php
/**
 * Linkbank Section — native (non-ACF) dynamic Gutenberg block.
 *
 * A two-column section: a left text column with a plain eyelash, a dual-color
 * heading authored with the core Highlight (text color) format, and an intro
 * paragraph; plus a right column containing an unlimited repeater of links
 * rendered as a single unordered list that flows into four columns.
 *
 * Content is edited inline in the editor canvas via
 * src/js/editor/linkbank-section.js while the front end is rendered from PHP
 * through the render callback below.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_linkbank_section_block_attributes() {
	return array(
		'eyebrowText' => array(
			'type'    => 'string',
			'default' => '',
		),
		'heading'     => array(
			'type'    => 'string',
			'default' => '',
		),
		'description' => array(
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
function chw_render_linkbank_section_block( $attributes ) {
	$wrapper_classes = 'linkbank-section';

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => $wrapper_classes ) )
		: 'class="' . esc_attr( $wrapper_classes ) . '"';

	ob_start();
	include __DIR__ . '/linkbank-section.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_linkbank_section_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/linkbank-section.js';

	wp_register_script(
		'chw-linkbank-section',
		get_template_directory_uri() . '/src/js/editor/linkbank-section.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	register_block_type(
		'chw/linkbank-section',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_linkbank_section_block_attributes(),
			'editor_script'   => 'chw-linkbank-section',
			'render_callback' => 'chw_render_linkbank_section_block',
		)
	);
}
add_action( 'init', 'chw_register_linkbank_section_block' );

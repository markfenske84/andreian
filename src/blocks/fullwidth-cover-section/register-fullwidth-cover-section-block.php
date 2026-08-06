<?php
/**
 * Fullwidth Cover Section: native (non-ACF) dynamic Gutenberg block.
 *
 * A simple full-width section with an editable background (solid color by
 * default, or an image). Everything inside is authored with InnerBlocks, so
 * editors can use core columns and blocks as they normally would.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_fullwidth_cover_section_block_attributes() {
	return array(
		'backgroundType'  => array(
			'type'    => 'string',
			'default' => 'color',
		),
		'backgroundColor' => array(
			'type'    => 'string',
			'default' => '',
		),
		'imageId'         => array(
			'type'    => 'number',
			'default' => 0,
		),
		'imageUrl'        => array(
			'type'    => 'string',
			'default' => '',
		),
		'imageAlt'        => array(
			'type'    => 'string',
			'default' => '',
		),
	);
}

/**
 * Server-side render callback for the block.
 *
 * @param array  $attributes Block attributes.
 * @param string $content    InnerBlocks rendered content.
 * @return string
 */
function chw_render_fullwidth_cover_section_block( $attributes, $content ) {
	ob_start();
	include __DIR__ . '/fullwidth-cover-section.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_fullwidth_cover_section_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/fullwidth-cover-section.js';

	wp_register_script(
		'chw-fullwidth-cover-section',
		get_template_directory_uri() . '/src/js/editor/fullwidth-cover-section.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	register_block_type(
		'chw/fullwidth-cover-section',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_fullwidth_cover_section_block_attributes(),
			'editor_script'   => 'chw-fullwidth-cover-section',
			'render_callback' => 'chw_render_fullwidth_cover_section_block',
		)
	);
}
add_action( 'init', 'chw_register_fullwidth_cover_section_block' );

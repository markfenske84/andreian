<?php
/**
 * Fullwidth Halfscreen: native (non-ACF) dynamic Gutenberg block.
 *
 * A two-up, full-width section: a media half (image or video) and a content
 * half. The content half is authored with InnerBlocks; the collapsible
 * "read more" is achieved by adding a core Details block inside InnerBlocks.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_fullwidth_halfscreen_block_attributes() {
	return array(
		'orientation'       => array(
			'type'    => 'string',
			'default' => '-media-content',
		),
		'mediaType'         => array(
			'type'    => 'string',
			'default' => 'image',
		),
		'imageId'           => array(
			'type'    => 'number',
			'default' => 0,
		),
		'imageUrl'          => array(
			'type'    => 'string',
			'default' => '',
		),
		'imageAlt'          => array(
			'type'    => 'string',
			'default' => '',
		),
		'videoSource'       => array(
			'type'    => 'string',
			'default' => 'self',
		),
		'videoFileId'       => array(
			'type'    => 'number',
			'default' => 0,
		),
		'videoFileUrl'      => array(
			'type'    => 'string',
			'default' => '',
		),
		'videoUrl'          => array(
			'type'    => 'string',
			'default' => '',
		),
		'mediaOverlay'      => array(
			'type'    => 'string',
			'default' => '',
		),
		'contentBackground' => array(
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
function chw_render_fullwidth_halfscreen_block( $attributes, $content ) {
	ob_start();
	include __DIR__ . '/fullwidth-halfscreen.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_fullwidth_halfscreen_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/fullwidth-halfscreen.js';

	wp_register_script(
		'chw-fullwidth-halfscreen',
		get_template_directory_uri() . '/src/js/editor/fullwidth-halfscreen.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	register_block_type(
		'chw/fullwidth-halfscreen',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_fullwidth_halfscreen_block_attributes(),
			'editor_script'   => 'chw-fullwidth-halfscreen',
			'render_callback' => 'chw_render_fullwidth_halfscreen_block',
		)
	);
}
add_action( 'init', 'chw_register_fullwidth_halfscreen_block' );

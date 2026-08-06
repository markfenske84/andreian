<?php
/**
 * Video Modal: native (non-ACF) dynamic Gutenberg block.
 *
 * Content is edited via src/js/editor/video-modal.js, while the front end is
 * rendered from PHP through the render callback below. The frontend behavior
 * (open/close modal) is handled by src/blocks/video-modal/video-modal.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_video_modal_block_attributes() {
	return array(
		'videoSource'     => array(
			'type'    => 'string',
			'default' => 'third-party',
		),
		'videoUrl'        => array(
			'type'    => 'string',
			'default' => '',
		),
		'videoFileId'     => array(
			'type'    => 'number',
			'default' => 0,
		),
		'videoFileUrl'    => array(
			'type'    => 'string',
			'default' => '',
		),
		'thumbnailId'     => array(
			'type'    => 'number',
			'default' => 0,
		),
		'thumbnailUrl'    => array(
			'type'    => 'string',
			'default' => '',
		),
		'thumbnailAlt'    => array(
			'type'    => 'string',
			'default' => '',
		),
		'modalBackground' => array(
			'type'    => 'string',
			'default' => 'rgba(0,0,0,0.8)',
		),
	);
}

/**
 * Server-side render callback for the block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function chw_render_video_modal_block( $attributes ) {
	ob_start();
	include __DIR__ . '/video-modal.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_video_modal_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/video-modal.js';

	wp_register_script(
		'chw-video-modal',
		get_template_directory_uri() . '/src/js/editor/video-modal.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	register_block_type(
		'chw/video-modal',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_video_modal_block_attributes(),
			'editor_script'   => 'chw-video-modal',
			'render_callback' => 'chw_render_video_modal_block',
		)
	);
}
add_action( 'init', 'chw_register_video_modal_block' );

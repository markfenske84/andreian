<?php
/**
 * Awards & Recognition: native (non-ACF) dynamic Gutenberg block.
 *
 * Content is edited inline in the editor canvas via src/js/editor/awards-recognition.js,
 * while the front end is rendered from PHP through the render callback below.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_awards_block_attributes() {
	return array(
		'eyebrow'        => array(
			'type'    => 'string',
			'default' => '',
		),
		'heading'        => array(
			'type'    => 'string',
			'default' => '',
		),
		'intro'   => array(
			'type'    => 'string',
			'default' => '',
		),
		'logos'   => array(
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
function chw_render_awards_block( $attributes ) {
	$wrapper_classes = 'awards-recognition _bg -quaternary';

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => $wrapper_classes ) )
		: 'class="' . esc_attr( $wrapper_classes ) . '"';

	ob_start();
	include __DIR__ . '/awards-recognition.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_awards_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/awards-recognition.js';

	wp_register_script(
		'chw-awards-recognition',
		get_template_directory_uri() . '/src/js/editor/awards-recognition.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	wp_localize_script(
		'chw-awards-recognition',
		'awardsRecognitionData',
		array(
			'legalText' => get_theme_mod( 'legal_text', '' ),
		)
	);

	register_block_type(
		'chw/awards-recognition',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_awards_block_attributes(),
			'editor_script'   => 'chw-awards-recognition',
			'render_callback' => 'chw_render_awards_block',
		)
	);
}
add_action( 'init', 'chw_register_awards_block' );

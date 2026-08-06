<?php
/**
 * Plans Section: native (non-ACF) dynamic Gutenberg block.
 *
 * Content is edited inline in the editor canvas via src/js/editor/plans-section.js,
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
function chw_plans_block_attributes() {
	return array(
		'eyebrow'       => array(
			'type'    => 'string',
			'default' => '',
		),
		'heading'       => array(
			'type'    => 'string',
			'default' => '',
		),
		'headingAccent' => array(
			'type'    => 'string',
			'default' => '',
		),
		'description'   => array(
			'type'    => 'string',
			'default' => '',
		),
		'plans'         => array(
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
function chw_render_plans_block( $attributes ) {
	$wrapper_classes = 'plans-section _bg -quaternary';

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => $wrapper_classes ) )
		: 'class="' . esc_attr( $wrapper_classes ) . '"';

	ob_start();
	include __DIR__ . '/plans-section.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_plans_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/plans-section.js';

	wp_register_script(
		'chw-plans-section',
		get_template_directory_uri() . '/src/js/editor/plans-section.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	// Provide the global legal text to the editor preview.
	wp_localize_script(
		'chw-plans-section',
		'chwPlansData',
		array(
			'legalText' => get_theme_mod( 'legal_text', '' ),
		)
	);

	register_block_type(
		'chw/plans-section',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_plans_block_attributes(),
			'editor_script'   => 'chw-plans-section',
			'render_callback' => 'chw_render_plans_block',
		)
	);
}
add_action( 'init', 'chw_register_plans_block' );

<?php
/**
 * Call To Action: Subtle — native (non-ACF) dynamic Gutenberg block.
 *
 * A contained card with off-white background, headline, supporting text,
 * and a single primary CTA button. Edited inline in the editor canvas via
 * src/js/editor/cta-subtle.js while the front end is rendered from PHP.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_cta_subtle_block_attributes() {
	return array(
		'heading'        => array(
			'type'    => 'string',
			'default' => '',
		),
		'description'    => array(
			'type'    => 'string',
			'default' => '',
		),
		'buttonText'     => array(
			'type'    => 'string',
			'default' => '',
		),
		'buttonUrl'      => array(
			'type'    => 'string',
			'default' => '',
		),
		'buttonModifier' => array(
			'type'    => 'string',
			'default' => '',
		),
		'buttonNewTab'   => array(
			'type'    => 'boolean',
			'default' => false,
		),
	);
}

/**
 * Server-side render callback for the block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function chw_render_cta_subtle_block( $attributes ) {
	$wrapper_classes = 'cta-subtle';

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => $wrapper_classes ) )
		: 'class="' . esc_attr( $wrapper_classes ) . '"';

	ob_start();
	include __DIR__ . '/cta-subtle.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_cta_subtle_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/cta-subtle.js';

	wp_register_script(
		'chw-cta-subtle',
		get_template_directory_uri() . '/src/js/editor/cta-subtle.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	register_block_type(
		'chw/cta-subtle',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_cta_subtle_block_attributes(),
			'editor_script'   => 'chw-cta-subtle',
			'render_callback' => 'chw_render_cta_subtle_block',
		)
	);
}
add_action( 'init', 'chw_register_cta_subtle_block' );

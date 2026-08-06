<?php
/**
 * Call To Action: Banner — native (non-ACF) dynamic Gutenberg block.
 *
 * A navy banner with a left content column (heading, large paragraph, small
 * paragraph) and a right column of CTA buttons (first primary, rest outline).
 * A global legal disclaimer can be shown below the content (pulled from
 * the Customizer legal_text setting; toggleable per block, on by default).
 * inline in the editor canvas via src/js/editor/cta-banner.js while the front
 * end is rendered from PHP.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_cta_banner_block_attributes() {
	return array(
		'heading'     => array(
			'type'    => 'string',
			'default' => '',
		),
		'description' => array(
			'type'    => 'string',
			'default' => '',
		),
		'subText'     => array(
			'type'    => 'string',
			'default' => '',
		),
		'buttons'     => array(
			'type'    => 'array',
			'default' => array(),
		),
		'showLegal'   => array(
			'type'    => 'boolean',
			'default' => true,
		),
	);
}

/**
 * Server-side render callback for the block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function chw_render_cta_banner_block( $attributes ) {
	$wrapper_classes = 'cta-banner';

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => $wrapper_classes ) )
		: 'class="' . esc_attr( $wrapper_classes ) . '"';

	ob_start();
	include __DIR__ . '/cta-banner.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_cta_banner_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/cta-banner.js';

	wp_register_script(
		'chw-cta-banner',
		get_template_directory_uri() . '/src/js/editor/cta-banner.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	wp_localize_script(
		'chw-cta-banner',
		'chwCtaBannerData',
		array(
			'legalText' => get_theme_mod( 'legal_text', '' ),
		)
	);

	register_block_type(
		'chw/cta-banner',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_cta_banner_block_attributes(),
			'editor_script'   => 'chw-cta-banner',
			'render_callback' => 'chw_render_cta_banner_block',
		)
	);
}
add_action( 'init', 'chw_register_cta_banner_block' );

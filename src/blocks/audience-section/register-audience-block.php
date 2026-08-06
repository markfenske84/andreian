<?php
/**
 * Audience Section — native (non-ACF) dynamic Gutenberg block.
 *
 * An eyebrow badge with a custom inline SVG, a dual-color heading authored with
 * the core Highlight (text color) format, an intro paragraph, and an unlimited
 * repeater of clickable, auto-numbered icon cards displayed four per row. The
 * global legal text is always rendered beneath the cards.
 *
 * Content is edited inline in the editor canvas via
 * src/js/editor/audience-section.js while the front end is rendered from PHP
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
function chw_audience_section_block_attributes() {
	return array(
		'eyebrowIcon' => array(
			'type'    => 'string',
			'default' => '',
		),
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
		'cards'       => array(
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
function chw_render_audience_section_block( $attributes ) {
	$wrapper_classes = 'audience-section';

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => $wrapper_classes ) )
		: 'class="' . esc_attr( $wrapper_classes ) . '"';

	ob_start();
	include __DIR__ . '/audience-section.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_audience_section_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/audience-section.js';

	wp_register_script(
		'chw-audience-section',
		get_template_directory_uri() . '/src/js/editor/audience-section.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	wp_localize_script(
		'chw-audience-section',
		'chwAudienceSectionData',
		array(
			'legalText' => get_theme_mod( 'legal_text', '' ),
		)
	);

	register_block_type(
		'chw/audience-section',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_audience_section_block_attributes(),
			'editor_script'   => 'chw-audience-section',
			'render_callback' => 'chw_render_audience_section_block',
		)
	);
}
add_action( 'init', 'chw_register_audience_section_block' );

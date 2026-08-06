<?php
/**
 * Call To Action: Highlights — native (non-ACF) dynamic Gutenberg block.
 *
 * A fixed-style navy CTA box with a left content column (badge, h1-styled
 * heading, paragraph, CTA buttons, sub-text, optional legal text) and a right
 * info box (eyebrow, checkmark highlights, and a final paragraph with link
 * support). Content is edited inline in the editor canvas via
 * src/js/editor/cta-highlights.js while the front end is rendered from PHP.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_cta_highlights_block_attributes() {
	return array(
		'badgeText'   => array(
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
		'buttons'     => array(
			'type'    => 'array',
			'default' => array(),
		),
		'subText'     => array(
			'type'    => 'string',
			'default' => '',
		),
		'showLegal'   => array(
			'type'    => 'boolean',
			'default' => false,
		),
		'infoEyebrow' => array(
			'type'    => 'string',
			'default' => '',
		),
		'highlights'  => array(
			'type'    => 'array',
			'default' => array(),
		),
		'infoText'    => array(
			'type'    => 'string',
			'default' => '',
		),
	);
}

/**
 * Server-side render callback for the block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function chw_render_cta_highlights_block( $attributes ) {
	$wrapper_classes = 'cta-highlights';

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => $wrapper_classes ) )
		: 'class="' . esc_attr( $wrapper_classes ) . '"';

	ob_start();
	include __DIR__ . '/cta-highlights.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_cta_highlights_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/cta-highlights.js';

	wp_register_script(
		'chw-cta-highlights',
		get_template_directory_uri() . '/src/js/editor/cta-highlights.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	wp_localize_script(
		'chw-cta-highlights',
		'chwCtaHighlightsData',
		array(
			'legalText' => get_theme_mod( 'legal_text', '' ),
		)
	);

	register_block_type(
		'chw/cta-highlights',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_cta_highlights_block_attributes(),
			'editor_script'   => 'chw-cta-highlights',
			'render_callback' => 'chw_render_cta_highlights_block',
		)
	);
}
add_action( 'init', 'chw_register_cta_highlights_block' );

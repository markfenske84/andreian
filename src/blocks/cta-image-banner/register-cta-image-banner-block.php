<?php
/**
 * Call To Action: Image Banner — native (non-ACF) dynamic Gutenberg block.
 *
 * A contained image-background CTA card with configurable SVG icon, badge,
 * heading, support text, CTA button repeater, and optional global legal text.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default document icon for the circular icon wrapper.
 *
 * @return string
 */
function chw_cta_image_banner_default_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/><path d="M14 2v6h6M9 13h6M9 17h6M9 9h1" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

/**
 * Default background image URL.
 *
 * @return string
 */
function chw_cta_image_banner_default_image_url() {
	return get_template_directory_uri() . '/assets/images/claim-house.webp';
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_cta_image_banner_block_attributes() {
	return array(
		'icon'        => array(
			'type'    => 'string',
			'default' => '',
		),
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
		'showLegal'   => array(
			'type'    => 'boolean',
			'default' => true,
		),
		'imageId'     => array(
			'type'    => 'number',
			'default' => 0,
		),
		'imageUrl'    => array(
			'type'    => 'string',
			'default' => '',
		),
		'imageAlt'    => array(
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
function chw_render_cta_image_banner_block( $attributes ) {
	$wrapper_classes = 'cta-image-banner';

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => $wrapper_classes ) )
		: 'class="' . esc_attr( $wrapper_classes ) . '"';

	ob_start();
	include __DIR__ . '/cta-image-banner.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_cta_image_banner_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/cta-image-banner.js';

	wp_register_script(
		'chw-cta-image-banner',
		get_template_directory_uri() . '/src/js/editor/cta-image-banner.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	wp_localize_script(
		'chw-cta-image-banner',
		'chwCtaImageBannerData',
		array(
			'legalText'       => get_theme_mod( 'legal_text', '' ),
			'defaultImageUrl' => chw_cta_image_banner_default_image_url(),
			'defaultIcon'     => chw_cta_image_banner_default_icon(),
		)
	);

	register_block_type(
		'chw/cta-image-banner',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_cta_image_banner_block_attributes(),
			'supports'        => array(
				'anchor' => true,
			),
			'editor_script'   => 'chw-cta-image-banner',
			'render_callback' => 'chw_render_cta_image_banner_block',
		)
	);
}
add_action( 'init', 'chw_register_cta_image_banner_block' );

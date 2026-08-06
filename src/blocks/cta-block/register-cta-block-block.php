<?php
/**
 * CTA Block — native (non-ACF) dynamic Gutenberg block.
 *
 * A centered navy call-to-action box with badge, heading, description, CTA
 * buttons, and the global legal disclaimer. Container width with rounded
 * borders; content is edited inline in the editor canvas via
 * src/js/editor/cta-block.js while the front end is rendered from PHP.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Static shield icon used in the badge.
 *
 * @return string
 */
function chw_cta_block_badge_icon() {
	return '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="M12 2l8 4v6c0 5.25-3.5 10-8 12-4.5-2-8-6.75-8-12V6l8-4z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_cta_block_block_attributes() {
	return array(
		'align'       => array(
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
	);
}

/**
 * Server-side render callback for the block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function chw_render_cta_block_block( $attributes ) {
	$wrapper_classes = 'cta-block';

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => $wrapper_classes ) )
		: 'class="' . esc_attr( $wrapper_classes ) . '"';

	ob_start();
	include __DIR__ . '/cta-block.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_cta_block_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/cta-block.js';

	wp_register_script(
		'chw-cta-block',
		get_template_directory_uri() . '/src/js/editor/cta-block.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	wp_localize_script(
		'chw-cta-block',
		'chwCtaBlockData',
		array(
			'legalText' => get_theme_mod( 'legal_text', '' ),
		)
	);

	register_block_type(
		'chw/cta-block',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_cta_block_block_attributes(),
			'supports'        => array(
				'anchor' => true,
				'align'  => array( 'wide', 'full' ),
			),
			'editor_script'   => 'chw-cta-block',
			'render_callback' => 'chw_render_cta_block_block',
		)
	);
}
add_action( 'init', 'chw_register_cta_block_block' );

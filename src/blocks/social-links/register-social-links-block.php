<?php
/**
 * Social Links: native (non-ACF) dynamic Gutenberg block.
 *
 * The block has no content attributes — it renders the global social profile
 * links managed in the Customizer (Appearance > Customize > Social Links) via
 * chw_get_social_links().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Server-side render callback for the block.
 *
 * @return string
 */
function chw_render_social_links_block() {
	ob_start();
	include __DIR__ . '/social-links.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_social_links_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/social-links.js';

	wp_register_script(
		'chw-social-links',
		get_template_directory_uri() . '/src/js/editor/social-links.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	// Provide the Customizer-managed links to the editor preview.
	$links = chw_get_social_links();
	wp_localize_script(
		'chw-social-links',
		'chwSocialLinksData',
		array(
			'links' => array_values( $links ),
		)
	);

	register_block_type(
		'chw/social-links',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'editor_script'   => 'chw-social-links',
			'render_callback' => 'chw_render_social_links_block',
		)
	);
}
add_action( 'init', 'chw_register_social_links_block' );

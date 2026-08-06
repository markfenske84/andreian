<?php
/**
 * Comparison Table: native (non-ACF) dynamic Gutenberg block.
 *
 * Content is edited inline in the editor canvas via src/js/editor/comparison-table.js,
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
function chw_comparison_table_block_attributes() {
	return array(
		'eyebrow'     => array(
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
		'comparisons' => array(
			'type'    => 'array',
			'default' => array(),
		),
		'button'      => array(
			'type'    => 'object',
			'default' => array(
				'text'     => '',
				'url'      => '',
				'new_tab'  => false,
				'modifier' => 'arrow',
			),
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
function chw_render_comparison_table_block( $attributes ) {
	$wrapper_classes = 'comparison-table';

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => $wrapper_classes ) )
		: 'class="' . esc_attr( $wrapper_classes ) . '"';

	ob_start();
	include __DIR__ . '/comparison-table.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_comparison_table_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/comparison-table.js';

	wp_register_script(
		'chw-comparison-table',
		get_template_directory_uri() . '/src/js/editor/comparison-table.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	wp_localize_script(
		'chw-comparison-table',
		'chwComparisonTableData',
		array(
			'legalText' => get_theme_mod( 'legal_text', '' ),
		)
	);

	register_block_type(
		'chw/comparison-table',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_comparison_table_block_attributes(),
			'editor_script'   => 'chw-comparison-table',
			'render_callback' => 'chw_render_comparison_table_block',
		)
	);
}
add_action( 'init', 'chw_register_comparison_table_block' );

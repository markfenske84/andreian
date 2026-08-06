<?php
/**
 * Core Columns block — reverse stack order on mobile.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register chwReverseMobile attribute server-side.
 *
 * @param array  $args        Block type registration arguments.
 * @param string $block_type  Block name.
 * @return array
 */
function chw_register_columns_reverse_mobile_attribute( $args, $block_type ) {
	if ( 'core/columns' !== $block_type ) {
		return $args;
	}

	$args['attributes']['chwReverseMobile'] = array(
		'type'    => 'boolean',
		'default' => false,
	);

	return $args;
}
add_filter( 'register_block_type_args', 'chw_register_columns_reverse_mobile_attribute', 10, 2 );

/**
 * Enqueue block editor script.
 */
function chw_enqueue_columns_reverse_mobile() {
	$script_path = get_template_directory() . '/src/js/editor/columns-reverse-mobile.js';

	wp_enqueue_script(
		'chw-columns-reverse-mobile',
		get_template_directory_uri() . '/src/js/editor/columns-reverse-mobile.js',
		array( 'wp-hooks', 'wp-compose', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'chw_enqueue_columns_reverse_mobile' );

/**
 * Ensure reverse-mobile class is present on rendered columns blocks.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data.
 * @return string
 */
function chw_render_columns_reverse_mobile( $block_content, $block ) {
	if ( 'core/columns' !== ( $block['blockName'] ?? '' ) ) {
		return $block_content;
	}

	if ( empty( $block['attrs']['chwReverseMobile'] ) ) {
		return $block_content;
	}

	if ( str_contains( $block_content, 'is-chw-reverse-mobile' ) ) {
		return $block_content;
	}

	$processor = new WP_HTML_Tag_Processor( $block_content );

	if ( $processor->next_tag() ) {
		$processor->add_class( 'is-chw-reverse-mobile' );
		return $processor->get_updated_html();
	}

	return $block_content;
}
add_filter( 'render_block', 'chw_render_columns_reverse_mobile', 10, 2 );

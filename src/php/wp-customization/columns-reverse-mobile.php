<?php
/**
 * Core Columns block — reverse stack order on mobile.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register andreianReverseMobile attribute server-side.
 *
 * @param array  $args        Block type registration arguments.
 * @param string $block_type  Block name.
 * @return array
 */
function andreian_register_columns_reverse_mobile_attribute( $args, $block_type ) {
	if ( 'core/columns' !== $block_type ) {
		return $args;
	}

	$args['attributes']['andreianReverseMobile'] = array(
		'type'    => 'boolean',
		'default' => false,
	);

	return $args;
}
add_filter( 'register_block_type_args', 'andreian_register_columns_reverse_mobile_attribute', 10, 2 );

/**
 * Enqueue block editor script.
 */
function andreian_enqueue_columns_reverse_mobile() {
	$script_path = get_template_directory() . '/src/js/editor/columns-reverse-mobile.js';

	wp_enqueue_script(
		'andreian-columns-reverse-mobile',
		get_template_directory_uri() . '/src/js/editor/columns-reverse-mobile.js',
		array( 'wp-hooks', 'wp-compose', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n' ),
		andreian_asset_version( $script_path ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'andreian_enqueue_columns_reverse_mobile' );

/**
 * Ensure reverse-mobile class is present on rendered columns blocks.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data.
 * @return string
 */
function andreian_render_columns_reverse_mobile( $block_content, $block ) {
	if ( 'core/columns' !== ( $block['blockName'] ?? '' ) ) {
		return $block_content;
	}

	if ( empty( $block['attrs']['andreianReverseMobile'] ) ) {
		return $block_content;
	}

	if ( str_contains( $block_content, 'is-andreian-reverse-mobile' ) ) {
		return $block_content;
	}

	$processor = new WP_HTML_Tag_Processor( $block_content );

	if ( $processor->next_tag() ) {
		$processor->add_class( 'is-andreian-reverse-mobile' );
		return $processor->get_updated_html();
	}

	return $block_content;
}
add_filter( 'render_block', 'andreian_render_columns_reverse_mobile', 10, 2 );

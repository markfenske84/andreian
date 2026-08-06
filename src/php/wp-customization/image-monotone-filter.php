<?php
/**
 * Core Image block — Monotone black/white filter.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register chwMonotone attribute server-side.
 */
function chw_register_image_monotone_attribute( $args, $block_type ) {
	if ( $block_type !== 'core/image' ) {
		return $args;
	}

	$args['attributes']['chwMonotone'] = array(
		'type'    => 'string',
		'default' => '',
	);

	return $args;
}
add_filter( 'register_block_type_args', 'chw_register_image_monotone_attribute', 10, 2 );

/**
 * Enqueue block editor script and styles.
 */
function chw_enqueue_image_monotone_filter() {
	$script_path = get_template_directory() . '/src/js/editor/image-monotone-filter.js';

	wp_enqueue_script(
		'chw-image-monotone-filter',
		get_template_directory_uri() . '/src/js/editor/image-monotone-filter.js',
		array( 'wp-hooks', 'wp-compose', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'chw_enqueue_image_monotone_filter' );

/**
 * Ensure monotone class is present on rendered image blocks.
 */
function chw_render_image_monotone_filter( $block_content, $block ) {
	if ( $block['blockName'] !== 'core/image' ) {
		return $block_content;
	}

	$monotone = $block['attrs']['chwMonotone'] ?? '';

	if ( ! $monotone || ! in_array( $monotone, array( 'normal', 'inverted' ), true ) ) {
		return $block_content;
	}

	$class = 'has-chw-monotone-' . $monotone;

	if ( str_contains( $block_content, $class ) ) {
		return $block_content;
	}

	$processor = new WP_HTML_Tag_Processor( $block_content );

	if ( $processor->next_tag( array( 'class_name' => 'wp-block-image' ) ) ) {
		$processor->add_class( $class );
		return $processor->get_updated_html();
	}

	return $block_content;
}
add_filter( 'render_block', 'chw_render_image_monotone_filter', 10, 2 );

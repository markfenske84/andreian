<?php
/**
 * Core Image block — Monotone black/white filter.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register andreianMonotone attribute server-side.
 */
function andreian_register_image_monotone_attribute( $args, $block_type ) {
	if ( $block_type !== 'core/image' ) {
		return $args;
	}

	$args['attributes']['andreianMonotone'] = array(
		'type'    => 'string',
		'default' => '',
	);

	return $args;
}
add_filter( 'register_block_type_args', 'andreian_register_image_monotone_attribute', 10, 2 );

/**
 * Enqueue block editor script and styles.
 */
function andreian_enqueue_image_monotone_filter() {
	$script_path = get_template_directory() . '/src/js/editor/image-monotone-filter.js';

	wp_enqueue_script(
		'andreian-image-monotone-filter',
		get_template_directory_uri() . '/src/js/editor/image-monotone-filter.js',
		array( 'wp-hooks', 'wp-compose', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n' ),
		andreian_asset_version( $script_path ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'andreian_enqueue_image_monotone_filter' );

/**
 * Ensure monotone class is present on rendered image blocks.
 */
function andreian_render_image_monotone_filter( $block_content, $block ) {
	if ( $block['blockName'] !== 'core/image' ) {
		return $block_content;
	}

	$monotone = $block['attrs']['andreianMonotone'] ?? '';

	if ( ! $monotone || ! in_array( $monotone, array( 'normal', 'inverted' ), true ) ) {
		return $block_content;
	}

	$class = 'has-andreian-monotone-' . $monotone;

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
add_filter( 'render_block', 'andreian_render_image_monotone_filter', 10, 2 );

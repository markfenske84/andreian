<?php
/**
 * Stats and Testimonials: native (non-ACF) dynamic Gutenberg block.
 *
 * A full-width section over an editable grayscale background image. It contains
 * a repeatable stats card (max 4) and repeatable testimonial cards (max 3).
 * Content is edited inline in the editor canvas via
 * src/js/editor/stats-testimonials.js, while the front end is rendered from PHP
 * through the render callback below.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default background image used when no custom image is selected.
 *
 * @return string
 */
function chw_stats_testimonials_default_background() {
	return get_template_directory_uri() . '/assets/images/bg-01.webp';
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_stats_testimonials_block_attributes() {
	return array(
		'backgroundImageId'  => array(
			'type'    => 'number',
			'default' => 0,
		),
		'backgroundImageUrl' => array(
			'type'    => 'string',
			'default' => '',
		),
		'stats'              => array(
			'type'    => 'array',
			'default' => array(),
		),
		'testimonials'       => array(
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
function chw_render_stats_testimonials_block( $attributes ) {
	ob_start();
	include __DIR__ . '/stats-testimonials.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_stats_testimonials_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/stats-testimonials.js';

	wp_register_script(
		'chw-stats-testimonials',
		get_template_directory_uri() . '/src/js/editor/stats-testimonials.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	wp_localize_script(
		'chw-stats-testimonials',
		'chwStatsTestimonialsData',
		array(
			'defaultBackground' => chw_stats_testimonials_default_background(),
		)
	);

	register_block_type(
		'chw/stats-testimonials',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_stats_testimonials_block_attributes(),
			'editor_script'   => 'chw-stats-testimonials',
			'render_callback' => 'chw_render_stats_testimonials_block',
		)
	);
}
add_action( 'init', 'chw_register_stats_testimonials_block' );

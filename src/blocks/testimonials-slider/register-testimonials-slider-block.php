<?php
/**
 * Testimonials Slider: native (non-ACF) dynamic Gutenberg block.
 *
 * Pulls ordered testimonial CPT posts into a Swiper carousel.
 * Content is selected in the editor via src/js/editor/testimonials-slider.js,
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
function chw_testimonials_slider_block_attributes() {
	return array(
		'testimonialIds' => array(
			'type'    => 'array',
			'default' => array(),
		),
	);
}

/**
 * Get the first character of a name for the avatar initial.
 *
 * @param string $name Post title / author name.
 * @return string
 */
function chw_testimonials_slider_get_initial( $name ) {
	$name = trim( wp_strip_all_tags( (string) $name ) );
	if ( '' === $name ) {
		return '';
	}

	if ( function_exists( 'mb_substr' ) && function_exists( 'mb_strtoupper' ) ) {
		return mb_strtoupper( mb_substr( $name, 0, 1 ) );
	}

	return strtoupper( substr( $name, 0, 1 ) );
}

/**
 * Get the location label for a testimonial post.
 *
 * @param int $post_id Testimonial post ID.
 * @return string
 */
function chw_testimonials_slider_get_location( $post_id ) {
	$terms = get_the_terms( $post_id, 'location' );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '';
	}

	$names = wp_list_pluck( $terms, 'name' );

	return implode( ', ', array_map( 'wp_strip_all_tags', $names ) );
}

/**
 * Fetch testimonial items in the order specified by post IDs.
 *
 * @param array $ids Ordered testimonial post IDs.
 * @return array<int, array{id: int, name: string, location: string, quote: string, initial: string}>
 */
function chw_testimonials_slider_get_items( array $ids ) {
	$ids = array_values(
		array_filter(
			array_map( 'absint', $ids ),
			function ( $id ) {
				return $id > 0;
			}
		)
	);

	if ( empty( $ids ) ) {
		return array();
	}

	$posts = get_posts(
		array(
			'post_type'              => 'testimonial',
			'post__in'               => $ids,
			'orderby'                => 'post__in',
			'posts_per_page'         => count( $ids ),
			'post_status'            => 'publish',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => true,
		)
	);

	if ( empty( $posts ) ) {
		return array();
	}

	$items = array();

	foreach ( $posts as $post ) {
		$name = get_the_title( $post );

		$items[] = array(
			'id'       => (int) $post->ID,
			'name'     => $name,
			'location' => chw_testimonials_slider_get_location( $post->ID ),
			'quote'    => apply_filters( 'the_content', $post->post_content ),
			'initial'  => chw_testimonials_slider_get_initial( $name ),
		);
	}

	return $items;
}

/**
 * Server-side render callback for the block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function chw_render_testimonials_slider_block( $attributes ) {
	$attributes = is_array( $attributes ) ? $attributes : array();
	$ids        = isset( $attributes['testimonialIds'] ) && is_array( $attributes['testimonialIds'] )
		? $attributes['testimonialIds']
		: array();

	$items = chw_testimonials_slider_get_items( $ids );

	if ( empty( $items ) ) {
		return '';
	}

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => 'testimonials-slider' ) )
		: 'class="testimonials-slider"';

	ob_start();
	include __DIR__ . '/testimonials-slider.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_testimonials_slider_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/testimonials-slider.js';

	wp_register_script(
		'chw-testimonials-slider',
		get_template_directory_uri() . '/src/js/editor/testimonials-slider.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch' ),
		chw_asset_version( $script_path ),
		true
	);

	register_block_type(
		'chw/testimonials-slider',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_testimonials_slider_block_attributes(),
			'editor_script'   => 'chw-testimonials-slider',
			'render_callback' => 'chw_render_testimonials_slider_block',
		)
	);
}
add_action( 'init', 'chw_register_testimonials_slider_block' );

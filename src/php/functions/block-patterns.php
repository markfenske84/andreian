<?php
/**
 * Block pattern helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve block pattern content by slug.
 *
 * Checks the block pattern registry (theme-registered patterns) and synced
 * patterns saved as wp_block posts.
 *
 * @param string $slug Pattern slug (e.g. generic-cta-banner or chw/generic-cta-banner).
 * @return string Block markup or empty string when not found.
 */
function chw_get_block_pattern_content( $slug ) {
	$slug = sanitize_title( $slug );

	if ( '' === $slug ) {
		return '';
	}

	$theme_slug = sanitize_title( get_stylesheet() );
	$candidates = array_unique(
		array(
			$slug,
			$theme_slug . '/' . $slug,
		)
	);

	if ( class_exists( 'WP_Block_Patterns_Registry' ) ) {
		$registry = WP_Block_Patterns_Registry::get_instance();

		foreach ( $candidates as $pattern_slug ) {
			$pattern = $registry->get_registered( $pattern_slug );

			if ( ! empty( $pattern['content'] ) ) {
				return $pattern['content'];
			}
		}
	}

	$pattern_post = get_page_by_path( $slug, OBJECT, 'wp_block' );

	if ( $pattern_post instanceof WP_Post && 'publish' === $pattern_post->post_status ) {
		return $pattern_post->post_content;
	}

	return '';
}

/**
 * Render a registered block pattern by slug.
 *
 * @param string $slug Pattern slug.
 * @return void
 */
function chw_render_block_pattern( $slug ) {
	$content = chw_get_block_pattern_content( $slug );

	if ( '' === $content ) {
		return;
	}

	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Block markup is rendered by core.
	echo do_blocks( $content );
}

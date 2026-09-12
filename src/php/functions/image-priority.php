<?php
/**
 * Eager-load above-the-fold images (homepage featured, post hero, leading content).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether raw post content begins with an image.
 *
 * @param string $content Post content.
 * @return bool
 */
function andreian_post_content_starts_with_image( $content ) {
	$trimmed = ltrim( (string) $content );

	if ( '' === $trimmed ) {
		return false;
	}

	if ( preg_match( '/^<!--\s+wp:image\b/', $trimmed ) ) {
		return true;
	}

	return (bool) preg_match(
		'/^(?:\s|<p>\s*<\/p>)*'
		. '(?:\[caption\b|<figure\b|<div\b[^>]*wp-caption|<p\b[^>]*>\s*(?:<a\b[^>]*>\s*)?<img\b|<img\b)/i',
		$trimmed
	);
}

/**
 * First image attachment in leading post content, if any.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function andreian_get_leading_content_image_id( $post_id ) {
	$content = (string) get_post_field( 'post_content', $post_id );

	if ( ! andreian_post_content_starts_with_image( $content ) ) {
		return 0;
	}

	if ( preg_match( '/^<!--\s+wp:image\s+(\{.*?\})\s+-->/s', ltrim( $content ), $json_match ) ) {
		$attrs = json_decode( $json_match[1], true );

		if ( ! empty( $attrs['id'] ) ) {
			return (int) $attrs['id'];
		}
	}

	if ( preg_match( '/\bwp-image-(\d+)\b/', $content, $class_match ) ) {
		return (int) $class_match[1];
	}

	return 0;
}

/**
 * Print a high-priority image preload.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $size          Registered image size.
 * @param string $sizes         sizes attribute.
 * @return void
 */
function andreian_print_image_preload( $attachment_id, $size, $sizes ) {
	$attachment_id = (int) $attachment_id;

	if ( ! $attachment_id ) {
		return;
	}

	$src = wp_get_attachment_image_src( $attachment_id, $size );

	if ( ! $src ) {
		return;
	}

	$srcset = wp_get_attachment_image_srcset( $attachment_id, $size );
	$attrs  = sprintf( ' href="%s"', esc_url( $src[0] ) );

	if ( $srcset ) {
		$attrs .= sprintf( ' imagesrcset="%s"', esc_attr( $srcset ) );
	}

	if ( $sizes ) {
		$attrs .= sprintf( ' imagesizes="%s"', esc_attr( $sizes ) );
	}

	printf( '<link rel="preload" as="image"%s fetchpriority="high">' . "\n", $attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built with esc_url/esc_attr above.
}

/**
 * Preload the likely LCP image for the current view.
 *
 * @return void
 */
function andreian_preload_lcp_image() {
	if ( is_singular( 'post' ) ) {
		$post_id = get_queried_object_id();

		if ( has_post_thumbnail( $post_id ) ) {
			andreian_print_image_preload(
				(int) get_post_thumbnail_id( $post_id ),
				'andreian-feature',
				'100vw'
			);
			return;
		}

		$leading_id = andreian_get_leading_content_image_id( $post_id );

		if ( $leading_id ) {
			andreian_print_image_preload( $leading_id, 'large', '(max-width: 1200px) 100vw, 1200px' );
		}
	}
}
add_action( 'wp_head', 'andreian_preload_lcp_image', 2 );

/**
 * Honor fetchpriority=high so core does not re-apply lazy loading.
 *
 * @param array  $loading_attrs Loading attributes.
 * @param string $tag_name      Tag name.
 * @param array  $attr          Existing attributes.
 * @param string $context       Caller context.
 * @return array
 */
function andreian_preserve_priority_loading_attrs( $loading_attrs, $tag_name, $attr, $context ) {
	unset( $context );

	if ( 'img' !== $tag_name ) {
		return $loading_attrs;
	}

	if ( empty( $attr['fetchpriority'] ) || 'high' !== $attr['fetchpriority'] ) {
		return $loading_attrs;
	}

	$loading_attrs['loading']       = 'eager';
	$loading_attrs['fetchpriority'] = 'high';

	return $loading_attrs;
}
add_filter( 'wp_get_loading_optimization_attributes', 'andreian_preserve_priority_loading_attrs', 20, 4 );

/**
 * Eager-load a leading in-content image when it is the first-viewport photo.
 *
 * Posts with a featured-image hero already have an above-the-fold LCP image;
 * a later content image stays lazy. Posts that open with an image and have no
 * hero get that first image marked eager.
 *
 * @param string $content Post HTML.
 * @return string
 */
function andreian_eager_leading_content_image( $content ) {
	if ( is_admin() || ! is_singular( 'post' ) || has_post_thumbnail() ) {
		return $content;
	}

	if ( ! andreian_post_content_starts_with_image( get_post_field( 'post_content', get_queried_object_id() ) ) ) {
		return $content;
	}

	if ( ! preg_match( '/<img\b[^>]*>/i', $content ) ) {
		return $content;
	}

	return preg_replace_callback(
		'/<img\b[^>]*>/i',
		static function ( $match ) {
			$img = $match[0];
			$img = preg_replace( '/\sloading=(["\'])[^"\']*\1/i', '', $img );
			$img = preg_replace( '/\sfetchpriority=(["\'])[^"\']*\1/i', '', $img );

			return preg_replace( '/<img\b/i', '<img loading="eager" fetchpriority="high"', $img, 1 );
		},
		$content,
		1
	);
}
add_filter( 'the_content', 'andreian_eager_leading_content_image', 20 );

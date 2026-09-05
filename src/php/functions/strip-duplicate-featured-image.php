<?php
/**
 * Remove a leading post-content image that duplicates the featured image.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether HTML refers to the featured image.
 *
 * @param string $html          Image markup.
 * @param int    $thumbnail_id  Featured attachment ID.
 * @return bool
 */
function andreian_html_is_featured_image( $html, $thumbnail_id ) {
	$thumbnail_id = (int) $thumbnail_id;

	if ( $thumbnail_id && preg_match( '/wp-image-' . $thumbnail_id . '\b/', $html ) ) {
		return true;
	}

	if ( $thumbnail_id && preg_match( '/attachment_' . $thumbnail_id . '\b/', $html ) ) {
		return true;
	}

	$file = $thumbnail_id ? get_attached_file( $thumbnail_id ) : '';
	$name = $file ? pathinfo( wp_basename( $file ), PATHINFO_FILENAME ) : '';

	if ( $name && preg_match( '/' . preg_quote( $name, '/' ) . '[^"\']*\.(?:jpe?g|png|gif|webp|avif)/i', $html ) ) {
		return true;
	}

	return false;
}

/**
 * Remove a leading featured-image duplicate from content.
 *
 * @param string $content      Post content.
 * @param int    $thumbnail_id Featured attachment ID.
 * @return string
 */
function andreian_strip_leading_featured_image( $content, $thumbnail_id ) {
	$thumbnail_id = (int) $thumbnail_id;

	if ( ! $thumbnail_id || '' === trim( $content ) ) {
		return $content;
	}

	$leading = '/^(?:\s|<p>\s*<\/p>)+/i';

	if ( preg_match( '/^<!--\s+wp:image\s+(\{.*?\})\s+-->/s', ltrim( $content ), $json_match ) ) {
		$attrs = json_decode( $json_match[1], true );
		$block = preg_replace( '/^<!--\s+wp:image.*?<!--\s+\/wp:image\s+-->\s*/s', '', ltrim( $content ), 1 );

		if ( is_string( $block ) && $block !== ltrim( $content ) ) {
			$matches_id  = ! empty( $attrs['id'] ) && (int) $attrs['id'] === $thumbnail_id;
			$matches_src = andreian_html_is_featured_image( $json_match[0], $thumbnail_id );

			if ( $matches_id || $matches_src ) {
				return $block;
			}
		}
	}

	$pattern = '/^'
		. '(?:\s|<p>\s*<\/p>)*'
		. '('
		. '\[caption[^\]]*\][\s\S]*?\[\/caption\]'
		. '|<figure\b[^>]*>[\s\S]*?<\/figure>'
		. '|<div\b[^>]*wp-caption[^>]*>[\s\S]*?<\/div>'
		. '|<p\b[^>]*>\s*(?:<a\b[^>]*>\s*)?<img\b[^>]*>\s*(?:<\/a>\s*)?(?:<br\s*\/?>\s*)?<\/p>'
		. '|<img\b[^>]*>'
		. ')'
		. '\s*/i';

	if ( preg_match( $pattern, $content, $match ) && andreian_html_is_featured_image( $match[1], $thumbnail_id ) ) {
		$stripped = substr( $content, strlen( $match[0] ) );
		$stripped = preg_replace( $leading, '', $stripped );

		return is_string( $stripped ) ? $stripped : $content;
	}

	return $content;
}

/**
 * Strip leading featured-image duplicates from posts.
 *
 * @param bool $dry_run Count only.
 * @return int Number of posts updated (or that would update).
 */
function andreian_strip_duplicate_featured_images( $dry_run = false ) {
	$query = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'any',
			'posts_per_page'         => -1,
			'meta_key'               => '_thumbnail_id',
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		)
	);

	$updated = 0;

	foreach ( $query->posts as $post ) {
		$thumbnail_id = (int) get_post_thumbnail_id( $post );
		$cleaned      = andreian_strip_leading_featured_image( $post->post_content, $thumbnail_id );

		if ( $cleaned === $post->post_content ) {
			continue;
		}

		++$updated;

		if ( $dry_run ) {
			continue;
		}

		wp_update_post(
			array(
				'ID'           => $post->ID,
				'post_content' => $cleaned,
			),
			true
		);
	}

	return $updated;
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * Remove a leading content image that matches the featured image.
	 *
	 * ## OPTIONS
	 *
	 * [--dry-run]
	 * : Count matches without updating posts.
	 *
	 * ## EXAMPLES
	 *
	 *     wp andreian strip-duplicate-featured
	 *     wp andreian strip-duplicate-featured --dry-run
	 *
	 * @when after_wp_load
	 */
	WP_CLI::add_command(
		'andreian strip-duplicate-featured',
		function ( $args, $assoc_args ) {
			$dry_run = ! empty( $assoc_args['dry-run'] );
			$updated = andreian_strip_duplicate_featured_images( $dry_run );
			$message = $dry_run
				? 'Would remove a leading featured-image duplicate from %d post(s).'
				: 'Removed a leading featured-image duplicate from %d post(s).';
			WP_CLI::success( sprintf( $message, $updated ) );
		}
	);
}

<?php
/**
 * Table of contents helpers and heading ID injection.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Heading regex for h2–h3.
 *
 * @return string
 */
function andreian_toc_heading_pattern() {
	return '/<h([2-3])(\s[^>]*)?>(.*?)<\/h\1>/is';
}

/**
 * Visible heading label.
 *
 * @param string $inner Heading inner HTML.
 * @return string
 */
function andreian_toc_heading_label( $inner ) {
	$label = wp_strip_all_tags( $inner );
	$label = html_entity_decode( $label, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$label = preg_replace( '/\s+/u', ' ', $label );

	return trim( $label );
}

/**
 * Unique slug for a heading label.
 *
 * @param string   $label Heading text.
 * @param string[] $used  Slugs already assigned.
 * @return string
 */
function andreian_toc_unique_id( $label, array &$used ) {
	$id   = function_exists( 'slugify' ) ? slugify( $label ) : sanitize_title( $label );
	$base = $id;
	$n    = 2;

	while ( in_array( $id, $used, true ) ) {
		$id = $base . '-' . $n;
		++$n;
	}

	$used[] = $id;

	return $id;
}

/**
 * Parse headings from HTML.
 *
 * @param string $html Post HTML.
 * @return array<int, array{level:int,id:string,label:string}>
 */
function andreian_parse_content_headings( $html ) {
	$headings = array();
	$used     = array();

	if ( ! preg_match_all( andreian_toc_heading_pattern(), $html, $matches, PREG_SET_ORDER ) ) {
		return $headings;
	}

	foreach ( $matches as $match ) {
		$label = andreian_toc_heading_label( $match[3] );

		if ( '' === $label ) {
			continue;
		}

		$id = '';
		if ( ! empty( $match[2] ) && preg_match( '/\sid=(["\'])(.*?)\1/i', $match[2], $id_match ) ) {
			$id = $id_match[2];
		}

		if ( '' === $id ) {
			$id = andreian_toc_unique_id( $label, $used );
		} else {
			$used[] = $id;
		}

		$headings[] = array(
			'level' => (int) $match[1],
			'id'    => $id,
			'label' => $label,
		);
	}

	return $headings;
}

/**
 * Add IDs to headings that do not already have one.
 *
 * @param string $content Post content HTML.
 * @return string
 */
function andreian_add_heading_ids_to_content( $content ) {
	$used = array();

	return preg_replace_callback(
		andreian_toc_heading_pattern(),
		static function ( $match ) use ( &$used ) {
			$level = $match[1];
			$attrs = isset( $match[2] ) ? $match[2] : '';
			$inner = $match[3];
			$label = andreian_toc_heading_label( $inner );

			if ( '' === $label ) {
				return $match[0];
			}

			if ( preg_match( '/\sid=(["\'])(.*?)\1/i', $attrs, $id_match ) ) {
				$used[] = $id_match[2];
				return $match[0];
			}

			$id    = andreian_toc_unique_id( $label, $used );
			$attrs = rtrim( $attrs ) . ' id="' . esc_attr( $id ) . '"';

			return '<h' . $level . $attrs . '>' . $inner . '</h' . $level . '>';
		},
		$content
	);
}

/**
 * Inject heading IDs on singular content.
 *
 * @param string $content Post content HTML.
 * @return string
 */
function andreian_inject_heading_ids( $content ) {
	if ( is_admin() || ! is_singular() || ! is_main_query() ) {
		return $content;
	}

	return andreian_add_heading_ids_to_content( $content );
}
add_filter( 'the_content', 'andreian_inject_heading_ids', 12 );

/**
 * Render a nested contents list.
 *
 * @param array<int, array{level:int,id:string,label:string}> $headings Headings.
 * @return string
 */
function andreian_render_toc_list( $headings ) {
	if ( empty( $headings ) ) {
		return '';
	}

	$html  = '<ol class="andreian-toc__list">';
	$stack = array( (int) $headings[0]['level'] );

	foreach ( $headings as $index => $heading ) {
		$level = (int) $heading['level'];

		if ( $index > 0 ) {
			$current = (int) $stack[ count( $stack ) - 1 ];

			if ( $level > $current ) {
				$html   .= '<ol>';
				$stack[] = $level;
			} else {
				while ( count( $stack ) > 1 && $level < $stack[ count( $stack ) - 1 ] ) {
					$html .= '</li></ol>';
					array_pop( $stack );
				}

				$html .= '</li>';

				if ( $level < $stack[ count( $stack ) - 1 ] ) {
					$stack[ count( $stack ) - 1 ] = $level;
				}
			}
		}

		$html .= '<li><a href="#' . esc_attr( $heading['id'] ) . '">' . esc_html( $heading['label'] ) . '</a>';
	}

	while ( count( $stack ) > 1 ) {
		$html .= '</li></ol>';
		array_pop( $stack );
	}

	$html .= '</li></ol>';

	return $html;
}

/**
 * Whether a post has enough headings for a table of contents.
 *
 * @param int|null $post_id Post ID.
 * @return bool
 */
function andreian_post_has_toc( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();

	return count( andreian_get_post_toc_headings( $post_id ) ) > 3;
}

/**
 * Headings for the current post body.
 *
 * @param int $post_id Post ID.
 * @return array<int, array{level:int,id:string,label:string}>
 */
function andreian_get_post_toc_headings( $post_id ) {
	$post = get_post( $post_id );

	if ( ! $post ) {
		return array();
	}

	return andreian_parse_content_headings( $post->post_content );
}

/**
 * Remove LuckyWP TOC shortcodes from HTML.
 *
 * @param string $content Post content.
 * @return string
 */
function andreian_strip_lwptoc_shortcodes( $content ) {
	$stripped = preg_replace( '/\[lwptoc(?:\s[^\]]*)?\](?:[\s\S]*?\[\/lwptoc\])?/i', '', $content );
	$stripped = preg_replace( '/<p>\s*<\/p>/i', '', $stripped );

	return is_string( $stripped ) ? $stripped : $content;
}

/**
 * Strip LuckyWP TOC shortcodes from matching posts.
 *
 * @return int Number of posts updated.
 */
function andreian_strip_lwptoc_from_posts() {
	global $wpdb;

	$post_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_content LIKE %s",
			'%[lwptoc%'
		)
	);

	$updated = 0;

	if ( empty( $post_ids ) ) {
		return $updated;
	}

	foreach ( $post_ids as $post_id ) {
		$post = get_post( (int) $post_id );

		if ( ! $post || false === stripos( $post->post_content, '[lwptoc' ) ) {
			continue;
		}

		$cleaned = andreian_strip_lwptoc_shortcodes( $post->post_content );

		if ( $cleaned === $post->post_content ) {
			continue;
		}

		$result = wp_update_post(
			array(
				'ID'           => $post->ID,
				'post_content' => $cleaned,
			),
			true
		);

		if ( ! is_wp_error( $result ) ) {
			++$updated;
		}
	}

	return $updated;
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * Remove [lwptoc] shortcodes from post content.
	 *
	 * ## EXAMPLES
	 *
	 *     wp andreian strip-lwptoc
	 *
	 * @when after_wp_load
	 */
	WP_CLI::add_command(
		'andreian strip-lwptoc',
		function () {
			$updated = andreian_strip_lwptoc_from_posts();
			WP_CLI::success( sprintf( 'Removed LuckyWP TOC shortcodes from %d post(s).', $updated ) );
		}
	);
}

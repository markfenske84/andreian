<?php
/**
 * Blog post helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Estimate reading time from post content.
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function andreian_get_reading_time( $post_id = null ) {
	$post_id    = $post_id ?: get_the_ID();
	$content    = wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post_id ) ) );
	$words      = preg_split( '/\s+/u', trim( $content ), -1, PREG_SPLIT_NO_EMPTY );
	$word_count = is_array( $words ) ? count( $words ) : 0;
	$minutes    = max( 1, (int) ceil( $word_count / 225 ) );

	return sprintf(
		/* translators: %d: estimated reading time in minutes. */
		_n( '%d minute read', '%d minute read', $minutes, 'andreian' ),
		$minutes
	);
}

/**
 * Whether the current singular view should render the sidebar.
 */
function andreian_singular_has_sidebar() {
	if ( ! is_active_sidebar( 'sidebar' ) ) {
		return false;
	}

	if ( is_singular( 'post' ) ) {
		return true;
	}

	if ( is_singular( 'page' ) ) {
		return 'src/templates/template-sidebar.php' === get_page_template_slug();
	}

	return false;
}

function andreian_is_post_updated( $post_id = null ) {
	$post_id   = $post_id ?: get_the_ID();
	$published = (int) get_the_time( 'U', $post_id );
	$modified  = (int) get_the_modified_time( 'U', $post_id );

	return $modified > $published;
}

function andreian_get_post_date_label( $post_id = null ) {
	return andreian_is_post_updated( $post_id )
		? __( 'Updated On', 'andreian' )
		: __( 'Posted On', 'andreian' );
}

function andreian_get_post_display_date( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();

	return andreian_is_post_updated( $post_id )
		? get_the_modified_time( 'F j, Y', $post_id )
		: get_the_time( 'F j, Y', $post_id );
}

function andreian_get_post_datetime( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();

	return andreian_is_post_updated( $post_id )
		? get_the_modified_time( 'c', $post_id )
		: get_the_time( 'c', $post_id );
}

function andreian_get_post_author_name( $post_id = null ) {
	$post_id   = $post_id ?: get_the_ID();
	$author_id = (int) get_post_field( 'post_author', $post_id );

	return get_the_author_meta( 'display_name', $author_id )
		?: get_the_author_meta( 'user_nicename', $author_id );
}

function andreian_get_post_author_url( $post_id = null ) {
	$post_id   = $post_id ?: get_the_ID();
	$author_id = (int) get_post_field( 'post_author', $post_id );

	return get_author_posts_url( $author_id );
}

function andreian_get_breadcrumb_items() {
	$items = array(
		array(
			'label' => __( 'Home', 'andreian' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( ! is_singular( 'post' ) ) {
		return $items;
	}

	$categories = get_the_category();
	if ( ! empty( $categories ) ) {
		$category = $categories[0];

		foreach ( $categories as $cat ) {
			if ( 'uncategorized' !== strtolower( $cat->slug ) ) {
				$category = $cat;
				break;
			}
		}

		if ( 'uncategorized' !== strtolower( $category->slug ) ) {
			$items[] = array(
				'label' => $category->name,
				'url'   => get_category_link( $category->term_id ),
			);
		}
	}

	return $items;
}

function andreian_get_author_bio_data( $author_id = null ) {
	$author_id = $author_id ?: (int) get_the_author_meta( 'ID' );

	return array(
		'id'          => $author_id,
		'name'        => get_the_author_meta( 'display_name', $author_id ),
		'description' => get_the_author_meta( 'description', $author_id ),
		'url'         => get_author_posts_url( $author_id ),
		'avatar'      => get_avatar(
			$author_id,
			96,
			'',
			get_the_author_meta( 'display_name', $author_id ),
			array( 'class' => 'author-bio__avatar-img' )
		),
	);
}

/**
 * Output concise BlogPosting schema for single posts.
 */
function andreian_output_blogposting_schema() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$post_id   = get_queried_object_id();
	$author_id = (int) get_post_field( 'post_author', $post_id );
	$schema    = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'BlogPosting',
		'headline'         => wp_strip_all_tags( get_the_title( $post_id ) ),
		'description'      => wp_strip_all_tags( get_the_excerpt( $post_id ) ),
		'datePublished'    => get_the_date( DATE_W3C, $post_id ),
		'dateModified'     => get_the_modified_date( DATE_W3C, $post_id ),
		'mainEntityOfPage' => get_permalink( $post_id ),
		'author'           => array(
			'@type' => 'Person',
			'name'  => get_the_author_meta( 'display_name', $author_id ),
			'url'   => get_author_posts_url( $author_id ),
		),
		'publisher'        => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
	);

	$image = get_the_post_thumbnail_url( $post_id, 'full' );
	if ( $image ) {
		$schema['image'] = array( esc_url_raw( $image ) );
	}

	printf(
		'<script type="application/ld+json">%s</script>' . "\n",
		wp_json_encode(
			$schema,
			JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
		)
	);
}
add_action( 'wp_head', 'andreian_output_blogposting_schema', 20 );

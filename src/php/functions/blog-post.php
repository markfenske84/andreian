<?php
/**
 * Blog post helpers: breadcrumbs, entry meta, author data.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the current singular view should render the blog sidebar.
 *
 * Applies to blog posts and default (constrained) pages when the sidebar widget
 * area is active. Excludes the full-width page builder template.
 *
 * @return bool
 */
function chw_singular_has_sidebar() {
	if ( ! is_active_sidebar( 'sidebar' ) ) {
		return false;
	}

	if ( is_singular( 'post' ) ) {
		return true;
	}

	if ( is_singular( 'page' ) ) {
		if ( function_exists( 'chw_page_uses_builder_template' ) && chw_page_uses_builder_template() ) {
			return false;
		}

		$template = get_page_template_slug();

		// Legacy sidebar template uses its own layout.
		if ( 'src/templates/template-sidebar.php' === $template ) {
			return false;
		}

		return true;
	}

	return false;
}

/**
 * Whether the current single post should render the blog sidebar.
 *
 * @return bool
 */
function chw_single_post_has_sidebar() {
	return chw_singular_has_sidebar() && is_singular( 'post' );
}

/**
 * Blog archive URL and label.
 *
 * @return array{url: string, label: string}
 */
function chw_get_blog_archive_link() {
	$posts_page_id = (int) get_option( 'page_for_posts' );
	$label         = get_theme_mod( 'blog_masthead_heading', __( 'Blog', 'chw' ) );

	if ( $posts_page_id ) {
		return array(
			'url'   => get_permalink( $posts_page_id ),
			'label' => get_the_title( $posts_page_id ) ?: $label,
		);
	}

	return array(
		'url'   => home_url( '/blog/' ),
		'label' => $label,
	);
}

/**
 * Whether the post publish date differs from the modified date.
 *
 * @param int|null $post_id Post ID.
 * @return bool
 */
function chw_is_post_updated( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();

	return get_the_modified_time( 'U', $post_id ) !== get_the_time( 'U', $post_id );
}

/**
 * Posted On / Updated On label for entry meta.
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function chw_get_post_date_label( $post_id = null ) {
	return chw_is_post_updated( $post_id )
		? __( 'Updated On', 'chw' )
		: __( 'Posted On', 'chw' );
}

/**
 * Display date for a post (modified when updated).
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function chw_get_post_display_date( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();

	return chw_is_post_updated( $post_id )
		? get_the_modified_time( 'F j, Y', $post_id )
		: get_the_time( 'F j, Y', $post_id );
}

/**
 * Machine-readable date for entry meta time elements.
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function chw_get_post_datetime( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();

	return chw_is_post_updated( $post_id )
		? get_the_modified_time( 'c', $post_id )
		: get_the_time( 'c', $post_id );
}

/**
 * Author display name for the current or given post.
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function chw_get_post_author_name( $post_id = null ) {
	$post_id   = $post_id ?: get_the_ID();
	$author_id = (int) get_post_field( 'post_author', $post_id );

	return get_the_author_meta( 'display_name', $author_id )
		?: get_the_author_meta( 'user_nicename', $author_id );
}

/**
 * Author archive URL for the current or given post.
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function chw_get_post_author_url( $post_id = null ) {
	$post_id   = $post_id ?: get_the_ID();
	$author_id = (int) get_post_field( 'post_author', $post_id );

	return get_author_posts_url( $author_id );
}

/**
 * Breadcrumb trail for single blog posts.
 *
 * @return array<int, array{label: string, url: string}>
 */
function chw_get_breadcrumb_items() {
	$items = array(
		array(
			'label' => __( 'Home', 'chw' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( ! is_singular( 'post' ) ) {
		return $items;
	}

	$blog = chw_get_blog_archive_link();
	$items[] = array(
		'label' => $blog['label'],
		'url'   => $blog['url'],
	);

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

/**
 * Author data for EEAT bio block.
 *
 * @param int|null $author_id Author user ID.
 * @return array<string, mixed>
 */
function chw_get_author_bio_data( $author_id = null ) {
	$author_id = $author_id ?: (int) get_the_author_meta( 'ID' );

	return array(
		'id'          => $author_id,
		'name'        => get_the_author_meta( 'display_name', $author_id ),
		'title'       => get_the_author_meta( 'chw_author_title', $author_id ),
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

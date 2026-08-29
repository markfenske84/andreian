<?php
/**
 * Blog post helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
		if ( function_exists( 'andreian_page_uses_builder_template' ) && andreian_page_uses_builder_template() ) {
			return false;
		}

		$template = get_page_template_slug();

		if ( 'src/templates/template-sidebar.php' === $template ) {
			return false;
		}

		return true;
	}

	return false;
}

function andreian_get_blog_archive_link() {
	$posts_page_id = (int) get_option( 'page_for_posts' );

	if ( $posts_page_id ) {
		return array(
			'url'   => get_permalink( $posts_page_id ),
			'label' => get_the_title( $posts_page_id ) ?: __( 'Blog', 'andreian' ),
		);
	}

	return array(
		'url'   => get_post_type_archive_link( 'post' ) ?: home_url( '/' ),
		'label' => __( 'Blog', 'andreian' ),
	);
}

function andreian_is_post_updated( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();

	return get_the_modified_time( 'U', $post_id ) !== get_the_time( 'U', $post_id );
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

	$blog    = andreian_get_blog_archive_link();
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

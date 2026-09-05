<?php
/**
 * Post helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function andreian_post_categories() {
	$cats      = get_the_category();
	$cat_links = array();

	foreach ( $cats as $category ) {
		if ( 'Uncategorized' === $category->name ) {
			continue;
		}
		$cat_links[] = '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a>';
	}

	return implode( ', ', $cat_links );
}

function andreian_excerpt( $limit = 150 ) {
	$excerpt = get_the_excerpt();

	if ( strlen( $excerpt ) > $limit ) {
		$excerpt = substr( $excerpt, 0, strpos( $excerpt, ' ', $limit ) ) . '...';
	}

	return $excerpt;
}

/**
 * Trailing mark for trimmed excerpts.
 *
 * @return string
 */
function andreian_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'andreian_excerpt_more' );

<?php
/**
 * Random-post navigation link.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get one random published post URL per request.
 *
 * @return string
 */
function andreian_get_random_post_url() {
	static $url = null;

	if ( null !== $url ) {
		return $url;
	}

	$post_ids = get_posts(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'orderby'                => 'rand',
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
		)
	);

	$url = $post_ids ? get_permalink( $post_ids[0] ) : home_url( '/' );

	return $url;
}

/**
 * Markup for the Random menu link, including icon.
 *
 * @return string
 */
function andreian_get_random_post_menu_link() {
	$icon = svg( 'ico-random', 'andreian-random-post-menu-item__icon' );

	return sprintf(
		'<li class="menu-item andreian-random-post-menu-item"><a href="%1$s">%2$s<span>%3$s</span></a></li>',
		esc_url( andreian_get_random_post_url() ),
		$icon ? $icon : '',
		esc_html__( 'Random', 'andreian' )
	);
}

/**
 * Append Random to the assigned primary menu.
 *
 * @param string $items Rendered menu items.
 * @param object $args  Menu arguments.
 * @return string
 */
function andreian_add_random_post_menu_item( $items, $args ) {
	if ( empty( $args->theme_location ) || 'main-nav' !== $args->theme_location ) {
		return $items;
	}

	$items .= andreian_get_random_post_menu_link();

	return $items;
}
add_filter( 'wp_nav_menu_items', 'andreian_add_random_post_menu_item', 10, 2 );

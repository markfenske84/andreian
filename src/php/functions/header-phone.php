<?php
/**
 * Header phone link helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the first tel: link from the CTA menu, if present.
 *
 * @return array{url: string, label: string}|null
 */
function chw_get_header_phone_link() {
	$menu = wp_get_nav_menu_object( 'cta_menu' );

	if ( ! $menu ) {
		return null;
	}

	$items = wp_get_nav_menu_items( $menu->term_id );

	if ( ! $items ) {
		return null;
	}

	foreach ( $items as $item ) {
		if ( str_starts_with( $item->url, 'tel:' ) ) {
			return array(
				'url'   => $item->url,
				'label' => $item->title,
			);
		}
	}

	return null;
}

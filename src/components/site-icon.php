<?php
/**
 * Site icon (Customizer Site Identity icon).
 */

$icon_url = get_site_icon_url( 96 );

if ( ! $icon_url ) {
	return;
}
?>

<a
	class="site-icon"
	href="<?= esc_url( home_url( '/' ) ); ?>"
	title="<?= esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"
	itemprop="url">

	<img
		src="<?= esc_url( $icon_url ); ?>"
		alt="<?= esc_attr( get_bloginfo( 'name' ) ); ?>"
		width="40"
		height="40"
		loading="lazy"
		decoding="async">

</a>

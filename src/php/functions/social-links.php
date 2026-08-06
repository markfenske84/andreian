<?php
/**
 * Social Links helpers — always loaded so the block and front end can read
 * Customizer-managed links outside of customize_register.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Canonical list of supported social platforms.
 *
 * @return array
 */
function chw_social_platforms() {
	return array(
		'facebook-f'  => __( 'Facebook', 'chw' ),
		'x-twitter'   => __( 'X (Twitter)', 'chw' ),
		'instagram'   => __( 'Instagram', 'chw' ),
		'linkedin-in' => __( 'LinkedIn', 'chw' ),
		'youtube'     => __( 'YouTube', 'chw' ),
		'pinterest-p' => __( 'Pinterest', 'chw' ),
		'tiktok'      => __( 'TikTok', 'chw' ),
		'yelp'        => __( 'Yelp', 'chw' ),
	);
}

/**
 * Sanitize the JSON-encoded list of social links.
 *
 * @param string $value Raw JSON from the control.
 * @return string Sanitized JSON.
 */
function chw_sanitize_social_links_json( $value ) {
	$decoded = json_decode( (string) $value, true );

	if ( ! is_array( $decoded ) ) {
		return '';
	}

	$platforms = chw_social_platforms();
	$clean     = array();

	foreach ( $decoded as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$platform = isset( $row['platform'] ) ? sanitize_key( $row['platform'] ) : '';
		$url      = isset( $row['url'] ) ? esc_url_raw( $row['url'] ) : '';

		if ( ! isset( $platforms[ $platform ] ) || empty( $url ) ) {
			continue;
		}

		$clean[] = array(
			'platform' => $platform,
			'url'      => $url,
		);
	}

	return wp_json_encode( $clean );
}

/**
 * Return the configured social links as a normalized array.
 *
 * @return array[] List of [ 'platform' => slug, 'label' => string, 'url' => string ].
 */
function chw_get_social_links() {
	$value = get_theme_mod( 'social_links', '' );

	if ( empty( $value ) ) {
		return array();
	}

	$decoded = json_decode( $value, true );
	if ( ! is_array( $decoded ) ) {
		return array();
	}

	$platforms = chw_social_platforms();
	$links     = array();

	foreach ( $decoded as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$platform = isset( $row['platform'] ) ? (string) $row['platform'] : '';
		$url      = isset( $row['url'] ) ? (string) $row['url'] : '';

		if ( ! isset( $platforms[ $platform ] ) || empty( $url ) ) {
			continue;
		}

		$links[] = array(
			'platform' => $platform,
			'label'    => $platforms[ $platform ],
			'url'      => $url,
		);
	}

	return $links;
}

<?php
/**
 * Gravity Forms theme alignment.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default GF style settings — hex values approximate src/scss/abstracts/_variables.scss.
 *
 * @param array|false|string $styles Existing styles.
 * @return array|string
 */
function chw_gform_default_styles( $styles ) {
	$theme_styles = array(
		'theme'                        => 'gravity-theme',
		'inputSize'                    => 'md',
		'inputBorderRadius'            => '6',
		'inputBorderColor'             => '#d5dae3',
		'inputBackgroundColor'         => '#ffffff',
		'inputColor'                   => '#1c2433',
		'inputPrimaryColor'            => '#e8863a',
		'labelFontSize'                => '15',
		'labelColor'                   => '#1c2433',
		'descriptionFontSize'          => '13',
		'descriptionColor'             => '#6b7588',
		'buttonPrimaryBackgroundColor' => '#e8863a',
		'buttonPrimaryColor'           => '#ffffff',
	);

	if ( is_array( $styles ) ) {
		return array_merge( $styles, $theme_styles );
	}

	return wp_json_encode( $theme_styles );
}
add_filter( 'gform_default_styles', 'chw_gform_default_styles' );

<?php
/**
 * Badge: native (non-ACF) dynamic Gutenberg block.
 *
 * Renders the shared ._eyebrow pill pattern with inline text editing in the
 * editor and icon/color controls in the block sidebar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed style preset values.
 *
 * @return array
 */
function chw_badge_style_options() {
	return array( 'on-light', 'on-dark', 'brand-orange', 'plain', 'custom' );
}

/**
 * Style presets that include their own text color (skip _text token class).
 *
 * @return array
 */
function chw_badge_styles_with_bundled_text_color() {
	return array( 'on-dark', 'brand-orange' );
}

/**
 * Allowed theme text color tokens.
 *
 * @return array
 */
function chw_badge_color_options() {
	return chw_editor_text_color_tokens();
}

/**
 * Allowed alignment values (empty string = left default).
 *
 * @return array
 */
function chw_badge_align_options() {
	return array( 'center', 'right' );
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_badge_block_attributes() {
	return array(
		'text'            => array(
			'type'    => 'string',
			'default' => '',
		),
		'icon'            => array(
			'type'    => 'string',
			'default' => '',
		),
		'align'           => array(
			'type'    => 'string',
			'default' => '',
		),
		'style'           => array(
			'type'    => 'string',
			'default' => 'on-light',
		),
		'borderColor'     => array(
			'type'    => 'string',
			'default' => '',
		),
		'backgroundColor' => array(
			'type'    => 'string',
			'default' => '',
		),
		'textColor'       => array(
			'type'    => 'string',
			'default' => 'tertiary',
		),
		'iconColor'       => array(
			'type'    => 'string',
			'default' => 'primary',
		),
	);
}

/**
 * Build eyebrow class list from block attributes.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function chw_badge_eyebrow_classes( $attributes ) {
	$style = isset( $attributes['style'] ) ? (string) $attributes['style'] : 'on-light';
	if ( ! in_array( $style, chw_badge_style_options(), true ) ) {
		$style = 'on-light';
	}

	$classes = array( '_eyebrow' );

	// Preset chrome classes only apply to the named presets; "custom" relies on
	// inline background/border styles instead.
	if ( 'custom' !== $style ) {
		$classes[] = '-' . sanitize_html_class( $style );
	}

	$text_color = isset( $attributes['textColor'] ) ? (string) $attributes['textColor'] : '';
	if (
		! in_array( $style, chw_badge_styles_with_bundled_text_color(), true )
		&& in_array( $text_color, chw_badge_color_options(), true )
	) {
		$classes[] = '_text';
		$classes[] = '-' . sanitize_html_class( $text_color );
	}

	return implode( ' ', $classes );
}

/**
 * Build icon wrapper class list from block attributes.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function chw_badge_icon_classes( $attributes ) {
	$classes = array( '_eyebrow__icon' );

	$icon_color = isset( $attributes['iconColor'] ) ? (string) $attributes['iconColor'] : '';
	if ( in_array( $icon_color, chw_badge_color_options(), true ) ) {
		$classes[] = '_text';
		$classes[] = '-' . sanitize_html_class( $icon_color );
	}

	return implode( ' ', $classes );
}

/**
 * Build optional inline style overrides for custom colors.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function chw_badge_inline_styles( $attributes ) {
	$style = isset( $attributes['style'] ) ? (string) $attributes['style'] : 'on-light';

	// Custom colors only apply to the "custom" preset; named presets style
	// themselves via their modifier class.
	if ( 'custom' !== $style ) {
		return '';
	}

	$border_color     = isset( $attributes['borderColor'] ) ? trim( (string) $attributes['borderColor'] ) : '';
	$background_color = isset( $attributes['backgroundColor'] ) ? trim( (string) $attributes['backgroundColor'] ) : '';

	$rules = array();

	if ( $background_color !== '' ) {
		$rules[] = 'background-color:' . $background_color;
	}

	if ( $border_color !== '' ) {
		$rules[] = 'box-shadow:inset 0 0 0 1px ' . $border_color;
	}

	if ( empty( $rules ) ) {
		return '';
	}

	return implode( ';', $rules );
}

/**
 * Build the wrapper class list, including alignment modifier.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function chw_badge_wrapper_classes( $attributes ) {
	$classes = array( 'badge-block' );

	$align = isset( $attributes['align'] ) ? (string) $attributes['align'] : '';
	if ( in_array( $align, chw_badge_align_options(), true ) ) {
		$classes[] = '-align-' . sanitize_html_class( $align );
	}

	return implode( ' ', $classes );
}

/**
 * Server-side render callback for the block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function chw_render_badge_block( $attributes ) {
	$attributes = is_array( $attributes ) ? $attributes : array();

	$text = isset( $attributes['text'] ) ? (string) $attributes['text'] : '';
	$icon = isset( $attributes['icon'] ) ? (string) $attributes['icon'] : '';

	if ( $text === '' && $icon === '' ) {
		return '';
	}

	$eyebrow_classes = chw_badge_eyebrow_classes( $attributes );
	$icon_classes      = chw_badge_icon_classes( $attributes );
	$inline_styles     = chw_badge_inline_styles( $attributes );

	$wrapper_classes = chw_badge_wrapper_classes( $attributes );

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => $wrapper_classes ) )
		: 'class="' . esc_attr( $wrapper_classes ) . '"';

	ob_start();
	include __DIR__ . '/badge.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_badge_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/badge.js';

	wp_register_script(
		'chw-badge',
		get_template_directory_uri() . '/src/js/editor/badge.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	register_block_type(
		'chw/badge',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_badge_block_attributes(),
			'editor_script'   => 'chw-badge',
			'render_callback' => 'chw_render_badge_block',
		)
	);
}
add_action( 'init', 'chw_register_badge_block' );

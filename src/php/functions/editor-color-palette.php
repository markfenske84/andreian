<?php
/**
 * Gutenberg editor color palette — mirrors src/scss/abstracts/_variables.scss.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme color swatches for the block editor.
 *
 * Values match the SCSS brand palette so pickers and compiled CSS stay aligned.
 *
 * @return array<int, array{name: string, slug: string, color: string}>
 */
function chw_editor_color_palette() {
	return array(
		array(
			'name'  => __( 'Primary', 'chw' ),
			'slug'  => 'primary',
			'color' => 'oklch(69% .18 47)',
		),
		array(
			'name'  => __( 'Secondary', 'chw' ),
			'slug'  => 'secondary',
			'color' => 'lab(39.0216% -2.16544 -40.8018)',
		),
		array(
			'name'  => __( 'Tertiary', 'chw' ),
			'slug'  => 'tertiary',
			'color' => 'oklch(28% .06 252)',
		),
		array(
			'name'  => __( 'Quaternary', 'chw' ),
			'slug'  => 'quaternary',
			'color' => 'oklch(97.5% .005 240)',
		),
		array(
			'name'  => __( 'Text', 'chw' ),
			'slug'  => 'text',
			'color' => 'oklch(22% .03 252)',
		),
		array(
			'name'  => __( 'Muted', 'chw' ),
			'slug'  => 'muted',
			'color' => 'color-mix(in oklab, oklch(28% .06 252) 72%, white)',
		),
		array(
			'name'  => __( 'White', 'chw' ),
			'slug'  => 'white',
			'color' => '#ffffff',
		),
		array(
			'name'  => __( 'Black', 'chw' ),
			'slug'  => 'black',
			'color' => '#000000',
		),
	);
}

/**
 * Color slugs that map to the `._text -{slug}` utility classes.
 *
 * @return string[]
 */
function chw_editor_text_color_tokens() {
	return array( 'primary', 'secondary', 'tertiary', 'quaternary', 'text', 'muted', 'white' );
}

/**
 * Register the editor color palette with WordPress.
 */
function chw_register_editor_color_palette() {
	add_theme_support( 'editor-color-palette', chw_editor_color_palette() );
}
add_action( 'after_setup_theme', 'chw_register_editor_color_palette' );

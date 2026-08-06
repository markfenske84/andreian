<?php
/**
 * Highlight Cards: native (non-ACF) dynamic Gutenberg block.
 *
 * Content is edited inline in the editor canvas via src/js/editor/highlight-cards.js,
 * while the front end is rendered from PHP through the render callback below.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed badge style presets (excludes Badge block "custom").
 *
 * @return array
 */
function chw_highlight_cards_badge_style_options() {
	return array( 'plain', 'on-light', 'on-dark', 'brand-orange' );
}

/**
 * Allowed cards-per-row values.
 *
 * @return array
 */
function chw_highlight_cards_per_row_options() {
	return array( 3, 4 );
}

/**
 * Allowed card accent styles.
 *
 * @return array
 */
function chw_highlight_cards_card_style_options() {
	return array( 'blue', 'orange' );
}

/**
 * Allowed legal text alignment values.
 *
 * @return array
 */
function chw_highlight_cards_legal_align_options() {
	return array( 'center', 'left', 'right' );
}

/**
 * Build section wrapper class list from block attributes.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function chw_highlight_cards_wrapper_classes( $attributes ) {
	$classes = array( 'highlight-cards' );

	$card_style = isset( $attributes['cardStyle'] ) ? (string) $attributes['cardStyle'] : 'blue';
	if ( 'orange' === $card_style ) {
		$classes[] = '-card-style-orange';
	}

	return implode( ' ', $classes );
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_highlight_cards_block_attributes() {
	return array(
		'eyebrow'       => array(
			'type'    => 'string',
			'default' => '',
		),
		'heading'       => array(
			'type'    => 'string',
			'default' => '',
		),
		'headingAccent' => array(
			'type'    => 'string',
			'default' => '',
		),
		'description'   => array(
			'type'    => 'string',
			'default' => '',
		),
		'cards'         => array(
			'type'    => 'array',
			'default' => array(),
		),
		'buttons'       => array(
			'type'    => 'array',
			'default' => array(),
		),
		'showLegal'     => array(
			'type'    => 'boolean',
			'default' => false,
		),
		'badgeStyle'    => array(
			'type'    => 'string',
			'default' => 'plain',
		),
		'cardsPerRow'   => array(
			'type'    => 'number',
			'default' => 3,
		),
		'cardStyle'     => array(
			'type'    => 'string',
			'default' => 'blue',
		),
		'legalAlign'    => array(
			'type'    => 'string',
			'default' => 'center',
		),
	);
}

/**
 * Server-side render callback for the block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function chw_render_highlight_cards_block( $attributes ) {
	$attributes      = is_array( $attributes ) ? $attributes : array();
	$wrapper_classes = chw_highlight_cards_wrapper_classes( $attributes );

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => $wrapper_classes ) )
		: 'class="' . esc_attr( $wrapper_classes ) . '"';

	ob_start();
	include __DIR__ . '/highlight-cards.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_highlight_cards_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/highlight-cards.js';

	wp_register_script(
		'chw-highlight-cards',
		get_template_directory_uri() . '/src/js/editor/highlight-cards.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	wp_localize_script(
		'chw-highlight-cards',
		'chwHighlightCardsData',
		array(
			'legalText' => get_theme_mod( 'legal_text', '' ),
		)
	);

	register_block_type(
		'chw/highlight-cards',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_highlight_cards_block_attributes(),
			'editor_script'   => 'chw-highlight-cards',
			'render_callback' => 'chw_render_highlight_cards_block',
		)
	);
}
add_action( 'init', 'chw_register_highlight_cards_block' );

<?php
/**
 * Core Heading block — editable top margin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default heading top margin multiplier (matches theme typography).
 */
function andreian_heading_margin_top_default_multiplier() {
	return 2.5;
}

/**
 * Register andreianMarginTopMultiplier attribute server-side.
 *
 * @param array  $args        Block type registration arguments.
 * @param string $block_type  Block name.
 * @return array
 */
function andreian_register_heading_margin_top_attribute( $args, $block_type ) {
	if ( 'core/heading' !== $block_type ) {
		return $args;
	}

	$args['attributes']['andreianMarginTopMultiplier'] = array(
		'type'    => 'number',
		'default' => andreian_heading_margin_top_default_multiplier(),
	);

	return $args;
}
add_filter( 'register_block_type_args', 'andreian_register_heading_margin_top_attribute', 10, 2 );

/**
 * Enqueue block editor script.
 */
function andreian_enqueue_heading_margin_top() {
	$script_path = get_template_directory() . '/src/js/editor/heading-margin-top.js';

	wp_enqueue_script(
		'andreian-heading-margin-top',
		get_template_directory_uri() . '/src/js/editor/heading-margin-top.js',
		array( 'wp-hooks', 'wp-compose', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n' ),
		andreian_asset_version( $script_path ),
		true
	);

	wp_localize_script(
		'andreian-heading-margin-top',
		'andreianHeadingMarginTop',
		array(
			'defaultMultiplier' => andreian_heading_margin_top_default_multiplier(),
			'containerGutter'   => (int) get_theme_mod( 'container_gutter', 16 ),
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'andreian_enqueue_heading_margin_top' );

/**
 * Sanitize a margin multiplier for inline CSS.
 *
 * @param mixed $multiplier Raw multiplier value.
 * @return string
 */
function andreian_sanitize_heading_margin_multiplier( $multiplier ) {
	$multiplier = round( (float) $multiplier, 2 );

	return rtrim( rtrim( sprintf( '%.2F', $multiplier ), '0' ), '.' );
}

/**
 * Whether the multiplier matches the theme default (no override needed).
 *
 * @param mixed $multiplier Raw multiplier value.
 * @return bool
 */
function andreian_heading_margin_top_is_default( $multiplier ) {
	if ( null === $multiplier || '' === $multiplier ) {
		return true;
	}

	return abs( (float) $multiplier - andreian_heading_margin_top_default_multiplier() ) < 0.001;
}

/**
 * Build inline margin-top style for a heading multiplier.
 *
 * @param mixed $multiplier Raw multiplier value.
 * @return string
 */
function andreian_get_heading_margin_top_inline_style( $multiplier ) {
	if ( andreian_heading_margin_top_is_default( $multiplier ) ) {
		return '';
	}

	if ( abs( (float) $multiplier ) < 0.001 ) {
		return 'margin-top:0';
	}

	return sprintf(
		'margin-top:calc(var(--container-gutter) * %s)',
		andreian_sanitize_heading_margin_multiplier( $multiplier )
	);
}

/**
 * Merge a margin-top rule into an existing inline style attribute.
 *
 * @param string $existing Existing inline style value.
 * @param string $rule     New margin-top rule.
 * @return string
 */
function andreian_merge_heading_margin_top_style( $existing, $rule ) {
	if ( empty( $existing ) ) {
		return $rule;
	}

	$existing = preg_replace( '/margin-top\s*:[^;]+;?\s*/i', '', $existing );
	$existing = trim( $existing, " \t\n\r\0\x0B;" );

	if ( '' === $existing ) {
		return $rule;
	}

	return $existing . ';' . $rule;
}

/**
 * Ensure custom top margin is present on rendered heading blocks.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data.
 * @return string
 */
function andreian_render_heading_margin_top( $block_content, $block ) {
	if ( 'core/heading' !== ( $block['blockName'] ?? '' ) ) {
		return $block_content;
	}

	$multiplier = $block['attrs']['andreianMarginTopMultiplier'] ?? null;
	$style_rule = andreian_get_heading_margin_top_inline_style( $multiplier );

	if ( '' === $style_rule ) {
		return $block_content;
	}

	$processor = new WP_HTML_Tag_Processor( $block_content );

	if ( ! $processor->next_tag() ) {
		return $block_content;
	}

	$existing_style = $processor->get_attribute( 'style' );
	$processor->set_attribute( 'style', andreian_merge_heading_margin_top_style( (string) $existing_style, $style_rule ) );

	return $processor->get_updated_html();
}
add_filter( 'render_block', 'andreian_render_heading_margin_top', 10, 2 );

<?php
/**
 * Style variations for the core Quote block.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register core/quote styles that match the theme quote modifiers.
 */
function andreian_register_quote_block_styles() {
	$styles = array(
		'pullquote'    => __( 'Pull Quote', 'andreian' ),
		'inline-quote' => __( 'Inline Quote', 'andreian' ),
		'framed-quote' => __( 'Framed Quote', 'andreian' ),
		'shadow-quote' => __( 'Shadow Quote', 'andreian' ),
		'rule-quote'   => __( 'Rule Quote', 'andreian' ),
	);

	foreach ( $styles as $name => $label ) {
		register_block_style(
			'core/quote',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}
}
add_action( 'init', 'andreian_register_quote_block_styles' );

<?php
/**
 * Disable block editor for posts (Classic Editor).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter(
	'use_block_editor_for_post_type',
	function ( $use, $post_type ) {
		return 'post' === $post_type ? false : $use;
	},
	10,
	2
);

/**
 * Register TinyMCE pull quote format.
 *
 * @param array $init TinyMCE settings.
 * @return array
 */
function andreian_classic_editor_formats( $init ) {
	$style_formats = array(
		array(
			'title'   => __( 'Pull Quote', 'andreian' ),
			'block'   => 'blockquote',
			'classes' => 'pullquote',
			'wrapper' => true,
		),
		array(
			'title'   => __( 'Inline Quote', 'andreian' ),
			'block'   => 'blockquote',
			'classes' => 'inline-quote',
			'wrapper' => true,
		),
	);

	if ( isset( $init['style_formats_merge'] ) ) {
		$init['style_formats_merge'] = true;
	}

	$init['style_formats'] = wp_json_encode( $style_formats );

	return $init;
}
add_filter( 'tiny_mce_before_init', 'andreian_classic_editor_formats' );

/**
 * Restore inset padding inside TinyMCE.
 *
 * Theme styles zero body margin, and wp-autoresize zeros padding with !important.
 *
 * @param array $init TinyMCE settings.
 * @return array
 */
function andreian_classic_editor_content_style( $init ) {
	$padding = 'body.mce-content-body,body.wp-autoresize{margin:0!important;padding:1.25rem 1.5rem!important;box-sizing:border-box;}';

	$init['content_style'] = trim(
		( isset( $init['content_style'] ) ? (string) $init['content_style'] . ' ' : '' ) . $padding
	);

	return $init;
}
add_filter( 'tiny_mce_before_init', 'andreian_classic_editor_content_style' );

/**
 * Add the Formats dropdown to the Classic Editor toolbar.
 *
 * @param array $buttons Toolbar buttons.
 * @return array
 */
function andreian_classic_editor_toolbar( $buttons ) {
	array_unshift( $buttons, 'styleselect' );

	return $buttons;
}
add_filter( 'mce_buttons_2', 'andreian_classic_editor_toolbar' );

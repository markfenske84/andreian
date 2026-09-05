<?php
/**
 * Page layout editor script for block editor canvas width.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function andreian_enqueue_page_layout_editor_assets() {
	$script_path = get_template_directory() . '/src/js/editor/page-layout.js';

	wp_enqueue_script(
		'andreian-page-layout',
		get_template_directory_uri() . '/src/js/editor/page-layout.js',
		array( 'wp-data', 'wp-dom-ready' ),
		andreian_asset_version( $script_path ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'andreian_enqueue_page_layout_editor_assets' );

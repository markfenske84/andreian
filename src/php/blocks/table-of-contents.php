<?php
/**
 * Table of Contents block registration.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/src/blocks/table-of-contents/render.php';

/**
 * Register the Table of Contents block.
 */
function andreian_register_table_of_contents_block() {
	$script_path = get_template_directory() . '/src/blocks/table-of-contents/editor.js';

	wp_register_script(
		'andreian-table-of-contents-editor',
		get_template_directory_uri() . '/src/blocks/table-of-contents/editor.js',
		array(
			'wp-block-editor',
			'wp-blocks',
			'wp-element',
			'wp-i18n',
		),
		andreian_asset_version( $script_path ),
		true
	);

	register_block_type(
		get_template_directory() . '/src/blocks/table-of-contents',
		array(
			'editor_script'   => 'andreian-table-of-contents-editor',
			'render_callback' => 'andreian_render_table_of_contents_block',
		)
	);
}
add_action( 'init', 'andreian_register_table_of_contents_block' );

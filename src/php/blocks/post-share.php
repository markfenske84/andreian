<?php
/**
 * Post Share block registration.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/src/blocks/post-share/render.php';

/**
 * Register the Post Share block.
 */
function andreian_register_post_share_block() {
	$script_path = get_template_directory() . '/src/blocks/post-share/editor.js';

	wp_register_script(
		'andreian-post-share-editor',
		get_template_directory_uri() . '/src/blocks/post-share/editor.js',
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
		get_template_directory() . '/src/blocks/post-share',
		array(
			'editor_script'   => 'andreian-post-share-editor',
			'render_callback' => 'andreian_render_post_share_block',
		)
	);
}
add_action( 'init', 'andreian_register_post_share_block' );

<?php
/**
 * Social Links block registration.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/src/blocks/social-links/render.php';

function andreian_register_social_links_block() {
	$script_path = get_template_directory() . '/src/blocks/social-links/editor.js';

	wp_register_script(
		'andreian-social-links-editor',
		get_template_directory_uri() . '/src/blocks/social-links/editor.js',
		array(
			'wp-block-editor',
			'wp-blocks',
			'wp-element',
			'wp-i18n',
			'wp-server-side-render',
		),
		andreian_asset_version( $script_path ),
		true
	);

	register_block_type(
		get_template_directory() . '/src/blocks/social-links',
		array(
			'editor_script'   => 'andreian-social-links-editor',
			'render_callback' => 'andreian_render_social_links_block',
		)
	);
}
add_action( 'init', 'andreian_register_social_links_block' );

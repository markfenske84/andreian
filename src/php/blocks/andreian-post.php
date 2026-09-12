<?php
/**
 * Andreian Post block registration.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/src/blocks/andreian-post/render.php';

function andreian_register_post_block() {
	$script_path = get_template_directory() . '/src/blocks/andreian-post/editor.js';

	wp_register_script(
		'andreian-post-editor',
		get_template_directory_uri() . '/src/blocks/andreian-post/editor.js',
		array(
			'wp-block-editor',
			'wp-blocks',
			'wp-components',
			'wp-data',
			'wp-element',
			'wp-i18n',
			'wp-server-side-render',
		),
		andreian_asset_version( $script_path ),
		true
	);

	register_block_type(
		get_template_directory() . '/src/blocks/andreian-post',
		array(
			'editor_script'   => 'andreian-post-editor',
			'render_callback' => 'andreian_render_post_block',
		)
	);
}
add_action( 'init', 'andreian_register_post_block' );

/**
 * Keep unsynced patterns as normal blocks so Andreian Post inspector stays visible.
 */
function andreian_disable_pattern_content_only( $settings ) {
	$settings['disableContentOnlyForUnsyncedPatterns'] = true;

	return $settings;
}
add_filter( 'block_editor_settings_all', 'andreian_disable_pattern_content_only' );

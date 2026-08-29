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

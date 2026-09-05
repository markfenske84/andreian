<?php
/**
 * Server render callback for the Post Share block.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render sharing links for the current single post.
 *
 * @return string
 */
function andreian_render_post_share_block() {
	if ( ! is_singular( 'post' ) || ! get_the_ID() ) {
		return '';
	}

	ob_start();
	get_template_part( 'src/components/post-share' );

	return (string) ob_get_clean();
}

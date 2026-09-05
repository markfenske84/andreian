<?php
/**
 * Server render callback for the Table of Contents block.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the table of contents for the current content.
 *
 * @return string
 */
function andreian_render_table_of_contents_block() {
	if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST && ! get_the_ID() ) ) {
		return '<div class="andreian-toc andreian-toc--editor"><strong>' . esc_html__( 'Andreian Table of Contents', 'andreian' ) . '</strong><p>' . esc_html__( 'Heading links appear here on the front end.', 'andreian' ) . '</p></div>';
	}

	$post_id = get_the_ID() ? get_the_ID() : get_queried_object_id();

	if ( ! $post_id ) {
		return '';
	}

	ob_start();
	get_template_part(
		'src/components/table-of-contents',
		null,
		array(
			'post_id' => $post_id,
		)
	);

	return (string) ob_get_clean();
}

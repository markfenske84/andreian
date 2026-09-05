<?php
/**
 * Table of contents for the current content.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id  = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
$headings = andreian_get_post_toc_headings( $post_id );

if ( count( $headings ) <= 3 ) {
	return;
}

$list = andreian_render_toc_list( $headings );

if ( '' === $list ) {
	return;
}
?>

<nav class="andreian-toc" aria-label="<?php esc_attr_e( 'Table of contents', 'andreian' ); ?>">
	<details class="andreian-toc__details" open>
		<summary class="andreian-toc__summary"><?php esc_html_e( 'Contents', 'andreian' ); ?></summary>
		<?php echo $list; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built with escaped labels and IDs. ?>
	</details>
</nav>

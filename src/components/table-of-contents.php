<?php
/**
 * Table of contents for the current content.
 *
 * @var array $args Template arguments.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id  = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
$variant  = isset( $args['variant'] ) ? sanitize_key( $args['variant'] ) : 'sidebar';
$headings = andreian_get_post_toc_headings( $post_id );

if ( count( $headings ) <= 3 ) {
	return;
}

$list = andreian_render_toc_list( $headings );

if ( '' === $list ) {
	return;
}

$nav_class = 'andreian-toc';

if ( 'drawer' === $variant ) {
	$nav_class .= ' andreian-toc--drawer';
}
?>

<nav class="<?php echo esc_attr( $nav_class ); ?>" aria-label="<?php esc_attr_e( 'Table of contents', 'andreian' ); ?>">
	<?php if ( 'drawer' === $variant ) : ?>
		<h2 class="andreian-toc__heading"><?php esc_html_e( 'Contents', 'andreian' ); ?></h2>
		<?php echo $list; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built with escaped labels and IDs. ?>
	<?php else : ?>
		<details class="andreian-toc__details" open>
			<summary class="andreian-toc__summary">
				<?php esc_html_e( 'Contents', 'andreian' ); ?>
				<?php echo svg( 'ico-chevron-up', 'andreian-toc__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Theme SVG asset. ?>
			</summary>
			<?php echo $list; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built with escaped labels and IDs. ?>
		</details>
	<?php endif; ?>
</nav>

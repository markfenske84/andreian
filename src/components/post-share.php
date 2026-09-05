<?php
/**
 * Single-post sharing controls.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id    = get_the_ID();
$title      = wp_strip_all_tags( get_the_title( $post_id ) );
$permalink  = get_permalink( $post_id );
$platforms  = andreian_share_platforms( $permalink, $title );
$share_keys = array( 'facebook', 'x', 'email', 'telegram' );
?>

<nav class="post-share" aria-label="<?php echo esc_attr( sprintf( __( 'Share %s', 'andreian' ), $title ) ); ?>">
	<h2 class="post-share__title"><?php esc_html_e( 'Share', 'andreian' ); ?></h2>
	<ul class="post-share__list">
		<?php foreach ( $share_keys as $key ) : ?>
			<?php if ( ! isset( $platforms[ $key ] ) ) : ?>
				<?php continue; ?>
			<?php endif; ?>
			<li>
				<?php echo andreian_render_share_link( $platforms[ $key ], 'post-share__link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in renderer. ?>
			</li>
		<?php endforeach; ?>
		<li>
			<button
				class="post-share__copy share-link share-link--copy"
				type="button"
				data-copy-url="<?php echo esc_url( $permalink ); ?>"
				data-copy-success="<?php esc_attr_e( 'Link copied.', 'andreian' ); ?>"
				data-copy-error="<?php esc_attr_e( 'Unable to copy the link.', 'andreian' ); ?>"
				aria-label="<?php esc_attr_e( 'Copy link', 'andreian' ); ?>">
				<?php
				$copy_icon = svg( 'ico-share-link', 'share-link__icon post-share__link__icon' );
				if ( $copy_icon ) {
					echo $copy_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</button>
		</li>
	</ul>
	<p class="post-share__status" aria-live="polite"></p>
</nav>

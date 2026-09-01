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
$share_url  = rawurlencode( $permalink );
$share_text = rawurlencode( $title );
?>

<nav class="post-share" aria-label="<?php echo esc_attr( sprintf( __( 'Share %s', 'andreian' ), $title ) ); ?>">
	<h2 class="post-share__title"><?php esc_html_e( 'Share', 'andreian' ); ?></h2>
	<ul class="post-share__list">
		<li>
			<a href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . $share_url ); ?>" target="_blank" rel="nofollow noopener">
				<?php esc_html_e( 'Facebook', 'andreian' ); ?>
			</a>
		</li>
		<li>
			<a href="<?php echo esc_url( 'https://x.com/intent/post?url=' . $share_url . '&text=' . $share_text ); ?>" target="_blank" rel="nofollow noopener">
				<?php esc_html_e( 'X', 'andreian' ); ?>
			</a>
		</li>
		<li>
			<a href="<?php echo esc_url( 'mailto:?subject=' . $share_text . '&body=' . $share_url ); ?>">
				<?php esc_html_e( 'Email', 'andreian' ); ?>
			</a>
		</li>
		<li>
			<a href="<?php echo esc_url( 'https://t.me/share/url?url=' . $share_url . '&text=' . $share_text ); ?>" target="_blank" rel="nofollow noopener">
				<?php esc_html_e( 'Telegram', 'andreian' ); ?>
			</a>
		</li>
		<li>
			<button
				class="post-share__copy"
				type="button"
				data-copy-url="<?php echo esc_url( $permalink ); ?>"
				data-copy-success="<?php esc_attr_e( 'Link copied.', 'andreian' ); ?>"
				data-copy-error="<?php esc_attr_e( 'Unable to copy the link.', 'andreian' ); ?>">
				<?php esc_html_e( 'Copy link', 'andreian' ); ?>
			</button>
		</li>
	</ul>
	<p class="post-share__status" aria-live="polite"></p>
</nav>

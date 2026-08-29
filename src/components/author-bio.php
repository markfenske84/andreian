<?php
/**
 * Author bio for single posts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$author = andreian_get_author_bio_data();

if ( empty( $author['name'] ) ) {
	return;
}
?>

<aside class="author-bio" aria-label="<?php esc_attr_e( 'About the author', 'andreian' ); ?>">
	<div class="author-bio__avatar">
		<?php echo $author['avatar']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<div class="author-bio__content">
		<h2 class="author-bio__name">
			<a href="<?php echo esc_url( $author['url'] ); ?>"><?php echo esc_html( $author['name'] ); ?></a>
		</h2>
		<?php if ( ! empty( $author['description'] ) ) : ?>
			<div class="author-bio__description">
				<?php echo wp_kses_post( wpautop( $author['description'] ) ); ?>
			</div>
		<?php endif; ?>
	</div>
</aside>

<?php
/**
 * EEAT author bio for single blog posts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$author = chw_get_author_bio_data();

if ( empty( $author['name'] ) ) {
	return;
}
?>

<aside
	class="author-bio"
	itemscope
	itemtype="https://schema.org/Person"
	aria-label="<?php esc_attr_e( 'About the author', 'chw' ); ?>">

	<div class="author-bio__avatar" itemprop="image">
		<?php echo $author['avatar']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar() ?>
	</div>

	<div class="author-bio__content">
		<p class="author-bio__eyebrow _eyebrow -plain _text -muted">
			<?php esc_html_e( 'About the author', 'chw' ); ?>
		</p>

		<h2 class="author-bio__name _text-style -h5">
			<a
				class="author-bio__name-link"
				href="<?php echo esc_url( $author['url'] ); ?>"
				itemprop="url">
				<span itemprop="name"><?php echo esc_html( $author['name'] ); ?></span>
			</a>
		</h2>

		<?php if ( ! empty( $author['title'] ) ) : ?>
			<p class="author-bio__title" itemprop="jobTitle"><?php echo esc_html( $author['title'] ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $author['description'] ) ) : ?>
			<div class="author-bio__description" itemprop="description">
				<?php echo wp_kses_post( wpautop( $author['description'] ) ); ?>
			</div>
		<?php endif; ?>

		<a class="author-bio__posts-link _text -secondary" href="<?php echo esc_url( $author['url'] ); ?>">
			<?php
			printf(
				/* translators: %s: author display name */
				esc_html__( 'View all posts by %s', 'chw' ),
				esc_html( $author['name'] )
			);
			?>
		</a>
	</div>
</aside>

<?php
/**
 * Breadcrumb navigation for single blog posts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = chw_get_breadcrumb_items();

if ( count( $items ) < 2 ) {
	return;
}
?>

<nav class="post-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'chw' ); ?>">
	<ol class="post-breadcrumbs__list">
		<?php foreach ( $items as $index => $item ) : ?>
			<li class="post-breadcrumbs__item">
				<?php if ( ! empty( $item['url'] ) ) : ?>
					<a class="post-breadcrumbs__link" href="<?php echo esc_url( $item['url'] ); ?>">
						<?php echo esc_html( $item['label'] ); ?>
					</a>
				<?php else : ?>
					<span class="post-breadcrumbs__current" aria-current="page">
						<?php echo esc_html( $item['label'] ); ?>
					</span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
</nav>

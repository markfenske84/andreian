<?php
/**
 * Shared editorial post card.
 *
 * @var array $args Template arguments.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id       = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
$layout        = isset( $args['layout'] ) ? sanitize_html_class( $args['layout'] ) : 'grid';
$position      = isset( $args['position'] ) ? (int) $args['position'] : 1;
$priority      = ! empty( $args['priority'] );
$show_excerpt  = ! empty( $args['show_excerpt'] );
$show_all_categories = ! empty( $args['show_all_categories'] );
$show_author   = ! empty( $args['show_author'] );
$show_share    = ! empty( $args['show_share_links'] );
$current_category_id = isset( $args['current_category_id'] ) ? (int) $args['current_category_id'] : 0;
$item_list     = ! empty( $args['item_list'] );
$image_size    = isset( $args['image_size'] ) ? $args['image_size'] : 'large';
$thumbnail_id  = get_post_thumbnail_id( $post_id );
$categories    = get_the_category( $post_id );

$categories = array_values(
	array_filter(
		$categories,
		static function ( $category ) {
			return 'uncategorized' !== strtolower( $category->slug );
		}
	)
);

if ( $current_category_id ) {
	usort(
		$categories,
		static function ( $first, $second ) use ( $current_category_id ) {
			if ( (int) $first->term_id === $current_category_id ) {
				return -1;
			}

			if ( (int) $second->term_id === $current_category_id ) {
				return 1;
			}

			return strcasecmp( $first->name, $second->name );
		}
	);
}

$primary_cat   = ! empty( $categories ) ? $categories[0] : null;
$title         = get_the_title( $post_id );
$permalink     = get_permalink( $post_id );
$image_alt     = $thumbnail_id ? get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) : '';
$image_alt     = $image_alt ? $image_alt : $title;
$image_sizes   = 'full' === $layout
	? '(max-width: 1200px) 100vw, 1200px'
	: ( 'list' === $layout
		? '(max-width: 640px) 100vw, (max-width: 1200px) 38vw, 380px'
		: '(max-width: 640px) 100vw, (max-width: 960px) 50vw, 25vw' );
$image_attrs   = array(
	'alt'      => $image_alt,
	'decoding' => 'async',
	'loading'  => $priority ? 'eager' : 'lazy',
	'sizes'    => $image_sizes,
);

if ( $priority ) {
	$image_attrs['fetchpriority'] = 'high';
}

$share_url   = rawurlencode( $permalink );
$share_title = rawurlencode( wp_strip_all_tags( $title ) );
?>
<article
	class="andreian-card andreian-card--<?php echo esc_attr( $layout ); ?>"
	<?php if ( $item_list ) : ?>
		role="listitem" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"
	<?php endif; ?>>
	<?php if ( $item_list ) : ?>
		<meta itemprop="position" content="<?php echo esc_attr( $position ); ?>">
	<?php endif; ?>

	<?php if ( $thumbnail_id ) : ?>
		<a class="andreian-card__image-link" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
			<?php echo wp_get_attachment_image( $thumbnail_id, $image_size, false, $image_attrs ); ?>
		</a>
	<?php endif; ?>

	<div class="andreian-card__content">
		<?php if ( $show_all_categories && $categories ) : ?>
			<div class="andreian-card__categories">
				<?php foreach ( $categories as $category ) : ?>
					<a class="andreian-card__category" href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
						<?php echo esc_html( $category->name ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php elseif ( $primary_cat ) : ?>
			<a class="andreian-card__category" href="<?php echo esc_url( get_category_link( $primary_cat ) ); ?>">
				<?php echo esc_html( $primary_cat->name ); ?>
			</a>
		<?php endif; ?>

		<?php if ( 'archive-grid' === $layout ) : ?>
			<h2 class="andreian-card__title">
				<a href="<?php echo esc_url( $permalink ); ?>" itemprop="url">
					<span itemprop="name"><?php echo esc_html( $title ); ?></span>
				</a>
			</h2>
		<?php else : ?>
			<h3 class="andreian-card__title">
				<a href="<?php echo esc_url( $permalink ); ?>"<?php echo $item_list ? ' itemprop="url"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<span<?php echo $item_list ? ' itemprop="name"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $title ); ?></span>
				</a>
			</h3>
		<?php endif; ?>

		<div class="andreian-card__meta">
			<?php if ( $show_author ) : ?>
				<a href="<?php echo esc_url( andreian_get_post_author_url( $post_id ) ); ?>">
					<?php echo esc_html( andreian_get_post_author_name( $post_id ) ); ?>
				</a>
				<span aria-hidden="true">·</span>
			<?php endif; ?>
			<time datetime="<?php echo esc_attr( andreian_get_post_datetime( $post_id ) ); ?>">
				<?php echo esc_html( andreian_get_post_display_date( $post_id ) ); ?>
			</time>
			<?php if ( 'archive-grid' !== $layout ) : ?>
				<span aria-hidden="true">·</span>
				<span><?php echo esc_html( andreian_get_reading_time( $post_id ) ); ?></span>
			<?php endif; ?>
		</div>

		<?php if ( $show_excerpt ) : ?>
			<p class="andreian-card__excerpt"><?php echo esc_html( get_the_excerpt( $post_id ) ); ?></p>
		<?php endif; ?>

		<?php if ( $show_share ) : ?>
			<nav class="andreian-card__share" aria-label="<?php echo esc_attr( sprintf( __( 'Share %s', 'andreian' ), $title ) ); ?>">
				<span class="andreian-card__share-label"><?php esc_html_e( 'Share', 'andreian' ); ?></span>
				<a href="<?php echo esc_url( 'https://www.facebook.com/sharer/sharer.php?u=' . $share_url ); ?>" target="_blank" rel="nofollow noopener"><?php esc_html_e( 'Facebook', 'andreian' ); ?></a>
				<a href="<?php echo esc_url( 'https://twitter.com/intent/tweet?url=' . $share_url . '&text=' . $share_title ); ?>" target="_blank" rel="nofollow noopener"><?php esc_html_e( 'X', 'andreian' ); ?></a>
				<a href="<?php echo esc_url( 'mailto:?subject=' . $share_title . '&body=' . $share_url ); ?>"><?php esc_html_e( 'Email', 'andreian' ); ?></a>
				<a href="<?php echo esc_url( 'https://t.me/share/url?url=' . $share_url . '&text=' . $share_title ); ?>" target="_blank" rel="nofollow noopener"><?php esc_html_e( 'Telegram', 'andreian' ); ?></a>
				<a href="<?php echo esc_url( 'https://share.flipboard.com/bookmarklet/popout?v=2&url=' . $share_url . '&title=' . $share_title ); ?>" target="_blank" rel="nofollow noopener"><?php esc_html_e( 'Flipboard', 'andreian' ); ?></a>
			</nav>
		<?php endif; ?>
	</div>
</article>

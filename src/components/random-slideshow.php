<?php
/**
 * Random post slideshow for the Latest Entries sidebar.
 *
 * @var array $args Template arguments.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$query       = isset( $args['query'] ) && $args['query'] instanceof WP_Query ? $args['query'] : null;
$title       = isset( $args['title'] ) ? $args['title'] : __( 'Random', 'andreian' );
$slide_count = $query ? (int) $query->post_count : 0;

if ( ! $query || ! $query->have_posts() ) {
	return;
}

$slideshow_id = wp_unique_id( 'andreian-random-slideshow-' );
?>
<section
	class="andreian-random-slideshow"
	id="<?php echo esc_attr( $slideshow_id ); ?>"
	aria-roledescription="<?php esc_attr_e( 'carousel', 'andreian' ); ?>"
	aria-label="<?php echo esc_attr( $title ); ?>"
	data-autoplay="6000">
	<h3 class="andreian-random-slideshow__title widget-title"><?php echo esc_html( $title ); ?></h3>

	<div class="andreian-random-slideshow__viewport">
		<div class="andreian-random-slideshow__track">
			<?php
			$position = 0;
			while ( $query->have_posts() ) :
				$query->the_post();
				++$position;
				$post_id      = get_the_ID();
				$thumbnail_id = get_post_thumbnail_id( $post_id );
				$categories   = get_the_category( $post_id );
				$primary_cat  = null;

				foreach ( $categories as $category ) {
					if ( 'uncategorized' !== strtolower( $category->slug ) ) {
						$primary_cat = $category;
						break;
					}
				}

				$is_active = 1 === $position;
				?>
				<article
					class="andreian-card andreian-card--slideshow andreian-random-slideshow__slide<?php echo $is_active ? ' is-active' : ''; ?>"
					id="<?php echo esc_attr( $slideshow_id . '-slide-' . $position ); ?>"
					aria-hidden="<?php echo $is_active ? 'false' : 'true'; ?>"
					aria-roledescription="<?php esc_attr_e( 'slide', 'andreian' ); ?>"
					aria-label="<?php echo esc_attr( sprintf( __( '%1$d of %2$d', 'andreian' ), $position, $slide_count ) ); ?>">
					<?php if ( $thumbnail_id ) : ?>
						<a class="andreian-card__image-link" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
							<?php
							echo wp_get_attachment_image(
								$thumbnail_id,
								'andreian-card',
								false,
								array(
									'alt'      => get_the_title( $post_id ),
									'decoding' => 'async',
									'loading'  => $is_active ? 'eager' : 'lazy',
									'sizes'    => '(max-width: 960px) 100vw, 300px',
								)
							);
							?>
						</a>
					<?php endif; ?>

					<div class="andreian-card__content">
						<?php if ( $primary_cat ) : ?>
							<a class="andreian-card__category" href="<?php echo esc_url( get_category_link( $primary_cat->term_id ) ); ?>">
								<?php echo esc_html( $primary_cat->name ); ?>
							</a>
						<?php endif; ?>

						<h2 class="andreian-card__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>

						<div class="andreian-card__meta">
							<time datetime="<?php echo esc_attr( andreian_get_post_datetime( $post_id ) ); ?>">
								<?php echo esc_html( andreian_get_post_display_date( $post_id ) ); ?>
							</time>
							<span aria-hidden="true">·</span>
							<span><?php echo esc_html( andreian_get_reading_time( $post_id ) ); ?></span>
						</div>
					</div>
				</article>
				<?php
			endwhile;
			?>
		</div>
	</div>

	<?php if ( $slide_count > 1 ) : ?>
		<div class="andreian-random-slideshow__controls">
			<button
				class="andreian-random-slideshow__button andreian-random-slideshow__button--prev"
				type="button"
				aria-controls="<?php echo esc_attr( $slideshow_id ); ?>"
				aria-label="<?php esc_attr_e( 'Previous slide', 'andreian' ); ?>">
				<span aria-hidden="true">&larr;</span>
			</button>

			<div class="andreian-random-slideshow__dots" role="tablist" aria-label="<?php esc_attr_e( 'Slide navigation', 'andreian' ); ?>">
				<?php for ( $index = 1; $index <= $slide_count; $index++ ) : ?>
					<button
						class="andreian-random-slideshow__dot<?php echo 1 === $index ? ' is-active' : ''; ?>"
						type="button"
						role="tab"
						aria-selected="<?php echo 1 === $index ? 'true' : 'false'; ?>"
						aria-controls="<?php echo esc_attr( $slideshow_id . '-slide-' . $index ); ?>"
						data-slide-index="<?php echo esc_attr( $index - 1 ); ?>">
						<span class="sr-only"><?php echo esc_html( sprintf( __( 'Slide %d', 'andreian' ), $index ) ); ?></span>
					</button>
				<?php endfor; ?>
			</div>

			<button
				class="andreian-random-slideshow__button andreian-random-slideshow__button--next"
				type="button"
				aria-controls="<?php echo esc_attr( $slideshow_id ); ?>"
				aria-label="<?php esc_attr_e( 'Next slide', 'andreian' ); ?>">
				<span aria-hidden="true">&rarr;</span>
			</button>
		</div>
	<?php endif; ?>

	<p class="andreian-random-slideshow__live sr-only" aria-live="polite"></p>
</section>
<?php
wp_reset_postdata();

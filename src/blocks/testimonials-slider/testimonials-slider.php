<?php
/**
 * Frontend render for the Testimonials Slider block.
 *
 * Expects $items and $block_wrapper_attributes from the render callback.
 *
 * @var array  $items
 * @var string $block_wrapper_attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = isset( $items ) && is_array( $items ) ? $items : array();

if ( empty( $items ) ) {
	return;
}
?>

<section <?php echo $block_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="_container">
		<div class="testimonials-slider__swiper swiper">
			<div class="swiper-wrapper">
				<?php foreach ( $items as $item ) : ?>
					<figure class="swiper-slide testimonials-slider__card">
						<div class="testimonials-slider__stars" role="img" aria-label="<?php esc_attr_e( 'Rated 5 out of 5 stars', 'chw' ); ?>">
							<?php for ( $i = 0; $i < 5; $i++ ) : ?>
								<svg class="testimonials-slider__star" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
									<path d="M12 2l2.95 5.98 6.6.96-4.77 4.65 1.13 6.57L12 17.02l-5.91 3.1 1.13-6.57L2.45 8.94l6.6-.96L12 2z" />
								</svg>
							<?php endfor; ?>
						</div>

						<?php if ( ! empty( $item['quote'] ) ) : ?>
							<div class="testimonials-slider__quote"><?php echo wp_kses_post( $item['quote'] ); ?></div>
						<?php endif; ?>

						<figcaption class="testimonials-slider__author">
							<?php if ( ! empty( $item['initial'] ) ) : ?>
								<div class="testimonials-slider__avatar" aria-hidden="true">
									<span><?php echo esc_html( $item['initial'] ); ?></span>
								</div>
							<?php endif; ?>

							<div class="testimonials-slider__author-text">
								<?php if ( ! empty( $item['name'] ) ) : ?>
									<div class="testimonials-slider__name"><?php echo esc_html( wp_strip_all_tags( $item['name'] ) ); ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $item['location'] ) ) : ?>
									<div class="testimonials-slider__location"><?php echo esc_html( $item['location'] ); ?></div>
								<?php endif; ?>
							</div>
						</figcaption>
					</figure>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="testimonials-slider__nav">
			<button type="button" class="testimonials-slider__prev" aria-label="<?php esc_attr_e( 'Previous testimonial', 'chw' ); ?>">
				<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
					<path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</button>
			<button type="button" class="testimonials-slider__next" aria-label="<?php esc_attr_e( 'Next testimonial', 'chw' ); ?>">
				<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
					<path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
				</svg>
			</button>
		</div>
	</div>
</section>

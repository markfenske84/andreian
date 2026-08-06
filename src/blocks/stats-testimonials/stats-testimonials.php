<?php
/**
 * Frontend render for the Stats and Testimonials block.
 *
 * Expects $attributes provided by the render callback in
 * register-stats-testimonials-block.php.
 *
 * @var array $attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();

$background_url = isset( $attributes['backgroundImageUrl'] ) ? $attributes['backgroundImageUrl'] : '';
$stats         = isset( $attributes['stats'] ) && is_array( $attributes['stats'] ) ? $attributes['stats'] : array();
$testimonials  = isset( $attributes['testimonials'] ) && is_array( $attributes['testimonials'] ) ? $attributes['testimonials'] : array();

if ( empty( $background_url ) && function_exists( 'chw_stats_testimonials_default_background' ) ) {
	$background_url = chw_stats_testimonials_default_background();
}

$inline_allowed = array(
	'span'   => array( 'class' => array() ),
	'strong' => array(),
	'em'     => array(),
	'br'     => array(),
);

$stats = array_filter(
	$stats,
	function ( $stat ) {
		return ! empty( $stat['value'] ) || ! empty( $stat['label'] );
	}
);
$stats = array_values( array_slice( $stats, 0, 4 ) );

$testimonials = array_filter(
	$testimonials,
	function ( $testimonial ) {
		return ! empty( $testimonial['quote'] ) || ! empty( $testimonial['name'] ) || ! empty( $testimonial['location'] );
	}
);
$testimonials = array_values( array_slice( $testimonials, 0, 3 ) );

if ( empty( $stats ) && empty( $testimonials ) ) {
	return;
}
?>

<section class="stats-testimonials">
	<div class="stats-testimonials__bg" aria-hidden="true" style="background-image: url('<?php echo esc_url( $background_url ); ?>');"></div>
	<div class="stats-testimonials__overlay" aria-hidden="true"></div>

	<div class="stats-testimonials__inner _container">
		<?php if ( ! empty( $stats ) ) : ?>
			<div class="stats-testimonials__stats" data-count="<?php echo esc_attr( count( $stats ) ); ?>">
				<?php foreach ( $stats as $stat ) : ?>
					<div class="stats-testimonials__stat">
						<span class="stats-testimonials__stat-bar" aria-hidden="true"></span>
						<div class="stats-testimonials__stat-body">
							<?php if ( ! empty( $stat['value'] ) ) : ?>
								<div class="stats-testimonials__stat-value"><?php echo wp_kses( $stat['value'], $inline_allowed ); ?></div>
							<?php endif; ?>
							<?php if ( ! empty( $stat['label'] ) ) : ?>
								<div class="stats-testimonials__stat-label"><?php echo wp_kses( $stat['label'], $inline_allowed ); ?></div>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $testimonials ) ) : ?>
			<div
				class="stats-testimonials__cards-wrap"
				data-count="<?php echo esc_attr( count( $testimonials ) ); ?>">
				<div class="stats-testimonials__cards" data-count="<?php echo esc_attr( count( $testimonials ) ); ?>">
				<?php foreach ( $testimonials as $testimonial ) : ?>
					<figure class="stats-testimonials__card">
						<?php if ( ! empty( $testimonial['quote'] ) ) : ?>
							<blockquote class="stats-testimonials__quote"><?php echo wp_kses( $testimonial['quote'], $inline_allowed ); ?></blockquote>
						<?php endif; ?>
						<figcaption class="stats-testimonials__card-foot">
							<div class="stats-testimonials__author">
								<?php if ( ! empty( $testimonial['name'] ) ) : ?>
									<div class="stats-testimonials__author-name"><?php echo wp_kses( $testimonial['name'], $inline_allowed ); ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $testimonial['location'] ) ) : ?>
									<div class="stats-testimonials__author-location"><?php echo wp_kses( $testimonial['location'], $inline_allowed ); ?></div>
								<?php endif; ?>
							</div>
							<div class="stats-testimonials__stars" role="img" aria-label="<?php esc_attr_e( 'Rated 5 out of 5 stars', 'chw' ); ?>">
								<?php for ( $i = 0; $i < 5; $i++ ) : ?>
									<svg class="stats-testimonials__star" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
										<path d="M12 2l2.95 5.98 6.6.96-4.77 4.65 1.13 6.57L12 17.02l-5.91 3.1 1.13-6.57L2.45 8.94l6.6-.96L12 2z" />
									</svg>
								<?php endfor; ?>
							</div>
						</figcaption>
					</figure>
				<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

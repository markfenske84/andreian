<?php
/**
 * Frontend render for the Plans Section block.
 *
 * Expects $attributes (and optionally $block_wrapper_attributes) provided by the
 * render callback in register-plans-block.php.
 *
 * @var array  $attributes
 * @var string $block_wrapper_attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();

$eyebrow        = isset( $attributes['eyebrow'] ) ? $attributes['eyebrow'] : '';
$heading        = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$heading_accent = isset( $attributes['headingAccent'] ) ? $attributes['headingAccent'] : '';
$description    = isset( $attributes['description'] ) ? $attributes['description'] : '';
$plans          = isset( $attributes['plans'] ) && is_array( $attributes['plans'] ) ? $attributes['plans'] : array();

// Allowed inline formatting for RichText-authored fields.
$inline_allowed = array(
	'span'   => array( 'class' => array() ),
	'strong' => array(),
	'em'     => array(),
	'br'     => array(),
);

// Heading supports the core Highlight (text color) format, which outputs
// <mark>/<span> with has-*-color classes and inline color styles.
$heading_allowed = array(
	'span'   => array(
		'class' => array(),
		'style' => array(),
	),
	'mark'   => array(
		'class' => array(),
		'style' => array(),
	),
	'strong' => array(),
	'em'     => array(),
	'br'     => array(),
);

// Keep only plans that have at least some content.
$plans = array_filter(
	$plans,
	function ( $plan ) {
		$features = isset( $plan['features'] ) && is_array( $plan['features'] ) ? array_filter( $plan['features'] ) : array();
		return ! empty( $plan['label'] ) || ! empty( $plan['subtitle'] ) || ! empty( $features );
	}
);
$plans = array_slice( array_values( $plans ), 0, 3 );

// Global legal text (always rendered for this block).
$legal_text = get_theme_mod( 'legal_text', '' );

// Legacy headingAccent is kept in saved block data from the old dual-field editor.
// Skip rendering it when the same text already lives in the single heading field.
$heading_plain      = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $heading ) ) );
$accent_plain       = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $heading_accent ) ) );
$show_legacy_accent = $heading_accent && ( '' === $heading || false === stripos( $heading_plain, $accent_plain ) );

// Nothing meaningful to render.
if ( empty( $plans ) && empty( $eyebrow ) && empty( $heading ) && ! $show_legacy_accent && empty( $description ) ) {
	return;
}

$wrapper_attributes = isset( $block_wrapper_attributes ) ? $block_wrapper_attributes : '';
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>

	<div class="_container">

		<div class="plans-section__header _text -align-left">
			<?php if ( $eyebrow ) : ?>
				<span class="_eyebrow -plain _text -tertiary"><?php echo wp_kses( $eyebrow, $inline_allowed ); ?></span>
			<?php endif; ?>

			<?php if ( $heading || $show_legacy_accent ) : ?>
				<h2 class="plans-section__heading _text -secondary">
					<?php if ( $heading ) : ?>
						<?php echo wp_kses( $heading, $heading_allowed ); ?>
					<?php endif; ?>
					<?php if ( $show_legacy_accent ) : ?>
						<span class="accent"><?php echo wp_kses( $heading_accent, $inline_allowed ); ?></span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $description ) : ?>
				<p class="plans-section__intro _text -muted _text-size -lg"><?php echo wp_kses( $description, $inline_allowed ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $plans ) ) : ?>
			<div class="plans-section__cards" data-count="<?php echo esc_attr( count( $plans ) ); ?>">
				<?php foreach ( $plans as $index => $plan ) : ?>
					<?php
					// Card styling is fixed by position: 1st blue, 2nd orange
					// (popular), 3rd gray.
					if ( 1 === $index ) {
						$variant = 'featured';
					} elseif ( 2 === $index ) {
						$variant = 'muted';
					} else {
						$variant = 'blue';
					}
					$is_featured = ( 'featured' === $variant );

					$features   = isset( $plan['features'] ) && is_array( $plan['features'] ) ? array_filter( $plan['features'] ) : array();
					$card_class = 'plans-section__card -' . $variant;

					$button    = isset( $plan['button'] ) && is_array( $plan['button'] ) ? $plan['button'] : array();
					$btn_text  = isset( $button['text'] ) ? $button['text'] : '';
					$btn_url   = isset( $button['url'] ) ? $button['url'] : '#';
					$btn_blank = ! empty( $button['new_tab'] );
					// Button color follows the card variant; every CTA carries the
					// arrow icon to match the design.
					if ( 'featured' === $variant ) {
						$btn_color = '-primary';
					} elseif ( 'blue' === $variant ) {
						$btn_color = '-secondary';
					} else {
						$btn_color = '-outline -text';
					}
					$btn_class = '_button -arrow ' . $btn_color;

					// Eyebrow label color also follows the card variant.
					if ( 'featured' === $variant ) {
						$label_color = '-primary';
					} elseif ( 'muted' === $variant ) {
						$label_color = '-tertiary';
					} else {
						$label_color = '-secondary';
					}
					?>
					<article class="<?php echo esc_attr( $card_class ); ?>">
						<?php if ( $is_featured ) : ?>
							<span class="plans-section__ribbon"><?php esc_html_e( 'Most Popular', 'chw' ); ?></span>
						<?php endif; ?>

						<div class="plans-section__card-head">
							<?php if ( ! empty( $plan['label'] ) ) : ?>
								<span class="_eyebrow -on-light _text <?php echo esc_attr( $label_color ); ?> plans-section__card-label"><?php echo wp_kses( $plan['label'], $inline_allowed ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $plan['subtitle'] ) ) : ?>
								<p class="plans-section__card-subtitle _text -muted"><?php echo wp_kses( $plan['subtitle'], $inline_allowed ); ?></p>
							<?php endif; ?>
						</div>

						<?php if ( ! empty( $features ) ) : ?>
							<ul class="plans-section__features">
								<?php foreach ( $features as $feature ) : ?>
									<li class="plans-section__feature">
										<svg class="plans-section__check" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
											<path d="M20 6 9 17l-5-5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
										<span><?php echo wp_kses( $feature, $inline_allowed ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php if ( $btn_text ) : ?>
							<div class="plans-section__card-cta">
								<a
									class="<?php echo esc_attr( $btn_class ); ?>"
									href="<?php echo esc_url( $btn_url ? $btn_url : '#' ); ?>"
									<?php if ( $btn_blank ) { ?>target="_blank" rel="noopener"<?php } ?>>
									<?php echo esc_html( $btn_text ); ?>
								</a>
							</div>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $legal_text ) ) : ?>
			<div class="plans-section__legal _text -align-center _text -muted"><?php echo wp_kses_post( $legal_text ); ?></div>
		<?php endif; ?>

	</div>
</section>

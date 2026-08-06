<?php
/**
 * Frontend render for the Audience Section block.
 *
 * Expects $attributes (and optionally $block_wrapper_attributes) provided by the
 * render callback in register-audience-block.php.
 *
 * @var array  $attributes
 * @var string $block_wrapper_attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();

$eyebrow_icon = isset( $attributes['eyebrowIcon'] ) ? $attributes['eyebrowIcon'] : '';
$eyebrow_text = isset( $attributes['eyebrowText'] ) ? $attributes['eyebrowText'] : '';
$heading      = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$description  = isset( $attributes['description'] ) ? $attributes['description'] : '';
$cards        = isset( $attributes['cards'] ) && is_array( $attributes['cards'] ) ? $attributes['cards'] : array();

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

// Keep only cards that have meaningful content.
$cards = array_filter(
	$cards,
	function ( $card ) {
		return ! empty( $card['icon'] ) || ! empty( $card['headline'] ) || ! empty( $card['description'] ) || ! empty( $card['ctaLabel'] );
	}
);
$cards = array_values( $cards );

// Global legal text always renders for this block.
$legal_text = get_theme_mod( 'legal_text', '' );

// Nothing meaningful to render.
if ( empty( $cards ) && empty( $eyebrow_text ) && empty( $heading ) && empty( $description ) ) {
	return;
}

$wrapper_attributes = isset( $block_wrapper_attributes ) ? $block_wrapper_attributes : 'class="audience-section"';

// Inline arrow icon used in each card's CTA.
$arrow_icon = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>';
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="_container">
		<div class="audience-section__header _text -align-left">
			<?php if ( $eyebrow_icon || $eyebrow_text ) : ?>
				<span class="_eyebrow -brand-orange">
					<?php if ( $eyebrow_icon ) : ?>
						<span class="_eyebrow__icon" aria-hidden="true"><?php echo chw_sanitize_inline_svg( $eyebrow_icon ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<?php endif; ?>
					<?php if ( $eyebrow_text ) : ?>
						<span class="_eyebrow__label"><?php echo wp_kses( $eyebrow_text, $inline_allowed ); ?></span>
					<?php endif; ?>
				</span>
			<?php endif; ?>

			<?php if ( $heading ) : ?>
				<h2 class="audience-section__heading _text -secondary"><?php echo wp_kses( $heading, $heading_allowed ); ?></h2>
			<?php endif; ?>

			<?php if ( $description ) : ?>
				<p class="audience-section__intro _text -muted _text-size -lg"><?php echo wp_kses( $description, $inline_allowed ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $cards ) ) : ?>
			<div class="audience-section__cards" data-count="<?php echo esc_attr( count( $cards ) ); ?>">
				<?php foreach ( $cards as $i => $card ) : ?>
					<?php
					$card_url     = isset( $card['url'] ) ? $card['url'] : '';
					$card_blank   = ! empty( $card['new_tab'] );
					$card_label   = isset( $card['ctaLabel'] ) ? $card['ctaLabel'] : '';
					?>
					<a
						class="audience-section__card"
						href="<?php echo esc_url( $card_url ? $card_url : '#' ); ?>"
						<?php if ( $card_blank ) { ?>target="_blank" rel="noopener"<?php } ?>>
						<span class="audience-section__number" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>

						<?php if ( ! empty( $card['icon'] ) ) : ?>
							<span class="audience-section__icon" aria-hidden="true"><?php echo chw_sanitize_inline_svg( $card['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<?php endif; ?>

						<?php if ( ! empty( $card['headline'] ) ) : ?>
							<h3 class="audience-section__card-title _text -secondary"><?php echo wp_kses( $card['headline'], $inline_allowed ); ?></h3>
						<?php endif; ?>

						<?php if ( ! empty( $card['description'] ) ) : ?>
							<p class="audience-section__card-text _text -muted"><?php echo wp_kses( $card['description'], $inline_allowed ); ?></p>
						<?php endif; ?>

						<span class="audience-section__cta">
							<?php if ( $card_label ) : ?>
								<span class="audience-section__cta-label"><?php echo esc_html( $card_label ); ?></span>
							<?php endif; ?>
							<span class="audience-section__cta-arrow" aria-hidden="true"><?php echo $arrow_icon; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $legal_text ) ) : ?>
			<div class="audience-section__legal _text -align-center _text -muted"><?php echo wp_kses_post( $legal_text ); ?></div>
		<?php endif; ?>
	</div>
</section>

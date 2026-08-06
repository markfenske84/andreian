<?php
/**
 * Frontend render for the Comparison Table block.
 *
 * Expects $attributes (and optionally $block_wrapper_attributes) provided by the
 * render callback in register-comparison-table-block.php.
 *
 * @var array  $attributes
 * @var string $block_wrapper_attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();

$eyebrow     = isset( $attributes['eyebrow'] ) ? $attributes['eyebrow'] : '';
$heading     = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$description = isset( $attributes['description'] ) ? $attributes['description'] : '';
$comparisons = isset( $attributes['comparisons'] ) && is_array( $attributes['comparisons'] ) ? $attributes['comparisons'] : array();
$button      = isset( $attributes['button'] ) && is_array( $attributes['button'] ) ? $attributes['button'] : array();
$show_legal  = ! array_key_exists( 'showLegal', $attributes ) || ! empty( $attributes['showLegal'] );

$inline_allowed = array(
	'span'   => array( 'class' => array() ),
	'strong' => array(),
	'em'     => array(),
	'br'     => array(),
);

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

$comparisons = array_filter(
	$comparisons,
	function ( $comparison ) {
		$features = isset( $comparison['features'] ) && is_array( $comparison['features'] ) ? array_filter( $comparison['features'] ) : array();
		return ! empty( $comparison['title'] ) || ! empty( $comparison['icon'] ) || ! empty( $features );
	}
);
$comparisons = array_slice( array_values( $comparisons ), 0, 3 );

$btn_text      = isset( $button['text'] ) ? $button['text'] : '';
$btn_url       = isset( $button['url'] ) ? $button['url'] : '#';
$btn_blank     = ! empty( $button['new_tab'] );
$btn_modifier  = isset( $button['modifier'] ) ? (string) $button['modifier'] : 'arrow';
$allowed_mods  = function_exists( 'chw_masthead_button_modifiers' ) ? chw_masthead_button_modifiers() : array( '', 'arrow', 'phone', 'download' );
if ( ! in_array( $btn_modifier, $allowed_mods, true ) ) {
	$btn_modifier = 'arrow';
}
$btn_class = '_button -primary';
if ( $btn_modifier ) {
	$btn_class .= ' -' . sanitize_html_class( $btn_modifier );
}

$legal_text = get_theme_mod( 'legal_text', '' );

if ( count( $comparisons ) < 2 && empty( $eyebrow ) && empty( $heading ) && empty( $description ) && empty( $btn_text ) ) {
	return;
}

$wrapper_attributes = isset( $block_wrapper_attributes ) ? $block_wrapper_attributes : '';
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>

	<div class="_container">

		<div class="comparison-table__header _text -align-left">
			<?php if ( $eyebrow ) : ?>
				<span class="_eyebrow -plain _text -primary"><?php echo wp_kses( $eyebrow, $inline_allowed ); ?></span>
			<?php endif; ?>

			<?php if ( $heading ) : ?>
				<h2 class="comparison-table__heading _text -secondary"><?php echo wp_kses( $heading, $heading_allowed ); ?></h2>
			<?php endif; ?>

			<?php if ( $description ) : ?>
				<p class="comparison-table__intro _text -muted _text-size -lg"><?php echo wp_kses( $description, $inline_allowed ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( count( $comparisons ) >= 2 ) : ?>
			<div class="comparison-table__cards" data-count="<?php echo esc_attr( count( $comparisons ) ); ?>">
				<?php foreach ( $comparisons as $index => $comparison ) : ?>
					<?php
					if ( 1 === $index ) {
						$variant = 'secondary';
					} elseif ( 2 === $index ) {
						$variant = 'muted';
					} else {
						$variant = 'primary';
					}

					$features   = isset( $comparison['features'] ) && is_array( $comparison['features'] ) ? array_filter( $comparison['features'] ) : array();
					$card_class = 'comparison-table__card -' . $variant;
					?>
					<article class="<?php echo esc_attr( $card_class ); ?>">
						<div class="comparison-table__card-head">
							<?php if ( ! empty( $comparison['icon'] ) && function_exists( 'chw_sanitize_inline_svg' ) ) : ?>
								<div class="comparison-table__icon" aria-hidden="true">
									<?php echo chw_sanitize_inline_svg( $comparison['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $comparison['title'] ) ) : ?>
								<h3 class="comparison-table__card-title _text -secondary"><?php echo wp_kses( $comparison['title'], $inline_allowed ); ?></h3>
							<?php endif; ?>
						</div>

						<?php if ( ! empty( $features ) ) : ?>
							<ul class="comparison-table__features">
								<?php foreach ( $features as $feature ) : ?>
									<li class="comparison-table__feature">
										<span><?php echo wp_kses( $feature, $inline_allowed ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $btn_text ) : ?>
			<div class="comparison-table__cta">
				<a
					class="<?php echo esc_attr( $btn_class ); ?>"
					href="<?php echo esc_url( $btn_url ? $btn_url : '#' ); ?>"
					<?php if ( $btn_blank ) { ?>target="_blank" rel="noopener"<?php } ?>>
					<?php echo esc_html( $btn_text ); ?>
				</a>
			</div>
		<?php endif; ?>

		<?php if ( $show_legal && ! empty( $legal_text ) ) : ?>
			<div class="comparison-table__legal _text -align-left _text -muted"><?php echo wp_kses_post( $legal_text ); ?></div>
		<?php endif; ?>

	</div>
</section>

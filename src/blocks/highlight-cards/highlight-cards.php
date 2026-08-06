<?php
/**
 * Frontend render for the Highlight Cards block.
 *
 * Expects $attributes (and optionally $block_wrapper_attributes) provided by the
 * render callback in register-highlight-cards-block.php.
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
$cards          = isset( $attributes['cards'] ) && is_array( $attributes['cards'] ) ? $attributes['cards'] : array();
$buttons        = isset( $attributes['buttons'] ) && is_array( $attributes['buttons'] ) ? $attributes['buttons'] : array();
$show_legal     = ! empty( $attributes['showLegal'] );
$badge_style    = isset( $attributes['badgeStyle'] ) ? (string) $attributes['badgeStyle'] : 'plain';
$cards_per_row  = isset( $attributes['cardsPerRow'] ) ? (int) $attributes['cardsPerRow'] : 3;
$legal_align    = isset( $attributes['legalAlign'] ) ? (string) $attributes['legalAlign'] : 'center';

if ( ! in_array( $badge_style, chw_highlight_cards_badge_style_options(), true ) ) {
	$badge_style = 'plain';
}

if ( ! in_array( $cards_per_row, chw_highlight_cards_per_row_options(), true ) ) {
	$cards_per_row = 3;
}

if ( ! in_array( $legal_align, chw_highlight_cards_legal_align_options(), true ) ) {
	$legal_align = 'center';
}

$badge_attrs = array(
	'style'     => $badge_style,
	'textColor' => 'tertiary',
);
$eyebrow_classes = function_exists( 'chw_badge_eyebrow_classes' )
	? chw_badge_eyebrow_classes( $badge_attrs )
	: '_eyebrow -plain _text -tertiary';

$cards_grid_classes = 'highlight-cards__cards';
if ( 4 === $cards_per_row ) {
	$cards_grid_classes .= ' -cols-4';
}

$legal_classes = 'highlight-cards__legal _text -muted _text -align-' . sanitize_html_class( $legal_align );

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

$cards = array_filter(
	$cards,
	function ( $card ) {
		return ! empty( $card['icon'] ) || ! empty( $card['headline'] ) || ! empty( $card['description'] );
	}
);
$cards = array_values( $cards );

$buttons = array_filter(
	$buttons,
	function ( $button ) {
		return ! empty( $button['text'] );
	}
);
$buttons = array_values( $buttons );

$allowed_modifiers = function_exists( 'chw_masthead_button_modifiers' ) ? chw_masthead_button_modifiers() : array( '', 'arrow', 'phone' );

$legal_text = get_theme_mod( 'legal_text', '' );

// Legacy headingAccent is kept in saved block data from the old dual-field editor.
// Skip rendering it when the same text already lives in the single heading field.
$heading_plain      = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $heading ) ) );
$accent_plain       = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $heading_accent ) ) );
$show_legacy_accent = $heading_accent && ( '' === $heading || false === stripos( $heading_plain, $accent_plain ) );

if ( empty( $cards ) && empty( $buttons ) && empty( $eyebrow ) && empty( $heading ) && ! $show_legacy_accent && empty( $description ) ) {
	return;
}
?>

<section <?php echo $block_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="_container">
		<div class="highlight-cards__header _text -align-left">
			<?php if ( $eyebrow ) : ?>
				<span class="<?php echo esc_attr( $eyebrow_classes ); ?>">
					<span class="_eyebrow__label"><?php echo wp_kses( $eyebrow, $inline_allowed ); ?></span>
				</span>
			<?php endif; ?>

			<?php if ( $heading || $show_legacy_accent ) : ?>
				<h2 class="highlight-cards__heading _text -secondary">
					<?php if ( $heading ) : ?>
						<?php echo wp_kses( $heading, $heading_allowed ); ?>
					<?php endif; ?>
					<?php if ( $show_legacy_accent ) : ?>
						<span class="accent"><?php echo wp_kses( $heading_accent, $inline_allowed ); ?></span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $description ) : ?>
				<p class="highlight-cards__intro _text -muted _text-size -lg"><?php echo wp_kses( $description, $inline_allowed ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $cards ) ) : ?>
			<div class="<?php echo esc_attr( $cards_grid_classes ); ?>" data-count="<?php echo esc_attr( count( $cards ) ); ?>">
				<?php foreach ( $cards as $card ) : ?>
					<article class="highlight-cards__card">
						<?php if ( ! empty( $card['icon'] ) && function_exists( 'chw_sanitize_inline_svg' ) ) : ?>
							<div class="highlight-cards__icon" aria-hidden="true">
								<?php echo chw_sanitize_inline_svg( $card['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</div>
						<?php endif; ?>
						<?php if ( ! empty( $card['headline'] ) ) : ?>
							<h3 class="highlight-cards__card-title _text -secondary"><?php echo wp_kses( $card['headline'], $inline_allowed ); ?></h3>
						<?php endif; ?>
						<?php if ( ! empty( $card['description'] ) ) : ?>
							<p class="highlight-cards__card-text _text -muted"><?php echo wp_kses( $card['description'], $inline_allowed ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $buttons ) ) : ?>
			<div class="highlight-cards__buttons _flex -align-center -justify-center">
				<?php
				foreach ( $buttons as $index => $button ) {
					$btn_modifier = isset( $button['modifier'] ) ? (string) $button['modifier'] : '';
					if ( ! in_array( $btn_modifier, $allowed_modifiers, true ) ) {
						$btn_modifier = '';
					}
					$btn_class = '_button ' . ( 0 === $index ? '-primary' : '-outline -text' );
					if ( $btn_modifier ) {
						$btn_class .= ' -' . sanitize_html_class( $btn_modifier );
					}
					$btn_text  = isset( $button['text'] ) ? $button['text'] : '';
					$btn_url   = isset( $button['url'] ) ? $button['url'] : '#';
					$btn_blank = ! empty( $button['new_tab'] );
					?>
					<a
						class="<?php echo esc_attr( $btn_class ); ?>"
						href="<?php echo esc_url( $btn_url ? $btn_url : '#' ); ?>"
						<?php if ( $btn_blank ) { ?>target="_blank" rel="noopener"<?php } ?>>
						<?php echo esc_html( $btn_text ); ?>
					</a>
					<?php
				}
				?>
			</div>
		<?php endif; ?>

		<?php if ( $show_legal && ! empty( $legal_text ) ) : ?>
			<div class="<?php echo esc_attr( $legal_classes ); ?>"><?php echo wp_kses_post( $legal_text ); ?></div>
		<?php endif; ?>
	</div>
</section>

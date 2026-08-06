<?php
/**
 * Frontend render for the Call To Action: Highlights block.
 *
 * Expects $attributes (and optionally $block_wrapper_attributes) provided by the
 * render callback in register-cta-highlights-block.php.
 *
 * @var array  $attributes
 * @var string $block_wrapper_attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();

$badge_text   = isset( $attributes['badgeText'] ) ? $attributes['badgeText'] : '';
$heading      = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$description  = isset( $attributes['description'] ) ? $attributes['description'] : '';
$buttons      = isset( $attributes['buttons'] ) && is_array( $attributes['buttons'] ) ? $attributes['buttons'] : array();
$sub_text     = isset( $attributes['subText'] ) ? $attributes['subText'] : '';
$show_legal   = ! empty( $attributes['showLegal'] );
$info_eyebrow = isset( $attributes['infoEyebrow'] ) ? $attributes['infoEyebrow'] : '';
$highlights   = isset( $attributes['highlights'] ) && is_array( $attributes['highlights'] ) ? $attributes['highlights'] : array();
$info_text    = isset( $attributes['infoText'] ) ? $attributes['infoText'] : '';

// Allowed inline formatting for RichText-authored fields.
$inline_allowed = array(
	'span'   => array( 'class' => array() ),
	'strong' => array(),
	'em'     => array(),
	'br'     => array(),
);

// Heading supports WP text-color format (has-*-color classes and inline styles).
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

// Allowed formatting for the info paragraph (supports a phone/email link).
$info_allowed = array(
	'a'      => array(
		'href'   => array(),
		'target' => array(),
		'rel'    => array(),
	),
	'strong' => array(),
	'em'     => array(),
	'br'     => array(),
	'span'   => array( 'class' => array() ),
);

// Keep only buttons that have text.
$buttons = array_filter(
	$buttons,
	function ( $button ) {
		return ! empty( $button['text'] );
	}
);
$buttons = array_slice( array_values( $buttons ), 0, 3 );

// Keep only highlights that have text.
$highlights = array_filter(
	$highlights,
	function ( $highlight ) {
		return ! empty( $highlight['text'] );
	}
);
$highlights = array_values( $highlights );

$allowed_modifiers = function_exists( 'chw_masthead_button_modifiers' ) ? chw_masthead_button_modifiers() : array( '', 'arrow', 'phone' );

// Global legal text.
$legal_text = get_theme_mod( 'legal_text', '' );

// Nothing meaningful to render.
if ( empty( $badge_text ) && empty( $heading ) && empty( $description ) && empty( $buttons ) && empty( $sub_text ) && empty( $info_eyebrow ) && empty( $highlights ) && empty( $info_text ) ) {
	return;
}

$wrapper_attributes = isset( $block_wrapper_attributes ) ? $block_wrapper_attributes : '';

// Inline checkmark icon used for the highlight list markers.
$check_icon = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false"><path d="M5 12.5l4 4 10-10" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" /></svg>';
?>

<div class="_container">
	<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<div class="cta-highlights__box">

			<div class="cta-highlights__main">

				<?php if ( $badge_text ) : ?>
					<span class="_eyebrow cta-highlights__badge"><?php echo wp_kses( $badge_text, $inline_allowed ); ?></span>
				<?php endif; ?>

				<?php if ( $heading ) : ?>
					<h3 class="cta-highlights__heading _text-style -h1"><?php echo wp_kses( $heading, $heading_allowed ); ?></h3>
				<?php endif; ?>

				<?php if ( $description ) : ?>
					<p class="cta-highlights__intro -xl"><?php echo wp_kses( $description, $inline_allowed ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $buttons ) ) : ?>
					<div class="cta-highlights__buttons _flex -align-center">
						<?php
						foreach ( $buttons as $index => $button ) {
							$btn_modifier = isset( $button['modifier'] ) ? (string) $button['modifier'] : '';
							if ( ! in_array( $btn_modifier, $allowed_modifiers, true ) ) {
								$btn_modifier = '';
							}
							$btn_class = '_button ' . ( 0 === $index ? '-primary' : '-outline' );
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

				<?php if ( $sub_text ) : ?>
					<p class="cta-highlights__subtext _text -sm"><?php echo wp_kses( $sub_text, $inline_allowed ); ?></p>
				<?php endif; ?>

			</div>

			<?php if ( $info_eyebrow || ! empty( $highlights ) || $info_text ) : ?>
				<aside class="cta-highlights__info">

					<?php if ( $info_eyebrow ) : ?>
						<span class="_eyebrow -plain cta-highlights__info-eyebrow"><?php echo wp_kses( $info_eyebrow, $inline_allowed ); ?></span>
					<?php endif; ?>

					<?php if ( ! empty( $highlights ) ) : ?>
						<ul class="cta-highlights__checks">
							<?php foreach ( $highlights as $highlight ) : ?>
								<li class="cta-highlights__check">
									<span class="cta-highlights__check-icon" aria-hidden="true"><?php echo $check_icon; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
									<span class="cta-highlights__check-text"><?php echo wp_kses( $highlight['text'], $inline_allowed ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( $info_text ) : ?>
						<p class="cta-highlights__info-text"><?php echo wp_kses( $info_text, $info_allowed ); ?></p>
					<?php endif; ?>

				</aside>
			<?php endif; ?>

			<?php if ( $show_legal && ! empty( $legal_text ) ) : ?>
				<div class="cta-highlights__legal"><p class="_text -xs"><?php echo wp_kses_post( $legal_text ); ?></p></div>
			<?php endif; ?>

		</div>
	</section>
</div>
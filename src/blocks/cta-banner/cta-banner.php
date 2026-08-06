<?php
/**
 * Frontend render for the Call To Action: Banner block.
 *
 * Expects $attributes (and optionally $block_wrapper_attributes) provided by the
 * render callback in register-cta-banner-block.php.
 *
 * @var array  $attributes
 * @var string $block_wrapper_attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();

$heading     = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$description = isset( $attributes['description'] ) ? $attributes['description'] : '';
$sub_text    = isset( $attributes['subText'] ) ? $attributes['subText'] : '';
$buttons     = isset( $attributes['buttons'] ) && is_array( $attributes['buttons'] ) ? $attributes['buttons'] : array();
$show_legal  = ! array_key_exists( 'showLegal', $attributes ) || ! empty( $attributes['showLegal'] );

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

// Keep only buttons that have text.
$buttons = array_filter(
	$buttons,
	function ( $button ) {
		return ! empty( $button['text'] );
	}
);
$buttons = array_slice( array_values( $buttons ), 0, 3 );

$allowed_modifiers = function_exists( 'chw_masthead_button_modifiers' ) ? chw_masthead_button_modifiers() : array( '', 'arrow', 'phone' );

// Global legal text from the Customizer (optional per-block toggle).
$legal_text = get_theme_mod( 'legal_text', '' );

// Nothing meaningful to render.
if ( empty( $heading ) && empty( $description ) && empty( $sub_text ) && empty( $buttons ) ) {
	return;
}

$wrapper_attributes = isset( $block_wrapper_attributes ) ? $block_wrapper_attributes : '';
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="_container">
		<div class="cta-banner__top">

				<div class="cta-banner__main">
					<?php if ( $heading ) : ?>
						<h2 class="cta-banner__heading _text-style -h2"><?php echo wp_kses( $heading, $heading_allowed ); ?></h2>
					<?php endif; ?>

					<?php if ( $description ) : ?>
						<p class="cta-banner__intro -xl"><?php echo wp_kses( $description, $inline_allowed ); ?></p>
					<?php endif; ?>

					<?php if ( $sub_text ) : ?>
						<p class="cta-banner__subtext"><?php echo wp_kses( $sub_text, $inline_allowed ); ?></p>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $buttons ) ) : ?>
					<div class="cta-banner__buttons">
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

			</div>

		<?php if ( $show_legal && ! empty( $legal_text ) ) : ?>
			<div class="cta-banner__legal"><p class="_text -xs"><?php echo wp_kses_post( $legal_text ); ?></p></div>
		<?php endif; ?>
	</div>
</section>

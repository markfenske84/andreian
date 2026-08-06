<?php
/**
 * Frontend render for the Steps Section block.
 *
 * Expects $attributes (and optionally $block_wrapper_attributes) provided by the
 * render callback in register-steps-block.php.
 *
 * @var array  $attributes
 * @var string $block_wrapper_attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();

$heading        = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$heading_accent = isset( $attributes['headingAccent'] ) ? $attributes['headingAccent'] : '';
$description    = isset( $attributes['description'] ) ? $attributes['description'] : '';
$steps          = isset( $attributes['steps'] ) && is_array( $attributes['steps'] ) ? $attributes['steps'] : array();
$buttons        = isset( $attributes['buttons'] ) && is_array( $attributes['buttons'] ) ? $attributes['buttons'] : array();
$show_legal     = ! empty( $attributes['showLegal'] );

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

// Keep only steps that have at least some content.
$steps = array_filter(
	$steps,
	function ( $step ) {
		return ! empty( $step['icon'] ) || ! empty( $step['title'] ) || ! empty( $step['description'] );
	}
);
$steps = array_slice( array_values( $steps ), 0, 4 );

// Keep only buttons that have text.
$buttons = array_filter(
	$buttons,
	function ( $button ) {
		return ! empty( $button['text'] );
	}
);
$buttons = array_slice( array_values( $buttons ), 0, 2 );

$allowed_modifiers = function_exists( 'chw_masthead_button_modifiers' ) ? chw_masthead_button_modifiers() : array( '', 'arrow', 'phone' );

// Global legal text.
$legal_text = get_theme_mod( 'legal_text', '' );

// Legacy headingAccent is kept in saved block data from the old dual-field editor.
// Skip rendering it when the same text already lives in the single heading field.
$heading_plain      = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $heading ) ) );
$accent_plain       = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $heading_accent ) ) );
$show_legacy_accent = $heading_accent && ( '' === $heading || false === stripos( $heading_plain, $accent_plain ) );

// Nothing meaningful to render.
if ( empty( $steps ) && empty( $heading ) && ! $show_legacy_accent && empty( $description ) ) {
	return;
}

$wrapper_attributes = isset( $block_wrapper_attributes ) ? $block_wrapper_attributes : '';
$step_count         = count( $steps );
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>

	<div class="_container">

		<div class="steps-section__header _text -align-center">
			<?php if ( $heading || $show_legacy_accent ) : ?>
				<h2 class="steps-section__heading _text -tertiary">
					<?php if ( $heading ) : ?>
						<?php echo wp_kses( $heading, $heading_allowed ); ?>
					<?php endif; ?>
					<?php if ( $show_legacy_accent ) : ?>
						<span class="accent"><?php echo wp_kses( $heading_accent, $inline_allowed ); ?></span>
					<?php endif; ?>
				</h2>
			<?php endif; ?>

			<?php if ( $description ) : ?>
				<p class="steps-section__intro _text -muted _text-size -lg"><?php echo wp_kses( $description, $inline_allowed ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $steps ) ) : ?>
			<div class="steps-section__steps" data-count="<?php echo esc_attr( $step_count ); ?>">
				<?php foreach ( $steps as $index => $step ) : ?>
					<div class="steps-section__step _text -align-center">
						<div class="steps-section__badge">
							<span class="steps-section__icon">
								<?php
								if ( ! empty( $step['icon'] ) && function_exists( 'chw_sanitize_inline_svg' ) ) {
									echo chw_sanitize_inline_svg( $step['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput
								}
								?>
							</span>
							<span class="steps-section__number"><?php echo esc_html( $index + 1 ); ?></span>
						</div>
						<?php if ( ! empty( $step['title'] ) ) : ?>
							<h3 class="steps-section__step-title _text -tertiary"><?php echo wp_kses( $step['title'], $inline_allowed ); ?></h3>
						<?php endif; ?>
						<?php if ( ! empty( $step['description'] ) ) : ?>
							<p class="steps-section__step-text _text -muted"><?php echo wp_kses( $step['description'], $inline_allowed ); ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $buttons ) ) : ?>
			<div class="steps-section__buttons _flex -align-center -justify-center">
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
			<div class="steps-section__legal _text -align-center _text -muted"><?php echo wp_kses_post( $legal_text ); ?></div>
		<?php endif; ?>

	</div>
</section>

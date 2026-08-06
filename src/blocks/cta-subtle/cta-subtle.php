<?php
/**
 * Frontend render for the Call To Action: Subtle block.
 *
 * Expects $attributes (and optionally $block_wrapper_attributes) provided by the
 * render callback in register-cta-subtle-block.php.
 *
 * @var array  $attributes
 * @var string $block_wrapper_attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();

$heading         = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$description     = isset( $attributes['description'] ) ? $attributes['description'] : '';
$button_text     = isset( $attributes['buttonText'] ) ? $attributes['buttonText'] : '';
$button_url      = isset( $attributes['buttonUrl'] ) ? $attributes['buttonUrl'] : '';
$button_modifier = isset( $attributes['buttonModifier'] ) ? (string) $attributes['buttonModifier'] : '';
$button_new_tab  = ! empty( $attributes['buttonNewTab'] );

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

$allowed_modifiers = function_exists( 'chw_masthead_button_modifiers' ) ? chw_masthead_button_modifiers() : array( '', 'arrow', 'phone', 'download' );
if ( ! in_array( $button_modifier, $allowed_modifiers, true ) ) {
	$button_modifier = '';
}

// Nothing meaningful to render.
if ( empty( $heading ) && empty( $description ) && empty( $button_text ) ) {
	return;
}

$wrapper_attributes = isset( $block_wrapper_attributes ) ? $block_wrapper_attributes : '';
?>

<div class="_container">
	<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<div class="cta-subtle__card">
			<div class="cta-subtle__main">
				<?php if ( $heading ) : ?>
					<h6 class="cta-subtle__heading"><?php echo wp_kses( $heading, $heading_allowed ); ?></h6>
				<?php endif; ?>

				<?php if ( $description ) : ?>
					<p class="cta-subtle__description"><?php echo wp_kses( $description, $inline_allowed ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( $button_text ) : ?>
				<div class="cta-subtle__button">
					<?php
					$btn_class = '_button -primary';
					if ( $button_modifier ) {
						$btn_class .= ' -' . sanitize_html_class( $button_modifier );
					}
					?>
					<a
						class="<?php echo esc_attr( $btn_class ); ?>"
						href="<?php echo esc_url( $button_url ? $button_url : '#' ); ?>"
						<?php if ( $button_new_tab ) { ?>target="_blank" rel="noopener"<?php } ?>>
						<?php echo esc_html( $button_text ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</section>
</div>
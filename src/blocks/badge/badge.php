<?php
/**
 * Frontend render for the Badge block.
 *
 * Expects $attributes-derived variables from chw_render_badge_block().
 *
 * @var string $block_wrapper_attributes
 * @var string $eyebrow_classes
 * @var string $icon_classes
 * @var string $inline_styles
 * @var string $text
 * @var string $icon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div <?php echo $block_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<span
		class="<?php echo esc_attr( $eyebrow_classes ); ?>"
		<?php if ( $inline_styles ) : ?>
			style="<?php echo esc_attr( $inline_styles ); ?>"
		<?php endif; ?>>

		<?php if ( $icon && function_exists( 'chw_sanitize_inline_svg' ) ) : ?>
			<span class="<?php echo esc_attr( $icon_classes ); ?>">
				<?php echo chw_sanitize_inline_svg( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</span>
		<?php endif; ?>

		<?php if ( $text ) : ?>
			<span class="_eyebrow__label"><?php echo esc_html( $text ); ?></span>
		<?php endif; ?>

	</span>
</div>

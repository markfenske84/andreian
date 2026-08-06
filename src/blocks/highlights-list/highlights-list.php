<?php
/**
 * Frontend render for the Highlights List block.
 *
 * Expects $attributes and $block_wrapper_attributes from chw_render_highlights_list_block().
 *
 * @var array  $attributes
 * @var string $block_wrapper_attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow_icon = isset( $attributes['eyebrowIcon'] ) ? $attributes['eyebrowIcon'] : '';
$eyebrow_text = isset( $attributes['eyebrowText'] ) ? $attributes['eyebrowText'] : '';
$items        = isset( $attributes['items'] ) && is_array( $attributes['items'] ) ? $attributes['items'] : array();

$inline_allowed = array(
	'span'   => array( 'class' => array() ),
	'strong' => array(),
	'em'     => array(),
	'br'     => array(),
);

$items = array_filter(
	$items,
	function ( $item ) {
		return ! empty( $item['description'] ) || ! empty( $item['value'] );
	}
);
$items = array_values( $items );

if ( empty( $items ) && empty( $eyebrow_icon ) && empty( $eyebrow_text ) ) {
	return;
}
?>

<div <?php echo $block_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<?php if ( $eyebrow_icon || $eyebrow_text ) : ?>
		<header class="highlights-list__header">
			<span class="_eyebrow -plain _text -secondary highlights-list__eyebrow">
				<?php if ( $eyebrow_icon && function_exists( 'chw_sanitize_inline_svg' ) ) : ?>
					<span class="_eyebrow__icon" aria-hidden="true"><?php echo chw_sanitize_inline_svg( $eyebrow_icon ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				<?php endif; ?>
				<?php if ( $eyebrow_text ) : ?>
					<span class="_eyebrow__label"><?php echo wp_kses( $eyebrow_text, $inline_allowed ); ?></span>
				<?php endif; ?>
			</span>
		</header>
	<?php endif; ?>

	<?php if ( ! empty( $items ) ) : ?>
		<div class="highlights-list__items">
			<?php foreach ( $items as $item ) : ?>
				<div class="highlights-list__item">
					<?php if ( ! empty( $item['description'] ) ) : ?>
						<div class="highlights-list__description _text -muted"><?php echo wp_kses( $item['description'], $inline_allowed ); ?></div>
					<?php endif; ?>
					<?php if ( ! empty( $item['value'] ) ) : ?>
						<div class="highlights-list__value _text -tertiary _text-size -xl"><?php echo wp_kses( $item['value'], $inline_allowed ); ?></div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>

<?php
/**
 * Frontend render for the Quick Actions block.
 *
 * Expects $attributes (and optionally $block_wrapper_attributes) provided by the
 * render callback in register-quick-actions-block.php.
 *
 * @var array  $attributes
 * @var string $block_wrapper_attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();

$eyelash_text = isset( $attributes['eyelashText'] ) ? $attributes['eyelashText'] : '';
$links        = isset( $attributes['links'] ) && is_array( $attributes['links'] ) ? $attributes['links'] : array();

$inline_allowed = array(
	'span'   => array( 'class' => array() ),
	'strong' => array(),
	'em'     => array(),
	'br'     => array(),
);

$allowed_arrows = chw_quick_actions_arrow_directions();

// Keep only links that have a label or URL.
$links = array_filter(
	$links,
	function ( $link ) {
		return ! empty( $link['label'] ) || ! empty( $link['url'] );
	}
);
$links = array_values( $links );

if ( empty( $links ) && empty( $eyelash_text ) ) {
	return;
}

$wrapper_attributes = isset( $block_wrapper_attributes ) ? $block_wrapper_attributes : 'class="quick-actions"';
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="_container">
		<?php if ( $eyelash_text ) : ?>
			<span class="_eyebrow -plain quick-actions__eyebrow"><?php echo wp_kses( $eyelash_text, $inline_allowed ); ?></span>
		<?php endif; ?>

		<?php if ( ! empty( $links ) ) : ?>
			<div class="quick-actions__grid" data-count="<?php echo esc_attr( count( $links ) ); ?>">
				<?php foreach ( $links as $link ) : ?>
					<?php
					$link_label   = isset( $link['label'] ) ? $link['label'] : '';
					$link_url     = isset( $link['url'] ) ? $link['url'] : '';
					$link_blank   = ! empty( $link['new_tab'] );
					$arrow_dir    = isset( $link['arrow_direction'] ) ? $link['arrow_direction'] : 'right';

					if ( ! in_array( $arrow_dir, $allowed_arrows, true ) ) {
						$arrow_dir = 'right';
					}

					$arrow_class = 'quick-actions__arrow -' . sanitize_html_class( $arrow_dir );
					?>
					<a
						class="quick-actions__card"
						href="<?php echo esc_url( $link_url ? $link_url : '#' ); ?>"
						<?php if ( $link_blank ) { ?>target="_blank" rel="noopener"<?php } ?>>
						<?php if ( ! empty( $link['icon'] ) && function_exists( 'chw_sanitize_inline_svg' ) ) : ?>
							<span class="quick-actions__icon" aria-hidden="true">
								<?php echo chw_sanitize_inline_svg( $link['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
							</span>
						<?php endif; ?>
						<?php if ( $link_label ) : ?>
							<span class="quick-actions__label"><?php echo esc_html( $link_label ); ?></span>
						<?php endif; ?>
						<span class="<?php echo esc_attr( $arrow_class ); ?>" aria-hidden="true">
							<?php echo chw_quick_actions_arrow_icon( $arrow_dir ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
/**
 * Frontend render for the CHW Buttons block.
 *
 * Expects $attributes and $block_wrapper_attributes from chw_render_chw_buttons_block().
 *
 * @var array  $attributes
 * @var string $block_wrapper_attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$buttons = isset( $attributes['buttons'] ) && is_array( $attributes['buttons'] ) ? $attributes['buttons'] : array();

$buttons = array_filter(
	$buttons,
	function ( $button ) {
		return ! empty( $button['text'] );
	}
);
$buttons = array_values( $buttons );

if ( empty( $buttons ) ) {
	return;
}
?>

<div <?php echo $block_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="chw-buttons__inner _flex -align-center -justify-center">
		<?php foreach ( $buttons as $button ) : ?>
			<?php
			$style    = isset( $button['style'] ) ? (string) $button['style'] : 'primary';
			$modifier = isset( $button['modifier'] ) ? (string) $button['modifier'] : '';
			$btn_class = chw_button_classes_from_style( $style, $modifier );
			$btn_text  = isset( $button['text'] ) ? $button['text'] : '';
			$btn_url   = isset( $button['url'] ) ? $button['url'] : '#';
			$btn_blank = ! empty( $button['new_tab'] );
			?>
			<a
				class="<?php echo esc_attr( $btn_class ); ?>"
				href="<?php echo esc_url( $btn_url ? $btn_url : '#' ); ?>"
				<?php if ( $btn_blank ) { ?>target="_blank" rel="noopener noreferrer"<?php } ?>>
				<?php echo esc_html( $btn_text ); ?>
			</a>
		<?php endforeach; ?>
	</div>
</div>

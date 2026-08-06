<?php
/**
 * Frontend render for the CHW Legal block.
 *
 * Expects $block_wrapper_attributes and $legal_text from chw_render_chw_legal_block().
 *
 * @var string $block_wrapper_attributes
 * @var string $legal_text
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div <?php echo $block_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="chw-legal__content"><?php echo wp_kses_post( $legal_text ); ?></div>
</div>

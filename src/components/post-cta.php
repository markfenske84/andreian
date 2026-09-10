<?php
/**
 * ConvertKit CTA for single posts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_singular( 'post' ) ) {
	return;
}
?>

<div class="post-cta">
	<?php echo do_shortcode( '[convertkit form=9684991]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ConvertKit plugin output. ?>
</div>

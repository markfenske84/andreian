<?php
/**
 * Frontend render for the Fullwidth Cover Section block.
 *
 * @var array  $attributes Provided by the render callback.
 * @var string $content    InnerBlocks rendered content.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();
$content    = isset( $content ) ? $content : '';

$background_type  = ! empty( $attributes['backgroundType'] ) ? $attributes['backgroundType'] : 'color';
$background_color = isset( $attributes['backgroundColor'] ) ? $attributes['backgroundColor'] : '';
$image_url        = isset( $attributes['imageUrl'] ) ? $attributes['imageUrl'] : '';

$styles = array();

if ( 'image' === $background_type && $image_url ) {
	$styles[] = 'background-image: url(' . esc_url( $image_url ) . ')';
} elseif ( $background_color ) {
	$styles[] = 'background-color: ' . $background_color;
}

$style_attr = $styles ? ' style="' . esc_attr( implode( '; ', $styles ) ) . ';"' : '';
?>

<section class="fullwidth-cover-section"<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput - style values escaped above. ?>>
	<div class="_inner _gutter">
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput - inner blocks render their own escaped output. ?>
	</div>
</section>

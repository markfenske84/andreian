<?php
/**
 * Frontend render for the Fullwidth Halfscreen block.
 *
 * @var array  $attributes Provided by the render callback.
 * @var string $content    InnerBlocks rendered content.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();
$content    = isset( $content ) ? $content : '';

$orientation       = ! empty( $attributes['orientation'] ) ? $attributes['orientation'] : '-media-content';
$media_type        = ! empty( $attributes['mediaType'] ) ? $attributes['mediaType'] : 'image';
$image_url         = isset( $attributes['imageUrl'] ) ? $attributes['imageUrl'] : '';
$image_alt         = isset( $attributes['imageAlt'] ) ? $attributes['imageAlt'] : '';
$video_source      = ! empty( $attributes['videoSource'] ) ? $attributes['videoSource'] : 'self';
$video_file_url    = isset( $attributes['videoFileUrl'] ) ? $attributes['videoFileUrl'] : '';
$video_url         = isset( $attributes['videoUrl'] ) ? $attributes['videoUrl'] : '';
$media_overlay     = isset( $attributes['mediaOverlay'] ) ? $attributes['mediaOverlay'] : '';
$content_background = isset( $attributes['contentBackground'] ) ? $attributes['contentBackground'] : '';
?>

<section class="fullwidth-halfscreen <?php echo esc_attr( $orientation ); ?> _flex">

	<div class="_media">

		<?php if ( $media_overlay ) : ?>
			<span class="_overlay" style="background-color: <?php echo esc_attr( $media_overlay ); ?>"></span>
		<?php endif; ?>

		<?php if ( 'image' === $media_type ) : ?>

			<?php if ( $image_url ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
			<?php endif; ?>

		<?php elseif ( 'video' === $media_type ) : ?>

			<?php if ( 'self' === $video_source && $video_file_url ) : ?>
				<video autoplay loop muted playsinline>
					<source src="<?php echo esc_url( $video_file_url ); ?>" type="video/mp4">
				</video>
			<?php elseif ( 'youtube' === $video_source && $video_url ) : ?>
				<iframe
					src="<?php echo esc_url( $video_url ); ?>&controls=0&showinfo=0&rel=0&autoplay=1&loop=1&modestbranding=1&disablekb=1&mute=1"
					frameborder="0"
					webkitallowfullscreen
					mozallowfullscreen
					allowfullscreen></iframe>
			<?php elseif ( 'vimeo' === $video_source && $video_url ) : ?>
				<iframe
					src="<?php echo esc_url( $video_url ); ?>?background=1&autoplay=1&loop=1&byline=0&title=0&rel=0"
					frameborder="0"
					allow="autoplay; fullscreen"
					webkitallowfullscreen
					mozallowfullscreen
					allowfullscreen></iframe>
			<?php endif; ?>

		<?php endif; ?>

	</div>

	<div
		class="_content _flex -justify-center -align-center"
		<?php if ( $content_background ) : ?>style="background-color: <?php echo esc_attr( $content_background ); ?>;"<?php endif; ?>>

		<div class="_inner _gutter">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput - inner blocks render their own escaped output. ?>
		</div>

	</div>

</section>

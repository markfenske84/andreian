<?php
/**
 * Frontend render for the Video Modal block.
 *
 * @var array $attributes Provided by the render callback in register-video-modal-block.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();

$video_source     = ! empty( $attributes['videoSource'] ) ? $attributes['videoSource'] : 'third-party';
$video_url        = isset( $attributes['videoUrl'] ) ? $attributes['videoUrl'] : '';
$video_file_url   = isset( $attributes['videoFileUrl'] ) ? $attributes['videoFileUrl'] : '';
$thumbnail_url    = isset( $attributes['thumbnailUrl'] ) ? $attributes['thumbnailUrl'] : '';
$thumbnail_alt    = isset( $attributes['thumbnailAlt'] ) ? $attributes['thumbnailAlt'] : '';
$modal_background = isset( $attributes['modalBackground'] ) && '' !== $attributes['modalBackground'] ? $attributes['modalBackground'] : 'rgba(0,0,0,0.8)';

$is_self_hosted = ( 'self-hosted' === $video_source );
$vid_url        = $is_self_hosted ? $video_file_url : $video_url;

// Nothing to play.
if ( empty( $vid_url ) ) {
	return;
}

$play_label = $thumbnail_alt
	? sprintf( __( 'Play video: %s', 'chw' ), $thumbnail_alt )
	: __( 'Play video', 'chw' );
?>

<button
	type="button"
	class="video-modal-trigger"
	aria-label="<?php echo esc_attr( $play_label ); ?>"
	data-vid-url="<?php echo esc_url( $vid_url ); ?>">
	<i class="fa-solid fa-circle-play" aria-hidden="true"></i>
	<div class="thumbnail">
		<?php if ( $thumbnail_url ) : ?>
		<img
			src="<?php echo esc_url( $thumbnail_url ); ?>"
			alt=""
			aria-hidden="true">
		<?php endif; ?>
	</div>
</button>

<div
	class="video-modal"
	style="background-color: <?php echo esc_attr( $modal_background ); ?>;"
	role="dialog" aria-label="<?php esc_attr_e( 'Video player modal', 'chw' ); ?>">

	<?php get_template_part( 'src/components/close-button' ); ?>

	<div class="_inner">
		<?php if ( $is_self_hosted ) : ?>
			<video controls>
				<source src="">
			</video>
		<?php else : ?>
			<iframe src="" frameborder="0" allow="autoplay; fullscreen"></iframe>
		<?php endif; ?>
	</div>

</div>

<?php
/**
 * Featured image focal point for posts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANDREIAN_FEATURED_FOCAL_META', '_andreian_featured_focal_point' );

/**
 * Default focal point: top center.
 *
 * @return array{x: float, y: float}
 */
function andreian_get_default_featured_focal_point() {
	return array(
		'x' => 0.5,
		'y' => 0.0,
	);
}

/**
 * Whether a focal point matches the theme default.
 *
 * @param array{x?: mixed, y?: mixed} $focal Focal point.
 * @return bool
 */
function andreian_featured_focal_point_is_default( $focal ) {
	$default = andreian_get_default_featured_focal_point();

	return abs( (float) ( $focal['x'] ?? $default['x'] ) - $default['x'] ) < 0.001
		&& abs( (float) ( $focal['y'] ?? $default['y'] ) - $default['y'] ) < 0.001;
}

/**
 * Sanitize a 0–1 focal point coordinate.
 *
 * @param mixed $value Raw value.
 * @return float
 */
function andreian_sanitize_focal_coordinate( $value ) {
	return max( 0, min( 1, round( (float) $value, 4 ) ) );
}

/**
 * Get a post's featured-image focal point.
 *
 * @param int|null $post_id Post ID.
 * @return array{x: float, y: float}
 */
function andreian_get_featured_image_focal_point( $post_id = null ) {
	$default = andreian_get_default_featured_focal_point();
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();

	if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
		return $default;
	}

	$stored = get_post_meta( $post_id, ANDREIAN_FEATURED_FOCAL_META, true );

	if ( ! is_array( $stored ) || ! isset( $stored['x'], $stored['y'] ) ) {
		return $default;
	}

	return array(
		'x' => andreian_sanitize_focal_coordinate( $stored['x'] ),
		'y' => andreian_sanitize_focal_coordinate( $stored['y'] ),
	);
}

/**
 * CSS object-position value for a focal point.
 *
 * @param array{x?: mixed, y?: mixed}|null $focal Focal point.
 * @return string
 */
function andreian_featured_image_object_position( $focal = null ) {
	$focal = is_array( $focal ) ? $focal : andreian_get_featured_image_focal_point();

	return sprintf(
		'%s%% %s%%',
		rtrim( rtrim( sprintf( '%.2F', andreian_sanitize_focal_coordinate( $focal['x'] ?? 0.5 ) * 100 ), '0' ), '.' ),
		rtrim( rtrim( sprintf( '%.2F', andreian_sanitize_focal_coordinate( $focal['y'] ?? 0 ) * 100 ), '0' ), '.' )
	);
}

/**
 * Append a focal point picker to the Featured Image metabox.
 *
 * @param string $content      Metabox HTML.
 * @param int    $post_id      Post ID.
 * @param int    $thumbnail_id Attachment ID.
 * @return string
 */
function andreian_featured_image_focal_point_html( $content, $post_id, $thumbnail_id ) {
	if ( 'post' !== get_post_type( $post_id ) || ! $thumbnail_id ) {
		return $content;
	}

	$focal = andreian_get_featured_image_focal_point( $post_id );
	$image = wp_get_attachment_image_url( $thumbnail_id, 'medium_large' );

	if ( ! $image ) {
		return $content;
	}

	ob_start();
	wp_nonce_field( 'andreian_save_featured_focal_point', 'andreian_featured_focal_nonce' );
	?>
	<div class="andreian-focal-point">
		<p class="post-attributes-label-wrapper">
			<label class="post-attributes-label"><?php esc_html_e( 'Focal point', 'andreian' ); ?></label>
		</p>
		<p class="description"><?php esc_html_e( 'Drag the point to choose which part of the image stays in view. Default is top center.', 'andreian' ); ?></p>
		<div class="andreian-focal-point__picker" data-andreian-focal-picker>
			<img src="<?php echo esc_url( $image ); ?>" alt="" />
			<button
				type="button"
				class="andreian-focal-point__handle"
				style="left: <?php echo esc_attr( (string) ( $focal['x'] * 100 ) ); ?>%; top: <?php echo esc_attr( (string) ( $focal['y'] * 100 ) ); ?>%;"
				aria-label="<?php esc_attr_e( 'Featured image focal point', 'andreian' ); ?>"
			></button>
		</div>
		<input type="hidden" name="andreian_featured_focal_x" value="<?php echo esc_attr( (string) $focal['x'] ); ?>" />
		<input type="hidden" name="andreian_featured_focal_y" value="<?php echo esc_attr( (string) $focal['y'] ); ?>" />
		<p>
			<button type="button" class="button-link andreian-focal-point__reset">
				<?php esc_html_e( 'Reset to top center', 'andreian' ); ?>
			</button>
		</p>
	</div>
	<?php

	return $content . ob_get_clean();
}
add_filter( 'admin_post_thumbnail_html', 'andreian_featured_image_focal_point_html', 10, 3 );

/**
 * Save the featured-image focal point.
 *
 * @param int $post_id Post ID.
 */
function andreian_save_featured_image_focal_point( $post_id ) {
	if (
		! isset( $_POST['andreian_featured_focal_nonce'] )
		|| ! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST['andreian_featured_focal_nonce'] ) ),
			'andreian_save_featured_focal_point'
		)
	) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) || 'post' !== get_post_type( $post_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ! isset( $_POST['andreian_featured_focal_x'], $_POST['andreian_featured_focal_y'] ) ) {
		return;
	}

	$focal = array(
		'x' => andreian_sanitize_focal_coordinate( wp_unslash( $_POST['andreian_featured_focal_x'] ) ),
		'y' => andreian_sanitize_focal_coordinate( wp_unslash( $_POST['andreian_featured_focal_y'] ) ),
	);

	if ( andreian_featured_focal_point_is_default( $focal ) ) {
		delete_post_meta( $post_id, ANDREIAN_FEATURED_FOCAL_META );
		return;
	}

	update_post_meta( $post_id, ANDREIAN_FEATURED_FOCAL_META, $focal );
}
add_action( 'save_post_post', 'andreian_save_featured_image_focal_point' );

/**
 * Enqueue the focal point picker on post edit screens.
 *
 * @param string $hook_suffix Current admin page.
 */
function andreian_enqueue_featured_image_position_admin( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = get_current_screen();

	if ( ! $screen || 'post' !== $screen->post_type ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/admin/featured-image-position.js';

	wp_enqueue_script(
		'andreian-featured-image-position',
		get_template_directory_uri() . '/src/js/admin/featured-image-position.js',
		array( 'jquery' ),
		andreian_asset_version( $script_path ),
		true
	);
}
add_action( 'admin_enqueue_scripts', 'andreian_enqueue_featured_image_position_admin' );

<?php
/**
 * Short Description metadata for posts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get a post's Short Description.
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function andreian_get_short_description( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
		return '';
	}

	return (string) get_post_meta( $post_id, '_andreian_short_description', true );
}

/**
 * Register the Short Description metabox and remove the core Excerpt box.
 */
function andreian_register_short_description_metabox() {
	remove_meta_box( 'postexcerpt', 'post', 'normal' );

	add_meta_box(
		'andreian-short-description',
		__( 'Short Description', 'andreian' ),
		'andreian_render_short_description_metabox',
		'post',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_post', 'andreian_register_short_description_metabox' );

/**
 * Render the Short Description metabox.
 *
 * @param WP_Post $post Current post.
 */
function andreian_render_short_description_metabox( $post ) {
	wp_nonce_field( 'andreian_save_short_description', 'andreian_short_description_nonce' );
	?>
	<p>
		<label class="screen-reader-text" for="andreian-short-description-field">
			<?php esc_html_e( 'Short Description', 'andreian' ); ?>
		</label>
		<textarea
			id="andreian-short-description-field"
			name="andreian_short_description"
			class="widefat"
			rows="4"
			placeholder="<?php esc_attr_e( 'A concise introduction shown beneath the post headline and in post previews.', 'andreian' ); ?>"
		><?php echo esc_textarea( andreian_get_short_description( $post->ID ) ); ?></textarea>
	</p>
	<?php
}

/**
 * Save the Short Description.
 *
 * @param int $post_id Post ID.
 */
function andreian_save_short_description( $post_id ) {
	if (
		! isset( $_POST['andreian_short_description_nonce'] )
		|| ! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST['andreian_short_description_nonce'] ) ),
			'andreian_save_short_description'
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

	$description = isset( $_POST['andreian_short_description'] )
		? sanitize_textarea_field( wp_unslash( $_POST['andreian_short_description'] ) )
		: '';

	if ( $description ) {
		update_post_meta( $post_id, '_andreian_short_description', $description );
	} else {
		delete_post_meta( $post_id, '_andreian_short_description' );
	}
}
add_action( 'save_post_post', 'andreian_save_short_description' );

/**
 * Prefer the Short Description anywhere WordPress requests a post excerpt.
 *
 * @param string       $excerpt Existing excerpt.
 * @param WP_Post|null $post    Post object.
 * @return string
 */
function andreian_filter_post_excerpt( $excerpt, $post = null ) {
	$post = get_post( $post );

	if ( ! $post || 'post' !== $post->post_type ) {
		return $excerpt;
	}

	$description = andreian_get_short_description( $post->ID );

	return $description ? $description : $excerpt;
}
add_filter( 'get_the_excerpt', 'andreian_filter_post_excerpt', 10, 2 );

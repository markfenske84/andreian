<?php
/**
 * User profile customizations.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * EEAT author title field on user profiles.
 *
 * @param WP_User $user User object.
 */
function andreian_author_profile_fields( $user ) {
	?>
	<h2><?php esc_html_e( 'Author Bio (EEAT)', 'andreian' ); ?></h2>
	<p class="description">
		<?php esc_html_e( 'Shown at the bottom of blog posts written by this author. Use the Biographical Info field above for the main bio text.', 'andreian' ); ?>
	</p>
	<table class="form-table" role="presentation">
		<tr>
			<th><label for="andreian_author_title"><?php esc_html_e( 'Professional Title', 'andreian' ); ?></label></th>
			<td>
				<input
					type="text"
					name="andreian_author_title"
					id="andreian_author_title"
					class="regular-text"
					value="<?php echo esc_attr( get_the_author_meta( 'andreian_author_title', $user->ID ) ); ?>" />
				<p class="description">
					<?php esc_html_e( 'Credentials or role shown under the author name (e.g. Home Warranty Expert).', 'andreian' ); ?>
				</p>
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'andreian_author_profile_fields' );
add_action( 'edit_user_profile', 'andreian_author_profile_fields' );

/**
 * Save EEAT author profile fields.
 *
 * @param int $user_id User ID.
 */
function andreian_save_author_profile_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}

	if ( isset( $_POST['andreian_author_title'] ) ) {
		update_user_meta( $user_id, 'andreian_author_title', sanitize_text_field( wp_unslash( $_POST['andreian_author_title'] ) ) );
	}
}
add_action( 'personal_options_update', 'andreian_save_author_profile_fields' );
add_action( 'edit_user_profile_update', 'andreian_save_author_profile_fields' );

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
function chw_author_profile_fields( $user ) {
	?>
	<h2><?php esc_html_e( 'Author Bio (EEAT)', 'chw' ); ?></h2>
	<p class="description">
		<?php esc_html_e( 'Shown at the bottom of blog posts written by this author. Use the Biographical Info field above for the main bio text.', 'chw' ); ?>
	</p>
	<table class="form-table" role="presentation">
		<tr>
			<th><label for="chw_author_title"><?php esc_html_e( 'Professional Title', 'chw' ); ?></label></th>
			<td>
				<input
					type="text"
					name="chw_author_title"
					id="chw_author_title"
					class="regular-text"
					value="<?php echo esc_attr( get_the_author_meta( 'chw_author_title', $user->ID ) ); ?>" />
				<p class="description">
					<?php esc_html_e( 'Credentials or role shown under the author name (e.g. Home Warranty Expert).', 'chw' ); ?>
				</p>
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'chw_author_profile_fields' );
add_action( 'edit_user_profile', 'chw_author_profile_fields' );

/**
 * Save EEAT author profile fields.
 *
 * @param int $user_id User ID.
 */
function chw_save_author_profile_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}

	if ( isset( $_POST['chw_author_title'] ) ) {
		update_user_meta( $user_id, 'chw_author_title', sanitize_text_field( wp_unslash( $_POST['chw_author_title'] ) ) );
	}
}
add_action( 'personal_options_update', 'chw_save_author_profile_fields' );
add_action( 'edit_user_profile_update', 'chw_save_author_profile_fields' );

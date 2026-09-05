<?php
/**
 * Homepage featured post selection.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANDREIAN_FEATURED_HOMEPAGE_META', '_andreian_featured_homepage' );
define( 'ANDREIAN_FEATURED_HOMEPAGE_ORDER_META', '_andreian_featured_order' );
define( 'ANDREIAN_FEATURED_HOMEPAGE_SLOTS', 8 );

/**
 * Register featured homepage metabox.
 */
function andreian_featured_homepage_metabox() {
	add_meta_box(
		'andreian-featured-homepage',
		__( 'Homepage Featured', 'andreian' ),
		'andreian_featured_homepage_metabox_render',
		'post',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'andreian_featured_homepage_metabox' );

/**
 * Render featured homepage metabox.
 *
 * @param WP_Post $post Current post.
 */
function andreian_featured_homepage_metabox_render( $post ) {
	wp_nonce_field( 'andreian_featured_homepage', 'andreian_featured_homepage_nonce' );

	$is_featured = '1' === get_post_meta( $post->ID, ANDREIAN_FEATURED_HOMEPAGE_META, true );
	$order       = (int) get_post_meta( $post->ID, ANDREIAN_FEATURED_HOMEPAGE_ORDER_META, true );
	$count       = count( andreian_get_featured_homepage_ids() );
	?>
	<p>
		<label>
			<input type="checkbox" name="andreian_featured_homepage" value="1" <?php checked( $is_featured ); ?> />
			<?php esc_html_e( 'Feature on homepage', 'andreian' ); ?>
		</label>
	</p>
	<p>
		<label for="andreian_featured_order">
			<?php esc_html_e( 'Display order', 'andreian' ); ?>
		</label>
		<input
			type="number"
			id="andreian_featured_order"
			name="andreian_featured_order"
			value="<?php echo esc_attr( $order > 0 ? (string) $order : '' ); ?>"
			min="1"
			max="<?php echo esc_attr( (string) ANDREIAN_FEATURED_HOMEPAGE_SLOTS ); ?>"
			class="small-text" />
	</p>
	<p class="description">
		<?php
		printf(
			/* translators: 1: current featured count, 2: max slots. */
			esc_html__( '%1$d of %2$d featured slots in use.', 'andreian' ),
			$count,
			ANDREIAN_FEATURED_HOMEPAGE_SLOTS
		);
		?>
	</p>
	<?php
}

/**
 * Save featured homepage metabox.
 *
 * @param int $post_id Post ID.
 */
function andreian_featured_homepage_save( $post_id ) {
	if ( ! isset( $_POST['andreian_featured_homepage_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['andreian_featured_homepage_nonce'] ) ), 'andreian_featured_homepage' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$is_featured = ! empty( $_POST['andreian_featured_homepage'] );

	if ( $is_featured ) {
		update_post_meta( $post_id, ANDREIAN_FEATURED_HOMEPAGE_META, '1' );

		$order = isset( $_POST['andreian_featured_order'] ) ? (int) $_POST['andreian_featured_order'] : 0;
		if ( $order > 0 ) {
			update_post_meta( $post_id, ANDREIAN_FEATURED_HOMEPAGE_ORDER_META, $order );
		} else {
			delete_post_meta( $post_id, ANDREIAN_FEATURED_HOMEPAGE_ORDER_META );
		}
	} else {
		delete_post_meta( $post_id, ANDREIAN_FEATURED_HOMEPAGE_META );
		delete_post_meta( $post_id, ANDREIAN_FEATURED_HOMEPAGE_ORDER_META );
	}
}
add_action( 'save_post_post', 'andreian_featured_homepage_save' );

/**
 * Get IDs of posts marked featured for the homepage hero.
 *
 * @return int[]
 */
function andreian_get_featured_homepage_ids() {
	static $ids = null;

	if ( null !== $ids ) {
		return $ids;
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => 50,
			'meta_key'               => ANDREIAN_FEATURED_HOMEPAGE_META,
			'meta_value'             => '1',
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
		)
	);

	$post_ids = $query->posts;

	usort(
		$post_ids,
		static function ( $first_id, $second_id ) {
			$first_order  = (int) get_post_meta( $first_id, ANDREIAN_FEATURED_HOMEPAGE_ORDER_META, true );
			$second_order = (int) get_post_meta( $second_id, ANDREIAN_FEATURED_HOMEPAGE_ORDER_META, true );
			$first_rank   = $first_order > 0 ? $first_order : 999;
			$second_rank  = $second_order > 0 ? $second_order : 999;

			if ( $first_rank !== $second_rank ) {
				return $first_rank <=> $second_rank;
			}

			return get_post_time( 'U', true, $second_id ) <=> get_post_time( 'U', true, $first_id );
		}
	);

	$ids = array_slice( array_map( 'absint', $post_ids ), 0, ANDREIAN_FEATURED_HOMEPAGE_SLOTS );

	return $ids;
}

/**
 * Build hero-tiles post IDs: featured first, backfill with latest.
 *
 * @param int   $limit        Number of posts.
 * @param int[] $exclude_ids  Additional IDs to exclude from backfill.
 * @return int[]
 */
function andreian_get_hero_tiles_post_ids( $limit = 8, $exclude_ids = array() ) {
	$featured_ids = andreian_get_featured_homepage_ids();
	$post_ids     = array_slice( $featured_ids, 0, $limit );

	if ( count( $post_ids ) >= $limit ) {
		return $post_ids;
	}

	$backfill = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => $limit - count( $post_ids ),
			'post__not_in'           => array_merge( $post_ids, array_map( 'absint', $exclude_ids ) ),
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
		)
	);

	return array_merge( $post_ids, $backfill->posts );
}

/**
 * Add featured column to posts list.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function andreian_featured_homepage_column( $columns ) {
	$columns['andreian_featured'] = __( 'Featured', 'andreian' );

	return $columns;
}
add_filter( 'manage_post_posts_columns', 'andreian_featured_homepage_column' );

/**
 * Render featured column content.
 *
 * @param string $column  Column name.
 * @param int    $post_id Post ID.
 */
function andreian_featured_homepage_column_content( $column, $post_id ) {
	if ( 'andreian_featured' !== $column ) {
		return;
	}

	if ( '1' === get_post_meta( $post_id, ANDREIAN_FEATURED_HOMEPAGE_META, true ) ) {
		echo esc_html( '★' );
	}
}
add_action( 'manage_post_posts_custom_column', 'andreian_featured_homepage_column_content', 10, 2 );

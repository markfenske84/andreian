<?php
/**
 * Recommended posts for single post views.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id    = get_the_ID();
$categories = get_the_category( $post_id );
$cat_id     = ! empty( $categories ) ? (int) $categories[0]->term_id : 0;

if ( ! $cat_id ) {
	return;
}

$query = new WP_Query(
	array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => 4,
		'post__not_in'           => array( $post_id ),
		'cat'                    => $cat_id,
		'orderby'                => 'date',
		'order'                  => 'DESC',
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	)
);

if ( ! $query->have_posts() ) {
	return;
}
?>

<section class="recommended-posts" aria-label="<?php esc_attr_e( 'Recommended posts', 'andreian' ); ?>">
	<h2 class="recommended-posts__title"><?php esc_html_e( 'Recommended', 'andreian' ); ?></h2>
	<div class="recommended-posts__grid">
		<?php
		while ( $query->have_posts() ) :
			$query->the_post();
			get_template_part(
				'src/components/post-card',
				null,
				array(
					'post_id'    => get_the_ID(),
					'layout'     => 'grid',
					'image_size' => 'andreian-card',
				)
			);
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>

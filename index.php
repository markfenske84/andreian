<?
/**
 * The main template file for displaying posts and pages.
 */

get_header();
get_template_part('src/components/inner-masthead');
?>

<section 
	id="index-template" 
	class="
		_container 
		_archive">

	<div 
		class="_posts">

		<div class="_inner">

			<?
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			$args = array(
				'post_type'      => 'post',
				'posts_per_page' => get_option('posts_per_page'),
				'order'          => 'DESC',
				'paged'          => $paged,
			);

			$query = new WP_Query($args);

			if ($query->have_posts()) {
				while ($query->have_posts()) {
					$query->the_post();
					get_template_part('src/templates/partials/index/content', 'single');
				}

				wp_reset_postdata();

			} else {

				// No posts found
				echo 'No posts found';

			} ?>

		</div>

		<? 
		// Pagination
		echo '<div class="_pagination">';
		echo paginate_links(array(
			'total'     => $query->max_num_pages,
			'current'   => max(1, $paged),
			'prev_text' => __('<i class="fa-solid fa-angles-left"></i>', 'krypton'),
			'next_text' => __('<i class="fa-solid fa-angles-right"></i>', 'krypton'),
		));
		echo '</div>'; ?>
		

    </div>

    <? if(is_active_sidebar('sidebar')) { ?>
		
	<div 
		class="_sidebar">

		<? dynamic_sidebar('sidebar'); ?>

	</div>

	<? } ?>

</section>

<? get_footer(); ?>

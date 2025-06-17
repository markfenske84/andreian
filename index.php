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

		<? if ($query->have_posts()) : ?>
			<div class="_pagination">
				<?php
				global $wp_rewrite;
				
				$pagination = array(
					'base'       => @add_query_arg('paged','%#%'),
					'format'     => '',
					'total'      => $query->max_num_pages,
					'current'    => $paged,
					'prev_text'  => __('<i class="fa-solid fa-angles-left"></i>', 'arabesque'),
					'next_text'  => __('<i class="fa-solid fa-angles-right"></i>', 'arabesque'),
					'type'       => 'list',
					'end_size'   => 3,
					'mid_size'   => 3
				);
				
				if($wp_rewrite->using_permalinks()) {
					$pagination['base'] = user_trailingslashit(trailingslashit(remove_query_arg('s', get_pagenum_link(1))) . 'page/%#%/', 'paged');
				}
				
				echo paginate_links($pagination);
				?>
			</div>
		<? endif; ?>
		

    </div>

    <? if(is_active_sidebar('sidebar')) { ?>
		
	<div 
		class="_sidebar">

		<? dynamic_sidebar('sidebar'); ?>

	</div>

	<? } ?>

</section>

<? get_footer(); ?>

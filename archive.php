<?php
/**
 * The template for displaying archive pages, including categories, tags, authors, dates, etc.
 */

get_header();
get_template_part('src/components/inner-masthead');
?>

<section 
	id="archive-template" 
	aria-label="<?php esc_attr_e( 'Archive', 'chw' ); ?>"
	class="
		_container 
		_archive">

	<div 
		class="_posts">

		<div class="_inner">
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<?php get_template_part('src/templates/partials/index/content', 'single'); ?>
				<?php endwhile; ?>
			<?php else : ?>
				<p>No posts found in this archive.</p>
			<?php endif; ?>
		</div>

		<?php if (have_posts()) : ?>
			<div class="_pagination">
				<?php 
				global $wp_rewrite;
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
				
				$pagination = array(
					'base' => @add_query_arg('paged','%#%'),
					'format' => '',
					'total' => $wp_query->max_num_pages,
					'current' => $paged,
					'prev_text' => __('<i class="fa-solid fa-angles-left"></i>', 'arabesque'),
					'next_text' => __('<i class="fa-solid fa-angles-right"></i>', 'arabesque'),
					'type' => 'list',
					'end_size' => 3,
					'mid_size' => 3
				);
				
				if($wp_rewrite->using_permalinks()) {
					$pagination['base'] = user_trailingslashit(trailingslashit(remove_query_arg('s', get_pagenum_link(1))) . 'page/%#%/', 'paged');
				}
				
				echo paginate_links($pagination);
				?>
			</div>
		<?php endif; ?>
	</div>

	<?php if(is_active_sidebar('sidebar')) { ?>
	<div 
		class="_sidebar">
		<?php dynamic_sidebar('sidebar'); ?>
	</div>
	<?php } ?>

</section>

<?php get_footer(); ?> 
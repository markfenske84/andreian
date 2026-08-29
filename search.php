<?php
/**
 * Search results template.
 */

get_header();
?>

<section id="search-template" class="_container _archive">
	<div class="_posts">
		<div class="_inner">
			<h1 class="page-title">
				<?php
				printf(
					/* translators: %s: search query */
					esc_html__( 'Search results for: %s', 'andreian' ),
					esc_html( get_search_query() )
				);
				?>
			</h1>

			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'src/templates/partials/index/content', 'single' ); ?>
				<?php endwhile; ?>
			<?php else : ?>
				<p><?php esc_html_e( 'No results found. Please try a different search term.', 'andreian' ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( have_posts() ) : ?>
			<div class="_pagination">
				<?php the_posts_pagination(); ?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( is_active_sidebar( 'sidebar' ) ) : ?>
		<div class="_sidebar">
			<?php dynamic_sidebar( 'sidebar' ); ?>
		</div>
	<?php endif; ?>
</section>

<?php get_footer(); ?>

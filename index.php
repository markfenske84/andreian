<?php
/**
 * Blog index template.
 */

get_header();
?>

<section id="index-template" class="_container _archive" aria-label="<?php esc_attr_e( 'Blog posts', 'andreian' ); ?>">
	<div class="_posts">
		<div class="_inner">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'src/templates/partials/index/content', 'single' ); ?>
				<?php endwhile; ?>
			<?php else : ?>
				<p><?php esc_html_e( 'No posts found.', 'andreian' ); ?></p>
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

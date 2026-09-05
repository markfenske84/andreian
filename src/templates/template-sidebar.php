<?php
/**
 * Sidebar page template.
 *
 * Template Name: Sidebar
 * Template Post Type: page
 */

get_header();
?>

<div id="singular-template" class="_container _archive">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<div class="_posts">
				<div class="_inner">
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<?php get_template_part( 'src/components/page-entry-header' ); ?>
						<div class="entry-content">
							<?php the_content(); ?>
						</div>
					</article>
				</div>
			</div>
			<aside class="_sidebar post-sidebar" aria-label="<?php esc_attr_e( 'Page sidebar', 'andreian' ); ?>">
				<div class="post-sidebar__inner">
					<?php dynamic_sidebar( 'sidebar' ); ?>
				</div>
			</aside>
		<?php endwhile; ?>
	<?php endif; ?>
</div>

<?php get_footer(); ?>

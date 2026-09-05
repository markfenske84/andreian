<?php
/**
 * Default page template.
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<div id="singular-template" class="_container -content-width">
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php get_template_part( 'src/components/page-entry-header' ); ?>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</article>
			<?php comments_template(); ?>
		</div>
	<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>

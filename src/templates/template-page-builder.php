<?php
/**
 * Page builder template.
 *
 * Template Name: Page Builder
 * Template Post Type: page
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<div id="singular-template" class="_container -content-width">
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
			<?php comments_template(); ?>
		</div>
	<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>

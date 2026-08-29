<?php
/**
 * Full-width page builder template.
 *
 * Template Name: Page Builder (Full Width)
 * Template Post Type: page
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>

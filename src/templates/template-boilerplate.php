<?php
/**
 * Template boilerplate.
 */

get_header();
?>

<div class="_container">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>
	<?php endif; ?>
</div>

<?php get_footer(); ?>

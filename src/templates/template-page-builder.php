<?php
/*
Template Name: Page Builder (Full Width)
Template Post Type: page
*/

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php get_template_part( 'src/components/inner-masthead' ); ?>
		<?php the_content(); ?>
	<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>

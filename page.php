<?php
/**
 * Default page template.
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php
		$show_sidebar = andreian_singular_has_sidebar();
		$wrapper_tag  = $show_sidebar ? 'section' : 'div';
		?>

		<<?php echo $wrapper_tag; ?>
			id="singular-template"
			class="_container -content-width<?php echo $show_sidebar ? ' _archive' : ''; ?>">

			<?php if ( $show_sidebar ) : ?>
				<div class="_posts"><div class="_inner">
			<?php endif; ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php get_template_part( 'src/components/page-entry-header' ); ?>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</article>

			<?php if ( $show_sidebar ) : ?>
				</div></div>
				<div class="_sidebar">
					<?php dynamic_sidebar( 'sidebar' ); ?>
				</div>
			<?php endif; ?>

		</<?php echo $wrapper_tag; ?>>
	<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>

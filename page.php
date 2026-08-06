<?php
/**
 * Default page template — constrained content column (matches blog post measure).
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-page
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php get_template_part( 'src/components/inner-masthead' ); ?>

		<?php
		$show_sidebar = chw_singular_has_sidebar();
		$wrapper_tag  = $show_sidebar ? 'section' : 'div';
		?>

		<<?php echo $wrapper_tag; ?>
			id="singular-template"
			<?php if ( $show_sidebar ) : ?>
				aria-label="<?php esc_attr_e( 'Page content', 'chw' ); ?>"
			<?php endif; ?>
			class="_container -blog-content<?php echo $show_sidebar ? ' _archive' : ''; ?>">

			<?php if ( $show_sidebar ) : ?>
				<div class="_posts">
					<div class="_inner">
			<?php endif; ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix' ); ?>>
				<div class="page-content entry-content clearfix">
					<?php the_content(); ?>
				</div>
			</article>

			<?php if ( $show_sidebar ) : ?>
					</div>
				</div>
				<div class="_sidebar">
					<?php dynamic_sidebar( 'sidebar' ); ?>
				</div>
			<?php endif; ?>

		</<?php echo $wrapper_tag; ?>>
	<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>

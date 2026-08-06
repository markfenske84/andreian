<?php
/**
 * The template for displaying all singular posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 */

get_header();
get_template_part( 'src/components/inner-masthead' );

$show_sidebar = chw_singular_has_sidebar();
$wrapper_tag  = $show_sidebar ? 'section' : 'div';
?>

<<?php echo $wrapper_tag; ?>
	id="singular-template"
	<?php if ( $show_sidebar ) : ?>
		aria-label="<?php esc_attr_e( 'Blog post', 'chw' ); ?>"
	<?php endif; ?>
	class="_container -blog-content<?php echo $show_sidebar ? ' _archive' : ''; ?>">

	<?php if ( $show_sidebar ) : ?>
		<div class="_posts">
			<div class="_inner">
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix' ); ?> itemscope itemtype="http://schema.org/BlogPosting">
				<div class="page-content entry-content clearfix" itemprop="articleBody">
					<?php the_content(); ?>
				</div>
				<?php if ( 'post' === get_post_type() ) : ?>
					<?php get_template_part( 'src/components/author-bio' ); ?>
				<?php endif; ?>
			</article>
		<?php endwhile; ?>
	<?php endif; ?>

	<?php if ( $show_sidebar ) : ?>
			</div>
		</div>
		<div class="_sidebar">
			<?php dynamic_sidebar( 'sidebar' ); ?>
		</div>
	<?php endif; ?>

</<?php echo $wrapper_tag; ?>>

<?php if ( is_singular( 'post' ) ) : ?>
	<?php chw_render_block_pattern( 'generic-cta-banner' ); ?>
<?php endif; ?>

<?php get_footer(); ?>

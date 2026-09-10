<?php
/**
 * Singular post template — Classic Editor content with sidebar layout.
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php
		$has_thumbnail = has_post_thumbnail();
		$hero_class    = $has_thumbnail ? ' single-article__hero--has-image' : '';
		?>
		<div class="single-article__hero<?php echo esc_attr( $hero_class ); ?>">
			<?php if ( $has_thumbnail ) : ?>
				<figure class="single-article__featured-image" style="--featured-position: <?php echo esc_attr( andreian_featured_image_object_position() ); ?>;">
					<?php
					the_post_thumbnail(
						'andreian-feature',
						array(
							'loading'       => 'eager',
							'fetchpriority' => 'high',
							'decoding'      => 'async',
							'sizes'         => '100vw',
						)
					);
					?>
				</figure>
			<?php endif; ?>

			<div class="single-article__hero-inner _container">
				<?php get_template_part( 'src/components/post-breadcrumbs' ); ?>
				<header class="entry-header">
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php if ( andreian_get_short_description() ) : ?>
						<p class="entry-deck"><?php echo esc_html( andreian_get_short_description() ); ?></p>
					<?php endif; ?>
					<?php get_template_part( 'src/components/post-entry-meta' ); ?>
				</header>
			</div>
		</div>

		<section id="singular-template" class="_container _archive" aria-label="<?php esc_attr_e( 'Blog post', 'andreian' ); ?>">
			<div class="_posts">
				<div class="_inner">
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-article' ); ?>>
						<div class="entry-content">
							<?php the_content(); ?>
						</div>
						<?php if ( is_singular( 'post' ) ) : ?>
							<?php get_template_part( 'src/components/post-cta' ); ?>
							<?php get_template_part( 'src/components/post-share' ); ?>
						<?php endif; ?>
						<?php get_template_part( 'src/components/recommended-posts' ); ?>
					</article>
					<?php comments_template(); ?>
				</div>
			</div>
			<aside class="_sidebar post-sidebar" aria-label="<?php esc_attr_e( 'Post sidebar', 'andreian' ); ?>">
				<div class="post-sidebar__inner">
					<?php dynamic_sidebar( 'sidebar' ); ?>
				</div>
			</aside>
		</section>
	<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>

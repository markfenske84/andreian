<?php
/**
 * Singular post template — Classic Editor content with sidebar layout.
 */

get_header();
?>

<section id="singular-template" class="_container _archive" aria-label="<?php esc_attr_e( 'Blog post', 'andreian' ); ?>">
	<div class="_posts">
		<div class="_inner">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-article' ); ?>>
						<?php get_template_part( 'src/components/post-breadcrumbs' ); ?>
						<header class="entry-header">
							<h1 class="entry-title"><?php the_title(); ?></h1>
							<?php get_template_part( 'src/components/post-entry-meta' ); ?>
						</header>
						<?php if ( has_post_thumbnail() ) : ?>
							<figure class="single-article__featured-image">
								<?php
								the_post_thumbnail(
									'andreian-feature',
									array(
										'loading'       => 'eager',
										'fetchpriority' => 'high',
										'decoding'      => 'async',
										'sizes'         => '(max-width: 1200px) 100vw, 1200px',
									)
								);
								?>
							</figure>
						<?php endif; ?>
						<div class="entry-content">
							<?php the_content(); ?>
						</div>
						<?php get_template_part( 'src/components/author-bio' ); ?>
					</article>
				<?php endwhile; ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="_sidebar">
		<?php dynamic_sidebar( 'sidebar' ); ?>
	</div>
</section>

<?php get_footer(); ?>

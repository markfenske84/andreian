<?php
/**
 * Singular post template — Classic Editor content with sidebar layout.
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="single-article__featured-image">
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

		<section id="singular-template" class="_container _archive" aria-label="<?php esc_attr_e( 'Blog post', 'andreian' ); ?>">
			<div class="_posts">
				<div class="_inner">
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-article' ); ?>>
						<?php get_template_part( 'src/components/post-breadcrumbs' ); ?>
						<header class="entry-header">
							<h1 class="entry-title"><?php the_title(); ?></h1>
							<?php if ( andreian_get_short_description() ) : ?>
								<p class="entry-deck"><?php echo esc_html( andreian_get_short_description() ); ?></p>
							<?php endif; ?>
							<?php get_template_part( 'src/components/post-entry-meta' ); ?>
						</header>
						<div class="entry-content">
							<?php the_content(); ?>
						</div>
						<?php get_template_part( 'src/components/author-bio' ); ?>
					</article>
				</div>
			</div>
			<aside class="_sidebar post-sidebar" aria-label="<?php esc_attr_e( 'Post sidebar', 'andreian' ); ?>">
				<div class="post-sidebar__sticky">
					<?php get_template_part( 'src/components/post-share' ); ?>
				</div>
				<?php dynamic_sidebar( 'sidebar' ); ?>
			</aside>
		</section>
	<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>

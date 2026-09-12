<?php
/**
 * Archive template.
 */

get_header();

$is_category_archive = is_category();
?>

<section id="archive-template" class="_container<?php echo $is_category_archive ? ' category-archive' : ' _archive'; ?>" aria-label="<?php esc_attr_e( 'Archive', 'andreian' ); ?>">
	<div class="_posts">
		<header class="archive-header">
			<?php if ( $is_category_archive ) : ?>
				<h1 class="archive-title"><?php single_cat_title(); ?></h1>
			<?php else : ?>
				<?php the_archive_title( '<h1 class="archive-title">', '</h1>' ); ?>
			<?php endif; ?>
			<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
		</header>
		<div class="_inner archive-post-list<?php echo $is_category_archive ? ' category-post-grid' : ''; ?>">
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php
					get_template_part(
						'src/templates/partials/index/content',
						$is_category_archive ? 'category' : 'single'
					);
					?>
				<?php endwhile; ?>
			<?php else : ?>
				<p><?php esc_html_e( 'No posts found in this archive.', 'andreian' ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( have_posts() ) : ?>
			<div class="_pagination">
				<?php the_posts_pagination(); ?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( ! $is_category_archive && is_active_sidebar( 'sidebar' ) ) : ?>
		<div class="_sidebar">
			<?php dynamic_sidebar( 'sidebar' ); ?>
		</div>
	<?php endif; ?>
</section>

<?php get_footer(); ?>

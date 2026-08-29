<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="article-image">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'large', array( 'alt' => get_the_title() ) ); ?>
			</a>
		</div>
	<?php endif; ?>
	<div class="article-content">
		<header class="entry-header">
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php get_template_part( 'src/components/post-entry-meta' ); ?>
		</header>
		<div class="entry-summary">
			<?php echo esc_html( andreian_excerpt() ); ?>
		</div>
		<p><a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more', 'andreian' ); ?></a></p>
	</div>
</article>

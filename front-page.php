<?php
/**
 * Front page template.
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<div class="entry-content">
			<?php
			$content = trim( get_the_content() );

			if ( '' !== $content ) {
				the_content();
			} else {
				echo do_blocks( '<!-- wp:pattern {"slug":"andreian/editorial-homepage"} /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
	<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>

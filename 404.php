<?php
/**
 * The template for displaying 404 pages.
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 */

get_header();
?>

<div class="error404-content">
	<article id="post-not-found">
		<h1><?php esc_html_e( '404 - Page Not Found', 'andreian' ); ?></h1>
		<p>
			<?php
			printf(
				/* translators: %s: homepage URL */
				esc_html__( 'This page doesn’t exist. Click here to head to the %s.', 'andreian' ),
				'<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'home page', 'andreian' ) . '</a>'
			);
			?>
		</p>
	</article>
</div>

<?php get_footer(); ?>

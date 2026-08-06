<?php
/**
 * The template for displaying the footer. Contains the closing of the main and body
 * elements.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */
?>

		</main>

		<footer id="site-footer" class="site-footer">
			<div class="_container">

			<div class="footer-top">
				<div class="footer-columns">
					<div class="footer-col footer-col--brand">
						<?php dynamic_sidebar( 'footer_col1' ); ?>
					</div>
					<div class="footer-nav-cols _grid -col4">
						<?php for ( $i = 2; $i <= 5; $i++ ) : ?>
						<div class="footer-col footer-col--nav">
							<?php dynamic_sidebar( "footer_col{$i}" ); ?>
						</div>
						<?php endfor; ?>
					</div>
				</div>
			</div>

			<div class="footer-legal">
				<?php if ( is_active_sidebar( 'footer_legal' ) ) : ?>
				<div class="footer-legal__content">
					<?php dynamic_sidebar( 'footer_legal' ); ?>
				</div>
				<?php endif; ?>
				<p class="footer-copyright">
					&copy; <?= date( 'Y' ); ?> <?= esc_html( get_bloginfo( 'name' ) ); ?>. All rights reserved.
				</p>
			</div>

			</div>
		</footer>

		<?php wp_footer(); ?>

	</body>
</html>

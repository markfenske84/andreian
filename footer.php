<?php
/**
 * Footer widget area template.
 */

?>
		</main>

		<footer id="site-footer" class="site-footer">
			<div class="_container">
				<?php if ( is_active_sidebar( 'footer_main' ) ) : ?>
					<div class="footer-main">
						<?php dynamic_sidebar( 'footer_main' ); ?>
					</div>
				<?php endif; ?>
				<p class="footer-copyright">
					&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
				</p>
			</div>
		</footer>

		<?php wp_footer(); ?>
	</body>
</html>

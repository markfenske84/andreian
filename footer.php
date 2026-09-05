<?php
/**
 * Footer widget area template.
 */

?>
		</main>

		<footer id="site-footer" class="site-footer">
			<div class="_container">
				<div class="site-footer__brand">
					<a class="site-footer__wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
					</a>
					<p>FTA-TPP.</p>
				</div>
				<?php if ( is_active_sidebar( 'footer_main' ) && ! is_front_page() ) : ?>
					<div class="footer-main">
						<?php dynamic_sidebar( 'footer_main' ); ?>
					</div>
				<?php endif; ?>
				<div class="site-footer__lower">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer-nav',
							'menu_id'        => 'footer-menu',
							'container'      => 'nav',
							'container_aria_label' => __( 'Footer navigation', 'andreian' ),
							'fallback_cb'    => false,
							'depth'          => 1,
						)
					);
					?>
					<p class="footer-copyright">
						&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
					</p>
				</div>
				<p class="site-footer__disclosure">
					<?php
					printf(
						/* translators: %s: site name */
						esc_html__( '%s takes part in affiliate marketing programs, which means we may earn a commission on any products purchased through our links. We only recommend products we believe in. Those purchases help support this site and the free writing published here.', 'andreian' ),
						esc_html( get_bloginfo( 'name' ) )
					);
					?>
				</p>
			</div>
		</footer>

		<?php wp_footer(); ?>
	</body>
</html>

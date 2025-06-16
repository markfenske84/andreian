<?
/**
 * The template for displaying the footer. Contains the closing of the main and body
 * elements.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */

$page_scripts = get_field('page_scripts');
if($page_scripts) {
	$page_footer_scripts = $page_scripts['footer_scripts']; 
	$page_body_scripts = $page_scripts['body_scripts']; 
}
?>

		</main>

		<footer id="site-footer" class="site-footer">

			<div class="_inner _container _flex -justify-space-between">

				<? if ( is_active_sidebar( 'footer_col1' ) ) : ?>
				<div class="footer-col">
					<? dynamic_sidebar( 'footer_col1' ); ?>
				</div>
				<? endif; ?>

				<? if ( is_active_sidebar( 'footer_col2' ) ) : ?>
				<div class="footer-col">
					<? dynamic_sidebar( 'footer_col2' ); ?>
				</div>
				<? endif; ?>

				<? if ( is_active_sidebar( 'footer_col3' ) ) : ?>
				<div class="footer-col">
					<? dynamic_sidebar( 'footer_col3' ); ?>
				</div>
				<? endif; ?>

				<? if ( is_active_sidebar( 'footer_col4' ) ) : ?>
				<div class="footer-col">
					<? dynamic_sidebar( 'footer_col4' ); ?>
				</div>
				<? endif; ?>

			</div>

			<div class="footer-bottom _bg _container -max-width-100 _text -align-center">
				<p><?= '&copy; ' . date('Y') . ' ' . get_option('blogname'); ?>. All Rights Reserved. Website by <a href="https://webfor.com" target="_blank">Webfor</a>.</p>
			</div>

			<? $settings_footer_scripts = get_field('footer_scripts', 'option'); if($settings_footer_scripts) { echo $settings_footer_scripts; } ?>
			<? if($page_scripts && $page_footer_scripts) { echo $page_footer_scripts; } ?>

		</footer>

		<? wp_footer(); ?>

		<? $settings_body_scripts = get_field('body_scripts', 'option'); if($settings_body_scripts) { echo $settings_body_scripts; } ?>
		<? if($page_scripts && $page_body_scripts) { echo $page_body_scripts; } ?>

	</body>
</html>
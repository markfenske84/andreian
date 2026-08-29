<style>
	:root {
		--container-gutter: <?php echo esc_attr( get_theme_mod( 'container_gutter', '16' ) ); ?>px;
		--container-width: <?php echo esc_attr( get_theme_mod( 'container_width', '1200' ) ); ?>px;
		--header-height: 0px;
		--admin-bar-height: <?php echo is_admin_bar_showing() ? '32px' : '0px'; ?>;
	}
</style>

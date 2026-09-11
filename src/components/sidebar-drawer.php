<?php
/**
 * Mobile tab and backdrop for the shared post sidebar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_singular( 'post' ) || ! is_active_sidebar( 'sidebar' ) ) {
	return;
}
?>

<div class="andreian-sidebar-drawer" data-andreian-sidebar-drawer>
	<button
		class="andreian-sidebar-drawer__tab"
		type="button"
		aria-expanded="false"
		aria-controls="andreian-post-sidebar"
		data-open-label="<?php esc_attr_e( 'Open sidebar', 'andreian' ); ?>"
		data-close-label="<?php esc_attr_e( 'Close sidebar', 'andreian' ); ?>"
		aria-label="<?php esc_attr_e( 'Open sidebar', 'andreian' ); ?>"
	>
		<?php echo svg( 'ico-chevron-up', 'andreian-sidebar-drawer__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Theme SVG asset. ?>
	</button>
	<div class="andreian-sidebar-drawer__backdrop" aria-hidden="true"></div>
</div>

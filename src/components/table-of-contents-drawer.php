<?php
/**
 * Mobile table of contents drawer.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! andreian_post_has_toc() ) {
	return;
}

ob_start();
get_template_part(
	'src/components/table-of-contents',
	null,
	array(
		'variant' => 'drawer',
	)
);
$toc_markup = trim( ob_get_clean() );

if ( '' === $toc_markup ) {
	return;
}
?>

<div class="andreian-toc-drawer" data-andreian-toc-drawer>
	<button
		class="andreian-toc-drawer__tab"
		type="button"
		aria-expanded="false"
		aria-controls="andreian-toc-drawer-panel"
		data-open-label="<?php esc_attr_e( 'Open table of contents', 'andreian' ); ?>"
		data-close-label="<?php esc_attr_e( 'Close table of contents', 'andreian' ); ?>"
		aria-label="<?php esc_attr_e( 'Open table of contents', 'andreian' ); ?>"
	>
		<?php echo svg( 'ico-chevron-up', 'andreian-toc-drawer__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Theme SVG asset. ?>
	</button>
	<div class="andreian-toc-drawer__backdrop" aria-hidden="true"></div>
	<div
		id="andreian-toc-drawer-panel"
		class="andreian-toc-drawer__panel"
		role="dialog"
		aria-modal="true"
		aria-hidden="true"
		aria-label="<?php esc_attr_e( 'Table of contents', 'andreian' ); ?>"
	>
		<?php echo $toc_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rendered TOC component. ?>
	</div>
</div>

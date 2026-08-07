<?php
/**
 * Frontend render for the Tabbed Comparison Table block.
 *
 * Expects $attributes (and optionally $block_wrapper_attributes) provided by the
 * render callback in register-tabbed-comparison-table-block.php.
 *
 * @var array  $attributes
 * @var string $block_wrapper_attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();

$tabs       = isset( $attributes['tabs'] ) && is_array( $attributes['tabs'] ) ? $attributes['tabs'] : array();

$tabs         = chw_tabbed_comparison_table_filter_tabs( $tabs );
$tab_count    = count( $tabs );
$show_tablist = $tab_count >= 2;
$dom_id       = wp_unique_id( 'tabbed-comparison-table-' );

if ( 0 === $tab_count ) {
	return;
}

$wrapper_attributes = isset( $block_wrapper_attributes ) ? $block_wrapper_attributes : '';
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>

	<div class="_container">

		<div class="tabbed-comparison-table__body<?php echo $show_tablist ? ' -has-tabs' : ' -single-tab'; ?>">

			<?php if ( $show_tablist ) : ?>
				<div class="tabbed-comparison-table__tabs _tabs -flex" role="tablist" aria-label="<?php echo esc_attr__( 'Coverage tables', 'chw' ); ?>">
					<?php foreach ( $tabs as $index => $tab ) : ?>
						<?php
						$is_active = ( 0 === $index );
						$tab_id    = $dom_id . '-tab-' . $index;
						$pane_id   = $dom_id . '-pane-' . $index;
						$label     = isset( $tab['label'] ) ? $tab['label'] : '';
						?>
						<button
							type="button"
							class="_tab<?php echo $is_active ? ' -active' : ''; ?>"
							id="<?php echo esc_attr( $tab_id ); ?>"
							role="tab"
							aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
							aria-controls="<?php echo esc_attr( $pane_id ); ?>"
							tabindex="<?php echo $is_active ? '0' : '-1'; ?>">
							<?php echo esc_html( $label ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="tabbed-comparison-table__panes<?php echo $show_tablist ? ' _panes' : ''; ?>">
				<?php foreach ( $tabs as $index => $tab ) : ?>
					<?php
					$is_active  = ( 0 === $index );
					$tab_id     = $dom_id . '-tab-' . $index;
					$pane_id    = $dom_id . '-pane-' . $index;
					$pane_class = 'tabbed-comparison-table__pane';
					if ( $show_tablist ) {
						$pane_class .= ' _pane';
						if ( $is_active ) {
							$pane_class .= ' -active';
						}
					}
					?>
					<div
						class="<?php echo esc_attr( $pane_class ); ?>"
						<?php if ( $show_tablist ) : ?>
							id="<?php echo esc_attr( $pane_id ); ?>"
							role="tabpanel"
							aria-labelledby="<?php echo esc_attr( $tab_id ); ?>"
							tabindex="0"
							<?php echo $is_active ? '' : 'hidden'; ?>
						<?php endif; ?>>
						<?php chw_tabbed_comparison_table_render_table( $tab ); ?>
					</div>
				<?php endforeach; ?>
			</div>

		</div>

	</div>
</section>

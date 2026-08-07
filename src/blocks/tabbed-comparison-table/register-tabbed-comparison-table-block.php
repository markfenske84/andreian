<?php
/**
 * Tabbed Comparison Table: native (non-ACF) dynamic Gutenberg block.
 *
 * Content is edited inline in the editor canvas via
 * src/js/editor/tabbed-comparison-table.js, while the front end is rendered
 * from PHP through the render callback below.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attribute schema shared by the editor script and the server-side render.
 *
 * @return array
 */
function chw_tabbed_comparison_table_block_attributes() {
	return array(
		'tabs' => array(
			'type'    => 'array',
			'default' => array(),
		),
	);
}

/**
 * Return a positional CSS variant slug for a plan column index.
 *
 * @param int $index Zero-based column index.
 * @return string
 */
function chw_tabbed_comparison_table_column_variant( $index ) {
	if ( 0 === (int) $index ) {
		return 'primary';
	}
	if ( 1 === (int) $index ) {
		return 'secondary';
	}
	return 'tertiary';
}

/**
 * Determine whether a tab has meaningful table content.
 *
 * @param array $tab Tab attribute array.
 * @return bool
 */
function chw_tabbed_comparison_table_tab_has_content( $tab ) {
	if ( ! is_array( $tab ) ) {
		return false;
	}

	if ( ! empty( $tab['label'] ) || ! empty( $tab['cornerLabel'] ) ) {
		return true;
	}

	$columns = isset( $tab['columns'] ) && is_array( $tab['columns'] ) ? $tab['columns'] : array();
	foreach ( $columns as $column ) {
		if ( ! empty( $column['title'] ) || ! empty( $column['subtitle'] ) ) {
			return true;
		}
	}

	$groups = isset( $tab['groups'] ) && is_array( $tab['groups'] ) ? $tab['groups'] : array();
	foreach ( $groups as $group ) {
		if ( ! empty( $group['title'] ) ) {
			return true;
		}

		$column_labels = isset( $group['columnLabels'] ) && is_array( $group['columnLabels'] ) ? $group['columnLabels'] : array();
		foreach ( $column_labels as $label ) {
			if ( ! empty( $label ) ) {
				return true;
			}
		}

		$rows = isset( $group['rows'] ) && is_array( $group['rows'] ) ? $group['rows'] : array();
		foreach ( $rows as $row ) {
			if ( ! empty( $row['label'] ) ) {
				return true;
			}

			$cells = isset( $row['cells'] ) && is_array( $row['cells'] ) ? $row['cells'] : array();
			foreach ( $cells as $cell ) {
				if ( ! is_array( $cell ) ) {
					continue;
				}
				$type = isset( $cell['type'] ) ? $cell['type'] : 'none';
				if ( 'check' === $type ) {
					return true;
				}
				if ( 'text' === $type && ! empty( $cell['text'] ) ) {
					return true;
				}
			}
		}
	}

	return false;
}

/**
 * Filter tabs to those with content.
 *
 * @param array $tabs Tab attribute arrays.
 * @return array
 */
function chw_tabbed_comparison_table_filter_tabs( $tabs ) {
	if ( ! is_array( $tabs ) ) {
		return array();
	}

	return array_values(
		array_filter(
			$tabs,
			'chw_tabbed_comparison_table_tab_has_content'
		)
	);
}

/**
 * Render a single comparison table for one tab.
 *
 * @param array $tab Tab attribute array.
 */
function chw_tabbed_comparison_table_render_table( $tab ) {
	$columns = isset( $tab['columns'] ) && is_array( $tab['columns'] ) ? array_slice( array_values( $tab['columns'] ), 0, 4 ) : array();
	$groups  = isset( $tab['groups'] ) && is_array( $tab['groups'] ) ? array_values( $tab['groups'] ) : array();
	$corner  = isset( $tab['cornerLabel'] ) ? $tab['cornerLabel'] : '';

	if ( count( $columns ) < 2 ) {
		$columns = array(
			array( 'title' => '', 'subtitle' => '' ),
			array( 'title' => '', 'subtitle' => '' ),
		);
	}

	$inline_allowed = array(
		'span'   => array( 'class' => array() ),
		'strong' => array(),
		'em'     => array(),
		'br'     => array(),
	);

	$column_count = count( $columns );
	?>
	<div class="tabbed-comparison-table__scroll">
		<table class="tabbed-comparison-table__table" data-columns="<?php echo esc_attr( $column_count ); ?>">
			<thead>
				<tr class="tabbed-comparison-table__head-row">
					<th scope="col" class="tabbed-comparison-table__corner">
						<?php if ( $corner ) : ?>
							<?php echo wp_kses( $corner, $inline_allowed ); ?>
						<?php endif; ?>
					</th>
					<?php foreach ( $columns as $col_index => $column ) : ?>
						<?php
						$variant = chw_tabbed_comparison_table_column_variant( $col_index );
						?>
						<th
							scope="col"
							class="tabbed-comparison-table__plan-head -<?php echo esc_attr( $variant ); ?>">
							<?php if ( ! empty( $column['title'] ) ) : ?>
								<span class="tabbed-comparison-table__plan-title"><?php echo wp_kses( $column['title'], $inline_allowed ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $column['subtitle'] ) ) : ?>
								<span class="tabbed-comparison-table__plan-subtitle"><?php echo wp_kses( $column['subtitle'], $inline_allowed ); ?></span>
							<?php endif; ?>
						</th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $groups as $group ) : ?>
					<?php
					$group_title   = isset( $group['title'] ) ? $group['title'] : '';
					$column_labels = isset( $group['columnLabels'] ) && is_array( $group['columnLabels'] ) ? $group['columnLabels'] : array();
					$rows          = isset( $group['rows'] ) && is_array( $group['rows'] ) ? $group['rows'] : array();
					$has_group_head = $group_title || array_filter( $column_labels );
					?>
					<?php if ( $has_group_head ) : ?>
						<tr class="tabbed-comparison-table__group-row">
							<th scope="row" class="tabbed-comparison-table__group-label">
								<?php if ( $group_title ) : ?>
									<?php echo wp_kses( $group_title, $inline_allowed ); ?>
								<?php endif; ?>
							</th>
							<?php foreach ( $columns as $col_index => $column ) : ?>
								<?php
								$variant      = chw_tabbed_comparison_table_column_variant( $col_index );
								$column_label = isset( $column_labels[ $col_index ] ) ? $column_labels[ $col_index ] : '';
								?>
								<td class="tabbed-comparison-table__group-col -<?php echo esc_attr( $variant ); ?>">
									<?php if ( $column_label ) : ?>
										<?php echo wp_kses( $column_label, $inline_allowed ); ?>
									<?php endif; ?>
								</td>
							<?php endforeach; ?>
						</tr>
					<?php endif; ?>

					<?php foreach ( $rows as $row_index => $row ) : ?>
						<?php
						$row_label = isset( $row['label'] ) ? $row['label'] : '';
						$cells     = isset( $row['cells'] ) && is_array( $row['cells'] ) ? $row['cells'] : array();
						$row_class = 'tabbed-comparison-table__feature-row';
						if ( 0 === $row_index % 2 ) {
							$row_class .= ' -even';
						}
						?>
						<tr class="<?php echo esc_attr( $row_class ); ?>">
							<th scope="row" class="tabbed-comparison-table__feature-label">
								<?php if ( $row_label ) : ?>
									<?php echo wp_kses( $row_label, $inline_allowed ); ?>
								<?php endif; ?>
							</th>
							<?php foreach ( $columns as $col_index => $column ) : ?>
								<?php
								$variant = chw_tabbed_comparison_table_column_variant( $col_index );
								$cell    = isset( $cells[ $col_index ] ) && is_array( $cells[ $col_index ] ) ? $cells[ $col_index ] : array( 'type' => 'none', 'text' => '' );
								$type    = isset( $cell['type'] ) ? $cell['type'] : 'none';
								$text    = isset( $cell['text'] ) ? $cell['text'] : '';
								?>
								<td class="tabbed-comparison-table__cell -<?php echo esc_attr( $variant ); ?>">
									<?php if ( 'check' === $type ) : ?>
										<span class="tabbed-comparison-table__check-wrap" aria-hidden="true">
											<svg class="tabbed-comparison-table__check" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
												<path d="M20 6 9 17l-5-5" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
											</svg>
										</span>
										<span class="sr-only"><?php esc_html_e( 'Included', 'chw' ); ?></span>
									<?php elseif ( 'text' === $type && $text ) : ?>
										<span class="tabbed-comparison-table__cell-text"><?php echo wp_kses( $text, $inline_allowed ); ?></span>
									<?php else : ?>
										<span class="sr-only"><?php esc_html_e( 'Not included', 'chw' ); ?></span>
									<?php endif; ?>
								</td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}

/**
 * Server-side render callback for the block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function chw_render_tabbed_comparison_table_block( $attributes ) {
	$wrapper_classes = 'tabbed-comparison-table';

	$block_wrapper_attributes = function_exists( 'get_block_wrapper_attributes' )
		? get_block_wrapper_attributes( array( 'class' => $wrapper_classes ) )
		: 'class="' . esc_attr( $wrapper_classes ) . '"';

	ob_start();
	include __DIR__ . '/tabbed-comparison-table.php';
	return ob_get_clean();
}

/**
 * Register the block type.
 */
function chw_register_tabbed_comparison_table_block() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/tabbed-comparison-table.js';

	wp_register_script(
		'chw-tabbed-comparison-table',
		get_template_directory_uri() . '/src/js/editor/tabbed-comparison-table.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	register_block_type(
		'chw/tabbed-comparison-table',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'attributes'      => chw_tabbed_comparison_table_block_attributes(),
			'editor_script'   => 'chw-tabbed-comparison-table',
			'render_callback' => 'chw_render_tabbed_comparison_table_block',
		)
	);
}
add_action( 'init', 'chw_register_tabbed_comparison_table_block' );

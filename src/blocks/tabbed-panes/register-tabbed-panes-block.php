<?php
/**
 * Tabbed Panes: native (non-ACF) dynamic Gutenberg blocks.
 *
 * Two blocks:
 *  - chw/tabbed-panes (parent): InnerBlocks container of chw/tab-pane children.
 *  - chw/tab-pane     (child):  a label (RichText) + InnerBlocks body.
 *
 * The parent render callback builds the tablist + panes markup (role=tab /
 * role=tabpanel, IDs, active state) so the existing frontend tab switcher in
 * src/blocks/tabbed-panes/tabbed-panes.js keeps working.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Parent render: build tablist buttons + panes from the child blocks.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    InnerBlocks rendered content (unused; rendered manually).
 * @param WP_Block $block      Block instance (gives access to inner_blocks).
 * @return string
 */
function chw_render_tabbed_panes_block( $attributes, $content, $block ) {
	if ( empty( $block->inner_blocks ) ) {
		return '';
	}

	$dom_id = wp_unique_id( 'tabbed-panes-' );

	// Pre-compute each tab's label, slug, and rendered body.
	$tabs = array();
	$i    = 0;
	foreach ( $block->inner_blocks as $child ) {
		$label = isset( $child->attributes['label'] ) ? wp_strip_all_tags( $child->attributes['label'] ) : '';

		$body = '';
		if ( ! empty( $child->inner_blocks ) ) {
			foreach ( $child->inner_blocks as $grandchild ) {
				$body .= $grandchild->render();
			}
		}

		$tabs[] = array(
			'label' => $label,
			'slug'  => function_exists( 'slugify' ) ? slugify( $label ) : sanitize_title( $label ),
			'body'  => $body,
		);
		$i++;
	}

	ob_start();
	?>
	<div class="tabbed-panes">

		<div class="_tabs -flex" role="tablist" aria-label="<?php echo esc_attr__( 'Content tabs', 'chw' ); ?>">
			<?php foreach ( $tabs as $index => $tab ) :
				$is_active = ( 0 === $index );
				$tab_id    = $dom_id . '-tab-' . $index;
				$pane_id   = $dom_id . '-pane-' . $index;
				?>
				<button
					type="button"
					class="_tab<?php echo $is_active ? ' -active' : ''; ?>"
					id="<?php echo esc_attr( $tab_id ); ?>"
					role="tab"
					aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
					aria-controls="<?php echo esc_attr( $pane_id ); ?>"
					tabindex="<?php echo $is_active ? '0' : '-1'; ?>"
					data-slug="<?php echo esc_attr( $tab['slug'] ); ?>">
					<?php echo esc_html( $tab['label'] ); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<div class="_panes">
			<?php foreach ( $tabs as $index => $tab ) :
				$is_active = ( 0 === $index );
				$tab_id    = $dom_id . '-tab-' . $index;
				$pane_id   = $dom_id . '-pane-' . $index;
				?>
				<div
					class="_pane<?php echo $is_active ? ' -active' : ''; ?>"
					id="<?php echo esc_attr( $pane_id ); ?>"
					role="tabpanel"
					data-slug="<?php echo esc_attr( $tab['slug'] ); ?>"
					aria-labelledby="<?php echo esc_attr( $tab_id ); ?>"
					tabindex="0"
					<?php echo $is_active ? '' : 'hidden'; ?>>
					<?php echo $tab['body']; // phpcs:ignore WordPress.Security.EscapeOutput - inner blocks render their own escaped output. ?>
				</div>
			<?php endforeach; ?>
		</div>

	</div>
	<?php
	return ob_get_clean();
}

/**
 * Child render: parent renders everything, so the child outputs nothing on its own.
 *
 * @return string
 */
function chw_render_tab_pane_child_block() {
	return '';
}

/**
 * Register both block types.
 */
function chw_register_tabbed_panes_blocks() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/tabbed-panes.js';

	wp_register_script(
		'chw-tabbed-panes',
		get_template_directory_uri() . '/src/js/editor/tabbed-panes.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	register_block_type(
		'chw/tabbed-panes',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'editor_script'   => 'chw-tabbed-panes',
			'render_callback' => 'chw_render_tabbed_panes_block',
		)
	);

	register_block_type(
		'chw/tab-pane',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'parent'          => array( 'chw/tabbed-panes' ),
			'attributes'      => array(
				'label' => array(
					'type'    => 'string',
					'default' => '',
				),
			),
			'render_callback' => 'chw_render_tab_pane_child_block',
		)
	);
}
add_action( 'init', 'chw_register_tabbed_panes_blocks' );

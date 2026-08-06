<?php
/**
 * Accordions: native (non-ACF) dynamic Gutenberg blocks.
 *
 * Two blocks:
 *  - chw/accordions (parent): an InnerBlocks container of chw/accordion children.
 *  - chw/accordion  (child):  a RichText summary + InnerBlocks body.
 *
 * The parent render callback builds the <details> markup (index-based IDs, ARIA,
 * first item open) so the existing frontend toggle in
 * src/blocks/accordions/accordions.js keeps working.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Parent render: iterate child blocks and emit the accordion markup.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    InnerBlocks rendered content (unused; we render manually).
 * @param WP_Block $block      Block instance (gives access to inner_blocks).
 * @return string
 */
function chw_render_accordions_block( $attributes, $content, $block ) {
	if ( empty( $block->inner_blocks ) ) {
		return '';
	}

	$prefix = wp_unique_id( 'accordion-' );

	ob_start();
	?>
	<div class="accordions">
		<?php
		$index = 0;
		foreach ( $block->inner_blocks as $child ) {
			$summary = isset( $child->attributes['summary'] ) ? $child->attributes['summary'] : '';

			$body = '';
			if ( ! empty( $child->inner_blocks ) ) {
				foreach ( $child->inner_blocks as $grandchild ) {
					$body .= $grandchild->render();
				}
			}

			$is_open    = ( 0 === $index );
			$summary_id = $prefix . '-summary-' . $index;
			$content_id = $prefix . '-content-' . $index;
			?>
			<details
				class="accordion"
				role="group"
				aria-labelledby="<?php echo esc_attr( $summary_id ); ?>"
				<?php echo $is_open ? 'open' : ''; ?>>

				<summary
					class="_summary"
					id="<?php echo esc_attr( $summary_id ); ?>"
					role="button"
					aria-controls="<?php echo esc_attr( $content_id ); ?>"
					aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>">
					<?php echo wp_kses_post( $summary ); ?>
				</summary>

				<div
					class="_inner"
					id="<?php echo esc_attr( $content_id ); ?>"
					role="region"
					aria-labelledby="<?php echo esc_attr( $summary_id ); ?>"
					aria-hidden="<?php echo $is_open ? 'false' : 'true'; ?>">
					<?php echo $body; // phpcs:ignore WordPress.Security.EscapeOutput - inner blocks render their own escaped output. ?>
				</div>

			</details>
			<?php
			$index++;
		}
		?>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Child render: parent renders everything, so the child outputs nothing on its own.
 *
 * @return string
 */
function chw_render_accordion_child_block() {
	return '';
}

/**
 * Register both block types.
 */
function chw_register_accordions_blocks() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$script_path = get_template_directory() . '/src/js/editor/accordions.js';

	wp_register_script(
		'chw-accordions',
		get_template_directory_uri() . '/src/js/editor/accordions.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-i18n' ),
		chw_asset_version( $script_path ),
		true
	);

	register_block_type(
		'chw/accordions',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'editor_script'   => 'chw-accordions',
			'render_callback' => 'chw_render_accordions_block',
		)
	);

	register_block_type(
		'chw/accordion',
		array(
			'api_version'     => 2,
			'category'        => 'choice-home-warranty',
			'parent'          => array( 'chw/accordions' ),
			'attributes'      => array(
				'summary' => array(
					'type'    => 'string',
					'default' => '',
				),
			),
			'render_callback' => 'chw_render_accordion_child_block',
		)
	);
}
add_action( 'init', 'chw_register_accordions_blocks' );

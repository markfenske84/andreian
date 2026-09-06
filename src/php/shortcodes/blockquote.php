<?php
/**
 * Blockquote shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed visual styles for [blockquote] and core/quote block styles.
 *
 * @return array<string, string> Style key => CSS class.
 */
function andreian_blockquote_style_map() {
	return array(
		'pull'      => 'pullquote',
		'pullquote' => 'pullquote',
		'inline'    => 'inline-quote',
		'framed'    => 'framed-quote',
		'shadow'    => 'shadow-quote',
		'rule'      => 'rule-quote',
	);
}

/**
 * CSS class for a quote style key.
 *
 * @param string $style Style key.
 * @return string
 */
function andreian_blockquote_style_class( $style ) {
	$map  = andreian_blockquote_style_map();
	$key  = strtolower( trim( (string) $style ) );
	return isset( $map[ $key ] ) ? $map[ $key ] : 'pullquote';
}

/**
 * Render a styled blockquote.
 *
 * [blockquote quote="…" author="…" style="framed"]
 * [blockquote author="…" style="shadow"]Quote text[/blockquote]
 *
 * @param array       $atts    Shortcode attributes.
 * @param string|null $content Enclosed content.
 * @return string
 */
function andreian_blockquote_shortcode( $atts, $content = null ) {
	$atts = shortcode_atts(
		array(
			'quote'  => '',
			'author' => '',
			'style'  => 'pull',
		),
		$atts,
		'blockquote'
	);

	$has_quote_attr = '' !== trim( (string) $atts['quote'] );
	$quote          = $has_quote_attr ? trim( (string) $atts['quote'] ) : trim( (string) $content );

	if ( '' === $quote ) {
		return '';
	}

	$quote_html = $has_quote_attr ? esc_html( $quote ) : wp_kses_post( $quote );
	$author     = trim( (string) $atts['author'] );
	$class      = andreian_blockquote_style_class( $atts['style'] );

	ob_start();
	?>
	<blockquote class="<?php echo esc_attr( $class ); ?>">
		<?php echo $quote_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped or kses'd above. ?>
		<?php if ( '' !== $author ) : ?>
			<cite><?php echo esc_html( $author ); ?></cite>
		<?php endif; ?>
	</blockquote>
	<?php

	return (string) ob_get_clean();
}
add_shortcode( 'blockquote', 'andreian_blockquote_shortcode' );

<?php
/**
 * Frontend render for the Linkbank Section block.
 *
 * Expects $attributes (and optionally $block_wrapper_attributes) provided by the
 * render callback in register-linkbank-section-block.php.
 *
 * @var array  $attributes
 * @var string $block_wrapper_attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();

$eyebrow_text = isset( $attributes['eyebrowText'] ) ? $attributes['eyebrowText'] : '';
$heading      = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$description  = isset( $attributes['description'] ) ? $attributes['description'] : '';
$links        = isset( $attributes['links'] ) && is_array( $attributes['links'] ) ? $attributes['links'] : array();

// Allowed inline formatting for RichText-authored fields.
$inline_allowed = array(
	'span'   => array( 'class' => array() ),
	'strong' => array(),
	'em'     => array(),
	'br'     => array(),
);

// Heading supports the core Highlight (text color) format, which outputs
// <mark>/<span> with has-*-color classes and inline color styles.
$heading_allowed = array(
	'span'   => array(
		'class' => array(),
		'style' => array(),
	),
	'mark'   => array(
		'class' => array(),
		'style' => array(),
	),
	'strong' => array(),
	'em'     => array(),
	'br'     => array(),
);

// Keep only links that have a label.
$links = array_filter(
	$links,
	function ( $link ) {
		return ! empty( $link['label'] );
	}
);
$links = array_values( $links );

// Nothing meaningful to render.
if ( empty( $links ) && empty( $eyebrow_text ) && empty( $heading ) && empty( $description ) ) {
	return;
}

$wrapper_attributes = isset( $block_wrapper_attributes ) ? $block_wrapper_attributes : 'class="linkbank-section"';
?>

<div class="_container">
	<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<div class="linkbank-section__inner">
			<div class="linkbank-section__intro _text -align-left">
				<?php if ( $eyebrow_text ) : ?>
					<span class="_eyebrow -plain linkbank-section__eyebrow"><?php echo wp_kses( $eyebrow_text, $inline_allowed ); ?></span>
				<?php endif; ?>

				<?php if ( $heading ) : ?>
					<h2 class="linkbank-section__heading _text -secondary"><?php echo wp_kses( $heading, $heading_allowed ); ?></h2>
				<?php endif; ?>

				<?php if ( $description ) : ?>
					<p class="linkbank-section__text _text -muted _text-size -xl"><?php echo wp_kses( $description, $inline_allowed ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $links ) ) : ?>
				<ul class="linkbank-section__links">
					<?php foreach ( $links as $link ) : ?>
						<?php
						$link_url   = isset( $link['url'] ) ? $link['url'] : '';
						$link_blank = ! empty( $link['new_tab'] );
						?>
						<li class="linkbank-section__link-item">
							<a
								class="linkbank-section__link"
								href="<?php echo esc_url( $link_url ? $link_url : '#' ); ?>"
								<?php if ( $link_blank ) { ?>target="_blank" rel="noopener"<?php } ?>>
								<?php echo esc_html( $link['label'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</section>
</div>
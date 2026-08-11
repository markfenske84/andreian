<?php
/**
 * Frontend render for the Awards & Recognition block.
 *
 * Expects $attributes (and optionally $block_wrapper_attributes) provided by the
 * render callback in register-awards-block.php.
 *
 * @var array  $attributes
 * @var string $block_wrapper_attributes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$attributes = isset( $attributes ) && is_array( $attributes ) ? $attributes : array();

$eyebrow = isset( $attributes['eyebrow'] ) ? $attributes['eyebrow'] : '';
$heading = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$intro   = isset( $attributes['intro'] ) ? $attributes['intro'] : '';
$logos   = isset( $attributes['logos'] ) && is_array( $attributes['logos'] ) ? $attributes['logos'] : array();

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

$logos = array_filter(
	$logos,
	function ( $logo ) {
		if ( ! is_array( $logo ) ) {
			return false;
		}
		$id  = isset( $logo['id'] ) ? absint( $logo['id'] ) : 0;
		$url = isset( $logo['url'] ) ? (string) $logo['url'] : '';
		return $id > 0 || '' !== $url;
	}
);

// Nothing meaningful to render.
if ( empty( $logos ) && empty( $eyebrow ) && empty( $heading ) && empty( $intro ) ) {
	return;
}

$wrapper_attributes = isset( $block_wrapper_attributes ) ? $block_wrapper_attributes : '';
?>

<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="awards-recognition__topline" aria-hidden="true"></div>

	<div class="_container">

		<div class="awards-recognition__header _text -align-center">
			<?php if ( $eyebrow ) : ?>
				<span class="_eyebrow -on-light _text -tertiary">
					<span class="_eyebrow__icon _text -primary">
						<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
							<path d="M12 2a5 5 0 0 1 5 5 5 5 0 0 1-3 4.58V13l1.7 6.3a.5.5 0 0 1-.66.6L12 18.5l-3.04 1.4a.5.5 0 0 1-.66-.6L10 13v-1.42A5 5 0 0 1 7 7a5 5 0 0 1 5-5Zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/>
						</svg>
					</span>
					<span class="_eyebrow__label"><?php echo wp_kses( $eyebrow, $inline_allowed ); ?></span>
				</span>
			<?php endif; ?>

			<?php if ( $heading ) : ?>
				<h2 class="awards-recognition__heading _text -secondary _text-style -h4"><?php echo wp_kses( $heading, $heading_allowed ); ?></h2>
			<?php endif; ?>

			<?php if ( $intro ) : ?>
				<p class="awards-recognition__intro _text -muted _text-size -lg"><?php echo wp_kses( $intro, $inline_allowed ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $logos ) ) : ?>
			<div class="awards-recognition__gallery">
				<ul class="awards-recognition__logos">
					<?php foreach ( $logos as $logo ) : ?>
						<?php
						$attachment_id = isset( $logo['id'] ) ? absint( $logo['id'] ) : 0;
						$url           = isset( $logo['url'] ) ? (string) $logo['url'] : '';
						$alt           = isset( $logo['alt'] ) ? (string) $logo['alt'] : '';
						?>
						<li class="awards-recognition__logo">
							<?php if ( $attachment_id && wp_attachment_is_image( $attachment_id ) ) : ?>
								<?php
								echo wp_get_attachment_image(
									$attachment_id,
									'medium',
									false,
									array(
										'loading' => 'lazy',
										'class'   => 'awards-recognition__logo-img',
									)
								); // phpcs:ignore WordPress.Security.EscapeOutput
								?>
							<?php elseif ( $url ) : ?>
								<img
									class="awards-recognition__logo-img"
									src="<?php echo esc_url( $url ); ?>"
									alt="<?php echo esc_attr( $alt ); ?>"
									loading="lazy" />
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<?php
		if ( function_exists( 'chw_render_chw_legal_block' ) ) {
			echo chw_render_chw_legal_block( array( 'textStyle' => 'dark' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		?>

	</div>
</section>

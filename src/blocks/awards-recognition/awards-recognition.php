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

$eyebrow         = isset( $attributes['eyebrow'] ) ? $attributes['eyebrow'] : '';
$heading         = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$intro           = isset( $attributes['intro'] ) ? $attributes['intro'] : '';
$featured        = isset( $attributes['featured'] ) && is_array( $attributes['featured'] ) ? $attributes['featured'] : array();
$supporting      = isset( $attributes['supporting'] ) && is_array( $attributes['supporting'] ) ? $attributes['supporting'] : array();
$show_additional = ! empty( $attributes['showAdditional'] );

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

/**
 * Returns true when at least one meaningful field of an item is populated.
 */
$item_has_content = function ( $item, $fields ) {
	foreach ( $fields as $field ) {
		if ( ! empty( $item[ $field ] ) ) {
			return true;
		}
	}
	return false;
};

$featured = array_filter(
	$featured,
	function ( $item ) use ( $item_has_content ) {
		return $item_has_content( $item, array( 'imageUrl', 'title', 'source', 'year' ) );
	}
);

$supporting = array_filter(
	$supporting,
	function ( $item ) use ( $item_has_content ) {
		return $item_has_content( $item, array( 'imageUrl', 'wordmark', 'title', 'caption' ) );
	}
);

// Nothing meaningful to render.
if ( empty( $featured ) && empty( $eyebrow ) && empty( $heading ) && empty( $intro ) ) {
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

		<?php if ( ! empty( $featured ) ) : ?>
			<div class="awards-recognition__featured">
				<?php foreach ( $featured as $award ) : ?>
					<article class="awards-recognition__card _flex -column -align-center _text -align-center">
						<div class="awards-recognition__card-media _flex -align-center -justify-center">
							<?php if ( ! empty( $award['imageUrl'] ) ) : ?>
								<img
									src="<?php echo esc_url( $award['imageUrl'] ); ?>"
									alt="<?php echo esc_attr( isset( $award['imageAlt'] ) ? $award['imageAlt'] : '' ); ?>"
									width="140"
									height="80"
									loading="lazy" />
							<?php endif; ?>
						</div>
						<div class="awards-recognition__card-rule"></div>
						<?php if ( ! empty( $award['title'] ) ) : ?>
							<p class="awards-recognition__card-title _text -tertiary _text-size -base"><?php echo wp_kses( $award['title'], $inline_allowed ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $award['source'] ) ) : ?>
							<p class="awards-recognition__card-source _text -secondary _text-size -sm"><?php echo wp_kses( $award['source'], $inline_allowed ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $award['year'] ) ) : ?>
							<p class="awards-recognition__card-year _text -muted -transform-uppercase _text-size -sm"><?php echo wp_kses( $award['year'], $inline_allowed ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( $show_additional && ! empty( $supporting ) ) : ?>
			<div class="awards-recognition__divider _flex -align-center">
				<span class="_text -muted -transform-uppercase _text-size -sm"><?php esc_html_e( 'Additional recognition', 'chw' ); ?></span>
			</div>

			<div
				class="awards-recognition__supporting-wrap"
				data-count="<?php echo esc_attr( count( $supporting ) ); ?>">
				<div
					class="awards-recognition__supporting"
					data-count="<?php echo esc_attr( count( $supporting ) ); ?>">
				<?php foreach ( $supporting as $item ) : ?>
					<figure class="awards-recognition__item _flex -align-center">
						<div class="awards-recognition__item-media _flex -align-center -justify-center">
							<?php if ( ! empty( $item['imageUrl'] ) ) : ?>
								<img
									src="<?php echo esc_url( $item['imageUrl'] ); ?>"
									alt="<?php echo esc_attr( isset( $item['imageAlt'] ) ? $item['imageAlt'] : '' ); ?>"
									width="80"
									height="40"
									loading="lazy" />
							<?php elseif ( ! empty( $item['wordmark'] ) ) : ?>
								<span class="awards-recognition__item-wordmark _text -tertiary _text-size -xl"><?php echo wp_kses( $item['wordmark'], $inline_allowed ); ?></span>
							<?php endif; ?>
						</div>
						<figcaption class="awards-recognition__item-caption">
							<?php if ( ! empty( $item['title'] ) ) : ?>
								<span class="awards-recognition__item-title _text -tertiary _text-size -sm"><?php echo wp_kses( $item['title'], $inline_allowed ); ?></span>
							<?php endif; ?>
							<?php if ( ! empty( $item['caption'] ) ) : ?>
								<span class="awards-recognition__item-text _text -muted _text-size -sm"><?php echo wp_kses( $item['caption'], $inline_allowed ); ?></span>
							<?php endif; ?>
						</figcaption>
					</figure>
				<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php
		if ( function_exists( 'chw_render_chw_legal_block' ) ) {
			echo chw_render_chw_legal_block( array( 'textStyle' => 'dark' ) ); // phpcs:ignore WordPress.Security.EscapeOutput
		}
		?>

	</div>
</section>
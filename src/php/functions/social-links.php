<?php
/**
 * Social link helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Supported social platforms mapped to SVG assets.
 *
 * @return array<string, array{label: string, icon: string}>
 */
function andreian_social_platforms() {
	return array(
		'facebook' => array(
			'label' => __( 'Facebook', 'andreian' ),
			'icon'  => 'ico-facebook',
		),
		'x'        => array(
			'label' => __( 'X', 'andreian' ),
			'icon'  => 'ico-x',
		),
		'instagram' => array(
			'label' => __( 'Instagram', 'andreian' ),
			'icon'  => 'ico-instagram',
		),
		'linkedin' => array(
			'label' => __( 'LinkedIn', 'andreian' ),
			'icon'  => 'ico-linkedin',
		),
	);
}

/**
 * Platform labels for Customizer select options.
 *
 * @return array<string, string>
 */
function andreian_social_platform_labels() {
	$labels = array();

	foreach ( andreian_social_platforms() as $platform => $details ) {
		$labels[ $platform ] = $details['label'];
	}

	return $labels;
}

/**
 * Sanitize the JSON-encoded list of social links.
 *
 * @param string $value Raw JSON from the control.
 * @return string
 */
function andreian_sanitize_social_links_json( $value ) {
	$decoded = json_decode( (string) $value, true );

	if ( ! is_array( $decoded ) ) {
		return '';
	}

	$platforms = andreian_social_platforms();
	$clean     = array();

	foreach ( $decoded as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$platform = isset( $row['platform'] ) ? sanitize_key( $row['platform'] ) : '';
		$url      = isset( $row['url'] ) ? esc_url_raw( $row['url'] ) : '';

		if ( ! isset( $platforms[ $platform ] ) || empty( $url ) ) {
			continue;
		}

		$clean[] = array(
			'platform' => $platform,
			'url'      => $url,
		);
	}

	return wp_json_encode( $clean );
}

/**
 * Return configured social links.
 *
 * @return array<int, array{platform: string, label: string, url: string, icon: string}>
 */
function andreian_get_social_links() {
	$value = get_theme_mod( 'social_links', '' );

	if ( empty( $value ) ) {
		return array();
	}

	$decoded = json_decode( $value, true );

	if ( ! is_array( $decoded ) ) {
		return array();
	}

	$platforms = andreian_social_platforms();
	$links     = array();

	foreach ( $decoded as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$platform = isset( $row['platform'] ) ? (string) $row['platform'] : '';
		$url      = isset( $row['url'] ) ? (string) $row['url'] : '';

		if ( ! isset( $platforms[ $platform ] ) || empty( $url ) ) {
			continue;
		}

		$links[] = array(
			'platform' => $platform,
			'label'    => $platforms[ $platform ]['label'],
			'url'      => $url,
			'icon'     => $platforms[ $platform ]['icon'],
		);
	}

	return $links;
}

/**
 * Render social profile links as SVG icons.
 *
 * @param array $args {
 *     Optional render arguments.
 *
 *     @type string $class Extra classes for the links wrapper.
 * }
 * @return string
 */
function andreian_render_social_links( $args = array() ) {
	$links = andreian_get_social_links();

	if ( empty( $links ) ) {
		return '';
	}

	$args = wp_parse_args(
		$args,
		array(
			'class' => '',
		)
	);

	$extra_class = isset( $args['class'] ) ? sanitize_html_class( $args['class'] ) : '';
	$classes       = trim( 'social-links' . ( $extra_class ? ' ' . $extra_class : '' ) );
	$site    = get_bloginfo( 'name', 'display' );

	ob_start();
	?>
	<div class="<?php echo esc_attr( $classes ); ?>">
		<?php foreach ( $links as $link ) : ?>
			<a
				class="social-links__item"
				href="<?php echo esc_url( $link['url'] ); ?>"
				target="_blank"
				rel="noopener noreferrer">
				<span class="sr-only">
					<?php
					echo esc_html(
						sprintf(
							/* translators: 1: site name, 2: social platform label */
							__( 'Visit %1$s on %2$s', 'andreian' ),
							$site,
							$link['label']
						)
					);
					?>
				</span>
				<?php
				$icon = svg( $link['icon'], 'social-links__icon' );

				if ( $icon ) {
					echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitized SVG helper output.
				}
				?>
			</a>
		<?php endforeach; ?>
	</div>
	<?php

	return (string) ob_get_clean();
}

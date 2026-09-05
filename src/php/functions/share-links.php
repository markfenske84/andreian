<?php
/**
 * Share link helpers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Supported share platforms.
 *
 * @param string $permalink Post URL.
 * @param string $title     Post title.
 * @return array<string, array{label: string, url: string, icon: string, class: string, external: bool}>
 */
function andreian_share_platforms( $permalink, $title ) {
	$share_url  = rawurlencode( $permalink );
	$share_text = rawurlencode( wp_strip_all_tags( $title ) );

	return array(
		'facebook' => array(
			'label'    => __( 'Facebook', 'andreian' ),
			'url'      => 'https://www.facebook.com/sharer/sharer.php?u=' . $share_url,
			'icon'     => 'ico-share-facebook',
			'class'    => 'share-link share-link--facebook',
			'external' => true,
		),
		'x'        => array(
			'label'    => __( 'X', 'andreian' ),
			'url'      => 'https://x.com/intent/post?url=' . $share_url . '&text=' . $share_text,
			'icon'     => 'ico-share-x',
			'class'    => 'share-link share-link--x',
			'external' => true,
		),
		'email'    => array(
			'label'    => __( 'Email', 'andreian' ),
			'url'      => 'mailto:?subject=' . $share_text . '&body=' . $share_url,
			'icon'     => 'ico-share-email',
			'class'    => 'share-link share-link--email',
			'external' => false,
		),
		'telegram' => array(
			'label'    => __( 'Telegram', 'andreian' ),
			'url'      => 'https://t.me/share/url?url=' . $share_url . '&text=' . $share_text,
			'icon'     => 'ico-share-telegram',
			'class'    => 'share-link share-link--telegram',
			'external' => true,
		),
		'flipboard' => array(
			'label'    => __( 'Flipboard', 'andreian' ),
			'url'      => 'https://share.flipboard.com/bookmarklet/popout?v=2&url=' . $share_url . '&title=' . $share_text,
			'icon'     => 'ico-share-flipboard',
			'class'    => 'share-link share-link--flipboard',
			'external' => true,
		),
	);
}

/**
 * Render a share link with a colored brand icon.
 *
 * @param array  $platform Platform config from andreian_share_platforms().
 * @param string $context  Optional BEM context class prefix.
 * @return string
 */
function andreian_render_share_link( $platform, $context = 'share-link' ) {
	$icon = svg( $platform['icon'], 'share-link__icon ' . $context . '__icon' );

	if ( ! $icon ) {
		return '';
	}

	$class   = $platform['class'];
	$is_mail = 0 === stripos( $platform['url'], 'mailto:' );
	$rel     = ! empty( $platform['external'] ) ? 'nofollow noopener' : '';
	$target  = ! empty( $platform['external'] ) ? '_blank' : '';
	$href    = $is_mail
		? esc_attr( esc_url( $platform['url'], array( 'mailto' ), 'db' ) )
		: esc_url( $platform['url'] );

	ob_start();
	?>
	<a
		class="<?php echo esc_attr( $class ); ?>"
		href="<?php echo $href; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?>"
		<?php if ( $target ) : ?>
			target="<?php echo esc_attr( $target ); ?>"
		<?php endif; ?>
		<?php if ( $rel ) : ?>
			rel="<?php echo esc_attr( $rel ); ?>"
		<?php endif; ?>>
		<span class="sr-only"><?php echo esc_html( $platform['label'] ); ?></span>
		<?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitized SVG helper output. ?>
	</a>
	<?php

	return (string) ob_get_clean();
}

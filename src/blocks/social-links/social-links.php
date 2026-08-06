<?php
/**
 * Frontend render for the Social Links block.
 *
 * Pulls the global links managed in the Customizer (Social Links section).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$social_links = chw_get_social_links();

if ( empty( $social_links ) ) {
	return;
}
?>

<div class="social-links">

	<?php foreach ( $social_links as $social_link ) : ?>
		<a
			href="<?php echo esc_url( $social_link['url'] ); ?>"
			target="_blank"
			rel="noopener noreferrer">
			<span class="sr-only"><?php echo esc_html( sprintf( __( 'Visit %1$s on %2$s', 'chw' ), get_bloginfo( 'name', 'display' ), $social_link['label'] ) ); ?></span>
			<i class="fa-brands fa-<?php echo esc_attr( $social_link['platform'] ); ?>" aria-hidden="true"></i>
		</a>
	<?php endforeach; ?>

</div>

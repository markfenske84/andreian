<?
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Hide email from Spam Bots using a shortcode.
 *
 * @param array  $atts    Shortcode attributes. Not used.
 * @param string $content The shortcode content. Should be an email address.
 *
 * @return string The obfuscated email address. 
 */
function wpcodex_hide_email_shortcode( $atts , $content = null ) {
	if ( ! is_email( $content ) ) {
		return;
	}

	$content = antispambot( $content );
	$email_link = sprintf( 'mailto:%s', $content );
	return sprintf( '<a href="%s">%s</a>', esc_url( $email_link, array( 'mailto' ) ), esc_html( $content ) );
}
add_shortcode( 'email', 'wpcodex_hide_email_shortcode' );
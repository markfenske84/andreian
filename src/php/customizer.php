<?php
/**
 * Initialization function for the Webfor theme.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Return a sanitized hex color from the Customizer, with fallback.
 */
function chw_get_hex_theme_mod( $name, $default = '#333333' ) {
    $color = get_theme_mod( $name, $default );
    $sanitized = sanitize_hex_color( $color );

    return $sanitized ? $sanitized : $default;
}

function chw_customizer_sections($wp_customize) {

    include_once 'customizer/site-logos.php';
    include_once 'customizer/layout-settings.php';
    include_once 'customizer/legal-text.php';
    include_once 'customizer/masthead-settings.php';
    include_once 'customizer/as-seen-in.php';
    include_once 'customizer/announcement-bar.php';
    include_once 'customizer/blog-settings.php';
    include_once 'customizer/social-links.php';
    
}

add_action('customize_register', 'chw_customizer_sections');

/**
 * Live-update announcement bar colors in the Customizer preview.
 */
function chw_customize_preview_init() {
    wp_add_inline_script(
        'customize-preview',
        "(function () {
            function setAnnouncementBarColors() {
                var bar = document.getElementById('announcement-bar');
                if (!bar) return;

                var bg = wp.customize('announcement_bar_background_color').get() || '#333333';
                var text = wp.customize('announcement_bar_text_color').get() || '#ffffff';

                document.documentElement.style.setProperty('--announcement-bar-background-color', bg);
                document.documentElement.style.setProperty('--announcement-bar-text-color', text);
                bar.style.backgroundColor = bg;
                bar.style.color = text;
            }

            wp.customize('announcement_bar_background_color', function (value) {
                value.bind(setAnnouncementBarColors);
            });

            wp.customize('announcement_bar_text_color', function (value) {
                value.bind(setAnnouncementBarColors);
            });
        })();"
    );
}
add_action( 'customize_preview_init', 'chw_customize_preview_init' );

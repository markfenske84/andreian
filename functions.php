<?php
/**
 * Theme bootstrap.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 */

define( 'THEME_ASSETS', get_template_directory() . '/assets' );
define( 'THEME_IMAGES', get_template_directory_uri() . '/assets/images' );
define( 'THEME_SVGS', get_template_directory_uri() . '/assets/svg' );

require_once 'src/php/andreian.php';
include_once 'src/php/theme.php';
include_once 'src/php/customizer.php';

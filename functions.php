<?
/**
 * The main PHP file to initialize all other PHP files. Functions should be written within /src/php 
 * and included here.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 */

// Definitions
define('THEME_ASSETS', get_template_directory() . '/assets');
define('THEME_FONTS', get_template_directory_uri() . '/assets/fonts');
define('THEME_IMAGES', get_template_directory_uri() . '/assets/images');
define('THEME_SVGS', get_template_directory_uri() . '/assets/svg');

// Includes
require_once( 'src/php/krypton.php' ); // Core Theme Files
include_once( 'src/php/theme.php' ); // Theme Specific
include_once( 'src/php/customizer.php' ); // Customizer Specific
include_once( 'src/blocks/blocks.php' ); // Include Blocks
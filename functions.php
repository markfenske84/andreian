<?php
/**
 * The main PHP file to initialize all other PHP files. Functions should be written within /src/php 
 * and included here.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 */

// Definitions
define('THEME_ASSETS', get_template_directory() . '/assets');
define('THEME_IMAGES', get_template_directory_uri() . '/assets/images');
define('THEME_SVGS', get_template_directory_uri() . '/assets/svg');

// Includes
require_once( 'src/php/chw.php' ); // Core Theme Files
include_once( 'src/php/theme.php' ); // Theme Specific
include_once( 'src/php/customizer.php' ); // Customizer Specific
include_once( 'src/blocks/blocks.php' ); // Include Blocks
include_once( 'src/blocks/awards-recognition/register-awards-block.php' ); // Native Awards & Recognition block
include_once( 'src/blocks/steps-section/register-steps-block.php' ); // Native Steps Section block
include_once( 'src/blocks/plans-section/register-plans-block.php' ); // Native Plans Section block
include_once( 'src/blocks/comparison-table/register-comparison-table-block.php' ); // Native Comparison Table block
include_once( 'src/blocks/tabbed-comparison-table/register-tabbed-comparison-table-block.php' ); // Native Tabbed Comparison Table block
include_once( 'src/blocks/highlight-cards/register-highlight-cards-block.php' ); // Native Highlight Cards block
include_once( 'src/blocks/highlights-list/register-highlights-list-block.php' ); // Native Highlights List block
include_once( 'src/blocks/video-modal/register-video-modal-block.php' ); // Native Video Modal block
include_once( 'src/blocks/accordions/register-accordions-block.php' ); // Native Accordions blocks
include_once( 'src/blocks/tabbed-panes/register-tabbed-panes-block.php' ); // Native Tabbed Panes blocks
include_once( 'src/blocks/fullwidth-halfscreen/register-fullwidth-halfscreen-block.php' ); // Native Fullwidth Halfscreen block
include_once( 'src/blocks/social-links/register-social-links-block.php' ); // Native Social Links block
include_once( 'src/blocks/badge/register-badge-block.php' ); // Native Badge block
include_once( 'src/blocks/chw-buttons/register-chw-buttons-block.php' ); // Native CHW Buttons block
include_once( 'src/blocks/chw-legal/register-chw-legal-block.php' ); // Native CHW Legal block
include_once( 'src/blocks/stats-testimonials/register-stats-testimonials-block.php' ); // Native Stats and Testimonials block
include_once( 'src/blocks/cta-highlights/register-cta-highlights-block.php' ); // Native Call To Action: Highlights block
include_once( 'src/blocks/cta-banner/register-cta-banner-block.php' ); // Native Call To Action: Banner block
include_once( 'src/blocks/cta-image-banner/register-cta-image-banner-block.php' ); // Native Call To Action: Image Banner block
include_once( 'src/blocks/cta-block/register-cta-block-block.php' ); // Native CTA Block
include_once( 'src/blocks/cta-subtle/register-cta-subtle-block.php' ); // Native Call To Action: Subtle block
include_once( 'src/blocks/audience-section/register-audience-block.php' ); // Native Audience Section block
include_once( 'src/blocks/linkbank-section/register-linkbank-section-block.php' ); // Native Linkbank Section block
include_once( 'src/blocks/quick-actions/register-quick-actions-block.php' ); // Native Quick Actions block
include_once( 'src/blocks/fullwidth-cover-section/register-fullwidth-cover-section-block.php' ); // Native Fullwidth Cover Section block
include_once( 'src/blocks/testimonials-slider/register-testimonials-slider-block.php' ); // Native Testimonials Slider block
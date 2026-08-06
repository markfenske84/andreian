<?php
/**
 * Social Links Customizer section registration.
 *
 * Helper functions live in src/php/functions/social-links.php (always loaded).
 */

require_once get_template_directory() . '/src/php/customizer/controls/class-chw-social-links-control.php';

$wp_customize->add_section( 'social_links_section', array(
	'title'    => __( 'Social Links', 'chw' ),
	'priority' => 38,
) );

$wp_customize->add_setting( 'social_links', array(
	'default'           => '',
	'sanitize_callback' => 'chw_sanitize_social_links_json',
) );

$wp_customize->add_control( new CHW_Social_Links_Control( $wp_customize, 'social_links', array(
	'label'       => __( 'Social Links', 'chw' ),
	'section'     => 'social_links_section',
	'settings'    => 'social_links',
	'description' => __( 'Add your social profiles. Choose a platform and paste its URL. Drag to reorder.', 'chw' ),
	'platforms'   => chw_social_platforms(),
) ) );

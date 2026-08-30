<?php
/**
 * Social Links Customizer section.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/src/php/customizer/controls/class-andreian-social-links-control.php';

$wp_customize->add_section(
	'social_links_section',
	array(
		'title'    => __( 'Social Links', 'andreian' ),
		'priority' => 38,
	)
);

$wp_customize->add_setting(
	'social_links',
	array(
		'default'           => '',
		'sanitize_callback' => 'andreian_sanitize_social_links_json',
	)
);

$wp_customize->add_control(
	new Andreian_Social_Links_Control(
		$wp_customize,
		'social_links',
		array(
			'label'       => __( 'Social Links', 'andreian' ),
			'section'     => 'social_links_section',
			'settings'    => 'social_links',
			'description' => __( 'Add your social profiles. Choose a platform and paste its URL. Drag to reorder.', 'andreian' ),
			'platforms'   => andreian_social_platform_labels(),
		)
	)
);

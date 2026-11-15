<?php
/**
 * Random slideshow Customizer section.
 *
 * Appearance → Customize → Random Slideshow.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$config = andreian_random_slideshow_speed_config();

$wp_customize->add_section(
	'andreian_random_slideshow',
	array(
		'title'       => __( 'Random Slideshow', 'andreian' ),
		'description' => __( 'The Random slideshow in the Latest Entries sidebar.', 'andreian' ),
		'priority'    => 30,
	)
);

$wp_customize->add_setting(
	'random_slideshow_speed',
	array(
		'default'           => $config['default'],
		'sanitize_callback' => 'andreian_sanitize_random_slideshow_speed',
	)
);

$wp_customize->add_control(
	'random_slideshow_speed',
	array(
		'label'       => __( 'Rotation speed (seconds)', 'andreian' ),
		'description' => __( 'How long each slide stays visible before advancing. Whole seconds from 1 to 60. Default is 5, the speed the slideshow already uses.', 'andreian' ),
		'section'     => 'andreian_random_slideshow',
		'settings'    => 'random_slideshow_speed',
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => $config['min'],
			'max'  => $config['max'],
			'step' => 1,
		),
	)
);

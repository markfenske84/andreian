<?php
// Masthead section — global content used by the masthead component (award seal).
$wp_customize->add_section('masthead_settings_section', array(
    'title'    => __('Masthead', 'chw'),
    'priority' => 31,
));

// Award seal image.
$wp_customize->add_setting('masthead_award_image', array(
    'default'           => '',
    'sanitize_callback' => 'esc_url_raw',
));

$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'masthead_award_image', array(
    'label'    => __('Award Seal Image', 'chw'),
    'section'  => 'masthead_settings_section',
    'settings' => 'masthead_award_image',
)));

// Award eyebrow (e.g. "AWARDED 2025-26").
$wp_customize->add_setting('masthead_award_eyebrow', array(
    'default'           => '',
    'sanitize_callback' => 'sanitize_text_field',
));

$wp_customize->add_control('masthead_award_eyebrow', array(
    'label'    => __('Award Eyebrow', 'chw'),
    'section'  => 'masthead_settings_section',
    'settings' => 'masthead_award_eyebrow',
    'type'     => 'text',
));

// Award title (e.g. "Best Home Warranty Company").
$wp_customize->add_setting('masthead_award_title', array(
    'default'           => '',
    'sanitize_callback' => 'sanitize_text_field',
));

$wp_customize->add_control('masthead_award_title', array(
    'label'    => __('Award Title', 'chw'),
    'section'  => 'masthead_settings_section',
    'settings' => 'masthead_award_title',
    'type'     => 'text',
));

// Award source (e.g. "U.S. News & World Report").
$wp_customize->add_setting('masthead_award_source', array(
    'default'           => '',
    'sanitize_callback' => 'sanitize_text_field',
));

$wp_customize->add_control('masthead_award_source', array(
    'label'    => __('Award Source', 'chw'),
    'section'  => 'masthead_settings_section',
    'settings' => 'masthead_award_source',
    'type'     => 'text',
));

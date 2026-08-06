<?php
// Legal Text section — global legal disclaimer reused across the site (masthead, footer, etc.).
$wp_customize->add_section('legal_text_section', array(
    'title'    => __('Legal Text', 'chw'),
    'priority' => 30,
));

$wp_customize->add_setting('legal_text', array(
    'default'           => '',
    'sanitize_callback' => 'wp_kses_post',
));

$wp_customize->add_control('legal_text', array(
    'label'       => __('Legal Text', 'chw'),
    'section'     => 'legal_text_section',
    'settings'    => 'legal_text',
    'description' => __('Global legal disclaimer. Pulled into the masthead (when enabled per page) and other site areas.', 'chw'),
    'type'        => 'textarea',
));

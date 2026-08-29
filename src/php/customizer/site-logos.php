<?php 
// Logo
$wp_customize->add_setting('custom_logo', array(
    'default'           => '',
    'sanitize_callback' => 'esc_url_raw',
));

$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'custom_logo', array(
    'label'    => __('Main Logo', 'andreian'),
    'section'  => 'title_tagline',
    'settings' => 'custom_logo',
)));

// Uncomment and/or duplicate if you need additional Logo alternatives

// $wp_customize->add_setting('home_logo', array(
//     'default'           => '',
//     'sanitize_callback' => 'esc_url_raw',
// ));

// $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'home_logo', array(
//     'label'    => __('Home Logo', 'andreian'),
//     'section'  => 'title_tagline',
//     'settings' => 'home_logo',
// )));
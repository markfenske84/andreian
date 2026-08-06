<?php 
// Add a new section for Layout Settings
$wp_customize->add_section('layout_settings_section', array(
    'title'    => __('Layout Settings', 'chw'),
    'priority' => 29,
));

$layout_option = array(
    'container_gutter' => __('Container Gutter', 'chw'),
    'container_width' => __('Container Width', 'chw'),
);

foreach ($layout_option as $option_name => $option_label) {
    $default_value = '';

    if($option_name === 'container_gutter') {
        $default_value = '16';
    } elseif ($option_name === 'container_width') {
        $default_value = '1440';
    }

    $wp_customize->add_setting($option_name, array(
        'default' => $default_value,
        'sanitize_callback' => 'absint', // Use 'absint' to ensure a positive integer
    ));

    $control_args = array(
        'label'    => $option_label,
        'section'  => 'layout_settings_section',
        'settings' => $option_name,
        'type' => 'number',
        'description' => __('Input values are in pixels (px).', 'chw'), // Provide a description
    );

    $wp_customize->add_control($option_name, $control_args);
}

/**
 * Mobile Menu Layout (dropdown vs slide-in panels)
 */

// Register the setting with safe default.
$wp_customize->add_setting( 'mobile_menu_layout', array(
    'default'           => 'dropdown',
    'sanitize_callback' => function ( $value ) {
        // Only allow the two expected values.
        return in_array( $value, array( 'dropdown', 'panel' ), true ) ? $value : 'dropdown';
    },
) );

// Add the control to choose layout.
$wp_customize->add_control( 'mobile_menu_layout', array(
    'label'   => __( 'Mobile Menu Layout', 'chw' ),
    'section' => 'layout_settings_section',
    'type'    => 'select',
    'choices' => array(
        'dropdown' => __( 'Expansion Dropdowns', 'chw' ),
        'panel'    => __( 'Slide-in Panels', 'chw' ),
    ),
) );
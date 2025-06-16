<? 
// Add a new section for Layout Settings
$wp_customize->add_section('layout_settings_section', array(
    'title'    => __('Layout Settings', 'krypton'),
    'priority' => 29,
));

$layout_option = array(
    'container_gutter' => __('Container Gutter', 'krypton'),
    'container_width' => __('Container Width', 'krypton'),
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
        'description' => __('Input values are in pixels (px).', 'krypton'), // Provide a description
    );

    $wp_customize->add_control($option_name, $control_args);
}
<?
// Add a new section for button styles
$wp_customize->add_section('button_styles_section', array(
    'title'    => __('Button Styles', 'krypton'),
    'priority' => 31, // Adjust priority to place it after color palette section
    'description' => __('Input values are in pixels (px).', 'krypton'), // Provide a description
));

// Define button style options
$button_style_options = array(
    'button_padding' => __('Button Padding', 'krypton'),
    'button_border_width' => __('Button Border Width', 'krypton'),
    'button_border_radius' => __('Button Border Radius', 'krypton'),
    'button_font_weight' => __('Button Font Weight', 'krypton'),
    'button_font_size' => __('Button Font Size', 'krypton'),
    'button_text_transform' => __('Button Text Transform', 'krypton'),
);

// Add settings and controls for button style options
foreach ($button_style_options as $option_name => $option_label) {
    $default_value = '';
    $sanitize_callback = '';

    if($option_name === 'button_padding') {
        $default_value = '.5rem 2rem';
        $sanitize_callback = 'sanitize_text_field';
    } elseif ($option_name === 'button_font_weight') {
        $default_value = '700';
    } elseif ($option_name === 'button_font_size') {
        $default_value = '18';
        $sanitize_callback = 'absint';
    } elseif ($option_name === 'button_border_width') {
        $default_value = '3';
        $sanitize_callback = 'absint';
    } elseif ($option_name === 'button_border_radius') {
        $default_value = '10';
        $sanitize_callback = 'absint';
    } elseif ($option_name === 'button_text_transform') {
        $default_value = 'Uppercase';
    }

    $wp_customize->add_setting($option_name, array(
        'default' => $default_value,
    ));

    if ($sanitize_callback !== '') {
        $setting_args['sanitize_callback'] = $sanitize_callback;
    }

    $control_args = array(
        'label'    => $option_label,
        'section'  => 'button_styles_section',
        'settings' => $option_name,
    );

    // Add specific controls for different options
    if ($option_name === 'button_font_weight') {
        $control_args['type'] = 'select';
        $control_args['choices'] = array(
            '100' => '100',
            '200' => '200',
            '300' => '300',
            '400' => '400',
            '500' => '500',
            '600' => '600',
            '700' => '700',
            '800' => '800',
            '900' => '900',
        );
    } elseif($option_name === 'button_padding') {
        $control_args['type'] = 'text';
    } elseif($option_name === 'button_text_transform') {
        $control_args['type'] = 'select';
        $control_args['choices'] = array(
            'uppercase' => 'Uppercase',
            'capitalize' => 'Capitalize',
            'lowercase' => 'Lowercase',
        );
    } else {
        $control_args['type'] = 'number'; // Change to number input
    }

    $wp_customize->add_control($option_name, $control_args);
}
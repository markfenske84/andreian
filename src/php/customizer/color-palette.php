<?
// Add a new section for color palette
$wp_customize->add_section('color_palette_section', array(
    'title'    => __('Color Palette', 'krypton'),
    'priority' => 30,
));

// Define color options
$color_options = array(
    'primary_color'   => __('Primary Color', 'krypton'),
    'secondary_color' => __('Secondary Color', 'krypton'),
    'tertiary_color'  => __('Tertiary Color', 'krypton'),
    'quaternary_color'=> __('Quaternary Color', 'krypton'),
    'text_color'=> __('Text Color', 'krypton'),
);

// Add settings and controls for color options
foreach ($color_options as $option_name => $option_label) {
    $wp_customize->add_setting($option_name, array(
        'default'           => '#3498db', // Adjust default color as needed
        'sanitize_callback' => 'sanitize_hex_color',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $option_name, array(
        'label'    => $option_label,
        'section'  => 'color_palette_section',
        'settings' => $option_name,
    )));
}
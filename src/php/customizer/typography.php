<? 
// Add a new section for typography
$wp_customize->add_section('typography_section', array(
    'title'    => __('Typography', 'krypton'),
    'priority' => 30.5, // Adjust priority as needed
));

// Add a setting for font URL
$wp_customize->add_setting('font_url_setting', array(
    'default'           => '',
    'sanitize_callback' => 'esc_url_raw',
));

$wp_customize->add_control('font_url_setting', array(
    'label'    => __('Font URL', 'krypton'),
    'section'  => 'typography_section',
    'settings' => 'font_url_setting',
    'description' => __('Enter the URL link for the theme fonts.', 'krypton'),
    'type'     => 'url',
));

// Add a setting for font URL
$wp_customize->add_setting('font_family_heading', array(
    'default' => '',
));

// add control for header font family setting
$wp_customize->add_control('font_family_heading', array(
    'label'    => __('Heading Font Family CSS', 'krypton'),
    'section'  => 'typography_section',
    'settings' => 'font_family_heading',
    'description' => __('Enter the font family CSS for the headings.', 'krypton'),
    'type'     => 'text',
));

// add control for body font family setting
$wp_customize->add_setting('font_family_body', array(
    'default' => '',
));

$wp_customize->add_control('font_family_body', array(
    'label'    => __('Body Font Family CSS', 'krypton'),
    'section'  => 'typography_section',
    'settings' => 'font_family_body',
    'description' => __('Enter the font family CSS for the body.', 'krypton'),
    'type'     => 'text',
));

// add control for base font size setting
$wp_customize->add_setting('base_font_size', array(
    'default' => '18',
    'sanitize_callback' => 'absint', // Use 'absint' to ensure a positive integer
));

$wp_customize->add_control('base_font_size', array(
    'label'    => __('Base Font Size', 'krypton'),
    'section'  => 'typography_section',
    'settings' => 'base_font_size',
    'description' => __('Input values are in pixels (px).', 'krypton'),
    'type'     => 'number',
));
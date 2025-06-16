<?
// add section for Announcement Bar
$wp_customize->add_section('announcement_bar_section', array(
    'title'    => __('Announcement Bar', 'krypton'),
    'priority' => 33, // Adjust priority as needed
));

// add setting for announcement bar text and allow html to be included in the text
$wp_customize->add_setting('announcement_bar_text', array(
    'default' => '',
    'sanitize_callback' => 'wp_kses_post',
));

$wp_customize->add_control('announcement_bar_text', array(
    'label'    => __('Announcement Bar Text', 'krypton'),
    'section'  => 'announcement_bar_section',
    'settings' => 'announcement_bar_text',
    'description' => __('Enter the text for the announcement bar.', 'krypton'),
    'type'     => 'textarea',
));

// add colorpicker for announcement bar background color
$wp_customize->add_setting('announcement_bar_background_color', array(
    'default' => '#3498db',
    'sanitize_callback' => 'sanitize_hex_color',
));

$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'announcement_bar_background_color', array(
    'label'    => __('Background Color', 'krypton'),
    'section'  => 'announcement_bar_section',
    'settings' => 'announcement_bar_background_color',
)));

// add colorpicker for announcement bar text color
$wp_customize->add_setting('announcement_bar_text_color', array(
    'default' => '#ffffff',
    'sanitize_callback' => 'sanitize_hex_color',
));

$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'announcement_bar_text_color', array(
    'label'    => __('Text Color', 'krypton'),
    'section'  => 'announcement_bar_section',
    'settings' => 'announcement_bar_text_color',
)));

// add dismissal reset selection option for announcement bar, options should be; 1 day, 1 week, 1 month, 1 year, forever.  default should be 1 day.
// Sanitize callback for announcement bar dismissal reset option
function webfor_sanitize_dismissal_reset($input) {
    $valid_values = array(
        '1 minute',
        '1 hour',
        '1 day',
        '1 week',
        '1 month',
        '1 year',
        'forever',
    );

    // If the selected value is valid, return it; otherwise, use the default value
    return in_array($input, $valid_values) ? $input : '1 day';
}

// Add dismissal reset selection option for announcement bar
$wp_customize->add_setting('announcement_bar_dismissal_reset', array(
    'default' => '1 day',
    'sanitize_callback' => 'webfor_sanitize_dismissal_reset',
));

$wp_customize->add_control('announcement_bar_dismissal_reset', array(
    'label'       => __('Dismissal Length', 'krypton'),
    'description' => __('The amount of time the bar will be dismissed when the user closes it.', 'krypton'),
    'section'     => 'announcement_bar_section',
    'settings'    => 'announcement_bar_dismissal_reset',
    'type'        => 'select',
    'choices'     => array(
        // '1 minute'   => __('1 Minute [TESTING]', 'krypton'),
        '1 hour'   => __('1 Hour', 'krypton'),
        '1 day'    => __('1 Day', 'krypton'),
        '1 week'   => __('1 Week', 'krypton'),
        '1 month'  => __('1 Month', 'krypton'),
        '1 year'   => __('1 Year', 'krypton'),
        'forever'  => __('Forever', 'krypton'),
    ),
));

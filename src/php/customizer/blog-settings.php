<? 
// add blog settings section
$wp_customize->add_section('blog_settings_section', array(
    'title'    => __('Blog Settings', 'krypton'),
    'priority' => 32, // Adjust priority as needed
));

// add blog masthead image setting
$wp_customize->add_setting('blog_masthead_image', array(
    'default' => '',
    'sanitize_callback' => 'esc_url_raw',
));
$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'blog_masthead_image', array(
    'label'    => __('Blog Masthead Image', 'krypton'),
    'section'  => 'blog_settings_section',
    'settings' => 'blog_masthead_image',
)));   

// blog mastead heading text
$wp_customize->add_setting('blog_masthead_heading', array(
    'default' => 'Blog',
    'sanitize_callback' => 'sanitize_text_field',
));

$wp_customize->add_control('blog_masthead_heading', array(
    'label'    => __('Blog Masthead Heading', 'krypton'),
    'section'  => 'blog_settings_section',
    'settings' => 'blog_masthead_heading',
    'description' => __('Enter the text to display as the heading on the blog masthead.', 'krypton'),
    'type'     => 'text',
));

// add setting for default featured image (fallback when no post featured image is set)
$wp_customize->add_setting('blog_default_featured_image', array(
    'default' => '',
    'sanitize_callback' => 'esc_url_raw',
));

$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'blog_default_featured_image', array(
    'label'    => __('Default Featured Image', 'krypton'),
    'section'  => 'blog_settings_section',
    'settings' => 'blog_default_featured_image',
    'description' => __('Image that will be used when a standard post does not have its own Featured Image.', 'krypton'),
)));

// add setting for blog sidebar positioning, this should be a dropdown select with left default
$wp_customize->add_setting('blog_sidebar_position', array(
    'default' => 'row-reverse',
));

$wp_customize->add_control('blog_sidebar_position', array(
    'label'    => __('Blog Sidebar Position', 'krypton'),
    'section'  => 'blog_settings_section',
    'settings' => 'blog_sidebar_position',
    'description' => __('Select the position of the sidebar on the blog page.  Sidebar will only display if there are Widgets set on the sidebar', 'krypton'),
    'type'     => 'select',
    'choices'  => array(
        'row-reverse' => 'Left',
        'row' => 'Right',
    ),
));
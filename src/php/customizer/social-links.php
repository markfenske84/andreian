<? 
// add section for Social Links
$wp_customize->add_section('social_links_section', array(
    'title'    => __('Social Links', 'krypton'),
    'priority' => 38, // Adjust priority as needed
));

// add field for Facebook URL
$wp_customize->add_setting('facebook_url', array(
    'default' => '',
    'sanitize_callback' => 'esc_url',
));

$wp_customize->add_control('facebook_url', array(
    'label'    => __('Facebook', 'krypton'),
    'section'  => 'social_links_section',
    'settings' => 'facebook_url',
    'description' => __('Enter the URL for your Facebook page.', 'krypton'),
    'type'     => 'text',
));

// add field for Twitter URL
$wp_customize->add_setting('twitter_url', array(
    'default' => '',
    'sanitize_callback' => 'esc_url',
));

$wp_customize->add_control('twitter_url', array(
    'label'    => __('X (Twitter)', 'krypton'),
    'section'  => 'social_links_section',
    'settings' => 'twitter_url',
    'description' => __('Enter the URL for your X (Twitter) page.', 'krypton'),
    'type'     => 'text',
));

// add field for Instagram URL
$wp_customize->add_setting('instagram_url', array(
    'default' => '',
    'sanitize_callback' => 'esc_url',
));

$wp_customize->add_control('instagram_url', array(
    'label'    => __('Instagram', 'krypton'),
    'section'  => 'social_links_section',
    'settings' => 'instagram_url',
    'description' => __('Enter the URL for your Instagram page.', 'krypton'),
    'type'     => 'text',
));

// add field for LinkedIn URL
$wp_customize->add_setting('linkedin_url', array(
    'default' => '',
    'sanitize_callback' => 'esc_url',
));

$wp_customize->add_control('linkedin_url', array(
    'label'    => __('LinkedIn', 'krypton'),
    'section'  => 'social_links_section',
    'settings' => 'linkedin_url',
    'description' => __('Enter the URL for your LinkedIn page.', 'krypton'),
    'type'     => 'text',
));

// add field for YouTube URL
$wp_customize->add_setting('youtube_url', array(
    'default' => '',
    'sanitize_callback' => 'esc_url',
));

$wp_customize->add_control('youtube_url', array(
    'label'    => __('YouTube', 'krypton'),
    'section'  => 'social_links_section',
    'settings' => 'youtube_url',
    'description' => __('Enter the URL for your YouTube page.', 'krypton'),
    'type'     => 'text',
));

// add field for Pinterest URL
$wp_customize->add_setting('pinterest_url', array(
    'default' => '',
    'sanitize_callback' => 'esc_url',
));

$wp_customize->add_control('pinterest_url', array(
    'label'    => __('Pinterest', 'krypton'),
    'section'  => 'social_links_section',
    'settings' => 'pinterest_url',
    'description' => __('Enter the URL for your Pinterest page.', 'krypton'),
    'type'     => 'text',
));

// add field for Yelp URL
$wp_customize->add_setting('yelp_url', array(
    'default' => '',
    'sanitize_callback' => 'esc_url',
));

$wp_customize->add_control('yelp_url', array(
    'label'    => __('Yelp', 'krypton'),
    'section'  => 'social_links_section',
    'settings' => 'yelp_url',
    'description' => __('Enter the URL for your Yelp page.', 'krypton'),
    'type'     => 'text',
));

// Copy, paste and update the following snippet to add additional social links as needed...

// $wp_customize->add_setting('example_url', array(
//     'default' => '',
//     'sanitize_callback' => 'esc_url',
// ));
//
// $wp_customize->add_control('example_url', array(
//     'label'    => __('Example', 'krypton'),
//     'section'  => 'social_links_section',
//     'settings' => 'example_url',
//     'description' => __('Enter the URL for your Example page.', 'krypton'),
//     'type'     => 'text',
// ));
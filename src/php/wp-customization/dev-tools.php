<?php 
/**
 * Initialization function for the Webfor theme.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// register a new admin settings page called "Developer Tools", hide after launch
function chw_developer_tools() {
    if (current_user_can('administrator')) {
        add_menu_page(
            'Developer Tools',
            'Developer Tools',
            'manage_options',
            'developer-tools',
            'chw_developer_tools_page',
            'dashicons-editor-code',
            80
        );
    }
}

add_action('admin_menu', 'chw_developer_tools');

// Function to render the developer tools page
function chw_developer_tools_page() {

    ?> 
        <style>
            .dev-tool-item {
                border: 1px solid rgba(0, 0, 0, 0.1);
                background-color: #f9f9f9;
                padding: 5px 20px 15px;
                margin-right: 20px;
                margin-bottom: 20px;
                border-radius: 8px;
            }

            .clipboard-notice {
                color: green;
                background-color: rgba(0, 128, 0, 0.1); 
                margin: 0;
                padding: .25rem .5rem;
                border: 1px solid rgba(0, 128, 0, 0.2);
                display: none;
            }
            
        </style>
    <?php
    // Add your code here to render the developer tools page
    echo '<h1>Developer Tools</h1>';

    // Include the hex CSS filter tool
    echo '<div class="dev-tool-item">';
    include_once( 'dev-tools/hex-css-filter.php' ); 
    echo '</div>';

    // Include SVG to Data URI tool
    echo '<div class="dev-tool-item">';
    include_once( 'dev-tools/svg-to-data-uri.php' ); 
    echo '</div>';

}

<style>
    :root {
        /*** THEME STYLES ***/ 
        /* Layout Settings */
        --container-gutter: <?= get_theme_mod('container_gutter', '16'); ?>px;
        --container-width: <?= get_theme_mod('container_width', '1440'); ?>px;

        /* Color Palette */
        --color-primary: <?= get_theme_mod('primary_color', '#3498db'); ?>;
        --color-secondary: <?= get_theme_mod('secondary_color', '#2ecc71'); ?>;
        --color-tertiary: <?= get_theme_mod('tertiary_color', '#f1c40f'); ?>;
        --color-quaternary: <?= get_theme_mod('quaternary_color', '#e74c3c'); ?>;
        --color-text: <?= get_theme_mod('text_color', '#333333'); ?>;

        /* Typography */
        --font-family-heading: <?= get_theme_mod('font_family_heading', 'HelveticaNeue-Light,"Helvetica Neue Light","Helvetica Neue",Helvetica,Arial,"Lucida Grande",sans-serif;'); ?>;
        --font-family-body: <?= get_theme_mod('font_family_body', 'HelveticaNeue-Light,"Helvetica Neue Light","Helvetica Neue",Helvetica,Arial,"Lucida Grande",sans-serif;'); ?>;
        --base-font-size: <?= get_theme_mod('base_font_size', '16'); ?>px;

        /* Button Styles */
        --button-padding: <?= get_theme_mod('button_padding', '.5rem 2rem'); ?>;
        --button-border-width: <?= get_theme_mod('button_border_width', '3'); ?>px;
        --button-border-radius: <?= get_theme_mod('button_border_radius', '4'); ?>px;
        --button-font-weight: <?= get_theme_mod('button_font_weight', '700'); ?>;
        --button-font-size: <?= get_theme_mod('button_font_size', '16'); ?>px;
        --button_text_transform: <?= get_theme_mod('button_text_transform', 'uppercase'); ?>;

        /* Blog Settings */
        --blog-sidebar-pos: <?= get_theme_mod('blog_sidebar_position', 'row-reverse'); ?>;

        /*** COMPONENTS ***/ 
        /* Announcement Bar */
        --announcement-bar-background-color: <?= get_theme_mod('announcement_bar_background_color', '#333333'); ?>;
        --announcement-bar-text-color: <?= get_theme_mod('announcement_bar_text_color', '#ffffff'); ?>;
    }
</style>
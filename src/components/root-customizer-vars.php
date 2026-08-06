<style>
    :root {
        /*** THEME STYLES ***/ 
        /* Layout Settings */
        --container-gutter: <?= esc_attr( get_theme_mod('container_gutter', '16') ); ?>px;
        --container-width: <?= esc_attr( get_theme_mod('container_width', '1440') ); ?>px;

        /* Blog Settings */
        --blog-sidebar-pos: <?= esc_attr( get_theme_mod('blog_sidebar_position', 'row-reverse') ); ?>;

        /*** COMPONENTS ***/ 
        /* Announcement Bar */
        --announcement-bar-background-color: <?= esc_attr( chw_get_hex_theme_mod( 'announcement_bar_background_color', '#333333' ) ); ?>;
        --announcement-bar-text-color: <?= esc_attr( chw_get_hex_theme_mod( 'announcement_bar_text_color', '#ffffff' ) ); ?>;

        /* Header */
        --header-height: 0px;
        --admin-bar-height: <?= is_admin_bar_showing() ? '32px' : '0px' ?>;
    }

    #announcement-bar {
        background-color: var(--announcement-bar-background-color);
        color: var(--announcement-bar-text-color);
    }
</style>
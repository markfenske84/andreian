<? 
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Returns a SVG element as a string. The SVG library is located in /assets/svg.
 * @param string $icon The icon name.
 * @param string $additional_class The additional class to add.
 * 
 * @return string $content The SVG element.
 */
function svg($icon, $additional_class = '') {
    if (strpos($icon, 'http') !== false) {
        $path = $icon;
    } else {
        $path = THEME_ASSETS . "/svg/" . $icon . '.svg';
    }

    $content = @file_get_contents($path); 

    if (!$content) {
        return false;
    }

    if (strpos($icon, '/')) {
        $icon = explode('/', $icon);
        $icon = $icon[1];
    }

    $class = 'svg-' . $icon;
    if ($additional_class) {
        $class .= ' ' . $additional_class;
    }

    $content = str_replace('<svg ', '<svg class="' . $class . '" ', $content);
    return $content;
}

/**
 * Allow exceptions for specified MIME types to be uploaded into the media editor.
 */

function cc_mime_types( $mimes ){
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter( 'upload_mimes', 'cc_mime_types' );

function svg_icon_set() {
    $svg_icons = [];
    $svg_files = scandir(THEME_ASSETS . '/svg/');

    foreach ($svg_files as $key => $file) {
        if (strpos($file, '.svg') !== false) {
        $filename = str_replace('.svg', '', $file);
        $svg_icons[] = [
            'name' => $filename,
            'icon' => svg($filename)
        ];
        }
    }

    return $svg_icons;
}
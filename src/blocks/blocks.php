<?
function krypton_get_blocks() {
    return [
        // EXAMPLE AND STYLE ONLY, REMOVE BEFORE LAUNCH
        [
            'title' => 'Style Guide',
            'description' => 'Style Guide block is used for matching a mocks Style Guide screen. This should be removed starting every new project.',
            'render_template' => 'src/blocks/style-guide/style-guide.php',
            'supports' => [
                'anchor' => true,
            ],
        ],  // REMOVE THIS BLOCK BEFORE LAUNCH
        [
            'title' => 'Accordions',
            'description' => 'Collapsable accordion content.',
            'render_template' => 'src/blocks/accordions/accordions.php',
            'mode' => 'edit',
            'supports' => [
                'anchor' => true,
            ],
            'mode' => 'edit',
        ],
        [
            'title' => 'Fullwidth Video Content',
            'description' => 'Fullwidth content block with video media background.',
            'render_template' => 'src/blocks/fullwidth-video/fullwidth-video.php',
            'supports' => [
                'anchor' => true,
            ],
            'mode' => 'edit',
        ],
        [
            'title' => 'Fullwidth Slideshow Content',
            'description' => 'Fullwidth content block with swiper.js slideshow for multiple slides.',
            'render_template' => 'src/blocks/fullwidth-slideshow/fullwidth-slideshow.php',
            'supports' => [
                'anchor' => true,
            ],
            'mode' => 'edit',
        ],
        [
            'title' => 'Fullwidth Halfscreen Content',
            'description' => 'Fullwidth content block with half-screen being content and the other being media.',
            'render_template' => 'src/blocks/fullwidth-halfscreen/fullwidth-halfscreen.php',
            'supports' => [
                'anchor' => true,
            ],
            'mode' => 'edit',
        ],
        [
            'title' => 'Gallery Tiles',
            'description' => 'Tile based static image gallery system with modal functionality.',
            'render_template' => 'src/blocks/gallery-tiles/gallery-tiles.php',
            'supports' => [
                'anchor' => true,
            ],
            'mode' => 'edit',
        ],
        [
            'title' => 'Logo Carousel',
            'description' => 'Customizable block for display logos in a carousel or static mode.',
            'render_template' => 'src/blocks/logo-carousel/logo-carousel.php',
            'mode' => 'edit',
        ],
        [
            'title' => 'Read More Content',
            'description' => 'Dismissable content for website sections with long form content pieces.',
            'render_template' => 'src/blocks/read-more/read-more.php',
        ],
        [
            'title' => 'Social Links',
            'description' => 'Social links block, fields pulled from the Social Links options page.',
            'render_template' => 'src/blocks/social-links/social-links.php',
        ],
        [
            'title' => 'Tabbed Panes',
            'description' => 'Tabbed panes for multiple top content displayed in a condensed fashion.',
            'render_template' => 'src/blocks/tabbed-panes/tabbed-panes.php',
            'mode' => 'edit',
            'supports' => [
                'anchor' => true,
            ],
        ],
        [
            'title' => 'Video Modal',
            'description' => 'Clickable element with a placeholder image that opens a modal to play a video from multiple source options.',
            'render_template' => 'src/blocks/video-modal/video-modal.php',
            'mode' => 'edit',
        ],
    ];
}

function krypton_register_blocks() {
    if ( function_exists('acf_register_block_type') ) {
        $blocks = krypton_get_blocks();
        foreach ($blocks as $block) {
            $block_args = [
                'name' => sanitize_title_with_dashes($block['title']),
                'category' => 'webfor',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="111.04" height="111.075" viewBox="0 0 111.04 111.075"><path d="M71.332,94.178,58.448,73.8,64.3,64.661,71.45,75.906l32.094-48.492a55.637,55.637,0,0,0-95.93,0l32.8,48.609,24.715-38.77H46.735l6.794,10.659-5.857,9.136L28.931,27.414H83.045l-42.4,66.647-.469-.586L2.811,37.955A58.1,58.1,0,0,0,0,55.525a55.52,55.52,0,1,0,111.04,0,57.511,57.511,0,0,0-2.811-17.57Z" transform="translate(0 0.03)"/></svg>',
                'mode' => 'preview',
            ];

            $block_args = array_merge($block_args, $block);
            acf_register_block_type($block_args);    
        }
    }
}
add_action('acf/init', 'krypton_register_blocks');

function krypton_block_category( $categories, $post ) {
    return array_merge($categories,
        [
            [
                'slug' => 'custom',
                'title' => __( 'Webfor Custom Blocks', 'custom' ),
            ],
        ]
    );
}
add_filter( 'block_categories', 'krypton_block_category', 10, 2);
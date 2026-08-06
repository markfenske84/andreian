<?php
/**
 * Block category registration.
 *
 * All custom blocks are now native (non-ACF) Gutenberg blocks registered from
 * their own src/blocks/<slug>/register-<slug>-block.php files (included in
 * functions.php). This file only registers the shared block category they use.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function chw_block_category( $categories, $post ) {
    return array_merge($categories,
        [
            [
                'slug' => 'choice-home-warranty',
                'title' => __( 'Choice Home Warranty Custom Blocks', 'choice-home-warranty' ),
            ],
        ]
    );
}
add_filter( 'block_categories_all', 'chw_block_category', 10, 2);

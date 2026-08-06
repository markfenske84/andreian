<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Add "Duplicate" link to post row actions for posts, pages, and custom post types
 *
 * @param array $actions The existing row actions.
 * @param WP_Post $post The current post object.
 * @return array The modified row actions.
 */
function add_duplicate_post_link( $actions, $post ) {
    // Check if the current user has permission to edit posts
    if ( current_user_can( 'edit_posts' ) ) {
        // Add Duplicate link for posts and pages
        if ( $post->post_type === 'post' || $post->post_type === 'page' || get_post_type_object( $post->post_type )->public ) {
            $actions['duplicate'] = '<a href="' . wp_nonce_url( admin_url( 'admin.php?action=duplicate_post&post=' . $post->ID ), 'duplicate_post_' . $post->ID ) . '">Duplicate</a>';
        }
    }
    return $actions;
}
add_filter( 'page_row_actions', 'add_duplicate_post_link', 10, 2 );
add_filter( 'post_row_actions', 'add_duplicate_post_link', 10, 2 );

/**
 * Duplicate a post when the "Duplicate" action is clicked.
 */
function duplicate_post_action() {
    // Check for action and nonce
    if ( isset( $_GET['action'] ) && $_GET['action'] === 'duplicate_post' && isset( $_GET['post'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'duplicate_post_' . $_GET['post'] ) ) {
        // Get the ID of the post to duplicate
        $post_id = absint( $_GET['post'] );

        // Duplicate the post
        $new_post_id = duplicate_post( $post_id );

        // Redirect to the edit screen of the new post
        if ( ! is_wp_error( $new_post_id ) && $new_post_id ) {
            wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
            exit;
        } else {
            wp_die( 'Error duplicating post.' );
        }
    }
}
add_action( 'admin_init', 'duplicate_post_action' );

/**
 * Duplicate a post.
 *
 * @param int $post_id The ID of the post to duplicate.
 * @return int|WP_Error The ID of the new duplicated post, or WP_Error object on failure.
 */
function duplicate_post( $post_id ) {
    // Get the original post object
    $post = get_post( $post_id );

    // If post object is not valid, return error
    if ( ! $post ) {
        return new WP_Error( 'invalid_post', __( 'Invalid post ID.' ) );
    }

    // Create an array for post data
    $new_post_data = array(
        'post_title'   => $post->post_title . ' (Copy)',
        'post_content' => $post->post_content,
        'post_status'  => 'draft',
        'post_type'    => $post->post_type,
    );

    // Insert the new post
    $new_post_id = wp_insert_post( $new_post_data );

    // If new post creation failed, return error
    if ( is_wp_error( $new_post_id ) ) {
        return $new_post_id;
    }

    // Duplicate post meta
    $post_meta = get_post_meta( $post_id );
    if ( ! empty( $post_meta ) ) {
        foreach ( $post_meta as $key => $values ) {
            foreach ( $values as $value ) {
                add_post_meta( $new_post_id, $key, $value );
            }
        }
    }

    // Duplicate taxonomy terms
    $taxonomies = get_object_taxonomies( $post->post_type );
    if ( ! empty( $taxonomies ) ) {
        foreach ( $taxonomies as $taxonomy ) {
            $post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
            wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
        }
    }

    // Return the ID of the new duplicated post
    return $new_post_id;
}

<?php
/**
 * Plugin Name: Custom Admin Features
 * Description: Adds custom features to the admin posts list.
 */

// Add custom column for featured image
function custom_posts_columns($columns) {
    $columns['featured_image'] = 'Featured Image';
    return $columns;
}

// Apply the column to any post type that shows an admin UI (posts, pages, CPTs)
function custom_columns_setup() {
    // Fetch all public post types that can be edited in the admin
    $post_types = get_post_types([
        'public'  => true,
        'show_ui' => true,
    ], 'names');

    foreach ($post_types as $post_type) {
        // Add the column header
        add_filter("manage_edit-{$post_type}_columns", 'custom_posts_columns');
        // Render each row value
        add_action("manage_{$post_type}_posts_custom_column", 'custom_posts_custom_column', 10, 2);
    }
}
add_action('init', 'custom_columns_setup');

// Display featured image in custom column
function custom_posts_custom_column($column, $post_id) {
    if ($column === 'featured_image') {
        $thumbnail_id = get_post_thumbnail_id($post_id);
        $thumbnail_img = $thumbnail_id ? wp_get_attachment_image($thumbnail_id, array(50, 50)) : '';

        if ($thumbnail_id) {
            // When an image already exists
            echo '<div class="featured-image-wrapper">';
            // Thumbnail itself acts as "Replace" trigger
            printf(
                '<a href="#" class="replace-featured-image" data-post-id="%1$d">%2$s</a>',
                esc_attr($post_id),
                $thumbnail_img
            );

            // Action links: Replace | Remove | Edit
            printf(
                '<div class="featured-image-actions" style="margin-top:4px;">'
                    .'<a href="#" class="replace-featured-image" data-post-id="%1$d">%2$s</a> | '
                    .'<a href="#" class="remove-featured-image" data-post-id="%1$d">%3$s</a> | '
                    .'<a href="%4$s" target="_blank">%5$s</a>'
                .'</div>',
                esc_attr($post_id),
                esc_html__('Replace', 'textdomain'),
                esc_html__('Remove', 'textdomain'),
                esc_url(get_edit_post_link($thumbnail_id)),
                esc_html__('Edit', 'textdomain')
            );

            echo '</div>'; // .featured-image-wrapper
        } else {
            // No thumbnail yet: show Set link
            printf(
                '<a href="#" class="set-featured-image" data-post-id="%1$d">%2$s</a>',
                esc_attr($post_id),
                esc_html__('Set Image', 'textdomain')
            );
        }
    }
}

// Handle AJAX request to set featured image
function set_featured_image() {
    check_ajax_referer('set-featured-image', 'security');

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $attachment_id = isset($_POST['attachment_id']) ? intval($_POST['attachment_id']) : 0;

    if ($post_id && $attachment_id) {
        set_post_thumbnail($post_id, $attachment_id);
        echo 'success';
    }

    wp_die();
}
add_action('wp_ajax_set_featured_image', 'set_featured_image');

// AJAX: remove featured image
function remove_featured_image() {
    check_ajax_referer('set-featured-image', 'security');

    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

    if ($post_id) {
        delete_post_thumbnail($post_id);
        echo 'success';
    }

    wp_die();
}
add_action('wp_ajax_remove_featured_image', 'remove_featured_image');

// Enqueue JavaScript for media uploader
function custom_admin_enqueue_scripts() {
    global $pagenow;

    $allowed_pages = array('edit.php', 'post.php', 'post-new.php');

    if (in_array($pagenow, $allowed_pages)) {
        wp_enqueue_media();
        ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Delegate click events for dynamic elements
                document.body.addEventListener('click', function(e) {
                    // Replace or set
                    if (e.target.matches('.replace-featured-image, .set-featured-image')) {
                        e.preventDefault();
                        var post_id = e.target.getAttribute('data-post-id');

                        var frame = wp.media({
                            title: 'Select or Upload Featured Image',
                            button: { text: 'Use this image' },
                            multiple: false
                        });

                        frame.on('select', function() {
                            var attachment = frame.state().get('selection').first().toJSON();

                            var data = new FormData();
                            data.append('action', 'set_featured_image');
                            data.append('post_id', post_id);
                            data.append('attachment_id', attachment.id);
                            data.append('security', '<?php echo wp_create_nonce('set-featured-image'); ?>');

                            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                                method: 'POST',
                                body: data
                            })
                            .then(function(response) { return response.text(); })
                            .then(function() { window.location.reload(); });
                        });

                        frame.open();
                    }

                    // Remove
                    if (e.target.matches('.remove-featured-image')) {
                        e.preventDefault();
                        var post_id = e.target.getAttribute('data-post-id');

                        var data = new FormData();
                        data.append('action', 'remove_featured_image');
                        data.append('post_id', post_id);
                        data.append('security', '<?php echo wp_create_nonce('set-featured-image'); ?>');

                        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                            method: 'POST',
                            body: data
                        })
                        .then(function(response) { return response.text(); })
                        .then(function() { window.location.reload(); });
                    }
                });
            });
        </script>
        <?php
    }
}
add_action('admin_enqueue_scripts', 'custom_admin_enqueue_scripts');

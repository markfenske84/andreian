<? 
// Add the blog prefix to post permalinks
function add_blog_slug_to_posts($permalink, $post) {
    // Check if the post type is 'post' (default WordPress blog posts)
    if ($post->post_type == 'post') {
        // Ensure permalink structure has %postname%
        $permalink = home_url('/blog/' . $post->post_name . '/');
    }
    return $permalink;
}
add_filter('post_link', 'add_blog_slug_to_posts', 10, 2);

// Register rewrite tag for blog pagination
function add_blog_rewrite_tag() {
    add_rewrite_tag('%blog_page%', '([0-9]+)');
}
add_action('init', 'add_blog_rewrite_tag', 10);

// Remove the custom category modification - we want standard WP category URLs
// function modify_category_link($link) {
//     return str_replace('/category/', '/blog/category/', $link);
// }
// add_filter('category_link', 'modify_category_link');

// Add rewrite rules for the blog structure
function add_blog_rewrite_rules($rules) {
    $new_rules = array();
    
    // Rule for blog pagination - using the blog_page tag
    $new_rules['blog/page/([0-9]+)/?$'] = 'index.php?blog_page=$matches[1]&post_type=post';
    
    // Rule for single blog posts
    $new_rules['blog/([^/]+)/?$'] = 'index.php?name=$matches[1]';
    
    // Rule for blog base URL
    $new_rules['blog/?$'] = 'index.php?post_type=post';
    
    // Standard category rules are already handled by WordPress core
    // No need to add custom rules for /category/ URLs
    
    // Return rules with our additions at the beginning
    return $new_rules + $rules;
}
add_filter('rewrite_rules_array', 'add_blog_rewrite_rules');

// Fix pagination links to use /blog/ prefix for main blog only
function fix_pagination_base($link) {
    // Only fix non-category pagination links
    if (strpos($link, '/page/') !== false && 
        strpos($link, '/blog/') === false && 
        strpos($link, '/category/') === false) {
        $link = preg_replace('~/page/([0-9]+)/?~', '/blog/page/$1/', $link);
    }
    return $link;
}
add_filter('paginate_links', 'fix_pagination_base');

// Set default blog query
function set_blog_query($query) {
    if (!is_admin() && $query->is_main_query()) {
        // Only modify the main /blog/ URL - include paged URLs too
        if (isset($_SERVER['REQUEST_URI'])) {
            // Main blog pages
            if (strpos($_SERVER['REQUEST_URI'], '/blog/') === 0 && 
                !preg_match('~/blog/[^/]+/$~', $_SERVER['REQUEST_URI'])) {
                
                // Reset conflicting query vars
                $query->set('page', '');
                $query->set('p', '');
                $query->set('post_type', 'post');
                $query->set('name', '');
                
                // Set up the blog query
                $query->is_home = true;
                $query->is_archive = true;
                $query->is_front_page = false;
                
                // Handle pagination
                if (preg_match('~/blog/page/([0-9]+)/?$~', $_SERVER['REQUEST_URI'], $matches)) {
                    $page = intval($matches[1]);
                    $query->set('paged', $page);
                    $query->set('blog_page', $page);
                } else {
                    // Ensure first page is set correctly
                    $query->set('paged', 1);
                }
                
                // Ensure proper ordering
                $query->set('orderby', 'date');
                $query->set('order', 'DESC');
            }
            
            // Standard category pages should be handled by WordPress core
            // No need to modify them
        }
    }
    return $query;
}
add_action('pre_get_posts', 'set_blog_query', 2);

// Force flush rewrite rules - but only do this once or when needed
// Flushing rules on every page load can cause performance issues
function flush_rewrite_rules_once() {
    // Only flush if a transient doesn't exist
    if (!get_transient('blog_rules_flushed')) {
        flush_rewrite_rules();
        // Set transient to prevent flushing on every load
        set_transient('blog_rules_flushed', 1, HOUR_IN_SECONDS * 24); // Once per day
    }
}
add_action('init', 'flush_rewrite_rules_once', 999);

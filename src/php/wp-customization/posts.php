<? 
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Returns a string with anchor linked categories.
 * @return string The categories.
 */
function krypton_post_categories() {
    global $post;
    $cats = get_the_category();
    $cat_array = array();

    foreach ($cats as $category) {
        if ($category->name == 'Uncategorized') {
            continue;
        }
        array_push($cat_array, '<a href="' . get_category_link( $category->term_id ) . '">' . $category->name . '</a>');
    }

    $cat_string = implode(', ', $cat_array);
    return $cat_string;
}

/**
 * Returns an excerpt limited to amount within argument.
 * @param int $limit The character limit desired for the excerpt.
 * @return string The post's excerpt.
 */
function krypton_excerpt($limit = 150) {
    global $post;
    $excerpt = get_the_excerpt($post);

    if (strlen($excerpt) > $limit) {
        $excerpt = substr($excerpt, 0, strpos($excerpt, ' ', $limit)) . '...';
    }

    return $excerpt;
}

/**
 * Returns an array with the current post's categories.
 * @return array The categories.
 */
function get_post_categories() {
  global $post;
  $cats = get_the_category();
  $cat_array = [];
  foreach ($cats as $category) {
    if ($category->name == 'Uncategorized') {
      continue;
    }

    $cat_array[] = [
      'title' => $category->name,
      'permalink' => get_category_link( $category->term_id ),
    ];
  }

  return $cat_array;
}

/**
 * Returns an array with the general post categories available.
 * @return array The categories.
 */
function list_post_categories() {
  $categories = get_categories();
  $res = [];
  
  foreach($categories as $category) {
    $res[] = [
      'id' => $category->term_id,
      'title' => $category->name,
      'permalink' => get_category_link($category->term_id),
    ];
  }

  return $res;
}

/**
 * Returns pagination links for the current post.
 * WordPress returns in reverse chronological order for these functions so prev and next are switched.
 *
 * @return array The pagination URLs for previous and next posts.
 */
function get_sibling_post_links() {
	global $post;

	$prev = get_previous_post();
	$next = get_next_post();

	if ($prev) {
		$prev_link = get_permalink($prev->ID);
	} else {
		$prev_link = false;
	}

	if ($next) {
		$next_link = get_permalink($next->ID);
	} else {
		$next_link = false;
	}

	// Reverse Chronological Order so we switch the variables
	return ['prev' => $next_link, 'next' => $prev_link];
}

/**
 * Retrieve page data for the posts page.
 */
function get_posts_page() {
  $posts_page_id = get_option('page_for_posts');
  $posts_page = get_page($posts_page_id);
  $posts_page->permalink = get_permalink($posts_page_id);
  return $posts_page;
}

// Adds a fallback featured image when a post does not have one set.
function krypton_default_featured_image_html($html, $post_id, $post_thumbnail_id, $size, $attr) {
  // If the post already has a featured image, keep it.
  if (!empty($html)) {
    return $html;
  }

  // Fetch URL saved in Customizer.
  $default_url = get_theme_mod('blog_default_featured_image');
  if (!$default_url) {
    return $html; // No default defined.
  }

  // Build attributes string.
  $attr_string = '';
  if (is_array($attr)) {
    foreach ($attr as $key => $value) {
      $attr_string .= sprintf(' %s="%s"', esc_attr($key), esc_attr($value));
    }
  }

  // Ensure alt attribute at minimum.
  if (strpos($attr_string, ' alt=') === false) {
    $attr_string .= ' alt="' . esc_attr(get_the_title($post_id)) . '"';
  }

  return '<img src="' . esc_url($default_url) . '"' . $attr_string . ' />';
}
add_filter('post_thumbnail_html', 'krypton_default_featured_image_html', 10, 5);
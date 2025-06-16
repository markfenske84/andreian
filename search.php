<?php
/**
 * The template for displaying search results pages
 */

get_header();
get_template_part('src/components/inner-masthead');
?>

<section 
    id="search-template" 
    class="_container _archive">

    <div class="_posts">
        <div class="_inner">
            <?php
            // Get the search query and category
            $search_query = get_search_query();
            $category = get_query_var('category_name');

            // Set up the search query
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $args = array(
                's' => $search_query,
                'post_type' => 'post',
                'posts_per_page' => get_option('posts_per_page'),
                'order' => 'DESC',
                'paged' => $paged,
            );

            // Add category filter if specified
            if ($category) {
                $args['category_name'] = $category;
            }

            $query = new WP_Query($args);

            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    get_template_part('src/templates/partials/index/content', 'single');
                }

                wp_reset_postdata();
            } else {
                echo '<div class="no-results">';
                echo '<p>' . __('No results found. Please try a different search term.', 'arabesque') . '</p>';
                echo '</div>';
            }
            ?>
        </div>

        <?php if ($query->have_posts()) : ?>
            <div class="_pagination">
                <?php
                global $wp_rewrite;
                
                $pagination = array(
                    'base' => @add_query_arg('paged','%#%'),
                    'format' => '',
                    'total' => $query->max_num_pages,
                    'current' => $paged,
                    'prev_text' => __('<i class="fa-solid fa-angles-left"></i>', 'arabesque'),
                    'next_text' => __('<i class="fa-solid fa-angles-right"></i>', 'arabesque'),
                    'type' => 'list',
                    'end_size' => 3,
                    'mid_size' => 3
                );
                
                if($wp_rewrite->using_permalinks()) {
                    $pagination['base'] = user_trailingslashit(trailingslashit(remove_query_arg('s', get_pagenum_link(1))) . 'page/%#%/', 'paged');
                }
                
                echo paginate_links($pagination);
                ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if(is_active_sidebar('sidebar')) { ?>
        <div class="_sidebar">
            <?php dynamic_sidebar('sidebar'); ?>
        </div>
    <?php } ?>

</section>

<?php get_footer(); ?> 
<?php 
/*
Template Name: Sidebar
Template Post Type: page, post
*/

get_header();
get_template_part('src/components/inner-masthead');
?>

<div id="singular-template" class="_container _grid -sidebar">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> itemscope itemtype="http://schema.org/BlogPosting">
        <div class="page-content entry-content clearfix" itemprop="articleBody">
          <?php the_content(); ?>
        </div>
      </article>
      <?php get_sidebar(); ?>
    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>

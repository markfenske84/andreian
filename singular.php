<? 
/**
 * The template for displaying all singular posts.
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 */

get_header();
get_template_part('src/components/inner-masthead');
?>

<div id="singular-template" class="_container">
    <? if (have_posts()) : while (have_posts()) : the_post(); ?>
      <article id="post-<? the_ID(); ?>" <? post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
        <section class="page-content entry-content clearfix" itemprop="articleBody">
          <? the_content(); ?>
        </section> <!-- end article section -->
      </article> <!-- end article -->
    <? endwhile; endif; ?>
</div>

<? get_footer(); ?>
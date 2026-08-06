<?php
/**
 * 
 */

get_header();
get_template_part('src/components/inner-masthead'); 
?>

<div id="-template">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
      <?php the_content(); ?>
    <?php endwhile; endif; ?>	
</div>

<?php get_footer(); ?>
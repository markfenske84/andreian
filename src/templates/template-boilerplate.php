<?
/**
 * 
 */

get_header();
get_template_part('src/components/inner-masthead'); 
?>

<div id="-template">
    <? if (have_posts()) : while (have_posts()) : the_post(); ?>
      <? the_content(); ?>
    <? endwhile; endif; ?>	
</div>

<? get_footer(); ?>
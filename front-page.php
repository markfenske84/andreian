<? 
/**
 * On the WordPress administration settings, the page set to be the Homepage under "Settings > Reading" will
 * display the template partial below.
 * 
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 */

get_header(); ?>

<section class="_container">
    <? the_content(); ?>
</section>

<? get_footer(); ?>

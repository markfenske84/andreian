<?php
/**
 * Template part for displaying posts
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="article-image">
        <a href="<?php the_permalink() ?>">
         <?php the_post_thumbnail('large', ['alt' => get_the_title()]); ?>
        </a>
    </div>
    <div class="article-content">
        <header class="entry-header">
            <h2 class="title _text-style -h4"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
            <?php get_template_part( 'src/components/post-entry-meta' ); ?>
        </header>
        <div class="entry-content">
            <?= chw_excerpt(); ?>
        </div>
        <a href="<?php the_permalink() ?>" class="_button -primary">Read More</a>
    </div>
</article>

<?
/**
 * Template part for displaying posts
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 */
?>

<article id="post-<? the_ID(); ?>" <? post_class(); ?>>
    <div class="article-image">
        <a href="<? the_permalink() ?>">
         <? the_post_thumbnail('large', ['alt' => get_the_title()]); ?>
        </a>
    </div>
    <div class="article-content">
        <header class="entry-header">
            <h2 class="title"><a href="<? the_permalink() ?>"><? the_title(); ?></a></h2>
            <div class="entry-meta">
                <small class="_flex">
                    <span class="meta-category"><? the_category(', '); ?></span>
                    <span class="meta-date"><?php echo ( get_the_modified_time('U') !== get_the_time('U') ) ? get_the_modified_time('F j, Y') : get_the_time('F j, Y'); ?></span>
                    <span class="meta-author">By <? the_author(); ?></span>
                </small>
            </div>
        </header>
        <div class="entry-content">
            <?= krypton_excerpt(); ?>
        </div>
        <br>
        <a href="<? the_permalink() ?>" class="_button -primary">Read More</a>
    </div>
</article>

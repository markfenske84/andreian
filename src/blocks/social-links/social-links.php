<? 
/**
 * Block: Social Links
 */

$social_links = get_field('social_links', 'option');
?>

<div class="
    social-links
    <? if(isset($block['className'])) { echo ' ' . $block['className']; } ?>">

    <? foreach($social_links as $social_link) {
        $channel = $social_link['social_channel'];
        $link = $social_link['social_url']; ?>
        <a 
            href="<?= $link; ?>" 
            target="_blank" 
            <? if ( is_user_logged_in() && current_user_can('administrator') ) : // assist with Gutenberg rendering ?>
            style="text-decoration: none;"<? endif; ?>>
            <span class="sr-only">Visit <?= esc_attr( get_bloginfo( 'name', 'display' ) ); ?> on <?= $channel['label']; ?></span>
            <i class="fa-brands fa-<?= $channel['value']; ?>"></i>
        </a>
    <? } ?>

</div>

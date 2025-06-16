</section>

<? 
    // unique id
    $block_id = $block['id'];
    
    $video_source = get_field('video_source'); // Select (self : Self Hosted [Default], youtube : YouTube, vimeo : Vimeo)
    if($video_source == 'self') {
        $video_file = get_field('video_file'); // File
    } else {
        $video_url = get_field('video_url'); // URL
    }
    $video_overlay = get_field('video_overlay'); // Color Picker
    $video_height = get_field('video_height'); // Select (full : Fullscreen, custom : Custom Height)
    if($video_height == 'custom') {
        $video_desktop_padding = get_field('video_desktop_padding'); // Text
        $video_mobile_padding = get_field('video_mobile_padding'); // Text
    }
    $video_placeholder = get_field('video_placeholder'); // Image
    $video_content = get_field('video_content'); // Group
    if($video_content) {
        $text_alignment = $video_content['text_alignment']; // Select (left : Left, center : Center, right : Right)
        $headline = $video_content['headline']; // Text Area
        $subheading = $video_content['subheading']; // Text Area
        $cta_links = $video_content['cta_links']; // Repeater (link array)
        $additional_content_classes = $video_content['additional_content_classes']; // Text
    }
?>

<? if($video_height == 'custom') { ?>
<style>
    .fullwidth-video.-<?= $block_id; ?> {
        padding: <?= $video_desktop_padding; ?> !important;
    }
    @media (max-width: 992px) {
        .fullwidth-video.-<?= $block_id; ?> {
            padding: <?= $video_mobile_padding; ?> !important;
        }
    }
</style>
<? } ?>

<section 
    <? if(isset($block['anchor'])) { ?>
    id="<?= esc_attr( $block['anchor'] ); ?>" 
    <? } ?>
    class="
        fullwidth-video 
        -<?= $block_id; ?> 
        -height-<?= $video_height; ?> 
        _flex 
        -align-center 
        -justify-center
        <? if(isset($block['className'])) { echo ' ' . $block['className']; } ?>" 
    <? if($video_placeholder) { ?>
        style="background: url('<?= $video_placeholder['url']; ?>') center/cover;"
    <? } ?>>

    <div 
        class="_video">

        <span 
            class="_overlay" 
            style="background-color: <?= $video_overlay; ?>"></span>
        
        <? if($video_source == 'self') { ?>
            <? if($video_file) { ?>
            <video 
                autoplay 
                loop 
                muted 
                playsinline>
                <source 
                    src="<?= $video_file['url']; ?>" 
                    type="video/mp4">
            </video>
            <? } ?>

        <? } elseif($video_source == 'youtube') { ?>

            <? if($video_url) { ?>
            <iframe 
                src="<?= $video_url; ?>&controls=0&showinfo=0&rel=0&autoplay=1&loop=1&modestbranding=1&disablekb=1&mute=1" 
                frameborder="0" 
                webkitallowfullscreen 
                mozallowfullscreen 
                allowfullscreen></iframe>
            <? } ?>
        
        <? } elseif($video_source == 'vimeo') { ?>
            
            <? if($video_url) { ?>
            <iframe 
                src="<?= $video_url; ?>?background=1&autoplay=1&loop=1&byline=0&title=0?rel=0" 
                frameborder="0" 
                frameborder="0" 
                allow="autoplay; fullscreen" 
                webkitallowfullscreen 
                mozallowfullscreen 
                allowfullscreen></iframe>
            <? } ?>
        
        <? } ?>

    </div>

    <? if($video_content) { ?>

    <div 
        class="_container">
        
        <div 
            class="
                _content 
                _text 
                <?= $text_alignment; ?> 
                <?= $additional_content_classes; ?>">

            <? if($headline) { ?>

            <h1><?= $headline; ?></h1>

            <? } ?>

            <? if($subheading) { ?>

            <h2><?= $subheading; ?></h2>

            <? } ?>

            <? if($cta_links) { ?>

                <div class="_cta-links">

                    <? foreach($cta_links as $cta_link) { 
                        $link = $cta_link['link'];
                        $cta_class = ($cta_link === reset($cta_links)) ? '-primary' : '-outline'; ?>
                        
                        <? if($link) { ?>
                        <a 
                            href="<?= $link['url']; ?>" 
                            class="_button <?= $cta_class; ?>"
                            <? if($link['target']) { ?> target="<?= $link['target']; ?>"<? } ?>>

                            <?= $link['title']; ?>

                        </a>
                        <? } ?>

                    <? } ?>

                </div>

            <? } ?>

        </div>

    </div>

    <? } ?>

</section>

<section 
    class="
        _container">
</section>

<?
    // Settings
    $orientation = get_field('orientation'); // Select (-media-content : Media | Content [Default], -content-media : Content | Media)
    $media_settings = get_field('media_settings'); // Group
    // -Media Block
    if($media_settings) {
        $media_animation = $media_settings['animation']; // Select (-none : None [Default], -fade-top : Fade In (Top), -fade-bottom : Fade In (Bottom), -fade-left : Fade In (Left), -fade-right : Fade In (Right)
        $media_animation_delay = $media_settings['animation_delay']; // Range (100 - 10000, [Default: 1000ms])
        $media_overlay = $media_settings['overlay']; // Color Picker
    }
    // -Content Block
    $content_settings = get_field('content_settings'); // Group
    if($content_settings) {
        $content_animation = $content_settings['animation']; // Select (-none : None [Default], -fade-top : Fade In (Top), -fade-bottom : Fade In (Bottom), -fade-left : Fade In (Left), -fade-right : Fade In (Right)
        $content_animation_delay = $content_settings['animation_delay']; // Range (100 - 10000, [Default: 1000ms])
        $background_color = $content_settings['background_color']; // Color Picker
    }

    // Content
    // -Media Block
    $media_block = get_field('media_block'); // Group
    if($media_block) {
        $media_type = $media_block['media_type']; // Select (image : Image [Default], video : Video)
        if($media_type == 'image') {
            $image = $media_block['image']; // Image
        } else {
            $video_source = $media_block['video_source']; // Select (self : Self Hosted [Default], youtube : YouTube, vimeo : Vimeo)
            if($video_source == 'self') {
                $video_file = $media_block['video_file']; // File
            } else {
                $video_url = $media_block['video_url']; // URL
            }
        }
    }
    // -Content Block
    $content_block = get_field('content_block'); // Group
    if($content_block) {
        $content = $content_block['content']; // WYSIWYG
        $read_more = $content_block['collapsed_content']; // WYSIWYG
    }
?>

<section 
    <? if(isset($block['anchor'])) { ?>
    id="<?= esc_attr( $block['anchor'] ); ?>" 
    <? } ?>
    class="
        fullwidth-halfscreen 
        <?= $orientation; ?> 
        _flex
        <? if(isset($block['className'])) { echo ' ' . $block['className']; } ?>">

    <div 
        class="
            _media 
            _animation 
            <?= $media_animation; ?>" 
        style="
            animation-delay: <?= $media_animation_delay; ?>ms; 
            -webkit-animation-delay: <?= $media_animation_delay; ?>ms;">
        
        <span 
            class="_overlay" 
            style="background-color: <?= $media_overlay; ?>"></span>

        <? if($media_type == 'image') { if($image) { ?>
            
            <img 
                src="<?= $image['url']; ?>" 
                alt="<?= $image['alt']; ?>">

        <? } } elseif($media_type == 'video') { ?>

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

        <? } ?>

    </div>

    <div 
        class="
            _content 
            _flex 
            -justify-center 
            -align-center 
            _animation 
            <?= $content_animation; ?>" 
        style="
            background-color: <?= $background_color; ?>; 
            animation-delay: <?= $content_animation_delay; ?>ms; 
            -webkit-animation-delay: <?= $content_animation_delay; ?>ms;">

        <div 
            class="
                _inner 
                _gutter">

            <?= $content; ?>

            <? if($read_more) { ?>

                <details class="read-more">

                    <summary class="_label">
                        + Read More
                    </summary>

                    <div class="_inner">

                        <?= $read_more; ?>

                    </div>

                </details>
                
            <? } ?>

        </div>

    </div>

</section>

<section 
    class="
        _container">
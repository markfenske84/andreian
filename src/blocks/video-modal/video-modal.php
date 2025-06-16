<?
    $video_source = get_field('video_source'); // select (third-party, self-hosted)
    if($video_source == 'third-party') {
        $video_embed_url = get_field('video_embed_url'); // url
    } else {
        $video_file = get_field('video_file'); // file
    }
    $thumbnail = get_field('thumbnail'); // image (array)
    $modal_background = get_field('modal_background'); // color picker w/ opacity enabled
?>

<button 
    class="
        video-modal-trigger
        <? if(isset($block['className'])) { echo ' ' . $block['className']; } ?>" 
    data-vid-url="<?php 
    if ($video_source == 'third-party') {
        echo $video_embed_url;
    } else {
        echo $video_file['url'];
    } ?>">
    <i class="fa-solid fa-circle-play"></i>
    <div class="thumbnail">
        <? if ($thumbnail) { ?>
        <img 
            src="<?= $thumbnail['url']; ?>" 
            alt="<?= $thumbnail['alt']; ?>">
        <? } ?>
    </div>
</button>

<div 
    class="video-modal"
    style="background-color: <?= $modal_background; ?>;"
    role="dialog" aria-label="Image viewer modal">
    
    <? get_template_part('src/components/close-button');  ?>

    <div class="_inner">

        <? if($video_source == 'self-hosted') { ?>

        <video controls>

            <source 
                src="">

        </video>

        <? } else { ?>

        <iframe 
            src="" 
            frameborder="0">
        </iframe>

        <? } ?>
    </div>

</div>
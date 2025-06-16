</section>

<? 
    // unique id
    $block_id = $block['id'];

    $slideshow_height = get_field('slideshow_height'); // Select (full : Fullscreen, custom : Custom Height)
    if($slideshow_height == 'custom') {
        $slideshow_desktop_padding = get_field('slideshow_desktop_padding'); // Text
        $slideshow_mobile_padding = get_field('slideshow_mobile_padding'); // Text
    }
    // To add or modify swiper setting options, refer to the following link: https://swiperjs.com/swiper-api

    // Define mapping for boolean values
    $boolean_mapping = array(
        'autoplay' => 'autoplay',
        'loop' => 'loop',
        'navigation' => 'navigation',
        'pagination' => 'pagination'
    );
    
    // Iterate through boolean settings and convert values
    foreach ($boolean_mapping as $setting_key => $variable_name) {
        ${$variable_name} = get_field($setting_key) ? 'true' : 'false';
    }
    
    // Other settings
    $transition_speed = get_field('transition_speed'); // Range (500-10000, step 500, default 1000)
    $slide_speed = get_field('slide_speed'); // Range (500-10000, step 500, default 6000)
    
    // Slides
    $slides = get_field('slideshow_slides'); // Repeater
?>

<? if($slideshow_height == 'custom') { ?>
<style>
    .fullwidth-slideshow.-<?= $block_id; ?> ._content {
        padding: <?= $slideshow_desktop_padding; ?> !important;
    }
    @media (max-width: 992px) {
        .fullwidth-slideshow.-<?= $block_id; ?> ._content {
            padding: <?= $slideshow_mobile_padding; ?> !important;
        }
    }
</style>
<? } ?>

<section 
    <? if(isset($block['anchor'])) { ?>
    id="<?= esc_attr( $block['anchor'] ); ?>" 
    <? } ?>
    class="
        fullwidth-slideshow 
        -<?= $block_id; ?> 
        -size-<?= $slideshow_height; ?>
        <? if($block['className']) { echo ' ' . $block['className']; } ?>">

    <div 
        class="swiper-container" 
        data-swiper="{'loop': <?= $loop; ?>, 'autoplay': <?= $autoplay; ?>, 'speed': <?= $transition_speed; ?>
        <? if($navigation == 'true') { ?>, 'navigation': { 'nextEl': '.swiper-button-next', 'prevEl': '.swiper-button-prev' }<? } ?>
        <? if($pagination == 'true') { ?>, 'pagination': { 'el': '.swiper-pagination', 'clickable': true}<? } ?>}">

        <div class="swiper-wrapper">

            <? if($slides) { ?>

                <? foreach($slides as $slide) { 
                    $image = $slide['image']; // Image
                    $title = $slide['title']; // Text
                    $description = $slide['description']; // Text Area
                    $cta_links = $slide['cta_links']; // Repeater
                    ?>

                    <div 
                        class="swiper-slide" 
                        data-swiper-autoplay="<?= $slide_speed; ?>">

                        <div class="_inner">

                            <img 
                                src="<?= $image['url']; ?>" 
                                alt="<?= $image['alt']; ?>" 
                                loading="lazy">

                            <div class="_content _container">

                                <? if($title) { ?><h2><?= $title; ?></h2><? } ?>

                                <? if($description) { ?><p><?= $description; ?></p><? } ?>

                                <? if($cta_links) { ?>

                                    <div class="_cta-links">

                                        <? foreach($cta_links as $cta_link) { 
                                            $link = $cta_link['link'];
                                            $cta_class = ($cta_link === reset($cta_links)) ? '-primary' : '-outline'; ?>

                                            <a 
                                                href="<?= $link['url']; ?>" 
                                                class="_button <?= $cta_class; ?>"
                                                <? if($link['target']) { ?> target="<?= $link['target']; ?>"<? } ?>>

                                                <?= $link['title']; ?>

                                            </a>

                                        <? } ?>

                                    </div>

                                <? } ?>

                            </div>

                        </div>

                    </div>

                <? } ?>

            <? } ?>

        </div>
        
        <? if($navigation == 'true') { ?>
        <div class="
            _swiper-actions 
            -prev-next">
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
        <? } ?>

        <? if($pagination == 'true') { ?>
        <div class="
            _swiper-actions 
            -bullets">
            <div class="swiper-pagination"></div>
        </div>
        <? } ?>

    </div>

</section>

<section class="_container">
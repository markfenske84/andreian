</section>

<?php // To add or modify swiper setting options, refer to the following link: https://swiperjs.com/swiper-api

    // Define mapping for boolean values
    $boolean_mapping = array(
        'carousel_toggle' => 'carousel_toggle',
        'autoplay' => 'autoplay',
        'loop' => 'loop',
        'grayscale_logos' => 'grayscale_logos'
    );
    
    // Iterate through boolean settings and convert values
    foreach ($boolean_mapping as $setting_key => $variable_name) {
        ${$variable_name} = get_field($setting_key) ? 'true' : 'false';
    }
    
    // Other settings
    $transition_speed = get_field('transition_speed'); // Range (500-10000, step 500, default 6000)
    $slide_speed = get_field('slide_speed'); // Range (500-10000, step 500, default 6000)
    $slides_per_view = get_field('slides_per_view'); // Range (1-10, step 1, default 6)
    $logos = get_field('logos'); // Gallery ?>

<section 
    class="
        logo-carousel-section
        <?php if(isset($block['className'])) { echo ' ' . $block['className']; } ?>">

    <?php if($carousel_toggle == 'true') { ?>

    <div 
        class="swiper-container" 
        data-swiper="{'centeredSlides': true, 'loop': <?= $loop; ?>, 'autoplay': <?= $autoplay; ?>, 'speed': <?= $transition_speed; ?>, 'slidesPerView': 1.5, 'breakpoints': { '768': { 'slidesPerView': <?= $slides_per_view; ?> } } }">
        
        <div class="swiper-wrapper">

            <?php if($logos) { ?>

                <?php foreach($logos as $logo) { ?>

                    <?php // Determine if description contains a valid URL to use as a link
                       $logo_link = (!empty($logo['description']) && filter_var($logo['description'], FILTER_VALIDATE_URL)) ? $logo['description'] : false;
                    ?>

                    <div 
                        class="
                            swiper-slide 
                            _flex 
                            -justify-center 
                            -align-center" 
                        data-swiper-autoplay="<?= $slide_speed; ?>">

                        <?php if($logo_link) { ?>
                            <a href="<?= esc_url($logo_link); ?>" target="_blank" rel="noopener">
                        <?php } ?>

                        <img 
                            <?php if($grayscale_logos == 'true') { ?>
                            class="grayscale"
                            <?php } ?>
                            src="<?= $logo['url']; ?>" 
                            alt="<?= $logo['alt']; ?>" 
                            loading="lazy">

                        <?php if($logo_link) { ?>
                            </a>
                        <?php } ?>

                    </div>

                <?php } ?>

            <?php } ?>

        </div>

    </div>

    <?php } else { ?>

    <div class="_container">

        <?php if($logos) { ?>
            
            <div class="_flex -wrap -align-center -justify-center">

            <?php foreach($logos as $logo) { ?>

                <?php // Determine if description contains a valid URL to use as a link
                   $logo_link = (!empty($logo['description']) && filter_var($logo['description'], FILTER_VALIDATE_URL)) ? $logo['description'] : false;
                ?>

                <div class="logo">
                    <?php if($logo_link) { ?>
                        <a href="<?= esc_url($logo_link); ?>" target="_blank" rel="noopener">
                    <?php } ?>
                    <img 
                        <?php if($grayscale_logos == 'true') { ?>
                        class="grayscale"
                        <?php } ?>
                        src="<?= $logo['url']; ?>" 
                        alt="<?= $logo['alt']; ?>" 
                        loading="lazy">
                    <?php if($logo_link) { ?>
                        </a>
                    <?php } ?>
                </div>

            <?php } ?>

            </div>

        <?php } ?>

    </div>

    <?php } ?>

</section>

<section class="_container">
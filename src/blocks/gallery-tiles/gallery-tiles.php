</section>

<?
    $block_width = get_field('block_width'); // select, container, or full options
    $mobile_display = get_field('mobile_display'); // select, tiles (default) or swipe
    $modal_background = get_field('modal_background'); // color picker w/ opacity enabled
    $tile_border_radius = get_field('tile_border_radius'); // range 0-100, step 1
    $tile_gap = get_field('tile_gap'); // range 0-50, step 1
    $gallery_images = get_field('gallery_images'); // gallery 
?>

<? if ($gallery_images) { ?>
<section 
    class="
        gallery-tiles 
        -mobile-<?= $mobile_display; ?>
        <? if(isset($block['className'])) { echo ' ' . $block['className']; } ?>"
    style="padding: <?= $tile_gap; ?>px<? if ($block_width == 'container') { ?> 0<? } ?>;"
    role="region" aria-label="Image gallery">

    <? if ($block_width == 'container') { ?>
        <div class="_container">
    <? } ?>

        <div 
            class="
                _tiles 
                -mobile-<?= $mobile_display; ?>" 
            style="
                gap: <?= $tile_gap; ?>px; 
                grid-template-columns: repeat(auto-fill, minmax(calc(25% - <?= $tile_gap; ?>px), 1fr));"
            role="grid">
            
        <? foreach ($gallery_images as $image) { 
            $img_url = $image['url'];
            $img_alt = $image['alt']; ?>

            <button 
                class="_tile" 
                data-img-url="<?= $img_url; ?>"
                data-img-alt="<?= $img_alt ?? 'Image'; ?>"
                data-img-caption="<?= $image['caption']; ?>"
                data-img-description="<?= $image['description']; ?>"
                role="button" aria-label="<?= $img_alt ?? 'Image'; ?>">

                <img 
                    src="<?= $img_url; ?>" 
                    alt="<?= $img_alt ?? 'Image'; ?>" 
                    style="border-radius: <?= $tile_border_radius; ?>px;">

            </button>

        <? } ?>

        </div>

    <? if ($block_width == 'container') { ?>
        </div>
    <? } ?>

</section>
<? } ?>

<div 
    class="gallery-modal" 
    style="background-color: <?= $modal_background; ?>;"
    role="dialog" aria-label="Image viewer modal">

    <? get_template_part('src/components/close-button');  ?>

    <div class="_inner" role="document">

        <button class="_prev" aria-label="Previous image"></button>

        <img 
            src="" 
            alt="" 
            role="img" 
            aria-label="Displayed image">

        <button class="_next" aria-label="Next image"></button>

        <p class="_caption" aria-live="polite"></p>

    </div>

</div>

<section class="_container">

<?php
$accordions = get_field('accordions'); // repeater -> summery (text), details_inner (WYSIWYG)
?>

<?php if($accordions) { ?>
<div 
    <?php if(isset($block['anchor'])) { ?>
        id="<?= esc_attr($block['anchor']); ?>" 
    <?php } ?>
    class="
        accordions
        <?php if(isset($block['className'])) { echo ' ' . $block['className']; } ?>">

    <?php foreach($accordions as $index => $accordion) { ?>

        <details 
            class="accordion" 
            role="group" 
            aria-labelledby="summary-<?= $index; ?>" 
            <?= ($index === 0) ? 'open' : ''; ?>>

            <summary 
                class="_summary" 
                id="summary-<?= $index; ?>" 
                aria-label="Toggle element for collapsible accordion content"
                role="button"
                aria-controls="content-<?= $index; ?>"
                aria-expanded="<?= ($index === 0) ? 'true' : 'false'; ?>">

                <?= $accordion['summary']; ?>

            </summary>

            <div 
                class="_inner" 
                id="content-<?= $index; ?>"
                role="region"
                aria-labelledby="summary-<?= $index; ?>"
                aria-hidden="<?= ($index === 0) ? 'false' : 'true'; ?>">

                <?= $accordion['details_inner']; ?>

            </div>

        </details>

    <?php } ?>

</div>
<?php } ?>
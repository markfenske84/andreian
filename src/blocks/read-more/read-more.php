<?php
$unique_id = uniqid('read-more-label-');  
$read_more_content = get_field('read_more_content'); ?>

<details 
    class="
        read-more
        <?php if(isset($block['className'])) { echo ' ' . $block['className']; } ?>" 
    role="group" 
    aria-labelledby="<?= $unique_id ?>" 
    aria-expanded="false" 
    tabindex="0">

    <summary 
        id="<?= $unique_id ?>" 
        class="_label">
        + Read More
    </summary>

    <div 
        class="_inner">
        <?= $read_more_content; ?>
    </div>

</details>
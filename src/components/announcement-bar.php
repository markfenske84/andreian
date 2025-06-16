<? 
$announcement_bar_text = get_theme_mod('announcement_bar_text');
$announcement_bar_dismissal_reset = get_theme_mod('announcement_bar_dismissal_reset');

if (is_user_logged_in() && $announcement_bar_text) {
    echo '<style>#announcement-bar { display: block !important; }</style>';
} ?>

<? if ($announcement_bar_text) { ?>
<div 
    id="announcement-bar" 
    class="
        announcement-bar 
        _container 
        -max-width-100" 
    data-dismiss-length="<?= $announcement_bar_dismissal_reset; ?>">
    
    <div 
        class="_inner">

        <? if($announcement_bar_text) { ?>

            <p><?= $announcement_bar_text; ?></p>

        <? } ?>

    </div>

    <? get_template_part('src/components/close-button');  ?>

</div>
<? } ?>
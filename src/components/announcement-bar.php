<?php 
$announcement_bar_text = get_theme_mod('announcement_bar_text');
$announcement_bar_dismissal_reset = get_theme_mod('announcement_bar_dismissal_reset');
$announcement_bar_bg = chw_get_hex_theme_mod( 'announcement_bar_background_color', '#333333' );
$announcement_bar_text_color = chw_get_hex_theme_mod( 'announcement_bar_text_color', '#ffffff' );

if (is_user_logged_in() && $announcement_bar_text) {
    echo '<style>#announcement-bar { display: block !important; }</style>';
} ?>

<?php if ($announcement_bar_text) { ?>
<div 
    id="announcement-bar" 
    class="
        announcement-bar 
        _container 
        -max-width-100" 
    data-dismiss-length="<?= $announcement_bar_dismissal_reset; ?>"
    style="background-color: <?= esc_attr( $announcement_bar_bg ); ?>; color: <?= esc_attr( $announcement_bar_text_color ); ?>;">
    
    <div 
        class="_inner">

        <?php if($announcement_bar_text) { ?>

            <p class="-sm"><?= $announcement_bar_text; ?></p>

        <?php } ?>

    </div>

    <?php get_template_part('src/components/close-button');  ?>

</div>
<?php } ?>
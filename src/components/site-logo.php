<? 
// Logos
$custom_logo = get_theme_mod('custom_logo');

if ($custom_logo): ?>
    <a class="site-logo _flex" href="<?= esc_url( home_url( '/' ) ); ?>" title="<?= esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url">
        <img src="<?= $custom_logo; ?>" alt="Logo image for <?= bloginfo( 'name' ); ?>">
    </a>
<? else : ?>
    <a class="site-logo _flex" href="<?= esc_url( home_url( '/' ) ); ?>" title="<?= esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url"><?= bloginfo( 'name' ); ?></a>	
<? endif; ?>
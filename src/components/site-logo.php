<?php 
// Logos
$custom_logo = get_theme_mod('custom_logo');
$logo_dims   = '';

if ( $custom_logo ) {
	$attachment_id = attachment_url_to_postid( $custom_logo );
	if ( $attachment_id ) {
		$meta = wp_get_attachment_metadata( $attachment_id );
		if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
			$logo_dims = sprintf(
				' width="%d" height="%d"',
				(int) $meta['width'],
				(int) $meta['height']
			);
		}
	}
	if ( ! $logo_dims ) {
		$logo_dims = ' width="150" height="50"';
	}
}

if ($custom_logo): ?>
    <a class="site-logo _flex" href="<?= esc_url( home_url( '/' ) ); ?>" title="<?= esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url">
        <img src="<?= esc_url( $custom_logo ); ?>" alt="Logo image for <?= esc_attr( get_bloginfo( 'name' ) ); ?>"<?= $logo_dims; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
    </a>
<?php else : ?>
    <a class="site-logo _flex" href="<?= esc_url( home_url( '/' ) ); ?>" title="<?= esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url"><?= bloginfo( 'name' ); ?></a>	
<?php endif; ?>
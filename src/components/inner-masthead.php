<?php
$blog_masthead_image   = get_theme_mod( 'blog_masthead_image' );
$blog_masthead_heading = get_theme_mod( 'blog_masthead_heading' );

// Early output masthead for archive pages (categories, tags, authors, dates, custom taxonomies, etc.)
if ( is_archive() && ! is_home() ) {
	echo '<section class="masthead -has-image" style="background-image: url(\'' . esc_url( THEME_IMAGES . '/masthead-default.webp' ) . '\');">';
	echo '<div class="masthead__overlay"></div>';
	echo '<div class="-inner _container"><div class="masthead__content">';
	echo '<h1 class="masthead__title">' . get_the_archive_title() . '</h1>';
	echo '</div></div>';
	echo '</section>';
	return;
}

// Early output masthead for search results pages
if ( is_search() ) {
	echo '<section class="masthead -has-image" style="background-image: url(\'' . esc_url( THEME_IMAGES . '/masthead-default.webp' ) . '\');">';
	echo '<div class="masthead__overlay"></div>';
	echo '<div class="-inner _container"><div class="masthead__content">';
	echo '<h1 class="masthead__title">' . sprintf( esc_html__( 'Search results for: %s', 'chw' ), esc_html( get_search_query() ) ) . '</h1>';
	echo '</div></div>';
	echo '</section>';
	return;
}
?>

<?php if ( is_home() ) { ?>

	<section
		class="masthead -has-image"
		style="background-image: url('<?= esc_url( $blog_masthead_image ? $blog_masthead_image : THEME_IMAGES . '/masthead-default.webp' ); ?>');">

		<div class="masthead__overlay"></div>

		<div class="-inner _container">
			<div class="masthead__content">
				<h1 class="masthead__title"><?= esc_html( $blog_masthead_heading ); ?></h1>
			</div>
		</div>

	</section>

<?php } elseif ( is_page() || ( is_single() && get_post_type() === 'post' ) ) { ?>

	<?php
	$post_id = get_the_ID();

	if ( chw_is_masthead_disabled( $post_id ) ) {
		return;
	}

	$custom_page_title = chw_get_custom_page_title( $post_id );
	$background        = chw_get_masthead_background( $post_id );
	$globals           = chw_get_masthead_globals();

	$eyelash_icon   = get_post_meta( $post_id, 'masthead_eyelash_icon', true );
	$eyelash_text   = get_post_meta( $post_id, 'masthead_eyelash_text', true );
	$description     = get_post_meta( $post_id, 'masthead_description', true );
	$buttons        = get_post_meta( $post_id, 'masthead_buttons', true );
	$buttons        = is_array( $buttons ) ? $buttons : array();

	$sec_text       = get_post_meta( $post_id, 'masthead_secondary_cta_text', true );
	$sec_btn_text   = get_post_meta( $post_id, 'masthead_secondary_cta_button_text', true );
	$sec_btn_url    = get_post_meta( $post_id, 'masthead_secondary_cta_button_url', true );
	$sec_btn_icon   = get_post_meta( $post_id, 'masthead_secondary_cta_button_icon', true );

	$show_stats     = get_post_meta( $post_id, 'masthead_show_stats', true );
	$stats          = get_post_meta( $post_id, 'masthead_stats', true );
	$stats          = is_array( $stats ) ? $stats : array();

	$show_legal     = get_post_meta( $post_id, 'masthead_show_legal', true );
	$show_as_seen   = get_post_meta( $post_id, 'masthead_show_as_seen_in', true );
	$show_award     = get_post_meta( $post_id, 'masthead_show_award_seal', true );

	$rep_enabled    = get_post_meta( $post_id, 'masthead_rep_box_enabled', true );

	$title_html = $custom_page_title ? wp_kses( $custom_page_title, chw_masthead_title_allowed_html() ) : esc_html( get_the_title() );
	$is_blog_post = is_single() && 'post' === get_post_type();
	$has_sidebar = chw_singular_has_sidebar();
	?>

	<section
		class="masthead -has-image -editable<?php echo $is_blog_post ? ' -blog-post' : ''; ?><?php echo $has_sidebar ? ' -has-blog-sidebar' : ''; ?>"
		style="background-image: url('<?= esc_url( $background ); ?>');">

		<div class="masthead__overlay"></div>

		<div class="-inner _container">

			<?php if ( $has_sidebar ) : ?>
				<div class="masthead__posts-col">
			<?php endif; ?>

			<div class="masthead__content">

				<?php if ( $eyelash_text || $eyelash_icon ) { ?>
					<div class="masthead__eyelash _eyebrow -on-dark">
						<?php if ( $eyelash_icon ) { ?>
							<span class="_eyebrow__icon _text -primary"><?= chw_sanitize_inline_svg( $eyelash_icon ); ?></span>
						<?php } ?>
						<?php if ( $eyelash_text ) { ?>
							<span class="_eyebrow__label"><?= esc_html( $eyelash_text ); ?></span>
						<?php } ?>
					</div>
				<?php } ?>

				<?php if ( $is_blog_post ) { ?>
					<?php get_template_part( 'src/components/post-breadcrumbs' ); ?>
				<?php } ?>

				<h1 class="masthead__title"><?= $title_html; ?></h1>

				<?php if ( $is_blog_post ) { ?>
					<?php get_template_part( 'src/components/post-entry-meta', null, array( 'variant' => 'masthead' ) ); ?>
				<?php } ?>

				<?php if ( $description ) { ?>
					<p class="-xl masthead__description"><?= esc_html( $description ); ?></p>
				<?php } ?>

				<?php if ( ! empty( $buttons ) ) { ?>
					<div class="masthead__buttons">
						<?php foreach ( $buttons as $index => $button ) {
							$btn_modifier = isset( $button['modifier'] ) ? $button['modifier'] : '';
							$btn_class    = '_button ' . ( 0 === $index ? '-primary' : '-outline' );
							if ( $btn_modifier ) {
								$btn_class .= ' -' . sanitize_html_class( $btn_modifier );
							}
							$btn_text  = isset( $button['text'] ) ? $button['text'] : '';
							$btn_url   = isset( $button['url'] ) ? $button['url'] : '#';
							$btn_blank = ! empty( $button['new_tab'] );
							?>
							<a
								class="<?= esc_attr( $btn_class ); ?>"
								href="<?= esc_url( $btn_url ); ?>"
								<?php if ( $btn_blank ) { ?>target="_blank" rel="noopener"<?php } ?>>
								<?= esc_html( $btn_text ); ?>
							</a>
						<?php } ?>
					</div>
				<?php } ?>

				<?php if ( $sec_text || $sec_btn_text ) { ?>
					<div class="masthead__secondary-cta">
						<?php if ( $sec_text ) { ?>
							<span class="masthead__secondary-cta-text"><?= chw_sanitize_masthead_inline_text( $sec_text ); ?></span>
						<?php } ?>
						<?php if ( $sec_btn_text ) { ?>
							<a class="_button -outline -small" href="<?= esc_url( $sec_btn_url ? $sec_btn_url : '#' ); ?>">
								<?php if ( $sec_btn_icon ) { ?>
									<span class="masthead__button-icon"><?= chw_sanitize_inline_svg( $sec_btn_icon ); ?></span>
								<?php } ?>
								<span><?= esc_html( $sec_btn_text ); ?></span>
							</a>
						<?php } ?>
					</div>
				<?php } ?>

				<?php if ( $show_stats && ! empty( $stats ) ) { ?>
					<div class="masthead__stats">
						<?php foreach ( $stats as $stat ) { ?>
							<div class="masthead__stat">
								<span class="masthead__stat-value"><?= esc_html( isset( $stat['value'] ) ? $stat['value'] : '' ); ?></span>
								<span class="masthead__stat-label"><?= esc_html( isset( $stat['label'] ) ? $stat['label'] : '' ); ?></span>
							</div>
						<?php } ?>
					</div>
				<?php } ?>

				<?php if ( $show_legal && ! empty( $globals['legalText'] ) ) { ?>
					<div class="masthead__legal"><?= wp_kses_post( $globals['legalText'] ); ?></div>
				<?php } ?>

			</div>

			<?php if ( $has_sidebar ) : ?>
				</div>
				<div class="masthead__sidebar-spacer" aria-hidden="true"></div>
			<?php endif; ?>

			<?php if ( $rep_enabled ) {
				$rep_eyebrow  = get_post_meta( $post_id, 'masthead_rep_box_eyebrow', true );
				$rep_heading  = get_post_meta( $post_id, 'masthead_rep_box_heading', true );
				$rep_text     = get_post_meta( $post_id, 'masthead_rep_box_text', true );
				$rep_btn_text = get_post_meta( $post_id, 'masthead_rep_box_button_text', true );
				$rep_btn_url  = get_post_meta( $post_id, 'masthead_rep_box_button_url', true );
				$rep_phone    = get_post_meta( $post_id, 'masthead_rep_box_phone', true );
				?>
				<aside class="masthead__rep-box">
					<?php if ( $rep_eyebrow ) { ?>
						<span class="masthead__rep-box-eyebrow _eyebrow -plain _text -primary">&bull; <?= esc_html( $rep_eyebrow ); ?></span>
					<?php } ?>
					<?php if ( $rep_heading ) { ?>
						<h2 class="masthead__rep-box-heading"><?= esc_html( $rep_heading ); ?></h2>
					<?php } ?>
					<?php if ( $rep_text ) { ?>
						<p class="masthead__rep-box-text"><?= esc_html( $rep_text ); ?></p>
					<?php } ?>
					<?php if ( $rep_btn_text ) { ?>
						<a class="_button -primary" href="<?= esc_url( $rep_btn_url ? $rep_btn_url : '#' ); ?>"><?= esc_html( $rep_btn_text ); ?></a>
					<?php } ?>
					<?php if ( $rep_phone ) { ?>
						<a class="_button -secondary" href="tel:<?= esc_attr( preg_replace( '/[^0-9+]/', '', $rep_phone ) ); ?>"><?= esc_html( $rep_phone ); ?></a>
					<?php } ?>
				</aside>
			<?php } ?>

		</div>

		<?php
		$has_award   = $show_award && ( ! empty( $globals['award']['title'] ) || ! empty( $globals['award']['image'] ) );
		$has_as_seen = $show_as_seen && ! empty( $globals['asSeenInLogos'] );
		?>

		<?php if ( $has_award || $has_as_seen ) { ?>
			<div class="masthead__footer _container">

				<?php if ( $has_award ) { ?>
					<div class="masthead__award">
						<?php if ( ! empty( $globals['award']['image'] ) ) { ?>
							<img class="masthead__award-img" src="<?= esc_url( $globals['award']['image'] ); ?>" alt="" width="54" height="54" />
						<?php } ?>
						<div class="masthead__award-text">
							<?php if ( ! empty( $globals['award']['eyebrow'] ) ) { ?>
								<span class="masthead__award-eyebrow _eyebrow -plain _text -muted"><?= esc_html( $globals['award']['eyebrow'] ); ?></span>
							<?php } ?>
							<?php if ( ! empty( $globals['award']['title'] ) ) { ?>
								<strong class="masthead__award-title"><?= esc_html( $globals['award']['title'] ); ?></strong>
							<?php } ?>
							<?php if ( ! empty( $globals['award']['source'] ) ) { ?>
								<span class="masthead__award-source"><?= esc_html( $globals['award']['source'] ); ?></span>
							<?php } ?>
						</div>
					</div>
				<?php } ?>

				<?php if ( $has_as_seen ) { ?>
					<div class="masthead__as-seen-in">
						<?php if ( ! empty( $globals['asSeenInLabel'] ) ) { ?>
							<span class="masthead__as-seen-in-label"><?= esc_html( $globals['asSeenInLabel'] ); ?></span>
						<?php } ?>
						<div
							class="masthead__as-seen-in-logos"
							data-count="<?= esc_attr( count( $globals['asSeenInLogos'] ) ); ?>">
							<?php foreach ( $globals['asSeenInLogos'] as $logo ) { ?>
								<img src="<?= esc_url( $logo['url'] ); ?>" alt="<?= esc_attr( $logo['alt'] ); ?>" width="160" height="40" loading="lazy" />
							<?php } ?>
						</div>
					</div>
				<?php } ?>

			</div>
		<?php } ?>

	</section>

<?php } ?>

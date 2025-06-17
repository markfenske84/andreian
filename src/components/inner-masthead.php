<?php
$blog_masthead_image = get_theme_mod('blog_masthead_image');
$blog_masthead_heading = get_theme_mod('blog_masthead_heading');
if ( ! is_home() ) {
	$masthead         = get_field( 'masthead' );

	// Avoid PHP warnings when the ACF field group or its sub-fields are empty.
	if ( is_array( $masthead ) ) {
		$masthead_toggle    = $masthead['masthead_toggle'] ?? false;
		$custom_page_title  = $masthead['custom_page_title'] ?? '';
	} else {
		$masthead_toggle    = false;
		$custom_page_title  = '';
	}

	// Ensure single blog posts always show a masthead with the post title even if no ACF field is set.
	if ( is_single() && get_post_type() === 'post' ) {
		$masthead_toggle = true;
	}
}

// Early output masthead for archive pages (categories, tags, authors, dates, custom taxonomies, etc.)
if ( is_archive() && ! is_home() ) {
	echo '<section class="masthead _container -max-width-100">';
	echo '<div class="-inner">';
	echo '<h1>' . get_the_archive_title() . '</h1>';
	echo '</div>';
	echo '</section>';
	return; // Bail out so the rest of the template does not render an additional masthead.
}

// Early output masthead for search results pages
if ( is_search() ) {
	echo '<section class="masthead _container -max-width-100">';
	echo '<div class="-inner">';
	echo '<h1>' . sprintf( esc_html__( 'Search results for: %s', 'arabesque' ), esc_html( get_search_query() ) ) . '</h1>';
	echo '</div>';
	echo '</section>';
	return; // Bail out so the rest of the template does not render an additional masthead.
}
?>

<? if(!is_home()) { ?>

	<? if($masthead_toggle) { ?> 

	<section 
		class="
			masthead 
			_container 
			-max-width-100">

		<div 
			class="-inner">

			<h1>

				<? if($custom_page_title) { ?>
					<?= esc_html($custom_page_title); ?>
				<? } else { ?>
					<? the_title(); ?>
				<? } ?>

			</h1>

		</div>

	</section>

	<? } ?>

<? } else { ?> 
	
	<section 
		class="
			masthead 
			_container 
			-max-width-100" 
		<? if ($blog_masthead_image) { ?>
			style="
				background-image: url('<?= esc_url($blog_masthead_image); ?>');"
		<? } ?>>

		<div 
			class="-inner">

			<h1>
				
				<?= esc_html($blog_masthead_heading); ?>
				
			</h1>

		</div>

	</section>

<? } ?>
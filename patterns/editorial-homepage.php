<?php
/**
 * Title: Andreian Editorial Homepage
 * Slug: andreian/editorial-homepage
 * Categories: featured, posts
 * Description: Editorial homepage with lead, latest, random sidebar, and category sections.
 * Inserter: true
 */
?>

<!-- wp:group {"align":"wide","className":"andreian-home","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide andreian-home">
	<!-- wp:group {"className":"editorial-section editorial-section--lead","layout":{"type":"constrained"}} -->
	<div class="wp-block-group editorial-section editorial-section--lead">
		<!-- wp:heading {"className":"sr-only"} -->
		<h2 class="wp-block-heading sr-only">Featured stories</h2>
		<!-- /wp:heading -->
		<!-- wp:andreian/post {"postsToShow":5,"layout":"hero-tiles","prioritizeFirstImage":true} /-->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"editorial-section editorial-section--latest","layout":{"type":"constrained"}} -->
	<div class="wp-block-group editorial-section editorial-section--latest">
		<!-- wp:heading {"textAlign":"center","className":"section-title"} -->
		<h2 class="wp-block-heading has-text-align-center section-title">Latest Entries</h2>
		<!-- /wp:heading -->
		<!-- wp:andreian/post {"postsToShow":9,"layout":"list","offset":5,"showExcerpt":true,"showRandomSidebar":true,"sidebarPostsToShow":9,"sidebarTitle":"Random"} /-->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"editorial-section","layout":{"type":"constrained"}} -->
	<div class="wp-block-group editorial-section">
		<!-- wp:heading {"textAlign":"center","className":"section-title"} -->
		<h2 class="wp-block-heading has-text-align-center section-title">Body</h2>
		<!-- /wp:heading -->
		<!-- wp:andreian/post {"categorySlug":"body","postsToShow":9,"layout":"category-tiles"} /-->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"editorial-section","layout":{"type":"constrained"}} -->
	<div class="wp-block-group editorial-section">
		<!-- wp:heading {"textAlign":"center","className":"section-title"} -->
		<h2 class="wp-block-heading has-text-align-center section-title">Mind</h2>
		<!-- /wp:heading -->
		<!-- wp:andreian/post {"categorySlug":"mind","postsToShow":9,"layout":"category-tiles"} /-->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"editorial-section","layout":{"type":"constrained"}} -->
	<div class="wp-block-group editorial-section">
		<!-- wp:heading {"textAlign":"center","className":"section-title"} -->
		<h2 class="wp-block-heading has-text-align-center section-title">Spirit</h2>
		<!-- /wp:heading -->
		<!-- wp:andreian/post {"categorySlug":"spirit","postsToShow":9,"layout":"category-tiles"} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

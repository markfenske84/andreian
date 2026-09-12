<?php
global $wp_query;

get_template_part(
	'src/components/post-card',
	null,
	array(
		'post_id'             => get_the_ID(),
		'layout'              => 'archive-grid',
		'position'            => isset( $wp_query->current_post ) ? (int) $wp_query->current_post + 1 : 1,
		'show_excerpt'        => true,
		'show_all_categories' => true,
		'show_share_links'    => true,
		'current_category_id' => get_queried_object_id(),
		'image_size'          => 'andreian-feature',
	)
);

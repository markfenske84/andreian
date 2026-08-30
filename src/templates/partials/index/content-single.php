<?php
global $wp_query;

get_template_part(
	'src/components/post-card',
	null,
	array(
		'post_id'      => get_the_ID(),
		'layout'       => 'list',
		'position'     => isset( $wp_query->current_post ) ? (int) $wp_query->current_post + 1 : 1,
		'show_excerpt' => true,
		'item_list'    => true,
		'image_size'   => 'andreian-card',
	)
);

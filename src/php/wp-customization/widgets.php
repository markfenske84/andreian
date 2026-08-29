<?php
/**
 * Widget areas.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'widgets_init', 'andreian_widgets_init' );

function andreian_widgets_init() {
	$sidebar_args = array(
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<p class="widget-title">',
		'after_title'   => '</p>',
	);

	register_sidebar(
		array_merge(
			$sidebar_args,
			array(
				'name'        => __( 'Sidebar', 'andreian' ),
				'id'          => 'sidebar',
				'description' => __( 'Sidebar widget area', 'andreian' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$sidebar_args,
			array(
				'name'        => __( 'Footer', 'andreian' ),
				'id'          => 'footer_main',
				'description' => __( 'Footer widget area', 'andreian' ),
			)
		)
	);
}

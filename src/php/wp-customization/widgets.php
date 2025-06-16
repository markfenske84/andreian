<?php
/**
 * Declaring widgets
 *
 * @package webfor
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Add filter to the parameters passed to a widget's display callback.
 * The filter is evaluated on both the front and the back end!
 *
 * @link https://developer.wordpress.org/reference/hooks/dynamic_sidebar_params/
 */

add_action( 'widgets_init', 'krypton_widgets_init' );

if ( ! function_exists( 'krypton_widgets_init' ) ) {
	/**
	 * Initializes themes widgets.
	 */
	function krypton_widgets_init() {

		register_sidebar(
			array(
				'name'          => __( 'Sidebar', 'krypton' ),
				'id'            => 'sidebar',
				'description'   => __( 'Sidebar widget area', 'krypton' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar( 
			array(
				'name'          => 'Footer Column 1',
				'id'            => 'footer_col1',
				'before_widget' => '<div class="footer-widget">',
				'after_widget'  => '</div>',
			) 
		);
		
		register_sidebar( 
			array(
				'name'          => 'Footer Column 2',
				'id'            => 'footer_col2',
				'before_widget' => '<div class="footer-widget">',
				'after_widget'  => '</div>',
			) 
		);
		
		register_sidebar( 
			array(
				'name'          => 'Footer Column 3',
				'id'            => 'footer_col3',
				'before_widget' => '<div class="footer-widget">',
				'after_widget'  => '</div>',
			) 
		);
		
		register_sidebar( 
			array(
				'name'          => 'Footer Column 4',
				'id'            => 'footer_col4',
				'before_widget' => '<div class="footer-widget">',
				'after_widget'  => '</div>',
			) 
		);
	}
} // endif function_exists( 'understrap_widgets_init' ).

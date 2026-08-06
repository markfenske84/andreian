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

add_action( 'widgets_init', 'chw_widgets_init' );

if ( ! function_exists( 'chw_widgets_init' ) ) {
	/**
	 * Initializes themes widgets.
	 */
	function chw_widgets_init() {
		$footer_sidebar_args = array(
			'before_widget' => '<div class="footer-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<p class="widget-title">',
			'after_title'   => '</p>',
			'show_in_rest'  => true,
		);

		register_sidebar(
			array(
				'name'          => __( 'Sidebar', 'chw' ),
				'id'            => 'sidebar',
				'description'   => __( 'Sidebar widget area', 'chw' ),
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<p class="widget-title">',
				'after_title'   => '</p>',
			)
		);

		register_sidebar(
			array_merge(
				$footer_sidebar_args,
				array(
					'name'        => 'Footer Column 1',
					'id'          => 'footer_col1',
					'description' => __( 'Brand content: Site Logo, Paragraph (tagline), phone link, Social Links block.', 'chw' ),
				)
			)
		);

		register_sidebar(
			array_merge(
				$footer_sidebar_args,
				array(
					'name'        => 'Footer Column 2',
					'id'          => 'footer_col2',
					'description' => __( 'Widget title + Navigation block (e.g. Company).', 'chw' ),
				)
			)
		);

		register_sidebar(
			array_merge(
				$footer_sidebar_args,
				array(
					'name'        => 'Footer Column 3',
					'id'          => 'footer_col3',
					'description' => __( 'Widget title + Navigation block (e.g. Coverage).', 'chw' ),
				)
			)
		);

		register_sidebar(
			array_merge(
				$footer_sidebar_args,
				array(
					'name'        => 'Footer Column 4',
					'id'          => 'footer_col4',
					'description' => __( 'Widget title + Navigation block (e.g. Support).', 'chw' ),
				)
			)
		);

		register_sidebar(
			array_merge(
				$footer_sidebar_args,
				array(
					'name'        => 'Footer Column 5',
					'id'          => 'footer_col5',
					'description' => __( 'Widget title + Navigation block (e.g. Legal).', 'chw' ),
				)
			)
		);

		register_sidebar(
			array_merge(
				$footer_sidebar_args,
				array(
					'name'        => 'Footer Legal',
					'id'          => 'footer_legal',
					'description' => __( 'Disclaimer / fine print: Paragraph or Custom HTML block.', 'chw' ),
				)
			)
		);
	}
}

/**
 * Ensure footer widget areas exist in the sidebars_widgets option.
 */
function chw_ensure_footer_sidebars() {
	$sidebars_widgets = get_option( 'sidebars_widgets', array() );

	if ( ! is_array( $sidebars_widgets ) ) {
		$sidebars_widgets = array();
	}

	$footer_sidebars = array(
		'footer_col1',
		'footer_col2',
		'footer_col3',
		'footer_col4',
		'footer_col5',
		'footer_legal',
	);

	$updated = false;

	foreach ( $footer_sidebars as $sidebar_id ) {
		if ( ! array_key_exists( $sidebar_id, $sidebars_widgets ) ) {
			$sidebars_widgets[ $sidebar_id ] = array();
			$updated                       = true;
		}
	}

	if ( $updated ) {
		update_option( 'sidebars_widgets', $sidebars_widgets );
	}
}
add_action( 'after_setup_theme', 'chw_ensure_footer_sidebars', 20 );

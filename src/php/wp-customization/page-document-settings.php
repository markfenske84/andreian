<?php
/**
 * Page document settings — masthead post meta and block editor panel.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Allowed HTML for the masthead headline (lets editors wrap text in <span> for the orange accent).
 */
function chw_masthead_title_allowed_html() {
	return array(
		'span' => array( 'class' => true ),
		'br'   => array(),
	);
}

/**
 * Sanitize callback for the headline meta — keep only the limited HTML above.
 */
function chw_sanitize_masthead_title( $value ) {
	return wp_kses( (string) $value, chw_masthead_title_allowed_html() );
}

/**
 * Sanitize callback for short rich-text fields that allow inline emphasis
 * (e.g. the secondary CTA lead-in). Permits <span>, <strong>, <em>, <br>.
 */
function chw_sanitize_masthead_inline_text( $value ) {
	return wp_kses( (string) $value, array(
		'span'   => array( 'class' => true ),
		'strong' => array( 'class' => true ),
		'b'      => array( 'class' => true ),
		'em'     => array( 'class' => true ),
		'i'      => array( 'class' => true ),
		'br'     => array(),
	) );
}

/**
 * Sanitize callback for raw inline SVG icon fields.
 */
function chw_sanitize_inline_svg( $value ) {
	$value = (string) $value;

	if ( $value === '' ) {
		return '';
	}

	$allowed = array(
		'svg'      => array( 'class' => true, 'xmlns' => true, 'width' => true, 'height' => true, 'viewbox' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'aria-hidden' => true, 'focusable' => true, 'role' => true ),
		'path'     => array( 'd' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'fill-rule' => true, 'clip-rule' => true, 'opacity' => true ),
		'g'        => array( 'fill' => true, 'stroke' => true, 'transform' => true ),
		'circle'   => array( 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true ),
		'rect'     => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true, 'fill' => true, 'stroke' => true ),
		'line'     => array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true, 'stroke-width' => true ),
		'polyline' => array( 'points' => true, 'fill' => true, 'stroke' => true ),
		'polygon'  => array( 'points' => true, 'fill' => true, 'stroke' => true ),
		'ellipse'  => array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true, 'fill' => true, 'stroke' => true ),
		'defs'     => array(),
		'title'    => array(),
	);

	return wp_kses( $value, $allowed );
}

/**
 * Register all masthead-related post meta for pages and standard posts.
 */
function chw_register_masthead_meta() {
	$post_types = array( 'page', 'post' );

	$auth = function () {
		return current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' );
	};

	foreach ( $post_types as $post_type ) {

		// Headline override (allows limited HTML for the orange <span> accent).
		register_post_meta( $post_type, 'custom_page_title', array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => 'chw_sanitize_masthead_title',
			'auth_callback'     => $auth,
		) );

		// Eyelash (eyebrow) icon + text.
		register_post_meta( $post_type, 'masthead_eyelash_icon', array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => 'chw_sanitize_inline_svg',
			'auth_callback'     => $auth,
		) );

		register_post_meta( $post_type, 'masthead_eyelash_text', array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $auth,
		) );

		// Description paragraph below the H1.
		register_post_meta( $post_type, 'masthead_description', array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => 'sanitize_textarea_field',
			'auth_callback'     => $auth,
		) );

		// CTA buttons repeater. First = primary, rest = outline.
		register_post_meta( $post_type, 'masthead_buttons', array(
			'type'              => 'array',
			'single'            => true,
			'default'           => array(),
			'auth_callback'     => $auth,
			'sanitize_callback' => 'chw_sanitize_masthead_buttons',
			'show_in_rest'      => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'text'     => array( 'type' => 'string' ),
							'url'      => array( 'type' => 'string' ),
							'modifier' => array( 'type' => 'string' ),
							'new_tab'  => array( 'type' => 'boolean' ),
						),
					),
				),
			),
		) );

		// Secondary CTA (text + single button beneath the main buttons).
		register_post_meta( $post_type, 'masthead_secondary_cta_text', array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => 'chw_sanitize_masthead_inline_text',
			'auth_callback'     => $auth,
		) );

		register_post_meta( $post_type, 'masthead_secondary_cta_button_text', array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $auth,
		) );

		register_post_meta( $post_type, 'masthead_secondary_cta_button_url', array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'auth_callback'     => $auth,
		) );

		register_post_meta( $post_type, 'masthead_secondary_cta_button_icon', array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => 'chw_sanitize_inline_svg',
			'auth_callback'     => $auth,
		) );

		// Stats.
		register_post_meta( $post_type, 'masthead_show_stats', array(
			'type'              => 'boolean',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => false,
			'auth_callback'     => $auth,
		) );

		register_post_meta( $post_type, 'masthead_stats', array(
			'type'              => 'array',
			'single'            => true,
			'default'           => array(),
			'auth_callback'     => $auth,
			'sanitize_callback' => 'chw_sanitize_masthead_stats',
			'show_in_rest'      => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'value' => array( 'type' => 'string' ),
							'label' => array( 'type' => 'string' ),
						),
					),
				),
			),
		) );

		// Global content toggles.
		register_post_meta( $post_type, 'masthead_show_legal', array(
			'type'              => 'boolean',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => false,
			'auth_callback'     => $auth,
		) );

		register_post_meta( $post_type, 'masthead_show_as_seen_in', array(
			'type'              => 'boolean',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => false,
			'auth_callback'     => $auth,
		) );

		register_post_meta( $post_type, 'masthead_show_award_seal', array(
			'type'              => 'boolean',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => false,
			'auth_callback'     => $auth,
		) );

		// Territory representative CTA box.
		register_post_meta( $post_type, 'masthead_rep_box_enabled', array(
			'type'              => 'boolean',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => false,
			'auth_callback'     => $auth,
		) );

		register_post_meta( $post_type, 'masthead_rep_box_eyebrow', array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $auth,
		) );

		register_post_meta( $post_type, 'masthead_rep_box_heading', array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $auth,
		) );

		register_post_meta( $post_type, 'masthead_rep_box_text', array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => 'sanitize_textarea_field',
			'auth_callback'     => $auth,
		) );

		register_post_meta( $post_type, 'masthead_rep_box_button_text', array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $auth,
		) );

		register_post_meta( $post_type, 'masthead_rep_box_button_url', array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'auth_callback'     => $auth,
		) );

		register_post_meta( $post_type, 'masthead_rep_box_phone', array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => $auth,
		) );
	}
}
add_action( 'init', 'chw_register_masthead_meta' );

/**
 * Allowed icon modifiers for masthead CTA buttons (map to ._button modifier classes).
 */
function chw_masthead_button_modifiers() {
	return array( '', 'arrow', 'phone', 'download' );
}

/**
 * Sanitize the CTA buttons repeater value.
 */
function chw_sanitize_masthead_buttons( $buttons ) {
	if ( ! is_array( $buttons ) ) {
		return array();
	}

	$allowed_modifiers = chw_masthead_button_modifiers();
	$clean = array();

	foreach ( $buttons as $button ) {
		if ( ! is_array( $button ) ) {
			continue;
		}

		$modifier = isset( $button['modifier'] ) ? (string) $button['modifier'] : '';
		if ( ! in_array( $modifier, $allowed_modifiers, true ) ) {
			$modifier = '';
		}

		$clean[] = array(
			'text'     => isset( $button['text'] ) ? sanitize_text_field( $button['text'] ) : '',
			'url'      => isset( $button['url'] ) ? esc_url_raw( $button['url'] ) : '',
			'modifier' => $modifier,
			'new_tab'  => ! empty( $button['new_tab'] ),
		);
	}

	return $clean;
}

/**
 * Sanitize the stats repeater value.
 */
function chw_sanitize_masthead_stats( $stats ) {
	if ( ! is_array( $stats ) ) {
		return array();
	}

	$clean = array();

	foreach ( $stats as $stat ) {
		if ( ! is_array( $stat ) ) {
			continue;
		}

		$clean[] = array(
			'value' => isset( $stat['value'] ) ? sanitize_text_field( $stat['value'] ) : '',
			'label' => isset( $stat['label'] ) ? sanitize_text_field( $stat['label'] ) : '',
		);
	}

	return $clean;
}

/**
 * Enqueue the block editor masthead panel + live preview scripts.
 */
function chw_enqueue_masthead_editor_assets() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! $screen || ! in_array( $screen->post_type, array( 'page', 'post' ), true ) ) {
		return;
	}

	$panel_path = get_template_directory() . '/src/js/editor/page-title-panel.js';
	wp_enqueue_script(
		'chw-masthead-panel',
		get_template_directory_uri() . '/src/js/editor/page-title-panel.js',
		array( 'wp-plugins', 'wp-edit-post', 'wp-components', 'wp-element', 'wp-data', 'wp-i18n', 'wp-block-editor' ),
		chw_asset_version( $panel_path ),
		true
	);

	$preview_path = get_template_directory() . '/src/js/editor/masthead-preview.js';
	wp_enqueue_script(
		'chw-masthead-preview',
		get_template_directory_uri() . '/src/js/editor/masthead-preview.js',
		array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-data', 'wp-i18n' ),
		chw_asset_version( $preview_path ),
		true
	);

	$layout_path = get_template_directory() . '/src/js/editor/page-layout.js';
	wp_enqueue_script(
		'chw-page-layout',
		get_template_directory_uri() . '/src/js/editor/page-layout.js',
		array( 'wp-data', 'wp-dom-ready' ),
		chw_asset_version( $layout_path ),
		true
	);

	wp_localize_script(
		'chw-page-layout',
		'chwPageLayout',
		array(
			'builderTemplate' => chw_get_page_builder_template_slug(),
		)
	);

	// Provide global content (legal text, award seal, as-seen-in logos) to the live preview.
	wp_localize_script( 'chw-masthead-preview', 'chwMastheadGlobals', chw_get_masthead_globals() );
}
add_action( 'enqueue_block_editor_assets', 'chw_enqueue_masthead_editor_assets' );

/**
 * Custom page title for masthead, with legacy ACF meta fallback.
 */
function chw_get_custom_page_title( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$custom_page_title = get_post_meta( $post_id, 'custom_page_title', true );

	if ( ! $custom_page_title ) {
		$custom_page_title = get_post_meta( $post_id, 'masthead_custom_page_title', true );
	}

	return $custom_page_title;
}

/**
 * Resolve the masthead background image: page Featured Image, else theme default.
 */
function chw_get_masthead_background( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	if ( $post_id && has_post_thumbnail( $post_id ) ) {
		$url = get_the_post_thumbnail_url( $post_id, 'full' );
		if ( $url ) {
			return $url;
		}
	}

	return THEME_IMAGES . '/masthead-default.webp';
}

/**
 * Collect the global masthead content (Customizer-driven) used by both the
 * front-end render and the editor live preview.
 */
function chw_get_masthead_globals() {
	$logos = array();
	$logo_ids = get_theme_mod( 'as_seen_in_logos', '' );

	if ( ! empty( $logo_ids ) ) {
		$ids = is_array( $logo_ids ) ? $logo_ids : json_decode( $logo_ids, true );

		if ( is_array( $ids ) ) {
			foreach ( $ids as $id ) {
				$id = absint( $id );
				if ( ! $id ) {
					continue;
				}
				$src = wp_get_attachment_image_url( $id, 'medium' );
				if ( $src ) {
					$logos[] = array(
						'url' => $src,
						'alt' => get_post_meta( $id, '_wp_attachment_image_alt', true ),
					);
				}
			}
		}
	}

	return array(
		'defaultBackground' => THEME_IMAGES . '/masthead-default.webp',
		'legalText'    => get_theme_mod( 'legal_text', '' ),
		'asSeenInLabel' => get_theme_mod( 'as_seen_in_label', 'As Seen In' ),
		'asSeenInLogos' => $logos,
		'award'        => array(
			'image'    => get_theme_mod( 'masthead_award_image', '' ),
			'eyebrow'  => get_theme_mod( 'masthead_award_eyebrow', '' ),
			'title'    => get_theme_mod( 'masthead_award_title', '' ),
			'source'   => get_theme_mod( 'masthead_award_source', '' ),
		),
	);
}

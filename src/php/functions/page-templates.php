<?php
/**
 * Page template registration, resolution, and one-time backfill.
 *
 * Templates live in src/templates/ (two levels deep), which core's
 * get_post_templates() scandir does not discover on its own.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registered theme templates keyed by relative file path.
 *
 * @return array<string, string> slug => label
 */
function chw_get_registered_page_templates() {
	return array(
		'src/templates/template-page-builder.php' => __( 'Page Builder (Full Width)', 'chw' ),
		'src/templates/template-sidebar.php'      => __( 'Sidebar', 'chw' ),
	);
}

/**
 * Slug for the full-width page builder template.
 *
 * @return string
 */
function chw_get_page_builder_template_slug() {
	return 'src/templates/template-page-builder.php';
}

/**
 * Whether the given page uses the page builder (full-width) template.
 *
 * @param int|null $post_id Post ID.
 * @return bool
 */
function chw_page_uses_builder_template( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	if ( ! $post_id || 'page' !== get_post_type( $post_id ) ) {
		return false;
	}

	$template = get_page_template_slug( $post_id );

	return chw_get_page_builder_template_slug() === $template;
}

/**
 * Inject src/templates/ files into the page Template dropdown.
 *
 * @param array<string, string> $post_templates Existing templates.
 * @return array<string, string>
 */
function chw_register_theme_page_templates( $post_templates ) {
	foreach ( chw_get_registered_page_templates() as $slug => $label ) {
		$post_templates[ $slug ] = $label;
	}

	return $post_templates;
}
add_filter( 'theme_page_templates', 'chw_register_theme_page_templates' );

/**
 * Sidebar template is also available to posts.
 *
 * @param array<string, string> $post_templates Existing templates.
 * @return array<string, string>
 */
function chw_register_theme_post_templates( $post_templates ) {
	$post_templates['src/templates/template-sidebar.php'] = __( 'Sidebar', 'chw' );

	return $post_templates;
}
add_filter( 'theme_post_templates', 'chw_register_theme_post_templates' );

/**
 * Resolve nested template paths when core cannot locate them.
 *
 * @param string $template Path to the template file about to be loaded.
 * @return string
 */
function chw_resolve_page_template( $template ) {
	if ( ! is_singular() ) {
		return $template;
	}

	$post_id = get_queried_object_id();

	if ( ! $post_id ) {
		return $template;
	}

	$slug = get_page_template_slug( $post_id );

	if ( ! $slug || 'default' === $slug ) {
		return $template;
	}

	$registered = chw_get_registered_page_templates();

	if ( ! isset( $registered[ $slug ] ) ) {
		return $template;
	}

	$resolved = locate_template( array( $slug ) );

	return $resolved ? $resolved : $template;
}
add_filter( 'template_include', 'chw_resolve_page_template', 99 );

/**
 * One-time backfill: existing pages on the default template keep full-width layout.
 */
function chw_backfill_page_builder_templates() {
	if ( get_option( 'chw_page_template_backfill_v1' ) ) {
		return;
	}

	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$skip_ids = array_filter(
		array(
			(int) get_option( 'page_on_front' ),
			(int) get_option( 'page_for_posts' ),
		)
	);

	$page_ids = get_posts(
		array(
			'post_type'              => 'page',
			'post_status'            => 'any',
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	$builder_slug = chw_get_page_builder_template_slug();

	foreach ( $page_ids as $page_id ) {
		if ( in_array( (int) $page_id, $skip_ids, true ) ) {
			continue;
		}

		$current = get_post_meta( $page_id, '_wp_page_template', true );

		if ( $current && 'default' !== $current ) {
			continue;
		}

		update_post_meta( $page_id, '_wp_page_template', $builder_slug );
	}

	update_option( 'chw_page_template_backfill_v1', 1, true );
}
add_action( 'admin_init', 'chw_backfill_page_builder_templates' );

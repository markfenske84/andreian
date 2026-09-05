<?php
/**
 * Server render callback for the Andreian Post block.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build shared query args for a post collection block.
 *
 * @param array $attributes Block attributes.
 * @param array $overrides  Optional query overrides.
 * @return array
 */
function andreian_get_post_block_query_args( $attributes, $overrides = array() ) {
	$posts_to_show = min( 15, max( 1, (int) ( $attributes['postsToShow'] ?? 9 ) ) );
	$category_id   = max( 0, (int) ( $attributes['categoryId'] ?? 0 ) );
	$category_slug = sanitize_title( $attributes['categorySlug'] ?? '' );
	$offset        = min( 40, max( 0, (int) ( $attributes['offset'] ?? 0 ) ) );
	$order_by      = $attributes['orderBy'] ?? 'date';
	$order_by      = in_array( $order_by, array( 'date', 'title', 'rand', 'custom' ), true ) ? $order_by : 'date';
	$order         = isset( $attributes['order'] ) && 'ASC' === strtoupper( $attributes['order'] ) ? 'ASC' : 'DESC';
	$excluded_ids  = array_filter( array_map( 'absint', $attributes['excludePostIds'] ?? array() ) );
	$layout        = $attributes['layout'] ?? 'grid';

	if ( ! empty( $attributes['excludeFeaturedPosts'] ) && function_exists( 'andreian_get_hero_tiles_post_ids' ) ) {
		$excluded_ids = array_values(
			array_unique(
				array_merge(
					$excluded_ids,
					andreian_get_hero_tiles_post_ids( 8, $excluded_ids )
				)
			)
		);
		$offset = 0;
	}

	if ( 'full' === $layout ) {
		$posts_to_show = 1;
	} elseif ( 'hero-tiles' === $layout ) {
		$posts_to_show = 8;
	}

	$query_args = array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => $posts_to_show,
		'orderby'                => $order_by,
		'order'                  => $order,
		'offset'                 => $offset,
		'post__not_in'           => $excluded_ids,
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => true,
	);

	if ( $category_slug ) {
		$query_args['category_name'] = $category_slug;
	} elseif ( $category_id ) {
		$query_args['cat'] = $category_id;
	}

	return array_merge( $query_args, $overrides );
}

/**
 * Resolve positional custom post IDs, backfilling empty slots.
 *
 * @param array  $attributes Block attributes.
 * @param string $layout     Collection layout.
 * @return int[]
 */
function andreian_get_custom_collection_post_ids( $attributes, $layout ) {
	$limit = min( 15, max( 1, (int) ( $attributes['postsToShow'] ?? 9 ) ) );

	if ( 'full' === $layout ) {
		$limit = 1;
	} elseif ( 'hero-tiles' === $layout ) {
		$limit = 8;
	}

	$selected = array_map( 'absint', (array) ( $attributes['selectedPostIds'] ?? array() ) );
	$slots    = array();

	for ( $index = 0; $index < $limit; $index++ ) {
		$slots[] = isset( $selected[ $index ] ) ? $selected[ $index ] : 0;
	}

	$used = array_values( array_filter( $slots ) );
	$needed = $limit - count( $used );

	$backfill = array();
	if ( $needed > 0 ) {
		$backfill_query = new WP_Query(
			array(
				'post_type'              => 'post',
				'post_status'            => 'publish',
				'posts_per_page'         => $needed,
				'orderby'                => 'date',
				'order'                  => 'DESC',
				'post__not_in'           => $used,
				'ignore_sticky_posts'    => true,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
			)
		);
		$backfill = $backfill_query->posts;
	}

	$backfill_index = 0;
	foreach ( $slots as $index => $post_id ) {
		if ( $post_id ) {
			continue;
		}

		$slots[ $index ] = isset( $backfill[ $backfill_index ] ) ? (int) $backfill[ $backfill_index ] : 0;
		++$backfill_index;
	}

	return array_values( array_filter( array_map( 'absint', $slots ) ) );
}

/**
 * Render a dynamic post collection.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Saved block content.
 * @param WP_Block $block      Block instance.
 * @return string
 */
function andreian_render_post_block( $attributes, $content, $block ) {
	$allowed_layouts = array( 'grid', 'list', 'full', 'hero-tiles', 'category-tiles' );
	$layout          = isset( $attributes['layout'] ) && in_array( $attributes['layout'], $allowed_layouts, true )
		? $attributes['layout']
		: 'grid';
	$prioritize      = ! empty( $attributes['prioritizeFirstImage'] ) && ! is_admin();

	$widget_sidebar_requested = 'grid' === $layout && ! empty( $attributes['showSidebar'] );
	$random_sidebar_requested = 'list' === $layout && ! empty( $attributes['showRandomSidebar'] );

	$query_args = andreian_get_post_block_query_args( array_merge( $attributes, array( 'layout' => $layout ) ) );
	$is_custom  = 'custom' === ( $attributes['orderBy'] ?? 'date' );

	if ( $is_custom ) {
		$custom_ids = andreian_get_custom_collection_post_ids( $attributes, $layout );

		if ( empty( $custom_ids ) ) {
			if ( is_admin() || wp_is_json_request() ) {
				return '<p class="andreian-posts__empty">' . esc_html__( 'Choose posts for this collection.', 'andreian' ) . '</p>';
			}

			return '';
		}

		$query_args = array_merge(
			$query_args,
			array(
				'post__in'       => $custom_ids,
				'orderby'        => 'post__in',
				'posts_per_page' => count( $custom_ids ),
				'offset'         => 0,
				'post__not_in'   => array(),
			)
		);
		unset( $query_args['category_name'], $query_args['cat'] );
	} elseif ( 'hero-tiles' === $layout && function_exists( 'andreian_get_hero_tiles_post_ids' ) ) {
		$hero_ids = andreian_get_hero_tiles_post_ids(
			8,
			$query_args['post__not_in'] ?? array()
		);

		if ( ! empty( $hero_ids ) ) {
			$query_args = array_merge(
				$query_args,
				array(
					'post__in'       => $hero_ids,
					'orderby'        => 'post__in',
					'posts_per_page' => count( $hero_ids ),
					'offset'         => 0,
				)
			);
		}
	}

	$query = new WP_Query( $query_args );

	if ( ! $query->have_posts() ) {
		if ( is_admin() || wp_is_json_request() ) {
			return '<p class="andreian-posts__empty">' . esc_html__( 'No posts match this collection.', 'andreian' ) . '</p>';
		}

		return '';
	}

	$main_post_ids = wp_list_pluck( $query->posts, 'ID' );
	$has_widget_sidebar = $widget_sidebar_requested && (
		is_active_sidebar( 'homepage_latest' ) ||
		is_admin() ||
		wp_is_json_request()
	);

	$sidebar_posts_to_show = min( 15, max( 1, (int) ( $attributes['sidebarPostsToShow'] ?? 9 ) ) );
	$sidebar_title         = isset( $attributes['sidebarTitle'] ) && '' !== trim( $attributes['sidebarTitle'] )
		? $attributes['sidebarTitle']
		: __( 'Random', 'andreian' );

	$random_query = null;
	if ( $random_sidebar_requested ) {
		$random_query = new WP_Query(
			andreian_get_post_block_query_args(
				$attributes,
				array(
					'posts_per_page' => $sidebar_posts_to_show,
					'orderby'        => 'rand',
					'order'          => 'DESC',
					'offset'         => 0,
					'post__not_in'   => array_merge(
						array_filter( array_map( 'absint', $attributes['excludePostIds'] ?? array() ) ),
						$main_post_ids
					),
				)
			)
		);
	}

	$has_random_sidebar = $random_sidebar_requested && (
		( $random_query && $random_query->have_posts() ) ||
		is_admin() ||
		wp_is_json_request()
	);

	$has_sidebar = $has_widget_sidebar || $has_random_sidebar;

	$main_layout_class = $has_widget_sidebar ? 'grid' : $layout;

	$wrapper_attributes = get_block_wrapper_attributes(
		$has_sidebar
			? array( 'class' => 'andreian-post-collection andreian-post-collection--sidebar' )
			: array(
				'class' => 'andreian-posts andreian-posts--' . $layout,
				'role'  => 'list',
			)
	);

	ob_start();
	?>
	<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo $has_sidebar ? '' : ' itemscope itemtype="https://schema.org/ItemList"'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php if ( $has_sidebar ) : ?>
			<div class="andreian-posts andreian-posts--<?php echo esc_attr( $main_layout_class ); ?>" role="list" itemscope itemtype="https://schema.org/ItemList">
		<?php endif; ?>
		<meta itemprop="numberOfItems" content="<?php echo esc_attr( $query->post_count ); ?>">
		<?php
		$position = 0;
		while ( $query->have_posts() ) :
			$query->the_post();
			++$position;
			$image_size = in_array( $layout, array( 'full', 'hero-tiles' ), true )
				? 'andreian-feature'
				: 'andreian-card';
			get_template_part(
				'src/components/post-card',
				null,
				array(
					'post_id'      => get_the_ID(),
					'layout'       => $main_layout_class,
					'position'     => $position,
					'priority'     => $prioritize && 1 === $position,
					'show_excerpt' => 'full' === $layout || ! empty( $attributes['showExcerpt'] ),
					'item_list'    => true,
					'image_size'   => $image_size,
				)
			);
		endwhile;
		wp_reset_postdata();

		if ( ! empty( $attributes['showArchiveLink'] ) ) {
			$category_slug = sanitize_title( $attributes['categorySlug'] ?? '' );
			$archive_url   = '';

			if ( ! empty( $attributes['archiveLinkUrl'] ) ) {
				$archive_url = $attributes['archiveLinkUrl'];
			} elseif ( $category_slug ) {
				$category = get_category_by_slug( $category_slug );
				if ( $category ) {
					$archive_url = get_category_link( $category );
				}
			}

			$archive_label = ! empty( $attributes['archiveLinkLabel'] )
				? $attributes['archiveLinkLabel']
				: __( 'See more', 'andreian' );

			if ( $archive_url && ! is_wp_error( $archive_url ) ) {
				printf(
					'<p class="andreian-posts__archive-link"><a href="%1$s">%2$s</a></p>',
					esc_url( $archive_url ),
					esc_html( $archive_label )
				);
			}
		}
		?>
		<?php if ( $has_sidebar ) : ?>
			</div>
			<aside class="andreian-post-collection__sidebar" aria-label="<?php esc_attr_e( 'Latest entries sidebar', 'andreian' ); ?>">
				<div class="andreian-post-collection__sidebar-inner">
					<?php if ( $has_random_sidebar ) : ?>
						<?php
						get_template_part(
							'src/components/random-slideshow',
							null,
							array(
								'query' => $random_query,
								'title' => $sidebar_title,
							)
						);
						?>
					<?php endif; ?>

					<?php if ( $has_random_sidebar || $has_widget_sidebar ) : ?>
						<div class="andreian-post-collection__sidebar-widgets">
							<?php if ( is_active_sidebar( 'homepage_latest' ) ) : ?>
								<?php dynamic_sidebar( 'homepage_latest' ); ?>
							<?php elseif ( is_admin() || wp_is_json_request() ) : ?>
								<p class="andreian-posts__empty"><?php esc_html_e( 'Add CTA widgets to Homepage Latest Sidebar.', 'andreian' ); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</aside>
		<?php endif; ?>
	</div>
	<?php

	return (string) ob_get_clean();
}

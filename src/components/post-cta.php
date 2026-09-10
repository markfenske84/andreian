<?php
/**
 * ConvertKit CTA for single posts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_singular( 'post' ) ) {
	return;
}

andreian_render_convertkit_cta( 'post-cta' );

<?php
/**
 * Page entry header.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<header class="entry-header">
	<h1 class="entry-title"<?php echo function_exists( 'aht_headline_attributes' ) ? aht_headline_attributes( get_the_ID() ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>><?php the_title(); ?></h1>
</header>

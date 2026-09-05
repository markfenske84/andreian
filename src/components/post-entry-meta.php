<?php
/**
 * Post entry meta.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id     = get_the_ID();
$author_name = andreian_get_post_author_name( $post_id );
$author_url  = andreian_get_post_author_url( $post_id );
$categories  = andreian_post_categories();
?>

<div class="entry-meta">
	<ul class="entry-meta__list">
		<?php if ( $categories ) : ?>
			<li class="entry-meta__item"><span class="entry-meta__value"><?php echo wp_kses_post( $categories ); ?></span></li>
		<?php endif; ?>
		<?php if ( $author_name ) : ?>
			<li class="entry-meta__item"><a href="<?php echo esc_url( $author_url ); ?>"><?php echo esc_html( $author_name ); ?></a></li>
		<?php endif; ?>
		<li class="entry-meta__item"><time datetime="<?php echo esc_attr( andreian_get_post_datetime( $post_id ) ); ?>"><?php echo esc_html( andreian_get_post_display_date( $post_id ) ); ?></time></li>
		<li class="entry-meta__item"><span class="entry-meta__value"><?php echo esc_html( andreian_get_reading_time( $post_id ) ); ?></span></li>
	</ul>
</div>

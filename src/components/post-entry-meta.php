<?php
/**
 * Post entry meta: category, date, and author.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id     = get_the_ID();
$author_name = chw_get_post_author_name( $post_id );
$author_url  = chw_get_post_author_url( $post_id );
$categories  = chw_post_categories();
$variant = isset( $args['variant'] ) ? $args['variant'] : 'default';
?>

<div class="entry-meta<?php echo 'masthead' === $variant ? ' -on-dark' : ''; ?>">
	<ul class="entry-meta__list">
		<?php if ( $categories ) : ?>
			<li class="entry-meta__item">
				<span class="entry-meta__label"><?php esc_html_e( 'Posted In', 'chw' ); ?></span>
				<span class="entry-meta__value meta-category"><?php echo wp_kses_post( $categories ); ?></span>
			</li>
		<?php endif; ?>

		<li class="entry-meta__item">
			<span class="entry-meta__label"><?php echo esc_html( chw_get_post_date_label( $post_id ) ); ?></span>
			<time class="entry-meta__value meta-date" datetime="<?php echo esc_attr( chw_get_post_datetime( $post_id ) ); ?>">
				<?php echo esc_html( chw_get_post_display_date( $post_id ) ); ?>
			</time>
		</li>

		<?php if ( $author_name ) : ?>
			<li class="entry-meta__item">
				<span class="entry-meta__label"><?php esc_html_e( 'By', 'chw' ); ?></span>
				<span class="entry-meta__value meta-author">
					<a href="<?php echo esc_url( $author_url ); ?>"><?php echo esc_html( $author_name ); ?></a>
				</span>
			</li>
		<?php endif; ?>
	</ul>
</div>

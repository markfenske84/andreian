<?php
/**
 * Shared post/page sidebar widgets.
 *
 * @var array $args Template arguments.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$label  = isset( $args['label'] ) ? $args['label'] : __( 'Post sidebar', 'andreian' );
$drawer = ! empty( $args['drawer'] );
$class  = '_sidebar post-sidebar';

if ( $drawer ) {
	$class .= ' post-sidebar--drawer';
}
?>

<aside
	<?php if ( $drawer ) : ?>id="andreian-post-sidebar"<?php endif; ?>
	class="<?php echo esc_attr( $class ); ?>"
	aria-label="<?php echo esc_attr( $label ); ?>"
>
	<div class="post-sidebar__inner">
		<?php dynamic_sidebar( 'sidebar' ); ?>
	</div>
</aside>

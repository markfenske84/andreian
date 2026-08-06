<?php
/**
 * Customizer control: an ordered, repeatable list of images.
 *
 * Stores the selected attachment IDs as a JSON-encoded array string.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'CHW_Multi_Image_Control' ) ) {

	class CHW_Multi_Image_Control extends WP_Customize_Control {

		public $type = 'chw_multi_image';

		/**
		 * Enqueue the control scripts + the media library.
		 */
		public function enqueue() {
			wp_enqueue_media();

			$src = get_template_directory_uri() . '/src/js/customizer/multi-image-control.js';
			$path = get_template_directory() . '/src/js/customizer/multi-image-control.js';

			wp_enqueue_script(
				'chw-multi-image-control',
				$src,
				array( 'jquery', 'customize-controls', 'jquery-ui-sortable' ),
				file_exists( $path ) ? filemtime( $path ) : null,
				true
			);
		}

		/**
		 * Render the control markup.
		 */
		public function render_content() {
			$value = $this->value();
			$ids   = array();

			if ( ! empty( $value ) ) {
				$decoded = json_decode( $value, true );
				if ( is_array( $decoded ) ) {
					$ids = array_map( 'absint', $decoded );
				}
			}
			?>
			<div class="chw-multi-image-control">
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif; ?>

				<?php if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
				<?php endif; ?>

				<ul class="chw-multi-image-control__list">
					<?php foreach ( $ids as $id ) :
						$thumb = wp_get_attachment_image_url( $id, 'thumbnail' );
						if ( ! $thumb ) {
							continue;
						}
						?>
						<li class="chw-multi-image-control__item" data-id="<?php echo esc_attr( $id ); ?>">
							<img src="<?php echo esc_url( $thumb ); ?>" alt="" />
							<button type="button" class="chw-multi-image-control__remove" aria-label="<?php esc_attr_e( 'Remove image', 'chw' ); ?>">&times;</button>
						</li>
					<?php endforeach; ?>
				</ul>

				<button type="button" class="button chw-multi-image-control__add"><?php esc_html_e( 'Add / Edit Images', 'chw' ); ?></button>

				<input
					type="hidden"
					class="chw-multi-image-control__value"
					value="<?php echo esc_attr( $value ); ?>"
					<?php $this->link(); ?>
				/>
			</div>
			<?php
		}
	}
}

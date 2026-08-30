<?php
/**
 * Customizer control: repeatable social profile links.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'Andreian_Social_Links_Control' ) ) {

	class Andreian_Social_Links_Control extends WP_Customize_Control {

		public $type = 'andreian_social_links';

		/**
		 * Platform options (slug => label).
		 *
		 * @var array<string, string>
		 */
		public $platforms = array();

		public function enqueue() {
			$path = get_template_directory() . '/src/js/customizer/social-links-control.js';

			wp_enqueue_script(
				'andreian-social-links-control',
				get_template_directory_uri() . '/src/js/customizer/social-links-control.js',
				array( 'jquery', 'customize-controls', 'jquery-ui-sortable' ),
				file_exists( $path ) ? filemtime( $path ) : null,
				true
			);
		}

		protected function get_rows() {
			$value = $this->value();
			$rows  = array();

			if ( empty( $value ) ) {
				return $rows;
			}

			$decoded = json_decode( $value, true );

			if ( ! is_array( $decoded ) ) {
				return $rows;
			}

			foreach ( $decoded as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}

				$rows[] = array(
					'platform' => isset( $row['platform'] ) ? (string) $row['platform'] : '',
					'url'      => isset( $row['url'] ) ? (string) $row['url'] : '',
				);
			}

			return $rows;
		}

		protected function platform_options( $selected = '' ) {
			foreach ( $this->platforms as $slug => $label ) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $slug ),
					selected( $selected, $slug, false ),
					esc_html( $label )
				);
			}
		}

		public function render_content() {
			$rows  = $this->get_rows();
			$value = $this->value();
			?>
			<div class="andreian-social-links-control">
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif; ?>

				<?php if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
				<?php endif; ?>

				<ul class="andreian-social-links-control__list">
					<?php foreach ( $rows as $row ) : ?>
						<li class="andreian-social-links-control__item">
							<span class="andreian-social-links-control__handle dashicons dashicons-move" aria-hidden="true"></span>
							<select class="andreian-social-links-control__platform">
								<?php $this->platform_options( $row['platform'] ); ?>
							</select>
							<input
								type="url"
								class="andreian-social-links-control__url"
								placeholder="https://"
								value="<?php echo esc_attr( $row['url'] ); ?>" />
							<button type="button" class="andreian-social-links-control__remove button-link" aria-label="<?php esc_attr_e( 'Remove link', 'andreian' ); ?>">&times;</button>
						</li>
					<?php endforeach; ?>
				</ul>

				<button type="button" class="button andreian-social-links-control__add"><?php esc_html_e( 'Add Social Link', 'andreian' ); ?></button>

				<script type="text/html" class="andreian-social-links-control__row-template">
					<li class="andreian-social-links-control__item">
						<span class="andreian-social-links-control__handle dashicons dashicons-move" aria-hidden="true"></span>
						<select class="andreian-social-links-control__platform">
							<?php $this->platform_options(); ?>
						</select>
						<input type="url" class="andreian-social-links-control__url" placeholder="https://" value="" />
						<button type="button" class="andreian-social-links-control__remove button-link" aria-label="<?php esc_attr_e( 'Remove link', 'andreian' ); ?>">&times;</button>
					</li>
				</script>

				<input
					type="hidden"
					class="andreian-social-links-control__value"
					value="<?php echo esc_attr( $value ); ?>"
					<?php $this->link(); ?>
				/>
			</div>
			<?php
		}
	}
}

<?php
/**
 * Customizer control: an ordered, repeatable list of social links.
 *
 * Each row is a platform (mapped to a Font Awesome brand slug) + a URL.
 * The full list is stored as a JSON-encoded array string:
 *   [ { "platform": "facebook-f", "url": "https://..." }, ... ]
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'CHW_Social_Links_Control' ) ) {

	class CHW_Social_Links_Control extends WP_Customize_Control {

		public $type = 'chw_social_links';

		/**
		 * Platform options (FA brand slug => label). Injected on construction.
		 *
		 * @var array
		 */
		public $platforms = array();

		/**
		 * Enqueue the control script.
		 */
		public function enqueue() {
			$src  = get_template_directory_uri() . '/src/js/customizer/social-links-control.js';
			$path = get_template_directory() . '/src/js/customizer/social-links-control.js';

			wp_enqueue_script(
				'chw-social-links-control',
				$src,
				array( 'jquery', 'customize-controls', 'jquery-ui-sortable' ),
				file_exists( $path ) ? filemtime( $path ) : null,
				true
			);
		}

		/**
		 * Decode the stored value into an array of rows.
		 *
		 * @return array
		 */
		protected function get_rows() {
			$value = $this->value();
			$rows  = array();

			if ( ! empty( $value ) ) {
				$decoded = json_decode( $value, true );
				if ( is_array( $decoded ) ) {
					foreach ( $decoded as $row ) {
						if ( ! is_array( $row ) ) {
							continue;
						}
						$rows[] = array(
							'platform' => isset( $row['platform'] ) ? (string) $row['platform'] : '',
							'url'      => isset( $row['url'] ) ? (string) $row['url'] : '',
						);
					}
				}
			}

			return $rows;
		}

		/**
		 * Render the <option> tags for a platform select.
		 *
		 * @param string $selected Currently selected platform slug.
		 */
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

		/**
		 * Render the control markup.
		 */
		public function render_content() {
			$rows  = $this->get_rows();
			$value = $this->value();
			?>
			<div class="chw-social-links-control">
				<?php if ( ! empty( $this->label ) ) : ?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php endif; ?>

				<?php if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
				<?php endif; ?>

				<ul class="chw-social-links-control__list">
					<?php foreach ( $rows as $row ) : ?>
						<li class="chw-social-links-control__item">
							<span class="chw-social-links-control__handle dashicons dashicons-move" aria-hidden="true"></span>
							<select class="chw-social-links-control__platform">
								<?php $this->platform_options( $row['platform'] ); ?>
							</select>
							<input
								type="url"
								class="chw-social-links-control__url"
								placeholder="https://"
								value="<?php echo esc_attr( $row['url'] ); ?>" />
							<button type="button" class="chw-social-links-control__remove button-link" aria-label="<?php esc_attr_e( 'Remove link', 'chw' ); ?>">&times;</button>
						</li>
					<?php endforeach; ?>
				</ul>

				<button type="button" class="button chw-social-links-control__add"><?php esc_html_e( 'Add Social Link', 'chw' ); ?></button>

				<?php // Template used by JS to create new rows. ?>
				<script type="text/html" class="chw-social-links-control__row-template">
					<li class="chw-social-links-control__item">
						<span class="chw-social-links-control__handle dashicons dashicons-move" aria-hidden="true"></span>
						<select class="chw-social-links-control__platform">
							<?php $this->platform_options(); ?>
						</select>
						<input type="url" class="chw-social-links-control__url" placeholder="https://" value="" />
						<button type="button" class="chw-social-links-control__remove button-link" aria-label="<?php esc_attr_e( 'Remove link', 'chw' ); ?>">&times;</button>
					</li>
				</script>

				<input
					type="hidden"
					class="chw-social-links-control__value"
					value="<?php echo esc_attr( $value ); ?>"
					<?php $this->link(); ?>
				/>
			</div>
			<?php
		}
	}
}

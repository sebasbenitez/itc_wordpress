<?php
/**
 * Gutenberg google fonts class.
 *
 * @package woodmart
 */

namespace XTS\Gutenberg;

use XTS\Singleton;
use XTS\Admin\Modules\Options\Google_Fonts\Google_Fonts as Global_Google_Fonts;
use XTS\Modules\Styles_Storage;

/**
 * Google Fonts module.
 *
 * @package woodmart
 */
class Google_Fonts extends Singleton {

	/**
	 * Post ID being saved.
	 *
	 * @var int
	 */
	private $save_post_id = 0;

	/**
	 * Init.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wp', array( $this, 'enqueue_google_fonts' ), 130 );
		add_action( 'init', array( $this, 'enqueue_google_fonts' ), 130 );
		add_action( 'save_post', array( $this, 'remove_post_meta' ), 5 );
		add_action( 'save_post', array( $this, 'set_save_post_id' ), 6 );

		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_google_fonts_on_editor' ), 10 );
	}

	/**
	 * Enqueue google fonts on editor.
	 *
	 * @return void
	 */
	public function enqueue_google_fonts_on_editor() {
		if ( ! is_admin() ) {
			return;
		}

		$this->enqueue_google_fonts();

		if ( woodmart_get_opt( 'local_google_fonts' ) ) {
			$fonts_storage = new Styles_Storage( 'local-google-fonts', 'option', '', false );
			$fonts_storage->inline_css();
		}
	}

	/**
	 * Remove post meta.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function remove_post_meta( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		delete_post_meta( $post_id, 'xts_blocks_google_fonts' );
	}

	/**
	 * Set save post ID.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function set_save_post_id( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$this->save_post_id = $post_id;
	}

	/**
	 * Add google font at block.
	 *
	 * @param array $font_data Font data.
	 * @return void
	 */
	public function add_google_font( $font_data ) {
		$font_data = wp_parse_args(
			$font_data,
			array(
				'font-family' => '',
				'font-weight' => '',
				'font-style'  => '',
				'font-subset' => '',
			)
		);

		$post_id = $this->get_post_id();

		if ( ! $post_id ) {
			return;
		}

		$fonts = (array) get_post_meta( $post_id, 'xts_blocks_google_fonts', true );

		$fonts[] = $font_data;

		update_post_meta( $post_id, 'xts_blocks_google_fonts', $fonts );
	}

	/**
	 * Enqueue google fonts in frontend.
	 *
	 * @return void
	 */
	public function enqueue_google_fonts() {
		$post_id = $this->get_post_id();

		if ( ! $post_id ) {
			return;
		}
		$fonts = get_post_meta( $post_id, 'xts_blocks_google_fonts', true );

		if ( empty( $fonts ) ) {
			return;
		}

		foreach ( $fonts as $font ) {
			Global_Google_Fonts::add_google_font( $font );
		}
	}

	/**
	 * Enqueue inline google fonts.
	 *
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function enqueue_inline_google_fonts( $post_id ) {
		$fonts = get_post_meta( $post_id, 'xts_blocks_google_fonts', true );

		if ( empty( $fonts ) ) {
			return;
		}

		Global_Google_Fonts::get_instance()->enqueue_inline_fonts( $fonts, $post_id );
	}

	/**
	 * Get post ID.
	 *
	 * @return int
	 */
	protected function get_post_id() {
		global $post;

		$post_id = '';

		if ( is_admin() && isset( $_GET['post'] ) ) { // phpcs:ignore
			$post_id = $_GET['post']; // phpcs:ignore
		} elseif ( ! empty( $post ) && ! empty( $post->ID ) ) {
			$post_id = $post->ID;
		} elseif ( ! empty( $this->save_post_id ) ) {
			$post_id = $this->save_post_id;
		}

		return $post_id;
	}
}

Google_Fonts::get_instance();

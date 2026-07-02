<?php
/**
 * Class for managing HTML blocks in the Woodmart theme.
 *
 * @package woodmart
 */

namespace XTS\Admin\Modules\Dashboard;

use XTS\Singleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Class HTML_Blocks
 */
class HTML_Blocks extends Singleton {
	/**
	 * Constructor for the class.
	 */
	public function init() {
		add_filter( 'manage_edit-cms_block_columns', array( $this, 'edit_html_blocks_columns' ) );
		add_action( 'manage_cms_block_posts_custom_column', array( $this, 'manage_html_blocks_columns' ), 10, 2 );
	}

	/**
	 * Edit the columns displayed in the HTML blocks post type list table.
	 *
	 * @param array $columns The existing columns.
	 */
	public function edit_html_blocks_columns( $columns ) {
		unset( $columns['date'] );

		$new_columns = array(
			'shortcode'     => esc_html__( 'Shortcode', 'woodmart' ),
			'wd_categories' => esc_html__( 'Categories', 'woodmart' ),
			'date'          => esc_html__( 'Date', 'woodmart' ),
		);

		$columns = $columns + $new_columns;

		return $columns;
	}

	/**
	 * Manage the content of custom columns for the HTML blocks post type.
	 *
	 * @param string $column  The name of the column being rendered.
	 * @param int    $post_id The ID of the current post.
	 */
	public function manage_html_blocks_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'shortcode':
				echo '[html_block id="' . absint( $post_id ) . '"]';
				break;
			case 'wd_categories':
				$terms     = wp_get_post_terms( $post_id, 'cms_block_cat' );
				$post_type = get_post_type( $post_id );
				$keys      = array_keys( $terms );
				$last_key  = end( $keys );

				if ( ! $terms ) {
					echo '—';
				}

				foreach ( $terms as $key => $term ) {
					$name = $term->name;

					if ( $key !== $last_key ) {
						$name .= ',';
					}
					?>
					<a href="<?php echo esc_url( 'edit.php?post_type=' . $post_type . '&cms_block_cat=' . $term->slug ); ?>">
						<?php echo esc_html( $name ); ?>
					</a>
					<?php
				}
				break;
		}
	}
}

HTML_Blocks::get_instance();

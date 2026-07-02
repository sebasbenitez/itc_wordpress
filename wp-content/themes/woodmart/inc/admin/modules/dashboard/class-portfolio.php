<?php
/**
 * Class for managing portfolio items in the Woodmart theme.
 *
 * @package woodmart
 */

namespace XTS\Admin\Modules\Dashboard;

use XTS\Singleton;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Class Portfolio
 */
class Portfolio extends Singleton {
	/**
	 * Constructor for the class.
	 */
	public function init() {
		add_filter( 'manage_edit-portfolio_columns', array( $this, 'edit_portfolio_columns' ) );
		add_action( 'manage_portfolio_posts_custom_column', array( $this, 'manage_portfolio_columns' ), 10, 2 );
	}

	/**
	 * Edit the columns displayed in the portfolio post type list table.
	 *
	 * @param array $columns The existing columns.
	 */
	public function edit_portfolio_columns( $columns ) {
		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'thumb'       => '',
			'title'       => esc_html__( 'Title', 'woodmart' ),
			'project-cat' => esc_html__( 'Categories', 'woodmart' ),
			'date'        => esc_html__( 'Date', 'woodmart' ),
		);

		return $columns;
	}

	/**
	 * Manage the content of custom columns for the portfolio post type.
	 *
	 * @param string $column  The name of the column being rendered.
	 * @param int    $post_id The ID of the current post.
	 */
	public function manage_portfolio_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'thumb':
				if ( has_post_thumbnail( $post_id ) ) {
					the_post_thumbnail( array( 60, 60 ) );
				}
				break;
			case 'project-cat':
				$terms = get_the_terms( $post_id, 'project-cat' );

				if ( $terms && ! is_wp_error( $terms ) ) {
					$cats_links = array();

					foreach ( $terms as $term ) {
						$cats_links[] = $term->name;
					}

					$cats = join( ', ', $cats_links );
				}
				?>
				<span><?php echo esc_html( $cats ); ?></span>
				<?php
				break;
		}
	}
}

Portfolio::get_instance();

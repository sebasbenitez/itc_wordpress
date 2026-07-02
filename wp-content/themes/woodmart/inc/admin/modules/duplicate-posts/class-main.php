<?php
/**
 * Class for duplicate posts
 *
 * @package woodmart
 */

namespace XTS\Admin\Modules\Duplicate_Posts;

use XTS\Singleton;
use WP_Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Class Main
 */
class Main extends Singleton {
	/**
	 * Array of post types that support duplication.
	 *
	 * @var array
	 */
	public $allowed_post_types = array(
		'woodmart_layout',
		'woodmart_slide',
		'cms_block',
		'wd_popup',
		'wd_floating_block',
		'wd_woo_est_del',
		'wd_custom_label',
	);

	/**
	 * Constructor for the class.
	 */
	public function init() {
		add_filter( 'post_row_actions', array( $this, 'duplicate_action' ), 10, 2 );
		add_filter( 'admin_action_woodmart_duplicate_post_as_draft', array( $this, 'duplicate_post_as_draft' ), 10, 2 );
	}

	/**
	 * Add duplicate link to post actions.
	 *
	 * @param array   $actions The existing post actions.
	 * @param WP_Post $post    The post object.
	 *
	 * @return array Modified post actions with duplicate link.
	 */
	public function duplicate_action( $actions, $post ) {
		if ( ! in_array( $post->post_type, $this->allowed_post_types, true ) || ! current_user_can( 'edit_posts' ) ) {
			return $actions;
		}

		$url = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'woodmart_duplicate_post_as_draft',
					'post'   => $post->ID,
				),
				'admin.php'
			),
			'woodmart_duplicate_post_as_draft',
			'duplicate_nonce'
		);

		ob_start();
		?>
		<a href="<?php echo esc_url( $url ); ?>" title="<?php esc_html_e( 'Duplicate this item', 'woodmart' ); ?>" rel="permalink">
			<?php esc_html_e( 'Duplicate', 'woodmart' ); ?>
		</a>
		<?php
		$actions['duplicate'] = ob_get_clean();

		return $actions;
	}

	/**
	 * Handle the duplication of a post as a draft.
	 */
	public function duplicate_post_as_draft() {
		if (
			! isset( $_REQUEST['post'] ) ||
			! isset( $_REQUEST['action'] ) ||
			'woodmart_duplicate_post_as_draft' !== $_REQUEST['action']
		) {
			wp_die( 'No post to duplicate has been supplied!' );
		}

		/*
		 * Nonce verification
		 */
		if (
			! isset( $_GET['duplicate_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['duplicate_nonce'] ) ), 'woodmart_duplicate_post_as_draft' )
		) {
			return;
		}

		$post_id = absint( $_REQUEST['post'] );
		$post    = get_post( $post_id );

		/*
		 * If post data exists, create the post duplicate.
		 */
		if ( isset( $post ) && null !== $post ) {
			$new_post_id = $this->create_post( $post );

			$this->duplicate_taxonomies( $post_id, $new_post_id );

			$this->duplicate_post_meta( $post_id, $new_post_id );

			wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
			exit;
		} else {
			wp_die( 'Post creation failed, could not find original post: ' . esc_html( $post_id ) );
		}
	}

	/**
	 * Create a duplicate of the given post.
	 *
	 * @param WP_Post $post The post to duplicate.
	 *
	 * @return int The ID of the newly created post.
	 */
	public function create_post( $post ) {
		$post_content = $post->post_content;

		if ( has_blocks( $post_content ) ) {
			$post_content = wp_slash( $post_content );
		}

		/*
		* New post data array.
		*/
		$args = array(
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => get_current_user_id(),
			'post_content'   => $post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title . ' (duplicate)',
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order,
		);

		/*
		* Insert the post by wp_insert_post() function.
		*/
		return wp_insert_post( $args );
	}

	/**
	 * Duplicate the taxonomies of the original post to the new post.
	 *
	 * @param int $post_id     The ID of the original post.
	 * @param int $new_post_id The ID of the new post.
	 */
	public function duplicate_taxonomies( $post_id, $new_post_id ) {
		$post       = get_post( $post_id );
		$taxonomies = get_object_taxonomies( $post->post_type ); // returns array of taxonomy names for post type, ex array("category", "post_tag");

		foreach ( $taxonomies as $taxonomy ) {
			$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );

			wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
		}
	}

	/**
	 * Duplicate the post meta of the original post to the new post.
	 *
	 * @param int $post_id     The ID of the original post.
	 * @param int $new_post_id The ID of the new post.
	 */
	public function duplicate_post_meta( $post_id, $new_post_id ) {
		global $wpdb;

		$post_meta_infos = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL

		if ( 0 !== count( $post_meta_infos ) ) {
			$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";

			foreach ( $post_meta_infos as $meta_info ) {
				$meta_key = $meta_info->meta_key;

				if ( '_wp_old_slug' === $meta_key ) {
					continue;
				}

				$meta_value      = addslashes( $meta_info->meta_value );
				$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
			}

			if ( empty( $sql_query_sel ) ) {
				return;
			}

			$sql_query .= implode( ' UNION ALL ', $sql_query_sel );

			$wpdb->query( $sql_query ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL
		}
	}
}

Main::get_instance();

<?php
/**
 * Parenting class for letters from the waiting list.
 *
 * @package woodmart
 */

use XTS\Modules\Waitlist\DB_Storage;
use XTS\Modules\Unit_Of_Measure\Main as Unit_Of_Measure;

/**
 * Parenting class for letters from the waiting list.
 */
class XTS_Email_Waitlist extends WC_Email {
	/**
	 * List of registered placeholder keys for show in content options descritions..
	 *
	 * @var array
	 */
	protected $placeholders_text = array();

	/**
	 * DB_Storage instance.
	 *
	 * @var DB_Storage
	 */
	protected $db_storage;

	/**
	 * Unit_Of_Measure instance.
	 *
	 * @var Unit_Of_Measure|false
	 */
	protected $unit_of_measure = false;

	/**
	 * WC_Product instance.
	 *
	 * @var WC_Product;
	 */
	public $object;

	/**
	 * Product image html.
	 *
	 * @var string
	 */
	public $product_image = '';

	/**
	 * Product price html/plain.
	 *
	 * @var string
	 */
	public $product_price = '';

	/**
	 * User name.
	 *
	 * @var string
	 */
	public $user_name = '';

	/**
	 * Email language.
	 *
	 * @var string
	 */
	public $email_language = '';

	/**
	 * Confirm url.
	 *
	 * @var string
	 */
	public $confirm_url = '';

	/**
	 * Original locale storage for restoration.
	 *
	 * @var string
	 */
	protected $original_locale = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! woodmart_get_opt( 'waitlist_enabled' ) ) {
			return;
		}

		parent::__construct();

		$this->template_base = WOODMART_THEMEROOT . '/woocommerce/';

		$this->db_storage = DB_Storage::get_instance();

		if ( class_exists( 'XTS\Modules\Unit_Of_Measure\Main', false ) ) {
			$this->unit_of_measure = Unit_Of_Measure::get_instance();
		}

		add_filter( 'woodmart_emails_list', array( $this, 'register_woodmart_email' ) );
	}

	/**
	 * Set email args.
	 */
	public function set_email_args() {
		$user = get_user_by( 'email', $this->recipient );

		if ( ! empty( $this->email_language ) && defined( 'WCML_VERSION' ) && defined( 'ICL_SITEPRESS_VERSION' ) ) {
			$product_id = $this->object->get_id();
			$product_id = apply_filters( 'wpml_object_id', $product_id, 'product', false, $this->email_language );

			$this->object = wc_get_product( $product_id );
		}

		$this->user_name     = $user instanceof WP_User ? $user->display_name : esc_html__( 'Customer', 'woodmart' );
		$this->product_image = $this->get_product_image_html();
		$this->product_price = $this->get_product_price();
	}

	/**
	 * Register email in WoodMart emails list.
	 *
	 * @param array $email_class List of email classes.
	 *
	 * @return array
	 */
	public function register_woodmart_email( $email_class ) {
		$email_class[] = get_class( $this );

		return $email_class;
	}

	/**
	 * Init form fields for email on admin panel.
	 */
	public function init_form_fields() {
		parent::init_form_fields();

		unset( $this->form_fields['additional_content'] );
	}

	/**
	 * Get content html.
	 *
	 * @return string
	 */
	public function get_content_html() {
		ob_start();

		wc_get_template(
			$this->template_html,
			array(
				'email'            => $this,
				'email_heading'    => $this->get_heading(),
				'unsubscribe_link' => $this->get_unsubscribe_link(),
				'sent_to_admin'    => false,
				'plain_text'       => false,
			)
		);

		return ob_get_clean();
	}

	/**
	 * Get content plain.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();

		wc_get_template(
			$this->template_plain,
			array(
				'email'            => $this,
				'email_heading'    => $this->get_heading(),
				'unsubscribe_link' => $this->get_unsubscribe_link(),
				'sent_to_admin'    => false,
				'plain_text'       => true,
			)
		);

		return ob_get_clean();
	}

	/**
	 * Get unsubscribe link.
	 * Create unsubscribe token if not exists.
	 *
	 * @return string Unsubscribe url.
	 */
	public function get_unsubscribe_link() {
		$waitlist = $this->db_storage->get_subscription( $this->object, $this->recipient );

		if ( empty( $waitlist ) ) {
			return '';
		}

		$unsubscribe_token = $waitlist->unsubscribe_token;

		return apply_filters(
			'woodmart_waitlist_unsubscribe_url',
			add_query_arg(
				array(
					'action' => 'woodmart_waitlist_unsubscribe',
					'token'  => $unsubscribe_token,
				),
				$this->object->get_permalink()
			)
		);
	}

	/**
	 * Returns the product image.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return string Product image html.
	 */
	public function get_product_image_html() {
		if ( ! $this->object instanceof WC_Product ) {
			return '';
		}

		$product    = $this->object;
		$image_size = apply_filters( 'woodmart_waitlist_email_thumbnail_size', array( 32, 32 ) );
		$img_styles = 'vertical-align:middle;font-size:12px;margin-' . ( is_rtl() ? 'left' : 'right' ) . ': 10px';
		$image      = $product->get_image(
			'thumbnail',
			array(
				'style'  => $img_styles,
				'height' => $image_size[1],
				'width'  => $image_size[0],
				'alt'    => __( 'Product image', 'woodmart' ),
			),
			true
		);

		if ( ! $image ) {
			return '';
		}

		ob_start();
		?>
			<div style="margin-bottom: 5px">
				<?php echo wp_kses_post( apply_filters( 'woocommerce_order_item_thumbnail', $image ) ); ?>
			</div>
		<?php
		$image_html = ob_get_clean();

		return $image_html;
	}

	/**
	 * Get product price with unit of measure if applicable.
	 *
	 * @return string
	 */
	public function get_product_price() {
		$product_price   = wc_price( $this->object->get_price() );
		$unit_of_measure = false;

		if ( class_exists( 'XTS\Modules\Unit_Of_Measure\Main', false ) ) {
			$unit_of_measure = Unit_Of_Measure::get_instance();
		}

		if ( $unit_of_measure instanceof Unit_Of_Measure ) {
			$unit_of_measure = $this->unit_of_measure->get_unit_of_measure_db( $this->object );

			if ( $unit_of_measure ) {
				$product_price = str_replace( $this->object->get_price_suffix(), '', $product_price );

				if ( 'html' === $this->get_email_type() ) {
					$product_price .= '<span class="xts-unit-slash">/</span><span>' . $unit_of_measure . '</span>' . $this->object->get_price_suffix();
				} else {
					$product_price .= $unit_of_measure . $this->object->get_price_suffix();
				}
			}
		}

		return $product_price;
	}

	/**
	 * Switch to specified locale for non-WPML systems.
	 *
	 * @param string $locale Target locale.
	 */
	public function switch_locale( $locale ) {
		if ( empty( $locale ) ) {
			return;
		}

		// Store original locale.
		$this->original_locale = get_locale();

		// Only switch if locale is different.
		if ( $locale !== $this->original_locale ) {
			if ( function_exists( 'switch_to_locale' ) ) {
				switch_to_locale( $locale );
			}
		}
	}

	/**
	 * Restore original locale for non-WPML systems.
	 */
	public function restore_locale() {
		if ( ! empty( $this->original_locale ) && function_exists( 'restore_previous_locale' ) ) {
			restore_previous_locale();
		}
	}
}

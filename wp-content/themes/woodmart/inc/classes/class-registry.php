<?php
/**
 * Registry helper class.
 *
 * @package woodmart
 */

namespace XTS; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedNamespaceFound

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Object Registry
 */
class Registry {
	/**
	 * Holds an instance of the class
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Short names of some know objects
	 *
	 * @var array
	 */
	private $known_objects = array();

	/**
	 * Restrict direct initialization, use Registry::get_instance() instead
	 */
	private function __construct() {}

	/**
	 * Get instance of the object (the singleton method)
	 *
	 * @return  Registry
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Dynamically load missing object and assign it to the Registry property.
	 *
	 * @param string $obj Object name (first char will be converted to upper case).
	 *
	 * @return object
	 */
	public function __get( $obj ) {
		if ( ! isset( $this->known_objects[ $obj ] ) ) {
			try {
				$this->save_object( $obj );
			} catch ( \Exception $e ) {
				echo esc_html( $e->getTraceAsString() );
			}
		}

		return $this->known_objects[ $obj ];
	}

	/**
	 * Save object to the registry if it exists and is not already assigned.
	 *
	 * @param string $obj Object name (first char will be converted to upper case).
	 *
	 * @return void
	 */
	private function save_object( $obj ) {
		if ( class_exists( 'WOODMART_' . ucfirst( $obj ) ) ) {
			$objname = 'WOODMART_' . ucfirst( $obj );
		} elseif ( class_exists( 'XTS\\' . ucfirst( $obj ) ) ) {
			$objname = 'XTS\\' . ucfirst( $obj );
		} else {
			$objname = $obj;
		}

		if ( is_string( $obj ) && ! isset( $this->$obj ) && class_exists( $objname ) ) {
			$this->known_objects[ $obj ] = new $objname();
		}
	}

	/**
	 * Prevent users to clone the instance
	 */
	public function __clone() {
		wp_trigger_error( __METHOD__, 'Clone is not allowed.', E_USER_ERROR );
	}
}

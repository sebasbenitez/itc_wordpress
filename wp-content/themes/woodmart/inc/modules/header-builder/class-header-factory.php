<?php
/**
 * Header factory class file.
 *
 * @package woodmart
 */

namespace XTS\Modules\Header_Builder;

/**
 * Wrapper for our header class instance. CRUD actions
 */
class Header_Factory {

	/**
	 * Elements object classes.
	 *
	 * @var null
	 */
	private $elements = null;

	/**
	 * Constructor
	 *
	 * @param object $elements Elements object classes.
	 */
	public function __construct( $elements ) {
		$this->elements = $elements;
	}

	/**
	 * Get header by ID.
	 *
	 * @param integer $id Header ID.
	 *
	 * @return Header
	 */
	public function get_header( $id ) {
		return new Header( $this->elements, $id );
	}

	/**
	 * Update header settings.
	 *
	 * @param integer $id Header ID.
	 * @param string  $name Header name.
	 * @param array   $structure Header structure.
	 * @param array   $settings Header settings.
	 *
	 * @return Header
	 */
	public function update_header( $id, $name, $structure, $settings ) {
		$header = new Header( $this->elements, $id );

		$header->set_name( $name );
		$header->set_structure( $structure );
		$header->set_settings( $settings );

		$header->save();

		return $header;
	}

	/**
	 * Create new header.
	 *
	 * @param integer $id Header ID.
	 * @param string  $name Header name.
	 * @param array   $structure Header structure.
	 * @param array   $settings Header settings.
	 *
	 * @return Header
	 */
	public function create_new( $id, $name, $structure = false, $settings = false ) {
		$header = new Header( $this->elements, $id, true );

		if ( $structure ) {
			$header->set_structure( $structure );
		}
		if ( $settings ) {
			$header->set_settings( $settings );
		}

		$header->set_name( $name );
		$header->save();

		return $header;
	}

	/**
	 * Create new draft header.
	 *
	 * @param integer $id Header ID.
	 * @param string  $name Header name.
	 * @param array   $structure Header structure.
	 * @param array   $settings Header settings.
	 * @return Header
	 */
	public function draft_header( $id, $name, $structure = false, $settings = false ) {
		$header = new Header( $this->elements, $id, true );

		if ( $structure ) {
			$header->set_structure( $structure );
		}
		if ( $settings ) {
			$header->set_settings( $settings );
		}

		$header->set_name( $name );

		return $header;
	}
}

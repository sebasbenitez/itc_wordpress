<?php
/**
 * Notices helper class.
 *
 * @package woodmart
 */


namespace XTS;

if ( ! defined( 'WOODMART_THEME_DIR' ) ) {
	exit( 'No direct script access allowed' );
}

/**
 * Notices helper class
 */
class Notices {
	/**
	 * All notices.
	 *
	 * @var array
	 */
	public $notices;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->notices = array();
	}

	/**
	 * Add notice message.
	 *
	 * @param string  $msg Message.
	 * @param string  $type Notice type.
	 * @param boolean $global Is global message.
	 *
	 * @return void
	 */
	public function add_msg( $msg, $type, $global = false ) {
		$this->notices[] = array(
			'msg'    => $msg,
			'type'   => $type,
			'global' => $global,
		);
	}

	/**
	 * Get all message.
	 *
	 * @param boolean $globals Is global message.
	 *
	 * @return array
	 */
	public function get_msgs( $globals = false ) {
		if ( $globals ) {
			return array_filter(
				$this->notices,
				function( $v ) {
					return $v['global'];
				}
			);
		}

		return $this->notices;
	}

	/**
	 * Clear message.
	 *
	 * @param boolean $globals Is global message.
	 *
	 * @return void
	 */
	public function clear_msgs( $globals = true ) {
		if ( $globals ) {
			$this->notices = array_filter(
				$this->notices,
				function( $v ) {
					return ! $v['global'];
				}
			);
		} else {
			$this->notices = array();
		}
	}

	/**
	 * Show message.
	 *
	 * @param boolean $globals Is global message.
	 *
	 * @return void
	 */
	public function show_msgs( $globals = false ) {
		$msgs = $this->get_msgs( $globals );
		if ( ! empty( $msgs ) ) {
			foreach ( $msgs as $key => $msg ) {
				if ( ! $globals && $msg['global'] ) {
					continue;
				}
				echo '<div class="woodmart-msg xts-notice xts-' . $msg['type'] . '">';
					echo '<div>' . $msg['msg'] . '</div>';
				echo '</div>';
			}
		}

		$this->clear_msgs( $globals );
	}

	/**
	 * Add error message.
	 *
	 * @param string  $msg Message.
	 * @param boolean $global Is global message.
	 *
	 * @return void
	 */
	public function add_error( $msg, $global = false ) {
		$this->add_msg( $msg, 'error', $global );
	}

	/**
	 * Add warning message.
	 *
	 * @param string  $msg Message.
	 * @param boolean $global Is global message.
	 *
	 * @return void
	 */
	public function add_warning( $msg, $global = false ) {
		$this->add_msg( $msg, 'warning', $global );
	}

	/**
	 * Add success message.
	 *
	 * @param string  $msg Message.
	 * @param boolean $global Is global message.
	 *
	 * @return void
	 */
	public function add_success( $msg, $global = false ) {
		$this->add_msg( $msg, 'success', $global );
	}
}

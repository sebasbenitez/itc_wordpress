<?php
/**
 * Wordfence integration.
 *
 * @package woodmart
 */

use WordfenceLS\Controller_WordfenceLS;

if ( ! defined( 'WORDFENCE_VERSION' ) ) {
	return;
}

add_action( 'woocommerce_login_form_start', array( Controller_WordfenceLS::shared(), '_woocommerce_login_enqueue_scripts' ) );

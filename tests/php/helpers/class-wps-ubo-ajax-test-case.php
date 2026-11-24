<?php
/**
 * Base AJAX test case with WC state reset.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * AJAX test case.
 */
abstract class WPS_UBO_Ajax_Test_Case extends WP_Ajax_UnitTestCase {

	/**
	 * Set up.
	 */
	public function set_up() {
		parent::set_up();
		WC()->session = new WC_Session_Handler();
		WC()->session->init();
		WC()->customer = new WC_Customer( get_current_user_id(), true );
		WC()->cart     = new WC_Cart();
	}

	/**
	 * Tear down.
	 */
	public function tear_down() {
		WC()->cart->empty_cart();
		$_POST    = array();
		$_GET     = array();
		$_REQUEST = array();
		parent::tear_down();
	}

	/**
	 * Run an AJAX action and return decoded JSON (or raw output).
	 *
	 * @param string $action Action.
	 * @param array  $post   Request body ( $_POST ).
	 * @param array  $get    Query args ( $_GET ).
	 * @return mixed
	 */
	protected function ajax( $action, $post = array(), $get = array() ) {
		$_GET     = $get;
		$_POST    = $post;
		$_REQUEST = array_merge( $get, $post );
		try {
			$this->_handleAjax( $action );
		} catch ( WPAjaxDieContinueException | WPAjaxDieStopException $e ) {
			// The handler ended in wp_die(), the normal way out of an AJAX callback.
			unset( $e );
		}
		$decoded = json_decode( $this->_last_response, true );
		return null === $decoded ? $this->_last_response : $decoded;
	}
}

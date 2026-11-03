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
	 * @return mixed
	 */
	protected function ajax( $action ) {
		$_REQUEST = array_merge( $_GET, $_POST );
		try {
			$this->_handleAjax( $action );
		} catch ( WPAjaxDieContinueException $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		} catch ( WPAjaxDieStopException $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		}
		$decoded = json_decode( $this->_last_response, true );
		return null === $decoded ? $this->_last_response : $decoded;
	}
}

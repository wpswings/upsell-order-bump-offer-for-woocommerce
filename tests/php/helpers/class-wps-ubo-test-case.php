<?php
/**
 * Base test case: fresh WC session + cart for every test.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

/**
 * Base test case.
 */
abstract class WPS_UBO_Test_Case extends WP_UnitTestCase {

	/**
	 * Set up a clean WooCommerce front-end state.
	 */
	public function set_up() {
		parent::set_up();
		$GLOBALS['wps_ubo_test_mails'] = array();
		self::reset_wc();
	}

	/**
	 * Tear down.
	 */
	public function tear_down() {
		if ( WC()->cart ) {
			WC()->cart->empty_cart();
		}
		if ( WC()->session ) {
			WC()->session->destroy_session();
		}
		parent::tear_down();
	}

	/**
	 * Re-initialise WC session, customer and cart.
	 */
	protected static function reset_wc() {
		WC()->session = new WC_Session_Handler();
		WC()->session->init();
		WC()->customer = new WC_Customer( get_current_user_id(), true );
		WC()->cart     = new WC_Cart();
		WC()->cart->get_cart_from_session();
	}

	/**
	 * Mails captured by the pre_wp_mail filter in bootstrap.php.
	 *
	 * @return array
	 */
	protected function sent_mails() {
		return isset( $GLOBALS['wps_ubo_test_mails'] ) ? $GLOBALS['wps_ubo_test_mails'] : array();
	}

	/**
	 * Run an AJAX callback directly and return its decoded JSON response.
	 *
	 * wp_send_json()/check_ajax_referer() end in wp_die(); in AJAX mode that is routed to a
	 * handler that throws, so the test keeps running.
	 *
	 * @param callable $callback Handler.
	 * @param array    $post     $_POST payload.
	 * @return array{raw:string,json:mixed}
	 */
	protected function ajax_call( $callback, $post = array() ) {
		$_POST    = $post;
		$_REQUEST = $post;
		$doing    = static function () {
			return true;
		};
		$handler  = static function () {
			return static function ( $message = '' ) {
				if ( is_scalar( $message ) ) {
					echo (string) $message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- test capture.
				}
				throw new WPAjaxDieContinueException( 'ajax-die' );
			};
		};
		add_filter( 'wp_doing_ajax', $doing );
		add_filter( 'wp_die_ajax_handler', $handler, 999 );
		ob_start();
		try {
			call_user_func( $callback );
		} catch ( WPAjaxDieContinueException $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		} catch ( WPAjaxDieStopException $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		} finally {
			$raw = (string) ob_get_clean();
			remove_filter( 'wp_doing_ajax', $doing );
			remove_filter( 'wp_die_ajax_handler', $handler, 999 );
			$_POST    = array();
			$_REQUEST = array();
		}
		return array(
			'raw'  => $raw,
			'json' => json_decode( trim( $raw ), true ),
		);
	}

	/**
	 * Run a callback that ends in wp_safe_redirect(); exit; and return the location.
	 *
	 * @param callable $callback Callback.
	 * @param array    $get      $_GET payload.
	 * @return string Redirect location ('' when no redirect happened).
	 */
	protected function redirect_of( $callback, $get = array() ) {
		$_GET     = $get;
		$location = '';
		$stop     = static function ( $loc ) use ( &$location ) {
			$location = $loc;
			throw new WPDieException( 'redirect' );
		};
		add_filter( 'wp_redirect', $stop, 1 );
		try {
			call_user_func( $callback );
		} catch ( WPDieException $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		} finally {
			remove_filter( 'wp_redirect', $stop, 1 );
			$_GET = array();
		}
		return $location;
	}

	/**
	 * Call a private/protected method.
	 *
	 * @param object|string $object_or_class Instance or class name (static).
	 * @param string        $method          Method.
	 * @param array         $args            Args.
	 * @return mixed
	 */
	protected function invoke( $object_or_class, $method, $args = array() ) {
		$ref = new ReflectionMethod( $object_or_class, $method );
		$ref->setAccessible( true );
		return $ref->invokeArgs( is_object( $object_or_class ) ? $object_or_class : null, $args );
	}
}

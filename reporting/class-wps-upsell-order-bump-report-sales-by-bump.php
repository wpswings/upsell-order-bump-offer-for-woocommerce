<?php
/**
 * Sales by Order Bump - Data handling and Stats.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      1.4.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/reporting
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly.
}

if ( class_exists( 'Wps_Upsell_Order_Bump_Report_Sales_By_Bump' ) ) {
	return;
}

/**
 * The Admin-facing reporting functionality of the plugin.
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/reporting
 * @author     WP Swings <webmaster@wpswings.com>
 */
class Wps_Upsell_Order_Bump_Report_Sales_By_Bump {

	/**
	 * Order Bump ID for operations.
	 *
	 * @since    1.4.0
	 * @access   protected
	 * @var      int    $bump_id    Order Bump ID.
	 */
	protected $bump_id;

	/**
	 * Order Bump Series array.
	 *
	 * @since    1.4.0
	 * @access   protected
	 * @var      array    $bump_series    Order Bump Series.
	 */
	protected $bump_series = array();

	/**
	 * Initialize the Class.
	 *
	 * @param string $bump_id Bump id.
	 *
	 * @since    1.4.0
	 */
	public function __construct( $bump_id = 0 ) {

		$this->bump_id = $bump_id;

		$this->set_bump_series();
	}

	/**
	 * Get all Order Bumps.
	 *
	 * @since    1.4.0
	 * @access   protected
	 */
	protected function set_bump_series() {

		$this->bump_series = get_option( 'wps_ubo_bump_list', array() );
	}

	/**
	 * Validate Order Bump series and check for Order Bump ID index.
	 *
	 * @since    1.4.0
	 * @access   protected
	 */
	protected function validate_bump_series() {

		if ( ! empty( $this->bump_series ) && is_array( $this->bump_series ) && ! empty( $this->bump_series[ $this->bump_id ] ) ) {

			return true;
		} else {

			return false;
		}
	}

	/**
	 * Save the Order Bump series with the Updated data.
	 *
	 * @param array $bump_series Bump series.
	 *
	 * @since    1.4.0
	 * @access   protected
	 */
	protected function save_bump_series( $bump_series = array() ) {

		update_option( 'wps_ubo_bump_list', $bump_series );
	}

	/**
	 * Add Offer View Count for the current Order Bump.
	 *
	 * @since    1.4.0
	 * @access   public
	 */
	public function add_offer_view_count() {

		if ( $this->validate_bump_series() ) {

			if ( ! empty( $this->bump_series[ $this->bump_id ]['offer_view_count'] ) ) {

				$this->bump_series[ $this->bump_id ]['offer_view_count'] += 1;
			} else {

				$this->bump_series[ $this->bump_id ]['offer_view_count'] = 1;
			}

			$this->save_bump_series( $this->bump_series );
		}
	}

	/**
	 * Add Offer Accept Count for the current Order Bump.
	 *
	 * @since    1.4.0
	 * @access   public
	 */
	public function add_offer_accept_count() {

		if ( $this->validate_bump_series() ) {

			if ( ! empty( $this->bump_series[ $this->bump_id ]['offer_accept_count'] ) ) {

				$this->bump_series[ $this->bump_id ]['offer_accept_count'] += 1;
			} else {

				$this->bump_series[ $this->bump_id ]['offer_accept_count'] = 1;
			}

			$this->save_bump_series( $this->bump_series );
		}
	}


	/**
	 * Add Offer Accept Count for the current Order Bump Pro.
	 *
	 * @since    1.4.0
	 * @access   public
	 */
	public function add_offer_accept_count_pro() {
		if ( $this->validate_bump_series() ) {

			if ( ! empty( $this->bump_series[ $this->bump_id ]['offer_accept_count_pro'] ) ) {

				$this->bump_series[ $this->bump_id ]['offer_accept_count_pro'] += 1;
			} else {

				$this->bump_series[ $this->bump_id ]['offer_accept_count_pro'] = 1;
			}

			$this->save_bump_series( $this->bump_series );
		}
	}

	/**
	 * Add Offer Remove Count for the current Order Bump.
	 *
	 * @since    1.4.0
	 * @access   public
	 */
	public function add_offer_remove_count() {

		if ( $this->validate_bump_series() ) {

			if ( ! empty( $this->bump_series[ $this->bump_id ]['offer_remove_count'] ) ) {

				$this->bump_series[ $this->bump_id ]['offer_remove_count'] += 1;
			} else {

				$this->bump_series[ $this->bump_id ]['offer_remove_count'] = 1;
			}

			$this->save_bump_series( $this->bump_series );
		}
	}

	/**
	 * Add Order Bump Success Count ( When Payment processes and customer Reaches Thankyou Page
	 * after Offer is Accepted ).
	 *
	 * @since    1.4.0
	 * @access   public
	 */
	public function add_bump_success_count() {

		if ( $this->validate_bump_series() ) {

			if ( ! empty( $this->bump_series[ $this->bump_id ]['bump_success_count'] ) ) {

				$this->bump_series[ $this->bump_id ]['bump_success_count'] += 1;
			} else {

				$this->bump_series[ $this->bump_id ]['bump_success_count'] = 1;
			}

			$this->save_bump_series( $this->bump_series );
		}
	}

	/**
	 * Add Order Bump Total Sales ( Order Bump items ) without tax.
	 *
	 * @param string $order_bump_item_total The total.
	 * @since    1.4.0
	 * @access   public
	 */
	public function add_bump_total_sales( $order_bump_item_total = 0 ) {

		if ( $this->validate_bump_series() ) {

			if ( ! empty( $this->bump_series[ $this->bump_id ]['bump_total_sales'] ) ) {

				$this->bump_series[ $this->bump_id ]['bump_total_sales'] += $order_bump_item_total;
			} else {

				$this->bump_series[ $this->bump_id ]['bump_total_sales'] = $order_bump_item_total;
			}

			$this->save_bump_series( $this->bump_series );
		}
	}
}

<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      1.0.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/public
 */

use Automattic\WooCommerce\Utilities\OrderUtil;
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/public
 * @author     WP Swings <webmaster@wpswings.com>
 */
class Upsell_Order_Bump_Offer_For_Woocommerce_Public {


	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		/**
		   * This function is provided for demonstration purposes only.
		   *
		   * An instance of this class should be passed to the run() function
		   * defined in Upsell_Order_Bump_Offer_For_Woocommerce_Loader as all of the hooks are defined
		   * in that particular class.
		   *
		   * The Upsell_Order_Bump_Offer_For_Woocommerce_Loader will then create the relationship
		   * between the defined hooks and the functions defined in this
		   * class.
		   */

		wp_enqueue_style( $this->plugin_name . 'recommendated_popup', plugin_dir_url( __FILE__ ) . 'css/wps-recommendation-popup.css', array(), $this->version, 'all' );

		if ( is_checkout() || is_cart() ) {
			wp_enqueue_style( $this->plugin_name . '_slick_css', plugin_dir_url( __FILE__ ) . 'css/slick.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/upsell-order-bump-offer-for-woocommerce-public.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		 /**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Upsell_Order_Bump_Offer_For_Woocommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Upsell_Order_Bump_Offer_For_Woocommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// Only enqueue on the Checkout page.
		if ( is_checkout() || is_cart() ) {

			$wps_is_checkout_page = false;
			$wps_popup_body_class = 'No';
			$wps_ubo_timer_evegreen_countdown = array();
			$wps_traditional_checkout = false;
			$wps_traditional_cart = false;
			// To check the checkout page is there or not for jquery.
			if ( ( function_exists( 'is_checkout' ) || is_checkout() ) || ( function_exists( 'is_cart' ) || is_cart() ) ) {
				$wps_is_checkout_page = true;
			}

			// To Check whther checkout or cart block Start Here.

					// Get the ID of the selected checkout page from WooCommerce settings.
					$checkout_page_id = get_option( 'woocommerce_checkout_page_id' );

					// Get the content of the checkout page.
					$checkout_page_content = get_post_field( 'post_content', $checkout_page_id );

					// Check if the content contains a class associated with the block editor.
			if ( strpos( $checkout_page_content, 'wp-block-woocommerce-checkout' ) !== false ) {
				$wps_traditional_checkout = true;
			} else {
				$wps_traditional_checkout = false;
			}

					// Get the ID of the selected cart page from WooCommerce settings.
					$cart_page_id = get_option( 'woocommerce_cart_page_id' );

					// Get the content of the checkout page.
					$cart_page_content = get_post_field( 'post_content', $cart_page_id );

					// Check if the content contains a class associated with the block editor.
			if ( strpos( $cart_page_content, 'wp-block-woocommerce-cart' ) !== false ) {
				$wps_traditional_cart = false;
			} else {
				$wps_traditional_cart = true;
			}
			// To Check whther checkout or cart block End Here.

			$current_theme = wp_get_theme();

			// Enable the bump offer with or without pop-up.
			$wps_ubo_global_options = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );
			$wps_bump_target_popup_bump = ! empty( $wps_ubo_global_options['wps_bump_popup_bump_offer'] ) ? $wps_ubo_global_options['wps_bump_popup_bump_offer'] : 'on';
			if ( 'without_popup' == $wps_bump_target_popup_bump ) {
				$wps_popup_body_class = 'yes';
			}

			// Enable the popup exit intent.
			$wps_bump_popup_exit_intent = ! empty( $wps_ubo_global_options['wps_ubo_enable_popup_exit_intent'] ) ? $wps_ubo_global_options['wps_ubo_enable_popup_exit_intent'] : 'on';

			// Public Script.
			wp_enqueue_script( 'wps-ubo-lite-public-script', plugin_dir_url( __FILE__ ) . 'js/wps_ubo_lite_public_script.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( 'wps-ubo-lite-public-script-new', plugin_dir_url( __FILE__ ) . 'js/wps_ubo_lite_public_script_new_template.js', array( 'jquery' ), $this->version, false );

			// Checkout Block and Cart block Comaptibility.
			$wps_ubo_global_options = get_option( 'wps_ubo_global_options', array() );
			$bump_offer_location = ! empty( $wps_ubo_global_options['wps_ubo_offer_location'] ) ? $wps_ubo_global_options['wps_ubo_offer_location'] : '_before_order_summary';
			$bump_cart_offer_location = ! empty( $wps_ubo_global_options['wps_enable_cart_upsell_location'] ) ? $wps_ubo_global_options['wps_enable_cart_upsell_location'] : '';
			$bump_cart_offer_enable = ! empty( $wps_ubo_global_options['wps_enable_cart_upsell_feature'] ) ? $wps_ubo_global_options['wps_enable_cart_upsell_feature'] : '';

			// Localizing Array.
			$local_arr = array(
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'mobile_view' => wp_is_mobile(),
				'auth_nonce'  => wp_create_nonce( 'wps_ubo_lite_nonce' ),
				'current_theme' => $current_theme->get( 'Name' ),
				'is_checkout_page' => $wps_is_checkout_page,
				'wps_popup_body_class' => $wps_popup_body_class,
				'wps_popup_exit_intent' => $wps_bump_popup_exit_intent,
				'wps_order_bump_location_on_checkout' => $bump_offer_location,
				'wps_order_bump_location_on_cart' => $bump_cart_offer_location,
				'wps_enable_cart_upsell' => $bump_cart_offer_enable,
				'wps_is_checkout_block_use' => $wps_traditional_checkout,
				'wps_is_cart_block_use' => $wps_traditional_cart,
			);

			// Timer Functionality starts.
			if ( isset( WC()->session ) && ! empty( WC()->session->get( 'encountered_bump_array' ) ) ) {

				$wps_upsell_bumps_list = get_option( 'wps_ubo_bump_list', array() );

				$wps_ubo_timer_countdown   = array();

				$encountered_order_bump_id = WC()->session->get( 'encountered_bump_array' );
				// To fetch the countdown timer for the encountered bump.
				if ( ! empty( $encountered_order_bump_id ) && ! empty( $wps_upsell_bumps_list ) && ( is_array( $encountered_order_bump_id ) || is_object( $encountered_order_bump_id ) ) ) {
					foreach ( $encountered_order_bump_id as $key => $order_bump_id ) {
						if ( ! empty( $wps_upsell_bumps_list[ $order_bump_id ]['wps_ubo_offer_timer'] ) && 'yes' === $wps_upsell_bumps_list[ $order_bump_id ]['wps_ubo_offer_timer'] ) {
							$wps_ubo_timer_countdown[ $order_bump_id ] = array(
								'enabled' => 'yes',
								'counter' => ! empty( $wps_upsell_bumps_list[ $order_bump_id ]['wps_upsell_bump_offer_timer'] ) ? $wps_upsell_bumps_list[ $order_bump_id ]['wps_upsell_bump_offer_timer'] : '',
							);
						} elseif ( ! empty( $wps_upsell_bumps_list[ $order_bump_id ]['wps_evergreen_timer_switch'] ) && 'yes' === $wps_upsell_bumps_list[ $order_bump_id ]['wps_evergreen_timer_switch'] ) {
							$wps_ubo_timer_evegreen_countdown[ $order_bump_id ] = array(
								'enabled' => 'yes',
								'evegreen_counter' => ! empty( $wps_upsell_bumps_list[ $order_bump_id ]['wps_upsell_bump_offer_evergreen_timer'] ) ? $wps_upsell_bumps_list[ $order_bump_id ]['wps_upsell_bump_offer_evergreen_timer'] : '',
							);
						}
					}
				} elseif ( empty( $encountered_order_bump_id ) ) {
					$encountered_order_bump_id = 'reload';
				}
			}
			if ( ! empty( $encountered_order_bump_id ) ) {
				$local_arr['check_if_reload'] = $encountered_order_bump_id;
			} else {
				$local_arr['check_if_reload'] = 'reload';
			}
			if ( ! empty( $wps_ubo_timer_countdown ) ) {
				$local_arr['timer'] = $wps_ubo_timer_countdown;
			}
			$local_arr['evergreen_timer'] = $wps_ubo_timer_evegreen_countdown;
			// Timer Functionality ends.

			wp_localize_script(
				'wps-ubo-lite-public-script',
				'wps_ubo_lite_public',
				$local_arr
			);
			wp_localize_script(
				'wps-ubo-lite-public-script-new',
				'wps_ubo_lite_public_new',
				$local_arr
			);
			// Do not work in mobile-view mode.
			if ( ! wp_is_mobile() ) {

				wp_enqueue_script( 'zoom-script', plugins_url( '/js/zoom-script.js', __FILE__ ), array( 'jquery' ), $this->version, true );
			}

				wp_enqueue_script( 'script_slick_js', plugins_url( '/js/slick.min.js', __FILE__ ), array( 'jquery' ), $this->version, true );
		}
		if ( is_product() ) {
			$wps_plugin_list = get_option( 'active_plugins' );
			$wps_is_pro_active = false;
			$wps_plugin = 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php';
			if ( in_array( $wps_plugin, $wps_plugin_list ) ) {
				$wps_is_pro_active = true;
			}

			$wps_is_recommendation_enable = get_post_meta( get_the_ID(), 'is_recommendation_enable' );
			$wps_selected_recommendated_product = get_post_meta( get_the_ID(), 'wps_recommendated_product_ids' );

			if ( ! empty( $wps_is_recommendation_enable ) && ( true == $wps_is_pro_active ) ) {
				if ( ( 'yes' == $wps_is_recommendation_enable[0] ) && isset( $wps_is_recommendation_enable[0] ) && ! empty( $wps_selected_recommendated_product ) && is_array( $wps_selected_recommendated_product ) && isset( $wps_selected_recommendated_product ) ) {
					// Localizing Array.
					$local_arr = array(
						'ajaxurl'     => admin_url( 'admin-ajax.php' ),
						'mobile_view' => wp_is_mobile(),
						'auth_nonce'  => wp_create_nonce( 'wps_ubo_lite_nonce_recommend' ),
						'product_id'  => get_the_ID(),
					);

					// Public facing regarding popup.
					wp_enqueue_script( 'wps-ubo-lite-public-script-for-recommdation', plugin_dir_url( __FILE__ ) . 'js/wps_ubo_lite_recommdation_popup.js', array( 'jquery' ), $this->version, false );
					wp_localize_script(
						'wps-ubo-lite-public-script-for-recommdation',
						'wps_ubo_lite_public_recommendated',
						$local_arr
					);
				}
			}
		}

		if ( is_cart() ) {
			$wps_plugin_list = get_option( 'active_plugins' );
			$wps_is_pro_active = false;
			$wps_plugin = 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php';
			if ( in_array( $wps_plugin, $wps_plugin_list ) ) {
				$wps_is_pro_active = true;
			}

			// Localizing Array.
			$local_arr = array(
				'ajaxurl'     => admin_url( 'admin-ajax.php' ),
				'auth_nonce'  => wp_create_nonce( 'wps_ubo_lite_nonce_recommend' ),
			);

			if ( true == $wps_is_pro_active ) {
				// Public facing regarding popup.
				wp_enqueue_script( 'wps-ubo-lite-public-script-for-cart', plugin_dir_url( __FILE__ ) . 'js/wps_ubo_lite_public_cart_script.js', array( 'jquery' ), $this->version, false );
				wp_localize_script(
					'wps-ubo-lite-public-script-for-cart',
					'wps_ubo_lite_public_cart',
					$local_arr
				);
			}
		}
	}

	/**
	 * Add custom hook to show offer bump after payment gateways but before
	 * terms as one is not provided by Woocommerce.
	 *
	 * @param    string $template_name       Get checkout page template.
	 * @param    string $template_path       Get checkout page template path.
	 * @since    1.0.0
	 */
	public function add_bump_offer_custom_hook( $template_name, $template_path ) {

		if ( 'checkout/terms.php' === $template_name ) {

			do_action( 'wps_ubo_after_pg_before_terms' );

			remove_action( 'woocommerce_before_template_part', array( $this, 'add_bump_offer_custom_hook' ), 10 );
		}
	}

	/**
	 * Register the Filter for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function show_offer_bump() {
		 /**
		 * This adds the bump to checkout page.
		 */
		if ( function_exists( 'is_checkout' ) && is_checkout() ) {

			require_once plugin_dir_path( __FILE__ ) . '/partials/upsell-order-bump-offer-for-woocommerce-public-display.php';
		}
	}

	/**
	 * Register the Filter for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function show_offer_bump_on_cart() {
		 /**
		 * This adds the bump to cart page.
		 */
		if ( function_exists( 'is_cart' ) && is_cart() ) {
			$wps_ubo_global_options = get_option( 'wps_ubo_global_options', array() );

			// Check cart upsell enabled.
			$wps_enable_cart_upsell_feature = ! empty( $wps_ubo_global_options['wps_enable_cart_upsell_feature'] ) ? $wps_ubo_global_options['wps_enable_cart_upsell_feature'] : 'on';
			if ( 'on' == $wps_enable_cart_upsell_feature ) {
				require_once plugin_dir_path( __FILE__ ) . '/partials/upsell-order-bump-offer-for-woocommerce-public-display.php';
			}
		}
	}

	/**
	 * Add bump offer product to cart ( checkbox ).
	 *
	 * @since    1.0.0
	 */
	public function add_offer_in_cart() {
		// Nonce verification.
		check_ajax_referer( 'wps_ubo_lite_nonce', 'nonce' );

		// The id of the offer to be added.
		$bump_product_id = ! empty( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

		$bump_discounted_price = ! empty( $_POST['discount'] ) ? sanitize_text_field( wp_unslash( $_POST['discount'] ) ) : '';
		$bump_index            = ! empty( $_POST['bump_index'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_index'] ) ) : '';
		$bump_target_cart_key  = ! empty( $_POST['bump_target_cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_target_cart_key'] ) ) : '';
		$order_bump_id         = ! empty( $_POST['order_bump_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_bump_id'] ) ) : '';
		$smart_offer_upgrade   = ! empty( $_POST['smart_offer_upgrade'] ) ? sanitize_text_field( wp_unslash( $_POST['smart_offer_upgrade'] ) ) : '';
		$form_data             = ! empty( $_POST['form_data'] ) ? map_deep( wp_unslash( $_POST['form_data'] ), 'sanitize_text_field' ) : array();

		// Quantity of product.
		$wps_qty_variable = ! empty( $_POST['wps_qty_variable'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_qty_variable'] ) ) : '1';

		$active_plugin = get_option( 'active_plugins', false );
		if ( in_array( 'woo-gift-cards-lite/woocommerce_gift_cards_lite.php', $active_plugin, true ) && wps_ubo_lite_if_pro_exists() && ! empty( $form_data ) ) {
			$gift_card_form = array(
				'wps_wgm_to_email'      => '',
				'wps_wgm_from_name'     => '',
				'wps_wgm_message'       => '',
				'delivery_method'       => '',
				'wps_wgm_price'         => '',
				'wps_wgm_selected_temp' => '',
			);
			$gift_card_data = get_post_meta( $bump_product_id, 'wps_wgm_pricing' );
			foreach ( $gift_card_data as $key => $value ) {
				$gift_card_form = array_merge(
					$gift_card_form,
					array(
						'wps_wgm_price'         => $value['default_price'],
						'wps_wgm_selected_temp' => $value['template'][0],
					)
				);
			}

			foreach ( $form_data as $key => $value ) {
				if ( 'from' === $value['name'] ) {
					$gift_card_form = array_merge( $gift_card_form, array( 'wps_wgm_from_name' => $value['value'] ) );
				} elseif ( 'gift message' === $value['name'] ) {
					$gift_card_form = array_merge( $gift_card_form, array( 'wps_wgm_message' => $value['value'] ) );
				} elseif ( 'mail to recepient' === $value['name'] ) {
					$gift_card_form = array_merge( $gift_card_form, array( 'wps_wgm_to_email' => $value['value'] ) );
				}
			}

			$cart_item_data = array(
				'wps_ubo_offer_product' => true,
				'wps_ubo_offer_index'   => $bump_index,
				'wps_ubo_bump_id'       => $order_bump_id,
				'wps_discounted_price'  => $bump_discounted_price,
				'wps_ubo_target_key'    => $bump_target_cart_key,
				'flag_' . uniqid()      => true,
				'wps_ubo_meta_form'     => $form_data,
				'product_meta'          => array( 'meta_data' => $gift_card_form ),
			);
		} else {
			$cart_item_data = array(
				'wps_ubo_offer_product' => true,
				'wps_ubo_offer_index'   => $bump_index,
				'wps_ubo_bump_id'       => $order_bump_id,
				'wps_discounted_price'  => $bump_discounted_price,
				'wps_ubo_target_key'    => $bump_target_cart_key,
				'flag_' . uniqid()      => true,
				'wps_ubo_meta_form'     => $form_data,
			);
		}

		$_product = wc_get_product( $bump_product_id );

		$added = 'added';

		if ( wps_ubo_lite_reload_required_after_adding_offer( $_product ) ) {

			$added = 'subs_reload';
		}

		if ( ! empty( $_product ) && $_product->has_child() ) {

			// Generate default price html.
			$bump_price_html = wps_ubo_lite_custom_price_html( $bump_product_id, $bump_discounted_price );

			$response = array(
				'key'     => 'true',
				'message' => $bump_price_html,
			);

			// Now we have to add a pop up.
			echo wp_json_encode( $response );
		} elseif ( ! empty( $_product ) ) {

			// If simple product or any single variations.
			// Add to cart the same.

			$bump_offer_cart_item_key = WC()->cart->add_to_cart( $bump_product_id, $quantity = $wps_qty_variable, $variation_id = 0, $variation = array(), $cart_item_data );

			// Add Order Bump Offer Accept Count for the respective Order Bump.
			$sales_by_bump = new Wps_Upsell_Order_Bump_Report_Sales_By_Bump( $order_bump_id );
			$sales_by_bump->add_offer_accept_count();

			WC()->session->set( 'bump_offer_status', 'added' );
			WC()->session->set( "bump_offer_status_$bump_index", $bump_offer_cart_item_key );

			// Smart offer Upgrade.
			if ( wps_ubo_lite_if_pro_exists() && 'yes' === $smart_offer_upgrade ) {

				// Get all saved bumps.
				$wps_ubo_bump_callback          = Upsell_Order_Bump_Offer_For_Woocommerce::$wps_upsell_bump_list_callback_function;
				$wps_ubo_offer_array_collection = Upsell_Order_Bump_Offer_For_Woocommerce::$wps_ubo_bump_callback();

				$encountered_bump_array = ! empty( $wps_ubo_offer_array_collection[ $order_bump_id ] ) ? $wps_ubo_offer_array_collection[ $order_bump_id ] : array();

				$wps_upsell_bump_replace_target = ! empty( $encountered_bump_array['wps_ubo_offer_replace_target'] ) ? $encountered_bump_array['wps_ubo_offer_replace_target'] : '';

				if ( 'yes' === $wps_upsell_bump_replace_target && class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) && method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_upgrade_offer' ) ) {

					Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_upgrade_offer( $bump_offer_cart_item_key, $bump_target_cart_key );
				}
			}

			/**
			 * After v1.3.0 (pro)
			 * Apply Exclusive Limits in case of pro is active.
			 */
			if ( wps_ubo_lite_if_pro_exists() ) {
				if ( class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) ) {
					if ( method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_manage_exclusive_limit' ) ) {
						$single_bump_id = str_replace( 'index_', '', $bump_index );
						if ( true === Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_manage_exclusive_limit( $single_bump_id ) ) {
							$associations = WC()->session->get( 'bump_offer_associations' );

							if ( null !== $associations ) {
								$associations .= '___';
							} else {
								$associations = '';
							}

							$associations .= $bump_index;

							WC()->session->set( 'bump_offer_associations', $associations );
						}
					}
				}
			}

			echo wp_json_encode( $added );
		}

		wp_die();
	}

	/**
	 * Mini Cart Price( checkbox ).
	 *
	 * @param string $cart_item_data is an output.
	 * @param object $cart_item is cart item object.
	 * @param string $cart_item_key is key.
	 * @return string
	 * @since    1.0.0
	 */
	public function change_mini_cart_content( $cart_item_data, $cart_item, $cart_item_key ) {

		if ( ! empty( $cart_item['wps_ubo_offer_index'] ) ) {
			if ( ! empty( $cart_item['wps_discounted_price'] ) ) {

				$product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];

				$price_discount = wps_ubo_lite_custom_price_html( $product_id, $cart_item['wps_discounted_price'], 'price' );
				if ( is_wps_role_based_pricing_active() ) {
					if ( ( -1 < strpos( $cart_item['wps_discounted_price'], 'no_disc' ) ) ) {
						$prod_obj   = wc_get_product( $product_id );
						$prod_type  = $prod_obj->get_type();
						$bump_price = wps_mrbpfw_role_based_price( $prod_obj->get_price(), $prod_obj, $prod_type );
						$bump_price = strip_tags( str_replace( get_woocommerce_currency_symbol(), '', $bump_price ) );
						$cart_item['data']->set_price( $bump_price );
					} else {
						$cart_item['data']->set_price( $price_discount );
					}
				} else {
					$cart_item['data']->set_price( $price_discount );
				}
			}
		}
		return $cart_item['data'];
	}


	/**
	 * Remove bump offer product to cart.
	 *
	 * @since    1.0.0
	 */
	public function remove_offer_in_cart() {
		// Nonce verification.
		check_ajax_referer( 'wps_ubo_lite_nonce', 'nonce' );

		$bump_index = ! empty( $_POST['bump_index'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_index'] ) ) : '';

		if ( null !== WC()->session->get( "bump_offer_status_$bump_index" ) ) {

			WC()->cart->remove_cart_item( WC()->session->get( "bump_offer_status_$bump_index" ) );
		}

		WC()->session->__unset( "bump_offer_status_$bump_index" );

		/**
		 * After v1.3.0 (pro)
		 * Apply Exclusive Limits in case of pro is active.
		 */
		if ( wps_ubo_lite_if_pro_exists() ) {

			if ( class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) ) {
				if ( method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_manage_exclusive_limit' ) ) {
					$single_bump_id = str_replace( 'index_', '', $bump_index );
					if ( true === Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_manage_exclusive_limit( $single_bump_id ) ) {
						$associations = WC()->session->get( 'bump_offer_associations' );
						$associations = array_unique( explode( '___', $associations ) );
						$key          = array_search( $bump_index, $associations, true );
						unset( $associations[ $key ] );
						WC()->session->set( 'bump_offer_associations', implode( '___', $associations ) );
					}
				}
			}
		}

		echo wp_json_encode( 'removed' );

		wp_die();
	}


	/**
	 * Search selected variation.
	 *
	 * @since    1.0.0
	 */
	public function search_variation_id_by_select() {
		// Nonce verification.
		check_ajax_referer( 'wps_ubo_lite_nonce', 'nonce' );

		$bump_offer_id = ! empty( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';  // variation parent.

		$bump_offer_discount = ! empty( $_POST['discount'] ) ? sanitize_text_field( wp_unslash( $_POST['discount'] ) ) : '';

		$attributes_selected_options = ! empty( $_POST['attributes_selected_options'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['attributes_selected_options'] ) ) : array();

		// Got all values to search for variation id from selected attributes.
		$product = wc_get_product( $bump_offer_id );

		if ( empty( $product ) ) {

			echo wp_json_encode( esc_html__( 'Product Not Found.', 'upsell-order-bump-offer-for-woocommerce' ) );
			return;
		}

		$product_data_store         = new WC_Product_Data_Store_CPT();
		$variation_id               = $product_data_store->find_matching_product_variation( $product, $attributes_selected_options );
		$selected_variation_product = wc_get_product( $variation_id );

		// Image to reflect on select change.
		$image_id = get_post_thumbnail_id( $variation_id );

		if ( ! empty( $image_id ) ) {

			$html           = wc_get_gallery_image_html( $image_id, true );
			$bump_var_image = apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $image_id );
		} else {

			// If no variation image is present show default one.
			$bump_var_image = wps_ubo_lite_get_bump_image( $bump_offer_id );
		}

		// Variation id will be empty if selected variation is not available.
		if ( empty( $variation_id ) || empty( $selected_variation_product ) ) {

			$response = array(
				'key'     => 'not_available',
				'message' => '<p class="stock out-of-stock">' . esc_html__( 'Sorry, this variation is not available.', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>',
				'image'   => $bump_var_image,
			);
			echo wp_json_encode( $response );
		} else if ( ! empty( $variation_id ) || ! empty( $selected_variation_product ) ) {

			// Check if in stock?
			if ( ! $selected_variation_product->is_in_stock() ) {

				// Out of stock.
				$response = array(

					'key'     => 'stock',
					'message' => '<p class="stock out-of-stock">' . esc_html__( 'Out of stock.', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>',
					'image'   => $bump_var_image,
				);

				echo wp_json_encode( $response );
			} else {

				$response = array(
					'key'     => $variation_id,
					'message' => wps_ubo_lite_custom_price_html( $variation_id, $bump_offer_discount ),
					'image'   => $bump_var_image,
				);

				echo wp_json_encode( $response );
			}
		}

		wp_die();
	}

	/**
	 * Add selected variation as bump offer product to cart ( by button )
	 *
	 * @since    1.0.0
	 */
	public function add_variation_offer_in_cart() {
		 check_ajax_referer( 'wps_ubo_lite_nonce', 'nonce' );

		// Contains selected variation ID.
		$variation_id = ! empty( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

		// Contains parent variable ID.
		$variation_parent_id = ! empty( $_POST['parent_id'] ) ? sanitize_text_field( wp_unslash( $_POST['parent_id'] ) ) : '';

		// Contains bump discount.
		$bump_offer_discount = ! empty( $_POST['discount'] ) ? sanitize_text_field( wp_unslash( $_POST['discount'] ) ) : '';

		// Contains target cart key.
		$bump_target_cart_key = ! empty( $_POST['bump_target_cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_target_cart_key'] ) ) : '';

		$bump_index = ! empty( $_POST['bump_index'] ) || '0' === (string) sanitize_text_field( wp_unslash( $_POST['bump_index'] ) ) ? sanitize_text_field( wp_unslash( $_POST['bump_index'] ) ) : '';

		$order_bump_id       = ! empty( $_POST['order_bump_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_bump_id'] ) ) : '';
		$smart_offer_upgrade = ! empty( $_POST['smart_offer_upgrade'] ) ? sanitize_text_field( wp_unslash( $_POST['smart_offer_upgrade'] ) ) : '';
		$form_data           = ! empty( $_POST['form_data'] ) ? map_deep( wp_unslash( $_POST['form_data'] ), 'sanitize_text_field' ) : array();

		// variation product data.
		$wps_orderbump_any_variation = ! empty( $_POST['wps_orderbump_any_variation'] ) ? map_deep( wp_unslash( $_POST['wps_orderbump_any_variation'] ), 'sanitize_text_field' ) : array();

		// Quantity of product.
		$wps_qty_variable = ! empty( $_POST['wps_qty_variable'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_qty_variable'] ) ) : '1';

		// Now safe to add to cart.
		$cart_item_data = array(
			'wps_ubo_offer_product' => true,
			'wps_discounted_price'  => $bump_offer_discount,
			'flag_' . uniqid()      => true,
			'wps_ubo_offer_index'   => 'index_' . $bump_index,
			'wps_ubo_bump_id'       => $order_bump_id,
			'wps_ubo_target_key'    => $bump_target_cart_key,
			'wps_ubo_meta_form'     => $form_data,
		);

		$_product = wc_get_product( $variation_id );

		$added = 'added';

		if ( wps_ubo_lite_reload_required_after_adding_offer( $_product ) ) {

			$added = 'subs_reload';
		}

		$bump_offer_cart_item_key = WC()->cart->add_to_cart( $variation_parent_id, $quantity = $wps_qty_variable, $variation_id, $variation = $wps_orderbump_any_variation, $cart_item_data );

		// Add Order Bump Offer Accept Count for the respective Order Bump.
		$sales_by_bump = new Wps_Upsell_Order_Bump_Report_Sales_By_Bump( $order_bump_id );
		$sales_by_bump->add_offer_accept_count();

		WC()->session->set( "bump_offer_status_index_$bump_index", $bump_offer_cart_item_key );

		WC()->session->set( 'bump_offer_status', 'added' );

		// Smart offer Upgrade.
		if ( wps_ubo_lite_if_pro_exists() && 'yes' === $smart_offer_upgrade ) {

			// Get all saved bumps.
			$wps_ubo_bump_callback          = Upsell_Order_Bump_Offer_For_Woocommerce::$wps_upsell_bump_list_callback_function;
			$wps_ubo_offer_array_collection = Upsell_Order_Bump_Offer_For_Woocommerce::$wps_ubo_bump_callback();

			$encountered_bump_array = ! empty( $wps_ubo_offer_array_collection[ $order_bump_id ] ) ? $wps_ubo_offer_array_collection[ $order_bump_id ] : array();

			$wps_upsell_bump_replace_target = ! empty( $encountered_bump_array['wps_ubo_offer_replace_target'] ) ? $encountered_bump_array['wps_ubo_offer_replace_target'] : '';

			if ( 'yes' === $wps_upsell_bump_replace_target && class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) && method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_upgrade_offer' ) ) {

				Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_upgrade_offer( $bump_offer_cart_item_key, $bump_target_cart_key );
			}
		}
		echo wp_json_encode( $added );
		wp_die();
	}

	/**
	 * Disabling the offer quantity for bump product in Cart page.
	 *
	 * @param    string $product_quantity       Quantity at cart page.
	 * @param    string $cart_item_key          Cart item key.
	 * @since    1.0.0
	 */
	public function disable_quantity_bump_product_in_cart( $product_quantity, $cart_item_key ) {

		if ( null !== WC()->session->get( 'bump_offer_status' ) ) {

			$cart_item = WC()->cart->cart_contents[ $cart_item_key ];

			if ( ! empty( $cart_item['wps_ubo_offer_product'] ) ) {

				// For Bump product allowed quantity is one.
				$product_quantity = 1;
				return $product_quantity;
			}
		}

		return $product_quantity;
	}

	/**
	 * When cart item product remove is triggered.
	 *
	 * Remove encountered session to refetch order bumps when any product is removed from cart.
	 *
	 * Removal of target and bump offer product is handled here.
	 *
	 * @param   string $key_to_be_removed      The cart item key which is being removed.
	 * @param   object $cart_object            The cart object.
	 * @since   1.0.0
	 */
	public function after_remove_product( $key_to_be_removed, $cart_object ) {

		if ( empty( $key_to_be_removed ) || empty( WC()->cart->cart_contents ) ) {

			return;
		}

		$current_cart_item = ! empty( $cart_object->cart_contents[ $key_to_be_removed ] ) ? $cart_object->cart_contents[ $key_to_be_removed ] : '';

		// When the removed product is an Offer product.
		if ( ! empty( $current_cart_item['wps_ubo_offer_product'] ) ) {

			// Hide Undo notice for Offer Products.
			add_filter( 'woocommerce_cart_item_removed_notice_type', '__return_null' );

			$bump_index = ! empty( $current_cart_item['wps_ubo_offer_index'] ) ? $current_cart_item['wps_ubo_offer_index'] : '';
			$bump_id    = ! empty( $current_cart_item['wps_ubo_bump_id'] ) ? $current_cart_item['wps_ubo_bump_id'] : '';

			// Add Order Bump Offer Remove Count for the respective Order Bump.
			$sales_by_bump = new Wps_Upsell_Order_Bump_Report_Sales_By_Bump( $bump_id );
			$sales_by_bump->add_offer_remove_count();

			// When the removed product is a Smart Offer Upgrade - Offer product.
			if ( ! empty( $current_cart_item['wps_ubo_sou_offer'] ) && ! empty( $current_cart_item['wps_ubo_target_key'] ) ) {

				// Restore Target product.
				if ( class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) && method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_retrieve_target' ) ) {

					Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_retrieve_target( $current_cart_item['wps_ubo_target_key'] );
				}
			}

			/**
			 * Unset order bump params from WC cart and index session for the removed offer product.
			 * Do not unset other session variables.
			 */

			/**
			 * Unset order bump params from WC cart to prevent Offer rollback on undo.
			 * Commenting : Unset in WC() Cart doesn't work for the current cart item
			 * that is being removed, works with other cart items though.
			 */
			WC()->session->__unset( 'bump_offer_status_' . $bump_index );

			// As offer product is removed so no need remove encountered session to refetch order bumps.
			return;
		} elseif ( ! empty( $current_cart_item['wps_ubo_sou_target'] ) ) { // When the removed product is a Smart Offer Upgrade - Target product.

			// Do nothing.
			return;
		} elseif ( ! empty( $cart_object->cart_contents ) && is_array( $cart_object->cart_contents ) ) { // When the removed product is a Normal or Target product.

			// Global settings.
			$wps_ubo_global_options = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );

			foreach ( $cart_object->cart_contents as $cart_offer_item_key => $cart_offer_item ) {

				// Check Offer product and Target keys.
				if ( ! empty( $cart_offer_item['wps_ubo_offer_product'] ) && ! empty( $cart_offer_item['wps_ubo_target_key'] ) ) {

					// When Target key matches means Removed product is a Target product.
					if ( $cart_offer_item['wps_ubo_target_key'] === $key_to_be_removed ) {

						// If the same target key is found in order cart item, Handle offer product too.
						$bump_index = ! empty( $cart_offer_item['wps_ubo_offer_index'] ) ? $cart_offer_item['wps_ubo_offer_index'] : '';
						$bump_id    = ! empty( $cart_offer_item['wps_ubo_bump_id'] ) ? $cart_offer_item['wps_ubo_bump_id'] : '';

						$sales_by_bump = new Wps_Upsell_Order_Bump_Report_Sales_By_Bump( $bump_id );

						// When Target dependency is set to Remove Offer product.
						if ( ! empty( $wps_ubo_global_options['wps_ubo_offer_removal'] ) && 'yes' === $wps_ubo_global_options['wps_ubo_offer_removal'] ) {

							/**
							 * Remove Target dependent Offer product.
							 * Unset order bump params from WC cart and index session for the dependent offer product.
							 * Do not unset other session variables.
							 */
							if ( ! empty( $cart_offer_item_key ) ) {

								// Unset order bump params from WC cart to prevent Offer rollback on undo.
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['wps_ubo_offer_product'] );
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['wps_ubo_offer_index'] );
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['wps_ubo_bump_id'] );
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['wps_discounted_price'] );
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['wps_ubo_target_key'] );

								// Remove the Offer product.
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ] );

								// Add Order Bump Offer Remove Count for the respective Order Bump.
								$sales_by_bump->add_offer_remove_count();

								WC()->session->__unset( 'bump_offer_status_' . $bump_index );
							}
						} else if ( empty( $wps_ubo_global_options['wps_ubo_offer_removal'] ) && 'yes' !== $wps_ubo_global_options['wps_ubo_offer_removal'] ) { // When Target dependency is set to Keep Offer product.

							/**
							 * Convert Target dependent Offer product into Normal product.
							 * Unset order bump params from WC cart and index session for the dependent offer product.
							 * Do not unset other session variables.
							 */
							if ( ! empty( $cart_offer_item_key ) ) {

								// Convert Offer product to normal product.
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['wps_ubo_offer_product'] );
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['wps_ubo_offer_index'] );
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['wps_ubo_bump_id'] );
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['wps_discounted_price'] );
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['wps_ubo_target_key'] );

								// Add Order Bump Offer Remove Count for the respective Order Bump.
								$sales_by_bump->add_offer_remove_count();

								WC()->session->__unset( 'bump_offer_status_' . $bump_index );
							}
						}
					}
				}
			}
		}

		// Remove encountered session to refetch order bumps.
		wps_ubo_destroy_encountered_session();
	}

	/**
	 * Change price at last for bump offer product.
	 *
	 * @param   object $cart_object            The cart object.
	 * @since    1.0.0
	 */
	public function woocommerce_custom_price_to_cart_item( $cart_object ) {

		if ( ! WC()->session->__isset( 'reload_checkout' ) ) {

			foreach ( $cart_object->cart_contents as $key => $value ) {

				if ( ! empty( $value['wps_discounted_price'] ) ) {

					$product_id = ! empty( $value['variation_id'] ) ? $value['variation_id'] : $value['product_id'];

					$price_discount = wps_ubo_lite_custom_price_html( $product_id, $value['wps_discounted_price'], 'price' );
					if ( is_wps_role_based_pricing_active() ) {
						if ( ( -1 < strpos( $value['wps_discounted_price'], 'no_disc' ) ) ) {
							$prod_obj   = wc_get_product( $product_id );
							$prod_type  = $prod_obj->get_type();
							$bump_price = wps_mrbpfw_role_based_price( $prod_obj->get_price(), $prod_obj, $prod_type );
							$bump_price = strip_tags( str_replace( get_woocommerce_currency_symbol(), '', $bump_price ) );
							$value['data']->set_price( $bump_price );
						} else {
							$value['data']->set_price( $price_discount );
						}
					} else {
						$value['data']->set_price( $price_discount );
					}
				}
			}
		}
	}

	/**
	 * Adds custom CSS to site.
	 *
	 * @since    1.0.2
	 */
	public function global_custom_css() {
		// Only enqueue on the cart and checkout page.
		if ( is_cart() || is_checkout() ) {

			// Ignore admin, feed, robots or trackbacks.
			if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {

				return;
			}

			$wps_ubo_global_options = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );

			$global_custom_css = ! empty( $wps_ubo_global_options['wps_ubo_offer_global_css'] ) ? $wps_ubo_global_options['wps_ubo_offer_global_css'] : '';

			if ( empty( $global_custom_css ) ) {

				return;
			}

			?>

			<style id="wps-ubo-global-css" type="text/css">
				<?php echo wp_kses_post( wp_unslash( $global_custom_css ) ); ?>
			</style>

			<?php
		}
	}

	/**
	 * Adds custom JS to site.
	 *
	 * @since    1.0.2
	 */
	public function global_custom_js() {
		// Only enqueue on the cart and checkout page.
		if ( is_cart() || is_checkout() ) {

			// Ignore admin, feed, robots or trackbacks.
			if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {

				return;
			}

			$wps_ubo_global_options = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );

			$global_custom_js = ! empty( $wps_ubo_global_options['wps_ubo_offer_global_js'] ) ? $wps_ubo_global_options['wps_ubo_offer_global_js'] : '';

			if ( empty( $global_custom_js ) ) {

				return;
			}

			?>

			<script id="wps-ubo-global-js" type="text/javascript">
				<?php echo wp_kses_post( wp_unslash( $global_custom_js ) ); ?>
			</script>

			<?php
		}
	}

	/**
	 * Disable quantity field for bump offer product.
	 *
	 * @param   boolean $boolean             Show/Hide.
	 * @param   object  $cart_item           The cart object.
	 * @since    1.2.0
	 */
	public function disable_quantity_field_in_aerocheckout( $boolean, $cart_item ) {

		if ( ! empty( $cart_item['wps_ubo_offer_product'] ) ) {

			return false;
		}

		return $boolean;
	}

	/**
	 * Hide undo notice for bump target/offer product.
	 *
	 * @param   boolean $boolean             Show/Hide.
	 * @param   object  $cart_item           The cart object.
	 * @since    1.2.0
	 */
	public function hide_undo_notice_in_aerocheckout( $boolean, $cart_item ) {

		if ( ! empty( $cart_item['wps_ubo_offer_product'] ) ) {

			return true;
		}

		if ( ! empty( $cart_item['key'] ) && null !== WC()->session->get( 'encountered_bump_tarket_key_array' ) && is_array( WC()->session->get( 'encountered_bump_tarket_key_array' ) ) ) {

			if ( in_array( $cart_item['key'], WC()->session->get( 'encountered_bump_tarket_key_array' ), true ) ) {

				return true;
			}
		}

		return $boolean;
	}

	/**
	 * Trigger order bump according to targets.
	 *
	 * @param   array $order_bump_collection   All order bump collection.
	 * @param   array $wps_ubo_global_options  All global collection.
	 * @since    1.2.0
	 */
	public static function fetch_order_bump_from_collection( $order_bump_collection = array(), $wps_ubo_global_options = array() ) {

		/**
		 * Check enability of the plugin at settings page,
		 * Get all bump lists,
		 * Check for live ones and scheduled for today only,
		 * Rest leave No need to check,
		 * For live one check if target id is present and after this category check,
		 * Save the array index that is encountered and target product key.
		 */

		// Get Multiple Order Bumps limit. Default limit is 1.
		$order_bump_limit = ! empty( $wps_ubo_global_options['wps_bump_order_bump_limit'] ) ? $wps_ubo_global_options['wps_bump_order_bump_limit'] : '1';

		$global_skip_settings = ! empty( $wps_ubo_global_options['wps_bump_skip_offer'] ) ? $wps_ubo_global_options['wps_bump_skip_offer'] : 'yes';

		$encountered_bump_key_array   = array();
		$encountered_target_key_array = array();

		if ( ! empty( $order_bump_collection ) && is_array( $order_bump_collection ) ) {

			foreach ( $order_bump_collection as $single_bump_id => $single_bump_array ) {

				if ( count( $encountered_bump_key_array ) >= $order_bump_limit ) {
					continue;
				}

				// If already encountered and saved. ( Just if happens : Worst case. )!
				if ( ! empty( $encountered_bump_key_array ) && in_array( (string) $single_bump_id, $encountered_bump_key_array, true ) ) {
					continue;
				}

				// Check Bump status.
				$single_bump_status = ! empty( $single_bump_array['wps_upsell_bump_status'] ) ? $single_bump_array['wps_upsell_bump_status'] : '';

				// Not live so continue.
				if ( 'yes' !== $single_bump_status ) {
					continue;
				}

				/**
				 * Check for Bump Schedule.
				 * For earlier versions here we will get a string instaed of array.
				 */
				if ( empty( $single_bump_array['wps_upsell_bump_schedule'] ) ) {

					// Could be '0' or array( '0' ).
					$single_bump_array['wps_upsell_bump_schedule'] = array( '0' );
				} elseif ( ! empty( $single_bump_array['wps_upsell_bump_schedule'] ) && ! is_array( $single_bump_array['wps_upsell_bump_schedule'] ) ) {                // If is string means for earlier versions.

					$single_bump_array['wps_upsell_bump_schedule'] = array( $single_bump_array['wps_upsell_bump_schedule'] );
				}

				// Check for current day condition.
				if ( ! is_array( $single_bump_array['wps_upsell_bump_schedule'] ) ) {

					continue;
				}

				// Got an array. Now check.
				if ( ! in_array( '0', $single_bump_array['wps_upsell_bump_schedule'], true ) && ! in_array( gmdate( 'N' ), $single_bump_array['wps_upsell_bump_schedule'], true ) ) {

					continue;
				}

				// WIW-CC : Comment - Don't check target products and categories as we always have to show the offer.
				// Check if target products or target categories are empty.
				$single_bump_target_ids = ! empty( $single_bump_array['wps_upsell_bump_target_ids'] ) ? $single_bump_array['wps_upsell_bump_target_ids'] : array();
				$single_bump_categories = ! empty( $single_bump_array['wps_upsell_bump_target_categories'] ) ? $single_bump_array['wps_upsell_bump_target_categories'] : array();
				$is_global_funnel       = ! empty( $single_bump_array['wps_ubo_offer_global_funnel'] ) ? $single_bump_array['wps_ubo_offer_global_funnel'] : '';

				// When both target products or target categories are empty, continue.
				if ( ( empty( $single_bump_target_ids ) && empty( $single_bump_categories ) ) && ( 'yes' !== $is_global_funnel ) ) {
					continue;
				}

				// Lets check for offer be present.
				if ( ! empty( $single_bump_array['wps_upsell_bump_products_in_offer'] ) ) {

					/**
					 * After v1.0.1 (pro)
					 * Apply smart-skip in case of pro is active.
					 */
					if ( wps_ubo_lite_if_pro_exists() && is_user_logged_in() ) {

						$wps_upsell_bump_global_smart_skip = ! empty( $wps_ubo_global_options['wps_ubo_offer_purchased_earlier'] ) ? $wps_ubo_global_options['wps_ubo_offer_purchased_earlier'] : '';
						if ( 'yes' === $wps_upsell_bump_global_smart_skip && class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) ) {

							if ( method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_skip_for_pre_order' ) && Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_skip_for_pre_order( $single_bump_array['wps_upsell_bump_products_in_offer'] ) ) {

								continue;
							}
						}
					}

					/**
					 * After v1.3.0 (pro)
					 * Apply Exclusive Limits in case of pro is active.
					 */
					if ( wps_ubo_lite_if_pro_exists() ) {

						if ( class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) ) {
							if ( method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_manage_exclusive_limit' ) ) {

								if ( false === Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_manage_exclusive_limit( $single_bump_id ) ) {
									continue;
								}
							}
						}
					}

					/**
					 * WPS Fix :: for mutliple order bump for categories.
					 */
					if ( ! empty( $encountered_bump_array ) ) {
						$encountered_bump_array = 0;
					}

					// If  target category is present.
					if ( ! empty( $single_bump_array['wps_upsell_bump_target_ids'] ) && is_array( $single_bump_array['wps_upsell_bump_target_ids'] ) ) :

						// Check if these product are present in cart one by one.
						foreach ( $single_bump_array['wps_upsell_bump_target_ids'] as $key => $single_target_id ) {

							// Check if present in cart.
							$wps_upsell_bump_target_key = wps_ubo_lite_check_if_in_cart( $single_target_id );

							// If we product is present we get the cart key.
							if ( ! empty( $wps_upsell_bump_target_key ) ) {

								// Check offer product must be in stock.
								$offer_product = wc_get_product( $single_bump_array['wps_upsell_bump_products_in_offer'] );

								if ( empty( $offer_product ) ) {
									continue;
								}

								if ( 'publish' !== $offer_product->get_status() ) {

									continue;
								}

								if ( ! $offer_product->is_in_stock() ) {

									continue;
								}

								// Check if offer product is already in cart.
								if ( wps_ubo_lite_already_in_cart( $single_bump_array['wps_upsell_bump_products_in_offer'] ) && 'yes' === $global_skip_settings ) {

									continue;
								}

								// If everything is good just break !!
								$encountered_bump_array = $single_bump_id;

								// Push the data on same index.
								array_push( $encountered_bump_key_array, $encountered_bump_array );
								array_push( $encountered_target_key_array, $wps_upsell_bump_target_key );
							}
						}

					endif;

					// 2nd foreach end for product id.

					// If target key is still empty means no target category is found yet.
					if ( empty( $encountered_bump_array ) && ! empty( $single_bump_array['wps_upsell_bump_target_categories'] ) && is_array( $single_bump_array['wps_upsell_bump_target_categories'] ) ) {

						foreach ( $single_bump_array['wps_upsell_bump_target_categories'] as $key => $single_category_id ) {

							// No target Id is found go for category,
							// Check if the category is in cart.
							$wps_upsell_bump_target_key = wps_ubo_lite_check_category_in_cart( $single_category_id );

							// If we product is present we get the cart key.
							if ( ! empty( $wps_upsell_bump_target_key ) ) {

								// Check offer product must be in stock.
								$offer_product = wc_get_product( $single_bump_array['wps_upsell_bump_products_in_offer'] );

								if ( empty( $offer_product ) ) {

									continue;
								}

								if ( 'publish' !== $offer_product->get_status() ) {

									continue;
								}

								if ( ! $offer_product->is_in_stock() ) {

									continue;
								}

								// Check if offer product is already in cart.
								if ( wps_ubo_lite_already_in_cart( $single_bump_array['wps_upsell_bump_products_in_offer'] ) && 'yes' === $global_skip_settings ) {

									continue;
								}

								// If everything is good just break !!
								$encountered_bump_array = $single_bump_id;

								// Push the data on same index.
								array_push( $encountered_bump_key_array, $encountered_bump_array );
								array_push( $encountered_target_key_array, $wps_upsell_bump_target_key );
							}
						} // Second foreach for category search end.
					}

					// If no target product/category not matched/added in bump.
					if ( empty( $encountered_bump_array ) && 'yes' === $is_global_funnel ) {

						// Check offer product must be in stock.
						$offer_product = wc_get_product( $single_bump_array['wps_upsell_bump_products_in_offer'] );

						if ( empty( $offer_product ) ) {
							continue;
						}

						if ( 'publish' !== $offer_product->get_status() ) {

							continue;
						}

						if ( ! $offer_product->is_in_stock() ) {

							continue;
						}

						// Check if offer product is already in cart.
						if ( wps_ubo_lite_already_in_cart( $single_bump_array['wps_upsell_bump_products_in_offer'] ) && 'yes' === $global_skip_settings ) {

							continue;
						}

						// If everything is good just break !!
						$encountered_bump_array     = $single_bump_id;
						$wps_upsell_bump_target_key = 'NoTarGetProDuctIsGlobalFunnel'; // Just because for global there is not target product.

						// Push the data on same index.
						if ( ! empty( $encountered_bump_array ) ) {
							array_push( $encountered_bump_key_array, $encountered_bump_array );
						}
						if ( ! empty( $wps_upsell_bump_target_key ) ) {
							array_push( $encountered_target_key_array, $wps_upsell_bump_target_key );
						}
					}
				} else {

					// If offer product is not saved, continue.
					continue;
				}
			} // $order_bump_collection foreach end.

		} // Empty and Array Condition check for $order_bump_collection.

		if ( ! empty( $encountered_bump_key_array ) && ! empty( $encountered_target_key_array ) ) {

			$result = array(
				'encountered_bump_array'     => $encountered_bump_key_array, // Order Bump IDs to be shown.
				'wps_upsell_bump_target_key' => $encountered_target_key_array,
			);

			return $result;
		}
	}

	/**
	 * Add functionality after WC exists/loaded.
	 *
	 * @since    1.2.0
	 */
	public function woocommerce_init_ubo_functions() {
		// Check woocommrece class exists.
		// if ( ! function_exists( 'WC' ) || empty( WC()->session ) ) {.

		// return;.
		// }.

		// if ( 'true' === WC()->session->get( 'encountered_bump_array_display' ) ) {.

			// Cost calculations only when the offer is added.
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'woocommerce_custom_price_to_cart_item' ) );

			// Disable quantity field at cart page.
			add_filter( 'woocommerce_cart_item_quantity', array( $this, 'disable_quantity_bump_product_in_cart' ), 10, 2 );

			// For Aerocheckout pages.
			add_filter( 'wfacp_show_item_quantity', array( $this, 'disable_quantity_field_in_aerocheckout' ), 10, 2 );

			add_filter( 'wfacp_show_undo_message_for_item', array( $this, 'hide_undo_notice_in_aerocheckout' ), 10, 2 );

			// Removing offer or target product manually by cart.
			add_action( 'woocommerce_remove_cart_item', array( $this, 'after_remove_product' ), 10, 2 );

			// Add meta data to order item for order review.
			add_action( 'woocommerce_checkout_create_order', array( $this, 'add_order_item_meta' ), 10 );

			// Add Order Bump - Order Post meta.
			add_action( 'woocommerce_checkout_order_processed', array( $this, 'add_bump_order_post_meta' ), 10 );
		// }

		// Handle Order Bump Orders on Thankyou for Success Rate and Stats.
		add_action( 'woocommerce_thankyou', array( $this, 'report_sales_by_bump_handling' ), 15 );

		// Reset Order Bump session data.
		add_action( 'woocommerce_cart_emptied', array( $this, 'reset_order_bump' ), 11 );
		add_action( 'woocommerce_thankyou', array( $this, 'reset_session_variable' ), 55 );

		// Add the custom price for the recommendation product on product page.
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'wps_add_custom_price_to_cart_item' ) );
	}

	/**
	 * Add order item meta to bump product.
	 *
	 * @param    object $order      The order in which bump offer is added.
	 * @since    1.0.0
	 */
	public function add_order_item_meta( $order ) {

		$order_items = $order->get_items();

		if ( ! empty( $order_items ) && is_array( $order_items ) ) {

			foreach ( $order_items as $item_key => $single_order_item ) {

				if ( ! empty( $single_order_item->legacy_values['wps_ubo_offer_product'] ) ) {

					$single_order_item->update_meta_data( 'is_order_bump_purchase', 'true' );
				}

				if ( ! empty( $single_order_item->legacy_values['wps_ubo_bump_id'] ) ) {

					$single_order_item->update_meta_data( 'wps_order_bump_id', $single_order_item->legacy_values['wps_ubo_bump_id'] );
				}

				if ( ! empty( $single_order_item->legacy_values['wps_ubo_meta_form'] ) && is_array( $single_order_item->legacy_values['wps_ubo_meta_form'] ) ) {

					foreach ( $single_order_item->legacy_values['wps_ubo_meta_form'] as $key => $value ) {
						$single_order_item->update_meta_data( $value['name'], $value['value'] );
					}
				}
			}
		}
	}

	/**
	 * Hide Order Bump meta from order items.
	 *
	 * @param array $formatted_meta formatted meta.
	 *
	 * @since       1.4.0
	 */
	public function hide_order_bump_meta( $formatted_meta ) {

		if ( ! empty( $formatted_meta ) && is_array( $formatted_meta ) ) {

			// Hide bump id meta for both Customers and Admin.
			foreach ( $formatted_meta as $key => $meta ) {

				if ( ! empty( $meta->key ) && 'wps_order_bump_id' === $meta->key ) {

					unset( $formatted_meta[ $key ] );
				}
			}

			// To hide bump purchase meta only for Customers place the below foreach loop in a condition( ! is_admin() ).

			foreach ( $formatted_meta as $key => $meta ) {

				if ( ! empty( $meta->key ) && 'is_order_bump_purchase' === $meta->key ) {

					unset( $formatted_meta[ $key ] );
				}
			}
		}

		return $formatted_meta;
	}

	/**
	 * Add Order Bump - Order Post meta.
	 *
	 * @param string $order_id order id.
	 *
	 * @since    1.0.0
	 */
	public function add_bump_order_post_meta( $order_id ) {

		$order = new WC_Order( $order_id );

		$order_items = $order->get_items();

		if ( ! empty( $order_items ) && is_array( $order_items ) ) {

			foreach ( $order_items as $item_id => $single_item ) {

				if ( ! empty( wc_get_order_item_meta( $item_id, 'is_order_bump_purchase', true ) ) ) {

					if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
						// HPOS usage is enabled.
						$order->update_meta_data( 'wps_bump_order', 'true' );
						$order->update_meta_data( 'wps_bump_order_process_sales_stats', 'true' );
						$order->save();
					} else {
						// Add post meta as this is a Order Bump order.
						update_post_meta( $order_id, 'wps_bump_order', 'true' );

						// Add post meta for processing Success Rate and Stats on Thankyou page.
						update_post_meta( $order_id, 'wps_bump_order_process_sales_stats', 'true' );
					}
					break;
				}
			}
		}
	}

	/**
	 * Handle Order Bump Orders on Thankyou for Success Rate and Stats.
	 *
	 * @param string $order_id order id.
	 *
	 * @since    1.4.0
	 */
	public function report_sales_by_bump_handling( $order_id ) {

		if ( ! $order_id ) {

			return;
		}
		// Get Order Object.
		$order = new WC_Order( $order_id );

		// Process once and only for Order Bump orders.
		if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
			// HPOS usage is enabled.
			$bump_order = $order->get_meta( 'wps_bump_order_process_sales_stats', true );
		} else {
			$bump_order = get_post_meta( $order_id, 'wps_bump_order_process_sales_stats', true );
		}

		if ( empty( $bump_order ) ) {

			return;
		}

		$order = new WC_Order( $order_id );

		if ( empty( $order ) ) {

			return;
		}

		$processed_order_statuses = array(
			'processing',
			'completed',
			'on-hold',
		);

		if ( ! in_array( $order->get_status(), $processed_order_statuses, true ) ) {

			return;
		}

		$order_items = $order->get_items();

		if ( ! empty( $order_items ) && is_array( $order_items ) ) {

			foreach ( $order_items as $item_id => $single_item ) {

				if ( ! empty( wc_get_order_item_meta( $item_id, 'is_order_bump_purchase', true ) ) && ! empty( wc_get_order_item_meta( $item_id, 'wps_order_bump_id', true ) ) ) {

					$order_bump_item_total = wc_get_order_item_meta( $item_id, '_line_total', true );

					// Add Order Bump Success count and Total Sales for the respective Order Bump.
					$sales_by_bump = new Wps_Upsell_Order_Bump_Report_Sales_By_Bump( wc_get_order_item_meta( $item_id, 'wps_order_bump_id', true ) );

					$sales_by_bump->add_bump_success_count();
					$sales_by_bump->add_bump_total_sales( $order_bump_item_total );

					// Delete bump id as it might change so no need to associate the order item with it.
					wc_delete_order_item_meta( $item_id, 'wps_order_bump_id' );
				}
			}
		}

		/**
		 * Delete Order Bump sales stats meta so that this is processed only once.
		 */
		if ( OrderUtil::custom_orders_table_usage_is_enabled() ) {
			// HPOS usage is enabled.
			$order->delete_meta_data( 'wps_bump_order_process_sales_stats' );
			$order->save();
		} else {
			delete_post_meta( $order_id, 'wps_bump_order_process_sales_stats' );
		}
	}

	/**
	 * On successful order reset data.
	 *
	 * @param string $order_id The order id.
	 *
	 * @since    1.0.0
	 */
	public function reset_session_variable( $order_id = '' ) {

		if ( class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) ) {
			$pro_class = new Upsell_Order_Bump_Offer_For_Woocommerce_Pro( 'Order Bump', '1.3.1' );
			$pro_class->exclusive_limit_callback( $order_id );
		}

		// Destroy session on order completed.
		wps_ubo_session_destroy();
	}

	/**
	 * On successful order reset data.
	 *
	 * @param string $order_id The order id.
	 *
	 * @since    1.0.0
	 */
	public function reset_order_bump( $order_id = '' ) {

		// Destroy session on order completed.
		wps_ubo_session_destroy();
	}

	/**
	 * Add selected product product to cart ( by button )
	 *
	 * @since    1.0.0
	 */
	public function wps_add_the_product() {
		 // The id of the offer to be added.
		check_ajax_referer( 'wps_ubo_lite_nonce', 'nonce' );
		$bump_product_id = ! empty( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

		$bump_discounted_price = ! empty( $_POST['discount'] ) ? sanitize_text_field( wp_unslash( $_POST['discount'] ) ) : '';
		$bump_index            = ! empty( $_POST['bump_index'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_index'] ) ) : '';
		$bump_target_cart_key  = ! empty( $_POST['bump_target_cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_target_cart_key'] ) ) : '';
		$order_bump_id         = ! empty( $_POST['order_bump_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_bump_id'] ) ) : '';
		$smart_offer_upgrade   = ! empty( $_POST['smart_offer_upgrade'] ) ? sanitize_text_field( wp_unslash( $_POST['smart_offer_upgrade'] ) ) : '';
		$form_data             = ! empty( $_POST['form_data'] ) ? map_deep( wp_unslash( $_POST['form_data'] ), 'sanitize_text_field' ) : array();

		// Quantity of product.
		$wps_qty_variable = ! empty( $_POST['wps_qty_variable'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_qty_variable'] ) ) : '1';

		$active_plugin = get_option( 'active_plugins', false );
		if ( in_array( 'woo-gift-cards-lite/woocommerce_gift_cards_lite.php', $active_plugin, true ) && wps_ubo_lite_if_pro_exists() && ! empty( $form_data ) ) {
			$gift_card_form = array(
				'wps_wgm_to_email'      => '',
				'wps_wgm_from_name'     => '',
				'wps_wgm_message'       => '',
				'delivery_method'       => '',
				'wps_wgm_price'         => '',
				'wps_wgm_selected_temp' => '',
			);
			$gift_card_data = get_post_meta( $bump_product_id, 'wps_wgm_pricing' );
			foreach ( $gift_card_data as $key => $value ) {
				$gift_card_form = array_merge(
					$gift_card_form,
					array(
						'wps_wgm_price'         => $value['default_price'],
						'wps_wgm_selected_temp' => $value['template'][0],
					)
				);
			}

			foreach ( $form_data as $key => $value ) {
				if ( 'from' === $value['name'] ) {
					$gift_card_form = array_merge( $gift_card_form, array( 'wps_wgm_from_name' => $value['value'] ) );
				} elseif ( 'gift message' === $value['name'] ) {
					$gift_card_form = array_merge( $gift_card_form, array( 'wps_wgm_message' => $value['value'] ) );
				} elseif ( 'mail to recepient' === $value['name'] ) {
					$gift_card_form = array_merge( $gift_card_form, array( 'wps_wgm_to_email' => $value['value'] ) );
				}
			}

			$cart_item_data = array(
				'wps_ubo_offer_product' => true,
				'wps_ubo_offer_index'   => $bump_index,
				'wps_ubo_bump_id'       => $order_bump_id,
				'wps_discounted_price'  => $bump_discounted_price,
				'wps_ubo_target_key'    => $bump_target_cart_key,
				'flag_' . uniqid()      => true,
				'wps_ubo_meta_form'     => $form_data,
				'product_meta'          => array( 'meta_data' => $gift_card_form ),
			);
		} else {
			$cart_item_data = array(
				'wps_ubo_offer_product' => true,
				'wps_ubo_offer_index'   => $bump_index,
				'wps_ubo_bump_id'       => $order_bump_id,
				'wps_discounted_price'  => $bump_discounted_price,
				'wps_ubo_target_key'    => $bump_target_cart_key,
				'flag_' . uniqid()      => true,
				'wps_ubo_meta_form'     => $form_data,
			);
		}

		$_product = wc_get_product( $bump_product_id );

		$added = 'added';

		if ( wps_ubo_lite_reload_required_after_adding_offer( $_product ) ) {

			$added = 'subs_reload';
		}

		if ( ! empty( $_product ) && $_product->has_child() ) {

			// Generate default price html.
			$bump_price_html = wps_ubo_lite_custom_price_html( $bump_product_id, $bump_discounted_price );

			$response = array(
				'key'     => 'true',
				'message' => $bump_price_html,
			);

			// Now we have to add a pop up.
			echo wp_json_encode( $response );
		} elseif ( ! empty( $_product ) ) {

			// If simple product or any single variations.
			// Add to cart the same.

			$bump_offer_cart_item_key = WC()->cart->add_to_cart( $bump_product_id, $quantity = $wps_qty_variable, $variation_id = 0, $variation = array(), $cart_item_data );

			// Add Order Bump Offer Accept Count for the respective Order Bump.
			$sales_by_bump = new Wps_Upsell_Order_Bump_Report_Sales_By_Bump( $order_bump_id );
			$sales_by_bump->add_offer_accept_count();

			WC()->session->set( 'bump_offer_status', 'added' );
			WC()->session->set( "bump_offer_status_$bump_index", $bump_offer_cart_item_key );

			// Smart offer Upgrade.
			if ( wps_ubo_lite_if_pro_exists() && 'yes' === $smart_offer_upgrade ) {

				// Get all saved bumps.
				$wps_ubo_bump_callback          = Upsell_Order_Bump_Offer_For_Woocommerce::$wps_upsell_bump_list_callback_function;
				$wps_ubo_offer_array_collection = Upsell_Order_Bump_Offer_For_Woocommerce::$wps_ubo_bump_callback();

				$encountered_bump_array = ! empty( $wps_ubo_offer_array_collection[ $order_bump_id ] ) ? $wps_ubo_offer_array_collection[ $order_bump_id ] : array();

				$wps_upsell_bump_replace_target = ! empty( $encountered_bump_array['wps_ubo_offer_replace_target'] ) ? $encountered_bump_array['wps_ubo_offer_replace_target'] : '';

				if ( 'yes' === $wps_upsell_bump_replace_target && class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) && method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_upgrade_offer' ) ) {

					Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_upgrade_offer( $bump_offer_cart_item_key, $bump_target_cart_key );
				}
			}

			/**
			 * After v1.3.0 (pro)
			 * Apply Exclusive Limits in case of pro is active.
			 */
			if ( wps_ubo_lite_if_pro_exists() ) {
				if ( class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) ) {
					if ( method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_manage_exclusive_limit' ) ) {
						$single_bump_id = str_replace( 'index_', '', $bump_index );
						if ( true === Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_manage_exclusive_limit( $single_bump_id ) ) {
							$associations = WC()->session->get( 'bump_offer_associations' );

							if ( null !== $associations ) {
								$associations .= '___';
							} else {
								$associations = '';
							}

							$associations .= $bump_index;

							WC()->session->set( 'bump_offer_associations', $associations );
						}
					}
				}
			}

			echo wp_json_encode( $added );
		}

		wp_die();
	}

	/**
	 * Remove the selected variation as bump offer product to cart ( by button )
	 *
	 * @since    1.0.0
	 */
	public function wps_remove_offer_product() {
		check_ajax_referer( 'wps_ubo_lite_nonce', 'nonce' );
		$bump_index = ! empty( $_POST['bump_index'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_index'] ) ) : '';

		if ( null !== WC()->session->get( "bump_offer_status_$bump_index" ) ) {

			WC()->cart->remove_cart_item( WC()->session->get( "bump_offer_status_$bump_index" ) );
		}

		WC()->session->__unset( "bump_offer_status_$bump_index" );

		/**
		 * After v1.3.0 (pro)
		 * Apply Exclusive Limits in case of pro is active.
		 */
		if ( wps_ubo_lite_if_pro_exists() ) {

			if ( class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) ) {
				if ( method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'wps_ubo_manage_exclusive_limit' ) ) {
					$single_bump_id = str_replace( 'index_', '', $bump_index );
					if ( true === Upsell_Order_Bump_Offer_For_Woocommerce_Pro::wps_ubo_manage_exclusive_limit( $single_bump_id ) ) {
						$associations = WC()->session->get( 'bump_offer_associations' );
						$associations = array_unique( explode( '___', $associations ) );
						$key          = array_search( $bump_index, $associations, true );
						unset( $associations[ $key ] );
						WC()->session->set( 'bump_offer_associations', implode( '___', $associations ) );
					}
				}
			}
		}

		echo wp_json_encode( 'removed' );

		wp_die();
	}

	/**
	 * Add selected variation as bump offer product to cart ( by button )
	 *
	 * @since    1.0.0
	 */
	public function wps_variation_select_added() {
		check_ajax_referer( 'wps_ubo_lite_nonce', 'nonce' );
		$bump_offer_id = ! empty( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';  // variation parent.

		$bump_offer_discount = ! empty( $_POST['discount'] ) ? sanitize_text_field( wp_unslash( $_POST['discount'] ) ) : '';

		$attributes_selected_options = ! empty( $_POST['attributes_selected_options'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['attributes_selected_options'] ) ) : array();

		// Got all values to search for variation id from selected attributes.
		$product = wc_get_product( $bump_offer_id );

		if ( empty( $product ) ) {

			echo wp_json_encode( esc_html__( 'Product Not Found.', 'upsell-order-bump-offer-for-woocommerce' ) );
			return;
		}

		$product_data_store         = new WC_Product_Data_Store_CPT();
		$variation_id               = $product_data_store->find_matching_product_variation( $product, $attributes_selected_options );
		$selected_variation_product = wc_get_product( $variation_id );

		// Image to reflect on select change.
		$image_id = get_post_thumbnail_id( $variation_id );

		if ( ! empty( $image_id ) ) {

			$html           = wc_get_gallery_image_html( $image_id, true );
			$bump_var_image = apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $image_id );
		} else {

			// If no variation image is present show default one.
			$bump_var_image = wps_ubo_lite_get_bump_image( $bump_offer_id );
		}

		// Variation id will be empty if selected variation is not available.
		if ( empty( $variation_id ) || empty( $selected_variation_product ) ) {

			$response = array(
				'key'     => 'not_available',
				'message' => '<p class="stock out-of-stock">' . esc_html__( 'Sorry, this variation is not available.', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>',
				'image'   => $bump_var_image,
			);
			echo wp_json_encode( $response );
		} else if ( ! empty( $variation_id ) || ! empty( $selected_variation_product ) ) {

			// Check if in stock?
			if ( ! $selected_variation_product->is_in_stock() ) {

				// Out of stock.
				$response = array(

					'key'     => 'stock',
					'message' => '<p class="stock out-of-stock">' . esc_html__( 'Out of stock.', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>',
					'image'   => $bump_var_image,
				);

				echo wp_json_encode( $response );
			} else {

				$response = array(
					'key'     => $variation_id,
					'message' => wps_ubo_lite_custom_price_html( $variation_id, $bump_offer_discount ),
					'image'   => $bump_var_image,
				);

				echo wp_json_encode( $response );
			}
		}

		wp_die();
	}

	/**
	 * Add selected variation as bump offer product to cart ( by button )
	 *
	 * @since    1.0.0
	 */
	public function wps_add_recommendated_offer_in_popup() {
		check_ajax_referer( 'wps_ubo_lite_nonce_recommend', 'nonce' );
		$wps_target_product = isset( $_POST['target_product_id'] ) ? absint( $_POST['target_product_id'] ) : '';
		$wps_product_id_shop = isset( $_POST['wps_product_id_shop'] ) ? absint( $_POST['wps_product_id_shop'] ) : '';
		$wps_targeted_varaition_id = isset( $_POST['wps_targeted_varaition_id'] ) ? absint( $_POST['wps_targeted_varaition_id'] ) : '';
		$wps_show_recommend_product_in_popup = false;

		if ( isset( $wps_product_id_shop ) && ! empty( $wps_product_id_shop ) ) {

			$wps_product_id_set = $wps_product_id_shop;
		} elseif ( isset( $wps_target_product ) && ! empty( $wps_target_product ) ) {

			$wps_product_id_set = $wps_target_product;
		} elseif ( isset( $wps_targeted_varaition_id ) && ! empty( $wps_targeted_varaition_id ) ) {

			$wps_product_id = $wps_targeted_varaition_id;
			$variation = wc_get_product( $wps_product_id );
			$wps_product_id_set = $variation->get_parent_id();
		}

		if ( isset( $wps_product_id_set ) && ! empty( $wps_product_id_set ) ) {
			$wps_is_recommendation_enable = get_post_meta( $wps_product_id_set, 'is_recommendation_enable' );
			$wps_selected_recommendated_product = get_post_meta( $wps_product_id_set, 'wps_recommendated_product_ids' );

			if ( ! empty( $wps_is_recommendation_enable ) ) {
				if ( ( 'yes' == $wps_is_recommendation_enable[0] ) && isset( $wps_is_recommendation_enable[0] ) && ( 0 < count( $wps_selected_recommendated_product ) ) && is_array( $wps_selected_recommendated_product ) && isset( $wps_selected_recommendated_product ) ) {
					$wps_show_recommend_product_in_popup = true;
				} else {
					$wps_show_recommend_product_in_popup = false;
				}
			}
		}

		$response = array(
			'wps_target_product' => $wps_product_id_set,
			'wps_show_recommend_product_in_popup'   => $wps_show_recommend_product_in_popup,
			'cart_item_number' => WC()->cart->get_cart_contents_count(),
			'count' => $wps_selected_recommendated_product,
		);

		echo wp_json_encode( $response );
		wp_die();
	}

	/**
	 * Ajax add to cart.
	 *
	 * @since    1.0.0
	 */
	public function ql_woocommerce_ajax_add_to_cart() {
		 check_ajax_referer( 'wps_ubo_lite_nonce_recommend', 'nonce' );
		$product_id = apply_filters( 'ql_woocommerce_add_to_cart_product_id', isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : '' );
		$quantity = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( sanitize_text_field( wp_unslash( $_POST['quantity'] ) ) );
		$variation_id = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : '';
		$passed_validation = apply_filters( 'ql_woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		$product_status = get_post_status( $product_id );
		if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id ) && 'publish' === $product_status ) {
			do_action( 'ql_woocommerce_ajax_added_to_cart', $product_id );
			if ( 'yes' === get_option( 'ql_woocommerce_cart_redirect_after_add' ) ) {
				wc_add_to_cart_message( array( $product_id => $quantity ), true );
			}
		} else {
			$data = array(
				'error' => true,
				'product_url' => apply_filters( 'ql_woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
			);
		}
		echo wp_json_encode( WC()->cart->get_cart_contents_count() );
		wp_die();
	}


	/**
	 * Add selected variation as bump offer product to cart ( by button )
	 *
	 * @since    1.0.0
	 */
	public function wps_add_to_cart_recommendation() {
		check_ajax_referer( 'wps_ubo_lite_nonce_recommend', 'nonce' );
		if ( isset( $_POST['wps_product_id'] ) ) {

			$wps_product_id = isset( $_POST['wps_product_id'] ) ? absint( $_POST['wps_product_id'] ) : '';
			$wps_product_price = isset( $_POST['wps_product_price'] ) ? absint( $_POST['wps_product_price'] ) : '';

			$wps_target_product_id = isset( $_POST['wps_target_product_id'] ) ? absint( $_POST['wps_target_product_id'] ) : '';

			$wps_select_option_discount = get_post_meta( $wps_target_product_id, 'wps_select_option_discount', true );
			$wps_recommendation_discount_val = get_post_meta( $wps_target_product_id, 'wps_recommendation_discount_val', true );

			if ( 'no_disc' == $wps_select_option_discount ) {

				// Add the product to the cart when no discount is set.
				WC()->cart->add_to_cart( $wps_product_id, 1, 0, array(), array( 'wps_cart_offer_custom_price' => $wps_product_price ) );
			} elseif ( 'wps_percent' == $wps_select_option_discount ) {

				// Get the discounted price.
				$discount_percentage = $wps_recommendation_discount_val / 100;
				$custom_discounted_price = $wps_product_price * ( 1 - $discount_percentage );

				// Add the product to the cart with Discounted Price.
				WC()->cart->add_to_cart( $wps_product_id, 1, 0, array(), array( 'wps_cart_offer_custom_price' => $custom_discounted_price ) );
			} elseif ( 'wps_fixed' == $wps_select_option_discount ) {

				// Add the product to the cart with Fixed Price.
				WC()->cart->add_to_cart( $wps_product_id, 1, 0, array(), array( 'wps_cart_offer_custom_price' => $wps_recommendation_discount_val ) );
			}
			// Return a success response.
			$data = array(
				'cart_count' => WC()->cart->get_cart_contents_count(),
				'product_id' => $wps_product_id,
			);
			echo wp_json_encode( $data );
		} else {

			// Return an error response.
			wp_send_json_error();
		}
		wp_die();
	}

	/**
	 * Change price at last for bump offer product.
	 *
	 * @param   object $cart_object The cart object.
	 * @since    1.0.0
	 */
	public function wps_add_custom_price_to_cart_item( $cart_object ) {
		foreach ( $cart_object->get_cart() as $item ) {

			if ( array_key_exists( 'wps_cart_offer_custom_price', $item ) ) {
				$item['data']->set_price( $item['wps_cart_offer_custom_price'] );
			}
		}
	}

	/**
	 * Set The Order Success Page On Placing Order.
	 *
	 * @since    1.0.0
	 */
	public function wps_redirect_custom_thank_you() {
		// Saved Global Options.
		$wps_ubo_global_options = get_option( 'wps_ubo_global_options', array() );
		$wps_custom_order_success_page = ! empty( $wps_ubo_global_options['wps_custom_order_success_page'] ) ? $wps_ubo_global_options['wps_custom_order_success_page'] : '';

		// Do nothing if we are not on the order received page.
		if ( ! is_wc_endpoint_url( 'order-received' ) || empty( $_GET['key'] ) ) {
			return;
		}
		if ( ! empty( $wps_custom_order_success_page ) || '' != $wps_custom_order_success_page ) {
			wp_safe_redirect( site_url( $wps_custom_order_success_page ) );
		} else {
			return;
		}
	}

	/**
	 * Triggered The Shortcode On Placing Order.
	 *
	 * @since    1.0.0
	 */
	public function wps_triggered_shortcode_page() {
		add_shortcode( 'wps_order_details', array( $this, 'wps_display_order_details_shortcode' ), 10 );
	}

	/**
	 * Shortcode callback On Placing Order.
	 *
	 * @since    1.0.0
	 */
	public function wps_display_order_details_shortcode() {
		if ( ! is_admin() ) {
			$stored_order_id = WC()->session->get( 'custom_order_id' );
			$order_id = absint( $stored_order_id );
			if ( ! empty( $order_id ) ) {
				ob_start();

				// Load WooCommerce templates for order details.
				wc_get_template( 'order/order-details.php', array( 'order_id' => $order_id ) );

				// Now that you've used the value, you can remove it from the session.
				return ob_get_clean();
			}
		}
	}

	/**
	 * Set the Order ID On Placing Order.
	 *
	 * @param   int $order_id The cart object.
	 * @since    1.0.0
	 */
	public function wps_custom_get_current_order_id( $order_id ) {

		// This action hook fires when a new order is created.
		// The $order_id parameter contains the ID of the newly created order.
		if ( WC()->session !== null ) {

			WC()->session->set( 'custom_order_id', $order_id );
		}
		return $order_id;
	}

	/**
	 * Set the discount on cart section html.
	 *
	 * @since    1.0.0
	 */
	public function wps_woo_cart_discount_section() {
		// Define a condition (e.g., extracting even numbers).
		$condition = function ( $value ) {
			// Check if the index exists in the array before accessing it.
			if ( isset( $value ) ) {
				if ( get_post_meta( $value, 'is_recommendation_enable_for_cart', true ) == 'yes' ) {
					return true;
				}
			} else {
				// Handle the case where the index does not exist.
				return false; // Or any appropriate default value.
			}
		};

		$wps_target_product_array = array();

		$args = array(
			'limit' => -1,
			'status' => 'publish',
			'return' => 'ids',
		);
		$products = wc_get_products( $args );

		foreach ( $products as $key => $value ) {
			if ( $condition( $value ) ) {
				$wps_target_product_array[] = $value;
			}
		}

		// Initialize an empty array to store converted integers.
		$wps_int_array = $wps_target_product_array;

		$wps_html_discount_section = '';

		// Get the cart contents.
		$cart_contents = WC()->cart->get_cart();

		if ( ! empty( $wps_int_array ) && is_array( $wps_int_array ) ) {
			foreach ( $wps_int_array as $wps_outer_array ) {
				// Check if the product ID is in the cart.
				foreach ( $cart_contents as $cart_item ) {

					if ( $cart_item['product_id'] == $wps_outer_array ) {

						$wps_offer_product_array = get_post_meta( $cart_item['product_id'], 'wps_recommendated_product_ids' );
						$wps_offer_product_discount_type = get_post_meta( $cart_item['product_id'], 'wps_select_option_discount' );
						$wps_offer_product_discount_val = get_post_meta( $cart_item['product_id'], 'wps_recommendation_discount_val' );

						if ( is_array( $wps_offer_product_array ) && ! empty( $wps_offer_product_array ) ) {

							foreach ( $wps_offer_product_array as $values ) {
								if ( is_array( $values ) && ! empty( $values ) ) {
									foreach ( $values as $value ) {

										$product = wc_get_product( $value );

										$image = wp_get_attachment_image_src( get_post_thumbnail_id( $value ), 'single-post-thumbnail' );

										$wps_html_discount_section .= '<div class="wps_main_class_order" id="wps_main_class_id_' . $value . '">';
										$wps_html_discount_section .= '<div class ="wps_product_image"><img width="100" height="300" src =' . esc_url( $image[0] ) . ' /></div>';
										$wps_html_discount_section .= '<div class ="wps_product_name">' . $product->get_name() . '</div>';

										if ( $product->is_type( 'variable' ) ) {
											// Get all variations of the parent product.
											$variations = $product->get_available_variations();

											$wps_html_discount_section .= '<div class ="wps_product_select"> <select name="select-category" id="wps-order-bump-child-id_' . $value . '">';

											foreach ( $variations as $variation ) {

												$variation_id = $variation['variation_id'];
												$variation_obj = wc_get_product( $variation_id );
												$variation_name = $variation_obj->get_name();
												$wps_discount_price = $this->wps_get_cart_offer_discount_value( $wps_offer_product_discount_type, $wps_offer_product_discount_val, $variation_obj->get_price() );
												$wps_html_discount_section .= ' <option value="' . $variation_id . '">' . $variation_name . ' - ' . wc_price( $wps_discount_price ) . '</option>';
											}

											$wps_html_discount_section .= '</select>';
											$wps_html_discount_section .= '</div>';
										}

										$wps_discount_price = $this->wps_get_cart_offer_discount_value( $wps_offer_product_discount_type, $wps_offer_product_discount_val, $product->get_price() );

										$wps_html_discount_section .= '<div class ="wps_discounted_price">' . esc_html__( 'Price:', 'upsell-order-bump-offer-for-woocommerce' ) . '<strike>' . wc_price( $product->get_price() ) . '</strike>' . wc_price( $wps_discount_price ) . '</div>';
										$wps_html_discount_section .= '<div class ="wps_discounted_qty">' . esc_html__( 'Quantity: 01', 'upsell-order-bump-offer-for-woocommerce' ) . '</div>';
										$wps_html_discount_section .= '<div class ="wps_discounted_offer_title">' . esc_html__( 'Offer!', 'upsell-order-bump-offer-for-woocommerce' ) . '</div>';
										$wps_html_discount_section .= '<div class ="wps_product_discount" value =' . $value . '><button type="button" class="button">' . esc_html__( 'Add to Cart', 'upsell-order-bump-offer-for-woocommerce' ) . '</button></div>';
										$wps_html_discount_section .= '<input id="wps_cart_offer_quantity" type="hidden" value ="1">';
										$wps_html_discount_section .= '<input id="wps_cart_offer_product_id_' . $value . '" type="hidden" value =' . $cart_item['product_id'] . '>';
										$wps_html_discount_section .= '<input class ="wps_offered_product_id" type="hidden" value =' . $value . '>';
										$wps_html_discount_section .= '<input id="wps_cart_offer_product_price_' . $value . '" type="hidden" value =' . $product->get_price() . '>';
										$wps_html_discount_section .= '</div>';
									}
								}
							}
						} else {
							wc_add_notice( 'Check Recommendation Product.', 'error' );
						}
					}
				}
			}
		}
		$allowed_html = wps_ubo_lite_allowed_html();
		echo wp_kses( $wps_html_discount_section, $allowed_html );
	}

	/**
	 * Ajax to add the cart offer in cart.
	 *
	 * @since    1.0.0
	 */
	public function wps_add_cart_discount_offer_in_cart() {
		 check_ajax_referer( 'wps_ubo_lite_nonce_recommend', 'nonce' );
		$parent_product_id = isset( $_POST['parent_product_id'] ) ? absint( $_POST['parent_product_id'] ) : '';
		$child_product_id = isset( $_POST['child_product_id'] ) ? absint( $_POST['child_product_id'] ) : '';
		$wps_cart_offer_product_price = isset( $_POST['wps_cart_offer_product_price'] ) ? absint( $_POST['wps_cart_offer_product_price'] ) : '';
		$wps_cart_offer_quantity_value = isset( $_POST['wps_cart_offer_quantity_value'] ) ? absint( $_POST['wps_cart_offer_quantity_value'] ) : '';
		$wps_cart_offer_product_id_value = ! empty( $_POST['wps_cart_offer_product_id_value'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_cart_offer_product_id_value'] ) ) : '';

		$message = '';
		$wps_discount_price = '';

		$wps_offer_product_discount_type = get_post_meta( $wps_cart_offer_product_id_value, 'wps_select_option_discount' );
		$wps_offer_product_discount_val = get_post_meta( $wps_cart_offer_product_id_value, 'wps_recommendation_discount_val' );

		$product = wc_get_product( $parent_product_id );

		if ( $product->is_type( 'variable' ) ) {
			try {
				if ( ! empty( $child_product_id ) ) {
					// Get the variation object.
					$variation_product = wc_get_product( $child_product_id );
					// Get the price of the variation.
					$variation_price = $variation_product->get_price();
					$wps_discount_price = $this->wps_get_cart_offer_discount_value( $wps_offer_product_discount_type, $wps_offer_product_discount_val, $variation_price );
					// Create an array of product data to add to the cart.
					$cart_item_data = array(
						'_price' => $wps_discount_price, // Set the discounted price.
					);
					// Add the product to the cart.
					$result = WC()->cart->add_to_cart( $child_product_id, $wps_cart_offer_quantity_value, 0, array(), $cart_item_data );
				}

				if ( $result ) {
					// Product added to the cart successfully.
					$message = 'remove';
					wc_add_notice( 'Cart Offer Successfully Added To cart.', 'success' );
				} else {
					// Product could not be added to the cart (e.g., if it's out of stock).
					$message = 'Product could not be added to the cart.';
					wc_add_notice( 'Cart Offer Unable  To  Add To cart.', 'error' );
				}
			} catch ( \Exception $e ) {
				wc_add_notice( 'Unexpected error occurred.', 'error' );
			}
		} else {

			try {
				if ( ! empty( $parent_product_id ) ) {
					$wps_discount_price = $this->wps_get_cart_offer_discount_value( $wps_offer_product_discount_type, $wps_offer_product_discount_val, $wps_cart_offer_product_price );
					$cart_item_data = array(
						'_price' => $wps_discount_price, // Set the discounted price.
					);

					$result = WC()->cart->add_to_cart( $parent_product_id, $wps_cart_offer_quantity_value, 0, array(), $cart_item_data );
				}

				if ( $result ) {
					// Product added to the cart successfully.
					$message = 'remove';
					wc_add_notice( 'Cart Offer Successfully Added To cart.', 'success' );
				} else {
					// Product could not be added to the cart (e.g., if it's out of stock).
					$message = 'Product could not be added to the cart.';
					wc_add_notice( 'Cart Offer Unable  To  Add To cart.', 'error' );
				}
			} catch ( \Exception $e ) {
				wc_add_notice( 'Unexpected error occurred.', 'error' );
			}
		}

		$response = array(
			'key'     => 'true',
			'message' => $message,
		);

		echo wp_json_encode( $response );
		wp_die();
	}

	/**
	 * Get the discounted price on cart offer.
	 *
	 * @param   array  $wps_offer_product_discount_type The discount string.
	 * @param   array  $wps_offer_product_discount_val The discount string.
	 * @param   string $wps_cart_offer_product_price The discount string.
	 * @since    1.0.0
	 */
	public function wps_get_cart_offer_discount_value( $wps_offer_product_discount_type, $wps_offer_product_discount_val, $wps_cart_offer_product_price ) {

		if ( is_array( $wps_offer_product_discount_type ) && ! empty( $wps_offer_product_discount_type ) && is_array( $wps_offer_product_discount_val ) && ! empty( $wps_offer_product_discount_val ) ) {
			if ( 'wps_percent' == $wps_offer_product_discount_type[0] ) {   // For the Percentaged count.
				// Get the product's regular price.
				$regular_price = floatval( $wps_cart_offer_product_price );

				$wps_discounted_price = $regular_price - ( $regular_price * ( $wps_offer_product_discount_val[0] / 100 ) );
			}
			if ( 'wps_fixed' == $wps_offer_product_discount_type[0] ) {

				$regular_price = floatval( $wps_offer_product_discount_val[0] );
				$wps_discounted_price = $wps_offer_product_discount_val[0];
			}
			if ( 'no_disc' == $wps_offer_product_discount_type[0] ) {
				$wps_discounted_price = $wps_cart_offer_product_price;
			}
		}

		return $wps_discounted_price;
	}

	/**
	 * Set the custom price in cart.
	 *
	 * @param   object $cart_object cart object.
	 * @since    1.0.0
	 */
	public function wps_order_cart_custom_price_refresh( $cart_object ) {

		foreach ( $cart_object->get_cart() as $item ) {

			if ( array_key_exists( '_price', $item ) ) {
				$item['data']->set_price( $item['_price'] );
			}
		}
	}

	/**
	 * Show Order Bump On Checkout Block.
	 *
	 * @since    1.0.0
	 */
	public function wps_show_bump_on_checkout_block_callback() {
		// Get the ID of the selected checkout page from WooCommerce settings.
		$checkout_page_id = get_option( 'woocommerce_checkout_page_id' );

		// Get the content of the checkout page.
		$checkout_page_content = get_post_field( 'post_content', $checkout_page_id );

		// Check if the content contains a class associated with the block editor.
		if ( strpos( $checkout_page_content, 'wp-block-woocommerce-checkout' ) !== false ) {
			require_once plugin_dir_path( __FILE__ ) . '/partials/upsell-order-bump-offer-for-woocommerce-public-display.php';
		}
	}

	/**
	 * Show Order Bump On Cart Block.
	 *
	 * @since    1.0.0
	 */
	public function wps_show_bump_on_cart_block_callback() {
		$wps_ubo_global_options = get_option( 'wps_ubo_global_options', array() );
		// Get the ID of the selected cart page from WooCommerce settings.
		$cart_page_id = get_option( 'woocommerce_cart_page_id' );

		// Get the content of the checkout page.
		$cart_page_content = get_post_field( 'post_content', $cart_page_id );

		// Check cart upsell enabled.
		$wps_enable_cart_upsell_feature = ! empty( $wps_ubo_global_options['wps_enable_cart_upsell_feature'] ) ? $wps_ubo_global_options['wps_enable_cart_upsell_feature'] : 'on';
		if ( 'on' == $wps_enable_cart_upsell_feature && strpos( $cart_page_content, 'wp-block-woocommerce-cart' ) !== false ) {
			require_once plugin_dir_path( __FILE__ ) . '/partials/upsell-order-bump-offer-for-woocommerce-public-display.php';
		}
	}
	// End of class.
}

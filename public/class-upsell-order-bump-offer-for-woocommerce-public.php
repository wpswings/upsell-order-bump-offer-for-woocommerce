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

		// Only enqueue on the Checkout page.
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {

			return;
		}

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/upsell-order-bump-offer-for-woocommerce-public.css', array(), $this->version, 'all' );
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
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {

			return;
		}

		// Public Script.
		wp_enqueue_script( 'wps-ubo-lite-public-script', plugin_dir_url( __FILE__ ) . 'js/wps_ubo_lite_public_script.js', array( 'jquery' ), $this->version, false );

		// Localizing Array.
		$local_arr = array(
			'ajaxurl'     => admin_url( 'admin-ajax.php' ),
			'mobile_view' => wp_is_mobile(),
			'auth_nonce'  => wp_create_nonce( 'wps_ubo_lite_nonce' ),
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
							'counter' => ! empty( $wps_upsell_bumps_list[ $order_bump_id ]['design_text']['wps_upsell_bump_offer_timer'] ) ? $wps_upsell_bumps_list[ $order_bump_id ]['design_text']['wps_upsell_bump_offer_timer'] : '',
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
		// Timer Functionality ends.

		wp_localize_script(
			'wps-ubo-lite-public-script',
			'wps_ubo_lite_public',
			$local_arr
		);

		// Do not work in mobile-view mode.
		if ( ! wp_is_mobile() ) {

			wp_enqueue_script( 'zoom-script', plugins_url( '/js/zoom-script.js', __FILE__ ), array( 'jquery' ), $this->version, true );
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

		} else {

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
						} else { // When Target dependency is set to Keep Offer product.

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

		// Only enqueue on the Checkout page.
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {

			return;
		}

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

	/**
	 * Adds custom JS to site.
	 *
	 * @since    1.0.2
	 */
	public function global_custom_js() {

		// Only enqueue on the Checkout page.
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {

			return;
		}

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
		if ( ! function_exists( 'WC' ) || empty( WC()->session ) ) {

			return;
		}

		if ( 'true' === WC()->session->get( 'encountered_bump_array_display' ) ) {

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

		}

		// Handle Order Bump Orders on Thankyou for Success Rate and Stats.
		add_action( 'woocommerce_thankyou', array( $this, 'report_sales_by_bump_handling' ), 15 );

		// Reset Order Bump session data.
		add_action( 'woocommerce_cart_emptied', array( $this, 'reset_order_bump' ), 11 );
		add_action( 'woocommerce_thankyou', array( $this, 'reset_session_variable' ), 55 );

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

					// Add post meta as this is a Order Bump order.
					update_post_meta( $order_id, 'wps_bump_order', 'true' );
					// Add post meta for processing Success Rate and Stats on Thankyou page.
					update_post_meta( $order_id, 'wps_bump_order_process_sales_stats', 'true' );
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

		// Process once and only for Order Bump orders.
		$bump_order = get_post_meta( $order_id, 'wps_bump_order_process_sales_stats', true );

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
		delete_post_meta( $order_id, 'wps_bump_order_process_sales_stats' );
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

	// End of class.
}

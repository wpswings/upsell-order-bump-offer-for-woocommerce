<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
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
 * @author     Make Web Better <webmaster@makewebbetter.com>
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
		$this->version = $version;

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/upsell-order-bump-offer-for-woocommerce-public.js', array( 'jquery' ), $this->version, false );

		// Public ajax.
		wp_enqueue_script( 'add_bump_offer_to_cart', plugin_dir_url( __FILE__ ) . 'js/mwb_ubo_lite_public_script.js', array( 'jquery' ), $this->version, false  );

		wp_localize_script(
			'add_bump_offer_to_cart',
			'mwb',
			array(
				'ajaxurl'       => admin_url( 'admin-ajax.php' ),
				'mobile_view'   => wp_is_mobile(),
				'auth_nonce'    => wp_create_nonce( 'mwb_ubo_lite_nonce' ),
			)
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

			do_action( 'mwb_ubo_after_pg_before_terms' );

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
		if( function_exists( 'is_checkout' ) && is_checkout() ) {

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
		check_ajax_referer( 'mwb_ubo_lite_nonce', 'nonce' );

		// The id of the offer to be added.
		$bump_product_id = ! empty( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

		$bump_discounted_price = ! empty( $_POST['discount'] ) ? sanitize_text_field( wp_unslash( $_POST['discount'] ) ) : '';
		$bump_index = ! empty( $_POST['bump_index'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_index'] ) ) : '';
		$bump_target_cart_key = ! empty( $_POST['bump_target_cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_target_cart_key'] ) ) : '';

		$cart_item_data = array(
			'mwb_ubo_offer_product' => true,
			'mwb_ubo_offer_index' => $bump_index,
			'mwb_discounted_price' => $bump_discounted_price,
			'mwb_ubo_target_key' => $bump_target_cart_key,
			'flag_' . uniqid() => true,
		);

		$_product = wc_get_product( $bump_product_id );

		if ( ! empty( $_product ) && $_product->has_child() ) {

			// Generate default price html.
			$bump_price_html = mwb_ubo_lite_custom_price_html( $bump_product_id, $bump_discounted_price );

			$response = array(
				'key' => esc_html__( 'true', 'upsell-order-bump-offer-for-woocommerce' ),
				'message' => $bump_price_html,
			);

			// Now we have to add a pop up.
			echo json_encode( $response );

		} else {

			// If simple product or any single variations.
			// Add to cart the same.

			$bump_offer_cart_item_key = WC()->cart->add_to_cart( $bump_product_id, $quantity = 1, $variation_id = 0, $variation = array(), $cart_item_data );

			WC()->session->set( 'bump_offer_status' , esc_html__( 'added', 'upsell-order-bump-offer-for-woocommerce' ) );
			WC()->session->set( "bump_offer_status_$bump_index" , $bump_offer_cart_item_key );

			// WIW-CC : No need to upgrade.
			// if ( null != WC()->session->get( 'bump_offer_product_key' ) ) {


				
			// 	if ( mwb_ubo_lite_if_pro_exists() ) {

			// 		// Get all saved bumps.
			// 		$mwb_ubo_bump_callback = Upsell_Order_Bump_Offer_For_Woocommerce::$mwb_upsell_bump_list_callback_function;
			// 		$mwb_ubo_offer_array_collection = Upsell_Order_Bump_Offer_For_Woocommerce::$mwb_ubo_bump_callback();

			// 		$order_bump_index = null != WC()->session->get( 'encountered_bump_array' ) ? WC()->session->get( 'encountered_bump_array' ) : '';

			// 		$encountered_bump_array = ! empty( $mwb_ubo_offer_array_collection[ $order_bump_index ] ) ? $mwb_ubo_offer_array_collection[ $order_bump_index ] : array();

			// 		$mwb_upsell_bump_replace_target = ! empty( $encountered_bump_array['mwb_ubo_offer_replace_target'] ) ? $encountered_bump_array['mwb_ubo_offer_replace_target'] : '';

			// 		if ( 'yes' == $mwb_upsell_bump_replace_target && class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) && method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'mwb_ubo_upgrade_offer' ) ) {

			// 			Upsell_Order_Bump_Offer_For_Woocommerce_Pro::mwb_ubo_upgrade_offer();
			// 		}
			// 	}
			// }

			echo json_encode( WC()->session->get( 'bump_offer_status' ) );
		}

		wp_die();
	}


	/**
	 * Remove bump offer product to cart.
	 *
	 * @since    1.0.0
	 */
	public function remove_offer_in_cart() {

		// Nonce verification.
		check_ajax_referer( 'mwb_ubo_lite_nonce', 'nonce' );

		$bump_index = ! empty( $_POST['bump_index'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_index'] ) ) : '';

		// At this time, we already have the offer product cart key.
		// This settings won't be applicable if the pro feature ( smart upgrade is enabled ).
		// if ( mwb_ubo_lite_if_pro_exists() ) {

		// 	// Get all saved bumps.
		// 	$mwb_ubo_bump_callback = Upsell_Order_Bump_Offer_For_Woocommerce::$mwb_upsell_bump_list_callback_function;
		// 	$mwb_ubo_offer_array_collection = Upsell_Order_Bump_Offer_For_Woocommerce::$mwb_ubo_bump_callback();

		// 	$order_bump_index = null != WC()->session->get( 'encountered_bump_array' ) ? WC()->session->get( 'encountered_bump_array' ) : '';

		// 	$encountered_bump_array = ! empty( $mwb_ubo_offer_array_collection[ $order_bump_index ] ) ? $mwb_ubo_offer_array_collection[ $order_bump_index ] : array();

		// 	$mwb_upsell_bump_offer_upgrade = ! empty( $encountered_bump_array['mwb_ubo_offer_replace_target'] ) ? $encountered_bump_array['mwb_ubo_offer_replace_target'] : '';

		// 	if ( 'yes' == $mwb_upsell_bump_offer_upgrade && class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) && method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'mwb_ubo_retrieve_target' ) ) {

		// 		// On removal of offer product retrieve the target product.
		// 		Upsell_Order_Bump_Offer_For_Woocommerce_Pro::mwb_ubo_retrieve_target();
		// 	}
		// }

		if( null != WC()->session->get( "bump_offer_status_$bump_index" ) ) {

			WC()->cart->remove_cart_item( WC()->session->get( "bump_offer_status_$bump_index" ) );
		}

		WC()->session->__unset( "bump_offer_status_$bump_index" );

		echo json_encode( esc_html__( 'removed', 'upsell-order-bump-offer-for-woocommerce' ) );

		wp_die();
	}


	/**
	 * Search selected variation.
	 *
	 * @since    1.0.0
	 */
	public function search_variation_id_by_select() {

		// Nonce verification.
		check_ajax_referer( 'mwb_ubo_lite_nonce', 'nonce' );

		$bump_offer_id = ! empty( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';  // variation parent.

		$bump_offer_discount = ! empty( $_POST['discount'] ) ? sanitize_text_field( wp_unslash( $_POST['discount'] ) ) : '';

		// Unused Here.
		// $bump_target_cart_key = ! empty( $_POST['bump_target_cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_target_cart_key'] ) ) : '';

		$attributes_selected_options = ! empty( $_POST['attributes_selected_options'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['attributes_selected_options'] ) ) : array();

		// Got all values to search for variation id from selected attributes.
		$product = wc_get_product( $bump_offer_id );

		if ( empty( $product ) ) {

			echo json_encode( esc_html__( 'Product Not Found.', 'upsell-order-bump-offer-for-woocommerce' ) );
			return;
		}

		$product_data_store = new WC_Product_Data_Store_CPT();
		$variation_id = $product_data_store->find_matching_product_variation( $product, $attributes_selected_options );
		$selected_variation_product = wc_get_product( $variation_id );

		// Image to reflect on select change.
		$image_id = get_post_thumbnail_id( $variation_id );

		if ( ! empty( $image_id ) ) {

			$html = wc_get_gallery_image_html( $image_id, true );
			$bump_var_image = apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $image_id );

		} else {

			// If no variation image is present show default one.
			$bump_var_image = mwb_ubo_lite_get_bump_image( $bump_offer_id );
		}

		// Variation id will be empty if selected variation is not available.
		if ( empty( $variation_id ) || empty( $selected_variation_product ) ) {

			$response = array(
				'key' => 'not_available',
				'message' => '<p class="stock out-of-stock">' . esc_html__( 'Sorry, this variation is not available.', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>',
				'image' => $bump_var_image,
			);
			echo json_encode( $response );

		} else {

			// Check if in stock?
			if ( ! $selected_variation_product->is_in_stock() ) {

				// Out of stock.
				$response = array(

					'key' => 'stock',
					'message' => '<p class="stock out-of-stock">' . esc_html__( 'Out of stock.', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>',
					'image' => $bump_var_image,
				);

				echo json_encode( $response );

			} else {

				$response = array(
					'key' => $variation_id,
					'message' => mwb_ubo_lite_custom_price_html( $variation_id, $bump_offer_discount ),
					'image' => $bump_var_image,
				);

				echo json_encode( $response );
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

		check_ajax_referer( 'mwb_ubo_lite_nonce', 'nonce' );

		// Contains selected variation ID.
		$variation_id = ! empty( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

		// Contains parent variable ID.
		$variation_parent_id = ! empty( $_POST['parent_id'] ) ? sanitize_text_field( wp_unslash( $_POST['parent_id'] ) ) : '';

		// Contains bump discount.
		$bump_offer_discount = ! empty( $_POST['discount'] ) ? sanitize_text_field( wp_unslash( $_POST['discount'] ) ) : '';

		// Contains target cart key.
		$bump_target_cart_key = ! empty( $_POST['bump_target_cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_target_cart_key'] ) ) : '';

		$bump_index = ! empty( $_POST['bump_index'] ) || '0' == $_POST['bump_index'] ? $_POST['bump_index'] : '';

		// Now safe to add to cart.
		$cart_item_data = array(
			'mwb_ubo_offer_product' => true,
			'mwb_discounted_price' => $bump_offer_discount,
			'flag_' . uniqid() => true,
			'mwb_ubo_offer_index' => 'index_' . $bump_index,
			'mwb_ubo_target_key' => $bump_target_cart_key,
		);

		$bump_offer_cart_item_key = WC()->cart->add_to_cart( $variation_parent_id, $quantity = '1', $variation_id, $variation = array(), $cart_item_data );

		WC()->session->set( "bump_offer_status_index_$bump_index" , $bump_offer_cart_item_key );

		WC()->session->set( 'bump_offer_status' , esc_html__( 'added', 'upsell-order-bump-offer-for-woocommerce' ) );

		// if ( null != WC()->session->get( 'bump_offer_product_key' ) ) {

		// 	if ( mwb_ubo_lite_if_pro_exists() ) {

		// 		// Get all saved bumps.
		// 		$mwb_ubo_bump_callback = Upsell_Order_Bump_Offer_For_Woocommerce::$mwb_upsell_bump_list_callback_function;
		// 		$mwb_ubo_offer_array_collection = Upsell_Order_Bump_Offer_For_Woocommerce::$mwb_ubo_bump_callback();

		// 		$order_bump_index = null != WC()->session->get( 'encountered_bump_array' ) ? WC()->session->get( 'encountered_bump_array' ) : '';

		// 		$encountered_bump_array = ! empty( $mwb_ubo_offer_array_collection[ $order_bump_index ] ) ? $mwb_ubo_offer_array_collection[ $order_bump_index ] : array();

		// 		$mwb_upsell_bump_replace_target = ! empty( $encountered_bump_array['mwb_ubo_offer_replace_target'] ) ? $encountered_bump_array['mwb_ubo_offer_replace_target'] : '';

		// 		if ( 'yes' == $mwb_upsell_bump_replace_target && class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) && method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'mwb_ubo_upgrade_offer' ) ) {

		// 			Upsell_Order_Bump_Offer_For_Woocommerce_Pro::mwb_ubo_upgrade_offer();
		// 		}
		// 	}
		// }

		echo json_encode( WC()->session->get( 'bump_offer_status' ) );
		wp_die();
	}

	/**
	 * On successful order reset data.
	 *
	 * @since    1.0.0
	 */
	public function reset_session_variable() {

		// Destroy session on order completed.
		mwb_ubo_session_destroy();
	}

	/**
	 * Add order item meta to bump product.
	 *
	 * @param    object $order      The order in which bump offer is added.
	 * @since    1.0.0
	 */
	public function add_order_item_meta( $order ) {

		$order_items = $order->get_items();

		foreach ( $order_items as $item_key => $single_order_item ) {

			if ( ! empty( $single_order_item->legacy_values['mwb_ubo_offer_product'] ) ) {

				$single_order_item->update_meta_data( esc_html__( 'Bump Offer', 'upsell-order-bump-offer-for-woocommerce' ), esc_html__( 'applied', 'upsell-order-bump-offer-for-woocommerce' ) );
			}
		}
	}

	/**
	 * Disabling the offer quantity for bump product in Cart page.
	 *
	 * @param    string $product_quantity       Quantity at cart page.
	 * @param    string $cart_item_key          Cart item key.
	 * @since    1.0.0
	 */
	public function disable_quantity_bump_product_in_cart( $product_quantity, $cart_item_key ) {

		if ( null != WC()->session->get( 'bump_offer_status' ) ) {

			$cart_item = WC()->cart->cart_contents[ $cart_item_key ];

			if( ! empty( $cart_item['mwb_ubo_offer_product'] ) ) {

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
	 * Removal of target and bump product is handled here.
	 *
	 * @param   string $key_to_be_removed      The cart item key which is being removed.
	 * @param   object $cart_object            The cart object.
	 * @since   1.0.0
	 */
	public function after_remove_product( $key_to_be_removed, $cart_object ) {

		$may_be_offer_item = false;
		$may_be_target_item = false;

		if( ! empty( $key_to_be_removed ) ) {

			$cart_item = WC()->cart->cart_contents[ $key_to_be_removed ];

			if( ! empty( $cart_item['mwb_ubo_offer_product'] ) ) {

				// The item which is removing is offer product.
				$bump_index = !empty( $cart_item['mwb_ubo_offer_index'] ) ?  $cart_item['mwb_ubo_offer_index'] : '';
				$may_be_offer_item = true;

			}

			// If the removing item is normal, then verify is a target item for some offer product.
			elseif( ! empty( $cart_object->cart_contents ) && is_array( $cart_object->cart_contents ) ) {

				foreach ( $cart_object->cart_contents as $cart_offer_item_key => $cart_offer_item ) {

					// If item is offer product, continue.
					if( ! empty( $cart_offer_item['mwb_ubo_offer_product'] ) ) {

						if( $cart_offer_item['mwb_ubo_target_key'] == $key_to_be_removed ) {

							// If the same target key is found in order cart item, Handle offer product too.
							$bump_index = !empty( $cart_offer_item['mwb_ubo_offer_index'] ) ?  $cart_offer_item['mwb_ubo_offer_index'] : '';
							$may_be_target_item = true;
							break;
						}
					}
				}
			}
		}

		if( $may_be_offer_item ) {

			/**
			 * Here the removing product is an offer product.
			 * Remove the offer product and unset the session index.
			 * Do not delete other session variables.
			 */
			if( ! empty( $bump_index ) ) {

				// Prevent offer rollback on undo.
				unset( WC()->cart->cart_contents[ $key_to_be_removed ]['mwb_ubo_offer_product'] );
				unset( WC()->cart->cart_contents[ $key_to_be_removed ]['mwb_ubo_offer_index'] );
				unset( WC()->cart->cart_contents[ $key_to_be_removed ]['mwb_discounted_price'] );
				unset( WC()->cart->cart_contents[ $key_to_be_removed ]['mwb_ubo_target_key'] );

				WC()->session->__unset( 'bump_offer_status_' . $bump_index );
			}

		} elseif ( $may_be_target_item ) {

			/**
			 * Here the removing product is an Target product.
			 * If target dependency is yes then remove the offer product and unset the session index.
			 * Do not delete other session variables.
			 */

			// Global settings.
			$mwb_ubo_global_options = get_option( 'mwb_ubo_global_options', mwb_ubo_lite_default_global_options() );

			// Do this only when settings are setted yes.
			if ( 'yes' == $mwb_ubo_global_options['mwb_ubo_offer_removal'] ) {

				// Remove bump offer product too if present.
				if ( ! empty( $cart_offer_item_key ) ) {

					// Prevent roll back.
					unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_ubo_offer_product'] );
					unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_ubo_offer_index'] );
					unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_discounted_price'] );
					unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_ubo_target_key'] );
					unset( WC()->cart->cart_contents[ $cart_offer_item_key ] );
				}

				// Here you need to unset only offer key from session. No need to reset session completely.
				WC()->session->__unset( 'bump_offer_status_' . $bump_index );

			} else {

				if( ! empty( $cart_offer_item_key ) ) {

					// Convert to normal product.
					unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_ubo_offer_product'] );
					unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_ubo_offer_index'] );
					unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_discounted_price'] );
					unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_ubo_target_key'] );

					// Here you need to unset only offer key from session. No need to reset session completely.
					WC()->session->__unset( 'bump_offer_status_' . $bump_index );
				}
			}

			/**
			 * When Target is removed it means that funnel is no longer been shown,
			 * hence remove it from session too.
			 */
			$remove_index = str_replace( 'index_', '', $bump_index );
	
			if( ! empty( $remove_index ) || '0' == $remove_index ) {

				$encountered_bump_ids_array = WC()->session->get( 'encountered_bump_array' );
				unset( $encountered_bump_ids_array[ $remove_index ] );
				WC()->session->set( 'encountered_bump_array' , $encountered_bump_ids_array );
			}
		}
	}

	/**
	 * When the cart item product is removed.
	 *
     * WIW-CC start : if only offer products are present then remove them as any one target needs to be
	 * present.
	 *
	 * @param   string $key_to_be_removed      The cart item key which is being removed.
	 * @param   object $cart_object            The cart object.
	 * @since   1.0.0
	 */
	public function after_product_removed_from_cart( $key_that_removed, $after_remove_cart_object ) {

		$cart_items = $cart_object->cart_contents;

		if( empty( $cart_items ) || ! is_array( $cart_items ) ) {

			return;
		}

		$removed_item = $cart_items[ $key_that_removed ];

		// If offer product do nothing.
		if( $removed_item[ 'mwb_ubo_offer_product' ] ) {

			return;
		}

		else {

			$target_product_ids = WC()->session->get( 'encountered_bump_tarket_key_array' );
			if( in_array( $key_that_removed , $target_product_ids ) ) {

				// Incase the target was removed, we will face some issues in maintaining indexing.
				foreach ( $cart_items as $cart_item_key => $cart_item_data ) {

					if( ! empty( $cart_item_data[ 'mwb_ubo_offer_product' ] ) ) {

						unset( WC()->cart->cart_contents[ $cart_item_key ]['mwb_ubo_offer_product'] );
						unset( WC()->cart->cart_contents[ $cart_item_key ]['mwb_ubo_offer_index'] );
						unset( WC()->cart->cart_contents[ $cart_item_key ]['mwb_discounted_price'] );
						unset( WC()->cart->cart_contents[ $cart_item_key ]['mwb_ubo_target_key'] );
						unset( WC()->cart->cart_contents[ $cart_item_key ] );
					}
				}

				mwb_ubo_session_destroy();
			}
		}
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

				if ( ! empty( $value['mwb_discounted_price'] ) ) {

					$product_id = ! empty( $value['variation_id'] ) ? $value['variation_id'] : $value['product_id'];

					$price_discount = mwb_ubo_lite_custom_price_html( $product_id, $value['mwb_discounted_price'], 'price' );

					$value['data']->set_price( $price_discount );
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

		// Ignore admin, feed, robots or trackbacks.
		if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {

			return;
		}

		$mwb_ubo_global_options = get_option( 'mwb_ubo_global_options', mwb_ubo_lite_default_global_options() );

		$global_custom_css = ! empty( $mwb_ubo_global_options['mwb_ubo_offer_global_css'] ) ? $mwb_ubo_global_options['mwb_ubo_offer_global_css'] : '';

		if ( empty( $global_custom_css ) ) {

			return;
		}

		?>

		<style id="mwb-ubo-global-css" type="text/css">

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

		// Ignore admin, feed, robots or trackbacks.
		if ( is_admin() || is_feed() || is_robots() || is_trackback() ) {

			return;
		}

		$mwb_ubo_global_options = get_option( 'mwb_ubo_global_options', mwb_ubo_lite_default_global_options() );

		$global_custom_js = ! empty( $mwb_ubo_global_options['mwb_ubo_offer_global_js'] ) ? $mwb_ubo_global_options['mwb_ubo_offer_global_js'] : '';

		if ( empty( $global_custom_js ) ) {

			return;
		}

		?>

		<script id="mwb-ubo-global-js" type="text/javascript">

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

		if ( ! empty( $cart_item['mwb_ubo_offer_product'] ) ) {

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

		if ( ! empty( $cart_item['mwb_ubo_offer_product'] ) ) {

			return true;
		}

		if ( ! empty( $cart_item['key'] ) && null != WC()->session->get( 'mwb_upsell_bump_target_key' ) ) {

			if ( $cart_item['key'] == WC()->session->get( 'mwb_upsell_bump_target_key' ) ) {

				return true;
			}
		}

		return $boolean;
	}

	/**
	 * Trigger order bump according to targets.
	 *
	 * @param   array $order_bump_collection            All order bump collection.
	 * @since    1.2.0
	 */
	public function fetch_order_bump_from_collection( $order_bump_collection = array(), $bump_limit = '1' ) {

		/**
		 * Check enability of the plugin at settings page,
		 * Get all bump lists,
		 * Check for live ones and scheduled for today only,
		 * Rest leave No need to check,
		 * For live one check if target id is present and after this category check,
		 * Save the array index that is encountered and target product key.
		 */

		$mwb_ubo_global_options = get_option( 'mwb_ubo_global_options', mwb_ubo_lite_default_global_options() );

		$mwb_upsell_bump_global_skip_settings = ! empty( $mwb_ubo_global_options['mwb_bump_skip_offer'] ) ? $mwb_ubo_global_options['mwb_bump_skip_offer'] : 'yes';

		$encountered_bump_key_array = array();
		$encountered_target_key_array = array();

		// WIW-CC : Send Two Order Bump IDs after validations ( Means N no. will be send when they are Live and good else 1 will be sent or nill ) .
		if( $bump_limit >= count( $encountered_bump_key_array ) ) {

			foreach ( $order_bump_collection as $single_bump_id => $single_bump_array ) {

				// If already encountered and saved. ( Just if happens : Worst case. )
				if( ! empty( $encountered_bump_key_array ) && in_array( $single_bump_id , $encountered_bump_key_array ) ) {

					continue;
				}

				// Check Bump status.
				$single_bump_status = ! empty( $single_bump_array['mwb_upsell_bump_status'] ) ? $single_bump_array['mwb_upsell_bump_status'] : '';

				// Not live so continue.
				if ( 'yes' != $single_bump_status ) {

					continue;
				}

				/**
				 * Check for Bump Schedule.
				 * For earlier versions here we will get a string instaed of array.
				 */
				if( empty( $single_bump_array['mwb_upsell_bump_schedule'] ) ) {

					// Could be '0' or array( '0' );
					$single_bump_array['mwb_upsell_bump_schedule'] = array( '0' );

				} 

				// If is string means for earlier versions.
				elseif( ! empty( $single_bump_array['mwb_upsell_bump_schedule'] ) && ! is_array( $single_bump_array['mwb_upsell_bump_schedule'] ) ) {

					$single_bump_array['mwb_upsell_bump_schedule'] = array( $single_bump_array['mwb_upsell_bump_schedule'] );

				}

				// Check for current day condition.
				if ( ! is_array( $single_bump_array['mwb_upsell_bump_schedule'] ) ) {

					continue;
				}

				// Got an array. Now check.
				if( ! in_array( '0', $single_bump_array['mwb_upsell_bump_schedule'] ) && ! in_array( date( 'N' ), $single_bump_array['mwb_upsell_bump_schedule'] ) ) {

					continue;
				}

				// WIW-CC : Comment - Don't check target products and categories as we always have to show the offer.
				// Check if target products or target categories are empty.
				$single_bump_target_ids = ! empty( $single_bump_array['mwb_upsell_bump_target_ids'] ) ? $single_bump_array['mwb_upsell_bump_target_ids'] : array();

				$single_bump_categories = ! empty( $single_bump_array['mwb_upsell_bump_target_categories'] ) ? $single_bump_array['mwb_upsell_bump_target_categories'] : array();

				// When both target products or target categories are empty, continue.
				if ( empty( $single_bump_target_ids ) && empty( $single_bump_categories ) ) {

					continue;

				}

				// Lets check for offer be present.
				if ( ! empty( $single_bump_array['mwb_upsell_bump_products_in_offer'] ) ) {

					/**
					 * After v1.0.1 (pro)
					 * Apply smart-skip in case of pro is active.
					 */
					if ( mwb_ubo_lite_if_pro_exists() && is_user_logged_in() ) {

						$mwb_upsell_bump_global_smart_skip = ! empty( $mwb_ubo_global_options['mwb_ubo_offer_purchased_earlier'] ) ? $mwb_ubo_global_options['mwb_ubo_offer_purchased_earlier'] : '';
						if ( 'yes' == $mwb_upsell_bump_global_smart_skip && class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) ) {

							if ( method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'mwb_ubo_skip_for_pre_order' ) && Upsell_Order_Bump_Offer_For_Woocommerce_Pro::mwb_ubo_skip_for_pre_order( $single_bump_array['mwb_upsell_bump_products_in_offer'] ) ) {

								continue;
							}
						}
					}

					/**
					 * MWB Fix :: for mutliple order bump for categories.
					 */
					if( ! empty( $encountered_bump_array ) ) {
						$encountered_bump_array = 0;
					}

					// If  target category is present.
					if( ! empty( $single_bump_array['mwb_upsell_bump_target_ids'] ) ) :

						// Check if these product are present in cart one by one.
						foreach ( $single_bump_array['mwb_upsell_bump_target_ids'] as $key => $single_target_id ) {

							// Check if present in cart.
							$mwb_upsell_bump_target_key = mwb_ubo_lite_check_if_in_cart( $single_target_id );

							// If we product is present we get the cart key.
							if ( ! empty( $mwb_upsell_bump_target_key ) ) {

								// Check offer product must be in stock.
								$offer_product = wc_get_product( $single_bump_array['mwb_upsell_bump_products_in_offer'] );

								if ( empty( $offer_product ) ) {

									continue;
								}

								if ( 'publish' != $offer_product->get_status() ) {

									continue;
								}

								if ( ! $offer_product->is_in_stock() ) {

									continue;
								}

								// Check if offer product is already in cart.
								if ( mwb_ubo_lite_already_in_cart( $single_bump_array['mwb_upsell_bump_products_in_offer'] ) && 'yes' == $mwb_upsell_bump_global_skip_settings ) {

									continue;
								}
								// If everything is good just break !!
								$encountered_bump_array = $single_bump_id;

								// Push the data on same index.
								array_push( $encountered_bump_key_array, $encountered_bump_array );
								array_push( $encountered_target_key_array, $mwb_upsell_bump_target_key );
								break;
							}

						} 

					endif;

					// 2nd foreach end for product id.

					// If target key is still empty means no target category is found yet.
					if ( empty( $encountered_bump_array ) && ! empty( $single_bump_array['mwb_upsell_bump_target_categories'] ) && is_array( $single_bump_array['mwb_upsell_bump_target_categories'] ) ) {

						foreach ( $single_bump_array['mwb_upsell_bump_target_categories'] as $key => $single_category_id ) {

							// No target Id is found go for category,
							// Check if the category is in cart.
							$mwb_upsell_bump_target_key = mwb_ubo_lite_check_category_in_cart( $single_category_id );

							// If we product is present we get the cart key.
							if ( ! empty( $mwb_upsell_bump_target_key ) ) {

								// Check offer product must be in stock.
								$offer_product = wc_get_product( $single_bump_array['mwb_upsell_bump_products_in_offer'] );

								if ( empty( $offer_product ) ) {

									continue;
								}

								if ( 'publish' != $offer_product->get_status() ) {

									continue;
								}

								if ( ! $offer_product->is_in_stock() ) {

									continue;
								}

								// Check if offer product is already in cart.
								if ( mwb_ubo_lite_already_in_cart( $single_bump_array['mwb_upsell_bump_products_in_offer'] ) && 'yes' == $mwb_upsell_bump_global_skip_settings ) {

									continue;

								}

								// If everything is good just break !!
								$encountered_bump_array = $single_bump_id;

								// Push the data on same index.
								array_push( $encountered_bump_key_array, $encountered_bump_array );
								array_push( $encountered_target_key_array, $mwb_upsell_bump_target_key );
								break;
							}
						} // Second foreach for category search end.
					}
					
				} else {

					// If offer product is not saved, continue.
					continue;
				}

			} // First foreach end.
			
		} // Condition check for bump limit.

		if ( ! empty( $encountered_bump_key_array ) && ! empty( $encountered_target_key_array ) ) {

			$result = array(
				'encountered_bump_array' => $encountered_bump_key_array, // Order Bump IDs to be shown.
				'mwb_upsell_bump_target_key' => $encountered_target_key_array,
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
		if( ! function_exists( 'WC' ) || empty( WC()->session ) ) {

			return;
		}

		if ( 'true' == WC()->session->get( 'encountered_bump_array_display' ) ) {

			// Cost calculations only when the offer is added.
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'woocommerce_custom_price_to_cart_item' ) );

			// Disable quantity field at cart page.
			add_filter( 'woocommerce_cart_item_quantity', array( $this, 'disable_quantity_bump_product_in_cart' ), 10, 2 );

			// For Aerocheckout pages.
			add_filter( 'wfacp_show_item_quantity', array( $this, 'disable_quantity_field_in_aerocheckout' ), 10, 2 );

			add_filter( 'wfacp_show_undo_message_for_item', array( $this, 'hide_undo_notice_in_aerocheckout', 10, 2 ) );

			// Removing offer or target product manually by cart.
			add_action( 'woocommerce_remove_cart_item', array( $this, 'after_remove_product' ), 10, 2 );

			// When the cart item product is actually removed.
			add_action( 'woocommerce_cart_item_removed', array( $this, 'after_product_removed_from_cart' ), 10, 2 );

			// Add meta data to order item for order review.
			add_action( 'woocommerce_checkout_create_order', array( $this, 'add_order_item_meta' ), 10 );

			// Reset custom session data.
			add_action( 'woocommerce_thankyou', array( $this, 'reset_session_variable' ), 10 );
		}
	}

// End of class.
}

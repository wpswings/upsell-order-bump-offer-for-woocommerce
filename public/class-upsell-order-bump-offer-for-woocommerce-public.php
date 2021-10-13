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
		wp_enqueue_script( 'mwb-ubo-lite-public-script', plugin_dir_url( __FILE__ ) . 'js/mwb_ubo_lite_public_script.js', array( 'jquery' ), $this->version, false );

		wp_localize_script(
			'mwb-ubo-lite-public-script',
			'mwb_ubo_lite_public',
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
		check_ajax_referer( 'mwb_ubo_lite_nonce', 'nonce' );

		// The id of the offer to be added.
		$bump_product_id                    = ! empty( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
		$bump_discounted_price              = ! empty( $_POST['discount'] ) ? sanitize_text_field( wp_unslash( $_POST['discount'] ) ) : '';
		$bump_index                         = ! empty( $_POST['bump_index'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_index'] ) ) : '';
		$bump_target_cart_key               = ! empty( $_POST['bump_target_cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_target_cart_key'] ) ) : '';
		$order_bump_id                      = ! empty( $_POST['order_bump_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_bump_id'] ) ) : '';
		$smart_offer_upgrade                = ! empty( $_POST['smart_offer_upgrade'] ) ? sanitize_text_field( wp_unslash( $_POST['smart_offer_upgrade'] ) ) : '';
		$custom_form_toggle                 = ! empty( $_POST['custom_form_toggle'] ) ? sanitize_text_field( wp_unslash( $_POST['custom_form_toggle'] ) ) : '';
		$global_settings_custom_form_toggle = ! empty( $_POST['global_settings_custom_form_toggle'] ) ? sanitize_text_field( wp_unslash( $_POST['global_settings_custom_form_toggle'] ) ) : '';

		if ( 'none+none' == $bump_discounted_price ) {
			$bump_discounted_price = '0+%';
		}
		$cart_item_data        = array(
			'mwb_ubo_offer_product' => true,
			'mwb_ubo_offer_index'   => $bump_index,
			'mwb_ubo_bump_id'       => $order_bump_id,
			'mwb_discounted_price'  => $bump_discounted_price,
			'mwb_ubo_target_key'    => $bump_target_cart_key,
			'flag_' . uniqid()      => true,
		);

		$_product = wc_get_product( $bump_product_id );

		$added = 'added';

		if ( mwb_ubo_lite_reload_required_after_adding_offer( $_product ) ) {

			$added = 'subs_reload';
		}

		// Set the session for the id of bump product.
		// This session will mainly be used in coupon_restriction_for_bump on checkout page.
		if ( null == WC()->session->get( 'restrict_coupon_on_bump_target' . $order_bump_id ) ) {
			WC()->session->set( 'restrict_coupon_on_bump_target' . $order_bump_id, $bump_product_id );
		}

		if ( ! empty( $_product ) && $_product->has_child() ) {
			// Generate default price html.
			$bump_price_html = mwb_ubo_lite_custom_price_html( $bump_product_id, $bump_discounted_price );

			$response = array(
				'key'     => 'true',
				'message' => $bump_price_html,
			);

			// Now we have to add a pop up.
			echo json_encode( $response );

		} elseif ( ! empty( $_product ) ) {

			// If simple product or any single variations.

			// This will run in two cases
			// First is when the pro version is not active or it is active but the fields are not set.
			if ( ! mwb_ubo_lite_if_pro_exists() || ( mwb_ubo_lite_if_pro_exists() && ( 'hide' == $custom_form_toggle ) && ( 'yes' == $global_settings_custom_form_toggle ) ) || ( mwb_ubo_lite_if_pro_exists() && ( 'hide' == $custom_form_toggle ) && ( 'no' == $global_settings_custom_form_toggle ) ) || ( mwb_ubo_lite_if_pro_exists() && ( ( 'show' == $custom_form_toggle ) ) && ( 'no' == $global_settings_custom_form_toggle ) ) ) {
				$bump_offer_cart_item_key = WC()->cart->add_to_cart( $bump_product_id, $quantity = 1, $variation_id = 0, $variation = array(), $cart_item_data );
				// Add Order Bump Offer Accept Count for the respective Order Bump.
				$sales_by_bump = new Mwb_Upsell_Order_Bump_Report_Sales_By_Bump( $order_bump_id );
				$sales_by_bump->add_offer_accept_count();
				WC()->session->set( 'bump_offer_status', 'added' );
				WC()->session->set( "bump_offer_status_$bump_index", $bump_offer_cart_item_key );
			}

			// Smart offer Upgrade.
			// When the custom fields will be off but pro will be on , the ajax to add in cart will be run from here
			// Therefore we have to check three conditions here.
			if ( ( mwb_ubo_lite_if_pro_exists() && ( 'yes' == $smart_offer_upgrade ) ) ) {

				// Get all saved bumps.
				$mwb_ubo_bump_callback          = Upsell_Order_Bump_Offer_For_Woocommerce::$mwb_upsell_bump_list_callback_function; // Checks the validity of the license of the plugin.
				$mwb_ubo_offer_array_collection = Upsell_Order_Bump_Offer_For_Woocommerce::$mwb_ubo_bump_callback();

				$encountered_bump_array = ! empty( $mwb_ubo_offer_array_collection[ $order_bump_id ] ) ? $mwb_ubo_offer_array_collection[ $order_bump_id ] : array();

				$mwb_upsell_bump_replace_target = ! empty( $encountered_bump_array['mwb_ubo_offer_replace_target'] ) ? $encountered_bump_array['mwb_ubo_offer_replace_target'] : '';

				if ( 'yes' == $mwb_upsell_bump_replace_target && class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) && method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'mwb_ubo_upgrade_offer' ) ) {
					Upsell_Order_Bump_Offer_For_Woocommerce_Pro::mwb_ubo_upgrade_offer( $bump_offer_cart_item_key, $bump_target_cart_key );
				}
			}
			// BACKUP.
			echo json_encode( $added );
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

		$bump_index    = ! empty( $_POST['bump_index'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_index'] ) ) : '';
		$order_bump_id = ! empty( $_POST['order_bump_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_bump_id'] ) ) : '';

		if ( null != WC()->session->get( "bump_offer_status_$bump_index" ) ) {

			WC()->cart->remove_cart_item( WC()->session->get( "bump_offer_status_$bump_index" ) );
		}

		WC()->session->__unset( "bump_offer_status_$bump_index" );

		// Unset the session, when the checkbox is unchecked.
		WC()->session->__unset( 'restrict_coupon_on_bump_target' . $order_bump_id );

		echo json_encode( 'removed' );

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
			$bump_var_image = mwb_ubo_lite_get_bump_image( $bump_offer_id );
		}

		// Variation id will be empty if selected variation is not available.
		if ( empty( $variation_id ) || empty( $selected_variation_product ) ) {

			$response = array(
				'key'     => 'not_available',
				'message' => '<p class="stock out-of-stock">' . esc_html__( 'Sorry, this variation is not available.', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>',
				'image'   => $bump_var_image,
			);
			echo json_encode( $response );

		} else {

			// Check if in stock?
			if ( ! $selected_variation_product->is_in_stock() ) {

				// Out of stock.
				$response = array(

					'key'     => 'stock',
					'message' => '<p class="stock out-of-stock">' . esc_html__( 'Out of stock.', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>',
					'image'   => $bump_var_image,
				);

				echo json_encode( $response );

			} else {

				$response = array(
					'key'     => $variation_id,
					'message' => mwb_ubo_lite_custom_price_html( $variation_id, $bump_offer_discount ),
					'image'   => $bump_var_image,
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

		if ( 'none+none' == $bump_offer_discount ) {
			$bump_offer_discount = '0+%';
		}

		// Contains target cart key.
		$bump_target_cart_key = ! empty( $_POST['bump_target_cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_target_cart_key'] ) ) : '';
		$bump_index           = ! empty( $_POST['bump_index'] ) || '0' == $_POST['bump_index'] ? sanitize_text_field( wp_unslash( $_POST['bump_index'] ) ) : '';
		$order_bump_id        = ! empty( $_POST['order_bump_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_bump_id'] ) ) : '';
		$smart_offer_upgrade  = ! empty( $_POST['smart_offer_upgrade'] ) ? sanitize_text_field( wp_unslash( $_POST['smart_offer_upgrade'] ) ) : '';
		$values               = ! empty( $_POST['all_values'] ) ? $_POST['all_values'] : '';
		$custom_fields_add    = ! empty( $_POST['custom_fields_add'] ) ? sanitize_text_field( wp_unslash( $_POST['custom_fields_add'] ) ) : '';

		// Now safe to add to cart.
		$cart_item_data = array(
			'mwb_ubo_offer_product' => true,
			'mwb_discounted_price'  => $bump_offer_discount,
			'flag_' . uniqid()      => true,
			'mwb_ubo_offer_index'   => 'index_' . $bump_index,
			'mwb_ubo_bump_id'       => $order_bump_id,
			'mwb_ubo_target_key'    => $bump_target_cart_key,
		);

		// Check for pro and check whether to show the custom fields or not.
		$check_for_pro      = ! empty( $_POST['check_for_pro'] ) ? sanitize_text_field( wp_unslash( $_POST['check_for_pro'] ) ) : '';
		$custom_form_toggle = ! empty( $_POST['custom_form_toggle'] ) ? sanitize_text_field( wp_unslash( $_POST['custom_form_toggle'] ) ) : '';
		$_product           = wc_get_product( $variation_id );

		$added = 'added';

		if ( mwb_ubo_lite_reload_required_after_adding_offer( $_product ) ) {

			$added = 'subs_reload';
		}

		$bump_offer_cart_item_key = WC()->cart->add_to_cart( $variation_parent_id, $quantity = '1', $variation_id, $variation = array(), $cart_item_data );

		// Add Order Bump Offer Accept Count for the respective Order Bump.
		$sales_by_bump = new Mwb_Upsell_Order_Bump_Report_Sales_By_Bump( $order_bump_id );
		$sales_by_bump->add_offer_accept_count();

		WC()->session->set( "bump_offer_status_index_$bump_index", $bump_offer_cart_item_key );

		WC()->session->set( 'bump_offer_status', 'added' );

		// Smart offer Upgrade.
		if ( mwb_ubo_lite_if_pro_exists() && 'yes' == $smart_offer_upgrade ) {

			// Get all saved bumps.
			$mwb_ubo_bump_callback          = Upsell_Order_Bump_Offer_For_Woocommerce::$mwb_upsell_bump_list_callback_function;
			$mwb_ubo_offer_array_collection = Upsell_Order_Bump_Offer_For_Woocommerce::$mwb_ubo_bump_callback();
			$encountered_bump_array         = ! empty( $mwb_ubo_offer_array_collection[ $order_bump_id ] ) ? $mwb_ubo_offer_array_collection[ $order_bump_id ] : array();
			$mwb_upsell_bump_replace_target = ! empty( $encountered_bump_array['mwb_ubo_offer_replace_target'] ) ? $encountered_bump_array['mwb_ubo_offer_replace_target'] : '';

			if ( 'yes' == $mwb_upsell_bump_replace_target && class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) && method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'mwb_ubo_upgrade_offer' ) ) {

				Upsell_Order_Bump_Offer_For_Woocommerce_Pro::mwb_ubo_upgrade_offer( $bump_offer_cart_item_key, $bump_target_cart_key );
			}
		}

		// Set the custom form values in the option table and use them on thankyou page hook to set metadata.

		$order_complete_show_custom_values = get_option( 'order_complete_show_custom_values', false );
		if ( false == $order_complete_show_custom_values ) {
			$order_complete_show_custom_values = array();
		}

		if ( '' != $values ) {
			$order_complete_show_custom_values[ $variation_parent_id ] = $values;
			update_option( 'order_complete_show_custom_values', $order_complete_show_custom_values );
		}

		echo( json_encode( $added ) );
		wp_die();
	}

	/**
	 * Upload the values from custom fields into the item meta database for simple products.
	 *
	 * @return void
	 */
	public function add_simple_offer_in_cart() {
		check_ajax_referer( 'mwb_ubo_lite_nonce', 'nonce' );

		// The id of the offer to be added.
		$bump_product_id       = ! empty( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
		$bump_discounted_price = ! empty( $_POST['discount'] ) ? sanitize_text_field( wp_unslash( $_POST['discount'] ) ) : '';
		$bump_index            = ! empty( $_POST['bump_index'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_index'] ) ) : '';
		$bump_target_cart_key  = ! empty( $_POST['bump_target_cart_key'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_target_cart_key'] ) ) : '';
		$order_bump_id         = ! empty( $_POST['order_bump_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_bump_id'] ) ) : '';
		$smart_offer_upgrade   = ! empty( $_POST['smart_offer_upgrade'] ) ? sanitize_text_field( wp_unslash( $_POST['smart_offer_upgrade'] ) ) : '';
		// $custom_fields_add  = ! empty( $_POST['custom_fields_add'] ) ? sanitize_text_field( wp_unslash( $_POST['custom_fields_add'] ) ) : '';
		if ( 'none+none' == $bump_discounted_price ) {
			$bump_discounted_price = '0+%';
		}
		$cart_item_data        = array(
			'mwb_ubo_offer_product' => true,
			'mwb_ubo_offer_index'   => $bump_index,
			'mwb_ubo_bump_id'       => $order_bump_id,
			'mwb_discounted_price'  => $bump_discounted_price,
			'mwb_ubo_target_key'    => $bump_target_cart_key,
			'flag_' . uniqid()      => true,
		);

		$_product = wc_get_product( $bump_product_id );

		$added = 'added';

		if ( mwb_ubo_lite_reload_required_after_adding_offer( $_product ) ) {

			$added = 'subs_reload';
		}

		$bump_offer_cart_item_key = WC()->cart->add_to_cart( $bump_product_id, $quantity = 1, $variation_id = 0, $variation = array(), $cart_item_data );

		// Add Order Bump Offer Accept Count for the respective Order Bump.
		$sales_by_bump = new Mwb_Upsell_Order_Bump_Report_Sales_By_Bump( $order_bump_id );
		$sales_by_bump->add_offer_accept_count();

		WC()->session->set( 'bump_offer_status', 'added' );
		WC()->session->set( "bump_offer_status_$bump_index", $bump_offer_cart_item_key );

		// Smart offer Upgrade.
		if ( mwb_ubo_lite_if_pro_exists() && 'yes' === $smart_offer_upgrade ) {
			$mwb_ubo_bump_callback          = Upsell_Order_Bump_Offer_For_Woocommerce::$mwb_upsell_bump_list_callback_function; // Checks the validity of the license of the plugin.
			$mwb_ubo_offer_array_collection = Upsell_Order_Bump_Offer_For_Woocommerce::$mwb_ubo_bump_callback();

			$encountered_bump_array = ! empty( $mwb_ubo_offer_array_collection[ $order_bump_id ] ) ? $mwb_ubo_offer_array_collection[ $order_bump_id ] : array();
			$mwb_upsell_bump_replace_target = ! empty( $encountered_bump_array['mwb_ubo_offer_replace_target'] ) ? $encountered_bump_array['mwb_ubo_offer_replace_target'] : '';
			if ( 'yes' == $mwb_upsell_bump_replace_target && class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) && method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'mwb_ubo_upgrade_offer' ) ) {

				Upsell_Order_Bump_Offer_For_Woocommerce_Pro::mwb_ubo_upgrade_offer( $bump_offer_cart_item_key, $bump_target_cart_key );
			}
		}

		// ADD CUSTOM FORM FIELD VALUES TO OPTION TABLE.

		$all_values                        = ! ( empty( $_POST['all_values'] ) ) ? $_POST['all_values'] : '';
		$order_complete_show_custom_values = get_option( 'order_complete_show_custom_values', false );

		if ( false == $order_complete_show_custom_values ) {
			$order_complete_show_custom_values = array();
		}
		$order_complete_show_custom_values[ $bump_product_id ] = $all_values;
		update_option( 'order_complete_show_custom_values', $order_complete_show_custom_values );

		echo json_encode( $added );
		wp_die();
	}

	/**
	 * Function to get the order id in the function so that we can update the item meta succesfully.
	 * This function is used to transfer the details entered in the popup into the item meta.
	 */
	public function temporary_function_to_get_order_id( $order_id ) {

		$order = wc_get_order( $order_id );
		$opt   = get_option( 'order_complete_show_custom_values', false );

		if ( ( false == $opt ) || ( '' == $opt ) ) {
			return;
		} else {
			foreach ( $order->get_items() as $item_id => $item ) {
				$product_id   = $item->get_product_id();
				$variation_id = $item->get_variation_id();

				foreach ( $opt as $key => $value ) {
					if ( $key == $product_id ) {
						foreach ( $opt[ $key ] as $key1 => $value1 ) {
							wc_update_order_item_meta( $item_id, $value1['n'], $value1['v'] );
						}
					}
				}
			}
			delete_option( 'order_complete_show_custom_values' );
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

			if ( ! empty( $cart_item['mwb_ubo_offer_product'] ) ) {

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
		if ( ! empty( $current_cart_item['mwb_ubo_offer_product'] ) ) {

			// Hide Undo notice for Offer Products.
			add_filter( 'woocommerce_cart_item_removed_notice_type', '__return_null' );

			$bump_index = ! empty( $current_cart_item['mwb_ubo_offer_index'] ) ? $current_cart_item['mwb_ubo_offer_index'] : '';
			$bump_id = ! empty( $current_cart_item['mwb_ubo_bump_id'] ) ? $current_cart_item['mwb_ubo_bump_id'] : '';

			// Add Order Bump Offer Remove Count for the respective Order Bump.
			$sales_by_bump = new Mwb_Upsell_Order_Bump_Report_Sales_By_Bump( $bump_id );
			$sales_by_bump->add_offer_remove_count();

			// When the removed product is a Smart Offer Upgrade - Offer product.
			if ( ! empty( $current_cart_item['mwb_ubo_sou_offer'] ) && ! empty( $current_cart_item['mwb_ubo_target_key'] ) ) {

				// Restore Target product.
				if ( class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) && method_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro', 'mwb_ubo_retrieve_target' ) ) {

					Upsell_Order_Bump_Offer_For_Woocommerce_Pro::mwb_ubo_retrieve_target( $current_cart_item['mwb_ubo_target_key'] );
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
			// unset( WC()->cart->cart_contents[ $key_to_be_removed ]['mwb_ubo_offer_product'] );
			// unset( WC()->cart->cart_contents[ $key_to_be_removed ]['mwb_ubo_offer_index'] );
			// unset( WC()->cart->cart_contents[ $key_to_be_removed ]['mwb_discounted_price'] );
			// unset( WC()->cart->cart_contents[ $key_to_be_removed ]['mwb_ubo_target_key'] );

			WC()->session->__unset( 'bump_offer_status_' . $bump_index );

			// As offer product is removed so no need remove encountered session to refetch order bumps.
			return;

		}

		// When the removed product is a Smart Offer Upgrade - Target product.
		elseif ( ! empty( $current_cart_item['mwb_ubo_sou_target'] ) ) {

			// Do nothing.
			return;
		}

		// When the removed product is a Normal or Target product.
		elseif ( ! empty( $cart_object->cart_contents ) && is_array( $cart_object->cart_contents ) ) {

			// Global settings.
			$mwb_ubo_global_options = get_option( 'mwb_ubo_global_options', mwb_ubo_lite_default_global_options() );

			foreach ( $cart_object->cart_contents as $cart_offer_item_key => $cart_offer_item ) {

				// Check Offer product and Target keys.
				if ( ! empty( $cart_offer_item['mwb_ubo_offer_product'] ) && ! empty( $cart_offer_item['mwb_ubo_target_key'] ) ) {

					// When Target key matches means Removed product is a Target product.
					if ( $cart_offer_item['mwb_ubo_target_key'] == $key_to_be_removed ) {

						// If the same target key is found in order cart item, Handle offer product too.
						$bump_index    = ! empty( $cart_offer_item['mwb_ubo_offer_index'] ) ? $cart_offer_item['mwb_ubo_offer_index'] : '';
						$bump_id       = ! empty( $cart_offer_item['mwb_ubo_bump_id'] ) ? $cart_offer_item['mwb_ubo_bump_id'] : '';
						$sales_by_bump = new Mwb_Upsell_Order_Bump_Report_Sales_By_Bump( $bump_id );

						// When Target dependency is set to Remove Offer product.
						if ( ! empty( $mwb_ubo_global_options['mwb_ubo_offer_removal'] ) && 'yes' == $mwb_ubo_global_options['mwb_ubo_offer_removal'] ) {

							/**
							 * Remove Target dependent Offer product.
							 * Unset order bump params from WC cart and index session for the dependent offer product.
							 * Do not unset other session variables.
							 */
							if ( ! empty( $cart_offer_item_key ) ) {

								// Unset order bump params from WC cart to prevent Offer rollback on undo.
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_ubo_offer_product'] );
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_ubo_offer_index'] );
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_ubo_bump_id'] );
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_discounted_price'] );
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_ubo_target_key'] );

								// Remove the Offer product.
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ] );

								// Add Order Bump Offer Remove Count for the respective Order Bump.
								$sales_by_bump->add_offer_remove_count();

								WC()->session->__unset( 'bump_offer_status_' . $bump_index );
							}
						}

						// When Target dependency is set to Keep Offer product.
						else {

							/**
							 * Convert Target dependent Offer product into Normal product.
							 * Unset order bump params from WC cart and index session for the dependent offer product.
							 * Do not unset other session variables.
							 */
							if ( ! empty( $cart_offer_item_key ) ) {

								// Convert Offer product to normal product.
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_ubo_offer_product'] );
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_ubo_offer_index'] );
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_ubo_bump_id'] );
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_discounted_price'] );
								unset( WC()->cart->cart_contents[ $cart_offer_item_key ]['mwb_ubo_target_key'] );

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
		mwb_ubo_destroy_encountered_session();
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
	 * Function to restrict the use of coupons on the bump offer product.
	 * COUPON RESTRICTION Function.
	 */
	public function woocommerce_custom_price_to_cart_item_remove_coupons( $cart_object ) {

		global $woocommerce;
		$cart = WC()->cart;
		$mwb_ubo_bump_list = get_option( 'mwb_ubo_bump_list', false );

		// Get all live Order Bumps.
		$live_order_bumps  = array();
		if ( false != $mwb_ubo_bump_list ) {
			foreach ( $mwb_ubo_bump_list as $k => $v ) {
				if ( isset( $mwb_ubo_bump_list[ $k ]['mwb_upsell_bump_status'] ) && $mwb_ubo_bump_list[ $k ]['mwb_upsell_bump_status'] == 'yes' ) {
					// Send the ID of the Order Bumps who are live and for those IDs check on the checkout page if any session is set.
					array_push( $live_order_bumps, $k );
				}
			}
		}

		// Get subtotal of cart without any bump offer and subtotal of prices of all bump offers applied.
		$added_bumps_product_id = array();
		$subtotal_without_bump  = 0;
		$price_of_bump_offer    = 0;
		if ( ! empty( $live_order_bumps ) ) {
			foreach ( $live_order_bumps as $i => $j ) {
				if ( WC()->session->__isset( 'restrict_coupon_on_bump_target' . $j ) ) {
					$bump_offer_product_id = WC()->session->get( 'restrict_coupon_on_bump_target' . $j );
					array_push( $added_bumps_product_id, $bump_offer_product_id );
				}
			}
		}

		$subtotal_with_bumps             = 0;
		$subtotal_without_bumps          = 0;
		$number_of_bump_products_in_cart = count( $added_bumps_product_id );
		foreach ( $added_bumps_product_id as $k => $v ) {
			foreach ( $cart_object->cart_contents as $key => $value ) {
				if ( $value['product_id'] == $v ) {
					$subtotal_with_bumps += $value['data']->get_price();
					break;
				}
			}
		}

		$subtotal_without_bump = $woocommerce->cart->subtotal - $subtotal_with_bumps;

		if ( ( ! empty( WC()->cart->get_applied_coupons() ) ) ) {
			$applied_coupons     = WC()->cart->get_applied_coupons();
			$price_of_bump_offer = 0;

			if ( empty( $applied_coupons ) ) {
				return;
			} else {
				foreach ( $applied_coupons as $k => $v ) {
					$c = new WC_Coupon( $v );
					// In case coupon is percentage coupon.
					if ( $c->get_discount_type() == 'percent' ) {
						$amount                = $c->get_amount();
						$subtotal_without_bump = $subtotal_without_bump - ( ( $amount / 100 ) * $subtotal_without_bump );
					}
					// In case coupon is fixed price on the cart.
					if ( $c->get_discount_type() == 'fixed_cart' ) {
						$amount                = $c->get_amount();
						$subtotal_without_bump = $subtotal_without_bump - $amount;
					}
					// In case coupon is fixed price on particular products on.
					if ( $c->get_discount_type() == 'fixed_product' ) {
						$amount                                       = $c->get_amount();
						$cart_contents_number                         = WC()->cart->get_cart_contents_count();
						$cart_contents_number_excluding_bump_products = $cart_contents_number - $number_of_bump_products_in_cart;
						$subtotal_without_bump                        = $subtotal_without_bump - ( $cart_contents_number_excluding_bump_products * $amount );
					}
				}
			}
			$subtotal              = $subtotal_without_bump + $subtotal_with_bumps;
			// $cart_object->subtotal = $cart_object->discount_total;
			$cart->set_discount_total( $woocommerce->cart->subtotal - $subtotal );
			$this->change_the_total_value_after_coupons_applied_set_session( $subtotal );
		}
	}

	/**
	 * Since the above function woocommerce_custom_price_to_cart_item_remove_coupons can not change the total value in the
	 * checkout page we have to use a workaround method.
	 *
	 * @param name $subtotal This has the subtotal of cart, now we will set the session and pass the value to
	 * change_the_total_value_after_coupons_applied_CHANGE_TOTAL function.
	 *
	 * @return void
	 */
	public function change_the_total_value_after_coupons_applied_set_session( $subtotal ) {
		WC()->session->set( 'Total_value_after_coupon_deduction', $subtotal );
		add_filter( 'woocommerce_calculated_total', array( $this, 'change_the_total_value_after_coupons_applied_CHANGE_TOTAL' ), 30, 2 );
	}


	/**
	 * The change_the_total_value_after_coupons_applied_set_session function will set session and call the right hook so that
	 * we can change the price effectively in this function below.
	 *
	 * @return $total
	 */
	public function change_the_total_value_after_coupons_applied_CHANGE_TOTAL( $total, $cart ) {
		if ( null == WC()->session->get( 'Total_value_after_coupon_deduction' ) ) {
			print_r( 'session is null' );
			wp_die();
		} else {
			$final_total = WC()->session->get( 'Total_value_after_coupon_deduction' );
			$total       = $final_total;
			WC()->session->__unset( 'Total_value_after_coupon_deduction' );
			return $total;
		}
	}

	/**
	 * Manipulate the prices on the applied coupons HTML whcih is shown on the checkout page when any coupon is applied to cart.
	 *
	 * @param [type] $coupon_html           This is the HTML Object shown on the checkout page.
	 * @param [type] $coupon                The coupon object will give us all the properties of the coupons being applied.
	 * @param [type] $discount_amount_html  The HTML (Manupulated) Object shown on the checkout page.
	 * @return $coupon_html
	 */
	public function custom_cart_totals_coupon_html( $coupon_html, $coupon, $discount_amount_html ) {

		global $woocommerce;
		$mwb_ubo_bump_list = get_option( 'mwb_ubo_bump_list', false );

		// Get all live Order Bumps.
		$live_order_bumps  = array();
		if ( false != $mwb_ubo_bump_list ) {
			foreach ( $mwb_ubo_bump_list as $k => $v ) {
				if ( isset( $mwb_ubo_bump_list[ $k ]['mwb_upsell_bump_status'] ) && $mwb_ubo_bump_list[ $k ]['mwb_upsell_bump_status'] == 'yes' ) {
					// Send the ID of the Order Bumps who are live and for those IDs check on the checkout page if any session is set.
					array_push( $live_order_bumps, $k );
				}
			}
		}

		if ( empty( $live_order_bumps ) ) {
			return $coupon_html;
		}

		// From live bumps check which ones are added in the cart and get an array of their product IDs.
		$added_bumps_product_id = array();
		$subtotal_without_bump  = 0;
		$price_of_bump_offer    = 0;
		if ( ! empty( $live_order_bumps ) ) {
			foreach ( $live_order_bumps as $i => $j ) {
				if ( WC()->session->__isset( 'restrict_coupon_on_bump_target' . $j ) ) {
					$bump_offer_product_id = WC()->session->get( 'restrict_coupon_on_bump_target' . $j );
					array_push( $added_bumps_product_id, $bump_offer_product_id );
				}
			}
		}
		// print_r( $added_bumps_product_id );

		if ( empty( $added_bumps_product_id ) ) {
			return $coupon_html;
		}

		$subtotal_with_bumps             = 0;
		$subtotal_without_bumps          = 0;
		$number_of_bump_products_in_cart = count( $added_bumps_product_id );
		foreach ( $added_bumps_product_id as $k => $v ) {
			foreach ( WC()->cart->get_cart() as $key => $value ) {
				if ( $value['product_id'] == $v ) {
					$subtotal_with_bumps += $value['data']->get_price();
					break;
				}
			}
		}

		$subtotal_without_bump = $woocommerce->cart->subtotal - $subtotal_with_bumps;

		if ( ! empty( WC()->cart->get_applied_coupons() ) ) {

			if ( 'percent' == $coupon->get_discount_type() ) {
				$percent               = $coupon->get_amount();
				$res                   = ( $percent / 100 ) * $subtotal_without_bump;
				$subtotal_without_bump = $subtotal_without_bump - $res;
				$discount_amount_html  = '<span>-' . wc_price( $res ) . '</span>';
				$coupon_html           = $discount_amount_html . ' <a href="' . esc_url( add_query_arg( 'remove_coupon', urlencode( $coupon->get_code() ), defined( 'WOOCOMMERCE_CHECKOUT' ) ? wc_get_checkout_url() : wc_get_cart_url() ) ) . '" class="woocommerce-remove-coupon" data-coupon="' . esc_attr( $coupon->get_code() ) . '">' . __( '[Remove]', 'woocommerce' ) . '</a>';
			}

			if ( 'fixed_product' == $coupon->get_discount_type() ) {
				$fixed_product                                = $coupon->get_amount();
				$cart_contents_number                         = WC()->cart->get_cart_contents_count();
				$cart_contents_number_excluding_bump_products = $cart_contents_number - $number_of_bump_products_in_cart;
				$subtotal_without_bump                        = $subtotal_without_bump - ( $cart_contents_number_excluding_bump_products * $fixed_product );
				$total_discount                               = $fixed_product * $cart_contents_number_excluding_bump_products;
				$discount_amount_html                         = '<span>-' . wc_price( $total_discount ) . '</span>';
				$coupon_html                                  = $discount_amount_html . ' <a href="' . esc_url( add_query_arg( 'remove_coupon', urlencode( $coupon->get_code() ), defined( 'WOOCOMMERCE_CHECKOUT' ) ? wc_get_checkout_url() : wc_get_cart_url() ) ) . '" class="woocommerce-remove-coupon" data-coupon="' . esc_attr( $coupon->get_code() ) . '">' . __( '[Remove]', 'woocommerce' ) . '</a>';
			}
		}
		return $coupon_html;
	}

	/**
	 * Function to unset the session if on checkout page the bump was unchecked.
	 *
	 * @return void
	 */
	public function unset_session_if_bump_unchecked() {
		// If the session was not set , return.
		if ( null == WC()->session->get( 'restrict_coupon_on_bump_target' ) ) {
			return;
		}
		// If the session is set , unset it.
		WC()->session->__unset( 'restrict_coupon_on_bump_target' );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function lets_see() {
		$total_contents        = WC()->cart->cart_contents_count;
		$discount              = WC()->cart->discount_total;
		$bump_offer_product_id = 20;
		$cart                  = WC()->cart->get_cart();
		$applied_coupons       = WC()->cart->get_applied_coupons();
		$total_of_cart         = floatval( preg_replace( '#[^\d.]#', '', WC()->cart->get_cart_total() ) );
		foreach ( $cart as $item => $values ) {
			$products_ids_array[] = $values['product_id'];
			$price[]              = get_post_meta( $values['product_id'], '_price', true );
		}
		// Now we have two arrays one with product id and one with all prices.
		// Bump id and its price will be at same index but in different arrays.
		// Also the price showing for the bump is not the discounted one, but the original.
		if ( ! empty( $products_ids_array ) ) {
			$price_of_bump_offer = $total_of_cart;
			foreach ( $products_ids_array as $k => $v ) {
				// If any id in this array = id of bump offer that means we have offer product in cart.
				if ( $v != $bump_offer_product_id ) {
					$temp                = $price_of_bump_offer - floatval( $price[ $k ] );
					$price_of_bump_offer = $temp;
				}
			}
			// $price_of_bump_offer will now have the price of bump offer product.
			// $price_of_other_items will have the price of all the other items.
			$price_of_other_items = $total_of_cart - $price_of_bump_offer;
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

		// Only enqueue on the Checkout page.
		if ( ! function_exists( 'is_checkout' ) || ! is_checkout() ) {

			return;
		}

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

		if ( ! empty( $cart_item['key'] ) && null != WC()->session->get( 'encountered_bump_tarket_key_array' ) && is_array( WC()->session->get( 'encountered_bump_tarket_key_array' ) ) ) {

			if ( in_array( $cart_item['key'], WC()->session->get( 'encountered_bump_tarket_key_array' ) ) ) {

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
	public function fetch_order_bump_from_collection( $order_bump_collection = array(), $mwb_ubo_global_options = array() ) {

		/**
		 * Check enability of the plugin at settings page,
		 * Get all bump lists,
		 * Check for live ones and scheduled for today only,
		 * Rest leave No need to check,
		 * For live one check if target id is present and after this category check,
		 * Save the array index that is encountered and target product key.
		 */

		// Get Multiple Order Bumps limit. Default limit is 1.
		$order_bump_limit             = ! empty( $mwb_ubo_global_options['mwb_bump_order_bump_limit'] ) ? $mwb_ubo_global_options['mwb_bump_order_bump_limit'] : '1';
		$global_skip_settings         = ! empty( $mwb_ubo_global_options['mwb_bump_skip_offer'] ) ? $mwb_ubo_global_options['mwb_bump_skip_offer'] : 'yes';
		$encountered_bump_key_array   = array();
		$encountered_target_key_array = array();

		if ( ! empty( $order_bump_collection ) && is_array( $order_bump_collection ) ) {

			foreach ( $order_bump_collection as $single_bump_id => $single_bump_array ) {

				if ( count( $encountered_bump_key_array ) >= $order_bump_limit ) {
					break;
				}

				// If already encountered and saved. ( Just if happens : Worst case. ).
				if ( ! empty( $encountered_bump_key_array ) && in_array( $single_bump_id, $encountered_bump_key_array ) ) {
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
				if ( empty( $single_bump_array['mwb_upsell_bump_schedule'] ) ) {

					// Could be '0' or array( '0' );.
					$single_bump_array['mwb_upsell_bump_schedule'] = array( '0' );
				}

				// If is string means for earlier versions.
				elseif ( ! empty( $single_bump_array['mwb_upsell_bump_schedule'] ) && ! is_array( $single_bump_array['mwb_upsell_bump_schedule'] ) ) {

					$single_bump_array['mwb_upsell_bump_schedule'] = array( $single_bump_array['mwb_upsell_bump_schedule'] );

				}

				// Check for current day condition.
				if ( ! is_array( $single_bump_array['mwb_upsell_bump_schedule'] ) ) {

					continue;
				}

				// Got an array. Now check.
				if ( ! in_array( '0', $single_bump_array['mwb_upsell_bump_schedule'] ) && ! in_array( date( 'N' ), $single_bump_array['mwb_upsell_bump_schedule'] ) ) {

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
					if ( ! empty( $encountered_bump_array ) ) {
						$encountered_bump_array = 0;
					}

					// If  target category is present.
					if ( ! empty( $single_bump_array['mwb_upsell_bump_target_ids'] ) && is_array( $single_bump_array['mwb_upsell_bump_target_ids'] ) ) :

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
								if ( mwb_ubo_lite_already_in_cart( $single_bump_array['mwb_upsell_bump_products_in_offer'] ) && 'yes' == $global_skip_settings ) {

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
								if ( mwb_ubo_lite_already_in_cart( $single_bump_array['mwb_upsell_bump_products_in_offer'] ) && 'yes' == $global_skip_settings ) {

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
			} // $order_bump_collection foreach end.

		} // Empty and Array Condition check for $order_bump_collection.

		if ( ! empty( $encountered_bump_key_array ) && ! empty( $encountered_target_key_array ) ) {

			$result = array(
				'encountered_bump_array'     => $encountered_bump_key_array, // Order Bump IDs to be shown.
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
		if ( ! function_exists( 'WC' ) || empty( WC()->session ) ) {

			return;
		}

		if ( 'true' == WC()->session->get( 'encountered_bump_array_display' ) ) {

			// Cost calculations only when the offer is added.
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'woocommerce_custom_price_to_cart_item' ) );

			// Custom calculation of cost when Order bump is added to cart and coupons are applied.
			add_action( 'woocommerce_calculate_totals', array( $this, 'woocommerce_custom_price_to_cart_item_remove_coupons' ), 99 );

			// For changing the HTML of coupons applied.
			add_filter( 'woocommerce_cart_totals_coupon_html', array( $this, 'custom_cart_totals_coupon_html' ), 30, 3 );

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

			// Handle Order Bump Orders on Thankyou for Success Rate and Stats.
			add_action( 'woocommerce_thankyou', array( $this, 'report_sales_by_bump_handling' ), 10 );

			// Reset Order Bump session data.
			add_action( 'woocommerce_thankyou', array( $this, 'reset_session_variable' ), 11 );

			// add_action( 'woocommerce_applied_coupon', array( $this, 'lets_see' ) );
		}
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

				if ( ! empty( $single_order_item->legacy_values['mwb_ubo_offer_product'] ) ) {

					$single_order_item->update_meta_data( 'is_order_bump_purchase', 'true' );
				}

				if ( ! empty( $single_order_item->legacy_values['mwb_ubo_bump_id'] ) ) {

					$single_order_item->update_meta_data( 'mwb_order_bump_id', $single_order_item->legacy_values['mwb_ubo_bump_id'] );
				}
			}
		}
	}

	/**
	 * Hide Order Bump meta from order items.
	 *
	 * @since       1.4.0
	 */
	public function hide_order_bump_meta( $formatted_meta ) {

		if ( ! empty( $formatted_meta ) && is_array( $formatted_meta ) ) {

			// Hide bump id meta for both Customers and Admin.
			foreach ( $formatted_meta as $key => $meta ) {

				if ( ! empty( $meta->key ) && 'mwb_order_bump_id' == $meta->key ) {

					unset( $formatted_meta[ $key ] );
				}
			}

			// Hide bump purchase meta only for Customers.
			if ( ! is_admin() ) {

				foreach ( $formatted_meta as $key => $meta ) {

					if ( ! empty( $meta->key ) && 'is_order_bump_purchase' == $meta->key ) {

						unset( $formatted_meta[ $key ] );
					}
				}
			}
		}

		return $formatted_meta;
	}

	/**
	 * Add Order Bump - Order Post meta.
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
					update_post_meta( $order_id, 'mwb_bump_order', 'true' );

					// Add post meta for processing Success Rate and Stats on Thankyou page.
					update_post_meta( $order_id, 'mwb_bump_order_process_sales_stats', 'true' );

					break;
				}
			}
		}
	}

	/**
	 * Handle Order Bump Orders on Thankyou for Success Rate and Stats.
	 *
	 * @since    1.4.0
	 */
	public function report_sales_by_bump_handling( $order_id ) {

		if ( ! $order_id ) {

			return;
		}

		// Process once and only for Order Bump orders.
		$bump_order = get_post_meta( $order_id, 'mwb_bump_order_process_sales_stats', true );

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

		if ( ! in_array( $order->get_status(), $processed_order_statuses ) ) {

			return;
		}

		$order_items = $order->get_items();

		if ( ! empty( $order_items ) && is_array( $order_items ) ) {

			foreach ( $order_items as $item_id => $single_item ) {

				if ( ! empty( wc_get_order_item_meta( $item_id, 'is_order_bump_purchase', true ) ) && ! empty( wc_get_order_item_meta( $item_id, 'mwb_order_bump_id', true ) ) ) {

					$order_bump_item_total = wc_get_order_item_meta( $item_id, '_line_total', true );

					// Add Order Bump Success count and Total Sales for the respective Order Bump.
					$sales_by_bump = new Mwb_Upsell_Order_Bump_Report_Sales_By_Bump( wc_get_order_item_meta( $item_id, 'mwb_order_bump_id', true ) );

					$sales_by_bump->add_bump_success_count();
					$sales_by_bump->add_bump_total_sales( $order_bump_item_total );

					// Delete bump id as it might change so no need to associate the order item with it.
					wc_delete_order_item_meta( $item_id, 'mwb_order_bump_id' );
				}
			}
		}

		/**
		 * Delete Order Bump sales stats meta so that this is processed only once.
		 */
		delete_post_meta( $order_id, 'mwb_bump_order_process_sales_stats' );
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
	 * This function decides whether to show custom form on checkout page or not.
	 * The custom form should not be shown in 3 three cases
	 *
	 * If the pro plugin is disabled.(This is already checked in add_offer_in_cart, no need to check here.)
	 * If either of the global form toggle or individual form toggle is found to be off.
	 * If both global form toggle and individual form toggle settings are on but no custom fields has been added.
	 *
	 * @return id yes or false that will tell us to show bump popup or not.
	 */
	public function fetch_options_for_demo_purpose() {

		$same_id_check = isset( $_POST['same_id_check'] ) ? $_POST['same_id_check'] : '';
		if ( '' == $same_id_check ) {
			return;
		}
		$option_bump_list = get_option( 'mwb_ubo_bump_list' );
		$global_option    = get_option( 'mwb_ubo_global_options', false );

		// First check for the Global option.
		if ( false != $global_option ) {
			$global_option_form_toggle = isset( $global_option['mwb_bump_enable_plugin_form'] ) ? $global_option['mwb_bump_enable_plugin_form'] : '';
			if ( 'yes' == $global_option_form_toggle ) {
				// Now check for the 'Show Custom Form Fields' option in individual Order bump edit window.
				foreach ( $option_bump_list as $key => $value ) {
					if ( $key == $same_id_check ) {
						if ( $value['mwb_ubo_offer_add_custom_fields'] == 'yes' ) {
							$data = 'yes';
							echo json_encode( $data );
							wp_die();
						}
					} else {
						continue;
					}
				}
			}
		}
		$data = false;
		echo json_encode( $data );
		wp_die();
	}

	/**
	 * This function is made to check if whether to show  custom fields or not for a particular Order Bump.
	 */
	public function show_or_hide_custom_fields_toggle() {

		$id = isset( $_POST['id'] ) ? $_POST['id'] : '';

		if ( '' == $id ) {
			return;
		}

		$option_bump_list = get_option( 'mwb_ubo_bump_list' );

		foreach ( $option_bump_list as $key => $value ) {
			if ( $key == $id ) {
				if ( $value['mwb_ubo_offer_add_custom_fields'] == 'yes' ) {
					return true;
				}
				return false;
			}
		}
		return false;
	}




	// ===============================================================================================================================================================
													// LIMIT THE USE OF BUMP OFFER FEATURE STARTS
	// ===============================================================================================================================================================

	/**
	 * Check if the bump can be used anymore. By this function the Order Bump will check
	 * for the use limit of the Order Bump.
	 */
	public function check_if_the_bump_can_be_used_anymore() {

		check_ajax_referer( 'mwb_ubo_lite_nonce', 'nonce' );
		$response = '';
		$bump_id  = ! empty( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

		if ( ! empty( get_option( 'mwb_ubo_bump_list' ) ) ) {
			$all_bump = get_option( 'mwb_ubo_bump_list' );

			// count the number of Order IDs.
			$current_count = count( $all_bump[ $bump_id ]['unique_order_ids_for_this_bump'] );

			// What is the limit that has been set by the admin.
			$set_limit = $all_bump[ $bump_id ]['mwb_upsell_bump_use_limit'];
			$previous_limit = $all_bump[ $bump_id ]['mwb_upsell_bump_previous_limit'];

			// If the limit is not set and the bump can be used unlimited number of times, simply return positive.
			if ( $set_limit == 'unlimited' ) {
				$response = 'positive';
				echo $response;
				wp_die();
			}

			if ( ! empty( $set_limit ) && ! empty( $previous_limit ) ) {
				if ( 'unlimited' != $previous_limit ) {
					if ( $set_limit != $previous_limit ) {
						// reset unique order ids.
						$all_bump[ $bump_id ]['unique_order_ids_for_this_bump'] = array();
						update_option( 'mwb_ubo_bump_list', $all_bump );
					}
				}
			}

			$answer = $set_limit - $current_count;
			// If the set limit is more than the number of times it has been used, retun true else false.
			if ( $answer > 0 ) {
				$response = 'positive';
			} else {
				$response = 'negative';
			}
		} else {
			$response = 'negative';
		}
		echo $response;
		wp_die();
	}

	/**
	 * This function is used to receive the value of count and order bump id and
	 * set them into a session variable and use them on the thankyou page.
	 *
	 * This function will only run when the pro version of plugin is Activated.
	 */
	public function send_value_of_count_and_bump_id_start_session() {

		check_ajax_referer( 'mwb_ubo_lite_nonce', 'nonce' );
		$id               = isset( $_POST['was_order_bump_id'] ) ? sanitize_text_field( wp_unslash( $_POST['was_order_bump_id'] ) ) : '';
		$unique_order_ids = isset( $_POST['order_ids_associated_with_order_bump'] ) ? sanitize_text_field( wp_unslash( $_POST['order_ids_associated_with_order_bump'] ) ) : '';

		if ( ! empty( get_option( 'mwb_ubo_bump_list' ) ) ) {

			$all_bump          = get_option( 'mwb_ubo_bump_list' );
			$already_set_limit = isset( $all_bump[ $id ]['mwb_upsell_bump_use_limit'] ) ? $all_bump[ $id ]['mwb_upsell_bump_use_limit'] : '';

			// If the limit is not set and the bump can be used unlimited number of times, do not set the session
			// hence the session will be null on thankyou page and therefore the Order Id will not be saved.
			if ( $already_set_limit == 'unlimited' ) {
				$data = 'unlimited';
				wp_die();
			} else {
				// If multiple Bumps are there, session will be null for the first bump in the loop but for every bump
				// after the first the session will already be set therefore we are using if else below.

				$this_bump_details = array(
					'id'               => $id,
					'unique_order_ids' => json_decode( $unique_order_ids ),
				);

				if ( null == WC()->session->get( 'bump_use_count' ) ) {
					// For first time session is not set, the array has to be made.
					$session_data_count = array();
				} else {
					// If the session is already set, simply get the array been set into the session.
					$session_data_count = WC()->session->get( 'bump_use_count' );
				}
				array_push( $session_data_count, $this_bump_details );

				WC()->session->set( 'bump_use_count', $session_data_count );
				$data = WC()->session->get( 'bump_use_count' );
			}
			// echo '<pre>'; print_r( $data ); echo '</pre>';die('session');
			echo json_encode( $data );
			wp_die();
		}
	}

	/**
	 * This function will inform on thanyou page that the free version was used hence no need to alter the Order ID's and
	 * the count feature and all values related to it will not be altered.
	 */
	public function send_pro_not_activated_message() {
		check_ajax_referer( 'mwb_ubo_lite_nonce', 'nonce' );
		$id                       = isset( $_POST['was_order_bump_id'] ) ? sanitize_text_field( wp_unslash( $_POST['was_order_bump_id'] ) ) : '';
		$check_if_pro_activated   = isset( $_POST['check_if_pro_activated'] ) ? sanitize_text_field( wp_unslash( $_POST['check_if_pro_activated'] ) ) : '';
		$session_data_pro_message = array(
			'id'                     => $id,
			'check_if_pro_activated' => $check_if_pro_activated,
		);
		if ( null == WC()->session->get( 'bump_free_only_signal' ) ) {
			WC()->session->set( 'bump_free_only_signal', $session_data_pro_message );
		}
		wp_die();

	}


	/**
	 * Function to upgrade the value of count in database.
	 *
	 * After the function send_value_of_count_and_bump_id_start_session, upgrade the value of
	 * count in the database.
	 */
	public function update_the_value_count_for_bump_use( $order_id ) {

		if ( '' == $order_id || empty( $order_id ) ) {
			return;
		}
		if ( null == WC()->session->get( 'bump_use_count' ) ) {
			return;
		}
		// echo '<pre>'; print_r( WC()->session->get( 'bump_use_count' ) ); echo '</pre>';
		$all_bumps_info = ! empty( get_option( 'mwb_ubo_bump_list' ) ) ? get_option( 'mwb_ubo_bump_list' ) : '';
		foreach ( WC()->session->get( 'bump_use_count' ) as $key => $value ) {
			echo '<pre>'; print_r( $value ); echo '</pre>';
			$id_of_previous_bump = $value['id'];
			$unique_order_ids    = ! empty( $value['unique_order_ids'] ) ? $value['unique_order_ids'] : array();
			// $unique_order_ids    = json_decode( $unique_order_ids, true );
			if ( ! in_array( $order_id, $unique_order_ids ) ) {
				array_push( $unique_order_ids, $order_id );
			}
			// echo '<pre>'; print_r( $unique_order_ids ); echo '</pre>';die;
			$all_bumps_info[ $id_of_previous_bump ]['unique_order_ids_for_this_bump'] = $unique_order_ids;
			update_option( 'mwb_ubo_bump_list', $all_bumps_info );
		}
		WC()->session->__unset( 'bump_use_count' );

	}

	// ===============================================================================================================================================================
												// LIMIT THE USE OF BUMP OFFER FEATURE ENDS
	// ===============================================================================================================================================================

	/**
	 * This function will unset the session 'restrict_coupon_on_bump_target' if the Order bump is not clicked.
	 *
	 * @return void
	 */
	public function unset_coupon_session_if_bump_not_checked() {
		check_ajax_referer( 'mwb_ubo_lite_nonce', 'nonce' );
		$id = isset( $_POST['order_bump_id'] ) ? sanitize_text_field( wp_unslash( $_POST['order_bump_id'] ) ) : '';
		// Order bump was unchecked for this id.. simply check the session and unset if session is set.
		if ( WC()->session->__isset( 'restrict_coupon_on_bump_target' . $id ) ) {
			WC()->session->__unset( 'restrict_coupon_on_bump_target' . $id );
			echo 'Session for Order bump ' . $id . ' has been destroyed';
		} else {
			echo 'No coupon sessions found for Order Bump' . $id;
		}
		wp_die();
	}	



	// End of class.

}

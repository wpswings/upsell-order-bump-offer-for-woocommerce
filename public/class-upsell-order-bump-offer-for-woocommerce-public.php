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
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
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
		wp_register_script( 'add_bump_offer_to_cart', plugin_dir_url( __FILE__ ) . 'js/mwb_ubo_lite_public_script.js', array('jquery') );

	  	wp_localize_script( 'add_bump_offer_to_cart', 'mwb', 
	  		array( 
	  			'ajaxurl' 		=> admin_url( 'admin-ajax.php' ),
	  			'mobile_view' 	=> wp_is_mobile(),
	  			'auth_nonce'	=> wp_create_nonce( 'mwb_ubo_lite_nonce' )
	  		)
	  	);

	   	wp_enqueue_script( 'add_bump_offer_to_cart' );
	   	
	   	// Do not work in mobile-view mode.
	   	if( ! wp_is_mobile() ) {

	   		wp_enqueue_script( 'zoom-script', plugins_url( '/js/zoom-script.js', __FILE__ ), array('jquery'), $this->version, true );
	   	}

	}

	/**
	 * Add custom hook to show offer bump after payment gateways but before 
	 * terms as one is not provided by Woocommerce. 
	 *
	 * @since    1.0.0
	 */
	public function add_bump_offer_custom_hook( $template_name, $template_path ) {

		if ( 'checkout/terms.php' === $template_name ) {

			do_action( 'mwb_ubo_after_pg_before_terms' );

			remove_action( 'woocommerce_before_template_part', [ $this, 'add_bump_offer_custom_hook' ], 10 );
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
		require_once plugin_dir_path( __FILE__ ) . '/partials/upsell-order-bump-offer-for-woocommerce-public-display.php';
	}

	/**
	 * Add bump offer product to cart ( checkbox ).
	 *
	 * @since    1.0.0
	 */

	public function add_offer_in_cart() {

		// Nonce verification.
		check_ajax_referer( 'mwb_ubo_lite_nonce' , 'nonce' );

		// The id of the offer to be added.
		$bump_product_id = ! empty( $_POST[ 'id' ] ) ? sanitize_text_field( $_POST[ 'id' ] ) : '';

		$bump_target_cart_key = ! empty( $_POST[ 'bump_target_cart_key' ] ) ? sanitize_text_field( $_POST[ 'bump_target_cart_key' ] ) : '';

		$bump_discounted_price = ! empty( $_POST[ 'discount' ] ) ? sanitize_text_field( $_POST[ 'discount' ] ) : '';
		
		$cart_item_data = array( 'mwb_discounted_price' => $bump_discounted_price );

		$_product = wc_get_product( $bump_product_id );

		if( $_product->has_child() ) {

			// Generate default price html.
            $bump_price_html =  mwb_ubo_lite_custom_price_html( $bump_product_id, $bump_discounted_price );

            $response = array(
							'key' => esc_html__( 'true', 'upsell-order-bump-offer-for-woocommerce' ),
							'message' => $bump_price_html
						);

            // Now we have to add a pop up.
			echo json_encode( $response );

		} else {

			// If simple product or any single variations.
			// Add to cart the same.

			if( ! session_id() ) {

				session_start();
			}
			
			$_SESSION[ 'bump_offer_product_key' ] = WC()->cart->add_to_cart( $bump_product_id, $quantity = 1, $variation_id = 0, $variation = array(), $cart_item_data );

			$_SESSION[ 'bump_offer_status' ] = esc_html__( 'added', 'upsell-order-bump-offer-for-woocommerce' );

			echo json_encode( $_SESSION[ 'bump_offer_status' ] );
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
		check_ajax_referer( 'mwb_ubo_lite_nonce' , 'nonce' );

		// At this time, we already have the offer product cart key. 
		if( ! session_id() ) {

			session_start(); 
		}

	    WC()->cart->remove_cart_item( $_SESSION[ 'bump_offer_product_key' ] );

	    unset( $_SESSION[ 'bump_offer_product_key' ] );
	    unset( $_SESSION[ 'bump_offer_status' ] );
	    
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
		check_ajax_referer( 'mwb_ubo_lite_nonce' , 'nonce' );

		$bump_offer_id = ! empty( $_POST[ 'id' ] ) ? sanitize_text_field( $_POST[ 'id' ] ) : ''; 	//variation parent.

		$bump_offer_discount = ! empty( $_POST[ 'discount' ] ) ? sanitize_text_field( $_POST[ 'discount' ] ) : '';

		$bump_target_cart_key = ! empty( $_POST[ 'bump_target_cart_key' ] ) ? sanitize_text_field( $_POST[ 'bump_target_cart_key' ] ) : '';

		$attributes_selected_options = $_POST[ 'attributes_selected_options' ];

		foreach ( $attributes_selected_options as $key => $value) {
			 
			$value = ! empty( $value ) ? sanitize_text_field( $value ) : '';
		}

		// Stripslashes if encountered
		$attributes_selected_options['attribute_games'] = stripslashes( $attributes_selected_options['attribute_games'] );

		// Got all values to search for variation id from selected attributes.
		$product = wc_get_product( $bump_offer_id );
		$product_data_store = new WC_Product_Data_Store_CPT();
		$variation_id = $product_data_store->find_matching_product_variation( $product, $attributes_selected_options );

		// Image to reflect on select change.
		$image_id = get_post_thumbnail_id( $variation_id );
		if( ! empty( $image_id ) ) {

			$html = wc_get_gallery_image_html( $image_id, true );
			$bump_var_image = apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $image_id );

		} else {

			$bump_var_image = wc_placeholder_img();
		}

		// Variation id will be empty if selected variation is not available.
		if( empty( $variation_id ) ) {

			$response = array( 
				'key' => 'not_available',
				'message' => '<p class="stock out-of-stock">' . esc_html__( 'Sorry, this variation is not available.', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>',
				'image' => $bump_var_image, 
			);
			echo json_encode( $response );

		} else {

			// Check if in instock.
			$selected_variation_product = wc_get_product( $variation_id );

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

		check_ajax_referer( 'mwb_ubo_lite_nonce' , 'nonce' );

		// Contains selected variation ID.
		$variation_id = ! empty( $_POST[ 'id' ] ) ? sanitize_text_field( $_POST[ 'id' ] ) : ''; 	

		// Contains parent variable ID.		
		$variation_parent_id = ! empty( $_POST[ 'parent_id' ] ) ? sanitize_text_field( $_POST[ 'parent_id' ] ) : ''; 

		// Contains bump discount.
		$bump_offer_discount = ! empty( $_POST[ 'discount' ] ) ? sanitize_text_field( $_POST[ 'discount' ] ) : ''; 		

		// Contains target cart key.
		$bump_target_cart_key = ! empty( $_POST[ 'bump_target_cart_key' ] ) ? sanitize_text_field( $_POST[ 'bump_target_cart_key' ] ) : ''; 

		// Now safe to add to cart.
		$cart_item_data = array(
			'mwb_discounted_price' => $bump_offer_discount
		);

		if( ! session_id() ) {

			session_start();
		}

		$_SESSION[ 'bump_offer_product_key' ] = WC()->cart->add_to_cart( $variation_parent_id, $quantity = '1', $variation_id , $variation = array(), $cart_item_data );

		$_SESSION[ 'bump_offer_status' ] = esc_html__( 'added', 'upsell-order-bump-offer-for-woocommerce' );

		echo json_encode( $_SESSION[ 'bump_offer_status' ] );
		wp_die();
	}

	/**
	 * On successful order reset data.
	 *
	 * @since    1.0.0
	 */

	public function reset_session_variable() {

		if( ! session_id() ) {

			session_start();
		}

		// Destroy session on order completed.
		session_destroy();
	}

	/**
	 * Add order item meta to bump product.
	 *
	 * @since    1.0.0
	 */
    public function add_order_item_meta( $order ) {

	    $order_items = $order->get_items();
		
		if( ! session_id() ) {

			session_start();
		}

		foreach ( $order_items as $item_key => $single_order_item ) {

			if( ! empty( $_SESSION[ 'bump_offer_product_key' ] ) && ( $single_order_item->legacy_cart_item_key == $_SESSION[ 'bump_offer_product_key' ] ) ) {

				$single_order_item->update_meta_data( esc_html__( 'Bump Offer', 'upsell-order-bump-offer-for-woocommerce' ) , esc_html__( 'applied', 'upsell-order-bump-offer-for-woocommerce' ) );
			}
		}
    }

	/**
	 * Disabling the offer quantity for bump product in Cart page.
	 *
	 * @since    1.0.0
	 */

	public function disable_quantity_bump_product_in_cart( $product_quantity, $cart_item_key ) {

		if( ! session_id() ) {

			session_start();
		}
		
		if( ! empty( $_SESSION[ 'bump_offer_product_key' ] ) && $cart_item_key == $_SESSION[ 'bump_offer_product_key' ]  ) {

			// For Bump product allowed quantity is one.
			$product_quantity = 1;
			return $product_quantity;
		}
		
		return $product_quantity;
	}	


	/**
	* Removal of target and bump product is handled here.
	*
	* @since 1.0.0
	*/

	public function after_remove_product( $key_to_be_removed, $cart_object ) {

		$mwb_ubo_global_options = get_option( 'mwb_ubo_global_options', mwb_ubo_lite_default_global_options() );

		if( ! session_id() ) {

			session_start();
		}

		// Case of target being removed.
		if( ! empty( $_SESSION[ 'mwb_upsell_bump_target_key' ] ) && ! empty( $key_to_be_removed ) && $key_to_be_removed == $_SESSION[ 'mwb_upsell_bump_target_key' ] ) {

			// Do this only when settings are setted yes.
			if( 'yes' == $mwb_ubo_global_options[ 'mwb_ubo_offer_removal' ] ) {

				// Remove bump offer too if present.
				if( ! empty( $_SESSION[ 'bump_offer_product_key' ] ) ) {

					unset( WC()->cart->cart_contents[ $_SESSION[ 'bump_offer_product_key' ] ] );
				}

				// Reset session.
				session_destroy();

			} else {

				// Convert to normal product.
				unset( WC()->cart->cart_contents[ $_SESSION[ 'bump_offer_product_key' ] ][ 'mwb_discounted_price' ] );

				// Reset session.
				session_destroy();
			}
		}

		// Case of offer being removed.
		if( ! empty( $_SESSION[ 'bump_offer_product_key' ] ) && ! empty( $key_to_be_removed ) && $key_to_be_removed == $_SESSION[ 'bump_offer_product_key' ] ) {

			// When bump key is deleted from cart page, reset session variables.
			unset( $_SESSION[ 'bump_offer_product_key' ] );
			unset( $_SESSION[ 'bump_offer_status' ] );
		}
	}


	/**
	 * Change price at last for bump offer product.
	 *
	 * @since    1.0.0
	 */

	public function woocommerce_custom_price_to_cart_item( $cart_object ) {  

    	if( ! WC()->session->__isset( "reload_checkout" ) ) {

	        foreach ( $cart_object->cart_contents as $key => $value ) {

	            if( ! empty( $value["mwb_discounted_price"] ) ) {

	            	$product_id = ! empty( $value[ 'variation_id' ] ) ? $value[ 'variation_id' ] : $value[ 'product_id' ];

	            	$price_discount = mwb_ubo_lite_custom_price_html( $product_id, $value[ 'mwb_discounted_price' ], 'price' );

	            	$value['data']->set_price( $price_discount );
	            }
	        }
	    }  
	}
	
// End of class.
}

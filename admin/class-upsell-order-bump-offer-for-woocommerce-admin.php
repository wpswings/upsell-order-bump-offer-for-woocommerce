<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin
 * @author     Make Web Better <webmaster@makewebbetter.com>
 */
class Upsell_Order_Bump_Offer_For_Woocommerce_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		$screen = get_current_screen();

		if( isset( $screen->id ) )
		{

			$pagescreen = $screen->id;

			if( 'toplevel_page_upsell-order-bump-offer-for-woocommerce-setting' == $pagescreen ) {

				wp_register_style( 'mwb_ubo_lite_admin_style', plugin_dir_url( __FILE__ ) . 'css/upsell-order-bump-offer-for-woocommerce-admin.css', array(), $this->version, 'all' );

				wp_enqueue_style( 'mwb_ubo_lite_admin_style' );

				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );

				wp_enqueue_style( 'woocommerce_admin_menu_styles' );

				wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
				
				wp_enqueue_style( 'woocommerce_admin_styles' );

				wp_enqueue_style( 'wp-color-picker' );

			}
		}

	}

	/**
	 * Register the JavaScript for the admin area.
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

		$screen = get_current_screen();

		if( isset( $screen->id ) ) {

			$pagescreen = $screen->id;

			if( 'toplevel_page_upsell-order-bump-offer-for-woocommerce-setting' == $pagescreen ) {

				wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, false );

				wp_enqueue_script('mwb_ubo_lite_admin_script', plugin_dir_url( __FILE__ ) . 'js/upsell-order-bump-offer-for-woocommerce-admin.js', array( 'jquery' ), $this->version, false );

				wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip', 'wc-enhanced-select' ), WC_VERSION );

				wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.js', array( 'jquery' ), WC_VERSION, true );	
					$locale  = localeconv();
					$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';
					$params = array(
						/* Translators: %s: decimal. */
						'i18n_decimal_error'                => sprintf( __( 'Please enter in decimal (%s) format without thousand separators.', 'upsell-order-bump-offer-for-woocommerce' ), $decimal ),
						/* Translators: %s: price decimal separator. */
						'i18n_mon_decimal_error'            => sprintf( __( 'Please enter in monetary decimal (%s) format without thousand separators and currency symbols.', 'upsell-order-bump-offer-for-woocommerce' ), wc_get_price_decimal_separator() ),
						'i18n_country_iso_error'            => __( 'Please enter in country code with two capital letters.', 'upsell-order-bump-offer-for-woocommerce' ),
						'i18_sale_less_than_regular_error'  => __( 'Please enter in a value less than the regular price.', 'upsell-order-bump-offer-for-woocommerce' ),
						'decimal_point'                     => $decimal,
						'mon_decimal_point'                 => wc_get_price_decimal_separator(),
						'strings' => array(
							'import_products' => __( 'Import', 'upsell-order-bump-offer-for-woocommerce' ),
							'export_products' => __( 'Export', 'upsell-order-bump-offer-for-woocommerce' ),
						),
						'urls' => array(
							'import_products' => esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer' ) ),
							'export_products' => esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_exporter' ) ),
						),
					);

				wp_enqueue_script( 'wp-color-picker');

				wp_localize_script( 'mwb_ubo_lite_admin_script', 'mwb_ubo_lite_ajaxurl', admin_url('admin-ajax.php') );

				wp_localize_script( 'mwb_ubo_lite_admin_script', 'mwb_ubo_lite_location', admin_url('admin.php').'?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting' );

				if( ! empty( $_GET['mwb-bump-offer-section'] ) ) {

					$bump_offer_section['value'] = sanitize_text_field( wp_unslash( $_GET['mwb-bump-offer-section'] ) );

					wp_localize_script( 'mwb_ubo_lite_admin_script', 'offer_section_obj', $bump_offer_section );
				}

				if( ! empty( $_GET['mwb-bump-template-section'] ) ) {

					$bump_template_section['value'] = sanitize_text_field( wp_unslash( $_GET['mwb-bump-template-section'] ) );

					wp_localize_script( 'mwb_ubo_lite_admin_script', 'template_section_obj', $bump_template_section );
				}

				wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );
				
				wp_enqueue_script( 'woocommerce_admin' );

				wp_enqueue_script('mwb_ubo_lite_add_new_offer_script',plugin_dir_url( __FILE__ ) . 'js/mwb_ubo_lite_add_new_offer_script.js', array( 'woocommerce_admin','wc-enhanced-select' ), $this->version,false);

				wp_localize_script( 'mwb_ubo_lite_add_new_offer_script', 'ajax_url', admin_url('admin-ajax.php') );

				wp_enqueue_script('mwb_ubo_lite_sticky_js',plugin_dir_url( __FILE__ ) . 'js/jquery.sticky-sidebar.js', array( 'jquery' ), $this->version ,false );
			}
		}
	}


	/**
	 * Adding upsell bump menu page.
	 *
	 * @since    1.0.0
	 */
	public function mwb_ubo_lite_admin_menu() {

		add_menu_page(
			__( 'Order Bump', 'upsell-order-bump-offer-for-woocommerce' ),
			__( 'Order Bump', 'upsell-order-bump-offer-for-woocommerce' ),
			'manage_woocommerce',
			'upsell-order-bump-offer-for-woocommerce-setting',
			array( $this, 'mwb_ubo_lite_add_backend' ),
			'dashicons-yes-alt',
			56
		);
	}

	/**
	 * Callable function for upsell bump menu page.
	 *
	 * @since    1.0.0
	 */
	public function mwb_ubo_lite_add_backend() {

		if( is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) && class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) ) {

			$mwb_upsell_bump_callname_lic = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$mwb_upsell_bump_lic_callback_function;
        
	        $mwb_upsell_bump_callname_lic_initial = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$mwb_upsell_bump_lic_ini_callback_function;

	        $day_count = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$mwb_upsell_bump_callname_lic_initial();

			if( Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$mwb_upsell_bump_callname_lic() || 0 <= $day_count ) {

				if ( ! Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$mwb_upsell_bump_callname_lic() && 0 <= $day_count ):

					$day_count_warning = floor( $day_count );

					// Days warning.
					$day_string = sprintf( _n( '%s day', '%s days', $day_count_warning, 'upsell-order-bump-offer-for-woocommerce' ), number_format_i18n( $day_count_warning ) );

					// Days warning html.
					$day_string = '<span id="mwb-upsell-bump-day-count" >'.$day_string.'</span>';

					?>
					
					<div id="mwb-bump-thirty-days-notify" class="notice notice-warning">
					    <p>
					    	<strong><a href="?page=mwb-bump-offer-setting&tab=license">

					    	<!-- License warning. -->
					    	<?php esc_html_e( 'Activate', 'upsell-order-bump-offer-for-woocommerce' ); ?></a><?php printf( __( ' the license key before %s or you may risk losing data and the plugin will also become dysfunctional.', 'upsell-order-bump-offer-for-woocommerce' ), $day_string ); ?></strong>
					    </p>
					</div>

					<?php

				endif;

				require_once plugin_dir_path( __FILE__ ).'/partials/upsell-order-bump-offer-for-woocommerce-admin-display.php';

			} else { ?>

				<div class="wrap woocommerce" id="mwb_upsell_bump_setting_wrapper">

					<div class="mwb_upsell_bump_setting_title"><?php esc_html_e( apply_filters( 'mwb_ubo_lite_heading', esc_html__( 'Upsell Order Bump Offers', 'upsell-order-bump-offer-for-woocommerce' ) ) ); ?>
			        <span class="mwb_upsell_bump_setting_title_version"><?php esc_html_e( 'v', 'upsell-order-bump-offer-for-woocommerce'); esc_html_e( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_VERSION ); ?></span>
			    	</div>
			    </div><?php

				//Failed Activation.
				include_once( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_DIRPATH . '/admin/partials/templates/mwb_upsell_bump-license.php' );
			}

		} else {

			// With org files only.
			require_once plugin_dir_path( __FILE__ ).'/partials/upsell-order-bump-offer-for-woocommerce-admin-display.php';
		}
	}


	/**
	 * Select2 search for adding bump target products.
	 *
	 * @since    1.0.0
	 */
	public function search_products_for_bump() {
		
		$return = array();
		$search_results = new WP_Query( array( 
			's'						=> ! empty( $_GET['q'] )? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '',
			'post_type' 			=> array( 'product', 'product_variation' ),
			'post_status' 			=> array( 'publish' ),
			'ignore_sticky_posts' 	=> 1,
			'posts_per_page' 		=> -1 
		) );

		if( $search_results->have_posts() ) :

			while( $search_results->have_posts() ): 

				$search_results->the_post();
					
				$title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;

				/**
				 * Check for post type as query sometimes returns posts even after mentioning post_type.
				 * As some plugins alter query which causes issues.
				 */
				$post_type = get_post_type( $search_results->post->ID );

				if( 'product' != $post_type && 'product_variation' != $post_type ) {

					continue;
				}

				$product = wc_get_product( $search_results->post->ID );
				$downloadable = $product->is_downloadable();
				$stock = $product->get_stock_status();
				$product_type = $product->get_type();

				$unsupported_product_types = array(
					'grouped',
					'external',
					'subscription',
					'variable-subscription',
					'subscription_variation',
				);

				if( in_array( $product_type, $unsupported_product_types ) || 'outofstock' == $stock ) {

					continue;
				}

				$return[] = array( $search_results->post->ID, $title ); 

			endwhile;

		endif;

		echo json_encode( $return );

		wp_die();
	}


	/**
	 * Select2 search for adding bump offer products.
	 *
	 * @since    1.0.0
	 */
	public function search_products_for_offers() {

		$return = array();
		$search_results = new WP_Query( array( 
			's'						=> ! empty( $_GET['q'] )? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '',
			'post_type' 			=> array( 'product' , 'product_variation' ),
			'post_status' 			=> array( 'publish' ),
			'ignore_sticky_posts' 	=> 1,
			'posts_per_page' 		=> -1 
		) );

		if( $search_results->have_posts() ) :

			while( $search_results->have_posts() ): 

				$search_results->the_post();	

				$title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;

				/**
				 * Check for post type as query sometimes returns posts even after mentioning post_type.
				 * As some plugins alter query which causes issues.
				 */
				$post_type = get_post_type( $search_results->post->ID );

				if( 'product' != $post_type && 'product_variation' != $post_type ) {

					continue;
				}

				$product = wc_get_product( $search_results->post->ID );
				$downloadable = $product->is_downloadable();
				$stock = $product->get_stock_status();
				$product_type = $product->get_type();

				$unsupported_product_types = array(
					'grouped',
					'external',
					'subscription',
					'variable-subscription',
					'subscription_variation',
				);

				if( in_array( $product_type, $unsupported_product_types ) || 'outofstock' == $stock ) {

					continue;
				}

				$return[] = array( $search_results->post->ID, $title ); 

			endwhile;

		endif;
		
		echo json_encode( $return );

		wp_die();
	}


	/**
	 * Select2 search for adding bump target categories.
	 *
	 * @since    1.0.0
	 */
	public function search_product_categories_for_bump() {
		
		$return = array();
		$args = array(
			'search'	 => ! empty( $_GET['q'] )? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '',
		    'taxonomy'   => 'product_cat',
		    'orderby'    => 'name',
		);

		$product_categories = get_terms($args);

		if( ! empty( $product_categories ) && is_array( $product_categories ) && count( $product_categories ) ) {

			foreach ( $product_categories as $single_product_category ) {

				$cat_name = ( mb_strlen( $single_product_category->name ) > 50 ) ? mb_substr( $single_product_category->name, 0, 49 ) . '...' : $single_product_category->name;
				
				$return[] = array( $single_product_category->term_id, $single_product_category->name );
			}
		}

		echo json_encode( $return );

		wp_die();
	}

	/**
	 * Function for showing bump offer price in order table.
	 *
	 * @since    1.0.0
	 */
	public function show_bump_total_content( $column_name, $post_ID ) {

		// Add bump offer price to order total column.
		if ( 'order_total' == $column_name ) {

			// Get order id as post id.
			$order = wc_get_order( $post_ID );

			foreach ( $order->get_items() as $item_id => $item ) { 

				$bump_offer = wc_get_order_item_meta( $item_id, 'Bump Offer', true );
				$bump_price = $item->get_total();

			}

	        if( ! empty( $bump_offer ) ) : ?>

	        	<p class= "mwb_bump_table_html" >
        			<?php esc_html_e( 'Order Bump: ', 'upsell-order-bump-offer-for-woocommerce' ); 
        				_e( wc_price( $bump_price ) );
        			?>
        		</p>

	        <?php endif;
		}
	}

// End of class.
}

<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
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
 * @author     WP Swings <webmaster@wpswings.com>
 */
class Upsell_Order_Bump_Offer_For_Woocommerce_Admin {


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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
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

		$valid_screens = array(
			'toplevel_page_upsell-order-bump-offer-for-woocommerce-setting',
			'order-bump_page_upsell-order-bump-offer-for-woocommerce-reporting',
			'plugins',
		);

		$screen = get_current_screen();
		if ( isset( $screen->id ) ) {

			$pagescreen = $screen->id;

			if ( in_array( $pagescreen, $valid_screens, true ) ) {

				wp_register_style( 'wps_ubo_lite_admin_style', plugin_dir_url( __FILE__ ) . 'css/upsell-order-bump-offer-for-woocommerce-admin.css', array(), $this->version, 'all' );

				wp_enqueue_style( 'wps_ubo_lite_admin_style' );

				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );

				wp_enqueue_style( 'woocommerce_admin_menu_styles' );

				wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );

				wp_enqueue_style( 'woocommerce_admin_styles' );

				wp_enqueue_style( 'wp-color-picker' );
			}
		}

		if ( $screen && 'product' === $screen->post_type && 'post' === $screen->base ) {

			wp_register_style( 'wps_ubo_lite_admin_product_edit_style', plugin_dir_url( __FILE__ ) . 'css/upsell-order-bump-offer-for-woocommerce-product-edit-admin.css', array(), $this->version, 'all' );

			wp_enqueue_style( 'wps_ubo_lite_admin_product_edit_style' );
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

		if ( isset( $screen->id ) ) {

			$pagescreen = $screen->id;

			if ( 'toplevel_page_upsell-order-bump-offer-for-woocommerce-setting' === $pagescreen || 'plugins' === $pagescreen || 'order-bump_page_upsell-order-bump-offer-for-woocommerce-reporting' == $pagescreen ) {

				$wps_plugin_list = get_option( 'active_plugins' );
				$wps_is_pro_active = false;
				$wps_plugin = 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php';
				if ( in_array( $wps_plugin, $wps_plugin_list ) ) {
					$wps_is_pro_active = true;
				}
				wp_enqueue_script( $this->plugin_name . '_masonry_effects', plugin_dir_url( __FILE__ ) . 'js/masonry_effects.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->plugin_name . '_sweet_alert', plugin_dir_url( __FILE__ ) . 'js/swal.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( 'wps_ubo_lite_admin_script', plugin_dir_url( __FILE__ ) . 'js/upsell-order-bump-offer-for-woocommerce-admin.js', array( 'jquery' ), $this->version, false );
				wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip', 'wc-enhanced-select' ), WC_VERSION, false );
				wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.js', array( 'jquery' ), WC_VERSION, true );
				$locale  = localeconv();
				$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';
				$params  = array(
					/* Translators: %s: decimal. */
					'i18n_decimal_error'               => sprintf( __( 'Please enter in decimal (%s) format without thousand separators.', 'upsell-order-bump-offer-for-woocommerce' ), $decimal ),
					/* Translators: %s: price decimal separator. */
					'i18n_mon_decimal_error'           => sprintf( __( 'Please enter in monetary decimal (%s) format without thousand separators and currency symbols.', 'upsell-order-bump-offer-for-woocommerce' ), wc_get_price_decimal_separator() ),
					'i18n_country_iso_error'           => __( 'Please enter in country code with two capital letters.', 'upsell-order-bump-offer-for-woocommerce' ),
					'i18_sale_less_than_regular_error' => __( 'Please enter in a value less than the regular price.', 'upsell-order-bump-offer-for-woocommerce' ),
					'decimal_point'                    => $decimal,
					'mon_decimal_point'                => wc_get_price_decimal_separator(),
					'strings'                          => array(
						'import_products' => __( 'Import', 'upsell-order-bump-offer-for-woocommerce' ),
						'export_products' => __( 'Export', 'upsell-order-bump-offer-for-woocommerce' ),
					),
					'urls'                             => array(
						'import_products' => esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer' ) ),
						'export_products' => esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_exporter' ) ),
					),
					'is_pro_active' => $wps_is_pro_active,
				);

				$secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
				$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

				if ( ! $id_nonce_verified ) {
					wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
				}

				wp_enqueue_script( 'wp-color-picker' );

				wp_enqueue_media();

				if ( ! empty( $_GET['wps-bump-offer-section'] ) ) {

					$bump_offer_section['value'] = sanitize_text_field( wp_unslash( $_GET['wps-bump-offer-section'] ) );

					wp_localize_script(
						'wps_ubo_lite_admin_script',
						'wps_ubo_lite_offer_section_obj',
						array(
							'value' => $bump_offer_section,
							'ajaxurl'    => admin_url( 'admin-ajax.php' ),
							'auth_nonce' => wp_create_nonce( 'wps_admin_nonce' ),
						)
					);
				}

				wp_localize_script(
					'wps_ubo_lite_admin_script',
					'wps_ubo_lite_banner_offer_section_obj',
					array(
						'ajaxurl'    => admin_url( 'admin-ajax.php' ),
						'auth_nonce' => wp_create_nonce( 'wps_admin_nonce' ),
					)
				);

				if ( ! empty( $_GET['wps-bump-template-section'] ) ) {

					$bump_template_section['value'] = sanitize_text_field( wp_unslash( $_GET['wps-bump-template-section'] ) );

					wp_localize_script(
						'wps_ubo_lite_admin_script',
						'wps_ubo_lite_template_section_obj',
						array(
							'value' => $bump_template_section,
						)
					);
				}

				wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $params );

				wp_enqueue_script( 'woocommerce_admin' );

				wp_enqueue_script( 'wps_ubo_lite_add_new_offer_script', plugin_dir_url( __FILE__ ) . 'js/wps_ubo_lite_add_new_offer_script.js', array( 'woocommerce_admin', 'wc-enhanced-select' ), $this->version, false );

				wp_localize_script(
					'wps_ubo_lite_add_new_offer_script',
					'wps_ubo_lite_ajaxurl',
					array(
						'ajaxurl'    => admin_url( 'admin-ajax.php' ),
						'auth_nonce' => wp_create_nonce( 'wps_onboarding_nonce' ),
					)
				);

				wp_enqueue_script( 'wps_ubo_lite_sticky_js', plugin_dir_url( __FILE__ ) . 'js/jquery.sticky-sidebar.js', array( 'jquery' ), $this->version, false );
			}
		}
	}


	/**
	 * Adding upsell bump menu page.
	 *
	 * @since    1.0.0
	 */
	public function wps_ubo_lite_admin_menu() {
		add_menu_page(
			esc_html__( 'Order Bump', 'upsell-order-bump-offer-for-woocommerce' ),
			esc_html__( 'Order Bump', 'upsell-order-bump-offer-for-woocommerce' ),
			'manage_woocommerce',
			'upsell-order-bump-offer-for-woocommerce-setting',
			array( $this, 'wps_ubo_lite_add_backend' ),
			'dashicons-yes-alt',
			57
		);

		/**
		 * Add sub-menu for funnel settings.
		 */
		add_submenu_page( 'upsell-order-bump-offer-for-woocommerce-setting', esc_html__( 'Order Bumps & Settings', 'upsell-order-bump-offer-for-woocommerce' ), esc_html__( 'Order Bumps & Settings', 'upsell-order-bump-offer-for-woocommerce' ), 'manage_woocommerce', 'upsell-order-bump-offer-for-woocommerce-setting' );

		/**
		 * Add sub-menu for reportings settings.
		 */
		add_submenu_page( 'upsell-order-bump-offer-for-woocommerce-setting', esc_html__( 'Sales Reports & Analytics', 'upsell-order-bump-offer-for-woocommerce' ), esc_html__( 'Sales Reports & Analytics', 'upsell-order-bump-offer-for-woocommerce' ), 'manage_woocommerce', 'upsell-order-bump-offer-for-woocommerce-reporting', array( $this, 'add_submenu_page_reporting_callback' ) );
	}

	/**
	 * Callable function for upsell bump menu page.
	 *
	 * @since    1.0.0
	 */
	public function wps_ubo_lite_add_backend() {
		$plugin_active = wps_ubo_lite_if_pro_exists();
		if ( $plugin_active ) {

			require_once ABSPATH . 'wp-admin/includes/plugin.php';

			$old_pro_present   = false;
			$installed_plugins = get_plugins();

			if ( array_key_exists( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', $installed_plugins ) ) {
				$pro_plugin = $installed_plugins['upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php'];
				if ( version_compare( $pro_plugin['Version'], '2.1.0', '<' ) ) {
					$old_pro_present = true;
				}
			}

			if ( true === $old_pro_present ) {
				// With org files only.
				require_once plugin_dir_path( __FILE__ ) . '/partials/upsell-order-bump-offer-for-woocommerce-incompatible.php';
				return;
			}

			$wps_upsell_bump_callname_lic         = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_lic_callback_function;
			$wps_upsell_bump_callname_lic_initial = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_lic_ini_callback_function;

			$day_count = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_callname_lic_initial();

			if ( Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_callname_lic() || 0 <= $day_count ) {

				if ( ! Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_callname_lic() && 0 <= $day_count ) :

					$day_count_warning = floor( $day_count );

					// Days warning.
					/* translators: %s is replaced with "days remaining" */
					$day_string = sprintf( _n( '%s day', '%s days', $day_count_warning, 'upsell-order-bump-offer-for-woocommerce' ), number_format_i18n( $day_count_warning ) );

					?>
					<div id="wps-bump-thirty-days-notify" class="notice notice-warning wps-notice">
						<p>
							<strong><a href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=license">

									<!-- License warning. -->
									<?php esc_html_e( 'Activate', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
								<?php
								/* translators: %s is replaced with "days remaining" */
								printf( esc_html__( ' the license key before %s or you may risk losing data and the plugin will also become dysfunctional.', 'upsell-order-bump-offer-for-woocommerce' ), '<span id="wps-upsell-bump-day-count" >' . esc_html( $day_string ) . '</span>' );
								?>
							</strong>
						</p>
					</div>
					<?php

				endif;

				require_once plugin_dir_path( __FILE__ ) . '/partials/upsell-order-bump-offer-for-woocommerce-admin-display.php';
			} else {

				?>
				<div class="wrap woocommerce" id="wps_upsell_bump_setting_wrapper">

					<div class="wps_upsell_bump_setting_title"><?php esc_html_e( 'Upsell Order Bump Offers Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
					<?php
					// Failed Activation.
					include_once UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_DIRPATH . '/admin/partials/templates/wps-upsell-bump-license.php';
					?>
				</div>
				<?php
			}
		} else {

			// With org files only.
			require_once plugin_dir_path( __FILE__ ) . '/partials/upsell-order-bump-offer-for-woocommerce-admin-display.php';
		}
	}

	/**
	 * Reporting and Funnel Stats Sub menu callback.
	 *
	 * @since       1.4.0
	 */
	public function add_submenu_page_reporting_callback() {
		require_once UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_DIR_PATH . 'admin/reporting/upsell-order-bump-reporting-config-panel.php';
	}


	/**
	 * Select2 search for adding bump target products.
	 *
	 * @since    1.0.0
	 */
	public function search_products_for_bump() {
		$secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
		$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

		if ( ! $id_nonce_verified ) {
			wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
		}

		$return         = array();
		$search_results = new WP_Query(
			array(
				's'                   => ! empty( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '',
				'post_type'           => array( 'product', 'product_variation' ),
				'post_status'         => array( 'publish' ),
				'ignore_sticky_posts' => 1,
				'posts_per_page'      => -1,
			)
		);

		if ( $search_results->have_posts() ) :

			while ( $search_results->have_posts() ) :

				$search_results->the_post();

				$title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;

				/**
				 * Check for post type as query sometimes returns posts even after mentioning post_type.
				 * As some plugins alter query which causes issues.
				 */
				$post_type = get_post_type( $search_results->post->ID );

				if ( 'product' !== $post_type && 'product_variation' !== $post_type ) {

					continue;
				}

				$product      = wc_get_product( $search_results->post->ID );
				$downloadable = $product->is_downloadable();
				$stock        = $product->get_stock_status();
				$product_type = $product->get_type();

				$unsupported_product_types = array(
					'grouped',
					'external',
				);

				if ( in_array( $product_type, $unsupported_product_types, true ) || 'outofstock' === $stock ) {

					continue;
				}

				$return[] = array( $search_results->post->ID, $title );

			endwhile;

		endif;

		echo wp_json_encode( $return );

		wp_die();
	}


	/**
	 * Select2 search for adding bump offer products.
	 *
	 * @since    1.0.0
	 */
	public function search_products_for_offers() {
		$secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
		$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

		if ( ! $id_nonce_verified ) {
			wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
		}

		$return         = array();
		$search_results = new WP_Query(
			array(
				's'                   => ! empty( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '',
				'post_type'           => array( 'product', 'product_variation' ),
				'post_status'         => array( 'publish' ),
				'ignore_sticky_posts' => 1,
				'posts_per_page'      => -1,
			)
		);

		if ( $search_results->have_posts() ) :

			while ( $search_results->have_posts() ) :

				$search_results->the_post();

				$title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;

				/**
				 * Check for post type as query sometimes returns posts even after mentioning post_type.
				 * As some plugins alter query which causes issues.
				 */
				$post_type = get_post_type( $search_results->post->ID );

				if ( 'product' !== $post_type && 'product_variation' !== $post_type ) {

					continue;
				}

				$product      = wc_get_product( $search_results->post->ID );
				$downloadable = $product->is_downloadable();
				$stock        = $product->get_stock_status();
				$product_type = $product->get_type();

				$unsupported_product_types = array(
					'grouped',
					'external',
				);

				if ( in_array( $product_type, $unsupported_product_types, true ) || 'outofstock' === $stock ) {

					continue;
				}

				$return[] = array( $search_results->post->ID, $title );

			endwhile;

		endif;

		echo wp_json_encode( $return );

		wp_die();
	}


	/**
	 * Select2 search for adding bump target categories.
	 *
	 * @since    1.0.0
	 */
	public function search_product_categories_for_bump() {
		$secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
		$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

		if ( ! $id_nonce_verified ) {
			wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
		}

		$return = array();
		$args   = array(
			'search'   => ! empty( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '',
			'taxonomy' => 'product_cat',
			'orderby'  => 'name',
		);

		$product_categories = get_terms( $args );

		if ( ! empty( $product_categories ) && is_array( $product_categories ) && count( $product_categories ) ) {

			foreach ( $product_categories as $single_product_category ) {

				$cat_name = ( mb_strlen( $single_product_category->name ) > 50 ) ? mb_substr( $single_product_category->name, 0, 49 ) . '...' : $single_product_category->name;

				$return[] = array( $single_product_category->term_id, $single_product_category->name );
			}
		}

		echo wp_json_encode( $return );

		wp_die();
	}

	/**
	 * Function for showing bump offer price in order table.
	 *
	 * @param   string $column_name        Column name in order review table.
	 * @param   string $post_ID            Order id as the post.
	 * @since    1.0.0
	 */
	public function show_bump_total_content( $column_name, $post_ID ) {

		// Add bump offer price to order total column.
		if ( 'order_total' === $column_name ) {

			// Get order id as post id.
			$order = wc_get_order( $post_ID );

			$order_items = $order->get_items();

			$order_bump_purchased = false;

			if ( ! empty( $order_items ) && is_array( $order_items ) ) {

				$order_bump_item_total = 0;

				foreach ( $order_items as $item_id => $single_item ) {

					if ( ! empty( wc_get_order_item_meta( $item_id, 'is_order_bump_purchase', true ) ) || ! empty( wc_get_order_item_meta( $item_id, 'Bump Offer', true ) ) ) {

						$order_bump_purchased   = true;
						$order_bump_item_total += $single_item->get_total();
					}
				}
			}

			if ( $order_bump_purchased ) :
				?>

				<p class="wps_bump_table_html">
					<?php
					$allowed_html = wps_ubo_lite_allowed_html();
					echo esc_html_e( 'Order Bump: ', 'upsell-order-bump-offer-for-woocommerce' ) . wp_kses( wc_price( $order_bump_item_total ), $allowed_html );
					?>
				</p>

				<?php
			endif;
		}
	}

	/**
	 * Add Upsell Reporting in Woo Admin reports.
	 *
	 * @param string $reports  report type.
	 *
	 * @since       1.4.0
	 */
	public function add_order_bump_reporting( $reports ) {

		$reports['wps_order_bump'] = array(

			'title'   => esc_html__( 'Order Bump', 'upsell-order-bump-offer-for-woocommerce' ),
			'reports' => array(

				'sales_by_date'     => array(
					'title'       => esc_html__( 'Order Bump Sales by date', 'upsell-order-bump-offer-for-woocommerce' ),
					'description' => '',
					'hide_title'  => 1,
					'callback'    => array( 'Upsell_Order_Bump_Offer_For_Woocommerce_Admin', 'order_bump_reporting_callback' ),
				),

				'sales_by_product'  => array(
					'title'       => esc_html__( 'Order Bump Sales by product', 'upsell-order-bump-offer-for-woocommerce' ),
					'description' => '',
					'hide_title'  => 1,
					'callback'    => array( 'Upsell_Order_Bump_Offer_For_Woocommerce_Admin', 'order_bump_reporting_callback' ),
				),

				'sales_by_category' => array(
					'title'       => esc_html__( 'Order Bump Sales by category', 'upsell-order-bump-offer-for-woocommerce' ),
					'description' => '',
					'hide_title'  => 1,
					'callback'    => array( 'Upsell_Order_Bump_Offer_For_Woocommerce_Admin', 'order_bump_reporting_callback' ),
				),
			),
		);

		return $reports;
	}

	/**
	 * Add custom report. callback.
	 *
	 * @param string $report_type  report type.
	 *
	 * @since       1.4.0
	 */
	public static function order_bump_reporting_callback( $report_type ) {

		$report_file      = ! empty( $report_type ) ? str_replace( '_', '-', $report_type ) : '';
		$preformat_string = ! empty( $report_type ) ? ucwords( str_replace( '_', ' ', $report_type ) ) : '';
		$class_name       = ! empty( $preformat_string ) ? 'Wps_Upsell_Order_Bump_Report_' . str_replace( ' ', '_', $preformat_string ) : '';

		/**
		 * The file responsible for defining reporting.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'reporting/class-wps-upsell-order-bump-report-' . $report_file . '.php';

		if ( class_exists( $class_name ) ) {

			$report = new $class_name();
			$report->output_report();
		} else {

			?>
			<div class="wps_ubo_report_error_wrap" style="text-align: center;">
				<h2 class="wps_ubo_report_error_text">
					<?php esc_html_e( 'Some Error Occured while creating report.', 'upsell-order-bump-offer-for-woocommerce' ); ?>
				</h2>
			</div>
			<?php
		}
	}

	/**
	 * Include Order Bump screen for Onboarding pop-up.
	 *
	 * @param array $valid_screens  report type.
	 * @since    1.4.0
	 */
	public function add_wps_frontend_screens( $valid_screens = array() ) {

		if ( is_array( $valid_screens ) ) {

			// Push your screen here.
			array_push( $valid_screens, 'toplevel_page_upsell-order-bump-offer-for-woocommerce-setting' );
		}

		return $valid_screens;
	}

	/**
	 * Include Order Bump plugin for Deactivation pop-up.
	 *
	 * @param array $valid_screens  report type.
	 * @since    1.4.0
	 */
	public function add_wps_deactivation_screens( $valid_screens = array() ) {

		if ( is_array( $valid_screens ) ) {

			// Push your screen here.
			array_push( $valid_screens, 'upsell-order-bump-offer-for-woocommerce' );
			array_push( $valid_screens, 'upsell-order-bump-offer-for-woocommerce-pro' );
		}

		return $valid_screens;
	}

	/**
	 * Validate Pro version compatibility.
	 *
	 * @since    1.4.0
	 */
	public function validate_version_compatibility() {
		$result = wps_ubo_lite_pro_version_incompatible();

		// When Pro version in incompatible.
		if ( 'incompatible' === $result ) {

			set_transient( 'wps_ubo_lite_pro_version_incompatible', 'true' );

			// Deactivate Pro Plugin.
			add_action( 'admin_init', array( $this, 'deactivate_pro_plugin' ) );
		} elseif ( 'compatible' === $result && 'true' === get_transient( 'wps_ubo_lite_pro_version_incompatible' ) ) {  // When Pro version in compatible and transient is set.

			delete_transient( 'wps_ubo_lite_pro_version_incompatible' );
		}

		if ( 'true' === get_transient( 'wps_ubo_lite_pro_version_incompatible' ) ) {

			// Deactivate Pro Plugin admin notice.
			add_action( 'admin_notices', array( $this, 'deactivate_pro_admin_notice' ) );
		}
	}

	/**
	 * Deactivate Pro Plugin.
	 *
	 * @since    1.4.0
	 */
	public function deactivate_pro_plugin() {
		$secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
		$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

		if ( ! $id_nonce_verified ) {
			wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
		}

		// To hide Plugin activated notice.
		if ( ! empty( $_GET['activate'] ) ) {

			unset( $_GET['activate'] );
		}

		deactivate_plugins( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' );
	}

	/**
	 * Deactivate Pro Plugin admin notice.
	 *
	 * @since    1.4.0
	 */
	public function deactivate_pro_admin_notice() {
		$screen = get_current_screen();

		$valid_screens = array(
			'toplevel_page_upsell-order-bump-offer-for-woocommerce-setting',
			'order-bump_page_upsell-order-bump-offer-for-woocommerce-reporting',
			'plugins',
		);

		if ( ! empty( $screen->id ) && in_array( $screen->id, $valid_screens, true ) ) :
			?>

			<div class="notice notice-error is-dismissible wps-notice">
				<p><strong><?php esc_html_e( 'Upsell Order Bump Offer for WooCommerce Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong> <?php esc_html_e( 'is deactivated, Please Update the PRO version as this version is outdated and will not work with the current', 'upsell-order-bump-offer-for-woocommerce' ); ?><strong> <?php esc_html_e( 'Upsell Order Bump Offer for WooCommerce', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong> <?php esc_html_e( 'Free version.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
			</div>

			<?php
		endif;
	}

	/**
	 * Add custom image upload.
	 *
	 * @param mixed $image_post_id image post id.
	 * @since       3.0.0
	 */
	public static function wps_ubo_image_uploader_field( $image_post_id = '' ) {

		// Image present!
		if ( ! empty( $image_post_id ) ) {

			// $image_attributes[0] - Image URL.
			// $image_attributes[1] - Image width.
			// $image_attributes[2] - Image height.
			$image_attributes = wp_get_attachment_image_src( $image_post_id, 'thumbnail' );
			?>
			<div class="wps_wocuf_saved_custom_image">
				<a href="#" class="wps_ubo_upload_image_button"><img src="<?php echo esc_url( $image_attributes[0] ); ?>" style="max-width:150px;display:block;"></a>
				<input type="hidden" name="wps_upsell_offer_image" id="wps_upsell_offer_image" value="<?php echo esc_attr( $image_post_id ); ?>">
				<a href="#" class="wps_ubo_remove_image_button button" style="display:inline-block;margin-top: 10px;display:inline-block;"><?php esc_html_e( 'Remove Image', 'upsell-order-bump-offer-for-woocommerce-pro' ); ?></a>
			</div>
			<?php

		} else {
			// Image not present!
			?>
			<div class="wps_wocuf_saved_custom_image">
				<a href="#" class="wps_ubo_upload_image_button button"><?php esc_html_e( 'Upload image', 'upsell-order-bump-offer-for-woocommerce-pro' ); ?></a>
				<input type="hidden" name="wps_upsell_offer_image" id="wps_upsell_offer_image" value="<?php echo esc_attr( $image_post_id ); ?>">
				<a href="#" class="wps_ubo_remove_image_button button" style="display:inline-block;margin-top: 10px;display:none;"><?php esc_html_e( 'Remove Image', 'upsell-order-bump-offer-for-woocommerce-pro' ); ?></a>
			</div>
			<?php
		}
	}

	/**
	 * Function to migrate db keys.
	 *
	 * @return void
	 */
	public function wps_migrate_db_keys() {
		if ( 'deleted' !== get_option( 'mwb_ubo_bump_list', 'deleted' ) ) {
			$wps_ubo_global_options_copy = str_replace( 'mwb', 'wps', wp_json_encode( get_option( 'mwb_ubo_global_options' ) ) );
			$wps_ubo_bump_list_copy      = str_replace( 'mwb', 'wps', wp_json_encode( get_option( 'mwb_ubo_bump_list' ) ) );

			update_option( 'wps_ubo_global_options', json_decode( $wps_ubo_global_options_copy, true ) );
			update_option( 'wps_ubo_bump_list', json_decode( $wps_ubo_bump_list_copy, true ) );
			delete_option( 'mwb_ubo_global_options' );
			delete_option( 'mwb_ubo_bump_list' );

			$meta_keys = array(
				'mwb_upsell_bump_license_key',
				'mwb_upsell_bump_license_check',
				'mwb_upsell_bump_activated_timestamp',
			);

			foreach ( $meta_keys as $key => $meta_key ) {
				$new_meta_key = str_replace( 'mwb', 'wps', $meta_key );
				$meta_value   = get_option( $meta_key, '' );
				update_option( $new_meta_key, $meta_value );
				delete_option( $meta_key );
			}
		}

		$this->import_postmeta( 'mwb_bump_order' );
		$this->import_postmeta( 'mwb_bump_order_process_sales_stats' );
	}

	/**
	 * Migration for postmeta.
	 *
	 * @param string $meta_key meta key to import.
	 * @since       3.1.4
	 */
	public function import_postmeta( $meta_key = false ) {

		if ( ! empty( $meta_key ) ) {

			$new_meta_key = str_replace( 'mwb', 'wps', $meta_key );

			global $wpdb;
			wp_cache_delete( 'wps_migration_keys' );
			if ( empty( wp_cache_get( 'wps_migration_keys' ) || ! empty( wp_cache_get( 'wps_migration_keys' ) ) ) ) {

				$result = $wpdb->get_results( // phpcs:ignore.
					$wpdb->prepare(
						'UPDATE ' . $wpdb->prefix . 'postmeta SET `meta_key` = %s WHERE `meta_key` = %s',
						$new_meta_key,
						$meta_key
					),
					ARRAY_A
				);

				wp_cache_set(
					'wps_migration_keys',
					$result
				);
			}
		}
	}

	/**
	 * Set The Cron For The Banner Image.
	 *
	 * @since 2.2.8
	 */
	public function wps_uob_set_cron_for_plugin_notification() {
		$wps_sfw_offset = get_option( 'gmt_offset' );
		$wps_sfw_time   = time() + $wps_sfw_offset * 60 * 60;
		if ( ! wp_next_scheduled( 'wps_wgm_check_for_notification_update' ) ) {
			wp_schedule_event( $wps_sfw_time, 'daily', 'wps_wgm_check_for_notification_update' );
		}
	}

	/**
	 * Set The Message For The Banner Image.
	 *
	 * @since 2.2.8
	 */
	public function wps_uob_save_notice_message() {
		 $wps_notification_data = $this->wps_uob_get_update_notification_data();
		if ( is_array( $wps_notification_data ) && ! empty( $wps_notification_data ) ) {
			$banner_id      = array_key_exists( 'notification_id', $wps_notification_data[0] ) ? $wps_notification_data[0]['wps_banner_id'] : '';
			$banner_image = array_key_exists( 'notification_message', $wps_notification_data[0] ) ? $wps_notification_data[0]['wps_banner_image'] : '';
			$banner_url = array_key_exists( 'notification_message', $wps_notification_data[0] ) ? $wps_notification_data[0]['wps_banner_url'] : '';
			$banner_type = array_key_exists( 'notification_message', $wps_notification_data[0] ) ? $wps_notification_data[0]['wps_banner_type'] : '';
			update_option( 'wps_wgm_notify_new_banner_id', $banner_id );
			update_option( 'wps_wgm_notify_new_banner_image', $banner_image );
			update_option( 'wps_wgm_notify_new_banner_url', $banner_url );
			if ( 'regular' == $banner_type ) {
				update_option( 'wps_wgm_notify_hide_baneer_notification', '' );
			}
		}
	}


	/**
	 * Callback For The Banner Image.
	 *
	 * @since 2.2.8
	 */
	public function wps_uob_get_update_notification_data() {
		$wps_notification_data = array();
		$url                   = 'https://demo.wpswings.com/client-notification/woo-gift-cards-lite/wps-client-notify.php';
		$attr                  = array(
			'action'         => 'wps_notification_fetch',
			'plugin_version' => UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_VERSION,
		);
		$query                 = esc_url_raw( add_query_arg( $attr, $url ) );
		$response              = wp_remote_get(
			$query,
			array(
				'timeout'   => 20,
				'sslverify' => false,
			)
		);

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo '<p><strong>Something went wrong: ' . esc_html( stripslashes( $error_message ) ) . '</strong></p>';
		} else {
			$wps_notification_data = json_decode( wp_remote_retrieve_body( $response ), true );
		}
		return $wps_notification_data;
	}

	/**
	 * Ajax Callback For The Banner Image.
	 *
	 * @since 2.2.8
	 */
	public function wps_uob_dismiss_notice_banner_callback() {
		if ( isset( $_REQUEST['wps_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['wps_nonce'] ) ), 'wps_admin_nonce' ) ) {

			$banner_id = get_option( 'wps_wgm_notify_new_banner_id', false );

			if ( isset( $banner_id ) && '' != $banner_id ) {
				update_option( 'wps_wgm_notify_hide_baneer_notification', $banner_id );
			}

			wp_send_json_success();
		}
	}

	/**
	 * Ajax Callback For One Click Upsell.
	 *
	 * @since 2.2.8
	 */
	public function wps_install_and_redirect_upsell_plugin_callback() {
		$secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
		$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

		if ( ! $id_nonce_verified ) {
			wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
		}
		wp_send_json_success( array( 'redirect_url' => 'https://wordpress.org/plugins/woo-one-click-upsell-funnel/' ) );
		wp_die();

	}
} // End of class.

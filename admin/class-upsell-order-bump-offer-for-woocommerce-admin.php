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
			'upsell-funnel-builder_page_upsell-order-bump-offer-for-woocommerce-pre-reporting',
			'upsell-funnel-builder_page_upsell-order-bump-offer-for-woocommerce-post-reporting',
			'upsell-order-bump-offer-for-woocommerce-abandoned-cart-reporting',
			'upsell-funnel-builder_page_upsell-order-bump-offer-for-woocommerce-abandoned-cart-reporting',
		);

		$screen = get_current_screen();
		$pagescreen = $screen->id;

		if ( isset( $screen->id ) ) {

			if ( in_array( $pagescreen, $valid_screens, true ) ) {

				wp_register_style( 'wps_ubo_lite_admin_style', plugin_dir_url( __FILE__ ) . 'css/upsell-order-bump-offer-for-woocommerce-admin.css', array(), time(), 'all' );

				wp_enqueue_style( 'wps_ubo_lite_admin_style' );

				wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );

				wp_enqueue_style( 'woocommerce_admin_menu_styles' );

				wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), time() );

				wp_enqueue_style( 'woocommerce_admin_styles' );

				wp_enqueue_style( 'wp-color-picker' );

				wp_register_style( 'wps_wocuf_pro_admin_style', plugin_dir_url( __FILE__ ) . 'css/woocommerce_one_click_upsell_funnel_pro-admin.css', array(), $this->version, 'all' );

				wp_register_style( 'wps_ubo_lite_admin_new_style', plugin_dir_url( __FILE__ ) . 'css/upsell-order-bump-offer-for-woocommerce-new-admin.css', array(), time(), 'all' );

				wp_enqueue_style( 'wps_ubo_lite_admin_new_style' );
			}
		}

		if ( $screen && 'product' === $screen->post_type && 'post' === $screen->base ) {

			wp_register_style( 'wps_ubo_lite_admin_product_edit_style', plugin_dir_url( __FILE__ ) . 'css/upsell-order-bump-offer-for-woocommerce-product-edit-admin.css', array(), time(), 'all' );

			wp_enqueue_style( 'wps_ubo_lite_admin_product_edit_style' );
		}

		if ( 'woocommerce_page_wc-settings' === $pagescreen ) {
			wp_register_style( 'wps_wocuf_pro_banner_admin_style', plugin_dir_url( __FILE__ ) . 'css/woocommerce_one_click_upsell_funnel_pro_banner_payment.css', array(), $this->version, 'all' );

			wp_enqueue_style( 'wps_wocuf_pro_banner_admin_style' );
		}

		if ( isset( $screen->id ) && 'product' == $screen->id ) {

			wp_register_style( 'woocommerce_one_click_upsell_funnel_product_shipping', plugin_dir_url( __FILE__ ) . 'css/woocommerce_one_click_upsell_funnel_product_shipping.css', array(), $this->version, 'all' );

			wp_enqueue_style( 'woocommerce_one_click_upsell_funnel_product_shipping' );
		}

		wp_register_style( 'wps_ubo_lite_admin_style-global', plugin_dir_url( __FILE__ ) . 'css/upsell-order-bump-offer-for-woocommerce-admin-global.css', array(), time(), 'all' );

		wp_enqueue_style( 'wps_ubo_lite_admin_style-global' );
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

		$valid_screens = array(
			'toplevel_page_upsell-order-bump-offer-for-woocommerce-setting',
			'order-bump_page_upsell-order-bump-offer-for-woocommerce-reporting',
			'plugins',
			'upsell-funnel-builder_page_upsell-order-bump-offer-for-woocommerce-pre-reporting',
			'upsell-funnel-builder_page_upsell-order-bump-offer-for-woocommerce-post-reporting',
			'woocommerce_page_wc-settings',
		);

		if ( isset( $screen->id ) ) {

			$pagescreen = $screen->id;

			if ( in_array( $pagescreen, $valid_screens, true ) ) {

				$wps_plugin_list = get_option( 'active_plugins' );
				$wps_is_pro_active = false;
				$wps_plugin = 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php';
				if ( in_array( $wps_plugin, $wps_plugin_list ) ) {
					$wps_is_pro_active = true;
				}
				wp_enqueue_script( $this->plugin_name . '_masonry_effects', plugin_dir_url( __FILE__ ) . 'js/masonry_effects.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->plugin_name . '_sweet_alert', plugin_dir_url( __FILE__ ) . 'js/swal.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array( 'jquery'  , 'wp-element'), time(), false );

				wp_enqueue_script(
						'wps-combined-reports', 
						plugin_dir_url( __FILE__ ) . 'js/wps-upsell-combined-reports.js', // Adjust path as needed
						array( 'wp-element', 'jquery' ), // Dependencies
						'1.0.0', 
						true // Load in footer
					);


				wp_enqueue_script( 'wps_ubo_lite_admin_script', plugin_dir_url( __FILE__ ) . 'js/upsell-order-bump-offer-for-woocommerce-admin.js', array( 'jquery' ), time(), false );
				wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip', 'wc-enhanced-select', 'dompurify' ), WC_VERSION, false );
				wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.js', array( 'jquery', 'dompurify' ), WC_VERSION, true );
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

				// Get all funnels.
				if ( wps_is_plugin_active_with_version( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '3.0.0' ) ) {
					$funnels_list = get_option( 'wps_wocuf_pro_funnels_list', array() );
				} else {
					$funnels_list = get_option( 'wps_wocuf_funnels_list', array() );
				}

				wp_localize_script(
					'wps_ubo_lite_admin_script',
					'wps_ubo_lite_banner_offer_section_obj',
					array(
						'ajaxurl'    => admin_url( 'admin-ajax.php' ),
						'nonce'   => wp_create_nonce( 'wps_ubo_labels' ),
						'auth_nonce' => wp_create_nonce( 'wps_admin_nonce' ),
						'check_pro_activate'     => ! wps_upsell_funnel_builder_is_pdf_pro_plugin_active(),
						'wps_all_order_bump_data' => get_option( 'wps_ubo_bump_list', array() ),
						'wps_post_funnels_list'    => $funnels_list,
						'is_pro_active' => wps_ubo_lite_is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ),
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
						'one_click_funnel_nonce' => wp_create_nonce( 'wps_wocuf_nonce' ),
						'screen_id' => $screen->id,
						'funnel_builder_pro_is_enable' => wps_is_plugin_active_with_version( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '3.0.0' ),
					)
				);

				wp_enqueue_script( 'wps_ubo_lite_sticky_js', plugin_dir_url( __FILE__ ) . 'js/jquery.sticky-sidebar.js', array( 'jquery' ), $this->version, false );

				wp_enqueue_script( 'wps_ubo_lite_sweet-alert-2-js', plugin_dir_url( __FILE__ ) . 'js/sweet-alert.js', array( 'jquery' ), $this->version, false );

				if ( 'toplevel_page_upsell-order-bump-offer-for-woocommerce-setting' === $pagescreen && function_exists( 'wp_enqueue_code_editor' ) ) {
					$wps_ubo_css_editor_settings = wp_enqueue_code_editor(
						array(
							'type' => 'text/css',
						)
					);
					$wps_ubo_js_editor_settings  = wp_enqueue_code_editor(
						array(
							'type' => 'application/javascript',
						)
					);

					if ( $wps_ubo_css_editor_settings || $wps_ubo_js_editor_settings ) {
						wp_enqueue_script( 'code-editor' );
						wp_enqueue_style( 'wp-codemirror' );

						$wps_ubo_editor_init  = 'jQuery(function($){if(typeof wp !== "undefined" && wp.codeEditor){';
						$wps_ubo_editor_init .= 'var wpsCssSettings=' . ( $wps_ubo_css_editor_settings ? wp_json_encode( $wps_ubo_css_editor_settings ) : 'null' ) . ';';
						$wps_ubo_editor_init .= 'var wpsJsSettings=' . ( $wps_ubo_js_editor_settings ? wp_json_encode( $wps_ubo_js_editor_settings ) : 'null' ) . ';';
						$wps_ubo_editor_init .= 'document.querySelectorAll(".wps-ubo-code-field").forEach(function(field){var type=(field.getAttribute("data-code-type")||"css").toLowerCase();var settings="js"===type?wpsJsSettings:wpsCssSettings;if(!settings){return;}wp.codeEditor.initialize(field,settings);});';
						$wps_ubo_editor_init .= '}});';

						wp_add_inline_script( 'code-editor', $wps_ubo_editor_init );
					}
				}
			}
		}

		wp_enqueue_script( 'jquery-ui-droppable' );
		wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip', 'wc-enhanced-select' ), WC_VERSION, false );
		wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.js', array( 'jquery', 'dompurify' ), WC_VERSION, true );
	}


	/**
	 * Adding upsell bump menu page.
	 *
	 * @since    1.0.0
	 */
	public function wps_ubo_lite_admin_menu() {
		add_menu_page(
			esc_html__( 'Upsell Funnel Builder', 'upsell-order-bump-offer-for-woocommerce' ),
			esc_html__( 'Upsell Funnel Builder', 'upsell-order-bump-offer-for-woocommerce' ),
			'manage_woocommerce',
			'upsell-order-bump-offer-for-woocommerce-setting',
			array( $this, 'wps_ubo_lite_add_backend' ),
			plugin_dir_url( __FILE__ ) . 'resources/OFB-wordpress-icon.svg',
			57
		);

		/**
		 * Add sub-menu for funnel settings.
		 */
		add_submenu_page( 'upsell-order-bump-offer-for-woocommerce-setting', esc_html__( 'Upsell Funnel Builder & Settings', 'upsell-order-bump-offer-for-woocommerce' ), esc_html__( 'Upsell Funnel Builder & Settings', 'upsell-order-bump-offer-for-woocommerce' ), 'manage_woocommerce', 'upsell-order-bump-offer-for-woocommerce-setting' );

		/**
		 * Add sub-menu for order bump reportings settings.
		 */
		// Saved Global Options.
		$wps_ubo_global_options = get_option( 'wps_ubo_global_options', array() );
		$wps_abandoned_cart_enable = ! empty( $wps_ubo_global_options['wps_ubo_abandoned_cart'] ) ? $wps_ubo_global_options['wps_ubo_abandoned_cart'] : 'no';
		if ( is_plugin_active( 'woo-cart-abandonment-recovery/woo-cart-abandonment-recovery.php' ) && is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) && 'yes' === $wps_abandoned_cart_enable ) {
			add_submenu_page(
				'upsell-order-bump-offer-for-woocommerce-setting',
				esc_html__( 'Abandoned Cart Bump List', 'upsell-order-bump-offer-for-woocommerce' ),
				esc_html__( 'Abandoned Cart Bump List', 'upsell-order-bump-offer-for-woocommerce' ),
				'manage_woocommerce',
				'upsell-order-bump-offer-for-woocommerce-abandoned-cart-reporting', // UNIQUE SLUG.
				array( $this, 'pre_add_submenu_page_reporting_callback_pro' )
			);
		}

		/**
		 * Add sub-menu for one click upsell reportings settings.
		 */
		add_submenu_page(
			'upsell-order-bump-offer-for-woocommerce-setting',
			esc_html__( 'Order Bump Sales Reports & Analytics', 'upsell-order-bump-offer-for-woocommerce' ),
			esc_html__( 'Order Bump Sales Reports & Analytics', 'upsell-order-bump-offer-for-woocommerce' ),
			'manage_woocommerce',
			'upsell-order-bump-offer-for-woocommerce-pre-reporting', // UNIQUE SLUG.
			array( $this, 'pre_add_submenu_page_reporting_callback' )
		);

		/**
		 * Add sub-menu for one click upsell reportings settings.
		 */
		add_submenu_page(
			'upsell-order-bump-offer-for-woocommerce-setting',
			esc_html__( 'Upsell Funnel Sales Reports & Analytics', 'upsell-order-bump-offer-for-woocommerce' ),
			esc_html__( 'Upsell Funnel Sales Reports & Analytics', 'upsell-order-bump-offer-for-woocommerce' ),
			'manage_woocommerce',
			'upsell-order-bump-offer-for-woocommerce-post-reporting', // UNIQUE SLUG.
			array( $this, 'post_add_submenu_page_reporting_callback' )
		);
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
	public function pre_add_submenu_page_reporting_callback() {
		 require_once UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_DIR_PATH . 'admin/reporting/upsell-order-bump-reporting-config-panel.php';
	}



	/**
	 * Reporting and Funnel Stats Sub menu callback.
	 *
	 * @since       1.4.0
	 */
	public function post_add_submenu_page_reporting_callback() {
		require_once UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_DIR_PATH . 'admin/reporting/upsell-reporting-and-tracking-config-panel.php';
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
	 * Select2 search for adding bump offer products.
	 *
	 * @since    1.0.0
	 */
	public function search_products_for_bump_offer_id() {
		$secure_nonce = wp_create_nonce( 'wps-upsell-auth-nonce' );
		$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

		if ( ! $id_nonce_verified ) {
			wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
		}

		$return = array();

		// Retrieve the data stored in the options table.
		$saved_products = get_option( 'wps_ubo_bump_list', array() ); // Replace 'wps_saved_products' with your actual option name.
		$search_query   = ! empty( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';

		// Check if the option data exists and is an array.
		if ( is_array( $saved_products ) && ! empty( $saved_products ) ) {
			foreach ( $saved_products as $key => $value ) {

				$title = ( mb_strlen( $value['wps_upsell_bump_name'] ) > 50 ) ? mb_substr( $value['wps_upsell_bump_name'], 0, 49 ) . '...' : $value['wps_upsell_bump_name'];

				// Add a search filter if needed.
				if ( $search_query && stripos( $title, $search_query ) === false ) {
					continue;
				}

				$return[] = array( $key, $title );
			}
		}

		echo wp_json_encode( $return );
		wp_die();
	}


	/**
	 * Select2 search for adding bump offer coupon;
	 *
	 * @since    1.0.0
	 */
	public function search_coupon_for_offers() {
		$secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
		$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

		if ( ! $id_nonce_verified ) {
			wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
		}

		$return         = array();
		$search_results = new WP_Query(
			array(
				's'                   => ! empty( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '',
				'post_type'           => 'shop_coupon',
				'post_status'         => array( 'publish' ),
				'ignore_sticky_posts' => 1,
				'posts_per_page'      => -1,
			)
		);

		if ( $search_results->have_posts() ) :

			while ( $search_results->have_posts() ) :

				$search_results->the_post();

				$title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;

				// Ensure the post type is 'shop_coupon'.
				if ( 'shop_coupon' !== get_post_type( $search_results->post->ID ) ) {
					continue;
				}

				$coupon = new WC_Coupon( $search_results->post->ID );

				// Additional checks can be added here if needed.

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
		require_once plugin_dir_path( __DIR__ ) . 'reporting/class-wps-upsell-order-bump-report-' . $report_file . '.php';

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
			array_push( $valid_screens, 'upsell-order-bump-offer-for-woocommerce' );
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
				<a href="#" class="wps_ubo_remove_image_button button" style="display:inline-block;margin-top: 10px;display:inline-block;"><?php esc_html_e( 'Remove Image', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
			</div>
			<?php

		} else {
			// Image not present!
			?>
			<div class="wps_wocuf_saved_custom_image">
				<a href="#" class="wps_ubo_upload_image_button button"><?php esc_html_e( 'Upload image', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
				<input type="hidden" name="wps_upsell_offer_image" id="wps_upsell_offer_image" value="<?php echo esc_attr( $image_post_id ); ?>">
				<a href="#" class="wps_ubo_remove_image_button button" style="display:inline-block;margin-top: 10px;display:none;"><?php esc_html_e( 'Remove Image', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
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
				update_option( 'wps_wgm_notify_hide_baneer_notification', 0 );
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
		wp_send_json_success( array( 'redirect_url' => admin_url( 'plugin-install.php?s=Upsell%2520Order%2520Bump%2520Offer%2520for%2520WooCommerce%2520%25E2%2580%2593%2520Increase%2520Sales%2520and%2520AOV%252C%2520Upsell%2520%2526%2520Cross-sell%2520Offers%2520on%2520Checkout%2520Page%2520%2520wp%2520swings&tab=search&type=term' ) ) );
		wp_die();
	}


	/**
	 * Select2 search for adding funnel target products
	 *
	 * @since    1.0.0
	 */
	public function seach_products_for_funnel() {
		$return = array();

		$secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
		$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

		if ( ! $id_nonce_verified ) {
			wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
		}

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
	 * Select2 search for adding funnel target product categories
	 *
	 * @since    3.0.1
	 */
	public function search_product_categories_for_funnel() {
		$return = array();

		$secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
		$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

		if ( ! $id_nonce_verified ) {
			wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
		}

		$args = array(
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
	 * Select2 search for adding offer products.
	 *
	 * @since    1.0.0
	 */
	public function seach_products_for_offers() {
		$return = array();

		$secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
		$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

		if ( ! $id_nonce_verified ) {
			wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
		}

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
	 * Dismiss Elementor inactive notice.
	 *
	 * @since       2.0.0
	 */
	public function dismiss_elementor_inactive_notice() {
		set_transient( 'wps_upsell_elementor_inactive_notice', 'notice_dismissed' );

		wp_die();
	}


	/**
	 * Hide Upsell offer pages in admin panel 'Pages'.
	 *
	 * @param mixed $query query.
	 * @since       2.0.0
	 */
	public function hide_upsell_offer_pages_in_admin( $query ) {

		// Make sure we're in the admin and it's the main query.
		if ( ! is_admin() && ! $query->is_main_query() ) {
			return;
		}

		global $typenow;

		// Only do this for pages.
		if ( ! empty( $typenow ) && 'page' === $typenow ) {

			$saved_offer_post_ids = get_option( 'wps_upsell_lite_offer_post_ids', array() );

			if ( ! empty( $saved_offer_post_ids ) && is_array( $saved_offer_post_ids ) && count( $saved_offer_post_ids ) ) {

				// Don't show the special pages.
				$query->set( 'post__not_in', $saved_offer_post_ids );

				return;
			}
		}
	}


	/**
	 * Adding distraction free mode to the offers page.
	 *
	 * @since       1.0.0
	 * @param mixed $page_template default template for the page.
	 */
	public function wps_wocuf_pro_page_template( $page_template ) {

		$pages_available = get_posts(
			array(
				'posts_per_page' => -1,
				'post_type'      => 'any',
				'post_status'    => 'publish',
				's'              => '[wps_wocuf_pro_funnel_default_offer_page]',
				'orderby'        => 'ID',
				'order'          => 'ASC',
			)
		);

		foreach ( $pages_available as $single_page ) {

			if ( is_page( $single_page->ID ) ) {

				$page_template = __DIR__ . '/partials/templates/wps-wocuf-pro-template.php';
			}
		}

		return $page_template;
	}



	/**
	 * Offer Html for appending in funnel when add new offer is clicked - ajax handle function.
	 * Also Dynamic page post is created while adding new offer.
	 *
	 * @since    1.0.0
	 */
	public function return_funnel_offer_section_content() {
		 check_ajax_referer( 'wps_wocuf_nonce', 'nonce' );

		if ( isset( $_POST['wps_wocuf_pro_flag'] ) && isset( $_POST['wps_wocuf_pro_funnel'] ) ) {

			// New Offer id.
			$offer_index = sanitize_text_field( wp_unslash( $_POST['wps_wocuf_pro_flag'] ) );
			// Funnel id.
			$funnel_id = sanitize_text_field( wp_unslash( $_POST['wps_wocuf_pro_funnel'] ) );

			unset( $_POST['wps_wocuf_pro_flag'] );
			unset( $_POST['wps_wocuf_pro_funnel'] );

			$funnel_offer_post_html = '<input type="hidden" name="wps_upsell_post_id_assigned[' . $offer_index . ']" value="">';

			$funnel_offer_template_section_html = '';
			$funnel_offer_post_id               = '';

			if ( wps_upsell_lite_elementor_plugin_active_funnel_builder() || wps_upsell_divi_builder_plugin_active_funnel_builder() ) {

				// Create post for corresponding funnel and offer id.
				$funnel_offer_post_id = wp_insert_post(
					array(
						'comment_status' => 'closed',
						'ping_status'    => 'closed',
						'post_content'   => '',
						'post_name'      => uniqid( 'special-offer-' ), // post slug.
						'post_title'     => 'Special Offer',
						'post_status'    => 'publish',
						'post_type'      => 'page',
						'page_template'  => 'elementor_canvas',
					)
				);

				if ( $funnel_offer_post_id ) {

					if ( wps_upsell_lite_elementor_plugin_active_funnel_builder() ) {
						$elementor_data = wps_upsell_lite_elementor_offer_template_1_funnel_builder();
						update_post_meta( $funnel_offer_post_id, '_elementor_data', $elementor_data );
						update_post_meta( $funnel_offer_post_id, '_elementor_edit_mode', 'builder' );
					} elseif ( wps_upsell_divi_builder_plugin_active_funnel_builder() ) {

						delete_post_meta( $funnel_offer_post_id, '_elementor_css' );
						delete_post_meta( $funnel_offer_post_id, '_elementor_data' );
						global $post;
						$get_post_contents = '[et_pb_section fb_built="1" _builder_version="4.18.1" _module_preset="default" global_colors_info="{}"][et_pb_row column_structure="1_2,1_2" make_equal="on" _builder_version="4.18.1" _module_preset="default" custom_css_main_element="align-items: center" global_colors_info="{}"][et_pb_column type="1_2" _builder_version="4.18.1" _module_preset="default" global_colors_info="{}"][wps_upsell_image][/et_pb_column][et_pb_column type="1_2" _builder_version="4.18.1" _module_preset="default" global_colors_info="{}"][et_pb_text _builder_version="4.18.1" _module_preset="default" header_font="|700|||||||" header_text_color="#000000" header_font_size="40px" header_line_height="1.9em" header_2_font="|600|||||||" header_2_text_color="#000000" header_2_font_size="36px" header_2_line_height="1.6em" header_5_font="|700|||||||" header_5_text_color="#000000" header_5_line_height="2.3em" global_colors_info="{}"]<h2>[wps_upsell_title]</h2>
						<p>[wps_upsell_desc]</p>
						<h5>EXPIRING SOON</h5>
						<h1>[wps_upsell_price]</h1>[/et_pb_text][et_pb_code _builder_version="4.18.1" _module_preset="default" global_colors_info="{}"]<style><!-- [et_pb_line_break_holder] -->  .custom-btn{<!-- [et_pb_line_break_holder] -->    background-color: #3ebf2e;<!-- [et_pb_line_break_holder] -->    padding: 14px 50px;<!-- [et_pb_line_break_holder] -->    color: #ffffff;<!-- [et_pb_line_break_holder] -->    display: inline-block;<!-- [et_pb_line_break_holder] -->    <!-- [et_pb_line_break_holder] -->  }<!-- [et_pb_line_break_holder] --></style><!-- [et_pb_line_break_holder] --><a href="[wps_upsell_yes]" style="background-color: #3ebf2e; padding: 10px 28px; display: inline-block; color: #fff; border-radius: 5px; margin-right: 20px; font-weight: 600;">ADD THIS TO MY ORDER</a><a href="[wps_upsell_no]" style="color: #05063d; text-decoration: underline;">No, I’m not interested</a>[/et_pb_code][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fb_built="1" _builder_version="4.18.1" _module_preset="default" custom_padding="||0px||false|false" global_colors_info="{}"][et_pb_row _builder_version="4.18.1" _module_preset="default" global_colors_info="{}"][et_pb_column type="4_4" _builder_version="4.18.1" _module_preset="default" global_colors_info="{}"][et_pb_text _builder_version="4.18.1" _module_preset="default" header_3_font="|600|||||||" header_3_text_color="#000000" header_3_font_size="28px" width="61%" module_alignment="center" global_colors_info="{}"]<h3 style="text-align: center;">Amazing Features</h3>
						<div>
						<div style="text-align: center;"><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Tristique sit ut id cursus bibendum et. At ut odio tincidunt ipsum hac amet.Lorem</span></div>
						</div>[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fb_built="1" _builder_version="4.18.1" _module_preset="default" custom_padding="0px||||false|false" global_colors_info="{}"][et_pb_row column_structure="1_3,1_3,1_3" _builder_version="4.18.1" _module_preset="default" global_colors_info="{}"][et_pb_column type="1_3" _builder_version="4.18.1" _module_preset="default" global_colors_info="{}"][et_pb_text _builder_version="4.18.1" _module_preset="default" header_3_font="|600|||||||" header_3_text_color="#000000" header_3_font_size="24px" header_3_line_height="2em" global_colors_info="{}"]<h3 style="text-align: center;">Features #1</h3>
						<p style="text-align: center;">Your content goes here. Edit or remove this text inline or in the module Content settings. You can also style every aspect of this content.</p>[/et_pb_text][/et_pb_column][et_pb_column type="1_3" _builder_version="4.18.1" _module_preset="default" global_colors_info="{}"][et_pb_text _builder_version="4.18.1" _module_preset="default" header_3_font="|600|||||||" header_3_text_color="#000000" header_3_font_size="24px" header_3_line_height="2em" global_colors_info="{}"]<h3 style="text-align: center;">Features #1</h3>
						<p style="text-align: center;">Your content goes here. Edit or remove this text inline or in the module Content settings. You can also style every aspect of this content.</p>[/et_pb_text][/et_pb_column][et_pb_column type="1_3" _builder_version="4.18.1" _module_preset="default" global_colors_info="{}"][et_pb_text _builder_version="4.18.1" _module_preset="default" header_3_font="|600|||||||" header_3_text_color="#000000" header_3_font_size="24px" header_3_line_height="2em" global_colors_info="{}"]<h3 style="text-align: center;">Features #1</h3>
						<p style="text-align: center;">Your content goes here. Edit or remove this text inline or in the module Content settings. You can also style every aspect of this content.</p>[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fb_built="1" _builder_version="4.18.1" _module_preset="default" hover_enabled="0" global_colors_info="{}" sticky_enabled="0"][et_pb_row _builder_version="4.18.1" _module_preset="default" custom_css_main_element="text-align: center" width="500px" hover_enabled="0" sticky_enabled="0" border_radii="on|7px|7px|7px|7px" border_color_all="#c6c6c6" box_shadow_style="preset1" custom_padding="40px|30px|40px|30px|false|false"][et_pb_column _builder_version="4.18.1" _module_preset="default" type="4_4"][et_pb_text _builder_version="4.18.1" _module_preset="default" custom_css_main_element="text-align: center;" hover_enabled="0" sticky_enabled="0" header_3_font_size="21px" header_3_font="|700|||||||" header_3_line_height="0.6em"]<div style="display: flex; justify-content: center; margin-bottom: 15px;"><svg width="22" height="20" viewbox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.5245 0.463526C10.6741 0.00287054 11.3259 0.00287005 11.4755 0.463525L13.5819 6.9463C13.6488 7.15232 13.8408 7.2918 14.0574 7.2918H20.8738C21.3582 7.2918 21.5596 7.9116 21.1677 8.1963L15.6531 12.2029C15.4779 12.3302 15.4046 12.5559 15.4715 12.7619L17.5779 19.2447C17.7276 19.7053 17.2003 20.0884 16.8085 19.8037L11.2939 15.7971C11.1186 15.6698 10.8814 15.6698 10.7061 15.7971L5.19153 19.8037C4.79967 20.0884 4.27243 19.7053 4.42211 19.2447L6.52849 12.7619C6.59542 12.5559 6.5221 12.3302 6.34685 12.2029L0.832272 8.1963C0.440415 7.9116 0.641802 7.2918 1.12616 7.2918H7.94256C8.15917 7.2918 8.35115 7.15232 8.41809 6.9463L10.5245 0.463526Z" fill="#FDD600"></path></svg><br /><svg width="22" height="20" viewbox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.5245 0.463526C10.6741 0.00287054 11.3259 0.00287005 11.4755 0.463525L13.5819 6.9463C13.6488 7.15232 13.8408 7.2918 14.0574 7.2918H20.8738C21.3582 7.2918 21.5596 7.9116 21.1677 8.1963L15.6531 12.2029C15.4779 12.3302 15.4046 12.5559 15.4715 12.7619L17.5779 19.2447C17.7276 19.7053 17.2003 20.0884 16.8085 19.8037L11.2939 15.7971C11.1186 15.6698 10.8814 15.6698 10.7061 15.7971L5.19153 19.8037C4.79967 20.0884 4.27243 19.7053 4.42211 19.2447L6.52849 12.7619C6.59542 12.5559 6.5221 12.3302 6.34685 12.2029L0.832272 8.1963C0.440415 7.9116 0.641802 7.2918 1.12616 7.2918H7.94256C8.15917 7.2918 8.35115 7.15232 8.41809 6.9463L10.5245 0.463526Z" fill="#FDD600"></path></svg><br /><svg width="22" height="20" viewbox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.5245 0.463526C10.6741 0.00287054 11.3259 0.00287005 11.4755 0.463525L13.5819 6.9463C13.6488 7.15232 13.8408 7.2918 14.0574 7.2918H20.8738C21.3582 7.2918 21.5596 7.9116 21.1677 8.1963L15.6531 12.2029C15.4779 12.3302 15.4046 12.5559 15.4715 12.7619L17.5779 19.2447C17.7276 19.7053 17.2003 20.0884 16.8085 19.8037L11.2939 15.7971C11.1186 15.6698 10.8814 15.6698 10.7061 15.7971L5.19153 19.8037C4.79967 20.0884 4.27243 19.7053 4.42211 19.2447L6.52849 12.7619C6.59542 12.5559 6.5221 12.3302 6.34685 12.2029L0.832272 8.1963C0.440415 7.9116 0.641802 7.2918 1.12616 7.2918H7.94256C8.15917 7.2918 8.35115 7.15232 8.41809 6.9463L10.5245 0.463526Z" fill="#FDD600"></path></svg><br /><svg width="22" height="20" viewbox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.5245 0.463526C10.6741 0.00287054 11.3259 0.00287005 11.4755 0.463525L13.5819 6.9463C13.6488 7.15232 13.8408 7.2918 14.0574 7.2918H20.8738C21.3582 7.2918 21.5596 7.9116 21.1677 8.1963L15.6531 12.2029C15.4779 12.3302 15.4046 12.5559 15.4715 12.7619L17.5779 19.2447C17.7276 19.7053 17.2003 20.0884 16.8085 19.8037L11.2939 15.7971C11.1186 15.6698 10.8814 15.6698 10.7061 15.7971L5.19153 19.8037C4.79967 20.0884 4.27243 19.7053 4.42211 19.2447L6.52849 12.7619C6.59542 12.5559 6.5221 12.3302 6.34685 12.2029L0.832272 8.1963C0.440415 7.9116 0.641802 7.2918 1.12616 7.2918H7.94256C8.15917 7.2918 8.35115 7.15232 8.41809 6.9463L10.5245 0.463526Z" fill="#FDD600"></path></svg><br /><svg width="11" height="19" viewbox="0 0 11 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.19161 18.8037L10.794 14.7333C10.9235 14.6393 11.0001 14.4889 11.0001 14.3288V1.15688C11.0001 0.587488 10.2005 0.460849 10.0246 1.00237L8.41817 5.9463C8.35123 6.15232 8.15925 6.2918 7.94264 6.2918H1.12624C0.641882 6.2918 0.440495 6.9116 0.832352 7.1963L6.34693 11.2029C6.52218 11.3302 6.59551 11.5559 6.52857 11.7619L4.42219 18.2447C4.27251 18.7053 4.79975 19.0884 5.19161 18.8037Z" fill="#FDD600"></path></svg></div>
						<p style="text-align: center;">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Tristique sit ut id cursus bibendum et. At ut odio tincidunt ipsum hac amet.Lorem</p>
						<h3 style="text-align: center;">JANE AUSTIN</h3>
						<p style="text-align: center;">FASHON BLOGGER</p>[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fb_built="1" theme_builder_area="post_content" _builder_version="4.18.1" _module_preset="default"][et_pb_row _builder_version="4.18.1" _module_preset="default" column_structure="1_3,1_3,1_3" theme_builder_area="post_content"][et_pb_column _builder_version="4.18.1" _module_preset="default" type="1_3" theme_builder_area="post_content"][et_pb_text _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" hover_enabled="0" sticky_enabled="0"]<h3>Fast Delivery</h3>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Tristique sit ut id cursus bibendum et. At ut odio tincidunt ipsum hac amet.Lorem</p>[/et_pb_text][/et_pb_column][et_pb_column _builder_version="4.18.1" _module_preset="default" type="1_3" theme_builder_area="post_content"][et_pb_text _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" hover_enabled="0" sticky_enabled="0"]<h3>Fast Delivery</h3>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Tristique sit ut id cursus bibendum et. At ut odio tincidunt ipsum hac amet.Lorem</p>[/et_pb_text][/et_pb_column][et_pb_column _builder_version="4.18.1" _module_preset="default" type="1_3" theme_builder_area="post_content"][et_pb_text _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" hover_enabled="0" sticky_enabled="0"]<h3>Fast Delivery</h3>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Tristique sit ut id cursus bibendum et. At ut odio tincidunt ipsum hac amet.Lorem</p>[/et_pb_text][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fb_built="1" theme_builder_area="post_content" _builder_version="4.18.1" _module_preset="default" custom_padding="0px||0px||false|false" hover_enabled="0" sticky_enabled="0"][et_pb_row _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content"][et_pb_column _builder_version="4.18.1" _module_preset="default" type="4_4" theme_builder_area="post_content"][et_pb_text _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" hover_enabled="0" sticky_enabled="0" header_5_font_size="12px" header_2_font="|700|||||||" header_2_font_size="31px" header_2_text_color="#000000"]<h5 style="text-align: center;">QUALITY YOU CAN TRUST</h5>
					<h2 style="text-align: center;">Porduct details</h2>[/et_pb_text][et_pb_tabs _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" custom_css_main_element="border: solid 0px||" custom_css_tabs_controls="  background-color: transparent;||  display: flex;||||" custom_css_tab="border: solid 0px;||margin-bottom: 0px;||color: #B8822C !important;||font-size: 24px" custom_css_active_tab="background-color: transparent;||color: #B8822C !important;" hover_enabled="0" sticky_enabled="0"][et_pb_tab title="info" _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" hover_enabled="0" sticky_enabled="0"]<p>Your content goes here. Edit or remove this text inline or in the module Content settings. You can also style every aspect of this content in the module Design settings and even apply custom CSS to this text in the module Advanced settings.</p>[/et_pb_tab][et_pb_tab title="Size" _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" hover_enabled="0" sticky_enabled="0"]<p>Your content goes here. Edit or remove this text inline or in the module Content settings. You can also style every aspect of this content in the module Design settings and even apply custom CSS to this text in the module Advanced settings.</p>[/et_pb_tab][et_pb_tab title="order" _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" hover_enabled="0" sticky_enabled="0"]<p>Your content goes here. Edit or remove this text inline or in the module Content settings. You can also style every aspect of this content in the module Design settings and even apply custom CSS to this text in the module Advanced settings.</p>[/et_pb_tab][/et_pb_tabs][/et_pb_column][/et_pb_row][/et_pb_section][et_pb_section fb_built="1" theme_builder_area="post_content" _builder_version="4.18.1" _module_preset="default" disabled_on="off|off|off" hover_enabled="0" sticky_enabled="0"][et_pb_row _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content"][et_pb_column _builder_version="4.18.1" _module_preset="default" type="4_4" theme_builder_area="post_content"][et_pb_text _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" header_2_font="|700|||||||" header_2_text_color="#000000" header_2_font_size="48px" hover_enabled="0" sticky_enabled="0"]<h2 style="text-align: center;"><strong>[wps_upsell_price]</strong></h2>[/et_pb_text][/et_pb_column][/et_pb_row][et_pb_row _builder_version="4.18.1" _module_preset="default" column_structure="1_6,1_6,1_6,1_6,1_6,1_6" theme_builder_area="post_content" hover_enabled="0" sticky_enabled="0" custom_css_main_element="display: flex;" width="500px" custom_padding="0px||0px||false|false"][et_pb_column _builder_version="4.18.1" _module_preset="default" type="1_6" theme_builder_area="post_content" hover_enabled="0" sticky_enabled="0"][et_pb_icon _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" font_icon="&#xf1f3;||fa||400" hover_enabled="0" sticky_enabled="0" icon_width="60px" icon_color="#848484"][/et_pb_icon][/et_pb_column][et_pb_column _builder_version="4.18.1" _module_preset="default" type="1_6" theme_builder_area="post_content"][et_pb_icon _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" font_icon="&#xf1f0;||fa||400" hover_enabled="0" sticky_enabled="0" icon_width="60px" icon_color="#848484"][/et_pb_icon][/et_pb_column][et_pb_column _builder_version="4.18.1" _module_preset="default" type="1_6" theme_builder_area="post_content"][et_pb_icon _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" font_icon="&#xf1f2;||fa||400" hover_enabled="0" sticky_enabled="0" icon_width="60px" icon_color="#848484"][/et_pb_icon][/et_pb_column][et_pb_column _builder_version="4.18.1" _module_preset="default" type="1_6" theme_builder_area="post_content"][et_pb_icon _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" font_icon="&#xf1f4;||fa||400" hover_enabled="0" sticky_enabled="0" icon_width="60px" icon_color="#848484"][/et_pb_icon][/et_pb_column][et_pb_column _builder_version="4.18.1" _module_preset="default" type="1_6" theme_builder_area="post_content"][et_pb_icon _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" font_icon="&#xf1f5;||fa||400" hover_enabled="0" sticky_enabled="0" icon_width="60px" icon_color="#848484"][/et_pb_icon][/et_pb_column][et_pb_column _builder_version="4.18.1" _module_preset="default" type="1_6" theme_builder_area="post_content"][et_pb_icon _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" font_icon="&#xf1f1;||fa||400" hover_enabled="0" sticky_enabled="0" icon_width="60px" icon_color="#848484"][/et_pb_icon][/et_pb_column][/et_pb_row][et_pb_row _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content"][et_pb_column _builder_version="4.18.1" _module_preset="default" type="4_4" theme_builder_area="post_content"][et_pb_code _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" hover_enabled="0" sticky_enabled="0"]<style><!-- [et_pb_line_break_holder] -->  .custom-btn-full{<!-- [et_pb_line_break_holder] -->    width: 100%;<!-- [et_pb_line_break_holder] -->    text-align: center;<!-- [et_pb_line_break_holder] -->        border-radius: 5px;<!-- [et_pb_line_break_holder] -->  }.custom-btn-full-not{<!-- [et_pb_line_break_holder] -->    width: 80%;background:red;<!-- [et_pb_line_break_holder] -->    text-align: center;<!-- [et_pb_line_break_holder] -->        border-radius: 5px;<!-- [et_pb_line_break_holder] -->  }<!-- [et_pb_line_break_holder] --></style><!-- [et_pb_line_break_holder] --><a href="[wps_upsell_yes]" class="custom-btn custom-btn-full">Add This To My Order</a>[/et_pb_code][/et_pb_column][/et_pb_row][et_pb_row _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" width="48%" hover_enabled="0" sticky_enabled="0"][et_pb_column _builder_version="4.18.1" _module_preset="default" type="4_4" theme_builder_area="post_content"][et_pb_text _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" hover_enabled="0" sticky_enabled="0"]<p style="text-align: center;">Your content goes here. Edit or remove this text inline or in the module Content settings. You can also style every aspect of this content in the module Design settings and even apply custom CSS to this text in the module Advanced settings.</p>[/et_pb_text][et_pb_code _builder_version="4.18.1" _module_preset="default" theme_builder_area="post_content" hover_enabled="0" sticky_enabled="0"]<style><!-- [et_pb_line_break_holder] -->  .custom-btn-half{<!-- [et_pb_line_break_holder] -->    width: 100%;<!-- [et_pb_line_break_holder] -->    text-align: center;<!-- [et_pb_line_break_holder] -->        border-radius: 5px;<!-- [et_pb_line_break_holder] -->        background-color: #f00;<!-- [et_pb_line_break_holder] -->  }<!-- [et_pb_line_break_holder] --></style><!-- [et_pb_line_break_holder] --><a href="[wps_upsell_no]" class="custom-btn custom-btn-full-not">No, I’m not interested</a>[/et_pb_code][/et_pb_column][/et_pb_row][/et_pb_section]}';

						$my_post = array();
						$my_post['ID'] = $funnel_offer_post_id;
						$my_post['post_content'] = $get_post_contents;
						wp_update_post( $my_post );
						// Delete temporary meta key.
						delete_post_meta( $funnel_offer_post_id, 'divi_content' );
					}

					$wps_upsell_funnel_data = array(
						'funnel_id' => $funnel_id,
						'offer_id'  => $offer_index,
					);

					update_post_meta( $funnel_offer_post_id, 'wps_upsell_funnel_data', $wps_upsell_funnel_data );

					$funnel_offer_post_html = '<input type="hidden" name="wps_upsell_post_id_assigned[' . $offer_index . ']" value="' . $funnel_offer_post_id . '">';

					$funnel_offer_template_section_html = $this->get_funnel_offer_template_section_html( $funnel_offer_post_id, $offer_index, $funnel_id );

					// Save an array of all created upsell offer-page post ids.
					$upsell_offer_post_ids = get_option( 'wps_upsell_lite_offer_post_ids', array() );

					$upsell_offer_post_ids[] = $funnel_offer_post_id;

					update_option( 'wps_upsell_lite_offer_post_ids', $upsell_offer_post_ids );
				}
			} else { // When Elementor is not active.

				// Will return 'Feature not supported' part as $funnel_offer_post_id is empty.
				$funnel_offer_template_section_html = $this->get_funnel_offer_template_section_html( $funnel_offer_post_id, $offer_index, $funnel_id );
			}

			// Get all funnels.
			$wps_wocuf_pro_funnel = get_option( 'wps_wocuf_funnels_list' );

			// Funnel offers array.
			$wps_wocuf_pro_offers_to_add = isset( $wps_wocuf_pro_funnel[ $funnel_id ]['wps_wocuf_applied_offer_number'] ) ? $wps_wocuf_pro_funnel[ $funnel_id ]['wps_wocuf_applied_offer_number'] : array();

			// Buy now action select html.
			$buy_now_action_select_html = '<select name="wps_wocuf_attached_offers_on_buy[' . $offer_index . ']"><option value="thanks">' . esc_html__( 'Order ThankYou Page', 'upsell-order-bump-offer-for-woocommerce' ) . '</option>';

			// No thanks action select html.
			$no_thanks_action_select_html = '<select name="wps_wocuf_attached_offers_on_no[' . $offer_index . ']"><option value="thanks">' . esc_html__( 'Order ThankYou Page', 'upsell-order-bump-offer-for-woocommerce' ) . '</option>';

			// If there are other offers then add them to select html.
			if ( ! empty( $wps_wocuf_pro_offers_to_add ) ) {

				foreach ( $wps_wocuf_pro_offers_to_add as $offer_id ) {

					$buy_now_action_select_html .= '<option value=' . $offer_id . '>' . esc_html__( 'Offer #', 'upsell-order-bump-offer-for-woocommerce' ) . $offer_id . '</option>';

					$no_thanks_action_select_html .= '<option value=' . $offer_id . '>' . esc_html__( 'Offer #', 'upsell-order-bump-offer-for-woocommerce' ) . $offer_id . '</option>';
				}
			}

			$buy_now_action_select_html   .= '</select>';
			$no_thanks_action_select_html .= '</select>';

			$offer_scroll_id_val = "#offer-section-$offer_index";

			$allowed_html = wps_upsell_lite_allowed_html_funnel_builder();

			$data = '<div style="display:none;" data-id="' . $offer_index . '" data-scroll-id="' . $offer_scroll_id_val . '" class="new_created_offers wps_upsell_single_offer">
			<h2 class="wps_upsell_offer_title">' . esc_html__( 'Offer #', 'upsell-order-bump-offer-for-woocommerce' ) . $offer_index . '</h2>
			<table>
			<tr>
			<th><label><h4>' . esc_html__( 'Offer Product', 'upsell-order-bump-offer-for-woocommerce' ) . '</h4></label></th>
			<td><select class="wc-offer-product-search wps_upsell_offer_product" name="wps_wocuf_products_in_offer[' . $offer_index . ']" data-placeholder="' . esc_html__( 'Search for a product&hellip;', 'upsell-order-bump-offer-for-woocommerce' ) . '"></select></td>
			</tr>
			<tr>
			<th><label><h4>' . esc_html__( 'Offer Price / Discount', 'upsell-order-bump-offer-for-woocommerce' ) . '</h4></label></th>
			<td>
			<input type="text" class="wps_upsell_offer_price" name="wps_wocuf_offer_discount_price[' . $offer_index . ']" value="50%" >
			<span class="wps_upsell_offer_description" >' . esc_html__( 'Specify new offer price or discount %', 'upsell-order-bump-offer-for-woocommerce' ) . '</span>
			</td>
			<tr>
				<th><label><h4>' . esc_html__( 'Offer Image', 'upsell-order-bump-offer-for-woocommerce' ) . '</h4></label>
				</th>
				<td>' . $this->wps_wocuf_pro_image_uploader_field( $offer_index ) . '</td>
			</tr>
			</tr>
			<tr>
			<th><label><h4>' . esc_html__( 'After \'Buy Now\' go to', 'upsell-order-bump-offer-for-woocommerce' ) . '</h4></label></th>
			<td>' . $buy_now_action_select_html . '<span class="wps_upsell_offer_description">' . esc_html__( 'Select where the customer will be redirected after accepting this offer', 'upsell-order-bump-offer-for-woocommerce' ) . '</span></td>
			</tr>
			<tr>
			<th><label><h4>' . esc_html__( 'After \'No thanks\' go to', 'upsell-order-bump-offer-for-woocommerce' ) . '</h4></label></th>
			<td>' . $no_thanks_action_select_html . '<span class="wps_upsell_offer_description">' . esc_html__( 'Select where the customer will be redirected after rejecting this offer', 'upsell-order-bump-offer-for-woocommerce' ) . '</td>
			</tr>' . $funnel_offer_template_section_html . '
			<tr>
			<th><label><h4>' . esc_html__( 'Offer Custom Page Link', 'upsell-order-bump-offer-for-woocommerce' ) . '</h4></label></th>
			<td>
			<input type="text" class="wps_upsell_custom_offer_page_url" name="wps_wocuf_offer_custom_page_url[' . $offer_index . ']" >
			</td>
			</tr>
			<tr>
			<td colspan="2">
			<button class="button wps_wocuf_pro_delete_new_created_offers" data-id="' . $offer_index . '">' . esc_html__( 'Remove', 'upsell-order-bump-offer-for-woocommerce' ) . '</button>
			</td>
			</tr>
			</table>
			<input type="hidden" name="wps_wocuf_applied_offer_number[' . $offer_index . ']" value="' . $offer_index . '">
			' . $funnel_offer_post_html . '</div>';

			$new_data = apply_filters( 'wps_wocuf_pro_add_more_to_offers', $data );

			echo wp_kses( $new_data, $allowed_html );
			// It just displayes the html itself. Content in it is already escaped if required.
		}

		wp_die();
	}


	/**
	 * Returns Funnel Offer Template section html.
	 *
	 * @param mixed $funnel_offer_post_id funnel offer post id.
	 * @param mixed $offer_index offer index.
	 * @param mixed $funnel_id funnel id.
	 * @since    2.0.0
	 */
	public function get_funnel_offer_template_section_html( $funnel_offer_post_id, $offer_index, $funnel_id ) {

		ob_start();

		?>

		<!-- Section : Offer template start -->
		<tr>
			<th><label>
					<h4><?php esc_html_e( 'Offer Template', 'upsell-order-bump-offer-for-woocommerce' ); ?></h4>
				</label>
			</th>
			<?php
			$assigned_post_id        = ! empty( $funnel_offer_post_id ) ? $funnel_offer_post_id : '';
			$current_offer_id        = $offer_index;
			$wps_wocuf_pro_funnel_id = $funnel_id;

			?>
			<td>

				<?php if ( ! empty( $assigned_post_id ) ) : ?>

					<?php
					// As default is "one".
					$offer_template_active = 'one';

					$offer_templates_array = array(
						'one'   => esc_html__( 'STANDARD TEMPLATE', 'upsell-order-bump-offer-for-woocommerce' ),
						'two'   => esc_html__( 'CREATIVE TEMPLATE', 'upsell-order-bump-offer-for-woocommerce' ),
						'three' => esc_html__( 'VIDEO TEMPLATE', 'upsell-order-bump-offer-for-woocommerce' ),
					);

					?>

					<!-- Offer templates parent div start -->
					<div class="wps_upsell_offer_templates_parent">

						<input class="wps_wocuf_pro_offer_template_input" type="hidden" name="wps_wocuf_pro_offer_template[<?php echo esc_html( $current_offer_id ); ?>]" value="<?php echo esc_html( $offer_template_active ); ?>">

						<?php

						foreach ( $offer_templates_array as $template_key => $template_name ) :

							?>
							<!-- Offer templates foreach start-->

							<div class="wps_upsell_offer_template 
							<?php
							echo $template_key === $offer_template_active ? 'active' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							// It just displayes the html itself. Content in it is already escaped if required.
							?>
							">
								<div class="wps_upsell_offer_template_sub_div">

									<h5><?php echo esc_html( $template_name ); ?></h5>

									<div class="wps_upsell_offer_preview">

										<?php
										if ( 'one' == $template_key || 'two' == $template_key || 'three' == $template_key ) {

											if ( wps_upsell_divi_builder_plugin_active_funnel_builder() ) {
												?>
												<a href="javascript:void(0)" class="wps_upsell_view_offer_template" data-template-id="<?php echo esc_html( $template_key ); ?>"><img src="<?php echo esc_url( WPS_WOCUF_URL_FUNNEL_BUILDER . "admin/resources/offer-thumbnails/divi/offer-template-$template_key.png" ); ?>"></a>
												<?php
											} else {
												?>
												<a href="javascript:void(0)" class="wps_upsell_view_offer_template" data-template-id="<?php echo esc_html( $template_key ); ?>"><img src="<?php echo esc_url( WPS_WOCUF_URL_FUNNEL_BUILDER . "admin/resources/offer-thumbnails/offer-template-$template_key.jpg" ); ?>"></a>
												<?php

											}
										} else {

											?>
											<a href="javascript:void(0)" class="wps_upsell_view_offer_template" data-template-id="<?php echo esc_html( $template_key ); ?>"><img src="<?php echo esc_url( WPS_WOCUF_URL_FUNNEL_BUILDER . "admin/resources/offer-thumbnails/offer-template-$template_key.png" ); ?>"></a>
											<?php

										}

										?>


									</div>

									<div class="wps_upsell_offer_action">

										<?php if ( (string) $template_key !== (string) $offer_template_active ) : ?>

											<button class="button-primary wps_upsell_activate_offer_template" data-template-id="<?php echo esc_html( $template_key ); ?>" data-offer-id="<?php echo esc_html( $current_offer_id ); ?>" data-funnel-id="<?php echo esc_html( $wps_wocuf_pro_funnel_id ); ?>" data-offer-post-id="<?php echo esc_html( $assigned_post_id ); ?>"><?php esc_html_e( 'Insert and Activate', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>

										<?php else : ?>

											<a class="button" href="<?php echo esc_url( get_permalink( $assigned_post_id ) ); ?>" target="_blank"><?php esc_html_e( 'View &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>


											<?php
											if ( ! wps_upsell_divi_builder_plugin_active_funnel_builder() ) {
												?>
												<a class="button" href="<?php echo esc_url( admin_url( "post.php?post=$assigned_post_id&action=elementor" ) ); ?>" target="_blank"><?php esc_html_e( 'Customize &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

												<?php
											}
											?>

										<?php endif; ?>
									</div>
								</div>
							</div>
							<!-- Offer templates foreach end-->
							<?php
						endforeach;

						if ( wps_upsell_lite_elementor_plugin_active_funnel_builder() || wps_upsell_divi_builder_plugin_active_funnel_builder() ) {
							?>

							<!-- Offer templates 4 foreach start-->

							<div class="wps_upsell_offer_template ">

								<div class="wps_upsell_offer_template_sub_div">

									<h5> <?php esc_html_e( 'FITNESS TEMPLATE', 'upsell-order-bump-offer-for-woocommerce' ); ?></h5>

									<div class="wps_upsell_offer_preview">

										<a href="javascript:void(0)" class="wps_upsell_view_offer_template" data-template-id="four">
											<span class="wps_wupsell_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><img src="<?php echo esc_url( WPS_WOCUF_URL_FUNNEL_BUILDER . 'admin/resources/offer-thumbnails/offer-template-four.png' ); ?>"></a>
									</div>

									<div class="wps_upsell_offer_action">

										<?php if ( $template_key !== $offer_template_active ) : ?>

											<input type="button" class=" wps_upsell_activate_offer_template_pro ubo_offer_input" value="<?php esc_html_e( 'Upgrade To Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?>" />


										<?php else : ?>

											<a class="button" href="<?php echo esc_url( get_permalink( $assigned_post_id ) ); ?>" target="_blank"><?php esc_html_e( 'View &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

											<?php
											if ( ! wps_upsell_divi_builder_plugin_active_funnel_builder() ) {
												?>
												<a class="button" href="<?php echo esc_url( admin_url( "post.php?post=$assigned_post_id&action=elementor" ) ); ?>" target="_blank"><?php esc_html_e( 'Customize &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

												<?php
											}
											?>
										<?php endif; ?>
									</div>
								</div>
							</div>

							<!-- Offer templates 4 foreach start-->


							<!-- Offer templates 5 foreach start-->

							<div class="wps_upsell_offer_template ">

								<div class="wps_upsell_offer_template_sub_div">

									<h5> <?php esc_html_e( 'PET SHOP TEMPLATE', 'upsell-order-bump-offer-for-woocommerce' ); ?></h5>

									<div class="wps_upsell_offer_preview">

										<a href="javascript:void(0)" class="wps_upsell_view_offer_template" data-template-id="five">
											<span class="wps_wupsell_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><img src="<?php echo esc_url( WPS_WOCUF_URL_FUNNEL_BUILDER . 'admin/resources/offer-thumbnails/offer-template-five.png' ); ?>"></a>
									</div>

									<div class="wps_upsell_offer_action">

										<?php if ( $template_key !== $offer_template_active ) : ?>

											<input type="button" class=" wps_upsell_activate_offer_template_pro ubo_offer_input" value="<?php esc_html_e( 'Upgrade To Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?>" />


										<?php else : ?>

											<a class="button" href="<?php echo esc_url( get_permalink( $assigned_post_id ) ); ?>" target="_blank"><?php esc_html_e( 'View &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

											<?php
											if ( ! wps_upsell_divi_builder_plugin_active_funnel_builder() ) {
												?>
												<a class="button" href="<?php echo esc_url( admin_url( "post.php?post=$assigned_post_id&action=elementor" ) ); ?>" target="_blank"><?php esc_html_e( 'Customize &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

												<?php
											}
											?>
										<?php endif; ?>
									</div>
								</div>
							</div>

							<!-- Offer templates 5 foreach start-->


							<!-- Offer templates 6 foreach start-->

							<div class="wps_upsell_offer_template ">

								<div class="wps_upsell_offer_template_sub_div">

									<h5> <?php esc_html_e( 'ROSE PINK TEMPLATE', 'upsell-order-bump-offer-for-woocommerce' ); ?></h5>

									<div class="wps_upsell_offer_preview">

										<a href="javascript:void(0)" class="wps_upsell_view_offer_template" data-template-id="six">
											<span class="wps_wupsell_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><img src="<?php echo esc_url( WPS_WOCUF_URL_FUNNEL_BUILDER . 'admin/resources/offer-thumbnails/offer-template-six.png' ); ?>"></a>
									</div>

									<div class="wps_upsell_offer_action">

										<?php if ( $template_key !== $offer_template_active ) : ?>

											<input type="button" class=" wps_upsell_activate_offer_template_pro ubo_offer_input" value="<?php esc_html_e( 'Upgrade To Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?>" />


										<?php else : ?>

											<a class="button" href="<?php echo esc_url( get_permalink( $assigned_post_id ) ); ?>" target="_blank"><?php esc_html_e( 'View &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

											<?php
											if ( ! wps_upsell_divi_builder_plugin_active_funnel_builder() ) {
												?>
												<a class="button" href="<?php echo esc_url( admin_url( "post.php?post=$assigned_post_id&action=elementor" ) ); ?>" target="_blank"><?php esc_html_e( 'Customize &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

												<?php
											}
											?>
										<?php endif; ?>
									</div>
								</div>
							</div>

							<!-- Offer templates 6 foreach start-->






							<!-- Offer templates 7 foreach start-->

							<div class="wps_upsell_offer_template ">

								<div class="wps_upsell_offer_template_sub_div">

									<h5> <?php esc_html_e( 'BEAUTY & MAKEUP TEMPLATE', 'upsell-order-bump-offer-for-woocommerce' ); ?></h5>

									<div class="wps_upsell_offer_preview">

										<a href="javascript:void(0)" class="wps_upsell_view_offer_template" data-template-id="seven">
											<span class="wps_wupsell_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><img src="<?php echo esc_url( WPS_WOCUF_URL_FUNNEL_BUILDER . 'admin/resources/offer-thumbnails/offer-template-seven.png' ); ?>"></a>
									</div>

									<div class="wps_upsell_offer_action">

										<?php if ( $template_key !== $offer_template_active ) : ?>

											<input type="button" class=" wps_upsell_activate_offer_template_pro ubo_offer_input" value="<?php esc_html_e( 'Upgrade To Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?>" />


										<?php else : ?>

											<a class="button" href="<?php echo esc_url( get_permalink( $assigned_post_id ) ); ?>" target="_blank"><?php esc_html_e( 'View &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

											<?php
											if ( ! wps_upsell_divi_builder_plugin_active_funnel_builder() ) {
												?>
												<a class="button" href="<?php echo esc_url( admin_url( "post.php?post=$assigned_post_id&action=elementor" ) ); ?>" target="_blank"><?php esc_html_e( 'Customize &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

												<?php
											}
											?>
										<?php endif; ?>
									</div>
								</div>
							</div>

							<!-- Offer templates 7 foreach start-->


							<!-- Offer templates 8 foreach start-->

							<div class="wps_upsell_offer_template ">

								<div class="wps_upsell_offer_template_sub_div">

									<h5> <?php esc_html_e( 'BEAUTY & MAKEUP TEMPLATE', 'upsell-order-bump-offer-for-woocommerce' ); ?></h5>

									<div class="wps_upsell_offer_preview">

										<a href="javascript:void(0)" class="wps_upsell_view_offer_template" data-template-id="eight">
											<span class="wps_wupsell_premium_strip"><?php esc_html_e( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><img src="<?php echo esc_url( WPS_WOCUF_URL_FUNNEL_BUILDER . 'admin/resources/offer-thumbnails/offer-template-eight.png' ); ?>"></a>
									</div>

									<div class="wps_upsell_offer_action">

										<?php if ( $template_key !== $offer_template_active ) : ?>

											<input type="button" class=" wps_upsell_activate_offer_template_pro ubo_offer_input" value="<?php esc_html_e( 'Upgrade To Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?>" />


										<?php else : ?>

											<a class="button" href="<?php echo esc_url( get_permalink( $assigned_post_id ) ); ?>" target="_blank"><?php esc_html_e( 'View &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

											<?php
											if ( ! wps_upsell_divi_builder_plugin_active_funnel_builder() ) {
												?>
												<a class="button" href="<?php echo esc_url( admin_url( "post.php?post=$assigned_post_id&action=elementor" ) ); ?>" target="_blank"><?php esc_html_e( 'Customize &rarr;', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

												<?php
											}
											?>
										<?php endif; ?>
									</div>
								</div>
							</div>

							<!-- Offer templates 8 foreach start-->
							<?php
						}
						?>

						<!-- Offer link to custom page start-->
						<div class="wps_upsell_offer_template wps_upsell_custom_page_link_div <?php echo esc_html( 'custom' === $offer_template_active ? 'active' : '' ); ?>">

							<h5><?php esc_html_e( 'LINK TO CUSTOM PAGE', 'upsell-order-bump-offer-for-woocommerce' ); ?></h5>

							<?php if ( 'custom' !== $offer_template_active ) : ?>

								<button class="button-primary wps_upsell_activate_offer_template" data-template-id="custom" data-offer-id="<?php echo esc_html( $current_offer_id ); ?>" data-funnel-id="<?php echo esc_html( $wps_wocuf_pro_funnel_id ); ?>" data-offer-post-id="<?php echo esc_html( $assigned_post_id ); ?>"><?php esc_html_e( 'Activate', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>

							<?php else : ?>

								<h5><?php esc_html_e( 'Activated', 'upsell-order-bump-offer-for-woocommerce' ); ?></h5>
								<p><?php esc_html_e( 'Please enter and save your custom page link below.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>

							<?php endif; ?>
						</div>
						<!-- Offer link to custom page end-->
					</div>
					<!-- Offer templates parent div end -->

				<?php else : ?>

					<div class="wps_upsell_offer_template_unsupported">
						<h4><?php esc_html_e( 'Please activate Elementor/Divi Theme if you want to use our Pre-defined Templates, else make a custom page yourself and add link below.', 'upsell-order-bump-offer-for-woocommerce' ); ?></h4>
					</div>

				<?php endif; ?>
			</td>
		</tr>
		<!-- Section : Offer template end -->

		<?php

		return ob_get_clean();
	}



	/**
	 * Add custom image upload.
	 *
	 * @param mixed $hidden_field_index hidden field index.
	 * @param mixed $image_post_id image post id.
	 * @since       3.0.0
	 */
	public function wps_wocuf_pro_image_uploader_field( $hidden_field_index, $image_post_id = '' ) {

		$image   = ' button">' . esc_html__( 'Upload image', 'upsell-order-bump-offer-for-woocommerce' );
		$display = 'none'; // Display state ot the "Remove image" button.

		if ( ! empty( $image_post_id ) ) {

			// $image_attributes[0] - Image URL.
			// $image_attributes[1] - Image width.
			// $image_attributes[2] - Image height.
			$image_attributes = wp_get_attachment_image_src( $image_post_id, 'thumbnail' );

			$image   = '"><img src="' . $image_attributes[0] . '" style="max-width:150px;display:block;" />';
			$display = 'inline-block';
		}

		return '<div class="wps_wocuf_saved_custom_image">
		<a href="#" class="wps_wocuf_pro_upload_image_button' . $image . '</a>
		<input type="hidden" name="wps_upsell_offer_image[' . $hidden_field_index . ']" id="wps_upsell_offer_image_for_' . $hidden_field_index . '" value="' . esc_attr( $image_post_id ) . '" />
		<a href="#" class="wps_wocuf_pro_remove_image_button button" style="margin-top: 10px;display:' . $display . '">Remove image</a>
		</div>';
	}


	/**
	 * Insert and Activate respective template ajax handle function.
	 *
	 * @since    2.0.0
	 */
	public function activate_respective_offer_template() {
		check_ajax_referer( 'wps_wocuf_nonce', 'nonce' );

		$funnel_id     = isset( $_POST['funnel_id'] ) ? sanitize_text_field( wp_unslash( $_POST['funnel_id'] ) ) : '';
		$offer_id      = isset( $_POST['offer_id'] ) ? sanitize_text_field( wp_unslash( $_POST['offer_id'] ) ) : '';
		$template_id   = isset( $_POST['template_id'] ) ? sanitize_text_field( wp_unslash( $_POST['template_id'] ) ) : '';
		$offer_post_id = isset( $_POST['offer_post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['offer_post_id'] ) ) : '';

		// IF custom then don't update and just return.
		if ( 'custom' === $template_id ) {

			echo wp_json_encode( array( 'status' => true ) );
			wp_die();
		}

		$offer_templates_array = array(
			'one'   => 'wps_upsell_lite_elementor_offer_template_1_funnel_builder',
			'two'   => 'wps_upsell_lite_elementor_offer_template_2_funnel_builder',
			'three' => 'wps_upsell_lite_elementor_offer_template_3_funnel_builder',
		);

		foreach ( $offer_templates_array as $template_key => $callback_function ) {

			if ( $template_id === $template_key ) {
				if ( wps_upsell_lite_elementor_plugin_active_funnel_builder() ) {

					// Delete previous elementor css.
					delete_post_meta( $offer_post_id, '_elementor_css' );
					delete_post_meta( $offer_post_id, 'ct_builder_shortcodes' );
					$my_post = array();
					$my_post['ID'] = $offer_post_id;
					$my_post['post_content'] = '';
					wp_update_post( $my_post );
					$elementor_data = $callback_function();
					update_post_meta( $offer_post_id, '_elementor_data', $elementor_data );

					break;
				} elseif ( wps_upsell_divi_builder_plugin_active_funnel_builder() ) {

					delete_post_meta( $offer_post_id, '_elementor_css' );
					delete_post_meta( $offer_post_id, '_elementor_data' );
					$get_post_contents = $callback_function();
					global $post;
					$my_post = array();
					$my_post['ID'] = $offer_post_id;
					$my_post['post_content'] = $get_post_contents;
					wp_update_post( $my_post );
					// Delete temporary meta key.
					delete_post_meta( $offer_post_id, 'divi_content' );
					break;
				}
			}
		}

		echo wp_json_encode( array( 'status' => true ) );

		wp_die();
	}

	/**
	 * Add attribute to styles allowed properties in wp_kses.
	 *
	 * @param array $styles Allowed properties.
	 * @return array
	 *
	 * @since    3.6.7
	 */
	public function wocuf_lite_add_style_attribute( $styles ) {

		$styles[] = 'display';
		return $styles;
	}


	/**
	 * Adding custom column in orders table at backend
	 *
	 * @since    1.0.0
	 * @param    array $columns    array of columns on orders table.
	 * @return   array    $columns    array of columns on orders table alongwith upsell column
	 */
	public function wps_wocuf_pro_add_columns_to_admin_orders( $columns ) {

		$columns['wps-upsell-orders'] = esc_html__( 'Upsell Orders', 'upsell-order-bump-offer-for-woocommerce' );

		return $columns;
	}


	/**
	 * Populating Upsell Orders column with Single Order or Upsell order.
	 *
	 * @since    1.0.0
	 * @param    array $column    Array of available columns.
	 * @param    int   $post_id   Current Order post id.
	 */
	public function wps_wocuf_pro_populate_upsell_order_column( $column, $post_id ) {

		$upsell_order = wps_wocfo_hpos_get_meta_data_funnel_builder( $post_id, 'wps_wocuf_upsell_order', true );
		$wps_onclick_upsell_already_existed = get_option( 'wps_manual_create_upsell', '' );
		if ( wps_ubo_lite_is_plugin_active( 'woo-one-click-upsell-funnel/woocommerce-one-click-upsell-funnel.php' ) ) {

			if ( 'done' != $wps_onclick_upsell_already_existed ) {
				return;
			}
		}

		switch ( $column ) {

			case 'wps-upsell-orders':
				if ( 'true' === $upsell_order ) :
					?>
					<a href="<?php echo esc_url( get_edit_post_link( $post_id ) ); ?>"><?php esc_html_e( 'Upsell Order', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
				<?php else : ?>
					<?php esc_html_e( 'Single Order', 'upsell-order-bump-offer-for-woocommerce' ); ?>
					<?php
				endif;
				break;
		}
	}


	/**
	 * Add Upsell Filtering dropdown for All Orders, No Upsell Orders, Only Upsell Orders.
	 *
	 * @since    1.0.0
	 */
	public function wps_wocuf_pro_restrict_manage_posts() {
		 $secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
		$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

		if ( ! $id_nonce_verified ) {
			wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
		}

		if ( isset( $_GET['post_type'] ) && 'shop_order' === sanitize_key( wp_unslash( $_GET['post_type'] ) ) ) {

			if ( isset( $_GET['wps_wocuf_pro_upsell_filter'] ) ) :

				?>
				<select name="wps_wocuf_pro_upsell_filter">
					<option value="all" <?php echo 'all' === sanitize_key( wp_unslash( $_GET['wps_wocuf_pro_upsell_filter'] ) ) ? 'selected=selected' : ''; ?>><?php esc_html_e( 'All Orders', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
					<option value="no_upsells" <?php echo 'no_upsells' === sanitize_key( wp_unslash( $_GET['wps_wocuf_pro_upsell_filter'] ) ) ? 'selected=selected' : ''; ?>><?php esc_html_e( 'No Upsell Orders', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
					<option value="all_upsells" <?php echo 'all_upsells' === sanitize_key( wp_unslash( $_GET['wps_wocuf_pro_upsell_filter'] ) ) ? 'selected=selected' : ''; ?>><?php esc_html_e( 'Only Upsell Orders', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
				</select>
				<?php
			endif;

			if ( ! isset( $_GET['wps_wocuf_pro_upsell_filter'] ) ) :
				?>
				<select name="wps_wocuf_pro_upsell_filter">
					<option value="all"><?php esc_html_e( 'All Orders', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
					<option value="no_upsells"><?php esc_html_e( 'No Upsell Orders', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
					<option value="all_upsells"><?php esc_html_e( 'Only Upsell Orders', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
				</select>
				<?php
			endif;
		}
	}

	/**
	 * Modifying query vars for filtering Upsell Orders.
	 *
	 * @since    1.0.0
	 * @param    array $vars    array of queries.
	 * @return   array    $vars    array of queries alongwith select dropdown query for upsell
	 */
	public function wps_wocuf_pro_request_query( $vars ) {

		$secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
		$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

		if ( ! $id_nonce_verified ) {
			wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
		}

		if ( isset( $_GET['wps_wocuf_pro_upsell_filter'] ) && 'all_upsells' === $_GET['wps_wocuf_pro_upsell_filter'] ) {

			$vars = array_merge(
				$vars,
				array(
					'meta_key' => 'wps_wocuf_upsell_order',     // phpcs:ignore
				)
			);
		} elseif ( isset( $_GET['wps_wocuf_pro_upsell_filter'] ) && 'no_upsells' === $_GET['wps_wocuf_pro_upsell_filter'] ) {

			$vars = array_merge(
				$vars,
				array(
					'meta_key'     => 'wps_wocuf_upsell_order',    // phpcs:ignore
					'meta_compare' => 'NOT EXISTS',
				)
			);
		}

		return $vars;
	}



	/**
	 * Add 'Upsell Support' column on payment gateways page.
	 *
	 * @param mixed $default_columns default columns.
	 * @since       2.0.0
	 */
	public function upsell_support_in_payment_gateway( $default_columns ) {

		$new_column['wps_upsell'] = esc_html__( 'Upsell Supported', 'upsell-order-bump-offer-for-woocommerce' );
		wps_upsee_lite_go_pro_funnel_builder( 'pro' );
		// Place at second last position.
		$position = count( $default_columns ) - 1;

		$default_columns = array_slice( $default_columns, 0, $position, true ) + $new_column + array_slice( $default_columns, $position, count( $default_columns ) - $position, true );

		return $default_columns;
	}


	/**
	 * 'Upsell Support' content on payment gateways page.
	 *
	 * @param mixed $gateway gateway.
	 * @since       2.0.0
	 */
	public function upsell_support_content_in_payment_gateway( $gateway ) {

		$supported_gateways = wps_upsell_lite_supported_gateways_funnel_builder();

		$supported_gateways_pro = wps_upsell_pro_supported_gateways_funnel_builder();

		echo '<td class="wps_upsell_supported">';

		if ( in_array( $gateway->id, $supported_gateways, true ) ) {

			echo '<span class="status-enabled">' . esc_html__( 'Yes', 'upsell-order-bump-offer-for-woocommerce' ) . '</span>';
		} elseif ( in_array( $gateway->id, $supported_gateways_pro, true ) ) {

			echo '	<span class="wps_wupsell_premium_strip">' . esc_html__( 'pro', 'upsell-order-bump-offer-for-woocommerce' ) . '</span>';
		} else {

			echo '<span class="status-disabled">' . esc_html__( 'No', 'upsell-order-bump-offer-for-woocommerce' ) . '</span>';
		}

		echo "<input type='hidden' id='wps_ubo_pro_status' value='inactive'>
		</td>";
	}

	/**
	 * Product simple product.
	 *
	 * @return void
	 */
	public function upsell_simple_product_settings() {
		$upsell_shipping_product = get_post_meta( get_the_ID(), 'wps_upsell_simple_shipping_product_' . get_the_ID(), true );
		if ( function_exists( 'wp_nonce_field' ) ) {
			wp_nonce_field( 'simple-product', 'upsell-custom-shipping-simple-nonce' );
		}

		?>
		<div class="wps_product_custom_field product_custom_field options_group show_if_simple show_if_external ">
			<h4>
				<?php
				echo esc_html__( 'Upsell setting', 'upsell-order-bump-offer-for-woocommerce' );
				?>
				<span class="wps-help-tip"></span>
				<p>
					<?php
					echo esc_html__( 'Add shipping price of this product for upsell offer.', 'upsell-order-bump-offer-for-woocommerce' );
					?>
				</p>
			</h4>
			<p class="form-field _sale_price_field">
				<label><?php echo esc_html__( 'Upsell shipping Price', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
				<input type="number" class="wps_product_shipping_input" name="wps_upsell_simple_shipping_product_<?php echo esc_attr( get_the_ID() ); ?>" id="wps_upsell_simple_shipping_product_<?php echo esc_attr( get_the_ID() ); ?>" value="<?php echo esc_attr( $upsell_shipping_product ); ?>">
			</p>
		</div>
		<?php
	}

	/**
	 * Upsell saving for simple products.
	 *
	 * @param [type] $post_id Is the post id.
	 * @return void
	 */
	public function upsell_saving_simple_product_dynamic_shipping( $post_id ) {
		if ( isset( $_POST['upsell-custom-shipping-simple-nonce'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['upsell-custom-shipping-simple-nonce'] ) ), 'simple-product' ) ) {
				wp_die();
			}
		}
		$upsell_shipping_price = ! empty( $_POST[ 'wps_upsell_simple_shipping_product_' . $post_id ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'wps_upsell_simple_shipping_product_' . $post_id ] ) ) : '';

		update_post_meta( $post_id, 'wps_upsell_simple_shipping_product_' . $post_id, $upsell_shipping_price );
	}


	/**
	 * Upsell setting for variable products.
	 *
	 * @param [type] $loop Is the loop.
	 * @param [type] $variation_data Is the variation data.
	 * @param [type] $variation Is the variation object.
	 * @return void
	 */
	public function upsell_add_custom_price_to_variations( $loop, $variation_data, $variation ) {
		$upsell_shipping_product = get_post_meta( $variation->ID, 'wps_upsell_simple_shipping_product_' . $variation->ID, true );

		if ( 0 === $loop ) {
			wp_nonce_field( 'variable-product', 'wps-upsell-price-variation-nonce' );
		}

		?>
		<div class="wps_product_custom_field product_custom_field options_group show_if_simple show_if_external ">
			<h4>
				<?php
				echo esc_html__( 'Upsell setting', 'upsell-order-bump-offer-for-woocommerce' );
				?>
				<span class="wps-help-tip"></span>
				<p>
					<?php
					echo esc_html__( 'Add shipping price of this product for upsell offer.', 'upsell-order-bump-offer-for-woocommerce' );
					?>
				</p>
			</h4>

			<label>
				<?php echo esc_html__( 'Upsell shipping Price', 'upsell-order-bump-offer-for-woocommerce' ); ?>
			</label>
			<input type="number" class="wps_product_shipping_input" name="wps_upsell_simple_shipping_product_<?php echo esc_attr( $variation->ID ); ?>" id="wps_upsell_simple_shipping_product_<?php echo esc_attr( $variation->ID ); ?>" value="<?php echo esc_attr( $upsell_shipping_product ); ?>">

		</div>
		<?php
	}


	/**
	 * Upsell save data setting for variable.
	 *
	 * @param [type] $variation_id Is the variation id.
	 * @param [type] $i Is the number of variation.
	 * @return void
	 */
	public function upsell_save_custom_price_variations( $variation_id, $i ) {

		if ( isset( $_POST['wps-upsell-price-variation-nonce'] ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wps-upsell-price-variation-nonce'] ) ), 'variable-product' ) ) {
				wp_die();
			}
		}

		$upsell_shipping_price = ! empty( $_POST[ 'wps_upsell_simple_shipping_product_' . $variation_id ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'wps_upsell_simple_shipping_product_' . $variation_id ] ) ) : '';
		update_post_meta( $variation_id, 'wps_upsell_simple_shipping_product_' . $variation_id, $upsell_shipping_price );
	}


	/**
	 * Add Upsell Reporting in Woo Admin reports.
	 *
	 * @param mixed $reports reports.
	 * @since       3.0.0
	 */
	public function add_upsell_reporting( $reports ) {

		$reports['upsell'] = array(

			'title'   => esc_html__( '1 Click Upsell', 'upsell-order-bump-offer-for-woocommerce' ),
			'reports' => array(

				'sales_by_date'     => array(
					'title'       => esc_html__( 'Upsell Sales by date', 'upsell-order-bump-offer-for-woocommerce' ),
					'description' => '',
					'hide_title'  => 1,
					'callback'    => array( 'Upsell_Order_Bump_Offer_For_Woocommerce_Admin', 'upsell_reporting_callback' ),
				),

				'sales_by_product'  => array(
					'title'       => esc_html__( 'Upsell Sales by product', 'upsell-order-bump-offer-for-woocommerce' ),
					'description' => '',
					'hide_title'  => 1,
					'callback'    => array( 'Upsell_Order_Bump_Offer_For_Woocommerce_Admin', 'upsell_reporting_callback' ),
				),

				'sales_by_category' => array(
					'title'       => esc_html__( 'Upsell Sales by category', 'upsell-order-bump-offer-for-woocommerce' ),
					'description' => '',
					'hide_title'  => 1,
					'callback'    => array( 'Upsell_Order_Bump_Offer_For_Woocommerce_Admin', 'upsell_reporting_callback' ),
				),
			),
		);

		return $reports;
	}


	/**
	 * Add custom report. callback.
	 *
	 * @param mixed $report_type report type.
	 * @since       3.0.0
	 */
	public static function upsell_reporting_callback( $report_type ) {

		$report_file      = ! empty( $report_type ) ? str_replace( '_', '-', $report_type ) : '';
		$preformat_string = ! empty( $report_type ) ? ucwords( str_replace( '_', ' ', $report_type ) ) : '';
		$class_name       = ! empty( $preformat_string ) ? 'WPS_Upsell_Report_' . str_replace( ' ', '_', $preformat_string ) : '';

		/**
		 * The file responsible for defining reporting.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'reporting/class-wps-upsell-report-' . $report_file . '.php';

		if ( class_exists( $class_name ) ) {

			$report = new $class_name();
			$report->output_report();
		} else {

			?>
			<div class="wps_wocuf_report_error_wrap" style="text-align: center;">
				<h2 class="wps_wocuf_report_error_text">
					<?php esc_html_e( 'Some Error Occured while creating report.', 'upsell-order-bump-offer-for-woocommerce' ); ?>
				</h2>
			</div>
			<?php
		}
	}

	/**
	 * Add menu redirect URL. callback.
	 *
	 * @since       3.0.0
	 */
	public function wps_redirect_upsell_page() {
		if (
			isset( $_GET['page'] ) && 'upsell-order-bump-offer-for-woocommerce-setting' === $_GET['page'] &&
			! isset( $_GET['tab'] ) &&
			! defined( 'DOING_AJAX' ) && ! defined( 'DOING_CRON' )
		) {
			wp_safe_redirect( admin_url( 'admin.php?page=upsell-order-bump-offer-for-woocommerce-setting&tab=general-setting' ) );
			exit;
		}
	}

	/**
	 * Add submenu page for upsell cart abandoned bump.
	 *
	 * @since       3.0.0
	 */
	public function pre_add_submenu_page_reporting_callback_pro() {
		 include_once WPS_WOCUF_DIRPATH_FUNNEL_BUILDER . '/admin/partials/templates/wps-upsell-cart-abandoned-bump.php';
	}


	/**
	 * Create label callback.
	 *
	 * @since       3.0.0
	 */
	public function wps_ubo_create_label_callback() {
		// ✅ Nonce verification (AJAX-safe).
		check_ajax_referer( 'wps_ubo_labels', 'nonce' );

		// (Optional) Capability gate.
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'upsell-order-bump-offer-for-woocommerce' ) ) );
		}

		// ✅ Snake_case + sanitize + unslash.
		$label_name_raw  = isset( $_POST['wps_ubo_label_name'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_ubo_label_name'] ) ) : '';
		$label_color_raw = isset( $_POST['wps_ubo_label_color'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_ubo_label_color'] ) ) : '';

		$label_name  = sanitize_text_field( $label_name_raw );
		$label_color = sanitize_hex_color( $label_color_raw );
		if ( null === $label_color ) {
			// Fallback if you allow non-hex values.
			$label_color = sanitize_text_field( $label_color_raw );
		}

		// ✅ Yoda conditions.
		if ( '' === $label_name ) {
			wp_send_json_error( array( 'message' => __( 'Label name is required.', 'upsell-order-bump-offer-for-woocommerce' ) ) );
		}
		if ( '' === $label_color ) {
			wp_send_json_error( array( 'message' => __( 'Label color is required.', 'upsell-order-bump-offer-for-woocommerce' ) ) );
		}

		// Get & normalize option.
		$global_options = get_option( 'wps_ubo_global_options', array() );
		if ( ! isset( $global_options['wps_bump_label'] ) || ! is_array( $global_options['wps_bump_label'] ) ) {
			$global_options['wps_bump_label'] = array();
		}

		// ✅ Duplicate check (case/space-insensitive).
		$incoming_key = strtolower( trim( $label_name ) );
		$is_duplicate = false;

		foreach ( $global_options['wps_bump_label'] as $existing ) {
			if ( ! is_array( $existing ) ) {
				continue;
			}
			$existing_name = isset( $existing['name'] ) ? (string) $existing['name'] : '';
			if ( strtolower( trim( $existing_name ) ) === $incoming_key ) {
				$is_duplicate = true;
				break;
			}
		}

		if ( true === $is_duplicate ) {
			wp_send_json_error( array( 'message' => __( 'A label with the same name already exists.', 'upsell-order-bump-offer-for-woocommerce' ) ) );
		}

		// Prepend new label.
		$new_label = array(
			'name'  => $label_name,
			'color' => $label_color,
		);
		array_unshift( $global_options['wps_bump_label'], $new_label );

		update_option( 'wps_ubo_global_options', $global_options );

		wp_send_json_success(
			array(
				'message' => __( 'Label and color saved successfully.', 'upsell-order-bump-offer-for-woocommerce' ),
				'label'   => $new_label,
			)
		);
	}

	/**
	 * Create label callback.
	 *
	 * @since       3.0.0
	 */
	public function wps_ubo_save_popup_system_settings_callback() {
		check_ajax_referer( 'wps_ubo_labels', 'nonce' );
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'upsell-order-bump-offer-for-woocommerce' ) ) );
		}

		$popup_type = isset( $_POST['popup_type'] ) ? sanitize_text_field( wp_unslash( $_POST['popup_type'] ) ) : '';
		$popup_delay = isset( $_POST['popup_delay'] ) ? intval( $_POST['popup_delay'] ) : 1;

		update_option( 'wps_ubo_popup_type', $popup_type );
		update_option( 'wps_ubo_popup_delay', $popup_delay );

		wp_send_json_success(
			array(
				'message' => 'Popup settings saved successfully',
				'popup_type' => $popup_type,
				'popup_delay' => $popup_delay,
			)
		);
	}

	/**
	 * Export order bumps as CSV.
	 *
	 * @since 3.1.9
	 */
	public function handle_bump_export_csv() {
		if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to export order bumps.', 'upsell-order-bump-offer-for-woocommerce' ) );
		}

		check_admin_referer( 'wps_ubo_export_bumps' );

		$wps_upsell_bumps = get_option( 'wps_ubo_bump_list', array() );

		if ( ! is_array( $wps_upsell_bumps ) ) {
			$wps_upsell_bumps = array();
		}

		// Attempt to bump memory for large exports.
		if ( function_exists( 'wp_raise_memory_limit' ) ) {
			wp_raise_memory_limit( 'admin' );
		}

		$payload  = array(
			'_comments'         => array(
				'about'        => __( 'Order bump export. Edit values under wps_ubo_bump_list and keep keys intact for re-import.', 'upsell-order-bump-offer-for-woocommerce' ),
				'bump_fields'  => $this->get_bump_json_field_comments(),
			),
			'wps_ubo_bump_list' => $wps_upsell_bumps,
		);
		$filename = 'order-bump-export-' . gmdate( 'Y-m-d' ) . '.json';

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

		$json = wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		echo is_string( $json ) ? $json : wp_json_encode( $payload );
		exit;
	}

	/**
	 * Import order bumps from uploaded JSON.
	 *
	 * @since 3.1.9
	 */
	public function handle_bump_import() {
		if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to import order bumps.', 'upsell-order-bump-offer-for-woocommerce' ) );
		}

		check_admin_referer( 'wps_ubo_import_bumps' );

		$redirect_url = $this->get_bump_list_redirect_url();

		if ( empty( $_FILES['wps_ubo_import_file'] ) || ! isset( $_FILES['wps_ubo_import_file']['tmp_name'] ) || UPLOAD_ERR_OK !== $_FILES['wps_ubo_import_file']['error'] ) {
			wp_safe_redirect( add_query_arg( 'wps_bump_import_status', 'file_error', $redirect_url ) );
			exit;
		}

		$file        = $_FILES['wps_ubo_import_file']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$file_detail = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'] );

			// Allow JSON uploads only.
			if ( ! empty( $file_detail['ext'] ) && 'json' !== $file_detail['ext'] ) {
				wp_safe_redirect( add_query_arg( 'wps_bump_import_status', 'invalid_type', $redirect_url ) );
				exit;
			}

			$raw_contents = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$decoded_data = json_decode( $raw_contents, true );

		if ( isset( $decoded_data['wps_ubo_bump_list'] ) && is_array( $decoded_data['wps_ubo_bump_list'] ) ) {
			$decoded_data = $decoded_data['wps_ubo_bump_list'];
		}

			if ( empty( $decoded_data ) || ! is_array( $decoded_data ) ) {
				wp_safe_redirect( add_query_arg( 'wps_bump_import_status', 'invalid_data', $redirect_url ) );
				exit;
			}

		$existing_bumps = get_option( 'wps_ubo_bump_list', array() );

		if ( ! is_array( $existing_bumps ) ) {
			$existing_bumps = array();
		}

		$last_index     = $this->get_last_bump_index( $existing_bumps );
		$imported_count = 0;

			foreach ( $decoded_data as $bump ) {

				if ( ! is_array( $bump ) ) {
					continue;
				}

				$bump = $this->normalize_bump_row( $bump );

				$last_index ++;
				$existing_bumps[ $last_index ] = $bump;
				$imported_count ++;
			}

		update_option( 'wps_ubo_bump_list', $existing_bumps );

		$query_args = array(
			'wps_bump_import_status' => 'success',
			'wps_bump_imported'      => $imported_count,
		);

		wp_safe_redirect( add_query_arg( $query_args, $redirect_url ) );
		exit;
	}

	/**
	 * Import order bumps via AJAX using CSV upload.
	 *
	 * @since 3.1.9
	 */
	public function handle_bump_import_ajax() {
		check_ajax_referer( 'wps_admin_nonce', 'security' );

		if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to import order bumps.', 'upsell-order-bump-offer-for-woocommerce' ) ) );
		}

		if ( empty( $_FILES['wps_ubo_import_file'] ) || UPLOAD_ERR_OK !== $_FILES['wps_ubo_import_file']['error'] ) {
			wp_send_json_error( array( 'message' => __( 'Upload failed. Please try again.', 'upsell-order-bump-offer-for-woocommerce' ) ) );
		}

		$file        = $_FILES['wps_ubo_import_file']; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$file_detail = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'] );

		if ( ! empty( $file_detail['ext'] ) && 'json' !== $file_detail['ext'] ) {
			wp_send_json_error( array( 'message' => __( 'Please upload a JSON file.', 'upsell-order-bump-offer-for-woocommerce' ) ) );
		}

		$raw_contents = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$parsed       = json_decode( $raw_contents, true );

		if ( isset( $parsed['wps_ubo_bump_list'] ) && is_array( $parsed['wps_ubo_bump_list'] ) ) {
			$parsed = $parsed['wps_ubo_bump_list'];
		}

		if ( empty( $parsed ) || ! is_array( $parsed ) ) {
			wp_send_json_error( array( 'message' => __( 'No valid rows found in the JSON file.', 'upsell-order-bump-offer-for-woocommerce' ) ) );
		}

		$existing_bumps = get_option( 'wps_ubo_bump_list', array() );

		if ( ! is_array( $existing_bumps ) ) {
			$existing_bumps = array();
		}

		$last_index     = $this->get_last_bump_index( $existing_bumps );
		$imported_count = 0;

		foreach ( $parsed as $bump_data ) {
			if ( ! is_array( $bump_data ) || empty( $bump_data ) ) {
				continue;
			}
			$last_index ++;
			$existing_bumps[ $last_index ] = $bump_data;
			$imported_count ++;
		}

		update_option( 'wps_ubo_bump_list', $existing_bumps );

		wp_send_json_success(
			array(
				'message'         => __( 'Import completed.', 'upsell-order-bump-offer-for-woocommerce' ),
				'imported_count'  => $imported_count,
				'total_after_import' => count( $existing_bumps ),
			)
		);
	}

	/**
	 * Parse CSV into bumps array.
	 *
	 * @since 3.1.9
	 * @param string $file_path Uploaded CSV path.
	 * @return array
	 */
	private function parse_csv_import( $file_path ) {
		$rows = array();

		if ( ! file_exists( $file_path ) ) {
			return $rows;
		}

		$handle = fopen( $file_path, 'r' );

		if ( false === $handle ) {
			return $rows;
		}

		$headers = fgetcsv( $handle );

		if ( empty( $headers ) ) {
			fclose( $handle );
			return $rows;
		}

			$headers = array_map(
				function( $header ) {
					return $this->map_csv_header_to_key( $header );
				},
				$headers
			);

			while ( ( $data = fgetcsv( $handle ) ) !== false ) {
				$row = array();
				foreach ( $headers as $index => $column_name ) {
					if ( ! isset( $data[ $index ] ) ) {
						continue;
					}

					if ( empty( $column_name ) || $this->should_skip_bump_export_key( $column_name ) ) {
						continue;
					}

					$raw_value = $data[ $index ];
					$column    = sanitize_text_field( $column_name );

				$row[ $column ] = $this->maybe_convert_csv_value( $column, $raw_value );
			}

			// Remove bump_id if present; indexes are handled when inserting.
				if ( isset( $row['bump_id'] ) ) {
					unset( $row['bump_id'] );
				}

				$row = $this->normalize_bump_row( $row );

				if ( ! empty( $row ) ) {
					$rows[] = $row;
				}
			}

			fclose( $handle );

		return $rows;
	}

	/**
	 * Convert CSV string to appropriate type.
	 *
	 * @since 3.1.9
	 * @param string $column Column name.
	 * @param string $raw_value Value read from CSV.
	 * @return mixed
	 */
	private function maybe_convert_csv_value( $column, $raw_value ) {
		$array_columns = array(
			'wps_upsell_bump_target_ids',
			'wps_upsell_bump_target_categories',
			'wps_upsell_bump_products_in_offer',
			'wps_wocuf_target_pro_ids',
			'wps_wocuf_target_cat_ids',
			'wps_wocuf_products_in_offer',
		);

		if ( in_array( $column, $array_columns, true ) ) {
			$raw_value = (string) $raw_value;
			if ( '' === trim( $raw_value ) ) {
				return array();
			}
			return array_filter( array_map( 'trim', explode( '|', $raw_value ) ) );
		}

		// Attempt to decode JSON if present for nested structures.
		if ( is_string( $raw_value ) && ( 0 === strpos( $raw_value, '{' ) || 0 === strpos( $raw_value, '[' ) ) ) {
			$decoded = json_decode( $raw_value, true );
			if ( null !== $decoded ) {
				return $decoded;
			}
		}

		return $raw_value;
	}

	/**
	 * Determine CSV columns dynamically.
	 *
	 * @since 3.1.9
	 * @param array $bumps Bump list.
	 * @return array
	 */
	private function get_bump_csv_columns( $bumps ) {
		$columns = array( 'bump_id' );

		if ( empty( $bumps ) || ! is_array( $bumps ) ) {
			return array_values( array_unique( array_merge( $columns, array_keys( $this->get_bump_field_labels() ) ) ) );
		}

		$allowed_keys = array_keys( $this->get_bump_field_labels() );

		foreach ( $bumps as $bump_data ) {
			if ( ! is_array( $bump_data ) ) {
				continue;
			}
			foreach ( $bump_data as $key => $value ) {
				if ( $this->should_skip_bump_export_key( $key ) ) {
					continue;
				}

				if ( ! in_array( $key, $columns, true ) && in_array( $key, $allowed_keys, true ) ) {
					$columns[] = $key;
				}
			}
		}

		return array_values( array_unique( $columns ) );
	}

	/**
	 * Generate readable CSV headers from column keys.
	 *
	 * @since 3.1.9
	 * @param array $columns Column keys.
	 * @return array
	 */
	private function get_bump_csv_headers( $columns ) {
		$label_map = $this->get_bump_field_labels();
		$headers   = array();

		foreach ( $columns as $column ) {
			if ( $this->should_skip_bump_export_key( $column ) ) {
				continue;
			}
			$headers[] = isset( $label_map[ $column ] ) ? $label_map[ $column ] : $column;
		}

		return $headers;
	}

	/**
	 * Prepare a CSV row for a bump.
	 *
	 * @since 3.1.9
	 * @param string|int $bump_id Bump id/key.
	 * @param array      $bump_data Bump data array.
	 * @param array      $columns Ordered columns.
	 * @return array
	 */
	private function prepare_bump_csv_row( $bump_id, $bump_data, $columns ) {
		$row = array();

		foreach ( $columns as $column ) {
			if ( $this->should_skip_bump_export_key( $column ) ) {
				continue;
			}
			if ( 'bump_id' === $column ) {
				$row[] = $bump_id;
				continue;
			}

			if ( isset( $bump_data[ $column ] ) ) {
				$row[] = $this->convert_value_for_csv( $bump_data[ $column ] );
			} else {
				$row[] = '';
			}
		}

		return $row;
	}

	/**
	 * Convert value to CSV-safe string.
	 *
	 * @since 3.1.9
	 * @param mixed $value Value.
	 * @return string
	 */
	private function convert_value_for_csv( $value ) {
		if ( is_array( $value ) ) {
			// For simple lists, join with pipe. For complex arrays, fall back to JSON.
			$is_assoc = array_keys( $value ) !== range( 0, count( $value ) - 1 );
			if ( ! $is_assoc ) {
				return implode( '|', array_map( 'strval', $value ) );
			}

			return wp_json_encode( $value );
		}

		if ( is_bool( $value ) ) {
			return $value ? 'yes' : 'no';
		}

		return (string) $value;
	}

	/**
	 * Export funnels as CSV.
	 *
	 * @since 3.1.9
	 */
	public function handle_funnel_export_csv() {
		if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to export funnels.', 'upsell-order-bump-offer-for-woocommerce' ) );
		}

		check_admin_referer( 'wps_ubo_export_bumps' );

		// Prefer pro funnels if present; otherwise use lite funnels.
		$storage_key = 'wps_wocuf_pro_funnels_list';
		$funnels     = get_option( 'wps_wocuf_pro_funnels_list', array() );

		if ( ! is_array( $funnels ) || empty( $funnels ) ) {
			$storage_key = 'wps_wocuf_funnels_list';
			$funnels     = get_option( 'wps_wocuf_funnels_list', array() );
			if ( ! is_array( $funnels ) ) {
				$funnels = array();
			}
		}

		if ( function_exists( 'wp_raise_memory_limit' ) ) {
			wp_raise_memory_limit( 'admin' );
		}

		$payload  = array(
			'_comments' => array(
				'about'         => __( 'Funnel export. Update values under the funnel list key and keep keys unchanged for import.', 'upsell-order-bump-offer-for-woocommerce' ),
				'funnel_fields' => $this->get_funnel_json_field_comments(),
			),
			$storage_key => $funnels,
		);
		$filename = 'upsell-funnels-' . gmdate( 'Y-m-d' ) . '.json';

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

		$json = wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		echo is_string( $json ) ? $json : wp_json_encode( $payload );
		exit;
	}

	/**
	 * Import funnels via AJAX (CSV).
	 *
	 * @since 3.1.9
	 */
	public function handle_funnel_import_ajax() {
		check_ajax_referer( 'wps_admin_nonce', 'security' );

		if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'You do not have permission to import funnels.', 'upsell-order-bump-offer-for-woocommerce' ) ) );
		}

		if ( empty( $_FILES['wps_ubo_import_file'] ) || UPLOAD_ERR_OK !== $_FILES['wps_ubo_import_file']['error'] ) {
			wp_send_json_error( array( 'message' => __( 'Upload failed. Please try again.', 'upsell-order-bump-offer-for-woocommerce' ) ) );
		}

		$file        = $_FILES['wps_ubo_import_file']; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$file_detail = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'] );

		if ( ! empty( $file_detail['ext'] ) && 'json' !== $file_detail['ext'] ) {
			wp_send_json_error( array( 'message' => __( 'Please upload a JSON file.', 'upsell-order-bump-offer-for-woocommerce' ) ) );
		}

		$raw_contents = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$parsed       = json_decode( $raw_contents, true );

		if ( isset( $parsed['wps_wocuf_pro_funnels_list'] ) && is_array( $parsed['wps_wocuf_pro_funnels_list'] ) ) {
			$storage_key = 'wps_wocuf_pro_funnels_list';
			$parsed      = $parsed['wps_wocuf_pro_funnels_list'];
		} elseif ( isset( $parsed['wps_wocuf_funnels_list'] ) && is_array( $parsed['wps_wocuf_funnels_list'] ) ) {
			$storage_key = 'wps_wocuf_funnels_list';
			$parsed      = $parsed['wps_wocuf_funnels_list'];
		}

		if ( empty( $parsed ) || ! is_array( $parsed ) ) {
			wp_send_json_error( array( 'message' => __( 'No valid rows found in the JSON file.', 'upsell-order-bump-offer-for-woocommerce' ) ) );
		}

		// Choose storage based on pro availability/data.
			$storage_key    = isset( $storage_key ) ? $storage_key : 'wps_wocuf_funnels_list';
			$existing       = get_option( $storage_key, array() );
			$existing_pro   = get_option( 'wps_wocuf_pro_funnels_list', array() );
			$is_pro_active  = wps_is_plugin_active_with_version( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '0.0.0' );

			if ( ( is_array( $existing_pro ) && ! empty( $existing_pro ) ) || $is_pro_active ) {
				$storage_key = 'wps_wocuf_pro_funnels_list';
				$existing    = is_array( $existing_pro ) ? $existing_pro : array();
			}

		if ( ! is_array( $existing ) ) {
			$existing = array();
		}

		$last_index     = $this->get_last_numeric_index( $existing );
		$imported_count = 0;

		foreach ( $parsed as $funnel_data ) {
			if ( ! is_array( $funnel_data ) || empty( $funnel_data ) ) {
				continue;
			}
			$funnel_data = $this->normalize_funnel_row( $funnel_data );
			$last_index ++;
			$existing[ $last_index ] = $funnel_data;
			$imported_count ++;
		}

		update_option( $storage_key, $existing );

		wp_send_json_success(
			array(
				'message'           => __( 'Import completed.', 'upsell-order-bump-offer-for-woocommerce' ),
				'imported_count'    => $imported_count,
				'total_after_import' => count( $existing ),
			)
		);
	}

	/**
	 * Toggle funnel status via AJAX.
	 *
	 * @since 3.1.9
	 */
	public function handle_funnel_status_toggle() {
		check_ajax_referer( 'wps_admin_nonce', 'security' );

		if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized', 'upsell-order-bump-offer-for-woocommerce' ) ) );
		}

		$funnel_id   = isset( $_POST['funnel_id'] ) ? sanitize_text_field( wp_unslash( $_POST['funnel_id'] ) ) : '';
		$new_status  = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'no';
		$valid_state = in_array( $new_status, array( 'yes', 'no' ), true ) ? $new_status : 'no';

		// Determine which storage to use (pro funnels vs lite funnels).
		$storage_key = 'wps_wocuf_funnels_list';
		$existing    = get_option( $storage_key, array() );

		$pro_funnels = get_option( 'wps_wocuf_pro_funnels_list', array() );
		if ( is_array( $pro_funnels ) && isset( $pro_funnels[ $funnel_id ] ) ) {
			$existing    = $pro_funnels;
			$storage_key = 'wps_wocuf_pro_funnels_list';
		}

		if ( empty( $funnel_id ) || ! is_array( $existing ) || ! isset( $existing[ $funnel_id ] ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid funnel.', 'upsell-order-bump-offer-for-woocommerce' ) ) );
		}

		$existing[ $funnel_id ]['wps_upsell_funnel_status'] = $valid_state;
		update_option( $storage_key, $existing );

		$label = 'yes' === $valid_state ? __( 'Live', 'upsell-order-bump-offer-for-woocommerce' ) : __( 'Sandbox', 'upsell-order-bump-offer-for-woocommerce' );

		wp_send_json_success(
			array(
				'status' => $valid_state,
				'label'  => $label,
			)
		);
	}

	/**
	 * Parse generic CSV rows.
	 *
	 * @since 3.1.9
	 * @param string $file_path File path.
	 * @return array
	 */
		private function parse_csv_import_generic( $file_path ) {
			$rows = array();

			if ( ! file_exists( $file_path ) ) {
				return $rows;
			}

		$handle = fopen( $file_path, 'r' );
		if ( false === $handle ) {
			return $rows;
		}

		$headers = fgetcsv( $handle );
		if ( empty( $headers ) ) {
			fclose( $handle );
			return $rows;
		}

		$headers = array_map( 'sanitize_text_field', $headers );

			while ( ( $data = fgetcsv( $handle ) ) !== false ) {
				$row = array();
				foreach ( $headers as $index => $column_name ) {
					if ( ! isset( $data[ $index ] ) ) {
						continue;
					}

					$mapped_key = $this->map_csv_header_to_key( $column_name );

					if ( empty( $mapped_key ) ) {
						continue;
					}

					$row[ $mapped_key ] = $this->maybe_convert_csv_value( $mapped_key, $data[ $index ] );
				}

				if ( isset( $row['funnel_id'] ) ) {
					unset( $row['funnel_id'] );
				}

			if ( ! empty( $row ) ) {
				$rows[] = $row;
			}
		}

		fclose( $handle );

		return $rows;
	}

	/**
	 * Funnel CSV columns.
	 *
	 * @since 3.1.9
	 * @param array $funnels Funnel list.
	 * @return array
	 */
	private function get_funnel_csv_columns( $funnels ) {
		$columns = array(
			'funnel_id',
			'wps_wocuf_funnel_name',
			'wps_upsell_funnel_status',
			'wps_wocuf_target_pro_ids',
			'wps_wocuf_target_cat_ids',
			'wps_wocuf_products_in_offer',
			'wps_wocuf_global_funnel',
			'wps_wocuf_exclusive_offer',
			'wps_wocuf_smart_offer_upgrade',
		);

		if ( empty( $funnels ) || ! is_array( $funnels ) ) {
			return $columns;
		}

		foreach ( $funnels as $funnel_data ) {
			if ( ! is_array( $funnel_data ) ) {
				continue;
			}
			foreach ( $funnel_data as $key => $value ) {
				if ( ! in_array( $key, $columns, true ) ) {
					$columns[] = $key;
				}
			}
		}

		return array_values( array_unique( $columns ) );
	}

	/**
	 * Funnel CSV headers.
	 *
	 * @since 3.1.9
	 * @param array $columns Columns.
	 * @return array
	 */
	private function get_funnel_csv_headers( $columns ) {
		$labels = array(
			'funnel_id'                 => __( 'Funnel ID', 'upsell-order-bump-offer-for-woocommerce' ),
			'wps_wocuf_funnel_name'     => __( 'Funnel Name', 'upsell-order-bump-offer-for-woocommerce' ),
			'wps_upsell_funnel_status'  => __( 'Status', 'upsell-order-bump-offer-for-woocommerce' ),
			'wps_wocuf_target_pro_ids'  => __( 'Target Product IDs', 'upsell-order-bump-offer-for-woocommerce' ),
			'wps_wocuf_target_cat_ids'  => __( 'Target Category IDs', 'upsell-order-bump-offer-for-woocommerce' ),
			'wps_wocuf_products_in_offer' => __( 'Offer Product IDs', 'upsell-order-bump-offer-for-woocommerce' ),
			'wps_wocuf_global_funnel'   => __( 'Global Funnel', 'upsell-order-bump-offer-for-woocommerce' ),
			'wps_wocuf_exclusive_offer' => __( 'Exclusive Offer', 'upsell-order-bump-offer-for-woocommerce' ),
			'wps_wocuf_smart_offer_upgrade' => __( 'Smart Offer Upgrade', 'upsell-order-bump-offer-for-woocommerce' ),
		);

		$headers = array();
		foreach ( $columns as $col ) {
			$headers[] = isset( $labels[ $col ] ) ? $labels[ $col ] : $col;
		}

		return $headers;
	}

	/**
	 * Prepare funnel CSV row.
	 *
	 * @since 3.1.9
	 * @param string|int $funnel_id Funnel id.
	 * @param array      $funnel_data Funnel data.
	 * @param array      $columns Columns.
	 * @return array
	 */
	private function prepare_funnel_csv_row( $funnel_id, $funnel_data, $columns ) {
		$row = array();
		foreach ( $columns as $column ) {
			if ( 'funnel_id' === $column ) {
				$row[] = $funnel_id;
				continue;
			}

			$row[] = isset( $funnel_data[ $column ] ) ? $this->convert_value_for_csv( $funnel_data[ $column ] ) : '';
		}
		return $row;
	}

	/**
	 * Get highest numeric index in array.
	 *
	 * @since 3.1.9
	 * @param array $items Items.
	 * @return int
	 */
	private function get_last_numeric_index( $items ) {
		$last_index = 0;
		if ( is_array( $items ) && ! empty( $items ) ) {
			foreach ( array_keys( $items ) as $key ) {
				if ( is_numeric( $key ) ) {
					$last_index = max( $last_index, (int) $key );
				}
			}
		}
		return $last_index;
	}

	/**
	 * Map a CSV header back to the internal bump key.
	 *
	 * @since 3.1.9
	 * @param string $header Header label from CSV.
	 * @return string
	 */
		private function map_csv_header_to_key( $header ) {
			$label_map    = array_merge( $this->get_bump_field_labels(), $this->get_funnel_field_labels() );
			$reverse_map  = array();
			$clean_header = trim( $header );
			$lower_header = strtolower( $clean_header );

			foreach ( $label_map as $key => $label ) {
				$reverse_map[ strtolower( $label ) ] = $key;
				$reverse_map[ strtolower( $key ) ]   = $key;
			}

			if ( isset( $reverse_map[ $lower_header ] ) ) {
				return $reverse_map[ $lower_header ];
			}

			$sanitized = sanitize_key( $clean_header );

			if ( $this->should_skip_bump_export_key( $sanitized ) ) {
				return '';
			}

			return $sanitized;
		}

	/**
	 * Field labels for CSV headers.
	 *
	 * @since 3.1.9
	 * @return array
	 */
		private function get_bump_field_labels() {
			return array(
				'bump_id'                           => __( 'Bump ID', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_bump_name'              => __( 'Bump Name', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_bump_status'            => __( 'Status', 'upsell-order-bump-offer-for-woocommerce' ),
			'wps_upsell_bump_priority'          => __( 'Priority', 'upsell-order-bump-offer-for-woocommerce' ),
			'wps_upsell_bump_target_ids'        => __( 'Target Product IDs', 'upsell-order-bump-offer-for-woocommerce' ),
			'wps_upsell_bump_target_categories' => __( 'Target Category IDs', 'upsell-order-bump-offer-for-woocommerce' ),
			'wps_upsell_bump_products_in_offer' => __( 'Offer Product IDs', 'upsell-order-bump-offer-for-woocommerce' ),
			'wps_bump_label_campaign'           => __( 'Label Campaign', 'upsell-order-bump-offer-for-woocommerce' ),
			'wps_display_method'                => __( 'Display Method', 'upsell-order-bump-offer-for-woocommerce' ),
			'wps_upsell_bump_description'       => __( 'Description', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_bump_discount'          => __( 'Discount Type', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_bump_discount_value'    => __( 'Discount Value', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_bump_coupon'            => __( 'Coupon', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_bump_min_cart'          => __( 'Min Cart Amount', 'upsell-order-bump-offer-for-woocommerce' ),
			);
		}

		/**
		 * Field descriptions for JSON exports to help users edit values safely.
		 *
		 * @since 3.2.0
		 * @return array
		 */
		private function get_bump_json_field_comments() {
			return array(
				'bump_id'                           => __( 'Leave empty when importing; the plugin assigns the next numeric ID automatically.', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_bump_name'              => __( 'Name shown with the bump offer (plain text).', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_bump_status'            => __( 'Use "yes" to make the bump live or "no" to keep it off.', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_bump_priority'          => __( 'Numeric priority used to sort bumps; lower numbers are shown first.', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_bump_target_ids'        => __( 'Product IDs where the bump should appear. Provide an array of product IDs.', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_bump_target_categories' => __( 'Category IDs where the bump should appear. Provide an array of category IDs.', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_bump_products_in_offer' => __( 'Product ID to be offered. Supply a single product ID.', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_bump_label_campaign'           => __( 'Optional label key/name configured under bump labels.', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_display_method'                => __( 'Display method slug (for example, "ab_method" for abandoned cart bumps).', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_bump_description'       => __( 'Short description shown with the bump (plain text or basic HTML).', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_bump_discount'          => __( 'Discount type slug, such as "percent", "fixed", or coupon-based option used in the admin.', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_bump_discount_value'    => __( 'Numeric discount amount that pairs with the selected discount type.', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_bump_coupon'            => __( 'Existing coupon code to apply. Leave blank if not using a coupon.', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_bump_min_cart'          => __( 'Minimum cart subtotal required to show the bump (numeric).', 'upsell-order-bump-offer-for-woocommerce' ),
			);
		}

		/**
		 * Field labels for funnel CSV headers.
		 *
		 * @since 3.1.9
		 * @return array
		 */
		private function get_funnel_field_labels() {
			return array(
				'funnel_id'                     => __( 'Funnel ID', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_funnel_name'         => __( 'Funnel Name', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_funnel_status'      => __( 'Status', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_target_pro_ids'      => __( 'Target Product IDs', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_target_cat_ids'      => __( 'Target Category IDs', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_products_in_offer'   => __( 'Offer Product IDs', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_pro_products_in_offer' => __( 'Offer Product IDs', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_pro_add_products_in_offer' => __( 'Frequently Bought Offer Product IDs', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_global_funnel'       => __( 'Global Funnel', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_exclusive_offer'     => __( 'Exclusive Offer', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_smart_offer_upgrade' => __( 'Smart Offer Upgrade', 'upsell-order-bump-offer-for-woocommerce' ),
			);
		}

		/**
		 * Field descriptions for funnel JSON exports.
		 *
		 * @since 3.2.0
		 * @return array
		 */
		private function get_funnel_json_field_comments() {
			return array(
				'funnel_id'                     => __( 'Leave empty when importing; the plugin will assign the next numeric ID.', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_funnel_name'         => __( 'Name of the funnel (plain text).', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_upsell_funnel_status'      => __( 'Use "yes" for live funnels or "no" to keep them off.', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_target_pro_ids'      => __( 'Product IDs that trigger the funnel. Provide an array of product IDs.', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_target_cat_ids'      => __( 'Category IDs that trigger the funnel. Provide an array of category IDs.', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_products_in_offer'   => __( 'Product IDs offered inside this funnel (array).', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_pro_products_in_offer' => __( 'Additional offer product IDs for the Pro layout (array).', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_pro_add_products_in_offer' => __( 'Frequently bought together product IDs (array).', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_global_funnel'       => __( 'Use "yes" to treat this as a global funnel fallback, otherwise "no".', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_exclusive_offer'     => __( 'Use "yes" to prevent this funnel from showing with other funnels.', 'upsell-order-bump-offer-for-woocommerce' ),
				'wps_wocuf_smart_offer_upgrade' => __( 'Use "yes" to enable smart upgrade logic for matching products.', 'upsell-order-bump-offer-for-woocommerce' ),
			);
		}

	/**
	 * Normalize funnel data types after CSV import.
	 *
	 * @since 3.1.9
	 * @param array $row Funnel row.
	 * @return array
	 */
	private function normalize_funnel_row( $row ) {
			$array_fields = array(
				'wps_wocuf_target_pro_ids',
				'wps_wocuf_target_cat_ids',
				'wps_wocuf_products_in_offer',
				'wps_wocuf_pro_products_in_offer',
				'wps_wocuf_pro_add_products_in_offer',
				'wps_wocuf_offer_discount_price',
				'wps_wocuf_attached_offers_on_buy',
				'wps_wocuf_attached_offers_on_no',
				'wps_wocuf_pro_offer_template',
				'wps_wocuf_offer_custom_page_url',
				'wps_wocuf_applied_offer_number',
				'wps_upsell_post_id_assigned',
				'wps_upsell_offer_image',
			);

		$try_decode_json = static function( $value ) {
			if ( is_string( $value ) && ( 0 === strpos( $value, '{' ) || 0 === strpos( $value, '[' ) ) ) {
				$decoded = json_decode( $value, true );
				if ( null !== $decoded ) {
					return $decoded;
				}
			}
			return null;
		};

		foreach ( $array_fields as $field ) {
			if ( ! array_key_exists( $field, $row ) ) {
				continue;
			}

			$value = $row[ $field ];

			// Try to decode JSON when the value is a string.
			if ( is_string( $value ) ) {
				$decoded = $try_decode_json( $value );
				if ( null !== $decoded ) {
					$row[ $field ] = $decoded;
					continue;
				}

				if ( false !== strpos( $value, '|' ) ) {
					$row[ $field ] = array_filter( array_map( 'trim', explode( '|', $value ) ) );
					continue;
				}

				$row[ $field ] = '' === trim( $value ) ? array() : array( trim( $value ) );
				continue;
			}

			if ( is_array( $value ) ) {
				// Handle array with single JSON or pipe string entry.
				if ( 1 === count( $value ) ) {
					$first = reset( $value );
					if ( is_string( $first ) ) {
						$decoded = $try_decode_json( $first );
						if ( null !== $decoded ) {
							$row[ $field ] = $decoded;
							continue;
						}

						if ( false !== strpos( $first, '|' ) ) {
							$row[ $field ] = array_filter( array_map( 'trim', explode( '|', $first ) ) );
							continue;
						}

						$row[ $field ] = '' === trim( $first ) ? array() : array( trim( $first ) );
						continue;
					}
				}

				// Already a usable array.
				continue;
			}

			$row[ $field ] = array();
		}

		return $row;
	}

	/**
	 * Decide if a bump key should be excluded from export/import.
	 *
	 * @since 3.1.9
	 * @param string $key Key name.
	 * @return bool
	 */
	private function should_skip_bump_export_key( $key ) {
		$skip_keys = array(
			'bump_orders_count',
			'wps_abandoned_session_id',
			'wps_abandoned_session_time',
			'wps_abandon_cart_data',
			'wps_ubo_abandoned_cart',
			'wps_abandoned_cart_products',
			'wps_abandon_bump_status',
			'wps_abandoned_cart_total',
			'wps_abandoned_cart_subtotal',
			'wps_upsell_bump_sales_count',
			'wps_upsell_bump_qty_count',
			'wps_upsell_abandon_count',
			'wps_abandoned_cart_currency',
		);

		$normalized = sanitize_key( $key );

		return in_array( $normalized, $skip_keys, true );
	}

	/**
	 * Toggle bump status via AJAX from list.
	 *
	 * @since 3.1.9
	 */
	public function handle_bump_status_toggle() {
		check_ajax_referer( 'wps_admin_nonce', 'security' );

		if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized', 'upsell-order-bump-offer-for-woocommerce' ) ) );
		}

		$bump_id     = isset( $_POST['bump_id'] ) ? sanitize_text_field( wp_unslash( $_POST['bump_id'] ) ) : '';
		$new_status  = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'no';
		$valid_state = in_array( $new_status, array( 'yes', 'no' ), true ) ? $new_status : 'no';

		$existing_bumps = get_option( 'wps_ubo_bump_list', array() );

		if ( empty( $bump_id ) || ! is_array( $existing_bumps ) || ! isset( $existing_bumps[ $bump_id ] ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid bump.', 'upsell-order-bump-offer-for-woocommerce' ) ) );
		}

		$existing_bumps[ $bump_id ]['wps_upsell_bump_status'] = $valid_state;

		update_option( 'wps_ubo_bump_list', $existing_bumps );

		$label = 'yes' === $valid_state ? __( 'Live', 'upsell-order-bump-offer-for-woocommerce' ) : __( 'Sandbox', 'upsell-order-bump-offer-for-woocommerce' );

		wp_send_json_success(
			array(
				'status' => $valid_state,
				'label'  => $label,
			)
		);
	}

	/**
	 * Get the bump list page URL for redirects.
	 *
	 * @since 3.1.9
	 * @return string
	 */
	private function get_bump_list_redirect_url() {
		$query_args = array(
			'page'    => 'upsell-order-bump-offer-for-woocommerce-setting',
			'tab'     => 'order-bump-section',
			'sub_tab' => 'pre-list-offer-section',
		);

		return add_query_arg( $query_args, admin_url( 'admin.php' ) );
	}

	/**
	 * Get highest numeric bump index.
	 *
	 * @since 3.1.9
	 * @param array $existing_bumps Existing bumps array.
	 * @return int
	 */
		private function get_last_bump_index( $existing_bumps ) {
			$last_index = 0;

			if ( is_array( $existing_bumps ) && ! empty( $existing_bumps ) ) {
				foreach ( array_keys( $existing_bumps ) as $bump_key ) {
				if ( is_numeric( $bump_key ) ) {
					$last_index = max( $last_index, (int) $bump_key );
				}
			}
		}

			return $last_index;
		}

		/**
		 * Normalize bump data after CSV/JSON import.
		 *
		 * @since 3.1.9
		 * @param array $row Bump row.
		 * @return array
		 */
		private function normalize_bump_row( $row ) {
			if ( ! is_array( $row ) ) {
				return array();
			}

			$array_fields = array(
				'wps_upsell_bump_target_ids',
				'wps_upsell_bump_target_categories',
				'wps_upsell_bump_products_in_offer',
			);

			foreach ( $array_fields as $field ) {
				if ( ! array_key_exists( $field, $row ) ) {
					continue;
				}

				$value = $row[ $field ];

				if ( is_string( $value ) ) {
					if ( false !== strpos( $value, '|' ) ) {
						$row[ $field ] = array_filter( array_map( 'trim', explode( '|', $value ) ) );
					} else {
						$row[ $field ] = '' === trim( $value ) ? array() : array( trim( $value ) );
					}
				}

				if ( ! is_array( $row[ $field ] ) ) {
					$row[ $field ] = array_filter( (array) $row[ $field ] );
				}
			}

			// Downstream expects single ID for offer product.
			if ( isset( $row['wps_upsell_bump_products_in_offer'] ) && is_array( $row['wps_upsell_bump_products_in_offer'] ) ) {
				$first_product = reset( $row['wps_upsell_bump_products_in_offer'] );
				$row['wps_upsell_bump_products_in_offer'] = ! empty( $first_product ) ? $first_product : '';
			}

			return $row;
		}

}

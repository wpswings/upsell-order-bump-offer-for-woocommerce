<?php
/**
 * Gamified bump offers - admin "Gamified Offers" tab.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      3.1.8
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin
 * @author     WP Swings <webmaster@wpswings.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin tab for gamified bump formats.
 *
 * @since 3.1.8
 */
class Upsell_Order_Bump_Gamified_Admin {

	/**
	 * Tab slug.
	 *
	 * @var string
	 */
	const TAB = 'gamified-offers';

	/**
	 * Settings page slug.
	 *
	 * @var string
	 */
	const PAGE = 'upsell-order-bump-offer-for-woocommerce-setting';

	/**
	 * Nonce action.
	 *
	 * @var string
	 */
	const NONCE_ACTION = 'wps_ubo_gamified_save';

	/**
	 * Capability.
	 *
	 * @var string
	 */
	const CAPABILITY = 'manage_woocommerce';

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Constructor.
	 *
	 * @param string $version Plugin version.
	 */
	public function __construct( $version ) {
		$this->version = $version;
	}

	/**
	 * Whether the current admin screen is this tab.
	 *
	 * @return bool
	 */
	private function is_tab() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only routing.
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		$tab  = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';
		// phpcs:enable
		return self::PAGE === $page && self::TAB === $tab;
	}

	/**
	 * Tab URL.
	 *
	 * @param array $args Extra query args.
	 * @return string
	 */
	public static function url( $args = array() ) {
		return add_query_arg(
			array_merge(
				array(
					'page' => self::PAGE,
					'tab'  => self::TAB,
				),
				$args
			),
			admin_url( 'admin.php' )
		);
	}

	/**
	 * Action: wps_ubo_setting_tab — nav link.
	 *
	 * @return void
	 */
	public function render_tab_link() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			return;
		}
		?>
		<a class="nav-tab <?php echo $this->is_tab() ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( self::url() ); ?>"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
<circle cx="12" cy="12" r="8.5" stroke="#303030"/>
<path d="M12 3.5V12L18 6M12 12L20.5 12M12 12L6 18" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
<circle cx="12" cy="12" r="1.5" fill="#303030"/>
</svg>
<?php esc_html_e( 'Gamified Offers', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
		<?php
	}

	/**
	 * Action: admin_init — save settings / reset statistics (POST → redirect → GET).
	 *
	 * @return void
	 */
	public function handle_post() {
		if ( empty( $_POST['wps_ubo_gamified_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified below.
			return;
		}
		check_admin_referer( self::NONCE_ACTION, 'wps_ubo_gamified_nonce' );
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'upsell-order-bump-offer-for-woocommerce' ), 403 );
		}

		$action = sanitize_key( wp_unslash( $_POST['wps_ubo_gamified_action'] ) );

		if ( 'reset_stats' === $action ) {
			delete_option( Upsell_Order_Bump_Gamified::STATS_OPTION );
			wp_safe_redirect( self::url( array( 'wps_notice' => 'reset' ) ) );
			exit;
		}

		$raw = array(
			'enabled'                  => isset( $_POST['enabled'] ) ? 'on' : 'off',
			'guarantee_offer_discount' => isset( $_POST['guarantee_offer_discount'] ) ? 'on' : 'off',
			'allow_skip'               => isset( $_POST['allow_skip'] ) ? 'on' : 'off',
			'max_discount'             => isset( $_POST['max_discount'] ) ? sanitize_text_field( wp_unslash( $_POST['max_discount'] ) ) : '',
			'formats'                  => isset( $_POST['formats'] ) && is_array( $_POST['formats'] ) ? map_deep( wp_unslash( $_POST['formats'] ), 'sanitize_text_field' ) : array(),
			'prizes'                   => isset( $_POST['prizes'] ) && is_array( $_POST['prizes'] ) ? map_deep( wp_unslash( $_POST['prizes'] ), 'sanitize_text_field' ) : array(),
		);
		foreach ( array( 'headline_wheel', 'headline_scratch', 'button_wheel', 'button_scratch', 'win_message', 'color_primary', 'color_accent', 'color_text' ) as $key ) {
			$raw[ $key ] = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
		}

		update_option( Upsell_Order_Bump_Gamified::SETTINGS_OPTION, Upsell_Order_Bump_Gamified::sanitize_settings( $raw ) );
		wp_safe_redirect( self::url( array( 'wps_notice' => 'saved' ) ) );
		exit;
	}

	/**
	 * Action: admin_enqueue_scripts.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		if ( ! $this->is_tab() ) {
			return;
		}
		$base = plugin_dir_url( __DIR__ );

		// The live preview runs the real front-end widget.
		Upsell_Order_Bump_Gamified_Public::register_assets( $this->version );
		wp_enqueue_style( 'wps-ubo-gamified' );
		wp_enqueue_script( 'wps-ubo-gamified' );

		wp_enqueue_style( 'wps-ubo-gamified-admin', $base . 'admin/css/wps-ubo-gamified-admin.css', array( 'wps-ubo-gamified' ), $this->version );
		wp_enqueue_script( 'wps-ubo-gamified-admin', $base . 'admin/js/wps-ubo-gamified-admin.js', array(), $this->version, true );
		wp_localize_script(
			'wps-ubo-gamified-admin',
			'wpsUboGameAdmin',
			array(
				'min'           => Upsell_Order_Bump_Gamified::MIN_PRIZES,
				'max'           => Upsell_Order_Bump_Gamified::MAX_PRIZES,
				'confirmReset'  => __( 'Reset all gamified offer statistics?', 'upsell-order-bump-offer-for-woocommerce' ),
				'minPrizes'     => __( 'Keep at least two prizes.', 'upsell-order-bump-offer-for-woocommerce' ),
				'maxPrizes'     => __( 'The wheel supports up to 12 prizes.', 'upsell-order-bump-offer-for-woocommerce' ),
				'noWeight'      => __( 'At least one prize needs a weight above 0.', 'upsell-order-bump-offer-for-woocommerce' ),
			)
		);
	}

	/**
	 * Action: wps_ubo_lite_setting_tab_html — tab content.
	 *
	 * @return void
	 */
	public function render_tab_content() {
		if ( ! $this->is_tab() || ! current_user_can( self::CAPABILITY ) ) {
			return;
		}

		$settings = Upsell_Order_Bump_Gamified::get_settings();
		$stats    = Upsell_Order_Bump_Gamified::get_stats();
		$bumps    = Upsell_Order_Bump_Gamified::raw_bumps();
		$lite     = ! wps_ubo_lite_if_pro_exists();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display only.
		$notice = isset( $_GET['wps_notice'] ) ? sanitize_key( wp_unslash( $_GET['wps_notice'] ) ) : '';

		// Preview data (admin only: weights are never printed on the front end).
		$sample = '';
		foreach ( $bumps as $bump ) {
			if ( ! empty( $bump['wps_upsell_bump_products_in_offer'] ) ) {
				$product = wc_get_product( $bump['wps_upsell_bump_products_in_offer'] );
				$sample  = $product ? $product->get_name() : '';
				break;
			}
		}
		$preview = array(
			'segments' => array(),
			'weights'  => array(),
			'results'  => array(),
			'best'     => 0,
		);
		foreach ( $settings['prizes'] as $row ) {
			$discount              = min( (int) $settings['max_discount'], (int) $row['discount'] );
			$preview['segments'][] = array(
				'label' => $row['label'],
				'color' => $row['color'],
			);
			$preview['weights'][]  = (int) $row['weight'];
			$preview['results'][]  = array(
				'label'    => $row['label'],
				'discount' => $discount,
				'message'  => $discount > 0
					? strtr(
						$settings['win_message'],
						array(
							'{discount}' => $discount . '%',
							'{label}'    => $row['label'],
							'{product}'  => $sample ? $sample : __( 'your offer', 'upsell-order-bump-offer-for-woocommerce' ),
						)
					)
					: __( 'Here is your offer!', 'upsell-order-bump-offer-for-woocommerce' ),
			);
			$preview['best']       = max( $preview['best'], $discount );
		}

		include plugin_dir_path( __FILE__ ) . 'partials/templates/wps-ubo-gamified-display.php';
	}
}

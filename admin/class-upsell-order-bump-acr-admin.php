<?php
/**
 * Native cart abandonment upsell recovery - admin "Cart Recovery" tab.
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
 * Admin tab for native cart recovery.
 *
 * @since 3.1.8
 */
class Upsell_Order_Bump_Acr_Admin {

	/**
	 * Tab slug, page slug, nonce, capability.
	 *
	 * @var string
	 */
	const TAB          = 'cart-recovery';
	const PAGE         = 'upsell-order-bump-offer-for-woocommerce-setting';
	const NONCE_ACTION = 'wps_ubo_acr_admin';
	const CAPABILITY   = 'manage_woocommerce';

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
	 * Current screen is this tab.
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
	 * @param array $args Query args.
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
	 * Whether the CartFlows "WooCommerce Cart Abandonment Recovery" plugin is active.
	 *
	 * @return bool
	 */
	public static function external_plugin_active() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return is_plugin_active( 'woo-cart-abandonment-recovery/woo-cart-abandonment-recovery.php' );
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
<path d="M3 4H5L7.4 15.2C7.5 15.7 7.9 16 8.4 16H18L20 8H6" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
<circle cx="9" cy="20" r="1.3" stroke="#303030"/><circle cx="17" cy="20" r="1.3" stroke="#303030"/>
<path d="M11 11.5C11 10.1 12.1 9 13.5 9C14.4 9 15.2 9.5 15.6 10.2M16 9V10.5H14.5" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
<?php esc_html_e( 'Cart Recovery', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
		<?php
	}

	/**
	 * Action: admin_init — form posts (POST → redirect → GET).
	 *
	 * @return void
	 */
	public function handle_post() {
		if ( empty( $_POST['wps_ubo_acr_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified below.
			return;
		}
		check_admin_referer( self::NONCE_ACTION, 'wps_ubo_acr_nonce' );
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'upsell-order-bump-offer-for-woocommerce' ), 403 );
		}
		$action = sanitize_key( wp_unslash( $_POST['wps_ubo_acr_action'] ) );

		if ( 'save' === $action ) {
			$raw = isset( $_POST['acr'] ) && is_array( $_POST['acr'] ) ? wp_unslash( $_POST['acr'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized field by field in sanitize_settings().
			update_option( Upsell_Order_Bump_Acr_Db::SETTINGS_OPTION, Upsell_Order_Bump_Acr_Db::sanitize_settings( $raw ), false );
			Upsell_Order_Bump_Acr_Db::reschedule();
			wp_safe_redirect( self::url( array( 'acr_notice' => 'saved' ) ) );
			exit;
		}

		if ( 'test' === $action ) {
			$index = isset( $_POST['step'] ) ? absint( $_POST['step'] ) : 0;
			$user  = wp_get_current_user();
			$ok    = Upsell_Order_Bump_Acr_Mailer::send_step( Upsell_Order_Bump_Acr_Mailer::sample_row(), min( 2, $index ), $user->user_email );
			wp_safe_redirect( self::url( array( 'acr_notice' => $ok ? 'test_sent' : 'test_failed' ) ) );
			exit;
		}

		$id = isset( $_POST['cart_id'] ) ? absint( $_POST['cart_id'] ) : 0;
		if ( 'delete' === $action && $id ) {
			Upsell_Order_Bump_Acr_Db::delete( $id );
			wp_safe_redirect( self::url( array( 'acr_notice' => 'deleted' ) ) );
			exit;
		}
		if ( 'send_now' === $action && $id ) {
			$result = Upsell_Order_Bump_Acr_Engine::send_next( $id );
			wp_safe_redirect( self::url( array( 'acr_notice' => 'send_' . $result ) ) );
			exit;
		}
	}

	/**
	 * Action: admin_post_wps_ubo_acr_preview — render an email step in the browser.
	 *
	 * @return void
	 */
	public function preview() {
		check_admin_referer( self::NONCE_ACTION );
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'upsell-order-bump-offer-for-woocommerce' ), 403 );
		}
		$index = isset( $_GET['step'] ) ? min( 2, absint( $_GET['step'] ) ) : 0;
		$email = Upsell_Order_Bump_Acr_Mailer::render( Upsell_Order_Bump_Acr_Mailer::sample_row(), $index );
		$obj   = new WC_Email();
		echo $obj->style_inline( $email['html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Email HTML built from escaped parts.
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
		wp_enqueue_style( 'woocommerce_admin_styles' );
		wp_enqueue_script( 'wc-enhanced-select' );
		wp_enqueue_style( 'wps-ubo-acr-admin', $base . 'admin/css/wps-ubo-acr-admin.css', array(), $this->version );
		wp_enqueue_script( 'wps-ubo-acr-admin', $base . 'admin/js/wps-ubo-acr-admin.js', array( 'jquery', 'wc-enhanced-select' ), $this->version, true );
		wp_localize_script(
			'wps-ubo-acr-admin',
			'wpsUboAcrAdmin',
			array(
				'confirmDelete' => __( 'Delete this saved cart?', 'upsell-order-bump-offer-for-woocommerce' ),
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
		Upsell_Order_Bump_Acr_Db::maybe_upgrade();

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Display filters only.
		$notice = isset( $_GET['acr_notice'] ) ? sanitize_key( wp_unslash( $_GET['acr_notice'] ) ) : '';
		$status = isset( $_GET['acr_status'] ) ? sanitize_key( wp_unslash( $_GET['acr_status'] ) ) : '';
		$paged  = isset( $_GET['acr_page'] ) ? max( 1, absint( $_GET['acr_page'] ) ) : 1;
		// phpcs:enable

		$settings = Upsell_Order_Bump_Acr_Db::get_settings();
		$stats    = Upsell_Order_Bump_Acr_Db::stats( 30 );
		$list     = Upsell_Order_Bump_Acr_Db::list_rows( $status, $paged, 20 );
		$external = self::external_plugin_active();
		$reco_on  = class_exists( 'Upsell_Order_Bump_Reco_Db' ) && Upsell_Order_Bump_Reco_Db::is_enabled() && Upsell_Order_Bump_Reco_Db::has_model();
		$roles    = wp_roles()->get_names();

		include plugin_dir_path( __FILE__ ) . 'partials/templates/wps-ubo-acr-display.php';
	}
}

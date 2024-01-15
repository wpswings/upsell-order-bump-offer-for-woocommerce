<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      1.0.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ONBOARD_PLUGIN_NAME', 'Upsell Order Bump Offer for WooCommerce' );

if ( class_exists( 'Wpswings_Onboarding_Helper' ) ) {
	$this->onboard = new Wpswings_Onboarding_Helper();
}

$secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

if ( ! $id_nonce_verified ) {
	wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
}

$wps_ubo_lite_active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'bump-list';

if ( 'overview' === get_transient( 'wps_ubo_lite_default_settings_tab' ) ) {

	$wps_ubo_lite_active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'overview';
}

do_action( 'wps_ubo_lite_tab_active' );

?>
<div class="wrap woocommerce" id="wps_upsell_bump_setting_wrapper">
	<div class="wps_upsell_bump_setting_title"><?php echo esc_html( apply_filters( 'wps_ubo_lite_heading', esc_html__( 'Upsell Order Bump Offers', 'upsell-order-bump-offer-for-woocommerce' ) ) ); ?>
	</div>

	<nav class="nav-tab-wrapper woo-nav-tab-wrapper">

		<a class="nav-tab <?php echo esc_html( 'creation-setting' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting"><?php esc_html_e( 'Save Order Bump', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

		<a class="nav-tab <?php echo esc_html( 'bump-list' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=bump-list"><?php esc_html_e( 'Order Bumps List', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

		<a class="nav-tab <?php echo esc_html( 'settings' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=settings"><?php esc_html_e( 'Global Settings', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

		<!-- If Org version set overview tab. -->
		<a class="nav-tab <?php echo esc_html( 'overview' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=overview"><?php esc_html_e( 'Overview', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

		<?php do_action( 'wps_ubo_setting_tab' ); ?>
		<?php
		$global_custom_css = 'const triggerError = () => {
            swal({
                title: "Attention Required!",
                text: "The premium version you are using is too old! Please Update the plugin. You should be getting the update now button for now.",
                icon: "error",
                button: "Go to update page",
				closeOnClickOutside: false,
            }).then(function() {
				window.location = "' . admin_url( 'plugins.php' ) . '";
			});
        }
        triggerError();';
		wp_register_script( 'wps_upsell_incompatible_css', false, array(), WC_VERSION, 'all' );
		wp_enqueue_script( 'wps_upsell_incompatible_css' );
		wp_add_inline_script( 'wps_upsell_incompatible_css', $global_custom_css );
		?>
	</nav>

</div>

<!-- Connect us on skype. -->
<div id="wps_ubo_lite_skype_connect_with_us">   
	<div class="wps_ubo_lite_skype_connect_title"><?php esc_html_e( 'Connect with Us in one click', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>

	<a class="button" target="_blank" href="https://wa.me/message/JSDF7KNKMUSKA1"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/whatsapp.gif' ); ?>"><?php esc_html_e( 'Connect', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

	<p><?php esc_html_e( 'Regarding any issue, query or feature request for Order Bump Offers.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
	<div class="wps_ubo_lite_skype_setting"><span class="dashicons dashicons-admin-generic"></span></div>
</div>

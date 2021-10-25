<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com/?utm_source=MWB-orderbump-backend&utm_medium=MWB-Site-backend&utm_campaign=MWB-backend
 * @since      1.4.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin/reporting
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$secure_nonce      = wp_create_nonce( 'mwb-upsell-auth-nonce' );
$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'mwb-upsell-auth-nonce' );

if ( ! $id_nonce_verified ) {
	wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
}

$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'reporting';

?>

<div class="wrap woocommerce" id="mwb_upsell_bump_setting_wrapper">
	<div class="mwb_upsell_bump_setting_title"><?php echo esc_html( apply_filters( 'mwb_ubo_lite_heading', esc_html__( 'Upsell Order Bump Offers', 'upsell-order-bump-offer-for-woocommerce' ) ) ); ?>
	</div>

	<nav class="nav-tab-wrapper woo-nav-tab-wrapper">

		<a class="nav-tab <?php echo esc_html( 'reporting' === $active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-reporting&tab=reporting"><?php esc_html_e( 'Sales Reports', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

	</nav>

	<!-- For notification control. -->
	<h1></h1>

	<?php

	if ( 'reporting' === $active_tab ) {
		include_once 'templates/reporting.php';
	}

	?>
</div>

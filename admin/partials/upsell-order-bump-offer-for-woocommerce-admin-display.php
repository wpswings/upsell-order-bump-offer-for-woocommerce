<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) 
{
	exit;
}

$mwb_ubo_lite_active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'bump-list';

if( 'overview' == get_transient( 'mwb_ubo_lite_default_settings_tab' ) ) {

    $mwb_ubo_lite_active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'overview';
}

do_action( 'mwb_ubo_lite_tab_active' );

?>
<div class="wrap woocommerce" id="mwb_upsell_bump_setting_wrapper">
    <div class="mwb_upsell_bump_setting_title"><?php esc_html_e( apply_filters( 'mwb_ubo_lite_heading', esc_html__( 'Upsell Order Bump Offers', 'upsell-order-bump-offer-for-woocommerce' ) ) ); ?>
    <span class="mwb_upsell_bump_setting_title_version"><?php esc_html_e( 'v', 'upsell-order-bump-offer-for-woocommerce'); esc_html_e( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_VERSION ); ?></span>
    </div>

	<nav class="nav-tab-wrapper woo-nav-tab-wrapper">

		<a class="nav-tab <?php esc_html_e( 'creation-setting' == $mwb_ubo_lite_active_tab  ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting"><?php esc_html_e( 'Save Order Bump', 'upsell-order-bump-offer-for-woocommerce' );?></a>

		<a class="nav-tab <?php esc_html_e( 'bump-list' == $mwb_ubo_lite_active_tab  ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=bump-list"><?php esc_html_e( 'Order Bumps List', 'upsell-order-bump-offer-for-woocommerce' );?></a>

		<a class="nav-tab <?php esc_html_e( 'settings' == $mwb_ubo_lite_active_tab  ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=settings"><?php esc_html_e(  'Global Settings', 'upsell-order-bump-offer-for-woocommerce' );?></a>

		<?php if( class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) ) {

                $mwb_upsell_bump_callname_lic = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$mwb_upsell_bump_lic_callback_function;
                
                $mwb_upsell_bump_callname_lic_initial = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$mwb_upsell_bump_lic_ini_callback_function;

                $day_count = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$mwb_upsell_bump_callname_lic_initial();
            } 
        ?>

        <!-- If premium version is available, set license tab. -->
        <?php if( is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) && ! Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$mwb_upsell_bump_callname_lic() ) : ?>

            <a class="nav-tab <?php esc_html_e( 'license' == $mwb_ubo_lite_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=license"><?php esc_html_e( 'License', 'upsell-order-bump-offer-for-woocommerce' );?></a>

        <?php endif; ?>

        <!-- If Org version set overview tab. -->
        <a class="nav-tab <?php esc_html_e( 'overview' == $mwb_ubo_lite_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=overview"><?php esc_html_e( 'Overview', 'upsell-order-bump-offer-for-woocommerce' );?></a>

		<?php do_action( 'mwb_ubo_setting_tab' ); ?>

	</nav>

    <!-- For notification control. -->
	<h1></h1>

	<?php

        if( is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) && class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) ) {

            // If license is activated or trial period is remaining.
            if( 'creation-setting' == $mwb_ubo_lite_active_tab )
            { 
                // Include creation file from pro version.
                include_once( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_DIRPATH . '/admin/partials/templates/mwb-upsell-bump-creation.php' );
            }
            elseif( 'bump-list' == $mwb_ubo_lite_active_tab )
            {   
                // Include listing file from pro version.
                include_once( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_DIRPATH . '/admin/partials/templates/mwb-upsell-bump-list.php' );
            }
            elseif( 'settings' == $mwb_ubo_lite_active_tab )
            {   
                // Include setting file from org version.
                include_once ( 'templates/mwb-ubo-lite-settings.php' );
            }
            elseif( 'overview' == $mwb_ubo_lite_active_tab )
            {
                include_once 'templates/mwb-ubo-lite-overview.php';
            }

            if( ! Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$mwb_upsell_bump_callname_lic() ) {

                if( 'license' == $mwb_ubo_lite_active_tab )
                {   
                    // Include license file from pro version.
                    include_once( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_DIRPATH . '/admin/partials/templates/mwb-upsell-bump-license.php' );
                }
            }
            
        } else {

            // Org files.
            if( 'creation-setting' == $mwb_ubo_lite_active_tab )
            {
                include_once 'templates/mwb-ubo-lite-creation.php';
            } 
            elseif( 'bump-list' == $mwb_ubo_lite_active_tab )
            {
                include_once 'templates/mwb-ubo-lite-list.php';
            }
            elseif( 'settings' == $mwb_ubo_lite_active_tab )
            {
                include_once 'templates/mwb-ubo-lite-settings.php';
            }
            elseif( 'overview' == $mwb_ubo_lite_active_tab )
            {
                include_once 'templates/mwb-ubo-lite-overview.php';
            }
        }

		do_action( 'mwb_ubo_lite_setting_tab_html' );
	?>
</div>

<!-- Connect us on skype. -->
<div id="mwb_ubo_lite_skype_connect_with_us">   
    <div class="mwb_ubo_lite_skype_connect_title"><?php esc_html_e( 'Connect with Us in one click', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>

    <a class="button" target="_blank" href="https://join.skype.com/invite/IKVeNkLHebpC"><img src="<?php esc_html_e( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . "admin/resources/logo/skype_logo.png" ); ?>"><?php esc_html_e( 'Connect', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

    <p><?php esc_html_e( 'Regarding any issue, query or feature request for Order Bump Offers.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
    <div class="mwb_ubo_lite_skype_setting"><span class="dashicons dashicons-admin-generic"></span></div>
</div>
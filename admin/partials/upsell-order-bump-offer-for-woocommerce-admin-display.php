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

$mwb_ubo_lite_active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] :'settings';

do_action('mwb_ubo_lite_tab_active');

?>
<div class="wrap woocommerce" id="mwb_upsell_bump_setting_wrapper">
	<div class="mwb_upsell_bump_setting_title"><?php esc_html_e( 'Upsell Order Bump Offers', 'upsell-order-bump-offer-for-woocommerce' ); ?>
        <span class="mwb_upsell_bump_setting_title_version"><?php esc_html_e( 'v', 'upsell-order-bump-offer-for-woocommerce'); echo UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_VERSION; ?></span>
    </div>

	<nav class="nav-tab-wrapper woo-nav-tab-wrapper">

		<a class="nav-tab <?php echo $mwb_ubo_lite_active_tab == 'creation-setting' ? 'nav-tab-active' : ''; ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting"><?php _e('Save Order Bump', 'upsell-order-bump-offer-for-woocommerce');?></a>

		<a class="nav-tab <?php echo $mwb_ubo_lite_active_tab == 'bump-list' ? 'nav-tab-active' : ''; ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=bump-list"><?php _e('Order Bumps List', 'upsell-order-bump-offer-for-woocommerce');?></a>

		<a class="nav-tab <?php echo $mwb_ubo_lite_active_tab == 'settings' ? 'nav-tab-active' : ''; ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=settings"><?php _e('Global Settings', 'upsell-order-bump-offer-for-woocommerce');?></a>
		
        <!-- If premium version is available -->
        <?php if( is_plugin_active( 'woocommerce-upsell-order-bump-offer-pro/woocommerce-upsell-order-bump-offer-pro.php' ) ) : ?>

            <a class="nav-tab <?php echo $mwb_ubo_lite_active_tab == 'license' ? 'nav-tab-active' : ''; ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=license"><?php _e('License', 'upsell-order-bump-offer-for-woocommerce');?></a>

        <?php else : ?>

            <a class="nav-tab <?php echo $mwb_ubo_lite_active_tab == 'overview' ? 'nav-tab-active' : ''; ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=overview"><?php _e('Overview', 'upsell-order-bump-offer-for-woocommerce');?></a>

        <?php endif; ?>

		<?php do_action('mwb_ubo_setting_tab'); ?>

	</nav>

    <!-- For notification control. -->
	<h1></h1>

	<?php 

        // If premium version is available.
        if( is_plugin_active( 'woocommerce-upsell-order-bump-offer-pro/woocommerce-upsell-order-bump-offer-pro.php' ) ) {

            if( $mwb_ubo_lite_active_tab == 'creation-setting' )
            {   
                // Include creation file from pro version.
                include_once( MWB_UPSELL_BUMP_OFFER_DIRPATH . '/admin/partials/templates/mwb_upsell_bump_creation.php' );
            } 
            elseif( $mwb_ubo_lite_active_tab == 'bump-list')
            {   
                // Include listing file from pro version.
                include_once( MWB_UPSELL_BUMP_OFFER_DIRPATH . '/admin/partials/templates/mwb_upsell_bump_list.php' );
            }
            elseif( $mwb_ubo_lite_active_tab == 'settings')
            {   
                // Include setting file from org version.
                include_once 'templates/mwb_ubo_lite_settings.php';
            }
            elseif( $mwb_ubo_lite_active_tab == 'license' )
            {   
                // Include license file from pro version.
                include_once( MWB_UPSELL_BUMP_OFFER_DIRPATH . '/admin/partials/templates/mwb_upsell_bump-license.php' );
            }

        } else {

            if( $mwb_ubo_lite_active_tab == 'creation-setting' )
            {
                include_once 'templates/mwb_ubo_lite_creation.php';
            } 
            elseif( $mwb_ubo_lite_active_tab == 'bump-list')
            {
                include_once 'templates/mwb_ubo_lite_list.php';
            }
            elseif( $mwb_ubo_lite_active_tab == 'settings')
            {
                include_once 'templates/mwb_ubo_lite_settings.php';
            }
            elseif( $mwb_ubo_lite_active_tab == 'overview' )
            {
                include_once 'templates/mwb_ubo_lite_overview.php';
            }
        }

		do_action('mwb_ubo_lite_setting_tab_html');
	?>
</div>

<!-- Connect us on skype. -->
<div id="mwb_ubo_lite_skype_connect_with_us">   
    <div class="mwb_ubo_lite_skype_connect_title"><?php esc_html_e( 'Connect with Us in one click', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>

    <a class="button" target="_blank" href="https://join.skype.com/invite/IKVeNkLHebpC"><img src="<?php echo UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . "admin/resources/logo/skype_logo.png"; ?>"><?php esc_html_e( 'Connect', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

    <p><?php esc_html_e( 'Regarding any issue, query or feature request for Bump Offers.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
    <div class="mwb_ubo_lite_skype_setting"><span class="dashicons dashicons-admin-generic"></span></div>
</div>
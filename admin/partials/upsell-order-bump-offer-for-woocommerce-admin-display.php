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

$secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

if ( ! $id_nonce_verified ) {
	wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
}

$wps_ubo_lite_active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';

if ( 'overview' === get_transient( 'wps_ubo_lite_default_settings_tab' ) ) {

	$wps_ubo_lite_active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'overview';
}

do_action( 'wps_ubo_lite_tab_active' );

?>
<div class="wrap woocommerce" id="wps_upsell_bump_setting_wrapper">
	<div class="wps_upsell_bump_setting_title"><?php echo esc_html( apply_filters( 'wps_ubo_lite_heading', esc_html__( 'Upsell Funnel Builder for WooCommerce ', 'upsell-order-bump-offer-for-woocommerce' ) ) ); ?>
	</div>

	<nav class="nav-tab-wrapper woo-nav-tab-wrapper">

	<div class="wps_main_global_wrapper">
		<a class="nav-tab <?php echo esc_html( 'general-setting' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=general-setting"><?php esc_html_e( 'General Setting', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
	</div>


		<!--Global Setting Tab start here --->
		<div class="wps_main_global_wrapper">
		<a class="nav-tab <?php echo esc_html( 'global-setting' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=global-setting&tab=global-setting&sub_tab=pre-global-sect"><?php esc_html_e( 'Global Setting', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

		<!-- Sub-tabs under Global Setting -->
		<?php if ( 'global-setting' === $wps_ubo_lite_active_tab ) : ?>
			
        <div class="nav-sub-tabs">
            <a class="sub-tab <?php echo esc_html( 'pre-global-sect' === $_GET['sub_tab'] ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=global-setting&sub_tab=pre-global-sect"><?php esc_html_e( 'Pre Bump Section', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
            <a class="sub-tab <?php echo esc_html( 'post-global-sect' === $_GET['sub_tab'] ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=global-setting&sub_tab=post-global-sect"><?php esc_html_e( 'Post Bump Section', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
        </div>
    <?php endif; ?>
		</div>
		<!--Global Setting Tab end here --->



		<!--Order Bump Section Tab start here --->
		<div class="wps_main_global_wrapper">
		<a class="nav-tab <?php echo esc_html( 'order-bump-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-list-offer-section"><?php esc_html_e( 'Order Bump Section', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

			<!-- Sub-tabs under Order Bump Section -->
			<?php if ( 'order-bump-section' === $wps_ubo_lite_active_tab ) : ?>
					
					<div class="nav-sub-tabs">
						<a class="sub-tab <?php echo esc_html( 'pre-list-offer-section' === $_GET['sub_tab'] ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-list-offer-section"><?php esc_html_e( 'Pre Bump List', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
						<a class="sub-tab <?php echo esc_html( 'pre-save-offer-section' === $_GET['sub_tab'] ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-save-offer-section"><?php esc_html_e( 'Save', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
					</div>
				<?php endif; ?>
		</div>
		<!-- Order Bump Section Tab start here --->



 		<!-- One Click Section Tab start here --->
		<div class="wps_main_global_wrapper">
		<a class="nav-tab <?php echo esc_html( 'one-click-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=one-click-section&sub_tab=post-list-offer-section"><?php esc_html_e( 'One Click Section', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

		<!-- Sub-tabs under One Click Section Tab -->
		<?php if ( 'one-click-section' === $wps_ubo_lite_active_tab ) : ?>
						
						<div class="nav-sub-tabs">
							<a class="sub-tab <?php echo esc_html( 'post-list-offer-section' === $_GET['sub_tab'] ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=one-click-section&sub_tab=post-list-offer-section"><?php esc_html_e( 'Post Bump List', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
							<a class="sub-tab <?php echo esc_html( 'post-save-offer-section' === $_GET['sub_tab'] ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=one-click-section&sub_tab=post-save-offer-section"><?php esc_html_e( 'Save', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
						</div>
					<?php endif; ?>
		 </div>
		<!-- One Click Section Tab end here --->


        <!-- Store Checkout Tab--->
		<a class="nav-tab <?php echo esc_html( 'store-checkout-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=store-checkout-section"><?php esc_html_e( 'Store Checkout Section', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

		 <!-- Shortcode Tab--->
		<a class="nav-tab <?php echo esc_html( 'shortcode-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=shortcode-section"><?php esc_html_e( 'Shortcode Section', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
		<?php
		if ( class_exists( 'Upsell_Order_Bump_Offer_For_Woocommerce_Pro' ) ) {

				$wps_upsell_bump_callname_lic = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_lic_callback_function;

				$wps_upsell_bump_callname_lic_initial = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_lic_ini_callback_function;

				$day_count = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_callname_lic_initial();
		}

		$plugin_version = wps_ubo_lite_if_pro_exists();
		?>

		<!-- If premium version is available, set license tab. -->
		<?php if ( $plugin_version && ! Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_callname_lic() ) : ?>

			<a class="nav-tab <?php echo esc_html( 'license' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=license"><?php esc_html_e( 'License', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

		<?php endif; ?>


		<!-- If Org version set overview tab. -->
		<a class="nav-tab <?php echo esc_html( 'overview' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : '' ); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=overview"><?php esc_html_e( 'Overview', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
		<?php do_action( 'wps_ubo_setting_tab' ); ?>

	</nav>

	<!-- For notification control. -->
	<h1></h1>
	<?php do_action( 'wps_ubo_migration_notice', '', '', '' ); ?>
	<?php

	if ( $plugin_version ) {
		$global_setting_sub_tab = isset( $_GET['sub_tab'] ) ? sanitize_text_field( wp_unslash( $_GET['sub_tab'] ) ) : 'pre-list-offer-section';

		// If license is activated or trial period is remaining.
		if ( 'creation-setting' === $wps_ubo_lite_active_tab ) {
			// Include creation file from pro version.
			include_once UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_DIRPATH . '/admin/partials/templates/wps-upsell-bump-creation.php';
		} elseif ( 'pre-list-offer-section' === $global_setting_sub_tab ) {
			// Include listing file from pro version.
			include_once UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_DIRPATH . '/admin/partials/templates/wps-upsell-bump-list.php';
		} elseif ( 'settings' === $wps_ubo_lite_active_tab ) {
			// Include setting file from org version.
			include_once 'templates/wps-ubo-lite-settings.php';
		} elseif ( 'overview' === $wps_ubo_lite_active_tab ) {
			// Include setting file from org version.
			include_once 'templates/wps-ubo-lite-overview.php';
		}

		if ( ! Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_callname_lic() ) {

			if ( 'license' === $wps_ubo_lite_active_tab ) {
				// Include license file from pro version.
				include_once UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_DIRPATH . '/admin/partials/templates/wps-upsell-bump-license.php';
			}
		}
	} else if ( ! $plugin_version ) {

		$global_setting_sub_tab = isset( $_GET['sub_tab'] ) ? sanitize_text_field( wp_unslash( $_GET['sub_tab'] ) ) : '';
		// var_dump($global_setting_sub_tab);
		// var_dump($wps_ubo_lite_active_tab);
		// Org files.
		if ( 'creation-setting' === $wps_ubo_lite_active_tab ) {
			include_once 'templates/wps-ubo-lite-creation.php';
		} elseif ( 'pre-list-offer-section' === $global_setting_sub_tab ) {
			include_once 'templates/wps-ubo-lite-list.php';
		} elseif ( 'settings' === $wps_ubo_lite_active_tab ) {
			include_once 'templates/wps-ubo-lite-settings.php';
		} elseif ( 'overview' === $wps_ubo_lite_active_tab ) {
			include_once 'templates/wps-ubo-lite-overview.php';
		}

		// Order Bump global setting rendering.
		if ( 'global-setting' === $wps_ubo_lite_active_tab ) {
			$global_setting_sub_tab = isset( $_GET['sub_tab'] ) ? sanitize_text_field( wp_unslash( $_GET['sub_tab'] ) ) : 'pre-global-sect';

			if ( 'pre-global-sect' === $global_setting_sub_tab ) {
				include_once 'templates/wps-pre-upsell-global-section.php';
			} elseif ( 'post-global-sect' === $global_setting_sub_tab ) {
				include_once 'templates/wps-post-upsell-global-section.php';
			}
		}


		// Order Bump list rendering.
		if ( 'order-bump-section' === $wps_ubo_lite_active_tab ) {
			$global_setting_sub_tab = isset( $_GET['sub_tab'] ) ? sanitize_text_field( wp_unslash( $_GET['sub_tab'] ) ) : 'pre-list-offer-section';

			if ( 'pre-list-offer-sections' === $global_setting_sub_tab ) {
				include_once 'templates/wps-pre-list-offer-section.php';
			} elseif ( 'pre-save-offer-section' === $global_setting_sub_tab ) {
				include_once 'templates/wps-pre-save-offer-section.php';
			}
}




		// One click upsell list rendering.
		if ( 'one-click-section' === $wps_ubo_lite_active_tab ) {
			$global_setting_sub_tab = isset( $_GET['sub_tab'] ) ? sanitize_text_field( wp_unslash( $_GET['sub_tab'] ) ) : 'post-list-offer-section';

			if ( 'post-list-offer-section' === $global_setting_sub_tab ) {
				include_once 'templates/wps-post-list-offer-section.php';
			} elseif ( 'post-save-offer-section' === $global_setting_sub_tab ) {
				include_once 'templates/wps-post-save-offer-section.php';
			}
		}



		// General setting rendering.
		if ( 'general-setting' === $wps_ubo_lite_active_tab ) {
				include_once 'templates/wps-general-setting-offer-section.php';
		}

		// Store Checkout setting rendering.
		if ( 'store-checkout-section' === $wps_ubo_lite_active_tab ) {
			include_once 'templates/wps-store-checkout-section.php';
		}

		// shortcode section setting rendering.
		if ( 'shortcode-section' === $wps_ubo_lite_active_tab ) {
			include_once 'templates/wps-shortcode-section.php';
		}

        if('creation-setting-post' === $wps_ubo_lite_active_tab){
			include_once 'templates/wps-post-save-offer-section.php';
		}




	}
	do_action( 'wps_ubo_lite_setting_tab_html' );

	?>
</div>

<!-- Connect us on skype. -->
<div id="wps_ubo_lite_skype_connect_with_us">

	<div class="wps_ubo_lite_skype_connect_title"><?php esc_html_e( 'Connect with Us in one click', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>

	<a class="button" target="_blank" href="https://wa.me/message/JSDF7KNKMUSKA1"><img src="<?php echo esc_url( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/whatsapp.gif' ); ?>"><?php esc_html_e( 'Connect', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

	<p><?php esc_html_e( 'Regarding any issue, query or feature request for Order Bump Offers.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
	<div class="wps_ubo_lite_skype_setting"><span class="dashicons dashicons-admin-generic"></span></div>
</div>


<!--Save Changes -->
<?php if ( 'creation-setting' === $wps_ubo_lite_active_tab ) { ?>
<div id="wps_ubo_lite_save_changes_bump">
<input type="submit" value="Save Changes" class="button-primary woocommerce-save-button wps-save-changes-ubo" name="wps_upsell_bump_creation_setting_save" id="wps_upsell_bump_creation_setting_save"><span class="dashicons dashicons-saved"></span>
</div>
<?php }





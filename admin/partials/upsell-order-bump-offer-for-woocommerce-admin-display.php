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

if (! defined('ABSPATH')) {
	exit;
}

define('ONBOARD_PLUGIN_NAME', 'Upsell Order Bump Offer for WooCommerce');

$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');

if (! $id_nonce_verified) {
	wp_die(esc_html__('Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce'));
}

$wps_ubo_lite_active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : '';
$wps_ubo_lite_active_page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
$sub_tab = isset($_GET['sub_tab']) ? sanitize_text_field(wp_unslash($_GET['sub_tab'])) : ''; // Unsplash before sanitizing.

if ('overview' === get_transient('wps_ubo_lite_default_settings_tab')) {

	$wps_ubo_lite_active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'overview';
}

do_action('wps_ubo_lite_tab_active');

?>
<div class="wrap woocommerce" id="wps_upsell_bump_setting_wrapper">
	<div class="wps_ubo_header_row">
		<div class="wps_upsell_bump_setting_title">
			<?php echo esc_html(apply_filters('wps_ubo_lite_heading', esc_html__('Upsell Funnel Builder for WooCommerce ', 'upsell-order-bump-offer-for-woocommerce'))); ?>
			<?php
			if (defined('UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_VERSION')) {
				$wps_ubo_display_version = UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_VERSION;
			} elseif (defined('UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_VERSION')) {
				$wps_ubo_display_version = UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_VERSION;
			} else {
				$wps_ubo_display_version = '';
			}
			?>
			<?php if (! empty($wps_ubo_display_version)) { ?>
				<span class="wps_ubo_version"><?php echo esc_html($wps_ubo_display_version); ?></span>
			<?php } ?>
		</div>
		<div class="wps_ubo_meta_links wps_ubo_meta_links--top">

			<a class="button" target="_blank" href="https://docs.wpswings.com/upsell-order-bump-offer-for-woocommerce/">
				<span class="dashicons dashicons-media-document"></span>
				<?php esc_html_e('Docs', 'upsell-order-bump-offer-for-woocommerce'); ?>
			</a>
			<a class="button" target="_blank" href="https://wpswings.com/submit-query/">
				<span class="dashicons dashicons-sos"></span>
				<?php esc_html_e('Support', 'upsell-order-bump-offer-for-woocommerce'); ?>
			</a>
			<a class="button" target="_blank" href="https://www.youtube.com/watch?v=SwIkS3EmwJQ">
				<span class="dashicons dashicons-video-alt3"></span>
				<?php esc_html_e('Video', 'upsell-order-bump-offer-for-woocommerce'); ?>
			</a>
			<?php if (! wps_ubo_lite_is_plugin_active('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php')) { ?>
				<a class="button button-primary active-pro-btn" target="_blank" href="https://wpswings.com/product/upsell-order-bump-offer-for-woocommerce-pro/?utm_source=upsell-backend&utm_medium=referral&utm_campaign=upsell-pro-page">
					<span class="dashicons dashicons-star-filled"></span>
					<?php esc_html_e('GO PRO NOW', 'upsell-order-bump-offer-for-woocommerce'); ?>
				</a>
			<?php } ?>
		</div>
	</div>

	<nav class="nav-tab-wrapper woo-nav-tab-wrapper">

		<div class="wps_main_global_wrapper">
			<a class="nav-tab <?php echo esc_html('general-setting' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=general-setting"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<circle cx="9" cy="6" r="2" stroke="#303030"/>
<path d="M4 6H7" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M11 6H20" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
<circle cx="9" cy="18" r="2" stroke="#303030"/>
<path d="M4 18H7" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M11 18H20" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
<circle cx="15" cy="12" r="2" stroke="#303030"/>
<path d="M4 12H13" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M17 12L20 12" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
<?php esc_html_e('General Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
		</div>


		<!--Global Setting Tab start here --->
		<div class="wps_main_global_wrapper">
			<a class="nav-tab <?php echo esc_html('global-setting' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=global-setting&tab=global-setting&sub_tab=pre-global-sect"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M3 12H21M3 12C3 16.9706 7.02944 21 12 21M3 12C3 7.02944 7.02944 3 12 3M21 12C21 16.9706 16.9706 21 12 21M21 12C21 7.02944 16.9706 3 12 3M12 21C4.75561 13.08 8.98151 5.7 12 3M12 21C19.2444 13.08 15.0185 5.7 12 3" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
<?php esc_html_e('Global Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>

			<!-- Sub-tabs under Global Setting -->
			<?php if ('global-setting' === $wps_ubo_lite_active_tab) : ?>

				<div class="nav-sub-tabs">
					<a class="sub-tab <?php echo esc_html('pre-global-sect' === $_GET['sub_tab'] ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=global-setting&sub_tab=pre-global-sect"><?php esc_html_e('Order Bump', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
					<?php if (wps_ubo_lite_is_plugin_active('woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php') && ! wps_is_plugin_active_with_version('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '3.0.0')) { ?>
						<a class="sub-tab <?php echo esc_html('post-global-sect' === $_GET['sub_tab'] ? 'nav-tab-active' : ''); ?>" href="?page=wps-wocuf-pro-setting"><?php esc_html_e('Upsell Funnel', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
					<?php } elseif (wps_ubo_lite_is_plugin_active('woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php') || wps_is_plugin_active_with_version('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '3.0.0')) { ?>
						<a class="sub-tab <?php echo esc_html('post-global-sect' === $_GET['sub_tab'] ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=global-setting&sub_tab=post-global-sect"><?php esc_html_e('Upsell Funnel', 'upsell-order-bump-offer-for-woocommerce'); ?></a>

					<?php } else { ?>
						<a class="sub-tab <?php echo esc_html('post-global-sect' === $_GET['sub_tab'] ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=global-setting&sub_tab=post-global-sect"><?php esc_html_e('Upsell Funnel', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
					<?php } ?>

				</div>
			<?php endif; ?>
		</div>
		<!--Global Setting Tab end here --->


		<!--Order Bump Section Tab start here --->
		<div class="wps_main_global_wrapper">
			<a class="nav-tab <?php echo esc_html('order-bump-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-list-offer-section"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M5 7H3M5 11H3M20 15H3M15 19H3M21 7H19M21 11H19M10.6 11H13.4C13.9601 11 14.2401 11 14.454 10.891C14.6422 10.7951 14.7951 10.6422 14.891 10.454C15 10.2401 15 9.96005 15 9.4V6.6C15 6.03995 15 5.75992 14.891 5.54601C14.7951 5.35785 14.6422 5.20487 14.454 5.10899C14.2401 5 13.9601 5 13.4 5H10.6C10.0399 5 9.75992 5 9.54601 5.10899C9.35785 5.20487 9.20487 5.35785 9.10899 5.54601C9 5.75992 9 6.03995 9 6.6V9.4C9 9.96005 9 10.2401 9.10899 10.454C9.20487 10.6422 9.35785 10.7951 9.54601 10.891C9.75992 11 10.0399 11 10.6 11Z" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
<?php esc_html_e('Order Bump Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>

			<!-- Sub-tabs under Order Bump Section -->
			<?php if ('order-bump-section' === $wps_ubo_lite_active_tab) : ?>

				<div class="nav-sub-tabs">
					<a class="sub-tab <?php echo esc_html('pre-list-offer-section' === $_GET['sub_tab'] ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-list-offer-section"><?php esc_html_e('Order Bump List', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
					<?php if (wps_ubo_lite_is_plugin_active('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php')) { ?>
						<a class="sub-tab wps-ubo-open-template-modal <?php echo esc_html('pre-save-offer-section' === $_GET['sub_tab'] ? 'nav-tab-active' : ''); ?>" href="#"><?php esc_html_e('Create', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
					<?php } else { ?>
						<a class="sub-tab  wps-ubo-open-template-modal<?php echo esc_html('pre-save-offer-section' === $_GET['sub_tab'] ? 'nav-tab-active' : ''); ?>" href="#"><?php esc_html_e('Create', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
					<?php } ?>
				</div>
			<?php endif; ?>
		</div>
		<!-- Order Bump Section Tab start here --->


		<!-- One Click Section Tab start here --->
		<div class="wps_main_global_wrapper">
			<?php if (wps_ubo_lite_is_plugin_active('woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php') && ! wps_is_plugin_active_with_version('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '3.0.0')) { ?>
				<a class="nav-tab <?php echo esc_html('one-click-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=wps-wocuf-pro-setting&tab=funnels-list"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M3 4.6C3 4.03995 3 3.75992 3.10899 3.54601C3.20487 3.35785 3.35785 3.20487 3.54601 3.10899C3.75992 3 4.03995 3 4.6 3H19.4C19.9601 3 20.2401 3 20.454 3.10899C20.6422 3.20487 20.7951 3.35785 20.891 3.54601C21 3.75992 21 4.03995 21 4.6V6.33726C21 6.58185 21 6.70414 20.9724 6.81923C20.9479 6.92127 20.9075 7.01881 20.8526 7.10828C20.7908 7.2092 20.7043 7.29568 20.5314 7.46863L14.4686 13.5314C14.2957 13.7043 14.2092 13.7908 14.1474 13.8917C14.0925 13.9812 14.0521 14.0787 14.0276 14.1808C14 14.2959 14 14.4182 14 14.6627V17L10 21V14.6627C10 14.4182 10 14.2959 9.97237 14.1808C9.94787 14.0787 9.90747 13.9812 9.85264 13.8917C9.7908 13.7908 9.70432 13.7043 9.53137 13.5314L3.46863 7.46863C3.29568 7.29568 3.2092 7.2092 3.14736 7.10828C3.09253 7.01881 3.05213 6.92127 3.02763 6.81923C3 6.70414 3 6.58185 3 6.33726V4.6Z" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
<?php esc_html_e('Upsell Funnel Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
			<?php } elseif (wps_ubo_lite_is_plugin_active('woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php') || wps_is_plugin_active_with_version('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '3.0.0')) { ?>
				<a class="nav-tab <?php echo esc_html('one-click-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=one-click-section&sub_tab=post-list-offer-section"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M3 4.6C3 4.03995 3 3.75992 3.10899 3.54601C3.20487 3.35785 3.35785 3.20487 3.54601 3.10899C3.75992 3 4.03995 3 4.6 3H19.4C19.9601 3 20.2401 3 20.454 3.10899C20.6422 3.20487 20.7951 3.35785 20.891 3.54601C21 3.75992 21 4.03995 21 4.6V6.33726C21 6.58185 21 6.70414 20.9724 6.81923C20.9479 6.92127 20.9075 7.01881 20.8526 7.10828C20.7908 7.2092 20.7043 7.29568 20.5314 7.46863L14.4686 13.5314C14.2957 13.7043 14.2092 13.7908 14.1474 13.8917C14.0925 13.9812 14.0521 14.0787 14.0276 14.1808C14 14.2959 14 14.4182 14 14.6627V17L10 21V14.6627C10 14.4182 10 14.2959 9.97237 14.1808C9.94787 14.0787 9.90747 13.9812 9.85264 13.8917C9.7908 13.7908 9.70432 13.7043 9.53137 13.5314L3.46863 7.46863C3.29568 7.29568 3.2092 7.2092 3.14736 7.10828C3.09253 7.01881 3.05213 6.92127 3.02763 6.81923C3 6.70414 3 6.58185 3 6.33726V4.6Z" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
</svg><?php esc_html_e('Upsell Funnel Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
			<?php } else { ?>
				<a class="nav-tab <?php echo esc_html('one-click-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=one-click-section&sub_tab=post-list-offer-section"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M3 4.6C3 4.03995 3 3.75992 3.10899 3.54601C3.20487 3.35785 3.35785 3.20487 3.54601 3.10899C3.75992 3 4.03995 3 4.6 3H19.4C19.9601 3 20.2401 3 20.454 3.10899C20.6422 3.20487 20.7951 3.35785 20.891 3.54601C21 3.75992 21 4.03995 21 4.6V6.33726C21 6.58185 21 6.70414 20.9724 6.81923C20.9479 6.92127 20.9075 7.01881 20.8526 7.10828C20.7908 7.2092 20.7043 7.29568 20.5314 7.46863L14.4686 13.5314C14.2957 13.7043 14.2092 13.7908 14.1474 13.8917C14.0925 13.9812 14.0521 14.0787 14.0276 14.1808C14 14.2959 14 14.4182 14 14.6627V17L10 21V14.6627C10 14.4182 10 14.2959 9.97237 14.1808C9.94787 14.0787 9.90747 13.9812 9.85264 13.8917C9.7908 13.7908 9.70432 13.7043 9.53137 13.5314L3.46863 7.46863C3.29568 7.29568 3.2092 7.2092 3.14736 7.10828C3.09253 7.01881 3.05213 6.92127 3.02763 6.81923C3 6.70414 3 6.58185 3 6.33726V4.6Z" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
</svg><?php esc_html_e('Upsell Funnel Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
			<?php } ?>

			<!-- Sub-tabs under One Click Section Tab -->
			<?php if ('one-click-section' === $wps_ubo_lite_active_tab) : ?>

				<div class="nav-sub-tabs">
					<a class="sub-tab <?php echo esc_html('post-list-offer-section' === $_GET['sub_tab'] ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=one-click-section&sub_tab=post-list-offer-section"><?php esc_html_e('Upsell Funnel List', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
					<?php if (wps_ubo_lite_is_plugin_active('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php')) { ?>
		<a class="sub-tab wps_ubo_lite_bump_create_button" id = "wps-wocuf-pro-open-funnel-template-modal" href="#"><?php esc_html_e('Create', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
					<?php } else { ?>
						<a class="sub-tab wps_ubo_lite_bump_create_button wps-wocuf-open-funnel-template-modal" href="#"><?php esc_html_e('Create', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
					<?php } ?>
				</div>
			<?php endif; ?>
		</div>
		<!-- One Click Section Tab end here --->


		<!-- Store Checkout Tab--->
		<?php if (wps_ubo_lite_is_plugin_active('woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php') && ! wps_is_plugin_active_with_version('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '3.0.0')) { ?>
			<a class="nav-tab <?php echo esc_html('store-checkout-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=wps-wocuf-pro-setting&tab=store_checkout"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M21 5L19 12H7.37671M20 16H8L6 3H3M13.5 3V9M13.5 3L11.5 5M13.5 3L15.5 5M9 20C9 20.5523 8.55228 21 8 21C7.44772 21 7 20.5523 7 20C7 19.4477 7.44772 19 8 19C8.55228 19 9 19.4477 9 20ZM20 20C20 20.5523 19.5523 21 19 21C18.4477 21 18 20.5523 18 20C18 19.4477 18.4477 19 19 19C19.5523 19 20 19.4477 20 20Z" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
<?php esc_html_e('Store Checkout Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
		<?php } elseif (wps_ubo_lite_is_plugin_active('woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php') || wps_is_plugin_active_with_version('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '3.0.0')) { ?>
			<a class="nav-tab <?php echo esc_html('store-checkout-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=store-checkout-section"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M21 5L19 12H7.37671M20 16H8L6 3H3M13.5 3V9M13.5 3L11.5 5M13.5 3L15.5 5M9 20C9 20.5523 8.55228 21 8 21C7.44772 21 7 20.5523 7 20C7 19.4477 7.44772 19 8 19C8.55228 19 9 19.4477 9 20ZM20 20C20 20.5523 19.5523 21 19 21C18.4477 21 18 20.5523 18 20C18 19.4477 18.4477 19 19 19C19.5523 19 20 19.4477 20 20Z" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
<?php esc_html_e('Store Checkout Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
		<?php } else { ?>
			<a class="nav-tab <?php echo esc_html('store-checkout-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=store-checkout-section"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M21 5L19 12H7.37671M20 16H8L6 3H3M13.5 3V9M13.5 3L11.5 5M13.5 3L15.5 5M9 20C9 20.5523 8.55228 21 8 21C7.44772 21 7 20.5523 7 20C7 19.4477 7.44772 19 8 19C8.55228 19 9 19.4477 9 20ZM20 20C20 20.5523 19.5523 21 19 21C18.4477 21 18 20.5523 18 20C18 19.4477 18.4477 19 19 19C19.5523 19 20 19.4477 20 20Z" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
<?php esc_html_e('Store Checkout Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
		<?php } ?>
		<!-- Shortcode Tab--->
		<a class="nav-tab <?php echo esc_html('shortcode-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=shortcode-section"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M7 8L3 11.6923L7 16M17 8L21 11.6923L17 16M14 4L10 20" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
<?php esc_html_e('Shortcodes', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
		<?php
		if (class_exists('Upsell_Order_Bump_Offer_For_Woocommerce_Pro')) {

			$wps_upsell_bump_callname_lic = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_lic_callback_function;

			$wps_upsell_bump_callname_lic_initial = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_lic_ini_callback_function;

			$day_count = Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_callname_lic_initial();
		}

		$plugin_version = wps_ubo_lite_if_pro_exists();
		?>

		<!-- If premium version is available, set license tab. -->
		<?php if ($plugin_version && ! Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_callname_lic()) : ?>

			<a class="nav-tab <?php echo esc_html('license' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=license"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M9 12L11 14L15 10M12 3L13.9101 4.87147L16.5 4.20577L17.2184 6.78155L19.7942 7.5L19.1285 10.0899L21 12L19.1285 13.9101L19.7942 16.5L17.2184 17.2184L16.5 19.7942L13.9101 19.1285L12 21L10.0899 19.1285L7.5 19.7942L6.78155 17.2184L4.20577 16.5L4.87147 13.9101L3 12L4.87147 10.0899L4.20577 7.5L6.78155 6.78155L7.5 4.20577L10.0899 4.87147L12 3Z" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
<?php esc_html_e('License', 'upsell-order-bump-offer-for-woocommerce'); ?></a>

		<?php endif; ?>


		<!-- If Org version set overview tab. -->
		<a class="nav-tab <?php echo esc_html('overview' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=overview"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M20 20L18.2678 18.2678M18.2678 18.2678C18.7202 17.8154 19 17.1904 19 16.5C19 15.1193 17.8807 14 16.5 14C15.1193 14 14 15.1193 14 16.5C14 17.8807 15.1193 19 16.5 19C17.1904 19 17.8154 18.7202 18.2678 18.2678ZM15.6 10H18.4C18.9601 10 19.2401 10 19.454 9.89101C19.6422 9.79513 19.7951 9.64215 19.891 9.45399C20 9.24008 20 8.96005 20 8.4V5.6C20 5.03995 20 4.75992 19.891 4.54601C19.7951 4.35785 19.6422 4.20487 19.454 4.10899C19.2401 4 18.9601 4 18.4 4H15.6C15.0399 4 14.7599 4 14.546 4.10899C14.3578 4.20487 14.2049 4.35785 14.109 4.54601C14 4.75992 14 5.03995 14 5.6V8.4C14 8.96005 14 9.24008 14.109 9.45399C14.2049 9.64215 14.3578 9.79513 14.546 9.89101C14.7599 10 15.0399 10 15.6 10ZM5.6 10H8.4C8.96005 10 9.24008 10 9.45399 9.89101C9.64215 9.79513 9.79513 9.64215 9.89101 9.45399C10 9.24008 10 8.96005 10 8.4V5.6C10 5.03995 10 4.75992 9.89101 4.54601C9.79513 4.35785 9.64215 4.20487 9.45399 4.10899C9.24008 4 8.96005 4 8.4 4H5.6C5.03995 4 4.75992 4 4.54601 4.10899C4.35785 4.20487 4.20487 4.35785 4.10899 4.54601C4 4.75992 4 5.03995 4 5.6V8.4C4 8.96005 4 9.24008 4.10899 9.45399C4.20487 9.64215 4.35785 9.79513 4.54601 9.89101C4.75992 10 5.03995 10 5.6 10ZM5.6 20H8.4C8.96005 20 9.24008 20 9.45399 19.891C9.64215 19.7951 9.79513 19.6422 9.89101 19.454C10 19.2401 10 18.9601 10 18.4V15.6C10 15.0399 10 14.7599 9.89101 14.546C9.79513 14.3578 9.64215 14.2049 9.45399 14.109C9.24008 14 8.96005 14 8.4 14H5.6C5.03995 14 4.75992 14 4.54601 14.109C4.35785 14.2049 4.20487 14.3578 4.10899 14.546C4 14.7599 4 15.0399 4 15.6V18.4C4 18.9601 4 19.2401 4.10899 19.454C4.20487 19.6422 4.35785 19.7951 4.54601 19.891C4.75992 20 5.03995 20 5.6 20Z" stroke="#303030" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
<?php esc_html_e('Overview', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
		<?php do_action('wps_ubo_setting_tab'); ?>

	</nav>

	<!-- For notification control. -->
	<?php do_action('wps_ubo_migration_notice', '', '', ''); ?>
	<?php

	if ($plugin_version) {
		$global_setting_sub_tab = isset($_GET['sub_tab']) ? sanitize_text_field(wp_unslash($_GET['sub_tab'])) : '';

		// If license is activated or trial period is remaining.
		if ('creation-setting' === $wps_ubo_lite_active_tab) {
			// Include creation file from pro version.
			include_once UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_DIRPATH . '/admin/partials/templates/wps-upsell-bump-creation.php';
		} elseif ('pre-list-offer-section' === $global_setting_sub_tab) {
			// Include listing file from pro version.
			include_once 'templates/wps-ubo-lite-list.php';
		} elseif ('settings' === $wps_ubo_lite_active_tab) {
			// Include setting file from org version.
			include_once 'templates/wps-ubo-lite-settings.php';
		} elseif ('overview' === $wps_ubo_lite_active_tab) {
			// Include setting file from org version.
			include_once 'templates/wps-ubo-lite-overview.php';
		}

		if (! Upsell_Order_Bump_Offer_For_Woocommerce_Pro::$wps_upsell_bump_callname_lic()) {

			if ('license' === $wps_ubo_lite_active_tab) {
				// Include license file from pro version.
				include_once UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_DIRPATH . '/admin/partials/templates/wps-upsell-bump-license.php';
			}
		}


		// Order Bump list rendering.
		if ('order-bump-section' === $wps_ubo_lite_active_tab) {
			$global_setting_sub_tab = isset($_GET['sub_tab']) ? sanitize_text_field(wp_unslash($_GET['sub_tab'])) : 'pre-list-offer-section';

			if ('pre-list-offer-sections' === $global_setting_sub_tab) {
				include_once 'templates/wps-pre-list-offer-section.php';
			} elseif ('pre-save-offer-section' === $global_setting_sub_tab) {
				include_once UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_DIRPATH . '/admin/partials/templates/wps-upsell-bump-creation.php';
			}
		}

		// Order Bump global setting rendering.
		if ('global-setting' === $wps_ubo_lite_active_tab) {
			$global_setting_sub_tab = isset($_GET['sub_tab']) ? sanitize_text_field(wp_unslash($_GET['sub_tab'])) : 'pre-global-sect';

			if ('pre-global-sect' === $global_setting_sub_tab) {
				include_once 'templates/wps-pre-upsell-global-section.php';
			} elseif ('post-global-sect' === $global_setting_sub_tab) {

				if (wps_ubo_lite_is_plugin_active('woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php')) {
					include_once WPS_WOCUF_PRO_DIRPATH . 'admin/partials/templates/wps-wocuf-pro-settings.php';
				} else {
					if (wps_is_plugin_active_with_version('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '3.0.0')) {
						include_once UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_DIRPATH . 'admin/partials/templates/wps-wocuf-pro-settings.php';
					} else {

						include_once 'templates/wps-post-upsell-global-section.php';
					}
				}
			}
		}



		// General setting rendering.
		if ('general-setting' === $wps_ubo_lite_active_tab || (isset($_GET['page']) && 'upsell-order-bump-offer-for-woocommerce-setting' === $_GET['page'] && count($_GET) === 1)) {
			include_once 'templates/wps-general-setting-offer-section.php';
		}


		// One click upsell list rendering.
		// Changes For the one click funnel builder pro.
		if ('one-click-section' === $wps_ubo_lite_active_tab) {
			$global_setting_sub_tab = isset($_GET['sub_tab']) ? sanitize_text_field(wp_unslash($_GET['sub_tab'])) : 'post-list-offer-section';

			if ('post-list-offer-section' === $global_setting_sub_tab) {

				if (wps_ubo_lite_is_plugin_active('woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php')) {
					include_once WPS_WOCUF_PRO_DIRPATH . 'admin/partials/templates/wps-wocuf-pro-funnels-list.php';
				} else {

					if (wps_is_plugin_active_with_version('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '3.0.0')) {

						include_once UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_DIRPATH . 'admin/partials/templates/wps-wocuf-pro-funnels-list.php';
					} else {

						include_once 'templates/wps-post-list-offer-section.php';
					}
				}
			} elseif ('post-save-offer-section' === $global_setting_sub_tab) {

				if (wps_ubo_lite_is_plugin_active('woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php')) {
					include_once WPS_WOCUF_PRO_DIRPATH . 'admin/partials/templates/wps-wocuf-pro-creation.php';
				} else {

					if (wps_is_plugin_active_with_version('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '3.0.0')) {
						include_once UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_DIRPATH . 'admin/partials/templates/wps-wocuf-pro-creation.php';
					} else {
						include_once 'templates/wps-post-save-offer-section.php';
					}
				}
			}
		}


		// General setting rendering.
		if ('general-setting' === $wps_ubo_lite_active_tab || (isset($_GET['page']) && 'upsell-order-bump-offer-for-woocommerce-setting' === $_GET['page'] && count($_GET) === 1)) {
			include_once 'templates/wps-general-setting-offer-section.php';
		}

		// Store Checkout setting rendering.
		if ('store-checkout-section' === $wps_ubo_lite_active_tab) {

			if (wps_ubo_lite_is_plugin_active('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php')) {
				include_once UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_DIRPATH . 'admin/partials/templates/wps-wocuf-pro-store-checkout.php';
			} else {
				include_once 'templates/wps-store-checkout-section.php';
			}
		}

		// shortcode section setting rendering.
		if ('shortcode-section' === $wps_ubo_lite_active_tab) {
			include_once 'templates/wps-shortcode-section.php';
		}

		if ('creation-setting-post' === $wps_ubo_lite_active_tab) {

			if (wps_is_plugin_active_with_version('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '3.0.0')) {
				include_once UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_PRO_DIRPATH . 'admin/partials/templates/wps-wocuf-pro-creation.php';
			} else {
				include_once 'templates/wps-post-save-offer-section.php';
			}
		}
	} else if (! $plugin_version) {

		$global_setting_sub_tab = isset($_GET['sub_tab']) ? sanitize_text_field(wp_unslash($_GET['sub_tab'])) : '';
		// Org files.
		if ('creation-setting' === $wps_ubo_lite_active_tab) {
			include_once 'templates/wps-ubo-lite-creation.php';
		} elseif ('pre-list-offer-section' === $global_setting_sub_tab) {
			include_once 'templates/wps-ubo-lite-list.php';
		} elseif ('settings' === $wps_ubo_lite_active_tab) {
			include_once 'templates/wps-ubo-lite-settings.php';
		} elseif ('overview' === $wps_ubo_lite_active_tab) {
			include_once 'templates/wps-ubo-lite-overview.php';
		}

		// Order Bump global setting rendering.
		if ('global-setting' === $wps_ubo_lite_active_tab) {
			$global_setting_sub_tab = isset($_GET['sub_tab']) ? sanitize_text_field(wp_unslash($_GET['sub_tab'])) : 'pre-global-sect';

			if ('pre-global-sect' === $global_setting_sub_tab) {
				include_once 'templates/wps-pre-upsell-global-section.php';
			} elseif ('post-global-sect' === $global_setting_sub_tab) {

				if (wps_ubo_lite_is_plugin_active('woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php')) {
					include_once WPS_WOCUF_PRO_DIRPATH . 'admin/partials/templates/wps-wocuf-pro-settings.php';
				} else {

					include_once 'templates/wps-post-upsell-global-section.php';
				}
			}
		}


		// Order Bump list rendering.
		if ('order-bump-section' === $wps_ubo_lite_active_tab) {
			$global_setting_sub_tab = isset($_GET['sub_tab']) ? sanitize_text_field(wp_unslash($_GET['sub_tab'])) : 'pre-list-offer-section';

			if ('pre-list-offer-sections' === $global_setting_sub_tab) {
				include_once 'templates/wps-pre-list-offer-section.php';
			} elseif ('pre-save-offer-section' === $global_setting_sub_tab) {
				include_once 'templates/wps-pre-save-offer-section.php';
			}
		}


		// One click upsell list rendering.
		if ('one-click-section' === $wps_ubo_lite_active_tab) {
			$global_setting_sub_tab = isset($_GET['sub_tab']) ? sanitize_text_field(wp_unslash($_GET['sub_tab'])) : 'post-list-offer-section';

			if ('post-list-offer-section' === $global_setting_sub_tab) {

				if (wps_ubo_lite_is_plugin_active('woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php')) {
					include_once WPS_WOCUF_PRO_DIRPATH . 'admin/partials/templates/wps-wocuf-pro-funnels-list.php';
				} else {
					include_once 'templates/wps-post-list-offer-section.php';
				}
			} elseif ('post-save-offer-section' === $global_setting_sub_tab) {

				if (wps_ubo_lite_is_plugin_active('woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php')) {
					include_once WPS_WOCUF_PRO_DIRPATH . 'admin/partials/templates/wps-wocuf-pro-creation.php';
				} else {
					include_once 'templates/wps-post-save-offer-section.php';
				}
			}
		}



		// General setting rendering.
		if ('general-setting' === $wps_ubo_lite_active_tab || (isset($_GET['page']) && ('upsell-order-bump-offer-for-woocommerce-setting' === $_GET['page']) && count($_GET) === 1)) {
			include_once 'templates/wps-general-setting-offer-section.php';
		}

		// Store Checkout setting rendering.
		if ('store-checkout-section' === $wps_ubo_lite_active_tab) {

			if (wps_ubo_lite_is_plugin_active('woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php')) {
				include_once WPS_WOCUF_PRO_DIRPATH . 'admin/partials/templates/wps-wocuf-pro-store-checkout.php';
			} else {
				include_once 'templates/wps-store-checkout-section.php';
			}
		}

		// shortcode section setting rendering.
		if ('shortcode-section' === $wps_ubo_lite_active_tab) {
			include_once 'templates/wps-shortcode-section.php';
		}

		if ('creation-setting-post' === $wps_ubo_lite_active_tab) {
			include_once 'templates/wps-post-save-offer-section.php';
		}
	}
	do_action('wps_ubo_lite_setting_tab_html');

	?>
</div>

<!-- Connect us on skype. -->
<div id="wps_ubo_lite_skype_connect_with_us">

	<div class="wps_ubo_lite_skype_connect_title"><?php esc_html_e('Connect with Us in one click', 'upsell-order-bump-offer-for-woocommerce'); ?></div>

	<a class="button" target="_blank" href="https://api.whatsapp.com/send/?phone=917376793083&text=Hello%20WP%20Swings!"><img src="<?php echo esc_url(UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/whatsapp.gif'); ?>"><?php esc_html_e('Connect', 'upsell-order-bump-offer-for-woocommerce'); ?></a>

	<p><?php esc_html_e('Regarding any issue, query or feature request for Order Bump Offers.', 'upsell-order-bump-offer-for-woocommerce'); ?></p>
	<div class="wps_ubo_lite_skype_setting"><span class="dashicons dashicons-admin-generic"></span></div>
	<?php if (wps_ubo_lite_is_plugin_active('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php')) { ?>
		<hr>
		<div class="wps_ubo_lite_skype_connect_title">
			<?php esc_html_e('Need Expert Help with WooCommerce?', 'upsell-order-bump-offer-for-woocommerce'); ?>
		</div>

		<a class="button" target="_blank" href="https://wpswings.com/woocommerce-services/">
			<img src="<?php echo esc_url(UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . 'admin/resources/icons/Services.svg'); ?>" alt="WooCommerce Services">
			<?php esc_html_e('Explore Services', 'upsell-order-bump-offer-for-woocommerce'); ?>
		</a>

		<p>
			<?php esc_html_e('Looking for customization, support, or new features for Order Bump Offers? Our WooCommerce experts are here to help you achieve more.', 'upsell-order-bump-offer-for-woocommerce'); ?>
		</p>
		<div class="wps_ubo_lite_skype_setting">
			<span class="dashicons dashicons-admin-generic"></span>
		</div>
	<?php } ?>
</div>


<!--Save Changes -->
<?php if ('creation-setting' === $wps_ubo_lite_active_tab) { ?>
	<!-- <div id="wps_ubo_lite_save_changes_bump">
		<input type="submit" value="Save Changes" class="button-primary woocommerce-save-button wps-save-changes-ubo" name="wps_upsell_bump_creation_setting_save" id="wps_upsell_bump_creation_setting_save"><span class="dashicons dashicons-saved"></span>
	</div> -->
<?php
}

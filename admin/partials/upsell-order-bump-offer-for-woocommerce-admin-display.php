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
				<a class="button button-primary" target="_blank" href="https://wpswings.com/product/upsell-order-bump-offer-for-woocommerce-pro/?utm_source=upsell-backend&utm_medium=referral&utm_campaign=upsell-pro-page">
					<span class="dashicons dashicons-star-filled"></span>
					<?php esc_html_e('GO PRO NOW', 'upsell-order-bump-offer-for-woocommerce'); ?>
				</a>
			<?php } ?>
		</div>
	</div>

	<nav class="nav-tab-wrapper woo-nav-tab-wrapper">

		<div class="wps_main_global_wrapper">
			<a class="nav-tab <?php echo esc_html('general-setting' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=general-setting"><?php esc_html_e('General Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
		</div>


		<!--Global Setting Tab start here --->
		<div class="wps_main_global_wrapper">
			<a class="nav-tab <?php echo esc_html('global-setting' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=global-setting&tab=global-setting&sub_tab=pre-global-sect"><?php esc_html_e('Global Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>

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
			<a class="nav-tab <?php echo esc_html('order-bump-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-list-offer-section"><?php esc_html_e('Order Bump Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>

			<!-- Sub-tabs under Order Bump Section -->
			<?php if ('order-bump-section' === $wps_ubo_lite_active_tab) : ?>

				<div class="nav-sub-tabs">
					<a class="sub-tab <?php echo esc_html('pre-list-offer-section' === $_GET['sub_tab'] ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-list-offer-section"><?php esc_html_e('Order Bump List', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
					<?php if (wps_ubo_lite_is_plugin_active('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php')) { ?>
						<a class="sub-tab <?php echo esc_html('pre-save-offer-section' === $_GET['sub_tab'] ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-save-offer-section"><?php esc_html_e('Create', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
					<?php } else { ?>
						<a class="sub-tab wps_ubo_lite_bump_create_button <?php echo esc_html('pre-save-offer-section' === $_GET['sub_tab'] ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&bump_id=1"><?php esc_html_e('Create', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
					<?php } ?>
				</div>
			<?php endif; ?>
		</div>
		<!-- Order Bump Section Tab start here --->


		<!-- One Click Section Tab start here --->
		<div class="wps_main_global_wrapper">
			<?php if (wps_ubo_lite_is_plugin_active('woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php') && ! wps_is_plugin_active_with_version('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '3.0.0')) { ?>
				<a class="nav-tab <?php echo esc_html('one-click-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=wps-wocuf-pro-setting&tab=funnels-list"><?php esc_html_e('Upsell Funnel Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
			<?php } elseif (wps_ubo_lite_is_plugin_active('woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php') || wps_is_plugin_active_with_version('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '3.0.0')) { ?>
				<a class="nav-tab <?php echo esc_html('one-click-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=one-click-section&sub_tab=post-list-offer-section"><?php esc_html_e('Upsell Funnel Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
			<?php } else { ?>
				<a class="nav-tab <?php echo esc_html('one-click-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=one-click-section&sub_tab=post-list-offer-section"><?php esc_html_e('Upsell Funnel Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
			<?php } ?>

			<!-- Sub-tabs under One Click Section Tab -->
			<?php if ('one-click-section' === $wps_ubo_lite_active_tab) : ?>

				<div class="nav-sub-tabs">
					<a class="sub-tab <?php echo esc_html('post-list-offer-section' === $_GET['sub_tab'] ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=one-click-section&sub_tab=post-list-offer-section"><?php esc_html_e('Upsell Funnel List', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
					<?php if (wps_ubo_lite_is_plugin_active('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php')) { ?>
						<a class="sub-tab <?php echo esc_html('post-save-offer-section' === $_GET['sub_tab'] ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=one-click-section&sub_tab=post-save-offer-section"><?php esc_html_e('Create', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
					<?php } else { ?>
						<a class="sub-tab wps_ubo_lite_bump_create_button" href="?page=upsell-order-bump-offer-for-woocommerce-setting&manage_nonce=be792f618d&tab=creation-setting-post&sub_tab=post-list-offer-section&funnel_id=1"><?php esc_html_e('Create', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
					<?php } ?>
				</div>
			<?php endif; ?>
		</div>
		<!-- One Click Section Tab end here --->


		<!-- Store Checkout Tab--->
		<?php if (wps_ubo_lite_is_plugin_active('woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php') && ! wps_is_plugin_active_with_version('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '3.0.0')) { ?>
			<a class="nav-tab <?php echo esc_html('store-checkout-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=wps-wocuf-pro-setting&tab=store_checkout"><?php esc_html_e('Store Checkout Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
		<?php } elseif (wps_ubo_lite_is_plugin_active('woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php') || wps_is_plugin_active_with_version('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php', '3.0.0')) { ?>
			<a class="nav-tab <?php echo esc_html('store-checkout-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=store-checkout-section"><?php esc_html_e('Store Checkout Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
		<?php } else { ?>
			<a class="nav-tab <?php echo esc_html('store-checkout-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=store-checkout-section"><?php esc_html_e('Store Checkout Setting', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
		<?php } ?>
		<!-- Shortcode Tab--->
		<a class="nav-tab <?php echo esc_html('shortcode-section' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=shortcode-section"><?php esc_html_e('Shortcodes', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
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

			<a class="nav-tab <?php echo esc_html('license' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=license"><?php esc_html_e('License', 'upsell-order-bump-offer-for-woocommerce'); ?></a>

		<?php endif; ?>


		<!-- If Org version set overview tab. -->
		<a class="nav-tab <?php echo esc_html('overview' === $wps_ubo_lite_active_tab ? 'nav-tab-active' : ''); ?>" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=overview"><?php esc_html_e('Overview', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
		<?php do_action('wps_ubo_setting_tab'); ?>

	</nav>

	<!-- For notification control. -->
	<h1></h1>
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
	<div id="wps_ubo_lite_save_changes_bump">
		<input type="submit" value="Save Changes" class="button-primary woocommerce-save-button wps-save-changes-ubo" name="wps_upsell_bump_creation_setting_save" id="wps_upsell_bump_creation_setting_save"><span class="dashicons dashicons-saved"></span>
	</div>
<?php
}

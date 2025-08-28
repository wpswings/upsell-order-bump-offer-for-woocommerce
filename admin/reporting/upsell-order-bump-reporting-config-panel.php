<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      1.4.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin/reporting
 */

if (! defined('ABSPATH')) {
	exit;
}

$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');

if (! $id_nonce_verified) {
	wp_die(esc_html__('Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce'));
}

$active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'reporting';

?>

<div class="wrap woocommerce" id="wps_upsell_bump_setting_wrapper">
	<div class="wps_upsell_bump_setting_title pre-bump-report_analytics"><?php echo esc_html__('Pre Sales Report & Analytics', 'upsell-order-bump-offer-for-woocommerce'); ?>

		<a target="_blank" href="<?php echo esc_url(admin_url('admin.php?page=wc-reports&tab=wps_order_bump')); ?>"><?php esc_html_e('View Sale Report', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
	</div>
	<?php
	if ('reporting' === $active_tab) {
		include_once 'templates/reporting.php';
	}

	?>
</div>
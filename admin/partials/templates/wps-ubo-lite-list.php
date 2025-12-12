<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to list all bump offers.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      1.0.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin/partials/templates
 */

/**
 * Exit if accessed directly.
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Bumps Listing Template.
 *
 * This template is used for listing all existing bumps with
 * view/edit and delete option.
 */

$secure_nonce      = wp_create_nonce('wps-upsell-auth-nonce');
$id_nonce_verified = wp_verify_nonce($secure_nonce, 'wps-upsell-auth-nonce');

if (! $id_nonce_verified) {
	wp_die(esc_html__('Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce'));
}

// Delete bumps.
if (isset($_GET['del_bump_id'])) {

	$bump_id = sanitize_text_field(wp_unslash($_GET['del_bump_id']));

	// Get all bumps.
	$wps_upsell_bumps = get_option('wps_ubo_bump_list');

	foreach ($wps_upsell_bumps as $single_bump => $data) {

		if ((string) $bump_id === (string) $single_bump) {

			unset($wps_upsell_bumps[$single_bump]);
			break;
		}
	}

	update_option('wps_ubo_bump_list', $wps_upsell_bumps);

	// Delete associated discount rules.
	$wc_dynamic_discount_rules = get_option('wc_dynamic_discount_rules', array());

	$funnel_type = 'wps_bump_one';
	if (! empty($funnel_type)) {

		if (isset($wc_dynamic_discount_rules[$funnel_type][$bump_id])) {
			unset($wc_dynamic_discount_rules[$funnel_type][$bump_id]);
		}

		if (empty($wc_dynamic_discount_rules[$funnel_type])) {
			unset($wc_dynamic_discount_rules[$funnel_type]);
		}
	} else {
		foreach ($wc_dynamic_discount_rules as $funnel_key => $bumps) {

			if (isset($wc_dynamic_discount_rules[$funnel_key][$bump_id])) {
				unset($wc_dynamic_discount_rules[$funnel_key][$bump_id]);
			}

			if (empty($wc_dynamic_discount_rules[$funnel_key])) {
				unset($wc_dynamic_discount_rules[$funnel_key]);
			}
		}
	}

	update_option('wc_dynamic_discount_rules', $wc_dynamic_discount_rules);

wp_safe_redirect( esc_url_raw( admin_url( 'admin.php?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-list-offer-section' ) ) );

	exit();
}


// Clone bumps.
if (isset($_GET['clone_bump_id'])) {

	$bump_id = sanitize_text_field(wp_unslash($_GET['clone_bump_id']));

	// Get all bumps.
	$wps_upsell_bumps = get_option('wps_ubo_bump_list');

	$wps_clone_bump_data = $wps_upsell_bumps[$bump_id];
	$wps_clone_bump_data['wps_upsell_bump_name'] = 'Clone ' . $wps_clone_bump_data['wps_upsell_bump_name'];
	array_push($wps_upsell_bumps, $wps_clone_bump_data);
	update_option('wps_ubo_bump_list', $wps_upsell_bumps);

	wp_safe_redirect(admin_url('admin.php') . '?page=upsell-order-bump-offer-for-woocommerce-setting&tab=bump-list');

	exit();
}




// Get all bumps.
$wps_upsell_bumps_list = get_option('wps_ubo_bump_list');


$wps_count_for_ab = 0;
if (is_array($wps_upsell_bumps_list) && ! empty($wps_upsell_bumps_list)) {
	foreach ($wps_upsell_bumps_list as $key => $value) {
		if (is_array($value) && ! empty($value) && isset($value['wps_display_method']) && ! empty($value['wps_display_method'])) {
			if (isset($value['wps_display_method']) && ! empty($value['wps_display_method']) && 'ab_method' == $value['wps_display_method']) {
				$wps_count_for_ab++;
			}
		}
	}
}


if (! empty($wps_upsell_bumps_list)) {

	// Temp bump variable.
	$wps_upsell_bumps_list_duplicate = $wps_upsell_bumps_list;

	// Make key pointer point to the end bump.
	end($wps_upsell_bumps_list_duplicate);

	// Now key function will return last bump key.
	$wps_upsell_bumps_last_index = key($wps_upsell_bumps_list_duplicate);
} else {

	// When no bump is there then new bump id will be 1 (0+1).
	$wps_upsell_bumps_last_index = 0;
}


$count_abandoned_bumps = 0;
if (is_array($wps_upsell_bumps_list) && ! empty($wps_upsell_bumps_list)) {
	foreach ($wps_upsell_bumps_list as $key => $value) {
		if ((isset($value['wps_is_abandoned_bump']) && 'yes' !== $value['wps_is_abandoned_bump']) || ! isset($value['wps_is_abandoned_bump'])) {
			$count_abandoned_bumps++; // Increment count for each abandoned bump.
		}
	}
}
?>

<div class="wps_upsell_bumps_list">

	<?php if ($count_abandoned_bumps < 1) : ?>

		<p class="wps_upsell_bump_no_bump"><?php esc_html_e('No Order Bumps added', 'upsell-order-bump-offer-for-woocommerce'); ?></p>

	<?php endif; ?>

	<?php if ($count_abandoned_bumps >= 1) : ?>
		<?php if (! wps_ubo_lite_if_pro_exists() && $count_abandoned_bumps >= 1) : ?>

			<div class="notice notice-warning wps-notice">
				<p>
					<strong><?php esc_html_e('Only first Order Bump will work. Please activate pro version to make all working.', 'upsell-order-bump-offer-for-woocommerce'); ?></strong>
				</p>
			</div>

		<?php endif; ?>
		<?php
		$pro_slugs     = array(
			'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php',
			'upsell-order-bump-offer-for-woocommerce-pro.php',
			'woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php',
		);
		$is_pro_active = false;
		foreach ( $pro_slugs as $pro_slug ) {
			if ( wps_ubo_lite_is_plugin_active( $pro_slug ) || wps_upsell_lite_is_plugin_active_funnel_builder( $pro_slug ) ) {
				$is_pro_active = true;
				break;
			}
		}
		if ( $is_pro_active ) :
			?>
			<div class="wps_ubo_export_import wps_ubo_export_import--top">
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( 'wps_ubo_export_bumps' ); ?>
					<input type="hidden" name="action" value="wps_ubo_export_bumps">
					<button class="button button-primary"><?php esc_html_e( 'Export Bumps (CSV)', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
				</form>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
					<?php wp_nonce_field( 'wps_ubo_import_bumps' ); ?>
					<input type="hidden" name="action" value="wps_ubo_import_bumps">
					<input type="file" name="wps_ubo_bumps_file" accept=".csv,text/csv" required>
					<button class="button"><?php esc_html_e( 'Import Bumps (CSV)', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
				</form>
			</div>
		<?php endif; ?>
		<?php $wps_ubo_global_options = get_option('wps_ubo_global_options', wps_ubo_lite_default_global_options()); ?>
		<?php $bump_offer_ab_method  = ! empty($wps_ubo_global_options['wps_ubo_offer_ab_method']) ? $wps_ubo_global_options['wps_ubo_offer_ab_method'] : 'no'; ?>
		<table>
			<tr>
				<th><?php esc_html_e('Name', 'upsell-order-bump-offer-for-woocommerce'); ?></th>
				<th><?php esc_html_e('Status', 'upsell-order-bump-offer-for-woocommerce'); ?></th>
				<th id="wps_upsell_bump_list_target_th"><?php esc_html_e('Target Product(s) and Categories', 'upsell-order-bump-offer-for-woocommerce'); ?></th>
				<th><?php esc_html_e('Offers', 'upsell-order-bump-offer-for-woocommerce'); ?></th>
				<?php if ('on' == $bump_offer_ab_method && wps_ubo_lite_is_plugin_active('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php')) { ?>
					<th><?php esc_html_e('AB Status', 'upsell-order-bump-offer-for-woocommerce'); ?></th>
				<?php } ?>
				<th><?php esc_html_e('Action', 'upsell-order-bump-offer-for-woocommerce'); ?></th>
			</tr>

			<!-- Foreach Bump start. -->
			<?php foreach ($wps_upsell_bumps_list as $key => $value) : ?>
				<?php
				// Skip if key 'wps_is_abandoned_bump' exists in serialized cart data.
				if (isset($value['wps_is_abandoned_bump']) && ! empty($value['wps_is_abandoned_bump']) && ('yes' === $value['wps_is_abandoned_bump'])) {
					continue; // skip this row.
				}

				$label_campaign = isset($value['wps_bump_label_campaign']) ? $value['wps_bump_label_campaign'] : '';

				list($color_hex, $label_name) = array_pad(explode('/',  $label_campaign, 2), 2, '');
				?>

				<tr>
					<!-- Bump Name. -->
					<td>

						<?php $wps_ubo_global_options = get_option('wps_ubo_global_options', wps_ubo_lite_default_global_options()); ?>
						<?php $wps_bump_enable_campaign_labels = ! empty($wps_ubo_global_options['wps_bump_enable_campaign_labels']) ? $wps_ubo_global_options['wps_bump_enable_campaign_labels'] : ''; ?>
						<?php if ('on' === $wps_bump_enable_campaign_labels) { ?>
							<span class="wps_label_color" style="background-color: <?php echo esc_attr($color_hex); ?>;"><?php echo esc_html($label_name); ?></span>
						<?php } ?>

						<a class="wps_upsell_bump_list_name" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&bump_id=<?php echo esc_html($key); ?>"><?php echo esc_html($value['wps_upsell_bump_name']); ?></a>
						<p><i><?php esc_html_e('Priority : ', 'upsell-order-bump-offer-for-woocommerce'); ?><span class="wps-bump-priority"><?php echo esc_html(! empty($value['wps_upsell_bump_priority']) ? $value['wps_upsell_bump_priority'] : 'No Priority'); ?></span></i></p>
					</td>
					</td>

					<!-- Bump Status. -->
					<td>
						<?php

						$bump_status = ! empty($value['wps_upsell_bump_status']) ? $value['wps_upsell_bump_status'] : 'no';

						$checked = 'yes' === $bump_status ? 'checked' : '';
						?>
						<label class="wps_ubo_bump_toggle_wrap">
							<input type="checkbox" class="wps_ubo_bump_status_toggle" data-bump-id="<?php echo esc_attr($key); ?>" <?php echo esc_html($checked); ?> />
							<span class="wps_ubo_bump_toggle_slider" aria-hidden="true"></span>
						</label>
						<span class="wps_ubo_bump_status_text" data-bump-id="<?php echo esc_attr($key); ?>">
							<?php echo 'yes' === $bump_status ? esc_html__('Live', 'upsell-order-bump-offer-for-woocommerce') : esc_html__('Sandbox', 'upsell-order-bump-offer-for-woocommerce'); ?>
						</span>

					</td>

					<!-- Bump Target products. -->
					<td>
						<?php

						// Target Product(s).
						if (! empty($value['wps_upsell_bump_target_ids'])) {

							echo '<div class="wps_upsell_bump_list_targets">';

							foreach ($value['wps_upsell_bump_target_ids'] as $single_target_product) :

						?>
								<p><?php echo esc_html(wps_ubo_lite_get_title($single_target_product) . "( #$single_target_product )"); ?></p>
							<?php

							endforeach;

							echo '</div>';
						} else {

							?>
							<p><i><?php esc_html_e('No Product(s) added', 'upsell-order-bump-offer-for-woocommerce'); ?></i></p>
							<?php
						}

						echo '<hr>';

						// Target Categories.

						if (! empty($value['wps_upsell_bump_target_categories'])) {

							echo '<p><i>' . esc_html__('Target Categories -', 'upsell-order-bump-offer-for-woocommerce') . '</i></p>';

							echo '<div class="wps_upsell_bump_list_targets">';

							foreach ($value['wps_upsell_bump_target_categories'] as $single_target_category_id) :

							?>
								<p><?php echo esc_html(wps_ubo_lite_getcat_title($single_target_category_id) . "( #$single_target_category_id )"); ?></p>
							<?php

							endforeach;

							echo '</div>';
						} else {

							?>
							<p><i><?php esc_html_e('No Categories added', 'upsell-order-bump-offer-for-woocommerce'); ?></i></p>
						<?php
						}

						?>
					</td>

					<!-- Bump Offer Product. -->
					<td>
						<p>
							<?php
							if (! empty($value['wps_upsell_bump_products_in_offer'])) {

								$single_offer_product = $value['wps_upsell_bump_products_in_offer'];
							?>
						<p><?php echo esc_html(wps_ubo_lite_get_title($single_offer_product) . "( #$single_offer_product )"); ?></p>
					<?php
							} else {

								esc_html_e('No offers Added', 'upsell-order-bump-offer-for-woocommerce');
							}

					?>
					</p>
					</td>
					<?php
					$wps_display_method = ! empty($value['wps_display_method']) ? sanitize_text_field(wp_unslash($value['wps_display_method'])) : '';
					if ('on' === $bump_offer_ab_method && wps_ubo_lite_is_plugin_active('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php')) :
					?>
						<td>

							<?php
							if ('ab_method' === $wps_display_method) :
								$has_two_bumps = (isset($wps_count_for_ab) && (int) $wps_count_for_ab > 1);
								if ($has_two_bumps) :

									$accept_count = isset($value['bump_success_count']) ? (int) $value['bump_success_count'] : 0;
									$orders_count = (isset($value['bump_orders_count']) && is_array($value['bump_orders_count'])) ? count($value['bump_orders_count']) : 0;
							?>
									<div class="wpsb-statcard">
										<div class="wpsb-head">
											<span class="wpsb-chip"><?php esc_html_e('A/B Mode', 'upsell-order-bump-offer-for-woocommerce'); ?></span>
											<div class="wpsb-title"><?php esc_html_e('Offer Performance', 'upsell-order-bump-offer-for-woocommerce'); ?></div>
										</div>
										<div class="wpsb-grid">
											<div class="wpsb-item">
												<div class="wpsb-label"><?php esc_html_e('Accept Offer', 'upsell-order-bump-offer-for-woocommerce'); ?></div>
												<div class="wpsb-value"><?php echo esc_html((string) $accept_count); ?></div>
											</div>
											<div class="wpsb-item">
												<div class="wpsb-label"><?php esc_html_e('Bump Shown', 'upsell-order-bump-offer-for-woocommerce'); ?></div>
												<div class="wpsb-value"><?php echo esc_html((string) $orders_count); ?></div>
											</div>
										</div>
										<?php if (0 === $accept_count && 0 === $orders_count) : ?>
											<div class="wpsb-muted"><?php esc_html_e('No activity recorded yet.', 'upsell-order-bump-offer-for-woocommerce'); ?></div>
										<?php endif; ?>
									</div>
								<?php

								else :
								?>
									<div class="wpsb-statcard">
										<div class="wpsb-head">
											<span class="wpsb-chip"><?php esc_html_e('A/B Mode', 'upsell-order-bump-offer-for-woocommerce'); ?></span>
											<div class="wpsb-title"><?php esc_html_e('Statistics Unavailable', 'upsell-order-bump-offer-for-woocommerce'); ?></div>
										</div>
										<div class="wpsb-muted"><?php esc_html_e('Ensure at least two bumps are configured to view A/B statistics.', 'upsell-order-bump-offer-for-woocommerce'); ?></div>
									</div>
								<?php
								endif;

							else :
								?>
								<div class="wpsb-statcard">
									<div class="wpsb-head">
										<span class="wpsb-chip"><?php esc_html_e('Default', 'upsell-order-bump-offer-for-woocommerce'); ?></span>
										<div class="wpsb-title"><?php esc_html_e('Default Bump Show', 'upsell-order-bump-offer-for-woocommerce'); ?></div>
									</div>
									<div class="wpsb-muted"><?php esc_html_e('A/B method is not active for this bump.', 'upsell-order-bump-offer-for-woocommerce'); ?></div>
								</div>
							<?php
							endif; ?>
						</td>
					<?php
					endif;
					?>
					<!-- Bump Action. -->
					<td>
						<!-- Bump View/Edit link. -->
						<a class="wps_upsell_bump_links" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&bump_id=<?php echo esc_html($key); ?>"><?php esc_html_e('View / Edit', 'upsell-order-bump-offer-for-woocommerce'); ?></a>

						<!-- Bump Delete link. -->
						<a class="wps_upsell_bump_links" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-list-offer-section&del_bump_id=<?php echo esc_html($key); ?>"><?php esc_html_e('Delete', 'upsell-order-bump-offer-for-woocommerce'); ?></a>

						<?php if (wps_ubo_lite_is_plugin_active('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php')) { ?>
							<!--Below will work for pro only -->
							<a class="wps_upsell_bump_links" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-list-offer-section&clone_bump_id=<?php echo esc_html($key); ?>"><?php esc_html_e('Clone', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
						<?php } ?>
					</td>

					<?php do_action('wps_ubo_add_more_col_data'); ?>
				</tr>
			<?php endforeach; ?>
			<!-- Foreach Bump end. -->
		</table>
	<?php endif; ?>
</div>

<!-- Add section to trigger Go Pro popup. -->
<?php if (! empty($wps_upsell_bumps_list) && count($wps_upsell_bumps_list)) : ?>

	<input type="hidden" class="wps_ubo_lite_saved_funnel" value="<?php echo (count($wps_upsell_bumps_list)); ?>">

<?php endif; ?>

<!-- Create New Bump. -->
<?php $installed_plugins = get_plugins(); ?>
<?php if (! wps_ubo_lite_is_plugin_active('upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php')) { ?>
	<!-- Create New Bump. -->
	<div class="wps_upsell_bump_create_new_bump">
		<a class="wps_ubo_lite_bump_create_button" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&bump_id=1"><?php esc_html_e('+Create New Order Bump', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
	</div>

<?php } else { ?>
	<div class="wps_upsell_bump_create_new_bump">
		<a href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&bump_id=<?php echo esc_html($wps_upsell_bumps_last_index + 1); ?>"><?php esc_html_e('+Create New Order Bump', 'upsell-order-bump-offer-for-woocommerce'); ?></a>
	</div>
<?php } ?>
<!-- Add Go pro popup. -->
<?php wps_ubo_go_pro('list'); ?>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		const toggleNonce = '<?php echo esc_js( wp_create_nonce( 'wps_ubo_toggle_bump_status' ) ); ?>';

		$('.wps_ubo_bump_status_toggle').on('change', function() {
			const $toggle = $(this);
			const bumpId = $toggle.data('bump-id');
			const isChecked = $toggle.is(':checked');
			const $text = $('.wps_ubo_bump_status_text[data-bump-id="' + bumpId + '"]');

			$toggle.prop('disabled', true);

			$.ajax({
				url: ajaxurl,
				method: 'POST',
				data: {
					action: 'wps_ubo_toggle_bump_status',
					bump_id: bumpId,
					status: isChecked ? 'yes' : 'no',
					nonce: toggleNonce
				},
				success: function(response) {
					if (response && response.success) {
						$text.text(response.data.status_label);
					} else {
						$toggle.prop('checked', !isChecked);
						alert(response && response.data && response.data.message ? response.data.message : '<?php echo esc_js( __( 'Unable to update status. Please try again.', 'upsell-order-bump-offer-for-woocommerce' ) ); ?>');
					}
				},
				error: function() {
					$toggle.prop('checked', !isChecked);
					alert('<?php echo esc_js( __( 'Error updating status. Please try again.', 'upsell-order-bump-offer-for-woocommerce' ) ); ?>');
				},
				complete: function() {
					$toggle.prop('disabled', false);
				}
			});
		});
	});
</script>

<style>
.wps_ubo_bump_toggle_wrap {
	display: inline-flex;
	align-items: center;
	margin-right: 8px;
}
.wps_ubo_bump_toggle_wrap input[type="checkbox"] {
	display: none;
}
.wps_ubo_bump_toggle_slider {
	position: relative;
	width: 42px;
	height: 22px;
	background: #c5c5c5;
	border-radius: 11px;
	cursor: pointer;
	transition: background 0.2s ease;
	display: inline-block;
}
.wps_ubo_bump_toggle_slider:before {
	content: '';
	position: absolute;
	left: 3px;
	top: 3px;
	width: 16px;
	height: 16px;
	background: #fff;
	border-radius: 50%;
	transition: transform 0.2s ease;
	box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}
.wps_ubo_bump_toggle_wrap input:checked + .wps_ubo_bump_toggle_slider {
	background: #4caf50;
}
.wps_ubo_bump_toggle_wrap input:checked + .wps_ubo_bump_toggle_slider:before {
	transform: translateX(20px);
}
</style>

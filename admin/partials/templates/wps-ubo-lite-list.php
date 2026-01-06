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
$wps_upsell_bumps_list = get_option('wps_ubo_bump_list', array());
$wps_upsell_bumps_list = is_array($wps_upsell_bumps_list) ? $wps_upsell_bumps_list : array();


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


$wps_bump_display_list = array_filter(
	$wps_upsell_bumps_list,
	function( $value ) {
		return is_array( $value ) && ( ! isset( $value['wps_is_abandoned_bump'] ) || 'yes' !== $value['wps_is_abandoned_bump'] );
	}
);
$count_abandoned_bumps = count( $wps_bump_display_list );

// Pagination setup.
$wps_bump_per_page   = absint( apply_filters( 'wps_ubo_bump_list_per_page', 10 ) );
$wps_bump_per_page   = $wps_bump_per_page > 0 ? $wps_bump_per_page : 10;
$wps_total_bumps     = $count_abandoned_bumps;
$wps_total_pages     = max( 1, (int) ceil( $wps_total_bumps / $wps_bump_per_page ) );
$wps_current_page    = isset( $_GET['wps_bump_page'] ) ? max( 1, absint( $_GET['wps_bump_page'] ) ) : 1;
$wps_current_page    = min( $wps_current_page, $wps_total_pages );
$wps_offset          = ( $wps_current_page - 1 ) * $wps_bump_per_page;
$wps_paginated_bumps = array_slice( $wps_bump_display_list, $wps_offset, $wps_bump_per_page, true );

$wps_current_tab     = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'order-bump-section';
$wps_current_sub_tab = isset( $_GET['sub_tab'] ) ? sanitize_text_field( wp_unslash( $_GET['sub_tab'] ) ) : '';
$wps_bump_base_args  = array(
	'page' => 'upsell-order-bump-offer-for-woocommerce-setting',
	'tab'  => $wps_current_tab,
);

if ( ! empty( $wps_current_sub_tab ) ) {
	$wps_bump_base_args['sub_tab'] = $wps_current_sub_tab;
}

$wps_bump_base_url = add_query_arg( $wps_bump_base_args, admin_url( 'admin.php' ) );

$wps_bump_import_status = isset($_GET['wps_bump_import_status']) ? sanitize_text_field(wp_unslash($_GET['wps_bump_import_status'])) : '';
$wps_bump_imported      = isset($_GET['wps_bump_imported']) ? absint($_GET['wps_bump_imported']) : 0;
?>

<div class="wps_ubo_action_bar">
	<div class="wps_ubo_bump_tools_wrapper">
			<div class="wps_ubo_bump_tool">
				<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
					<input type="hidden" name="action" value="wps_ubo_export_bumps_json">
					<?php wp_nonce_field('wps_ubo_export_bumps'); ?>
					<button type="submit" class="button button-primary"><?php esc_html_e('Export Bumps (JSON)', 'upsell-order-bump-offer-for-woocommerce'); ?></button>
				</form>
			</div>
			<div class="wps_ubo_bump_tool">
				<form id="wps_ubo_import_csv_form" enctype="multipart/form-data" data-nonce="<?php echo esc_attr(wp_create_nonce('wps_admin_nonce')); ?>">
					<input type="file" id="wps_ubo_import_file" name="wps_ubo_import_file" accept=".json,application/json" required>
					<button type="submit" class="button"><?php esc_html_e('Import Bumps (JSON)', 'upsell-order-bump-offer-for-woocommerce'); ?></button>
					<div id="wps_ubo_import_notice" class="wps_ubo_import_notice"></div>
				</form>
			</div>
	</div>
</div>

<?php if (! empty($wps_bump_import_status)) : ?>
	<?php
	$notice_class = 'notice-info';
	$notice_msg   = '';

	if ('success' === $wps_bump_import_status) {
		$notice_class = 'notice-success';
		$notice_msg   = sprintf(
			/* translators: %d number of imported bumps */
			esc_html__('%d order bump(s) imported successfully.', 'upsell-order-bump-offer-for-woocommerce'),
			$wps_bump_imported
		);
	} elseif ('file_error' === $wps_bump_import_status) {
		$notice_class = 'notice-error';
		$notice_msg   = esc_html__('Upload failed. Please try again.', 'upsell-order-bump-offer-for-woocommerce');
	} elseif ('invalid_type' === $wps_bump_import_status) {
		$notice_class = 'notice-error';
		$notice_msg   = esc_html__('Please upload a valid JSON export file.', 'upsell-order-bump-offer-for-woocommerce');
	} elseif ('invalid_data' === $wps_bump_import_status) {
		$notice_class = 'notice-error';
		$notice_msg   = esc_html__('The uploaded file could not be read. Please check the export file and try again.', 'upsell-order-bump-offer-for-woocommerce');
	}
	?>
	<?php if (! empty($notice_msg)) : ?>
		<div class="notice <?php echo esc_attr($notice_class); ?> is-dismissible">
			<p><?php echo esc_html($notice_msg); ?></p>
		</div>
	<?php endif; ?>
<?php endif; ?>

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
			<?php foreach ($wps_paginated_bumps as $key => $value) : ?>
				<?php
				// Skip if bump data is not an array.
				if (! is_array($value)) {
					continue;
				}

				// Skip if key 'wps_is_abandoned_bump' exists in serialized cart data.
				if (isset($value['wps_is_abandoned_bump']) && ! empty($value['wps_is_abandoned_bump']) && ('yes' === $value['wps_is_abandoned_bump'])) {
					continue; // skip this row.
				}

				$label_campaign = isset($value['wps_bump_label_campaign']) ? $value['wps_bump_label_campaign'] : '';
				$wps_bump_name = ! empty($value['wps_upsell_bump_name']) ? $value['wps_upsell_bump_name'] : '';

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

						<a class="wps_upsell_bump_list_name" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&bump_id=<?php echo esc_html($key); ?>"><?php echo esc_html($wps_bump_name); ?></a>
						<p><i><?php esc_html_e('Priority : ', 'upsell-order-bump-offer-for-woocommerce'); ?><span class="wps-bump-priority"><?php echo esc_html(! empty($value['wps_upsell_bump_priority']) ? $value['wps_upsell_bump_priority'] : 'No Priority'); ?></span></i></p>
					</td>
					</td>

					<!-- Bump Status. -->
					<td>
						<?php
						$bump_status = ! empty($value['wps_upsell_bump_status']) ? $value['wps_upsell_bump_status'] : 'no';
						?>
						<label class="wps_ubo_toggle_switch">
							<input type="checkbox" class="wps-ubo-status-toggle" data-bump-id="<?php echo esc_attr($key); ?>" <?php checked('yes', $bump_status); ?>>
							<span class="wps_ubo_toggle_slider"></span>
						</label>
						<span class="wps_ubo_status_label wps-ubo-status-text"><?php echo 'yes' === $bump_status ? esc_html__('Live', 'upsell-order-bump-offer-for-woocommerce') : esc_html__('Sandbox', 'upsell-order-bump-offer-for-woocommerce'); ?></span>
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

		<?php if ( $wps_total_pages > 1 ) : ?>
			<div class="wps_ubo_pagination">
				<div class="wps_ubo_page_info">
					<?php
					printf(
						/* translators: 1: current page, 2: total pages */
						esc_html__( 'Page %1$d of %2$d', 'upsell-order-bump-offer-for-woocommerce' ),
						(int) $wps_current_page,
						(int) $wps_total_pages
					);
					?>
				</div>
				<div class="wps_ubo_page_links">
					<?php if ( $wps_current_page > 1 ) : ?>
						<a class="button" href="<?php echo esc_url( add_query_arg( 'wps_bump_page', $wps_current_page - 1, $wps_bump_base_url ) ); ?>">&laquo; <?php esc_html_e( 'Previous', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
					<?php endif; ?>

					<span class="wps_ubo_page_number"><?php echo esc_html( $wps_current_page ); ?></span>

					<?php if ( $wps_current_page < $wps_total_pages ) : ?>
						<a class="button" href="<?php echo esc_url( add_query_arg( 'wps_bump_page', $wps_current_page + 1, $wps_bump_base_url ) ); ?>"><?php esc_html_e( 'Next', 'upsell-order-bump-offer-for-woocommerce' ); ?> &raquo;</a>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div>

<!-- Add section to trigger Go Pro popup. -->
<?php if ( ! empty( $wps_upsell_bumps_list ) && count( $wps_upsell_bumps_list ) ) : ?>

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
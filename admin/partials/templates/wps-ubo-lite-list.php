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
$wps_bump_per_page   = absint( apply_filters( 'wps_ubo_bump_list_per_page', 6 ) );
$wps_bump_per_page   = $wps_bump_per_page > 0 ? $wps_bump_per_page : 6;
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
				<form id="wps_ubo_import_csv_form" enctype="multipart/form-data" data-nonce="<?php echo esc_attr(wp_create_nonce('wps_admin_nonce')); ?>">
					<input type="file" id="wps_ubo_import_file" name="wps_ubo_import_file" accept=".json,application/json" required>
					<button type="submit" class="button"><?php esc_html_e('Import Bumps (JSON)', 'upsell-order-bump-offer-for-woocommerce'); ?></button>
					<?php wps_upsell_lite_wc_help_tip( esc_html__('Upload a JSON file exported from this plugin to restore your bumps.', 'upsell-order-bump-offer-for-woocommerce') ); ?>
					<div id="wps_ubo_import_notice" class="wps_ubo_import_notice"></div>
				</form>
			</div>

			<div class="wps_ubo_bump_tool">
				<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
					<input type="hidden" name="action" value="wps_ubo_export_bumps_json">
					<?php wp_nonce_field('wps_ubo_export_bumps'); ?>
					<button type="submit" class="button button-primary"><?php esc_html_e('Export Bumps (JSON)', 'upsell-order-bump-offer-for-woocommerce'); ?></button>
					<?php wps_upsell_lite_wc_help_tip( esc_html__('Download all bump offers as a JSON backup file.', 'upsell-order-bump-offer-for-woocommerce') ); ?>
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
		<div class="wps-ubo-bump-cards">
			<!-- Foreach Bump start. -->
			<?php foreach ( $wps_paginated_bumps as $key => $value ) : ?>
				<?php
				if ( ! is_array( $value ) ) {
					continue;
				}

				if ( isset( $value['wps_is_abandoned_bump'] ) && ! empty( $value['wps_is_abandoned_bump'] ) && ( 'yes' === $value['wps_is_abandoned_bump'] ) ) {
					continue;
				}

				$label_campaign = isset( $value['wps_bump_label_campaign'] ) ? $value['wps_bump_label_campaign'] : '';
				$wps_bump_name  = ! empty( $value['wps_upsell_bump_name'] ) ? $value['wps_upsell_bump_name'] : '';

				list( $color_hex, $label_name ) = array_pad( explode( '/', $label_campaign, 2 ), 2, '' );
				$wps_ubo_global_options             = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );
				$wps_bump_enable_campaign_labels    = ! empty( $wps_ubo_global_options['wps_bump_enable_campaign_labels'] ) ? $wps_ubo_global_options['wps_bump_enable_campaign_labels'] : '';
				$bump_status                        = ! empty( $value['wps_upsell_bump_status'] ) ? $value['wps_upsell_bump_status'] : 'no';
				?>

				<div class="wps-ubo-bump-card">
					<div class="wps-ubo-bump-card__header">
						<div class="wps-ubo-bump-card__title">
							<?php if ( 'on' === $wps_bump_enable_campaign_labels ) : ?>
								<span class="wps_label_color" style="background-color: <?php echo esc_attr( $color_hex ); ?>;"><?php echo esc_html( $label_name ); ?></span>
							<?php endif; ?>
							<a class="wps_upsell_bump_list_name" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&bump_id=<?php echo esc_html( $key ); ?>"><?php echo esc_html( $wps_bump_name ); ?></a>
							<p class="wps-ubo-bump-priority"><i><?php esc_html_e( 'Priority : ', 'upsell-order-bump-offer-for-woocommerce' ); ?><span class="wps-bump-priority"><?php echo esc_html( ! empty( $value['wps_upsell_bump_priority'] ) ? $value['wps_upsell_bump_priority'] : 'No Priority' ); ?></span></i></p>
						</div>
						<div class="wps-ubo-bump-card__status">
							<label class="wps_ubo_toggle_switch">
								<input type="checkbox" class="wps-ubo-status-toggle" data-bump-id="<?php echo esc_attr( $key ); ?>" <?php checked( 'yes', $bump_status ); ?>>
								<span class="wps_ubo_toggle_slider"></span>
							</label>
							<span class="wps_ubo_status_label wps-ubo-status-text"><?php echo 'yes' === $bump_status ? esc_html__( 'Live', 'upsell-order-bump-offer-for-woocommerce' ) : esc_html__( 'Sandbox', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						</div>
					</div>

					<div class="wps-ubo-bump-card__body">
						<div class="wps-ubo-bump-block">
							<div class="wps-ubo-block-title"><?php esc_html_e( 'Targets', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
							<?php
							if ( ! empty( $value['wps_upsell_bump_target_ids'] ) ) {
								echo '<div class="wps_upsell_bump_list_targets">';
								foreach ( $value['wps_upsell_bump_target_ids'] as $single_target_product ) :
									?>
									<p><?php echo esc_html( wps_ubo_lite_get_title( $single_target_product ) . "( #$single_target_product )" ); ?></p>
									<?php
								endforeach;
								echo '</div>';
							} else {
								?>
								<p><i><?php esc_html_e( 'No Product(s) added', 'upsell-order-bump-offer-for-woocommerce' ); ?></i></p>
								<?php
							}

							if ( ! empty( $value['wps_upsell_bump_target_categories'] ) ) {
								echo '<p><i>' . esc_html__( 'Target Categories -', 'upsell-order-bump-offer-for-woocommerce' ) . '</i></p>';
								echo '<div class="wps_upsell_bump_list_targets">';
								foreach ( $value['wps_upsell_bump_target_categories'] as $single_target_category_id ) :
									?>
									<p><?php echo esc_html( wps_ubo_lite_getcat_title( $single_target_category_id ) . "( #$single_target_category_id )" ); ?></p>
									<?php
								endforeach;
								echo '</div>';
							} else {
								?>
								<p><i><?php esc_html_e( 'No Categories added', 'upsell-order-bump-offer-for-woocommerce' ); ?></i></p>
								<?php
							}
							?>
						</div>

						<div class="wps-ubo-bump-block">
							<div class="wps-ubo-block-title"><?php esc_html_e( 'Offer', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
							<?php
							if ( ! empty( $value['wps_upsell_bump_products_in_offer'] ) ) {
								$single_offer_product = $value['wps_upsell_bump_products_in_offer'];
								?>
								<p><?php echo esc_html( wps_ubo_lite_get_title( $single_offer_product ) . "( #$single_offer_product )" ); ?></p>
								<?php
							} else {
								esc_html_e( 'No offers Added', 'upsell-order-bump-offer-for-woocommerce' );
							}
							?>
						</div>

						<?php
						$wps_display_method = ! empty( $value['wps_display_method'] ) ? sanitize_text_field( wp_unslash( $value['wps_display_method'] ) ) : '';
						if ( 'on' === $bump_offer_ab_method && wps_ubo_lite_is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) ) :
							?>
							<div class="wps-ubo-bump-block">
								<div class="wps-ubo-block-title"><?php esc_html_e( 'A/B Status', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
								<?php
								if ( 'ab_method' === $wps_display_method ) :
									$has_two_bumps = ( isset( $wps_count_for_ab ) && (int) $wps_count_for_ab > 1 );
									if ( $has_two_bumps ) :
										$accept_count = isset( $value['bump_success_count'] ) ? (int) $value['bump_success_count'] : 0;
										$orders_count = ( isset( $value['bump_orders_count'] ) && is_array( $value['bump_orders_count'] ) ) ? count( $value['bump_orders_count'] ) : 0;
										?>
										<div class="wpsb-statcard">
											<div class="wpsb-head">
												<span class="wpsb-chip"><?php esc_html_e( 'A/B Mode', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
												<div class="wpsb-title"><?php esc_html_e( 'Offer Performance', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
											</div>
											<div class="wpsb-grid">
												<div class="wpsb-item">
													<div class="wpsb-label"><?php esc_html_e( 'Accept Offer', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
													<div class="wpsb-value"><?php echo esc_html( (string) $accept_count ); ?></div>
												</div>
												<div class="wpsb-item">
													<div class="wpsb-label"><?php esc_html_e( 'Bump Shown', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
													<div class="wpsb-value"><?php echo esc_html( (string) $orders_count ); ?></div>
												</div>
											</div>
											<?php if ( 0 === $accept_count && 0 === $orders_count ) : ?>
												<div class="wpsb-muted"><?php esc_html_e( 'No activity recorded yet.', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
											<?php endif; ?>
										</div>
										<?php
									else :
										?>
										<div class="wpsb-statcard">
											<div class="wpsb-head">
												<span class="wpsb-chip"><?php esc_html_e( 'A/B Mode', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
												<div class="wpsb-title"><?php esc_html_e( 'Statistics Unavailable', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
											</div>
											<div class="wpsb-muted"><?php esc_html_e( 'Ensure at least two bumps are configured to view A/B statistics.', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
										</div>
										<?php
									endif;
								else :
									?>
									<div class="wpsb-statcard">
										<div class="wpsb-head">
											<span class="wpsb-chip"><?php esc_html_e( 'Default', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
											<div class="wpsb-title"><?php esc_html_e( 'Default Bump Show', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
										</div>
										<div class="wpsb-muted"><?php esc_html_e( 'A/B method is not active for this bump.', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
									</div>
									<?php
								endif;
								?>
							</div>
						<?php endif; ?>

						<div class="wps-ubo-bump-block wps-ubo-bump-actions">
							<a class="wps_upsell_bump_links" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&bump_id=<?php echo esc_html( $key ); ?>"><?php esc_html_e( 'View / Edit', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
							<a class="wps_upsell_bump_links" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-list-offer-section&del_bump_id=<?php echo esc_html( $key ); ?>"><?php esc_html_e( 'Delete', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
							<?php if ( wps_ubo_lite_is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) ) { ?>
								<a class="wps_upsell_bump_links" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-list-offer-section&clone_bump_id=<?php echo esc_html( $key ); ?>"><?php esc_html_e( 'Clone', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
							<?php } ?>
						<a class="wps_upsell_bump_links" href="?page=upsell-order-bump-offer-for-woocommerce-pre-reporting"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M10 7H7V16H10V7Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M17 7H14V12H17V7Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>

</a>
						</div>
					</div>

					<?php do_action( 'wps_ubo_add_more_col_data' ); ?>
				</div>
			<?php endforeach; ?>
			<!-- Foreach Bump end. -->
		</div>

		<?php
		if ( $wps_total_pages > 1 ) {
			wps_ubo_render_admin_pagination( $wps_current_page, $wps_total_pages, $wps_bump_base_url, 'wps_bump_page' );
		}
		?>
	<?php endif; ?>
</div>

<!-- Add section to trigger Go Pro popup. -->
<?php if ( ! empty( $wps_upsell_bumps_list ) && count( $wps_upsell_bumps_list ) ) : ?>

	<input type="hidden" class="wps_ubo_lite_saved_funnel" value="<?php echo (count($wps_upsell_bumps_list)); ?>">

<?php endif; ?>

<!-- Create New Bump via template modal. -->
<?php $installed_plugins = get_plugins(); ?>
<?php
$wps_ubo_is_pro_active     = wps_ubo_lite_if_pro_exists();
$wps_ubo_creation_bump_id = wps_ubo_lite_is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) ? $wps_upsell_bumps_last_index + 1 : 1;
$wps_ubo_creation_link    = add_query_arg(
	array(
		'page'    => 'upsell-order-bump-offer-for-woocommerce-setting',
		'tab'     => 'order-bump-section',
		'sub_tab' => 'pre-save-offer-section',
		'bump_id' => $wps_ubo_creation_bump_id,
	),
	admin_url( 'admin.php' )
);
$wps_ubo_template_cards = array(
	array(
		'slug'        => 'upsell-default',
		'title'       => __( 'Upsell Default', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Default upsell layout for standard offers.', 'upsell-order-bump-offer-for-woocommerce' ),
		'icon'        => 'dashicons-visibility',
	),
	array(
		'slug'        => 'conditional-display',
		'title'       => __( 'Bump Conditional Display', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Show bumps only when advanced display rules match.', 'upsell-order-bump-offer-for-woocommerce' ),
		'icon'        => 'dashicons-visibility',
	),
	array(
		'slug'        => 'smart-offer-upgrade',
		'title'       => __( 'Smart Offer Upgrade', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Upsell-ready layout to boost average order value.', 'upsell-order-bump-offer-for-woocommerce' ),
		'is_pro'      => true,
		'icon'        => 'dashicons-cart',
	),
	array(
		'slug'        => 'exclusive-limits',
		'title'       => __( 'Exclusive Limits', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Limited-quantity vibe to drive urgency.', 'upsell-order-bump-offer-for-woocommerce' ),
		'is_pro'      => true,
		'icon'        => 'dashicons-lock',
	),
	array(
		'slug'        => 'global-order-bump',
		'title'       => __( 'Global Order Bump', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Sitewide-ready template for broad bumps.', 'upsell-order-bump-offer-for-woocommerce' ),
		'is_pro'      => true,
		'icon'        => 'dashicons-admin-site',
	),
	array(
		'slug'        => 'countdown-timer',
		'title'       => __( 'Countdown Timer', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Time-boxed bump with a visible clock.', 'upsell-order-bump-offer-for-woocommerce' ),
		'is_pro'      => true,
		'icon'        => 'dashicons-clock',
	),
	array(
		'slug'        => 'evergreen-timer',
		'title'       => __( 'Evergreen Timer', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Rolling countdown for consistent urgency.', 'upsell-order-bump-offer-for-woocommerce' ),
		'is_pro'      => true,
		'icon'        => 'dashicons-backup',
	),
	array(
		'slug'        => 'gallery-slider',
		'title'       => __( 'Product Gallery Image Slider', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Carousel-style visuals to showcase offers.', 'upsell-order-bump-offer-for-woocommerce' ),
		'is_pro'      => true,
		'icon'        => 'dashicons-format-gallery',
	),
	array(
		'slug'        => 'meta-forms',
		'title'       => __( 'Meta Forms', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Collect quick user inputs with the bump.', 'upsell-order-bump-offer-for-woocommerce' ),
		'is_pro'      => true,
		'icon'        => 'dashicons-feedback',
	),
	array(
		'slug'        => 'ab-testing',
		'title'       => __( 'A/B Testing', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Preset for experimenting with variants.', 'upsell-order-bump-offer-for-woocommerce' ),
		'is_pro'      => true,
		'icon'        => 'dashicons-chart-bar',
	),
	array(
		'slug'        => 'smart-coupon',
		'title'       => __( 'Smart Coupon Offer', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Coupon-forward layout to highlight savings.', 'upsell-order-bump-offer-for-woocommerce' ),
		'is_pro'      => true,
		'icon'        => 'dashicons-tickets-alt',
	),
	array(
		'slug'        => 'upsell-popup',
		'title'       => __( 'Upsell Popup', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Popup-style upsell for high-impact offers.', 'upsell-order-bump-offer-for-woocommerce' ),
		'is_pro'      => true,
		'icon'        => 'dashicons-visibility',
	),
);
?>

<div class="wps_upsell_bump_create_new_bump">
	<button type="button"  id="wps-ubo-open-template-modal"><?php esc_html_e('+Create New Order Bump', 'upsell-order-bump-offer-for-woocommerce'); ?></button>
</div>

<div class="wps-ubo-template-modal" id="wps-ubo-template-modal" aria-hidden="true">
	<div class="wps-ubo-template-overlay" id="wps-ubo-template-overlay"></div>
	<div class="wps-ubo-template-dialog" role="dialog" aria-modal="true">
		<div class="wps-ubo-template-dialog__head">
			<h3><?php esc_html_e( 'Choose a Campaign Layout', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
			<?php if ( ! $wps_ubo_is_pro_active ) : ?>
				<p class="wps-ubo-template-note">
					<?php
printf(
	wp_kses(
		/* translators: 1: Opening link tag, 2: closing link tag. */
		__( '<em>Multiple order bumps are available in the Pro version. %1$sPurchase Pro%2$s to create additional bumps.</em>', 'upsell-order-bump-offer-for-woocommerce' ),
		array(
			'a'  => array(
				'href'   => array(),
				'class'  => array(),
				'target' => array(),
				'rel'    => array(),
			),
			'em' => array(), // ✅ allow italic tag
		)
	),
	'<a class="wps-ubo-template-upgrade-link" href="' . esc_url( 'https://wpswings.com/product/upsell-order-bump-offer-for-woocommerce-pro/?utm_source=order-bump-org&utm_medium=referral&utm_campaign=order-bump-pro' ) . '" target="_blank" rel="noopener noreferrer">',
	'</a>'
);

					?>
				</p>
			<?php endif; ?>
			<button type="button" class="wps-ubo-template-close" id="wps-ubo-template-close" aria-label="<?php esc_attr_e( 'Close', 'upsell-order-bump-offer-for-woocommerce' ); ?>">×</button>
		</div>
		<div class="wps-ubo-template-grid">
			<?php foreach ( $wps_ubo_template_cards as $card ) : ?>
				<?php
					$card_is_pro   = ! empty( $card['is_pro'] );
					$card_is_lock  = $card_is_pro && ! $wps_ubo_is_pro_active;
					$card_icon     = ! empty( $card['icon'] ) ? $card['icon'] : 'dashicons-layout';
					$button_label  = $card_is_lock ? __( 'Unlock with PRO', 'upsell-order-bump-offer-for-woocommerce' ) : __( 'Create Campaign', 'upsell-order-bump-offer-for-woocommerce' );
				?>
				<div class="wps-ubo-template-card <?php echo $card_is_lock ? 'is-locked' : 'is-open'; ?>" data-template="<?php echo esc_attr( $card['slug'] ); ?>">
					<div class="wps-ubo-template-card__head">
						<div class="wps-ubo-template-card__title">
							<span class="dashicons <?php echo esc_attr( $card_icon ); ?> wps-ubo-template-icon" aria-hidden="true"></span>
							<div class="wps-ubo-template-card__text">
								<h4><?php echo esc_html( $card['title'] ); ?></h4>
								<p><?php echo esc_html( $card['description'] ); ?></p>
							</div>
						</div>
						<?php if ( $card_is_pro ) : ?>
							<span class="wps-ubo-template-badge <?php echo $card_is_lock ? 'is-locked' : 'is-pro'; ?>">
								<?php echo $card_is_lock ? esc_html__( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ) : esc_html__( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?>
							</span>
						<?php endif; ?>
					</div>
					<a href="https://wpswings.com/product/upsell-order-bump-offer-for-woocommerce-pro/?utm_source=order-bump-org&utm_medium=referral&utm_campaign=order-bump-pro" class="button button-primary wps-ubo-template-choose <?php echo $card_is_lock ? 'is-locked' : ''; ?>" data-template="<?php echo esc_attr( $card['slug'] ); ?>" data-locked="<?php echo $card_is_lock ? '1' : '0'; ?>" <?php echo $card_is_lock ? 'aria-disabled="true"' : ''; ?> target="_blank">
				<?php echo esc_html( $button_label ); ?>
				</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<?php $next_bump_id = $wps_upsell_bumps_last_index + 1; ?>
<script>
	(function($){
		const modal = $('#wps-ubo-template-modal');
		const overlay = $('#wps-ubo-template-overlay');
		const openBtn = $('#wps-ubo-open-template-modal');
		const closeBtn = $('#wps-ubo-template-close');
		let createUrl = '<?php echo "?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&bump_id={$wps_ubo_creation_bump_id}"; ?>';
		function openModal() {
			modal.attr('aria-hidden', 'false').addClass('is-open');
			$('body').addClass('wps-ubo-modal-open');
		}
		function closeModal() {
			modal.attr('aria-hidden', 'true').removeClass('is-open');
			$('body').removeClass('wps-ubo-modal-open');
		}

		openBtn.on('click', openModal);
		closeBtn.on('click', closeModal);
		overlay.on('click', closeModal);

		$(document).on('keyup', function(e){
			if (27 === e.keyCode) {
				closeModal();
			}
		});

		modal.on('click', '.wps-ubo-template-choose', function(){
			if ( $(this).hasClass('is-locked') ) {
				// Locked cards link out to the Pro upgrade page; allow normal navigation.
				return;
			}
			const template = $(this).data('template') || 'default';
			window.location.href = createUrl + '&template=' + encodeURIComponent(template);
			console.log( createUrl + '&template=' + encodeURIComponent(template) );
		});
	})(jQuery);
</script>
<!-- Add Go pro popup. -->
<?php wps_ubo_go_pro('list'); ?>

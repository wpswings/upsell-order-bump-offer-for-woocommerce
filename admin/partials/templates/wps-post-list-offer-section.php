<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used for listing all the funnels of the plugin.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=upsell-org-backend&utm_campaign=official
 * @since      1.0.0
 *
 * @package     woo_one_click_upsell_funnel
 * @subpackage  woo_one_click_upsell_funnel/admin/partials/templates
 */

/**
 * Exit if accessed directly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Funnels Listing Template.
 *
 * This template is used for listing all existing funnels with
 * view/edit and delete option.
 */

if ( ! empty( $_GET['del_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['del_nonce'] ) ), 'del_funnel' ) ) {

	// Delete funnel.
	if ( isset( $_GET['del_funnel_id'] ) ) {

		$funnel_id = sanitize_text_field( wp_unslash( $_GET['del_funnel_id'] ) );

		// Get all funnels.
		$wps_wocuf_pro_funnels = get_option( 'wps_wocuf_funnels_list' );

		foreach ( $wps_wocuf_pro_funnels as $single_funnel => $data ) {

			if ( (int) $funnel_id === (int) $single_funnel ) {

				unset( $wps_wocuf_pro_funnels[ $single_funnel ] );
				break;
			}
		}

		update_option( 'wps_wocuf_funnels_list', $wps_wocuf_pro_funnels );
		// Delete associated discount rules.
		$wc_dynamic_discount_rules = get_option( 'wc_dynamic_discount_rules', array() );

		$funnel_type = 'wps_funnel_one';
		if ( ! empty( $funnel_type ) ) {

			if ( isset( $wc_dynamic_discount_rules[ $funnel_type ][ $funnel_id ] ) ) {
				unset( $wc_dynamic_discount_rules[ $funnel_type ][ $funnel_id ] );
			}

			if ( empty( $wc_dynamic_discount_rules[ $funnel_type ] ) ) {
				unset( $wc_dynamic_discount_rules[ $funnel_type ] );
			}
		} else {
			foreach ( $wc_dynamic_discount_rules as $funnel_key => $bumps ) {

				if ( isset( $wc_dynamic_discount_rules[ $funnel_key ][ $funnel_id ] ) ) {
					unset( $wc_dynamic_discount_rules[ $funnel_key ][ $funnel_id ] );
				}

				if ( empty( $wc_dynamic_discount_rules[ $funnel_key ] ) ) {
					unset( $wc_dynamic_discount_rules[ $funnel_key ] );
				}
			}
		}

		update_option( 'wc_dynamic_discount_rules', $wc_dynamic_discount_rules );

		wp_safe_redirect( esc_url_raw( admin_url( 'admin.php?page=upsell-order-bump-offer-for-woocommerce-setting&tab=one-click-section&sub_tab=post-list-offer-section' ) ) );
		exit;
	}
}

// Get all funnels.
$wps_wocuf_pro_funnels_list = get_option( 'wps_wocuf_funnels_list', array() );
$wps_wocuf_pro_funnels_list = is_array( $wps_wocuf_pro_funnels_list ) ? $wps_wocuf_pro_funnels_list : array();

// Pagination setup.
$wps_funnel_per_page = absint( apply_filters( 'wps_ubo_funnel_list_per_page', 6 ) );
$wps_funnel_per_page = $wps_funnel_per_page > 0 ? $wps_funnel_per_page : 6;
$wps_total_funnels = count( $wps_wocuf_pro_funnels_list );
$wps_total_pages = max( 1, (int) ceil( $wps_total_funnels / $wps_funnel_per_page ) );
$wps_current_page = isset( $_GET['wps_funnel_page'] ) ? max( 1, absint( $_GET['wps_funnel_page'] ) ) : 1;
$wps_current_page = min( $wps_current_page, $wps_total_pages );
$wps_offset = ( $wps_current_page - 1 ) * $wps_funnel_per_page;
$wps_paginated_funnels = array_slice( $wps_wocuf_pro_funnels_list, $wps_offset, $wps_funnel_per_page, true );
$wps_funnel_base_url = add_query_arg(
	array(
		'page' => 'upsell-order-bump-offer-for-woocommerce-setting',
		'tab' => 'one-click-section',
		'sub_tab' => 'post-list-offer-section',
	),
	admin_url( 'admin.php' )
);
?>
<div class="wps_ubo_action_bar <?php if(! wps_ubo_lite_if_pro_exists()) echo 'wps-disabled'; ?>">
	<div class="wps_ubo_bump_tools_wrapper">

		<div class="wps_ubo_bump_tool">
			<form id="wps_ubo_import_funnel_csv_form" enctype="multipart/form-data"
				data-nonce="<?php echo esc_attr( wp_create_nonce( 'wps_admin_nonce' ) ); ?>">
				<input type="file" id="wps_ubo_import_funnel_file" name="wps_ubo_import_file"
					accept=".json,application/json" required
					title="<?php echo esc_attr__( 'Upload a funnel export JSON file from this plugin to restore funnels.', 'upsell-order-bump-offer-for-woocommerce' ); ?>">
				<button type="submit" class="button"
					title="<?php echo esc_attr__( 'Import funnels from a previously exported JSON file.', 'upsell-order-bump-offer-for-woocommerce' ); ?>"><?php esc_html_e( 'Import Funnels (JSON)', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
				<?php wps_upsell_lite_wc_help_tip( esc_html__( 'Import funnels from a previously exported JSON file.', 'upsell-order-bump-offer-for-woocommerce' ) ); ?>
				<div id="wps_ubo_import_funnel_notice" class="wps_ubo_import_notice"></div>
			</form>
		</div>

		<div class="wps_ubo_bump_tool">
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="wps_wocuf_export_funnels_json">
				<?php wp_nonce_field( 'wps_ubo_export_bumps' ); ?>
				<button type="submit" class="button button-primary"
					title="<?php echo esc_attr__( 'Download all funnels as a JSON backup file.', 'upsell-order-bump-offer-for-woocommerce' ); ?>"><?php esc_html_e( 'Export Funnels (JSON)', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
				<?php wps_upsell_lite_wc_help_tip( esc_html__( 'Download all funnels as a JSON backup file.', 'upsell-order-bump-offer-for-woocommerce' ) ); ?>
			</form>
		</div>
	</div>
</div>

<?php
if ( ! empty( $wps_wocuf_pro_funnels_list ) ) {

	// Temp funnel variable.
	$wps_wocuf_pro_funnel_duplicate = $wps_wocuf_pro_funnels_list;

	// Make key pointer point to the end funnel.
	end( $wps_wocuf_pro_funnel_duplicate );

	// Now key function will return last funnel key.
	$wps_wocuf_pro_funnel_number = key( $wps_wocuf_pro_funnel_duplicate );
} else {
	// When no funnel is there then new funnel id will be 1 (0+1).
	$wps_wocuf_pro_funnel_number = 0;
}

?>
<!-- Add section to trigger Go Pro popup. -->
<?php if ( ! empty( $wps_wocuf_pro_funnels_list ) && count( $wps_wocuf_pro_funnels_list ) ) : ?>

	<input type="hidden" class="wps_ubo_lite_saved_funnel" value="<?php echo ( count( $wps_wocuf_pro_funnels_list ) ); ?>">

<?php endif; ?>

<div class="wps_wocuf_pro_funnels_list">

	<?php if ( empty( $wps_wocuf_pro_funnels_list ) ) : ?>

		<p class="wps_wocuf_pro_no_funnel">
			<?php esc_html_e( 'No funnels added', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>

	<?php endif; ?>

	<?php if ( ! empty( $wps_wocuf_pro_funnels_list ) ) : ?>
		<div class="wps-ubo-bump-cards wps-ubo-funnel-cards">
			<?php foreach ( $wps_paginated_funnels as $key => $value ) : ?>
				<?php
				if ( ! is_array( $value ) ) {
					continue;
				}

				$offers_count = ! empty( $value['wps_wocuf_products_in_offer'] ) ? $value['wps_wocuf_products_in_offer'] : array();
				$offers_count = count( $offers_count );
				$label_campaign = isset( $value['wps_bump_label_campaign'] ) ? $value['wps_bump_label_campaign'] : '';

				list($color_hex, $label_name) = array_pad( explode( '/', $label_campaign, 2 ), 2, '' );
				$wps_ubo_global_options = get_option( 'wps_ubo_global_options', wps_ubo_lite_default_global_options() );
				$wps_bump_enable_campaign_labels = ! empty( $wps_ubo_global_options['wps_bump_enable_campaign_labels'] ) ? $wps_ubo_global_options['wps_bump_enable_campaign_labels'] : '';

				$funnel_name = ! empty( $value['wps_wocuf_funnel_name'] ) ? $value['wps_wocuf_funnel_name'] : sprintf( /* translators: %d: funnel id */ __( 'Funnel #%d', 'upsell-order-bump-offer-for-woocommerce' ), $key );

				$funnel_status = ! empty( $value['wps_upsell_funnel_status'] ) ? $value['wps_upsell_funnel_status'] : 'no';
				$global_funnel = ! empty( $value['wps_wocuf_global_funnel'] ) ? $value['wps_wocuf_global_funnel'] : 'no';
				$exclusive_offer = ! empty( $value['wps_wocuf_exclusive_offer'] ) ? $value['wps_wocuf_exclusive_offer'] : 'no';
				$smart_offer_upgrade = ! empty( $value['wps_wocuf_smart_offer_upgrade'] ) ? $value['wps_wocuf_smart_offer_upgrade'] : 'no';

				// Pre v3.0.0 Funnels will be live.
				$funnel_status = ! empty( $value['wps_upsell_fsav3'] ) ? $funnel_status : 'yes';

				$status_label = 'yes' === $funnel_status ? esc_html__( 'Live', 'upsell-order-bump-offer-for-woocommerce' ) : esc_html__( 'Sandbox', 'upsell-order-bump-offer-for-woocommerce' );
				?>
				<div class="wps-ubo-bump-card">
					<div class="wps-ubo-bump-card__header">
						<div class="wps-ubo-bump-card__title">
							<?php if ( 'on' === $wps_bump_enable_campaign_labels && ! empty( $label_name ) ) : ?>
								<span class="wps_label_color"
									style="background-color: <?php echo esc_attr( $color_hex ); ?>;"><?php echo esc_html( $label_name ); ?></span>
							<?php endif; ?>
							<a class="wps_upsell_funnel_list_name"
								href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&sub_tab=post-list-offer-section&funnel_id=<?php echo esc_html( $key ); ?>"><?php echo esc_html( $funnel_name ); ?></a>
						</div>
						<div class="wps-ubo-bump-card__status">
							<label class="wps_ubo_toggle_switch">
								<input type="checkbox" class="wps-ubo-funnel-toggle"
									data-funnel-id="<?php echo esc_attr( $key ); ?>" <?php checked( 'yes', $funnel_status ); ?>>
								<span class="wps_ubo_toggle_slider"></span>
							</label>
							<span
								class="wps_ubo_status_label wps-ubo-status-text"><?php echo esc_html( $status_label ); ?></span>
						</div>
					</div>

					<div class="wps-ubo-bump-card__body">
						<div class="wps-ubo-bump-block">
							<div class="wps-ubo-block-title">
								<?php esc_html_e( 'Targets', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
							<?php
							if ( ! empty( $value['wps_wocuf_target_pro_ids'] ) ) {
								echo '<div class="wps_upsell_funnel_list_targets">';
								foreach ( $value['wps_wocuf_target_pro_ids'] as $single_target_product ) :
									$product = wc_get_product( $single_target_product );
									if ( empty( $product ) ) {
										continue;
									}
									?>
									<p><?php echo esc_html( $product->get_title() ) . '( #' . esc_html( $single_target_product ) . ' )'; ?>
									</p>
									<?php
								endforeach;
								echo '</div>';
							} else {
								?>
								<p><i><?php esc_html_e( 'No product(s) added', 'upsell-order-bump-offer-for-woocommerce' ); ?></i>
								</p>
								<?php
							}

							if ( ! empty( $value['target_categories_ids'] ) ) {
								echo '<p><i>' . esc_html__( 'Target Categories -', 'upsell-order-bump-offer-for-woocommerce' ) . '</i></p>';
								echo '<div class="wps_upsell_funnel_list_targets">';
								foreach ( $value['target_categories_ids'] as $single_target_category_id ) :
									$category_name = get_the_category_by_ID( $single_target_category_id );

									if ( empty( $category_name ) ) {
										continue;
									}
									?>
									<p><?php echo esc_html( $category_name . "( #$single_target_category_id )" ); ?></p>
									<?php
								endforeach;
								echo '</div>';
							} else {
								?>
								<p><i><?php esc_html_e( 'No Category(s) added', 'upsell-order-bump-offer-for-woocommerce' ); ?></i>
								</p>
								<?php
							}
							?>
						</div>

						<div class="wps-ubo-bump-block">
							<div class="wps-ubo-block-title">
								<?php esc_html_e( 'Offers', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
							<?php
							if ( ! empty( $value['wps_wocuf_products_in_offer'] ) ) {
								echo '<div class="wps_upsell_funnel_list_targets">';
								echo '<p><i>' . esc_html__( 'Offers Count', 'upsell-order-bump-offer-for-woocommerce' ) . ' - ' . esc_html( $offers_count ) . '</i></p>';

								foreach ( $value['wps_wocuf_products_in_offer'] as $offer_key => $single_offer_product ) :
									$product = wc_get_product( $single_offer_product );

									if ( empty( $product ) ) {
										continue;
									}
									?>
									<p><?php echo '<strong>' . esc_html__( 'Offer', 'upsell-order-bump-offer-for-woocommerce' ) . ' #' . esc_html( $offer_key ) . '</strong> &rarr; ' . esc_html( $product->get_title() ) . '( #' . esc_html( $single_offer_product ) . ' )'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</p>
									<?php
								endforeach;
								echo '</div>';
							} else {
								?>
								<p><i><?php esc_html_e( 'No Offers added', 'upsell-order-bump-offer-for-woocommerce' ); ?></i></p>
								<?php
							}
							?>
						</div>

						<?php if ( 'yes' === $global_funnel || 'yes' === $exclusive_offer || 'yes' === $smart_offer_upgrade ) : ?>
							<div class="wps-ubo-bump-block">
								<div class="wps-ubo-block-title">
									<?php esc_html_e( 'Funnel Attributes', 'upsell-order-bump-offer-for-woocommerce' ); ?></div>
								<div class="wps-upsell-funnel-attributes <?php echo esc_attr( $funnel_status ); ?>">
									<?php
									if ( 'yes' === $global_funnel ) {
										echo '<p>' . esc_html__( 'Global Funnel', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>';
									}

									if ( 'yes' === $exclusive_offer ) {
										echo '<p>' . esc_html__( 'Exclusive Offer', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>';
									}

									if ( 'yes' === $smart_offer_upgrade ) {
										echo '<p>' . esc_html__( 'Smart Offer Upgrade', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>';
									}
									?>
								</div>
							</div>
						<?php endif; ?>

						<?php
						ob_start();
						do_action( 'wps_wocuf_pro_funnel_add_more_col_data' );
						$wps_funnel_extra = ob_get_clean();

						if ( ! empty( trim( (string) $wps_funnel_extra ) ) ) :
							?>
							<div class="wps-ubo-bump-block wps-ubo-funnel-extra">
								<?php echo $wps_funnel_extra; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						<?php endif; ?>

						<div class="wps-ubo-bump-block wps-ubo-bump-actions">
							<a class="wps_wocuf_pro_funnel_links wps_upsell_bump_links"
								href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting-post&sub_tab=post-list-offer-section&funnel_id=<?php echo esc_html( $key ); ?>&manage_nonce=<?php echo esc_html( wp_create_nonce( 'manage_funnel' ) ); ?>"><?php esc_html_e( 'View / Edit', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

							<a class="wps_wocuf_pro_funnel_links wps_upsell_bump_links"
								href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=one-click-section&sub_tab=post-list-offer-section&del_funnel_id=<?php echo esc_html( $key ); ?>&del_nonce=<?php echo esc_html( wp_create_nonce( 'del_funnel' ) ); ?>"><?php esc_html_e( 'Delete', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

							<a class="wps_wocuf_pro_funnel_links wps_upsell_bump_links" href="?page=upsell-order-bump-offer-for-woocommerce-post-reporting"><svg width="24" height="24"
									viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path
										d="M19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3Z"
										stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
									<path d="M10 7H7V16H10V7Z" stroke="white" stroke-width="2" stroke-linecap="round"
										stroke-linejoin="round" />
									<path d="M17 7H14V12H17V7Z" stroke="white" stroke-width="2" stroke-linecap="round"
										stroke-linejoin="round" />
								</svg>
							</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<?php
		if ( $wps_total_pages > 1 ) {
			wps_ubo_render_admin_pagination( $wps_current_page, $wps_total_pages, $wps_funnel_base_url, 'wps_funnel_page' );
		}
		?>
	<?php endif; ?>
</div>

<br>

<!-- Create New Funnel -->
<div class="wps_wocuf_pro_create_new_funnel">
	<?php
	$installed_plugins = (array) get_option( 'active_plugins', array() );
	$wps_wocuf_is_builder_active = wps_upsell_lite_is_plugin_active_funnel_builder( 'woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php' );
	$wps_wocuf_create_funnel_id = $wps_wocuf_pro_funnel_number + 1;
	$wps_wocuf_manage_nonce = wp_create_nonce( 'manage_funnel' );
	$wps_wocuf_create_link = add_query_arg(
		array(
			'page' => 'upsell-order-bump-offer-for-woocommerce-setting',
			'tab' => 'creation-setting-post',
			'sub_tab' => 'post-list-offer-section',
			'funnel_id' => $wps_wocuf_create_funnel_id,
			'manage_nonce' => $wps_wocuf_manage_nonce,
		),
		admin_url( 'admin.php' )
	);

	$wps_wocuf_modal_locked = false;
	if ( ! $wps_wocuf_is_builder_active && 1 == $wps_wocuf_pro_funnel_number ) {
		$wps_wocuf_modal_locked = true;
	}
	?>
		<button type="button"
			id="wps-wocuf-open-funnel-template-modal"><?php esc_html_e( '+Create New Funnel', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
</div>

<?php
$wps_wocuf_template_cards = array(
	array(
		'slug' => 'funnel-default',
		'title' => __( 'Default Funnel', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Start with a clean, single-offer funnel layout.', 'upsell-order-bump-offer-for-woocommerce' ),
		'icon' => 'dashicons-randomize',
		'is_pro' => false,
	),
	array(
		'slug' => 'funnel-conditional-display',
		'title' => __( 'Funnel Conditional Display', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Show funnels only when shopper and cart rules are met.', 'upsell-order-bump-offer-for-woocommerce' ),
		'icon' => 'dashicons-visibility',
		'is_pro' => false,
	),
	array(
		'slug' => 'smart-offer',
		'title' => __( 'Smart Offer Upgrade', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Optimized flow focused on quick upsell acceptance.', 'upsell-order-bump-offer-for-woocommerce' ),
		'is_pro' => false,
		'icon' => 'dashicons-chart-line',
	),
	array(
		'slug' => 'exclusive-urgency',
		'title' => __( 'Exclusive + Urgency', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Highlights exclusivity and urgency cues.', 'upsell-order-bump-offer-for-woocommerce' ),
		'is_pro' => false,
		'icon' => 'dashicons-lock',
	),
	array(
		'slug' => 'global-funnel',
		'title' => __( 'Global Funnel', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Applies a universal funnel experience across products.', 'upsell-order-bump-offer-for-woocommerce' ),
		'icon' => 'dashicons-admin-site',
		'is_pro' => false,
	),
	array(
		'slug' => 'show-form-fields',
		'title' => __( 'Show Form Fields', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Collect key customer details within the funnel flow.', 'upsell-order-bump-offer-for-woocommerce' ),
		'icon' => 'dashicons-feedback',
		'is_pro' => true,
	),
	array(
		'slug' => 'frequently-bought-offers',
		'title' => __( 'Frequently Bought Offers', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Surface complementary products often purchased together.', 'upsell-order-bump-offer-for-woocommerce' ),
		'icon' => 'dashicons-products',
		'is_pro' => true,
	),
	array(
		'slug' => 'ab-testing',
		'title' => __( 'AB Testing', 'upsell-order-bump-offer-for-woocommerce' ),
		'description' => __( 'Experiment with funnel variants to boost conversions.', 'upsell-order-bump-offer-for-woocommerce' ),
		'icon' => 'dashicons-chart-pie',
		'is_pro' => true,
	),
);
?>

<div class="wps-ubo-template-modal" id="wps-wocuf-funnel-template-modal" aria-hidden="true">
	<div class="wps-ubo-template-overlay" id="wps-wocuf-funnel-template-overlay"></div>
	<div class="wps-ubo-template-dialog" role="dialog" aria-modal="true">
		<div class="wps-ubo-template-dialog__head">
			<h3><?php esc_html_e( 'Choose a Campaign Layout', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
			<?php if ( ! $wps_wocuf_is_builder_active ) : ?>
				<p class="wps-ubo-template-note">
					<?php
printf(
	wp_kses(
		/* translators: 1: Opening link tag, 2: closing link tag. */
		__( '<em>Multiple funnels are available in the Pro version. %1$sPurchase Pro%2$s to create additional funnels.</em>', 'upsell-order-bump-offer-for-woocommerce' ),
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
			<button type="button" class="wps-ubo-template-close" id="wps-wocuf-funnel-template-close"
				aria-label="<?php esc_attr_e( 'Close', 'upsell-order-bump-offer-for-woocommerce' ); ?>">×</button>
		</div>
		<div class="wps-ubo-template-grid">
			<?php foreach ( $wps_wocuf_template_cards as $card ) : ?>
				<?php
				$card_is_pro = ! empty( $card['is_pro'] );
				$card_is_lock = $card_is_pro && ! $wps_wocuf_is_builder_active;
				$card_icon = ! empty( $card['icon'] ) ? $card['icon'] : 'dashicons-layout';
				$button_label = $card_is_lock ? __( 'Unlock with Pro', 'upsell-order-bump-offer-for-woocommerce' ) : __( 'Create Campaign', 'upsell-order-bump-offer-for-woocommerce' );
				?>
				<div class="wps-ubo-template-card <?php echo $card_is_lock ? 'is-locked' : 'is-open'; ?>"
					data-template="<?php echo esc_attr( $card['slug'] ); ?>">
					<div class="wps-ubo-template-card__head">
						<div class="wps-ubo-template-card__title">
							<span class="dashicons <?php echo esc_attr( $card_icon ); ?> wps-ubo-template-icon"
								aria-hidden="true"></span>
							<div class="wps-ubo-template-card__text">
								<h4><?php echo esc_html( $card['title'] ); ?></h4>
								<p><?php echo esc_html( $card['description'] ); ?></p>
							</div>
						</div>
						<?php if ( $card_is_pro && ! wps_ubo_lite_if_pro_exists()) : ?>
							<span class="wps-ubo-template-badge <?php echo $card_is_lock ? 'is-locked' : 'is-pro'; ?>">
								<?php echo esc_html__( 'Pro', 'upsell-order-bump-offer-for-woocommerce' ); ?>
							</span>
						<?php endif; ?>
					</div>
					<?php if ( $card_is_lock ) : ?>
						<a href="https://wpswings.com/product/upsell-order-bump-offer-for-woocommerce-pro/?utm_source=order-bump-org&utm_medium=referral&utm_campaign=order-bump-pro"
							class="button button-primary wps-ubo-template-choose is-locked" target="_blank"
							rel="noopener noreferrer">
							<?php echo esc_html( $button_label ); ?>
						</a>
					<?php else : ?>
						<a href="#" class="button button-primary wps-ubo-template-choose"
							data-template="<?php echo esc_attr( $card['slug'] ); ?>">
							<?php echo esc_html( $button_label ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<script>
	(function ($) {
		const modal = $('#wps-wocuf-funnel-template-modal');
		const overlay = $('#wps-wocuf-funnel-template-overlay');
		const openBtn = $('#wps-wocuf-open-funnel-template-modal');
		const closeBtn = $('#wps-wocuf-funnel-template-close');
		const createUrl = '<?php echo '?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting-post&sub_tab=post-list-offer-section&funnel_id=1'; ?>';

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

		$(document).on('keyup', function (e) {
			if (27 === e.keyCode) {
				closeModal();
			}
		});

		modal.on('click', '.wps-ubo-template-choose', function (e) {
			if ($(this).hasClass('is-locked')) {
				// For locked cards (Pro upsell), allow the normal link to open in a new tab.
				return;
			}
			e.preventDefault();
			const template = $(this).data('template') || 'funnel-default';
			window.location.href = createUrl + '&template=' + encodeURIComponent(template);
		});
	})(jQuery);
</script>

<?php do_action( 'wps_wocuf_pro_extend_funnels_listing' ); ?>
<?php wps_upsee_lite_go_pro_funnel_builder( 'pro' ); ?>

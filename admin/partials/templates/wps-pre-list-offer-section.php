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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bumps Listing Template.
 *
 * This template is used for listing all existing bumps with
 * view/edit and delete option.
 */

$secure_nonce      = wp_create_nonce( 'wps-upsell-auth-nonce' );
$id_nonce_verified = wp_verify_nonce( $secure_nonce, 'wps-upsell-auth-nonce' );

if ( ! $id_nonce_verified ) {
	wp_die( esc_html__( 'Nonce Not verified', 'upsell-order-bump-offer-for-woocommerce' ) );
}

// Delete bumps.
if ( isset( $_GET['del_bump_id'] ) ) {

	$bump_id = sanitize_text_field( wp_unslash( $_GET['del_bump_id'] ) );

	// Get all bumps.
	$wps_upsell_bumps = get_option( 'wps_ubo_bump_list' );

	foreach ( $wps_upsell_bumps as $single_bump => $data ) {

		if ( (string) $bump_id === (string) $single_bump ) {

			unset( $wps_upsell_bumps[ $single_bump ] );
			break;
		}
	}

	update_option( 'wps_ubo_bump_list', $wps_upsell_bumps );
	
	// Delete associated discount rules.
	$wc_dynamic_discount_rules = get_option( 'wc_dynamic_discount_rules', array() );
	
	$funnel_type = 'wps_bump_one';
	if ( ! empty( $funnel_type ) ) {
		
		if ( isset( $wc_dynamic_discount_rules[$funnel_type][$bump_id] ) ) {
			unset( $wc_dynamic_discount_rules[$funnel_type][$bump_id] );
		}

		if ( empty( $wc_dynamic_discount_rules[$funnel_type] ) ) {
			unset( $wc_dynamic_discount_rules[$funnel_type] );
		}
	} else {
		foreach ( $wc_dynamic_discount_rules as $funnel_key => $bumps ) {
			
			if ( isset( $wc_dynamic_discount_rules[$funnel_key][$bump_id] ) ) {
				unset( $wc_dynamic_discount_rules[$funnel_key][$bump_id] );
			}

			if ( empty( $wc_dynamic_discount_rules[$funnel_key] ) ) {
				unset( $wc_dynamic_discount_rules[$funnel_key] );
			}
		}
	}

	update_option( 'wc_dynamic_discount_rules', $wc_dynamic_discount_rules );

wp_safe_redirect( esc_url_raw( admin_url( 'admin.php?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-list-offer-section' ) ) );

	exit();
}

// Get all bumps.
$wps_upsell_bumps_list = get_option( 'wps_ubo_bump_list' );

?>

<div class="wps_upsell_bumps_list">

	<?php if ( empty( $wps_upsell_bumps_list ) ) : ?>

		<p class="wps_upsell_bump_no_bump"><?php esc_html_e( 'No Order Bumps added', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>

	<?php endif; ?>

	<?php if ( ! empty( $wps_upsell_bumps_list ) ) : ?>
		<?php if ( ! wps_ubo_lite_if_pro_exists() && count( $wps_upsell_bumps_list ) > 1 ) : ?>

			<div class="notice notice-warning wps-notice">
				<p>
					<strong><?php esc_html_e( 'Only first Order Bump will work. Please activate pro version to make all working.', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong>
				</p>
			</div>

		<?php endif; ?>
		<table>
			<tr>
				<th><?php esc_html_e( 'Name', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Status', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
				<th id="wps_upsell_bump_list_target_th"><?php esc_html_e( 'Target Product(s) and Categories', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Offers', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Action', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
			</tr>

			<!-- Foreach Bump start. -->
			<?php foreach ( $wps_upsell_bumps_list as $key => $value ) : ?>
				<tr>
					<!-- Bump Name. -->
					<td>
						<a class="wps_upsell_bump_list_name" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&bump_id=<?php echo esc_html( $key ); ?>"><?php echo esc_html( $value['wps_upsell_bump_name'] ); ?></a>
						<p><i><?php esc_html_e( 'Priority : ', 'upsell-order-bump-offer-for-woocommerce' ); ?><span class="wps-bump-priority"><?php echo esc_html( ! empty( $value['wps_upsell_bump_priority'] ) ? $value['wps_upsell_bump_priority'] : 'No Priority' ); ?></span></i></p>
					</td>
					</td>

					<!-- Bump Status. -->
					<td>
						<?php

						$bump_status = ! empty( $value['wps_upsell_bump_status'] ) ? $value['wps_upsell_bump_status'] : 'no';
						$checked     = 'yes' === $bump_status ? 'checked' : '';
						?>
						<label class="wps_ubo_bump_toggle_wrap">
							<input type="checkbox" class="wps_ubo_bump_status_toggle" data-bump-id="<?php echo esc_attr( $key ); ?>" <?php echo esc_html( $checked ); ?> />
							<span class="wps_ubo_bump_toggle_slider" aria-hidden="true"></span>
						</label>
						<span class="wps_ubo_bump_status_text" data-bump-id="<?php echo esc_attr( $key ); ?>">
							<?php echo 'yes' === $bump_status ? esc_html__( 'Live', 'upsell-order-bump-offer-for-woocommerce' ) : esc_html__( 'Sandbox', 'upsell-order-bump-offer-for-woocommerce' ); ?>
						</span>

					</td>

					<!-- Bump Target products. -->
					<td>
						<?php

						// Target Product(s).
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

						echo '<hr>';

						// Target Categories.

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
					</td>

					<!-- Bump Offer Product. -->
					<td>
						<p>
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
					</p>
					</td>

					<!-- Bump Action. -->
					<td>
						<!-- Bump View/Edit link. -->
						<a class="wps_upsell_bump_links" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&bump_id=<?php echo esc_html( $key ); ?>"><?php esc_html_e( 'View / Edit', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

						<!-- Bump Delete link. -->
						<a class="wps_upsell_bump_links" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-list-offer-section&del_bump_id=<?php echo esc_html( $key ); ?>"><?php esc_html_e( 'Delete', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
					</td>
					<?php do_action( 'wps_ubo_add_more_col_data' ); ?>
				</tr>
			<?php endforeach; ?>
			<!-- Foreach Bump end. -->
		</table>
	<?php endif; ?>
</div>

<!-- Add section to trigger Go Pro popup. -->
<?php if ( ! empty( $wps_upsell_bumps_list ) && count( $wps_upsell_bumps_list ) ) : ?>

	<input type="hidden" class="wps_ubo_lite_saved_funnel" value="<?php echo ( count( $wps_upsell_bumps_list ) ); ?>">

<?php endif; ?>

<!-- Create New Bump. -->
<?php $installed_plugins = get_plugins(); ?>
<!-- Create New Bump. -->
<div class="wps_upsell_bump_create_new_bump">
	<a class="wps_ubo_lite_bump_create_button" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&bump_id=1"><?php esc_html_e( '+Create New Order Bump', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
</div>

<!-- Add Go pro popup. -->
<?php wps_ubo_go_pro( 'list' ); ?>

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

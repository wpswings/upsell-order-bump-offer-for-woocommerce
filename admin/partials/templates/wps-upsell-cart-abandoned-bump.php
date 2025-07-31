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
	$wps_deleted_abandoned_cart = (array) get_option( 'wps_ubo_deleted_abandoned_cart', array() );

	foreach ( $wps_upsell_bumps as $single_bump => $data ) {

		if (
		isset( $wps_upsell_bumps[ $single_bump ]['wps_abandoned_session_id'] ) &&
		isset( $wps_upsell_bumps[ $single_bump ]['wps_abandoned_checkout_useremail'] ) &&
		isset( $wps_upsell_bumps[ $single_bump ]['wps_upsell_bump_products_in_offer'] )
		) {
			$wps_deleted_abandoned_cart[] =
			$wps_upsell_bumps[ $single_bump ]['wps_abandoned_session_id']
			. $wps_upsell_bumps[ $single_bump ]['wps_abandoned_checkout_useremail']
			. $wps_upsell_bumps[ $single_bump ]['wps_upsell_bump_products_in_offer'];


		}

		if ( (string) $bump_id === (string) $single_bump ) {
			// $data = maybe_unserialize( $data );
			unset( $wps_upsell_bumps[ $single_bump ] );
			break;
		}
	}

	update_option( 'wps_ubo_deleted_abandoned_cart', $wps_deleted_abandoned_cart );
	update_option( 'wps_ubo_bump_list', $wps_upsell_bumps );

	wp_redirect( esc_url_raw( admin_url( 'admin.php?page=upsell-order-bump-offer-for-woocommerce-abandoned-cart-reporting' ) ) );

	exit();
}
?>
<div class="wrap woocommerce" id="wps_upsell_bump_setting_wrapper">
	<div class="wps_upsell_bump_setting_title"><?php echo esc_html( apply_filters( 'wps_ubo_lite_heading', esc_html__( 'Upsell Funnel Builder for WooCommerce ', 'upsell-order-bump-offer-for-woocommerce' ) ) ); ?>
	</div>

	<nav class="nav-tab-wrapper woo-nav-tab-wrapper">

		<a class="nav-tab nav-tab-active" href="#"><?php esc_html_e( 'Abandoned Cart Bump', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

	</nav>

	<!-- For notification control. -->
	<h1></h1>
	<?php


	// Get all bumps.
	$wps_upsell_bumps_list = get_option( 'wps_ubo_bump_list' );
	$last_key = 0;
	if ( ! empty( $wps_upsell_bumps_list ) ) {
		$keys = array_keys( $wps_upsell_bumps_list );
		$int_keys = array_filter( $keys, 'is_numeric' );
		if ( ! empty( $int_keys ) ) {
			$last_key = max( $int_keys ); // Last numeric key.
		}
	}

	$wps_count_for_ab = 0;
	if ( is_array( $wps_upsell_bumps_list ) && ! empty( $wps_upsell_bumps_list ) ) {
		foreach ( $wps_upsell_bumps_list as $key => $value ) {
			if ( is_array( $value ) && ! empty( $value ) && isset( $value['wps_display_method'] ) && ! empty( $value['wps_display_method'] ) ) {
				if ( isset( $value['wps_display_method'] ) && ! empty( $value['wps_display_method'] ) && 'ab_method' == $value['wps_display_method'] ) {
					$wps_count_for_ab++;
				}
			}
		}
	}


	if ( ! empty( $wps_upsell_bumps_list ) ) {

		// Temp bump variable.
		$wps_upsell_bumps_list_duplicate = $wps_upsell_bumps_list;

		// Make key pointer point to the end bump.
		end( $wps_upsell_bumps_list_duplicate );

		// Now key function will return last bump key.
		$wps_upsell_bumps_last_index = key( $wps_upsell_bumps_list_duplicate );
	} else {

		// When no bump is there then new bump id will be 1 (0+1).
		$wps_upsell_bumps_last_index = 0;
	}

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
				<?php
					// Skip if key 'wps_is_abandoned_bump' exists in serialized cart data.
				if ( isset( $value['wps_is_abandoned_bump'] ) && 'yes' != $value['wps_is_abandoned_bump'] ) {

						continue; // skip this row.
				}
				?>

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

						if ( 'yes' === $bump_status ) {

							echo '<span class="wps_upsell_bump_list_live"></span><span class="wps_upsell_bump_list_live_name">' . esc_html__( 'Live', 'upsell-order-bump-offer-for-woocommerce' ) . '</span>';
						} else {

							echo '<span class="wps_upsell_bump_list_sandbox"></span><span class="wps_upsell_bump_list_sandbox_name">' . esc_html__( 'Sandbox', 'upsell-order-bump-offer-for-woocommerce' ) . '</span>';
						}

						?>
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
						<a class="wps_upsell_bump_links" href="?page=upsell-order-bump-offer-for-woocommerce-abandoned-cart-reporting&del_bump_id=<?php echo esc_html( $key ); ?>"><?php esc_html_e( 'Delete', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

						<?php if ( wps_ubo_lite_is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) ) { ?>
							<!--Below will work for pro only -->
							<a class="wps_upsell_bump_links" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=order-bump-section&sub_tab=pre-list-offer-section&clone_bump_id=<?php echo esc_html( $key ); ?>"><?php esc_html_e( 'Clone', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
						<?php } ?>
					</td>

					<?php do_action( 'wps_ubo_add_more_col_data' ); ?>
				</tr>
			<?php endforeach; ?>
			<!-- Foreach Bump end. -->
		</table>
	<?php endif; ?>
</div>
</div>

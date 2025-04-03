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

		wp_redirect( esc_url_raw( admin_url( 'admin.php?page=upsell-order-bump-offer-for-woocommerce-setting&tab=one-click-section&sub_tab=post-list-offer-section' ) ) );
		exit;
	}
}

// Get all funnels.
$wps_wocuf_pro_funnels_list = get_option( 'wps_wocuf_funnels_list', array() );

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

<div class="wps_wocuf_pro_funnels_list">

	<?php if ( empty( $wps_wocuf_pro_funnels_list ) ) : ?>

		<p class="wps_wocuf_pro_no_funnel"><?php esc_html_e( 'No funnels added', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>

	<?php endif; ?>

	<?php if ( ! empty( $wps_wocuf_pro_funnels_list ) ) : ?>
		<table>
			<tr>
				<th><?php esc_html_e( 'Funnel Name', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Status', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
				<th id="wps_upsell_funnel_list_target_th"><?php esc_html_e( 'Target Product(s) and Categories', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Offers', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
				<th><?php esc_html_e( 'Action', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
				<?php do_action( 'wps_wocuf_pro_funnel_add_more_col_head' ); ?>
			</tr>

			<!-- Foreach Funnel start -->
			<?php
			foreach ( $wps_wocuf_pro_funnels_list as $key => $value ) :

				$offers_count = ! empty( $value['wps_wocuf_products_in_offer'] ) ? $value['wps_wocuf_products_in_offer'] : array();

				$offers_count = count( $offers_count );

				?>

				<tr>
					<!-- Funnel Name -->
					<td><a class="wps_upsell_funnel_list_name" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&sub_tab=post-list-offer-section&funnel_id=<?php echo esc_html( $key ); ?>"><?php echo esc_html( $value['wps_wocuf_funnel_name'] ); ?></a></td>

					<!-- Funnel Status -->
					<td>

						<?php

						$funnel_status       = ! empty( $value['wps_upsell_funnel_status'] ) ? $value['wps_upsell_funnel_status'] : 'no';
						$global_funnel       = ! empty( $value['wps_wocuf_global_funnel'] ) ? $value['wps_wocuf_global_funnel'] : 'no';
						$exclusive_offer     = ! empty( $value['wps_wocuf_exclusive_offer'] ) ? $value['wps_wocuf_exclusive_offer'] : 'no';
						$smart_offer_upgrade = ! empty( $value['wps_wocuf_smart_offer_upgrade'] ) ? $value['wps_wocuf_smart_offer_upgrade'] : 'no';

						// Pre v3.0.0 Funnels will be live.
						$funnel_status = ! empty( $value['wps_upsell_fsav3'] ) ? $funnel_status : 'yes';

						if ( 'yes' === $funnel_status ) {

							echo '<span class="wps_upsell_funnel_list_live"></span><span class="wps_upsell_funnel_list_live_name">' . esc_html__( 'Live', 'upsell-order-bump-offer-for-woocommerce' ) . '</span>';
						} else {

							echo '<span class="wps_upsell_funnel_list_sandbox"></span><span class="wps_upsell_funnel_list_sandbox_name">' . esc_html__( 'Sandbox', 'upsell-order-bump-offer-for-woocommerce' ) . '</span>';
						}

						echo "<div class='wps-upsell-funnel-attributes " . esc_html( $funnel_status ) . "'>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						if ( 'yes' === $global_funnel ) {

							echo '<p>' . esc_html__( 'Global Funnel', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>';
						}

						if ( 'yes' === $exclusive_offer ) {

							echo '<p>' . esc_html__( 'Exclusive Offer', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>';
						}

						if ( 'yes' === $smart_offer_upgrade ) {

							echo '<p>' . esc_html__( 'Smart Offer Upgrade', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>';
						}

						echo '</div>';

						?>
					</td>

					<!-- Funnel Target products. -->
					<td>
						<?php

						if ( ! empty( $value['wps_wocuf_target_pro_ids'] ) ) {

							echo '<div class="wps_upsell_funnel_list_targets">';

							foreach ( $value['wps_wocuf_target_pro_ids'] as $single_target_product ) :

								$product = wc_get_product( $single_target_product );
								?>
								<p><?php echo esc_html( $product->get_title() ) . '( #' . esc_html( $single_target_product ) . ' )'; ?></p>
								<?php

							endforeach;

							echo '</div>';
						} else {

							?>

							<p><?php esc_html_e( 'No product(s) added', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>

							<?php
						}

						echo '<hr>';
						// Target Categories.

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

					<p><i><?php esc_html_e( 'No Category(s) added', 'upsell-order-bump-offer-for-woocommerce' ); ?></i></p>

							<?php
						}

						?>
					</td>

					<!-- Offers -->
					<td>
						<?php

						if ( ! empty( $value['wps_wocuf_products_in_offer'] ) ) {

							echo '<div class="wps_upsell_funnel_list_targets">';

							echo '<p><i>' . esc_html__( 'Offers Count', 'upsell-order-bump-offer-for-woocommerce' ) . ' - ' . esc_html( $offers_count ) . '</i></p>';

							foreach ( $value['wps_wocuf_products_in_offer'] as $offer_key => $single_offer_product ) :

								$product = wc_get_product( $single_offer_product );

								if ( empty( $product ) ) {

									continue;
								}
								// phpcs:disable
								?>
								<p><?php echo '<strong>' . esc_html__( 'Offer', 'upsell-order-bump-offer-for-woocommerce' ) . ' #' . esc_html( $offer_key ) . '</strong> &rarr; ' . esc_html( $product->get_title() ) . '( #' . esc_html( $single_offer_product ) . ' )'; ?></p>
								<?php
								// phpcs:enable

							endforeach;

							echo '</div>';
						} else {

							?>
							<p><i><?php esc_html_e( 'No Offers added', 'upsell-order-bump-offer-for-woocommerce' ); ?></i></p>
							<?php
						}

						?>
					</td>

					<!-- Funnel Action -->
					<td>

						<!-- Funnel View/Edit link -->
						<a class="wps_wocuf_pro_funnel_links" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting-post&sub_tab=post-list-offer-section&funnel_id=<?php echo esc_html( $key ); ?>&manage_nonce=<?php echo esc_html( wp_create_nonce( 'manage_funnel' ) ); ?>"><?php esc_html_e( 'View / Edit', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

						<!-- Funnel Delete link -->
						<a class="wps_wocuf_pro_funnel_links" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=one-click-section&sub_tab=post-list-offer-section&del_funnel_id=<?php echo esc_html( $key ); ?>&del_nonce=<?php echo esc_html( wp_create_nonce( 'del_funnel' ) ); ?>"><?php esc_html_e( 'Delete', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
					</td>

					<?php do_action( 'wps_wocuf_pro_funnel_add_more_col_data' ); ?>
				</tr>
			<?php endforeach; ?>
			<!-- Foreach Funnel end -->
		</table>
	<?php endif; ?>
</div>

<br>

<!-- Create New Funnel -->
<div class="wps_wocuf_pro_create_new_funnel">

	<?php
	$installed_plugins = (array) get_option( 'active_plugins', array() );

	if ( wps_upsell_lite_is_plugin_active_funnel_builder( 'woocommerce-one-click-upsell-funnel-pro/woocommerce-one-click-upsell-funnel-pro.php' ) ) {    // Need to replace this function.
		?>
		<a href="?page=upsell-order-bump-offer-for-woocommerce-setting&manage_nonce=<?php echo esc_html( wp_create_nonce( 'manage_funnel' ) ); ?>&tab=creation-setting-post&sub_tab=post-list-offer-section&funnel_id=<?php echo esc_html( $wps_wocuf_pro_funnel_number + 1 ); ?>"><?php esc_html_e( '+Create New Funnel', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>

		<?php

	} elseif ( 1 == $wps_wocuf_pro_funnel_number ) {
		?>
		<span class="ubo_offer_input_pro">
			<a class="ubo_offer_input_pro"><?php esc_html_e( '+Create New Funnel', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
		</span>
		<?php
	} else {
		?>
		<a href="?page=upsell-order-bump-offer-for-woocommerce-setting&manage_nonce=<?php echo esc_html( wp_create_nonce( 'manage_funnel' ) ); ?>&tab=creation-setting-post&sub_tab=post-list-offer-section&funnel_id=1"><?php esc_html_e( '+Create New Funnel', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
		<?php

	}
	?>
</div>

<?php do_action( 'wps_wocuf_pro_extend_funnels_listing' ); ?>
<?php wps_upsee_lite_go_pro_funnel_builder( 'pro' ); ?>
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

	wp_safe_redirect( admin_url( 'admin.php' ) . '?page=upsell-order-bump-offer-for-woocommerce-setting&tab=bump-list' );

	exit();
}

// Get all bumps.
$wps_upsell_bumps_list = get_option( 'wps_ubo_bump_list' );

?>

<div class="wps_upsell_bumps_list" >

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
					<p><i><?php esc_html_e( 'Priority : ', 'upsell-order-bump-offer-for-woocommerce-pro' ); ?><span class="wps-bump-priority"><?php echo esc_html( ! empty( $value['wps_upsell_bump_priority'] ) ? $value['wps_upsell_bump_priority'] : 'No Priority' ); ?></span></i></p></td>
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
					<a class="wps_upsell_bump_links" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=bump-list&del_bump_id=<?php echo esc_html( $key ); ?>"><?php esc_html_e( 'Delete', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
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

	<input type="hidden" class="wps_ubo_lite_saved_funnel" value="<?php echo( count( $wps_upsell_bumps_list ) ); ?>">

<?php endif; ?>

<!-- Create New Bump. -->
<?php $installed_plugins = get_plugins(); ?>
<!-- Create New Bump. -->
<div class="wps_upsell_bump_create_new_bump">
	<a class="wps_ubo_lite_bump_create_button" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&bump_id=1"><?php esc_html_e( '+Create New Order Bump', 'upsell-order-bump-offer-for-woocommerce-pro' ); ?></a>
</div>

<!-- Add Go pro popup. -->
<?php wps_ubo_go_pro( 'list' ); ?>

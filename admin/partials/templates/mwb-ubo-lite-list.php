<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to list all bump offers.
 *
 * @link       https://makewebbetter.com/
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
 * 
 */

// Delete bumps.
if( isset( $_GET['del_bump_id'] ) ) {

	$bump_id = sanitize_text_field( wp_unslash( $_GET['del_bump_id'] ) );

	// Get all bumps.
	$mwb_upsell_bumps = get_option( 'mwb_ubo_bump_list' );

	foreach( $mwb_upsell_bumps as $single_bump => $data ) {

		if( $bump_id == $single_bump ) {

			unset( $mwb_upsell_bumps[ $single_bump ] );
			break;
		}
	}

	update_option( 'mwb_ubo_bump_list', $mwb_upsell_bumps );

	wp_redirect( admin_url('admin.php') . '?page=upsell-order-bump-offer-for-woocommerce-setting&tab=bump-list' );

	exit();
}

// Get all bumps.
$mwb_upsell_bumps_list = get_option( "mwb_ubo_bump_list" );

if( ! empty( $mwb_upsell_bumps_list ) ) {

	// Temp bump variable.
	$mwb_upsell_bumps_list_duplicate = $mwb_upsell_bumps_list;

	// Make key pointer point to the end bump.
	end( $mwb_upsell_bumps_list_duplicate );

	// Now key function will return last bump key.
	$mwb_upsell_bumps_last_index = key( $mwb_upsell_bumps_list_duplicate );
}

// When no bump is there then new bump id will be 1 (0+1).
else {

	$mwb_upsell_bumps_last_index = 0;
}

?>

<div class="mwb_upsell_bumps_list" >

	<?php if( empty( $mwb_upsell_bumps_list ) ):?>

		<p class="mwb_upsell_bump_no_bump"><?php esc_html_e( 'No Order Bumps added', 'upsell-order-bump-offer-for-woocommerce');?></p>

	<?php endif; ?>

	<?php if( ! empty( $mwb_upsell_bumps_list ) ):?>
	<?php if( ! is_plugin_active( 'upsell-order-bump-offer-for-woocommerce-pro/upsell-order-bump-offer-for-woocommerce-pro.php' ) && count( $mwb_upsell_bumps_list ) > 1 ) : ?>

		<div class="notice notice-warning">
		    <p>
		    	<strong><?php esc_html_e( 'Only first Order Bump will work. Please activate pro version to make all working.', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong>
		    </p>
		</div>

	<?php ?>

	<?php endif; ?>
		<table>
			<tr>
				<th><?php esc_html_e( 'Name', 'upsell-order-bump-offer-for-woocommerce' );?></th>
				<th><?php esc_html_e( 'Status', 'upsell-order-bump-offer-for-woocommerce' );?></th>
				<th id="mwb_upsell_bump_list_target_th"><?php esc_html_e( 'Target Product(s) and Categories', 'upsell-order-bump-offer-for-woocommerce' );?></th>
				<th><?php esc_html_e( 'Offers', 'upsell-order-bump-offer-for-woocommerce' );?></th>
				<th><?php esc_html_e( 'Action', 'upsell-order-bump-offer-for-woocommerce' );?></th>
			</tr>

			<!-- Foreach Bump start. -->
			<?php foreach ( $mwb_upsell_bumps_list as $key => $value ):

				$offer_present = ! empty( $value['mwb_upsell_bump_products_in_offer'] ) ? $value['mwb_upsell_bump_products_in_offer'] : '';
				
				$offers = wc_get_product( $offer_present );

			?>
			<tr>		
				<!-- Bump Name. -->
				<td><a class="mwb_upsell_bump_list_name" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&bump_id=<?php esc_html_e( $key ); ?>"><?php esc_html_e( $value["mwb_upsell_bump_name"] ); ?></a></td>

				<!-- Bump Status. -->
				<td>
					<?php 

					$bump_status = ! empty( $value["mwb_upsell_bump_status"] ) ? $value["mwb_upsell_bump_status"] : 'no';

					if( 'yes' == $bump_status ) {

						echo '<span class="mwb_upsell_bump_list_live"></span><span class="mwb_upsell_bump_list_live_name">' . esc_html__( 'Live', 'upsell-order-bump-offer-for-woocommerce' ) . '</span>';
					}

					else {

						echo '<span class="mwb_upsell_bump_list_sandbox"></span><span class="mwb_upsell_bump_list_sandbox_name">' . esc_html__( 'Sandbox', 'upsell-order-bump-offer-for-woocommerce' ) . '</span>';
					}

					?>
				
				</td>

				<!-- Bump Target products. -->
				<td>

					<?php 

					// Target Product(s).

					if( ! empty( $value['mwb_upsell_bump_target_ids'] ) ) { 

						echo '<div class="mwb_upsell_bump_list_targets">';

						foreach( $value['mwb_upsell_bump_target_ids'] as $single_target_product ):

							$product = wc_get_product( $single_target_product );

							if( empty( $product ) ) {

								continue;
							}
						?>
							<p><?php esc_html_e( $product->get_title() . "( #$single_target_product )" );?></p>
						<?php

						endforeach;

						echo '</div>';
					}

					else {

						?>

						<p><i><?php esc_html_e( 'No Product(s) added', 'upsell-order-bump-offer-for-woocommerce' );?></i></p>

						<?php 
					}

					echo '<hr>';

					// Target Categories.

					if( ! empty( $value['mwb_upsell_bump_target_categories'] ) ) { 

						echo '<p><i>' . esc_html__( 'Target Categories -', 'upsell-order-bump-offer-for-woocommerce' ) . '</i></p>';

						echo '<div class="mwb_upsell_bump_list_targets">';

						foreach( $value['mwb_upsell_bump_target_categories'] as $single_target_category_id ):

							$category_name = get_the_category_by_ID( $single_target_category_id );

							if( empty( $category_name ) ) {

								continue;
							}
						?>
							<p><?php esc_html_e( $category_name . "( #$single_target_category_id )" ); ?></p>
						<?php 

						endforeach;

						echo '</div>';
					} 

					else {

						?>

						<p><i><?php esc_html_e( 'No Categories added', 'upsell-order-bump-offer-for-woocommerce' );?></i></p>

						<?php 
					}


					?>
					
				</td>

				<!-- Offers Count. -->
				<td>
					<p>
					<?php 
						if( ! empty( $value['mwb_upsell_bump_products_in_offer'] ) ) {

							$offer = wc_get_product( $value['mwb_upsell_bump_products_in_offer'] );
							esc_html_e( $offer->get_title() . " (#".$value['mwb_upsell_bump_products_in_offer'].")" ); 
						} else {

							esc_html_e( "No offers Added" );
						}
					?>		
					</p>
				</td> 

				<!-- Bump Action. -->
				<td>

					<!-- Bump View/Edit link. -->
					<a class="mwb_upsell_bump_links" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&bump_id=<?php esc_html_e( $key ); ?>"><?php esc_html_e( 'View / Edit', 'upsell-order-bump-offer-for-woocommerce' );?></a>

					<!-- Bump Delete link. -->
					<a class="mwb_upsell_bump_links" href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=bump-list&del_bump_id=<?php esc_html_e( $key ); ?>"><?php esc_html_e( 'Delete', 'upsell-order-bump-offer-for-woocommerce' );?></a>
				</td>

				<?php do_action( 'mwb_ubo_add_more_col_data' ); ?>
			</tr>
			<?php endforeach;?>
			<!-- Foreach Bump end. -->
		</table>
	<?php endif;?>
</div>

<!-- Add section to trigger Go Pro popup. -->
<?php if( ! empty( $mwb_upsell_bumps_list ) && count( $mwb_upsell_bumps_list ) ) : ?>

	<input type="hidden" class="mwb_ubo_lite_saved_funnel" value="<?php echo( count( $mwb_upsell_bumps_list ) ); ?>">

<?php endif; ?>

<!-- Create New Bump. -->
<div class="mwb_upsell_bump_create_new_bump">
	<a href="?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&bump_id=1" class="mwb_ubo_lite_bump_create_button" ><?php esc_html_e( '+Create New Order Bump', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
</div>
<!-- Go pro popup wrap start. -->
<div class="mwb_ubo_lite_go_pro_popup_wrap">
	<!-- Go pro popup main start. -->
	<div class="mwb_ubo_lite_go_pro_popup">
		<!-- Main heading. -->
		<div class="mwb_ubo_lite_go_pro_popup_head">
			<h2><?php echo esc_html__( 'Want More? Go Pro !!', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
			<a href="" class="mwb_ubo_lite_go_pro_popup_close">
				<span>&times;</span>
			</a>
		</div>

		<!-- Close button. -->
		
		<!-- Notice icon. -->
		<div class="mwb_ubo_lite_go_pro_popup_head"><img src="<?php esc_html_e( UPSELL_ORDER_BUMP_OFFER_FOR_WOOCOMMERCE_URL . "admin/resources/Icons/pro.png" ); ?> ">
		</div>

		<!-- Notice. -->
		<div class="mwb_ubo_lite_go_pro_popup_content">
			<p class="mwb_ubo_lite_go_pro_popup_text">
				<?php esc_html_e( 'Stucked to just one order bump? Unlock your power to explore more.', 'upsell-order-bump-offer-for-woocommerce' ); ?>
			</p>
			<p class="mwb_ubo_lite_go_pro_popup_text">
				<?php esc_html_e( 'Go with our premium version and make unlimited numbers of order bumps. Make the most attractive offers with all of your products. Set Relevant offers for specific targets which will ensure customer satisfaction and higher conversion rates. ', 'upsell-order-bump-offer-for-woocommerce' ); ?>
			</p>
		</div>

		<!-- Go pro button. -->
		<div class="mwb_ubo_lite_go_pro_popup_button">
			<a class="button mwb_ubo_lite_overview_go_pro_button" target="_blank" href="https://makewebbetter.com/product/woocommerce-upsell-order-bump-offer-pro/?utm_source=mwb-ubo-lite-org&utm_medium=Overview&utm_campaign=ORG"><?php echo esc_html__( 'Upgrade to Premium', 'upsell-order-bump-offer-for-woocommerce' ) . ' <span class="dashicons dashicons-arrow-right-alt"></span>'; ?></a>
		</div>
	</div>
	<!-- Go pro popup main end. -->
</div>
<!-- Go pro popup wrap end. -->
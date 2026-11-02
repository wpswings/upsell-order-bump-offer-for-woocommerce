<?php
/**
 * "Gamified Offers" admin tab.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      3.1.8
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin/partials/templates
 *
 * @var array  $settings Settings.
 * @var array  $stats    Statistics.
 * @var array  $bumps    Saved bumps.
 * @var bool   $lite     Lite (single bump at checkout).
 * @var string $notice   Notice key.
 * @var array  $preview  Preview data.
 * @var string $sample   Sample product name.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wps_formats = array(
	'standard'     => __( 'Standard (checkbox)', 'upsell-order-bump-offer-for-woocommerce' ),
	'spin_wheel'   => __( 'Spin wheel', 'upsell-order-bump-offer-for-woocommerce' ),
	'scratch_card' => __( 'Scratch card', 'upsell-order-bump-offer-for-woocommerce' ),
);
$wps_total_weight = max( 1, array_sum( wp_list_pluck( $settings['prizes'], 'weight' ) ) );
?>
<div class="wps-ubo-gm" id="wps-ubo-gm">

	<div class="wps-ubo-gm__header">
		<h2 class="wps-ubo-gm__title"><?php esc_html_e( 'Gamified Offers', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
		<p class="wps-ubo-gm__subtitle"><?php esc_html_e( 'Turn an order bump into a spin wheel or scratch card. Customers play once to unlock their discount, then add the offer with the usual checkbox. Prizes are drawn on the server, so they cannot be faked or re-rolled.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
	</div>

	<?php if ( 'saved' === $notice ) : ?>
		<div class="notice notice-success inline"><p><?php esc_html_e( 'Settings saved.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p></div>
	<?php elseif ( 'reset' === $notice ) : ?>
		<div class="notice notice-success inline"><p><?php esc_html_e( 'Statistics reset.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p></div>
	<?php endif; ?>

	<form method="post" action="" class="wps-ubo-gm__form" id="wps-ubo-gm-form">
		<?php wp_nonce_field( Upsell_Order_Bump_Gamified_Admin::NONCE_ACTION, 'wps_ubo_gamified_nonce' ); ?>
		<input type="hidden" name="wps_ubo_gamified_action" value="save">

		<div class="wps-ubo-gm__grid">
			<div class="wps-ubo-gm__main">

				<!-- General -->
				<section class="wps-ubo-gm__card">
					<h3><?php esc_html_e( 'General', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
					<label class="wps-ubo-gm__check">
						<input type="checkbox" name="enabled" value="on" <?php checked( 'on', $settings['enabled'] ); ?>>
						<span><strong><?php esc_html_e( 'Enable gamified offers', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong>
						<small><?php esc_html_e( 'When off, every bump shows in its standard format.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></span>
					</label>
					<label class="wps-ubo-gm__check">
						<input type="checkbox" name="guarantee_offer_discount" value="on" <?php checked( 'on', $settings['guarantee_offer_discount'] ); ?>>
						<span><strong><?php esc_html_e( 'Never worse than the normal offer', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong>
						<small><?php esc_html_e( 'If a bump already gives a percentage discount, the customer gets whichever is higher: that discount or the prize.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></span>
					</label>
					<label class="wps-ubo-gm__check">
						<input type="checkbox" name="allow_skip" value="on" <?php checked( 'on', $settings['allow_skip'] ); ?>>
						<span><strong><?php esc_html_e( 'Let customers skip the game', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong>
						<small><?php esc_html_e( 'Shows "No thanks, just show me the offer", which reveals the standard offer.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></span>
					</label>
					<label class="wps-ubo-gm__field wps-ubo-gm__field--narrow">
						<span><?php esc_html_e( 'Maximum discount any prize can give (%)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<input type="number" name="max_discount" min="1" max="100" value="<?php echo esc_attr( $settings['max_discount'] ); ?>">
					</label>
				</section>

				<!-- Formats -->
				<section class="wps-ubo-gm__card">
					<h3><?php esc_html_e( 'Bump formats', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
					<?php if ( $lite && count( $bumps ) > 1 ) : ?>
						<p class="description"><?php esc_html_e( 'The free version shows only the first order bump at checkout; the others are used with the Pro version.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					<?php endif; ?>
					<?php if ( empty( $bumps ) ) : ?>
						<p class="description"><?php esc_html_e( 'No order bumps yet. Create one under Order Bump Setting first.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					<?php else : ?>
						<div class="wps-ubo-gm__table-wrap">
							<table class="widefat striped">
								<thead>
									<tr>
										<th scope="col"><?php esc_html_e( 'Order bump', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
										<th scope="col"><?php esc_html_e( 'Offer product', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
										<th scope="col"><?php esc_html_e( 'Normal offer', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
										<th scope="col"><?php esc_html_e( 'Format', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ( $bumps as $wps_bump_id => $wps_bump ) :
										if ( ! is_array( $wps_bump ) ) {
											continue;
										}
										$wps_product = ! empty( $wps_bump['wps_upsell_bump_products_in_offer'] ) ? wc_get_product( $wps_bump['wps_upsell_bump_products_in_offer'] ) : null;
										$wps_type    = isset( $wps_bump['wps_upsell_offer_price_type'] ) ? $wps_bump['wps_upsell_offer_price_type'] : '';
										$wps_amount  = isset( $wps_bump['wps_upsell_bump_offer_discount_price'] ) ? $wps_bump['wps_upsell_bump_offer_discount_price'] : '';
										if ( '%' === $wps_type ) {
											/* translators: %s: discount percent. */
											$wps_normal = sprintf( __( '%s%% off', 'upsell-order-bump-offer-for-woocommerce' ), $wps_amount );
										} elseif ( 'fixed' === $wps_type ) {
											/* translators: %s: fixed price. */
											$wps_normal = sprintf( __( 'Fixed price %s', 'upsell-order-bump-offer-for-woocommerce' ), wp_strip_all_tags( wc_price( (float) $wps_amount ) ) );
										} else {
											$wps_normal = __( 'No discount', 'upsell-order-bump-offer-for-woocommerce' );
										}
										$wps_current = isset( $settings['formats'][ $wps_bump_id ] ) ? $settings['formats'][ $wps_bump_id ] : 'standard';
										$wps_live    = isset( $wps_bump['wps_upsell_bump_status'] ) && 'yes' === $wps_bump['wps_upsell_bump_status'];
										?>
										<tr>
											<td>
												<strong><?php echo esc_html( ! empty( $wps_bump['wps_upsell_bump_name'] ) ? $wps_bump['wps_upsell_bump_name'] : '#' . $wps_bump_id ); ?></strong>
												<span class="wps-ubo-gm__badge <?php echo $wps_live ? 'is-on' : 'is-off'; ?>"><?php echo $wps_live ? esc_html__( 'Live', 'upsell-order-bump-offer-for-woocommerce' ) : esc_html__( 'Sandbox', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
											</td>
											<td><?php echo esc_html( $wps_product ? $wps_product->get_name() : '—' ); ?></td>
											<td><?php echo esc_html( $wps_normal ); ?></td>
											<td>
												<label class="screen-reader-text" for="wps-ubo-gm-format-<?php echo esc_attr( $wps_bump_id ); ?>"><?php esc_html_e( 'Format', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
												<select id="wps-ubo-gm-format-<?php echo esc_attr( $wps_bump_id ); ?>" name="formats[<?php echo esc_attr( $wps_bump_id ); ?>]">
													<?php foreach ( $wps_formats as $wps_value => $wps_label ) : ?>
														<option value="<?php echo esc_attr( $wps_value ); ?>" <?php selected( $wps_current, $wps_value ); ?>><?php echo esc_html( $wps_label ); ?></option>
													<?php endforeach; ?>
												</select>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
						<p class="description"><?php esc_html_e( 'A won prize replaces the bump\'s normal price with a percentage discount. With fixed-price bumps the prize applies to the product\'s regular price.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					<?php endif; ?>
				</section>

				<!-- Prizes -->
				<section class="wps-ubo-gm__card">
					<h3><?php esc_html_e( 'Prizes', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
					<p class="description"><?php esc_html_e( 'Each spin or scratch draws one prize. Weight sets how often it is drawn compared to the others. A 0% prize means "standard offer".', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					<div class="wps-ubo-gm__table-wrap">
						<table class="widefat wps-ubo-gm__prizes" id="wps-ubo-gm-prizes">
							<thead>
								<tr>
									<th scope="col"><?php esc_html_e( 'Label on wheel / card', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
									<th scope="col"><?php esc_html_e( '% off', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Weight', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Odds', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Colour', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
									<th scope="col"><span class="screen-reader-text"><?php esc_html_e( 'Remove', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $settings['prizes'] as $wps_i => $wps_row ) : ?>
									<tr>
										<td><input type="text" maxlength="24" required name="prizes[<?php echo esc_attr( $wps_i ); ?>][label]" value="<?php echo esc_attr( $wps_row['label'] ); ?>" aria-label="<?php esc_attr_e( 'Label', 'upsell-order-bump-offer-for-woocommerce' ); ?>"></td>
										<td><input type="number" min="0" max="100" class="small-text" name="prizes[<?php echo esc_attr( $wps_i ); ?>][discount]" value="<?php echo esc_attr( $wps_row['discount'] ); ?>" aria-label="<?php esc_attr_e( 'Percent off', 'upsell-order-bump-offer-for-woocommerce' ); ?>"></td>
										<td><input type="number" min="0" max="1000" class="small-text wps-ubo-gm__weight" name="prizes[<?php echo esc_attr( $wps_i ); ?>][weight]" value="<?php echo esc_attr( $wps_row['weight'] ); ?>" aria-label="<?php esc_attr_e( 'Weight', 'upsell-order-bump-offer-for-woocommerce' ); ?>"></td>
										<td class="wps-ubo-gm__odds"><?php echo esc_html( round( 100 * $wps_row['weight'] / $wps_total_weight, 1 ) . '%' ); ?></td>
										<td><input type="color" name="prizes[<?php echo esc_attr( $wps_i ); ?>][color]" value="<?php echo esc_attr( $wps_row['color'] ); ?>" aria-label="<?php esc_attr_e( 'Colour', 'upsell-order-bump-offer-for-woocommerce' ); ?>"></td>
										<td><button type="button" class="button-link wps-ubo-gm__remove" aria-label="<?php esc_attr_e( 'Remove prize', 'upsell-order-bump-offer-for-woocommerce' ); ?>">&times;</button></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<p class="wps-ubo-gm__prize-msg" id="wps-ubo-gm-prize-msg" role="status" aria-live="polite"></p>
					<button type="button" class="button" id="wps-ubo-gm-add"><?php esc_html_e( 'Add prize', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
					<template id="wps-ubo-gm-row">
						<tr>
							<td><input type="text" maxlength="24" required name="prizes[__i__][label]" value="" aria-label="<?php esc_attr_e( 'Label', 'upsell-order-bump-offer-for-woocommerce' ); ?>"></td>
							<td><input type="number" min="0" max="100" class="small-text" name="prizes[__i__][discount]" value="10" aria-label="<?php esc_attr_e( 'Percent off', 'upsell-order-bump-offer-for-woocommerce' ); ?>"></td>
							<td><input type="number" min="0" max="1000" class="small-text wps-ubo-gm__weight" name="prizes[__i__][weight]" value="10" aria-label="<?php esc_attr_e( 'Weight', 'upsell-order-bump-offer-for-woocommerce' ); ?>"></td>
							<td class="wps-ubo-gm__odds"></td>
							<td><input type="color" name="prizes[__i__][color]" value="#2271b1" aria-label="<?php esc_attr_e( 'Colour', 'upsell-order-bump-offer-for-woocommerce' ); ?>"></td>
							<td><button type="button" class="button-link wps-ubo-gm__remove" aria-label="<?php esc_attr_e( 'Remove prize', 'upsell-order-bump-offer-for-woocommerce' ); ?>">&times;</button></td>
						</tr>
					</template>
				</section>

				<!-- Texts & colours -->
				<section class="wps-ubo-gm__card">
					<h3><?php esc_html_e( 'Texts & colours', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
					<div class="wps-ubo-gm__fields">
						<label class="wps-ubo-gm__field"><span><?php esc_html_e( 'Spin wheel headline', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<input type="text" name="headline_wheel" value="<?php echo esc_attr( $settings['headline_wheel'] ); ?>"></label>
						<label class="wps-ubo-gm__field"><span><?php esc_html_e( 'Spin button', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<input type="text" name="button_wheel" value="<?php echo esc_attr( $settings['button_wheel'] ); ?>"></label>
						<label class="wps-ubo-gm__field"><span><?php esc_html_e( 'Scratch card headline', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<input type="text" name="headline_scratch" value="<?php echo esc_attr( $settings['headline_scratch'] ); ?>"></label>
						<label class="wps-ubo-gm__field"><span><?php esc_html_e( 'Reveal button (keyboard / one click)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<input type="text" name="button_scratch" value="<?php echo esc_attr( $settings['button_scratch'] ); ?>"></label>
						<label class="wps-ubo-gm__field wps-ubo-gm__field--wide"><span><?php esc_html_e( 'Win message — placeholders: {discount}, {label}, {product}', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<input type="text" name="win_message" value="<?php echo esc_attr( $settings['win_message'] ); ?>"></label>
						<label class="wps-ubo-gm__field"><span><?php esc_html_e( 'Primary colour', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<input type="color" name="color_primary" value="<?php echo esc_attr( $settings['color_primary'] ); ?>"></label>
						<label class="wps-ubo-gm__field"><span><?php esc_html_e( 'Accent colour', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<input type="color" name="color_accent" value="<?php echo esc_attr( $settings['color_accent'] ); ?>"></label>
						<label class="wps-ubo-gm__field"><span><?php esc_html_e( 'Text colour', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<input type="color" name="color_text" value="<?php echo esc_attr( $settings['color_text'] ); ?>"></label>
					</div>
				</section>

				<p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e( 'Save changes', 'upsell-order-bump-offer-for-woocommerce' ); ?></button></p>
			</div>

			<!-- Preview -->
			<aside class="wps-ubo-gm__side">
				<section class="wps-ubo-gm__card">
					<h3><?php esc_html_e( 'Live preview', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
					<p class="description"><?php esc_html_e( 'Try both formats. The preview uses your saved prizes and draws locally; save to update it.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					<div class="wps-ubo-gm__preview-tabs" role="tablist">
						<button type="button" role="tab" class="button is-active" aria-selected="true" data-preview="spin_wheel"><?php esc_html_e( 'Spin wheel', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
						<button type="button" role="tab" class="button" aria-selected="false" data-preview="scratch_card"><?php esc_html_e( 'Scratch card', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
					</div>
					<?php foreach ( array( 'spin_wheel', 'scratch_card' ) as $wps_format ) : ?>
						<div class="wps-ubo-gm__preview" data-preview-pane="<?php echo esc_attr( $wps_format ); ?>" <?php echo 'spin_wheel' === $wps_format ? '' : 'hidden'; ?>>
							<?php
							Upsell_Order_Bump_Gamified_Public::print_game(
								array(
									'bump_id'  => 0,
									'format'   => $wps_format,
									'segments' => $preview['segments'],
									'headline' => 'spin_wheel' === $wps_format ? $settings['headline_wheel'] : $settings['headline_scratch'],
									'button'   => 'spin_wheel' === $wps_format ? $settings['button_wheel'] : $settings['button_scratch'],
									'product'  => $sample,
									'best'     => $preview['best'],
									'skip'     => 'on' === $settings['allow_skip'],
									'preview'  => true,
									'weights'  => $preview['weights'],
									'results'  => $preview['results'],
								)
							);
							?>
						</div>
					<?php endforeach; ?>
				</section>
			</aside>
		</div>
	</form>

	<!-- Statistics -->
	<section class="wps-ubo-gm__card">
		<h3><?php esc_html_e( 'Statistics', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
		<?php if ( empty( $stats ) ) : ?>
			<p class="description"><?php esc_html_e( 'No games played yet.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
		<?php else : ?>
			<div class="wps-ubo-gm__table-wrap">
				<table class="widefat striped">
					<thead>
						<tr>
							<th scope="col"><?php esc_html_e( 'Order bump', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
							<th scope="col" class="num"><?php esc_html_e( 'Plays', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
							<th scope="col" class="num"><?php esc_html_e( 'Skipped', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
							<th scope="col" class="num"><?php esc_html_e( 'Accepted', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
							<th scope="col" class="num"><?php esc_html_e( 'Conversion', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Prizes drawn', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $stats as $wps_bump_id => $wps_row ) :
							$wps_shown = (int) $wps_row['plays'] + (int) $wps_row['skips'];
							$wps_drawn = array();
							foreach ( (array) $wps_row['prizes'] as $wps_index => $wps_count ) {
								$wps_drawn[] = ( isset( $settings['prizes'][ $wps_index ] ) ? $settings['prizes'][ $wps_index ]['label'] : '#' . ( $wps_index + 1 ) ) . ' × ' . $wps_count;
							}
							?>
							<tr>
								<td><?php echo esc_html( ! empty( $bumps[ $wps_bump_id ]['wps_upsell_bump_name'] ) ? $bumps[ $wps_bump_id ]['wps_upsell_bump_name'] : '#' . $wps_bump_id ); ?></td>
								<td class="num"><?php echo esc_html( number_format_i18n( (int) $wps_row['plays'] ) ); ?></td>
								<td class="num"><?php echo esc_html( number_format_i18n( (int) $wps_row['skips'] ) ); ?></td>
								<td class="num"><?php echo esc_html( number_format_i18n( (int) $wps_row['accepts'] ) ); ?></td>
								<td class="num"><?php echo esc_html( $wps_shown ? round( 100 * (int) $wps_row['accepts'] / $wps_shown, 1 ) . '%' : '–' ); ?></td>
								<td><?php echo esc_html( implode( ', ', $wps_drawn ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<form method="post" action="" class="wps-ubo-gm__reset" id="wps-ubo-gm-reset">
				<?php wp_nonce_field( Upsell_Order_Bump_Gamified_Admin::NONCE_ACTION, 'wps_ubo_gamified_nonce' ); ?>
				<input type="hidden" name="wps_ubo_gamified_action" value="reset_stats">
				<button type="submit" class="button"><?php esc_html_e( 'Reset statistics', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
			</form>
		<?php endif; ?>
	</section>
</div>

<?php
/**
 * "Gamified Offers" admin tab.
 *
 * Layout: four in-page tabs (Configuration, Prizes, Appearance, Analytics),
 * a sticky live preview and a sticky save bar. All field names are unchanged,
 * so saving is still handled by Upsell_Order_Bump_Gamified_Admin::handle_post().
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      3.1.8
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin/partials/templates
 *
 * @var array  $settings   Settings.
 * @var array  $stats      Statistics.
 * @var array  $bumps      Saved bumps.
 * @var bool   $lite       Lite (single bump at checkout).
 * @var string $notice     Notice key.
 * @var array  $preview    Preview data.
 * @var string $sample     Sample product name.
 * @var string $active_tab Active in-page tab.
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

$wps_tabs = array(
	'configuration' => __( 'Configuration', 'upsell-order-bump-offer-for-woocommerce' ),
	'prizes'        => __( 'Prizes', 'upsell-order-bump-offer-for-woocommerce' ),
	'appearance'    => __( 'Appearance', 'upsell-order-bump-offer-for-woocommerce' ),
	'analytics'     => __( 'Analytics', 'upsell-order-bump-offer-for-woocommerce' ),
);
if ( empty( $active_tab ) || ! isset( $wps_tabs[ $active_tab ] ) ) {
	$active_tab = 'configuration';
}

// Analytics totals.
$wps_totals = array(
	'plays'   => 0,
	'skips'   => 0,
	'accepts' => 0,
);
foreach ( (array) $stats as $wps_row ) {
	foreach ( $wps_totals as $wps_key => $wps_value ) {
		$wps_totals[ $wps_key ] += isset( $wps_row[ $wps_key ] ) ? (int) $wps_row[ $wps_key ] : 0;
	}
}
$wps_total_plays = $wps_totals['plays']; // Conversion = accepts ÷ plays ( spec ).

$wps_enabled = 'on' === $settings['enabled'];
?>
<div class="wps-ubo-gm" id="wps-ubo-gm" data-active-tab="<?php echo esc_attr( $active_tab ); ?>">

	<div class="wps-ubo-gm__header">
		<div class="wps-ubo-gm__header-text">
			<h2 class="wps-ubo-gm__title">
				<?php esc_html_e( 'Gamified Offers', 'upsell-order-bump-offer-for-woocommerce' ); ?>
				<span class="wps-ubo-gm__status <?php echo $wps_enabled ? 'is-on' : 'is-off'; ?>"><?php echo $wps_enabled ? esc_html__( 'Enabled', 'upsell-order-bump-offer-for-woocommerce' ) : esc_html__( 'Disabled', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
			</h2>
			<p class="wps-ubo-gm__subtitle"><?php esc_html_e( 'Turn an order bump into a spin wheel or scratch card. Prizes are drawn on the server, so they cannot be faked or re-rolled.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
		</div>
	</div>

	<?php if ( 'saved' === $notice ) : ?>
		<div class="notice notice-success inline is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p></div>
		<?php if ( ! empty( $capped ) ) : ?>
			<div class="notice notice-warning inline is-dismissible"><p>
				<?php
				echo esc_html(
					sprintf(
						/* translators: 1: number of prizes, 2: maximum discount. */
						_n( '%1$d prize was above the maximum discount and was lowered to %2$d%% (its label was updated to match).', '%1$d prizes were above the maximum discount and were lowered to %2$d%% (their labels were updated to match).', $capped, 'upsell-order-bump-offer-for-woocommerce' ),
						$capped,
						(int) $settings['max_discount']
					)
				);
				?>
			</p></div>
		<?php endif; ?>
	<?php elseif ( 'reset' === $notice ) : ?>
		<div class="notice notice-success inline is-dismissible"><p><?php esc_html_e( 'Statistics reset.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p></div>
	<?php endif; ?>

	<nav class="wps-ubo-gm__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Gamified Offers sections', 'upsell-order-bump-offer-for-woocommerce' ); ?>">
		<?php foreach ( $wps_tabs as $wps_slug => $wps_label ) : ?>
			<button type="button" role="tab" class="wps-ubo-gm__tab<?php echo $active_tab === $wps_slug ? ' is-active' : ''; ?>" id="wps-ubo-gm-tab-<?php echo esc_attr( $wps_slug ); ?>" aria-controls="wps-ubo-gm-panel-<?php echo esc_attr( $wps_slug ); ?>" aria-selected="<?php echo $active_tab === $wps_slug ? 'true' : 'false'; ?>" tabindex="<?php echo $active_tab === $wps_slug ? '0' : '-1'; ?>" data-tab="<?php echo esc_attr( $wps_slug ); ?>">
				<?php echo esc_html( $wps_label ); ?>
				<?php if ( 'prizes' === $wps_slug ) : ?>
					<span class="wps-ubo-gm__tab-count" id="wps-ubo-gm-prize-count"><?php echo esc_html( count( $settings['prizes'] ) ); ?></span>
				<?php endif; ?>
			</button>
		<?php endforeach; ?>
	</nav>

	<div class="wps-ubo-gm__layout<?php echo 'analytics' === $active_tab ? ' is-wide' : ''; ?>">
		<div class="wps-ubo-gm__content">

			<form method="post" action="" class="wps-ubo-gm__form" id="wps-ubo-gm-form" <?php echo 'analytics' === $active_tab ? 'hidden' : ''; ?>>
				<?php wp_nonce_field( Upsell_Order_Bump_Gamified_Admin::NONCE_ACTION, 'wps_ubo_gamified_nonce' ); ?>
				<input type="hidden" name="wps_ubo_gamified_action" value="save">
				<input type="hidden" name="wps_ubo_gamified_tab" id="wps-ubo-gm-active-tab" value="<?php echo esc_attr( $active_tab ); ?>">

				<!-- Configuration -->
				<section class="wps-ubo-gm__panel" role="tabpanel" id="wps-ubo-gm-panel-configuration" aria-labelledby="wps-ubo-gm-tab-configuration" data-panel="configuration" <?php echo 'configuration' === $active_tab ? '' : 'hidden'; ?>>

					<div class="wps-ubo-gm__group">
						<label class="wps-ubo-gm__switch">
							<input type="checkbox" name="enabled" value="on" <?php checked( 'on', $settings['enabled'] ); ?>>
							<span class="wps-ubo-gm__switch-ui" aria-hidden="true"></span>
							<span class="wps-ubo-gm__switch-text">
								<strong><?php esc_html_e( 'Enable gamified offers', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong>
								<small><?php esc_html_e( 'When off, every bump shows in its standard format.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small>
							</span>
						</label>
						<label class="wps-ubo-gm__switch">
							<input type="checkbox" name="allow_skip" value="on" <?php checked( 'on', $settings['allow_skip'] ); ?>>
							<span class="wps-ubo-gm__switch-ui" aria-hidden="true"></span>
							<span class="wps-ubo-gm__switch-text">
								<strong><?php esc_html_e( 'Let customers skip the game', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong>
								<small><?php esc_html_e( 'Shows "No thanks, just show me the offer", which reveals the standard offer.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small>
							</span>
						</label>
					</div>

					<div class="wps-ubo-gm__group">
						<div class="wps-ubo-gm__group-head">
							<h3><?php esc_html_e( 'Bump formats', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
							<p><?php esc_html_e( 'Choose how each order bump is shown at checkout.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
						</div>
						<?php if ( $lite && count( $bumps ) > 1 ) : ?>
							<p class="wps-ubo-gm__hint"><?php esc_html_e( 'The free version shows only the first order bump at checkout; the others are used with the Pro version.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
						<?php endif; ?>
						<?php if ( empty( $bumps ) ) : ?>
							<p class="wps-ubo-gm__empty"><?php esc_html_e( 'No order bumps yet. Create one under Order Bump Setting first.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
						<?php else : ?>
							<div class="wps-ubo-gm__table-wrap">
								<table class="wps-ubo-gm__table">
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
							<p class="wps-ubo-gm__hint"><?php esc_html_e( 'A won prize replaces the bump\'s normal price with a percentage discount. With fixed-price bumps the prize applies to the product\'s regular price.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
						<?php endif; ?>
					</div>

					<details class="wps-ubo-gm__advanced">
						<summary><?php esc_html_e( 'Advanced settings', 'upsell-order-bump-offer-for-woocommerce' ); ?></summary>
						<div class="wps-ubo-gm__advanced-body">
							<label class="wps-ubo-gm__switch">
								<input type="checkbox" name="guarantee_offer_discount" value="on" <?php checked( 'on', $settings['guarantee_offer_discount'] ); ?>>
								<span class="wps-ubo-gm__switch-ui" aria-hidden="true"></span>
								<span class="wps-ubo-gm__switch-text">
									<strong><?php esc_html_e( 'Never worse than the normal offer', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong>
									<small><?php esc_html_e( 'If a bump already gives a percentage discount, the customer gets whichever is higher: that discount or the prize.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small>
								</span>
							</label>
							<label class="wps-ubo-gm__field wps-ubo-gm__field--narrow">
								<span><?php esc_html_e( 'Maximum discount any prize can give', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
								<span class="wps-ubo-gm__input-suffix">
									<input type="number" name="max_discount" min="1" max="100" value="<?php echo esc_attr( $settings['max_discount'] ); ?>">
									<em aria-hidden="true">%</em>
								</span>
							</label>
						</div>
					</details>
				</section>

				<!-- Prizes -->
				<section class="wps-ubo-gm__panel" role="tabpanel" id="wps-ubo-gm-panel-prizes" aria-labelledby="wps-ubo-gm-tab-prizes" data-panel="prizes" <?php echo 'prizes' === $active_tab ? '' : 'hidden'; ?>>
					<div class="wps-ubo-gm__group-head">
						<h3><?php esc_html_e( 'Prizes', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
						<p><?php esc_html_e( 'Each spin or scratch draws one prize. Weight sets how often it is drawn compared to the others. A 0% prize means "standard offer".', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					</div>
					<div class="wps-ubo-gm__table-wrap">
						<table class="wps-ubo-gm__table wps-ubo-gm__prizes" id="wps-ubo-gm-prizes">
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
										<td><button type="button" class="wps-ubo-gm__remove" aria-label="<?php esc_attr_e( 'Remove prize', 'upsell-order-bump-offer-for-woocommerce' ); ?>"><span class="dashicons dashicons-trash" aria-hidden="true"></span></button></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<p class="wps-ubo-gm__prize-msg" id="wps-ubo-gm-prize-msg" role="status" aria-live="polite"></p>
					<button type="button" class="wps-ubo-gm__btn wps-ubo-gm__btn--ghost" id="wps-ubo-gm-add"><span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span><?php esc_html_e( 'Add prize', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
					<template id="wps-ubo-gm-row">
						<tr>
							<td><input type="text" maxlength="24" required name="prizes[__i__][label]" value="" aria-label="<?php esc_attr_e( 'Label', 'upsell-order-bump-offer-for-woocommerce' ); ?>"></td>
							<td><input type="number" min="0" max="100" class="small-text" name="prizes[__i__][discount]" value="10" aria-label="<?php esc_attr_e( 'Percent off', 'upsell-order-bump-offer-for-woocommerce' ); ?>"></td>
							<td><input type="number" min="0" max="1000" class="small-text wps-ubo-gm__weight" name="prizes[__i__][weight]" value="10" aria-label="<?php esc_attr_e( 'Weight', 'upsell-order-bump-offer-for-woocommerce' ); ?>"></td>
							<td class="wps-ubo-gm__odds"></td>
							<td><input type="color" name="prizes[__i__][color]" value="#2271b1" aria-label="<?php esc_attr_e( 'Colour', 'upsell-order-bump-offer-for-woocommerce' ); ?>"></td>
							<td><button type="button" class="wps-ubo-gm__remove" aria-label="<?php esc_attr_e( 'Remove prize', 'upsell-order-bump-offer-for-woocommerce' ); ?>"><span class="dashicons dashicons-trash" aria-hidden="true"></span></button></td>
						</tr>
					</template>
				</section>

				<!-- Appearance -->
				<section class="wps-ubo-gm__panel" role="tabpanel" id="wps-ubo-gm-panel-appearance" aria-labelledby="wps-ubo-gm-tab-appearance" data-panel="appearance" <?php echo 'appearance' === $active_tab ? '' : 'hidden'; ?>>
					<div class="wps-ubo-gm__group">
						<div class="wps-ubo-gm__group-head">
							<h3><?php esc_html_e( 'Spin wheel', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
						</div>
						<div class="wps-ubo-gm__fields">
							<label class="wps-ubo-gm__field"><span><?php esc_html_e( 'Headline', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
								<input type="text" maxlength="160" name="headline_wheel" value="<?php echo esc_attr( $settings['headline_wheel'] ); ?>"></label>
							<label class="wps-ubo-gm__field"><span><?php esc_html_e( 'Spin button', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
								<input type="text" maxlength="160" name="button_wheel" value="<?php echo esc_attr( $settings['button_wheel'] ); ?>"></label>
						</div>
					</div>

					<div class="wps-ubo-gm__group">
						<div class="wps-ubo-gm__group-head">
							<h3><?php esc_html_e( 'Scratch card', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
						</div>
						<div class="wps-ubo-gm__fields">
							<label class="wps-ubo-gm__field"><span><?php esc_html_e( 'Headline', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
								<input type="text" maxlength="160" name="headline_scratch" value="<?php echo esc_attr( $settings['headline_scratch'] ); ?>"></label>
							<label class="wps-ubo-gm__field"><span><?php esc_html_e( 'Reveal button (keyboard / one click)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
								<input type="text" maxlength="160" name="button_scratch" value="<?php echo esc_attr( $settings['button_scratch'] ); ?>"></label>
						</div>
					</div>

					<div class="wps-ubo-gm__group">
						<div class="wps-ubo-gm__group-head">
							<h3><?php esc_html_e( 'Win message', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
							<p>
								<?php esc_html_e( 'Placeholders:', 'upsell-order-bump-offer-for-woocommerce' ); ?>
								<code>{discount}</code> <code>{label}</code> <code>{product}</code>
							</p>
						</div>
						<label class="wps-ubo-gm__field">
							<span class="screen-reader-text"><?php esc_html_e( 'Win message', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							<input type="text" maxlength="160" name="win_message" value="<?php echo esc_attr( $settings['win_message'] ); ?>">
						</label>
					</div>

					<div class="wps-ubo-gm__group">
						<div class="wps-ubo-gm__group-head">
							<h3><?php esc_html_e( 'Colours', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
						</div>
						<div class="wps-ubo-gm__swatches">
							<label class="wps-ubo-gm__swatch">
								<input type="color" name="color_primary" value="<?php echo esc_attr( $settings['color_primary'] ); ?>">
								<span><?php esc_html_e( 'Primary', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							</label>
							<label class="wps-ubo-gm__swatch">
								<input type="color" name="color_accent" value="<?php echo esc_attr( $settings['color_accent'] ); ?>">
								<span><?php esc_html_e( 'Accent', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							</label>
							<label class="wps-ubo-gm__swatch">
								<input type="color" name="color_text" value="<?php echo esc_attr( $settings['color_text'] ); ?>">
								<span><?php esc_html_e( 'Text', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							</label>
						</div>
					</div>
				</section>

				<!-- Sticky save bar -->
				<div class="wps-ubo-gm__savebar">
					<span class="wps-ubo-gm__savebar-status" id="wps-ubo-gm-dirty" aria-live="polite"></span>
					<button type="submit" class="wps-ubo-gm__btn wps-ubo-gm__btn--primary"><?php esc_html_e( 'Save changes', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
				</div>
			</form>

			<!-- Analytics (separate from the settings form) -->
			<section class="wps-ubo-gm__panel" role="tabpanel" id="wps-ubo-gm-panel-analytics" aria-labelledby="wps-ubo-gm-tab-analytics" data-panel="analytics" <?php echo 'analytics' === $active_tab ? '' : 'hidden'; ?>>
				<div class="wps-ubo-gm__kpis">
					<div class="wps-ubo-gm__kpi">
						<span><?php esc_html_e( 'Plays', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<strong><?php echo esc_html( number_format_i18n( $wps_totals['plays'] ) ); ?></strong>
					</div>
					<div class="wps-ubo-gm__kpi">
						<span><?php esc_html_e( 'Skipped', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<strong><?php echo esc_html( number_format_i18n( $wps_totals['skips'] ) ); ?></strong>
					</div>
					<div class="wps-ubo-gm__kpi">
						<span><?php esc_html_e( 'Accepted', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<strong><?php echo esc_html( number_format_i18n( $wps_totals['accepts'] ) ); ?></strong>
					</div>
					<div class="wps-ubo-gm__kpi">
						<span><?php esc_html_e( 'Conversion', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<strong><?php echo esc_html( $wps_total_plays ? round( 100 * min( $wps_totals['accepts'], $wps_total_plays ) / $wps_total_plays, 1 ) . '%' : '–' ); ?></strong>
					</div>
				</div>

				<div class="wps-ubo-gm__group">
					<div class="wps-ubo-gm__group-head">
						<h3><?php esc_html_e( 'By order bump', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
					</div>
					<?php if ( empty( $stats ) ) : ?>
						<p class="wps-ubo-gm__empty"><?php esc_html_e( 'No games played yet.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					<?php else : ?>
						<div class="wps-ubo-gm__table-wrap">
							<table class="wps-ubo-gm__table">
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
										$wps_plays = (int) $wps_row['plays'];
										$wps_drawn = array();
										if ( ! empty( $wps_row['labels'] ) && is_array( $wps_row['labels'] ) ) {
											// Labels saved with each draw ( stay correct after the prize table is edited ).
											foreach ( $wps_row['labels'] as $wps_label => $wps_count ) {
												$wps_drawn[] = $wps_label . ' × ' . (int) $wps_count;
											}
										} else {
											// Statistics recorded before labels were saved.
											foreach ( (array) $wps_row['prizes'] as $wps_index => $wps_count ) {
												$wps_drawn[] = ( isset( $settings['prizes'][ $wps_index ] ) ? $settings['prizes'][ $wps_index ]['label'] : '#' . ( $wps_index + 1 ) ) . ' × ' . $wps_count;
											}
										}
										?>
										<tr>
											<td><?php echo esc_html( ! empty( $bumps[ $wps_bump_id ]['wps_upsell_bump_name'] ) ? $bumps[ $wps_bump_id ]['wps_upsell_bump_name'] : '#' . $wps_bump_id ); ?></td>
											<td class="num"><?php echo esc_html( number_format_i18n( (int) $wps_row['plays'] ) ); ?></td>
											<td class="num"><?php echo esc_html( number_format_i18n( (int) $wps_row['skips'] ) ); ?></td>
											<td class="num"><?php echo esc_html( number_format_i18n( (int) $wps_row['accepts'] ) ); ?></td>
											<td class="num"><?php echo esc_html( $wps_plays ? round( 100 * min( (int) $wps_row['accepts'], $wps_plays ) / $wps_plays, 1 ) . '%' : '–' ); ?></td>
											<td><?php echo esc_html( implode( ', ', $wps_drawn ) ); ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
						<form method="post" action="" class="wps-ubo-gm__reset" id="wps-ubo-gm-reset">
							<?php wp_nonce_field( Upsell_Order_Bump_Gamified_Admin::NONCE_ACTION, 'wps_ubo_gamified_nonce' ); ?>
							<input type="hidden" name="wps_ubo_gamified_action" value="reset_stats">
							<input type="hidden" name="wps_ubo_gamified_tab" value="analytics">
							<button type="submit" class="wps-ubo-gm__btn wps-ubo-gm__btn--danger"><?php esc_html_e( 'Reset statistics', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
						</form>
					<?php endif; ?>
				</div>
			</section>
		</div>

		<!-- Live preview -->
		<aside class="wps-ubo-gm__preview-col" id="wps-ubo-gm-preview" <?php echo 'analytics' === $active_tab ? 'hidden' : ''; ?>>
			<details class="wps-ubo-gm__preview-box" open>
				<summary>
					<span><?php esc_html_e( 'Live preview', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
				</summary>
				<div class="wps-ubo-gm__preview-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Preview format', 'upsell-order-bump-offer-for-woocommerce' ); ?>">
					<button type="button" role="tab" class="is-active" aria-selected="true" data-preview="spin_wheel"><?php esc_html_e( 'Spin wheel', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
					<button type="button" role="tab" aria-selected="false" data-preview="scratch_card"><?php esc_html_e( 'Scratch card', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
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
				<p class="wps-ubo-gm__preview-note"><?php esc_html_e( 'Uses your saved settings and draws locally. Save to update it.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
			</details>
		</aside>
	</div>
</div>

<?php
/**
 * "Cart Recovery" admin tab.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      3.1.8
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin/partials/templates
 *
 * @var array  $settings Settings.
 * @var array  $stats    Dashboard stats.
 * @var array  $list     { rows, total }.
 * @var string $status   Status filter.
 * @var int    $paged    Page.
 * @var string $notice   Notice key.
 * @var bool   $external External recovery plugin active.
 * @var bool   $reco_on  Smart Recommendations ready.
 * @var array  $roles    Role names.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$wps_notices = array(
	'saved'             => array( 'success', __( 'Settings saved.', 'upsell-order-bump-offer-for-woocommerce' ) ),
	'deleted'           => array( 'success', __( 'Saved cart deleted.', 'upsell-order-bump-offer-for-woocommerce' ) ),
	'test_sent'         => array( 'success', __( 'Test email sent to your address.', 'upsell-order-bump-offer-for-woocommerce' ) ),
	'test_failed'       => array( 'error', __( 'The test email could not be sent. Check your site email settings.', 'upsell-order-bump-offer-for-woocommerce' ) ),
	'send_sent'         => array( 'success', __( 'Next recovery email sent.', 'upsell-order-bump-offer-for-woocommerce' ) ),
	'send_recovered'    => array( 'success', __( 'This customer already ordered — the cart was marked as recovered.', 'upsell-order-bump-offer-for-woocommerce' ) ),
	'send_unsubscribed' => array( 'warning', __( 'This customer unsubscribed from reminders.', 'upsell-order-bump-offer-for-woocommerce' ) ),
	'send_done'         => array( 'warning', __( 'All emails of the sequence were already sent.', 'upsell-order-bump-offer-for-woocommerce' ) ),
	'send_failed'       => array( 'error', __( 'The email could not be sent.', 'upsell-order-bump-offer-for-woocommerce' ) ),
);
$wps_status_labels = array(
	''             => __( 'All', 'upsell-order-bump-offer-for-woocommerce' ),
	'active'       => __( 'Active', 'upsell-order-bump-offer-for-woocommerce' ),
	'abandoned'    => __( 'Abandoned', 'upsell-order-bump-offer-for-woocommerce' ),
	'recovered'    => __( 'Recovered', 'upsell-order-bump-offer-for-woocommerce' ),
	'lost'         => __( 'Lost', 'upsell-order-bump-offer-for-woocommerce' ),
	'unsubscribed' => __( 'Unsubscribed', 'upsell-order-bump-offer-for-woocommerce' ),
);
$wps_sources = array(
	'auto'  => __( 'Automatic — Smart Recommendations, then matching order bump, then the product below', 'upsell-order-bump-offer-for-woocommerce' ),
	'smart' => __( 'Smart Recommendations (bought together with the cart)', 'upsell-order-bump-offer-for-woocommerce' ),
	'bump'  => __( 'Matching order bump (its targets match the cart)', 'upsell-order-bump-offer-for-woocommerce' ),
	'fixed' => __( 'Always the product below', 'upsell-order-bump-offer-for-woocommerce' ),
);
$wps_units = array(
	'minutes' => __( 'minutes', 'upsell-order-bump-offer-for-woocommerce' ),
	'hours'   => __( 'hours', 'upsell-order-bump-offer-for-woocommerce' ),
	'days'    => __( 'days', 'upsell-order-bump-offer-for-woocommerce' ),
);
$wps_pages = max( 1, (int) ceil( $list['total'] / 20 ) );
?>
<div class="wps-ubo-acr" id="wps-ubo-acr">

	<div class="wps-ubo-acr__header">
		<h2 class="wps-ubo-acr__title"><?php esc_html_e( 'Cart Recovery', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
		<p class="wps-ubo-acr__subtitle"><?php esc_html_e( 'Built-in abandoned cart recovery: save carts, email customers who leave, and win them back with an upsell offer they can add in one click. No other plugin needed.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
	</div>

	<?php if ( isset( $wps_notices[ $notice ] ) ) : ?>
		<div class="wps-ubo-acr__notice is-<?php echo esc_attr( $wps_notices[ $notice ][0] ); ?>" role="status"><p><?php echo esc_html( $wps_notices[ $notice ][1] ); ?></p></div>
	<?php endif; ?>
	<?php if ( $external ) : ?>
		<div class="wps-ubo-acr__notice is-warning" role="status"><p><?php esc_html_e( 'The "WooCommerce Cart Abandonment Recovery" plugin is also active. If you enable Cart Recovery here as well, customers may get reminder emails from both. The existing "Abandoned Cart Bump" integration keeps working either way.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p></div>
	<?php endif; ?>

	<!-- Dashboard -->
	<div class="wps-ubo-acr__stats" aria-label="<?php esc_attr_e( 'Last 30 days', 'upsell-order-bump-offer-for-woocommerce' ); ?>">
		<div class="wps-ubo-acr__stat"><span><?php esc_html_e( 'Abandoned carts', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><strong><?php echo esc_html( number_format_i18n( $stats['abandoned'] ) ); ?></strong><small><?php echo esc_html( sprintf( /* translators: %s: number of emails. */ __( '%s emails sent', 'upsell-order-bump-offer-for-woocommerce' ), number_format_i18n( $stats['emails'] ) ) ); ?></small></div>
		<div class="wps-ubo-acr__stat"><span><?php esc_html_e( 'Recovered', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><strong><?php echo esc_html( number_format_i18n( $stats['recovered'] ) ); ?></strong><small><?php echo esc_html( sprintf( /* translators: 1: rate, 2: number via link. */ __( '%1$s%% recovery rate · %2$s via email link', 'upsell-order-bump-offer-for-woocommerce' ), $stats['rate'], number_format_i18n( $stats['via_link'] ) ) ); ?></small></div>
		<div class="wps-ubo-acr__stat"><span><?php esc_html_e( 'Recovered revenue', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><strong><?php echo wp_kses_post( wc_price( $stats['revenue'] ) ); ?></strong><small><?php echo esc_html( sprintf( /* translators: %s: lost carts. */ __( '%s carts lost', 'upsell-order-bump-offer-for-woocommerce' ), number_format_i18n( $stats['lost'] ) ) ); ?></small></div>
		<div class="wps-ubo-acr__stat"><span><?php esc_html_e( 'Upsell offers accepted', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><strong><?php echo esc_html( number_format_i18n( $stats['offers_accepted'] ) ); ?></strong><small><?php echo esc_html( sprintf( /* translators: 1: rate, 2: offers. */ __( '%1$s%% of %2$s offers sent', 'upsell-order-bump-offer-for-woocommerce' ), $stats['offer_rate'], number_format_i18n( $stats['offers_shown'] ) ) ); ?></small></div>
	</div>
	<p class="description wps-ubo-acr__period"><?php esc_html_e( 'Figures cover carts abandoned in the last 30 days.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>

	<form method="post" action="" class="wps-ubo-acr__form">
		<?php wp_nonce_field( Upsell_Order_Bump_Acr_Admin::NONCE_ACTION, 'wps_ubo_acr_nonce' ); ?>
		<input type="hidden" name="wps_ubo_acr_action" value="save">

		<!-- General -->
		<section class="wps-ubo-acr__card">
			<h3><?php esc_html_e( 'General', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
			<label class="wps-ubo-acr__check">
				<input type="checkbox" name="acr[enabled]" value="on" <?php checked( 'on', $settings['enabled'] ); ?>>
				<span><strong><?php esc_html_e( 'Enable cart recovery', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong>
				<small><?php esc_html_e( 'Save carts, detect abandonment and send the email sequence below.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></span>
			</label>
			<label class="wps-ubo-acr__check">
				<input type="checkbox" name="acr[capture_guests]" value="on" <?php checked( 'on', $settings['capture_guests'] ); ?>>
				<span><strong><?php esc_html_e( 'Save guest carts', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong>
				<small><?php esc_html_e( 'Guests are saved once they type a valid email at checkout. Logged-in customers are always saved.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></span>
			</label>
			<div class="wps-ubo-acr__fields">
				<label class="wps-ubo-acr__field"><span><?php esc_html_e( 'Cart counts as abandoned after (minutes idle)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
					<input type="number" name="acr[abandon_minutes]" min="5" max="1440" value="<?php echo esc_attr( $settings['abandon_minutes'] ); ?>"></label>
				<label class="wps-ubo-acr__field"><span><?php esc_html_e( 'Mark as lost after the last email (days)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
					<input type="number" name="acr[lost_after_days]" min="1" max="60" value="<?php echo esc_attr( $settings['lost_after_days'] ); ?>"></label>
				<label class="wps-ubo-acr__field"><span><?php esc_html_e( 'Delete saved carts after (days)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
					<input type="number" name="acr[retention_days]" min="7" max="730" value="<?php echo esc_attr( $settings['retention_days'] ); ?>"></label>
				<label class="wps-ubo-acr__field"><span><?php esc_html_e( 'Coupons expire after (days)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
					<input type="number" name="acr[coupon_expiry_days]" min="1" max="90" value="<?php echo esc_attr( $settings['coupon_expiry_days'] ); ?>"></label>
				<label class="wps-ubo-acr__field wps-ubo-acr__field--wide"><span><?php esc_html_e( 'Notice under the checkout email field (shown with an "Opt out" link; leave empty to hide)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
					<input type="text" name="acr[consent_notice]" value="<?php echo esc_attr( $settings['consent_notice'] ); ?>"></label>
				<fieldset class="wps-ubo-acr__field wps-ubo-acr__field--wide">
					<legend><span><?php esc_html_e( 'Never save carts of these roles', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></legend>
					<div class="wps-ubo-acr__roles">
						<?php foreach ( $roles as $wps_role => $wps_role_name ) : ?>
							<label><input type="checkbox" name="acr[exclude_roles][]" value="<?php echo esc_attr( $wps_role ); ?>" <?php checked( in_array( $wps_role, (array) $settings['exclude_roles'], true ) ); ?>> <?php echo esc_html( translate_user_role( $wps_role_name ) ); ?></label>
						<?php endforeach; ?>
					</div>
				</fieldset>
			</div>
		</section>

		<!-- Offer -->
		<section class="wps-ubo-acr__card">
			<h3><?php esc_html_e( 'Upsell offer in recovery', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
			<p class="description"><?php esc_html_e( 'Emails that include an offer show one extra product at an exclusive price, with an "Add to my order" button that restores the cart and adds the product. Customers who come back from any email also see the offer at checkout.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
			<div class="wps-ubo-acr__fields">
				<label class="wps-ubo-acr__field wps-ubo-acr__field--wide"><span><?php esc_html_e( 'Choose the offer product from', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
					<select name="acr[offer_source]">
						<?php foreach ( $wps_sources as $wps_value => $wps_label ) : ?>
							<option value="<?php echo esc_attr( $wps_value ); ?>" <?php selected( $settings['offer_source'], $wps_value ); ?>><?php echo esc_html( $wps_label ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php if ( ! $reco_on ) : ?>
						<small class="wps-ubo-acr__hint"><?php esc_html_e( 'Smart Recommendations is off or not trained yet, so "Automatic" uses the matching order bump or the product below.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small>
					<?php endif; ?>
				</label>
				<label class="wps-ubo-acr__field"><span><?php esc_html_e( 'Fallback / fixed offer product', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
					<select class="wc-product-search" name="acr[offer_product]" data-placeholder="<?php esc_attr_e( 'Search for a product…', 'upsell-order-bump-offer-for-woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-allow_clear="true" style="width:100%">
						<?php
						$wps_offer = $settings['offer_product'] ? wc_get_product( $settings['offer_product'] ) : null;
						if ( $wps_offer ) :
							?>
							<option value="<?php echo esc_attr( $wps_offer->get_id() ); ?>" selected><?php echo esc_html( wp_strip_all_tags( $wps_offer->get_formatted_name() ) ); ?></option>
						<?php endif; ?>
					</select>
				</label>
				<label class="wps-ubo-acr__field"><span><?php esc_html_e( 'Offer discount (%)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
					<input type="number" name="acr[offer_discount]" min="0" max="90" value="<?php echo esc_attr( $settings['offer_discount'] ); ?>"></label>
			</div>
			<label class="wps-ubo-acr__check">
				<input type="checkbox" name="acr[checkout_offer]" value="on" <?php checked( 'on', $settings['checkout_offer'] ); ?>>
				<span><strong><?php esc_html_e( 'Show a "Welcome back" offer at checkout', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong>
				<small><?php esc_html_e( 'For customers who return from a recovery email without adding the offer.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></span>
			</label>
		</section>

		<!-- Email sequence -->
		<section class="wps-ubo-acr__card">
			<h3><?php esc_html_e( 'Email sequence', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'Delays count from the moment the cart is abandoned. Emails stop as soon as the customer orders or unsubscribes. Placeholders:', 'upsell-order-bump-offer-for-woocommerce' ); ?>
				<code>{customer_name}</code> <code>{cart_items}</code> <code>{cart_total}</code> <code>{recovery_button}</code> <code>{recovery_link}</code> <code>{offer_block}</code> <code>{offer_product}</code> <code>{offer_price}</code> <code>{offer_discount}</code> <code>{coupon_code}</code> <code>{coupon_percent}</code> <code>{site_name}</code> <code>{unsubscribe_link}</code>
			</p>
			<div class="wps-ubo-acr__steps">
				<?php
				foreach ( $settings['steps'] as $wps_i => $wps_step ) :
					$wps_prefix  = 'acr[steps][' . $wps_i . ']';
					$wps_preview = wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'wps_ubo_acr_preview',
								'step'   => $wps_i,
							),
							admin_url( 'admin-post.php' )
						),
						Upsell_Order_Bump_Acr_Admin::NONCE_ACTION
					);
					?>
					<div class="wps-ubo-acr__step">
						<div class="wps-ubo-acr__step-head">
							<label class="wps-ubo-acr__check">
								<input type="checkbox" name="<?php echo esc_attr( $wps_prefix ); ?>[enabled]" value="on" <?php checked( 'on', $wps_step['enabled'] ); ?>>
								<strong><?php echo esc_html( sprintf( /* translators: %d: email number. */ __( 'Email %d', 'upsell-order-bump-offer-for-woocommerce' ), $wps_i + 1 ) ); ?></strong>
							</label>
							<span class="wps-ubo-acr__delay">
								<?php esc_html_e( 'Send', 'upsell-order-bump-offer-for-woocommerce' ); ?>
								<input type="number" min="1" max="999" class="small-text" name="<?php echo esc_attr( $wps_prefix ); ?>[delay]" value="<?php echo esc_attr( $wps_step['delay'] ); ?>" aria-label="<?php esc_attr_e( 'Delay', 'upsell-order-bump-offer-for-woocommerce' ); ?>">
								<select name="<?php echo esc_attr( $wps_prefix ); ?>[unit]" aria-label="<?php esc_attr_e( 'Unit', 'upsell-order-bump-offer-for-woocommerce' ); ?>">
									<?php foreach ( $wps_units as $wps_u => $wps_u_label ) : ?>
										<option value="<?php echo esc_attr( $wps_u ); ?>" <?php selected( $wps_step['unit'], $wps_u ); ?>><?php echo esc_html( $wps_u_label ); ?></option>
									<?php endforeach; ?>
								</select>
								<?php esc_html_e( 'after abandonment', 'upsell-order-bump-offer-for-woocommerce' ); ?>
							</span>
						</div>
						<div class="wps-ubo-acr__fields">
							<label class="wps-ubo-acr__field"><span><?php esc_html_e( 'Subject', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
								<input type="text" name="<?php echo esc_attr( $wps_prefix ); ?>[subject]" value="<?php echo esc_attr( $wps_step['subject'] ); ?>"></label>
							<label class="wps-ubo-acr__field"><span><?php esc_html_e( 'Heading', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
								<input type="text" name="<?php echo esc_attr( $wps_prefix ); ?>[heading]" value="<?php echo esc_attr( $wps_step['heading'] ); ?>"></label>
							<label class="wps-ubo-acr__field wps-ubo-acr__field--wide"><span><?php esc_html_e( 'Message', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
								<textarea rows="7" name="<?php echo esc_attr( $wps_prefix ); ?>[body]"><?php echo esc_textarea( $wps_step['body'] ); ?></textarea></label>
						</div>
						<div class="wps-ubo-acr__step-foot">
							<label class="wps-ubo-acr__check">
								<input type="checkbox" name="<?php echo esc_attr( $wps_prefix ); ?>[include_offer]" value="on" <?php checked( 'on', $wps_step['include_offer'] ); ?>>
								<span><?php esc_html_e( 'Include the upsell offer', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							</label>
							<label><?php esc_html_e( 'Offer discount', 'upsell-order-bump-offer-for-woocommerce' ); ?>
								<input type="number" min="1" max="90" class="small-text" name="<?php echo esc_attr( $wps_prefix ); ?>[discount]" value="<?php echo esc_attr( $wps_step['discount'] ); ?>" placeholder="<?php echo esc_attr( $settings['offer_discount'] ); ?>">%</label>
							<label><?php esc_html_e( 'Coupon (0 = none)', 'upsell-order-bump-offer-for-woocommerce' ); ?>
								<input type="number" min="0" max="90" class="small-text" name="<?php echo esc_attr( $wps_prefix ); ?>[coupon]" value="<?php echo esc_attr( $wps_step['coupon'] ); ?>">%</label>
							<span class="wps-ubo-acr__step-actions">
								<a class="button" href="<?php echo esc_url( $wps_preview ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Preview', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
								<button type="submit" class="button" form="wps-ubo-acr-test-<?php echo esc_attr( $wps_i ); ?>"><?php esc_html_e( 'Send test', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
							</span>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<p class="description"><?php esc_html_e( 'Preview and test emails use your saved settings and a sample cart. Save first to see your latest changes.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
		</section>

		<p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e( 'Save changes', 'upsell-order-bump-offer-for-woocommerce' ); ?></button></p>
	</form>

	<?php foreach ( array_keys( $settings['steps'] ) as $wps_i ) : ?>
		<form method="post" action="" id="wps-ubo-acr-test-<?php echo esc_attr( $wps_i ); ?>" hidden>
			<?php wp_nonce_field( Upsell_Order_Bump_Acr_Admin::NONCE_ACTION, 'wps_ubo_acr_nonce' ); ?>
			<input type="hidden" name="wps_ubo_acr_action" value="test">
			<input type="hidden" name="step" value="<?php echo esc_attr( $wps_i ); ?>">
		</form>
	<?php endforeach; ?>

	<!-- Carts -->
	<section class="wps-ubo-acr__card">
		<h3><?php esc_html_e( 'Saved carts', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
		<ul class="subsubsub wps-ubo-acr__filter">
			<?php
			$wps_links = array();
			foreach ( $wps_status_labels as $wps_value => $wps_label ) {
				$wps_links[] = sprintf(
					'<li><a href="%s" class="%s">%s</a></li>',
					esc_url( Upsell_Order_Bump_Acr_Admin::url( array( 'acr_status' => $wps_value ) ) ),
					$wps_value === $status ? 'current' : '',
					esc_html( $wps_label )
				);
			}
			echo wp_kses_post( implode( ' | ', $wps_links ) );
			?>
		</ul>
		<div class="wps-ubo-acr__table-wrap">
			<table class="widefat striped">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Customer', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Status', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Cart', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col" class="num"><?php esc_html_e( 'Total', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col" class="num"><?php esc_html_e( 'Emails', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Offer', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Last activity', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Actions', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $list['rows'] ) ) : ?>
						<tr><td colspan="8"><?php esc_html_e( 'No saved carts yet.', 'upsell-order-bump-offer-for-woocommerce' ); ?></td></tr>
					<?php endif; ?>
					<?php
					foreach ( $list['rows'] as $wps_row ) :
						$wps_items = array();
						foreach ( $wps_row['cart'] as $wps_item ) {
							$wps_items[] = get_the_title( ! empty( $wps_item['variation_id'] ) ? $wps_item['variation_id'] : $wps_item['product_id'] ) . ' × ' . (int) $wps_item['quantity'];
						}
						$wps_offer_name = $wps_row['offer_product'] ? get_the_title( $wps_row['offer_product'] ) . ' (' . (int) $wps_row['offer_discount'] . '%)' : '—';
						if ( $wps_row['offer_accepted'] ) {
							$wps_offer_name .= ' ✓';
						}
						$wps_time = strtotime( $wps_row['updated_at'] . ' UTC' );
						?>
						<tr>
							<td><?php echo esc_html( $wps_row['email'] ); ?><?php echo $wps_row['first_name'] ? '<br><small>' . esc_html( $wps_row['first_name'] ) . '</small>' : ''; ?></td>
							<td>
								<span class="wps-ubo-acr__status is-<?php echo esc_attr( $wps_row['status'] ); ?>"><?php echo esc_html( isset( $wps_status_labels[ $wps_row['status'] ] ) ? $wps_status_labels[ $wps_row['status'] ] : $wps_row['status'] ); ?></span>
								<?php if ( 'recovered' === $wps_row['status'] && $wps_row['recovered_via'] ) : ?>
									<br><small><?php echo 'link' === $wps_row['recovered_via'] ? esc_html__( 'via email link', 'upsell-order-bump-offer-for-woocommerce' ) : esc_html__( 'ordered directly', 'upsell-order-bump-offer-for-woocommerce' ); ?></small>
								<?php endif; ?>
							</td>
							<td><?php echo esc_html( implode( ', ', $wps_items ) ); ?></td>
							<td class="num"><?php echo wp_kses_post( wc_price( (float) $wps_row['cart_total'], array( 'currency' => $wps_row['currency'] ) ) ); ?></td>
							<td class="num"><?php echo esc_html( (int) $wps_row['emails_sent'] ); ?></td>
							<td><?php echo esc_html( $wps_offer_name ); ?></td>
							<td><?php echo esc_html( sprintf( /* translators: %s: time ago. */ __( '%s ago', 'upsell-order-bump-offer-for-woocommerce' ), human_time_diff( $wps_time ) ) ); ?></td>
							<td class="wps-ubo-acr__actions">
								<?php
								$wps_order = $wps_row['order_id'] ? wc_get_order( $wps_row['order_id'] ) : null;
								if ( $wps_order ) :
									?>
									<a href="<?php echo esc_url( $wps_order->get_edit_order_url() ); ?>"><?php echo esc_html( sprintf( /* translators: %d: order id. */ __( 'Order #%d', 'upsell-order-bump-offer-for-woocommerce' ), (int) $wps_row['order_id'] ) ); ?></a>
								<?php endif; ?>
								<?php if ( 'abandoned' === $wps_row['status'] ) : ?>
									<form method="post" action="">
										<?php wp_nonce_field( Upsell_Order_Bump_Acr_Admin::NONCE_ACTION, 'wps_ubo_acr_nonce' ); ?>
										<input type="hidden" name="wps_ubo_acr_action" value="send_now">
										<input type="hidden" name="cart_id" value="<?php echo esc_attr( $wps_row['id'] ); ?>">
										<button type="submit" class="button-link"><?php esc_html_e( 'Send next email now', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
									</form>
								<?php endif; ?>
								<form method="post" action="" class="wps-ubo-acr__delete">
									<?php wp_nonce_field( Upsell_Order_Bump_Acr_Admin::NONCE_ACTION, 'wps_ubo_acr_nonce' ); ?>
									<input type="hidden" name="wps_ubo_acr_action" value="delete">
									<input type="hidden" name="cart_id" value="<?php echo esc_attr( $wps_row['id'] ); ?>">
									<button type="submit" class="button-link wps-ubo-acr__danger"><?php esc_html_e( 'Delete', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php if ( $wps_pages > 1 ) : ?>
			<div class="tablenav"><div class="tablenav-pages">
				<?php
				echo wp_kses_post(
					paginate_links(
						array(
							'base'    => esc_url_raw( add_query_arg( 'acr_page', '%#%', Upsell_Order_Bump_Acr_Admin::url( array( 'acr_status' => $status ) ) ) ),
							'format'  => '',
							'current' => $paged,
							'total'   => $wps_pages,
						)
					)
				);
				?>
			</div></div>
		<?php endif; ?>
	</section>
</div>

<?php
/**
 * "Cart Recovery" admin tab.
 *
 * Split into sections: Dashboard, Abandoned carts (settings), Recovery offers,
 * Email sequence and Recovered carts (report). Sections are real links
 * (?acr_section=…) and the JS switches them in place without a reload.
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
 * @var string $section  Active section.
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
	'auto'  => __( 'Automatic — Smart Recommendations, then matching order bump, then the fallback product', 'upsell-order-bump-offer-for-woocommerce' ),
	'smart' => __( 'Smart Recommendations (bought together with the cart)', 'upsell-order-bump-offer-for-woocommerce' ),
	'bump'  => __( 'Matching order bump (its targets match the cart)', 'upsell-order-bump-offer-for-woocommerce' ),
	'fixed' => __( 'Always the fallback product', 'upsell-order-bump-offer-for-woocommerce' ),
);
$wps_units = array(
	'minutes' => __( 'minutes', 'upsell-order-bump-offer-for-woocommerce' ),
	'hours'   => __( 'hours', 'upsell-order-bump-offer-for-woocommerce' ),
	'days'    => __( 'days', 'upsell-order-bump-offer-for-woocommerce' ),
);
/**
 * "Sends N unit after abandonment" with a correctly pluralised unit.
 *
 * @param int    $delay Delay.
 * @param string $unit  minutes|hours|days.
 * @return string
 */
$wps_step_when = function ( $delay, $unit ) {
	$delay = (int) $delay;
	switch ( $unit ) {
		case 'minutes':
			/* translators: %s: number of minutes. */
			return sprintf( _n( 'Sends %s minute after abandonment', 'Sends %s minutes after abandonment', $delay, 'upsell-order-bump-offer-for-woocommerce' ), number_format_i18n( $delay ) );
		case 'days':
			/* translators: %s: number of days. */
			return sprintf( _n( 'Sends %s day after abandonment', 'Sends %s days after abandonment', $delay, 'upsell-order-bump-offer-for-woocommerce' ), number_format_i18n( $delay ) );
		default:
			/* translators: %s: number of hours. */
			return sprintf( _n( 'Sends %s hour after abandonment', 'Sends %s hours after abandonment', $delay, 'upsell-order-bump-offer-for-woocommerce' ), number_format_i18n( $delay ) );
	}
};
$wps_sections = array(
	'dashboard' => __( 'Dashboard', 'upsell-order-bump-offer-for-woocommerce' ),
	'settings'  => __( 'Abandoned carts', 'upsell-order-bump-offer-for-woocommerce' ),
	'offers'    => __( 'Recovery offers', 'upsell-order-bump-offer-for-woocommerce' ),
	'emails'    => __( 'Email sequence', 'upsell-order-bump-offer-for-woocommerce' ),
	'reports'   => __( 'Recovered carts', 'upsell-order-bump-offer-for-woocommerce' ),
);
$wps_section_icons = array(
	'dashboard' => 'dashicons-chart-bar',
	'settings'  => 'dashicons-cart',
	'offers'    => 'dashicons-tag',
	'emails'    => 'dashicons-email-alt',
	'reports'   => 'dashicons-list-view',
);
$wps_form_sections = array( 'settings', 'offers', 'emails' );
$wps_pages         = max( 1, (int) ceil( $list['total'] / 20 ) );
$wps_enabled       = 'on' === $settings['enabled'];
$wps_steps_on      = count(
	array_filter(
		$settings['steps'],
		function ( $step ) {
			return 'on' === $step['enabled'];
		}
	)
);
$wps_offer_product = $settings['offer_product'] ? wc_get_product( $settings['offer_product'] ) : null;

/**
 * Print the attributes that tie a panel to its nav link and hide it when inactive.
 *
 * @param string $key    Section slug.
 * @param string $active Active section slug.
 */
$wps_panel_attrs = function ( $key, $active ) {
	printf(
		'class="wps-ubo-acr__panel" id="wps-ubo-acr-panel-%1$s" data-section="%1$s" role="tabpanel" aria-labelledby="wps-ubo-acr-tab-%1$s" tabindex="-1"%2$s',
		esc_attr( $key ),
		$key === $active ? '' : ' hidden'
	);
};
?>
<div class="wps-ubo-acr" id="wps-ubo-acr" data-active="<?php echo esc_attr( $section ); ?>">

	<div class="wps-ubo-acr__header">
		<div class="wps-ubo-acr__heading">
			<h2 class="wps-ubo-acr__title"><?php esc_html_e( 'Cart Recovery', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
			<span class="wps-ubo-acr__pill <?php echo $wps_enabled ? 'is-on' : 'is-off'; ?>"><?php echo $wps_enabled ? esc_html__( 'Recovery on', 'upsell-order-bump-offer-for-woocommerce' ) : esc_html__( 'Recovery off', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
		</div>
		<p class="wps-ubo-acr__subtitle"><?php esc_html_e( 'Save carts, email customers who leave, and win them back with an upsell offer they can add in one click.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
	</div>

	<?php if ( isset( $wps_notices[ $notice ] ) ) : ?>
		<div class="wps-ubo-acr__notice is-<?php echo esc_attr( $wps_notices[ $notice ][0] ); ?>" role="status"><p><?php echo esc_html( $wps_notices[ $notice ][1] ); ?></p></div>
	<?php endif; ?>
	<?php if ( $external ) : ?>
		<div class="wps-ubo-acr__notice is-warning" role="status"><p><?php esc_html_e( 'The "WooCommerce Cart Abandonment Recovery" plugin is also active. If you enable Cart Recovery here as well, customers may get reminder emails from both. The existing "Abandoned Cart Bump" integration keeps working either way.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p></div>
	<?php endif; ?>

	<nav class="wps-ubo-acr__nav" role="tablist" aria-label="<?php esc_attr_e( 'Cart Recovery sections', 'upsell-order-bump-offer-for-woocommerce' ); ?>">
		<?php foreach ( $wps_sections as $wps_key => $wps_label ) : ?>
			<a
				id="wps-ubo-acr-tab-<?php echo esc_attr( $wps_key ); ?>"
				class="wps-ubo-acr__nav-link<?php echo $wps_key === $section ? ' is-active' : ''; ?>"
				href="<?php echo esc_url( Upsell_Order_Bump_Acr_Admin::url( array( 'acr_section' => $wps_key ) ) ); ?>"
				role="tab"
				aria-controls="wps-ubo-acr-panel-<?php echo esc_attr( $wps_key ); ?>"
				aria-selected="<?php echo $wps_key === $section ? 'true' : 'false'; ?>"
				data-section="<?php echo esc_attr( $wps_key ); ?>"
			><span class="dashicons <?php echo esc_attr( $wps_section_icons[ $wps_key ] ); ?>" aria-hidden="true"></span><?php echo esc_html( $wps_label ); ?>
				<?php if ( 'emails' === $wps_key ) : ?>
					<span class="wps-ubo-acr__count"><?php echo esc_html( $wps_steps_on . '/' . count( $settings['steps'] ) ); ?></span>
				<?php endif; ?>
			</a>
		<?php endforeach; ?>
	</nav>

	<!-- Dashboard -->
	<section <?php $wps_panel_attrs( 'dashboard', $section ); ?>>
		<div class="wps-ubo-acr__panel-head">
			<h3><?php esc_html_e( 'Last 30 days', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
			<p><?php esc_html_e( 'Figures cover carts abandoned in the last 30 days.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
		</div>
		<div class="wps-ubo-acr__stats" aria-label="<?php esc_attr_e( 'Last 30 days', 'upsell-order-bump-offer-for-woocommerce' ); ?>">
			<div class="wps-ubo-acr__stat"><span><?php esc_html_e( 'Abandoned carts', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><strong><?php echo esc_html( number_format_i18n( $stats['abandoned'] ) ); ?></strong><small><?php echo esc_html( sprintf( /* translators: %s: number of emails. */ __( '%s emails sent', 'upsell-order-bump-offer-for-woocommerce' ), number_format_i18n( $stats['emails'] ) ) ); ?></small></div>
			<div class="wps-ubo-acr__stat"><span><?php esc_html_e( 'Recovered', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><strong><?php echo esc_html( number_format_i18n( $stats['recovered'] ) ); ?></strong><small><?php echo esc_html( sprintf( /* translators: 1: rate, 2: number via link. */ __( '%1$s%% recovery rate · %2$s via email link', 'upsell-order-bump-offer-for-woocommerce' ), $stats['rate'], number_format_i18n( $stats['via_link'] ) ) ); ?></small></div>
			<div class="wps-ubo-acr__stat"><span><?php esc_html_e( 'Recovered revenue', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><strong><?php echo wp_kses_post( wc_price( $stats['revenue'] ) ); ?></strong><small><?php echo esc_html( sprintf( /* translators: %s: lost carts. */ __( '%s carts lost', 'upsell-order-bump-offer-for-woocommerce' ), number_format_i18n( $stats['lost'] ) ) ); ?></small></div>
			<div class="wps-ubo-acr__stat"><span><?php esc_html_e( 'Upsell offers accepted', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><strong><?php echo esc_html( number_format_i18n( $stats['offers_accepted'] ) ); ?></strong><small><?php echo esc_html( sprintf( /* translators: 1: rate, 2: offers. */ __( '%1$s%% of %2$s offers sent', 'upsell-order-bump-offer-for-woocommerce' ), $stats['offer_rate'], number_format_i18n( $stats['offers_shown'] ) ) ); ?></small></div>
		</div>

		<div class="wps-ubo-acr__card">
			<h3><?php esc_html_e( 'Setup at a glance', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
			<ul class="wps-ubo-acr__checklist">
				<li class="<?php echo $wps_enabled ? 'is-done' : 'is-todo'; ?>">
					<span><?php echo $wps_enabled ? esc_html__( 'Cart tracking is on', 'upsell-order-bump-offer-for-woocommerce' ) : esc_html__( 'Cart tracking is off — no carts are saved or emailed', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
					<a class="wps-ubo-acr__goto" href="<?php echo esc_url( Upsell_Order_Bump_Acr_Admin::url( array( 'acr_section' => 'settings' ) ) ); ?>" data-section="settings"><?php esc_html_e( 'Abandoned carts', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
				</li>
				<li class="<?php echo $wps_steps_on ? 'is-done' : 'is-todo'; ?>">
					<span><?php echo esc_html( sprintf( /* translators: 1: enabled emails, 2: total emails. */ __( '%1$d of %2$d recovery emails enabled', 'upsell-order-bump-offer-for-woocommerce' ), $wps_steps_on, count( $settings['steps'] ) ) ); ?></span>
					<a class="wps-ubo-acr__goto" href="<?php echo esc_url( Upsell_Order_Bump_Acr_Admin::url( array( 'acr_section' => 'emails' ) ) ); ?>" data-section="emails"><?php esc_html_e( 'Email sequence', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
				</li>
				<li class="<?php echo ( $wps_offer_product || 'fixed' !== $settings['offer_source'] ) ? 'is-done' : 'is-todo'; ?>">
					<span>
						<?php
						if ( $wps_offer_product ) {
							echo esc_html( sprintf( /* translators: 1: product name, 2: discount. */ __( 'Fallback offer: %1$s at %2$s%% off', 'upsell-order-bump-offer-for-woocommerce' ), wp_strip_all_tags( $wps_offer_product->get_name() ), $settings['offer_discount'] ) );
						} elseif ( 'fixed' === $settings['offer_source'] ) {
							esc_html_e( 'No offer product selected — emails will not include an offer', 'upsell-order-bump-offer-for-woocommerce' );
						} else {
							esc_html_e( 'Offer product is picked automatically from the cart', 'upsell-order-bump-offer-for-woocommerce' );
						}
						?>
					</span>
					<a class="wps-ubo-acr__goto" href="<?php echo esc_url( Upsell_Order_Bump_Acr_Admin::url( array( 'acr_section' => 'offers' ) ) ); ?>" data-section="offers"><?php esc_html_e( 'Recovery offers', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
				</li>
			</ul>
		</div>
	</section>

	<form method="post" action="" class="wps-ubo-acr__form" id="wps-ubo-acr-form">
		<?php wp_nonce_field( Upsell_Order_Bump_Acr_Admin::NONCE_ACTION, 'wps_ubo_acr_nonce' ); ?>
		<input type="hidden" name="wps_ubo_acr_action" value="save">
		<input type="hidden" name="acr_section" value="<?php echo esc_attr( in_array( $section, $wps_form_sections, true ) ? $section : 'settings' ); ?>" class="wps-ubo-acr__section-input">

		<!-- Abandoned carts -->
		<section <?php $wps_panel_attrs( 'settings', $section ); ?>>

			<div class="wps-ubo-acr__card">
				<h3><?php esc_html_e( 'Tracking', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
				<div class="wps-ubo-acr__row">
					<div class="wps-ubo-acr__row-label"><strong><?php esc_html_e( 'Enable cart recovery', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong><small><?php esc_html_e( 'Save carts, detect abandonment and send the email sequence.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></div>
					<div class="wps-ubo-acr__row-control"><label class="wps-ubo-acr__switch"><input type="checkbox" name="acr[enabled]" value="on" <?php checked( 'on', $settings['enabled'] ); ?>><span aria-hidden="true"></span><span class="screen-reader-text"><?php esc_html_e( 'Enable cart recovery', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></label></div>
				</div>
				<div class="wps-ubo-acr__row">
					<div class="wps-ubo-acr__row-label"><strong><?php esc_html_e( 'Save guest carts', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong><small><?php esc_html_e( 'Guests are saved once they type a valid email at checkout. Logged-in customers are always saved.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></div>
					<div class="wps-ubo-acr__row-control"><label class="wps-ubo-acr__switch"><input type="checkbox" name="acr[capture_guests]" value="on" <?php checked( 'on', $settings['capture_guests'] ); ?>><span aria-hidden="true"></span><span class="screen-reader-text"><?php esc_html_e( 'Save guest carts', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></label></div>
				</div>
				<div class="wps-ubo-acr__row">
					<label class="wps-ubo-acr__row-label" for="wps-ubo-acr-abandon"><strong><?php esc_html_e( 'Abandonment timeout', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong><small><?php esc_html_e( 'A cart counts as abandoned after this many idle minutes (5–1440).', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></label>
					<div class="wps-ubo-acr__row-control"><span class="wps-ubo-acr__unit"><input id="wps-ubo-acr-abandon" type="number" name="acr[abandon_minutes]" min="5" max="1440" value="<?php echo esc_attr( $settings['abandon_minutes'] ); ?>"><span><?php esc_html_e( 'minutes', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></span></div>
				</div>
				<fieldset class="wps-ubo-acr__row">
					<legend class="screen-reader-text"><?php esc_html_e( 'Never save carts of these roles', 'upsell-order-bump-offer-for-woocommerce' ); ?></legend>
					<div class="wps-ubo-acr__row-label"><strong><?php esc_html_e( 'Excluded roles', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong><small><?php esc_html_e( 'Never save carts of users with these roles.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></div>
					<div class="wps-ubo-acr__row-control">
						<div class="wps-ubo-acr__roles">
							<?php foreach ( $roles as $wps_role => $wps_role_name ) : ?>
								<label><input type="checkbox" name="acr[exclude_roles][]" value="<?php echo esc_attr( $wps_role ); ?>" <?php checked( in_array( $wps_role, (array) $settings['exclude_roles'], true ) ); ?>> <?php echo esc_html( translate_user_role( $wps_role_name ) ); ?></label>
							<?php endforeach; ?>
						</div>
					</div>
				</fieldset>
			</div>

			<div class="wps-ubo-acr__card">
				<h3><?php esc_html_e( 'Lifecycle & cleanup', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
				<div class="wps-ubo-acr__row">
					<label class="wps-ubo-acr__row-label" for="wps-ubo-acr-lost"><strong><?php esc_html_e( 'Mark as lost', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong><small><?php esc_html_e( 'Days after the last email before a cart counts as lost (1–60).', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></label>
					<div class="wps-ubo-acr__row-control"><span class="wps-ubo-acr__unit"><input id="wps-ubo-acr-lost" type="number" name="acr[lost_after_days]" min="1" max="60" value="<?php echo esc_attr( $settings['lost_after_days'] ); ?>"><span><?php esc_html_e( 'days', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></span></div>
				</div>
				<div class="wps-ubo-acr__row">
					<label class="wps-ubo-acr__row-label" for="wps-ubo-acr-retention"><strong><?php esc_html_e( 'Delete saved carts after', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong><small><?php esc_html_e( 'Old carts are removed automatically (7–730 days).', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></label>
					<div class="wps-ubo-acr__row-control"><span class="wps-ubo-acr__unit"><input id="wps-ubo-acr-retention" type="number" name="acr[retention_days]" min="7" max="730" value="<?php echo esc_attr( $settings['retention_days'] ); ?>"><span><?php esc_html_e( 'days', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></span></div>
				</div>
			</div>

			<div class="wps-ubo-acr__card">
				<h3><?php esc_html_e( 'Checkout consent', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
				<div class="wps-ubo-acr__row wps-ubo-acr__row--stack">
					<label class="wps-ubo-acr__row-label" for="wps-ubo-acr-consent"><strong><?php esc_html_e( 'Notice under the checkout email field', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong><small><?php esc_html_e( 'Shown with an "Opt out" link. Leave empty to hide it.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></label>
					<div class="wps-ubo-acr__row-control"><input id="wps-ubo-acr-consent" type="text" class="large-text" name="acr[consent_notice]" value="<?php echo esc_attr( $settings['consent_notice'] ); ?>"></div>
				</div>
			</div>
		</section>

		<!-- Recovery offers -->
		<section <?php $wps_panel_attrs( 'offers', $section ); ?>>
			<div class="wps-ubo-acr__card">
				<h3><?php esc_html_e( 'Upsell offer', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
				<p class="wps-ubo-acr__lead"><?php esc_html_e( 'Emails that include an offer show one extra product at an exclusive price, with an "Add to my order" button that restores the cart and adds the product.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
				<div class="wps-ubo-acr__row">
					<label class="wps-ubo-acr__row-label" for="wps-ubo-acr-source"><strong><?php esc_html_e( 'Offer product source', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong><small><?php esc_html_e( 'Where the offered product comes from.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></label>
					<div class="wps-ubo-acr__row-control">
						<select id="wps-ubo-acr-source" name="acr[offer_source]">
							<?php foreach ( $wps_sources as $wps_value => $wps_label ) : ?>
								<option value="<?php echo esc_attr( $wps_value ); ?>" <?php selected( $settings['offer_source'], $wps_value ); ?>><?php echo esc_html( $wps_label ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php if ( ! $reco_on ) : ?>
							<small class="wps-ubo-acr__hint"><?php esc_html_e( 'Smart Recommendations is off or not trained yet, so "Automatic" uses the matching order bump or the fallback product.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small>
						<?php endif; ?>
					</div>
				</div>
				<div class="wps-ubo-acr__row">
					<label class="wps-ubo-acr__row-label" for="wps-ubo-acr-product"><strong><?php esc_html_e( 'Fallback / fixed offer product', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong><small><?php esc_html_e( 'Used when no other source finds a product, or always with "Always the fallback product".', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></label>
					<div class="wps-ubo-acr__row-control">
						<select id="wps-ubo-acr-product" class="wc-product-search" name="acr[offer_product]" data-placeholder="<?php esc_attr_e( 'Search for a product…', 'upsell-order-bump-offer-for-woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-allow_clear="true" style="width:100%">
							<?php if ( $wps_offer_product ) : ?>
								<option value="<?php echo esc_attr( $wps_offer_product->get_id() ); ?>" selected><?php echo esc_html( wp_strip_all_tags( $wps_offer_product->get_formatted_name() ) ); ?></option>
							<?php endif; ?>
						</select>
					</div>
				</div>
				<div class="wps-ubo-acr__row">
					<label class="wps-ubo-acr__row-label" for="wps-ubo-acr-discount"><strong><?php esc_html_e( 'Default offer discount', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong><small><?php esc_html_e( 'Each email can override it (0–90%).', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></label>
					<div class="wps-ubo-acr__row-control"><span class="wps-ubo-acr__unit"><input id="wps-ubo-acr-discount" type="number" name="acr[offer_discount]" min="0" max="90" value="<?php echo esc_attr( $settings['offer_discount'] ); ?>"><span>%</span></span></div>
				</div>
			</div>

			<div class="wps-ubo-acr__card">
				<h3><?php esc_html_e( 'Checkout & coupons', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
				<div class="wps-ubo-acr__row">
					<div class="wps-ubo-acr__row-label"><strong><?php esc_html_e( 'Show a "Welcome back" offer at checkout', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong><small><?php esc_html_e( 'For customers who return from a recovery email without adding the offer.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></div>
					<div class="wps-ubo-acr__row-control"><label class="wps-ubo-acr__switch"><input type="checkbox" name="acr[checkout_offer]" value="on" <?php checked( 'on', $settings['checkout_offer'] ); ?>><span aria-hidden="true"></span><span class="screen-reader-text"><?php esc_html_e( 'Show a "Welcome back" offer at checkout', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></label></div>
				</div>
				<div class="wps-ubo-acr__row">
					<label class="wps-ubo-acr__row-label" for="wps-ubo-acr-coupon-expiry"><strong><?php esc_html_e( 'Coupons expire after', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong><small><?php esc_html_e( 'Applies to coupons generated by recovery emails (1–90 days).', 'upsell-order-bump-offer-for-woocommerce' ); ?></small></label>
					<div class="wps-ubo-acr__row-control"><span class="wps-ubo-acr__unit"><input id="wps-ubo-acr-coupon-expiry" type="number" name="acr[coupon_expiry_days]" min="1" max="90" value="<?php echo esc_attr( $settings['coupon_expiry_days'] ); ?>"><span><?php esc_html_e( 'days', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></span></div>
				</div>
			</div>
		</section>

		<!-- Email sequence -->
		<section <?php $wps_panel_attrs( 'emails', $section ); ?>>
			<div class="wps-ubo-acr__panel-head">
				<h3><?php esc_html_e( 'Email sequence', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
				<p><?php esc_html_e( 'Delays count from the moment the cart is abandoned. Emails stop as soon as the customer orders or unsubscribes. Preview, download and test emails use your saved settings and a sample cart — save first to see your latest changes.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
			</div>

			<details class="wps-ubo-acr__placeholders">
				<summary><?php esc_html_e( 'Available placeholders', 'upsell-order-bump-offer-for-woocommerce' ); ?></summary>
				<p>
					<code>{customer_name}</code> <code>{cart_items}</code> <code>{cart_total}</code> <code>{recovery_button}</code> <code>{recovery_link}</code> <code>{offer_block}</code> <code>{offer_product}</code> <code>{offer_price}</code> <code>{offer_discount}</code> <code>{coupon_code}</code> <code>{coupon_percent}</code> <code>{site_name}</code> <code>{unsubscribe_link}</code>
				</p>
			</details>

			<div class="wps-ubo-acr__steps">
				<?php
				foreach ( $settings['steps'] as $wps_i => $wps_step ) :
					$wps_prefix   = 'acr[steps][' . $wps_i . ']';
					$wps_body_id  = 'wps-ubo-acr-step-body-' . $wps_i;
					$wps_open     = 0 === $wps_i;
					$wps_preview  = wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'wps_ubo_acr_preview',
								'step'   => $wps_i,
							),
							admin_url( 'admin-post.php' )
						),
						Upsell_Order_Bump_Acr_Admin::NONCE_ACTION
					);
					$wps_download = add_query_arg( 'download', 1, $wps_preview );
					?>
					<div class="wps-ubo-acr__step" data-step="<?php echo esc_attr( $wps_i ); ?>">
						<div class="wps-ubo-acr__step-head">
							<label class="wps-ubo-acr__switch" title="<?php esc_attr_e( 'Send this email', 'upsell-order-bump-offer-for-woocommerce' ); ?>">
								<input type="checkbox" class="wps-ubo-acr__step-enabled" name="<?php echo esc_attr( $wps_prefix ); ?>[enabled]" value="on" <?php checked( 'on', $wps_step['enabled'] ); ?>>
								<span aria-hidden="true"></span>
								<span class="screen-reader-text"><?php echo esc_html( sprintf( /* translators: %d: email number. */ __( 'Send email %d', 'upsell-order-bump-offer-for-woocommerce' ), $wps_i + 1 ) ); ?></span>
							</label>
							<button type="button" class="wps-ubo-acr__step-toggle" aria-expanded="<?php echo $wps_open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $wps_body_id ); ?>">
								<span class="wps-ubo-acr__step-title"><?php echo esc_html( sprintf( /* translators: %d: email number. */ __( 'Email %d', 'upsell-order-bump-offer-for-woocommerce' ), $wps_i + 1 ) ); ?></span>
								<span class="wps-ubo-acr__step-meta">
									<span class="wps-ubo-acr__step-when"><?php echo esc_html( $wps_step_when( $wps_step['delay'], $wps_step['unit'] ) ); ?></span>
									<span class="wps-ubo-acr__step-subject"><?php echo esc_html( $wps_step['subject'] ); ?></span>
								</span>
								<span class="wps-ubo-acr__chevron" aria-hidden="true"></span>
							</button>
							<span class="wps-ubo-acr__step-actions">
								<a class="button button-small" href="<?php echo esc_url( $wps_preview ); ?>" target="_blank" rel="noopener"><span class="dashicons dashicons-visibility" aria-hidden="true"></span><?php esc_html_e( 'Preview', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
								<a class="button button-small" href="<?php echo esc_url( $wps_download ); ?>" download><span class="dashicons dashicons-download" aria-hidden="true"></span><?php esc_html_e( 'Download', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
								<button type="submit" class="button button-small" form="wps-ubo-acr-test-<?php echo esc_attr( $wps_i ); ?>"><span class="dashicons dashicons-email-alt" aria-hidden="true"></span><?php esc_html_e( 'Send test', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
							</span>
						</div>
						<div class="wps-ubo-acr__step-body" id="<?php echo esc_attr( $wps_body_id ); ?>"<?php echo $wps_open ? '' : ' hidden'; ?>>
							<div class="wps-ubo-acr__fields">
								<div class="wps-ubo-acr__field">
									<span><?php esc_html_e( 'Send after abandonment', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
									<span class="wps-ubo-acr__delay">
										<input type="number" min="1" max="999" class="wps-ubo-acr__step-delay" name="<?php echo esc_attr( $wps_prefix ); ?>[delay]" value="<?php echo esc_attr( $wps_step['delay'] ); ?>" aria-label="<?php esc_attr_e( 'Delay', 'upsell-order-bump-offer-for-woocommerce' ); ?>">
										<select class="wps-ubo-acr__step-unit" name="<?php echo esc_attr( $wps_prefix ); ?>[unit]" aria-label="<?php esc_attr_e( 'Unit', 'upsell-order-bump-offer-for-woocommerce' ); ?>">
											<?php foreach ( $wps_units as $wps_u => $wps_u_label ) : ?>
												<option value="<?php echo esc_attr( $wps_u ); ?>" <?php selected( $wps_step['unit'], $wps_u ); ?>><?php echo esc_html( $wps_u_label ); ?></option>
											<?php endforeach; ?>
										</select>
									</span>
								</div>
								<label class="wps-ubo-acr__field"><span><?php esc_html_e( 'Subject', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
									<input type="text" class="wps-ubo-acr__step-subject-input" name="<?php echo esc_attr( $wps_prefix ); ?>[subject]" value="<?php echo esc_attr( $wps_step['subject'] ); ?>"></label>
								<label class="wps-ubo-acr__field wps-ubo-acr__field--wide"><span><?php esc_html_e( 'Heading', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
									<input type="text" name="<?php echo esc_attr( $wps_prefix ); ?>[heading]" value="<?php echo esc_attr( $wps_step['heading'] ); ?>"></label>
								<label class="wps-ubo-acr__field wps-ubo-acr__field--wide"><span><?php esc_html_e( 'Message', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
									<textarea rows="7" name="<?php echo esc_attr( $wps_prefix ); ?>[body]"><?php echo esc_textarea( $wps_step['body'] ); ?></textarea></label>
							</div>
							<div class="wps-ubo-acr__step-foot">
								<label class="wps-ubo-acr__check">
									<input type="checkbox" name="<?php echo esc_attr( $wps_prefix ); ?>[include_offer]" value="on" <?php checked( 'on', $wps_step['include_offer'] ); ?>>
									<span><?php esc_html_e( 'Include the upsell offer', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
								</label>
								<label class="wps-ubo-acr__inline"><?php esc_html_e( 'Offer discount', 'upsell-order-bump-offer-for-woocommerce' ); ?>
									<span class="wps-ubo-acr__unit"><input type="number" min="1" max="90" name="<?php echo esc_attr( $wps_prefix ); ?>[discount]" value="<?php echo esc_attr( $wps_step['discount'] ); ?>" placeholder="<?php echo esc_attr( $settings['offer_discount'] ); ?>"><span>%</span></span></label>
								<label class="wps-ubo-acr__inline"><?php esc_html_e( 'Coupon (0 = none)', 'upsell-order-bump-offer-for-woocommerce' ); ?>
									<span class="wps-ubo-acr__unit"><input type="number" min="0" max="90" name="<?php echo esc_attr( $wps_prefix ); ?>[coupon]" value="<?php echo esc_attr( $wps_step['coupon'] ); ?>"><span>%</span></span></label>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</section>

		<div class="wps-ubo-acr__savebar">
			<span class="wps-ubo-acr__dirty" hidden><?php esc_html_e( 'You have unsaved changes', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save changes', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
		</div>
	</form>

	<?php foreach ( array_keys( $settings['steps'] ) as $wps_i ) : ?>
		<form method="post" action="" id="wps-ubo-acr-test-<?php echo esc_attr( $wps_i ); ?>" hidden>
			<?php wp_nonce_field( Upsell_Order_Bump_Acr_Admin::NONCE_ACTION, 'wps_ubo_acr_nonce' ); ?>
			<input type="hidden" name="wps_ubo_acr_action" value="test">
			<input type="hidden" name="step" value="<?php echo esc_attr( $wps_i ); ?>">
		</form>
	<?php endforeach; ?>

	<!-- Recovered carts report -->
	<section <?php $wps_panel_attrs( 'reports', $section ); ?>>
		<div class="wps-ubo-acr__card">
			<div class="wps-ubo-acr__report-head">
				<h3><?php esc_html_e( 'Saved carts', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
				<span class="wps-ubo-acr__total"><?php echo esc_html( sprintf( /* translators: %s: number of carts. */ _n( '%s cart', '%s carts', $list['total'], 'upsell-order-bump-offer-for-woocommerce' ), number_format_i18n( $list['total'] ) ) ); ?></span>
			</div>
			<ul class="wps-ubo-acr__filter">
				<?php foreach ( $wps_status_labels as $wps_value => $wps_label ) : ?>
					<li><a href="<?php echo esc_url( Upsell_Order_Bump_Acr_Admin::url( array( 'acr_section' => 'reports', 'acr_status' => $wps_value ) ) ); ?>" class="<?php echo $wps_value === $status ? 'current' : ''; ?>" <?php echo $wps_value === $status ? 'aria-current="page"' : ''; ?>><?php echo esc_html( $wps_label ); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<div class="wps-ubo-acr__table-wrap">
				<table class="widefat striped wps-ubo-acr__table">
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
							<tr class="wps-ubo-acr__empty"><td colspan="8"><?php esc_html_e( 'No saved carts yet.', 'upsell-order-bump-offer-for-woocommerce' ); ?></td></tr>
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
								<td data-label="<?php esc_attr_e( 'Customer', 'upsell-order-bump-offer-for-woocommerce' ); ?>"><?php echo esc_html( $wps_row['email'] ); ?><?php echo $wps_row['first_name'] ? '<br><small>' . esc_html( $wps_row['first_name'] ) . '</small>' : ''; ?></td>
								<td data-label="<?php esc_attr_e( 'Status', 'upsell-order-bump-offer-for-woocommerce' ); ?>">
									<span class="wps-ubo-acr__status is-<?php echo esc_attr( $wps_row['status'] ); ?>"><?php echo esc_html( isset( $wps_status_labels[ $wps_row['status'] ] ) ? $wps_status_labels[ $wps_row['status'] ] : $wps_row['status'] ); ?></span>
									<?php if ( 'recovered' === $wps_row['status'] && $wps_row['recovered_via'] ) : ?>
										<br><small><?php echo 'link' === $wps_row['recovered_via'] ? esc_html__( 'via email link', 'upsell-order-bump-offer-for-woocommerce' ) : esc_html__( 'ordered directly', 'upsell-order-bump-offer-for-woocommerce' ); ?></small>
									<?php endif; ?>
								</td>
								<td data-label="<?php esc_attr_e( 'Cart', 'upsell-order-bump-offer-for-woocommerce' ); ?>"><?php echo esc_html( implode( ', ', $wps_items ) ); ?></td>
								<td data-label="<?php esc_attr_e( 'Total', 'upsell-order-bump-offer-for-woocommerce' ); ?>" class="num"><?php echo wp_kses_post( wc_price( (float) $wps_row['cart_total'], array( 'currency' => $wps_row['currency'] ) ) ); ?></td>
								<td data-label="<?php esc_attr_e( 'Emails', 'upsell-order-bump-offer-for-woocommerce' ); ?>" class="num"><?php echo esc_html( (int) $wps_row['emails_sent'] ); ?></td>
								<td data-label="<?php esc_attr_e( 'Offer', 'upsell-order-bump-offer-for-woocommerce' ); ?>"><?php echo esc_html( $wps_offer_name ); ?></td>
								<td data-label="<?php esc_attr_e( 'Last activity', 'upsell-order-bump-offer-for-woocommerce' ); ?>"><?php echo esc_html( sprintf( /* translators: %s: time ago. */ __( '%s ago', 'upsell-order-bump-offer-for-woocommerce' ), human_time_diff( $wps_time ) ) ); ?></td>
								<td data-label="<?php esc_attr_e( 'Actions', 'upsell-order-bump-offer-for-woocommerce' ); ?>" class="wps-ubo-acr__actions">
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
											<input type="hidden" name="acr_status" value="<?php echo esc_attr( $status ); ?>">
											<input type="hidden" name="acr_page" value="<?php echo esc_attr( $paged ); ?>">
											<button type="submit" class="button-link"><?php esc_html_e( 'Send next email now', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
										</form>
									<?php endif; ?>
									<form method="post" action="" class="wps-ubo-acr__delete">
										<?php wp_nonce_field( Upsell_Order_Bump_Acr_Admin::NONCE_ACTION, 'wps_ubo_acr_nonce' ); ?>
										<input type="hidden" name="wps_ubo_acr_action" value="delete">
										<input type="hidden" name="cart_id" value="<?php echo esc_attr( $wps_row['id'] ); ?>">
										<input type="hidden" name="acr_status" value="<?php echo esc_attr( $status ); ?>">
										<input type="hidden" name="acr_page" value="<?php echo esc_attr( $paged ); ?>">
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
								'base'    => esc_url_raw(
									add_query_arg(
										'acr_page',
										'%#%',
										Upsell_Order_Bump_Acr_Admin::url(
											array(
												'acr_section' => 'reports',
												'acr_status'  => $status,
											)
										)
									)
								),
								'format'  => '',
								'current' => $paged,
								'total'   => $wps_pages,
							)
						)
					);
					?>
				</div></div>
			<?php endif; ?>
		</div>
	</section>
</div>
<noscript><style>.wps-ubo-acr__step-body[hidden]{display:block!important}</style></noscript>

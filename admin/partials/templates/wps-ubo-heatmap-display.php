<?php
/**
 * Offer Heatmaps admin page.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      3.1.8
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin/partials/templates
 *
 * @var array $settings Heatmap settings (from Upsell_Order_Bump_Heatmap_Admin::render_page()).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap wps-ubo-hm" id="wps-ubo-hm">

	<div class="wps-ubo-hm__header">
		<div>
			<h1 class="wps-ubo-hm__title"><?php esc_html_e( 'Offer Heatmaps', 'upsell-order-bump-offer-for-woocommerce' ); ?></h1>
			<p class="wps-ubo-hm__subtitle"><?php esc_html_e( 'See where customers click and hover on your order bump and upsell offer templates.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
		</div>
		<div class="wps-ubo-hm__header-actions">
			<span class="wps-ubo-hm__status <?php echo 'on' === $settings['enabled'] ? 'is-on' : 'is-off'; ?>">
				<?php echo 'on' === $settings['enabled'] ? esc_html__( 'Tracking active', 'upsell-order-bump-offer-for-woocommerce' ) : esc_html__( 'Tracking paused', 'upsell-order-bump-offer-for-woocommerce' ); ?>
			</span>
			<button type="button" class="button" id="wps-ubo-hm-settings-toggle" aria-expanded="false" aria-controls="wps-ubo-hm-settings">
				<?php esc_html_e( 'Settings', 'upsell-order-bump-offer-for-woocommerce' ); ?>
			</button>
		</div>
	</div>

	<div class="wps-ubo-hm__notice" id="wps-ubo-hm-notice" role="status" aria-live="polite" hidden></div>

	<!-- Settings -->
	<form class="wps-ubo-hm__card wps-ubo-hm__settings" id="wps-ubo-hm-settings" hidden>
		<div class="wps-ubo-hm__settings-grid">
			<label class="wps-ubo-hm__check">
				<input type="checkbox" name="enabled" value="on" <?php checked( 'on', $settings['enabled'] ); ?>>
				<span><?php esc_html_e( 'Enable interaction tracking', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
			</label>
			<label class="wps-ubo-hm__check">
				<input type="checkbox" name="track_hover" value="on" <?php checked( 'on', $settings['track_hover'] ); ?>>
				<span><?php esc_html_e( 'Record hover (mouse movement) samples', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
			</label>
			<label class="wps-ubo-hm__field">
				<span><?php esc_html_e( 'Sample rate (% of page views)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
				<input type="number" name="sample_rate" min="1" max="100" value="<?php echo esc_attr( $settings['sample_rate'] ); ?>">
			</label>
			<label class="wps-ubo-hm__field">
				<span><?php esc_html_e( 'Keep data for (days)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
				<input type="number" name="retention_days" min="1" max="730" value="<?php echo esc_attr( $settings['retention_days'] ); ?>">
			</label>
		</div>
		<p class="description">
			<?php esc_html_e( 'Data is anonymous: no IP address, account, email or order details are stored. Shop managers are never tracked, so you can test your checkout freely.', 'upsell-order-bump-offer-for-woocommerce' ); ?>
		</p>
		<div class="wps-ubo-hm__settings-actions">
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save settings', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
			<button type="button" class="button wps-ubo-hm__danger" id="wps-ubo-hm-delete"><?php esc_html_e( 'Delete all heatmap data', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
		</div>
	</form>

	<!-- Filters -->
	<div class="wps-ubo-hm__card wps-ubo-hm__filters">
		<label class="wps-ubo-hm__field">
			<span><?php esc_html_e( 'Offer type', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
			<select id="wps-ubo-hm-type">
				<option value="bump"><?php esc_html_e( 'Order Bump (checkout / cart)', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
				<option value="upsell"><?php esc_html_e( 'Upsell offer (post-purchase)', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
			</select>
		</label>
		<label class="wps-ubo-hm__field">
			<span id="wps-ubo-hm-offer-label"><?php esc_html_e( 'Order bump', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
			<select id="wps-ubo-hm-offer"></select>
		</label>
		<label class="wps-ubo-hm__field" id="wps-ubo-hm-suboffer-wrap" hidden>
			<span><?php esc_html_e( 'Offer in funnel', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
			<select id="wps-ubo-hm-suboffer"></select>
		</label>
		<label class="wps-ubo-hm__field">
			<span><?php esc_html_e( 'Template', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
			<select id="wps-ubo-hm-template"></select>
		</label>
		<label class="wps-ubo-hm__field">
			<span><?php esc_html_e( 'Device', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
			<select id="wps-ubo-hm-device">
				<option value=""><?php esc_html_e( 'All devices', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
				<option value="desktop"><?php esc_html_e( 'Desktop', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
				<option value="tablet"><?php esc_html_e( 'Tablet', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
				<option value="mobile"><?php esc_html_e( 'Mobile', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
			</select>
		</label>
		<label class="wps-ubo-hm__field">
			<span><?php esc_html_e( 'Date range', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
			<select id="wps-ubo-hm-days">
				<option value="1"><?php esc_html_e( 'Last 24 hours', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
				<option value="7"><?php esc_html_e( 'Last 7 days', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
				<option value="30" selected><?php esc_html_e( 'Last 30 days', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
				<option value="90"><?php esc_html_e( 'Last 90 days', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
				<option value="365"><?php esc_html_e( 'Last 12 months', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
			</select>
		</label>
	</div>

	<!-- Summary -->
	<div class="wps-ubo-hm__stats" id="wps-ubo-hm-stats">
		<div class="wps-ubo-hm__stat"><span class="wps-ubo-hm__stat-label"><?php esc_html_e( 'Impressions', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><span class="wps-ubo-hm__stat-value" data-stat="views">–</span></div>
		<div class="wps-ubo-hm__stat"><span class="wps-ubo-hm__stat-label"><?php esc_html_e( 'Clicks', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><span class="wps-ubo-hm__stat-value" data-stat="clicks">–</span></div>
		<div class="wps-ubo-hm__stat"><span class="wps-ubo-hm__stat-label"><?php esc_html_e( 'Clicks per impression', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><span class="wps-ubo-hm__stat-value" data-stat="ctr">–</span></div>
		<div class="wps-ubo-hm__stat"><span class="wps-ubo-hm__stat-label"><?php esc_html_e( 'Unique visitors (counted per day)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><span class="wps-ubo-hm__stat-value" data-stat="visitors">–</span></div>
	</div>

	<div class="wps-ubo-hm__body">
		<!-- Heatmap -->
		<div class="wps-ubo-hm__card wps-ubo-hm__viewer">
			<div class="wps-ubo-hm__toolbar">
				<div class="wps-ubo-hm__segmented" role="radiogroup" aria-label="<?php esc_attr_e( 'Heatmap mode', 'upsell-order-bump-offer-for-woocommerce' ); ?>">
					<label><input type="radio" name="wps-ubo-hm-mode" value="click" checked><span><?php esc_html_e( 'Clicks', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></label>
					<label><input type="radio" name="wps-ubo-hm-mode" value="hover"><span><?php esc_html_e( 'Hovers', 'upsell-order-bump-offer-for-woocommerce' ); ?></span></label>
				</div>
				<label class="wps-ubo-hm__range">
					<span><?php esc_html_e( 'Radius', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
					<input type="range" id="wps-ubo-hm-radius" min="8" max="60" value="25">
				</label>
				<label class="wps-ubo-hm__range">
					<span><?php esc_html_e( 'Opacity', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
					<input type="range" id="wps-ubo-hm-opacity" min="20" max="100" value="70">
				</label>
				<div class="wps-ubo-hm__legend" aria-hidden="true">
					<span><?php esc_html_e( 'Low', 'upsell-order-bump-offer-for-woocommerce' ); ?></span><i></i><span><?php esc_html_e( 'High', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
				</div>
			</div>

			<div class="wps-ubo-hm__stage" id="wps-ubo-hm-stage">
				<div class="wps-ubo-hm__frame" id="wps-ubo-hm-frame">
					<div class="wps-ubo-hm__preview" id="wps-ubo-hm-preview"></div>
					<canvas class="wps-ubo-hm__canvas" id="wps-ubo-hm-canvas"></canvas>
					<div class="wps-ubo-hm__shield"></div>
				</div>
				<div class="wps-ubo-hm__empty" id="wps-ubo-hm-empty" hidden></div>
				<div class="wps-ubo-hm__loading" id="wps-ubo-hm-loading" hidden><span class="spinner is-active"></span></div>
			</div>
		</div>

		<!-- Zones -->
		<div class="wps-ubo-hm__card wps-ubo-hm__zones">
			<h2><?php esc_html_e( 'Zone breakdown', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Which part of the template got the interaction. Hover a row to highlight it on the preview.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
			<table class="widefat striped">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Zone', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col" class="num"><?php esc_html_e( 'Clicks', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col" class="num"><?php esc_html_e( 'Share', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col" class="num"><?php esc_html_e( 'Hovers', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
					</tr>
				</thead>
				<tbody id="wps-ubo-hm-zones"></tbody>
			</table>
		</div>
	</div>
</div>

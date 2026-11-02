<?php
/**
 * Smart Recommendations admin page.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      3.1.8
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin/partials/templates
 *
 * @var array $settings  Settings (from Upsell_Order_Bump_Reco_Admin::render_page()).
 * @var array $ai_status AI status (from Upsell_Order_Bump_Reco_Ai::status()).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap wps-ubo-reco" id="wps-ubo-reco">

	<div class="wps-ubo-reco__header">
		<div>
			<h1 class="wps-ubo-reco__title"><?php esc_html_e( 'Smart Recommendations', 'upsell-order-bump-offer-for-woocommerce' ); ?></h1>
			<p class="wps-ubo-reco__subtitle"><?php esc_html_e( 'Let your order history decide when an order bump or upsell shows and which product it offers. Rule-based targeting stays available for every offer.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
		</div>
	</div>

	<div class="wps-ubo-reco__notice" id="wps-ubo-reco-notice" role="status" aria-live="polite" hidden></div>

	<!-- Status -->
	<div class="wps-ubo-reco__stats">
		<div class="wps-ubo-reco__stat">
			<span class="wps-ubo-reco__stat-label"><?php esc_html_e( 'Engine', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
			<span class="wps-ubo-reco__stat-value" data-status="engine">–</span>
			<span class="wps-ubo-reco__stat-sub" data-status="schedule"></span>
		</div>
		<div class="wps-ubo-reco__stat">
			<span class="wps-ubo-reco__stat-label"><?php esc_html_e( 'Model', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
			<span class="wps-ubo-reco__stat-value" data-status="trained">–</span>
			<span class="wps-ubo-reco__stat-sub" data-status="model"></span>
		</div>
		<div class="wps-ubo-reco__stat">
			<span class="wps-ubo-reco__stat-label"><?php esc_html_e( 'Associations', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
			<span class="wps-ubo-reco__stat-value" data-status="pairs">–</span>
			<span class="wps-ubo-reco__stat-sub" data-status="products"></span>
		</div>
		<div class="wps-ubo-reco__stat">
			<span class="wps-ubo-reco__stat-label"><?php esc_html_e( 'AI enhancement', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
			<span class="wps-ubo-reco__stat-value" data-status="ai">–</span>
			<span class="wps-ubo-reco__stat-sub" data-status="ai-sub"></span>
		</div>
	</div>

	<div class="wps-ubo-reco__grid">

		<!-- Settings -->
		<form class="wps-ubo-reco__card" id="wps-ubo-reco-settings">
			<h2><?php esc_html_e( 'Settings', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>

			<label class="wps-ubo-reco__check">
				<input type="checkbox" name="enabled" value="on" <?php checked( 'on', $settings['enabled'] ); ?>>
				<span>
					<strong><?php esc_html_e( 'Enable Smart Recommendations', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong>
					<small><?php esc_html_e( 'When off, every bump and funnel uses its rule-based targets and selected product.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small>
				</span>
			</label>

			<fieldset>
				<legend><?php esc_html_e( 'Learning from orders', 'upsell-order-bump-offer-for-woocommerce' ); ?></legend>
				<div class="wps-ubo-reco__fields">
					<label class="wps-ubo-reco__field">
						<span><?php esc_html_e( 'Use orders from the last (days)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<input type="number" name="lookback_days" min="7" max="1825" value="<?php echo esc_attr( $settings['lookback_days'] ); ?>">
					</label>
					<label class="wps-ubo-reco__field">
						<span><?php esc_html_e( 'Retrain', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<select name="schedule">
							<option value="daily" <?php selected( 'daily', $settings['schedule'] ); ?>><?php esc_html_e( 'Daily', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
							<option value="weekly" <?php selected( 'weekly', $settings['schedule'] ); ?>><?php esc_html_e( 'Weekly', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
							<option value="manual" <?php selected( 'manual', $settings['schedule'] ); ?>><?php esc_html_e( 'Only when I click "Train now"', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
						</select>
					</label>
					<label class="wps-ubo-reco__field">
						<span><?php esc_html_e( 'Minimum orders together', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<input type="number" name="min_support" min="1" max="1000" value="<?php echo esc_attr( $settings['min_support'] ); ?>">
					</label>
					<label class="wps-ubo-reco__field">
						<span><?php esc_html_e( 'Minimum confidence (%)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<input type="number" name="min_confidence" min="0" max="100" value="<?php echo esc_attr( $settings['min_confidence'] ); ?>">
					</label>
					<label class="wps-ubo-reco__field">
						<span><?php esc_html_e( 'Minimum lift', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<input type="number" name="min_lift" min="0" max="50" step="0.1" value="<?php echo esc_attr( $settings['min_lift'] ); ?>">
					</label>
					<label class="wps-ubo-reco__field">
						<span><?php esc_html_e( 'Recommendations kept per product', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<input type="number" name="max_candidates" min="3" max="100" value="<?php echo esc_attr( $settings['max_candidates'] ); ?>">
					</label>
					<label class="wps-ubo-reco__field">
						<span><?php esc_html_e( '"Bought next" window (days, 0 = off)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<input type="number" name="seq_window_days" min="0" max="365" value="<?php echo esc_attr( $settings['seq_window_days'] ); ?>">
					</label>
					<label class="wps-ubo-reco__field">
						<span><?php esc_html_e( '"Bought next" weight (%)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<input type="number" name="seq_weight" min="0" max="100" value="<?php echo esc_attr( $settings['seq_weight'] ); ?>">
					</label>
				</div>
			</fieldset>

			<fieldset>
				<legend><?php esc_html_e( 'Showing offers', 'upsell-order-bump-offer-for-woocommerce' ); ?></legend>
				<div class="wps-ubo-reco__fields">
					<label class="wps-ubo-reco__field">
						<span><?php esc_html_e( 'Smart match threshold (%)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<input type="number" name="match_threshold" min="1" max="100" value="<?php echo esc_attr( $settings['match_threshold'] ); ?>">
					</label>
				</div>
				<label class="wps-ubo-reco__check">
					<input type="checkbox" name="exclude_purchased" value="on" <?php checked( 'on', $settings['exclude_purchased'] ); ?>>
					<span><?php esc_html_e( 'Don\'t recommend products a logged-in customer already bought', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
				</label>
			</fieldset>

			<fieldset>
				<legend><?php esc_html_e( 'AI enhancement (WordPress AI Connectors)', 'upsell-order-bump-offer-for-woocommerce' ); ?></legend>
				<label class="wps-ubo-reco__check">
					<input type="checkbox" name="ai_enabled" value="on" <?php checked( 'on', $settings['ai_enabled'] ); ?>>
					<span>
						<strong><?php esc_html_e( 'Use AI to refine recommendations', 'upsell-order-bump-offer-for-woocommerce' ); ?></strong>
						<small><?php esc_html_e( 'While training, an AI model scores how well products complement each other and suggests add-ons for products with little sales history. Only catalog data (names, categories, prices, short descriptions) is sent — never customer or order data. AI is never called at checkout.', 'upsell-order-bump-offer-for-woocommerce' ); ?></small>
					</span>
				</label>
				<div class="wps-ubo-reco__fields">
					<label class="wps-ubo-reco__field">
						<span><?php esc_html_e( 'AI provider', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<select name="ai_provider">
							<option value=""><?php esc_html_e( 'Automatic (any connected provider)', 'upsell-order-bump-offer-for-woocommerce' ); ?></option>
							<?php foreach ( $ai_status['connectors'] as $connector_id => $connector ) : ?>
								<option value="<?php echo esc_attr( $connector_id ); ?>" <?php selected( $connector_id, $settings['ai_provider'] ); ?> <?php disabled( ! ( $connector['installed'] && $connector['has_key'] ) ); ?>>
									<?php echo esc_html( $connector['name'] . ( $connector['installed'] && $connector['has_key'] ? '' : ' — ' . __( 'not connected', 'upsell-order-bump-offer-for-woocommerce' ) ) ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</label>
					<label class="wps-ubo-reco__field">
						<span><?php esc_html_e( 'Products sent to AI per training', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<input type="number" name="ai_anchor_limit" min="1" max="200" value="<?php echo esc_attr( $settings['ai_anchor_limit'] ); ?>">
					</label>
					<label class="wps-ubo-reco__field">
						<span><?php esc_html_e( 'AI weight in final score (%)', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
						<input type="number" name="ai_weight" min="0" max="100" value="<?php echo esc_attr( $settings['ai_weight'] ); ?>">
					</label>
				</div>
				<label class="wps-ubo-reco__check">
					<input type="checkbox" name="ai_cold_start" value="on" <?php checked( 'on', $settings['ai_cold_start'] ); ?>>
					<span><?php esc_html_e( 'Let AI suggest add-ons for products without purchase history', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
				</label>
			</fieldset>

			<div class="wps-ubo-reco__actions">
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Save settings', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
			</div>
		</form>

		<div class="wps-ubo-reco__side">

			<!-- Actions -->
			<div class="wps-ubo-reco__card">
				<h2><?php esc_html_e( 'Model', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Training reads your completed and processing orders, finds products bought together (and bought next), and publishes the new model without interrupting checkout.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
				<p class="wps-ubo-reco__error" data-status="error" hidden></p>
				<div class="wps-ubo-reco__actions">
					<button type="button" class="button button-primary" id="wps-ubo-reco-train"><?php esc_html_e( 'Train now', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
					<button type="button" class="button wps-ubo-reco__danger" id="wps-ubo-reco-delete"><?php esc_html_e( 'Delete model', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
				</div>
			</div>

			<!-- AI connectors -->
			<div class="wps-ubo-reco__card">
				<h2><?php esc_html_e( 'AI Connectors', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
				<?php if ( empty( $ai_status['connectors'] ) ) : ?>
					<p class="description"><?php echo esc_html( $ai_status['reason'] ); ?></p>
				<?php else : ?>
					<ul class="wps-ubo-reco__connectors">
						<?php foreach ( $ai_status['connectors'] as $connector ) : ?>
							<li>
								<span class="wps-ubo-reco__connector-name"><?php echo esc_html( $connector['name'] ); ?></span>
								<span class="wps-ubo-reco__badge <?php echo $connector['installed'] ? 'is-on' : 'is-off'; ?>"><?php echo $connector['installed'] ? esc_html__( 'Installed', 'upsell-order-bump-offer-for-woocommerce' ) : esc_html__( 'Not installed', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
								<span class="wps-ubo-reco__badge <?php echo $connector['has_key'] ? 'is-on' : 'is-off'; ?>"><?php echo $connector['has_key'] ? esc_html__( 'Key set', 'upsell-order-bump-offer-for-woocommerce' ) : esc_html__( 'No key', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
					<?php if ( ! $ai_status['available'] ) : ?>
						<p class="description"><?php echo esc_html( $ai_status['reason'] ); ?></p>
					<?php endif; ?>
				<?php endif; ?>
				<div class="wps-ubo-reco__actions">
					<?php if ( Upsell_Order_Bump_Reco_Ai::client_exists() ) : ?>
						<a class="button" href="<?php echo esc_url( $ai_status['settings_url'] ); ?>"><?php esc_html_e( 'Manage connectors', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
					<?php endif; ?>
					<button type="button" class="button" id="wps-ubo-reco-test-ai" <?php disabled( ! $ai_status['available'] ); ?>><?php esc_html_e( 'Test AI connection', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
				</div>
			</div>

			<!-- Try it -->
			<div class="wps-ubo-reco__card">
				<h2><?php esc_html_e( 'Try it', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Pick products as if they were in the cart to see what would be recommended.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
				<select id="wps-ubo-reco-try-products" class="wc-product-search" multiple="multiple" style="width:100%" data-action="woocommerce_json_search_products_and_variations" data-placeholder="<?php esc_attr_e( 'Search for a product…', 'upsell-order-bump-offer-for-woocommerce' ); ?>"></select>
				<div class="wps-ubo-reco__actions">
					<button type="button" class="button" id="wps-ubo-reco-try"><?php esc_html_e( 'Show recommendations', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
				</div>
				<ol class="wps-ubo-reco__try-results" id="wps-ubo-reco-try-results"></ol>
			</div>
		</div>
	</div>

	<!-- Offers using AI -->
	<div class="wps-ubo-reco__card">
		<h2><?php esc_html_e( 'Offers using Smart Recommendations', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Choose the targeting mode and offer product source on each order bump and upsell funnel edit screen.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
		<div class="wps-ubo-reco__table-wrap">
			<table class="widefat striped">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Offer', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Type', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Targeting', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Offer product', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Status', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
					</tr>
				</thead>
				<tbody id="wps-ubo-reco-offers"></tbody>
			</table>
		</div>
	</div>

	<!-- Top associations -->
	<div class="wps-ubo-reco__card">
		<h2><?php esc_html_e( 'Strongest associations', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Confidence: share of orders with the first product that also contain the second. Lift: how much more likely than chance (above 1 = real association).', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
		<div class="wps-ubo-reco__table-wrap">
			<table class="widefat striped">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Customer buys', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Recommend', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col" class="num"><?php esc_html_e( 'Together', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col" class="num"><?php esc_html_e( 'Next', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col" class="num"><?php esc_html_e( 'Confidence', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col" class="num"><?php esc_html_e( 'Lift', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col" class="num"><?php esc_html_e( 'AI', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col" class="num"><?php esc_html_e( 'Score', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
						<th scope="col"><?php esc_html_e( 'AI note', 'upsell-order-bump-offer-for-woocommerce' ); ?></th>
					</tr>
				</thead>
				<tbody id="wps-ubo-reco-pairs"></tbody>
			</table>
		</div>
	</div>
</div>

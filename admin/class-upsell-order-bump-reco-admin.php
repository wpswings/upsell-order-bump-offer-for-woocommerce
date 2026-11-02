<?php
/**
 * Smart Offer Recommendations - admin side.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      3.1.8
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/admin
 * @author     WP Swings <webmaster@wpswings.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * "Smart Recommendations" page, AJAX endpoints and per-offer fields.
 *
 * @since 3.1.8
 */
class Upsell_Order_Bump_Reco_Admin {

	/**
	 * Sub-menu slug.
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'upsell-order-bump-offer-for-woocommerce-smart-reco';

	/**
	 * Nonce action.
	 *
	 * @var string
	 */
	const NONCE_ACTION = 'wps_ubo_reco_admin';

	/**
	 * Capability.
	 *
	 * @var string
	 */
	const CAPABILITY = 'manage_woocommerce';

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Page hook suffix.
	 *
	 * @var string
	 */
	private $hook_suffix = '';

	/**
	 * Constructor.
	 *
	 * @param string $version Plugin version.
	 */
	public function __construct( $version ) {
		$this->version = $version;
	}

	/**
	 * Register the sub-menu.
	 *
	 * @return void
	 */
	public function add_menu() {
		$this->hook_suffix = (string) add_submenu_page(
			'upsell-order-bump-offer-for-woocommerce-setting',
			esc_html__( 'Smart Recommendations', 'upsell-order-bump-offer-for-woocommerce' ),
			esc_html__( 'Smart Recommendations', 'upsell-order-bump-offer-for-woocommerce' ),
			self::CAPABILITY,
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Enqueue assets on the page only.
	 *
	 * @param string $hook_suffix Current page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook_suffix ) {
		if ( empty( $this->hook_suffix ) || $hook_suffix !== $this->hook_suffix ) {
			return;
		}

		$base = plugin_dir_url( __DIR__ );

		// WooCommerce product search (selectWoo).
		wp_enqueue_style( 'woocommerce_admin_styles' );
		wp_enqueue_script( 'wc-enhanced-select' );

		wp_enqueue_style( 'wps-ubo-reco-admin', $base . 'admin/css/wps-ubo-reco-admin.css', array(), $this->version );
		wp_enqueue_script( 'wps-ubo-reco-admin', $base . 'admin/js/wps-ubo-reco-admin.js', array( 'jquery', 'wc-enhanced-select' ), $this->version, true );

		wp_localize_script(
			'wps-ubo-reco-admin',
			'wpsUboReco',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( self::NONCE_ACTION ),
				'i18n'    => array(
					'saved'         => __( 'Settings saved.', 'upsell-order-bump-offer-for-woocommerce' ),
					'training'      => __( 'Training the model… this can take a minute on large stores.', 'upsell-order-bump-offer-for-woocommerce' ),
					'trained'       => __( 'Model trained and published.', 'upsell-order-bump-offer-for-woocommerce' ),
					'deleted'       => __( 'Model deleted.', 'upsell-order-bump-offer-for-woocommerce' ),
					'confirmDelete' => __( 'Delete the trained model? Smart offers fall back to rule-based targeting until you train again.', 'upsell-order-bump-offer-for-woocommerce' ),
					'testing'       => __( 'Contacting the AI provider…', 'upsell-order-bump-offer-for-woocommerce' ),
					'aiOk'          => __( 'AI connection works. Reply:', 'upsell-order-bump-offer-for-woocommerce' ),
					'pickProducts'  => __( 'Pick at least one product.', 'upsell-order-bump-offer-for-woocommerce' ),
					'noRecs'        => __( 'No recommendation for these products yet (not enough purchase data, or nothing passes the thresholds).', 'upsell-order-bump-offer-for-woocommerce' ),
					'noPairs'       => __( 'No associations yet. Train the model once you have orders.', 'upsell-order-bump-offer-for-woocommerce' ),
					'never'         => __( 'Never', 'upsell-order-bump-offer-for-woocommerce' ),
					'error'         => __( 'Something went wrong. Please reload and try again.', 'upsell-order-bump-offer-for-woocommerce' ),
				),
			)
		);
	}

	/**
	 * Render the page.
	 *
	 * @return void
	 */
	public function render_page() {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			return;
		}
		$settings  = Upsell_Order_Bump_Reco_Db::get_settings();
		$ai_status = Upsell_Order_Bump_Reco_Ai::status( $settings );
		include plugin_dir_path( __FILE__ ) . 'partials/templates/wps-ubo-reco-display.php';
	}

	/**
	 * Nonce + capability check for AJAX.
	 *
	 * @return void
	 */
	private function verify_request() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_send_json_error( array( 'message' => __( 'You are not allowed to do this.', 'upsell-order-bump-offer-for-woocommerce' ) ), 403 );
		}
	}

	/**
	 * AJAX: save settings.
	 *
	 * @return void
	 */
	public function ajax_save_settings() {
		$this->verify_request();

		$raw      = isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ? map_deep( wp_unslash( $_POST['settings'] ), 'sanitize_text_field' ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified in verify_request().
		$settings = Upsell_Order_Bump_Reco_Db::sanitize_settings( $raw );

		update_option( Upsell_Order_Bump_Reco_Db::SETTINGS_OPTION, $settings, false );
		Upsell_Order_Bump_Reco_Db::reschedule();

		wp_send_json_success( $this->status_payload() );
	}

	/**
	 * AJAX: train now (synchronous, with time limit lifted).
	 *
	 * @return void
	 */
	public function ajax_train() {
		$this->verify_request();

		$result = Upsell_Order_Bump_Reco_Engine::train();
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array_merge( $this->status_payload(), array( 'message' => $result->get_error_message() ) ) );
		}
		wp_send_json_success( $this->status_payload() );
	}

	/**
	 * AJAX: current status.
	 *
	 * @return void
	 */
	public function ajax_status() {
		$this->verify_request();
		wp_send_json_success( $this->status_payload() );
	}

	/**
	 * AJAX: delete model.
	 *
	 * @return void
	 */
	public function ajax_delete_model() {
		$this->verify_request();
		Upsell_Order_Bump_Reco_Db::delete_model();
		wp_send_json_success( $this->status_payload() );
	}

	/**
	 * AJAX: test the AI connector.
	 *
	 * @return void
	 */
	public function ajax_test_ai() {
		$this->verify_request();

		$raw      = isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ? map_deep( wp_unslash( $_POST['settings'] ), 'sanitize_text_field' ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified in verify_request().
		$settings = $raw ? Upsell_Order_Bump_Reco_Db::sanitize_settings( $raw ) : Upsell_Order_Bump_Reco_Db::get_settings();
		$reply    = Upsell_Order_Bump_Reco_Ai::test_connection( $settings );

		if ( is_wp_error( $reply ) ) {
			wp_send_json_error( array( 'message' => $reply->get_error_message() ) );
		}
		wp_send_json_success( array( 'reply' => $reply ) );
	}

	/**
	 * AJAX: "Try it" — recommendations for a simulated cart.
	 *
	 * @return void
	 */
	public function ajax_preview() {
		$this->verify_request();

		$ids = isset( $_POST['products'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['products'] ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified in verify_request().
		$ids = array_slice( array_values( array_unique( array_filter( array_map( array( 'Upsell_Order_Bump_Reco_Engine', 'parent_id' ), array_filter( $ids ) ) ) ) ), 0, 20 );

		$recs = Upsell_Order_Bump_Reco_Engine::recommend(
			$ids,
			array(
				'limit'   => 10,
				'context' => 'preview',
			)
		);

		foreach ( $recs as &$rec ) {
			$rec['name']        = wps_ubo_lite_get_title( $rec['id'] );
			$rec['anchor_name'] = wps_ubo_lite_get_title( $rec['anchor'] );
			$rec['url']         = get_edit_post_link( $rec['id'], 'raw' );
		}
		unset( $rec );

		$settings = Upsell_Order_Bump_Reco_Db::get_settings();
		wp_send_json_success(
			array(
				'items'     => $recs,
				'threshold' => (int) $settings['match_threshold'] / 100,
			)
		);
	}

	/**
	 * Data for the status cards / tables.
	 *
	 * @return array
	 */
	private function status_payload() {
		$settings = Upsell_Order_Bump_Reco_Db::get_settings();
		$model    = Upsell_Order_Bump_Reco_Db::get_model();
		$ai       = Upsell_Order_Bump_Reco_Ai::status( $settings );

		$pairs = array();
		foreach ( Upsell_Order_Bump_Reco_Engine::top_pairs( 25 ) as $row ) {
			$pairs[] = array(
				'source'    => wps_ubo_lite_get_title( $row['source_id'] ),
				'source_id' => (int) $row['source_id'],
				'target'    => wps_ubo_lite_get_title( $row['target_id'] ),
				'target_id' => (int) $row['target_id'],
				'co'        => (int) $row['co_count'],
				'seq'       => (int) $row['seq_count'],
				'ml'        => round( (float) $row['ml_score'], 4 ),
				'lift'      => round( (float) $row['lift'], 2 ),
				'ai'        => null === $row['ai_score'] ? null : round( (float) $row['ai_score'], 4 ),
				'reason'    => (string) $row['ai_reason'],
				'score'     => round( (float) $row['score'], 4 ),
			);
		}

		$next = wp_next_scheduled( Upsell_Order_Bump_Reco_Db::TRAIN_HOOK );
		if ( ! $next && function_exists( 'as_next_scheduled_action' ) ) {
			$next = as_next_scheduled_action( Upsell_Order_Bump_Reco_Db::TRAIN_HOOK, array(), Upsell_Order_Bump_Reco_Db::AS_GROUP );
		}

		return array(
			'enabled'  => 'on' === $settings['enabled'],
			'model'    => array_merge(
				$model,
				array(
					'trained_human' => $model['trained_at'] ? sprintf( /* translators: %s: human time difference. */ __( '%s ago', 'upsell-order-bump-offer-for-woocommerce' ), human_time_diff( (int) $model['trained_at'] ) ) : '',
					'next_human'    => is_numeric( $next ) && $next ? sprintf( /* translators: %s: human time difference. */ __( 'in %s', 'upsell-order-bump-offer-for-woocommerce' ), human_time_diff( (int) $next ) ) : '',
				)
			),
			'ai'       => array(
				'enabled'   => 'on' === $settings['ai_enabled'],
				'available' => $ai['available'],
				'reason'    => $ai['reason'],
				'provider'  => $ai['provider'] && isset( $ai['connectors'][ $ai['provider'] ] ) ? $ai['connectors'][ $ai['provider'] ]['name'] : '',
			),
			'pairs'    => $pairs,
			'offers'   => $this->offers_using_ai(),
		);
	}

	/**
	 * Bumps and funnels with smart targeting or an AI product.
	 *
	 * @return array[]
	 */
	private function offers_using_ai() {
		$labels = $this->labels();
		$out    = array();

		foreach ( (array) get_option( 'wps_ubo_bump_list', array() ) as $id => $bump ) {
			$mode   = Upsell_Order_Bump_Reco_Public::targeting_mode( $bump );
			$source = Upsell_Order_Bump_Reco_Public::product_source( $bump );
			if ( 'static' === $mode && 'static' === $source ) {
				continue;
			}
			$out[] = array(
				'type'   => __( 'Order bump', 'upsell-order-bump-offer-for-woocommerce' ),
				'name'   => ! empty( $bump['wps_upsell_bump_name'] ) ? $bump['wps_upsell_bump_name'] : '#' . $id,
				'mode'   => $labels['mode'][ $mode ],
				'source' => $labels['source'][ $source ],
				'live'   => ! empty( $bump['wps_upsell_bump_status'] ) && 'yes' === $bump['wps_upsell_bump_status'],
				'url'    => admin_url( 'admin.php?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting&bump_id=' . rawurlencode( (string) $id ) ),
			);
		}

		foreach ( (array) get_option( 'wps_wocuf_funnels_list', array() ) as $id => $funnel ) {
			$mode   = Upsell_Order_Bump_Reco_Public::targeting_mode( $funnel );
			$source = Upsell_Order_Bump_Reco_Public::product_source( $funnel );
			if ( 'static' === $mode && 'static' === $source ) {
				continue;
			}
			$out[] = array(
				'type'   => __( 'Upsell funnel', 'upsell-order-bump-offer-for-woocommerce' ),
				'name'   => ! empty( $funnel['wps_wocuf_funnel_name'] ) ? $funnel['wps_wocuf_funnel_name'] : '#' . $id,
				'mode'   => $labels['mode'][ $mode ],
				'source' => $labels['source'][ $source ],
				'live'   => ! empty( $funnel['wps_upsell_funnel_status'] ) && 'yes' === $funnel['wps_upsell_funnel_status'],
				'url'    => admin_url( 'admin.php?page=upsell-order-bump-offer-for-woocommerce-setting&tab=creation-setting-post&sub_tab=post-list-offer-section&funnel_id=' . rawurlencode( (string) $id ) . '&manage_nonce=' . wp_create_nonce( 'manage_funnel' ) ),
			);
		}
		return $out;
	}

	/**
	 * Option labels.
	 *
	 * @return array
	 */
	private function labels() {
		return array(
			'mode'   => array(
				'static' => __( 'Rule-based (target products / categories)', 'upsell-order-bump-offer-for-woocommerce' ),
				'smart'  => __( 'Smart — AI recommendations decide', 'upsell-order-bump-offer-for-woocommerce' ),
				'hybrid' => __( 'Hybrid — rules OR AI recommendations', 'upsell-order-bump-offer-for-woocommerce' ),
			),
			'source' => array(
				'static' => __( 'Use the selected offer product', 'upsell-order-bump-offer-for-woocommerce' ),
				'ai'     => __( 'AI-recommended product (falls back to the selected one)', 'upsell-order-bump-offer-for-woocommerce' ),
			),
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Per-offer fields (bump + funnel forms)
	|--------------------------------------------------------------------------
	*/

	/**
	 * Shared markup for both forms.
	 *
	 * @param array    $offer    Saved offer.
	 * @param callable $help_tip Help-tip renderer of the form.
	 * @param string   $kind     bump|funnel.
	 * @return void
	 */
	private function render_fields( $offer, $help_tip, $kind ) {
		$labels   = $this->labels();
		$mode     = Upsell_Order_Bump_Reco_Public::targeting_mode( $offer );
		$source   = Upsell_Order_Bump_Reco_Public::product_source( $offer );
		$enabled  = Upsell_Order_Bump_Reco_Db::is_enabled();
		$page_url = admin_url( 'admin.php?page=' . self::PAGE_SLUG );
		?>
		<tr valign="top" class="wps-ubo-reco-row">
			<th scope="row" class="titledesc">
				<label for="wps_ubo_targeting_mode"><?php esc_html_e( 'Targeting mode', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				call_user_func(
					$help_tip,
					'bump' === $kind
						? esc_html__( 'Rule-based shows this bump when a target product/category is in the cart. Smart shows it when customers who buy what is in the cart usually also buy the offer product (learned from your order history). Hybrid uses both.', 'upsell-order-bump-offer-for-woocommerce' )
						: esc_html__( 'Rule-based triggers this funnel for the target products/categories. Smart triggers it when the ordered products are usually bought together with (or before) the offer product, learned from your order history. Hybrid uses both.', 'upsell-order-bump-offer-for-woocommerce' )
				);
				?>
				<select id="wps_ubo_targeting_mode" name="wps_ubo_targeting_mode">
					<?php foreach ( $labels['mode'] as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $mode, $value ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr valign="top" class="wps-ubo-reco-row">
			<th scope="row" class="titledesc">
				<label for="wps_ubo_offer_product_source"><?php esc_html_e( 'Offer product', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
			</th>
			<td class="forminp forminp-text">
				<?php
				call_user_func(
					$help_tip,
					'bump' === $kind
						? esc_html__( 'AI-recommended picks the product most often bought with the current cart. The offer product selected below is used when there is no confident recommendation. A fixed offer price is not applied to a recommended product (it is shown at its regular price); percentage discounts apply.', 'upsell-order-bump-offer-for-woocommerce' )
						: esc_html__( 'AI-recommended fills each offer of this funnel with the next best product for the order (offer 1 = best match). The products selected in the offers are used when there are not enough recommendations. Fixed offer prices are not applied to recommended products; percentage discounts apply.', 'upsell-order-bump-offer-for-woocommerce' )
				);
				?>
				<select id="wps_ubo_offer_product_source" name="wps_ubo_offer_product_source">
					<?php foreach ( $labels['source'] as $value => $label ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $source, $value ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php if ( ! $enabled ) : ?>
					<p class="description">
						<?php
						printf(
							/* translators: %s: link to the Smart Recommendations page. */
							esc_html__( 'Smart Recommendations are switched off, so this offer uses rule-based targeting and the selected product. Turn them on in %s.', 'upsell-order-bump-offer-for-woocommerce' ),
							'<a href="' . esc_url( $page_url ) . '">' . esc_html__( 'Smart Recommendations', 'upsell-order-bump-offer-for-woocommerce' ) . '</a>'
						);
						?>
					</p>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Action: wps_ubo_bump_targeting_fields.
	 *
	 * @param int|string $bump_id Bump id.
	 * @param array      $bump    Saved bump.
	 * @return void
	 */
	public function render_bump_fields( $bump_id, $bump ) {
		$this->render_fields( (array) $bump, 'wps_ubo_lite_help_tip', 'bump' );
	}

	/**
	 * Action: wps_ubo_funnel_targeting_fields.
	 *
	 * @param int|string $funnel_id Funnel id.
	 * @param array      $funnel    Saved funnel.
	 * @return void
	 */
	public function render_funnel_fields( $funnel_id, $funnel ) {
		$this->render_fields( (array) $funnel, function_exists( 'wps_upsell_lite_wc_help_tip' ) ? 'wps_upsell_lite_wc_help_tip' : 'wps_ubo_lite_help_tip', 'funnel' );
	}

	/**
	 * Read the two fields from the (already nonce-verified) form POST.
	 *
	 * @param array $offer Offer being saved.
	 * @return array
	 */
	private function read_fields( $offer ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Called from the bump/funnel save handlers after check_admin_referer().
		$mode   = isset( $_POST['wps_ubo_targeting_mode'] ) ? sanitize_key( wp_unslash( $_POST['wps_ubo_targeting_mode'] ) ) : 'static';
		$source = isset( $_POST['wps_ubo_offer_product_source'] ) ? sanitize_key( wp_unslash( $_POST['wps_ubo_offer_product_source'] ) ) : 'static';
		// phpcs:enable

		$offer['wps_ubo_targeting_mode']       = in_array( $mode, Upsell_Order_Bump_Reco_Db::TARGETING_MODES, true ) ? $mode : 'static';
		$offer['wps_ubo_offer_product_source'] = in_array( $source, Upsell_Order_Bump_Reco_Db::PRODUCT_SOURCES, true ) ? $source : 'static';
		return $offer;
	}

	/**
	 * Filter: wps_ubo_bump_before_save.
	 *
	 * @param array      $bump    Bump.
	 * @param int|string $bump_id Bump id.
	 * @return array
	 */
	public function save_bump_fields( $bump, $bump_id ) {
		return $this->read_fields( (array) $bump );
	}

	/**
	 * Filter: wps_ubo_funnel_before_save.
	 *
	 * @param array      $funnel    Funnel.
	 * @param int|string $funnel_id Funnel id.
	 * @return array
	 */
	public function save_funnel_fields( $funnel, $funnel_id ) {
		return $this->read_fields( (array) $funnel );
	}

	/**
	 * Privacy policy suggestion.
	 *
	 * @return void
	 */
	public static function add_privacy_policy_content() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}
		$content = '<p>' . esc_html__( 'Product recommendations for checkout and post-purchase offers are calculated on this site from aggregated order history (which products are bought together). No personal data is stored by this feature beyond the order data WooCommerce already keeps.', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>'
			. '<p>' . esc_html__( 'If the AI enhancement is enabled, product catalog information only (product names, categories, prices and short descriptions) is sent to the AI provider configured under Settings → Connectors. No customer or order information is sent.', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>';

		wp_add_privacy_policy_content( __( 'Upsell Order Bump Offer — Smart Recommendations', 'upsell-order-bump-offer-for-woocommerce' ), wp_kses_post( $content ) );
	}
}

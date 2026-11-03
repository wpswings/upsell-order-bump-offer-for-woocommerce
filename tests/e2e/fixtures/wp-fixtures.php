<?php
/**
 * E2E fixture CLI. Seeds and restores the store the Playwright suite runs against.
 *
 * Usage: php wp-fixtures.php <command> [arg]
 * Every command prints one JSON document on stdout.
 *
 *   setup                 Back up plugin options, seed products/bump/admin user, enable features.
 *   teardown              Restore the backup and delete everything that was seeded.
 *   gamified <format|off> Turn the spin wheel / scratch card on or off for the seeded bump.
 *   acr-row <email>       Saved cart row for an email.
 *   acr-abandon <email>   Age the cart past the idle timeout and run the processor.
 *   acr-due <email>       Make the next recovery email due, run the processor, return the email.
 *   order <id>            Order status + line items with their plugin meta.
 *   heatmap-count         Heatmap events stored for the seeded bump.
 *   bump                  The seeded bump as stored.
 *
 * WARNING: setup changes the site's plugin settings until teardown runs.
 *
 * @package Upsell_Order_Bump_Offer_For_Woocommerce/Tests
 */

// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited, WordPress.DB.DirectDatabaseQuery, WordPress.Security.EscapeOutput.OutputNotEscaped

if ( 'cli' !== PHP_SAPI ) {
	exit( 1 );
}

$wps_root = getenv( 'WPS_E2E_WP_ROOT' ) ? getenv( 'WPS_E2E_WP_ROOT' ) : dirname( __DIR__, 6 );
if ( ! file_exists( $wps_root . '/wp-load.php' ) ) {
	fwrite( STDERR, "wp-load.php not found in {$wps_root}. Set WPS_E2E_WP_ROOT.\n" );
	exit( 1 );
}
define( 'WP_USE_THEMES', false );
$_SERVER['HTTP_HOST'] = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : 'localhost';
require $wps_root . '/wp-load.php';

const WPS_E2E_STATE   = 'wps_ubo_e2e_state';
const WPS_E2E_DOMAIN  = '@e2e.test';
const WPS_E2E_MISSING = '__wps_e2e_missing__';
const WPS_E2E_OPTIONS = array(
	'wps_ubo_bump_list',
	'wps_ubo_global_options',
	'wps_ubo_gamified_settings',
	'wps_ubo_gamified_stats',
	'wps_ubo_heatmap_settings',
	'wps_ubo_acr_settings',
	'wps_ubo_reco_settings',
	'wc_dynamic_discount_rules',
	'woocommerce_coming_soon',
	'wps_wocuf_funnels_list',
);

/**
 * Print JSON and stop.
 *
 * @param mixed $data Data.
 */
function wps_e2e_out( $data ) {
	echo wp_json_encode( $data ), "\n";
	exit( 0 );
}

/**
 * Seeded state.
 *
 * @return array
 */
function wps_e2e_state() {
	$state = get_option( WPS_E2E_STATE, array() );
	if ( empty( $state['products'] ) ) {
		fwrite( STDERR, "Not set up. Run: setup\n" );
		exit( 1 );
	}
	return $state;
}

/**
 * Cart recovery row by email.
 *
 * @param string $email Email.
 * @return array|null
 */
function wps_e2e_acr_row( $email ) {
	global $wpdb;
	$id = $wpdb->get_var( $wpdb->prepare( 'SELECT id FROM ' . Upsell_Order_Bump_Acr_Db::table() . ' WHERE email = %s ORDER BY id DESC LIMIT 1', strtolower( $email ) ) ); // phpcs:ignore
	return $id ? Upsell_Order_Bump_Acr_Db::get_by( 'id', $id ) : null;
}

/**
 * Create a product.
 *
 * @param string $name  Name.
 * @param string $price Regular price.
 * @return int
 */
function wps_e2e_product( $name, $price ) {
	$p = new WC_Product_Simple();
	$p->set_name( $name );
	$p->set_status( 'publish' );
	$p->set_regular_price( $price );
	$p->set_virtual( true ); // No shipping address needed at checkout.
	$p->update_meta_data( '_wps_ubo_e2e', 1 );
	return $p->save();
}

$wps_cmd = isset( $argv[1] ) ? $argv[1] : '';
$wps_arg = isset( $argv[2] ) ? $argv[2] : '';

// Emails sent while a command runs are captured instead of delivered.
$GLOBALS['wps_e2e_mails'] = array();
add_filter(
	'pre_wp_mail',
	static function ( $short, $atts ) {
		$GLOBALS['wps_e2e_mails'][] = $atts;
		return true;
	},
	10,
	2
);

switch ( $wps_cmd ) {
	case 'setup':
		if ( get_option( WPS_E2E_STATE ) ) {
			fwrite( STDERR, "Already set up; running teardown first would restore the backup.\n" );
			exit( 1 );
		}
		$backup = array();
		foreach ( WPS_E2E_OPTIONS as $name ) {
			$backup[ $name ] = get_option( $name, WPS_E2E_MISSING );
		}

		$products = array(
			'target'   => wps_e2e_product( 'E2E Camera', '200' ),
			'offer'    => wps_e2e_product( 'E2E Lens Cap', '50' ),
			'recovery' => wps_e2e_product( 'E2E Beanie', '20' ),
		);

		$password = wp_generate_password( 24, false );
		$login    = 'wps_e2e_admin_' . strtolower( wp_generate_password( 6, false ) );
		$user_id  = wp_insert_user(
			array(
				'user_login' => $login,
				'user_pass'  => $password,
				'user_email' => $login . WPS_E2E_DOMAIN,
				'role'       => 'administrator',
			)
		);

		update_option( 'woocommerce_coming_soon', 'no' );
		update_option( 'wps_ubo_global_options', array_merge( wps_ubo_lite_default_global_options(), array( 'wps_bump_popup_bump_offer' => 'without_popup' ) ) );
		delete_option( 'wc_dynamic_discount_rules' );
		// Existing upsell funnels would redirect orders away from the thank-you page.
		update_option( 'wps_wocuf_funnels_list', array() );
		update_option(
			'wps_ubo_bump_list',
			array(
				1 => array(
					'wps_upsell_bump_status'               => 'yes',
					'wps_upsell_bump_name'                 => 'E2E bump',
					'wps_upsell_bump_target_ids'           => array( (string) $products['target'] ),
					'wps_upsell_bump_target_categories'    => array(),
					'wps_upsell_bump_exclude_roles'        => array(),
					'wps_upsell_bump_schedule'             => array( '0' ),
					'wps_upsell_bump_products_in_offer'    => (string) $products['offer'],
					'wps_upsell_offer_price_type'          => '%',
					'wps_upsell_bump_offer_discount_price' => '20',
					'wps_bump_upsell_selected_template'    => '1',
					'wps_ubo_selected_template'            => '1',
					'wps_upsell_offer_image'               => '',
					'wps_ubo_offer_global_funnel'          => 'no',
					'wps_ubo_offer_replace_target'         => 'no',
					'design_css'                           => wps_ubo_lite_offer_template_1(),
					'design_text'                          => wps_ubo_lite_offer_default_text(),
				),
			)
		);
		update_option( Upsell_Order_Bump_Gamified::SETTINGS_OPTION, Upsell_Order_Bump_Gamified::sanitize_settings( array( 'enabled' => 'off' ) ) );
		delete_option( Upsell_Order_Bump_Gamified::STATS_OPTION );
		update_option( Upsell_Order_Bump_Heatmap_Db::SETTINGS_OPTION, array_merge( Upsell_Order_Bump_Heatmap_Db::default_settings(), array( 'enabled' => 'on', 'sample_rate' => 100 ) ) );
		update_option(
			Upsell_Order_Bump_Acr_Db::SETTINGS_OPTION,
			Upsell_Order_Bump_Acr_Db::sanitize_settings(
				array_merge(
					Upsell_Order_Bump_Acr_Db::default_settings(),
					array(
						'enabled'        => 'on',
						'capture_guests' => 'on',
						'checkout_offer' => 'on',
						'offer_source'   => 'fixed',
						'offer_product'  => $products['recovery'],
						'offer_discount' => 15,
					)
				)
			)
		);
		Upsell_Order_Bump_Acr_Db::reschedule();

		$state = array(
			'products' => $products,
			'user_id'  => $user_id,
			'backup'   => $backup,
		);
		update_option( WPS_E2E_STATE, $state, false );
		wps_e2e_out(
			array(
				'baseUrl'  => home_url( '/' ),
				'products' => $products,
				'admin'    => array(
					'user' => $login,
					'pass' => $password,
				),
			)
		);
		break;

	case 'teardown':
		global $wpdb;
		$state = get_option( WPS_E2E_STATE, array() );
		if ( ! empty( $state['backup'] ) ) {
			foreach ( $state['backup'] as $name => $value ) {
				if ( WPS_E2E_MISSING === $value ) {
					delete_option( $name );
				} else {
					update_option( $name, $value );
				}
			}
		}
		$orders = wc_get_orders(
			array(
				'limit'         => -1,
				'return'        => 'ids',
				'date_created'  => '>' . ( time() - DAY_IN_SECONDS ),
			)
		);
		$deleted_orders = 0;
		foreach ( $orders as $id ) {
			$order = wc_get_order( $id );
			if ( $order && false !== strpos( (string) $order->get_billing_email(), WPS_E2E_DOMAIN ) ) {
				$order->delete( true );
				++$deleted_orders;
			}
		}
		foreach ( $wpdb->get_col( 'SELECT coupon_code FROM ' . Upsell_Order_Bump_Acr_Db::table() . " WHERE email LIKE '%" . esc_sql( WPS_E2E_DOMAIN ) . "' AND coupon_code <> ''" ) as $code ) { // phpcs:ignore
			$coupon = new WC_Coupon( $code );
			if ( $coupon->get_id() ) {
				$coupon->delete( true );
			}
		}
		$wpdb->query( 'DELETE FROM ' . Upsell_Order_Bump_Acr_Db::table() . " WHERE email LIKE '%" . esc_sql( WPS_E2E_DOMAIN ) . "'" ); // phpcs:ignore
		$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . Upsell_Order_Bump_Heatmap_Db::table() . ' WHERE offer_type = %s AND offer_id = %d AND created_at > %s', 'bump', 1, gmdate( 'Y-m-d H:i:s', time() - DAY_IN_SECONDS ) ) ); // phpcs:ignore
		foreach ( wc_get_products( array( 'limit' => -1, 'status' => 'any', 'meta_key' => '_wps_ubo_e2e', 'return' => 'ids' ) ) as $pid ) { // phpcs:ignore WordPress.DB.SlowDBQuery.slow_query_meta_key
			wp_delete_post( $pid, true );
		}
		if ( ! empty( $state['user_id'] ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
			wp_delete_user( (int) $state['user_id'] );
		}
		Upsell_Order_Bump_Acr_Db::reschedule();
		delete_option( WPS_E2E_STATE );
		wps_e2e_out(
			array(
				'restored'       => ! empty( $state['backup'] ),
				'deleted_orders' => $deleted_orders,
			)
		);
		break;

	case 'gamified':
		wps_e2e_state();
		$on = in_array( $wps_arg, array( 'spin_wheel', 'scratch_card' ), true );
		update_option(
			Upsell_Order_Bump_Gamified::SETTINGS_OPTION,
			Upsell_Order_Bump_Gamified::sanitize_settings(
				array(
					'enabled'                  => $on ? 'on' : 'off',
					'formats'                  => $on ? array( 1 => $wps_arg ) : array(),
					'guarantee_offer_discount' => 'on',
					'allow_skip'               => 'on',
					'max_discount'             => 50,
				)
			)
		);
		wps_e2e_out( Upsell_Order_Bump_Gamified::get_settings() );
		break;

	case 'acr-row':
		wps_e2e_out( wps_e2e_acr_row( $wps_arg ) );
		break;

	case 'acr-abandon':
		$row = wps_e2e_acr_row( $wps_arg );
		if ( $row ) {
			Upsell_Order_Bump_Acr_Db::update( $row['id'], array( 'updated_at' => gmdate( 'Y-m-d H:i:s', time() - 2 * HOUR_IN_SECONDS ) ) );
			delete_transient( Upsell_Order_Bump_Acr_Engine::LOCK );
			Upsell_Order_Bump_Acr_Engine::process();
		}
		wps_e2e_out( wps_e2e_acr_row( $wps_arg ) );
		break;

	case 'acr-due':
		$row = wps_e2e_acr_row( $wps_arg );
		if ( $row ) {
			Upsell_Order_Bump_Acr_Db::update( $row['id'], array( 'next_email_at' => gmdate( 'Y-m-d H:i:s', time() - 60 ) ) );
			delete_transient( Upsell_Order_Bump_Acr_Engine::LOCK );
			Upsell_Order_Bump_Acr_Engine::process();
		}
		$mail = end( $GLOBALS['wps_e2e_mails'] );
		$row  = wps_e2e_acr_row( $wps_arg );
		wps_e2e_out(
			array(
				'row'         => $row,
				'subject'     => $mail ? $mail['subject'] : null,
				'hasOffer'    => $mail ? false !== strpos( $mail['message'], 'wps_ubo_acr_add=1' ) : false,
				'recoveryUrl' => $row ? Upsell_Order_Bump_Acr_Mailer::recovery_url( $row ) : null,
				'offerUrl'    => $row ? Upsell_Order_Bump_Acr_Mailer::recovery_url( $row, true ) : null,
				'unsubUrl'    => $row ? Upsell_Order_Bump_Acr_Mailer::unsubscribe_url( $row ) : null,
			)
		);
		break;

	case 'order':
		$order = wc_get_order( absint( $wps_arg ) );
		if ( ! $order ) {
			wps_e2e_out( null );
		}
		$items = array();
		foreach ( $order->get_items() as $item ) {
			$items[] = array(
				'name'       => $item->get_name(),
				'total'      => (float) $item->get_total(),
				'bump'       => $item->get_meta( 'is_order_bump_purchase' ),
				'acr_offer'  => $item->get_meta( '_wps_ubo_acr_offer' ),
				'game_prize' => $item->get_meta( '_wps_ubo_game_prize' ),
			);
		}
		wps_e2e_out(
			array(
				'status'        => $order->get_status(),
				'email'         => $order->get_billing_email(),
				'bump_order'    => $order->get_meta( 'wps_bump_order' ),
				'acr_recovered' => $order->get_meta( '_wps_ubo_acr_recovered' ),
				'items'         => $items,
			)
		);
		break;

	case 'heatmap-count':
		global $wpdb;
		wps_e2e_out(
			array(
				'count' => (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . Upsell_Order_Bump_Heatmap_Db::table() . ' WHERE offer_type = %s AND offer_id = %d AND created_at > %s', 'bump', 1, gmdate( 'Y-m-d H:i:s', time() - HOUR_IN_SECONDS ) ) ), // phpcs:ignore
			)
		);
		break;

	case 'bump':
		$list = get_option( 'wps_ubo_bump_list', array() );
		wps_e2e_out( isset( $list[1] ) ? $list[1] : null );
		break;

	default:
		fwrite( STDERR, "Unknown command: {$wps_cmd}\n" );
		exit( 1 );
}

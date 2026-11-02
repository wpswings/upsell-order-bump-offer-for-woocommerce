<?php
/**
 * Native cart abandonment upsell recovery - storage, settings and statistics.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      3.1.8
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/includes
 * @author     WP Swings <webmaster@wpswings.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Table, settings and queries for abandoned carts.
 *
 * @since 3.1.8
 */
class Upsell_Order_Bump_Acr_Db {

	/**
	 * Schema version.
	 *
	 * @var string
	 */
	const DB_VERSION = '1.0.0';

	/**
	 * Option names.
	 *
	 * @var string
	 */
	const DB_VERSION_OPTION = 'wps_ubo_acr_db_version';
	const SETTINGS_OPTION   = 'wps_ubo_acr_settings';

	/**
	 * Cron hook and Action Scheduler group.
	 *
	 * @var string
	 */
	const CRON_HOOK = 'wps_ubo_acr_process';
	const AS_GROUP  = 'wps-ubo-acr';

	/**
	 * Statuses.
	 *
	 * @var string[]
	 */
	const STATUSES = array( 'active', 'abandoned', 'recovered', 'lost', 'unsubscribed' );

	/**
	 * Table name.
	 *
	 * @return string
	 */
	public static function table() {
		global $wpdb;
		return $wpdb->prefix . 'wps_ubo_acr_carts';
	}

	/**
	 * Default email steps.
	 *
	 * @return array[]
	 */
	public static function default_steps() {
		return array(
			array(
				'enabled'       => 'on',
				'delay'         => 1,
				'unit'          => 'hours',
				'subject'       => __( 'You left something in your cart', 'upsell-order-bump-offer-for-woocommerce' ),
				'heading'       => __( 'Your cart is waiting for you', 'upsell-order-bump-offer-for-woocommerce' ),
				'body'          => __( "Hi {customer_name},\n\nWe noticed you left a few things in your cart at {site_name}. We saved them for you:\n\n{cart_items}\n\n{recovery_button}\n\nIf you have any questions, just reply to this email.", 'upsell-order-bump-offer-for-woocommerce' ),
				'include_offer' => 'off',
				'discount'      => '',
				'coupon'        => 0,
			),
			array(
				'enabled'       => 'on',
				'delay'         => 1,
				'unit'          => 'days',
				'subject'       => __( "Still thinking? Here's something extra for you", 'upsell-order-bump-offer-for-woocommerce' ),
				'heading'       => __( 'A little something extra', 'upsell-order-bump-offer-for-woocommerce' ),
				'body'          => __( "Hi {customer_name},\n\nYour cart is still saved:\n\n{cart_items}\n\nComplete your order today and add this at an exclusive {offer_discount} off:\n\n{offer_block}\n\n{recovery_button}", 'upsell-order-bump-offer-for-woocommerce' ),
				'include_offer' => 'on',
				'discount'      => '',
				'coupon'        => 0,
			),
			array(
				'enabled'       => 'on',
				'delay'         => 3,
				'unit'          => 'days',
				'subject'       => __( 'Last chance: your cart and an exclusive offer', 'upsell-order-bump-offer-for-woocommerce' ),
				'heading'       => __( 'Last chance to save', 'upsell-order-bump-offer-for-woocommerce' ),
				'body'          => __( "Hi {customer_name},\n\nThis is the last reminder about your cart. Use code {coupon_code} for {coupon_percent} off your order:\n\n{cart_items}\n\nAnd don't miss your exclusive offer:\n\n{offer_block}\n\n{recovery_button}", 'upsell-order-bump-offer-for-woocommerce' ),
				'include_offer' => 'on',
				'discount'      => '',
				'coupon'        => 10,
			),
		);
	}

	/**
	 * Default settings.
	 *
	 * @return array
	 */
	public static function default_settings() {
		return array(
			'enabled'            => 'off',
			'abandon_minutes'    => 30,
			'capture_guests'     => 'on',
			'consent_notice'     => __( 'We save your cart and email so we can send you a reminder if you don\'t finish your order.', 'upsell-order-bump-offer-for-woocommerce' ),
			'exclude_roles'      => array( 'administrator', 'shop_manager' ),
			'offer_source'       => 'auto',
			'offer_product'      => 0,
			'offer_discount'     => 15,
			'checkout_offer'     => 'on',
			'coupon_expiry_days' => 7,
			'lost_after_days'    => 7,
			'retention_days'     => 90,
			'steps'              => self::default_steps(),
		);
	}

	/**
	 * Settings merged with defaults.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$saved = get_option( self::SETTINGS_OPTION, array() );
		return wp_parse_args( is_array( $saved ) ? $saved : array(), self::default_settings() );
	}

	/**
	 * Whether the feature is on.
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		$settings = self::get_settings();
		return 'on' === $settings['enabled'];
	}

	/**
	 * Sanitize and clamp settings.
	 *
	 * @param array $raw Raw input.
	 * @return array
	 */
	public static function sanitize_settings( $raw ) {
		$d   = self::default_settings();
		$raw = is_array( $raw ) ? $raw : array();
		$on  = static function ( $value ) {
			return 'on' === $value ? 'on' : 'off';
		};
		$int = static function ( $key, $min, $max ) use ( $raw, $d ) {
			$v = isset( $raw[ $key ] ) && is_numeric( $raw[ $key ] ) ? (int) $raw[ $key ] : $d[ $key ];
			return max( $min, min( $max, $v ) );
		};

		$roles = array();
		if ( ! empty( $raw['exclude_roles'] ) && is_array( $raw['exclude_roles'] ) ) {
			$all = function_exists( 'wp_roles' ) ? array_keys( wp_roles()->roles ) : array();
			foreach ( $raw['exclude_roles'] as $role ) {
				$role = sanitize_key( $role );
				if ( in_array( $role, $all, true ) ) {
					$roles[] = $role;
				}
			}
		}

		$steps    = array();
		$defaults = self::default_steps();
		$in_steps = ! empty( $raw['steps'] ) && is_array( $raw['steps'] ) ? array_values( $raw['steps'] ) : array();
		foreach ( $defaults as $i => $default ) {
			$step    = isset( $in_steps[ $i ] ) && is_array( $in_steps[ $i ] ) ? $in_steps[ $i ] : array();
			$unit    = isset( $step['unit'] ) && in_array( $step['unit'], array( 'minutes', 'hours', 'days' ), true ) ? $step['unit'] : $default['unit'];
			$subject = isset( $step['subject'] ) ? sanitize_text_field( $step['subject'] ) : '';
			$heading = isset( $step['heading'] ) ? sanitize_text_field( $step['heading'] ) : '';
			$body    = isset( $step['body'] ) ? wp_kses_post( $step['body'] ) : '';
			$disc    = isset( $step['discount'] ) && '' !== trim( (string) $step['discount'] ) && is_numeric( $step['discount'] ) ? max( 1, min( 90, (int) $step['discount'] ) ) : '';

			$steps[] = array(
				'enabled'       => $on( isset( $step['enabled'] ) ? $step['enabled'] : 'off' ),
				'delay'         => max( 1, min( 999, isset( $step['delay'] ) && is_numeric( $step['delay'] ) ? (int) $step['delay'] : (int) $default['delay'] ) ),
				'unit'          => $unit,
				'subject'       => '' !== $subject ? $subject : $default['subject'],
				'heading'       => '' !== $heading ? $heading : $default['heading'],
				'body'          => '' !== trim( $body ) ? $body : $default['body'],
				'include_offer' => $on( isset( $step['include_offer'] ) ? $step['include_offer'] : 'off' ),
				'discount'      => $disc,
				'coupon'        => max( 0, min( 90, isset( $step['coupon'] ) && is_numeric( $step['coupon'] ) ? (int) $step['coupon'] : 0 ) ),
			);
		}

		$source = isset( $raw['offer_source'] ) && in_array( $raw['offer_source'], array( 'auto', 'smart', 'bump', 'fixed' ), true ) ? $raw['offer_source'] : $d['offer_source'];

		return array(
			'enabled'            => $on( isset( $raw['enabled'] ) ? $raw['enabled'] : 'off' ),
			'abandon_minutes'    => $int( 'abandon_minutes', 5, 1440 ),
			'capture_guests'     => $on( isset( $raw['capture_guests'] ) ? $raw['capture_guests'] : 'off' ),
			'consent_notice'     => isset( $raw['consent_notice'] ) ? mb_substr( sanitize_text_field( $raw['consent_notice'] ), 0, 300 ) : '',
			'exclude_roles'      => $roles,
			'offer_source'       => $source,
			'offer_product'      => isset( $raw['offer_product'] ) ? absint( $raw['offer_product'] ) : 0,
			'offer_discount'     => $int( 'offer_discount', 0, 90 ),
			'checkout_offer'     => $on( isset( $raw['checkout_offer'] ) ? $raw['checkout_offer'] : 'off' ),
			'coupon_expiry_days' => $int( 'coupon_expiry_days', 1, 90 ),
			'lost_after_days'    => $int( 'lost_after_days', 1, 60 ),
			'retention_days'     => $int( 'retention_days', 7, 730 ),
			'steps'              => $steps,
		);
	}

	/**
	 * Delay of a step in seconds.
	 *
	 * @param array $step Step.
	 * @return int
	 */
	public static function step_seconds( $step ) {
		$units = array(
			'minutes' => MINUTE_IN_SECONDS,
			'hours'   => HOUR_IN_SECONDS,
			'days'    => DAY_IN_SECONDS,
		);
		return (int) $step['delay'] * ( isset( $units[ $step['unit'] ] ) ? $units[ $step['unit'] ] : HOUR_IN_SECONDS );
	}

	/**
	 * Index of the next enabled step at or after $from, or -1.
	 *
	 * @param int $from Step index.
	 * @return int
	 */
	public static function next_step_index( $from ) {
		$settings = self::get_settings();
		foreach ( $settings['steps'] as $i => $step ) {
			if ( $i >= $from && 'on' === $step['enabled'] ) {
				return (int) $i;
			}
		}
		return -1;
	}

	/*
	|--------------------------------------------------------------------------
	| Install
	|--------------------------------------------------------------------------
	*/

	/**
	 * Create / upgrade the table.
	 *
	 * @return void
	 */
	public static function install() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table   = self::table();
		$collate = $wpdb->get_charset_collate();

		dbDelta(
			"CREATE TABLE {$table} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  session_key varchar(64) NOT NULL DEFAULT '',
  user_id bigint(20) unsigned NOT NULL DEFAULT 0,
  email varchar(190) NOT NULL DEFAULT '',
  first_name varchar(100) NOT NULL DEFAULT '',
  cart longtext NOT NULL,
  cart_total decimal(19,4) NOT NULL DEFAULT 0,
  currency char(3) NOT NULL DEFAULT '',
  status varchar(20) NOT NULL DEFAULT 'active',
  token char(32) NOT NULL DEFAULT '',
  emails_sent tinyint(3) unsigned NOT NULL DEFAULT 0,
  next_email_at datetime DEFAULT NULL,
  last_email_at datetime DEFAULT NULL,
  offer_product bigint(20) unsigned NOT NULL DEFAULT 0,
  offer_discount tinyint(3) unsigned NOT NULL DEFAULT 0,
  offer_source varchar(20) NOT NULL DEFAULT '',
  coupon_code varchar(50) NOT NULL DEFAULT '',
  clicked_at datetime DEFAULT NULL,
  order_id bigint(20) unsigned NOT NULL DEFAULT 0,
  recovered_total decimal(19,4) NOT NULL DEFAULT 0,
  recovered_via varchar(10) NOT NULL DEFAULT '',
  offer_accepted tinyint(1) unsigned NOT NULL DEFAULT 0,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  abandoned_at datetime DEFAULT NULL,
  recovered_at datetime DEFAULT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY session_key (session_key),
  UNIQUE KEY token (token),
  KEY email (email),
  KEY status_updated (status,updated_at),
  KEY status_next (status,next_email_at)
) {$collate};"
		);

		update_option( self::DB_VERSION_OPTION, self::DB_VERSION, false );
		if ( false === get_option( self::SETTINGS_OPTION, false ) ) {
			add_option( self::SETTINGS_OPTION, self::default_settings(), '', false );
		}
		self::reschedule();
	}

	/**
	 * Install when the schema is outdated.
	 *
	 * @return void
	 */
	public static function maybe_upgrade() {
		if ( get_option( self::DB_VERSION_OPTION ) !== self::DB_VERSION ) {
			self::install();
		}
	}

	/**
	 * Schedule processing while enabled, unschedule otherwise.
	 *
	 * @return void
	 */
	public static function reschedule() {
		self::unschedule();
		if ( ! self::is_enabled() ) {
			return;
		}
		if ( function_exists( 'as_schedule_recurring_action' ) ) {
			as_schedule_recurring_action( time() + 60, 5 * MINUTE_IN_SECONDS, self::CRON_HOOK, array(), self::AS_GROUP );
		} else {
			wp_schedule_event( time() + 60, 'wps_ubo_acr_5min', self::CRON_HOOK );
		}
	}

	/**
	 * Remove schedules.
	 *
	 * @return void
	 */
	public static function unschedule() {
		if ( function_exists( 'as_unschedule_all_actions' ) ) {
			as_unschedule_all_actions( self::CRON_HOOK, array(), self::AS_GROUP );
		}
		wp_clear_scheduled_hook( self::CRON_HOOK );
	}

	/**
	 * Filter: cron_schedules — 5 minute interval for the WP-Cron fallback.
	 *
	 * @param array $schedules Schedules.
	 * @return array
	 */
	public static function cron_schedules( $schedules ) {
		$schedules['wps_ubo_acr_5min'] = array(
			'interval' => 5 * MINUTE_IN_SECONDS,
			'display'  => __( 'Every 5 minutes (cart recovery)', 'upsell-order-bump-offer-for-woocommerce' ),
		);
		return $schedules;
	}

	/**
	 * Drop everything.
	 *
	 * @return void
	 */
	public static function uninstall() {
		global $wpdb;
		self::unschedule();
		$table = self::table();
		$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange
		delete_option( self::DB_VERSION_OPTION );
		delete_option( self::SETTINGS_OPTION );
	}

	/*
	|--------------------------------------------------------------------------
	| Rows
	|--------------------------------------------------------------------------
	*/

	/**
	 * Current GMT datetime.
	 *
	 * @param int $offset Seconds to add.
	 * @return string
	 */
	public static function now( $offset = 0 ) {
		return gmdate( 'Y-m-d H:i:s', time() + $offset );
	}

	/**
	 * Get a row by column.
	 *
	 * @param string $column id|session_key|token.
	 * @param mixed  $value  Value.
	 * @return array|null
	 */
	public static function get_by( $column, $value ) {
		global $wpdb;
		if ( ! in_array( $column, array( 'id', 'session_key', 'token' ), true ) || '' === (string) $value ) {
			return null;
		}
		$table = self::table();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE {$column} = %s LIMIT 1", (string) $value ), ARRAY_A );
		return $row ? self::hydrate( $row ) : null;
	}

	/**
	 * Decode JSON columns.
	 *
	 * @param array $row Row.
	 * @return array
	 */
	private static function hydrate( $row ) {
		$cart        = json_decode( (string) $row['cart'], true );
		$row['cart'] = is_array( $cart ) ? $cart : array();
		return $row;
	}

	/**
	 * Insert a row.
	 *
	 * @param array $data Columns.
	 * @return int Id.
	 */
	public static function insert( $data ) {
		global $wpdb;
		$now  = self::now();
		$data = array_merge(
			array(
				'token'      => strtolower( wp_generate_password( 32, false ) ), // Lowercase: links are case-insensitive.
				'status'     => 'active',
				'created_at' => $now,
				'updated_at' => $now,
			),
			$data
		);
		if ( isset( $data['cart'] ) && is_array( $data['cart'] ) ) {
			$data['cart'] = wp_json_encode( $data['cart'] );
		}
		$wpdb->insert( self::table(), $data ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return (int) $wpdb->insert_id;
	}

	/**
	 * Update a row.
	 *
	 * @param int   $id   Id.
	 * @param array $data Columns.
	 * @return void
	 */
	public static function update( $id, $data ) {
		global $wpdb;
		if ( isset( $data['cart'] ) && is_array( $data['cart'] ) ) {
			$data['cart'] = wp_json_encode( $data['cart'] );
		}
		$wpdb->update( self::table(), $data, array( 'id' => (int) $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	}

	/**
	 * Delete a row.
	 *
	 * @param int $id Id.
	 * @return void
	 */
	public static function delete( $id ) {
		global $wpdb;
		$wpdb->delete( self::table(), array( 'id' => (int) $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	}

	/**
	 * Whether an email has unsubscribed.
	 *
	 * @param string $email Email.
	 * @return bool
	 */
	public static function is_unsubscribed( $email ) {
		global $wpdb;
		$table = self::table();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
		return (bool) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE email = %s AND status = 'unsubscribed' LIMIT 1", strtolower( $email ) ) );
	}

	/**
	 * Latest abandoned / lost row for an email.
	 *
	 * @param string $email Email.
	 * @return array|null
	 */
	public static function open_row_for_email( $email ) {
		global $wpdb;
		$table = self::table();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE email = %s AND status IN ('abandoned','lost') ORDER BY abandoned_at DESC LIMIT 1", strtolower( $email ) ), ARRAY_A );
		return $row ? self::hydrate( $row ) : null;
	}

	/**
	 * Rows for the admin list.
	 *
	 * @param string $status Status filter ('' = all).
	 * @param int    $page   Page (1-based).
	 * @param int    $per    Per page.
	 * @return array { rows, total }
	 */
	public static function list_rows( $status, $page, $per = 20 ) {
		global $wpdb;
		$table  = self::table();
		$where  = in_array( $status, self::STATUSES, true ) ? $wpdb->prepare( 'WHERE status = %s', $status ) : "WHERE email <> ''";
		$offset = max( 0, ( $page - 1 ) * $per );
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
		$rows  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} {$where} ORDER BY updated_at DESC LIMIT %d, %d", $offset, $per ), ARRAY_A );
		$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} {$where}" );
		// phpcs:enable
		return array(
			'rows'  => array_map( array( __CLASS__, 'hydrate' ), (array) $rows ),
			'total' => $total,
		);
	}

	/**
	 * Dashboard statistics for the last N days.
	 *
	 * @param int $days Days.
	 * @return array
	 */
	public static function stats( $days = 30 ) {
		global $wpdb;
		$table = self::table();
		$since = self::now( -$days * DAY_IN_SECONDS );
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT
					SUM(abandoned_at IS NOT NULL) AS abandoned,
					SUM(status = 'recovered') AS recovered,
					SUM(status = 'recovered' AND recovered_via = 'link') AS via_link,
					SUM(CASE WHEN status = 'recovered' THEN recovered_total ELSE 0 END) AS revenue,
					SUM(emails_sent) AS emails,
					SUM(offer_product > 0) AS offers_shown,
					SUM(offer_accepted) AS offers_accepted,
					SUM(status = 'lost') AS lost
				FROM {$table} WHERE abandoned_at >= %s",
				$since
			),
			ARRAY_A
		);
		$row = array_map( 'floatval', (array) $row );
		return array(
			'abandoned'       => (int) $row['abandoned'],
			'recovered'       => (int) $row['recovered'],
			'via_link'        => (int) $row['via_link'],
			'revenue'         => (float) $row['revenue'],
			'emails'          => (int) $row['emails'],
			'offers_shown'    => (int) $row['offers_shown'],
			'offers_accepted' => (int) $row['offers_accepted'],
			'lost'            => (int) $row['lost'],
			'rate'            => $row['abandoned'] > 0 ? round( 100 * $row['recovered'] / $row['abandoned'], 1 ) : 0,
			'offer_rate'      => $row['offers_shown'] > 0 ? round( 100 * $row['offers_accepted'] / $row['offers_shown'], 1 ) : 0,
		);
	}
}

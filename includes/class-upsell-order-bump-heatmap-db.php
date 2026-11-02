<?php
/**
 * Offer Heatmaps - storage layer.
 *
 * Handles table creation/upgrade, inserting tracked events, aggregate
 * queries used by the admin heatmap view and data retention.
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
 * Offer Heatmaps - storage layer.
 *
 * @since 3.1.8
 */
class Upsell_Order_Bump_Heatmap_Db {

	/**
	 * Schema version. Bump when the table definition changes.
	 *
	 * @var string
	 */
	const DB_VERSION = '1.0.0';

	/**
	 * Option holding installed schema version.
	 *
	 * @var string
	 */
	const DB_VERSION_OPTION = 'wps_ubo_heatmap_db_version';

	/**
	 * Option holding heatmap settings.
	 *
	 * @var string
	 */
	const SETTINGS_OPTION = 'wps_ubo_heatmap_settings';

	/**
	 * Daily purge cron hook.
	 *
	 * @var string
	 */
	const PURGE_CRON = 'wps_ubo_heatmap_purge';

	/**
	 * Allowed values.
	 *
	 * @var array
	 */
	const OFFER_TYPES = array( 'bump', 'upsell' );
	const EVENT_TYPES = array( 'view', 'click', 'hover' );
	const DEVICES     = array( 'desktop', 'tablet', 'mobile' );
	const CONTEXTS    = array( 'checkout', 'cart', 'checkout_block', 'cart_block', 'shortcode', 'popup', 'upsell_page' );

	/**
	 * Full table name.
	 *
	 * @return string
	 */
	public static function table() {
		global $wpdb;
		return $wpdb->prefix . 'wps_ubo_heatmap_events';
	}

	/**
	 * Default settings.
	 *
	 * @return array
	 */
	public static function default_settings() {
		return array(
			'enabled'        => 'on',
			'track_hover'    => 'on',
			'sample_rate'    => 100,
			'retention_days' => 90,
		);
	}

	/**
	 * Saved settings merged with defaults.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$saved = get_option( self::SETTINGS_OPTION, array() );
		return wp_parse_args( is_array( $saved ) ? $saved : array(), self::default_settings() );
	}

	/**
	 * Create / upgrade the events table.
	 *
	 * @return void
	 */
	public static function install() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table           = self::table();
		$charset_collate = $wpdb->get_charset_collate();

		// dbDelta requires two spaces after PRIMARY KEY and one field per line.
		$sql = "CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			offer_type varchar(10) NOT NULL DEFAULT '',
			offer_id bigint(20) unsigned NOT NULL DEFAULT 0,
			sub_offer_id bigint(20) unsigned NOT NULL DEFAULT 0,
			template varchar(20) NOT NULL DEFAULT '',
			event_type varchar(10) NOT NULL DEFAULT '',
			x smallint(5) unsigned NOT NULL DEFAULT 0,
			y smallint(5) unsigned NOT NULL DEFAULT 0,
			zone varchar(30) NOT NULL DEFAULT 'other',
			device varchar(10) NOT NULL DEFAULT 'desktop',
			context varchar(20) NOT NULL DEFAULT '',
			session_hash char(32) NOT NULL DEFAULT '',
			created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY  (id),
			KEY offer_lookup (offer_type,offer_id,sub_offer_id,event_type,created_at),
			KEY created_at (created_at)
		) {$charset_collate};";

		dbDelta( $sql );

		update_option( self::DB_VERSION_OPTION, self::DB_VERSION, false );

		if ( ! get_option( self::SETTINGS_OPTION ) ) {
			add_option( self::SETTINGS_OPTION, self::default_settings(), '', false );
		}

		self::schedule_purge();
	}

	/**
	 * Install when the stored schema version is outdated (plugin updated without re-activation).
	 *
	 * @return void
	 */
	public static function maybe_upgrade() {
		if ( self::DB_VERSION !== get_option( self::DB_VERSION_OPTION ) ) {
			self::install();
		}
	}

	/**
	 * Ensure the daily purge event exists.
	 *
	 * @return void
	 */
	public static function schedule_purge() {
		if ( ! wp_next_scheduled( self::PURGE_CRON ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', self::PURGE_CRON );
		}
	}

	/**
	 * Remove the purge cron (deactivation).
	 *
	 * @return void
	 */
	public static function unschedule_purge() {
		wp_clear_scheduled_hook( self::PURGE_CRON );
	}

	/**
	 * Drop table and options (uninstall).
	 *
	 * @return void
	 */
	public static function uninstall() {
		global $wpdb;
		$table = self::table();
		$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
		delete_option( self::DB_VERSION_OPTION );
		delete_option( self::SETTINGS_OPTION );
		self::unschedule_purge();
	}

	/**
	 * Insert sanitized rows in one multi-row statement.
	 *
	 * @param array $rows Sanitized rows (see Upsell_Order_Bump_Heatmap_Public::sanitize_event()).
	 * @return int Number of rows inserted.
	 */
	public static function insert_events( $rows ) {
		global $wpdb;

		if ( empty( $rows ) ) {
			return 0;
		}

		$table        = self::table();
		$placeholders = array();
		$values       = array();
		$now          = current_time( 'mysql', true );

		foreach ( $rows as $row ) {
			$placeholders[] = '(%s,%d,%d,%s,%s,%d,%d,%s,%s,%s,%s,%s)';
			array_push(
				$values,
				$row['offer_type'],
				$row['offer_id'],
				$row['sub_offer_id'],
				$row['template'],
				$row['event_type'],
				$row['x'],
				$row['y'],
				$row['zone'],
				$row['device'],
				$row['context'],
				$row['session_hash'],
				$now
			);
		}

		$sql = "INSERT INTO {$table} (offer_type,offer_id,sub_offer_id,template,event_type,x,y,zone,device,context,session_hash,created_at) VALUES " . implode( ',', $placeholders );

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
		$result = $wpdb->query( $wpdb->prepare( $sql, $values ) );

		return false === $result ? 0 : (int) $result;
	}

	/**
	 * Build the shared WHERE clause for admin queries.
	 *
	 * @param array $filters Sanitized filters.
	 * @return array [ string $where_sql, array $args ]
	 */
	private static function build_where( $filters ) {
		$where = array( 'offer_type = %s', 'offer_id = %d', 'sub_offer_id = %d', 'created_at >= %s', 'created_at <= %s' );
		$args  = array(
			$filters['offer_type'],
			$filters['offer_id'],
			$filters['sub_offer_id'],
			$filters['date_from'],
			$filters['date_to'],
		);

		if ( ! empty( $filters['device'] ) ) {
			$where[] = 'device = %s';
			$args[]  = $filters['device'];
		}

		if ( '' !== (string) $filters['template'] ) {
			$where[] = 'template = %s';
			$args[]  = $filters['template'];
		}

		return array( implode( ' AND ', $where ), $args );
	}

	/**
	 * Points binned to a 100x100 grid.
	 *
	 * @param array  $filters    Filters.
	 * @param string $event_type click|hover.
	 * @return array List of [ gx, gy, count ].
	 */
	public static function get_grid( $filters, $event_type ) {
		global $wpdb;

		list( $where, $args ) = self::build_where( $filters );
		$args[]               = $event_type;
		$table                = self::table();

		$sql = "SELECT FLOOR(x/100) AS gx, FLOOR(y/100) AS gy, COUNT(*) AS c
			FROM {$table}
			WHERE {$where} AND event_type = %s
			GROUP BY gx, gy";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
		$rows = $wpdb->get_results( $wpdb->prepare( $sql, $args ), ARRAY_N );

		$points = array();
		foreach ( (array) $rows as $row ) {
			$points[] = array( min( 99, (int) $row[0] ), min( 99, (int) $row[1] ), (int) $row[2] );
		}
		return $points;
	}

	/**
	 * Zone x event-type counts.
	 *
	 * @param array $filters Filters.
	 * @return array zone => [ click => n, hover => n ].
	 */
	public static function get_zones( $filters ) {
		global $wpdb;

		list( $where, $args ) = self::build_where( $filters );
		$table                = self::table();

		$sql = "SELECT zone, event_type, COUNT(*) AS c
			FROM {$table}
			WHERE {$where} AND event_type IN ('click','hover')
			GROUP BY zone, event_type";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
		$rows = $wpdb->get_results( $wpdb->prepare( $sql, $args ), ARRAY_A );

		$zones = array();
		foreach ( (array) $rows as $row ) {
			$zone = $row['zone'];
			if ( ! isset( $zones[ $zone ] ) ) {
				$zones[ $zone ] = array(
					'click' => 0,
					'hover' => 0,
				);
			}
			$zones[ $zone ][ $row['event_type'] ] = (int) $row['c'];
		}
		return $zones;
	}

	/**
	 * Totals for summary cards.
	 *
	 * @param array $filters Filters.
	 * @return array
	 */
	public static function get_totals( $filters ) {
		global $wpdb;

		list( $where, $args ) = self::build_where( $filters );
		$table                = self::table();

		$sql = "SELECT
				SUM(event_type = 'view')  AS views,
				SUM(event_type = 'click') AS clicks,
				SUM(event_type = 'hover') AS hovers,
				COUNT(DISTINCT session_hash) AS visitors
			FROM {$table}
			WHERE {$where}";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery
		$row = $wpdb->get_row( $wpdb->prepare( $sql, $args ), ARRAY_A );

		return array(
			'views'    => (int) ( $row['views'] ?? 0 ),
			'clicks'   => (int) ( $row['clicks'] ?? 0 ),
			'hovers'   => (int) ( $row['hovers'] ?? 0 ),
			'visitors' => (int) ( $row['visitors'] ?? 0 ),
		);
	}

	/**
	 * Distinct templates that have data for an offer (lets admins compare designs).
	 *
	 * @param string $offer_type   Offer type.
	 * @param int    $offer_id     Offer id.
	 * @param int    $sub_offer_id Sub offer id.
	 * @return array
	 */
	public static function get_templates_with_data( $offer_type, $offer_id, $sub_offer_id ) {
		global $wpdb;
		$table = self::table();

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
		return $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT template FROM {$table} WHERE offer_type = %s AND offer_id = %d AND sub_offer_id = %d", $offer_type, $offer_id, $sub_offer_id ) );
	}

	/**
	 * Delete events older than the retention period.
	 *
	 * @return void
	 */
	public static function purge_old() {
		global $wpdb;

		$settings = self::get_settings();
		$days     = max( 1, absint( $settings['retention_days'] ) );
		$cutoff   = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );
		$table    = self::table();

		// Delete in chunks to avoid long table locks on big stores.
		do {
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
			$deleted = $wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE created_at < %s LIMIT 5000", $cutoff ) );
		} while ( $deleted >= 5000 );
	}

	/**
	 * Delete everything.
	 *
	 * @return void
	 */
	public static function truncate() {
		global $wpdb;
		$table = self::table();
		$wpdb->query( "TRUNCATE TABLE {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
	}
}

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
	 * Upsell funnels in one shape for the heatmap, whichever plugin runs them.
	 *
	 * With the Pro plugin active the live funnels are saved in wps_wocuf_pro_funnels_list
	 * with wps_wocuf_pro_* keys; the free plugin uses wps_wocuf_funnels_list.
	 *
	 * @return array funnel id => { name, offers: offer id => product id, template, custom_url, page }.
	 */
	public static function get_funnels() {
		$pro    = function_exists( 'wps_ubo_lite_if_pro_exists' ) && wps_ubo_lite_if_pro_exists();
		$list   = $pro ? get_option( 'wps_wocuf_pro_funnels_list', array() ) : array();
		$prefix = 'wps_wocuf_pro_';
		if ( empty( $list ) || ! is_array( $list ) ) {
			$list   = get_option( 'wps_wocuf_funnels_list', array() );
			$prefix = 'wps_wocuf_';
		}

		$funnels = array();
		foreach ( is_array( $list ) ? $list : array() as $funnel_id => $funnel ) {
			if ( ! is_array( $funnel ) ) {
				continue;
			}
			$field = static function ( $key ) use ( $funnel, $prefix ) {
				foreach ( array( $prefix . $key, 'wps_wocuf_' . $key, 'wps_wocuf_pro_' . $key ) as $name ) {
					if ( isset( $funnel[ $name ] ) ) {
						return $funnel[ $name ];
					}
				}
				return array();
			};

			$name = $field( 'funnel_name' );

			$funnels[ absint( $funnel_id ) ] = array(
				'name'       => is_string( $name ) ? $name : '',
				'offers'     => (array) $field( 'products_in_offer' ),
				// Pro saves the template under the wps_wocuf_pro_ key in both plugins.
				'template'   => (array) ( isset( $funnel['wps_wocuf_pro_offer_template'] ) ? $funnel['wps_wocuf_pro_offer_template'] : array() ),
				'custom_url' => (array) $field( 'offer_custom_page_url' ),
				'page'       => (array) ( isset( $funnel['wps_upsell_post_id_assigned'] ) ? $funnel['wps_upsell_post_id_assigned'] : array() ),
			);
		}

		return $funnels;
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
		// admin_init also runs for every admin-ajax.php request; upgrades only need page loads.
		if ( wp_doing_ajax() ) {
			return;
		}
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
		$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $table ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
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

		$values = array( self::table() );
		$now    = current_time( 'mysql', true );

		foreach ( $rows as $row ) {
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

		// One ( ... ) group per row; the table name is the first value ( %i ).
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$result = $wpdb->query( $wpdb->prepare( 'INSERT INTO %i (offer_type,offer_id,sub_offer_id,template,event_type,x,y,zone,device,context,session_hash,created_at) VALUES ' . implode( ',', array_fill( 0, count( $rows ), '(%s,%d,%d,%s,%s,%d,%d,%s,%s,%s,%s,%s)' ) ), $values ) );

		return false === $result ? 0 : (int) $result;
	}

	/**
	 * Values for the shared WHERE clause of the admin queries ( after the table name ):
	 * offer_type = %s AND offer_id = %d AND sub_offer_id = %d AND created_at >= %s AND
	 * created_at <= %s AND ( %s = '' OR device = %s ) AND ( %s = '' OR template = %s ).
	 * An empty device / template means "any".
	 *
	 * @param string $table   Table name.
	 * @param array  $filters Sanitized filters.
	 * @return array
	 */
	private static function filter_args( $table, $filters ) {
		$device   = ! empty( $filters['device'] ) ? (string) $filters['device'] : '';
		$template = (string) $filters['template'];
		return array( $table, $filters['offer_type'], $filters['offer_id'], $filters['sub_offer_id'], $filters['date_from'], $filters['date_to'], $device, $device, $template, $template );
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

		$args   = self::filter_args( self::table(), $filters );
		$args[] = $event_type;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT FLOOR(x/100) AS gx, FLOOR(y/100) AS gy, COUNT(*) AS c FROM %i WHERE offer_type = %s AND offer_id = %d AND sub_offer_id = %d AND created_at >= %s AND created_at <= %s AND ( %s = '' OR device = %s ) AND ( %s = '' OR template = %s ) AND event_type = %s GROUP BY gx, gy", $args ), ARRAY_N );

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

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$rows = $wpdb->get_results( $wpdb->prepare( "SELECT zone, event_type, COUNT(*) AS c, COUNT(DISTINCT session_hash) AS v FROM %i WHERE offer_type = %s AND offer_id = %d AND sub_offer_id = %d AND created_at >= %s AND created_at <= %s AND ( %s = '' OR device = %s ) AND ( %s = '' OR template = %s ) AND event_type IN ('click','hover') GROUP BY zone, event_type", self::filter_args( self::table(), $filters ) ), ARRAY_A );

		$zones = array();
		foreach ( (array) $rows as $row ) {
			$zone = $row['zone'];
			if ( ! isset( $zones[ $zone ] ) ) {
				$zones[ $zone ] = array(
					'click'    => 0,
					'hover'    => 0,
					'clickers' => 0,
				);
			}
			$zones[ $zone ][ $row['event_type'] ] = (int) $row['c'];
			if ( 'click' === $row['event_type'] ) {
				$zones[ $zone ]['clickers'] = (int) $row['v']; // Distinct visitors who clicked this zone.
			}
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

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT SUM(event_type = 'view') AS views, SUM(event_type = 'click') AS clicks, SUM(event_type = 'hover') AS hovers, COUNT(DISTINCT session_hash) AS visitors, COUNT(DISTINCT CASE WHEN event_type = 'view' THEN session_hash END) AS viewers, COUNT(DISTINCT CASE WHEN event_type = 'click' THEN session_hash END) AS clickers FROM %i WHERE offer_type = %s AND offer_id = %d AND sub_offer_id = %d AND created_at >= %s AND created_at <= %s AND ( %s = '' OR device = %s ) AND ( %s = '' OR template = %s )",
				self::filter_args( self::table(), $filters )
			),
			ARRAY_A
		);

		return array(
			'views'    => (int) ( $row['views'] ?? 0 ),
			'clicks'   => (int) ( $row['clicks'] ?? 0 ),
			'hovers'   => (int) ( $row['hovers'] ?? 0 ),
			'visitors' => (int) ( $row['visitors'] ?? 0 ),
			'viewers'  => (int) ( $row['viewers'] ?? 0 ),
			'clickers' => (int) ( $row['clickers'] ?? 0 ),
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

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		return $wpdb->get_col( $wpdb->prepare( 'SELECT DISTINCT template FROM %i WHERE offer_type = %s AND offer_id = %d AND sub_offer_id = %d', self::table(), $offer_type, $offer_id, $sub_offer_id ) );
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
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$deleted = $wpdb->query( $wpdb->prepare( 'DELETE FROM %i WHERE created_at < %s LIMIT 5000', $table, $cutoff ) );
		} while ( $deleted >= 5000 );
	}

	/**
	 * Delete the data of bumps removed from the bump list.
	 *
	 * The Pro plugin numbers a new bump "last id + 1", so after the last bump is deleted
	 * the next one gets its id; without this the new bump would show the old bump's data.
	 *
	 * @param mixed $old_value Previous bump list.
	 * @param mixed $value     New bump list.
	 * @return void
	 */
	public static function purge_removed_bumps( $old_value, $value ) {
		$removed = array_diff( array_keys( (array) $old_value ), array_keys( (array) $value ) );
		self::delete_offers( 'bump', $removed );
	}

	/**
	 * Delete the data of funnels removed from the funnel list.
	 *
	 * The free and the Pro plugin keep separate funnel lists ( Pro copies the free funnels ),
	 * so a funnel is only purged once it is in neither of them.
	 *
	 * @param mixed $old_value Previous funnel list.
	 * @param mixed $value     New funnel list.
	 * @return void
	 */
	public static function purge_removed_funnels( $old_value, $value ) {
		$removed = array_diff( array_keys( (array) $old_value ), array_keys( (array) $value ) );
		if ( empty( $removed ) ) {
			return;
		}
		$live    = array_merge(
			array_keys( (array) get_option( 'wps_wocuf_funnels_list', array() ) ),
			array_keys( (array) get_option( 'wps_wocuf_pro_funnels_list', array() ) )
		);
		self::delete_offers( 'upsell', array_diff( $removed, $live ) );
	}

	/**
	 * Delete all events of some offers.
	 *
	 * @param string $offer_type bump|upsell.
	 * @param array  $offer_ids  Offer ( bump / funnel ) ids.
	 * @return void
	 */
	public static function delete_offers( $offer_type, $offer_ids ) {
		global $wpdb;

		$offer_ids = array_filter( array_map( 'absint', (array) $offer_ids ) );
		if ( empty( $offer_ids ) || ! in_array( $offer_type, self::OFFER_TYPES, true ) ) {
			return;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->query( $wpdb->prepare( 'DELETE FROM %i WHERE offer_type = %s AND offer_id IN (' . implode( ',', array_fill( 0, count( $offer_ids ), '%d' ) ) . ')', array_merge( array( self::table(), $offer_type ), $offer_ids ) ) );
	}

	/**
	 * Delete everything.
	 *
	 * @return void
	 */
	public static function truncate() {
		global $wpdb;
		$table = self::table();
		$wpdb->query( $wpdb->prepare( 'TRUNCATE TABLE %i', $table ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	}
}

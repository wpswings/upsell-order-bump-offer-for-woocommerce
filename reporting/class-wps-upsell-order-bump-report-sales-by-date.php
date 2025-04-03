<?php
/**
 * Order Bump Sales by Date Report.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      1.4.0
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/reporting
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit; // Exit if accessed directly.
}

if ( class_exists( 'Wps_Upsell_Order_Bump_Report_Sales_By_Date' ) ) {
	return;
}

/**
 * The Admin-facing reporting functionality of the plugin.
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/reporting
 * @author     WP Swings <webmaster@wpswings.com>
 */
class Wps_Upsell_Order_Bump_Report_Sales_By_Date extends WC_Admin_Report {

	/**
	 * Chart colors.
	 *
	 * @var array
	 */
	public $chart_colours = array();

	/**
	 * The report data.
	 *
	 * @var stdClass
	 */
	private $report_data;

	/**
	 * Get report data.
	 *
	 * @return stdClass
	 */
	public function get_report_data() {
		if ( empty( $this->report_data ) ) {
			$this->query_report_data();
		}
		return $this->report_data;
	}

	/**
	 * Get all data needed for this report and store in the class.
	 */
	private function query_report_data() {
		$this->report_data = new stdClass();

		// Total Number of Order Bump Orders.
		$this->report_data->order_counts = (array) $this->get_order_report_data(
			array(
				'data'         => array(
					'ID'                     => array(
						'type'     => 'post_data',
						'function' => 'COUNT',
						'name'     => 'count',
						'distinct' => true,
					),
					'post_date'              => array(
						'type'     => 'post_data',
						'function' => '',
						'name'     => 'post_date',
					),
					'is_order_bump_purchase' => array(
						'type'            => 'order_item_meta',
						'order_item_type' => 'line_item',
						'function'        => '',
						'name'            => 'wps_upsell_order_bump_item_meta',
					),
				),
				'group_by'     => $this->group_by_query,
				'order_by'     => 'post_date ASC',
				'query_type'   => 'get_results',
				'filter_range' => true,
				'order_types'  => wc_get_order_types( 'order-count' ),
				'order_status' => array( 'completed', 'processing', 'on-hold', 'refunded' ),
				'nocache'      => true, // Using these as it was not updating latest orders data.
			)
		);

		// Total Number of Order Bump Items.
		$this->report_data->order_items = (array) $this->get_order_report_data(
			array(
				'data'         => array(
					'_qty'                   => array(
						'type'            => 'order_item_meta',
						'order_item_type' => 'line_item',
						'function'        => 'SUM',
						'name'            => 'order_item_count',
					),
					'post_date'              => array(
						'type'     => 'post_data',
						'function' => '',
						'name'     => 'post_date',
					),
					'is_order_bump_purchase' => array(
						'type'            => 'order_item_meta',
						'order_item_type' => 'line_item',
						'function'        => '',
						'name'            => 'wps_upsell_order_bump_item_meta',
					),
				),
				'where'        => array(
					array(
						'key'      => 'order_items.order_item_type',
						'value'    => 'line_item',
						'operator' => '=',
					),
				),
				'group_by'     => $this->group_by_query,
				'order_by'     => 'post_date ASC',
				'query_type'   => 'get_results',
				'filter_range' => true,
				'order_types'  => wc_get_order_types( 'order-count' ),
				'order_status' => array( 'completed', 'processing', 'on-hold', 'refunded' ),
				'nocache'      => true,
			)
		);

		/**
		 * Total Number of Order Bump Refunded Items.
		 */
		$this->report_data->refunded_order_items = absint(
			$this->get_order_report_data(
				array(
					'data'         => array(
						'_qty'                   => array(
							'type'            => 'order_item_meta',
							'order_item_type' => 'line_item',
							'function'        => 'SUM',
							'name'            => 'order_item_count',
						),
						'is_order_bump_purchase' => array(
							'type'            => 'order_item_meta',
							'order_item_type' => 'line_item',
							'function'        => '',
							'name'            => 'wps_upsell_order_bump_item_meta',
						),
					),
					'where'        => array(
						array(
							'key'      => 'order_items.order_item_type',
							'value'    => 'line_item',
							'operator' => '=',
						),
					),
					'query_type'   => 'get_var',
					'filter_range' => true,
					'order_types'  => wc_get_order_types( 'order-count' ),
					'order_status' => array( 'refunded' ),
					'nocache'      => true,
				)
			)
		);

		/**
		 * Amount Total for all Order Bump Items.
		 */
		$this->report_data->orders = (array) $this->get_order_report_data(
			array(
				'data'         => array(
					'_line_total'            => array(
						'type'            => 'order_item_meta',
						'order_item_type' => 'line_item',
						'function'        => 'SUM',
						'name'            => 'total_sales',
					),
					'_order_shipping'        => array(
						'type'     => 'meta',
						'function' => 'SUM',
						'name'     => 'total_shipping',
					),
					'_order_tax'             => array(
						'type'     => 'meta',
						'function' => 'SUM',
						'name'     => 'total_tax',
					),
					'_order_shipping_tax'    => array(
						'type'     => 'meta',
						'function' => 'SUM',
						'name'     => 'total_shipping_tax',
					),
					'is_order_bump_purchase' => array(
						'type'            => 'order_item_meta',
						'order_item_type' => 'line_item',
						'function'        => '',
						'name'            => 'wps_upsell_order_bump_item_meta',
					),
					'post_date'              => array(
						'type'     => 'post_data',
						'function' => '',
						'name'     => 'post_date',
					),
				),
				'group_by'     => $this->group_by_query,
				'order_by'     => 'post_date ASC',
				'query_type'   => 'get_results',
				'filter_range' => true,
				'order_types'  => wc_get_order_types( 'sales-reports' ),
				'order_status' => array( 'completed', 'processing', 'on-hold' ),
				'nocache'      => true,
			)
		);

		$this->report_data->total_tax_refunded          = 0;
		$this->report_data->total_shipping_refunded     = 0;
		$this->report_data->total_shipping_tax_refunded = 0;
		$this->report_data->total_refunds               = 0;

		// Totals from all orders - including those refunded. Subtract refunded amounts.
		$this->report_data->total_tax          = wc_format_decimal( array_sum( wp_list_pluck( $this->report_data->orders, 'total_tax' ) ) - $this->report_data->total_tax_refunded, 2 );
		$this->report_data->total_shipping     = wc_format_decimal( array_sum( wp_list_pluck( $this->report_data->orders, 'total_shipping' ) ) - $this->report_data->total_shipping_refunded, 2 );
		$this->report_data->total_shipping_tax = wc_format_decimal( array_sum( wp_list_pluck( $this->report_data->orders, 'total_shipping_tax' ) ) - $this->report_data->total_shipping_tax_refunded, 2 );

		// Total the refunds and sales amounts. Sales subract refunds. Note - total_sales also includes shipping costs.
		$this->report_data->total_sales = wc_format_decimal( array_sum( wp_list_pluck( $this->report_data->orders, 'total_sales' ) ) - $this->report_data->total_refunds, 2 );
		$this->report_data->net_sales   = wc_format_decimal( $this->report_data->total_sales - $this->report_data->total_shipping - max( 0, $this->report_data->total_tax ) - max( 0, $this->report_data->total_shipping_tax ), 2 );

		// Calculate average based on net.
		$this->report_data->average_sales       = wc_format_decimal( $this->report_data->net_sales / ( $this->chart_interval + 1 ), 2 );
		$this->report_data->average_total_sales = wc_format_decimal( $this->report_data->total_sales / ( $this->chart_interval + 1 ), 2 );

		// Total orders in this period, even if refunded.
		$this->report_data->total_orders = absint( array_sum( wp_list_pluck( $this->report_data->order_counts, 'count' ) ) );

		// Item items ordered in this period, even if refunded.
		$this->report_data->total_items = absint( array_sum( wp_list_pluck( $this->report_data->order_items, 'order_item_count' ) ) );

		// 3rd party filtering of report data
		$this->report_data = apply_filters( 'wps_upsell_order_bump_sales_report_data', $this->report_data );
	}

	/**
	 * Get the legend for the main chart sidebar.
	 *
	 * @return array
	 */
	public function get_chart_legend() {
		$legend = array();
		$data   = $this->get_report_data();

		switch ( $this->chart_groupby ) {
			case 'day':
				$average_total_sales_title = sprintf(
					/* translators: %s: average total sales */
					__( '%s average net daily order bump sales', 'upsell-order-bump-offer-for-woocommerce' ),
					'<strong>' . wc_price( $data->average_total_sales ) . '</strong>'
				);
				break;
			case 'month':
			default:
				$average_total_sales_title = sprintf(
					/* translators: %s: average total sales */
					__( '%s average net monthly order bump sales', 'upsell-order-bump-offer-for-woocommerce' ),
					'<strong>' . wc_price( $data->average_total_sales ) . '</strong>'
				);
				break;
		}

		$legend[] = array(
			'title'            => sprintf(
				/* translators: %s: total sales */
				__( '%s net order bump sales in this period', 'upsell-order-bump-offer-for-woocommerce' ),
				'<strong>' . wc_price( $data->total_sales ) . '</strong>'
			),
			'placeholder'      => __( 'This is the sum of the order bump item totals after any refunds ( whole order refunds ) and excluding shipping and taxes.', 'upsell-order-bump-offer-for-woocommerce' ),
			'color'            => $this->chart_colours['sales_amount'],
			'highlight_series' => 6,
		);
		if ( $data->average_total_sales > 0 ) {
			$legend[] = array(
				'title'            => $average_total_sales_title,
				'color'            => $this->chart_colours['average'],
				'highlight_series' => 2,
			);
		}

		$legend[] = array(
			'title'            => sprintf(
				/* translators: %s: total orders */
				__( '%s order bump orders placed', 'upsell-order-bump-offer-for-woocommerce' ),
				'<strong>' . $data->total_orders . '</strong>'
			),
			'color'            => $this->chart_colours['order_count'],
			'highlight_series' => 1,
		);

		$legend[] = array(
			'title'            => sprintf(
				/* translators: %s: total items */
				__( '%s order bump items purchased', 'upsell-order-bump-offer-for-woocommerce' ),
				'<strong>' . $data->total_items . '</strong>'
			),
			'color'            => $this->chart_colours['item_count'],
			'highlight_series' => 0,
		);
		$legend[] = array(
			'title'            => sprintf(
				/* translators: 1: total refunds 2: total refunded orders 3: refunded items */
				__( '%s order bump refunded items', 'upsell-order-bump-offer-for-woocommerce' ),
				'<strong>' . $data->refunded_order_items . '</strong>'
			),

			'placeholder'      => __( 'Total order bump refunded items from fully refunded orders.', 'upsell-order-bump-offer-for-woocommerce' ),
			'color'            => $this->chart_colours['refund_amount'],
			'highlight_series' => 8,
		);

		return $legend;
	}

	/**
	 * Output the report.
	 */
	public function output_report() {
		$ranges = array(
			'year'       => __( 'Year', 'upsell-order-bump-offer-for-woocommerce' ),
			'last_month' => __( 'Last month', 'upsell-order-bump-offer-for-woocommerce' ),
			'month'      => __( 'This month', 'upsell-order-bump-offer-for-woocommerce' ),
			'7day'       => __( 'Last 7 days', 'upsell-order-bump-offer-for-woocommerce' ),
		);

		$this->chart_colours = array(
			'sales_amount'  => '#8eba36',
			'average'       => '#b4db65',
			'order_count'   => '#dbe1e3',
			'item_count'    => '#ecf0f1',
			'refund_amount' => '#e74c3c',
		);

		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '7day'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ), true ) ) {
			$current_range = '7day';
		}

		$this->check_current_range_nonce( $current_range );
		$this->calculate_current_range( $current_range );

		include WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php';
	}

	/**
	 * Output an export link.
	 */
	public function get_export_button() {
		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( wp_unslash( $_GET['range'] ) ) : '7day'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		?>
	<a
	href="#"
	download="report-<?php echo esc_attr( $current_range ); ?>-<?php echo esc_attr( date_i18n( 'Y-m-d', time() ) ); ?>.csv"
	class="export_csv"
	data-export="chart"
	data-xaxes="<?php esc_attr_e( 'Date', 'upsell-order-bump-offer-for-woocommerce' ); ?>"
	data-exclude_series="2"
	data-groupby="<?php echo esc_attr( $this->chart_groupby ); ?>"
	>
		<?php esc_html_e( 'Export CSV', 'upsell-order-bump-offer-for-woocommerce' ); ?>
	</a>
		<?php
	}

	/**
	 * Round our totals correctly.
	 *
	 * @param array|string $amount Amount.
	 *
	 * @return array|string
	 */
	private function round_chart_totals( $amount ) {
		if ( is_array( $amount ) ) {
			return array( $amount[0], wc_format_decimal( $amount[1], wc_get_price_decimals() ) );
		} else {
			return wc_format_decimal( $amount, wc_get_price_decimals() );
		}
	}

	/**
	 * Get the main chart.
	 */
	public function get_main_chart() {
		global $wp_locale;

		// Prepare data for report.
		$data = array(
			'order_counts'         => $this->prepare_chart_data( $this->report_data->order_counts, 'post_date', 'count', $this->chart_interval, $this->start_date, $this->chart_groupby ),
			'order_item_counts'    => $this->prepare_chart_data( $this->report_data->order_items, 'post_date', 'order_item_count', $this->chart_interval, $this->start_date, $this->chart_groupby ),
			'order_amounts'        => $this->prepare_chart_data( $this->report_data->orders, 'post_date', 'total_sales', $this->chart_interval, $this->start_date, $this->chart_groupby ),
			'shipping_amounts'     => $this->prepare_chart_data( $this->report_data->orders, 'post_date', 'total_shipping', $this->chart_interval, $this->start_date, $this->chart_groupby ),
			'shipping_tax_amounts' => $this->prepare_chart_data( $this->report_data->orders, 'post_date', 'total_shipping_tax', $this->chart_interval, $this->start_date, $this->chart_groupby ),
			'tax_amounts'          => $this->prepare_chart_data( $this->report_data->orders, 'post_date', 'total_tax', $this->chart_interval, $this->start_date, $this->chart_groupby ),
			'net_order_amounts'    => array(),
			'gross_order_amounts'  => array(),
		);

		foreach ( $data['order_amounts'] as $order_amount_key => $order_amount_value ) {
			$data['gross_order_amounts'][ $order_amount_key ]   = $order_amount_value;
			$data['net_order_amounts'][ $order_amount_key ]     = $order_amount_value;
			$data['net_order_amounts'][ $order_amount_key ][1] -=
			$data['shipping_amounts'][ $order_amount_key ][1] +
			$data['shipping_tax_amounts'][ $order_amount_key ][1] +
			$data['tax_amounts'][ $order_amount_key ][1];
		}

		// 3rd party filtering of report data
		$data = apply_filters( 'woocommerce_admin_report_chart_data', $data );

		// Encode in json format.
		$chart_data = wp_json_encode(
			array(
				'order_counts'        => array_values( $data['order_counts'] ),
				'order_item_counts'   => array_values( $data['order_item_counts'] ),
				'order_amounts'       => array_map( array( $this, 'round_chart_totals' ), array_values( $data['order_amounts'] ) ),
				'gross_order_amounts' => array_map( array( $this, 'round_chart_totals' ), array_values( $data['gross_order_amounts'] ) ),
			)
		);
		?>
	<div class="chart-container">
	<div class="chart-placeholder main"></div>
	</div>
	<script type="text/javascript">

	var main_chart;

	jQuery(function(){
		var order_data = JSON.parse( decodeURIComponent( '<?php echo rawurlencode( $chart_data ); ?>' ) );
		var drawGraph = function( highlight ) {
		var series = [
			{
			label: "<?php echo esc_js( __( 'Number of items sold', 'upsell-order-bump-offer-for-woocommerce' ) ); ?>",
			data: order_data.order_item_counts,
			color: '<?php echo esc_js( $this->chart_colours['item_count'] ); ?>',
			bars: { fillColor: '<?php echo esc_js( $this->chart_colours['item_count'] ); ?>', fill: true, show: true, lineWidth: 1, barWidth: <?php echo esc_js( $this->barwidth ); ?> * 0.5, align: 'center' },
			shadowSize: 0,
			hoverable: false
			},
			{
			label: "<?php echo esc_js( __( 'Number of orders', 'upsell-order-bump-offer-for-woocommerce' ) ); ?>",
			data: order_data.order_counts,
			color: '<?php echo esc_js( $this->chart_colours['order_count'] ); ?>',
			bars: { fillColor: '<?php echo esc_js( $this->chart_colours['order_count'] ); ?>', fill: true, show: true, lineWidth: 0, barWidth: <?php echo esc_js( $this->barwidth ); ?> * 0.5, align: 'center' },
			shadowSize: 0,
			hoverable: false
			},
			{
			label: "<?php echo esc_js( __( 'Average net sales amount', 'upsell-order-bump-offer-for-woocommerce' ) ); ?>",
			data: [ [ <?php echo esc_js( min( array_keys( $data['order_amounts'] ) ) ); ?>, <?php echo esc_js( $this->report_data->average_total_sales ); ?> ], [ <?php echo esc_js( max( array_keys( $data['order_amounts'] ) ) ); ?>, <?php echo esc_js( $this->report_data->average_total_sales ); ?> ] ],
			yaxis: 2,
			color: '<?php echo esc_js( $this->chart_colours['average'] ); ?>',
			points: { show: false },
			lines: { show: true, lineWidth: 3, fill: false },
			shadowSize: 0,
			hoverable: false
			},
			{
			label: "<?php echo esc_js( __( 'Net sales amount', 'upsell-order-bump-offer-for-woocommerce' ) ); ?>",
			data: order_data.gross_order_amounts,
			yaxis: 2,
			color: '<?php echo esc_js( $this->chart_colours['sales_amount'] ); ?>',
			points: { show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true },
			lines: { show: true, lineWidth: 3, fill: false },
			shadowSize: 0,
			prepend_tooltip: "<?php echo esc_html( get_woocommerce_currency_symbol() ); ?>"
			},
		];

		if ( highlight !== 'undefined' && series[ highlight ] ) {
			highlight_series = series[ highlight ];

			highlight_series.color = '#9c5d90';

			if ( highlight_series.bars ) {
			highlight_series.bars.fillColor = '#9c5d90';
			}

			if ( highlight_series.lines ) {
			highlight_series.lines.lineWidth = 5;
		}
		}

		main_chart = jQuery.plot(
			jQuery('.chart-placeholder.main'),
			series,
			{
			legend: {
				show: false
			},
			grid: {
				color: '#aaa',
				borderColor: 'transparent',
				borderWidth: 0,
				hoverable: true
			},
			xaxes: [ {
				color: '#aaa',
				position: "bottom",
				tickColor: 'transparent',
				mode: "time",
				timeformat: "<?php echo ( 'day' === $this->chart_groupby ) ? '%d %b' : '%b'; ?>",
				monthNames: JSON.parse( decodeURIComponent( '<?php echo rawurlencode( wp_json_encode( array_values( $wp_locale->month_abbrev ) ) ); ?>' ) ),
				tickLength: 1,
				minTickSize: [1, "<?php echo esc_js( $this->chart_groupby ); ?>"],
				font: {
				color: "#aaa"
			}
			} ],
			yaxes: [
				{
				min: 0,
				minTickSize: 1,
				tickDecimals: 0,
				color: '#d4d9dc',
				font: { color: "#aaa" }
				},
				{
				position: "right",
				min: 0,
				tickDecimals: 2,
				alignTicksWithAxis: 1,
				color: 'transparent',
				font: { color: "#aaa" }
				}
			],
		}
		);

		jQuery('.chart-placeholder').resize();
		}

		drawGraph();

		jQuery('.highlight_series').hover(
		function() {
			drawGraph( jQuery(this).data('series') );
		},
		function() {
			drawGraph();
		});
	});
	</script>
		<?php
	}
}

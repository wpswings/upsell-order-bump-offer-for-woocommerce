<?php
/**
 * Gamified bump offers - front end.
 *
 * Renders spin-wheel / scratch-card games in place of the standard bump, serves
 * the server-side prize draw over AJAX and swaps in the standard bump (at the
 * won price) once revealed.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      3.1.8
 *
 * @package    Upsell_Order_Bump_Offer_For_Woocommerce
 * @subpackage Upsell_Order_Bump_Offer_For_Woocommerce/public
 * @author     WP Swings <webmaster@wpswings.com>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gamified bump offers - front end.
 *
 * @since 3.1.8
 */
class Upsell_Order_Bump_Gamified_Public {

	/**
	 * AJAX action / nonce action.
	 *
	 * @var string
	 */
	const AJAX_ACTION  = 'wps_ubo_game_play';
	const NONCE_ACTION = 'wps_ubo_game';

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Constructor.
	 *
	 * @param string $version Plugin version.
	 */
	public function __construct( $version ) {
		$this->version = $version;
	}

	/**
	 * Whether any bump uses a game format.
	 *
	 * @return bool
	 */
	private function has_games() {
		$settings = Upsell_Order_Bump_Gamified::get_settings();
		return 'on' === $settings['enabled'] && ! empty( $settings['formats'] );
	}

	/**
	 * Enqueue assets where bumps can appear.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		if ( ! $this->has_games() ) {
			return;
		}
		$on_page = ( function_exists( 'is_checkout' ) && is_checkout() ) || ( function_exists( 'is_cart' ) && is_cart() );
		if ( ! $on_page ) {
			$post    = get_post();
			$on_page = $post && has_shortcode( (string) $post->post_content, 'wps_bump_offer_shortcode' );
		}
		if ( ! $on_page ) {
			return;
		}
		self::register_assets( $this->version );
		wp_enqueue_style( 'wps-ubo-gamified' );
		wp_enqueue_script( 'wps-ubo-gamified' );
	}

	/**
	 * Register the shared front-end assets (also used by the admin preview).
	 *
	 * @param string $version Asset version.
	 * @return void
	 */
	public static function register_assets( $version ) {
		$base = plugin_dir_url( __DIR__ );
		$dir  = plugin_dir_path( __DIR__ );
		// File-time versions so browsers pick up updated game assets immediately.
		$css_ver = file_exists( $dir . 'public/css/wps-ubo-gamified.css' ) ? $version . '.' . filemtime( $dir . 'public/css/wps-ubo-gamified.css' ) : $version;
		$js_ver  = file_exists( $dir . 'public/js/wps-ubo-gamified.js' ) ? $version . '.' . filemtime( $dir . 'public/js/wps-ubo-gamified.js' ) : $version;
		wp_register_style( 'wps-ubo-gamified', $base . 'public/css/wps-ubo-gamified.css', array(), $css_ver );
		wp_register_script( 'wps-ubo-gamified', $base . 'public/js/wps-ubo-gamified.js', array(), $js_ver, true );
		wp_localize_script(
			'wps-ubo-gamified',
			'wpsUboGame',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'action'  => self::AJAX_ACTION,
				'nonce'   => wp_create_nonce( self::NONCE_ACTION ),
				'i18n'    => array(
					'scratchHere' => __( 'Scratch here', 'upsell-order-bump-offer-for-woocommerce' ),
					'spinning'    => __( 'Spinning…', 'upsell-order-bump-offer-for-woocommerce' ),
					'standard'    => __( 'Here is your offer!', 'upsell-order-bump-offer-for-woocommerce' ),
					'error'       => __( 'Something went wrong. Please refresh the page.', 'upsell-order-bump-offer-for-woocommerce' ),
				),
			)
		);
	}

	/**
	 * Label of a format.
	 *
	 * @param string $format Format.
	 * @return string
	 */
	public static function format_label( $format ) {
		$labels = array(
			'standard'     => __( 'Standard (checkbox)', 'upsell-order-bump-offer-for-woocommerce' ),
			'spin_wheel'   => __( 'Spin wheel', 'upsell-order-bump-offer-for-woocommerce' ),
			'scratch_card' => __( 'Scratch card', 'upsell-order-bump-offer-for-woocommerce' ),
		);
		return isset( $labels[ $format ] ) ? $labels[ $format ] : $format;
	}

	/**
	 * Winning message with placeholders replaced.
	 *
	 * @param array $prize    Prize state.
	 * @param int   $discount Effective discount.
	 * @param int   $bump_id  Bump id.
	 * @return string
	 */
	public static function win_message( $prize, $discount, $bump_id ) {
		$settings = Upsell_Order_Bump_Gamified::get_settings();
		$bumps    = get_option( 'wps_ubo_bump_list', array() );
		$product  = ! empty( $bumps[ $bump_id ]['wps_upsell_bump_products_in_offer'] ) ? wc_get_product( $bumps[ $bump_id ]['wps_upsell_bump_products_in_offer'] ) : null;

		return strtr(
			$settings['win_message'],
			array(
				'{discount}' => $discount . '%',
				'{label}'    => (string) $prize['label'],
				'{product}'  => $product ? $product->get_name() : '',
			)
		);
	}

	/**
	 * CSS variables for the game colours.
	 *
	 * @return string
	 */
	private static function style_vars() {
		$s = Upsell_Order_Bump_Gamified::get_settings();
		return sprintf( '--wps-game-primary:%s;--wps-game-accent:%s;--wps-game-text:%s;', $s['color_primary'], $s['color_accent'], $s['color_text'] );
	}

	/**
	 * Filter: wps_ubo_bump_offer_output_handled — print the game instead of the bump.
	 *
	 * @param bool       $handled    Already handled.
	 * @param int|string $bump_id    Bump id.
	 * @param string     $target_key Target cart key.
	 * @param mixed      $key        Bump key.
	 * @return bool
	 */
	public function render_game( $handled, $bump_id, $target_key = '', $key = '' ) {
		if ( $handled ) {
			return $handled;
		}
		$format = Upsell_Order_Bump_Gamified::format_for( $bump_id );
		if ( 'standard' === $format || Upsell_Order_Bump_Gamified::get_prize( $bump_id ) ) {
			return false;
		}

		$settings = Upsell_Order_Bump_Gamified::get_settings();
		$prizes   = Upsell_Order_Bump_Gamified::prizes_for( $bump_id );
		$segments = array();
		$best     = 0;
		foreach ( $prizes as $row ) {
			$segments[] = array(
				'label' => $row['label'],
				'color' => $row['color'],
			);
			$best       = max( $best, min( (int) $settings['max_discount'], (int) $row['discount'] ) );
		}

		$bumps   = get_option( 'wps_ubo_bump_list', array() );
		$product = ! empty( $bumps[ $bump_id ]['wps_upsell_bump_products_in_offer'] ) ? wc_get_product( $bumps[ $bump_id ]['wps_upsell_bump_products_in_offer'] ) : null;

		$this->print_game(
			array(
				'bump_id'  => (int) $bump_id,
				'format'   => $format,
				'segments' => $segments,
				'headline' => 'spin_wheel' === $format ? $settings['headline_wheel'] : $settings['headline_scratch'],
				'button'   => 'spin_wheel' === $format ? $settings['button_wheel'] : $settings['button_scratch'],
				'product'  => $product ? $product->get_name() : '',
				'best'     => $best,
				'skip'     => 'on' === $settings['allow_skip'],
				'preview'  => false,
			)
		);
		return true;
	}

	/**
	 * Print game markup (front end and admin preview).
	 *
	 * @param array $args Game arguments.
	 * @return void
	 */
	public static function print_game( $args ) {
		$attrs = array(
			'class'         => 'wps-ubo-game wps-ubo-game--' . $args['format'],
			'data-bump-id'  => (string) $args['bump_id'],
			'data-format'   => $args['format'],
			'data-segments' => wp_json_encode( $args['segments'] ),
			'style'         => self::style_vars(),
		);
		if ( ! empty( $args['preview'] ) ) {
			$attrs['data-preview'] = '1';
			$attrs['data-weights'] = wp_json_encode( $args['weights'] );
			$attrs['data-results'] = wp_json_encode( $args['results'] );
		}
		?>
		<div
		<?php
		foreach ( $attrs as $name => $value ) {
			printf( ' %s="%s"', esc_attr( $name ), esc_attr( $value ) );
		}
		?>
		>
			<p class="wps-ubo-game__headline"><?php echo esc_html( $args['headline'] ); ?></p>
			<?php if ( ! empty( $args['product'] ) && $args['best'] > 0 ) : ?>
				<p class="wps-ubo-game__teaser">
					<?php
					printf(
						/* translators: 1: max discount, 2: product name. */
						esc_html__( 'Win up to %1$s off %2$s', 'upsell-order-bump-offer-for-woocommerce' ),
						'<strong>' . esc_html( $args['best'] . '%' ) . '</strong>',
						'<strong>' . esc_html( $args['product'] ) . '</strong>'
					);
					?>
				</p>
			<?php endif; ?>

			<div class="wps-ubo-game__stage">
				<?php if ( 'spin_wheel' === $args['format'] ) : ?>
					<div class="wps-ubo-game__wheel-wrap">
						<span class="wps-ubo-game__pointer" aria-hidden="true"></span>
						<div class="wps-ubo-game__wheel" aria-hidden="true"></div>
					</div>
				<?php else : ?>
					<div class="wps-ubo-game__card">
						<div class="wps-ubo-game__prize" aria-hidden="true">?</div>
						<canvas class="wps-ubo-game__scratch" aria-hidden="true"></canvas>
					</div>
				<?php endif; ?>
			</div>

			<button type="button" class="wps-ubo-game__play"><?php echo esc_html( $args['button'] ); ?></button>
			<p class="wps-ubo-game__result" role="status" aria-live="polite"></p>
			<?php if ( ! empty( $args['skip'] ) ) : ?>
				<button type="button" class="wps-ubo-game__skip"><?php esc_html_e( 'No thanks, just show me the offer', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Action: wps_ubo_before_bump_offer_html — ribbon above a revealed bump.
	 *
	 * @param int|string $bump_id Bump id.
	 * @return void
	 */
	public function before_bump_html( $bump_id ) {
		if ( 'standard' === Upsell_Order_Bump_Gamified::format_for( $bump_id ) ) {
			return;
		}
		$prize = Upsell_Order_Bump_Gamified::get_prize( $bump_id );
		if ( ! $prize ) {
			return;
		}
		$raw      = Upsell_Order_Bump_Gamified::raw_bumps();
		$discount = isset( $raw[ $bump_id ] ) ? Upsell_Order_Bump_Gamified::effective_discount( $raw[ $bump_id ], $prize ) : 0;

		printf( '<div class="wps-ubo-game-won" data-bump-id="%d" style="%s">', (int) $bump_id, esc_attr( self::style_vars() ) );
		if ( $discount > 0 ) {
			printf( '<p class="wps-ubo-game-won__ribbon">%s</p>', esc_html( self::win_message( $prize, $discount, $bump_id ) ) );
		}
	}

	/**
	 * Action: wps_ubo_after_bump_offer_html — close the ribbon wrapper.
	 *
	 * @param int|string $bump_id Bump id.
	 * @return void
	 */
	public function after_bump_html( $bump_id ) {
		if ( 'standard' !== Upsell_Order_Bump_Gamified::format_for( $bump_id ) && Upsell_Order_Bump_Gamified::get_prize( $bump_id ) ) {
			echo '</div>';
		}
	}

	/**
	 * Target cart key the bump was matched with (from the bump session).
	 *
	 * @param int $bump_id Bump id.
	 * @return string
	 */
	private function target_key_for( $bump_id ) {
		$ids  = (array) WC()->session->get( 'encountered_bump_array', array() );
		$keys = (array) WC()->session->get( 'encountered_bump_tarket_key_array', array() );
		foreach ( $ids as $index => $id ) {
			if ( (int) $id === (int) $bump_id ) {
				return isset( $keys[ $index ] ) ? (string) $keys[ $index ] : '';
			}
		}
		return '';
	}

	/**
	 * AJAX: draw the prize (server side) and return the revealed bump.
	 *
	 * @return void
	 */
	public function ajax_play() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );

		$bump_id = isset( $_POST['bump_id'] ) ? absint( wp_unslash( $_POST['bump_id'] ) ) : 0;
		$skip    = ! empty( $_POST['skip'] );

		if ( ! $bump_id || ! function_exists( 'WC' ) || ! WC()->session || 'standard' === Upsell_Order_Bump_Gamified::format_for( $bump_id ) ) {
			wp_send_json_error( array( 'message' => __( 'This offer is not available.', 'upsell-order-bump-offer-for-woocommerce' ) ), 400 );
		}

		// Only bumps that are currently being shown to this customer.
		$shown = array_map( 'intval', (array) WC()->session->get( 'encountered_bump_array', array() ) );
		$raw   = Upsell_Order_Bump_Gamified::raw_bumps();
		if ( ! in_array( $bump_id, $shown, true ) || empty( $raw[ $bump_id ] ) || 'yes' !== ( isset( $raw[ $bump_id ]['wps_upsell_bump_status'] ) ? $raw[ $bump_id ]['wps_upsell_bump_status'] : '' ) ) {
			wp_send_json_error( array( 'message' => __( 'This offer is not available.', 'upsell-order-bump-offer-for-woocommerce' ) ), 403 );
		}

		$settings = Upsell_Order_Bump_Gamified::get_settings();
		if ( $skip && 'on' !== $settings['allow_skip'] ) {
			$skip = false;
		}

		$result   = Upsell_Order_Bump_Gamified::draw( $bump_id, $skip );
		$prize    = $result['prize'];
		$discount = Upsell_Order_Bump_Gamified::effective_discount( $raw[ $bump_id ], $prize );

		ob_start();
		wps_ubo_analyse_and_display_order_bump( $bump_id, $this->target_key_for( $bump_id ), $bump_id );
		$html = ob_get_clean();

		wp_send_json_success(
			array(
				'index'    => (int) $prize['index'],
				'label'    => (string) $prize['label'],
				'discount' => $discount,
				'skipped'  => ! empty( $prize['skipped'] ),
				'repeat'   => ! $result['new'],
				'message'  => $discount > 0 ? self::win_message( $prize, $discount, $bump_id ) : __( 'Here is your offer!', 'upsell-order-bump-offer-for-woocommerce' ),
				'html'     => $html,
			)
		);
	}

	/**
	 * Action: woocommerce_add_to_cart — count accepts after a game.
	 *
	 * @param string $cart_item_key  Cart key.
	 * @param int    $product_id     Product.
	 * @param int    $quantity       Qty.
	 * @param int    $variation_id   Variation.
	 * @param array  $variation      Attributes.
	 * @param array  $cart_item_data Cart item data.
	 * @return void
	 */
	public function track_accept( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
		if ( empty( $cart_item_data['wps_ubo_offer_product'] ) || empty( $cart_item_data['wps_ubo_bump_id'] ) ) {
			return;
		}
		$bump_id = absint( $cart_item_data['wps_ubo_bump_id'] );
		if ( 'standard' !== Upsell_Order_Bump_Gamified::format_for( $bump_id ) && Upsell_Order_Bump_Gamified::get_prize( $bump_id ) ) {
			Upsell_Order_Bump_Gamified::bump_stat( $bump_id, 'accepts' );
		}
	}

	/**
	 * Action: woocommerce_checkout_create_order_line_item — record the prize.
	 *
	 * @param WC_Order_Item_Product $item          Item.
	 * @param string                $cart_item_key Cart key.
	 * @param array                 $values        Cart item.
	 * @return void
	 */
	public function add_item_meta( $item, $cart_item_key, $values ) {
		if ( empty( $values['wps_ubo_offer_product'] ) || empty( $values['wps_ubo_bump_id'] ) ) {
			return;
		}
		$bump_id = absint( $values['wps_ubo_bump_id'] );
		$prize   = Upsell_Order_Bump_Gamified::get_prize( $bump_id );
		$raw     = Upsell_Order_Bump_Gamified::raw_bumps();
		if ( ! $prize || empty( $raw[ $bump_id ] ) ) {
			return;
		}
		$discount = Upsell_Order_Bump_Gamified::effective_discount( $raw[ $bump_id ], $prize );
		if ( $discount > 0 ) {
			$item->add_meta_data( Upsell_Order_Bump_Gamified::ITEM_META, sprintf( '%s: %s (%d%%)', self::format_label( $prize['format'] ), $prize['label'], $discount ), true );
		}
	}

	/**
	 * Action: order placed — prizes are single use.
	 *
	 * @return void
	 */
	public function clear_after_order() {
		Upsell_Order_Bump_Gamified::clear_session();
	}
}

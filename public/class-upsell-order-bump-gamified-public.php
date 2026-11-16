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
					'spin'        => _x( 'Spin', 'Label on the centre of the wheel', 'upsell-order-bump-offer-for-woocommerce' ),
					'youWon'      => __( 'You won!', 'upsell-order-bump-offer-for-woocommerce' ),
					'off'         => _x( 'Off', 'As in "5% off"', 'upsell-order-bump-offer-for-woocommerce' ),
					/* translators: %s: product name. */
					'onProduct'   => __( 'on %s', 'upsell-order-bump-offer-for-woocommerce' ),
					'applied'     => __( 'Applied to your offer automatically', 'upsell-order-bump-offer-for-woocommerce' ),
					'standardSub' => __( 'You can still add the offer below.', 'upsell-order-bump-offer-for-woocommerce' ),
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
			$discount   = min( (int) $settings['max_discount'], (int) $row['discount'] );
			$segments[] = array(
				'label'    => Upsell_Order_Bump_Gamified::label_for_discount( $row['label'], $discount ),
				'color'    => $row['color'],
				// Shown on the wheel anyway; used to pick the slice icon. Weights stay server-side.
				'discount' => $discount,
			);
			$best       = max( $best, $discount );
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
			'data-product'  => isset( $args['product'] ) ? (string) $args['product'] : '',
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
			<?php if ( ! empty( $args['product'] ) && $args['best'] > 0 ) : ?>
				<div class="wps-ubo-game__teaser">
					<?php
					printf(
						/* translators: 1: max discount, 2: product name. */
						esc_html__( 'Win up to %1$s off %2$s', 'upsell-order-bump-offer-for-woocommerce' ),
						'<strong>' . esc_html( $args['best'] . '%' ) . '</strong>',
						'<strong>' . esc_html( $args['product'] ) . '</strong>'
					);
					?>
				</div>
			<?php endif; ?>

			<div class="wps-ubo-game__stage">
				<?php if ( 'spin_wheel' === $args['format'] ) : ?>
					<div class="wps-ubo-game__wheel-wrap">
						<span class="wps-ubo-game__pointer" aria-hidden="true">
							<svg viewBox="0 0 40 52" focusable="false"><path d="M20 51C20 51 4 30.5 4 19.5a16 16 0 0 1 32 0C36 30.5 20 51 20 51Z"/><circle cx="20" cy="19" r="6"/></svg>
						</span>
						<div class="wps-ubo-game__wheel" aria-hidden="true"></div>
						<span class="wps-ubo-game__hub" aria-hidden="true"><b></b></span>
					</div>
				<?php else : ?>
					<div class="wps-ubo-game__card">
						<div class="wps-ubo-game__prize" aria-hidden="true">?</div>
						<canvas class="wps-ubo-game__scratch" aria-hidden="true"></canvas>
					</div>
				<?php endif; ?>
				<div class="wps-ubo-game__win" hidden></div>
			</div>

			<div class="wps-ubo-game__bar">
				<span class="wps-ubo-game__bar-icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" focusable="false"><path d="M3 8h18v4H3zM5 12h14v9H5zM12 8v13M12 8c-1.6-3.6-5.6-4.1-5.6-1.6C6.4 8 12 8 12 8Zm0 0c1.6-3.6 5.6-4.1 5.6-1.6C17.6 8 12 8 12 8Z"/></svg>
				</span>
				<div class="wps-ubo-game__headline"><?php echo esc_html( $args['headline'] ); ?></div>
				<button type="button" class="wps-ubo-game__play"><?php echo esc_html( $args['button'] ); ?><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m9 6 6 6-6 6"/></svg></button>
			</div>

			<div class="wps-ubo-game__result" role="status" aria-live="polite"></div>
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
		// Re-adding the offer (remove + add again) is one accept per session, not several.
		if ( 'standard' !== Upsell_Order_Bump_Gamified::format_for( $bump_id ) && Upsell_Order_Bump_Gamified::mark_accepted( $bump_id ) ) {
			Upsell_Order_Bump_Gamified::bump_stat( $bump_id, 'accepts' );
		}
	}

	/**
	 * Filter: woocommerce_add_cart_item_data — keep the won prize on the cart line, so the
	 * order meta always matches the price charged for that line (not a later session state).
	 *
	 * @param array $cart_item_data Cart item data.
	 * @return array
	 */
	public function add_prize_to_cart_item( $cart_item_data ) {
		if ( empty( $cart_item_data['wps_ubo_offer_product'] ) || empty( $cart_item_data['wps_ubo_bump_id'] ) ) {
			return $cart_item_data;
		}
		$bump_id = absint( $cart_item_data['wps_ubo_bump_id'] );
		if ( 'standard' === Upsell_Order_Bump_Gamified::format_for( $bump_id ) ) {
			return $cart_item_data;
		}
		$prize = Upsell_Order_Bump_Gamified::get_prize( $bump_id );
		$raw   = Upsell_Order_Bump_Gamified::raw_bumps();
		if ( ! $prize || empty( $raw[ $bump_id ] ) ) {
			return $cart_item_data;
		}
		$discount = Upsell_Order_Bump_Gamified::effective_discount( $raw[ $bump_id ], $prize );
		if ( $discount > 0 ) {
			$cart_item_data[ Upsell_Order_Bump_Gamified::CART_KEY ] = array(
				'format'   => (string) $prize['format'],
				'label'    => (string) $prize['label'],
				'discount' => $discount,
			);
		}
		return $cart_item_data;
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

		// Prize stored on the cart line when it was accepted (matches the charged price).
		$won = isset( $values[ Upsell_Order_Bump_Gamified::CART_KEY ] ) && is_array( $values[ Upsell_Order_Bump_Gamified::CART_KEY ] ) ? $values[ Upsell_Order_Bump_Gamified::CART_KEY ] : null;
		if ( ! $won ) {
			// Lines added before this was stored: fall back to the session prize.
			$prize = Upsell_Order_Bump_Gamified::get_prize( $bump_id );
			$raw   = Upsell_Order_Bump_Gamified::raw_bumps();
			if ( ! $prize || empty( $raw[ $bump_id ] ) ) {
				return;
			}
			$won = array(
				'format'   => (string) $prize['format'],
				'label'    => (string) $prize['label'],
				'discount' => Upsell_Order_Bump_Gamified::effective_discount( $raw[ $bump_id ], $prize ),
			);
		}
		if ( $won['discount'] > 0 ) {
			$item->add_meta_data( Upsell_Order_Bump_Gamified::ITEM_META, sprintf( '%s: %s (%s%%)', self::format_label( $won['format'] ), $won['label'], wc_format_localized_decimal( $won['discount'] ) ), true );
		}
	}

	/**
	 * Action: woocommerce_cart_emptied — prizes are single use.
	 *
	 * WooCommerce empties the cart after a successful payment (or when an offline order
	 * is placed), in the customer's session. The prize is NOT cleared when the order is
	 * created, because that happens before payment: a failed or cancelled payment would
	 * otherwise let the customer play again (re-roll) and lose the prize on the retry.
	 *
	 * @return void
	 */
	public function clear_after_order() {
		Upsell_Order_Bump_Gamified::clear_session();
	}

	/**
	 * Action: woocommerce_thankyou — also clear on the order-received page, but only when
	 * the order really went through (not failed / pending / cancelled).
	 *
	 * @param int $order_id Order id.
	 * @return void
	 */
	public function clear_after_paid_order( $order_id ) {
		$order = $order_id ? wc_get_order( $order_id ) : null;
		if ( $order && ! $order->has_status( array( 'failed', 'pending', 'cancelled' ) ) ) {
			Upsell_Order_Bump_Gamified::clear_session();
		}
	}
}

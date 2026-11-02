<?php
/**
 * Native cart abandonment upsell recovery - front end.
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
 * Capture, recovery links, checkout offer, attribution and privacy tools.
 *
 * @since 3.1.8
 */
class Upsell_Order_Bump_Acr_Public {

	/**
	 * Nonce action for public AJAX.
	 *
	 * @var string
	 */
	const NONCE_ACTION = 'wps_ubo_acr';

	/**
	 * Session keys.
	 *
	 * @var string
	 */
	const S_EMAIL    = 'wps_ubo_acr_email';
	const S_NAME     = 'wps_ubo_acr_name';
	const S_OPTOUT   = 'wps_ubo_acr_optout';
	const S_RECOVERY = 'wps_ubo_acr_recovery';

	/**
	 * Cart item data key of the recovery offer.
	 *
	 * @var string
	 */
	const ITEM_KEY = 'wps_ubo_acr_offer';

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Cart changed during this request.
	 *
	 * @var bool
	 */
	private $dirty = false;

	/**
	 * Constructor.
	 *
	 * @param string $version Plugin version.
	 */
	public function __construct( $version ) {
		$this->version = $version;
	}

	/**
	 * WC cart + session available.
	 *
	 * @return bool
	 */
	private static function has_wc() {
		return function_exists( 'WC' ) && WC()->session && WC()->cart;
	}

	/**
	 * Whether the current visitor may be tracked.
	 *
	 * @return bool
	 */
	public static function should_track() {
		if ( ! Upsell_Order_Bump_Acr_Db::is_enabled() || ! self::has_wc() ) {
			return false;
		}
		$settings = Upsell_Order_Bump_Acr_Db::get_settings();
		$track    = ! WC()->session->get( self::S_OPTOUT );
		$user     = wp_get_current_user();
		if ( $track && $user->exists() && array_intersect( (array) $user->roles, (array) $settings['exclude_roles'] ) ) {
			$track = false;
		}
		/**
		 * Filters whether the visitor's cart is tracked for recovery.
		 *
		 * @since 3.1.8
		 * @param bool $track   Track.
		 * @param int  $user_id User id (0 = guest).
		 */
		return (bool) apply_filters( 'wps_ubo_acr_should_track', $track, get_current_user_id() );
	}

	/*
	|--------------------------------------------------------------------------
	| Capture
	|--------------------------------------------------------------------------
	*/

	/**
	 * Cart change hooks: sync once at shutdown.
	 *
	 * @return void
	 */
	public function mark_dirty() {
		$this->dirty = true;
	}

	/**
	 * Action: shutdown.
	 *
	 * @return void
	 */
	public function maybe_sync() {
		if ( $this->dirty ) {
			self::sync();
		}
	}

	/**
	 * Snapshot of the cart (without bump / recovery offer items).
	 *
	 * @return array
	 */
	private static function snapshot() {
		$items = array();
		foreach ( WC()->cart->get_cart() as $item ) {
			if ( ! empty( $item['wps_ubo_offer_product'] ) || ! empty( $item[ self::ITEM_KEY ] ) ) {
				continue;
			}
			$items[] = array(
				'product_id'   => (int) $item['product_id'],
				'variation_id' => (int) $item['variation_id'],
				'quantity'     => (int) $item['quantity'],
				'variation'    => isset( $item['variation'] ) && is_array( $item['variation'] ) ? array_map( 'sanitize_text_field', $item['variation'] ) : array(),
			);
		}
		return $items;
	}

	/**
	 * Upsert the visitor's cart row.
	 *
	 * @return void
	 */
	public static function sync() {
		if ( ! self::should_track() ) {
			return;
		}
		$key  = (string) WC()->session->get_customer_id();
		$user = wp_get_current_user();

		$email = $user->exists() ? $user->user_email : (string) WC()->session->get( self::S_EMAIL );
		$name  = $user->exists() ? ( $user->first_name ? $user->first_name : $user->display_name ) : (string) WC()->session->get( self::S_NAME );
		if ( '' === $key || ! is_email( $email ) ) {
			return;
		}

		$row = Upsell_Order_Bump_Acr_Db::get_by( 'session_key', $key );
		if ( $row && in_array( $row['status'], array( 'recovered', 'lost', 'unsubscribed' ), true ) ) {
			// Closed rows keep their history; free the session key for a new cart.
			Upsell_Order_Bump_Acr_Db::update( $row['id'], array( 'session_key' => substr( 'closed-' . $row['id'] . '-' . $key, 0, 64 ) ) );
			$row = null;
		}

		$items = self::snapshot();
		if ( empty( $items ) ) {
			if ( $row && 'active' === $row['status'] ) {
				Upsell_Order_Bump_Acr_Db::delete( $row['id'] );
			}
			return;
		}

		WC()->cart->calculate_totals();
		$data = array(
			'user_id'       => get_current_user_id(),
			'email'         => strtolower( $email ),
			'first_name'    => mb_substr( sanitize_text_field( $name ), 0, 100 ),
			'cart'          => $items,
			'cart_total'    => (float) WC()->cart->get_total( 'edit' ),
			'currency'      => get_woocommerce_currency(),
			'status'        => 'active',
			'next_email_at' => null,
			'updated_at'    => Upsell_Order_Bump_Acr_Db::now(),
		);

		if ( $row ) {
			if ( '' === $data['first_name'] ) {
				unset( $data['first_name'] ); // Never blank a name we already have.
			}
			Upsell_Order_Bump_Acr_Db::update( $row['id'], $data );
		} else {
			$data['session_key'] = $key;
			Upsell_Order_Bump_Acr_Db::insert( $data );
		}
	}

	/**
	 * AJAX: guest email typed at checkout.
	 *
	 * @return void
	 */
	public function ajax_capture() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );
		$settings = Upsell_Order_Bump_Acr_Db::get_settings();
		$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$name     = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';

		if ( ! is_email( $email ) || ! self::should_track() || ( ! is_user_logged_in() && 'on' !== $settings['capture_guests'] ) ) {
			wp_send_json_success( array( 'tracked' => false ) );
		}
		WC()->session->set( self::S_EMAIL, $email );
		if ( '' !== $name ) {
			WC()->session->set( self::S_NAME, $name );
		}
		self::sync();
		wp_send_json_success( array( 'tracked' => true ) );
	}

	/**
	 * AJAX: visitor opted out under the email field.
	 *
	 * @return void
	 */
	public function ajax_optout() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );
		if ( self::has_wc() ) {
			WC()->session->set( self::S_OPTOUT, 1 );
			$row = Upsell_Order_Bump_Acr_Db::get_by( 'session_key', (string) WC()->session->get_customer_id() );
			if ( $row && 'active' === $row['status'] ) {
				Upsell_Order_Bump_Acr_Db::delete( $row['id'] );
			}
		}
		wp_send_json_success();
	}

	/*
	|--------------------------------------------------------------------------
	| Links
	|--------------------------------------------------------------------------
	*/

	/**
	 * Action: template_redirect — recovery and unsubscribe links.
	 *
	 * @return void
	 */
	public function handle_links() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Token/signature protected links from emails.
		if ( isset( $_GET['wps_ubo_acr_unsub'] ) ) {
			$token = self::clean_token( sanitize_text_field( wp_unslash( $_GET['wps_ubo_acr_unsub'] ) ) );
			$sig   = isset( $_GET['sig'] ) ? sanitize_text_field( wp_unslash( $_GET['sig'] ) ) : '';
			$row   = '' !== $token ? Upsell_Order_Bump_Acr_Db::get_by( 'token', $token ) : null;
			if ( $row && hash_equals( Upsell_Order_Bump_Acr_Mailer::unsubscribe_sig( $token ), $sig ) ) {
				Upsell_Order_Bump_Acr_Db::update(
					$row['id'],
					array(
						'status'        => 'recovered' === $row['status'] ? 'recovered' : 'unsubscribed',
						'next_email_at' => null,
					)
				);
				if ( 'recovered' === $row['status'] ) {
					// Keep the recovered row; add a suppression row for the email.
					Upsell_Order_Bump_Acr_Db::insert(
						array(
							'session_key' => 'unsub-' . md5( $row['email'] ),
							'email'       => $row['email'],
							'cart'        => array(),
							'status'      => 'unsubscribed',
						)
					);
				}
				self::notice( __( 'You have been unsubscribed from cart reminders.', 'upsell-order-bump-offer-for-woocommerce' ), 'success' );
			}
			wp_safe_redirect( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) );
			exit;
		}

		if ( ! isset( $_GET['wps_ubo_acr'] ) || ! self::has_wc() ) {
			return;
		}
		$token     = self::clean_token( sanitize_text_field( wp_unslash( $_GET['wps_ubo_acr'] ) ) );
		$add_offer = ! empty( $_GET['wps_ubo_acr_add'] );
		// phpcs:enable

		$row = Upsell_Order_Bump_Acr_Db::get_by( 'token', $token );
		if ( ! $row || empty( $row['cart'] ) ) {
			self::notice( __( 'This cart link has expired.', 'upsell-order-bump-offer-for-woocommerce' ) );
			wp_safe_redirect( wc_get_cart_url() );
			exit;
		}
		if ( 'recovered' === $row['status'] ) {
			self::notice( __( 'This order has already been completed. Thank you!', 'upsell-order-bump-offer-for-woocommerce' ) );
			wp_safe_redirect( wc_get_page_permalink( 'shop' ) );
			exit;
		}

		$this->restore( $row, $add_offer );
		wp_safe_redirect( wc_get_checkout_url() );
		exit;
	}

	/**
	 * Show a notice on the next page. Email links usually open in a browser
	 * with no WooCommerce session yet, so start one or the notice is lost.
	 *
	 * @param string $message Notice text.
	 * @param string $type    success|notice|error.
	 * @return void
	 */
	private static function notice( $message, $type = 'notice' ) {
		if ( ! self::has_wc() || ! function_exists( 'wc_add_notice' ) ) {
			return;
		}
		if ( ! WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}
		wc_add_notice( $message, $type );
	}

	/**
	 * Restore a saved cart into the current session.
	 *
	 * @param array $row       Cart row.
	 * @param bool  $add_offer Also add the upsell offer.
	 * @return void
	 */
	public function restore( $row, $add_offer ) {
		if ( ! WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}

		WC()->cart->empty_cart();
		$missing = 0;
		foreach ( $row['cart'] as $item ) {
			$added = WC()->cart->add_to_cart( (int) $item['product_id'], max( 1, (int) $item['quantity'] ), (int) $item['variation_id'], (array) $item['variation'] );
			if ( ! $added ) {
				++$missing;
			}
		}
		if ( $missing ) {
			wc_add_notice( __( 'Some items from your saved cart are no longer available.', 'upsell-order-bump-offer-for-woocommerce' ), 'notice' );
		}

		if ( $add_offer ) {
			self::add_offer_item( $row );
		}

		// Billing email first: WooCommerce rejects email-restricted coupons until it is known.
		if ( WC()->customer && ! WC()->customer->get_billing_email() ) {
			WC()->customer->set_billing_email( $row['email'] );
			if ( $row['first_name'] && ! WC()->customer->get_billing_first_name() ) {
				WC()->customer->set_billing_first_name( $row['first_name'] );
			}
			WC()->customer->save();
		}
		if ( $row['coupon_code'] && ! WC()->cart->has_discount( $row['coupon_code'] ) ) {
			WC()->cart->apply_coupon( $row['coupon_code'] );
		}

		// Bind the row to this session so further edits update it.
		$key   = (string) WC()->session->get_customer_id();
		$other = Upsell_Order_Bump_Acr_Db::get_by( 'session_key', $key );
		if ( $other && (int) $other['id'] !== (int) $row['id'] && 'active' === $other['status'] ) {
			Upsell_Order_Bump_Acr_Db::delete( $other['id'] );
		} elseif ( $other && (int) $other['id'] !== (int) $row['id'] ) {
			Upsell_Order_Bump_Acr_Db::update( $other['id'], array( 'session_key' => substr( 'closed-' . $other['id'] . '-' . $key, 0, 64 ) ) );
		}
		Upsell_Order_Bump_Acr_Db::update(
			$row['id'],
			array(
				'session_key' => $key,
				'clicked_at'  => Upsell_Order_Bump_Acr_Db::now(),
			)
		);

		WC()->session->set( self::S_RECOVERY, (int) $row['id'] );
		WC()->session->set( self::S_EMAIL, $row['email'] );
		if ( '' !== (string) $row['first_name'] ) {
			WC()->session->set( self::S_NAME, $row['first_name'] );
		}
	}

	/**
	 * Token from a link parameter (32 lowercase letters/digits), or ''.
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	private static function clean_token( $value ) {
		$value = strtolower( (string) $value );
		return preg_match( '/^[a-z0-9]{32}$/', $value ) ? $value : '';
	}

	/*
	|--------------------------------------------------------------------------
	| Recovery offer
	|--------------------------------------------------------------------------
	*/

	/**
	 * Row of the recovery in progress for this session.
	 *
	 * @return array|null
	 */
	public static function recovery_row() {
		if ( ! self::has_wc() ) {
			return null;
		}
		$id = (int) WC()->session->get( self::S_RECOVERY );
		return $id ? Upsell_Order_Bump_Acr_Db::get_by( 'id', $id ) : null;
	}

	/**
	 * Cart key of the recovery offer item, or ''.
	 *
	 * @return string
	 */
	private static function offer_cart_key() {
		foreach ( WC()->cart->get_cart() as $key => $item ) {
			if ( ! empty( $item[ self::ITEM_KEY ] ) ) {
				return (string) $key;
			}
		}
		return '';
	}

	/**
	 * Add the row's offer product as a recovery offer item.
	 *
	 * @param array $row Cart row.
	 * @return bool
	 */
	public static function add_offer_item( $row ) {
		$product = ! empty( $row['offer_product'] ) ? wc_get_product( $row['offer_product'] ) : null;
		if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() || '' !== self::offer_cart_key() ) {
			return false;
		}
		$product_id   = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
		$variation_id = $product->get_parent_id() ? $product->get_id() : 0;
		$variation    = $variation_id ? $product->get_variation_attributes() : array();
		return (bool) WC()->cart->add_to_cart(
			$product_id,
			1,
			$variation_id,
			$variation,
			array(
				self::ITEM_KEY => array(
					'cart_id' => (int) $row['id'],
				),
			)
		);
	}

	/**
	 * Action: woocommerce_before_calculate_totals — offer price from the row.
	 *
	 * @param WC_Cart $cart Cart.
	 * @return void
	 */
	public function apply_offer_price( $cart ) {
		static $rows = array();
		foreach ( $cart->get_cart() as $item ) {
			if ( empty( $item[ self::ITEM_KEY ]['cart_id'] ) ) {
				continue;
			}
			$id = (int) $item[ self::ITEM_KEY ]['cart_id'];
			if ( ! isset( $rows[ $id ] ) ) {
				$rows[ $id ] = Upsell_Order_Bump_Acr_Db::get_by( 'id', $id );
			}
			$row     = $rows[ $id ];
			$product = $item['data'];
			if ( ! $row || ! $product || ! in_array( (int) $row['offer_product'], array( (int) $product->get_id(), (int) $product->get_parent_id() ), true ) ) {
				continue;
			}
			$product->set_price( Upsell_Order_Bump_Acr_Engine::offer_price( $product, (int) $row['offer_discount'] ) );
		}
	}

	/**
	 * Offer box markup (classic + block checkout).
	 *
	 * @return string
	 */
	public static function offer_box_html() {
		$settings = Upsell_Order_Bump_Acr_Db::get_settings();
		$row      = 'on' === $settings['checkout_offer'] ? self::recovery_row() : null;
		$product  = $row && ! empty( $row['offer_product'] ) ? wc_get_product( $row['offer_product'] ) : null;
		if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
			return '';
		}
		$in_cart = '' !== self::offer_cart_key();
		$price   = Upsell_Order_Bump_Acr_Engine::offer_price( $product, (int) $row['offer_discount'] );
		$normal  = (float) ( $product->get_regular_price() ? $product->get_regular_price() : $product->get_price() );
		$name    = $row['first_name'] ? $row['first_name'] : '';

		ob_start();
		?>
		<div class="wps-ubo-acr-offer<?php echo $in_cart ? ' is-added' : ''; ?>">
			<p class="wps-ubo-acr-offer__title">
				<?php
				echo esc_html(
					$name
						/* translators: %s: customer first name. */
						? sprintf( __( 'Welcome back, %s! An exclusive offer for you', 'upsell-order-bump-offer-for-woocommerce' ), $name )
						: __( 'Welcome back! An exclusive offer for you', 'upsell-order-bump-offer-for-woocommerce' )
				);
				?>
			</p>
			<div class="wps-ubo-acr-offer__body">
				<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'wps-ubo-acr-offer__img' ) ) ); ?>
				<div class="wps-ubo-acr-offer__info">
					<strong class="wps-ubo-acr-offer__name"><?php echo esc_html( $product->get_name() ); ?></strong>
					<span class="wps-ubo-acr-offer__price">
						<?php if ( $price < $normal ) : ?>
							<del><?php echo wp_kses_post( wc_price( $normal ) ); ?></del>
						<?php endif; ?>
						<ins><?php echo wp_kses_post( wc_price( $price ) ); ?></ins>
						<?php if ( (int) $row['offer_discount'] > 0 ) : ?>
							<span class="wps-ubo-acr-offer__badge"><?php echo esc_html( sprintf( /* translators: %d: percent. */ __( '%d%% off', 'upsell-order-bump-offer-for-woocommerce' ), (int) $row['offer_discount'] ) ); ?></span>
						<?php endif; ?>
					</span>
				</div>
			</div>
			<label class="wps-ubo-acr-offer__toggle">
				<input type="checkbox" class="wps-ubo-acr-offer__check" <?php checked( $in_cart ); ?>>
				<span><?php esc_html_e( 'Yes, add this to my order', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
			</label>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Action: woocommerce_before_checkout_form (classic checkout).
	 *
	 * @return void
	 */
	public function print_offer_box() {
		echo self::offer_box_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped while building.
	}

	/**
	 * Filter: render_block_woocommerce/checkout (block checkout).
	 *
	 * @param string $content Block HTML.
	 * @return string
	 */
	public function prepend_offer_box( $content ) {
		return self::offer_box_html() . $content;
	}

	/**
	 * AJAX: add / remove the recovery offer.
	 *
	 * @return void
	 */
	public function ajax_toggle_offer() {
		check_ajax_referer( self::NONCE_ACTION, 'nonce' );
		$row = self::recovery_row();
		if ( ! $row || empty( $row['offer_product'] ) ) {
			wp_send_json_error( null, 400 );
		}
		$add = ! empty( $_POST['add'] );
		$key = self::offer_cart_key();
		if ( $add && '' === $key ) {
			self::add_offer_item( $row );
		} elseif ( ! $add && '' !== $key ) {
			WC()->cart->remove_cart_item( $key );
		}
		wp_send_json_success( array( 'added' => '' !== self::offer_cart_key() ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Orders
	|--------------------------------------------------------------------------
	*/

	/**
	 * Action: woocommerce_checkout_create_order_line_item — mark offer lines.
	 *
	 * @param WC_Order_Item_Product $item          Item.
	 * @param string                $cart_item_key Key.
	 * @param array                 $values        Cart item.
	 * @return void
	 */
	public function add_item_meta( $item, $cart_item_key, $values ) {
		if ( empty( $values[ self::ITEM_KEY ]['cart_id'] ) ) {
			return;
		}
		$row = Upsell_Order_Bump_Acr_Db::get_by( 'id', (int) $values[ self::ITEM_KEY ]['cart_id'] );
		$item->add_meta_data(
			'_wps_ubo_acr_offer',
			/* translators: %d: percent. */
			sprintf( __( 'Cart recovery offer (%d%%)', 'upsell-order-bump-offer-for-woocommerce' ), $row ? (int) $row['offer_discount'] : 0 ),
			true
		);
	}

	/**
	 * Classic checkout: woocommerce_checkout_order_processed.
	 *
	 * @param int $order_id Order id.
	 * @return void
	 */
	public function on_order_classic( $order_id ) {
		$this->attribute( wc_get_order( $order_id ) );
	}

	/**
	 * Block checkout: woocommerce_store_api_checkout_order_processed.
	 *
	 * @param WC_Order $order Order.
	 * @return void
	 */
	public function on_order_block( $order ) {
		$this->attribute( $order );
	}

	/**
	 * Link the order to its cart row.
	 *
	 * @param WC_Order|false $order Order.
	 * @return void
	 */
	private function attribute( $order ) {
		if ( ! $order || ! self::has_wc() ) {
			return;
		}
		$recovery_id = (int) WC()->session->get( self::S_RECOVERY );
		$row         = $recovery_id ? Upsell_Order_Bump_Acr_Db::get_by( 'id', $recovery_id ) : null;
		if ( ! $row ) {
			$row = Upsell_Order_Bump_Acr_Db::get_by( 'session_key', (string) WC()->session->get_customer_id() );
		}
		if ( ! $row && $order->get_billing_email() ) {
			$row = Upsell_Order_Bump_Acr_Db::open_row_for_email( $order->get_billing_email() );
		}

		if ( $row ) {
			if ( in_array( $row['status'], array( 'abandoned', 'lost' ), true ) || ( $recovery_id && (int) $row['id'] === $recovery_id ) ) {
				Upsell_Order_Bump_Acr_Engine::mark_recovered( $row, $order, $recovery_id && (int) $row['id'] === $recovery_id ? 'link' : 'direct' );
			} elseif ( 'active' === $row['status'] ) {
				// Normal purchase: nothing to recover.
				Upsell_Order_Bump_Acr_Db::delete( $row['id'] );
			}
		}
		WC()->session->set( self::S_RECOVERY, null );
	}

	/*
	|--------------------------------------------------------------------------
	| Assets
	|--------------------------------------------------------------------------
	*/

	/**
	 * Enqueue on checkout.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		if ( ! Upsell_Order_Bump_Acr_Db::is_enabled() || ! function_exists( 'is_checkout' ) || ! is_checkout() || is_order_received_page() ) {
			return;
		}
		$settings = Upsell_Order_Bump_Acr_Db::get_settings();
		$base     = plugin_dir_url( __DIR__ );
		$dir      = plugin_dir_path( __DIR__ );
		$js_ver   = $this->version . '.' . filemtime( $dir . 'public/js/wps-ubo-acr.js' );
		$css_ver  = $this->version . '.' . filemtime( $dir . 'public/css/wps-ubo-acr.css' );

		wp_enqueue_style( 'wps-ubo-acr', $base . 'public/css/wps-ubo-acr.css', array(), $css_ver );
		wp_enqueue_script( 'wps-ubo-acr', $base . 'public/js/wps-ubo-acr.js', array(), $js_ver, true );
		wp_localize_script(
			'wps-ubo-acr',
			'wpsUboAcr',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( self::NONCE_ACTION ),
				'capture'  => self::should_track() && ! is_user_logged_in() && 'on' === $settings['capture_guests'],
				'notice'   => self::should_track() && ! is_user_logged_in() ? $settings['consent_notice'] : '',
				'optOut'   => __( 'Opt out', 'upsell-order-bump-offer-for-woocommerce' ),
				'optedOut' => __( 'Done. Your cart will not be saved for reminders.', 'upsell-order-bump-offer-for-woocommerce' ),
			)
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Privacy
	|--------------------------------------------------------------------------
	*/

	/**
	 * Filter: wp_privacy_personal_data_exporters.
	 *
	 * @param array $exporters Exporters.
	 * @return array
	 */
	public static function register_exporter( $exporters ) {
		$exporters['wps-ubo-acr'] = array(
			'exporter_friendly_name' => __( 'Saved carts (cart recovery)', 'upsell-order-bump-offer-for-woocommerce' ),
			'callback'               => array( __CLASS__, 'export' ),
		);
		return $exporters;
	}

	/**
	 * Filter: wp_privacy_personal_data_erasers.
	 *
	 * @param array $erasers Erasers.
	 * @return array
	 */
	public static function register_eraser( $erasers ) {
		$erasers['wps-ubo-acr'] = array(
			'eraser_friendly_name' => __( 'Saved carts (cart recovery)', 'upsell-order-bump-offer-for-woocommerce' ),
			'callback'             => array( __CLASS__, 'erase' ),
		);
		return $erasers;
	}

	/**
	 * Rows for an email.
	 *
	 * @param string $email Email.
	 * @return array[]
	 */
	private static function rows_for_email( $email ) {
		global $wpdb;
		$table = Upsell_Order_Bump_Acr_Db::table();
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
		return (array) $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE email = %s", strtolower( $email ) ), ARRAY_A );
	}

	/**
	 * Personal data exporter.
	 *
	 * @param string $email Email.
	 * @return array
	 */
	public static function export( $email ) {
		$data = array();
		foreach ( self::rows_for_email( $email ) as $row ) {
			$products = array();
			foreach ( (array) json_decode( $row['cart'], true ) as $item ) {
				$products[] = get_the_title( (int) $item['product_id'] ) . ' × ' . (int) $item['quantity'];
			}
			$data[] = array(
				'group_id'    => 'wps-ubo-acr',
				'group_label' => __( 'Saved carts', 'upsell-order-bump-offer-for-woocommerce' ),
				'item_id'     => 'wps-ubo-acr-' . $row['id'],
				'data'        => array(
					array(
						'name'  => __( 'Email', 'upsell-order-bump-offer-for-woocommerce' ),
						'value' => $row['email'],
					),
					array(
						'name'  => __( 'Name', 'upsell-order-bump-offer-for-woocommerce' ),
						'value' => $row['first_name'],
					),
					array(
						'name'  => __( 'Products', 'upsell-order-bump-offer-for-woocommerce' ),
						'value' => implode( ', ', $products ),
					),
					array(
						'name'  => __( 'Status', 'upsell-order-bump-offer-for-woocommerce' ),
						'value' => $row['status'],
					),
					array(
						'name'  => __( 'Last updated (UTC)', 'upsell-order-bump-offer-for-woocommerce' ),
						'value' => $row['updated_at'],
					),
				),
			);
		}
		return array(
			'data' => $data,
			'done' => true,
		);
	}

	/**
	 * Personal data eraser.
	 *
	 * @param string $email Email.
	 * @return array
	 */
	public static function erase( $email ) {
		$rows = self::rows_for_email( $email );
		foreach ( $rows as $row ) {
			Upsell_Order_Bump_Acr_Db::delete( $row['id'] );
		}
		return array(
			'items_removed'  => count( $rows ) > 0,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
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
		$content = '<p>' . esc_html__( 'When cart recovery is enabled, the contents of your cart and your email address (as you type it at checkout, or from your account) are stored so we can email you a reminder if you do not complete your order. Reminders may include a personalised product offer or coupon. You can opt out under the email field at checkout or with the unsubscribe link in any reminder. Saved carts are deleted automatically after a limited period.', 'upsell-order-bump-offer-for-woocommerce' ) . '</p>';
		wp_add_privacy_policy_content( __( 'Upsell Order Bump Offer — Cart Recovery', 'upsell-order-bump-offer-for-woocommerce' ), wp_kses_post( $content ) );
	}
}

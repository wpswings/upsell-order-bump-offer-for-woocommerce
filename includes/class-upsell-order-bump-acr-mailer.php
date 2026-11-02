<?php
/**
 * Native cart abandonment upsell recovery - emails.
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
 * Renders and sends recovery emails through the WooCommerce mailer.
 *
 * @since 3.1.8
 */
class Upsell_Order_Bump_Acr_Mailer {

	/**
	 * Recovery URL.
	 *
	 * @param array $row       Cart row.
	 * @param bool  $add_offer Also add the offer.
	 * @return string
	 */
	public static function recovery_url( $row, $add_offer = false ) {
		$args = array( 'wps_ubo_acr' => $row['token'] );
		if ( $add_offer ) {
			$args['wps_ubo_acr_add'] = 1;
		}
		return add_query_arg( $args, home_url( '/' ) );
	}

	/**
	 * Signed unsubscribe URL.
	 *
	 * @param array $row Cart row.
	 * @return string
	 */
	public static function unsubscribe_url( $row ) {
		return add_query_arg(
			array(
				'wps_ubo_acr_unsub' => $row['token'],
				'sig'               => self::unsubscribe_sig( $row['token'] ),
			),
			home_url( '/' )
		);
	}

	/**
	 * Unsubscribe signature.
	 *
	 * @param string $token Token.
	 * @return string
	 */
	public static function unsubscribe_sig( $token ) {
		return substr( wp_hash( $token . '|unsub' ), 0, 20 );
	}

	/**
	 * Inline-styled button.
	 *
	 * @param string $url   URL.
	 * @param string $label Label.
	 * @return string
	 */
	private static function button( $url, $label ) {
		$color = get_option( 'woocommerce_email_base_color', '#7f54b3' );
		return sprintf(
			'<p style="text-align:center;margin:24px 0;"><a href="%1$s" style="display:inline-block;padding:12px 28px;background:%2$s;color:#ffffff;text-decoration:none;border-radius:4px;font-weight:bold;">%3$s</a></p>',
			esc_url( $url ),
			esc_attr( $color ),
			esc_html( $label )
		);
	}

	/**
	 * Cart items table.
	 *
	 * @param array $row Cart row.
	 * @return string
	 */
	public static function cart_table( $row ) {
		$lines = '';
		foreach ( (array) $row['cart'] as $item ) {
			$product = wc_get_product( ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'] );
			if ( ! $product ) {
				continue;
			}
			$image  = wp_get_attachment_image_url( $product->get_image_id() ? $product->get_image_id() : ( $product->get_parent_id() ? get_post_thumbnail_id( $product->get_parent_id() ) : 0 ), 'thumbnail' );
			$image  = $image ? $image : wc_placeholder_img_src( 'thumbnail' );
			$lines .= sprintf(
				'<tr><td style="padding:8px;border-bottom:1px solid #eee;width:64px;"><img src="%1$s" width="56" height="56" alt="" style="display:block;border-radius:4px;"></td><td style="padding:8px;border-bottom:1px solid #eee;">%2$s × %3$d</td><td style="padding:8px;border-bottom:1px solid #eee;text-align:right;white-space:nowrap;">%4$s</td></tr>',
				esc_url( $image ),
				esc_html( $product->get_name() ),
				(int) $item['quantity'],
				wp_kses_post( wc_price( (float) $product->get_price() * (int) $item['quantity'], array( 'currency' => $row['currency'] ) ) )
			);
		}
		if ( '' === $lines ) {
			return '';
		}
		return '<table cellspacing="0" cellpadding="0" style="width:100%;border-collapse:collapse;margin:12px 0;">' . $lines . '</table>';
	}

	/**
	 * Offer block (image, name, prices, "Add to my order" button).
	 *
	 * @param array $row Cart row.
	 * @return string
	 */
	public static function offer_block( $row ) {
		$product = ! empty( $row['offer_product'] ) ? wc_get_product( $row['offer_product'] ) : null;
		if ( ! $product ) {
			return '';
		}
		$price  = Upsell_Order_Bump_Acr_Engine::offer_price( $product, (int) $row['offer_discount'] );
		$normal = (float) ( $product->get_regular_price() ? $product->get_regular_price() : $product->get_price() );
		$image  = wp_get_attachment_image_url( $product->get_image_id() ? $product->get_image_id() : ( $product->get_parent_id() ? get_post_thumbnail_id( $product->get_parent_id() ) : 0 ), 'woocommerce_thumbnail' );
		$image  = $image ? $image : wc_placeholder_img_src( 'woocommerce_thumbnail' );

		$prices = $price < $normal
			? '<del style="color:#888;">' . wp_strip_all_tags( wc_price( $normal, array( 'currency' => $row['currency'] ) ) ) . '</del> <strong style="font-size:18px;">' . wp_strip_all_tags( wc_price( $price, array( 'currency' => $row['currency'] ) ) ) . '</strong>'
			: '<strong style="font-size:18px;">' . wp_strip_all_tags( wc_price( $price, array( 'currency' => $row['currency'] ) ) ) . '</strong>';

		return '<table cellspacing="0" cellpadding="0" style="width:100%;border:2px dashed #dcdcde;border-radius:8px;margin:12px 0;"><tr>'
			. '<td style="padding:12px;width:120px;"><img src="' . esc_url( $image ) . '" width="110" alt="" style="display:block;border-radius:6px;"></td>'
			. '<td style="padding:12px;">'
			. ( (int) $row['offer_discount'] > 0 ? '<p style="margin:0 0 4px;font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#d63638;font-weight:bold;">' . esc_html( sprintf( /* translators: %d: percent. */ __( 'Exclusive %d%% off', 'upsell-order-bump-offer-for-woocommerce' ), (int) $row['offer_discount'] ) ) . '</p>' : '' )
			. '<p style="margin:0 0 6px;font-size:16px;font-weight:bold;">' . esc_html( $product->get_name() ) . '</p>'
			. '<p style="margin:0 0 10px;">' . wp_kses_post( $prices ) . '</p>'
			. '<a href="' . esc_url( self::recovery_url( $row, true ) ) . '" style="display:inline-block;padding:8px 18px;background:#00a32a;color:#fff;text-decoration:none;border-radius:4px;font-weight:bold;">' . esc_html__( 'Add to my order', 'upsell-order-bump-offer-for-woocommerce' ) . '</a>'
			. '</td></tr></table>';
	}

	/**
	 * Placeholder values for a row.
	 *
	 * @param array $row  Cart row.
	 * @param array $step Step.
	 * @return array
	 */
	public static function placeholders( $row, $step ) {
		$product    = ! empty( $row['offer_product'] ) ? wc_get_product( $row['offer_product'] ) : null;
		$with_offer = 'on' === $step['include_offer'] && $product;
		$coupon     = $row['coupon_code'] && (int) $step['coupon'] > 0 ? new WC_Coupon( $row['coupon_code'] ) : null;

		// Preview / test emails use a sample row (id 0): show an example code instead of creating a coupon.
		if ( empty( $row['id'] ) && (int) $step['coupon'] > 0 ) {
			$coupon = null;
			$sample = array(
				'{coupon_code}'    => '<strong style="font-family:monospace;font-size:16px;">ACR-SAMPLE</strong>',
				'{coupon_percent}' => (int) $step['coupon'] . '%',
			);
		}

		return ( isset( $sample ) ? $sample : array() ) + array(
			'{customer_name}'    => esc_html( $row['first_name'] ? $row['first_name'] : __( 'there', 'upsell-order-bump-offer-for-woocommerce' ) ),
			'{site_name}'        => esc_html( wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) ),
			'{cart_items}'       => self::cart_table( $row ),
			'{cart_total}'       => wp_kses_post( wc_price( (float) $row['cart_total'], array( 'currency' => $row['currency'] ) ) ),
			'{recovery_link}'    => esc_url( self::recovery_url( $row ) ),
			'{recovery_button}'  => self::button( self::recovery_url( $row ), __( 'Complete my order', 'upsell-order-bump-offer-for-woocommerce' ) ),
			'{offer_block}'      => $with_offer ? self::offer_block( $row ) : '',
			'{offer_product}'    => $with_offer ? esc_html( $product->get_name() ) : '',
			'{offer_price}'      => $with_offer ? wp_kses_post( wc_price( Upsell_Order_Bump_Acr_Engine::offer_price( $product, (int) $row['offer_discount'] ), array( 'currency' => $row['currency'] ) ) ) : '',
			'{offer_discount}'   => $with_offer ? (int) $row['offer_discount'] . '%' : '',
			'{coupon_code}'      => $coupon && $coupon->get_id() ? '<strong style="font-family:monospace;font-size:16px;">' . esc_html( strtoupper( $coupon->get_code() ) ) . '</strong>' : '',
			'{coupon_percent}'   => $coupon && $coupon->get_id() ? (int) $coupon->get_amount() . '%' : '',
			'{unsubscribe_link}' => esc_url( self::unsubscribe_url( $row ) ),
		);
	}

	/**
	 * Render a step for a row.
	 *
	 * @param array $row   Cart row.
	 * @param int   $index Step index.
	 * @return array { subject, heading, html }
	 */
	public static function render( $row, $index ) {
		$settings = Upsell_Order_Bump_Acr_Db::get_settings();
		$step     = $settings['steps'][ $index ];

		/**
		 * Filters a recovery email before rendering.
		 *
		 * @since 3.1.8
		 * @param array $args  { subject, heading, body }.
		 * @param array $row   Cart row.
		 * @param array $step  Step settings.
		 */
		$args = apply_filters(
			'wps_ubo_acr_email_args',
			array(
				'subject' => $step['subject'],
				'heading' => $step['heading'],
				'body'    => $step['body'],
			),
			$row,
			$step
		);

		$map  = self::placeholders( $row, $step );
		$body = wpautop( wp_kses_post( $args['body'] ) );
		// Block placeholders sit alone in a paragraph after wpautop; unwrap them.
		foreach ( array( '{cart_items}', '{offer_block}', '{recovery_button}' ) as $block ) {
			$body = str_replace( array( '<p>' . $block . '</p>', '<p>' . $block . "<br />\n", $block . '</p>' ), array( $block, $block, $block ), $body );
		}
		$body = strtr( $body, $map );

		if ( false === strpos( $args['body'], '{unsubscribe_link}' ) ) {
			$body .= '<p style="margin-top:24px;font-size:12px;color:#888;text-align:center;">'
				. sprintf(
					/* translators: %s: unsubscribe link. */
					esc_html__( "Don't want these reminders? %s", 'upsell-order-bump-offer-for-woocommerce' ),
					'<a href="' . $map['{unsubscribe_link}'] . '" style="color:#888;">' . esc_html__( 'Unsubscribe', 'upsell-order-bump-offer-for-woocommerce' ) . '</a>'
				)
				. '</p>';
		}

		$plain = array_map( 'wp_strip_all_tags', $map );
		$heading = strtr( $args['heading'], $plain );
		$mailer  = WC()->mailer();

		return array(
			// Subjects are plain text: decode entities such as the &#8377; from wc_price().
			'subject' => html_entity_decode( wp_strip_all_tags( strtr( $args['subject'], $plain ) ), ENT_QUOTES, 'UTF-8' ),
			'heading' => $heading,
			'html'    => $mailer->wrap_message( $heading, $body ),
		);
	}

	/**
	 * Send a step to the row's email.
	 *
	 * @param array  $row   Cart row.
	 * @param int    $index Step index.
	 * @param string $to    Override recipient (test emails).
	 * @return bool
	 */
	public static function send_step( $row, $index, $to = '' ) {
		$email = self::render( $row, $index );
		$email_obj = new WC_Email();
		$html      = $email_obj->style_inline( $email['html'] );
		return (bool) WC()->mailer()->send( $to ? $to : $row['email'], $email['subject'], $html, array( 'Content-Type: text/html; charset=UTF-8' ) );
	}

	/**
	 * Sample row for previews / tests.
	 *
	 * @return array
	 */
	public static function sample_row() {
		$settings = Upsell_Order_Bump_Acr_Db::get_settings();
		$ids      = wc_get_products(
			array(
				'status'       => 'publish',
				'type'         => 'simple',
				'stock_status' => 'instock',
				'limit'        => 3,
				'return'       => 'ids',
			)
		);
		$cart  = array();
		$total = 0;
		foreach ( array_slice( $ids, 0, 2 ) as $id ) {
			$cart[] = array(
				'product_id'   => $id,
				'variation_id' => 0,
				'quantity'     => 1,
				'variation'    => array(),
			);
			$total += (float) wc_get_product( $id )->get_price();
		}
		$user = wp_get_current_user();
		$row  = array(
			'id'             => 0,
			'token'          => 'preview' . str_repeat( '0', 25 ), // Never stored; links in previews do nothing.
			'email'          => $user->user_email,
			'first_name'     => $user->first_name ? $user->first_name : $user->display_name,
			'user_id'        => 0,
			'cart'           => $cart,
			'cart_total'     => $total,
			'currency'       => get_woocommerce_currency(),
			'offer_product'  => 0,
			'offer_discount' => (int) $settings['offer_discount'],
			'coupon_code'    => '',
		);
		$offer = Upsell_Order_Bump_Acr_Engine::select_offer( $row );
		if ( ! $offer && isset( $ids[2] ) ) {
			$offer = array(
				'product_id' => $ids[2],
				'discount'   => (int) $settings['offer_discount'],
			);
		}
		if ( $offer ) {
			$row['offer_product']  = $offer['product_id'];
			$row['offer_discount'] = $offer['discount'];
		}
		return $row;
	}
}

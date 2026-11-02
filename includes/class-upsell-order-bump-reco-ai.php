<?php
/**
 * Smart Offer Recommendations - WordPress AI Client / Connectors integration.
 *
 * Uses the AI provider the site owner connected under Settings → Connectors
 * (WordPress 7.0+). This plugin never reads or stores API keys. Only catalog
 * data (names, categories, prices, short descriptions) is sent to the provider.
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
 * LLM enrichment through the WordPress AI Client.
 *
 * @since 3.1.8
 */
class Upsell_Order_Bump_Reco_Ai {

	/**
	 * Max candidates sent per anchor.
	 *
	 * @var int
	 */
	const MAX_CANDIDATES = 15;

	/**
	 * Minimum LLM score for a cold-start (no purchase data) pair to be stored.
	 *
	 * @var float
	 */
	const COLD_START_MIN = 0.5;

	/**
	 * Whether the WordPress AI Client exists (WP 7.0+).
	 *
	 * @return bool
	 */
	public static function client_exists() {
		return function_exists( 'wp_ai_client_prompt' ) && function_exists( 'wp_get_connectors' );
	}

	/**
	 * AI provider connectors registered in WordPress, with their state.
	 *
	 * @return array id => [ name, description, installed, has_key, credentials_url ]
	 */
	public static function get_ai_connectors() {
		if ( ! self::client_exists() ) {
			return array();
		}

		$out = array();
		foreach ( wp_get_connectors() as $id => $connector ) {
			if ( empty( $connector['type'] ) || 'ai_provider' !== $connector['type'] ) {
				continue;
			}

			$installed = false;
			if ( ! empty( $connector['plugin']['is_active'] ) && is_callable( $connector['plugin']['is_active'] ) ) {
				try {
					$installed = (bool) call_user_func( $connector['plugin']['is_active'] );
				} catch ( Throwable $e ) {
					$installed = false;
				}
			}

			$auth    = isset( $connector['authentication'] ) ? $connector['authentication'] : array();
			$has_key = 'none' === ( isset( $auth['method'] ) ? $auth['method'] : '' ) || self::connector_has_key( $auth );

			$out[ $id ] = array(
				'name'            => isset( $connector['name'] ) ? $connector['name'] : $id,
				'description'     => isset( $connector['description'] ) ? $connector['description'] : '',
				'installed'       => $installed,
				'has_key'         => $has_key,
				'credentials_url' => isset( $auth['credentials_url'] ) ? $auth['credentials_url'] : '',
			);
		}
		return $out;
	}

	/**
	 * Whether a connector has credentials (env var, constant or saved setting).
	 * Only checks presence; the value is never read into this plugin.
	 *
	 * @param array $auth Connector authentication config.
	 * @return bool
	 */
	private static function connector_has_key( $auth ) {
		if ( ! empty( $auth['env_var_name'] ) ) {
			$env = getenv( $auth['env_var_name'] );
			if ( false !== $env && '' !== $env ) {
				return true;
			}
		}
		if ( ! empty( $auth['constant_name'] ) && defined( $auth['constant_name'] ) && '' !== (string) constant( $auth['constant_name'] ) ) {
			return true;
		}
		return ! empty( $auth['setting_name'] ) && '' !== (string) get_option( $auth['setting_name'], '' );
	}

	/**
	 * Overall AI availability for the current settings.
	 *
	 * @param array|null $settings Settings (defaults to saved).
	 * @return array { available: bool, reason: string, connectors: array, provider: string }
	 */
	public static function status( $settings = null ) {
		$settings = $settings ? $settings : Upsell_Order_Bump_Reco_Db::get_settings();
		$status   = array(
			'available'    => false,
			'reason'       => '',
			'connectors'   => self::get_ai_connectors(),
			'provider'     => (string) $settings['ai_provider'],
			'settings_url' => admin_url( 'options-connectors.php' ),
		);

		if ( ! self::client_exists() ) {
			$status['reason'] = __( 'The WordPress AI Client requires WordPress 7.0 or newer.', 'upsell-order-bump-offer-for-woocommerce' );
			return $status;
		}
		if ( function_exists( 'wp_supports_ai' ) && ! wp_supports_ai() ) {
			$status['reason'] = __( 'AI features are disabled on this site (WP_AI_SUPPORT or the wp_supports_ai filter).', 'upsell-order-bump-offer-for-woocommerce' );
			return $status;
		}

		$ready = array_filter(
			$status['connectors'],
			static function ( $c ) {
				return $c['installed'] && $c['has_key'];
			}
		);
		if ( empty( $ready ) ) {
			$status['reason'] = __( 'No AI provider is connected. Install a provider and add its API key under Settings → Connectors.', 'upsell-order-bump-offer-for-woocommerce' );
			return $status;
		}
		if ( '' !== $status['provider'] && ! isset( $ready[ $status['provider'] ] ) ) {
			$status['reason'] = __( 'The selected AI provider is not installed or has no API key.', 'upsell-order-bump-offer-for-woocommerce' );
			return $status;
		}

		try {
			$supported = self::builder( 'ping', $settings )->is_supported_for_text_generation();
		} catch ( Throwable $e ) {
			$supported = false;
		}
		if ( true !== $supported ) {
			$status['reason'] = __( 'No connected AI model supports text generation.', 'upsell-order-bump-offer-for-woocommerce' );
			return $status;
		}

		$status['available'] = true;
		return $status;
	}

	/**
	 * Prompt builder bound to the chosen provider.
	 *
	 * @param mixed $prompt   Prompt.
	 * @param array $settings Settings.
	 * @return WP_AI_Client_Prompt_Builder
	 */
	private static function builder( $prompt, $settings ) {
		$builder = wp_ai_client_prompt( $prompt );
		if ( ! empty( $settings['ai_provider'] ) ) {
			$builder = $builder->using_provider( $settings['ai_provider'] );
		}
		return $builder;
	}

	/**
	 * Send a tiny prompt to check the connection.
	 *
	 * @param array $settings Settings.
	 * @return string|WP_Error Model reply or error.
	 */
	public static function test_connection( $settings ) {
		$status = self::status( $settings );
		if ( ! $status['available'] ) {
			return new WP_Error( 'wps_ubo_reco_ai_unavailable', $status['reason'] );
		}
		try {
			$reply = self::builder( 'Reply with the single word: OK', $settings )
				->using_max_tokens( 10 )
				->generate_text();
		} catch ( Throwable $e ) {
			return new WP_Error( 'wps_ubo_reco_ai_failed', $e->getMessage() );
		}
		return is_wp_error( $reply ) ? $reply : sanitize_text_field( (string) $reply );
	}

	/**
	 * Catalog-only description of a product for the prompt.
	 *
	 * @param int $product_id Product id.
	 * @return array|null
	 */
	private static function describe( $product_id ) {
		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return null;
		}
		$terms = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'names' ) );
		$desc  = $product->get_short_description() ? $product->get_short_description() : $product->get_description();

		return array(
			'id'          => (int) $product_id,
			'name'        => wp_strip_all_tags( $product->get_name() ),
			'categories'  => is_wp_error( $terms ) ? array() : array_slice( array_map( 'wp_strip_all_tags', $terms ), 0, 3 ),
			'price'       => (float) $product->get_price(),
			'description' => wp_html_excerpt( wp_strip_all_tags( strip_shortcodes( (string) $desc ) ), 160, '…' ),
		);
	}

	/**
	 * Enrich the staging model with LLM complementarity scores.
	 * Called during training, before tables are swapped.
	 *
	 * @param array $settings Settings.
	 * @return array { ai_status, ai_anchors, ai_error }
	 */
	public static function enrich( $settings ) {
		global $wpdb;

		$status = self::status( $settings );
		if ( ! $status['available'] ) {
			return array(
				'ai_status'  => 'skipped',
				'ai_anchors' => 0,
				'ai_error'   => $status['reason'],
			);
		}

		$pairs  = Upsell_Order_Bump_Reco_Db::pairs_table( true );
		$prods  = Upsell_Order_Bump_Reco_Db::products_table( true );
		$weight = (int) $settings['ai_weight'] / 100;

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery
		$anchors      = $wpdb->get_col( $wpdb->prepare( "SELECT product_id FROM {$prods} ORDER BY order_count DESC LIMIT %d", (int) $settings['ai_anchor_limit'] ) );
		$best_sellers = $wpdb->get_col( "SELECT product_id FROM {$prods} ORDER BY order_count DESC LIMIT 60" );

		$done     = 0;
		$failures = 0;
		$error    = '';

		foreach ( $anchors as $anchor_id ) {
			$anchor_id = (int) $anchor_id;
			$anchor    = self::describe( $anchor_id );
			if ( ! $anchor ) {
				continue;
			}

			$ml_ids = array_map( 'intval', $wpdb->get_col( $wpdb->prepare( "SELECT target_id FROM {$pairs} WHERE source_id = %d ORDER BY score DESC LIMIT %d", $anchor_id, self::MAX_CANDIDATES ) ) );

			$candidate_ids = $ml_ids;
			if ( 'on' === $settings['ai_cold_start'] && count( $ml_ids ) < 5 ) {
				foreach ( $best_sellers as $id ) {
					$id = (int) $id;
					if ( $id !== $anchor_id && ! in_array( $id, $candidate_ids, true ) ) {
						$candidate_ids[] = $id;
					}
					if ( count( $candidate_ids ) >= self::MAX_CANDIDATES ) {
						break;
					}
				}
			}

			$candidates = array();
			foreach ( $candidate_ids as $id ) {
				$info = self::describe( $id );
				$p    = $info ? wc_get_product( $id ) : null;
				if ( $info && $p && 'publish' === $p->get_status() ) {
					$candidates[ $id ] = $info;
				}
			}
			if ( empty( $candidates ) ) {
				continue;
			}

			$result = self::ask( $anchor, array_values( $candidates ), $settings );
			if ( is_wp_error( $result ) ) {
				$error = $result->get_error_message();
				if ( ++$failures >= 3 ) {
					break;
				}
				continue;
			}
			$failures = 0;
			++$done;

			foreach ( $result as $target_id => $item ) {
				if ( ! isset( $candidates[ $target_id ] ) ) {
					continue;
				}
				if ( in_array( $target_id, $ml_ids, true ) ) {
					$wpdb->query(
						$wpdb->prepare(
							"UPDATE {$pairs} SET ai_score = %f, ai_reason = %s, score = LEAST(1, (1 - %f) * ml_score + %f * %f) WHERE source_id = %d AND target_id = %d",
							$item['score'],
							$item['reason'],
							$weight,
							$weight,
							$item['score'],
							$anchor_id,
							$target_id
						)
					);
				} elseif ( $item['score'] >= self::COLD_START_MIN ) {
					$wpdb->query(
						$wpdb->prepare(
							"INSERT INTO {$pairs} (source_id, target_id, co_count, seq_count, ml_score, lift, ai_score, ai_reason, score)
							VALUES (%d, %d, 0, 0, 0, 0, %f, %s, %f)
							ON DUPLICATE KEY UPDATE ai_score = VALUES(ai_score), ai_reason = VALUES(ai_reason), score = VALUES(score)",
							$anchor_id,
							$target_id,
							$item['score'],
							$item['reason'],
							$weight * $item['score']
						)
					);
				}
			}
		}
		// phpcs:enable

		return array(
			'ai_status'  => $done > 0 ? 'done' : ( $error ? 'error' : 'skipped' ),
			'ai_anchors' => $done,
			'ai_error'   => $error ? sanitize_text_field( $error ) : '',
		);
	}

	/**
	 * Ask the model to score candidates for one anchor.
	 *
	 * @param array $anchor     Anchor description.
	 * @param array $candidates Candidate descriptions.
	 * @param array $settings   Settings.
	 * @return array|WP_Error target_id => [ score, reason ]
	 */
	private static function ask( $anchor, $candidates, $settings ) {
		$payload = array(
			'anchor'     => $anchor,
			'candidates' => $candidates,
		);

		/**
		 * Filters the catalog payload sent to the AI provider.
		 * Must remain catalog-only (no customer or order data).
		 *
		 * @since 3.1.8
		 * @param array $payload   { anchor, candidates }.
		 * @param int   $anchor_id Anchor product id.
		 */
		$payload = (array) apply_filters( 'wps_ubo_reco_ai_prompt', $payload, $anchor['id'] );

		$instruction = 'You are an e-commerce merchandising expert. A shopper is buying the "anchor" product. '
			. 'For EACH candidate, estimate from 0 to 1 how likely the shopper would also add it as a one-click add-on '
			. '(checkout order bump or post-purchase upsell). Score complements and accessories high; score substitutes, '
			. 'near-duplicates and unrelated items low. Use only the candidate ids given. '
			. 'Respond with JSON only: {"recommendations":[{"product_id":<int>,"score":<0..1>,"reason":"<max 15 words>"}]}.';

		$schema = array(
			'type'       => 'object',
			'properties' => array(
				'recommendations' => array(
					'type'  => 'array',
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'product_id' => array( 'type' => 'integer' ),
							'score'      => array( 'type' => 'number' ),
							'reason'     => array( 'type' => 'string' ),
						),
						'required'   => array( 'product_id', 'score', 'reason' ),
					),
				),
			),
			'required'   => array( 'recommendations' ),
		);

		try {
			$text = self::builder( wp_json_encode( $payload ), $settings )
				->using_system_instruction( $instruction )
				->using_temperature( 0.2 )
				->using_max_tokens( 800 )
				->as_json_response( $schema )
				->generate_text();
		} catch ( Throwable $e ) {
			return new WP_Error( 'wps_ubo_reco_ai_failed', $e->getMessage() );
		}

		if ( is_wp_error( $text ) ) {
			return $text;
		}

		return self::parse( (string) $text );
	}

	/**
	 * Parse and validate the model's JSON.
	 *
	 * @param string $text Raw reply.
	 * @return array|WP_Error target_id => [ score, reason ]
	 */
	public static function parse( $text ) {
		$text = trim( $text );
		// Tolerate ```json fences.
		if ( preg_match( '/\{.*\}/s', $text, $m ) ) {
			$text = $m[0];
		}
		$data = json_decode( $text, true );
		if ( ! is_array( $data ) || empty( $data['recommendations'] ) || ! is_array( $data['recommendations'] ) ) {
			return new WP_Error( 'wps_ubo_reco_ai_bad_json', __( 'The AI provider returned an unexpected response.', 'upsell-order-bump-offer-for-woocommerce' ) );
		}

		$out = array();
		foreach ( $data['recommendations'] as $item ) {
			if ( ! is_array( $item ) || ! isset( $item['product_id'], $item['score'] ) || ! is_numeric( $item['product_id'] ) || ! is_numeric( $item['score'] ) ) {
				continue;
			}
			$out[ absint( $item['product_id'] ) ] = array(
				'score'  => (float) max( 0, min( 1, (float) $item['score'] ) ),
				'reason' => isset( $item['reason'] ) ? mb_substr( sanitize_text_field( (string) $item['reason'] ), 0, 191 ) : '',
			);
		}
		return $out;
	}
}

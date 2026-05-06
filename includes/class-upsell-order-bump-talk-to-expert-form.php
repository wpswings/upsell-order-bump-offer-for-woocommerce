<?php
/**
 * Handle the admin Talk to an Expert workflow for the Upsell Order Bump plugin.
 *
 * Mirrors the implementation shipped with woo-gift-cards-lite so the
 * upsell-order-bump dashboard can offer the same lead-capture experience.
 *
 * @package upsell-order-bump-offer-for-woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Upsell_Order_Bump_Talk_To_Expert_Form' ) ) :

	/**
	 * Talk to an Expert form handler for upsell-order-bump.
	 */
	class Upsell_Order_Bump_Talk_To_Expert_Form {

		const AJAX_ACTION    = 'wps_ubo_submit_talk_to_expert';
		const NONCE_ACTION   = 'wps_ubo_talk_to_expert_nonce';
		const HUBSPOT_PORTAL_ID = '25444144';
		const HUBSPOT_FORM_ID   = 'eab973a7-5c65-4264-a31d-3b1b10b82c82';

		/**
		 * Get the marketing-services URL used across the card.
		 */
		public static function get_services_landing_url() {
			return 'https://wpswings.com/woocommerce-services/?utm_source=wpswings-ubo-services&utm_medium=ubo-org-backend&utm_campaign=woocommerce-services';
		}

		/**
		 * Get service option labels keyed by submitted slug values.
		 */
		public static function get_service_options() {
			return array(
				'seo_services'                     => esc_html__( 'SEO services', 'upsell-order-bump-offer-for-woocommerce' ),
				'google_ads_setup_and_ga4_setup'   => esc_html__( 'Google Ads Setup and GA4 setup', 'upsell-order-bump-offer-for-woocommerce' ),
				'speed_optimization'               => esc_html__( 'Speed Optimization', 'upsell-order-bump-offer-for-woocommerce' ),
				'woocommerce_development_services' => esc_html__( 'WooCommerce Development Services', 'upsell-order-bump-offer-for-woocommerce' ),
			);
		}

		/**
		 * Get budget option labels keyed by submitted values.
		 */
		public static function get_budget_options() {
			return array(
				''            => esc_html__( 'Please Select', 'upsell-order-bump-offer-for-woocommerce' ),
				'500-1000'    => '$500 - $1000',
				'1001-5000'   => '$1001 - $5000',
				'5001-10000'  => '$5001 - $10000',
				'10001-15000' => '$10001 - $15000',
			);
		}

		/**
		 * Get default field values from the current user.
		 */
		public static function get_default_form_values() {
			$user       = function_exists( 'wp_get_current_user' ) ? wp_get_current_user() : null;
			$first_name = ! empty( $user->user_firstname ) ? (string) $user->user_firstname : '';
			$last_name  = ! empty( $user->user_lastname ) ? (string) $user->user_lastname : '';
			$email      = ! empty( $user->user_email ) ? (string) $user->user_email : '';

			if ( ( '' === $first_name || '' === $last_name ) && ! empty( $user->display_name ) ) {
				$display_name_parts = preg_split( '/\s+/', trim( (string) $user->display_name ) );

				if ( '' === $first_name && ! empty( $display_name_parts[0] ) ) {
					$first_name = $display_name_parts[0];
				}

				if ( '' === $last_name && count( $display_name_parts ) > 1 ) {
					array_shift( $display_name_parts );
					$last_name = implode( ' ', $display_name_parts );
				}
			}

			return array(
				'firstname' => $first_name,
				'lastname'  => $last_name,
				'email'     => $email,
				'phone'     => '',
				'budget'    => '',
				'message'   => '',
			);
		}

		/**
		 * Plugin label used in the HubSpot payload.
		 */
		public static function get_plugin_label() {
			return defined( 'ONBOARD_PLUGIN_NAME' )
				? (string) ONBOARD_PLUGIN_NAME
				: 'Upsell Order Bump Offer for WooCommerce';
		}

		/**
		 * Render the modal markup.
		 */
		public static function render_modal() {
			$defaults        = self::get_default_form_values();
			$service_options = self::get_service_options();
			$budget_options  = self::get_budget_options();
			?>
			<div class="wps-ubo-expert-modal" data-wps-ubo-expert-modal hidden>
				<div class="wps-ubo-expert-modal__backdrop" data-wps-ubo-expert-modal-close></div>
				<div class="wps-ubo-expert-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="wps-ubo-expert-modal-title">
					<button type="button" class="wps-ubo-expert-modal__close" data-wps-ubo-expert-modal-close aria-label="<?php echo esc_attr__( 'Close Talk to an Expert form', 'upsell-order-bump-offer-for-woocommerce' ); ?>">
						<span aria-hidden="true">&times;</span>
					</button>
					<div class="wps-ubo-expert-modal__header">
						<h2 id="wps-ubo-expert-modal-title"><?php esc_html_e( 'Talk to an Expert', 'upsell-order-bump-offer-for-woocommerce' ); ?></h2>
						<p><?php esc_html_e( 'Share your store goals and our team will reach out with the right next step.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
					</div>
					<div class="wps-ubo-expert-modal__panel">
						<div class="wps-ubo-expert-modal__status" data-wps-ubo-expert-modal-status hidden aria-live="polite"></div>
						<form class="wps-ubo-expert-modal__form" data-wps-ubo-expert-modal-form>
							<div class="wps-ubo-expert-modal__grid">
								<div class="wps-ubo-expert-modal__field">
									<label for="wps_ubo_expert_firstname"><?php esc_html_e( 'First Name', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									<input id="wps_ubo_expert_firstname" type="text" name="firstname" value="<?php echo esc_attr( $defaults['firstname'] ); ?>" placeholder="<?php esc_attr_e( 'John', 'upsell-order-bump-offer-for-woocommerce' ); ?>" autocomplete="given-name">
								</div>
								<div class="wps-ubo-expert-modal__field">
									<label for="wps_ubo_expert_lastname"><?php esc_html_e( 'Last Name', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									<input id="wps_ubo_expert_lastname" type="text" name="lastname" value="<?php echo esc_attr( $defaults['lastname'] ); ?>" placeholder="<?php esc_attr_e( 'Doe', 'upsell-order-bump-offer-for-woocommerce' ); ?>" autocomplete="family-name">
								</div>
								<div class="wps-ubo-expert-modal__field wps-ubo-expert-modal__field--span-2">
									<label for="wps_ubo_expert_email"><?php esc_html_e( 'Work Email', 'upsell-order-bump-offer-for-woocommerce' ); ?> <span class="wps-ubo-expert-modal__required">*</span></label>
									<input id="wps_ubo_expert_email" type="email" name="email" value="<?php echo esc_attr( $defaults['email'] ); ?>" required placeholder="<?php esc_attr_e( 'name@yourstore.com', 'upsell-order-bump-offer-for-woocommerce' ); ?>" autocomplete="email">
								</div>
								<div class="wps-ubo-expert-modal__field">
									<label for="wps_ubo_expert_phone"><?php esc_html_e( 'Contact Number', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
									<input id="wps_ubo_expert_phone" type="text" name="phone" value="<?php echo esc_attr( $defaults['phone'] ); ?>" placeholder="<?php esc_attr_e( '+1 000 000 0000', 'upsell-order-bump-offer-for-woocommerce' ); ?>">
								</div>
							</div>
							<div class="wps-ubo-expert-modal__field">
								<span class="wps-ubo-expert-modal__legend"><?php esc_html_e( 'What services do you need help with?', 'upsell-order-bump-offer-for-woocommerce' ); ?></span>
								<div class="wps-ubo-expert-modal__checkboxes">
									<?php foreach ( $service_options as $service_key => $service_label ) : ?>
										<label class="wps-ubo-expert-modal__checkbox">
											<input type="checkbox" name="what_services_do_you_need_help_with[]" value="<?php echo esc_attr( $service_key ); ?>">
											<span><?php echo esc_html( $service_label ); ?></span>
										</label>
									<?php endforeach; ?>
								</div>
							</div>
							<div class="wps-ubo-expert-modal__field">
								<label for="wps_ubo_expert_budget"><?php esc_html_e( 'Budget', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
								<select id="wps_ubo_expert_budget" name="budget">
									<?php foreach ( $budget_options as $budget_value => $budget_label ) : ?>
										<option value="<?php echo esc_attr( $budget_value ); ?>"<?php echo '' === $budget_value ? ' selected disabled' : ''; ?>><?php echo esc_html( $budget_label ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="wps-ubo-expert-modal__field">
								<label for="wps_ubo_expert_message"><?php esc_html_e( 'What do you need help with?', 'upsell-order-bump-offer-for-woocommerce' ); ?></label>
								<textarea id="wps_ubo_expert_message" name="message" rows="4" placeholder="<?php esc_attr_e( 'Share your goals, blockers, or the service you need.', 'upsell-order-bump-offer-for-woocommerce' ); ?>"><?php echo esc_textarea( $defaults['message'] ); ?></textarea>
							</div>
							<div class="wps-ubo-expert-modal__actions">
								<button
									type="submit"
									class="wps-ubo-expert-modal__submit"
									data-submit-label="<?php echo esc_attr__( 'Submit Request', 'upsell-order-bump-offer-for-woocommerce' ); ?>"
									data-loading-label="<?php echo esc_attr__( 'Sending...', 'upsell-order-bump-offer-for-woocommerce' ); ?>"
								><?php esc_html_e( 'Submit Request', 'upsell-order-bump-offer-for-woocommerce' ); ?></button>
							</div>
						</form>
						<div class="wps-ubo-expert-modal__success" data-wps-ubo-expert-modal-success hidden aria-live="polite">
							<div class="wps-ubo-expert-modal__success-mark" aria-hidden="true">
								<span class="wps-ubo-expert-modal__success-ring"></span>
								<span class="wps-ubo-expert-modal__success-core">
									<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
										<path d="M6.5 12.5l3.4 3.4L17.8 8" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</span>
							</div>
							<h3><?php esc_html_e( 'Thank you', 'upsell-order-bump-offer-for-woocommerce' ); ?></h3>
							<p data-wps-ubo-expert-modal-success-message><?php esc_html_e( 'Thank you for submitting your request.', 'upsell-order-bump-offer-for-woocommerce' ); ?></p>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Handle the admin ajax submission.
		 */
		public function handle_ajax_submission() {
			check_ajax_referer( self::NONCE_ACTION, 'nonce' );

			if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'manage_woocommerce' ) ) {
				wp_send_json_error( array( 'message' => esc_html__( 'You are not allowed to submit this request.', 'upsell-order-bump-offer-for-woocommerce' ) ), 403 );
			}

			$form_data = isset( $_POST['form_data'] ) ? wp_unslash( $_POST['form_data'] ) : '';
			$form_data = is_string( $form_data ) ? json_decode( $form_data, true ) : array();

			if ( ! is_array( $form_data ) ) {
				wp_send_json_error( array( 'message' => esc_html__( 'Invalid request payload.', 'upsell-order-bump-offer-for-woocommerce' ) ), 400 );
			}

			$sanitized_data = self::sanitize_submission( $form_data );

			if ( empty( $sanitized_data['email'] ) || ! is_email( $sanitized_data['email'] ) ) {
				wp_send_json_error( array( 'message' => esc_html__( 'Please enter a valid email address.', 'upsell-order-bump-offer-for-woocommerce' ) ), 400 );
			}

			$response = wp_remote_post( self::get_hubspot_endpoint(), self::get_hubspot_request_args( $sanitized_data ) );

			if ( is_wp_error( $response ) ) {
				wp_send_json_error( array( 'message' => esc_html__( 'We could not submit your request right now. Please try again.', 'upsell-order-bump-offer-for-woocommerce' ) ), 500 );
			}

			$response_code = (int) wp_remote_retrieve_response_code( $response );
			$response_body = json_decode( wp_remote_retrieve_body( $response ), true );
			$message       = self::get_hubspot_response_message( $response_body, $response_code );

			if ( $response_code >= 200 && $response_code < 300 ) {
				wp_send_json_success( array( 'message' => $message ) );
			}

			wp_send_json_error( array( 'message' => $message ), $response_code > 0 ? $response_code : 500 );
		}

		/**
		 * Sanitize the posted form payload.
		 */
		public static function sanitize_submission( $form_data ) {
			$form_data          = is_array( $form_data ) ? $form_data : array();
			$services           = self::get_service_options();
			$budgets            = self::get_budget_options();
			$submitted_services = array();

			if ( isset( $form_data['what_services_do_you_need_help_with'] ) ) {
				$raw_services = wp_unslash( $form_data['what_services_do_you_need_help_with'] );
				if ( ! is_array( $raw_services ) ) {
					$raw_services = array( $raw_services );
				}
				$submitted_services = array_filter(
					array_map( 'sanitize_text_field', $raw_services ),
					static function( $service ) {
						return '' !== $service;
					}
				);
			}

			$valid_services = array_values( array_intersect( array_keys( $services ), $submitted_services ) );

			$budget = isset( $form_data['budget'] ) ? sanitize_text_field( wp_unslash( $form_data['budget'] ) ) : '';
			if ( ! array_key_exists( $budget, $budgets ) || '' === $budget ) {
				$budget = '';
			}

			return array(
				'firstname'                           => isset( $form_data['firstname'] ) ? sanitize_text_field( wp_unslash( $form_data['firstname'] ) ) : '',
				'lastname'                            => isset( $form_data['lastname'] ) ? sanitize_text_field( wp_unslash( $form_data['lastname'] ) ) : '',
				'email'                               => isset( $form_data['email'] ) ? sanitize_email( wp_unslash( $form_data['email'] ) ) : '',
				'phone'                               => isset( $form_data['phone'] ) ? sanitize_text_field( wp_unslash( $form_data['phone'] ) ) : '',
				'what_services_do_you_need_help_with' => $valid_services,
				'budget'                              => $budget,
				'message'                             => isset( $form_data['message'] ) ? sanitize_textarea_field( wp_unslash( $form_data['message'] ) ) : '',
			);
		}

		/**
		 * Build the wp_remote_post arguments for HubSpot.
		 */
		public static function get_hubspot_request_args( $sanitized_data ) {
			$payload = self::build_hubspot_payload( $sanitized_data );

			return array(
				'method'      => 'POST',
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array( 'Content-Type' => 'application/json' ),
				'body'        => wp_json_encode(
					array(
						'fields'  => $payload['fields'],
						'context' => array(
							'pageUri'   => admin_url( 'admin.php?page=upsell-order-bump-offer-for-woocommerce-setting' ),
							'pageName'  => self::get_plugin_label(),
							'ipAddress' => self::get_client_ip(),
						),
					)
				),
				'cookies'     => array(),
			);
		}

		/**
		 * Build the HubSpot payload.
		 */
		public static function build_hubspot_payload( $sanitized_data ) {
			$selected_services = ! empty( $sanitized_data['what_services_do_you_need_help_with'] ) && is_array( $sanitized_data['what_services_do_you_need_help_with'] )
				? array_values( $sanitized_data['what_services_do_you_need_help_with'] )
				: array();

			$fields = array(
				self::maybe_build_hubspot_field( 'firstname', isset( $sanitized_data['firstname'] ) ? $sanitized_data['firstname'] : '' ),
				self::maybe_build_hubspot_field( 'lastname', isset( $sanitized_data['lastname'] ) ? $sanitized_data['lastname'] : '' ),
				self::maybe_build_hubspot_field( 'email', isset( $sanitized_data['email'] ) ? $sanitized_data['email'] : '' ),
				self::maybe_build_hubspot_field( 'phone', isset( $sanitized_data['phone'] ) ? $sanitized_data['phone'] : '' ),
				self::maybe_build_hubspot_field( 'what_services_do_you_need_help_with', $selected_services ),
				self::maybe_build_hubspot_field( 'budget', isset( $sanitized_data['budget'] ) ? $sanitized_data['budget'] : '' ),
				self::maybe_build_hubspot_field( 'message', isset( $sanitized_data['message'] ) ? $sanitized_data['message'] : '' ),
				self::maybe_build_hubspot_field( 'org_plugin_name', self::get_plugin_label() ),
				self::maybe_build_hubspot_field( 'company', function_exists( 'get_bloginfo' ) ? get_bloginfo( 'name' ) : '' ),
				self::maybe_build_hubspot_field( 'website', function_exists( 'home_url' ) ? home_url( '/' ) : '' ),
			);

			return array( 'fields' => array_values( array_filter( $fields ) ) );
		}

		/**
		 * Build a single HubSpot field if it has a non-empty value.
		 */
		public static function maybe_build_hubspot_field( $field_name, $field_value ) {
			if ( is_array( $field_value ) ) {
				$field_value = implode(
					';',
					array_values(
						array_filter(
							array_map( 'strval', $field_value ),
							static function( $value ) {
								return '' !== $value;
							}
						)
					)
				);
			}

			if ( null === $field_value ) {
				return null;
			}

			$field_value = is_scalar( $field_value ) ? trim( (string) $field_value ) : '';

			if ( '' === $field_value ) {
				return null;
			}

			return array(
				'name'  => (string) $field_name,
				'value' => $field_value,
			);
		}

		/**
		 * Clean the HubSpot inline message before it is shown.
		 */
		public static function clean_response_message( $message ) {
			$message = is_string( $message ) ? $message : '';
			if ( '' === $message ) {
				return '';
			}
			$charset = function_exists( 'get_bloginfo' ) ? get_bloginfo( 'charset' ) : 'UTF-8';
			$message = wp_strip_all_tags( $message );
			$message = html_entity_decode( $message, ENT_QUOTES, $charset ? $charset : 'UTF-8' );
			$message = preg_replace( '/[\x{00A0}\s]+/u', ' ', $message );
			return trim( (string) $message );
		}

		/**
		 * Get the HubSpot endpoint.
		 */
		public static function get_hubspot_endpoint() {
			return 'https://api.hsforms.com/submissions/v3/integration/submit/' . self::HUBSPOT_PORTAL_ID . '/' . self::HUBSPOT_FORM_ID;
		}

		/**
		 * Resolve the client IP address for HubSpot context.
		 */
		public static function get_client_ip() {
			$ip_headers = array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR' );
			foreach ( $ip_headers as $header_key ) {
				if ( empty( $_SERVER[ $header_key ] ) ) {
					continue;
				}
				$raw_value = wp_unslash( $_SERVER[ $header_key ] );
				$ip_list   = is_string( $raw_value ) ? explode( ',', $raw_value ) : array( $raw_value );
				foreach ( $ip_list as $ip_candidate ) {
					$ip_candidate = trim( sanitize_text_field( (string) $ip_candidate ) );
					if ( filter_var( $ip_candidate, FILTER_VALIDATE_IP ) ) {
						return $ip_candidate;
					}
				}
			}
			return '';
		}

		/**
		 * Resolve the message from a HubSpot response.
		 */
		public static function get_hubspot_response_message( $response_body, $response_code ) {
			$response_body = is_array( $response_body ) ? $response_body : array();
			$message       = '';

			if ( ! empty( $response_body['inlineMessage'] ) ) {
				$message = self::clean_response_message( $response_body['inlineMessage'] );
			} elseif ( ! empty( $response_body['errors'][0]['message'] ) ) {
				$message = self::clean_response_message( $response_body['errors'][0]['message'] );
			} elseif ( ! empty( $response_body['message'] ) ) {
				$message = self::clean_response_message( $response_body['message'] );
			}

			if ( '' !== $message ) {
				return $message;
			}

			if ( $response_code >= 200 && $response_code < 300 ) {
				return esc_html__( 'Thank you for submitting your request.', 'upsell-order-bump-offer-for-woocommerce' );
			}

			return esc_html__( 'We could not submit your request right now. Please try again.', 'upsell-order-bump-offer-for-woocommerce' );
		}
	}

endif;

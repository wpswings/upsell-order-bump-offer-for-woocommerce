<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=order-bump-org-backend&utm_campaign=official
 * @since      1.0.0
 *
 * @package    upsell-order-bump-for-woocommerce
 * @subpackage upsell-order-bump-for-woocommerce/extra-templates
 */

$screen   = get_current_screen();
$is_valid = in_array( $screen->id, apply_filters( 'wps_helper_valid_frontend_screens', array() ), true );
if ( ! $is_valid ) {
	return false;
}

$form_fields = apply_filters( 'wps_on_boarding_form_fields', array() );

?>

<?php if ( ! empty( $form_fields ) ) : ?>
	<div class="wps-onboarding-section-funnel-bulder">
		<div class="wps-on-boarding-wrapper-background">
		<div class="wps-on-boarding-wrapper">
			<div class="wps-on-boarding-close-btn">
				<a href="#">
					<span class="close-form">x</span>
				</a>
			</div>
			<h3 class="wps-on-boarding-heading">Welcome to WP Swings </h3>
			<p class="wps-on-boarding-desc">We love making new friends! Subscribe below and we promise to keep you up-to-date with our latest new plugins, updates, awesome deals and a few special offers.</p>
			<form action="#" method="post" class="wps-on-boarding-form">
				<?php foreach ( $form_fields as $key => $field_attr ) : ?>
					<?php $this->render_field_html( $field_attr ); ?>
				<?php endforeach; ?> 
				<div class="wps-on-boarding-form-btn__wrapper">
					<div class="wps-on-boarding-form-submit wps-on-boarding-form-verify ">
					<input type="submit" class="wps-on-boarding-submit wps-on-boarding-verify " value="Send Us">
				</div>
				<div class="wps-on-boarding-form-no_thanks">
					<a href="#" class="wps-on-boarding-no_thanks"><?php esc_html_e( 'Skip For Now', 'upsell-order-bump-offer-for-woocommerce' ); ?></a>
				</div>
				</div>
			</form>
		</div>
	</div>
	</div>
<?php endif; ?>

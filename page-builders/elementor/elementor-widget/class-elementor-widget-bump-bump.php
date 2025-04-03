<?php
/**
 * Upsell elementor widgets collection loader file.
 *
 * @link       https://wpswings.com/?utm_source=wpswings-official&utm_medium=upsell-org-backend&utm_campaign=official
 * @since      3.0.0
 *
 * @package    woo-one-click-upsell-funnel
 * @subpackage woo-one-click-upsell-funnel/page-builders/elementor/assets
 */

namespace ElementorUpsellWidgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Widgets loader for elementor.
 */
class Elementor_Widget_Bump_Bump {

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Include Widgets files
	 *
	 * Load widgets files
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function include_widgets_files() {

		$widget_files = array(
			'upsell-accept-bump',
			'upsell-reject-bump',
			'upsell-image-bump',
			'upsell-title-bump',
			'upsell-price-bump',
			'upsell-variations-bump',
			'upsell-desc-bump',
			'upsell-short-desc-bump',
			'upsell-star-review-bump',
			'upsell-quantity-bump',
			'upsell-forms-bump',
		);

		foreach ( $widget_files as $key => $file_name ) {
			require 'assets/widgets/class-' . $file_name . '.php';
		}
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_widgets() {

		// It's now safe to include Widgets files.
		$this->include_widgets_files();

		// Register the plugin widget classes.
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Upsell_Accept_Bump() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Upsell_Reject_Bump() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Upsell_Title_Bump() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Upsell_Image_Bump() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Upsell_Price_Bump() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Upsell_Variations_Bump() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Upsell_Desc_Bump() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Upsell_Short_Desc_Bump() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Upsell_Star_Review_Bump() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Upsell_Quantity_bump() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Upsell_Forms_Bump() );
	}

	/**
	 * Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		// Register the widgets.
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ) );
	}
}

// Instantiate the Widgets class.
Elementor_Widget_Bump_Bump::instance();

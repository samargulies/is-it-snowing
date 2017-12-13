<?php
namespace  belabor\is_it_snowing;
/**
 * Class Assets_Controller
 *
 * Sets up the JS and CSS needed for this plugin
 *
 * @package  belabor\is_it_snowing
 */
class Assets_Controller {

	/**
	 * Constructor
	 */
	function __construct() {}

	/**
	 * Do Work
	 */
	public function setup() {
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Is it snowing?
	 */
	public function is_it_snowing() {

		$enabled = Plugin_Options::factory()->get_option( 'enabled' );
		
		if( $enabled === 'enabled_always' ) {
			$enabled = true;
		} else if( $enabled === 'enabled_when_snowing' ) {
			$enabled = Weather_API::factory()->is_it_snowing();
		} else {
			$enabled = false;
		}
		
		return apply_filters( 'is_it_snowing_enabled', $enabled );
	}

	/**
	 * Enqueue Scripts
	 */
	public function wp_enqueue_scripts() {
		
		if( $this->is_it_snowing() ) {
			wp_enqueue_script( 'is-it-snowing', IS_IT_SNOWING_URL . 'js/snowstorm-min.js', array(), IS_IT_SNOWING_VERSION, true );
		}
		
	}

	/**
	 * Enqueue Admin Scripts
	 */
	public function admin_enqueue_scripts( $hook ) {
		
		if( $hook != 'settings_page_is_it_snowing' ) {
			return;
		}

		// add script to fetch lat/lng for your current location
		wp_enqueue_script( 'is-it-snowing-admin', IS_IT_SNOWING_URL . 'js/admin-scripts.js', array(), IS_IT_SNOWING_VERSION, true );
		wp_localize_script( 'is-it-snowing-admin', 'isItSnowingI18N', array(
			'failedToGetLocation' => 'Error: Unable to get current location',
		) );
		
	}

	public static function factory() {
		static $instance = false;
		if ( ! $instance ) {
			$instance = new self();
			$instance->setup();
		}
		return $instance;
	}
}

$is_it_snowing_assets_controller = Assets_Controller::factory();

<?php

namespace  belabor\is_it_snowing;

/**
 * Class Plugin Options
 *
 * Options page for the plugin
 *
 * @package  belabor\is_it_snowing
 */
class Plugin_Options {

	private $plugin_settings_url;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->plugin_settings_url = admin_url( 'options-general.php?page=is_it_snowing' );
	}

	public function setup() {
		add_action( 'admin_init', array( $this, 'init_options_page' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'plugin_action_links_' . plugin_basename( IS_IT_SNOWING_PATH ) , array( $this, 'add_settings_link' ) );
	}

	public function get_plugin_settings_url() {
		return $this->plugin_settings_url;
	}

	/**
	 * Initialise the Options Page
	 */
	public function init_options_page() {

		$settings = 'is_it_snowing_settings';

		// Register Settings
		register_setting( $settings . '_group','is_it_snowing_settings', array( $this, 'validate_settings' ) );

		// Add api section and fields
		add_settings_section( 'section_weather_api', 'OpenWeatherMap API', array( $this, 'render_section_weather_api' ), $settings );
		add_settings_field( 'weather_api_key', 'OpenWeatherMap API Key', array( $this, 'render_field_weather_api' ), $settings, 'section_weather_api' );
		
		// add lat,lng section and fields
		add_settings_section( 'section_weather_location', 'Weather Location', array( $this, 'render_section_weather_location' ), $settings );
		add_settings_field( 'weather_lat', 'Latitude', array( $this, 'render_field_weather_lat' ), $settings, 'section_weather_location' );
		add_settings_field( 'weather_lng', 'Longitude', array( $this, 'render_field_weather_lng' ), $settings, 'section_weather_location' );
		
		// add enabled toggles
		add_settings_section( 'section_enabled', 'Enable Snow', array( $this, 'render_section_enabled' ), $settings );
		add_settings_field( 'enabled', 'Enable snow', array( $this, 'render_field_enabled' ), $settings, 'section_enabled' );
		
		
	}

	/**
	 * Validates the plugin settings.
	 */
	function validate_settings( $settings ) {
		
		$settings['weather_api_key'] = esc_attr( $settings['weather_api_key'] );
		$settings['weather_lat'] = esc_attr( $settings['weather_lat'] );
		$settings['weather_lng'] = esc_attr( $settings['weather_lng'] );
		$settings['enabled'] = esc_attr( $settings['enabled'] );
		
		// delete transients on settings update
		delete_transient( 'is_it_snowing_weather_conditions' );
		
		// Return the validated/sanitized settings.
		return $settings;
	}
	
	/**
	 * Render the OpenWeatherMap API section
	 */
	public function render_section_weather_api() {

		$api_key_documentation_url = 'https://home.openweathermap.org/users/sign_up';

		echo '<p>';
		esc_html_e( 'Enter your API Key here.', 'is-it-snowing' );
		echo ' ';
		_e( sprintf( '%s' . $api_key_documentation_url . '%sIf you do not have an API Key, you can get one here%s.', '<a href="', '">', '</a>' ), 'is-it-snowing' );
		echo '</p>';

	}
	
	/**
	 * Render the location section
	 */
	public function render_section_weather_location() {

		echo '<p>' .
			wp_kses( __( 'Location to check for snowy conditions. <a id="is_it_snowing_get_location" href="##">Use current location</a>.', 'is-it-snowing' ),
				array(  'a' => array( 'href' => array(), 'id' => array() ) ) ) . '</p>';

	}
	
	/**
	 * Render the enabled section
	 */
	public function render_section_enabled() {

		echo '<p>' . _e( 'Enable snow on your website', 'is-it-snowing' ) . '</p>';

	}

	// Render the OpenWeatherMap API field
	public function render_field_weather_api() {
		$weather_api_key = $this->get_option( 'weather_api_key' );
		?>
		<div class="field field-text field-weather-api">
			<label class="screen-reader-text" for="weather_api_key"><?php esc_html_e( 'OpenWeatherMap API Key', 'is-it-snowing' );?></label>
			<input type="text" id="weather_api_key" name="is_it_snowing_settings[weather_api_key]" value="<?php echo $weather_api_key; ?>" size="50"/>
		</div>
		<?php
	}
	
	// Render the OpenWeatherMap API field
	public function render_field_weather_lat() {

		$weather_lat = $this->get_option( 'weather_lat' );
		
		?>
		<div class="field field-text field-weather-lat">
			<label class="screen-reader-text" for="weather_lat"><?php esc_html_e( 'Latitude', 'is-it-snowing' );?></label>
			<input type="text" id="weather_lat" name="is_it_snowing_settings[weather_lat]" value="<?php echo $weather_lat; ?>" size="50"/>
		</div>
		<?php
	}
	
	// Render the OpenWeatherMap API field
	public function render_field_weather_lng() {

		$weather_lng = $this->get_option( 'weather_lng' );
		?>
		<div class="field field-text field-weather-lng">
			<label class="screen-reader-text" for="weather_lng"><?php esc_html_e( 'Longitude', 'is-it-snowing' );?></label>
			<input type="text" id="weather_lng" name="is_it_snowing_settings[weather_lng]" value="<?php echo $weather_lng; ?>" size="50"/>
		</div>
		<?php
	}

	// Render the enabled field
	public function render_field_enabled() {
		$enabled = $this->get_option( 'enabled' );
		?>
		<div class="field field-text field-weather-api">
			<label class="screen-reader-text" for="enabled"><?php esc_html_e( 'Enable snow', 'is-it-snowing' );?></label>
			<select name="is_it_snowing_settings[enabled]">
				<?php
				$options = array(
					'' => 'Disabled',
					'enabled_when_snowing' => 'Enabled when snowing',
					'enabled_always' => 'Enabled always',
				);

				foreach($options as $value => $name) {

					?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected($value, $enabled); ?>><?php echo esc_attr( $name ); ?></option>
					<?php
				}
				?>
			</select>
		</div>
		<?php
	}
	
	/**
	 * Add the options page
	 */
	public function add_options_page() {
		add_submenu_page( 'options-general.php', esc_html__( 'Is it Snowing?', 'is-it-snowing' ), esc_html__( 'Is it Snowing?', 'is-it-snowing' ), 'manage_options', 'is_it_snowing', array( $this, 'render_options_page' ) );
	}

	/**
	 * Render the options page
	 */
	public function render_options_page() {
		$settings = 'is_it_snowing_settings';
		
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Is it Snowing?', 'is-it-snowing' );?></h2>
			<form action="options.php" method="POST">
	            <?php settings_fields( $settings . '_group' ); ?>
	            <?php do_settings_sections( $settings ); ?>
	            <?php submit_button(); ?>
	        </form>
		</div>
	<?php
	}

	/**
	 * Add 'Settings' action on installed plugin list
	 */
	public function add_settings_link( $links ) {
		array_unshift( $links, '<a href="' . $this->plugin_settings_url . '">' . esc_html__( 'Settings', 'is-it-snowing' ) . '</a>' );
		return $links;
	}
	
	public function get_options( ) {
		return get_option( 'is_it_snowing_settings', array() );
	}
	
	public function get_option( $option ) {
		$all_options = $this->get_options();
		return isset( $all_options[$option] ) ? $all_options[$option] : '';
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

$is_it_snowing_plugin_options = Plugin_Options::factory();

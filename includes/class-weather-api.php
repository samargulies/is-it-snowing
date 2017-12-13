<?php
namespace  belabor\is_it_snowing;
/**
 * Class Assets_Controller
 *
 * Sets up the JS and CSS needed for this plugin
 *
 * @package  belabor\is_it_snowing
 */
class Weather_API {

	/**
	 * Constructor
	 */
	function __construct() {}

	function setup() {}

	/**
	 * Is it snowing?
	 */
	public function is_it_snowing() {
				
		$weather = $this->get_weather_conditions();
		
		if( !is_array( $weather->weather ) ) {
			return false;
		}
		
		foreach( $weather->weather as $condition ) {
			if( isset( $condition->main ) && $condition->main === 'Snow' ) {
				// its snowwwiingggg!!
				return true;
			} 
		}

		return false;

	}
	
	/**
	 * Get weather conditions from the web or from our transient
	 */
	private function get_weather_conditions() {
		
		$weather_conditions = get_transient( 'is_it_snowing_weather_conditions' );

		if( false === $weather_conditions ) {
		    
			$options = Plugin_Options::factory()->get_options();
		
			if( empty( $options['weather_api_key'] ) || empty( $options['weather_lat'] ) || empty( $options['weather_lng'] ) ) {
				return false;
			}
		
			// Build base request URL.
			$request_url = 'https://api.openweathermap.org/data/2.5/weather';
		
			$request_url = add_query_arg( array(
				'lat' => $options['weather_lat'],
				'lon' => $options['weather_lng'],
				'appid' => $options['weather_api_key'],
			), $request_url );

			// Build base request arguments.
			$args = array(
				/**
				 * Filters if SSL verification should occur.
				 *
				 * @param bool false If the SSL certificate should be verified. Defalts to false.
				 *
				 * @return bool
				 */
				'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
				/**
				 * Sets the HTTP timeout, in seconds, for the request.
				 *
				 * @param int 30 The timeout limit, in seconds. Defalts to 30.
				 *
				 * @return int
				 */
				'timeout'   => apply_filters( 'http_request_timeout', 30 ),
			);


			// Get request response.
			$response = wp_remote_get( $request_url, $args );

			// If request was not successful, throw exception.
			if ( is_wp_error( $response ) ) {
				error_log( "is it snowing request error: " . $response->get_error_message() );
				set_transient( 'is_it_snowing_weather_conditions', false, apply_filters( 'is_it_snowing_error_cache_timeout', 10 * MINUTE_IN_SECONDS ) );
				return false;
			}

			if ( wp_remote_retrieve_response_code( $response ) != 200 ) {
				error_log( "is it snowing response error: " . json_encode( $response ) );
				set_transient( 'is_it_snowing_weather_conditions', false, apply_filters( 'is_it_snowing_error_cache_timeout', 10 * MINUTE_IN_SECONDS ) );
				return false;
			}
	
			// error_log( "is it snowing  response successful: " . json_encode( $response ) );
			
			$weather_conditions = json_decode( wp_remote_retrieve_body( $response ) );

			set_transient( 'is_it_snowing_weather_conditions', $weather_conditions, apply_filters( 'is_it_snowing_cache_timeout', HOUR_IN_SECONDS ) );
		}

		return apply_filters( 'is_it_snowing_weather_conditions', $weather_conditions );
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

$is_it_snowing_weather_api = Weather_API::factory();


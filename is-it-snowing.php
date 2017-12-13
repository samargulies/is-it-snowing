<?php

/**
 * @link              http://github.com/samargulies/is-it-snowing
 * @package           belabor\is_it_snowing;
 *
 * Plugin Name:       Is it Snowing?
 * Plugin URI:        https://github.com/smargulies/is-it-snowing
 * Description:       Show snow falling on your website based on current weather conditions where you live.
 * Version:           1.0.0
 * Author:            Sam Margulies
 * Author URI:        http://www.belabor.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       is-it-snowing
 * Domain Path:       /languages
 */

// Constants
define( 'IS_IT_SNOWING_VERSION', '1.0.0' );

define( 'IS_IT_SNOWING_URL',     plugin_dir_url( __FILE__ ) );
define( 'IS_IT_SNOWING_PATH',    dirname( __FILE__ ) . '/' );

// Load Classes
require_once 'includes/class-plugin-options.php';
require_once 'includes/class-assets-controller.php';
require_once 'includes/class-weather-api.php';

load_plugin_textdomain( 'is-it-snowing', false, IS_IT_SNOWING_PATH . '\languages' );
<?php

/**
 * @link              https://cloudnineapps.com/
 * @since             1.0.0
 * @package           C9_Variables
 *
 * @wordpress-plugin
 * Plugin Name:       C9 Variables
 * Plugin URI:        https://cloudnineapps.com/products/wordpress-plugins/c9-variables
 * Description:       Use variables to make smart reusable content.
 * Version:           1.0.0
 * Author:            Cloud Nine Apps
 * Author URI:        https://cloudnineapps.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       c9-variables
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'C9_VARIABLES_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-c9-variables-activator.php
 */
function activate_c9_variables() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-c9-variables-activator.php';
	C9_Variables_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-c9-variables-deactivator.php
 */
function deactivate_c9_variables() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-c9-variables-deactivator.php';
	C9_Variables_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_c9_variables' );
register_deactivation_hook( __FILE__, 'deactivate_c9_variables' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-c9-variables.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_c9_variables() {

	$plugin = new C9_Variables();
	$plugin->run();

}
run_c9_variables();

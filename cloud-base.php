<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Cloud_Base
 *
 * @wordpress-plugin
 * Plugin Name:       Cloud Base
 * Plugin URI:        http://ifrstudent.com/cloud-base/
 * Description:       This Base module for Cloud Base - A module for managin Glider Clubs.
 * Version:           1.0.0
 * Author:            Philadelphia Glider Council 
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cloud-base
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CLOUD_BASE_VERSION', '1.0.0' );

/**
 * REST interface version.
 * Start at version 1
 * Rename this for your plugin and update it as you update the REST interface.
 * should not be updated as often of the plugin, if ever. 
 */
define( 'CLOUD_BASE_REST_VERSION', '1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cloud-base-activator.php
 */
function activate_cloud_base() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cloud-base-activator.php';
	Cloud_Base_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cloud-base-deactivator.php
 */
function deactivate_cloud_base() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cloud-base-deactivator.php';
	Cloud_Base_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cloud_base' );
register_deactivation_hook( __FILE__, 'deactivate_cloud_base' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cloud-base.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cloud_base() {

	$plugin = new Cloud_Base();
	$plugin->run();

}
run_cloud_base();

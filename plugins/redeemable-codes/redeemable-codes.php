<?php
// define('WP_DEBUG', true);
// define('WP_DEBUG_DISPLAY', true);
// @ini_set('display_errors', 0);

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://andreasilvestri.dev
 * @since             1.0.0
 * @package           Redeemable_Codes
 *
 * @wordpress-plugin
 * Plugin Name:       Redeemable Codes
 * Plugin URI:        https://andreasilvestri.dev/wp-plugins/redeemable-codes
 * Description:       A simple system to generate and redeem codes.
 * Version:           1.0.0
 * Author:            Andrea Silvestri
 * Author URI:        https://andreasilvestri.dev/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       redeemable-codes
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * The file that contains all the constants.
 */
require_once plugin_dir_path(__FILE__) . 'includes/redeemable-codes-constants.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-redeemable-codes-activator.php
 */
function activate_redeemable_codes()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-redeemable-codes-activator.php';
	Redeemable_Codes_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-redeemable-codes-deactivator.php
 */
function deactivate_redeemable_codes()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-redeemable-codes-deactivator.php';
	Redeemable_Codes_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_redeemable_codes');
register_deactivation_hook(__FILE__, 'deactivate_redeemable_codes');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-redeemable-codes.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_redeemable_codes()
{

	$plugin = new Redeemable_Codes();
	$plugin->run();
}
run_redeemable_codes();

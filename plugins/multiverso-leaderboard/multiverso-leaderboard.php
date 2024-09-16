<?php
// define('WP_DEBUG', true);
// define('WP_DEBUG_DISPLAY', true);
// @ini_set('display_errors', 1);

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
 * @package           Multiverso_Leaderboard
 *
 * @wordpress-plugin
 * Plugin Name:       Multiverso Leaderboard
 * Plugin URI:        https://andreasilvestri.dev/wp-plugins/multiverso-leaderboard
 * Description:       A simple system to generate and redeem codes.
 * Version:           1.0.0
 * Author:            Andrea Silvestri
 * Author URI:        https://andreasilvestri.dev/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       multiverso-leaderboard
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * The file that contains all the constants.
 */
require_once plugin_dir_path(__FILE__) . 'includes/multiverso-leaderboard-constants.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-multiverso-leaderboard-activator.php
 */
function activate_multiverso_leaderboard()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-multiverso-leaderboard-activator.php';
	Multiverso_Leaderboard_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-multiverso-leaderboard-deactivator.php
 */
function deactivate_multiverso_leaderboard()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-multiverso-leaderboard-deactivator.php';
	Multiverso_Leaderboard_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_multiverso_leaderboard');
register_deactivation_hook(__FILE__, 'deactivate_multiverso_leaderboard');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-multiverso-leaderboard.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_multiverso_leaderboard()
{

	$plugin = new Multiverso_Leaderboard();
	$plugin->run();
}

run_multiverso_leaderboard();

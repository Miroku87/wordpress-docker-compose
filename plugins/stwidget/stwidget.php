<?php

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
 * @package           Stwidget
 *
 * @wordpress-plugin
 * Plugin Name:       Speed Tale Widget
 * Plugin URI:        https://andreasilvestri.dev/stwidget/stwidget.zip
 * Description:       This is a description of the plugin.
 * Version:           1.0.0
 * Author:            Andrea Silvestri
 * Author URI:        https://andreasilvestri.dev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       stwidget
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('STWIDGET_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-stwidget-activator.php
 */
function activate_stwidget()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-stwidget-activator.php';
	Stwidget_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-stwidget-deactivator.php
 */
function deactivate_stwidget()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-stwidget-deactivator.php';
	Stwidget_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_stwidget');
register_deactivation_hook(__FILE__, 'deactivate_stwidget');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-stwidget.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_stwidget()
{

	$plugin = new Stwidget();
	$plugin->run();
}
run_stwidget();

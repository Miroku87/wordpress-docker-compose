<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://andreasilvestri.dev
 * @since      1.0.0
 *
 * @package    Stwidget
 * @subpackage Stwidget/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Stwidget
 * @subpackage Stwidget/admin
 * @author     Andrea Silvestri <andrea.silvestri87@yahoo.it>
 */
class Stwidget_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
	}

	/**
	 * Initializes the admin panel menu.
	 *
	 * @since    1.0.0
	 */
	public function admin_menu()
	{
		$this->add_admin_menu();
	}

	/**
	 * Adds the menu for the plugin.
	 *
	 * @since    1.0.0
	 */
	private function add_admin_menu()
	{
		add_menu_page(
			__('SpeedTale Widget', 'stwidget'), // Page title
			__('SpeedTale Widget', 'stwidget'), // Menu title
			'manage_options', // Capability
			'stwidget', // Menu slug
			array($this, 'stwidget_admin_page'), // Callback function
			'dashicons-tickets-alt', // Icon
			25 // Position
		);
	}

	/**
	 * Admin page interface.
	 *
	 * @since    1.0.0
	 */
	public function stwidget_admin_page()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/stwidget-admin-display.php';
	}
}

<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://andreasilvestri.dev
 * @since      1.0.0
 *
 * @package    Multiverso_Leaderboard
 * @subpackage Multiverso_Leaderboard/admin
 */


require_once plugin_dir_path(__FILE__) . 'leaderboard/class-multiverso-leaderboard-widget.php';

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Multiverso_Leaderboard
 * @subpackage Multiverso_Leaderboard/widgets
 * @author     Andrea Silvestri <andrea.silvestri87@yahoo.it>
 */
class Multiverso_Leaderboard_Widgets
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
	 * Prints the admin page.
	 *
	 * @since    1.0.0
	 */
	public function register_widgets()
	{
		$leaderboard_widget = new Multiverso_Leaderboard_Widget($this->plugin_name, $this->version);
		register_widget( $leaderboard_widget );
	}
}

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

/**
 * The file that contains all the constants.
 */
require_once plugin_dir_path(__FILE__) . '../includes/multiverso-leaderboard-constants.php';
require_once plugin_dir_path(__FILE__) . '../includes/debug-utils.php';

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Multiverso_Leaderboard
 * @subpackage Multiverso_Leaderboard/admin
 * @author     Andrea Silvestri <andrea.silvestri87@yahoo.it>
 */
class Multiverso_Leaderboard_Admin
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
	 * The CORS allowed origins.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Array<string>    $origins    The CORS allowed origins.
	 */
	private $origins;

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
		$this->origins = array();

		$cors_origins = $this->get_allowed_origins();

		if (count($cors_origins) > 0) {
			foreach ($cors_origins as $cors_origin) {
				$this->origins[] = $cors_origin;
			}
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/multiverso-leaderboard-admin.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name, plugins_url('css/admin-style.css', __FILE__), array(), $this->version, 'all');
		wp_enqueue_style('dashicons');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/multiverso-leaderboard-admin.js', array('jquery'), $this->version, false);
	}

	/**
	 * Initializes admin panel specific features.
	 *
	 * @since    1.0.0
	 */
	public function admin_init()
	{
		$this->handle_origin_form_submission();
		$this->handle_origin_delete_request();
		$this->handle_lb_entry_delete_request();
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
	 * Initializes the REST API hooks.
	 *
	 * @since    1.0.0
	 */
	public function rest_api_init()
	{
		$this->add_rest_api_hooks();
	}

	/**
	 * Sets pre serve request rules up.
	 *
	 * @since    1.0.0
	 */
	public function rest_pre_serve_request($value)
	{
		return $this->add_rest_pre_serve_request($value);
	}

	/**
	 * Ensures that the browser gets CORS information.
	 *
	 * @since    1.0.0
	 */
	public function rest_api_options()
	{
		if (count($this->origins) > 0) {
			header('Access-Control-Allow-Origin: ' . implode(', ', $this->origins));
		}

		header('Access-Control-Allow-Methods: ' . MULTIVERSO_LB_CORS_ALLOWED_METHODS);
	}

	/**
	 * Prints the admin page.
	 *
	 * @since    1.0.0
	 */
	public function admin_page()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/leaderboard/multiverso-leaderboard-admin.php';
	}

	/**
	 * Prints the CORS origin admin page.
	 *
	 * @since    1.0.0
	 */
	public function cors_origin_page()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/cors/multiverso-leaderboard-admin.php';
	}

	/**
	 * Function to add leaderboard entry.
	 *
	 * @since    1.0.0
	 */
	public function add_leaderboard_entry($data)
	{
		global $wpdb;
		$entry = $data['entry'];
		$table_name = $wpdb->prefix . MULTIVERSO_LB_LEADERBOARD_TABLE_NAME;

		$rate_limit = $this->check_rate_limit();
		if (is_wp_error($rate_limit)) {
			return $rate_limit;
		}

		$this->reset_rate_limit();

		$resp = $wpdb->insert(
			$table_name,
			array(
				'school_name' => $data['school_name'],
				'class_name' => $data['class_name'],
				'group_name' => $data['group_name'],
				'speedtale_id' => $data['speedtale_id'],
				'created_at' => date('Y-m-d H:i:s'),
				'total_score' => $data['total_score'],
				'elapsed_time_seconds' => $data['elapsed_time_seconds'],
			),
			array('%s', '%s', '%s', '%s', '%s', '%d', '%d'),
		);

		if (!$resp) {
			return new WP_Error('add_leaderboard_entry_error', $wpdb->last_error);
		}

		$insert_id = $wpdb->insert_id;

		$this->increment_rate_limit();

		return new WP_REST_Response(array("id"=>$insert_id), 200);
	}

	/**
	 * Adds the menu for the plugin.
	 *
	 * @since    1.0.0
	 */
	private function add_admin_menu()
	{
		add_menu_page(
			__('Classifiche Multiverso', $this->plugin_name), // Page title
			__('Classifiche Multiverso', $this->plugin_name), // Menu title
			'manage_options', // Capability
			$this->plugin_name, // Menu slug
			array($this, 'admin_page'), // Callback function
			'dashicons-editor-table', // Icon
			25 // Position
		);

		add_submenu_page(
			$this->plugin_name, // Parent slug
			__('Gestisci Origini CORS', $this->plugin_name), // Page title
			__('Gestisci Origini CORS', $this->plugin_name), // Menu title
			'manage_options', // Capability
			$this->plugin_name . '-cors', // Menu slug
			array($this, 'cors_origin_page') // Callback function
		);
	}

	/**
	 * Handle the submission of a new CORS origin.
	 *
	 * @since    1.0.0
	 */
	private function handle_origin_form_submission()
	{
		if (!isset($_POST['multiverso_leaderboard_nonce_field']) || !isset($_POST['origin']))
			return;

		if (!check_admin_referer('multiverso_leaderboard_generate_action', 'multiverso_leaderboard_nonce_field'))
			wp_die(__('Non puoi accedere a questa sezione.', $this->plugin_name));

		if (!current_user_can('manage_options'))
			wp_die(__('Non sufficienti permessi per accedere a questa sezione.', $this->plugin_name));

		$origin = $_POST['origin'];

		if (isset($_POST['expiration_days']) && intval($_POST['expiration_days']) > 0) {
			$expiration_days = intval($_POST['expiration_days']);
		} else {
			$expiration_days = NULL;
		}

		// Generate and store the codes
		$insert_ok = $this->add_allowed_origin($origin, $expiration_days);

		if (is_wp_error($insert_ok)) {
			$message = $insert_ok->get_error_message();

			if (strpos($message, 'Duplicate entry') !== false) {
				$message = sprintf(__('L\'origine %s è già registrata.', $this->plugin_name), $origin);
			}

			add_settings_error('multiverso-leaderboard-cors-notices', 'multiverso-leaderboard-cors-notices', $message, 'error');
			return;
		}

		$message = sprintf(__('Origine %s aggiunta.', $this->plugin_name), $origin);
		add_settings_error('multiverso-leaderboard-cors-notices', 'multiverso-leaderboard-cors-notices', $message, 'updated');
	}

	/**
	 * Handle the a CORS origin delete request.
	 *
	 * @since    1.0.0
	 */
	private function handle_origin_delete_request()
	{
		if (!isset($_POST['action']) || !isset($_POST['namespace']) || !isset($_POST['origin_id']))
			return;

		if ($_POST['namespace'] != 'cors')
			return;

		if ($_POST['action'] != 'delete')
			return;

		if (!check_admin_referer('multiverso_leaderboard_generate_action', 'multiverso_leaderboard_nonce_field'))
			wp_die(__('Non puoi accedere a questa sezione.', $this->plugin_name));

		if (!current_user_can('manage_options'))
			wp_die(__('Non sufficienti permessi per accedere a questa sezione.', $this->plugin_name));

		$origin_id = intval($_POST['origin_id']);

		if ($origin_id <= 0) {
			add_settings_error('multiverso-leaderboard-cors-notices', 'multiverso-leaderboard-cors-notices', __('ID origine invalido.', $this->plugin_name), 'error');
		}

		$ok = $this->delete_allowed_origin($origin_id);

		if (is_wp_error($ok)) {
			add_settings_error('multiverso-leaderboard-cors-notices', 'multiverso-leaderboard-cors-notices', $ok->get_error_message(), 'error');
			return;
		}

		add_settings_error('multiverso-leaderboard-cors-notices', 'multiverso-leaderboard-cors-notices', __('Origine rimossa.', $this->plugin_name), 'updated');
	}

	/**
	 * Handle a leaderboard entry delete request.
	 *
	 * @since    1.0.0
	 */
	private function handle_lb_entry_delete_request()
	{
		if (!isset($_POST['action']) || !isset($_POST['namespace']) || !isset($_POST['entry_id']))
			return;

		if ($_POST['namespace'] != 'leaderboard')
			return;

		if ($_POST['action'] != 'delete')
			return;

		if (!check_admin_referer('multiverso_leaderboard_generate_action', 'multiverso_leaderboard_nonce_field'))
			wp_die(__('Non puoi accedere a questa sezione.', $this->plugin_name));

		if (!current_user_can('manage_options'))
			wp_die(__('Non sufficienti permessi per accedere a questa sezione.', $this->plugin_name));

		$entry_id = intval($_POST['entry_id']);

		if ($entry_id <= 0) {
			add_settings_error('multiverso-leaderboard-notices', 'multiverso-leaderboard-notices', __('ID voce invalido.', $this->plugin_name), 'error');
		}

		$ok = $this->delete_lb_entry($entry_id);

		if (is_wp_error($ok)) {
			add_settings_error('multiverso-leaderboard-notices', 'multiverso-leaderboard-notices', $ok->get_error_message(), 'error');
			return;
		}

		add_settings_error('multiverso-leaderboard-notices', 'multiverso-leaderboard-notices', __('Origine rimossa.', $this->plugin_name), 'updated');
	}

	/**
	 * Registers the REST API hooks.
	 *
	 * @since    1.0.0
	 */
	private function add_rest_api_hooks()
	{
		register_rest_route('multiverso-leaderboard/v1', '/health', array(
			'methods' => 'GET',
			'callback' => function () {
				return new WP_REST_Response(array('status' => 'ok'), 200);
			}
		));

		register_rest_route('multiverso-leaderboard/v1', '/entry', array(
			'methods' => 'POST',
			'callback' => array($this, 'add_leaderboard_entry'),
		));

		register_rest_route('multiverso-leaderboard/v1', '/entry', array(
			'methods' => 'OPTIONS',
			'callback' => array($this, 'rest_api_options'),
		));
	}

	/**
	 * Adds REST pre serve request rules.
	 *
	 * @since    1.0.0
	 */
	private function add_rest_pre_serve_request($value)
	{
		$this->rest_api_options();

		return $value;
	}

	/**
	 * Checks the rate limit of an IP.
	 *
	 * @since    1.0.0
	 */
	private function check_rate_limit()
	{
		$user_ip = $_SERVER['REMOTE_ADDR']; // Get the IP address of the client.
		$rate_limit_key = 'multiverso_leaderboard_rate_limit_' . $user_ip;
		$rate_limit = get_transient($rate_limit_key);

		// Check if the current IP has hit the rate limit.
		if ($rate_limit >= MULTIVERSO_LB_RATE_LIMIT_REQUESTS) {
			return new WP_Error('rate_limit_exceeded', 'Troppe chiamate API', array('status' => 429));
		}

		return true;
	}

	/**
	 * Increments the rate limit of an IP.
	 *
	 * @since    1.0.0
	 */
	private function increment_rate_limit()
	{
		$user_ip = $_SERVER['REMOTE_ADDR']; // Get the IP address of the client.
		$rate_limit_key = 'multiverso_leaderboard_rate_limit_' . $user_ip;
		$rate_limit = get_transient($rate_limit_key);

		// Increment the rate limit counter.
		if ($rate_limit) {
			set_transient($rate_limit_key, $rate_limit + 1, MULTIVERSO_LB_RATE_LIMIT_SECONDS); // Increment the count and set a 1-minute expiration.
		} else {
			set_transient($rate_limit_key, 1, MULTIVERSO_LB_RATE_LIMIT_SECONDS); // Start the count with a 1-minute expiration.
		}
	}

	/**
	 * Resets the rate limit of an IP.
	 *
	 * @since    1.0.0
	 */
	private function reset_rate_limit()
	{
		$user_ip = $_SERVER['REMOTE_ADDR']; // Get the IP address of the client.
		$rate_limit_key = 'multiverso_leaderboard_rate_limit_' . $user_ip;

		delete_transient($rate_limit_key);
	}

	/**
	 * Gets the allowed origins from the database.
	 *
	 * @since    1.0.0
	 */
	private function get_allowed_origins()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . MULTIVERSO_LB_ORIGINS_TABLE_NAME;

		$allowed_origins = $wpdb->get_col("SELECT origin FROM $table_name WHERE expiration_date IS NULL OR expiration_date > NOW()");
		return $allowed_origins;
	}

	/**
	 * Adds an allowed origins to the database.
	 *
	 * @since    1.0.0
	 */
	private function add_allowed_origin($origin, $expiration_days = NULL)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . MULTIVERSO_LB_ORIGINS_TABLE_NAME;
		$resp = $wpdb->insert(
			$table_name,
			array(
				'origin' => $origin,
				'expiration_date' => $expiration_days ? date('Y-m-d H:i:s', strtotime('+' . $expiration_days . ' days')) : NULL,
				'created_at' => date('Y-m-d H:i:s'),
			),
			array('%s', '%s', '%s'),
		);

		if (!$resp) {
			return new WP_Error('add_allowed_origin_error', $wpdb->last_error);
		}

		return intval($wpdb->insert_id);
	}

	/**
	 * Deletes an allowed origins to the database.
	 *
	 * @since    1.0.0
	 */
	private function delete_allowed_origin($origin_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . MULTIVERSO_LB_ORIGINS_TABLE_NAME;
		$resp = $wpdb->delete(
			$table_name,
			array('id' => $origin_id),
			array('%d'),
		);

		if (!$resp) {
			return new WP_Error('delete_allowed_origin_error', $wpdb->last_error);
		}

		return True;
	}

	/**
	 * Deletes a leaderboard entry from the database.
	 *
	 * @since    1.0.0
	 */
	private function delete_lb_entry($entry_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . MULTIVERSO_LB_LEADERBOARD_TABLE_NAME;
		$resp = $wpdb->delete(
			$table_name,
			array('id' => $entry_id),
			array('%d'),
		);

		if (!$resp) {
			return new WP_Error('delete_lb_entry_error', $wpdb->last_error);
		}

		return True;
	}
}
